<?php

namespace App\Core;

/**
 * Base Controller třída
 * Všechny controllery dědí z této třídy
 */
class Controller
{
    protected Database $db;
    protected string $language = 'cs';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Render view
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extract data to variables
        extract($data);

        // Add language to data
        $currentLanguage = $this->language;

        // Start output buffering
        ob_start();

        // Load view file
        $viewFile = __DIR__ . "/../Views/{$view}.php";

        if (!file_exists($viewFile)) {
            die("View {$view} not found");
        }

        require $viewFile;

        // Get view content
        $content = ob_get_clean();

        // Load layout if specified
        if ($layout) {
            $layoutFile = __DIR__ . "/../Views/layouts/{$layout}.php";

            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, ?string $message = null): void
    {
        if ($message) {
            $_SESSION['flash_message'] = $message;
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Return error response
     */
    protected function error(string $message, int $statusCode = 400): void
    {
        http_response_code($statusCode);

        // Check if it's an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['error' => $message], $statusCode);
        } else {
            $this->view('errors/error', ['message' => $message], 'main');
        }

        exit;
    }

    /**
     * Set current language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }
}
