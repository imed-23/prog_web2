<?php
/**
 * Connexion à la base de données PostgreSQL via PDO
 * Gaming Campus — Sprint 4
 *
 * Utilisation dans les autres fichiers :
 *   require_once __DIR__ . '/../config/db.php';
 *   $stmt = $pdo->prepare("SELECT ...");
 */

<<<<<<< HEAD
// ── Paramètres de connexion (depuis les variables d'environnement Replit) ──
$db_host    = getenv('PGHOST')     ?: 'localhost';
$db_port    = getenv('PGPORT')     ?: '5432';
$db_name    = getenv('PGDATABASE') ?: 'gaming_campus';
$db_user    = getenv('PGUSER')     ?: 'postgres';
$db_pass    = getenv('PGPASSWORD') ?: '';

// ── Connexion PDO ──────────────────────────────────────────────────────────
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name}";
=======
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
>>>>>>> 14eabe1 (release v1.3)

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
<<<<<<< HEAD
        'message' => 'Connexion à la base de données impossible.',
=======
        'message' => 'Connexion à la base de données impossible. Vérifiez que MySQL est démarré et que DB_HOST/DB_PORT sont corrects.',
>>>>>>> 14eabe1 (release v1.3)
    ]));
}
