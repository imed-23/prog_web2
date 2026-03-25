<?php
$rootPath        = '../';
$pageTitle       = 'Profil Joueur - Gaming Campus';
$metaDescription = 'Profil d\'un joueur Gaming Campus. Statistiques, trophées et historique des participations.';
$cssSpecifique   = '';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête profil">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="participants.php">Participants</a></li>
                        <li aria-current="page">Profil Joueur</li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- ======== CARTE PROFIL ======== -->
        <section id="profil-card" aria-labelledby="titre-profil">
            <div class="section-container">

                <div class="profil-layout">
                    <!-- Carte identité -->
                    <div class="profil-identity-card">
                        <div class="profil-avatar">
                            <div class="user-avatar-placeholder" aria-hidden="true">👤</div>
                        </div>
                        <h1 id="titre-profil">— Pseudo —</h1>
                        <p class="profil-jeu">Jeu principal : —</p>
                        <p class="profil-membre-since">Membre depuis : —</p>
                    </div>

                    <!-- Statistiques -->
                    <div class="profil-stats">
                        <h2>📊 Statistiques</h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Tournois joués</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Victoires</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-value">0%</span>
                                <span class="stat-label">Win Rate</span>
                            </div>
                            <div class="stat-card">
                                <span class="stat-value">0</span>
                                <span class="stat-label">Points</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div class="profil-historique">
                    <h2>🎮 Historique des participations</h2>
                    <div class="empty-state">
                        <span class="empty-state-icon">🎮</span>
                        <p>Aucune participation pour le moment.</p>
                        <p class="empty-state-sub">L'historique des tournois apparaîtra ici.</p>
                    </div>
                </div>

            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
