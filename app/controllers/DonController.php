<?php

namespace app\controllers;

use flight\Engine;
use app\models\Don;

class DonController
{
    protected Engine $app;
    protected Don $donModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->donModel = new Don($app->db());
    }

    /**
     * GET /api/dons — Liste de tous les dons avec détails (article, type, montant, quantité dispatchée)
     */
    public function index(): void
    {
        $dons = $this->donModel->findAllDetailed();
        $this->app->json($dons, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/dons/@id — Détail d'un don
     */
    public function show(int $id): void
    {
        $don = $this->donModel->findById($id);
        if (!$don) {
            $this->app->json(['error' => 'Don non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($don, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/dons/disponibles — Liste des dons avec reste disponible (non totalement dispatchés)
     */
    public function disponibles(): void
    {
        $dons = $this->donModel->findAvailable();
        $this->app->json($dons, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/dons/etat — État des dons (quantité don, dispatchée, reste) pour le tableau de bord
     */
    public function etat(): void
    {
        $etat = $this->donModel->findEtatDons();
        $this->app->json($etat, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/dons — Créer un don
     * Body JSON attendu : { "article_id": 1, "quantite": 120 }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $article_id = $data->article_id ?? null;
        $quantite = $data->quantite ?? null;

        if (!$article_id || !$quantite) {
            $this->app->json(['error' => 'Les champs "article_id" et "quantite" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->donModel->create([
            'article_id' => (int) $article_id,
            'quantite' => (int) $quantite
        ]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/dons/@id — Modifier un don
     * Body JSON attendu : { "article_id": 1, "quantite": 120 }
     */
    public function update(int $id): void
    {
        $don = $this->donModel->findById($id);
        if (!$don) {
            $this->app->json(['error' => 'Don non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $updateData = [];

        if (isset($data->article_id)) {
            $updateData['article_id'] = (int) $data->article_id;
        }
        if (isset($data->quantite)) {
            $updateData['quantite'] = (int) $data->quantite;
        }

        if (empty($updateData)) {
            $this->app->json(['error' => 'Aucune donnée à mettre à jour'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->donModel->update($id, $updateData);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/dons/@id — Supprimer un don et ses dispatches associés
     */
    public function destroy(int $id): void
    {
        $don = $this->donModel->findById($id);
        if (!$don) {
            $this->app->json(['error' => 'Don non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->donModel->deleteWithDispatches($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
