-- ============================================================
-- Gaming Campus — Script d'initialisation de la base de données
-- Sprint 4 : Formulaire d'inscription
--
-- Instructions :
--   1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
--   2. Cliquez sur "SQL" dans le menu du haut
--   3. Copiez-collez tout ce fichier
--   4. Cliquez sur "Exécuter"
-- ============================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `gaming_campus`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `gaming_campus`;

-- ============================================================
-- Table : utilisateurs
-- ============================================================
CREATE TABLE IF NOT EXISTS `utilisateurs` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `pseudo`        VARCHAR(20)     NOT NULL UNIQUE,
    `prenom`        VARCHAR(50)     NOT NULL,
    `nom`           VARCHAR(50)     NOT NULL,
    `email`         VARCHAR(150)    NOT NULL UNIQUE,
    `mdp_hash`      VARCHAR(255)    NOT NULL,
    `avatar`        VARCHAR(255)    DEFAULT NULL,
    `jeu_principal` VARCHAR(50)     DEFAULT NULL,
    `role`          ENUM('visiteur','capitaine','admin') NOT NULL DEFAULT 'visiteur',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email`  (`email`),
    INDEX `idx_pseudo` (`pseudo`),
    INDEX `idx_role`   (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : tournois
-- ============================================================
CREATE TABLE IF NOT EXISTS `tournois` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nom`           VARCHAR(100)    NOT NULL,
    `jeu`           VARCHAR(50)     NOT NULL,
    `date_debut`    DATETIME        NOT NULL,
    `lieu`          VARCHAR(100)    DEFAULT 'Campus',
    `nb_places`     TINYINT UNSIGNED NOT NULL DEFAULT 16,
    `cashprize`     DECIMAL(8,2)    DEFAULT 0.00,
    `description`   TEXT            DEFAULT NULL,
    `statut`        ENUM('a-venir','en-cours','termine') NOT NULL DEFAULT 'a-venir',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_statut` (`statut`),
    INDEX `idx_jeu`    (`jeu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : reservations
-- ============================================================
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `tournoi_id`    INT UNSIGNED    NOT NULL,
    `capitaine_id`  INT UNSIGNED    NOT NULL,
    `nom_equipe`    VARCHAR(50)     NOT NULL,
    `statut`        ENUM('en-attente','confirmee','annulee') NOT NULL DEFAULT 'en-attente',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`tournoi_id`)   REFERENCES `tournois`(`id`)      ON DELETE CASCADE,
    FOREIGN KEY (`capitaine_id`) REFERENCES `utilisateurs`(`id`)  ON DELETE CASCADE,
    UNIQUE KEY `uq_equipe_tournoi` (`tournoi_id`, `capitaine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Compte administrateur de test (mdp : Admin1234!)
-- SUPPRIMER EN PRODUCTION
-- ============================================================
INSERT IGNORE INTO `utilisateurs`
    (`pseudo`, `prenom`, `nom`, `email`, `mdp_hash`, `role`)
VALUES (
    'admin',
    'Admin',
    'BDE',
    'admin@gamingcampus.fr',
    '$2y$12$OFfwEw6G6C05CDwuSNZTmu3iBNHj3L8VGx735Cqf4ZXaE.W14h1T.', -- password: Admin1234!
    'admin'
);
