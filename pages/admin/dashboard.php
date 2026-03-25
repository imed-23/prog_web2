<?php
require_once __DIR__ . '/../../assets/php/components/auth-guard.php';
authRequire('admin');

$rootPath        = '../../';
$pageTitle       = 'Dashboard Admin - Gaming Campus';
$metaDescription = 'Tableau de bord de l\'interface d\'administration Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'dashboard';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>📊 Dashboard</h1>
                <p>Vue d'ensemble de la plateforme</p>
            </div>

            <!-- Statistiques rapides -->
            <section aria-labelledby="titre-stats">
                <h2 id="titre-stats" class="sr-only">Statistiques</h2>
                <div class="admin-stats-grid">
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">👥</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Utilisateurs inscrits</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">🎮</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Tournois actifs</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">📋</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Réservations</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">🏆</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Tournois terminés</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Actions rapides -->
            <section aria-labelledby="titre-actions">
                <h2 id="titre-actions">⚡ Actions rapides</h2>
                <div class="admin-actions-grid">
                    <a href="tournois.php" class="admin-action-card">
                        <span class="admin-action-icon">🎮</span>
                        <span>Gérer les tournois</span>
                    </a>
                    <a href="utilisateurs.php" class="admin-action-card">
                        <span class="admin-action-icon">👥</span>
                        <span>Gérer les utilisateurs</span>
                    </a>
                    <a href="reservations.php" class="admin-action-card">
                        <span class="admin-action-icon">📋</span>
                        <span>Voir les réservations</span>
                    </a>
                    <a href="../../index.php" class="admin-action-card">
                        <span class="admin-action-icon">🌐</span>
                        <span>Voir le site public</span>
                    </a>
                </div>
            </section>
        </div>

    </main>

<?php include '../../assets/php/components/footer-admin.php'; ?>
