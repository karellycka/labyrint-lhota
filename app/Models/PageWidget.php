<?php

namespace App\Models;

use App\Core\Model;

/**
 * PageWidget Model - Widgets assigned to pages
 */
class PageWidget extends Model
{
    protected string $table = 'page_widgets';

    /**
     * Get all widgets for a page with translations
     */
    public function getByPage(int $pageId, string $language = 'cs'): array
    {
        return $this->query("
            SELECT
                pw.*,
                wt.type_key,
                wt.label as widget_label,
                wt.component_path,
                wt.icon,
                wt.category,
                pwt.settings
            FROM page_widgets pw
            JOIN widget_types wt ON pw.widget_type_key = wt.type_key
            LEFT JOIN page_widget_translations pwt
                ON pw.id = pwt.page_widget_id
                AND pwt.language = ?
            WHERE pw.page_id = ? AND pw.is_active = 1
            ORDER BY pw.display_order ASC
        ", [$language, $pageId]);
    }

    /**
     * Get widget with all translations
     */
    public function getWithTranslations(int $widgetId): ?object
    {
        $widget = $this->find($widgetId);

        if (!$widget) {
            return null;
        }

        // Get translations
        $translations = $this->query("
            SELECT language, settings
            FROM page_widget_translations
            WHERE page_widget_id = ?
        ", [$widgetId]);

        $widget->translations = [];
        foreach ($translations as $trans) {
            $widget->translations[$trans->language] = json_decode($trans->settings, true);
        }

        return $widget;
    }

    /**
     * Create widget with translations
     */
    public function createWithTranslations(int $pageId, string $widgetTypeKey, array $translationsData): int
    {
        // Get max display_order for this page
        $maxOrder = $this->querySingle("
            SELECT MAX(display_order) as max_order
            FROM page_widgets
            WHERE page_id = ?
        ", [$pageId]);

        $displayOrder = ($maxOrder->max_order ?? -1) + 1;

        // Insert widget
        $this->execute("
            INSERT INTO page_widgets (page_id, widget_type_key, display_order)
            VALUES (?, ?, ?)
        ", [$pageId, $widgetTypeKey, $displayOrder]);

        $widgetId = $this->db->lastInsertId();

        // Insert translations
        foreach ($translationsData as $language => $settings) {
            $this->execute("
                INSERT INTO page_widget_translations (page_widget_id, language, settings)
                VALUES (?, ?, ?)
            ", [$widgetId, $language, json_encode($settings)]);
        }

        return $widgetId;
    }

    /**
     * Update widget translations
     */
    public function updateTranslations(int $widgetId, array $translationsData): bool
    {
        try {
            foreach ($translationsData as $language => $settings) {
                $this->execute("
                    INSERT INTO page_widget_translations (page_widget_id, language, settings)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE settings = VALUES(settings)
                ", [$widgetId, $language, json_encode($settings)]);
            }

            return true;
        } catch (\Exception $e) {
            error_log('Error updating widget translations: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reorder widgets for a page
     */
    public function reorder(int $pageId, array $widgetIds): bool
    {
        try {
            $this->db->beginTransaction();

            foreach ($widgetIds as $order => $widgetId) {
                $this->execute("
                    UPDATE page_widgets
                    SET display_order = ?
                    WHERE id = ? AND page_id = ?
                ", [$order, $widgetId, $pageId]);
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Error reordering widgets: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Move widget up
     */
    public function moveUp(int $widgetId): bool
    {
        $widget = $this->find($widgetId);
        if (!$widget || $widget->display_order <= 0) {
            return false;
        }

        // Get widget above
        $widgetAbove = $this->querySingle("
            SELECT id, display_order
            FROM page_widgets
            WHERE page_id = ? AND display_order = ?
            LIMIT 1
        ", [$widget->page_id, $widget->display_order - 1]);

        if (!$widgetAbove) {
            return false;
        }

        // Swap orders
        try {
            $this->db->beginTransaction();

            $this->execute("UPDATE page_widgets SET display_order = ? WHERE id = ?",
                [$widgetAbove->display_order, $widgetId]);
            $this->execute("UPDATE page_widgets SET display_order = ? WHERE id = ?",
                [$widget->display_order, $widgetAbove->id]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Move widget down
     */
    public function moveDown(int $widgetId): bool
    {
        $widget = $this->find($widgetId);
        if (!$widget) {
            return false;
        }

        // Get widget below
        $widgetBelow = $this->querySingle("
            SELECT id, display_order
            FROM page_widgets
            WHERE page_id = ? AND display_order = ?
            LIMIT 1
        ", [$widget->page_id, $widget->display_order + 1]);

        if (!$widgetBelow) {
            return false;
        }

        // Swap orders
        try {
            $this->db->beginTransaction();

            $this->execute("UPDATE page_widgets SET display_order = ? WHERE id = ?",
                [$widgetBelow->display_order, $widgetId]);
            $this->execute("UPDATE page_widgets SET display_order = ? WHERE id = ?",
                [$widget->display_order, $widgetBelow->id]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Count widgets for a page
     */
    public function countByPage(int $pageId): int
    {
        $result = $this->querySingle("
            SELECT COUNT(*) as count
            FROM page_widgets
            WHERE page_id = ? AND is_active = 1
        ", [$pageId]);

        return $result->count ?? 0;
    }
}
