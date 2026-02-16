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
     * GET /api/dashboard — Tableau de bord complet
     * Retourne :
     *   - résumé des villes avec total des besoins
     *   - besoins détaillés par ville
     *   - dons attribués (dispatches) par ville
     *   - état global des dons
     *   - statistiques générales
     */
    public function index(): void
    {
        $db = $this->app->db();
        $villeModel = new Ville($db);
        $besoinModel = new Besoin($db);
        $donModel = new Don($db);
        $dispatchModel = new Dispatch($db);
        $regionModel = new Region($db);

        // Résumé des villes avec totaux
        $villesResume = $villeModel->findAllWithTotalBesoins();

        // Besoins par ville
        $besoinsParVille = $besoinModel->findBesoinsParVille();

        // Dispatches par ville
        $dispatchesParVille = $dispatchModel->findDispatchesParVille();

        // État des dons
        $etatDons = $donModel->findEtatDons();

        // Statistiques générales
        $stats = [
            'total_regions' => $regionModel->count(),
            'total_villes' => $villeModel->count(),
            'total_besoins' => $besoinModel->count(),
            'total_dons' => $donModel->count(),
            'total_dispatches' => $dispatchModel->count(),
        ];

        // Construction du tableau de bord structuré par ville
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

        $this->app->json([
            'statistiques' => $stats,
            'etat_dons' => $etatDons,
            'tableau_de_bord' => $dashboard,
        ], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
