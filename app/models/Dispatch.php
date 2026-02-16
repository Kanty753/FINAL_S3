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
     * Simuler le dispatch automatique des dons
     * Règle: par ordre de date de saisie des besoins, on attribue les dons disponibles
     */
    public function simuler(Don $donModel, Besoin $besoinModel): void
    {
        // Vider les dispatches existants
        $this->deleteAll();

        // Récupérer tous les dons par ordre de date
        $dons = $donModel->findAllForDispatch();

        // Pour chaque don, distribuer aux villes qui ont besoin de cet article
        foreach ($dons as $don) {
            $resteDon = (int) $don['quantite'];

            // Besoins pour cet article, par ordre de date de saisie
            $besoins = $besoinModel->findByArticle((int) $don['article_id']);

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) {
                    break;
                }

                $besoinRestant = (int) $besoin['quantite'] - (int) $besoin['deja_attribue'];
                if ($besoinRestant <= 0) {
                    continue;
                }

                $aAttribuer = min($resteDon, $besoinRestant);

                $this->create([
                    'don_id' => $don['id'],
                    'ville_id' => $besoin['ville_id'],
                    'quantite_attribuee' => $aAttribuer,
                ]);

                $resteDon -= $aAttribuer;
            }
        }
    }
}
