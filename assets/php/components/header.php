<?php
require_once __DIR__ . '/../config/auth.php';
gc_start_session();
$currentUser = gc_current_user();

/*
 * assets/php/components/header.php
 * Composant réutilisable — En-tête public du site
 *
 * Variables attendues (à définir avant l'include) :
 *   $rootPath        (string) : chemin vers la racine. Ex: '../', '../../', ''
 *   $pageTitle       (string) : contenu du <title>
 *   $metaDescription (string) : contenu de la meta description
 *   $cssSpecifique   (string) : nom du fichier CSS spécifique (ex: 'tournois.css')
 *   $jsSupplementaires (array) : noms des fichiers JS à ajouter en fin de page
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_isLoggedIn = !empty($_SESSION['user_id']);
$_userRole   = $_SESSION['user_role'] ?? '';
$_userPseudo = $_SESSION['user_pseudo'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Plateforme de Tournois Gaming Campus') ?>">
    <meta name="author" content="BDE Gaming Campus">
    <title><?= htmlspecialchars($pageTitle ?? 'Gaming Campus') ?></title>
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('gc_theme');
                if (theme === 'light') {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        }());
    </script>
    <link rel="stylesheet" href="<?= $rootPath ?>css/style.css">
    <?php if (!empty($cssSpecifique)): ?>
    <link rel="stylesheet" href="<?= $rootPath ?>css/<?= htmlspecialchars($cssSpecifique) ?>">
    <?php endif; ?>
</head>
<body>

    <!-- ============================================ -->
    <!-- HEADER / NAVIGATION -->
    <!-- ============================================ -->
    <header id="site-header">
        <div class="header-container">
            <a href="<?= $rootPath ?>index.php" class="logo" aria-label="Accueil Gaming Campus">
                <span class="logo-icon">🎮</span>
                <span class="logo-text">Gaming Campus</span>
            </a>

            <!-- Navigation principale -->
            <nav aria-label="Navigation principale">
                <button class="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>

                <ul id="main-nav" class="nav-list">
                    <li><a href="<?= $rootPath ?>index.php" class="nav-link">Accueil</a></li>
                    <li><a href="<?= $rootPath ?>pages/tournois.php" class="nav-link">Tournois</a></li>
                    <li><a href="<?= $rootPath ?>pages/classement.php" class="nav-link">Classement</a></li>
                    <li><a href="<?= $rootPath ?>pages/evenements.php" class="nav-link">Événements</a></li>
                    <li><a href="<?= $rootPath ?>pages/participants.php" class="nav-link">Participants</a></li>
                    <li><a href="<?= $rootPath ?>pages/blog.php" class="nav-link">Blog</a></li>
                    <li><a href="<?= $rootPath ?>pages/faq.php" class="nav-link">FAQ</a></li>
                    <li><a href="<?= $rootPath ?>pages/contact.php" class="nav-link">Contact</a></li>
                </ul>
            </nav>

            <!-- Actions utilisateur -->
            <div class="header-actions">
                <button type="button" class="btn btn-outline btn-sm theme-toggle" data-theme-toggle aria-label="Basculer le thème" title="Basculer le thème">
                    <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
                    <span class="theme-toggle-label">Dark</span>
                </button>
                <?php if ($currentUser): ?>
                <a href="<?= $rootPath ?>pages/espace-membre.php" class="btn btn-outline"><?= htmlspecialchars($currentUser['pseudo']) ?></a>
                <a href="<?= $rootPath ?>pages/logout.php" class="btn btn-primary">Déconnexion</a>
                <?php else: ?>
                <a href="<?= $rootPath ?>pages/connexion.php" class="btn btn-outline">Connexion</a>
                <a href="<?= $rootPath ?>pages/inscription.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
