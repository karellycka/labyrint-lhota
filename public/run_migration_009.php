<?php
/**
 * MIGRATION 009: Add Sticky Cards Widget
 * Run this file once via browser: http://localhost:8888/labyrint/run_migration_009.php
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
    
    echo "<h1>Migration 009: Add Sticky Cards Widget</h1>";
    echo "<p>Connected to database: {$dbConfig['database']}</p>";
    
    // =====================================================
    // Check if widget type already exists
    // =====================================================
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM widget_types WHERE type_key = ?");
    $stmt->execute(['sticky_cards']);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo "<p style='color: orange;'>⚠️ Widget type 'sticky_cards' already exists. Skipping insert.</p>";
    } else {
        // =====================================================
        // Color options for cards
        // =====================================================
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
        
        // =====================================================
        // Insert Sticky Cards widget type
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
                    'key' => 'titlePosition',
                    'type' => 'select',
                    'label' => 'Pozice nadpisu',
                    'translatable' => false,
                    'required' => false,
                    'default' => 'right',
                    'options' => [
                        ['value' => 'left', 'label' => 'Vlevo'],
                        ['value' => 'right', 'label' => 'Vpravo']
                    ]
                ],
                [
                    'key' => 'sectionBackgroundColor',
                    'type' => 'select',
                    'label' => 'Barva pozadí sekce',
                    'translatable' => false,
                    'required' => false,
                    'default' => 'var(--color-dark)',
                    'options' => [
                        ['value' => 'var(--color-dark)', 'label' => 'Tmavá'],
                        ['value' => 'var(--color-light)', 'label' => 'Světlá'],
                        ['value' => 'var(--color-primary)', 'label' => 'Zelená'],
                        ['value' => 'transparent', 'label' => 'Průhledná']
                    ]
                ],
                [
                    'key' => 'cards',
                    'type' => 'repeater',
                    'label' => 'Karty',
                    'translatable' => true,
                    'required' => true,
                    'min' => 2,
                    'max' => 6,
                    'fields' => [
                        [
                            'key' => 'title',
                            'type' => 'text',
                            'label' => 'Nadpis karty',
                            'required' => true
                        ],
                        [
                            'key' => 'text',
                            'type' => 'wysiwyg',
                            'label' => 'Text karty',
                            'required' => true
                        ],
                        [
                            'key' => 'backgroundColor',
                            'type' => 'select',
                            'label' => 'Barva pozadí karty',
                            'required' => true,
                            'options' => $colorOptions
                        ],
                        [
                            'key' => 'rotation',
                            'type' => 'select',
                            'label' => 'Rotace karty',
                            'required' => false,
                            'default' => 'none',
                            'options' => [
                                ['value' => 'none', 'label' => 'Žádná'],
                                ['value' => 'slight-left', 'label' => 'Mírně vlevo (-3°)'],
                                ['value' => 'slight-right', 'label' => 'Mírně vpravo (3°)']
                            ]
                        ],
                        [
                            'key' => 'buttonText',
                            'type' => 'text',
                            'label' => 'Text tlačítka (volitelné)',
                            'required' => false
                        ],
                        [
                            'key' => 'buttonUrl',
                            'type' => 'text',
                            'label' => 'URL tlačítka (volitelné)',
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
            'sticky_cards',
            'Sticky Cards (skládací karty)',
            'components/sticky-cards.php',
            '🃏',
            'content',
            $schema
        ]);
        
        echo "<p style='color: green;'>✓ Widget type 'sticky_cards' created successfully!</p>";
    }
    
    // =====================================================
    // Done
    // =====================================================
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Migration 009 completed!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='/labyrint/admin/pages'>Admin → Pages</a></li>";
    echo "<li>Edit a page and go to 'Widgety' tab</li>";
    echo "<li>Click '+ Přidat widget' - you should see 'Sticky Cards (skládací karty)' 🃏</li>";
    echo "<li><strong style='color: red;'>DELETE this file (run_migration_009.php) for security!</strong></li>";
    echo "</ol>";
    
    // Show example usage
    echo "<h3>Example data for Sticky Cards widget:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>";
    echo htmlspecialchars(json_encode([
        'sectionTitle' => 'Co nabízíme',
        'titlePosition' => 'right',
        'sectionBackgroundColor' => 'var(--color-dark)',
        'cards' => [
            [
                'title' => 'Individuální přístup',
                'text' => '<p>Každý žák je pro nás jedinečný...</p>',
                'backgroundColor' => 'var(--color-primary)',
                'rotation' => 'slight-right',
                'buttonText' => 'Více info',
                'buttonUrl' => '/pristup'
            ],
            [
                'title' => 'Moderní výuka',
                'text' => '<p>Využíváme nejnovější metody...</p>',
                'backgroundColor' => 'var(--color-blue)',
                'rotation' => 'none',
                'buttonText' => '',
                'buttonUrl' => ''
            ],
            [
                'title' => 'Bezpečné prostředí',
                'text' => '<p>Škola jako druhý domov...</p>',
                'backgroundColor' => 'var(--color-yellow)',
                'rotation' => 'slight-left',
                'buttonText' => 'Prohlídka',
                'buttonUrl' => '/prohlidka'
            ],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
    // Preview
    echo "<h3>Preview (static mockup):</h3>";
    echo "<div style='background: #1a1a1a; padding: 40px; border-radius: 8px; display: flex; gap: 40px; align-items: flex-start;'>";
    
    // Cards preview
    echo "<div style='flex: 1;'>";
    $previewCards = [
        ['Individuální přístup', '#00792E', '3deg'],
        ['Moderní výuka', '#4A90A4', '0deg'],
        ['Bezpečné prostředí', '#E5C07B', '-3deg'],
    ];
    foreach ($previewCards as $card) {
        echo "<div style='background: {$card[1]}; padding: 24px; border-radius: 12px; margin-bottom: 20px; transform: rotate({$card[2]}); box-shadow: 0 10px 30px rgba(0,0,0,0.3);'>";
        echo "<h3 style='color: white; margin: 0 0 12px 0; font-size: 20px;'>{$card[0]}</h3>";
        echo "<p style='color: rgba(255,255,255,0.9); margin: 0; font-size: 14px;'>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>";
        echo "</div>";
    }
    echo "</div>";
    
    // Title preview
    echo "<div style='flex: 0 0 200px; text-align: center;'>";
    echo "<h2 style='color: white; font-size: 32px; font-weight: bold; margin: 0;'>Co nabízíme</h2>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<p style='margin-top: 20px; color: #666; font-style: italic;'>Note: The real widget has a sticky scroll effect - cards stack on top of each other as you scroll down the page.</p>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: red;'>Database Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
