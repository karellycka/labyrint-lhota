<?php
// Test widget data loading

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Helpers/functions.php';

use App\Models\PageWidget;
use App\Models\WidgetType;

$pageWidgetModel = new PageWidget();
$widgetTypeModel = new WidgetType();

// Test 1: Load widgets for page 1
echo "=== TEST 1: Load widgets for page 1 ===\n";
$widgets = $pageWidgetModel->getByPage(1, 'cs');
echo "Found " . count($widgets) . " widgets\n";
foreach ($widgets as $widget) {
    echo "- Widget ID: {$widget->id}, Type: {$widget->type_key}, Label: {$widget->widget_label}\n";
}

// Test 2: Load widget types grouped by category
echo "\n=== TEST 2: Load widget types ===\n";
$widgetTypes = $widgetTypeModel->getAllGroupedByCategory();
foreach ($widgetTypes as $category => $types) {
    echo "Category: $category (" . count($types) . " types)\n";
    foreach ($types as $type) {
        echo "  - {$type->type_key}: {$type->label}\n";
    }
}

// Test 3: Check if schemas are valid JSON
echo "\n=== TEST 3: Check schemas ===\n";
$allTypes = $widgetTypeModel->getAllActive();
foreach ($allTypes as $type) {
    $schema = json_decode($type->schema, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "ERROR: Invalid JSON in {$type->type_key}: " . json_last_error_msg() . "\n";
    } else {
        echo "OK: {$type->type_key} - " . count($schema['fields'] ?? []) . " fields\n";
    }
}

echo "\n=== DONE ===\n";
