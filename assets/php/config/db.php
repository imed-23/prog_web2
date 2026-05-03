<?php
// Connexion SQLite via PDO
// La base est creee automatiquement si elle n'existe pas encore

define('DB_PATH', __DIR__ . '/../../../db/gaming_campus.sqlite');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $dbDir = dirname(DB_PATH);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    $isNew = !file_exists(DB_PATH);
    $pdo   = new PDO('sqlite:' . DB_PATH, null, null, $options);
    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Si la base vient d'etre creee, on initialise le schema
    if ($isNew) {
        $sql = file_get_contents(__DIR__ . '/../../sql/init.sql');
        if ($sql) {
            $pdo->exec($sql);
        }
    }
} catch (PDOException $e) {
    error_log('[DB ERROR] ' . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Connexion à la base de données impossible.',
    ]));
}
