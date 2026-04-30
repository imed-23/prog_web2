<?php
/**
 * Connexion à la base de données PostgreSQL via PDO
 * Gaming Campus
 */

define('DB_HOST', getenv('PGHOST') ?: '127.0.0.1');
define('DB_PORT', (int) (getenv('PGPORT') ?: 5432));
define('DB_NAME', getenv('PGDATABASE') ?: 'gaming_campus');
define('DB_USER', getenv('PGUSER') ?: 'postgres');
define('DB_PASS', getenv('PGPASSWORD') ?: '');

$dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log('[DB ERROR] ' . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Connexion à la base de données impossible.',
    ]));
}
