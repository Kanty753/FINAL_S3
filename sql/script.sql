CREATE DATABASE bngrc;
USE bngrc;

-- ==========================
-- TABLE REGION
-- ==========================
CREATE TABLE region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- ==========================
-- TABLE VILLE
-- ==========================
CREATE TABLE ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES region(id)
);

-- ==========================
-- TABLE TYPE_BESOIN
-- ==========================
CREATE TABLE type_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- ==========================
-- TABLE ARTICLE
-- ==========================
CREATE TABLE article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type_besoin_id INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (type_besoin_id) REFERENCES type_besoin(id)
);

-- ==========================
-- TABLE BESOIN
-- ==========================
CREATE TABLE besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ville(id),
    FOREIGN KEY (article_id) REFERENCES article(id)
);

-- ==========================
-- TABLE DON
-- ==========================
CREATE TABLE don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES article(id)
);

-- ==========================
-- TABLE DISPATCH
-- ==========================
CREATE TABLE dispatch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    ville_id INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES don(id),
    FOREIGN KEY (ville_id) REFERENCES ville(id)
);
 