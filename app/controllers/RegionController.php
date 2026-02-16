<?php

namespace app\controllers;

use app\models\Region;
use flight\Engine;

class RegionController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Region($this->app->db());
        $this->app->render('regions/index', ['regions' => $model->findAll()]);
    }

    public function create()
    {
        $this->app->render('regions/create');
    }

    public function store()
    {
        $model = new Region($this->app->db());
        $model->create([
            'nom' => $this->app->request()->data->nom,
        ]);
        $this->app->redirect('/regions');
    }
}
