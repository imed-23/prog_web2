<?php
// seeder.php
// Ce script permet de réinitialiser la base de données avec des données factices pour tester.

require_once __DIR__ . '/assets/php/config/db.php';

// Sécurité : n'exécuter qu'en CLI ou forcer un paramètre GET
if (php_sapi_name() !== 'cli' && empty($_GET['force'])) {
    die("Ce script doit être exécuté en ligne de commande (php seeder.php) ou avec ?force=1");
}

echo "🎮 Démarrage du Seeder Gaming Campus...\n\n";

try {
    $pdo->beginTransaction();

    echo "1. Nettoyage des anciennes données...\n";
    // Supprimer tout sauf l'administrateur
    $pdo->exec("DELETE FROM demandes_capitaine");
    $pdo->exec("DELETE FROM reservations");
    $pdo->exec("DELETE FROM tournois");
    $pdo->exec("DELETE FROM utilisateurs WHERE role != 'admin'");

    echo "2. Création de l'administrateur système (s'il n'existe pas)...\n";
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->exec("
            INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp_hash, role)
            VALUES ('AdminGC', 'Admin', 'Campus', 'admin@campus.fr', '" . password_hash('Admin123!', PASSWORD_BCRYPT) . "', 'admin')
        ");
        echo "   -> Admin 'admin@campus.fr' créé.\n";
    }

    echo "3. Création des utilisateurs de test...\n";
    $users = [
        ['pseudo' => 'JoueurPro', 'prenom' => 'Leo', 'nom' => 'Martin', 'email' => 'leo@campus.fr', 'role' => 'capitaine', 'jeu' => 'lol'],
        ['pseudo' => 'SniperElite', 'prenom' => 'Marc', 'nom' => 'Dubois', 'email' => 'marc@campus.fr', 'role' => 'capitaine', 'jeu' => 'valorant'],
        ['pseudo' => 'NoobMaster', 'prenom' => 'Paul', 'nom' => 'Bernard', 'email' => 'paul@campus.fr', 'role' => 'visiteur', 'jeu' => 'fortnite'],
        ['pseudo' => 'CarryMe', 'prenom' => 'Sophie', 'nom' => 'Leroy', 'email' => 'sophie@campus.fr', 'role' => 'visiteur', 'jeu' => 'lol'],
        ['pseudo' => 'ToxicPlayer', 'prenom' => 'Lucas', 'nom' => 'Richard', 'email' => 'lucas@campus.fr', 'role' => 'visiteur', 'jeu' => 'cs2'],
        ['pseudo' => 'FakerFR', 'prenom' => 'Hugo', 'nom' => 'Petit', 'email' => 'hugo@campus.fr', 'role' => 'capitaine', 'jeu' => 'lol'],
        ['pseudo' => 'RageQuit', 'prenom' => 'Emma', 'nom' => 'Durand', 'email' => 'emma@campus.fr', 'role' => 'capitaine', 'jeu' => 'rocket-league']
    ];

    $mdpHash = password_hash('Test1234!', PASSWORD_BCRYPT);
    $stmtUser = $pdo->prepare("INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp_hash, role, jeu_principal) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $userIds = [];

    foreach ($users as $u) {
        $stmtUser->execute([$u['pseudo'], $u['prenom'], $u['nom'], $u['email'], $mdpHash, $u['role'], $u['jeu']]);
        $userIds[$u['pseudo']] = $pdo->lastInsertId();
    }
    echo "   -> " . count($users) . " utilisateurs ajoutés.\n";

    echo "4. Création des demandes de capitaine factices...\n";
    $stmtDemande = $pdo->prepare("INSERT INTO demandes_capitaine (user_id, nom_equipe, jeu, statut) VALUES (?, ?, ?, ?)");
    // NoobMaster demande à être capitaine de son équipe Fortnite
    $stmtDemande->execute([$userIds['NoobMaster'], 'Les PGM du 93', 'fortnite', 'en-attente']);
    // CarryMe a été refusée
    $stmtDemande->execute([$userIds['CarryMe'], 'Trolls', 'lol', 'refusee']);
    echo "   -> 2 demandes ajoutées (1 attente, 1 refusée).\n";

    echo "5. Création des tournois...\n";
    $tournois = [
        ['nom' => 'Rift Rivals Summer', 'jeu' => 'lol', 'date_debut' => date('Y-m-d H:i:s', strtotime('+5 days')), 'nb_places' => 8, 'cashprize' => 500, 'statut' => 'a-venir'],
        ['nom' => 'Valorant Cup #3', 'jeu' => 'valorant', 'date_debut' => date('Y-m-d H:i:s', strtotime('+2 days')), 'nb_places' => 16, 'cashprize' => 300, 'statut' => 'a-venir'],
        ['nom' => 'CS2 Campus Major', 'jeu' => 'cs2', 'date_debut' => date('Y-m-d H:i:s', strtotime('-1 days')), 'nb_places' => 4, 'cashprize' => 1000, 'statut' => 'en-cours'],
        ['nom' => 'Rocket League 2v2', 'jeu' => 'rocket-league', 'date_debut' => date('Y-m-d H:i:s', strtotime('+10 days')), 'nb_places' => 32, 'cashprize' => 100, 'statut' => 'a-venir'],
        ['nom' => 'Fortnite Battle Royale', 'jeu' => 'fortnite', 'date_debut' => date('Y-m-d H:i:s', strtotime('-15 days')), 'nb_places' => 100, 'cashprize' => 0, 'statut' => 'termine']
    ];

    $stmtTournoi = $pdo->prepare("INSERT INTO tournois (nom, jeu, date_debut, lieu, nb_places, cashprize, description, statut) VALUES (?, ?, ?, 'Salle eSport', ?, ?, 'Tournoi de test', ?)");
    $tournoiIds = [];

    foreach ($tournois as $t) {
        $stmtTournoi->execute([$t['nom'], $t['jeu'], $t['date_debut'], $t['nb_places'], $t['cashprize'], $t['statut']]);
        $tournoiIds[$t['jeu']] = $pdo->lastInsertId(); // Associe le jeu à l'ID pour simplifier les inscriptions
    }
    echo "   -> " . count($tournois) . " tournois créés.\n";

    echo "6. Inscription des équipes aux tournois...\n";
    $stmtResa = $pdo->prepare("INSERT INTO reservations (tournoi_id, capitaine_id, nom_equipe, statut) VALUES (?, ?, ?, ?)");
    
    // JoueurPro inscrit à LoL
    $stmtResa->execute([$tournoiIds['lol'], $userIds['JoueurPro'], 'Alpha Team', 'confirmee']);
    // FakerFR inscrit à LoL
    $stmtResa->execute([$tournoiIds['lol'], $userIds['FakerFR'], 'SKT T1', 'en-attente']);
    // SniperElite inscrit à Valorant
    $stmtResa->execute([$tournoiIds['valorant'], $userIds['SniperElite'], 'One Tap', 'confirmee']);
    // RageQuit inscrit à Rocket League
    $stmtResa->execute([$tournoiIds['rocket-league'], $userIds['RageQuit'], 'Toxic Cars', 'annulee']);

    echo "   -> 4 réservations ajoutées (2 confirmées, 1 attente, 1 annulée).\n";

    $pdo->commit();
    echo "\n✅ SEEDING TERMINÉ AVEC SUCCÈS !\n";
    echo "Vous pouvez vous connecter avec n'importe quel email ci-dessus et le mot de passe 'Test1234!'\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "\n❌ ERREUR : " . $e->getMessage() . "\n";
}
