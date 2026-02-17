<?php

namespace app\models;

class Dispatch extends BaseModel
{
    protected string $table = 'dispatch';

    /**
     * Tous les dispatches avec détails ville et article
     */
    public function findAllDetailed(): array
    {
        return $this->db->fetchAll("
            SELECT di.*, v.nom AS ville_nom, a.nom AS article_nom,
                   a.prix_unitaire,
                   (di.quantite_attribuee * a.prix_unitaire) AS montant
            FROM dispatch di
            JOIN don d ON di.don_id = d.id
            JOIN article a ON d.article_id = a.id
            JOIN ville v ON di.ville_id = v.id
            ORDER BY di.date_dispatch DESC
        ");
    }

    /**
     * Dispatches groupés par ville (pour le tableau de bord)
     */
    public function findDispatchesParVille(): array
    {
        return $this->db->fetchAll("
            SELECT v.id as ville_id, v.nom AS ville, a.nom AS article,
                   SUM(d.quantite_attribuee) AS total_attribue,
                   SUM(d.quantite_attribuee * a.prix_unitaire) AS montant_attribue
            FROM dispatch d
            JOIN don dn ON d.don_id = dn.id
            JOIN article a ON dn.article_id = a.id
            JOIN ville v ON d.ville_id = v.id
            GROUP BY v.id, v.nom, a.nom
            ORDER BY v.nom, a.nom
        ");
    }

    /**
     * Supprimer tous les dispatches (pour la simulation)
     */
    public function deleteAll(): void
    {
        $this->db->runQuery("DELETE FROM dispatch");
    }

    // Constantes pour les stratégies de dispatch
    const STRATEGIE_FIFO = 'fifo';                   // Par ordre de saisie (premier arrivé, premier servi)
    const STRATEGIE_PLUS_PETIT = 'plus_petit';        // Par infériorité des ressources nécessaires
    const STRATEGIE_PROPORTIONNEL = 'proportionnel';  // Répartition proportionnelle

    /**
     * Récupérer les besoins bruts pour un article
     * @param string $strategie La stratégie de tri
     */
    private function fetchBesoinsForArticle(Besoin $besoinModel, int $articleId, string $strategie = self::STRATEGIE_FIFO): array
    {
        $orderBy = match ($strategie) {
            self::STRATEGIE_PLUS_PETIT => 'b.quantite ASC, b.date_saisie ASC, b.id ASC',
            default => 'b.date_saisie ASC, b.id ASC',
        };

        return $this->db->fetchAll("
            SELECT b.id, b.ville_id, b.quantite, b.date_saisie
            FROM besoin b
            WHERE b.article_id = ?
            ORDER BY {$orderBy}
        ", [$articleId]);
    }

    /**
     * Simuler le dispatch automatique des dons (sauvegarde en BDD)
     *
     * @param string $strategie La stratégie de dispatch :
     *  - 'fifo' : par ordre de saisie (premier arrivé, premier servi)
     *  - 'plus_petit' : priorité à celui qui a le plus petit besoin
     *  - 'proportionnel' : répartition proportionnelle entre les villes
     */
    public function simuler(Don $donModel, Besoin $besoinModel, string $strategie = self::STRATEGIE_FIFO): void
    {
        // Vider les dispatches existants
        $this->deleteAll();

        // Récupérer tous les dons par ordre chronologique
        $dons = $donModel->findAllForDispatch();

        if ($strategie === self::STRATEGIE_PROPORTIONNEL) {
            $this->simulerProportionnelSave($dons, $besoinModel);
        } else {
            $this->simulerSequentielSave($dons, $besoinModel, $strategie);
        }
    }

    /**
     * Dispatch séquentiel (FIFO ou plus petit besoin) — sauvegarde en BDD
     */
    private function simulerSequentielSave(array $dons, Besoin $besoinModel, string $strategie): void
    {
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id'], $strategie);

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) {
                    break;
                }

                $besoinId = (int) $besoin['id'];
                $dejaAttribue = $attribueParBesoin[$besoinId] ?? 0;
                $besoinRestant = (int) $besoin['quantite'] - $dejaAttribue;

                if ($besoinRestant <= 0) {
                    continue;
                }

                $aAttribuer = min($resteDon, $besoinRestant);

                $this->create([
                    'don_id' => $don['id'],
                    'ville_id' => $besoin['ville_id'],
                    'quantite_attribuee' => $aAttribuer,
                ]);

                $attribueParBesoin[$besoinId] = $dejaAttribue + $aAttribuer;
                $resteDon -= $aAttribuer;
            }
        }
    }

