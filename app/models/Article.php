<?php

namespace app\models;

class Article extends BaseModel
{
    protected string $table = 'article';

    /**
     * Tous les articles avec leur type de besoin
     */
    public function findAllWithType(): array
    {
        return $this->db->fetchAll("
            SELECT a.*, tb.libelle AS type_besoin
            FROM article a
            JOIN type_besoin tb ON a.type_besoin_id = tb.id
            ORDER BY a.nom
        ");
    }
}
