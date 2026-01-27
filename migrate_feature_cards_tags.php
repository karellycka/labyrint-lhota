<?php
/**
 * Migration: feature_cards_with_image – subtitle → tags
 * 1. Updates widget_types schema (subtitle replaced by tags repeater).
 * 2. Converts existing page_widget_translations: cards[].subtitle → cards[].tags.
 */

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'labyrint';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to database.\n";

    // 1. Run schema migration (widget_types) using bound params
    $sql = file_get_contents(__DIR__ . '/database/migrations/005_feature_cards_with_image_tags.sql');
    if (!$sql) {
        throw new Exception('Failed to read 005_feature_cards_with_image_tags.sql');
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
    echo "✓ Widget types schema updated.\n";

    // 2. Migrate translations: subtitle → tags
    $stmt = $pdo->query("
        SELECT pw.id, pwt.language, pwt.settings
        FROM page_widgets pw
        JOIN page_widget_translations pwt ON pwt.page_widget_id = pw.id
        WHERE pw.widget_type_key = 'feature_cards_with_image'
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        echo "✓ Migrated translations for widget {$row['id']} ({$row['language']}).\n";
    }

    echo "\n✅ Migration completed.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
