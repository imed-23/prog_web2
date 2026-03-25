<?php
$rootPath        = '../';
$pageTitle       = 'Détail Tournoi - Gaming Campus';
$metaDescription = 'Informations détaillées sur ce tournoi : format, règles, cashprize et équipes inscrites.';
$cssSpecifique   = 'tournoi-detail.css';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête tournoi">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li><a href="tournois.php">Tournois</a></li>
                        <li aria-current="page">Détail du tournoi</li>
                    </ol>
                </nav>
                <h1>🎮 Détail du Tournoi</h1>
                <p class="page-description">Informations complètes sur ce tournoi.</p>
            </div>
        </section>

        <!-- ======== CONTENU TOURNOI ======== -->
        <section id="tournoi-detail" aria-labelledby="titre-tournoi">
            <div class="section-container">
                <div class="tournoi-detail-grid">

                    <!-- Colonne principale -->
                    <div class="tournoi-main">

                        <!-- Bannière -->
                        <div class="tournoi-banner">
                            <img src="../img/lol_cover.webp" alt="Image du tournoi" loading="lazy">
                        </div>

                        <!-- Description -->
                        <div class="tournoi-description card">
                            <div class="card-body">
                                <div class="card-badge badge-en-cours">En cours</div>
                                <h2 id="titre-tournoi">Nom du tournoi</h2>

                                <h3>📖 Description</h3>
                                <p class="section-description">Les informations détaillées sur ce tournoi seront disponibles après connexion à la base de données.</p>

                                <h4>📜 Règles</h4>
                                <ul>
                                    <li>Uniquement les étudiants du campus sont autorisés à participer.</li>
                                    <li>Comportement fair-play obligatoire.</li>
                                    <li>Les résultats officiels sont définitifs.</li>
                                </ul>

                                <h4>🏆 Récompenses</h4>
                                <dl class="prizes-list">
                                    <dt>🥇 1ère place</dt><dd>—</dd>
                                    <dt>🥈 2ème place</dt><dd>—</dd>
                                    <dt>🥉 3ème place</dt><dd>—</dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Équipes inscrites -->
                        <div class="card">
                            <div class="card-body">
                                <h3>👥 Équipes inscrites</h3>
                                <div class="empty-state">
                                    <span class="empty-state-icon">👥</span>
                                    <p>Aucune équipe inscrite pour le moment.</p>
                                    <p class="empty-state-sub"><a href="inscription.php">Créer un compte</a> pour inscrire ton équipe !</p>
                                </div>
                            </div>
                        </div>

                        <!-- Arbre du tournoi -->
                        <div class="card">
                            <div class="card-body">
                                <h3>🌿 Bracket du tournoi</h3>
                                <div class="bracket-placeholder">
                                    <span>🎮</span>
                                    <p>Le bracket sera généré une fois les équipes confirmées.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="tournoi-detail-sidebar">
                        <div class="card card-info">
                            <div class="card-body">
                                <h3>📋 Informations</h3>
                                <dl class="info-details">
                                    <dt>🎮 Jeu</dt><dd>—</dd>
                                    <dt>📅 Date</dt><dd>—</dd>
                                    <dt>📍 Lieu</dt><dd>—</dd>
                                    <dt>👥 Format</dt><dd>—</dd>
                                    <dt>📊 Statut</dt><dd><span class="status-badge status-a-venir">À venir</span></dd>
                                    <dt>🏆 Cashprize</dt><dd>—</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="card card-places">
                            <div class="card-body">
                                <h3>🎟️ Places</h3>
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-fill" style="width: 0%"></div>
                                </div>
                                <p class="places-restantes">— places restantes</p>
                                <a href="inscription.php" class="btn btn-primary btn-block">S'inscrire</a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
