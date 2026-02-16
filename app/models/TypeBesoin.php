<?php

namespace app\models;

class TypeBesoin extends BaseModel
{
    protected string $table = 'type_besoin';

    /**
     * Tous les types triés par libellé
     */
    public function findAll(string $orderBy = 'libelle ASC'): array
    {
        return parent::findAll($orderBy);
    }
}
