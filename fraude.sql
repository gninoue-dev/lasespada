-- ============================================
--  DETECTION DE FRAUDE ASSURANCE
--  Script SQL -- MySQL 8
--  Ordre respectant les contraintes FK
-- ============================================

CREATE DATABASE IF NOT EXISTS fraude_assurance
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE fraude_assurance;

-- ============================================
-- TABLE 1 : administrateurs (aucune dépendance)
-- ============================================
CREATE TABLE administrateurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,          -- bcrypt
    role          ENUM('agent','super_admin') NOT NULL DEFAULT 'agent',
    date_creation DATETIME DEFAULT NOW()
);

-- ============================================
-- TABLE 2 : utilisateurs (aucune dépendance)
-- ============================================
CREATE TABLE utilisateurs (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    nom               VARCHAR(100) NOT NULL,
    prenom            VARCHAR(100) NOT NULL,
    email             VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe      VARCHAR(255) NOT NULL,      -- bcrypt
    telephone         VARCHAR(20),
    adresse_ip        VARCHAR(45),                -- détection multi-comptes
    score_global      INT DEFAULT 0,              -- score cumulé fraude
    statut            ENUM('normal','douteux','frauduleux') DEFAULT 'normal',
    date_inscription  DATETIME DEFAULT NOW()
);

-- ============================================
-- TABLE 3 : sinistres (dépend de utilisateurs)
-- ============================================
CREATE TABLE sinistres (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id      INT NOT NULL,
    type_sinistre       VARCHAR(80) NOT NULL,
    date_sinistre       DATE NOT NULL,
    date_declaration    DATETIME DEFAULT NOW(),
    description         TEXT,
    montant_estime      DECIMAL(15,2),
    localisation        VARCHAR(200),
    score_sinistre      INT DEFAULT 0,            -- score calculé automatiquement
    statut              ENUM('normal','douteux','frauduleux') DEFAULT 'normal',
    commentaire_admin   TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE 4 : preuves (dépend de sinistres)
-- ============================================
CREATE TABLE preuves (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    sinistre_id     INT NOT NULL,
    nom_fichier     VARCHAR(200),
    chemin_fichier  VARCHAR(300),
    type_fichier    VARCHAR(20),                  -- jpg, png, pdf
    date_exif       DATETIME,                     -- date issue des métadonnées EXIF
    gps_exif        VARCHAR(100),                 -- coordonnées GPS EXIF
    score_metadata  INT DEFAULT 0,               -- points ajoutés par l'analyse EXIF
    date_upload     DATETIME DEFAULT NOW(),
    FOREIGN KEY (sinistre_id) REFERENCES sinistres(id) ON DELETE CASCADE
);

-- ============================================
-- TABLE 5 : alertes (dépend de sinistres + utilisateurs)
-- ============================================
CREATE TABLE alertes (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    sinistre_id         INT NOT NULL,
    utilisateur_id      INT NOT NULL,
    type_alerte         VARCHAR(80),              -- 'score_eleve', 'exif_suspect', etc.
    score_declencheur   INT,                      -- score au moment de l'alerte
    message             TEXT,
    statut              ENUM('en_attente','en_cours','resolue') DEFAULT 'en_attente',
    traite_par          INT,                      -- FK vers administrateurs(id)
    date_alerte         DATETIME DEFAULT NOW(),
    FOREIGN KEY (sinistre_id)    REFERENCES sinistres(id)       ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)    ON DELETE CASCADE,
    FOREIGN KEY (traite_par)     REFERENCES administrateurs(id) ON DELETE SET NULL
);

-- ============================================
-- DONNÉES DE TEST
-- ============================================

-- Admin par défaut (mot de passe : admin123 -- à hasher en PHP en prod)
INSERT INTO administrateurs (nom, email, mot_de_passe, role)
VALUES ('Super Admin', 'admin@fraude.ci', '$2y$10$exampleHashedPasswordHere', 'super_admin');

-- Utilisateurs de test
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse_ip)
VALUES
    ('Koné', 'Mamadou', 'mamadou@test.ci', '$2y$10$hash1', '0701000001', '192.168.1.10'),
    ('Traoré', 'Aïcha', 'aicha@test.ci', '$2y$10$hash2', '0702000002', '192.168.1.11'),
    ('Bamba', 'Seydou', 'seydou@test.ci', '$2y$10$hash3', '0703000003', '192.168.1.10'); -- même IP = suspect

-- Sinistres de test
INSERT INTO sinistres (utilisateur_id, type_sinistre, date_sinistre, description, montant_estime, localisation, score_sinistre, statut)
VALUES
    (1, 'Accident automobile', '2026-04-20', 'Collision sur l autoroute du nord, véhicule détruit', 3500000, 'Abidjan, Autoroute du Nord', 45, 'douteux'),
    (2, 'Vol de moto',         '2026-04-28', 'Moto disparue devant le marché, urgent besoin remboursement', 1200000, 'Adjamé, Marché', 70, 'douteux'),
    (3, 'Incendie domicile',   '2026-05-01', 'Incendie dans la nuit, tout détruit rapidement', 8000000, 'Yopougon', 95, 'frauduleux');