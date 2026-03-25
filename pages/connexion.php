<?php
/**
 * connexion.php — Formulaire de connexion Gaming Campus
 * Sprint 4 : traitement PHP + message succès post-inscription
 */

<<<<<<< HEAD
session_start();

// Déjà connecté → redirection
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: espace-membre.php');
    }
    exit;
}
=======
require_once __DIR__ . '/../assets/php/config/auth.php';
gc_start_session();
>>>>>>> 14eabe1 (release v1.3)

// ── Variables ──────────────────────────────────────────────────────────────
$erreurConnexion = '';
$successMessage  = '';
$redirectPath    = trim($_GET['redirect'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirectPath = trim($_POST['redirect'] ?? $redirectPath);
}

// Autorise uniquement des redirections internes relatives (pas d'URL externe)
if ($redirectPath !== '') {
    if (preg_match('/^https?:\/\//i', $redirectPath) || str_starts_with($redirectPath, '//') || str_starts_with($redirectPath, '/')) {
        $redirectPath = '';
    }
}

$currentUser = gc_current_user();
if ($currentUser) {
    if ($redirectPath !== '') {
        header('Location: ' . $redirectPath);
        exit;
    }

    if ($currentUser['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: espace-membre.php');
    }
    exit;
}

// Message de succès après inscription
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $successMessage = 'Compte créé avec succès ! Tu peux maintenant te connecter.';
}
<<<<<<< HEAD
if (isset($_GET['deconnecte']) && $_GET['deconnecte'] === '1') {
    $successMessage = 'Tu as été déconnecté(e) avec succès.';
=======
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    $successMessage = 'Déconnexion réussie.';
>>>>>>> 14eabe1 (release v1.3)
}

// ── Traitement POST ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $erreurConnexion = 'Session expirée. Recharge la page puis réessaie.';
    }

    $email = trim(strtolower($_POST['email'] ?? ''));
    $mdp   = $_POST['password'] ?? '';

    if (empty($erreurConnexion) && (empty($email) || empty($mdp))) {
        $erreurConnexion = 'Veuillez remplir tous les champs.';
    } elseif (empty($erreurConnexion) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurConnexion = 'Format d\'email invalide.';
    } elseif (empty($erreurConnexion)) {
        // Connexion BDD
        require_once __DIR__ . '/../assets/php/config/db.php';
        try {
            $stmt = $pdo->prepare('SELECT id, pseudo, mdp_hash, role FROM utilisateurs WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($mdp, $user['mdp_hash'])) {
                $erreurConnexion = 'Email ou mot de passe incorrect.';
            } else {
<<<<<<< HEAD
                // Connexion réussie — session déjà démarrée en haut du fichier
=======
                // Connexion réussie — démarrage de session
                gc_start_session();
                session_regenerate_id(true);
>>>>>>> 14eabe1 (release v1.3)
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_pseudo'] = $user['pseudo'];
                $_SESSION['user_role']  = $user['role'];

                if ($redirectPath !== '') {
                    header('Location: ' . $redirectPath);
                    exit;
                }

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: espace-membre.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            error_log('[CONNEXION] ' . $e->getMessage());
            $erreurConnexion = 'Erreur de connexion à la base de données. Réessaie dans un moment.';
        }
    }
}

// ── En-tête de la page ────────────────────────────────────────────────────
$rootPath          = '../';
$pageTitle         = 'Connexion - Gaming Campus';
$metaDescription   = 'Connecte-toi à ton espace membre Gaming Campus.';
$cssSpecifique     = 'auth.css';
$jsSupplementaires = [];
include '../assets/php/components/header.php';
?>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">

        <section class="auth-section" aria-labelledby="titre-connexion">
            <div class="auth-container">
                <div class="auth-card">

                    <div class="auth-header">
                        <h1 id="titre-connexion">🔐 Connexion</h1>
                        <p>Connecte-toi pour accéder à ton espace membre et gérer tes tournois.</p>
                    </div>

                    <!-- Message de succès après inscription -->
                    <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success" role="alert">
                        ✅ <?= htmlspecialchars($successMessage) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Erreur de connexion -->
                    <?php if (!empty($erreurConnexion)): ?>
                    <div class="alert alert-error" role="alert">
                        ❌ <?= htmlspecialchars($erreurConnexion) ?>
                    </div>
                    <?php endif; ?>

                    <form class="auth-form" method="post" action="connexion.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(gc_csrf_token()) ?>">
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectPath) ?>">

                        <div class="form-group">
                            <label for="email">Adresse email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required
                                   placeholder="ton.email@campus.fr" autocomplete="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required
                                   placeholder="Ton mot de passe" autocomplete="current-password">
                        </div>

                        <div class="form-group form-options">
                            <div class="form-checkbox">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Se connecter</button>
                    </form>

                    <div class="auth-footer">
                        <p>Pas encore de compte ? <a href="inscription.php">Inscris-toi ici</a></p>
                    </div>

                </div>
            </div>
        </section>

    </main>

<?php include '../assets/php/components/footer.php'; ?>
