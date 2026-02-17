<?php

namespace app\models;

class Achat extends BaseModel
{
    protected string $table = 'achat';

    /**
     * Tous les achats avec détails (besoin, don, ville, article)
     */
    public function findAllDetailed(): array
    {
        return $this->db->fetchAll("
            SELECT ac.*, 
                   v.nom AS ville_nom,
                   a.nom AS article_nom,
                   a.prix_unitaire,
                   tb.libelle AS type_besoin,
                   b.quantite AS besoin_quantite,
                   ac.montant AS montant_achat
            FROM achat ac
            JOIN besoin b ON ac.besoin_id = b.id
            JOIN don d ON ac.don_id = d.id
            JOIN ville v ON ac.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            ORDER BY ac.date_achat DESC
        ");
    }

    /**
     * Achats filtrés par ville
     */
    public function findByVille(int $villeId): array
    {
        return $this->db->fetchAll("
            SELECT ac.*, 
                   v.nom AS ville_nom,
                   a.nom AS article_nom,
                   a.prix_unitaire,
                   tb.libelle AS type_besoin,
                   b.quantite AS besoin_quantite,
                   ac.montant AS montant_achat
            FROM achat ac
            JOIN besoin b ON ac.besoin_id = b.id
            JOIN don d ON ac.don_id = d.id
            JOIN ville v ON ac.ville_id = v.id
            JOIN article a ON b.article_id = a.id
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            WHERE ac.ville_id = ?
            ORDER BY ac.date_achat DESC
        ", [$villeId]);
    }

    /**
     * Besoins restants en nature et matériaux (non satisfaits par dispatch ni achat)
     * Ce sont les besoins qu'on peut acheter avec des dons en argent
     */
    public function findBesoinsRestantsAchetables(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM (
                SELECT b.id AS besoin_id, b.ville_id, v.nom AS ville_nom,
                       a.id AS article_id, a.nom AS article_nom, a.prix_unitaire,
                       tb.libelle AS type_besoin,
                       b.quantite AS besoin_quantite,
                       COALESCE((
                           SELECT SUM(di.quantite_attribuee) 
                           FROM dispatch di 
                           JOIN don dn ON di.don_id = dn.id 
                           WHERE di.ville_id = b.ville_id AND dn.article_id = b.article_id
                       ), 0) AS quantite_dispatche,
                       COALESCE((
                           SELECT FLOOR(SUM(ac2.montant / a.prix_unitaire))
                           FROM achat ac2 
                           WHERE ac2.besoin_id = b.id
                       ), 0) AS quantite_achetee,
                       (b.quantite 
                           - COALESCE((
                               SELECT SUM(di.quantite_attribuee) 
                               FROM dispatch di 
                               JOIN don dn ON di.don_id = dn.id 
                               WHERE di.ville_id = b.ville_id AND dn.article_id = b.article_id
                           ), 0)
                           - COALESCE((
                               SELECT FLOOR(SUM(ac2.montant / a.prix_unitaire))
                               FROM achat ac2 
                               WHERE ac2.besoin_id = b.id
                           ), 0)
                       ) AS quantite_restante
                FROM besoin b
                JOIN ville v ON b.ville_id = v.id
                JOIN article a ON b.article_id = a.id
                JOIN type_besoin tb ON a.type_besoin_id = tb.id
                WHERE tb.libelle IN ('Nature', 'Matériaux')
            ) AS sub
            WHERE sub.quantite_restante > 0
            ORDER BY sub.ville_nom, sub.article_nom
        ");
    }

    /**
     * Dons en argent disponibles (montant restant après dispatches et achats)
     */
    public function findDonsArgentDisponibles(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM (
                SELECT d.id AS don_id, 
                       d.quantite AS montant_don,
                       a.nom AS article_nom,
                       (d.quantite 
                           - COALESCE((SELECT SUM(di.quantite_attribuee) FROM dispatch di WHERE di.don_id = d.id), 0)
                           - COALESCE((SELECT SUM(ac.montant + (ac.montant * ac.frais / 100)) FROM achat ac WHERE ac.don_id = d.id), 0)
                       ) AS montant_restant
                FROM don d
                JOIN article a ON d.article_id = a.id
                JOIN type_besoin tb ON a.type_besoin_id = tb.id
                WHERE tb.libelle = 'Argent'
            ) AS sub
            WHERE sub.montant_restant > 0
            ORDER BY sub.don_id ASC
        ");
    }

    /**
     * Vérifier si un besoin est déjà entièrement couvert par des dons dispatchés
     */
    public function besoinDejaCouvertParDon(int $besoinId): bool
    {
        $row = $this->db->fetchRow("
            SELECT b.id,
                   b.quantite AS besoin_quantite,
                   COALESCE((
                       SELECT SUM(di.quantite_attribuee) 
                       FROM dispatch di 
                       JOIN don dn ON di.don_id = dn.id 
                       WHERE di.ville_id = b.ville_id AND dn.article_id = b.article_id
                   ), 0) AS quantite_dispatche
            FROM besoin b
            WHERE b.id = ?
        ", [$besoinId]);

        if (!$row) {
            return false;
        }

        return (int)$row['quantite_dispatche'] >= (int)$row['besoin_quantite'];
    }

    /**
     * Données pour la page de récapitulation
     */
    public function getRecapitulation(): array
    {
        // Besoins totaux en montant
        $besoinsTotaux = $this->db->fetchRow("
            SELECT COALESCE(SUM(b.quantite * a.prix_unitaire), 0) AS total
            FROM besoin b
            JOIN article a ON b.article_id = a.id
        ");

        // Besoins satisfaits par dispatch (en montant)
        $besoinsSatisfaitsDispatch = $this->db->fetchRow("
            SELECT COALESCE(SUM(di.quantite_attribuee * a.prix_unitaire), 0) AS total
            FROM dispatch di
            JOIN don d ON di.don_id = d.id
            JOIN article a ON d.article_id = a.id
        ");

        // Besoins satisfaits par achat (en montant)
        $besoinsSatisfaitsAchat = $this->db->fetchRow("
            SELECT COALESCE(SUM(ac.montant), 0) AS total
            FROM achat ac
        ");

        $totalBesoins = (float)($besoinsTotaux['total'] ?? 0);
        $totalSatisfaitsDispatch = (float)($besoinsSatisfaitsDispatch['total'] ?? 0);
        $totalSatisfaitsAchat = (float)($besoinsSatisfaitsAchat['total'] ?? 0);
        $totalSatisfaits = $totalSatisfaitsDispatch + $totalSatisfaitsAchat;
        $totalRestants = max(0, $totalBesoins - $totalSatisfaits);

        return [
            'besoins_totaux' => $totalBesoins,
            'besoins_satisfaits_dispatch' => $totalSatisfaitsDispatch,
            'besoins_satisfaits_achat' => $totalSatisfaitsAchat,
            'besoins_satisfaits' => $totalSatisfaits,
            'besoins_restants' => $totalRestants,
            'pourcentage_satisfait' => $totalBesoins > 0 ? round($totalSatisfaits / $totalBesoins * 100, 1) : 0,
        ];
    }
}
