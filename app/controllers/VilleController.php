<?php

namespace app\controllers;

use flight\Engine;
use app\models\Ville;

class VilleController
{
    protected Engine $app;
    protected Ville $villeModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->villeModel = new Ville($app->db());
    }

    /**
     * GET /api/villes — Liste de toutes les villes avec leur région
     */
    public function index(): void
    {
        $villes = $this->villeModel->findAllWithRegion();
        $this->app->json($villes, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/villes/@id — Détail d'une ville
     */
    public function show(int $id): void
    {
        $ville = $this->villeModel->findById($id);
        if (!$ville) {
            $this->app->json(['error' => 'Ville non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($ville, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/villes — Créer une ville
     * Body JSON attendu : { "nom": "...", "region_id": 1 }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $nom = $data->nom ?? null;
        $region_id = $data->region_id ?? null;

        if (!$nom || !$region_id) {
            $this->app->json(['error' => 'Les champs "nom" et "region_id" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->villeModel->create([
            'nom' => $nom,
            'region_id' => (int) $region_id
        ]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/villes/@id — Modifier une ville
     * Body JSON attendu : { "nom": "...", "region_id": 1 }
     */
    public function update(int $id): void
    {
        $ville = $this->villeModel->findById($id);
        if (!$ville) {
            $this->app->json(['error' => 'Ville non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $updateData = [];

        if (isset($data->nom)) {
            $updateData['nom'] = $data->nom;
        }
        if (isset($data->region_id)) {
            $updateData['region_id'] = (int) $data->region_id;
        }

        if (empty($updateData)) {
            $this->app->json(['error' => 'Aucune donnée à mettre à jour'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->villeModel->update($id, $updateData);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/villes/@id — Supprimer une ville
     */
    public function destroy(int $id): void
    {
        $ville = $this->villeModel->findById($id);
        if (!$ville) {
            $this->app->json(['error' => 'Ville non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->villeModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
