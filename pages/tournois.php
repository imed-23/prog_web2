<?php
$rootPath        = '../';
$pageTitle       = 'Tournois - Gaming Campus';
$metaDescription = 'Catalogue de tous les tournois gaming du campus. Consultez les jeux, dates, places disponibles et inscrivez votre équipe.';
$cssSpecifique   = 'tournois.css';
$jsSupplementaires = ['tournois-filtres.js'];
include '../assets/php/components/header.php';
?>

    <!-- ============================================ -->
    <!-- CONTENU PRINCIPAL -->
    <!-- ============================================ -->
    <main id="main-content">

        <!-- ======== EN-TÊTE DE PAGE ======== -->
        <section class="page-hero" aria-label="En-tête de la page Tournois">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Tournois</li>
                    </ol>
                </nav>
                <h1>🎮 Catalogue des Tournois</h1>
                <p class="page-description">Découvre tous les tournois organisés sur le campus. Choisis ton jeu, vérifie les places disponibles et inscris ton équipe !</p>
            </div>
        </section>

        <!-- ======== FILTRES ======== -->
        <section class="filters-section" aria-label="Filtres de recherche">
            <div class="section-container">
                <form class="filters-form" method="get" action="tournois.php">
                    <fieldset>
                        <legend>Filtrer les tournois</legend>

                        <div class="filter-group">
                            <label for="filter-jeu">Jeu</label>
                            <select id="filter-jeu" name="jeu">
                                <option value="">Tous les jeux</option>
                                <option value="lol">League of Legends</option>
                                <option value="valorant">Valorant</option>
                                <option value="cs2">Counter-Strike 2</option>
                                <option value="fortnite">Fortnite</option>
                                <option value="rocket-league">Rocket League</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter-statut">Statut</label>
                            <select id="filter-statut" name="statut">
                                <option value="">Tous les statuts</option>
                                <option value="a-venir">À venir</option>
                                <option value="en-cours">En cours</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter-places">Places disponibles</label>
                            <select id="filter-places" name="places">
                                <option value="">Toutes</option>
                                <option value="dispo">Places disponibles</option>
                                <option value="complet">Complet</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </fieldset>
                </form>
            </div>
        </section>

        <!-- ======== LISTE DES TOURNOIS ======== -->
        <section id="liste-tournois" aria-labelledby="titre-liste-tournois">
            <div class="section-container">
                <h2 id="titre-liste-tournois" class="sr-only">Liste des tournois</h2>

                <!-- Résumé des résultats — mis à jour dynamiquement par JS -->
                <div class="results-summary">
                    <p id="results-summary-text"><strong>0 tournoi</strong> pour le moment</p>
                </div>

                <div class="cards-grid">
                    <!-- Les tournois seront chargés depuis la base de données (PHP/MySQL Sprint 4) -->
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">🎮</span>
                        <p>Aucun tournoi disponible pour le moment.</p>
                        <p class="empty-state-sub">Le prochain tournoi sera annoncé très bientôt. Reviens vite !</p>
                        <a href="../index.php" class="btn btn-outline">Retour à l'accueil</a>
                    </div>
                </div>

            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
