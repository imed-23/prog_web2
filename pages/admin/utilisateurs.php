<?php
$rootPath        = '../../';
$pageTitle       = 'Gestion Utilisateurs - Admin - Gaming Campus';
$metaDescription = 'Administration des utilisateurs Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'utilisateurs';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>👥 Gestion des Utilisateurs</h1>
            </div>

            <!-- Formulaire d'ajout -->
            <section id="ajouter-utilisateur" aria-labelledby="titre-ajout-user">
                <h2 id="titre-ajout-user">➕ Ajouter un utilisateur</h2>
                <form class="admin-form" method="post" action="utilisateurs.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user-pseudo">Pseudo <span class="required">*</span></label>
                            <input type="text" id="user-pseudo" name="pseudo" required>
                        </div>
                        <div class="form-group">
                            <label for="user-email">Email <span class="required">*</span></label>
                            <input type="email" id="user-email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user-role">Rôle <span class="required">*</span></label>
                            <select id="user-role" name="role" required>
                                <option value="visiteur">Visiteur</option>
                                <option value="capitaine">Capitaine</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user-password">Mot de passe temporaire <span class="required">*</span></label>
                            <input type="password" id="user-password" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter l'utilisateur</button>
                </form>
            </section>

            <!-- Tableau des utilisateurs -->
            <section id="liste-utilisateurs" aria-labelledby="titre-users">
                <h2 id="titre-users">👤 Utilisateurs enregistrés</h2>
                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des utilisateurs">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Pseudo</th>
                                <th scope="col">Email</th>
                                <th scope="col">Rôle</th>
                                <th scope="col">Inscription</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-state-row">
                                <td colspan="6" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">👥</span>
                                        <p>Aucun utilisateur enregistré pour le moment.</p>
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
