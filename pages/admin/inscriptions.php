<?php
require_once __DIR__ . '/../../assets/php/config/auth.php';
gc_require_login('../../pages/connexion.php');

require_once __DIR__ . '/../../assets/php/config/db.php';

$messageSucces = '';
$messageErreur = '';

// ── Traitement des actions (suppression, changement de rôle) ──────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $messageErreur = 'Session expirée. Recharge la page puis réessaie.';
    } else {
        $action = trim($_POST['action'] ?? '');

        // Suppression d'un utilisateur
        if ($action === 'delete') {
            $deleteId = (int) ($_POST['id'] ?? 0);
            if ($deleteId > 0) {
                try {
                    $selfId = (int) (gc_current_user()['id'] ?? 0);
                    if ($deleteId === $selfId) {
                        $messageErreur = 'Tu ne peux pas supprimer ton propre compte.';
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
                    error_log('[ADMIN INSCRIPTIONS DELETE] ' . $e->getMessage());
                    $messageErreur = 'Suppression impossible pour cet utilisateur.';
                }
            }
        }
        // Changement de rôle
        elseif ($action === 'update_role') {
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
                    error_log('[ADMIN INSCRIPTIONS UPDATE ROLE] ' . $e->getMessage());
                    $messageErreur = 'Impossible de modifier le rôle utilisateur.';
                }
            }
        }
    }
}

// ── Récupération des filtres et recherche ─────────────────────────────────
$filterRole = trim($_GET['role'] ?? '');
$search = trim($_GET['search'] ?? '');
$sortBy = trim($_GET['sort'] ?? 'created_at');
$sortOrder = trim($_GET['order'] ?? 'DESC');

// Validation des paramètres de tri
$allowedSort = ['pseudo', 'email', 'jeu_principal', 'created_at', 'role'];
if (!in_array($sortBy, $allowedSort, true)) {
    $sortBy = 'created_at';
}
$sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

// ── Statistiques ───────────────────────────────────────────────────────────
$stats = [
    'total' => 0,
    'capitaines' => 0,
    'visiteurs' => 0,
    'admins' => 0,
];

try {
    $stats['total'] = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
    $stats['capitaines'] = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "capitaine"')->fetchColumn();
    $stats['visiteurs'] = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "visiteur"')->fetchColumn();
    $stats['admins'] = (int) $pdo->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "admin"')->fetchColumn();
} catch (PDOException $e) {
    error_log('[ADMIN INSCRIPTIONS STATS] ' . $e->getMessage());
}

// ── Récupération de la liste des utilisateurs ─────────────────────────────
$utilisateurs = [];
try {
    $sql = 'SELECT id, pseudo, prenom, nom, email, avatar, jeu_principal, role, created_at 
            FROM utilisateurs 
            WHERE 1=1';
    $params = [];

    // Filtre par rôle
    if (in_array($filterRole, ['visiteur', 'capitaine', 'admin'], true)) {
        $sql .= ' AND role = :role';
        $params[':role'] = $filterRole;
    } else {
        $filterRole = '';
    }

    // Recherche par pseudo ou email
    if ($search !== '') {
        $sql .= ' AND (pseudo LIKE :search OR email LIKE :search OR prenom LIKE :search OR nom LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY $sortBy $sortOrder";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $utilisateurs = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[ADMIN INSCRIPTIONS LIST] ' . $e->getMessage());
    $messageErreur = $messageErreur !== '' ? $messageErreur : 'Impossible de charger les inscriptions.';
}

