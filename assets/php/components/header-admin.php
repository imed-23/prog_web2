<?php
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
                    <li><a href="<?= $rootPath ?>pages/admin/tournois.php" class="nav-link<?= (($adminActivePage ?? '') === 'tournois') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'tournois') ? ' aria-current="page"' : '' ?>>🎮 Tournois</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/utilisateurs.php" class="nav-link<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'utilisateurs') ? ' aria-current="page"' : '' ?>>👥 Utilisateurs</a></li>
                    <li><a href="<?= $rootPath ?>pages/admin/reservations.php" class="nav-link<?= (($adminActivePage ?? '') === 'reservations') ? ' active' : '' ?>"<?= (($adminActivePage ?? '') === 'reservations') ? ' aria-current="page"' : '' ?>>📋 Réservations</a></li>
                </ul>
            </nav>

            <div class="header-actions header-actions-logged">
                <a href="<?= $rootPath ?>index.php" class="btn btn-outline btn-sm">← Retour au site</a>
                <div class="user-menu">
                    <div class="user-avatar-sm user-avatar-empty" aria-hidden="true">👤</div>
                    <span class="user-pseudo"><?= htmlspecialchars($_SESSION['user_pseudo'] ?? 'Admin') ?></span>
                </div>
                <a href="<?= $rootPath ?>pages/deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
            </div>
        </div>
    </header>
