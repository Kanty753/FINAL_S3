<?php

namespace app\controllers;

use app\models\Article;
use app\models\Don;
use flight\Engine;

class DonController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Don($this->app->db());
        $this->app->render('dons/index', ['dons' => $model->findAllDetailed()]);
    }

    public function create()
    {
        $articleModel = new Article($this->app->db());
        $this->app->render('dons/create', ['articles' => $articleModel->findAllWithType()]);
    }

    public function store()
    {
        $model = new Don($this->app->db());
        $model->create([
            'article_id' => $this->app->request()->data->article_id,
            'quantite'   => $this->app->request()->data->quantite,
        ]);
        $this->app->redirect('/dons');
    }

    public function delete($id)
    {
        $model = new Don($this->app->db());
        $model->deleteWithDispatches((int) $id);
        $this->app->redirect('/dons');
    }
}
