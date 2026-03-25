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
define('DB_HOST', 'localhost');
define('DB_NAME', 'gaming_campus');
define('DB_USER', 'root');       // Utilisateur XAMPP par défaut
define('DB_PASS', '');           // Mot de passe XAMPP par défaut (vide)
define('DB_CHARSET', 'utf8mb4');

// ── Connexion PDO ──────────────────────────────────────────────────────────
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Lance des exceptions en cas d'erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Vraies requêtes préparées
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // En production : logger l'erreur sans l'afficher
    error_log('[DB ERROR] ' . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Connexion à la base de données impossible. Vérifiez que XAMPP/MySQL est démarré.',
    ]));
}
