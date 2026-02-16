<?php

namespace app\controllers;

use flight\Engine;
use app\models\Article;

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
