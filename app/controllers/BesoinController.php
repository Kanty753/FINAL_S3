<?php

namespace app\controllers;

use app\models\Article;
use app\models\Besoin;
use app\models\Ville;
use flight\Engine;

class BesoinController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Besoin($this->app->db());
        $this->app->render('besoins/index', ['besoins' => $model->findAllDetailed()]);
    }

    public function create()
    {
        $db = $this->app->db();
        $villeModel   = new Ville($db);
        $articleModel = new Article($db);

        $this->app->render('besoins/create', [
            'villes'   => $villeModel->findAll(),
            'articles' => $articleModel->findAllWithType(),
        ]);
    }

    public function store()
    {
        $model = new Besoin($this->app->db());
        $model->create([
            'ville_id'   => $this->app->request()->data->ville_id,
            'article_id' => $this->app->request()->data->article_id,
            'quantite'   => $this->app->request()->data->quantite,
        ]);
        $this->app->redirect('/besoins');
    }

    public function delete($id)
    {
        $model = new Besoin($this->app->db());
        $model->delete((int) $id);
        $this->app->redirect('/besoins');
    }
}
