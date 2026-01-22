<?php

namespace App\Core;

/**
 * Router třída - zpracování URL a routing
 */
class Router
{
    private array $routes = [];
    private array $supportedLanguages = ['cs', 'en'];
    private string $defaultLanguage = 'cs';
    private string $currentLanguage;

    public function __construct()
    {
        $this->currentLanguage = $this->defaultLanguage;
    }

    /**
     * Register GET route
     */
    public function get(string $pattern, string $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    /**
     * Register POST route
     */
    public function post(string $pattern, string $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    /**
     * Add route to routes array
     */
    private function addRoute(string $method, string $pattern, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Detect language from URI
     */
    public function detectLanguage(string $uri): string
    {
        $segments = explode('/', trim($uri, '/'));

        if (isset($segments[0]) && in_array($segments[0], $this->supportedLanguages)) {
            return $segments[0];
        }

        return $this->defaultLanguage;
    }

    /**
     * Remove language prefix from URI
     */
    public function removeLanguageFromUri(string $uri): string
    {
        $language = $this->detectLanguage($uri);
        return preg_replace("#^/$language#", '', $uri);
    }

    /**
     * Dispatch route
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Get URI - basePath is already removed in index.php
        // We receive clean URI like: /cs/, /admin, /cs/blog, etc.
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove base path if present (for subdirectory installations)
        $basePath = '/labyrint';
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // Ensure URI starts with /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }

        // Remove trailing slash (except for root)
        if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
            $requestUri = rtrim($requestUri, '/');
        }

        // Detect language
        $this->currentLanguage = $this->detectLanguage($requestUri);

        // Remove language from URI for matching
        $cleanUri = $this->removeLanguageFromUri($requestUri);
        if (empty($cleanUri) || $cleanUri === '') {
            $cleanUri = '/';
        }

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertPatternToRegex($route['pattern']);

            if (preg_match($pattern, $cleanUri, $matches)) {
                array_shift($matches); // Remove full match
                $this->callHandler($route['handler'], $matches);
                return;
            }
        }

        // No route found - 404
        $this->handle404();
    }

    /**
     * Convert route pattern to regex
     */
    private function convertPatternToRegex(string $pattern): string
    {
        // Replace :param with ([^/]+)
        $pattern = preg_replace('#:([a-zA-Z0-9_]+)#', '([^/]+)', $pattern);

        // Escape forward slashes
        $pattern = str_replace('/', '\/', $pattern);

        return "#^{$pattern}$#";
    }

    /**
     * Call route handler
     */
    private function callHandler(string $handler, array $params): void
    {
        // Parse handler (Controller@method)
        [$controller, $method] = explode('@', $handler);

        // Convert forward slashes to backslashes for namespaces
        // Example: Admin/BlogAdminController -> Admin\BlogAdminController
        $controller = str_replace('/', '\\', $controller);

        // Build full controller class name
        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            die("Controller {$controllerClass} not found");
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $method)) {
            die("Method {$method} not found in {$controllerClass}");
        }

        // Add language as first parameter ONLY for non-admin controllers
        // Admin controllers don't have language parameter
        if (strpos($controller, 'Admin\\') !== 0) {
            array_unshift($params, $this->currentLanguage);
        }

        // Call controller method
        call_user_func_array([$controllerInstance, $method], $params);
    }

    /**
     * Handle 404 error
     */
    private function handle404(): void
    {
        http_response_code(404);

        if (file_exists(__DIR__ . '/../../app/Views/errors/404.php')) {
            require __DIR__ . '/../../app/Views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }

        exit;
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage(): string
    {
        return $this->currentLanguage;
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }
}
