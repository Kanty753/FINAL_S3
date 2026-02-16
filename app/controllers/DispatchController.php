<?php

namespace app\controllers;

use flight\Engine;
use app\models\Dispatch;
use app\models\Don;
use app\models\Besoin;

class DispatchController
{
    protected Engine $app;
    protected Dispatch $dispatchModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->dispatchModel = new Dispatch($app->db());
    }

    /**
     * GET /api/dispatches — Liste de tous les dispatches avec détails (ville, article, montant)
     */
    public function index(): void
    {
        $dispatches = $this->dispatchModel->findAllDetailed();
        $this->app->json($dispatches, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/dispatches/@id — Détail d'un dispatch
     */
    public function show(int $id): void
    {
        $dispatch = $this->dispatchModel->findById($id);
        if (!$dispatch) {
            $this->app->json(['error' => 'Dispatch non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($dispatch, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/dispatches/par-ville — Dispatches groupés par ville (pour tableau de bord)
     */
    public function parVille(): void
    {
        $dispatches = $this->dispatchModel->findDispatchesParVille();
        $this->app->json($dispatches, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/dispatches — Créer un dispatch manuellement
     * Body JSON attendu : { "don_id": 1, "ville_id": 1, "quantite_attribuee": 50 }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $don_id = $data->don_id ?? null;
        $ville_id = $data->ville_id ?? null;
        $quantite = $data->quantite_attribuee ?? null;

        if (!$don_id || !$ville_id || !$quantite) {
            $this->app->json(['error' => 'Les champs "don_id", "ville_id" et "quantite_attribuee" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->dispatchModel->create([
            'don_id' => (int) $don_id,
            'ville_id' => (int) $ville_id,
            'quantite_attribuee' => (int) $quantite
        ]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/dispatches/simuler — Lancer la simulation automatique du dispatch
     * Vide tous les dispatches existants et redistribue les dons par ordre de date
     */
    public function simuler(): void
    {
        $donModel = new Don($this->app->db());
        $besoinModel = new Besoin($this->app->db());

        $this->dispatchModel->simuler($donModel, $besoinModel);

        $dispatches = $this->dispatchModel->findAllDetailed();
        $this->app->json([
            'success' => true,
            'message' => 'Simulation du dispatch effectuée avec succès',
            'dispatches' => $dispatches
        ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/dispatches/@id — Supprimer un dispatch
     */
    public function destroy(int $id): void
    {
        $dispatch = $this->dispatchModel->findById($id);
        if (!$dispatch) {
            $this->app->json(['error' => 'Dispatch non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->dispatchModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/dispatches — Supprimer tous les dispatches (reset)
     */
    public function destroyAll(): void
    {
        $this->dispatchModel->deleteAll();
        $this->app->json(['success' => true, 'message' => 'Tous les dispatches ont été supprimés'], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
