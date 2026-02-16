<?php

namespace app\controllers;

use app\models\Region;
use app\models\Ville;
use flight\Engine;

class VilleController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Ville($this->app->db());
        $this->app->render('villes/index', ['villes' => $model->findAllWithRegion()]);
    }

    public function create()
    {
        $regionModel = new Region($this->app->db());
        $this->app->render('villes/create', ['regions' => $regionModel->findAll()]);
    }

    public function store()
    {
        $model = new Ville($this->app->db());
        $model->create([
            'nom'       => $this->app->request()->data->nom,
            'region_id' => $this->app->request()->data->region_id,
        ]);
        $this->app->redirect('/villes');
    }
}
