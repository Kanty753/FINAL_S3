CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;

-- Disable foreign key checks to allow dropping tables in any order
SET FOREIGN_KEY_CHECKS = 0;

-- Drop child tables first to avoid FK constraint errors
DROP TABLE IF EXISTS dispatch;
DROP TABLE IF EXISTS don;
DROP TABLE IF EXISTS besoin;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS type_besoin;
DROP TABLE IF EXISTS ville;
DROP TABLE IF EXISTS region;

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================
-- TABLE REGION
-- ==========================
CREATE TABLE IF NOT EXISTS region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ==========================
-- TABLE VILLE
-- ==========================
CREATE TABLE IF NOT EXISTS ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES region(id)
) ENGINE=InnoDB;

-- ==========================
-- TABLE TYPE_BESOIN
-- ==========================
CREATE TABLE IF NOT EXISTS type_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- ==========================
-- TABLE ARTICLE
-- ==========================
CREATE TABLE IF NOT EXISTS article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type_besoin_id INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (type_besoin_id) REFERENCES type_besoin(id)
) ENGINE=InnoDB;

-- ==========================
-- TABLE BESOIN
-- ==========================
CREATE TABLE IF NOT EXISTS besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ville(id),
    FOREIGN KEY (article_id) REFERENCES article(id)
) ENGINE=InnoDB;

-- ==========================
-- TABLE DON
-- ==========================
CREATE TABLE IF NOT EXISTS don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES article(id)
) ENGINE=InnoDB;


-- ==========================
-- TABLE DISPATCH
-- ==========================
CREATE TABLE IF NOT EXISTS dispatch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    ville_id INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES don(id),
    FOREIGN KEY (ville_id) REFERENCES ville(id)
) ENGINE=InnoDB;

-- ==========================
-- TABLE ACHAT
-- ==========================
CREATE TABLE IF NOT EXISTS achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    ville_id INT NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    frais DECIMAL(5,2) NOT NULL,
    date_achat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoin(id),
    FOREIGN KEY (don_id) REFERENCES don(id),
    FOREIGN KEY (ville_id) REFERENCES ville(id)
) ENGINE=InnoDB;

