<?php
/*
 * assets/php/components/footer-admin.php
 * Composant réutilisable — Pied de page interface d'administration
 *
 * Variables attendues :
 *   $rootPath          (string)           : chemin vers la racine
 *   $jsSupplementaires (array, optionnel) : JS supplémentaires
 */
?>

    <!-- FOOTER ADMIN -->
    <footer id="site-footer" class="footer-admin">
        <div class="footer-container">
            <div class="footer-bottom">
                <p>&copy; 2026 Gaming Campus - BDE. Interface d'administration.</p>
            </div>
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
