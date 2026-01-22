<?php
/**
 * Test script for image optimization
 * Tests the MediaAdminController's image optimization functionality
 */

// Load framework
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

require_once ROOT_PATH . '/config/bootstrap.php';
require_once ROOT_PATH . '/app/Core/Database.php';
require_once ROOT_PATH . '/app/Core/Model.php';
require_once ROOT_PATH . '/app/Models/Media.php';
require_once ROOT_PATH . '/app/Core/Session.php';
require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Controllers/Admin/MediaAdminController.php';

use App\Core\Session;

Session::start();

echo "<h1>🧪 Test Optimalizace Obrázků</h1>";
echo "<p>Tento test ověřuje funkčnost optimalizace obrázků při nahrávání.</p>";

// Check GD library
echo "<h2>1. GD Library Check</h2>";
if (extension_loaded('gd')) {
    $gdInfo = gd_info();
    echo "✅ GD Library je nainstalována<br>";
    echo "<ul>";
    echo "<li>GD Version: " . $gdInfo['GD Version'] . "</li>";
    echo "<li>JPEG Support: " . ($gdInfo['JPEG Support'] ? '✅' : '❌') . "</li>";
    echo "<li>PNG Support: " . ($gdInfo['PNG Support'] ? '✅' : '❌') . "</li>";
    echo "<li>GIF Support: " . ($gdInfo['GIF Create Support'] ? '✅' : '❌') . "</li>";
    echo "<li>WebP Support: " . ($gdInfo['WebP Support'] ?? false ? '✅' : '❌') . "</li>";
    echo "</ul>";
} else {
    echo "❌ GD Library není nainstalována!<br>";
}

// Check upload directory
echo "<h2>2. Upload Directory Check</h2>";
$uploadDir = PUBLIC_PATH . '/uploads/media/' . date('Y/m');
if (is_dir($uploadDir)) {
    echo "✅ Upload directory existuje: {$uploadDir}<br>";
    echo "Writeable: " . (is_writable($uploadDir) ? '✅ Ano' : '❌ Ne') . "<br>";
} else {
    echo "⚠️ Upload directory neexistuje, bude vytvořen při uploadu<br>";
}

// Check PHP limits
echo "<h2>3. PHP Upload Limits</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "s</li>";
echo "</ul>";

// Check MediaAdminController
echo "<h2>4. MediaAdminController Check</h2>";
if (class_exists('App\Controllers\Admin\MediaAdminController')) {
    echo "✅ MediaAdminController třída existuje<br>";

    // Check if optimizeImage method exists (it's private, but we can check the class)
    $reflection = new ReflectionClass('App\Controllers\Admin\MediaAdminController');
    $methods = $reflection->getMethods();
    $hasOptimize = false;
    foreach ($methods as $method) {
        if ($method->getName() === 'optimizeImage') {
            $hasOptimize = true;
            echo "✅ optimizeImage() metoda existuje (private)<br>";
            break;
        }
    }
    if (!$hasOptimize) {
        echo "❌ optimizeImage() metoda NEEXISTUJE!<br>";
    }

    // Check upload method
    if ($reflection->hasMethod('upload')) {
        echo "✅ upload() metoda existuje<br>";
    }
} else {
    echo "❌ MediaAdminController třída NEEXISTUJE!<br>";
}

// Test upload form
echo "<h2>5. Test Upload</h2>";
echo "<p>Pro otestování optimalizace nahrajte obrázek:</p>";

