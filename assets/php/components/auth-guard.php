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
        header('Location: /pages/connexion.php');
        exit;
    }
    if (!empty($role) && $_SESSION['user_role'] !== $role) {
        header('Location: /index.php');
        exit;
    }
}
