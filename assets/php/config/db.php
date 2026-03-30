<?php
/**
 * Connexion à la base de données MySQL via PDO
 * Gaming Campus — Sprint 4
 *
 * Utilisation dans les autres fichiers :
 *   require_once __DIR__ . '/../config/db.php';
 *   $stmt = $pdo->prepare("SELECT ...");
 */

// ── Paramètres de connexion ────────────────────────────────────────────────
// 127.0.0.1 force une connexion TCP (important sous WSL, évite l'erreur socket "No such file or directory")
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', (int) (getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_NAME') ?: 'gaming_campus');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── Connexion PDO ──────────────────────────────────────────────────────────
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

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
        'message' => 'Connexion à la base de données impossible. Vérifiez que MySQL est démarré et que DB_HOST/DB_PORT sont corrects.',
    ]));
}
