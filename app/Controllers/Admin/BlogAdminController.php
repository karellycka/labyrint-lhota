<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Blog;
use App\Models\User;

/**
 * Admin Blog Controller - CRUD for blog posts
 */
class BlogAdminController extends Controller
{
    private Blog $blogModel;

    public function __construct()
    {
        parent::__construct();

        // Require authentication
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $this->blogModel = new Blog();
    }

    /**
     * List all blog posts
     */
    public function index(): void
    {
        // Get all posts with translations
        $posts = $this->blogModel->query("
            SELECT
                p.*,
                bt_cs.title as title_cs,
                bt_en.title as title_en,
                u.username as author_name
            FROM blog_posts p
            LEFT JOIN blog_translations bt_cs ON p.id = bt_cs.post_id AND bt_cs.language = 'cs'
            LEFT JOIN blog_translations bt_en ON p.id = bt_en.post_id AND bt_en.language = 'en'
            JOIN users u ON p.author_id = u.id
            ORDER BY p.created_at DESC
        ");

        $this->view('admin/blog/index', [
            'title' => 'Blog Posts',
            'posts' => $posts
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/blog/create', [
            'title' => 'Create Blog Post'
        ], 'admin');
    }

    /**
     * Store new post
     */
    public function store(): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('blog/create'));
        }

        // Validate input
        $errors = [];

        if (empty($_POST['title_cs'])) {
            $errors[] = 'Czech title is required';
        }
        if (empty($_POST['title_en'])) {
            $errors[] = 'English title is required';
        }
        if (empty($_POST['content_cs'])) {
            $errors[] = 'Czech content is required';
        }
        if (empty($_POST['content_en'])) {
            $errors[] = 'English content is required';
        }

        if (!empty($errors)) {
            Session::flash('error', implode(', ', $errors));
            $_SESSION['old_input'] = $_POST;
            redirect(adminUrl('blog/create'));
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Generate slug
            $slug = generateSlug($_POST['title_cs'], 'cs');

            // Insert blog post
            $postId = $this->blogModel->insert([
                'slug' => $slug,
                'author_id' => userId(),
                'featured_image' => $_POST['featured_image'] ?? null,
                'published' => isset($_POST['published']) ? 1 : 0,
                'published_at' => isset($_POST['published']) ? date('Y-m-d H:i:s') : null,
                'meta_description' => $_POST['meta_description'] ?? null,
                'meta_keywords' => $_POST['meta_keywords'] ?? null
            ]);

            // Insert Czech translation
            $this->blogModel->execute("
                INSERT INTO blog_translations (post_id, language, title, excerpt, content)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $postId,
                'cs',
                $_POST['title_cs'],
                $_POST['excerpt_cs'] ?? null,
                $_POST['content_cs']
            ]);

            // Insert English translation
            $this->blogModel->execute("
                INSERT INTO blog_translations (post_id, language, title, excerpt, content)
                VALUES (?, ?, ?, ?, ?)
            ", [
                $postId,
                'en',
                $_POST['title_en'],
                $_POST['excerpt_en'] ?? null,
                $_POST['content_en']
            ]);

            $this->db->commit();

            Session::flash('success', 'Blog post created successfully');
            redirect(adminUrl('blog'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error creating post: ' . $e->getMessage());
            $_SESSION['old_input'] = $_POST;
            redirect(adminUrl('blog/create'));
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        // Get post with translations
        $post = $this->blogModel->find($id);

        if (!$post) {
            Session::flash('error', 'Post not found');
            redirect(adminUrl('blog'));
        }

        // Get translations
        $translations = $this->blogModel->query("
            SELECT language, title, excerpt, content
            FROM blog_translations
            WHERE post_id = ?
        ", [$id]);

        // Organize translations by language
        $translationsByLang = [];
        foreach ($translations as $trans) {
            $translationsByLang[$trans->language] = $trans;
        }

        $this->view('admin/blog/edit', [
            'title' => 'Edit Blog Post',
            'post' => $post,
            'translations' => $translationsByLang
        ], 'admin');
    }

    /**
     * Update post
     */
    public function update(int $id): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl("blog/{$id}/edit"));
        }

        try {
            $this->db->beginTransaction();

            // Update blog post
            $this->blogModel->update($id, [
                'featured_image' => $_POST['featured_image'] ?? null,
                'published' => isset($_POST['published']) ? 1 : 0,
                'published_at' => isset($_POST['published']) ? date('Y-m-d H:i:s') : null,
                'meta_description' => $_POST['meta_description'] ?? null,
                'meta_keywords' => $_POST['meta_keywords'] ?? null
            ]);

            // Update Czech translation
            $this->blogModel->execute("
                UPDATE blog_translations
                SET title = ?, excerpt = ?, content = ?
                WHERE post_id = ? AND language = 'cs'
            ", [
                $_POST['title_cs'],
                $_POST['excerpt_cs'] ?? null,
                $_POST['content_cs'],
                $id
            ]);

            // Update English translation
            $this->blogModel->execute("
                UPDATE blog_translations
                SET title = ?, excerpt = ?, content = ?
                WHERE post_id = ? AND language = 'en'
            ", [
                $_POST['title_en'],
                $_POST['excerpt_en'] ?? null,
                $_POST['content_en'],
                $id
            ]);

            $this->db->commit();

            Session::flash('success', 'Blog post updated successfully');
            redirect(adminUrl('blog'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error updating post: ' . $e->getMessage());
            redirect(adminUrl("blog/{$id}/edit"));
        }
    }

    /**
     * Delete post
     */
    public function delete(int $id): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('blog'));
        }

        try {
            $this->blogModel->delete($id);
            Session::flash('success', 'Blog post deleted successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error deleting post: ' . $e->getMessage());
        }

        redirect(adminUrl('blog'));
    }
}
