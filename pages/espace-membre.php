<?php
require_once __DIR__ . '/../assets/php/config/auth.php';
gc_require_login('connexion.php');

require_once __DIR__ . '/../assets/php/config/db.php';

$currentUser = gc_current_user();
$userId = (int) $currentUser['id'];

$messageSucces = '';
$messageErreur = '';

try {
    $stmt = $pdo->prepare('SELECT id, pseudo, prenom, nom, email, avatar, jeu_principal, role FROM utilisateurs WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $profil = $stmt->fetch();

    if (!$profil) {
        header('Location: logout.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('[ESPACE MEMBRE LOAD] ' . $e->getMessage());
    $profil = [
        'pseudo'        => $currentUser['pseudo'],
        'prenom'        => '',
        'nom'           => '',
        'email'         => '',
        'avatar'        => null,
        'jeu_principal' => null,
    ];
    $messageErreur = 'Impossible de charger ton profil pour le moment.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $messageErreur = 'Le fichier envoyé est trop volumineux (dépasse la limite du serveur).';
        $action = ''; // On empêche la suite
    } else {
        $action = trim($_POST['action'] ?? 'update_profil');
    }

    // demande de statut capitaine
    if ($action === 'demande_capitaine') {

        if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
            $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
        } elseif ($profil['role'] !== 'visiteur') {
            $messageErreur = 'Ton compte n\'est pas éligible à cette demande.';
        } else {
            $nomEquipe = trim($_POST['nom_equipe'] ?? '');
            $jeuDem    = trim($_POST['jeu_dem'] ?? '');
            $messageDem = trim($_POST['message_dem'] ?? '');
            $jeuxOk = ['lol', 'valorant', 'cs2', 'fortnite', 'rocket-league'];

            if (empty($nomEquipe) || strlen($nomEquipe) < 2) {
                $messageErreur = 'Le nom d\'équipe doit faire au moins 2 caractères.';
            } elseif (!in_array($jeuDem, $jeuxOk, true)) {
                $messageErreur = 'Choisis un jeu valide.';
            } else {
                try {
                    // Remplacer une ancienne demande refusée ou en créer une nouvelle
                    $pdo->prepare('DELETE FROM demandes_capitaine WHERE user_id = :uid AND statut IN (\'refusee\')')
                        ->execute([':uid' => $userId]);

                    $stmtDem = $pdo->prepare(
                        'INSERT OR IGNORE INTO demandes_capitaine (user_id, nom_equipe, jeu, message) VALUES (:uid, :nom, :jeu, :msg)'
                    );
                    $stmtDem->execute([
                        ':uid' => $userId,
                        ':nom' => $nomEquipe,
                        ':jeu' => $jeuDem,
                        ':msg' => $messageDem !== '' ? $messageDem : null,
                    ]);
                    $messageSucces = 'Demande envoyée ! L\'administrateur va la traiter sous peu.';
                } catch (PDOException $e) {
                    error_log('[DEMANDE CAPITAINE] ' . $e->getMessage());
                    $messageErreur = 'Erreur lors de l\'envoi de la demande. Réessaie plus tard.';
                }
            }
        }
    // mise a jour du profil
    } elseif ($action === 'update_profil') {

        // 1. Vérification CSRF
        if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
            $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
        } else {

        // 2. Récupération et nettoyage des champs
        $pseudo = trim($_POST['pseudo'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $nom    = trim($_POST['nom'] ?? '');
        $email  = trim(strtolower($_POST['email'] ?? ''));
        $jeu    = trim($_POST['jeu_principal'] ?? '');

        $jeuxAutorises = ['lol', 'valorant', 'cs2', 'fortnite', 'rocket-league', 'autre', ''];
        if (!in_array($jeu, $jeuxAutorises, true)) {
            $jeu = '';
        }

        // 3. Validations
        if (empty($pseudo) || strlen($pseudo) < 3 || strlen($pseudo) > 20 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $pseudo)) {
            $messageErreur = 'Pseudo invalide (3-20 caractères, lettres/chiffres/_/-).';
        } elseif (empty($prenom) || empty($nom)) {
            $messageErreur = 'Le prénom et le nom sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messageErreur = 'Adresse email invalide.';
        } else {
            // 4. Vérification unicité pseudo/email
            try {
                $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE (email = :email OR pseudo = :pseudo) AND id <> :id LIMIT 1');
                $stmt->execute([':email' => $email, ':pseudo' => $pseudo, ':id' => $userId]);
                if ($stmt->fetch()) {
                    $messageErreur = 'Email ou pseudo déjà utilisé par un autre compte.';
                }
            } catch (PDOException $e) {
                error_log('[ESPACE MEMBRE CHECK] ' . $e->getMessage());
                $messageErreur = 'Erreur de vérification. Réessaie plus tard.';
            }
        }

        // 5. Traitement avatar (seulement si pas d'erreur de validation)
        $avatarPath = isset($profil['avatar']) ? $profil['avatar'] : null;

        if ($messageErreur === '' && isset($_FILES['avatar']) && (int)$_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize      = 2 * 1024 * 1024;
            
            // Détection du type MIME avec fallback
            $mimeType = false;
            if (class_exists('finfo')) {
                $finfo    = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['avatar']['tmp_name']);
            } else {
                $mimeType = $_FILES['avatar']['type'];
            }

            // Fallback de sécurité basé sur l'extension si on doute du type MIME
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!$mimeType || !in_array($mimeType, $allowedTypes, true) || !in_array($ext, $allowedExts, true)) {
                $messageErreur = 'Format avatar invalide (JPG, PNG, WebP, GIF).';
            } elseif ((int)$_FILES['avatar']['size'] > $maxSize) {
                $messageErreur = 'Avatar trop volumineux (max 2 Mo).';
            } else {
                $uploadDir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = uniqid('avatar_', true) . '.' . $ext;
                $destPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                    $avatarPath = 'uploads/avatars/' . $filename;
                } else {
                    $messageErreur = 'Impossible d\'uploader l\'avatar. Vérifiez les permissions du dossier uploads/.';
                }
            }
        } elseif (isset($_FILES['avatar'])
               && (int)$_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE
               && (int)$_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            // Erreur PHP upload (fichier trop grand côté serveur, etc.)
            $messageErreur = 'Erreur lors de l\'envoi du fichier (code : ' . (int)$_FILES['avatar']['error'] . ').';
        }

        // 6. Sauvegarde en BDD si tout est OK
        if ($messageErreur === '') {
            try {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET pseudo = :pseudo, prenom = :prenom, nom = :nom, email = :email, jeu_principal = :jeu, avatar = :avatar WHERE id = :id');
                $stmt->execute([
                    ':pseudo'  => $pseudo,
                    ':prenom'  => $prenom,
                    ':nom'     => $nom,
                    ':email'   => $email,
                    ':jeu'     => $jeu !== '' ? $jeu : null,
                    ':avatar'  => $avatarPath,
                    ':id'      => $userId,
                ]);

                $_SESSION['user_pseudo'] = $pseudo;
                $messageSucces = 'Profil mis à jour avec succès.';

                // Rechargement du profil
                $stmt = $pdo->prepare('SELECT id, pseudo, prenom, nom, email, avatar, jeu_principal, role FROM utilisateurs WHERE id = ? LIMIT 1');
                $stmt->execute([$userId]);
                $profil = $stmt->fetch() ?: $profil;
            } catch (PDOException $e) {
                error_log('[ESPACE MEMBRE UPDATE] ' . $e->getMessage());
                $messageErreur = 'Impossible de sauvegarder le profil.';
            }
        }
    }
}
}

