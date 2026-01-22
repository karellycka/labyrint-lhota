<?php
/**
 * Jednoduchý skript pro vygenerování theme.css z databáze
 * Spustit po import theme_settings nebo z příkazové řádky
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Composer autoloader (if composer install was run)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback: Simple PSR-4 autoloader if composer not available
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
}

use App\Services\ThemeService;

try {
    $themeService = new ThemeService();
    $success = $themeService->forceRegenerate();

    if ($success) {
        echo "✓ Theme CSS úspěšně vygenerován!\n";
        echo "Umístění: " . $themeService->getCSSPath() . "\n";
        echo "Velikost: " . number_format(filesize($themeService->getCSSPath()) / 1024, 2) . " KB\n";
    } else {
        echo "✗ Chyba při generování CSS souboru\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Chyba: " . $e->getMessage() . "\n";
    exit(1);
}
