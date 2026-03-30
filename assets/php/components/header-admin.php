<?php
require_once __DIR__ . '/../config/auth.php';
gc_require_login($rootPath . 'pages/connexion.php');
$currentUser = gc_current_user();

/*
 * assets/php/components/header-admin.php
 * Composant réutilisable — En-tête interface d'administration
 *
 * Variables attendues :
 *   $rootPath        (string) : chemin vers la racine. Ex: '../../'
 *   $pageTitle       (string) : contenu du <title>
 *   $metaDescription (string) : contenu de la meta description
 *   $cssSpecifique   (string) : nom du fichier CSS spécifique (ex: 'admin.css')
 *   $adminActivePage (string) : page active admin ('dashboard','tournois','utilisateurs','reservations')
 */
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

    <!-- ============================================ -->
    <!-- HEADER ADMIN -->
    <!-- ============================================ -->
    <header id="site-header" class="header-admin">
        <div class="header-container">
            <a href="<?= $rootPath ?>index.php" class="logo" aria-label="Accueil Gaming Campus">
                <span class="logo-icon">🎮</span>
                <span class="logo-text">Gaming Campus</span>
                <span class="logo-admin-badge">Admin</span>
            </a>

            <nav aria-label="Navigation administration">
                <ul id="admin-nav" class="nav-list">
                    <li><a href="<?= $rootPath ?>pages/admin/dashboard.php" class="nav-link<?= (($adminActivePage ?? '') === 'dashboard') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'dashboard') ? ' aria-current="page"' : '' ?>>📊 Dashboard</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/inscriptions.php" class="nav-link<?= (($adminActivePage ?? '') === 'inscriptions') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'inscriptions') ? ' aria-current="page"' : '' ?>>👥 Inscriptions</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/tournois.php" class="nav-link<?= (($adminActivePage ?? '') === 'tournois') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'tournois') ? ' aria-current="page"' : '' ?>>🎮 Tournois</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/utilisateurs.php" class="nav-link<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' aria-current="page"' : '' ?>>🔧 Comptes</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/reservations.php" class="nav-link<?= (($adminActivePage ?? '') === 'reservations') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'reservations') ? ' aria-current="page"' : '' ?>>📋 Réservations</a></li>
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
