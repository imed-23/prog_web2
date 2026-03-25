<?php
/**
 * Connexion à la base de données PostgreSQL via PDO
 * Gaming Campus — Sprint 4
 *
 * Utilisation dans les autres fichiers :
 *   require_once __DIR__ . '/../config/db.php';
 *   $stmt = $pdo->prepare("SELECT ...");
 */

// ── Paramètres de connexion (depuis les variables d'environnement Replit) ──
$db_host    = getenv('PGHOST')     ?: 'localhost';
$db_port    = getenv('PGPORT')     ?: '5432';
$db_name    = getenv('PGDATABASE') ?: 'gaming_campus';
$db_user    = getenv('PGUSER')     ?: 'postgres';
$db_pass    = getenv('PGPASSWORD') ?: '';

// ── Connexion PDO ──────────────────────────────────────────────────────────
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    error_log('[DB ERROR] ' . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Connexion à la base de données impossible.',
    ]));
}
