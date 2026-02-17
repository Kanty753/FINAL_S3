<?php

namespace app\controllers;

use flight\Engine;
use app\models\Dispatch;
use app\models\Don;
use app\models\Besoin;
<<<<<<< HEAD
use app\models\Ville;
=======
>>>>>>> d3692f7 (commit v1)

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
<<<<<<< HEAD
     * GET /dispatches — Page liste des dispatches (HTML)
=======
     * GET /dispatches — Page liste des dispatches (HTML) avec possibilité de simuler
>>>>>>> d3692f7 (commit v1)
     */
    public function page(): void
    {
        $dispatches = $this->dispatchModel->findAllDetailed();
<<<<<<< HEAD
        $content = $this->app->view()->fetch('dispatches/index', ['dispatches' => $dispatches]);
=======
        $content = $this->app->view()->fetch('dispatches/index', [
            'dispatches' => $dispatches,
            'simulation' => null,
        ]);
>>>>>>> d3692f7 (commit v1)
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Dispatches',
            'active_page' => 'dispatches',
        ]);
    }

    /**
<<<<<<< HEAD
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
=======
>>>>>>> d3692f7 (commit v1)
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
<<<<<<< HEAD
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
     * POST /dispatches/simuler — Lancer la simulation depuis l'interface HTML et rediriger
=======
     * POST /dispatches/simuler — Simulation preview depuis l'interface HTML (sans sauvegarder)
     * Priorité : premier besoin saisi (date_saisie ASC) = premier servi
>>>>>>> d3692f7 (commit v1)
     */
    public function simulerPage(): void
    {
        $donModel = new Don($this->app->db());
        $besoinModel = new Besoin($this->app->db());
<<<<<<< HEAD
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
=======
        
        // Simuler SANS sauvegarder — ne touche pas à la BDD
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
     * POST /api/dispatches/simuler — Simulation preview via API (JSON, sans sauvegarder)
     */
    public function simulerApi(): void
    {
        $donModel = new Don($this->app->db());
        $besoinModel = new Besoin($this->app->db());

        $simulation = $this->dispatchModel->simulerPreview($donModel, $besoinModel);

        $this->app->json([
            'success' => true,
            'message' => 'Simulation du dispatch (preview uniquement, rien sauvegardé)',
            'dispatches' => $simulation
        ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
>>>>>>> d3692f7 (commit v1)
    }
}
