<?php
require_once __DIR__ . '/../assets/php/config/db.php';

$rows = [];
$dbError = '';

$jeuLabels = [
    'lol' => 'League of Legends',
    'valorant' => 'Valorant',
    'cs2' => 'CS2',
    'fortnite' => 'Fortnite',
    'rocket-league' => 'Rocket League',
    'autre' => 'Autre',
];

try {
    $stmt = $pdo->query('SELECT
                            u.id,
                            u.pseudo,
                            u.avatar,
                            u.jeu_principal,
                            (SELECT r2.nom_equipe FROM reservations r2 WHERE r2.capitaine_id = u.id ORDER BY r2.created_at DESC LIMIT 1) AS nom_equipe,
                            COUNT(r.id) AS matchs_joues,
                            SUM(CASE WHEN r.statut = "confirmee" THEN 1 ELSE 0 END) AS victoires,
                            SUM(CASE WHEN r.statut = "annulee" THEN 1 ELSE 0 END) AS defaites,
                            SUM(CASE WHEN r.statut = "confirmee" THEN 10 WHEN r.statut = "en-attente" THEN 3 ELSE 0 END) AS points
                        FROM utilisateurs u
                        LEFT JOIN reservations r ON r.capitaine_id = u.id
                        WHERE u.role <> "admin"
                        GROUP BY u.id
                        ORDER BY points DESC, victoires DESC, matchs_joues DESC, u.created_at ASC');
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[CLASSEMENT LIST] ' . $e->getMessage());
    $dbError = 'Impossible de charger le classement.';
}

$top = array_slice($rows, 0, 3);
$rootPath        = '../';
$pageTitle       = 'Classement - Gaming Campus';
$metaDescription = 'Classement général des joueurs et équipes. Top joueurs, statistiques et badges de rang.';
$cssSpecifique   = 'classement.css';
$jsSupplementaires = ['classement.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête classement">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Classement</li>
                    </ol>
                </nav>
                <h1>🏆 Classement Général</h1>
                <p class="page-description">Le leaderboard du campus. Consulte les meilleurs joueurs, leurs stats et leur progression.</p>
            </div>
        </section>

        <!-- ======== PODIUM TOP 3 ======== -->
        <section id="podium" aria-labelledby="titre-podium">
            <div class="section-container">
                <h2 id="titre-podium">🏅 Podium</h2>

                <div class="podium-grid">
                    <?php
                        $podiumOrder = [1, 0, 2];
                        $podiumMedals = ['🥇', '🥈', '🥉'];
                        $podiumClasses = ['podium-gold', 'podium-silver', 'podium-bronze'];
                    ?>
                    <?php foreach ($podiumOrder as $idx): ?>
                    <?php if (isset($top[$idx])): ?>
                    <?php
                        $j = $top[$idx];
                        $matchs = (int) $j['matchs_joues'];
                        $wins = (int) $j['victoires'];
                        $points = (int) $j['points'];
                    ?>
                    <article class="podium-card <?= $podiumClasses[$idx] ?>">
                        <span class="podium-rank"><?= $podiumMedals[$idx] ?></span>
                        <?php if (!empty($j['avatar'])): ?>
                        <img class="podium-avatar" src="../<?= htmlspecialchars($j['avatar']) ?>" alt="Avatar <?= htmlspecialchars($j['pseudo']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="podium-avatar podium-avatar-empty" aria-hidden="true">
                            <span class="avatar-placeholder-icon">👤</span>
                        </div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($j['pseudo']) ?></h3>
                        <p class="podium-team"><?= htmlspecialchars((string) ($j['nom_equipe'] ?: 'Aucune équipe')) ?></p>
                        <div class="podium-stats">
                            <span class="stat"><strong><?= $wins ?></strong> victoires</span>
                            <span class="stat"><strong><?= $points ?></strong> points</span>
                        </div>
                    </article>
                    <?php else: ?>
                    <!-- 2ème place -->
                    <article class="podium-card <?= $podiumClasses[$idx] ?>">
                        <span class="podium-rank"><?= $podiumMedals[$idx] ?></span>
                        <div class="podium-avatar podium-avatar-empty" aria-hidden="true">
                            <span class="avatar-placeholder-icon">👤</span>
                        </div>
                        <h3 class="podium-name-placeholder">—</h3>
                        <p class="podium-team podium-team-placeholder">Aucune équipe</p>
                        <div class="podium-stats">
                            <span class="stat"><strong>—</strong> victoires</span>
                            <span class="stat"><strong>—</strong> points</span>
                        </div>
                    </article>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ======== CLASSEMENT COMPLET ======== -->
        <section id="classement-complet" aria-labelledby="titre-classement-complet">
            <div class="section-container">
                <h2 id="titre-classement-complet">📊 Classement Complet</h2>

                <!-- Filtres de classement -->
                <div class="classement-filters">
                    <div class="filter-tabs" role="tablist" aria-label="Filtrer par jeu">
                        <button role="tab" aria-selected="true" class="tab-btn active">Tous les jeux</button>
                        <button role="tab" aria-selected="false" class="tab-btn">League of Legends</button>
                        <button role="tab" aria-selected="false" class="tab-btn">Valorant</button>
                        <button role="tab" aria-selected="false" class="tab-btn">CS2</button>
                    </div>
                </div>

                <!-- Tableau complet - données chargées depuis la base de données -->
                <div class="table-responsive">
                    <table id="leaderboard-table" class="leaderboard-table leaderboard-full" aria-label="Classement complet des joueurs">
                        <thead>
                            <tr>
                                <th scope="col" class="sortable" data-col="0" data-type="number" aria-sort="ascending">
                                    Rang <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col">Joueur</th>
                                <th scope="col" class="sortable" data-col="2" data-type="text">
                                    Équipe <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="3" data-type="text">
                                    Jeu principal <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="4" data-type="number">
                                    Matchs joués <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="5" data-type="number">
                                    Victoires <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="6" data-type="number">
                                    Défaites <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="7" data-type="percent">
                                    Win Rate <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                                <th scope="col" class="sortable" data-col="8" data-type="number">
                                    Points <span class="sort-icon" aria-hidden="true">↕</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-body">
                            <?php if ($dbError !== ''): ?>
                            <tr class="empty-state-row">
                                <td colspan="9" class="empty-state-cell">⚠️ <?= htmlspecialchars($dbError) ?></td>
                            </tr>
                            <?php elseif (!empty($rows)): ?>
                            <?php foreach ($rows as $index => $row): ?>
                            <?php
                                $matchs = (int) $row['matchs_joues'];
                                $wins = (int) $row['victoires'];
                                $loss = (int) $row['defaites'];
                                $points = (int) $row['points'];
                                $winRate = $matchs > 0 ? (int) round(($wins / $matchs) * 100) : 0;
                                $jeu = $jeuLabels[$row['jeu_principal'] ?? ''] ?? 'Non défini';
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($row['pseudo']) ?></td>
                                <td><?= htmlspecialchars((string) ($row['nom_equipe'] ?: 'Aucune')) ?></td>
                                <td><?= htmlspecialchars($jeu) ?></td>
                                <td><?= $matchs ?></td>
                                <td><?= $wins ?></td>
                                <td><?= $loss ?></td>
                                <td><?= $winRate ?>%</td>
                                <td><?= $points ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="empty-state-row">
                                <td colspan="9" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">🏆</span>
                                        <p>Aucun joueur classé pour le moment.</p>
                                        <p class="empty-state-sub">Le classement se mettra à jour automatiquement après les premiers tournois.</p>
                                        <a href="inscription.php" class="btn btn-primary btn-sm">Créer mon compte</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
