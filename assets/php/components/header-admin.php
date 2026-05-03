<?php
require_once __DIR__ . '/../config/auth.php';
gc_require_admin($rootPath . 'pages/connexion.php');
$currentUser = gc_current_user();

// variables attendues : $rootPath, $pageTitle, $metaDescription, $cssSpecifique, $adminActivePage
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Administration - Gaming Campus') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin - Gaming Campus') ?></title>
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

    <!-- header admin -->
    <header id="site-header" class="header-admin">
        <div class="header-container">
            <a href="<?= $rootPath ?>index.php" class="logo" aria-label="Accueil Gaming Campus">
                <span class="logo-icon">🎮</span>
                <span class="logo-text">Gaming Campus</span>
                <span class="logo-admin-badge">Admin</span>
            </a>

            <nav aria-label="Navigation administration">
                <button class="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="admin-nav">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
                <ul id="admin-nav" class="nav-list">
                    <li><a href="<?= $rootPath ?>pages/admin/dashboard.php" class="nav-link<?= (($adminActivePage ?? '') === 'dashboard') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'dashboard') ? ' aria-current="page"' : '' ?>>📊 Dashboard</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/tournois.php" class="nav-link<?= (($adminActivePage ?? '') === 'tournois') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'tournois') ? ' aria-current="page"' : '' ?>>🎮 Tournois</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/utilisateurs.php" class="nav-link<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' aria-current="page"' : '' ?>>👥 Utilisateurs</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/reservations.php" class="nav-link<?= (($adminActivePage ?? '') === 'reservations') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'reservations') ? ' aria-current="page"' : '' ?>>📋 Réservations</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/demandes.php" class="nav-link<?= (($adminActivePage ?? '') === 'demandes') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'demandes') ? ' aria-current="page"' : '' ?>>🏆 Demandes</a></li>
                </ul>
            </nav>

            <div class="header-actions header-actions-logged">
                <a href="<?= $rootPath ?>index.php" class="btn btn-outline btn-sm">← Retour au site</a>
                <button type="button" class="btn btn-outline btn-sm theme-toggle" data-theme-toggle aria-label="Basculer le thème" title="Basculer le thème">
                    <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
                    <span class="theme-toggle-label">Dark</span>
                </button>
                <div class="user-menu">
                    <div class="user-avatar-sm user-avatar-empty" aria-hidden="true">👤</div>
                    <span class="user-pseudo"><?= htmlspecialchars($currentUser['pseudo'] ?: 'Admin') ?></span>
                </div>
                <a href="<?= $rootPath ?>pages/logout.php" class="btn btn-outline btn-sm">Déconnexion</a>
            </div>
        </div>
    </header>
