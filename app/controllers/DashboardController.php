<?php

namespace app\controllers;

use app\models\Besoin;
use app\models\Dispatch;
use app\models\Don;
use app\models\Ville;
use flight\Engine;

class DashboardController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index()
    {
        $db = $this->app->db();

        $besoinModel   = new Besoin($db);
        $dispatchModel = new Dispatch($db);
        $villeModel    = new Ville($db);
        $donModel      = new Don($db);

        $this->app->render('dashboard', [
            'besoins'       => $besoinModel->findBesoinsParVille(),
            'dispatches'    => $dispatchModel->findDispatchesParVille(),
            'resume_villes' => $villeModel->findAllWithTotalBesoins(),
            'total_dons'    => $donModel->findEtatDons(),
        ]);
    }
}
