<?php
require_once __DIR__ . '/assets/php/config/auth.php';
gc_start_session();
$currentUser = gc_current_user();

$rootPath        = '';
$pageTitle       = 'Gaming Campus - Plateforme de Tournois';
$metaDescription = 'Plateforme de Tournois Gaming Campus - Consultez les tournois, inscrivez votre équipe et suivez les classements.';
$cssSpecifique   = 'index.css';
include 'assets/php/components/header.php';
?>

    <!-- ============================================ -->
    <!-- CONTENU PRINCIPAL -->
    <!-- ============================================ -->
    <main id="main-content">

        <!-- ======== SECTION HERO ======== -->
        <section id="hero" class="hero-section" aria-label="Bannière principale">
            <div class="hero-content">
                <h1>Rejoins la compétition <span class="text-accent">Gaming Campus</span></h1>
                <p class="hero-subtitle">Consulte les tournois à venir, inscris ton équipe et grimpe dans le classement. La prochaine victoire est à portée de clic.</p>
                <div class="hero-actions">
                    <a href="pages/tournois.php" class="btn btn-primary btn-lg">Voir les Tournois</a>
                    <?php if ($currentUser): ?>
                    <a href="pages/espace-membre.php" class="btn btn-outline btn-lg">Voir mon compte</a>
                    <?php else: ?>
                    <a href="pages/inscription.php" class="btn btn-outline btn-lg">Créer un Compte</a>
                    <?php endif; ?>
                </div>
                <!-- Statistiques dynamiques — seront générées par PHP/MySQL en Sprint 4 -->
                <!-- <div class="hero-stats"> ... </div> -->
            </div>
        </section>

        <!-- ======== SECTION TOURNOIS EN COURS ======== -->
        <section id="tournois-en-cours" aria-labelledby="titre-tournois-en-cours">
            <div class="section-container">
                <div class="section-header">
                    <h2 id="titre-tournois-en-cours">🎮 Tournois en Cours</h2>
                    <a href="pages/tournois.php" class="section-link">Voir tous les tournois →</a>
                </div>

                <!-- Tournois en cours — seront chargés depuis la base de données en Sprint 4 (PHP/MySQL) -->
                <div class="empty-state">
                    <span class="empty-state-icon">🎮</span>
                    <p>Aucun tournoi en cours pour le moment.</p>
                    <p class="empty-state-sub"><a href="pages/tournois.php">Voir tous les tournois</a></p>
                </div>
            </div>
        </section>

        <!-- ======== SECTION PROCHAINS MATCHS ======== -->
        <section id="prochains-matchs" aria-labelledby="titre-prochains-matchs">
            <div class="section-container">
                <h2 id="titre-prochains-matchs">⏱️ Prochains Matchs</h2>

                <div class="matchs-list" id="liste-matchs">
                    <!-- Les matchs seront affichés ici une fois les équipes inscrites -->
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">🎮</span>
                        <p>Aucun match à venir pour le moment.</p>
                        <p class="empty-state-sub"><a href="pages/tournois.php">Voir les tournois ouverts</a></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== SECTION CLASSEMENT APERÇU ======== -->
        <section id="classement-apercu" aria-labelledby="titre-classement">
            <div class="section-container">
                <div class="section-header">
                    <h2 id="titre-classement">🏆 Classement - Top 5</h2>
                    <a href="pages/classement.php" class="section-link">Voir le classement complet →</a>
                </div>

                <table class="leaderboard-table" aria-label="Top 5 du classement général">
                    <thead>
                        <tr>
                            <th scope="col">Rang</th>
                            <th scope="col">Joueur</th>
                            <th scope="col">Équipe</th>
                            <th scope="col">Victoires</th>
                            <th scope="col">Points</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboard-preview-body">
                        <tr class="empty-state-row">
                            <td colspan="5" class="empty-state-cell">
                                Aucun joueur classé pour le moment.
                                <?php if ($currentUser): ?>
                                <a href="pages/espace-membre.php">Voir mon compte</a> pour suivre tes stats.
                                <?php else: ?>
                                <a href="pages/inscription.php">Inscris-toi</a> pour apparaître ici !
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ======== SECTION CTA INSCRIPTION ======== -->
        <section id="cta-inscription" class="cta-section" aria-labelledby="titre-cta">
            <div class="section-container">
                <h2 id="titre-cta">Prêt à entrer dans l'arène ?</h2>
                <p>Crée ton compte, forme ton équipe et inscris-toi au prochain tournoi. Les places partent vite !</p>
                <div class="cta-actions">
                    <?php if ($currentUser): ?>
                    <a href="pages/espace-membre.php" class="btn btn-primary btn-lg">Voir mon espace membre</a>
                    <?php else: ?>
                    <a href="pages/inscription.php" class="btn btn-primary btn-lg">Créer mon compte</a>
                    <?php endif; ?>
                    <a href="pages/tournois.php" class="btn btn-outline btn-lg">Explorer les tournois</a>
                </div>
            </div>
        </section>

    </main>

<?php
$jsSupplementaires = [];
include 'assets/php/components/footer.php';
?>
