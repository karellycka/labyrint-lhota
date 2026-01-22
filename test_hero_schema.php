<?php
require_once __DIR__ . '/config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

$widgetTypeModel = new App\Models\WidgetType();
$hero = $widgetTypeModel->getByKey('hero');

echo "=== HERO WIDGET SCHEMA ===\n\n";
echo "Type Key: " . $hero->type_key . "\n";
echo "Label: " . $hero->label . "\n";
echo "Category: " . $hero->category . "\n\n";
echo "Schema:\n";
echo json_encode(json_decode($hero->schema), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