$reservations = [];
try {
    $stmt = $pdo->prepare('SELECT r.id, r.nom_equipe, r.statut, r.created_at, t.id AS tournoi_id, t.nom AS tournoi_nom, t.jeu, t.date_debut FROM reservations r INNER JOIN tournois t ON t.id = r.tournoi_id WHERE r.capitaine_id = ? ORDER BY r.created_at DESC');
    $stmt->execute([$userId]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[ESPACE MEMBRE RESERVATIONS] ' . $e->getMessage());
}

$nbTournois  = count($reservations);
$nbVictoires = 0;
foreach ($reservations as $reservation) {
    if ($reservation['statut'] === 'confirmee') {
        $nbVictoires++;
    }
}
$points   = ($nbVictoires * 10) + (($nbTournois - $nbVictoires) * 3);
$nomEquipe = $reservations[0]['nom_equipe'] ?? '—';

// Chargement de la demande capitaine en cours
$demandeCap = null;
try {
    $stmtCap = $pdo->prepare('SELECT * FROM demandes_capitaine WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
    $stmtCap->execute([$userId]);
    $demandeCap = $stmtCap->fetch();
} catch (PDOException $e) {
    error_log('[DEMANDE CAPITAINE LOAD] ' . $e->getMessage());
}

$rootPath          = '../';
$pageTitle         = 'Espace Membre - Gaming Campus';
$metaDescription   = 'Ton espace personnel Gaming Campus. Gère tes équipes, tes inscriptions et ton profil.';
$cssSpecifique     = 'espace-membre.css';
$jsSupplementaires = ['avatar-upload.js'];
include '../assets/php/components/header.php';
?>

    <main id="main-content">

        <section class="page-hero" aria-label="En-tête espace membre">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Espace Membre</li>
                    </ol>
                </nav>
                <div class="member-header">
                    <div class="member-avatar-wrapper">
                        <div class="member-avatar">
                            <?php if (!empty($profil['avatar'])): ?>
                            <img id="current-avatar-img" src="../<?= htmlspecialchars($profil['avatar']) ?>" alt="Avatar" loading="lazy">
                            <?php else: ?>
                            <div id="current-avatar-placeholder" class="user-avatar-placeholder" aria-hidden="true">👤</div>
                            <img id="current-avatar-img" src="" alt="Avatar" class="hidden" loading="lazy">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="member-info">
                        <h1>Ton Espace Membre, <?= htmlspecialchars($profil['pseudo']) ?></h1>
                        <p>Gère ton profil, tes équipes et tes inscriptions aux tournois.</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($messageSucces !== ''): ?>
        <section>
            <div class="section-container">
                <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($messageErreur !== ''): ?>
        <section>
            <div class="section-container">
                <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
            </div>
        </section>
        <?php endif; ?>

        <nav class="member-nav" aria-label="Navigation espace membre">
            <div class="section-container">
                <ul class="member-nav-list">
                    <li><a href="#dashboard" class="member-nav-link active">🏠 Tableau de bord</a></li>
                    <li><a href="#reservations" class="member-nav-link">📋 Mes Réservations</a></li>
                    <?php if ($profil['role'] === 'visiteur'): ?>
                    <li><a href="#devenir-capitaine" class="member-nav-link">🏆 Devenir Capitaine</a></li>
                    <?php endif; ?>
                    <li><a href="#profil" class="member-nav-link">👤 Mon Profil</a></li>
                </ul>
            </div>
        </nav>

        <section id="dashboard" aria-labelledby="titre-dashboard">
            <div class="section-container">
                <h2 id="titre-dashboard">🏠 Tableau de bord</h2>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <span class="stat-icon">🎮</span>
                        <span class="stat-value"><?= $nbTournois ?></span>
                        <span class="stat-label">Tournois joués</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">🏆</span>
                        <span class="stat-value"><?= $nbVictoires ?></span>
                        <span class="stat-label">Victoires</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">⭐</span>
                        <span class="stat-value"><?= $points ?></span>
                        <span class="stat-label">Points</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">👥</span>
                        <span class="stat-value"><?= htmlspecialchars($nomEquipe) ?></span>
                        <span class="stat-label">Mon équipe</span>
                    </div>
                </div>

                <?php if (!empty($reservations)): ?>
                <div class="empty-state">
                    <span class="empty-state-icon">✅</span>
                    <p>Tu as déjà <?= $nbTournois ?> inscription(s) enregistrée(s).</p>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="empty-state-icon">🎮</span>
                    <p>Tu n'as pas encore participé à de tournoi.</p>
                    <p class="empty-state-sub"><a href="tournois.php">Découvrir les tournois</a></p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="reservations" aria-labelledby="titre-reservations">
            <div class="section-container">
                <h2 id="titre-reservations">📋 Mes Réservations</h2>

                <?php if (!empty($reservations)): ?>
                <div class="table-responsive">
                    <table class="leaderboard-table" aria-label="Mes réservations">
                        <thead>
                            <tr>
                                <th scope="col">Tournoi</th>
                                <th scope="col">Équipe</th>
                                <th scope="col">Jeu</th>
                                <th scope="col">Date</th>
                                <th scope="col">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><a href="tournoi-detail.php?id=<?= (int) $reservation['tournoi_id'] ?>"><?= htmlspecialchars($reservation['tournoi_nom']) ?></a></td>
                                <td><?= htmlspecialchars($reservation['nom_equipe']) ?></td>
                                <td><?= htmlspecialchars($reservation['jeu']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $reservation['date_debut']))) ?></td>
                                <td><span class="status-badge status-<?= htmlspecialchars($reservation['statut']) ?>"><?= htmlspecialchars($reservation['statut']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="empty-state-icon">📋</span>
                    <p>Aucune réservation pour le moment.</p>
                    <p class="empty-state-sub"><a href="tournois.php">S'inscrire à un tournoi</a></p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ══ SECTION : Devenir Capitaine ══ -->
        <?php if ($profil['role'] === 'visiteur' || $demandeCap !== null): ?>
        <section id="devenir-capitaine" aria-labelledby="titre-devenir-capitaine">
            <div class="section-container">
                <h2 id="titre-devenir-capitaine">🏆 Devenir Capitaine</h2>

                <?php if ($demandeCap !== null && $demandeCap['statut'] === 'approuvee'): ?>
                <!-- Demande approuvée -->
                <div class="captain-request-status status-approved" style="background: rgba(39, 174, 96, 0.1); border: 1px solid rgba(39, 174, 96, 0.3); color: #2ecc71; padding: 1.5rem; border-radius: var(--radius-md); display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 2rem;">
                    <div class="status-icon" style="font-size: 2rem;">✅</div>
                    <div class="status-body">
                        <h3 style="margin-top:0; margin-bottom:0.5rem; color:#2ecc71;">Demande approuvée</h3>
                        <p style="margin:0; font-size:0.9rem; line-height:1.5;">Félicitations ! Ta demande pour l'équipe <strong><?= htmlspecialchars($demandeCap['nom_equipe']) ?></strong> a été acceptée par l'administrateur. Tu possèdes désormais le statut de <strong>Capitaine</strong> et peux inscrire ton équipe aux tournois.</p>
                    </div>
                </div>

                <?php elseif ($demandeCap !== null && $demandeCap['statut'] === 'en-attente'): ?>
                <!-- Demande déjà en cours -->
                <div class="captain-request-status status-pending">
                    <div class="status-icon">⏳</div>
                    <div class="status-body">
                        <h3>Demande en attente</h3>
                        <p>Ta demande pour l'équipe <strong><?= htmlspecialchars($demandeCap['nom_equipe']) ?></strong>
                           (<?= htmlspecialchars($demandeCap['jeu']) ?>) a bien été envoyée le
                           <?= htmlspecialchars(date('d/m/Y', strtotime((string)$demandeCap['created_at']))) ?>.</p>
                        <p class="status-sub">L'administrateur va l'examiner sous peu. Tu recevras ton statut de capitaine dès validation.</p>
                    </div>
                </div>

                <?php elseif ($demandeCap !== null && $demandeCap['statut'] === 'refusee'): ?>
                <!-- Demande refusée — peut re-soumettre -->
                <div class="captain-request-status status-refused">
                    <div class="status-icon">❌</div>
                    <div class="status-body">
                        <h3>Demande refusée</h3>
                        <p>Ta précédente demande a été refusée. Tu peux soumettre une nouvelle demande ci-dessous.</p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($profil['role'] === 'visiteur' && (!$demandeCap || $demandeCap['statut'] === 'refusee')): ?>
                <!-- Formulaire de demande -->
                <div class="captain-request-form-wrapper">
                    <p class="captain-info">
                        🎮 En tant que <strong>capitaine</strong>, tu pourras inscrire ton équipe aux tournois du campus.<br>
                        Remplis ce formulaire — l'administrateur validera ta demande.
                    </p>
                    <form class="profil-form captain-form" method="post" action="espace-membre.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                        <input type="hidden" name="action" value="demande_capitaine">

                        <div class="form-group">
                            <label for="cap-nom-equipe">Nom de ton équipe <span class="required">*</span></label>
                            <input type="text" id="cap-nom-equipe" name="nom_equipe"
                                   placeholder="Ex: Les Légendes du Campus" minlength="2" maxlength="50" required>
                        </div>

                        <div class="form-group">
                            <label for="cap-jeu">Jeu principal <span class="required">*</span></label>
                            <select id="cap-jeu" name="jeu_dem" required>
                                <option value="">— Choisir un jeu —</option>
                                <option value="lol">League of Legends</option>
                                <option value="valorant">Valorant</option>
                                <option value="cs2">Counter-Strike 2</option>
                                <option value="fortnite">Fortnite</option>
                                <option value="rocket-league">Rocket League</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cap-message">Message (optionnel)</label>
                            <textarea id="cap-message" name="message_dem" rows="3"
                                      placeholder="Présente ton équipe, ton niveau, vos objectifs..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Envoyer ma demande 🚀</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <section id="profil" aria-labelledby="titre-profil">
            <div class="section-container">
                <h2 id="titre-profil">👤 Mon Profil</h2>

                <form class="profil-form" method="post" action="espace-membre.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                    <input type="hidden" name="action" value="update_profil">

                    <div class="form-group form-group-avatar">
                        <label>Photo de profil</label>
                        <div class="avatar-upload">
                            <div class="avatar-preview-wrapper">
                                <div class="avatar-preview">
                                    <span class="avatar-preview-placeholder" id="avatar-placeholder" <?= !empty($profil['avatar']) ? 'class="hidden"' : '' ?>>👤</span>
                                    <img id="avatar-preview-img"
                                         src="<?= !empty($profil['avatar']) ? '../' . htmlspecialchars($profil['avatar']) : '' ?>"
                                         alt="Aperçu avatar"
                                         class="<?= empty($profil['avatar']) ? 'hidden' : '' ?>"
                                         loading="lazy">
                                </div>
                            </div>
                            <div class="avatar-upload-actions">
                                <label for="avatar-file" class="btn btn-outline">📷 Changer la photo</label>
                                <input type="file" id="avatar-file" name="avatar"
                                       accept="image/png, image/jpeg, image/webp, image/gif" class="sr-only">
                                <small class="form-help">JPG, PNG, WebP ou GIF. Max 2 Mo.</small>
                                <button type="button" id="avatar-remove-btn"
                                        class="btn btn-sm btn-outline hidden"
                                        aria-label="Supprimer l'avatar">✕ Supprimer</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profil-pseudo">Pseudo</label>
                        <input type="text" id="profil-pseudo" name="pseudo"
                               placeholder="Ton pseudo" minlength="3" maxlength="20"
                               value="<?= htmlspecialchars($profil['pseudo'] ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="profil-prenom">Prénom</label>
                            <input type="text" id="profil-prenom" name="prenom"
                                   placeholder="Ton prénom"
                                   value="<?= htmlspecialchars($profil['prenom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="profil-nom">Nom</label>
                            <input type="text" id="profil-nom" name="nom"
                                   placeholder="Ton nom"
                                   value="<?= htmlspecialchars($profil['nom'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profil-email">Email</label>
                        <input type="email" id="profil-email" name="email"
                               placeholder="ton.email@campus.fr"
                               value="<?= htmlspecialchars($profil['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="profil-jeu">Jeu principal</label>
                        <select id="profil-jeu" name="jeu_principal">
                            <option value="">— Choisir —</option>
                            <option value="lol"          <?= (($profil['jeu_principal'] ?? '') === 'lol')          ? 'selected' : '' ?>>League of Legends</option>
                            <option value="valorant"     <?= (($profil['jeu_principal'] ?? '') === 'valorant')     ? 'selected' : '' ?>>Valorant</option>
                            <option value="cs2"          <?= (($profil['jeu_principal'] ?? '') === 'cs2')          ? 'selected' : '' ?>>Counter-Strike 2</option>
                            <option value="fortnite"     <?= (($profil['jeu_principal'] ?? '') === 'fortnite')     ? 'selected' : '' ?>>Fortnite</option>
                            <option value="rocket-league" <?= (($profil['jeu_principal'] ?? '') === 'rocket-league') ? 'selected' : '' ?>>Rocket League</option>
                            <option value="autre"        <?= (($profil['jeu_principal'] ?? '') === 'autre')        ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                </form>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
