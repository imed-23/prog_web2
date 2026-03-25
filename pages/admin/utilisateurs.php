<?php
<<<<<<< HEAD
require_once __DIR__ . '/../../assets/php/components/auth-guard.php';
authRequire('admin');
=======
require_once __DIR__ . '/../../assets/php/config/auth.php';
gc_require_admin('../../pages/connexion.php');

require_once __DIR__ . '/../../assets/php/config/db.php';

$messageSucces = '';
$messageErreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    } else {
        $action = trim($_POST['action'] ?? 'create');
        if ($action === 'delete') {
            $deleteId = (int) ($_POST['id'] ?? 0);
            if ($deleteId > 0) {
                try {
                    $selfId = (int) (gc_current_user()['id'] ?? 0);
                    if ($deleteId === $selfId) {
                        $messageErreur = 'Tu ne peux pas supprimer ton propre compte admin.';
                    } else {
                        $stmt = $pdo->prepare('SELECT role FROM utilisateurs WHERE id = ? LIMIT 1');
                        $stmt->execute([$deleteId]);
                        $target = $stmt->fetch();

                        if ($target && $target['role'] === 'admin') {
                            $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "admin"')->fetchColumn();
                            if ($adminCount <= 1) {
                                $messageErreur = 'Impossible de supprimer le dernier administrateur.';
                            }
                        }

                        if ($messageErreur === '') {
                            $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
                            $stmt->execute([$deleteId]);
                            $messageSucces = 'Utilisateur supprimé.';
                        }
                    }
                } catch (PDOException $e) {
                    error_log('[ADMIN USERS DELETE] ' . $e->getMessage());
                    $messageErreur = 'Suppression impossible pour cet utilisateur.';
                }
            }
        } elseif ($action === 'update_role') {
            $targetId = (int) ($_POST['id'] ?? 0);
            $newRole = trim($_POST['role'] ?? 'visiteur');
            $rolesAutorises = ['visiteur', 'capitaine', 'admin'];

            if ($targetId <= 0 || !in_array($newRole, $rolesAutorises, true)) {
                $messageErreur = 'Rôle invalide.';
            } else {
                try {
                    $stmt = $pdo->prepare('SELECT role FROM utilisateurs WHERE id = ? LIMIT 1');
                    $stmt->execute([$targetId]);
                    $target = $stmt->fetch();

                    if (!$target) {
                        $messageErreur = 'Utilisateur introuvable.';
                    } else {
                        $selfId = (int) (gc_current_user()['id'] ?? 0);
                        if ($target['role'] === 'admin' && $newRole !== 'admin') {
                            $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "admin"')->fetchColumn();
                            if ($adminCount <= 1) {
                                $messageErreur = 'Impossible de rétrograder le dernier administrateur.';
                            }
                            if ($targetId === $selfId) {
                                $messageErreur = 'Tu ne peux pas retirer ton propre rôle admin.';
                            }
                        }

                        if ($messageErreur === '') {
                            $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
                            $stmt->execute([$newRole, $targetId]);
                            $messageSucces = 'Rôle utilisateur mis à jour.';
                        }
                    }
                } catch (PDOException $e) {
                    error_log('[ADMIN USERS UPDATE ROLE] ' . $e->getMessage());
                    $messageErreur = 'Impossible de modifier le rôle utilisateur.';
                }
            }
        } else {
            $pseudo = trim($_POST['pseudo'] ?? '');
            $email = trim(strtolower($_POST['email'] ?? ''));
            $role = trim($_POST['role'] ?? 'visiteur');
            $password = $_POST['password'] ?? '';

            $rolesAutorises = ['visiteur', 'capitaine', 'admin'];
            if ($pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || !in_array($role, $rolesAutorises, true)) {
                $messageErreur = 'Champs invalides pour la création de l\'utilisateur.';
            } else {
                try {
                    $stmt = $pdo->prepare('INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp_hash, role) VALUES (:pseudo, :prenom, :nom, :email, :mdp_hash, :role)');
                    $stmt->execute([
                        ':pseudo' => $pseudo,
                        ':prenom' => 'N/A',
                        ':nom' => 'N/A',
                        ':email' => $email,
                        ':mdp_hash' => password_hash($password, PASSWORD_BCRYPT),
                        ':role' => $role,
                    ]);
                    $messageSucces = 'Utilisateur ajouté avec succès.';
                } catch (PDOException $e) {
                    error_log('[ADMIN USERS CREATE] ' . $e->getMessage());
                    $messageErreur = 'Impossible d\'ajouter cet utilisateur (email/pseudo déjà utilisé ?).';
                }
            }
        }
    }
}

$utilisateurs = [];
try {
    $stmt = $pdo->query('SELECT id, pseudo, email, role, created_at FROM utilisateurs ORDER BY created_at DESC');
    $utilisateurs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[ADMIN USERS LIST] ' . $e->getMessage());
}
>>>>>>> 14eabe1 (release v1.3)

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

            <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
            <?php endif; ?>
            <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <section id="ajouter-utilisateur" aria-labelledby="titre-ajout-user">
                <h2 id="titre-ajout-user">➕ Ajouter un utilisateur</h2>
                <form class="admin-form" method="post" action="utilisateurs.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                    <input type="hidden" name="action" value="create">
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
                            <?php if (!empty($utilisateurs)): ?>
                            <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?= (int) $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['pseudo']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form method="post" action="utilisateurs.php" class="inline-role-form">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="action" value="update_role">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <select name="role" aria-label="Rôle de <?= htmlspecialchars($user['pseudo']) ?>">
                                            <option value="visiteur" <?= $user['role'] === 'visiteur' ? 'selected' : '' ?>>Visiteur</option>
                                            <option value="capitaine" <?= $user['role'] === 'capitaine' ? 'selected' : '' ?>>Capitaine</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline btn-sm">Mettre à jour</button>
                                    </form>
                                </td>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime((string) $user['created_at']))) ?></td>
                                <td>
                                    <form method="post" action="utilisateurs.php" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="empty-state-row">
                                <td colspan="6" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">👥</span>
                                        <p>Aucun utilisateur enregistré pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

    </main>

<?php include '../../assets/php/components/footer-admin.php'; ?>
