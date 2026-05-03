<?php
// Traitement du formulaire d'inscription 

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
gc_start_session();

$erreurs         = [];
$success         = false;
$anciennesValeurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!gc_verify_csrf($_POST['csrf_token'] ?? null)) {
        $erreurs['db'] = 'Session expirée. Merci de recharger la page et réessayer.';
        return;
    }

    // Recuperation et nettoyage des champs
    $pseudo    = trim($_POST['pseudo']          ?? '');
    $prenom    = trim($_POST['prenom']          ?? '');
    $nom       = trim($_POST['nom']             ?? '');
    $email     = trim(strtolower($_POST['email'] ?? ''));
    $mdp       = $_POST['password']             ?? '';
    $mdpConfm  = $_POST['password_confirm']     ?? '';
    $jeu       = trim($_POST['jeu_principal']   ?? '');
    $cgu       = isset($_POST['cgu']);

    $anciennesValeurs = compact('pseudo', 'prenom', 'nom', 'email', 'jeu');

    // Validation du pseudo
    if (empty($pseudo)) {
        $erreurs['pseudo'] = 'Le pseudo est obligatoire.';
    } elseif (strlen($pseudo) < 3 || strlen($pseudo) > 20) {
        $erreurs['pseudo'] = 'Le pseudo doit contenir entre 3 et 20 caractères.';
    } elseif (!preg_match('/^[a-zA-Z0-9_\-]+$/', $pseudo)) {
        $erreurs['pseudo'] = 'Le pseudo ne peut contenir que des lettres, chiffres, _ et -.';
    }

    if (empty($prenom)) $erreurs['prenom'] = 'Le prénom est obligatoire.';
    if (empty($nom))    $erreurs['nom']    = 'Le nom est obligatoire.';

    // Validation email
    if (empty($email)) {
        $erreurs['email'] = 'L\'adresse email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
    }

    // Validation mot de passe
    if (empty($mdp)) {
        $erreurs['password'] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($mdp) < 8) {
        $erreurs['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!preg_match('/[A-Z]/', $mdp)) {
        $erreurs['password'] = 'Le mot de passe doit contenir au moins une lettre majuscule.';
    } elseif (!preg_match('/[0-9]/', $mdp)) {
        $erreurs['password'] = 'Le mot de passe doit contenir au moins un chiffre.';
    }

    if (empty($mdpConfm)) {
        $erreurs['password_confirm'] = 'La confirmation du mot de passe est obligatoire.';
    } elseif ($mdp !== $mdpConfm) {
        $erreurs['password_confirm'] = 'Les mots de passe ne correspondent pas.';
    }

    if (!$cgu) {
        $erreurs['cgu'] = 'Vous devez accepter les conditions générales d\'utilisation.';
    }

    // Jeu principal : liste blanche
    $jeuxAutorises = ['lol', 'valorant', 'cs2', 'fortnite', 'rocket-league', 'autre', ''];
    if (!in_array($jeu, $jeuxAutorises, true)) {
        $jeu = '';
    }

    // Verification unicite email et pseudo
    if (empty($erreurs['email']) || empty($erreurs['pseudo'])) {
        try {
            if (empty($erreurs['email'])) {
                $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $erreurs['email'] = 'Cette adresse email est déjà utilisée. <a href="connexion.php">Se connecter ?</a>';
                }
            }

            if (empty($erreurs['pseudo'])) {
                $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE pseudo = ? LIMIT 1');
                $stmt->execute([$pseudo]);
                if ($stmt->fetch()) {
                    $erreurs['pseudo'] = 'Ce pseudo est déjà pris. Choisis-en un autre.';
                }
            }
        } catch (PDOException $e) {
            error_log('[INSCRIPTION] ' . $e->getMessage());
            $erreurs['db'] = 'Une erreur est survenue. Réessaie dans un moment.';
        }
    }

    // Gestion de l'avatar
    $avatarPath = null;

    if (isset($_FILES['avatar']) && (int)$_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize      = 2 * 1024 * 1024;

        $mimeType = false;
        if (class_exists('finfo')) {
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['avatar']['tmp_name']);
        } else {
            $mimeType = $_FILES['avatar']['type'];
        }
        
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!$mimeType || !in_array($mimeType, $allowedTypes, true) || !in_array($ext, $allowedExts, true)) {
            $erreurs['avatar'] = 'Format d\'image non autorisé (JPG, PNG, WebP ou GIF uniquement).';
        } elseif ($_FILES['avatar']['size'] > $maxSize) {
            $erreurs['avatar'] = 'L\'image dépasse la taille maximale de 2 Mo.';
        } else {
            $uploadDir = __DIR__ . '/../../../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename   = uniqid('avatar_', true) . '.' . $ext;
            $destPath   = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                $avatarPath = 'uploads/avatars/' . $filename;
            } else {
                $erreurs['avatar'] = 'Erreur lors de l\'upload de l\'avatar.';
            }
        }
    }

    // Insertion en BDD si tout est ok
    if (empty($erreurs)) {
        try {
            $mdpHash = password_hash($mdp, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare('
                INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp_hash, avatar, jeu_principal, role)
                VALUES (:pseudo, :prenom, :nom, :email, :mdp_hash, :avatar, :jeu_principal, \'visiteur\')
            ');
            $stmt->execute([
                ':pseudo'        => $pseudo,
                ':prenom'        => $prenom,
                ':nom'           => $nom,
                ':email'         => $email,
                ':mdp_hash'      => $mdpHash,
                ':avatar'        => $avatarPath,
                ':jeu_principal' => $jeu ?: null,
            ]);

            header('Location: connexion.php?success=1');
            exit;

        } catch (PDOException $e) {
            error_log('[INSCRIPTION INSERT] ' . $e->getMessage());
            if (($e->getCode() === '23000') || str_contains(strtolower($e->getMessage()), 'duplicate')) {
                $erreurs['db'] = 'Ce compte existe déjà (email ou pseudo déjà utilisé).';
            } else {
                $erreurs['db'] = 'Une erreur est survenue lors de la création du compte. Réessaie dans un moment.';
            }
        }
    }
}
