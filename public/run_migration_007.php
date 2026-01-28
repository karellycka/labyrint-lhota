<?php
/**
 * MIGRATION 007: Add New Hygge Colors
 * Run this file once via browser: http://localhost/run_migration_007.php
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
    
    echo "<h1>Migration 007: Add New Hygge Colors</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";
    
    // =====================================================
    // 1. Add missing original colors (weren't in production DB)
    // =====================================================
    echo "<h2>1. Adding missing original colors...</h2>";
    
    $missingColors = [
        ['color_green', '#A7C7B1', 'Zelená (šalvějová)', 'Jemná šalvějová zelená'],
        ['color_red_alt', '#D3A08F', 'Červená (koralová)', 'Jemnější korálový odstín červené'],
        ['color_teal', '#9EC4C0', 'Modrozelená', 'Tichá modrozelená v hygge paletě'],
    ];
    
    $stmtInsert = $pdo->prepare("
        INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) 
        VALUES (?, ?, 'colors', 'color', ?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), label = VALUES(label), description = VALUES(description)
    ");
    
    foreach ($missingColors as $color) {
        $stmtInsert->execute($color);
        echo "<p style='color: green;'>✓ {$color[2]} ({$color[0]}: {$color[1]})</p>";
    }
    
    // =====================================================
    // 2. Add new extended hygge colors
    // =====================================================
    echo "<h2>2. Adding new extended hygge colors...</h2>";
    
    $newColors = [
        ['color_moss', '#87A878', 'Zelená (mechová)', 'Teplá mechová/lesní zelená'],
        ['color_fjord_dark', '#6B8FA3', 'Modrá (fjordová tmavá)', 'Hlubší modrozelená jako hluboký fjord'],
        ['color_raspberry', '#C2727E', 'Růžová (malinová)', 'Růžovo-červená malinová'],
        ['color_sage_dark', '#849E8F', 'Zelená (šedozelená)', 'Tlumená šalvějová zelená'],
        ['color_lavender', '#B8A9C4', 'Levandulová', 'Jemná levandulová fialová'],
    ];
    
    foreach ($newColors as $color) {
        $stmtInsert->execute($color);
        echo "<p style='color: green;'>✓ {$color[2]} ({$color[0]}: {$color[1]})</p>";
    }
    
    // =====================================================
    // 3. Update widget schemas with new color options
    // =====================================================
    echo "<h2>3. Updating widget schemas...</h2>";
    
    $stmtUpdate = $pdo->prepare("UPDATE widget_types SET `schema` = ? WHERE type_key = ?");
    
    // Color options array (reusable)
    $colorOptions = [
        ['value' => 'var(--color-brown)', 'label' => 'Písková'],
        ['value' => 'var(--color-yellow)', 'label' => 'Medová'],
        ['value' => 'var(--color-blue)', 'label' => 'Fjordová'],
        ['value' => 'var(--color-teal)', 'label' => 'Modrozelená'],
        ['value' => 'var(--color-red)', 'label' => 'Terakota'],
        ['value' => 'var(--color-red-alt)', 'label' => 'Červená (koralová)'],
        ['value' => 'var(--color-primary)', 'label' => 'Zelená'],
        ['value' => 'var(--color-green)', 'label' => 'Zelená (šalvějová)'],
        ['value' => 'var(--color-moss)', 'label' => 'Zelená (mechová)'],
        ['value' => 'var(--color-fjord-dark)', 'label' => 'Modrá (fjordová tmavá)'],
        ['value' => 'var(--color-raspberry)', 'label' => 'Růžová (malinová)'],
        ['value' => 'var(--color-sage-dark)', 'label' => 'Zelená (šedozelená)'],
        ['value' => 'var(--color-lavender)', 'label' => 'Levandulová'],
    ];
    
    // 3.1 feature_cards_grid
    $schema = json_encode([
        'fields' => [
            ['key' => 'sectionTitle', 'type' => 'text', 'label' => 'Nadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'sectionSubtitle', 'type' => 'text', 'label' => 'Podnadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'cards', 'type' => 'repeater', 'label' => 'Karty', 'translatable' => true, 'required' => true, 'min' => 2, 'max' => 4, 'fields' => [
                ['key' => 'title', 'type' => 'text', 'label' => 'Nadpis karty', 'required' => true],
                ['key' => 'text', 'type' => 'wysiwyg', 'label' => 'Text karty', 'required' => true],
                ['key' => 'backgroundColor', 'type' => 'select', 'label' => 'Barva pozadí', 'required' => true, 'options' => $colorOptions],
            ]],
        ]
    ], JSON_UNESCAPED_UNICODE);
    $stmtUpdate->execute([$schema, 'feature_cards_grid']);
    echo "<p style='color: green;'>✓ feature_cards_grid schema updated</p>";
    
    // 3.2 feature_cards_with_image
    $schema = json_encode([
        'fields' => [
            ['key' => 'sectionTitle', 'type' => 'text', 'label' => 'Nadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'sectionSubtitle', 'type' => 'text', 'label' => 'Podnadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'cards', 'type' => 'repeater', 'label' => 'Karty', 'translatable' => true, 'required' => true, 'min' => 1, 'max' => 3, 'fields' => [
                ['key' => 'image', 'type' => 'image', 'label' => 'Obrázek', 'required' => true],
                ['key' => 'title', 'type' => 'text', 'label' => 'Nadpis', 'required' => true],
                ['key' => 'tags', 'type' => 'repeater', 'label' => 'Štítky', 'required' => false, 'min' => 0, 'max' => 8, 'fields' => [
                    ['key' => 'text', 'type' => 'text', 'label' => 'Text štítku', 'required' => true],
                    ['key' => 'tagColor', 'type' => 'select', 'label' => 'Barva štítku', 'required' => true, 'options' => $colorOptions],
                ]],
                ['key' => 'text', 'type' => 'wysiwyg', 'label' => 'Text', 'required' => true],
                ['key' => 'backgroundColor', 'type' => 'select', 'label' => 'Barva pozadí', 'required' => true, 'options' => $colorOptions],
                ['key' => 'buttonText', 'type' => 'text', 'label' => 'Text tlačítka (volitelné)', 'required' => false],
                ['key' => 'buttonUrl', 'type' => 'text', 'label' => 'URL tlačítka (volitelné)', 'required' => false],
            ]],
        ]
    ], JSON_UNESCAPED_UNICODE);
    $stmtUpdate->execute([$schema, 'feature_cards_with_image']);
    echo "<p style='color: green;'>✓ feature_cards_with_image schema updated</p>";
    
    // 3.3 asymmetric_text_video
    $schema = json_encode([
        'fields' => [
            ['key' => 'text', 'type' => 'wysiwyg', 'label' => 'Text (barevný blok)', 'translatable' => true, 'required' => true],
            ['key' => 'textBackgroundColor', 'type' => 'select', 'label' => 'Barva textového bloku', 'translatable' => false, 'required' => false, 'default' => 'var(--color-primary)', 'options' => $colorOptions],
            ['key' => 'videoId', 'type' => 'text', 'label' => 'YouTube Video ID', 'translatable' => false, 'required' => true],
            ['key' => 'aspectRatio', 'type' => 'select', 'label' => 'Poměr stran videa', 'translatable' => false, 'required' => false, 'default' => '16:9', 'options' => [
                ['value' => '16:9', 'label' => '16:9'],
                ['value' => '4:3', 'label' => '4:3'],
            ]],
        ]
    ], JSON_UNESCAPED_UNICODE);
    $stmtUpdate->execute([$schema, 'asymmetric_text_video']);
    echo "<p style='color: green;'>✓ asymmetric_text_video schema updated</p>";
    
    // 3.4 feature_cards_universal
    $schema = json_encode([
        'fields' => [
            ['key' => 'sectionTitle', 'type' => 'text', 'label' => 'Nadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'sectionSubtitle', 'type' => 'text', 'label' => 'Podnadpis sekce', 'translatable' => true, 'required' => false],
            ['key' => 'columns', 'type' => 'select', 'label' => 'Počet sloupců (desktop)', 'translatable' => false, 'required' => true, 'options' => [
                ['value' => '1', 'label' => '1 sloupec'],
                ['value' => '2', 'label' => '2 sloupce'],
                ['value' => '3', 'label' => '3 sloupce'],
                ['value' => '4', 'label' => '4 sloupce'],
            ]],
            ['key' => 'cards', 'type' => 'repeater', 'label' => 'Karty', 'translatable' => true, 'required' => true, 'min' => 1, 'fields' => [
                ['key' => 'image', 'type' => 'image', 'label' => 'Obrázek (volitelné)', 'required' => false],
                ['key' => 'title', 'type' => 'text', 'label' => 'Nadpis', 'required' => true],
                ['key' => 'subtitle', 'type' => 'text', 'label' => 'Podnadpis (volitelné)', 'required' => false],
                ['key' => 'text', 'type' => 'wysiwyg', 'label' => 'Text', 'required' => true],
                ['key' => 'backgroundColor', 'type' => 'select', 'label' => 'Barva pozadí', 'required' => true, 'options' => $colorOptions],
                ['key' => 'buttonText', 'type' => 'text', 'label' => 'Text tlačítka (volitelné)', 'required' => false],
                ['key' => 'buttonUrl', 'type' => 'text', 'label' => 'URL tlačítka (volitelné)', 'required' => false],
            ]],
        ]
    ], JSON_UNESCAPED_UNICODE);
    $stmtUpdate->execute([$schema, 'feature_cards_universal']);
    echo "<p style='color: green;'>✓ feature_cards_universal schema updated</p>";
    
    // 3.5 image_text (with transparent option first)
    $colorOptionsWithTransparent = array_merge(
        [['value' => 'transparent', 'label' => 'Průhledná (výchozí)']],
        $colorOptions
    );
    
    $schema = json_encode([
        'fields' => [
            ['key' => 'sectionTitle', 'type' => 'text', 'label' => 'Nadpis sekce (H2)', 'translatable' => true, 'required' => false],
            ['key' => 'sectionSubtitle', 'type' => 'text', 'label' => 'Podnadpis sekce (H3)', 'translatable' => true, 'required' => false],
            ['key' => 'imagePosition', 'type' => 'select', 'label' => 'Pozice fotky', 'translatable' => false, 'required' => false, 'default' => 'left', 'options' => [
                ['value' => 'left', 'label' => 'Nalevo'],
                ['value' => 'right', 'label' => 'Napravo'],
            ]],
            ['key' => 'image', 'type' => 'image', 'label' => 'Fotka', 'translatable' => false, 'required' => true],
            ['key' => 'textTitle', 'type' => 'text', 'label' => 'Nadpis textu (H4)', 'translatable' => true, 'required' => false],
            ['key' => 'textContent', 'type' => 'wysiwyg', 'label' => 'Text', 'translatable' => true, 'required' => true],
            ['key' => 'backgroundColor', 'type' => 'select', 'label' => 'Barva pozadí', 'translatable' => false, 'required' => false, 'default' => 'transparent', 'options' => $colorOptionsWithTransparent],
        ]
    ], JSON_UNESCAPED_UNICODE);
    $stmtUpdate->execute([$schema, 'image_text']);
    echo "<p style='color: green;'>✓ image_text schema updated</p>";
    
    // =====================================================
    // Done
    // =====================================================
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Migration 007 completed successfully!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='/admin/theme'>Admin → Theme Settings</a></li>";
    echo "<li>You should see the new colors in the list</li>";
    echo "<li>Click <strong>'Uložit změny a regenerovat CSS'</strong> to regenerate theme.css</li>";
    echo "<li><strong style='color: red;'>DELETE this file (run_migration_007.php) for security!</strong></li>";
    echo "</ol>";
    
    // Show color preview
    echo "<h3>Color Preview:</h3>";
    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
    $allColors = array_merge($missingColors, $newColors);
    foreach ($allColors as $color) {
        echo "<div style='text-align: center;'>";
        echo "<div style='width: 80px; height: 80px; background: {$color[1]}; border-radius: 8px; border: 1px solid #ccc;'></div>";
        echo "<small>{$color[2]}<br>{$color[1]}</small>";
        echo "</div>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
