<?php
/**
 * TEMPORARY MIGRATION RUNNER
 * Run this file once via browser: http://localhost/run_migration_005.php
 * Then DELETE this file for security!
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
    
    echo "<h1>Migration 005: Feature Cards Tags</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";
    
    // Run schema migration (safe parameter binding)
    $sql = file_get_contents(__DIR__ . '/../database/migrations/005_feature_cards_with_image_tags.sql');
    if (!$sql) {
        throw new Exception('Failed to read migration file');
    }
    $schema = null;
    if (preg_match("/`schema`\\s*=\\s*'(.*)'\\s*WHERE/s", $sql, $matches)) {
        $schema = $matches[1];
    }
    if (!$schema) {
        throw new Exception('Failed to extract schema JSON from migration file');
    }
    $stmtSchema = $pdo->prepare("UPDATE `widget_types` SET `schema` = ? WHERE `type_key` = ?");
    $stmtSchema->execute([$schema, 'feature_cards_with_image']);
    echo "<p style='color: green;'>✓ Widget schema updated successfully!</p>";
    
    // Check if any existing translations need migration
    $stmt = $pdo->query("
        SELECT pw.id, pwt.language, pwt.settings
        FROM page_widgets pw
        JOIN page_widget_translations pwt ON pwt.page_widget_id = pw.id
        WHERE pw.widget_type_key = 'feature_cards_with_image'
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        echo "<p>No existing feature_cards_with_image widgets found.</p>";
    } else {
        echo "<h2>Migrating existing translations (subtitle → tags):</h2>";
        $colorDefault = 'var(--color-brown)';
        
        foreach ($rows as $row) {
            $settings = json_decode($row['settings'], true);
            if (!is_array($settings) || empty($settings['cards'])) {
                continue;
            }
            $changed = false;
            foreach ($settings['cards'] as &$card) {
                if (!isset($card['subtitle'])) {
                    continue;
                }
                $text = $card['subtitle'];
                $tagColor = $card['backgroundColor'] ?? $colorDefault;
                $card['tags'] = [['text' => $text, 'tagColor' => $tagColor]];
                unset($card['subtitle']);
                $changed = true;
            }
            unset($card);
            
            if (!$changed) {
                continue;
            }
            
            $newSettings = json_encode($settings, JSON_UNESCAPED_UNICODE);
            $up = $pdo->prepare('UPDATE page_widget_translations SET settings = ? WHERE page_widget_id = ? AND language = ?');
            $up->execute([$newSettings, $row['id'], $row['language']]);
            echo "<p style='color: green;'>✓ Migrated widget {$row['id']} ({$row['language']})</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Migration completed successfully!</h2>";
    echo "<p><strong>IMPORTANT:</strong> Delete this file (run_migration_005.php) now for security!</p>";
    echo "<p>Reload your admin page and you should see 'Štítky' field in the widget.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Migration failed</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
