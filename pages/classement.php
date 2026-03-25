<?php
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

                <!-- Le podium sera rempli automatiquement depuis la base de données -->
                <div class="podium-grid">
                    <!-- 2ème place -->
                    <article class="podium-card podium-silver">
                        <span class="podium-rank">🥈</span>
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

                    <!-- 1ère place -->
                    <article class="podium-card podium-gold">
                        <span class="podium-rank">🥇</span>
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

                    <!-- 3ème place -->
                    <article class="podium-card podium-bronze">
                        <span class="podium-rank">🥉</span>
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
                </div>

                <p class="empty-state-note">🔒 Le podium sera mis à jour automatiquement dès que des joueurs s'inscrivent et participent aux tournois.</p>
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
                            <!-- Les joueurs seront affichés ici une fois inscrits et ayant participé à des tournois -->
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
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="pagination" aria-label="Pagination du classement">
                    <ul>
                        <li><a href="#" class="pagination-link active" aria-current="page">1</a></li>
                        <li><a href="#" class="pagination-link">2</a></li>
                        <li><a href="#" class="pagination-link">3</a></li>
                        <li><a href="#" class="pagination-link pagination-next" aria-label="Page suivante">→</a></li>
                    </ul>
                </nav>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
