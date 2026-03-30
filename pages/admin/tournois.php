<?php
require_once __DIR__ . '/../../assets/php/config/auth.php';
gc_require_login('../../pages/connexion.php');

require_once __DIR__ . '/../../assets/php/config/db.php';

$messageSucces = '';
$messageErreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    } else {
        $action = trim($_POST['action'] ?? 'create');

        if ($action === 'delete') {
            $deleteId = (int) ($_POST['id'] ?? 0);
            if ($deleteId > 0) {
                try {
                    $stmt = $pdo->prepare('DELETE FROM tournois WHERE id = ?');
                    $stmt->execute([$deleteId]);
                    $messageSucces = 'Tournoi supprimé.';
                } catch (PDOException $e) {
                    error_log('[ADMIN TOURNOIS DELETE] ' . $e->getMessage());
                    $messageErreur = 'Impossible de supprimer ce tournoi.';
                }
            }
        } elseif ($action === 'update') {
            $tournoiId = (int) ($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $jeu = trim($_POST['jeu'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $places = (int) ($_POST['places'] ?? 0);
            $lieu = trim($_POST['lieu'] ?? 'Campus');
            $cashprize = (float) ($_POST['cashprize'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $statut = trim($_POST['statut'] ?? 'a-venir');
            $dateSql = str_replace('T', ' ', $date) . ':00';

            $jeuxAutorises = ['lol', 'valorant', 'cs2', 'fortnite', 'rocket-league'];
            $statutsAutorises = ['a-venir', 'en-cours', 'termine'];

            if (
                $tournoiId <= 0
                || $nom === ''
                || !in_array($jeu, $jeuxAutorises, true)
                || $date === ''
                || strtotime($dateSql) === false
                || $places < 2
                || $places > 128
                || $cashprize < 0
                || !in_array($statut, $statutsAutorises, true)
            ) {
                $messageErreur = 'Données invalides pour la mise à jour du tournoi.';
            } else {
                try {
                    $stmt = $pdo->prepare('UPDATE tournois
                                           SET nom = :nom,
                                               jeu = :jeu,
                                               date_debut = :date_debut,
                                               lieu = :lieu,
                                               nb_places = :nb_places,
                                               cashprize = :cashprize,
                                               description = :description,
                                               statut = :statut
                                           WHERE id = :id');
                    $stmt->execute([
                        ':nom' => $nom,
                        ':jeu' => $jeu,
                        ':date_debut' => $dateSql,
                        ':lieu' => $lieu !== '' ? $lieu : 'Campus',
                        ':nb_places' => $places,
                        ':cashprize' => $cashprize,
                        ':description' => $description,
                        ':statut' => $statut,
                        ':id' => $tournoiId,
                    ]);
                    $messageSucces = 'Tournoi mis à jour avec succès.';
                } catch (PDOException $e) {
                    error_log('[ADMIN TOURNOIS UPDATE] ' . $e->getMessage());
                    $messageErreur = 'Impossible de mettre à jour ce tournoi.';
                }
            }
        } else {
            $nom = trim($_POST['nom'] ?? '');
            $jeu = trim($_POST['jeu'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $places = (int) ($_POST['places'] ?? 0);
            $lieu = trim($_POST['lieu'] ?? 'Campus');
            $cashprize = (float) ($_POST['cashprize'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $dateSql = str_replace('T', ' ', $date) . ':00';
            $dateTs = strtotime($dateSql);

            if ($nom === '' || $jeu === '' || $date === '' || $places < 2 || $places > 128 || $cashprize < 0) {
                $messageErreur = 'Tous les champs obligatoires doivent être valides.';
            } elseif ($dateTs === false || $dateTs < time()) {
                $messageErreur = 'La date du tournoi doit être dans le futur.';
            } else {
                try {
                    $stmt = $pdo->prepare('INSERT INTO tournois (nom, jeu, date_debut, lieu, nb_places, cashprize, description, statut)
                                           VALUES (:nom, :jeu, :date_debut, :lieu, :nb_places, :cashprize, :description, "a-venir")');
                    $stmt->execute([
                        ':nom' => $nom,
                        ':jeu' => $jeu,
                        ':date_debut' => $dateSql,
                        ':lieu' => $lieu !== '' ? $lieu : 'Campus',
                        ':nb_places' => $places,
                        ':cashprize' => $cashprize,
                        ':description' => $description,
                    ]);
                    $messageSucces = 'Tournoi créé avec succès.';
                } catch (PDOException $e) {
                    error_log('[ADMIN TOURNOIS CREATE] ' . $e->getMessage());
                    $messageErreur = 'Impossible de créer le tournoi.';
                }
            }
        }
    }
}

$tournois = [];
try {
    $stmt = $pdo->query('SELECT id, nom, jeu, date_debut, lieu, nb_places, cashprize, description, statut FROM tournois ORDER BY date_debut DESC');
    $tournois = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[ADMIN TOURNOIS LIST] ' . $e->getMessage());
}

$rootPath        = '../../';
$pageTitle       = 'Gestion Tournois - Admin - Gaming Campus';
$metaDescription = 'Administration des tournois Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'tournois';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>🎮 Gestion des Tournois</h1>
            </div>

            <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
            <?php endif; ?>
            <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <section id="ajouter-tournoi" aria-labelledby="titre-ajout-tournoi">
                <h2 id="titre-ajout-tournoi">➕ Ajouter un tournoi</h2>
                <form class="admin-form" method="post" action="tournois.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                    <input type="hidden" name="action" value="create">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-nom">Nom du tournoi <span class="required">*</span></label>
                            <input type="text" id="tournoi-nom" name="nom" required placeholder="Ex: Rift Rivals Season 2">
                        </div>
                        <div class="form-group">
                            <label for="tournoi-jeu">Jeu <span class="required">*</span></label>
                            <select id="tournoi-jeu" name="jeu" required>
                                <option value="" disabled selected>Choisir un jeu</option>
                                <option value="lol">League of Legends</option>
                                <option value="valorant">Valorant</option>
                                <option value="cs2">Counter-Strike 2</option>
                                <option value="fortnite">Fortnite</option>
                                <option value="rocket-league">Rocket League</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-date">Date <span class="required">*</span></label>
                            <input type="datetime-local" id="tournoi-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="tournoi-places">Nombre de places <span class="required">*</span></label>
                            <input type="number" id="tournoi-places" name="places" required min="2" max="128" placeholder="Ex: 16">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-lieu">Lieu</label>
                            <input type="text" id="tournoi-lieu" name="lieu" placeholder="Ex: Salle E-sport - Bâtiment A">
                        </div>
                        <div class="form-group">
                            <label for="tournoi-cashprize">Cashprize (€)</label>
                            <input type="number" id="tournoi-cashprize" name="cashprize" min="0" placeholder="Ex: 500">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tournoi-description">Description</label>
                        <textarea id="tournoi-description" name="description" rows="4" placeholder="Description du tournoi, règles spécifiques, format..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Créer le tournoi</button>
                </form>
            </section>

            <!-- Liste des tournois -->
            <section id="liste-tournois-admin" aria-labelledby="titre-liste-admin">
                <h2 id="titre-liste-admin">📋 Tournois existants</h2>
                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des tournois">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Jeu</th>
                                <th scope="col">Date</th>
                                <th scope="col">Places</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tournois)): ?>
                            <?php foreach ($tournois as $tournoi): ?>
                            <tr>
                                <td><?= (int) $tournoi['id'] ?></td>
                                <td><?= htmlspecialchars($tournoi['nom']) ?></td>
                                <td><?= htmlspecialchars($tournoi['jeu']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $tournoi['date_debut']))) ?></td>
                                <td><?= (int) $tournoi['nb_places'] ?></td>
                                <td><span class="status-badge status-<?= htmlspecialchars($tournoi['statut']) ?>"><?= htmlspecialchars($tournoi['statut']) ?></span></td>
                                <td>
                                    <details class="admin-inline-edit">
                                        <summary class="btn btn-outline btn-sm">Modifier</summary>
                                        <form method="post" action="tournois.php" class="admin-edit-form">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= (int) $tournoi['id'] ?>">

                                            <label>Nom</label>
                                            <input type="text" name="nom" required value="<?= htmlspecialchars($tournoi['nom']) ?>">

                                            <label>Jeu</label>
                                            <select name="jeu" required>
                                                <option value="lol" <?= $tournoi['jeu'] === 'lol' ? 'selected' : '' ?>>League of Legends</option>
                                                <option value="valorant" <?= $tournoi['jeu'] === 'valorant' ? 'selected' : '' ?>>Valorant</option>
                                                <option value="cs2" <?= $tournoi['jeu'] === 'cs2' ? 'selected' : '' ?>>Counter-Strike 2</option>
                                                <option value="fortnite" <?= $tournoi['jeu'] === 'fortnite' ? 'selected' : '' ?>>Fortnite</option>
                                                <option value="rocket-league" <?= $tournoi['jeu'] === 'rocket-league' ? 'selected' : '' ?>>Rocket League</option>
                                            </select>

                                            <label>Date</label>
                                            <input type="datetime-local" name="date" required value="<?= htmlspecialchars(date('Y-m-d\\TH:i', strtotime((string) $tournoi['date_debut']))) ?>">

                                            <label>Lieu</label>
                                            <input type="text" name="lieu" value="<?= htmlspecialchars((string) ($tournoi['lieu'] ?? 'Campus')) ?>">

                                            <label>Places</label>
                                            <input type="number" name="places" min="2" max="128" required value="<?= (int) $tournoi['nb_places'] ?>">

                                            <label>Cashprize (€)</label>
                                            <input type="number" name="cashprize" min="0" step="0.01" value="<?= htmlspecialchars((string) ($tournoi['cashprize'] ?? 0)) ?>">

                                            <label>Statut</label>
                                            <select name="statut" required>
                                                <option value="a-venir" <?= $tournoi['statut'] === 'a-venir' ? 'selected' : '' ?>>A venir</option>
                                                <option value="en-cours" <?= $tournoi['statut'] === 'en-cours' ? 'selected' : '' ?>>En cours</option>
                                                <option value="termine" <?= $tournoi['statut'] === 'termine' ? 'selected' : '' ?>>Termine</option>
                                            </select>

                                            <label>Description</label>
                                            <textarea name="description" rows="3" placeholder="Description du tournoi"><?= htmlspecialchars((string) ($tournoi['description'] ?? '')) ?></textarea>

                                            <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                                        </form>
                                    </details>
                                    <form method="post" action="tournois.php" onsubmit="return confirm('Supprimer ce tournoi ?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $tournoi['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="empty-state-row">
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">🎮</span>
                                        <p>Aucun tournoi créé pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

    </main>

<?php include '../../assets/php/components/footer-admin.php'; ?>
