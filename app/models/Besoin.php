<?php

namespace app\models;

class Besoin extends BaseModel
{
    protected string $table = 'besoin';

    /**
     * Tous les besoins avec infos ville, article et type
     */
    public function findAllDetailed(): array
    {
        return $this->db->fetchAll("
            SELECT b.*, v.nom AS ville_nom, a.nom AS article_nom,
                   a.prix_unitaire, tb.libelle AS type_besoin,
                   (b.quantite * a.prix_unitaire) AS montant_total
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            ORDER BY b.date_saisie DESC
        ");
    }

    /**
     * Besoins par ville pour le tableau de bord
     */
    public function findBesoinsParVille(): array
    {
        return $this->db->fetchAll("
            SELECT v.id as ville_id, v.nom AS ville, r.nom AS region,
                   a.nom AS article, tb.libelle AS type_besoin,
                   b.quantite AS besoin_quantite,
                   a.prix_unitaire,
                   (b.quantite * a.prix_unitaire) AS montant_besoin
            FROM besoin b
            JOIN ville v ON b.ville_id = v.id
            JOIN region r ON v.region_id = r.id
            JOIN article a ON b.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            ORDER BY v.nom, a.nom
        ");
    }

    /**
     * Besoins pour un article donné, par ordre de date de saisie
     */
    public function findByArticle(int $articleId): array
    {
        return $this->db->fetchAll("
            SELECT b.id, b.ville_id, b.quantite,
                   COALESCE((SELECT SUM(di.quantite_attribuee) FROM dispatch di
                             JOIN don dn ON di.don_id = dn.id
                             WHERE di.ville_id = b.ville_id AND dn.article_id = ?), 0) AS deja_attribue
            FROM besoin b
            WHERE b.article_id = ?
            ORDER BY b.date_saisie ASC, b.id ASC
        ", [$articleId, $articleId]);
    }
}
