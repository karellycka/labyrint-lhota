<?php

namespace App\Services;

/**
 * WidgetRenderer Service
 * Renders widgets from database data
 */
class WidgetRenderer
{
    /**
     * Render a single widget
     */
    public function render(object $widget): string
    {
        $componentPath = __DIR__ . '/../Views/' . $widget->component_path;

        // Check if component file exists
        if (!file_exists($componentPath)) {
            error_log("Widget component not found: {$widget->component_path}");
            return $this->renderError($widget);
        }

        // Parse settings JSON
        $settings = [];
        if (!empty($widget->settings)) {
            $settings = json_decode($widget->settings, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Invalid JSON in widget settings: " . json_last_error_msg());
                return $this->renderError($widget);
            }
        }

        // Render component
        try {
            ob_start();

            // Extract settings as variables for the component
            extract($settings);

            // Include component
            include $componentPath;

            return ob_get_clean();

        } catch (\Exception $e) {
            ob_end_clean();
            error_log("Error rendering widget: " . $e->getMessage());
            return $this->renderError($widget);
        }
    }

    /**
     * Render multiple widgets
     */
    public function renderAll(array $widgets): string
    {
        $output = '';

        foreach ($widgets as $widget) {
            $output .= $this->render($widget);
        }

        return $output;
    }

    /**
     * Render error placeholder (only in development)
     */
    private function renderError(object $widget): string
    {
        // Only show errors in development
        if (!defined('ENVIRONMENT') || ENVIRONMENT !== 'development') {
            return '<!-- Widget render error -->';
        }

        return sprintf(
            '<!-- Widget Error: %s (%s) - Component: %s -->',
            $widget->widget_label ?? 'Unknown',
            $widget->type_key ?? 'unknown',
            $widget->component_path ?? 'unknown'
        );
    }

    /**
     * Check if widget component exists
     */
    public function componentExists(string $componentPath): bool
    {
        return file_exists(__DIR__ . '/../Views/' . $componentPath);
    }
}
