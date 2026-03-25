<?php
$rootPath        = '../';
$pageTitle       = 'Événements - Gaming Campus';
$metaDescription = 'Calendrier des événements gaming du campus. Dates, lieux, prix et inscriptions.';
$cssSpecifique   = 'evenements.css';
$jsSupplementaires = ['evenements.js'];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête événements">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Événements</li>
                    </ol>
                </nav>
                <h1>📅 Événements</h1>
                <p class="page-description">Tous les événements gaming du campus. LAN parties, soirées e-sport, conférences et plus encore.</p>
            </div>
        </section>

        <!-- ======== CALENDRIER INTERACTIF ======== -->
        <section id="calendrier" aria-labelledby="titre-calendrier">
            <div class="section-container">
                <h2 id="titre-calendrier">📅 Calendrier</h2>

                <div class="calendar-navigation">
                    <button id="cal-prev" class="btn btn-outline" aria-label="Mois précédent">←</button>
                    <span class="calendar-current-month"><strong id="cal-label">Chargement...</strong></span>
                    <button id="cal-next" class="btn btn-outline" aria-label="Mois suivant">→</button>
                </div>

                <div id="calendar-container"></div>
            </div>
        </section>

        <!-- ======== LISTE DES ÉVÉNEMENTS ======== -->
        <section id="liste-evenements" aria-labelledby="titre-evenements">
            <div class="section-container">
                <h2 id="titre-evenements">🎯 Événements à venir</h2>

                <div class="events-list">
                    <!-- Les événements seront chargés depuis la base de données (PHP/MySQL Sprint 4) -->
                    <div class="empty-state empty-state-full">
                        <span class="empty-state-icon">📅</span>
                        <p>Aucun événement à venir pour le moment.</p>
                        <p class="empty-state-sub">Les prochains événements seront annoncés ici dès leur programmation.</p>
                        <a href="contact.php" class="btn btn-outline">Nous contacter</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
