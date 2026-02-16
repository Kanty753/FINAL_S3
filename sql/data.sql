


-- Types de besoins
INSERT INTO type_besoin (libelle) VALUES
('Nature'),
('Matériaux'),
('Argent');

-- Régions
INSERT INTO region (nom) VALUES
('Région Nord'),
('Région Sud'),
('Région Est'),
('Région Ouest');

-- Villes
INSERT INTO ville (nom, region_id) VALUES
('Ville A', 1),
('Ville B', 1),
('Ville C', 2),
('Ville D', 3),
('Ville E', 4);

-- Articles
INSERT INTO article (nom, type_besoin_id, prix_unitaire) VALUES
('Riz', 1, 2500),
('Huile', 1, 8000),
('Tôle', 2, 35000),
('Clou', 2, 200),
('Argent', 3, 1);

-- Besoins
INSERT INTO besoin (ville_id, article_id, quantite) VALUES
(1, 1, 100),   -- Ville A : 100 kg de riz
(1, 2, 50),    -- Ville A : 50 l d'huile
(2, 1, 80),    -- Ville B : 80 kg de riz
(2, 3, 20),    -- Ville B : 20 tôles
(3, 2, 40),    -- Ville C : 40 l d'huile
(3, 4, 500),   -- Ville C : 500 clous
(4, 5, 1000),  -- Ville D : 1000 unités d'argent
(5, 1, 60);    -- Ville E : 60 kg de riz

-- Dons
INSERT INTO don (article_id, quantite) VALUES
(1, 120),   -- Riz : 120 kg
(2, 70),    -- Huile : 70 l
(3, 30),    -- Tôle : 30 pièces
(4, 1000),  -- Clous : 1000 pièces
(5, 500);   -- Argent : 500 unités

-- Dispatch simulé
INSERT INTO dispatch (don_id, ville_id, quantite_attribuee) VALUES
(1, 1, 100),  -- Riz : 100 kg pour Ville A
(1, 2, 20),   -- Riz : 20 kg pour Ville B
(2, 1, 50),   -- Huile : 50 l pour Ville A
(2, 3, 20),   -- Huile : 20 l pour Ville C
(3, 2, 20),   -- Tôle : 20 pièces pour Ville B
(3, 4, 10),   -- Tôle : 10 pièces pour Ville D
(4, 3, 500),  -- Clous : 500 pièces pour Ville C
(5, 4, 500);  -- Argent : 500 unités pour Ville D

-- ======================================
-- REQUETE TABLEAU DE BORD EXEMPLE
-- ======================================
-- Besoins par ville avec montants
SELECT v.nom AS ville,
       a.nom AS article,
       b.quantite AS besoin,
       (b.quantite * a.prix_unitaire) AS montant_total
FROM besoin b
JOIN ville v ON b.ville_id = v.id
JOIN article a ON b.article_id = a.id;

-- Dons attribués par ville
SELECT v.nom AS ville,
       a.nom AS article,
       SUM(d.quantite_attribuee) AS total_attribue
FROM dispatch d
JOIN don dn ON d.don_id = dn.id
JOIN article a ON dn.article_id = a.id
JOIN ville v ON d.ville_id = v.id
GROUP BY v.nom, a.nom;
