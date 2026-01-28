<?php

namespace App\Core;

use PDO;
use SessionHandlerInterface;

/**
 * Database-backed session handler for PHP sessions
 */
class DbSessionHandler implements SessionHandlerInterface
{
    private PDO $pdo;
    private int $lifetime;

    public function __construct(PDO $pdo, int $lifetime)
    {
        $this->pdo = $pdo;
        $this->lifetime = $lifetime;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        $minTime = time() - $this->lifetime;
        $stmt = $this->pdo->prepare("
            SELECT data
            FROM sessions
            WHERE id = :id AND last_activity >= :min_time
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $id,
            ':min_time' => $minTime
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row['data'] === null) {
            return '';
        }

        return (string)$row['data'];
    }

    public function write($id, $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO sessions (id, data, last_activity)
            VALUES (:id, :data, :last_activity)
            ON DUPLICATE KEY UPDATE
                data = VALUES(data),
                last_activity = VALUES(last_activity)
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->bindValue(':data', $data, PDO::PARAM_LOB);
        $stmt->bindValue(':last_activity', time(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function destroy($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function gc($maxlifetime): int|false
    {
        $minTime = time() - $maxlifetime;
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_activity < :min_time");
        $stmt->execute([':min_time' => $minTime]);
        return $stmt->rowCount();
    }
}
