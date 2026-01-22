<?php

namespace App\Models;

use App\Core\Model;

/**
 * Page Model - Static pages with translations
 */
class Page extends Model
{
    protected string $table = 'pages';

    /**
     * Find page by slug
     */
    public function findBySlug(string $slug): ?object
    {
        return $this->whereFirst('slug', $slug);
    }

    /**
     * Get page with translation
     */
    public function getWithTranslation(string $slug, string $language): ?object
    {
        $sql = "
            SELECT p.*, pt.title, pt.meta_description, pt.meta_keywords, pt.content
            FROM pages p
            LEFT JOIN page_translations pt ON p.id = pt.page_id AND pt.language = ?
            WHERE p.slug = ? AND p.published = 1
            LIMIT 1
        ";

        return $this->querySingle($sql, [$language, $slug]);
    }

    /**
     * Get all published pages
     */
    public function getPublished(string $language): array
    {
        $sql = "
            SELECT p.*, pt.title
            FROM pages p
            JOIN page_translations pt ON p.id = pt.page_id
            WHERE p.published = 1 AND pt.language = ?
            ORDER BY p.created_at DESC
        ";

        return $this->query($sql, [$language]);
    }

    /**
     * Get page translations
     */
    public function getTranslations(int $pageId): array
    {
        $sql = "SELECT * FROM page_translations WHERE page_id = ?";
        return $this->query($sql, [$pageId]);
    }

    /**
     * Update page translation
     */
    public function updateTranslation(int $pageId, string $language, array $data): bool
    {
        $sql = "
            INSERT INTO page_translations (page_id, language, title, meta_description, meta_keywords, content)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                meta_description = VALUES(meta_description),
                meta_keywords = VALUES(meta_keywords),
                content = VALUES(content)
        ";

        return $this->execute($sql, [
            $pageId,
            $language,
            $data['title'] ?? '',
            $data['meta_description'] ?? null,
            $data['meta_keywords'] ?? null,
            $data['content'] ?? ''
        ]);
    }

    /**
     * Create page with translation
     */
    public function createWithTranslation(array $pageData, array $translationData, string $language): int
    {
        // Insert page
        $pageId = $this->insert($pageData);

        // Insert translation
        if ($pageId) {
            $this->updateTranslation($pageId, $language, $translationData);
        }

        return $pageId;
    }
}
