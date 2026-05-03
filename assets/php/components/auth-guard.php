<?php
// verifie l'authentification et les roles
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function authRequire(string $role = '') {
    if (empty($_SESSION['user_id'])) {
        header('Location: /pages/connexion.php');
        exit;
    }
    if (!empty($role) && $_SESSION['user_role'] !== $role) {
        header('Location: /index.php');
        exit;
    }
}
