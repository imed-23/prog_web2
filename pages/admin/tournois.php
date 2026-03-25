<?php
require_once __DIR__ . '/../../assets/php/components/auth-guard.php';
authRequire('admin');

$rootPath        = '../../';
$pageTitle       = 'Gestion Tournois - Admin - Gaming Campus';
$metaDescription = 'Administration des tournois Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'tournois';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>🎮 Gestion des Tournois</h1>
            </div>

            <!-- Formulaire d'ajout -->
            <section id="ajouter-tournoi" aria-labelledby="titre-ajout-tournoi">
                <h2 id="titre-ajout-tournoi">➕ Ajouter un tournoi</h2>
                <form class="admin-form" method="post" action="tournois.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-nom">Nom du tournoi <span class="required">*</span></label>
                            <input type="text" id="tournoi-nom" name="nom" required placeholder="Ex: Rift Rivals Season 2">
                        </div>
                        <div class="form-group">
                            <label for="tournoi-jeu">Jeu <span class="required">*</span></label>
                            <select id="tournoi-jeu" name="jeu" required>
                                <option value="" disabled selected>Choisir un jeu</option>
                                <option value="lol">League of Legends</option>
                                <option value="valorant">Valorant</option>
                                <option value="cs2">Counter-Strike 2</option>
                                <option value="fortnite">Fortnite</option>
                                <option value="rocket-league">Rocket League</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-date">Date <span class="required">*</span></label>
                            <input type="datetime-local" id="tournoi-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="tournoi-places">Nombre de places <span class="required">*</span></label>
                            <input type="number" id="tournoi-places" name="places" required min="2" max="128" placeholder="Ex: 16">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tournoi-lieu">Lieu</label>
                            <input type="text" id="tournoi-lieu" name="lieu" placeholder="Ex: Salle E-sport - Bâtiment A">
                        </div>
                        <div class="form-group">
                            <label for="tournoi-cashprize">Cashprize (€)</label>
                            <input type="number" id="tournoi-cashprize" name="cashprize" min="0" placeholder="Ex: 500">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Créer le tournoi</button>
                </form>
            </section>

            <!-- Liste des tournois -->
            <section id="liste-tournois-admin" aria-labelledby="titre-liste-admin">
                <h2 id="titre-liste-admin">📋 Tournois existants</h2>
                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des tournois">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Jeu</th>
                                <th scope="col">Date</th>
                                <th scope="col">Places</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-state-row">
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">🎮</span>
                                        <p>Aucun tournoi créé pour le moment.</p>
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
