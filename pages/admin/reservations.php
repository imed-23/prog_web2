<?php
require_once __DIR__ . '/../../assets/php/config/auth.php';
gc_require_login('../../pages/connexion.php');

require_once __DIR__ . '/../../assets/php/config/db.php';

$messageSucces = '';
$messageErreur = '';
$filterStatut = trim($_GET['statut'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_status'], $_POST['id'])) {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    }

    $reservationId = (int) $_POST['id'];
    $newStatus = trim($_POST['set_status']);
    $allowed = ['en-attente', 'confirmee', 'annulee'];

    if ($messageErreur === '' && $reservationId > 0 && in_array($newStatus, $allowed, true)) {
        try {
            $stmt = $pdo->prepare('UPDATE reservations SET statut = ? WHERE id = ?');
            $stmt->execute([$newStatus, $reservationId]);
            $messageSucces = 'Statut mis à jour.';
        } catch (PDOException $e) {
            error_log('[ADMIN RESERVATIONS UPDATE] ' . $e->getMessage());
            $messageErreur = 'Impossible de mettre à jour le statut.';
        }
    }
}

$stats = [
    'total' => 0,
    'confirmee' => 0,
    'en-attente' => 0,
    'annulee' => 0,
];

try {
    $stats['total'] = (int) $pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn();
    $stats['confirmee'] = (int) $pdo->query('SELECT COUNT(*) FROM reservations WHERE statut = "confirmee"')->fetchColumn();
    $stats['en-attente'] = (int) $pdo->query('SELECT COUNT(*) FROM reservations WHERE statut = "en-attente"')->fetchColumn();
    $stats['annulee'] = (int) $pdo->query('SELECT COUNT(*) FROM reservations WHERE statut = "annulee"')->fetchColumn();
} catch (PDOException $e) {
    error_log('[ADMIN RESERVATIONS STATS] ' . $e->getMessage());
}

$reservations = [];
try {
    $sql = 'SELECT r.id, r.nom_equipe, r.created_at, r.statut, t.nom AS tournoi_nom, u.pseudo AS capitaine_pseudo
            FROM reservations r
            INNER JOIN tournois t ON t.id = r.tournoi_id
            INNER JOIN utilisateurs u ON u.id = r.capitaine_id
            WHERE 1=1';
    $params = [];
    if (in_array($filterStatut, ['en-attente', 'confirmee', 'annulee'], true)) {
        $sql .= ' AND r.statut = :statut';
        $params[':statut'] = $filterStatut;
    } else {
        $filterStatut = '';
    }
    $sql .= ' ORDER BY r.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[ADMIN RESERVATIONS LIST] ' . $e->getMessage());
    $messageErreur = $messageErreur !== '' ? $messageErreur : 'Impossible de charger les réservations.';
}

$rootPath        = '../../';
$pageTitle       = 'Gestion Réservations - Admin - Gaming Campus';
$metaDescription = 'Administration des réservations Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'reservations';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>📋 Gestion des Réservations</h1>
            </div>

            <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
            <?php endif; ?>
            <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
            <?php endif; ?>

            <!-- Résumé -->
            <section aria-labelledby="titre-resume-reservations">
                <h2 id="titre-resume-reservations">📊 Résumé</h2>
                <div class="admin-stats-grid">
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">📋</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['total'] ?></span>
                            <span class="admin-stat-label">Total réservations</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">✅</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['confirmee'] ?></span>
                            <span class="admin-stat-label">Confirmées</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">⌛</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['en-attente'] ?></span>
                            <span class="admin-stat-label">En attente</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">❌</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['annulee'] ?></span>
                            <span class="admin-stat-label">Annulées</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tableau des réservations -->
            <section id="liste-reservations" aria-labelledby="titre-reservations">
                <div class="admin-section-header">
                    <h2 id="titre-reservations">📋 Toutes les réservations</h2>
                    <div class="admin-filters">
                        <form method="get" action="reservations.php">
                            <select name="statut" onchange="this.form.submit()">
                                <option value="" <?= $filterStatut === '' ? 'selected' : '' ?>>Tous les statuts</option>
                                <option value="confirmee" <?= $filterStatut === 'confirmee' ? 'selected' : '' ?>>Confirmées</option>
                                <option value="en-attente" <?= $filterStatut === 'en-attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="annulee" <?= $filterStatut === 'annulee' ? 'selected' : '' ?>>Annulées</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des réservations">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Équipe</th>
                                <th scope="col">Tournoi</th>
                                <th scope="col">Capitaine</th>
                                <th scope="col">Date inscription</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reservations)): ?>
                            <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><?= (int) $r['id'] ?></td>
                                <td><?= htmlspecialchars($r['nom_equipe']) ?></td>
                                <td><?= htmlspecialchars($r['tournoi_nom']) ?></td>
                                <td><?= htmlspecialchars($r['capitaine_pseudo']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $r['created_at']))) ?></td>
                                <td><span class="status-badge status-<?= htmlspecialchars($r['statut']) ?>"><?= htmlspecialchars($r['statut']) ?></span></td>
                                <td class="actions-cell">
                                    <form method="post" action="reservations.php" style="display:inline-block;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                        <input type="hidden" name="set_status" value="confirmee">
                                        <button type="submit" class="btn btn-outline btn-sm">Confirmer</button>
                                    </form>
                                    <form method="post" action="reservations.php" style="display:inline-block;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                        <input type="hidden" name="set_status" value="annulee">
                                        <button type="submit" class="btn btn-outline btn-sm">Annuler</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="empty-state-row">
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">📋</span>
                                        <p>Aucune réservation pour le moment.</p>
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
