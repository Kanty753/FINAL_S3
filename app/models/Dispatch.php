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
<<<<<<< HEAD
     * Supprimer tous les dispatches (pour la simulation)
     */
    public function deleteAll(): void
    {
        $this->db->runQuery("DELETE FROM dispatch");
    }

    /**
     * Simuler le dispatch automatique des dons
     * Règle: par ordre de date de saisie des besoins, on attribue les dons disponibles
     */
    public function simuler(Don $donModel, Besoin $besoinModel): void
    {
        // Vider les dispatches existants
        $this->deleteAll();
=======
     * Simuler le dispatch SANS sauvegarder — retourne un tableau de résultats preview
     * Même logique que simuler() : priorité par date_saisie ASC (premier arrivé = premier servi).
     * Les attributions sont suivies en mémoire pour redistribuer correctement les restes.
     */
    public function simulerPreview(Don $donModel, Besoin $besoinModel): array
    {
        $results = [];
>>>>>>> d3692f7 (commit v1)

        // Récupérer tous les dons par ordre de date
        $dons = $donModel->findAllForDispatch();

<<<<<<< HEAD
        // Pour chaque don, distribuer aux villes qui ont besoin de cet article
        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];

            // Besoins pour cet article, par ordre de date de saisie
=======
        // Maps pour affichage
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

        // Suivi en mémoire des quantités déjà attribuées par besoin_id
        $attribueParBesoin = [];

        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];

            // Besoins pour cet article, par ordre de date_saisie (priorité premier arrivé)
>>>>>>> d3692f7 (commit v1)
            $besoins = $besoinModel->findByArticle((int) $don['article_id']);

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) {
                    break;
                }

<<<<<<< HEAD
                $besoinRestant = (int) $besoin['quantite'] - (int) $besoin['deja_attribue'];
=======
                $besoinId = (int) $besoin['id'];
                $dejaAttribue = ($attribueParBesoin[$besoinId] ?? 0);
                $besoinRestant = (int) $besoin['quantite'] - $dejaAttribue;

>>>>>>> d3692f7 (commit v1)
                if ($besoinRestant <= 0) {
                    continue;
                }

                $aAttribuer = min($resteDon, $besoinRestant);
<<<<<<< HEAD

                $this->create([
                    'don_id' => $don['id'],
                    'ville_id' => $besoin['ville_id'],
                    'quantite_attribuee' => $aAttribuer,
                ]);

                $resteDon -= $aAttribuer;
            }
        }
=======
                $articleInfo = $articlesMap[(int)$don['article_id']] ?? null;
                $prixUnit = $articleInfo ? (float)$articleInfo['prix_unitaire'] : 0;

                $results[] = [
                    'don_id' => $don['id'],
                    'ville_id' => $besoin['ville_id'],
                    'ville_nom' => $villesMap[(int)$besoin['ville_id']] ?? 'Inconnu',
                    'article_nom' => $don['article_nom'],
                    'quantite_attribuee' => $aAttribuer,
                    'prix_unitaire' => $prixUnit,
                    'montant' => $aAttribuer * $prixUnit,
                ];

                // Mettre à jour le suivi en mémoire
                $attribueParBesoin[$besoinId] = $dejaAttribue + $aAttribuer;
                $resteDon -= $aAttribuer;
            }
        }

        return $results;
>>>>>>> d3692f7 (commit v1)
    }
}