$rootPath        = '../../';
$pageTitle       = 'Gestion des Inscriptions - Admin - Gaming Campus';
$metaDescription = 'Administration des inscriptions et utilisateurs Gaming Campus.';
$cssSpecifique   = 'admin.css';
$adminActivePage = 'inscriptions';
$jsSupplementaires = [];
include '../../assets/php/components/header-admin.php';
?>

    <!-- CONTENU PRINCIPAL ADMIN -->
    <main id="main-content">

        <div class="admin-container">
            <div class="admin-page-header">
                <h1>👥 Gestion des Inscriptions</h1>
                <p>Liste de tous les utilisateurs inscrits sur la plateforme</p>
            </div>

            <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success" role="alert">✅ <?= htmlspecialchars($messageSucces) ?></div>
            <?php endif; ?>
            <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-error" role="alert">❌ <?= htmlspecialchars($messageErreur) ?></div>
            <?php endif; ?>

            <!-- Résumé statistique -->
            <section aria-labelledby="titre-stats-inscriptions">
                <h2 id="titre-stats-inscriptions">📊 Résumé des inscriptions</h2>
                <div class="admin-stats-grid">
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">👥</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['total'] ?></span>
                            <span class="admin-stat-label">Total inscrits</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">🎮</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['capitaines'] ?></span>
                            <span class="admin-stat-label">Capitaines</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">👁️</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['visiteurs'] ?></span>
                            <span class="admin-stat-label">Visiteurs</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <span class="admin-stat-icon">🔐</span>
                        <div class="admin-stat-info">
                            <span class="admin-stat-value"><?= $stats['admins'] ?></span>
                            <span class="admin-stat-label">Administrateurs</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Filtres et recherche -->
            <section aria-labelledby="titre-filtres">
                <h2 id="titre-filtres" class="sr-only">Filtres et recherche</h2>
                <div class="admin-filters">
                    <form method="get" action="inscriptions.php" class="filters-form">
                        <div class="filters-row">
                            <div class="filter-group">
                                <label for="filter-role">Filtrer par rôle :</label>
                                <select name="role" id="filter-role" onchange="this.form.submit()">
                                    <option value="" <?= $filterRole === '' ? 'selected' : '' ?>>Tous les rôles</option>
                                    <option value="visiteur" <?= $filterRole === 'visiteur' ? 'selected' : '' ?>>Visiteurs</option>
                                    <option value="capitaine" <?= $filterRole === 'capitaine' ? 'selected' : '' ?>>Capitaines</option>
                                    <option value="admin" <?= $filterRole === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                                </select>
                            </div>
                            <div class="filter-group filter-search">
                                <label for="search">Rechercher :</label>
                                <input type="search" name="search" id="search" placeholder="Pseudo, email, nom..."
                                       value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-primary btn-sm">🔍</button>
                                <?php if ($search !== ''): ?>
                                <a href="inscriptions.php" class="btn btn-outline btn-sm">✕</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Tableau des inscriptions -->
            <section id="liste-inscriptions" aria-labelledby="titre-inscriptions">
                <div class="admin-section-header">
                    <h2 id="titre-inscriptions">📋 Tous les utilisateurs</h2>
                    <span class="admin-count-badge"><?= count($utilisateurs) ?> utilisateur(s)</span>
                </div>

                <div class="table-responsive">
                    <table class="admin-table" aria-label="Liste des inscriptions">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <a href="?role=<?= htmlspecialchars($filterRole) ?>&search=<?= urlencode($search) ?>&sort=pseudo&order=<?= $sortBy === 'pseudo' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">
                                        Pseudo <?= $sortBy === 'pseudo' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                                    </a>
                                </th>
                                <th scope="col">Prénom / Nom</th>
                                <th scope="col">
                                    <a href="?role=<?= htmlspecialchars($filterRole) ?>&search=<?= urlencode($search) ?>&sort=email&order=<?= $sortBy === 'email' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">
                                        Email <?= $sortBy === 'email' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                                    </a>
                                </th>
                                <th scope="col">
                                    <a href="?role=<?= htmlspecialchars($filterRole) ?>&search=<?= urlencode($search) ?>&sort=jeu_principal&order=<?= $sortBy === 'jeu_principal' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">
                                        Jeu principal <?= $sortBy === 'jeu_principal' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                                    </a>
                                </th>
                                <th scope="col">
                                    <a href="?role=<?= htmlspecialchars($filterRole) ?>&search=<?= urlencode($search) ?>&sort=role&order=<?= $sortBy === 'role' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">
                                        Rôle <?= $sortBy === 'role' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                                    </a>
                                </th>
                                <th scope="col">
                                    <a href="?role=<?= htmlspecialchars($filterRole) ?>&search=<?= urlencode($search) ?>&sort=created_at&order=<?= $sortBy === 'created_at' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">
                                        Date inscription <?= $sortBy === 'created_at' ? ($sortOrder === 'ASC' ? '↑' : '↓') : '' ?>
                                    </a>
                                </th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($utilisateurs)): ?>
                            <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-cell-with-avatar">
                                        <?php if (!empty($user['avatar'])): ?>
                                        <img src="../../<?= htmlspecialchars($user['avatar']) ?>" alt="" class="user-avatar-small" loading="lazy">
                                        <?php else: ?>
                                        <span class="user-avatar-placeholder">👤</span>
                                        <?php endif; ?>
                                        <span class="user-pseudo"><?= htmlspecialchars($user['pseudo']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    $jeux = [
                                        'lol' => 'League of Legends',
                                        'valorant' => 'Valorant',
                                        'cs2' => 'Counter-Strike 2',
                                        'fortnite' => 'Fortnite',
                                        'rocket-league' => 'Rocket League',
                                        'autre' => 'Autre'
                                    ];
                                    ?>
                                    <?php if (!empty($user['jeu_principal']) && isset($jeux[$user['jeu_principal']])): ?>
                                    <span class="game-badge"><?= htmlspecialchars($jeux[$user['jeu_principal']]) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">Non défini</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="inscriptions.php" class="inline-role-form">
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
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $user['created_at']))) ?></td>
                                <td>
                                    <form method="post" action="inscriptions.php" onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr class="empty-state-row">
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <span class="empty-state-icon">👥</span>
                                        <p>Aucun utilisateur trouvé.</p>
                                        <?php if ($search !== '' || $filterRole !== ''): ?>
                                        <p class="empty-state-sub">
                                            <a href="inscriptions.php">Afficher tous les utilisateurs</a>
                                        </p>
                                        <?php endif; ?>
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
