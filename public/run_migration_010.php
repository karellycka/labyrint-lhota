<?php
/**
 * MIGRATION 010: Add DB sessions table
 * Run this file once via browser and then DELETE it.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load database config
$dbConfig = require __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<h1>Migration 010: Add DB sessions table</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `sessions` (
            `id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `data` MEDIUMBLOB NOT NULL,
            `last_activity` INT UNSIGNED NOT NULL,
            INDEX `idx_last_activity` (`last_activity`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "<p style='color: green;'>✓ Sessions table is ready.</p>";
    echo "<p><strong style='color: red;'>DELETE this file (run_migration_010.php) for security!</strong></p>";
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
