<?php
$rootPath        = '../';
$pageTitle       = 'Contact - Gaming Campus';
$metaDescription = 'Une question, une suggestion ou un problème ? N\'hésite pas à nous contacter. On répond sous 24h !';
$cssSpecifique   = '';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <!-- ======== EN-TÊTE ======== -->
        <section class="page-hero" aria-label="En-tête contact">
            <div class="section-container">
                <nav aria-label="Fil d'Ariane" class="breadcrumb">
                    <ol>
                        <li><a href="../index.php">Accueil</a></li>
                        <li aria-current="page">Contact</li>
                    </ol>
                </nav>
                <h1>📧 Contactez-nous</h1>
                <p class="page-description">Une question, une suggestion ou un problème ? N'hésite pas à nous contacter. On répond sous 24h !</p>
            </div>
        </section>

        <!-- ======== CONTENU CONTACT ======== -->
        <section id="contact-content" aria-labelledby="titre-contact">
            <div class="section-container">

                <div class="contact-grid">

                    <!-- Formulaire de contact -->
                    <div class="contact-form-wrapper">
                        <h2 id="titre-contact">📝 Envoyer un message</h2>

                        <!-- Message de confirmation (affiché par PHP après envoi) -->
                        <?php if (!empty($successContact)): ?>
                        <div class="alert alert-success" role="alert">
                            <p>✅ Ton message a bien été envoyé ! On te répond sous 24h.</p>
                        </div>
                        <?php endif; ?>

                        <form class="contact-form" method="post" action="contact.php" novalidate>
                            <div class="form-group">
                                <label for="contact-nom">Nom complet <span class="required" aria-label="obligatoire">*</span></label>
                                <input type="text" id="contact-nom" name="nom" required placeholder="Ton nom complet" autocomplete="name">
                            </div>

                            <div class="form-group">
                                <label for="contact-email">Adresse email <span class="required" aria-label="obligatoire">*</span></label>
                                <input type="email" id="contact-email" name="email" required placeholder="ton.email@campus.fr" autocomplete="email">
                            </div>

                            <div class="form-group">
                                <label for="contact-sujet">Sujet <span class="required" aria-label="obligatoire">*</span></label>
                                <select id="contact-sujet" name="sujet" required>
                                    <option value="" disabled selected>Choisir un sujet</option>
                                    <option value="question">Question générale</option>
                                    <option value="inscription">Problème d'inscription</option>
                                    <option value="tournoi">Question sur un tournoi</option>
                                    <option value="bug">Signaler un bug</option>
                                    <option value="suggestion">Suggestion</option>
                                    <option value="partenariat">Partenariat</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contact-message">Message <span class="required" aria-label="obligatoire">*</span></label>
                                <textarea id="contact-message" name="message" rows="8" required placeholder="Décris ta demande en détail..."></textarea>
                            </div>

                            <p class="form-note"><span class="required">*</span> Champs obligatoires</p>

                            <button type="submit" class="btn btn-primary btn-lg">Envoyer le message</button>
                        </form>
                    </div>

                    <!-- Informations de contact -->
                    <aside class="contact-info">
                        <div class="sidebar-card">
                            <h3>📍 Nous trouver</h3>
                            <address>
                                <p><strong>BDE Gaming Campus</strong></p>
                                <p>Bâtiment A - Bureau du BDE<br>
                                123 Avenue du Campus<br>
                                75000 Paris</p>
                            </address>
                        </div>

                        <div class="sidebar-card">
                            <h3>📞 Nous appeler</h3>
                            <p><a href="tel:+33123456789">01 23 45 67 89</a></p>
                            <p class="contact-horaires">Du lundi au vendredi, 9h - 18h</p>
                        </div>

                        <div class="sidebar-card">
                            <h3>📧 Email</h3>
                            <p>Contact général : <a href="mailto:contact@gamingcampus.fr">contact@gamingcampus.fr</a></p>
                            <p>Support technique : <a href="mailto:support@gamingcampus.fr">support@gamingcampus.fr</a></p>
                            <p>BDE : <a href="mailto:bde@gamingcampus.fr">bde@gamingcampus.fr</a></p>
                        </div>

                        <div class="sidebar-card">
                            <h3>🌐 Réseaux sociaux</h3>
                            <ul class="social-links social-links-vertical">
                                <li><a href="#" target="_blank" rel="noopener noreferrer">🎧 Discord - Le hub de la communauté</a></li>
                                <li><a href="#" target="_blank" rel="noopener noreferrer">🐦 Twitter - @GamingCampus</a></li>
                                <li><a href="#" target="_blank" rel="noopener noreferrer">📷 Instagram - @gamingcampus</a></li>
                                <li><a href="#" target="_blank" rel="noopener noreferrer">▶️ YouTube - Gaming Campus</a></li>
                            </ul>
                        </div>
                    </aside>

                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
