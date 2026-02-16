# BNGRC — Suivi des Collectes et Distributions de Dons

## 📋 Description du Projet

Application de suivi des collectes et des distributions de dons pour les sinistrés, développée pour le **BNGRC** (Bureau National de Gestion des Risques et des Catastrophes).

### Contexte

Les sinistrés sont répartis par **ville** dans une **région**. Ils ont des besoins classés en trois catégories :
- **Nature** : riz, huile, etc.
- **Matériaux** : tôle, clou, etc.
- **Argent** : aide financière

### Fonctionnement

1. **Saisie des besoins** : Les besoins des sinistrés sont saisis par ville (pas d'identification personnelle des sinistrés)
2. **Saisie des dons** : Les dons reçus sont enregistrés par article et quantité
3. **Simulation du dispatch** : Les dons sont distribués automatiquement aux villes par ordre de date de saisie des besoins
4. **Tableau de bord** : Vue d'ensemble des villes avec leurs besoins et les dons attribués

### Règles de Gestion

- Chaque besoin possède un **prix unitaire** et une **quantité**
- Le prix unitaire ne change jamais
- Le dispatch se fait par **ordre chronologique** de date de saisie des besoins
- Un don est réparti entre les villes jusqu'à épuisement de la quantité disponible

---

## 🏗️ Architecture du Projet

```
FINAL_S3/
├── app/
│   ├── config/
│   │   ├── bootstrap.php         # Initialisation de l'application
│   │   ├── config.php            # Configuration (BDD, etc.)
│   │   ├── routes.php            # Définition de toutes les routes API
│   │   └── services.php          # Enregistrement des services (BDD, Tracy)
│   ├── controllers/
│   │   ├── RegionController.php      # CRUD des régions
│   │   ├── VilleController.php       # CRUD des villes
│   │   ├── ArticleController.php     # CRUD des articles
│   │   ├── TypeBesoinController.php  # CRUD des types de besoins
│   │   ├── BesoinController.php      # CRUD des besoins + vue par ville
│   │   ├── DonController.php         # CRUD des dons + état + disponibilité
│   │   ├── DispatchController.php    # CRUD des dispatches + simulation
│   │   └── DashboardController.php   # Tableau de bord global
│   ├── models/
│   │   ├── BaseModel.php         # Modèle abstrait (CRUD générique)
│   │   ├── Region.php
│   │   ├── Ville.php
│   │   ├── Article.php
│   │   ├── TypeBesoin.php
│   │   ├── Besoin.php
│   │   ├── Don.php
│   │   └── Dispatch.php
│   ├── middlewares/
│   │   └── SecurityHeadersMiddleware.php
│   └── views/
│       └── welcome.php
├── public/
│   └── index.php                 # Point d'entrée
├── sql/
│   ├── script.sql                # Création des tables
│   └── data.sql                  # Données de test
└── vendor/                       # Dépendances (FlightPHP, Tracy, etc.)
```

---

## 🗄️ Structure de la Base de Données

| Table | Description |
|---|---|
| `region` | Régions géographiques |
| `ville` | Villes rattachées à une région |
| `type_besoin` | Types de besoins (Nature, Matériaux, Argent) |
| `article` | Articles avec prix unitaire et type de besoin |
| `besoin` | Besoins par ville (article + quantité + date) |
| `don` | Dons reçus (article + quantité + date) |
| `dispatch` | Attribution des dons aux villes |

### Schéma relationnel

```
region (id, nom)
  └── ville (id, nom, region_id → region.id)
        └── besoin (id, ville_id → ville.id, article_id → article.id, quantite, date_saisie)
        └── dispatch (id, don_id → don.id, ville_id → ville.id, quantite_attribuee, date_dispatch)

type_besoin (id, libelle)
  └── article (id, nom, type_besoin_id → type_besoin.id, prix_unitaire)
        └── besoin
        └── don (id, article_id → article.id, quantite, date_don)
              └── dispatch
```

---

## 🚀 Installation

### Prérequis
- PHP 8.1+
- MySQL / MariaDB
- Composer

### Étapes

```bash
# 1. Installer les dépendances
composer install

# 2. Créer la base de données et les tables
mysql -u root -p < sql/script.sql

# 3. Insérer les données de test
mysql -u root -p bngrc < sql/data.sql

# 4. Configurer la base de données
# Modifier app/config/config.php avec vos identifiants MySQL

# 5. Lancer le serveur de développement
php -S localhost:8000 -t public
```

L'API est ensuite accessible sur `http://localhost:8000`

---

## 📡 Documentation des Routes API

### 🏠 Tableau de Bord

| Méthode | Route | Description | Retour |
|---|---|---|---|
| `GET` | `/api/dashboard` | **Tableau de bord complet** | Statistiques, état des dons, besoins et dispatches par ville |

**Exemple de retour** `/api/dashboard` :
```json
{
    "statistiques": {
        "total_regions": 4,
        "total_villes": 5,
        "total_besoins": 8,
        "total_dons": 5,
        "total_dispatches": 8
    },
    "etat_dons": [
        { "article": "Riz", "quantite_don": 120, "quantite_dispatche": 120, "reste": 0 }
    ],
    "tableau_de_bord": [
        {
            "ville": "Ville A",
            "region": "Région Nord",
            "total_besoin_montant": 650000,
            "besoins": [ ... ],
            "dons_attribues": [ ... ]
        }
    ]
}
```

---

### 🌍 Régions

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/regions` | Liste toutes les régions | — | `[{ "id": 1, "nom": "Région Nord" }, ...]` |
| `GET` | `/api/regions/{id}` | Détail d'une région | — | `{ "id": 1, "nom": "Région Nord" }` |
| `POST` | `/api/regions` | Créer une région | `{ "nom": "..." }` | `{ "success": true, "id": 5 }` |
| `PUT` | `/api/regions/{id}` | Modifier une région | `{ "nom": "..." }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/regions/{id}` | Supprimer une région | — | `{ "success": true }` |

---

### 🏙️ Villes

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/villes` | Liste toutes les villes avec le nom de leur région | — | `[{ "id": 1, "nom": "Ville A", "region_id": 1, "region_nom": "Région Nord" }, ...]` |
| `GET` | `/api/villes/{id}` | Détail d'une ville | — | `{ "id": 1, "nom": "Ville A", "region_id": 1 }` |
| `POST` | `/api/villes` | Créer une ville | `{ "nom": "...", "region_id": 1 }` | `{ "success": true, "id": 6 }` |
| `PUT` | `/api/villes/{id}` | Modifier une ville | `{ "nom": "...", "region_id": 1 }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/villes/{id}` | Supprimer une ville | — | `{ "success": true }` |

---

### 📦 Types de Besoins

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/types-besoins` | Liste tous les types | — | `[{ "id": 1, "libelle": "Nature" }, ...]` |
| `GET` | `/api/types-besoins/{id}` | Détail d'un type | — | `{ "id": 1, "libelle": "Nature" }` |
| `POST` | `/api/types-besoins` | Créer un type | `{ "libelle": "..." }` | `{ "success": true, "id": 4 }` |
| `PUT` | `/api/types-besoins/{id}` | Modifier un type | `{ "libelle": "..." }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/types-besoins/{id}` | Supprimer un type | — | `{ "success": true }` |

---

### 🛒 Articles

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/articles` | Liste tous les articles avec leur type de besoin | — | `[{ "id": 1, "nom": "Riz", "type_besoin_id": 1, "prix_unitaire": 2500, "type_besoin": "Nature" }, ...]` |
| `GET` | `/api/articles/{id}` | Détail d'un article | — | `{ "id": 1, "nom": "Riz", "type_besoin_id": 1, "prix_unitaire": 2500 }` |
| `POST` | `/api/articles` | Créer un article | `{ "nom": "...", "type_besoin_id": 1, "prix_unitaire": 2500 }` | `{ "success": true, "id": 6 }` |
| `PUT` | `/api/articles/{id}` | Modifier un article | `{ "nom": "...", "type_besoin_id": 1, "prix_unitaire": 2500 }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/articles/{id}` | Supprimer un article | — | `{ "success": true }` |

---

### 📝 Besoins

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/besoins` | Liste tous les besoins avec détails (ville, article, type, montant total) | — | `[{ "id": 1, "ville_nom": "Ville A", "article_nom": "Riz", "quantite": 100, "prix_unitaire": 2500, "type_besoin": "Nature", "montant_total": 250000 }, ...]` |
| `GET` | `/api/besoins/par-ville` | Besoins groupés par ville (pour le tableau de bord) | — | `[{ "ville_id": 1, "ville": "Ville A", "region": "Région Nord", "article": "Riz", "besoin_quantite": 100, "prix_unitaire": 2500, "montant_besoin": 250000 }, ...]` |
| `GET` | `/api/besoins/{id}` | Détail d'un besoin | — | `{ "id": 1, "ville_id": 1, "article_id": 1, "quantite": 100, "date_saisie": "..." }` |
| `POST` | `/api/besoins` | Créer un besoin pour une ville | `{ "ville_id": 1, "article_id": 1, "quantite": 100 }` | `{ "success": true, "id": 9 }` |
| `PUT` | `/api/besoins/{id}` | Modifier un besoin | `{ "ville_id": 1, "article_id": 1, "quantite": 100 }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/besoins/{id}` | Supprimer un besoin | — | `{ "success": true }` |

---

### 🎁 Dons

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/dons` | Liste tous les dons avec détails (article, type, montant, quantité dispatchée) | — | `[{ "id": 1, "article_nom": "Riz", "quantite": 120, "prix_unitaire": 2500, "type_besoin": "Nature", "montant_total": 300000, "quantite_dispatche": 120 }, ...]` |
| `GET` | `/api/dons/disponibles` | Dons avec reste disponible (non totalement distribués) | — | `[{ "id": 5, "article_nom": "Argent", "quantite": 500, "reste": 0 }, ...]` |
| `GET` | `/api/dons/etat` | État global des dons (quantité, dispatché, reste) | — | `[{ "article": "Riz", "quantite_don": 120, "quantite_dispatche": 120, "reste": 0 }, ...]` |
| `GET` | `/api/dons/{id}` | Détail d'un don | — | `{ "id": 1, "article_id": 1, "quantite": 120, "date_don": "..." }` |
| `POST` | `/api/dons` | Créer un don | `{ "article_id": 1, "quantite": 120 }` | `{ "success": true, "id": 6 }` |
| `PUT` | `/api/dons/{id}` | Modifier un don | `{ "article_id": 1, "quantite": 120 }` | `{ "success": true, "id": 1 }` |
| `DELETE` | `/api/dons/{id}` | Supprimer un don et ses dispatches associés | — | `{ "success": true }` |

---

### 📤 Dispatches (Distribution)

| Méthode | Route | Description | Body (JSON) | Retour |
|---|---|---|---|---|
| `GET` | `/api/dispatches` | Liste tous les dispatches avec détails (ville, article, montant) | — | `[{ "id": 1, "ville_nom": "Ville A", "article_nom": "Riz", "quantite_attribuee": 100, "prix_unitaire": 2500, "montant": 250000 }, ...]` |
| `GET` | `/api/dispatches/par-ville` | Dispatches groupés par ville (pour le tableau de bord) | — | `[{ "ville_id": 1, "ville": "Ville A", "article": "Riz", "total_attribue": 100, "montant_attribue": 250000 }, ...]` |
| `GET` | `/api/dispatches/{id}` | Détail d'un dispatch | — | `{ "id": 1, "don_id": 1, "ville_id": 1, "quantite_attribuee": 100 }` |
| `POST` | `/api/dispatches` | Créer un dispatch manuellement | `{ "don_id": 1, "ville_id": 1, "quantite_attribuee": 50 }` | `{ "success": true, "id": 9 }` |
| `POST` | `/api/dispatches/simuler` | **Lancer la simulation automatique** du dispatch | — | `{ "success": true, "message": "...", "dispatches": [...] }` |
| `DELETE` | `/api/dispatches/{id}` | Supprimer un dispatch | — | `{ "success": true }` |
| `DELETE` | `/api/dispatches` | Supprimer tous les dispatches (reset) | — | `{ "success": true, "message": "..." }` |

---

## ⚙️ Simulation du Dispatch

La route `POST /api/dispatches/simuler` exécute l'algorithme de distribution automatique :

1. **Supprime** tous les dispatches existants
2. **Parcourt** les dons par ordre chronologique (`date_don ASC`)
3. Pour chaque don, **identifie** les besoins correspondants au même article, par ordre de date de saisie
4. **Attribue** la quantité disponible du don aux villes dans l'ordre, jusqu'à épuisement
5. Retourne la liste complète des nouveaux dispatches

### Algorithme

```
Pour chaque DON (trié par date_don) :
    reste_don = don.quantite
    
    Pour chaque BESOIN du même article (trié par date_saisie) :
        besoin_restant = besoin.quantite - déjà_attribué
        
        si besoin_restant > 0 :
            a_attribuer = min(reste_don, besoin_restant)
            créer DISPATCH(don_id, ville_id, a_attribuer)
            reste_don -= a_attribuer
        
        si reste_don <= 0 : passer au don suivant
```

---

## 🧪 Exemples d'utilisation (curl)

```bash
# Récupérer le tableau de bord
curl http://localhost:8000/api/dashboard

# Lister les régions
curl http://localhost:8000/api/regions

# Créer une région
curl -X POST http://localhost:8000/api/regions \
  -H "Content-Type: application/json" \
  -d '{"nom": "Région Centre"}'

# Créer un besoin pour une ville
curl -X POST http://localhost:8000/api/besoins \
  -H "Content-Type: application/json" \
  -d '{"ville_id": 1, "article_id": 2, "quantite": 30}'

# Créer un don
curl -X POST http://localhost:8000/api/dons \
  -H "Content-Type: application/json" \
  -d '{"article_id": 1, "quantite": 200}'

# Lancer la simulation du dispatch
curl -X POST http://localhost:8000/api/dispatches/simuler

# Voir les besoins par ville
curl http://localhost:8000/api/besoins/par-ville

# Voir les dispatches par ville
curl http://localhost:8000/api/dispatches/par-ville

# Voir l'état des dons (quantité, dispatché, reste)
curl http://localhost:8000/api/dons/etat

# Modifier une ville
curl -X PUT http://localhost:8000/api/villes/1 \
  -H "Content-Type: application/json" \
  -d '{"nom": "Ville Alpha"}'

# Supprimer un don (et ses dispatches)
curl -X DELETE http://localhost:8000/api/dons/3
```

---

## 🛠️ Technologies

- **Framework** : [FlightPHP](https://flightphp.com/) (micro-framework PHP)
- **Base de données** : MySQL / MariaDB
- **Débogage** : [Tracy Debugger](https://tracy.nette.org/)
- **PHP** : 8.1+
