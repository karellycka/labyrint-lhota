<?php
/**
 * MIGRATION 011: Add Guides (Průvodci) Widget
 * Run this file once via browser: http://localhost/run_migration_011.php
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
    
    echo "<h1>Migration 011: Add Guides (Průvodci) Widget</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";
    
    // =====================================================
    // Check if widget type already exists
    // =====================================================
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM widget_types WHERE type_key = ?");
    $stmt->execute(['guides']);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo "<p style='color: orange;'>⚠️ Widget type 'guides' already exists. Skipping insert.</p>";
    } else {
        // =====================================================
        // Insert Guides widget type
        // =====================================================
        $schema = json_encode([
            'fields' => [
                [
                    'key' => 'sectionTitle',
                    'type' => 'text',
                    'label' => 'Nadpis sekce',
                    'translatable' => true,
                    'required' => false
                ],
                [
                    'key' => 'sectionSubtitle',
                    'type' => 'text',
                    'label' => 'Podnadpis sekce',
                    'translatable' => true,
                    'required' => false
                ],
                [
                    'key' => 'guides',
                    'type' => 'repeater',
                    'label' => 'Průvodci',
                    'translatable' => true,
                    'required' => true,
                    'min' => 1,
                    'max' => 30,
                    'fields' => [
                        [
                            'key' => 'photo',
                            'type' => 'image',
                            'label' => 'Fotka',
                            'required' => true
                        ],
                        [
                            'key' => 'name',
                            'type' => 'text',
                            'label' => 'Jméno',
                            'required' => true
                        ],
                        [
                            'key' => 'position',
                            'type' => 'text',
                            'label' => 'Pozice',
                            'required' => true
                        ],
                        [
                            'key' => 'quote',
                            'type' => 'textarea',
                            'label' => 'Citát',
                            'required' => false
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
            'guides',
            'Průvodci',
            'components/guides.php',
            '👥',
            'content',
            $schema
        ]);
        
        echo "<p style='color: green;'>✓ Widget type 'guides' created successfully!</p>";
    }
    
    // =====================================================
    // Done
    // =====================================================
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Migration 011 completed!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='/admin/pages'>Admin → Pages</a></li>";
    echo "<li>Edit a page and go to 'Widgety' tab</li>";
    echo "<li>Click '+ Přidat widget' - you should see 'Průvodci' 👥</li>";
    echo "<li><strong style='color: red;'>DELETE this file (run_migration_011.php) for security!</strong></li>";
    echo "</ol>";
    
    // Show example usage
    echo "<h3>Example data for Guides widget:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>";
    echo htmlspecialchars(json_encode([
        'sectionTitle' => 'Náš tým průvodců',
        'sectionSubtitle' => 'Poznáte nás osobně',
        'guides' => [
            [
                'photo' => '/uploads/media/pruvodce1.jpg',
                'name' => 'Jan Novák',
                'position' => 'Třídní průvodce',
                'quote' => 'Vzdělávání je cesta, ne cíl.'
            ],
            [
                'photo' => '/uploads/media/pruvodce2.jpg',
                'name' => 'Marie Svobodová',
                'position' => 'Průvodce matematikou',
                'quote' => 'Matematika je všude kolem nás.'
            ],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
    // Preview
    echo "<h3>Preview:</h3>";
    echo "<div style='background: #F5F1EB; padding: 60px 20px; border-radius: 8px;'>";
    echo "<h2 style='text-align: center; color: #2C323A; margin-bottom: 8px;'>Náš tým průvodců</h2>";
    echo "<h3 style='text-align: center; color: #6B7280; font-weight: normal; margin-bottom: 48px;'>Poznáte nás osobně</h3>";
    echo "<div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; text-align: center;'>";
    
    $examples = [
        ['Jan Novák', 'Třídní průvodce', 'Vzdělávání je cesta, ne cíl.'],
        ['Marie Svobodová', 'Průvodce matematikou', 'Matematika je všude kolem nás.'],
        ['Petr Veselý', 'Průvodce přírodovědou', 'Příroda je nejlepší učitelka.'],
        ['Eva Černá', 'Průvodce češtinou', 'Slova mají moc měnit svět.'],
    ];
    
    foreach ($examples as $item) {
        echo "<div style='display: flex; flex-direction: column; align-items: center;'>";
        echo "<div style='width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #00792E 0%, #005a22 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; margin-bottom: 16px;'>👤</div>";
        echo "<h4 style='margin: 0 0 4px 0; color: #2C323A;'>{$item[0]}</h4>";
        echo "<p style='margin: 0 0 8px 0; color: #00792E; font-weight: 500;'>{$item[1]}</p>";
        echo "<p style='margin: 0; color: #6B7280; font-style: italic; font-size: 14px;'>\"{$item[2]}\"</p>";
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
