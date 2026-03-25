<?php
require_once __DIR__ . '/../config/auth.php';
gc_start_session();
$currentUser = gc_current_user();

/*
 * assets/php/components/footer.php
 * Composant réutilisable — Pied de page public du site
 *
 * Variables attendues (définies avant l'include) :
 *   $rootPath          (string)           : chemin vers la racine
 *   $jsSupplementaires (array, optionnel) : noms des fichiers JS à charger après main.js
 */
?>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer id="site-footer">
        <div class="footer-container">

            <!-- Colonne 1 : À propos -->
            <div class="footer-col">
                <h3>🎮 Gaming Campus</h3>
                <p>La plateforme officielle de tournois gaming du campus. Organisée par le BDE pour les joueurs, par les joueurs.</p>
            </div>

            <!-- Colonne 2 : Navigation rapide -->
            <div class="footer-col">
                <h3>Navigation</h3>
                <ul class="footer-nav">
                    <li><a href="<?= $rootPath ?>index.php">Accueil</a></li>
                    <li><a href="<?= $rootPath ?>pages/tournois.php">Tournois</a></li>
                    <li><a href="<?= $rootPath ?>pages/classement.php">Classement</a></li>
                    <li><a href="<?= $rootPath ?>pages/evenements.php">Événements</a></li>
                    <li><a href="<?= $rootPath ?>pages/blog.php">Blog</a></li>
                </ul>
            </div>

            <!-- Colonne 3 : Compte -->
            <div class="footer-col">
                <h3>Mon Compte</h3>
                <ul class="footer-nav">
                    <?php if ($currentUser): ?>
                    <li><a href="<?= $rootPath ?>pages/espace-membre.php">Mon Espace</a></li>
                    <li><a href="<?= $rootPath ?>pages/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                    <li><a href="<?= $rootPath ?>pages/connexion.php">Connexion</a></li>
                    <li><a href="<?= $rootPath ?>pages/inscription.php">Inscription</a></li>
                    <?php endif; ?>
                    <li><a href="<?= $rootPath ?>pages/espace-membre.php">Espace Membre</a></li>
                    <li><a href="<?= $rootPath ?>pages/faq.php">FAQ</a></li>
                    <li><a href="<?= $rootPath ?>pages/contact.php">Contact</a></li>
                </ul>
            </div>

            <!-- Colonne 4 : Réseaux sociaux -->
            <div class="footer-col">
                <h3>Nous suivre</h3>
                <ul class="social-links">
                    <li><a href="#" aria-label="Discord Gaming Campus" rel="noopener noreferrer" target="_blank">🎧 Discord</a></li>
                    <li><a href="#" aria-label="Twitter Gaming Campus" rel="noopener noreferrer" target="_blank">🐦 Twitter</a></li>
                    <li><a href="#" aria-label="Instagram Gaming Campus" rel="noopener noreferrer" target="_blank">📷 Instagram</a></li>
                    <li><a href="#" aria-label="YouTube Gaming Campus" rel="noopener noreferrer" target="_blank">▶️ YouTube</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 Gaming Campus - BDE. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="<?= $rootPath ?>js/main.js"></script>
    <?php if (!empty($jsSupplementaires) && is_array($jsSupplementaires)): ?>
    <?php foreach ($jsSupplementaires as $jsFile): ?>
    <script src="<?= $rootPath ?>js/<?= htmlspecialchars($jsFile) ?>"></script>
    <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
