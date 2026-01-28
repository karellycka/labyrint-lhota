<?php
/**
 * MIGRATION 008: Add Highlights Widget
 * Run this file once via browser: http://localhost/run_migration_008.php
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
    
    echo "<h1>Migration 008: Add Highlights Widget</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";
    
    // =====================================================
    // Check if widget type already exists
    // =====================================================
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM widget_types WHERE type_key = ?");
    $stmt->execute(['highlights']);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo "<p style='color: orange;'>⚠️ Widget type 'highlights' already exists. Skipping insert.</p>";
    } else {
        // =====================================================
        // Insert Highlights widget type
        // =====================================================
        $schema = json_encode([
            'fields' => [
                [
                    'key' => 'highlights',
                    'type' => 'repeater',
                    'label' => 'Položky',
                    'translatable' => true,
                    'required' => true,
                    'min' => 1,
                    'max' => 6,
                    'fields' => [
                        [
                            'key' => 'value',
                            'type' => 'text',
                            'label' => 'Hodnota (velké číslo)',
                            'required' => true
                        ],
                        [
                            'key' => 'label',
                            'type' => 'text',
                            'label' => 'Popisek',
                            'required' => true
                        ]
                    ]
                ]
            ]
        ], JSON_UNESCAPED_UNICODE);
        
        $stmt = $pdo->prepare("
            INSERT INTO widget_types (type_key, label, component_path, icon, category, `schema`) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'highlights',
            'Highlights (statistiky)',
            'components/highlights.php',
            '📊',
            'content',
            $schema
        ]);
        
        echo "<p style='color: green;'>✓ Widget type 'highlights' created successfully!</p>";
    }
    
    // =====================================================
    // Done
    // =====================================================
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Migration 008 completed!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='/admin/pages'>Admin → Pages</a></li>";
    echo "<li>Edit a page and go to 'Widgety' tab</li>";
    echo "<li>Click '+ Přidat widget' - you should see 'Highlights (statistiky)' 📊</li>";
    echo "<li><strong style='color: red;'>DELETE this file (run_migration_008.php) for security!</strong></li>";
    echo "</ol>";
    
    // Show example usage
    echo "<h3>Example data for Highlights widget:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>";
    echo htmlspecialchars(json_encode([
        'highlights' => [
            ['value' => '2016', 'label' => 'rok založení školy'],
            ['value' => '90+', 'label' => 'žáků od přípravky po 9. ročník'],
            ['value' => '100%', 'label' => 'spokojenost s výběrem SŠ'],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
    // Preview
    echo "<h3>Preview:</h3>";
    echo "<div style='background: linear-gradient(135deg, #00792E 0%, #005a22 100%); padding: 60px 20px; border-radius: 8px;'>";
    echo "<div style='display: flex; justify-content: space-around; text-align: center;'>";
    $examples = [
        ['2016', 'rok založení školy'],
        ['90+', 'žáků od přípravky po 9. ročník'],
        ['100%', 'spokojenost s výběrem SŠ'],
    ];
    foreach ($examples as $item) {
        echo "<div>";
        echo "<div style='font-size: 48px; font-weight: bold; color: white;'>{$item[0]}</div>";
        echo "<div style='font-size: 16px; color: rgba(255,255,255,0.9);'>{$item[1]}</div>";
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
