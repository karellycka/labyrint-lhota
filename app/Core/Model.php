<?php

namespace App\Core;

/**
 * Base Model třída
 * Všechny modely dědí z této třídy
 */
class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?object
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->querySingle($sql, [$id]);
    }

    /**
     * Find all records
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql);
    }

    /**
     * Find records with WHERE condition
     */
    public function where(string $column, mixed $value): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->query($sql, [$value]);
    }

    /**
     * Find single record with WHERE condition
     */
    public function whereFirst(string $column, mixed $value): ?object
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1";
        return $this->db->querySingle($sql, [$value]);
    }

    /**
     * Insert new record
     */
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($data), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->db->execute($sql, array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update record by ID
     */
    public function update(int $id, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE {$this->primaryKey} = ?",
            $this->table,
            implode(', ', $setParts)
        );

        $params = array_values($data);
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /**
     * Delete record by ID
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Count records
     */
    public function count(?string $whereColumn = null, mixed $whereValue = null): int
    {
        if ($whereColumn && $whereValue !== null) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereColumn} = ?";
            $result = $this->db->querySingle($sql, [$whereValue]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $result = $this->db->querySingle($sql);
        }

        return $result ? (int) $result->count : 0;
    }

    /**
     * Execute custom query
     */
    public function query(string $sql, array $params = []): array
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Execute custom query and return single result
     */
    public function querySingle(string $sql, array $params = []): ?object
    {
        return $this->db->querySingle($sql, $params);
    }

    /**
     * Execute custom query (INSERT/UPDATE/DELETE)
     */
    public function execute(string $sql, array $params = []): bool
    {
        return $this->db->execute($sql, $params);
    }
}
