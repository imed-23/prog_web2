-- Gaming Campus — Schema SQLite
-- Execute automatiquement par install.php au premier lancement

PRAGMA foreign_keys = ON;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id            INTEGER  PRIMARY KEY AUTOINCREMENT,
    pseudo        TEXT     NOT NULL UNIQUE,
    prenom        TEXT     NOT NULL,
    nom           TEXT     NOT NULL,
    email         TEXT     NOT NULL UNIQUE,
    mdp_hash      TEXT     NOT NULL,
    avatar        TEXT     DEFAULT NULL,
    jeu_principal TEXT     DEFAULT NULL,
    role          TEXT     NOT NULL DEFAULT 'visiteur'
                           CHECK(role IN ('visiteur', 'capitaine', 'admin')),
    created_at    TEXT     NOT NULL DEFAULT (datetime('now')),
    updated_at    TEXT     NOT NULL DEFAULT (datetime('now'))
);

-- Table des tournois
CREATE TABLE IF NOT EXISTS tournois (
    id            INTEGER  PRIMARY KEY AUTOINCREMENT,
    nom           TEXT     NOT NULL,
    jeu           TEXT     NOT NULL,
    image         TEXT     DEFAULT NULL,
    date_debut    TEXT     NOT NULL,
    lieu          TEXT     DEFAULT 'Campus',
    nb_places     INTEGER  NOT NULL DEFAULT 16,
    cashprize     REAL     DEFAULT 0.00,
    description   TEXT     DEFAULT NULL,
    statut        TEXT     NOT NULL DEFAULT 'a-venir'
                           CHECK(statut IN ('a-venir', 'en-cours', 'termine')),
    created_at    TEXT     NOT NULL DEFAULT (datetime('now'))
);

-- Table des reservations (inscriptions equipe a un tournoi)
CREATE TABLE IF NOT EXISTS reservations (
    id            INTEGER  PRIMARY KEY AUTOINCREMENT,
    tournoi_id    INTEGER  NOT NULL,
    capitaine_id  INTEGER  NOT NULL,
    nom_equipe    TEXT     NOT NULL,
    statut        TEXT     NOT NULL DEFAULT 'en-attente'
                           CHECK(statut IN ('en-attente', 'confirmee', 'annulee')),
    created_at    TEXT     NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (tournoi_id)   REFERENCES tournois(id)      ON DELETE CASCADE,
    FOREIGN KEY (capitaine_id) REFERENCES utilisateurs(id)  ON DELETE CASCADE,
    UNIQUE (tournoi_id, capitaine_id)
);

-- Trigger : empeche l'inscription si le tournoi est complet
CREATE TRIGGER IF NOT EXISTS prevent_overbooking
BEFORE INSERT ON reservations
FOR EACH ROW
WHEN (
    (SELECT COUNT(*) FROM reservations WHERE tournoi_id = NEW.tournoi_id AND statut <> 'annulee')
    >= 
    (SELECT nb_places FROM tournois WHERE id = NEW.tournoi_id)
)
BEGIN
    SELECT RAISE(ABORT, 'Ce tournoi est complet.');
END;

-- Trigger : met a jour updated_at automatiquement
CREATE TRIGGER IF NOT EXISTS update_utilisateurs_updated_at
    AFTER UPDATE ON utilisateurs
    FOR EACH ROW
BEGIN
    UPDATE utilisateurs SET updated_at = datetime('now') WHERE id = OLD.id;
END;

-- Compte admin par defaut (mdp : Admin1234!)
INSERT OR IGNORE INTO utilisateurs
    (pseudo, prenom, nom, email, mdp_hash, role)
VALUES (
    'admin',
    'Admin',
    'BDE',
    'admin@gamingcampus.fr',
    '$2y$12$OFfwEw6G6C05CDwuSNZTmu3iBNHj3L8VGx735Cqf4ZXaE.W14h1T.',
    'admin'
);
CREATE TABLE IF NOT EXISTS demandes_capitaine (
    id          INTEGER  PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER  NOT NULL,
    nom_equipe  TEXT     NOT NULL,
    jeu         TEXT     NOT NULL,
    message     TEXT     DEFAULT NULL,
    statut      TEXT     NOT NULL DEFAULT 'en-attente'
                         CHECK(statut IN ('en-attente', 'approuvee', 'refusee')),
    created_at  TEXT     NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