if (!Session::isLoggedIn()) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
    echo "⚠️ <strong>Nejste přihlášeni!</strong><br>";
    echo "Pro upload je nutné se přihlásit do adminu.<br>";
    echo "<a href='" . BASE_URL . "/admin/login'>Přihlásit se</a>";
    echo "</div>";
} else {
    echo "✅ Jste přihlášeni jako: " . ($_SESSION['user_email'] ?? 'Unknown') . "<br><br>";

    // Generate CSRF token
    $csrfToken = Session::generateCSRFToken();

    echo "<form action='" . BASE_URL . "/admin/media/upload' method='POST' enctype='multipart/form-data' id='test-upload-form'>";
    echo "<input type='hidden' name='csrf_token' value='{$csrfToken}'>";
    echo "<input type='file' name='file' accept='image/*' required>";
    echo "<button type='submit'>📤 Nahrát a optimalizovat</button>";
    echo "</form>";

    echo "<div id='upload-result' style='margin-top: 20px;'></div>";

    // JavaScript for AJAX upload with detailed feedback
    echo "<script>
    document.getElementById('test-upload-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const resultDiv = document.getElementById('upload-result');
        const fileInput = this.querySelector('input[type=file]');
        const file = fileInput.files[0];

        resultDiv.innerHTML = '<p>⏳ Nahrávám a optimalizuji...</p>';

        // Show file info
        const fileSizeKB = (file.size / 1024).toFixed(1);
        resultDiv.innerHTML += '<p><strong>Původní soubor:</strong><br>' +
                              'Název: ' + file.name + '<br>' +
                              'Velikost: ' + fileSizeKB + ' KB<br>' +
                              'Typ: ' + file.type + '</p>';

        fetch('" . BASE_URL . "/admin/media/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                resultDiv.innerHTML += '<div style=\"background: #d4edda; padding: 15px; border-radius: 4px;\">';
                resultDiv.innerHTML += '<strong>✅ Upload a optimalizace úspěšná!</strong><br>';
                resultDiv.innerHTML += 'Media ID: ' + result.media_id + '<br>';
                resultDiv.innerHTML += 'Cesta: ' + result.filename + '<br>';
                resultDiv.innerHTML += '<img src=\"" . BASE_URL . "' + result.filename + '\" style=\"max-width: 400px; margin-top: 10px; border: 1px solid #ddd;\">';
                resultDiv.innerHTML += '</div>';
                resultDiv.innerHTML += '<p><em>💡 Zkontrolujte PHP error log pro detaily optimalizace (original vs optimized size)</em></p>';
            } else {
                resultDiv.innerHTML += '<div style=\"background: #f8d7da; padding: 15px; border-radius: 4px;\">';
                resultDiv.innerHTML += '<strong>❌ Chyba:</strong> ' + (result.error || 'Unknown error');
                resultDiv.innerHTML += '</div>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML += '<div style=\"background: #f8d7da; padding: 15px; border-radius: 4px;\">';
            resultDiv.innerHTML += '<strong>❌ Network Error:</strong> ' + error.message;
            resultDiv.innerHTML += '</div>';
        });
    });
    </script>";
}

echo "<h2>6. Optimalizační Specifikace</h2>";
echo "<ul>";
echo "<li>Max rozměr: <strong>1920px</strong></li>";
echo "<li>JPEG kvalita: <strong>85%</strong></li>";
echo "<li>PNG komprese: <strong>Level 9</strong></li>";
echo "<li>WebP kvalita: <strong>85%</strong></li>";
echo "<li>GIF: Beze změny (zachová animace)</li>";
echo "<li>Transparentnost: Zachována (PNG, GIF)</li>";
echo "</ul>";

echo "<h2>7. Jak sledovat optimalizaci</h2>";
echo "<p>Po nahrání obrázku zkontrolujte PHP error log:</p>";
echo "<code style='background: #f4f4f4; padding: 10px; display: block; margin: 10px 0;'>";
echo "tail -f /Applications/MAMP/logs/php_error.log";
echo "</code>";
echo "<p>Měli byste vidět logy podobné tomuto:</p>";
echo "<code style='background: #f4f4f4; padding: 10px; display: block; margin: 10px 0;'>";
echo "Image optimized: photo.jpg<br>";
echo "&nbsp;&nbsp;Original: 8990.4 KB<br>";
echo "&nbsp;&nbsp;Optimized: 1228.7 KB<br>";
echo "&nbsp;&nbsp;Saved: 7761.7 KB (86.3%)<br>";
echo "&nbsp;&nbsp;Dimensions: 1920x1440";
echo "</code>";

echo "<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    line-height: 1.6;
}
h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
h2 { color: #667eea; margin-top: 30px; }
code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Monaco', 'Courier New', monospace;
}
button {
    background: #667eea;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}
button:hover { background: #5568d3; }
input[type=file] {
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
ul {
    background: #f8f9fa;
    padding: 15px 15px 15px 35px;
    border-left: 3px solid #667eea;
}
</style>";
