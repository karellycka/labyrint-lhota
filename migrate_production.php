<?php
/**
 * Production Migration Script
 * Run migration 003_add_slug_en_to_pages.sql
 */

// Load database configuration from environment
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'railway';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Connected to database successfully.\n";

    // Read migration file
    $migrationFile = __DIR__ . '/database/migrations/003_add_slug_en_to_pages.sql';
    $sql = file_get_contents($migrationFile);

    if (!$sql) {
        throw new Exception("Failed to read migration file: {$migrationFile}");
    }

    echo "Running migration: 003_add_slug_en_to_pages.sql\n";

    // Split SQL into individual statements (by semicolon)
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            // Remove comments and empty statements
            $stmt = preg_replace('/--.*$/m', '', $stmt);
            $stmt = trim($stmt);
            return !empty($stmt);
        }
    );

    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo "✓ Executed statement\n";
        }
    }

    echo "\n✅ Migration completed successfully!\n";

} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
