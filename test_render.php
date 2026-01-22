<?php
// Test rendering admin page edit

require_once __DIR__ . '/config/config.php';

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require $file;
});

// Load helpers
require_once __DIR__ . '/app/Helpers/functions.php';

// Start session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';

// Create controller instance
$controller = new \App\Controllers\Admin\PageAdminController();

// Get reflection to call protected method
$reflection = new ReflectionClass($controller);
$viewMethod = $reflection->getMethod('view');
$viewMethod->setAccessible(true);

// Load page data
$pageModel = new \App\Models\Page();
$page = $pageModel->find(1);

$pageWidgetModel = new \App\Models\PageWidget();
$widgets = $pageWidgetModel->getByPage(1, 'cs');

$widgetTypeModel = new \App\Models\WidgetType();
$widgetTypes = $widgetTypeModel->getAllGroupedByCategory();

$translations = $pageModel->query("
    SELECT language, title, content
    FROM page_translations
    WHERE page_id = ?
", [1]);

$translationsByLang = [];
foreach ($translations as $trans) {
    $translationsByLang[$trans->language] = $trans;
}

// Try to render
echo "=== RENDERING TEST ===\n";
echo "Page ID: " . $page->id . "\n";
echo "Widgets count: " . count($widgets) . "\n";
echo "Widget types count: " . count($widgetTypes) . "\n";

// Extract data like view() method does
extract([
    'title' => 'Edit Page',
    'page' => $page,
    'translations' => $translationsByLang,
    'widgets' => $widgets,
    'widgetTypes' => $widgetTypes
]);

echo "\n=== LAYOUT FILE ===\n";
$layoutFile = __DIR__ . '/app/Views/layouts/admin.php';
echo "Layout exists: " . (file_exists($layoutFile) ? 'YES' : 'NO') . "\n";

echo "\n=== VIEW FILE ===\n";
$viewFile = __DIR__ . '/app/Views/admin/pages/edit.php';
echo "View exists: " . (file_exists($viewFile) ? 'YES' : 'NO') . "\n";

echo "\n=== WIDGETS TAB FILE ===\n";
$widgetsTabFile = __DIR__ . '/app/Views/admin/pages/widgets-tab.php';
echo "Widgets tab exists: " . (file_exists($widgetsTabFile) ? 'YES' : 'NO') . "\n";

// Test rendering view
ob_start();
include $viewFile;
$viewContent = ob_get_clean();

echo "\n=== VIEW OUTPUT LENGTH ===\n";
echo strlen($viewContent) . " characters\n";

echo "\n=== CHECKING FOR CRITICAL ELEMENTS ===\n";
echo "Has <style> tag: " . (strpos($viewContent, '<style>') !== false ? 'YES' : 'NO') . "\n";
echo "Has <script> tag: " . (strpos($viewContent, '<script>') !== false ? 'YES' : 'NO') . "\n";
echo "Has widget-manager: " . (strpos($viewContent, 'widget-manager') !== false ? 'YES' : 'NO') . "\n";
echo "Has add-widget-btn: " . (strpos($viewContent, 'add-widget-btn') !== false ? 'YES' : 'NO') . "\n";

// Now render with layout
ob_start();
$content = $viewContent;
include $layoutFile;
$fullOutput = ob_get_clean();

echo "\n=== FULL OUTPUT LENGTH ===\n";
echo strlen($fullOutput) . " characters\n";

echo "\n=== CHECKING LAYOUT ELEMENTS ===\n";
echo "Has <!DOCTYPE html>: " . (strpos($fullOutput, '<!DOCTYPE html>') !== false ? 'YES' : 'NO') . "\n";
echo "Has admin.css link: " . (strpos($fullOutput, 'admin.css') !== false ? 'YES' : 'NO') . "\n";
echo "Has admin.js script: " . (strpos($fullOutput, 'admin.js') !== false ? 'YES' : 'NO') . "\n";

echo "\n=== CSS LINK IN OUTPUT ===\n";
if (preg_match('/<link[^>]*admin\.css[^>]*>/', $fullOutput, $matches)) {
    echo $matches[0] . "\n";
} else {
    echo "NOT FOUND\n";
}

echo "\n=== FIRST 500 CHARS OF OUTPUT ===\n";
echo substr($fullOutput, 0, 500) . "\n...";
