<?php

namespace app\controllers;

use flight\Engine;
use app\models\Article;
use app\models\TypeBesoin;

class ArticleController
{
    protected Engine $app;
    protected Article $articleModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->articleModel = new Article($app->db());
    }

    /**
     * GET /api/articles — Liste de tous les articles avec leur type de besoin
     */
    public function index(): void
    {
        $articles = $this->articleModel->findAllWithType();
        $this->app->json($articles, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /articles — Page liste des articles (HTML)
     */
    public function page(): void
    {
        $articles = $this->articleModel->findAllWithType();
        $content = $this->app->view()->fetch('articles/index', ['articles' => $articles]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Articles',
            'active_page' => 'articles',
        ]);
    }

    /**
     * GET /articles/create — Formulaire de création (HTML)
     */
    public function createPage(): void
    {
        $typeBesoinModel = new TypeBesoin($this->app->db());
        $types_besoins = $typeBesoinModel->findAll();
        $content = $this->app->view()->fetch('articles/create', ['types_besoins' => $types_besoins]);
        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Nouvel article',
            'active_page' => 'articles',
        ]);
    }

    /**
     * POST /articles — Traiter le formulaire de création (HTML)
     */
    public function store(): void
    {
        $data = $this->app->request()->data;
        $nom = $data->nom ?? null;
        $type_besoin_id = $data->type_besoin_id ?? null;
        $prix_unitaire = $data->prix_unitaire ?? null;
        if ($nom && $type_besoin_id && $prix_unitaire !== null) {
            $this->articleModel->create([
                'nom' => $nom,
                'type_besoin_id' => (int) $type_besoin_id,
                'prix_unitaire' => (float) $prix_unitaire,
            ]);
        }
        $this->app->redirect('/articles');
    }

    /**
     * GET /api/articles/@id — Détail d'un article
     */
    public function show(int $id): void
    {
        $article = $this->articleModel->findById($id);
        if (!$article) {
            $this->app->json(['error' => 'Article non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }
        $this->app->json($article, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * POST /api/articles — Créer un article
     * Body JSON attendu : { "nom": "...", "type_besoin_id": 1, "prix_unitaire": 2500 }
     */
    public function create(): void
    {
        $data = $this->app->request()->data;
        $nom = $data->nom ?? null;
        $type_besoin_id = $data->type_besoin_id ?? null;
        $prix_unitaire = $data->prix_unitaire ?? null;

        if (!$nom || !$type_besoin_id || $prix_unitaire === null) {
            $this->app->json(['error' => 'Les champs "nom", "type_besoin_id" et "prix_unitaire" sont requis'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $id = $this->articleModel->create([
            'nom' => $nom,
            'type_besoin_id' => (int) $type_besoin_id,
            'prix_unitaire' => (float) $prix_unitaire
        ]);
        $this->app->json(['success' => true, 'id' => $id], 201, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * PUT /api/articles/@id — Modifier un article
     * Body JSON attendu : { "nom": "...", "type_besoin_id": 1, "prix_unitaire": 2500 }
     */
    public function update(int $id): void
    {
        $article = $this->articleModel->findById($id);
        if (!$article) {
            $this->app->json(['error' => 'Article non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $data = $this->app->request()->data;
        $updateData = [];

        if (isset($data->nom)) {
            $updateData['nom'] = $data->nom;
        }
        if (isset($data->type_besoin_id)) {
            $updateData['type_besoin_id'] = (int) $data->type_besoin_id;
        }
        if (isset($data->prix_unitaire)) {
            $updateData['prix_unitaire'] = (float) $data->prix_unitaire;
        }

        if (empty($updateData)) {
            $this->app->json(['error' => 'Aucune donnée à mettre à jour'], 400, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->articleModel->update($id, $updateData);
        $this->app->json(['success' => true, 'id' => $id], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * DELETE /api/articles/@id — Supprimer un article
     */
    public function destroy(int $id): void
    {
        $article = $this->articleModel->findById($id);
        if (!$article) {
            $this->app->json(['error' => 'Article non trouvé'], 404, true, 'utf-8', JSON_PRETTY_PRINT);
            return;
        }

        $this->articleModel->delete($id);
        $this->app->json(['success' => true], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
