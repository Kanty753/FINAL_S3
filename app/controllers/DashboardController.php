<?php

namespace app\controllers;

use flight\Engine;
use app\models\Ville;
use app\models\Besoin;
use app\models\Don;
use app\models\Dispatch;
use app\models\Region;

class DashboardController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * GET /api/dashboard — Tableau de bord complet (JSON)
     */
    public function index(): void
    {
        $data = $this->getDashboardData();

        $this->app->json([
            'statistiques' => $data['stats'],
            'etat_dons' => $data['etatDons'],
            'tableau_de_bord' => $data['dashboard'],
        ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    /**
     * GET /dashboard — Page tableau de bord (HTML)
     */
    public function page(): void
    {
        $data = $this->getDashboardData();

        $content = $this->app->view()->fetch('dashboard', [
            'stats' => $data['stats'],
            'etat_dons' => $data['etatDons'],
            'dashboard' => $data['dashboard'],
        ]);

        $this->app->render('layout', [
            'content' => $content,
            'page_title' => 'Tableau de bord',
            'active_page' => 'dashboard',
        ]);
    }

    private function getDashboardData(): array
    {
        $db = $this->app->db();
        $villeModel = new Ville($db);
        $besoinModel = new Besoin($db);
        $donModel = new Don($db);
        $dispatchModel = new Dispatch($db);
        $regionModel = new Region($db);

        $villesResume = $villeModel->findAllWithTotalBesoins();
        $besoinsParVille = $besoinModel->findBesoinsParVille();
        $dispatchesParVille = $dispatchModel->findDispatchesParVille();
        $etatDons = $donModel->findEtatDons();

        $stats = [
            'total_regions' => $regionModel->count(),
            'total_villes' => $villeModel->count(),
            'total_besoins' => $besoinModel->count(),
            'total_dons' => $donModel->count(),
            'total_dispatches' => $dispatchModel->count(),
        ];

        $dashboard = [];
        foreach ($villesResume as $ville) {
            $villeId = $ville['id'];
            $dashboard[] = [
                'ville' => $ville['ville'],
                'region' => $ville['region'],
                'total_besoin_montant' => $ville['total_besoin'],
                'besoins' => array_values(array_filter($besoinsParVille, fn($b) => (int)$b['ville_id'] === (int)$villeId)),
                'dons_attribues' => array_values(array_filter($dispatchesParVille, fn($d) => (int)$d['ville_id'] === (int)$villeId)),
            ];
        }

        return compact('stats', 'etatDons', 'dashboard');
    }
}
