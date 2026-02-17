


-- ======================================
-- DONNÉES DE RÉFÉRENCE — BNGRC
-- ======================================

-- Types de besoins
INSERT INTO type_besoin (libelle) VALUES
('Nature'),       -- id = 1
('Matériaux'),    -- id = 2
('Argent');       -- id = 3

-- Régions
INSERT INTO region (nom) VALUES
('Atsinanana'),       -- id = 1 (Toamasina)
('Vatovavy'),         -- id = 2 (Mananjary)
('Atsimo-Atsinanana'),-- id = 3 (Farafangana)
('Diana'),            -- id = 4 (Nosy Be)
('Menabe');           -- id = 5 (Morondava)

-- Villes (5 villes)
INSERT INTO ville (nom, region_id) VALUES
('Toamasina',   1),  -- id = 1
('Mananjary',   2),  -- id = 2
('Farafangana', 3),  -- id = 3
('Nosy Be',     4),  -- id = 4
('Morondava',   5);  -- id = 5

-- Articles (11 articles)
-- Nature (type_besoin_id = 1)
-- Matériaux (type_besoin_id = 2)
-- Argent (type_besoin_id = 3)
INSERT INTO article (nom, type_besoin_id, prix_unitaire) VALUES
('Riz (kg)',    1, 3000),      -- id = 1
('Eau (L)',     1, 1000),      -- id = 2
('Huile (L)',   1, 6000),      -- id = 3
('Haricots',   1, 4000),      -- id = 4
('Tôle',       2, 25000),     -- id = 5
('Bâche',      2, 15000),     -- id = 6
('Clous (kg)', 2, 8000),      -- id = 7
('Bois',       2, 10000),     -- id = 8
('Groupe électrogène', 2, 6750000), -- id = 9
('Argent',     3, 1);         -- id = 10

-- ======================================
-- BESOINS (26 lignes)
-- L'ordre FIFO est déterminé par l'ID auto-incrémenté (ordre d'insertion)
-- Les besoins sont insérés dans l'ordre de priorité voulu (1 à 26)
-- ======================================
INSERT INTO besoin (ville_id, article_id, quantite, date_saisie) VALUES
-- Ordre  1 : Toamasina  — Bâche
(1, 6, 200,       '2026-02-15'),
-- Ordre  2 : Nosy Be    — Tôle
(4, 5, 40,        '2026-02-15'),
-- Ordre  3 : Mananjary  — Argent
(2, 10, 6000000,  '2026-02-15'),
-- Ordre  4 : Toamasina  — Eau (L)
(1, 2, 1500,      '2026-02-15'),
-- Ordre  5 : Nosy Be    — Riz (kg)
(4, 1, 300,       '2026-02-15'),
-- Ordre  6 : Mananjary  — Tôle
(2, 5, 80,        '2026-02-15'),
-- Ordre  7 : Nosy Be    — Argent
(4, 10, 4000000,  '2026-02-15'),
-- Ordre  8 : Farafangana — Bâche
(3, 6, 150,       '2026-02-16'),
-- Ordre  9 : Mananjary  — Riz (kg)
(2, 1, 500,       '2026-02-15'),
-- Ordre 10 : Farafangana — Argent
(3, 10, 8000000,  '2026-02-16'),
-- Ordre 11 : Morondava  — Riz (kg)
(5, 1, 700,       '2026-02-16'),
-- Ordre 12 : Toamasina  — Argent
(1, 10, 12000000, '2026-02-16'),
-- Ordre 13 : Morondava  — Argent
(5, 10, 10000000, '2026-02-16'),
-- Ordre 14 : Farafangana — Eau (L)
(3, 2, 1000,      '2026-02-15'),
-- Ordre 15 : Morondava  — Bâche
(5, 6, 180,       '2026-02-16'),
-- Ordre 16 : Toamasina  — Groupe électrogène
(1, 9, 3,         '2026-02-15'),
-- Ordre 17 : Toamasina  — Riz (kg)
(1, 1, 800,       '2026-02-16'),
-- Ordre 18 : Nosy Be    — Haricots
(4, 4, 200,       '2026-02-16'),
-- Ordre 19 : Mananjary  — Clous (kg)
(2, 7, 60,        '2026-02-16'),
-- Ordre 20 : Morondava  — Eau (L)
(5, 2, 1200,      '2026-02-15'),
-- Ordre 21 : Farafangana — Riz (kg)
(3, 1, 600,       '2026-02-16'),
-- Ordre 22 : Morondava  — Bois
(5, 8, 150,       '2026-02-15'),
-- Ordre 23 : Toamasina  — Tôle
(1, 5, 120,       '2026-02-16'),
-- Ordre 24 : Nosy Be    — Clous (kg)
(4, 7, 30,        '2026-02-16'),
-- Ordre 25 : Mananjary  — Huile (L)
(2, 3, 120,       '2026-02-16'),
-- Ordre 26 : Farafangana — Bois
(3, 8, 100,       '2026-02-15');

-- ======================================
-- DONS (16 dons)
-- ======================================
INSERT INTO don (article_id, quantite, date_don) VALUES
-- Don  1 : Argent — 5 000 000 Ar
(10, 5000000,   '2026-02-16'),
-- Don  2 : Argent — 3 000 000 Ar
(10, 3000000,   '2026-02-16'),
-- Don  3 : Argent — 4 000 000 Ar
(10, 4000000,   '2026-02-17'),
-- Don  4 : Argent — 1 500 000 Ar
(10, 1500000,   '2026-02-17'),
-- Don  5 : Argent — 6 000 000 Ar
(10, 6000000,   '2026-02-17'),
-- Don  6 : Riz (kg) — 400
(1, 400,        '2026-02-16'),
-- Don  7 : Eau (L) — 600
(2, 600,        '2026-02-16'),
-- Don  8 : Tôle — 50
(5, 50,         '2026-02-17'),
-- Don  9 : Bâche — 70
(6, 70,         '2026-02-17'),
-- Don 10 : Haricots — 100
(4, 100,        '2026-02-17'),
-- Don 11 : Riz (kg) — 2 000
(1, 2000,       '2026-02-18'),
-- Don 12 : Tôle — 300
(5, 300,        '2026-02-18'),
-- Don 13 : Eau (L) — 5 000
(2, 5000,       '2026-02-18'),
-- Don 14 : Argent — 20 000 000 Ar
(10, 20000000,  '2026-02-19'),
-- Don 15 : Bâche — 500
(6, 500,        '2026-02-19'),
-- Don 16 : Haricots — 88
(4, 88,         '2026-02-17');

-- ======================================
-- DISPATCH (aucun — à générer via la simulation)
-- ======================================
