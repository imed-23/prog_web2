<?php
/**
 * Helpers d'authentification/sessions
 */

function gc_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

function gc_current_user(): ?array
{
    gc_start_session();

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'pseudo' => (string) ($_SESSION['user_pseudo'] ?? ''),
        'role' => (string) ($_SESSION['user_role'] ?? 'visiteur'),
    ];
}

function gc_is_logged_in(): bool
{
    return gc_current_user() !== null;
}

function gc_require_login(string $loginPath): void
{
    if (gc_is_logged_in()) {
        return;
    }

    header('Location: ' . $loginPath);
    exit;
}

function gc_require_admin(string $loginPath): void
{
    $user = gc_current_user();
    if ($user !== null && $user['role'] === 'admin') {
        return;
    }

    header('Location: ' . $loginPath);
    exit;
}

function gc_csrf_token(): string
{
    gc_start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function gc_verify_csrf(?string $token): bool
{
    gc_start_session();
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals((string) $_SESSION['csrf_token'], (string) $token);
}
