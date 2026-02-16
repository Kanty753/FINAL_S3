<?php

namespace app\models;

class Don extends BaseModel
{
    protected string $table = 'don';

    /**
     * Tous les dons avec détails article et quantité dispatchée
     */
    public function findAllDetailed(): array
    {
        return $this->db->fetchAll("
            SELECT d.*, a.nom AS article_nom, a.prix_unitaire,
                   tb.libelle AS type_besoin,
                   (d.quantite * a.prix_unitaire) AS montant_total,
                   COALESCE(SUM(di.quantite_attribuee), 0) AS quantite_dispatche
            FROM don d
            JOIN article a ON d.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            LEFT JOIN dispatch di ON di.don_id = d.id
            GROUP BY d.id
            ORDER BY d.date_don DESC
        ");
    }

    /**
     * Tous les dons par ordre de date (pour la simulation dispatch)
     */
    public function findAllForDispatch(): array
    {
        return $this->db->fetchAll("
            SELECT d.id, d.article_id, d.quantite, a.nom AS article_nom
            FROM don d
            JOIN article a ON d.article_id = a.id
            ORDER BY d.date_don ASC, d.id ASC
        ");
    }

    /**
     * Dons avec reste disponible (non encore dispatchés totalement)
     */
    public function findAvailable(): array
    {
        return $this->db->fetchAll("
            SELECT d.id, d.quantite, a.nom AS article_nom,
                   (d.quantite - COALESCE(SUM(di.quantite_attribuee), 0)) AS reste
            FROM don d
            JOIN article a ON d.article_id = a.id
            LEFT JOIN dispatch di ON di.don_id = d.id
            GROUP BY d.id
            HAVING reste > 0
            ORDER BY d.date_don ASC
        ");
    }

    /**
     * État des dons pour le tableau de bord
     */
    public function findEtatDons(): array
    {
        return $this->db->fetchAll("
            SELECT a.nom AS article, dn.quantite AS quantite_don,
                   COALESCE(SUM(d.quantite_attribuee), 0) AS quantite_dispatche,
                   (dn.quantite - COALESCE(SUM(d.quantite_attribuee), 0)) AS reste
            FROM don dn
            JOIN article a ON dn.article_id = a.id
            LEFT JOIN dispatch d ON d.don_id = dn.id
            GROUP BY dn.id, a.nom, dn.quantite
            ORDER BY a.nom
        ");
    }

    /**
     * Supprimer un don et ses dispatches associés
     */
    public function deleteWithDispatches(int $id): void
    {
        $this->db->runQuery("DELETE FROM dispatch WHERE don_id = ?", [$id]);
        $this->delete($id);
    }
}
