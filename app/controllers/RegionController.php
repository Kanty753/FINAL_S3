<?php

namespace app\controllers;

use flight\Engine;
use app\models\Region;

class RegionController
{
    protected Engine $app;
    protected Region $regionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->regionModel = new Region($app->db());
    }

    /**
     * GET /api/regions — Liste de toutes les régions
     */
    public function index(): void
    {
        $regions = $this->regionModel->findAll();
        $this->app->json($regions, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /regions — Page liste des régions (HTML)
     */
    public function page(): void
    {
        $regions = $this->regionModel->findAll();
        $content = $this->app->view()->fetch('regions/index', ['regions' => $regions]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Régions',
            'active_page' => 'regions',
        ]);
    }

    /**
     * GET /regions/create — Formulaire de création (HTML)
     */
    public function createPage(): void
    {
        $content = $this->app->view()->fetch('regions/create');
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Nouvelle région',
            'active_page' => 'regions',
        ]);
    }

    /**
     * POST /regions — Traiter le formulaire de création (HTML)
     */
    public function store(): void
    {
        $nom = $this->app->request()->data->nom ?? null;
        if ($nom) {
            $this->regionModel->create(['nom' => $nom]);
        }
        $this->app->redirect('/regions');
    }

    /**
     * GET /api/regions/@id — Détail d'une région
     */
    public function show(int $id): void
    {
        $region = $this->regionModel->findById($id);
        if (!$region) {
            $this->app->json(['error' => 'Région non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($region, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/regions — Créer une région
     * Body JSON attendu : { "nom": "..." }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $nom = $data->nom ?? null;

        if (!$nom) {
            $this->app->json(['error' => 'Le champ "nom" est requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->regionModel->create(['nom' => $nom]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/regions/@id — Modifier une région
     * Body JSON attendu : { "nom": "..." }
     */
    public function update(int $id): void
    {
        $region = $this->regionModel->findById($id);
        if (!$region) {
            $this->app->json(['error' => 'Région non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $nom = $data->nom ?? null;

        if (!$nom) {
            $this->app->json(['error' => 'Le champ "nom" est requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->regionModel->update($id, ['nom' => $nom]);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/regions/@id — Supprimer une région
     */
    public function destroy(int $id): void
    {
        $region = $this->regionModel->findById($id);
        if (!$region) {
            $this->app->json(['error' => 'Région non trouvée'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->regionModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
