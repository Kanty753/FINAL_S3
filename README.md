# BNGRC — Suivi des Collectes et Distributions de Dons

## � Routes

### Pages HTML

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/` | — | Redirection vers `/dashboard` |
| `GET` | `/dashboard` | `DashboardController` | `page()` |
| `GET` | `/regions` | `RegionController` | `page()` |
| `GET` | `/regions/create` | `RegionController` | `createPage()` |
| `POST` | `/regions` | `RegionController` | `store()` |
| `GET` | `/villes` | `VilleController` | `page()` |
| `GET` | `/villes/create` | `VilleController` | `createPage()` |
| `POST` | `/villes` | `VilleController` | `store()` |
| `GET` | `/articles` | `ArticleController` | `page()` |
| `GET` | `/articles/create` | `ArticleController` | `createPage()` |
| `POST` | `/articles` | `ArticleController` | `store()` |
| `GET` | `/besoins` | `BesoinController` | `page()` |
| `GET` | `/besoins/create` | `BesoinController` | `createPage()` |
| `POST` | `/besoins` | `BesoinController` | `store()` |
| `GET` | `/dons` | `DonController` | `page()` |
| `GET` | `/dons/create` | `DonController` | `createPage()` |
| `POST` | `/dons` | `DonController` | `store()` |
| `GET` | `/dispatches` | `DispatchController` | `page()` |
| `GET` | `/dispatches/create` | `DispatchController` | `createPage()` |
| `POST` | `/dispatches` | `DispatchController` | `store()` |

### API — Tableau de bord

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/dashboard` | `DashboardController` | `index()` |

### API — Régions

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/regions` | `RegionController` | `index()` |
| `GET` | `/api/regions/{id}` | `RegionController` | `show()` |
| `POST` | `/api/regions` | `RegionController` | `create()` |
| `PUT` | `/api/regions/{id}` | `RegionController` | `update()` |
| `DELETE` | `/api/regions/{id}` | `RegionController` | `destroy()` |

### API — Villes

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/villes` | `VilleController` | `index()` |
| `GET` | `/api/villes/{id}` | `VilleController` | `show()` |
| `POST` | `/api/villes` | `VilleController` | `create()` |
| `PUT` | `/api/villes/{id}` | `VilleController` | `update()` |
| `DELETE` | `/api/villes/{id}` | `VilleController` | `destroy()` |

### API — Types de besoins

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/types-besoins` | `TypeBesoinController` | `index()` |
| `GET` | `/api/types-besoins/{id}` | `TypeBesoinController` | `show()` |
| `POST` | `/api/types-besoins` | `TypeBesoinController` | `create()` |
| `PUT` | `/api/types-besoins/{id}` | `TypeBesoinController` | `update()` |
| `DELETE` | `/api/types-besoins/{id}` | `TypeBesoinController` | `destroy()` |

### API — Articles

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/articles` | `ArticleController` | `index()` |
| `GET` | `/api/articles/{id}` | `ArticleController` | `show()` |
| `POST` | `/api/articles` | `ArticleController` | `create()` |
| `PUT` | `/api/articles/{id}` | `ArticleController` | `update()` |
| `DELETE` | `/api/articles/{id}` | `ArticleController` | `destroy()` |

### API — Besoins

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/besoins` | `BesoinController` | `index()` |
| `GET` | `/api/besoins/par-ville` | `BesoinController` | `parVille()` |
| `GET` | `/api/besoins/{id}` | `BesoinController` | `show()` |
| `POST` | `/api/besoins` | `BesoinController` | `create()` |
| `PUT` | `/api/besoins/{id}` | `BesoinController` | `update()` |
| `DELETE` | `/api/besoins/{id}` | `BesoinController` | `destroy()` |

### API — Dons

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/dons` | `DonController` | `index()` |
| `GET` | `/api/dons/disponibles` | `DonController` | `disponibles()` |
| `GET` | `/api/dons/etat` | `DonController` | `etat()` |
| `GET` | `/api/dons/{id}` | `DonController` | `show()` |
| `POST` | `/api/dons` | `DonController` | `create()` |
| `PUT` | `/api/dons/{id}` | `DonController` | `update()` |
| `DELETE` | `/api/dons/{id}` | `DonController` | `destroy()` |

