<?php

namespace app\controllers;

use flight\Engine;
use app\models\Achat;
use app\models\Besoin;
use app\models\Don;
use app\models\Ville;
use app\models\Article;

class AchatController
{
    protected Engine $app;
    protected Achat $achatModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->achatModel = new Achat($app->db());
    }

    /**
     * GET /api/achats — Liste de tous les achats (JSON)
     */
    public function index(): void
    {
        $villeId = $this->app->request()->query->ville_id ?? null;
        if ($villeId) {
            $achats = $this->achatModel->findByVille((int) $villeId);
        } else {
            $achats = $this->achatModel->findAllDetailed();
        }
        $this->app->json($achats, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /achats — Page liste des achats (HTML) filtrable par ville
     */
    public function page(): void
    {
        $villeModel = new Ville($this->app->db());
        $villes = $villeModel->findAll();

        $villeId = $this->app->request()->query->ville_id ?? null;
        if ($villeId) {
            $achats = $this->achatModel->findByVille((int) $villeId);
        } else {
            $achats = $this->achatModel->findAllDetailed();
        }

        // Récupérer le frais configurable
        $fraisPourcent = $this->app->get('frais_achat_pourcent');

        $content = $this->app->view()->fetch('achats/index', [
            'achats' => $achats,
            'villes' => $villes,
            'ville_id_filtre' => $villeId,
            'frais_pourcent' => $fraisPourcent,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Achats',
            'active_page' => 'achats',
        ]);
    }

    /**
     * GET /achats/create — Formulaire de création d'achat (HTML)
     * Utilise la page des besoins restants pour faire les achats
     */
    public function createPage(): void
    {
        $besoinsRestants = $this->achatModel->findBesoinsRestantsAchetables();
        $donsArgent = $this->achatModel->findDonsArgentDisponibles();
        $villeModel = new Ville($this->app->db());
        $villes = $villeModel->findAll();

        // Récupérer le frais configurable
        $fraisPourcent = $this->app->get('frais_achat_pourcent');

        $content = $this->app->view()->fetch('achats/create', [
            'besoins_restants' => $besoinsRestants,
            'dons_argent' => $donsArgent,
            'villes' => $villes,
            'frais_pourcent' => $fraisPourcent,
            'error' => $this->app->get('achat_error') ?? null,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Nouvel achat',
            'active_page' => 'achats',
        ]);
    }

    /**
     * POST /achats — Traiter le formulaire de création d'achat (HTML)
     */
    public function store(): void
    {
        $data = $this->app->request()->data;
        $besoin_id = $data->besoin_id ?? null;
        $don_id = $data->don_id ?? null;
        $quantite = $data->quantite ?? null;

        if (!$besoin_id || !$don_id || !$quantite) {
            $this->app->redirect('/achats/create');
            return;
        }

        $besoin_id = (int) $besoin_id;
        $don_id = (int) $don_id;
        $quantite = (int) $quantite;

        // Vérifier que le besoin existe et récupérer ses infos
        $besoinModel = new Besoin($this->app->db());
        $besoin = $besoinModel->findById($besoin_id);
        if (!$besoin) {
            $this->app->redirect('/achats/create');
            return;
        }

        // Vérifier si le besoin est déjà couvert par les dons dispatchés
        if ($this->achatModel->besoinDejaCouvertParDon($besoin_id)) {
            $this->app->redirect('/achats/create');
            return;
        }

        // Récupérer le prix unitaire de l'article
        $articleModel = new Article($this->app->db());
        $article = $articleModel->findById((int) $besoin['article_id']);
        if (!$article) {
            $this->app->redirect('/achats/create');
            return;
        }

        $prixUnitaire = (float) $article['prix_unitaire'];
        $montantBrut = $quantite * $prixUnitaire;

        // Appliquer les frais d'achat
        $fraisPourcent = (float) $this->app->get('frais_achat_pourcent');

        // Créer l'achat
        $this->achatModel->create([
            'besoin_id' => $besoin_id,
            'don_id' => $don_id,
            'ville_id' => (int) $besoin['ville_id'],
            'montant' => $montantBrut,
            'frais' => $fraisPourcent,
        ]);

        $this->app->redirect('/achats');
    }

    /**
     * POST /api/achats — Créer un achat (JSON)
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $besoin_id = $data->besoin_id ?? null;
        $don_id = $data->don_id ?? null;
        $quantite = $data->quantite ?? null;

        if (!$besoin_id || !$don_id || !$quantite) {
            $this->app->json(['error' => 'Les champs "besoin_id", "don_id" et "quantite" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        // Vérifier si le besoin est couvert par les dons
        if ($this->achatModel->besoinDejaCouvertParDon((int) $besoin_id)) {
            $this->app->json(['error' => 'Ce besoin est déjà couvert par les dons restants'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $besoinModel = new Besoin($this->app->db());
        $besoin = $besoinModel->findById((int) $besoin_id);
        $articleModel = new Article($this->app->db());
        $article = $articleModel->findById((int) $besoin['article_id']);

        $prixUnitaire = (float) $article['prix_unitaire'];
        $montantBrut = (int)$quantite * $prixUnitaire;

        $fraisPourcent = (float) $this->app->get('frais_achat_pourcent');

        $id = $this->achatModel->create([
            'besoin_id' => (int) $besoin_id,
            'don_id' => (int) $don_id,
            'ville_id' => (int) $besoin['ville_id'],
            'montant' => $montantBrut,
            'frais' => $fraisPourcent,
        ]);

        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/achats/@id — Détail d'un achat
     */
    public function show(int $id): void
    {
        $achat = $this->achatModel->findById($id);
        if (!$achat) {
            $this->app->json(['error' => 'Achat non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($achat, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/achats/@id — Supprimer un achat
     */
    public function destroy(int $id): void
    {
        $achat = $this->achatModel->findById($id);
        if (!$achat) {
            $this->app->json(['error' => 'Achat non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->achatModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/recapitulation — Données de récapitulation (JSON, pour Ajax)
     */
    public function recapitulationApi(): void
    {
        $recap = $this->achatModel->getRecapitulation();
        $this->app->json($recap, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /recapitulation — Page de récapitulation (HTML)
     */
    public function recapitulationPage(): void
    {
        $recap = $this->achatModel->getRecapitulation();
        $content = $this->app->view()->fetch('recapitulation', ['recap' => $recap]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Récapitulation',
            'active_page' => 'recapitulation',
        ]);
    }
}
