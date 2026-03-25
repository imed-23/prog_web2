<?php
require_once __DIR__ . '/../../assets/php/components/auth-guard.php';
authRequire('admin');

$rootPath        = '../../';
$pageTitle       = 'Gestion Réservations - Admin - Gaming Campus';
$metaDescription = 'Administration des réservations Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'reservations';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>📋 Gestion des Réservations</h1>
            </div>

            <!-- Résumé -->
            <section aria-labelledby="titre-resume-reservations">
                <h2 id="titre-resume-reservations">📊 Résumé</h2>
                <div class="admin-stats-grid">
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">📋</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Total réservations</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">✅</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Confirmées</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">⌛</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">En attente</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">❌</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value">0</span>
                            <span class="admin-stat-label">Annulées</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tableau des réservations -->
            <section id="liste-reservations" aria-labelledby="titre-reservations">
                <div class="admin-section-header">
                    <h2 id="titre-reservations">📋 Toutes les réservations</h2>
                    <div class="admin-filters">
                        <form method="get" action="reservations.php">
                            <select name="statut" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="confirmee">Confirmées</option>
                                <option value="en-attente">En attente</option>
                                <option value="annulee">Annulées</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des réservations">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Équipe</th>
                                <th scope="col">Tournoi</th>
                                <th scope="col">Capitaine</th>
                                <th scope="col">Date inscription</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-state-row">
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">📋</span>
                                        <p>Aucune réservation pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

    </main>

<?php include '../../assets/php/components/footer-admin.php'; ?>
