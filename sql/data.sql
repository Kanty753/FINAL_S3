


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
-- Limité à 3 villes
INSERT INTO ville (nom, region_id) VALUES
('Ville A', 1),
('Ville B', 1),
('Ville C', 2);

-- Articles
-- Limité à 2 articles
INSERT INTO article (nom, type_besoin_id, prix_unitaire) VALUES
('Riz', 1, 2500),
('Huile', 1, 8000);

-- Besoins
-- 1 besoin par ville pour chaque article (3 villes x 2 articles = 6 besoins)
-- Ajout de date_saisie explicite pour tester les stratégies (FIFO, etc.)
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
(1, 1, 10, '2026-02-01 08:00:00'),   -- Ville A : 10 kg de riz
(1, 2, 5,  '2026-02-02 09:00:00'),   -- Ville A : 5 l d'huile
(2, 1, 8,  '2026-02-03 10:00:00'),   -- Ville B : 8 kg de riz
(2, 2, 6,  '2026-02-04 11:00:00'),   -- Ville B : 6 l d'huile
(3, 1, 12, '2026-02-05 12:00:00'),   -- Ville C : 12 kg de riz
(3, 2, 4,  '2026-02-06 13:00:00');   -- Ville C : 4 l d'huile

-- Dons
-- Dons uniquement pour les 2 articles
INSERT INTO don (article_id, quantite) VALUES
(1, 20),   -- Riz : 20 kg
(2, 10);   -- Huile : 10 l

-- Dispatch simulé
-- Exemple de dispatch minimal cohérent avec les dons ci-dessus
-- (Aucune insertion de dispatch dans ce dataset)

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
