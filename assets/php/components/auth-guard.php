<?php
/**
 * auth-guard.php — Vérification d'authentification
 * Gaming Campus — Sprint 4
 *
 * Usage :
 *   require_once __DIR__ . '/auth-guard.php';
 *   authRequire();           // connexion requise (n'importe quel rôle)
 *   authRequire('admin');    // rôle admin requis
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function authRequire(string $role = '') {
    if (empty($_SESSION['user_id'])) {
        // Non connecté → vers la page de connexion
        $root = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2);
        header('Location: ' . $root . 'pages/connexion.php');
        exit;
    }
    if (!empty($role) && $_SESSION['user_role'] !== $role) {
        // Rôle insuffisant → retour à l'accueil
        $root = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2);
        header('Location: ' . $root . 'index.php');
        exit;
    }
}
