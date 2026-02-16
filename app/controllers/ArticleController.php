<?php

namespace app\controllers;

use app\models\Article;
use app\models\TypeBesoin;
use flight\Engine;

class ArticleController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Article($this->app->db());
        $this->app->render('articles/index', ['articles' => $model->findAllWithType()]);
    }

    public function create()
    {
        $typeModel = new TypeBesoin($this->app->db());
        $this->app->render('articles/create', ['types' => $typeModel->findAll()]);
    }

    public function store()
    {
        $model = new Article($this->app->db());
        $model->create([
            'nom'            => $this->app->request()->data->nom,
            'type_besoin_id' => $this->app->request()->data->type_besoin_id,
            'prix_unitaire'  => $this->app->request()->data->prix_unitaire,
        ]);
        $this->app->redirect('/articles');
    }
}