### API — Dispatches

| Méthode | Route | Contrôleur | Méthode |
|---|---|---|---|
| `GET` | `/api/dispatches` | `DispatchController` | `index()` |
| `GET` | `/api/dispatches/par-ville` | `DispatchController` | `parVille()` |
| `GET` | `/api/dispatches/{id}` | `DispatchController` | `show()` |
| `POST` | `/api/dispatches` | `DispatchController` | `create()` |
| `POST` | `/api/dispatches/simuler` | `DispatchController` | `simuler()` |
| `DELETE` | `/api/dispatches/{id}` | `DispatchController` | `destroy()` |
| `DELETE` | `/api/dispatches` | `DispatchController` | `destroyAll()` |

---

## 🎮 Contrôleurs

### `DashboardController`

| Méthode | Description |
|---|---|
| `index()` | Retourne les statistiques, état des dons et tableau de bord en JSON |
| `page()` | Affiche la page HTML du tableau de bord |

### `RegionController`

| Méthode | Description |
|---|---|
| `index()` | Liste toutes les régions (JSON) |
| `show($id)` | Détail d'une région (JSON) |
| `create()` | Créer une région (JSON) |
| `update($id)` | Modifier une région (JSON) |
| `destroy($id)` | Supprimer une région (JSON) |
| `page()` | Affiche la liste des régions (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |

### `VilleController`

| Méthode | Description |
|---|---|
| `index()` | Liste toutes les villes avec leur région (JSON) |
| `show($id)` | Détail d'une ville (JSON) |
| `create()` | Créer une ville (JSON) |
| `update($id)` | Modifier une ville (JSON) |
| `destroy($id)` | Supprimer une ville (JSON) |
| `page()` | Affiche la liste des villes (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |

### `ArticleController`

| Méthode | Description |
|---|---|
| `index()` | Liste tous les articles avec leur type (JSON) |
| `show($id)` | Détail d'un article (JSON) |
| `create()` | Créer un article (JSON) |
| `update($id)` | Modifier un article (JSON) |
| `destroy($id)` | Supprimer un article (JSON) |
| `page()` | Affiche la liste des articles (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |

### `TypeBesoinController`

| Méthode | Description |
|---|---|
| `index()` | Liste tous les types de besoins (JSON) |
| `show($id)` | Détail d'un type (JSON) |
| `create()` | Créer un type (JSON) |
| `update($id)` | Modifier un type (JSON) |
| `destroy($id)` | Supprimer un type (JSON) |

### `BesoinController`

| Méthode | Description |
|---|---|
| `index()` | Liste tous les besoins avec détails (JSON) |
| `parVille()` | Besoins groupés par ville (JSON) |
| `show($id)` | Détail d'un besoin (JSON) |
| `create()` | Créer un besoin (JSON) |
| `update($id)` | Modifier un besoin (JSON) |
| `destroy($id)` | Supprimer un besoin (JSON) |
| `page()` | Affiche la liste des besoins (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |

### `DonController`

| Méthode | Description |
|---|---|
| `index()` | Liste tous les dons avec détails (JSON) |
| `disponibles()` | Dons avec reste disponible (JSON) |
| `etat()` | État global des dons (JSON) |
| `show($id)` | Détail d'un don (JSON) |
| `create()` | Créer un don (JSON) |
| `update($id)` | Modifier un don (JSON) |
| `destroy($id)` | Supprimer un don et ses dispatches (JSON) |
| `page()` | Affiche la liste des dons (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |

### `DispatchController`

| Méthode | Description |
|---|---|
| `index()` | Liste tous les dispatches avec détails (JSON) |
| `parVille()` | Dispatches groupés par ville (JSON) |
| `show($id)` | Détail d'un dispatch (JSON) |
| `create()` | Créer un dispatch manuellement (JSON) |
| `simuler()` | Lancer la simulation automatique du dispatch (JSON) |
| `destroy($id)` | Supprimer un dispatch (JSON) |
| `destroyAll()` | Supprimer tous les dispatches (JSON) |
| `page()` | Affiche la liste des dispatches (HTML) |
| `createPage()` | Affiche le formulaire de création (HTML) |
| `store()` | Traite le formulaire de création (POST HTML) |
