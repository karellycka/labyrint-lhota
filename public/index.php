<?php

/**
 * Front Controller - Entry point for all requests
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

// Load helper functions
require_once __DIR__ . '/../app/Helpers/functions.php';
require_once __DIR__ . '/../app/Helpers/widgets.php';

// Initialize core components
use App\Core\Router;
use App\Core\Session;

// Start secure session
Session::start();

// Create router instance
$router = new Router();

// Load routes
$routeLoader = require __DIR__ . '/../config/routes.php';
$routeLoader($router);

// Handle root redirect to language
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path for subdirectory installations
// For production (root domain): use empty string
// For localhost subdirectory: use '/labyrint'
$basePath = defined('BASE_PATH') ? BASE_PATH : (ENVIRONMENT === 'production' ? '' : '/labyrint');
if (!empty($basePath) && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Ensure URI starts with /
if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// Redirect /cs/admin or /en/admin to /admin (admin has no language prefix)
if (preg_match('#^/(cs|en)/admin#', $requestUri, $matches)) {
    $adminPath = preg_replace('#^/(cs|en)#', '', $requestUri);
    $redirectUrl = !empty($basePath) ? "{$basePath}{$adminPath}" : $adminPath;
    header("Location: {$redirectUrl}", true, 302);
    exit;
}

// Handle admin routes manually (no language prefix, different structure than Router expects)
if (strpos($requestUri, '/admin') === 0) {
    $_SESSION['language'] = 'cs';

    $method = $_SERVER['REQUEST_METHOD'];

    // Dashboard
    if ($requestUri === '/admin' || $requestUri === '/admin/') {
        $controller = new \App\Controllers\Admin\DashboardController();
        $controller->index();
        exit;
    }

    // Auth - Login
    if ($requestUri === '/admin/login') {
        $controller = new \App\Controllers\Admin\AuthController();
        if ($method === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        exit;
    }

    // Auth - Logout
    if ($requestUri === '/admin/logout') {
        $controller = new \App\Controllers\Admin\AuthController();
        $controller->logout();
        exit;
    }

    // Widget API - GET schema
    if (preg_match('#^/admin/widgets/schema/([a-z_]+)$#', $requestUri, $matches)) {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->getSchema($matches[1]);
        exit;
    }

    // Widget API - GET widget data
    if (preg_match('#^/admin/widgets/(\d+)$#', $requestUri, $matches) && $method === 'GET') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->getWidget((int)$matches[1]);
        exit;
    }

    // Widget API - CREATE widget
    if (preg_match('#^/admin/pages/(\d+)/widgets/create$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->create((int)$matches[1]);
        exit;
    }

    // Widget API - UPDATE widget
    if (preg_match('#^/admin/widgets/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->update((int)$matches[1]);
        exit;
    }

    // Widget API - DELETE widget
    if (preg_match('#^/admin/widgets/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->delete((int)$matches[1]);
        exit;
    }

    // Widget API - MOVE UP
    if (preg_match('#^/admin/widgets/(\d+)/move-up$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->moveUp((int)$matches[1]);
        exit;
    }

    // Widget API - MOVE DOWN
    if (preg_match('#^/admin/widgets/(\d+)/move-down$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->moveDown((int)$matches[1]);
        exit;
    }

    // Widget API - REORDER widgets
    if (preg_match('#^/admin/pages/(\d+)/widgets/reorder$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\WidgetAdminController();
        $controller->reorder((int)$matches[1]);
        exit;
    }

    // Pages - Index
    if ($requestUri === '/admin/pages') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->index();
        exit;
    }

    // Pages - Create (form)
    if ($requestUri === '/admin/pages/create') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->create();
        exit;
    }

    // Pages - Store (submit)
    if ($requestUri === '/admin/pages/store' && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->store();
        exit;
    }

    // Pages - Edit (form)
    if (preg_match('#^/admin/pages/(\d+)/edit$#', $requestUri, $matches)) {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->edit((int)$matches[1]);
        exit;
    }

    // Pages - Update (submit)
    if (preg_match('#^/admin/pages/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->update((int)$matches[1]);
        exit;
    }

    // Pages - Delete
    if (preg_match('#^/admin/pages/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\PageAdminController();
        $controller->delete((int)$matches[1]);
        exit;
    }

    // Blog - Index
    if ($requestUri === '/admin/blog') {
        $controller = new \App\Controllers\Admin\BlogAdminController();
        $controller->index();
        exit;
    }

    // Events - Index
    if ($requestUri === '/admin/events') {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->index();
        exit;
    }

    // Events - Create (form)
    if ($requestUri === '/admin/events/create') {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->create();
        exit;
    }

    // Events - Store (submit)
    if ($requestUri === '/admin/events/store' && $method === 'POST') {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->store();
        exit;
    }

    // Events - Edit (form)
    if (preg_match('#^/admin/events/(\d+)/edit$#', $requestUri, $matches)) {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->edit((int)$matches[1]);
        exit;
    }

    // Events - Update (submit)
    if (preg_match('#^/admin/events/(\d+)/update$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->update((int)$matches[1]);
        exit;
    }

    // Events - Delete
    if (preg_match('#^/admin/events/(\d+)/delete$#', $requestUri, $matches) && $method === 'POST') {
        $controller = new \App\Controllers\Admin\EventAdminController();
        $controller->delete((int)$matches[1]);
        exit;
    }

    // Media - API Get All (JSON)
    if ($requestUri === '/admin/media/api/all' && $method === 'GET') {
        $controller = new \App\Controllers\Admin\MediaAdminController();
        $controller->getAll();
        exit;
    }

    // Debug: Log request URI if it starts with /admin/media
    if (strpos($requestUri, '/admin/media') === 0 && ENVIRONMENT === 'development') {
        error_log("DEBUG: Media route request - URI: {$requestUri}, Method: {$method}");
    }

    // Media - Upload
    if ($requestUri === '/admin/media/upload' && $method === 'POST') {
        $controller = new \App\Controllers\Admin\MediaAdminController();
        $controller->upload();
        exit;
    }

    // Media - Index
    if ($requestUri === '/admin/media') {
        $controller = new \App\Controllers\Admin\MediaAdminController();
        $controller->index();
        exit;
    }

    // For any other /admin/* routes that aren't handled above, show 404
    http_response_code(404);
    if ($method === 'GET') {
        echo '<h1>404 - Admin Page Not Found</h1>';
        echo '<p>Route: ' . htmlspecialchars($requestUri) . '</p>';
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found', 'route' => $requestUri]);
    }
    exit;
}

if ($requestUri === '/' || $requestUri === '') {
    // Detect browser language or use default
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
    $language = in_array($browserLang, SUPPORTED_LANGUAGES) ? $browserLang : DEFAULT_LANGUAGE;

    // Store language in session
    $_SESSION['language'] = $language;

    $redirectUrl = !empty($basePath) ? "{$basePath}/{$language}/" : "/{$language}/";
    header("Location: {$redirectUrl}", true, 302);
    exit;
}

// Store current language in session
$currentLanguage = $router->detectLanguage($requestUri);
$_SESSION['language'] = $currentLanguage;

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    // Handle exceptions
    if (ENVIRONMENT === 'development') {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        // Log error
        error_log('Application error: ' . $e->getMessage());

        // Show generic error page
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
}
