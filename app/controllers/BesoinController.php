<?php

namespace app\controllers;

use flight\Engine;
use app\models\Besoin;
use app\models\Ville;
use app\models\Article;

class BesoinController
{
    protected Engine $app;
    protected Besoin $besoinModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->besoinModel = new Besoin($app->db());
    }

    /**
     * GET /api/besoins — Liste de tous les besoins avec détails (ville, article, type, montant)
     */
    public function index(): void
    {
        $besoins = $this->besoinModel->findAllDetailed();
        $this->app->json($besoins, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /besoins — Page liste des besoins (HTML)
     */
    public function page(): void
    {
        $besoins = $this->besoinModel->findAllDetailed();
        $content = $this->app->view()->fetch('besoins/index', ['besoins' => $besoins]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Besoins',
            'active_page' => 'besoins',
        ]);
    }

    /**
     * GET /besoins/create — Formulaire de création (HTML)
     */
    public function createPage(): void
    {
        $villeModel = new Ville($this->app->db());
        $articleModel = new Article($this->app->db());
        $villes = $villeModel->findAll();
        $articles = $articleModel->findAllWithType();
        $content = $this->app->view()->fetch('besoins/create', [
            'villes' => $villes,
            'articles' => $articles,
        ]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Nouveau besoin',
            'active_page' => 'besoins',
        ]);
    }

    /**
     * POST /besoins — Traiter le formulaire de création (HTML)
     */
    public function store(): void
    {
        $data = $this->app->request()->data;
        $ville_id = $data->ville_id ?? null;
        $article_id = $data->article_id ?? null;
        $quantite = $data->quantite ?? null;
        if ($ville_id && $article_id && $quantite) {
            $this->besoinModel->create([
                'ville_id' => (int) $ville_id,
                'article_id' => (int) $article_id,
                'quantite' => (int) $quantite,
            ]);
        }
        $this->app->redirect('/besoins');
    }

    /**
     * GET /api/besoins/@id — Détail d'un besoin
     */
    public function show(int $id): void
    {
        $besoin = $this->besoinModel->findById($id);
        if (!$besoin) {
            $this->app->json(['error' => 'Besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($besoin, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /api/besoins/par-ville — Besoins groupés par ville (pour tableau de bord)
     */
    public function parVille(): void
    {
        $besoins = $this->besoinModel->findBesoinsParVille();
        $this->app->json($besoins, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/besoins — Créer un besoin
     * Body JSON attendu : { "ville_id": 1, "article_id": 1, "quantite": 100 }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $ville_id = $data->ville_id ?? null;
        $article_id = $data->article_id ?? null;
        $quantite = $data->quantite ?? null;

        if (!$ville_id || !$article_id || !$quantite) {
            $this->app->json(['error' => 'Les champs "ville_id", "article_id" et "quantite" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->besoinModel->create([
            'ville_id' => (int) $ville_id,
            'article_id' => (int) $article_id,
            'quantite' => (int) $quantite
        ]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/besoins/@id — Modifier un besoin
     * Body JSON attendu : { "ville_id": 1, "article_id": 1, "quantite": 100 }
     */
    public function update(int $id): void
    {
        $besoin = $this->besoinModel->findById($id);
        if (!$besoin) {
            $this->app->json(['error' => 'Besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $updateData = [];

        if (isset($data->ville_id)) {
            $updateData['ville_id'] = (int) $data->ville_id;
        }
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

        $this->besoinModel->update($id, $updateData);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/besoins/@id — Supprimer un besoin
     */
    public function destroy(int $id): void
    {
        $besoin = $this->besoinModel->findById($id);
        if (!$besoin) {
            $this->app->json(['error' => 'Besoin non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->besoinModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
