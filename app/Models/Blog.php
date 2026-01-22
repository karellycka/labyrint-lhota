<?php

namespace App\Models;

use App\Core\Model;

/**
 * Blog Model - Blog posts with translations and categories
 */
class Blog extends Model
{
    protected string $table = 'blog_posts';

    /**
     * Get published posts with pagination
     */
    public function getPublished(string $language, int $page = 1, int $perPage = null): array
    {
        $perPage = $perPage ?? POSTS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT p.*, bt.title, bt.excerpt, bt.meta_description,
                   u.username as author_name
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            JOIN users u ON p.author_id = u.id
            WHERE p.published = 1
              AND p.published_at <= NOW()
              AND bt.language = ?
            ORDER BY p.published_at DESC
            LIMIT ? OFFSET ?
        ";

        return $this->query($sql, [$language, $perPage, $offset]);
    }

    /**
     * Get total count of published posts
     */
    public function countPublished(string $language): int
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            WHERE p.published = 1
              AND p.published_at <= NOW()
              AND bt.language = ?
        ";

        $result = $this->querySingle($sql, [$language]);
        return $result ? (int) $result->count : 0;
    }

    /**
     * Find post by slug with translation
     */
    public function findBySlug(string $slug, string $language): ?object
    {
        $sql = "
            SELECT p.*, bt.title, bt.excerpt, bt.content, bt.meta_description,
                   u.username as author_name, u.email as author_email
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            JOIN users u ON p.author_id = u.id
            WHERE p.slug = ?
              AND bt.language = ?
              AND p.published = 1
            LIMIT 1
        ";

        return $this->querySingle($sql, [$slug, $language]);
    }

    /**
     * Get posts by category
     */
    public function getByCategory(string $categorySlug, string $language, int $page = 1, int $perPage = null): array
    {
        $perPage = $perPage ?? POSTS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT p.*, bt.title, bt.excerpt,
                   u.username as author_name
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            JOIN users u ON p.author_id = u.id
            JOIN blog_post_categories bpc ON p.id = bpc.post_id
            JOIN blog_categories c ON bpc.category_id = c.id
            WHERE c.slug = ?
              AND p.published = 1
              AND p.published_at <= NOW()
              AND bt.language = ?
            ORDER BY p.published_at DESC
            LIMIT ? OFFSET ?
        ";

        return $this->query($sql, [$categorySlug, $language, $perPage, $offset]);
    }

    /**
     * Get post categories
     */
    public function getCategories(int $postId, string $language): array
    {
        $sql = "
            SELECT c.*, ct.name
            FROM blog_categories c
            JOIN blog_post_categories bpc ON c.id = bpc.category_id
            JOIN blog_category_translations ct ON c.id = ct.category_id
            WHERE bpc.post_id = ? AND ct.language = ?
        ";

        return $this->query($sql, [$postId, $language]);
    }

    /**
     * Get all categories
     */
    public function getAllCategories(string $language): array
    {
        $sql = "
            SELECT c.*, ct.name
            FROM blog_categories c
            JOIN blog_category_translations ct ON c.id = ct.category_id
            WHERE ct.language = ?
            ORDER BY ct.name ASC
        ";

        return $this->query($sql, [$language]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $postId): bool
    {
        return $this->execute(
            "UPDATE blog_posts SET views = views + 1 WHERE id = ?",
            [$postId]
        );
    }

    /**
     * Get recent posts
     */
    public function getRecent(string $language, int $limit = 5): array
    {
        $sql = "
            SELECT p.*, bt.title, bt.excerpt, bt.meta_description,
                   u.username as author_name
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.published = 1
              AND p.published_at <= NOW()
              AND bt.language = ?
            ORDER BY p.published_at DESC
            LIMIT ?
        ";

        return $this->query($sql, [$language, $limit]);
    }

    /**
     * Get related posts (same category)
     */
    public function getRelated(int $postId, string $language, int $limit = 3): array
    {
        $sql = "
            SELECT DISTINCT p.*, bt.title, bt.excerpt
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id
            JOIN blog_post_categories bpc ON p.id = bpc.post_id
            WHERE bpc.category_id IN (
                SELECT category_id FROM blog_post_categories WHERE post_id = ?
            )
            AND p.id != ?
            AND p.published = 1
            AND p.published_at <= NOW()
            AND bt.language = ?
            ORDER BY p.published_at DESC
            LIMIT ?
        ";

        return $this->query($sql, [$postId, $postId, $language, $limit]);
    }
}
