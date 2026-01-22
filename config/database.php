<?php

/**
 * Databázová konfigurace
 * Pro lokální přepsání použijte config.local.php
 */

// Check for local config overrides
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

return [
    'host' => $localConfig['DB_HOST'] ?? $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $localConfig['DB_PORT'] ?? $_ENV['DB_PORT'] ?? 8889,
    'database' => $localConfig['DB_NAME'] ?? $_ENV['DB_NAME'] ?? 'labyrint',
    'username' => $localConfig['DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
    'password' => $localConfig['DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
