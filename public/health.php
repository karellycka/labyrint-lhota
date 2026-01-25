<?php
// Simple health check endpoint
header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'environment' => getenv('APP_ENV') ?: 'not set',
    'base_url' => getenv('BASE_URL') ?: 'not set',
    'db_host' => getenv('DB_HOST') ?: 'not set',
    'db_name' => getenv('DB_NAME') ?: 'not set',
    'db_user' => getenv('DB_USER') ?: 'not set',
    'db_pass_set' => !empty(getenv('DB_PASS')) ? 'yes' : 'no',
    'timestamp' => date('Y-m-d H:i:s'),
];

// Test database connection
try {
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT') ?: 3306;
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    if ($dbHost && $dbName && $dbUser) {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $health['database'] = 'connected';
    } else {
        $health['database'] = 'config missing';
    }
} catch (Exception $e) {
    $health['database'] = 'error: ' . $e->getMessage();
}

echo json_encode($health, JSON_PRETTY_PRINT);
