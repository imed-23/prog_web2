-- initialisation base postgresql
-- equivalent de init.sql

-- table: utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id              SERIAL PRIMARY KEY,
    pseudo          VARCHAR(20)  NOT NULL UNIQUE,
    prenom          VARCHAR(50)  NOT NULL,
    nom             VARCHAR(50)  NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    mdp_hash        VARCHAR(255) NOT NULL,
    avatar          VARCHAR(255),
    jeu_principal   VARCHAR(50),
    role            VARCHAR(20)  NOT NULL DEFAULT 'visiteur'
                    CHECK (role IN ('visiteur','capitaine','admin')),
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- table: tournois
CREATE TABLE IF NOT EXISTS tournois (
    id           SERIAL PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL,
    jeu          VARCHAR(50)  NOT NULL,
    date_debut   TIMESTAMPTZ  NOT NULL,
    lieu         VARCHAR(100),
    nb_places    INT NOT NULL DEFAULT 8,
    cashprize    INT NOT NULL DEFAULT 0,
    description  TEXT,
    statut       VARCHAR(20)  NOT NULL DEFAULT 'a-venir'
                 CHECK (statut IN ('a-venir','en-cours','termine','annule')),
    created_at   TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- table: reservations
CREATE TABLE IF NOT EXISTS reservations (
    id            SERIAL PRIMARY KEY,
    tournoi_id    INT NOT NULL REFERENCES tournois(id)      ON DELETE CASCADE,
    capitaine_id  INT NOT NULL REFERENCES utilisateurs(id)  ON DELETE CASCADE,
    nom_equipe    VARCHAR(100) NOT NULL,
    statut        VARCHAR(20)  NOT NULL DEFAULT 'en-attente'
                  CHECK (statut IN ('en-attente','confirmee','annulee')),
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (tournoi_id, capitaine_id)
);

-- table: contacts
CREATE TABLE IF NOT EXISTS contacts (
    id          SERIAL PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    sujet       VARCHAR(50)  NOT NULL,
    message     TEXT         NOT NULL,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- compte admin (mdp : Admin1234!)
INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp_hash, role)
VALUES (
    'admin', 'Admin', 'BDE',
    'admin@gamingcampus.fr',
    '$2y$12$OFfwEw6G6C05CDwuSNZTmu3iBNHj3L8VGx735Cqf4ZXaE.W14h1T.',
    'admin'
)
ON CONFLICT (email) DO NOTHING;
