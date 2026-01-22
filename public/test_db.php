<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

echo "<h1>Database Connection Test</h1>";

echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
echo "<p><strong>ENVIRONMENT:</strong> " . ENVIRONMENT . "</p>";

try {
    $db = \App\Core\Database::getInstance();
    echo "<p style='color: green;'><strong>✓ Database connected successfully!</strong></p>";

    // Test query
    $result = $db->querySingle("SELECT COUNT(*) as count FROM pages");
    echo "<p><strong>Pages in database:</strong> " . $result->count . "</p>";

    $result = $db->querySingle("SELECT COUNT(*) as count FROM page_widgets WHERE page_id = 1");
    echo "<p><strong>Widgets on page 1:</strong> " . $result->count . "</p>";

    $result = $db->querySingle("SELECT * FROM page_widgets WHERE id = 2");
    echo "<p><strong>Hero widget (ID 2):</strong></p>";
    echo "<pre>" . print_r($result, true) . "</pre>";

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Database error:</strong> " . $e->getMessage() . "</p>";
}

// Load database config
$dbConfig = require __DIR__ . '/../config/database.php';
echo "<h2>Database Config:</h2>";
echo "<pre>" . print_r($dbConfig, true) . "</pre>";
