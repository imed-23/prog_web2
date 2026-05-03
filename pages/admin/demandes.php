<?php
require_once __DIR__ . '/../../assets/php/config/db.php';

$rootPath        = '../../';
$adminActivePage = 'demandes';
$pageTitle       = 'Demandes Capitaines — Admin';
$metaDescription = 'Gestion des demandes de statut capitaine — Gaming Campus';
$cssSpecifique   = 'admin.css';

$messageSucces = '';
$messageErreur = '';

// traitement POST (approuver / refuser)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once $rootPath . 'assets/php/config/auth.php';
    gc_require_admin($rootPath . 'pages/connexion.php');

    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page.';
    } else {
        $demandeId = (int) ($_POST['demande_id'] ?? 0);
        $actionDem = trim($_POST['action_dem'] ?? '');

        if ($demandeId > 0 && in_array($actionDem, ['approuver', 'refuser'], true)) {
            try {
                if ($actionDem === 'approuver') {
                    $stmtGet = $pdo->prepare('SELECT user_id FROM demandes_capitaine WHERE id = ? LIMIT 1');
                    $stmtGet->execute([$demandeId]);
                    $dem = $stmtGet->fetch();

                    if ($dem) {
                        // Promouvoir l'utilisateur en capitaine
                        $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?')
                            ->execute(['capitaine', $dem['user_id']]);
                        // Marquer la demande comme approuvée
                        $pdo->prepare('UPDATE demandes_capitaine SET statut = ? WHERE id = ?')
                            ->execute(['approuvee', $demandeId]);
                        $messageSucces = '✅ Utilisateur promu Capitaine avec succès.';
                    }
                } else {
                    $pdo->prepare('UPDATE demandes_capitaine SET statut = ? WHERE id = ?')
                        ->execute(['refusee', $demandeId]);
                    $messageSucces = '❌ Demande refusée.';
                }
            } catch (PDOException $e) {
                error_log('[ADMIN DEMANDES] ' . $e->getMessage());
                $messageErreur = 'Erreur lors du traitement. Réessaie.';
            }
        }
    }
}

require_once $rootPath . 'assets/php/config/auth.php';
gc_require_admin($rootPath . 'pages/connexion.php');

// chargement des demandes
$demandes   = [];
$nbAttente  = 0;
try {
    $stmt = $pdo->prepare(
        "SELECT d.id, d.nom_equipe, d.jeu, d.message, d.statut, d.created_at,
                u.id AS user_id, u.pseudo, u.email, u.role
         FROM demandes_capitaine d
         INNER JOIN utilisateurs u ON u.id = d.user_id
         ORDER BY
             CASE d.statut WHEN 'en-attente' THEN 0 WHEN 'refusee' THEN 1 ELSE 2 END,
             d.created_at DESC"
    );
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($demandes as $d) {
        if ($d['statut'] === 'en-attente') $nbAttente++;
    }
} catch (PDOException $e) {
    error_log('[ADMIN DEMANDES LOAD] ' . $e->getMessage());
    $messageErreur = 'Impossible de charger les demandes.';
}

$jeuLabels = [
    'lol'          => 'League of Legends',
    'valorant'     => 'Valorant',
    'cs2'          => 'Counter-Strike 2',
    'fortnite'     => 'Fortnite',
    'rocket-league'=> 'Rocket League',
];

