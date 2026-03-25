<?php
$rootPath        = '../';
$pageTitle       = 'Espace Membre - Gaming Campus';
$metaDescription = 'Ton espace personnel Gaming Campus. Gère tes équipes, tes inscriptions et ton profil.';
$cssSpecifique   = 'espace-membre.css';
$jsSupplementaires = ['avatar-upload.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête espace membre">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Espace Membre</li>
                    </ol>
                </nav>
                <div class="member-header">
                    <div class="member-avatar-wrapper">
                        <div class="member-avatar">
                            <div id="current-avatar-placeholder" class="user-avatar-placeholder" aria-hidden="true">👤</div>
                            <img id="current-avatar-img" src="" alt="Avatar" class="hidden" loading="lazy">
                        </div>
                    </div>
                    <div class="member-info">
                        <h1>Ton Espace Membre</h1>
                        <p>Gère ton profil, tes équipes et tes inscriptions aux tournois.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== NAVIGATION INTERNE ======== -->
        <nav class="member-nav" aria-label="Navigation espace membre">
            <div class="section-container">
                <ul class="member-nav-list">
                    <li><a href="#dashboard" class="member-nav-link active">🏠 Tableau de bord</a></li>
                    <li><a href="#reservations" class="member-nav-link">📋 Mes Réservations</a></li>
                    <li><a href="#profil" class="member-nav-link">👤 Mon Profil</a></li>
                </ul>
            </div>
        </nav>

        <!-- ======== TABLEAU DE BORD ======== -->
        <section id="dashboard" aria-labelledby="titre-dashboard">
            <div class="section-container">
                <h2 id="titre-dashboard">🏠 Tableau de bord</h2>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <span class="stat-icon">🎮</span>
                        <span class="stat-value">0</span>
                        <span class="stat-label">Tournois joués</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">🏆</span>
                        <span class="stat-value">0</span>
                        <span class="stat-label">Victoires</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">⭐</span>
                        <span class="stat-value">0</span>
                        <span class="stat-label">Points</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">👥</span>
                        <span class="stat-value">—</span>
                        <span class="stat-label">Mon équipe</span>
                    </div>
                </div>

                <div class="empty-state">
                    <span class="empty-state-icon">🎮</span>
                    <p>Tu n'as pas encore participé à de tournoi.</p>
                    <p class="empty-state-sub"><a href="tournois.php">Découvrir les tournois</a></p>
                </div>
            </div>
        </section>

        <!-- ======== MES RÉSERVATIONS ======== -->
        <section id="reservations" aria-labelledby="titre-reservations">
            <div class="section-container">
                <h2 id="titre-reservations">📋 Mes Réservations</h2>

                <div class="empty-state">
                    <span class="empty-state-icon">📋</span>
                    <p>Aucune réservation pour le moment.</p>
                    <p class="empty-state-sub"><a href="tournois.php">S'inscrire à un tournoi</a></p>
                </div>
            </div>
        </section>

        <!-- ======== MON PROFIL ======== -->
        <section id="profil" aria-labelledby="titre-profil">
            <div class="section-container">
                <h2 id="titre-profil">👤 Mon Profil</h2>

                <form class="profil-form" method="post" action="espace-membre.php" enctype="multipart/form-data">

                    <!-- Avatar -->
                    <div class="form-group form-group-avatar">
                        <label>Photo de profil</label>
                        <div class="avatar-upload">
                            <div class="avatar-preview-wrapper">
                                <div class="avatar-preview">
                                    <span class="avatar-preview-placeholder" id="avatar-placeholder">👤</span>
                                    <img id="avatar-preview-img" src="" alt="Aperçu avatar" class="hidden" loading="lazy">
                                </div>
                            </div>
                            <div class="avatar-upload-actions">
                                <label for="avatar-file" class="btn btn-outline">📷 Changer la photo</label>
                                <input type="file" id="avatar-file" name="avatar" accept="image/png, image/jpeg, image/webp" class="sr-only">
                                <small class="form-help">JPG, PNG ou WebP. Max 2 Mo.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profil-pseudo">Pseudo</label>
                        <input type="text" id="profil-pseudo" name="pseudo" placeholder="Ton pseudo" minlength="3" maxlength="20">
                    </div>

                    <div class="form-group">
                        <label for="profil-email">Email</label>
                        <input type="email" id="profil-email" name="email" placeholder="ton.email@campus.fr">
                    </div>

                    <div class="form-group">
                        <label for="profil-jeu">Jeu principal</label>
                        <select id="profil-jeu" name="jeu_principal">
                            <option value="">— Choisir —</option>
                            <option value="lol">League of Legends</option>
                            <option value="valorant">Valorant</option>
                            <option value="cs2">Counter-Strike 2</option>
                            <option value="fortnite">Fortnite</option>
                            <option value="rocket-league">Rocket League</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
                </form>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