    /**
     * Dispatch proportionnel — sauvegarde en BDD
     *
     * Pour chaque don, on calcule la part proportionnelle de chaque ville :
     *   part_ville = (besoin_ville / total_besoins) * quantite_don
     * Arrondissement : < 0.5 → arrondi par défaut, >= 0.5 → arrondi par excès
     * Le total attribué ne dépasse jamais le don disponible.
     */
    private function simulerProportionnelSave(array $dons, Besoin $besoinModel): void
    {
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id']);

            // Calculer les besoins restants effectifs
            $besoinsEffectifs = [];
            $totalBesoinsRestants = 0;
            foreach ($besoins as $besoin) {
                $besoinId = (int) $besoin['id'];
                $dejaAttribue = $attribueParBesoin[$besoinId] ?? 0;
                $besoinRestant = (int) $besoin['quantite'] - $dejaAttribue;
                if ($besoinRestant > 0) {
                    $besoinsEffectifs[] = [
                        'id' => $besoinId,
                        'ville_id' => $besoin['ville_id'],
                        'quantite' => (int) $besoin['quantite'],
                        'besoin_restant' => $besoinRestant,
                        'date_saisie' => $besoin['date_saisie'],
                    ];
                    $totalBesoinsRestants += $besoinRestant;
                }
            }

            if ($totalBesoinsRestants <= 0 || $resteDon <= 0) {
                continue;
            }

            // Calculer la part proportionnelle pour chaque ville
            $attributions = [];
            $totalAttribue = 0;
            foreach ($besoinsEffectifs as $be) {
                $partExacte = ($be['besoin_restant'] / $totalBesoinsRestants) * $resteDon;
                // On conserve uniquement la partie entière (arrondi à l'inférieur)
                $partArrondie = (int) floor($partExacte);
                // Ne pas dépasser le besoin restant de la ville
                $partArrondie = min($partArrondie, $be['besoin_restant']);
                $attributions[] = [
                    'besoin_id' => $be['id'],
                    'ville_id' => $be['ville_id'],
                    'quantite' => $partArrondie,
                    'besoin_restant' => $be['besoin_restant'],
                ];
                $totalAttribue += $partArrondie;
            }

            // S'assurer qu'on ne dépasse pas le don disponible
            // Si l'arrondi a causé un excédent, on réduit les dernières attributions
            while ($totalAttribue > $resteDon && !empty($attributions)) {
                for ($i = count($attributions) - 1; $i >= 0 && $totalAttribue > $resteDon; $i--) {
                    if ($attributions[$i]['quantite'] > 0) {
                        $attributions[$i]['quantite']--;
                        $totalAttribue--;
                    }
                }
            }

            // Enregistrer les attributions
            foreach ($attributions as $attr) {
                if ($attr['quantite'] > 0) {
                    $this->create([
                        'don_id' => $don['id'],
                        'ville_id' => $attr['ville_id'],
                        'quantite_attribuee' => $attr['quantite'],
                    ]);
                    $attribueParBesoin[$attr['besoin_id']] = ($attribueParBesoin[$attr['besoin_id']] ?? 0) + $attr['quantite'];
                }
            }
        }
    }

    /**
     * Simuler le dispatch SANS sauvegarder — retourne un tableau de résultats preview
     *
     * @param string $strategie La stratégie de dispatch ('fifo', 'plus_petit', 'proportionnel')
     */
    public function simulerPreview(Don $donModel, Besoin $besoinModel, string $strategie = self::STRATEGIE_FIFO): array
    {
        // Récupérer tous les dons par ordre chronologique
        $dons = $donModel->findAllForDispatch();

        // Récupérer les infos des villes et articles pour l'affichage
        $villesMap = [];
        $villes = $this->db->fetchAll("SELECT id, nom FROM ville");
        foreach ($villes as $v) {
            $villesMap[(int)$v['id']] = $v['nom'];
        }

        $articlesMap = [];
        $articles = $this->db->fetchAll("SELECT a.id, a.nom, a.prix_unitaire FROM article a");
        foreach ($articles as $a) {
            $articlesMap[(int)$a['id']] = $a;
        }

        if ($strategie === self::STRATEGIE_PROPORTIONNEL) {
            return $this->simulerProportionnelPreview($dons, $besoinModel, $villesMap, $articlesMap);
        } else {
            return $this->simulerSequentielPreview($dons, $besoinModel, $villesMap, $articlesMap, $strategie);
        }
    }

    /**
     * Preview séquentiel (FIFO ou plus petit besoin)
     */
    private function simulerSequentielPreview(array $dons, Besoin $besoinModel, array $villesMap, array $articlesMap, string $strategie): array
    {
        $results = [];
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id'], $strategie);

            // Compteur pour savoir le rang de la ville servie pour CE don
            $rangDansDon = 0;

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) {
                    break;
                }

                $besoinId = (int) $besoin['id'];
                $dejaAttribue = $attribueParBesoin[$besoinId] ?? 0;
                $besoinRestant = (int) $besoin['quantite'] - $dejaAttribue;

                if ($besoinRestant <= 0) {
                    continue;
                }

                $aAttribuer = min($resteDon, $besoinRestant);
                $articleInfo = $articlesMap[(int)$don['article_id']] ?? null;
                $prixUnit = $articleInfo ? (float)$articleInfo['prix_unitaire'] : 0;

                // La priorité dépend du rang dans le don : seul le 1er servi a la priorité
                $prioriteLabel = match ($strategie) {
                    self::STRATEGIE_PLUS_PETIT => $rangDansDon === 0 ? 'Plus petit besoin' : 'Redistribution',
                    default => $rangDansDon === 0 ? 'Premier servi' : 'Redistribution',
                };

                $results[] = [
                    'don_id' => $don['id'],
                    'besoin_id' => $besoinId,
                    'ville_id' => $besoin['ville_id'],
                    'ville_nom' => $villesMap[(int)$besoin['ville_id']] ?? 'Inconnu',
                    'article_nom' => $don['article_nom'],
                    'quantite_demandee' => (int) $besoin['quantite'],
                    'quantite_attribuee' => $aAttribuer,
                    'quantite_restante' => $besoinRestant - $aAttribuer,
                    'date_demande' => $besoin['date_saisie'],
                    'prix_unitaire' => $prixUnit,
                    'montant' => $aAttribuer * $prixUnit,
                    'priorite' => $prioriteLabel,
                ];

                $attribueParBesoin[$besoinId] = $dejaAttribue + $aAttribuer;
                $resteDon -= $aAttribuer;
                $rangDansDon++;
            }
        }

        return $results;
    }

    /**
     * Preview proportionnel
     */
    private function simulerProportionnelPreview(array $dons, Besoin $besoinModel, array $villesMap, array $articlesMap): array
    {
        $results = [];
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id']);

            // Calculer les besoins restants effectifs
            $besoinsEffectifs = [];
            $totalBesoinsRestants = 0;
            foreach ($besoins as $besoin) {
                $besoinId = (int) $besoin['id'];
                $dejaAttribue = $attribueParBesoin[$besoinId] ?? 0;
                $besoinRestant = (int) $besoin['quantite'] - $dejaAttribue;
                if ($besoinRestant > 0) {
                    $besoinsEffectifs[] = [
                        'id' => $besoinId,
                        'ville_id' => $besoin['ville_id'],
                        'quantite' => (int) $besoin['quantite'],
                        'besoin_restant' => $besoinRestant,
                        'date_saisie' => $besoin['date_saisie'],
                    ];
                    $totalBesoinsRestants += $besoinRestant;
                }
            }

            if ($totalBesoinsRestants <= 0 || $resteDon <= 0) {
                continue;
            }

            // Calculer la part proportionnelle pour chaque ville
            $attributionsTemp = [];
            $totalAttribue = 0;
            foreach ($besoinsEffectifs as $be) {
                $partExacte = ($be['besoin_restant'] / $totalBesoinsRestants) * $resteDon;
                // On conserve uniquement la partie entière (arrondi à l'inférieur)
                $partArrondie = (int) floor($partExacte);
                $partArrondie = min($partArrondie, $be['besoin_restant']);
                $attributionsTemp[] = [
                    'besoin_id' => $be['id'],
                    'ville_id' => $be['ville_id'],
                    'quantite_demandee' => $be['quantite'],
                    'besoin_restant' => $be['besoin_restant'],
                    'quantite_attribuee' => $partArrondie,
                    'part_exacte' => $partExacte,
                    'date_saisie' => $be['date_saisie'],
                ];
                $totalAttribue += $partArrondie;
            }

            // Ajuster si l'arrondi a causé un excédent
            while ($totalAttribue > $resteDon && !empty($attributionsTemp)) {
                for ($i = count($attributionsTemp) - 1; $i >= 0 && $totalAttribue > $resteDon; $i--) {
                    if ($attributionsTemp[$i]['quantite_attribuee'] > 0) {
                        $attributionsTemp[$i]['quantite_attribuee']--;
                        $totalAttribue--;
                    }
                }
            }

            $articleInfo = $articlesMap[(int)$don['article_id']] ?? null;
            $prixUnit = $articleInfo ? (float)$articleInfo['prix_unitaire'] : 0;

            foreach ($attributionsTemp as $attr) {
                $qteAttr = $attr['quantite_attribuee'];
                $resteNonCouvert = $attr['besoin_restant'] - $qteAttr;

                $results[] = [
                    'don_id' => $don['id'],
                    'besoin_id' => $attr['besoin_id'],
                    'ville_id' => $attr['ville_id'],
                    'ville_nom' => $villesMap[(int)$attr['ville_id']] ?? 'Inconnu',
                    'article_nom' => $don['article_nom'],
                    'quantite_demandee' => $attr['quantite_demandee'],
                    'quantite_attribuee' => $qteAttr,
                    'quantite_restante' => $resteNonCouvert,
                    'date_demande' => $attr['date_saisie'],
                    'prix_unitaire' => $prixUnit,
                    'montant' => $qteAttr * $prixUnit,
                    'priorite' => 'Proportionnel',
                    'part_exacte' => round($attr['part_exacte'], 2),
                ];

                if ($qteAttr > 0) {
                    $attribueParBesoin[$attr['besoin_id']] = ($attribueParBesoin[$attr['besoin_id']] ?? 0) + $qteAttr;
                }
            }
        }

        return $results;
    }
}
