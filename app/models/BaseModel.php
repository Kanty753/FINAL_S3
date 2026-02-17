<?php

namespace app\models;

use flight\database\PdoWrapper;

abstract class BaseModel
{
    protected PdoWrapper $db;
    protected string $table;

    public function __construct(PdoWrapper $db)
    {
        $this->db = $db;
    }

    /**
     * Récupérer tous les enregistrements
     */
    public function findAll(string $orderBy = 'id ASC'): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    /**
     * Récupérer un enregistrement par son id
     */
    public function findById(int $id): ?array
    {
        $result = $this->db->fetchRow("SELECT * FROM {$this->table} WHERE id = ?", [$id]);

        // The DB wrapper returns a flight\util\Collection instance for rows.
        // Convert it to a plain array to satisfy the ?array return type.
        if ($result instanceof \flight\util\Collection) {
            $result = $result->getData();
        }

        return $result ?: null;
    }

    /**
     * Insérer un enregistrement
     * @param array $data ['colonne' => valeur, ...]
     * @return int L'id de l'enregistrement inséré
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->db->runQuery(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Mettre à jour un enregistrement
     * @param int $id
     * @param array $data ['colonne' => valeur, ...]
     */
    public function update(int $id, array $data): void
    {
        $sets = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        $this->db->runQuery(
            "UPDATE {$this->table} SET {$sets} WHERE id = ?",
            $values
        );
    }

    /**
     * Supprimer un enregistrement
     */
    public function delete(int $id): void
    {
        $this->db->runQuery("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Compter tous les enregistrements
     */
    public function count(): int
    {
        $row = $this->db->fetchRow("SELECT COUNT(*) as total FROM {$this->table}");
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Rechercher par une colonne
     */
    public function findBy(string $column, mixed $value, string $orderBy = 'id ASC'): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy}",
            [$value]
        );
    }
}
