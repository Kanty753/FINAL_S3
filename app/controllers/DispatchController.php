<?php

namespace app\controllers;

use flight\Engine;
use app\models\Dispatch;
use app\models\Don;
use app\models\Besoin;
use app\models\Ville;

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
     * GET /dispatches — Page liste des dispatches (HTML)
     */
    public function page(): void
    {
        $dispatches = $this->dispatchModel->findAllDetailed();
        $content = $this->app->view()->fetch('dispatches/index', [
            'dispatches' => $dispatches,
            'simulation' => null,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Dispatches',
            'active_page' => 'dispatches',
        ]);
    }

    /**
     * GET /dispatches/create — Formulaire de création manuelle (HTML)
     */
    public function createPage(): void
    {
        $donModel = new Don($this->app->db());
        $villeModel = new Ville($this->app->db());
        $dons = $donModel->findAvailable();
        $villes = $villeModel->findAll();
        $content = $this->app->view()->fetch('dispatches/create', [
            'dons' => $dons,
            'villes' => $villes,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Nouveau dispatch',
            'active_page' => 'dispatches',
        ]);
    }

    /**
     * POST /dispatches — Traiter le formulaire de création (HTML)
     */
    public function store(): void
    {
        $data = $this->app->request()->data;
        $don_id = $data->don_id ?? null;
        $ville_id = $data->ville_id ?? null;
        $quantite = $data->quantite_attribuee ?? null;
        if ($don_id && $ville_id && $quantite) {
            $this->dispatchModel->create([
                'don_id' => (int) $don_id,
                'ville_id' => (int) $ville_id,
                'quantite_attribuee' => (int) $quantite,
            ]);
        }
        $this->app->redirect('/dispatches');
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

    /**
     * POST /dispatches/simuler — Simulation preview depuis l'interface HTML (sans sauvegarder)
     */
    public function simulerPage(): void
    {
        $donModel = new Don($this->app->db());
        $besoinModel = new Besoin($this->app->db());
        
        // Simuler SANS sauvegarder
        $simulation = $this->dispatchModel->simulerPreview($donModel, $besoinModel);
        
        // Afficher la page avec les dispatches existants + la simulation en preview
        $dispatches = $this->dispatchModel->findAllDetailed();
        $content = $this->app->view()->fetch('dispatches/index', [
            'dispatches' => $dispatches,
            'simulation' => $simulation,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Dispatches — Simulation',
            'active_page' => 'dispatches',
        ]);
    }

    /**
     * POST /dispatches/valider — Valider et sauvegarder le dispatch (supprime les anciens et recalcule)
     */
    public function validerPage(): void
    {
        $donModel = new Don($this->app->db());
        $besoinModel = new Besoin($this->app->db());
        $this->dispatchModel->simuler($donModel, $besoinModel);
        $this->app->redirect('/dispatches');
    }

    /**
     * POST /dispatches/reset — Supprimer tous les dispatches depuis l'interface HTML et rediriger
     */
    public function resetPage(): void
    {
        $this->dispatchModel->deleteAll();
        $this->app->redirect('/dispatches');
    }
}
