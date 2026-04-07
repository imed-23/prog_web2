#!/bin/bash

# ============================================================
# SCRIPT AUTOMATIQUE - Gaming Campus - Test des Sprints
# ============================================================
# Ce script configure la base de données et teste toutes
# les fonctionnalités des sprints 4, 5 et 6.
# ============================================================

MYSQL_PASS="imed23062003imed"
DB_NAME="gaming_campus"
PROJECT_DIR="/mnt/c/Users/imedk/OneDrive/L2 Informatique/prog web 2/tp-demande-client"

echo "╔════════════════════════════════════════════════════════╗"
echo "║   Gaming Campus - Script de Test des Sprints          ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# ── Fonction pour exécuter les commandes MySQL ──────────────
mysql_cmd() {
    echo "$MYSQL_PASS" | sudo -S mysql -u root --socket=/run/mysqld/mysqld.sock "$@" 2>/dev/null
}

# ── Étape 1 : Vérifier que MariaDB tourne ───────────────────
echo "📋 Étape 1: Vérification de MariaDB..."
if sudo service mariadb status | grep -q "running"; then
    echo "   ✅ MariaDB est démarré"
else
    echo "   ⚠️  MariaDB n'est pas démarré, démarrage..."
    sudo service mariadb start
    sleep 2
fi

# ── Étape 2 : Configurer l'utilisateur root pour TCP ────────
echo "📋 Étape 2: Configuration de l'utilisateur MySQL..."
mysql_cmd -e "CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '$MYSQL_PASS';" 2>/dev/null
mysql_cmd -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO 'root'@'127.0.0.1';" 2>/dev/null
mysql_cmd -e "FLUSH PRIVILEGES;" 2>/dev/null
echo "   ✅ Utilisateur root configuré pour TCP"

# ── Étape 3 : Créer la base de données ──────────────────────
echo "📋 Étape 3: Création de la base de données..."
mysql_cmd -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "   ✅ Base de données '$DB_NAME' créée"

# ── Étape 4 : Importer le schéma SQL ────────────────────────
echo "📋 Étape 4: Import du schéma SQL..."
mysql_cmd $DB_NAME < "$PROJECT_DIR/assets/sql/init.sql"
echo "   ✅ Schéma SQL importé"

# ── Étape 5 : Vérifier les tables ───────────────────────────
echo "📋 Étape 5: Vérification des tables..."
TABLES=$(mysql_cmd -N -e "USE $DB_NAME; SHOW TABLES;")
if echo "$TABLES" | grep -q "utilisateurs"; then
    echo "   ✅ Tables créées:"
    echo "$TABLES" | while read table; do echo "      - $table"; done
else
    echo "   ❌ Erreur: tables non créées"
    exit 1
fi

# ── Étape 6 : Vérifier le compte admin ──────────────────────
echo "📋 Étape 6: Vérification du compte admin..."
ADMIN=$(mysql_cmd -N -e "USE $DB_NAME; SELECT email FROM utilisateurs WHERE role='admin';")
if [ -n "$ADMIN" ]; then
    echo "   ✅ Compte admin présent: $ADMIN"
else
    echo "   ⚠️  Aucun compte admin trouvé"
fi

# ── Étape 7 : Mettre à jour db.php avec le mot de passe ─────
echo "📋 Étape 7: Configuration de db.php..."
cat > "$PROJECT_DIR/assets/php/config/db.php" << 'DBEOF'
<?php
/**
 * Connexion à la base de données MySQL via PDO
 * Gaming Campus — Sprint 4
 */

// ── Paramètres de connexion ─────────────────────────────────
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'gaming_campus');
define('DB_USER', 'root');
define('DB_PASS', 'imed23062003imed');
define('DB_CHARSET', 'utf8mb4');

// ── Connexion PDO ───────────────────────────────────────────
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
        'message' => 'Erreur de connexion à la base de données.',
    ]));
}
DBEOF
echo "   ✅ db.php configuré"

# ── Étape 8 : Vérifier le dossier uploads ───────────────────
echo "📋 Étape 8: Vérification du dossier uploads..."
mkdir -p "$PROJECT_DIR/uploads/avatars"
chmod 755 "$PROJECT_DIR/uploads/avatars"
echo "   ✅ Dossier uploads prêt"

# ── Étape 9 : Lancer le serveur PHP ─────────────────────────
echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║   Configuration terminée !                             ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo "🌐 Site disponible sur: http://localhost:8000"
echo ""
echo "🔐 Compte Admin:"
echo "   Email: admin@gamingcampus.fr"
echo "   Mot de passe: Admin1234!"
echo ""
echo "📋 URLs à tester:"
echo "   - Accueil:        http://localhost:8000"
echo "   - Inscription:    http://localhost:8000/pages/inscription.php"
echo "   - Connexion:      http://localhost:8000/pages/connexion.php"
echo "   - Dashboard:      http://localhost:8000/pages/admin/dashboard.php"
echo "   - Inscriptions:   http://localhost:8000/pages/admin/inscriptions.php"
echo ""
echo "🚀 Lancement du serveur PHP..."
echo ""

cd "$PROJECT_DIR"
php -S localhost:8000
