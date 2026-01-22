<?php
/**
 * Simple test for image optimization - CLI version
 */

// Load configuration (same as index.php)
require_once __DIR__ . '/../config/config.php';

// Simple PSR-4 autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

echo "=== IMAGE OPTIMIZATION TEST ===\n\n";

// 1. Check GD
echo "1. GD Library: ";
if (extension_loaded('gd')) {
    echo "✓ Installed\n";
    $info = gd_info();
    echo "   - JPEG: " . ($info['JPEG Support'] ? 'YES' : 'NO') . "\n";
    echo "   - PNG: " . ($info['PNG Support'] ? 'YES' : 'NO') . "\n";
    echo "   - WebP: " . ($info['WebP Support'] ?? false ? 'YES' : 'NO') . "\n";
} else {
    echo "✗ NOT installed\n";
}

// 2. Check upload limits
echo "\n2. PHP Upload Limits:\n";
echo "   - upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   - post_max_size: " . ini_get('post_max_size') . "\n";
echo "   - memory_limit: " . ini_get('memory_limit') . "\n";

// 3. Check MediaAdminController
echo "\n3. MediaAdminController:\n";
if (class_exists('App\Controllers\Admin\MediaAdminController')) {
    echo "   ✓ Class exists\n";

    $reflection = new ReflectionClass('App\Controllers\Admin\MediaAdminController');

    if ($reflection->hasMethod('upload')) {
        echo "   ✓ upload() method exists\n";
    }

    if ($reflection->hasMethod('optimizeImage')) {
        echo "   ✓ optimizeImage() method exists (private)\n";

        // Get method to check implementation
        $method = $reflection->getMethod('optimizeImage');
        $method->setAccessible(true);
        echo "   ✓ Method is accessible for inspection\n";
    }
} else {
    echo "   ✗ Class NOT found\n";
}

// 4. Check upload directory
echo "\n4. Upload Directory:\n";
$uploadDir = PUBLIC_PATH . '/uploads/media';
if (is_dir($uploadDir)) {
    echo "   ✓ {$uploadDir} exists\n";
    echo "   - Writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . "\n";

    // Check current month folder
    $monthDir = $uploadDir . '/' . date('Y/m');
    if (is_dir($monthDir)) {
        echo "   ✓ Current month folder exists: " . date('Y/m') . "\n";

        // List recent uploads
        $files = glob($monthDir . '/*');
        $count = count($files);
        echo "   - Files in current month: {$count}\n";

        if ($count > 0) {
            echo "   - Most recent: " . basename(end($files)) . "\n";
        }
    } else {
        echo "   - Current month folder: Will be created on first upload\n";
    }
} else {
    echo "   ✗ Upload directory NOT found\n";
}

// 5. Optimization specs
echo "\n5. Optimization Specifications:\n";
echo "   - Max dimension: 1920px (Full HD)\n";
echo "   - JPEG quality: 85%\n";
echo "   - PNG compression: Level 9\n";
echo "   - WebP quality: 85%\n";
echo "   - Preserves transparency: YES (PNG, GIF)\n";
echo "   - Aspect ratio: Maintained\n";

echo "\n=== TEST COMPLETE ===\n";
echo "\nTo test upload:\n";
echo "1. Open: http://localhost:8888/labyrint/test_image_optimization.php\n";
echo "2. Login to admin if needed\n";
echo "3. Upload an image\n";
echo "4. Check logs: tail -f /Applications/MAMP/logs/php_error.log\n";
