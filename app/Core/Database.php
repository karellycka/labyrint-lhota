<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database třída - Singleton pro práci s databází
 * Používá PDO pro bezpečné dotazy
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    /**
     * Private constructor - Singleton pattern
     */
    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        // Build DSN with port if specified
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'] ?? 3306,
            $config['database']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $options
            );
        } catch (PDOException $e) {
            // Log error
            error_log('Database connection failed: ' . $e->getMessage());

            // Show friendly error based on environment
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
                die('Database connection error. Please try again later.');
            } else {
                die('Database error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get instance of Database (Singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO instance
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute query and return all results
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute query and return single result
     */
    public function querySingle(string $sql, array $params = []): ?object
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Execute INSERT/UPDATE/DELETE query
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
