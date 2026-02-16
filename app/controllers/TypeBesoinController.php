<?php

namespace app\controllers;

use flight\Engine;
use app\models\TypeBesoin;

class TypeBesoinController
{
    protected Engine $app;
    protected TypeBesoin $typeBesoinModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->typeBesoinModel = new TypeBesoin($app->db());
    }

    /**
     * GET /api/types-besoins — Liste de tous les types de besoins
     */
    public function index(): void
    {
        $types = $this->typeBesoinModel->findAll();
        $this->app->json($types, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/types-besoins/@id — Détail d'un type de besoin
     */
    public function show(int $id): void
    {
        $type = $this->typeBesoinModel->findById($id);
        if (!$type) {
            $this->app->json(['error' => 'Type de besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($type, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/types-besoins — Créer un type de besoin
     * Body JSON attendu : { "libelle": "..." }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $libelle = $data->libelle ?? null;

        if (!$libelle) {
            $this->app->json(['error' => 'Le champ "libelle" est requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->typeBesoinModel->create(['libelle' => $libelle]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/types-besoins/@id — Modifier un type de besoin
     * Body JSON attendu : { "libelle": "..." }
     */
    public function update(int $id): void
    {
        $type = $this->typeBesoinModel->findById($id);
        if (!$type) {
            $this->app->json(['error' => 'Type de besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $libelle = $data->libelle ?? null;

        if (!$libelle) {
            $this->app->json(['error' => 'Le champ "libelle" est requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->typeBesoinModel->update($id, ['libelle' => $libelle]);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/types-besoins/@id — Supprimer un type de besoin
     */
    public function destroy(int $id): void
    {
        $type = $this->typeBesoinModel->findById($id);
        if (!$type) {
            $this->app->json(['error' => 'Type de besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->typeBesoinModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