include $rootPath . 'assets/php/components/header-admin.php';
?>
    <main id="main-content">

        <!-- En-tête de page -->
        <div class="admin-container">
            <div class="admin-page-header">
                <h1>🏆 Demandes de statut Capitaine</h1>
                <p>Valide ou refuse les demandes des visiteurs souhaitant devenir capitaine d'équipe.</p>
            </div>

            <!-- Stat résumé -->
            <div class="admin-stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.5rem;">
                <div class="admin-stat-card">
                    <span class="admin-stat-icon">📋</span>
                    <div class="admin-stat-info">
                        <span class="admin-stat-value"><?= count($demandes) ?></span>
                        <span class="admin-stat-label">Total demandes</span>
                    </div>
                </div>
                <div class="admin-stat-card" style="border-left:4px solid #f39c12;">
                    <span class="admin-stat-icon">⏳</span>
                    <div class="admin-stat-info">
                        <span class="admin-stat-value" style="color:#f39c12;"><?= $nbAttente ?></span>
                        <span class="admin-stat-label">En attente</span>
                    </div>
                </div>
                <div class="admin-stat-card" style="border-left:4px solid #2ecc71;">
                    <span class="admin-stat-icon">✅</span>
                    <div class="admin-stat-info">
                        <span class="admin-stat-value" style="color:#2ecc71;"><?= count($demandes) - $nbAttente ?></span>
                        <span class="admin-stat-label">Traitées</span>
                    </div>
                </div>
            </div>

            <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success" style="margin-bottom:1rem;"><?= htmlspecialchars($messageSucces) ?></div>
            <?php endif; ?>
            <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-error" style="margin-bottom:1rem;">❌ <?= htmlspecialchars($messageErreur) ?></div>
            <?php endif; ?>

            <!-- Tableau des demandes -->
            <div class="admin-section-header">
                <h2>📋 Liste des demandes</h2>
                <?php if ($nbAttente > 0): ?>
                <span class="admin-count-badge"><?= $nbAttente ?> en attente</span>
                <?php endif; ?>
            </div>

            <?php if (empty($demandes)): ?>
            <div class="empty-state">
                <span class="empty-state-icon">📭</span>
                <p>Aucune demande pour le moment.</p>
                <p class="empty-state-sub">Les visiteurs pourront demander le statut capitaine depuis leur espace membre.</p>
            </div>

            <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table" aria-label="Demandes capitaines">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Équipe demandée</th>
                            <th>Jeu</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th style="min-width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($demandes as $dem): ?>
                    <?php
                        $statut    = (string) $dem['statut'];
                        $isAttente = ($statut === 'en-attente');
                    ?>
                    <tr style="<?= $isAttente ? 'background:rgba(243,156,18,0.06);' : '' ?>">
                        <td>
                            <strong><?= htmlspecialchars($dem['pseudo']) ?></strong><br>
                            <small style="color:var(--text-muted,#888);font-size:.8em;"><?= htmlspecialchars($dem['email']) ?></small><br>
                            <small style="color:var(--text-muted,#888);font-size:.75em;">rôle actuel : <em><?= htmlspecialchars($dem['role']) ?></em></small>
                        </td>
                        <td><strong><?= htmlspecialchars($dem['nom_equipe']) ?></strong></td>
                        <td><?= htmlspecialchars($jeuLabels[$dem['jeu']] ?? $dem['jeu']) ?></td>
                        <td style="max-width:180px;white-space:normal;font-size:.85rem;">
                            <?= !empty($dem['message'])
                                ? htmlspecialchars(strlen((string)$dem['message']) > 80 ? substr((string)$dem['message'], 0, 80) . '…' : (string)$dem['message'])
                                : '<em style="color:var(--text-muted)">—</em>' ?>
                        </td>
                        <td style="white-space:nowrap;"><?= htmlspecialchars(date('d/m/Y', strtotime((string)$dem['created_at']))) ?></td>
                        <td>
                            <?php
                            $badgeColors = [
                                'en-attente' => ['bg' => 'rgba(243,156,18,.15)', 'color' => '#f39c12', 'border' => 'rgba(243,156,18,.4)', 'label' => '⏳ En attente'],
                                'approuvee'  => ['bg' => 'rgba(39,174,96,.15)',  'color' => '#2ecc71', 'border' => 'rgba(39,174,96,.4)',  'label' => '✅ Approuvée'],
                                'refusee'    => ['bg' => 'rgba(231,76,60,.15)',  'color' => '#e74c3c', 'border' => 'rgba(231,76,60,.4)',  'label' => '❌ Refusée'],
                            ];
                            $bc = $badgeColors[$statut] ?? ['bg' => 'rgba(128,128,128,.15)', 'color' => '#999', 'border' => 'rgba(128,128,128,.4)', 'label' => htmlspecialchars($statut)];
                            ?>
                            <span style="display:inline-block;padding:.25rem .65rem;border-radius:999px;font-size:.75rem;font-weight:700;background:<?= $bc['bg'] ?>;color:<?= $bc['color'] ?>;border:1px solid <?= $bc['border'] ?>;">
                                <?= $bc['label'] ?>
                            </span>
                        </td>
                        <td>
                        <?php if ($isAttente): ?>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                <!-- Bouton APPROUVER -->
                                <form method="post" action="demandes.php" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                    <input type="hidden" name="demande_id" value="<?= (int)$dem['id'] ?>">
                                    <input type="hidden" name="action_dem" value="approuver">
                                    <button type="submit"
                                            style="padding:.4rem .9rem;font-size:.82rem;font-weight:700;border:none;border-radius:6px;cursor:pointer;background:#2ecc71;color:#fff;"
                                            onclick="return confirm('Promouvoir <?= htmlspecialchars(addslashes($dem['pseudo'])) ?> en Capitaine ?')">
                                        ✅ Approuver
                                    </button>
                                </form>
                                <!-- Bouton REFUSER -->
                                <form method="post" action="demandes.php" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                    <input type="hidden" name="demande_id" value="<?= (int)$dem['id'] ?>">
                                    <input type="hidden" name="action_dem" value="refuser">
                                    <button type="submit"
                                            style="padding:.4rem .9rem;font-size:.82rem;font-weight:700;border:2px solid #e74c3c;border-radius:6px;cursor:pointer;background:transparent;color:#e74c3c;"
                                            onclick="return confirm('Refuser la demande de <?= htmlspecialchars(addslashes($dem['pseudo'])) ?> ?')">
                                        ❌ Refuser
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <span style="color:var(--text-muted,#888);font-size:.85rem;">Traité</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
    </main>

<?php include $rootPath . 'assets/php/components/footer-admin.php'; ?>
