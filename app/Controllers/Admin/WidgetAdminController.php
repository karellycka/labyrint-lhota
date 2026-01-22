<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\PageWidget;
use App\Models\WidgetType;

/**
 * Widget Admin Controller - AJAX operations for page widgets
 */
class WidgetAdminController extends Controller
{
    private PageWidget $pageWidgetModel;
    private WidgetType $widgetTypeModel;

    public function __construct()
    {
        parent::__construct();

        // Require authentication
        if (!Session::isLoggedIn()) {
            http_response_code(401);

            // Debug info in development
            $debugInfo = ['error' => 'Unauthorized'];
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                $debugInfo['debug'] = [
                    'session_id' => session_id(),
                    'session_status' => session_status(),
                    'has_user_id' => isset($_SESSION['user_id']),
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'session_keys' => array_keys($_SESSION ?? [])
                ];
            }

            echo json_encode($debugInfo);
            exit;
        }

        $this->pageWidgetModel = new PageWidget();
        $this->widgetTypeModel = new WidgetType();
    }

    /**
     * Get widget schema (for form generation)
     */
    public function getSchema(string $typeKey): void
    {
        $schema = $this->widgetTypeModel->getSchema($typeKey);

        if (!$schema) {
            http_response_code(404);
            echo json_encode(['error' => 'Widget type not found']);
            return;
        }

        echo json_encode($schema);
    }

    /**
     * Get widget data (for editing)
     */
    public function getWidget(int $widgetId): void
    {
        $widget = $this->pageWidgetModel->getWithTranslations($widgetId);

        if (!$widget) {
            http_response_code(404);
            echo json_encode(['error' => 'Widget not found']);
            return;
        }

        echo json_encode($widget);
    }

    /**
     * Create new widget
     */
    public function create(int $pageId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $widgetTypeKey = $_POST['widget_type_key'] ?? '';
        $translationsData = json_decode($_POST['translations'] ?? '{}', true);

        if (empty($widgetTypeKey) || empty($translationsData)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        try {
            $widgetId = $this->pageWidgetModel->createWithTranslations(
                $pageId,
                $widgetTypeKey,
                $translationsData
            );

            echo json_encode([
                'success' => true,
                'widget_id' => $widgetId
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error creating widget: ' . $e->getMessage()]);
        }
    }

    /**
     * Update widget
     */
    public function update(int $widgetId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $translationsData = json_decode($_POST['translations'] ?? '{}', true);

        if (empty($translationsData)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing translations data']);
            return;
        }

        try {
            $success = $this->pageWidgetModel->updateTranslations($widgetId, $translationsData);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update widget']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error updating widget: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete widget
     */
    public function delete(int $widgetId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        try {
            $this->pageWidgetModel->delete($widgetId);
            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error deleting widget: ' . $e->getMessage()]);
        }
    }

    /**
     * Move widget up
     */
    public function moveUp(int $widgetId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        try {
            $success = $this->pageWidgetModel->moveUp($widgetId);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Cannot move widget up']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error moving widget: ' . $e->getMessage()]);
        }
    }

    /**
     * Move widget down
     */
    public function moveDown(int $widgetId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        try {
            $success = $this->pageWidgetModel->moveDown($widgetId);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Cannot move widget down']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error moving widget: ' . $e->getMessage()]);
        }
    }

    /**
     * Reorder all widgets
     */
    public function reorder(int $pageId): void
    {
        // Validate CSRF
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $widgetIds = json_decode($_POST['widget_ids'] ?? '[]', true);

        if (empty($widgetIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing widget IDs']);
            return;
        }

        try {
            $success = $this->pageWidgetModel->reorder($pageId, $widgetIds);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to reorder widgets']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error reordering widgets: ' . $e->getMessage()]);
        }
    }
}
