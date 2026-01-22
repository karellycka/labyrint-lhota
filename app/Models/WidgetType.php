<?php

namespace App\Models;

use App\Core\Model;

/**
 * WidgetType Model - Widget types registry
 */
class WidgetType extends Model
{
    protected string $table = 'widget_types';

    /**
     * Get all active widget types
     */
    public function getAllActive(): array
    {
        return $this->query("
            SELECT * FROM widget_types
            WHERE is_active = 1
            ORDER BY category, label
        ");
    }

    /**
     * Get widget type by key
     */
    public function getByKey(string $typeKey): ?object
    {
        return $this->querySingle("
            SELECT * FROM widget_types
            WHERE type_key = ?
            LIMIT 1
        ", [$typeKey]);
    }

    /**
     * Get widget types by category
     */
    public function getByCategory(string $category): array
    {
        return $this->query("
            SELECT * FROM widget_types
            WHERE category = ? AND is_active = 1
            ORDER BY label
        ", [$category]);
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        $result = $this->query("
            SELECT DISTINCT category
            FROM widget_types
            WHERE is_active = 1
            ORDER BY
                CASE category
                    WHEN 'layout' THEN 1
                    WHEN 'content' THEN 2
                    WHEN 'media' THEN 3
                    WHEN 'dynamic' THEN 4
                END
        ");

        return array_column($result, 'category');
    }

    /**
     * Get JSON schema for widget type
     */
    public function getSchema(string $typeKey): ?array
    {
        $widget = $this->getByKey($typeKey);

        if (!$widget || empty($widget->schema)) {
            return null;
        }

        // Decode JSON schema
        $schema = json_decode($widget->schema, true);

        return $schema;
    }

    /**
     * Get widget types grouped by category
     */
    public function getAllGroupedByCategory(): array
    {
        $widgets = $this->getAllActive();
        $grouped = [];

        foreach ($widgets as $widget) {
            $category = $widget->category ?? 'content';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $widget;
        }

        return $grouped;
    }
}
