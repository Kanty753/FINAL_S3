<?php

namespace app\controllers;

use app\models\Besoin;
use app\models\Dispatch;
use app\models\Don;
use app\models\Ville;
use flight\Engine;

class DispatchController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $model = new Dispatch($this->app->db());
        $this->app->render('dispatches/index', ['dispatches' => $model->findAllDetailed()]);
    }

    /**
     * Simuler le dispatch automatique des dons
     */
    public function simuler()
    {
        $db = $this->app->db();
        $dispatchModel = new Dispatch($db);
        $donModel      = new Don($db);
        $besoinModel   = new Besoin($db);

        $dispatchModel->simuler($donModel, $besoinModel);
        $this->app->redirect('/dashboard');
    }

    public function create()
    {
        $db = $this->app->db();
        $donModel   = new Don($db);
        $villeModel = new Ville($db);

        $this->app->render('dispatches/create', [
            'dons'   => $donModel->findAvailable(),
            'villes' => $villeModel->findAll(),
        ]);
    }

    public function store()
    {
        $model = new Dispatch($this->app->db());
        $model->create([
            'don_id'             => $this->app->request()->data->don_id,
            'ville_id'           => $this->app->request()->data->ville_id,
            'quantite_attribuee' => $this->app->request()->data->quantite_attribuee,
        ]);
        $this->app->redirect('/dispatches');
    }
}
