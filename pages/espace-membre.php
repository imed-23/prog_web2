<?php
<<<<<<< HEAD
require_once __DIR__ . '/../assets/php/components/auth-guard.php';
authRequire();
=======
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
        'pseudo' => $currentUser['pseudo'],
        'prenom' => '',
        'nom' => '',
        'email' => '',
        'avatar' => null,
        'jeu_principal' => null,
    ];
    $messageErreur = 'Impossible de charger ton profil pour le moment.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    }

    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $jeu = trim($_POST['jeu_principal'] ?? '');
    $avatarPath = $profil['avatar'] ?? null;

    if ($messageErreur === '' && ($pseudo === '' || strlen($pseudo) < 3 || strlen($pseudo) > 20 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $pseudo))) {
        $messageErreur = 'Pseudo invalide (3-20 caractères, lettres/chiffres/_/-).';
    } elseif ($messageErreur === '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageErreur = 'Adresse email invalide.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE (email = :email OR pseudo = :pseudo) AND id <> :id LIMIT 1');
            $stmt->execute([
                ':email' => $email,
                ':pseudo' => $pseudo,
                ':id' => $userId,
            ]);
            if ($stmt->fetch()) {
                $messageErreur = 'Email ou pseudo déjà utilisé par un autre compte.';
            }
        } catch (PDOException $e) {
            error_log('[ESPACE MEMBRE CHECK] ' . $e->getMessage());
            $messageErreur = 'Erreur de vérification. Réessaie plus tard.';
        }
    }

    if ($messageErreur === '' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 2 * 1024 * 1024;
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($_FILES['avatar']['tmp_name']);

        if (!in_array($mimeType, $allowedTypes, true)) {
            $messageErreur = 'Format avatar invalide (JPG, PNG, WebP, GIF).';
        } elseif ($_FILES['avatar']['size'] > $maxSize) {
            $messageErreur = 'Avatar trop volumineux (max 2 Mo).';
        } else {
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('avatar_', true) . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                $avatarPath = 'uploads/avatars/' . $filename;
            } else {
                $messageErreur = 'Impossible d\'uploader l\'avatar.';
            }
        }
    }

    if ($messageErreur === '') {
        try {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET pseudo = :pseudo, email = :email, jeu_principal = :jeu, avatar = :avatar WHERE id = :id');
            $stmt->execute([
                ':pseudo' => $pseudo,
                ':email' => $email,
                ':jeu' => $jeu !== '' ? $jeu : null,
                ':avatar' => $avatarPath,
                ':id' => $userId,
            ]);

            $_SESSION['user_pseudo'] = $pseudo;
            $messageSucces = 'Profil mis à jour avec succès.';

            $stmt = $pdo->prepare('SELECT id, pseudo, prenom, nom, email, avatar, jeu_principal, role FROM utilisateurs WHERE id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $profil = $stmt->fetch() ?: $profil;
        } catch (PDOException $e) {
            error_log('[ESPACE MEMBRE UPDATE] ' . $e->getMessage());
            $messageErreur = 'Impossible de sauvegarder le profil.';
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

$nbTournois = count($reservations);
$nbVictoires = 0;
foreach ($reservations as $reservation) {
    if ($reservation['statut'] === 'confirmee') {
        $nbVictoires++;
    }
}
$points = ($nbVictoires * 10) + (($nbTournois - $nbVictoires) * 3);
$nomEquipe = $reservations[0]['nom_equipe'] ?? '—';
>>>>>>> 14eabe1 (release v1.3)

$rootPath        = '../';
$pageTitle       = 'Espace Membre - Gaming Campus';
$metaDescription = 'Ton espace personnel Gaming Campus. Gère tes équipes, tes inscriptions et ton profil.';
$cssSpecifique   = 'espace-membre.css';
$jsSupplementaires = ['avatar-upload.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
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

        <!-- ======== NAVIGATION INTERNE ======== -->
        <nav class="member-nav" aria-label="Navigation espace membre">
            <div class="section-container">
                <ul class="member-nav-list">
                    <li><a href="#dashboard" class="member-nav-link active">🏠 Tableau de bord</a></li>
                    <li><a href="#reservations" class="member-nav-link">📋 Mes Réservations</a></li>
                    <li><a href="#profil" class="member-nav-link">👤 Mon Profil</a></li>
                </ul>
            </div>
        </nav>

        <!-- ======== TABLEAU DE BORD ======== -->
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

        <!-- ======== MES RÉSERVATIONS ======== -->
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

        <!-- ======== MON PROFIL ======== -->
        <section id="profil" aria-labelledby="titre-profil">
            <div class="section-container">
                <h2 id="titre-profil">👤 Mon Profil</h2>

                <form class="profil-form" method="post" action="espace-membre.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">

                    <!-- Avatar -->
                    <div class="form-group form-group-avatar">
                        <label>Photo de profil</label>
                        <div class="avatar-upload">
                            <div class="avatar-preview-wrapper">
                                <div class="avatar-preview">
                                    <span class="avatar-preview-placeholder" id="avatar-placeholder">👤</span>
                                    <img id="avatar-preview-img" src="<?= !empty($profil['avatar']) ? '../' . htmlspecialchars($profil['avatar']) : '' ?>" alt="Aperçu avatar" class="<?= empty($profil['avatar']) ? 'hidden' : '' ?>" loading="lazy">
                                </div>
                            </div>
                            <div class="avatar-upload-actions">
                                <label for="avatar-file" class="btn btn-outline">📷 Changer la photo</label>
                                <input type="file" id="avatar-file" name="avatar" accept="image/png, image/jpeg, image/webp" class="sr-only">
                                <small class="form-help">JPG, PNG ou WebP. Max 2 Mo.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profil-pseudo">Pseudo</label>
                        <input type="text" id="profil-pseudo" name="pseudo" placeholder="Ton pseudo" minlength="3" maxlength="20" value="<?= htmlspecialchars($profil['pseudo'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="profil-email">Email</label>
                        <input type="email" id="profil-email" name="email" placeholder="ton.email@campus.fr" value="<?= htmlspecialchars($profil['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="profil-jeu">Jeu principal</label>
                        <select id="profil-jeu" name="jeu_principal">
                            <option value="">— Choisir —</option>
                            <option value="lol" <?= (($profil['jeu_principal'] ?? '') === 'lol') ? 'selected' : '' ?>>League of Legends</option>
                            <option value="valorant" <?= (($profil['jeu_principal'] ?? '') === 'valorant') ? 'selected' : '' ?>>Valorant</option>
                            <option value="cs2" <?= (($profil['jeu_principal'] ?? '') === 'cs2') ? 'selected' : '' ?>>Counter-Strike 2</option>
                            <option value="fortnite" <?= (($profil['jeu_principal'] ?? '') === 'fortnite') ? 'selected' : '' ?>>Fortnite</option>
                            <option value="rocket-league" <?= (($profil['jeu_principal'] ?? '') === 'rocket-league') ? 'selected' : '' ?>>Rocket League</option>
                            <option value="autre" <?= (($profil['jeu_principal'] ?? '') === 'autre') ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                </form>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
