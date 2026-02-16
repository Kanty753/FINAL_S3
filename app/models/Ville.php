<?php

namespace app\models;

class Ville extends BaseModel
{
    protected string $table = 'ville';

    /**
     * Toutes les villes avec le nom de leur région
     */
    public function findAllWithRegion(): array
    {
        return $this->db->fetchAll("
            SELECT v.*, r.nom AS region_nom
            FROM ville v
            JOIN region r ON v.region_id = r.id
            ORDER BY v.nom
        ");
    }

    /**
     * Toutes les villes triées par nom
     */
    public function findAll(string $orderBy = 'nom ASC'): array
    {
        return parent::findAll($orderBy);
    }

    /**
     * Résumé des villes avec total des besoins
     */
    public function findAllWithTotalBesoins(): array
    {
        return $this->db->fetchAll("
            SELECT v.id, v.nom AS ville, r.nom AS region,
                   COALESCE(SUM(b.quantite * a.prix_unitaire), 0) AS total_besoin
            FROM ville v
            JOIN region r ON v.region_id = r.id
            LEFT JOIN besoin b ON b.ville_id = v.id
            LEFT JOIN article a ON b.article_id = a.id
            GROUP BY v.id, v.nom, r.nom
            ORDER BY v.nom
        ");
    }
}
