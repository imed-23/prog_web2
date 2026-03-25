<?php
$rootPath        = '../';
$pageTitle       = 'Participants - Gaming Campus';
$metaDescription = 'Liste de tous les participants inscrits sur la plateforme Gaming Campus.';
$cssSpecifique   = '';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête participants">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Participants</li>
                    </ol>
                </nav>
                <h1>👥 Participants</h1>
                <p class="page-description">Retrouve tous les joueurs inscrits sur la plateforme et consulte leurs profils.</p>
            </div>
        </section>

        <!-- ======== SEARCH ======== -->
        <section class="search-section" aria-label="Recherche participants">
            <div class="section-container">
                <form class="search-form" method="get" action="participants.php" role="search">
                    <label for="search-participant" class="sr-only">Rechercher un joueur</label>
                    <input type="search" id="search-participant" name="q" placeholder="Rechercher un joueur par pseudo..." autocomplete="off">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>
        </section>

        <!-- ======== LISTE PARTICIPANTS ======== -->
        <section id="liste-participants" aria-labelledby="titre-participants">
            <div class="section-container">
                <h2 id="titre-participants">Joueurs inscrits</h2>

                <!-- Liste — sera chargée depuis la base de données en Sprint 4 -->
                <div class="participants-grid">
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">👥</span>
                        <p>Aucun participant pour le moment.</p>
                        <p class="empty-state-sub">Les joueurs inscrits apparaîtront ici.</p>
                        <a href="inscription.php" class="btn btn-primary">Rejoindre la communauté</a>
                    </div>
                </div>

                <!-- Pagination -->
                <nav class="pagination" aria-label="Pagination des participants">
                    <ul>
                        <li><a href="#" class="pagination-link active" aria-current="page">1</a></li>
                        <li><a href="#" class="pagination-link pagination-next" aria-label="Page suivante">→</a></li>
                    </ul>
                </nav>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
