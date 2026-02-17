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

    /**
     * Récupérer les besoins bruts pour un article (sans calcul deja_attribue depuis la BDD)
     * Triés par date_saisie ASC (priorité FIFO : premier arrivé, premier servi)
     */
    private function fetchBesoinsForArticle(Besoin $besoinModel, int $articleId): array
    {
        return $this->db->fetchAll("
            SELECT b.id, b.ville_id, b.quantite, b.date_saisie
            FROM besoin b
            WHERE b.article_id = ?
            ORDER BY b.date_saisie ASC, b.id ASC
        ", [$articleId]);
    }

    /**
     * Simuler le dispatch automatique des dons
     *
     * Règle de priorité FIFO :
     *  - Les dons sont traités par ordre chronologique (date_don ASC).
     *  - Pour chaque don, on liste les besoins de cet article triés par date_saisie ASC.
     *  - La ville qui a fait la demande en PREMIER est servie en priorité.
     *  - Si le don couvre entièrement son besoin et qu'il reste du stock,
     *    le reste est redistribué à la demande suivante, et ainsi de suite.
     */
    public function simuler(Don $donModel, Besoin $besoinModel): void
    {
        // Vider les dispatches existants
        $this->deleteAll();

        // Récupérer tous les dons par ordre chronologique
        $dons = $donModel->findAllForDispatch();

        // Suivi en mémoire de ce qui a déjà été attribué par besoin_id
        // Clé : besoin_id, Valeur : quantité déjà attribuée
        $attribueParBesoin = [];

        // Pour chaque don, distribuer aux villes qui ont besoin de cet article
        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];

            // Besoins pour cet article, par ordre de date de saisie (FIFO)
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id']);

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

                // On attribue le minimum entre ce qui reste du don et le besoin restant
                $aAttribuer = min($resteDon, $besoinRestant);

                $this->create([
                    'don_id' => $don['id'],
                    'ville_id' => $besoin['ville_id'],
                    'quantite_attribuee' => $aAttribuer,
                ]);

                // Mettre à jour le suivi en mémoire
                $attribueParBesoin[$besoinId] = $dejaAttribue + $aAttribuer;
                $resteDon -= $aAttribuer;
            }
        }
    }

    /**
     * Simuler le dispatch SANS sauvegarder — retourne un tableau de résultats preview
     *
     * Même logique de priorité FIFO que simuler() :
     *  - Premier demandeur servi en premier, le reste redistribué aux suivants.
     *  - Suivi entièrement en mémoire (aucune écriture en BDD).
     */
    public function simulerPreview(Don $donModel, Besoin $besoinModel): array
    {
        $results = [];

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

        // Suivi en mémoire de ce qui a déjà été attribué par besoin_id
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];
            $besoins = $this->fetchBesoinsForArticle($besoinModel, (int) $don['article_id']);

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
                    'priorite' => $dejaAttribue === 0 ? 'Premier servi' : 'Suite',
                ];

                // Mettre à jour le suivi en mémoire
                $attribueParBesoin[$besoinId] = $dejaAttribue + $aAttribuer;
                $resteDon -= $aAttribuer;
            }
        }

        return $results;
    }
}
