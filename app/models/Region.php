<?php

namespace app\models;

class Region extends BaseModel
{
    protected string $table = 'region';

    /**
     * Toutes les régions triées par nom
     */
    public function findAll(string $orderBy = 'nom ASC'): array
    {
        return parent::findAll($orderBy);
    }
}
