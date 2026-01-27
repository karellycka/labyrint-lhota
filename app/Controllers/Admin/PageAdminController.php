<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Page;
use App\Models\PageWidget;
use App\Models\WidgetType;

/**
 * Admin Page Controller - Edit static pages
 */
class PageAdminController extends Controller
{
    private Page $pageModel;
    private PageWidget $pageWidgetModel;
    private WidgetType $widgetTypeModel;

    public function __construct()
    {
        parent::__construct();

        // Require authentication
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $this->pageModel = new Page();
        $this->pageWidgetModel = new PageWidget();
        $this->widgetTypeModel = new WidgetType();
    }

    /**
     * List all pages
     */
    public function index(): void
    {
        // Get all pages with translations
        $pages = $this->pageModel->query("
            SELECT
                p.*,
                pt_cs.title as title_cs,
                pt_en.title as title_en
            FROM pages p
            LEFT JOIN page_translations pt_cs ON p.id = pt_cs.page_id AND pt_cs.language = 'cs'
            LEFT JOIN page_translations pt_en ON p.id = pt_en.page_id AND pt_en.language = 'en'
            ORDER BY p.slug
        ");

        $this->view('admin/pages/index', [
            'title' => 'Pages',
            'pages' => $pages
        ], 'admin');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        // Get page with translations
        $page = $this->pageModel->find($id);

        if (!$page) {
            Session::flash('error', 'Page not found');
            redirect(adminUrl('pages'));
        }

        // Get translations
        $translations = $this->pageModel->query("
            SELECT language, title, content
            FROM page_translations
            WHERE page_id = ?
        ", [$id]);

        // Organize translations by language
        $translationsByLang = [];
        foreach ($translations as $trans) {
            $translationsByLang[$trans->language] = $trans;
        }

        // Get widgets for this page (CS language for preview)
        $widgets = $this->pageWidgetModel->getByPage($id, 'cs');

        // Get available widget types grouped by category
        $widgetTypes = $this->widgetTypeModel->getAllGroupedByCategory();

        $this->view('admin/pages/edit', [
            'title' => 'Edit Page',
            'page' => $page,
            'translations' => $translationsByLang,
            'widgets' => $widgets,
            'widgetTypes' => $widgetTypes
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/pages/create', [
            'title' => 'Create New Page'
        ], 'admin');
    }

    /**
     * Store new page
     */
    public function store(): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('pages/create'));
        }

        // Validate required fields
        $slug = trim($_POST['slug'] ?? '');
        $titleCs = trim($_POST['title_cs'] ?? '');
        $hasEnglishVersion = isset($_POST['has_english_version']);
        $slugEn = trim($_POST['slug_en'] ?? '');
        $titleEn = trim($_POST['title_en'] ?? '');

        if (empty($slug) || empty($titleCs)) {
            Session::flash('error', 'Slug and Czech title are required');
            redirect(adminUrl('pages/create'));
        }

        // Validate slug format (lowercase, alphanumeric, hyphens only)
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            Session::flash('error', 'Slug must contain only lowercase letters, numbers, and hyphens');
            redirect(adminUrl('pages/create'));
        }

        // Validate slug_en format if provided
        if ($hasEnglishVersion && !empty($slugEn) && !preg_match('/^[a-z0-9\-]+$/', $slugEn)) {
            Session::flash('error', 'English slug must contain only lowercase letters, numbers, and hyphens');
            redirect(adminUrl('pages/create'));
        }

        try {
            $this->db->beginTransaction();

            // Check if slug already exists
            $existing = $this->pageModel->query("SELECT id FROM pages WHERE slug = ?", [$slug]);
            if (!empty($existing)) {
                throw new \Exception('Page with this slug already exists');
            }

            // Check if slug_en already exists (if provided)
            if ($hasEnglishVersion && !empty($slugEn)) {
                $existingEn = $this->pageModel->query("SELECT id FROM pages WHERE slug_en = ?", [$slugEn]);
                if (!empty($existingEn)) {
                    throw new \Exception('Page with this English slug already exists');
                }
            }

            // Insert page
            $this->pageModel->execute("
                INSERT INTO pages (slug, slug_en, published)
                VALUES (?, ?, ?)
            ", [
                $slug,
                ($hasEnglishVersion && !empty($slugEn)) ? $slugEn : null,
                isset($_POST['published']) ? 1 : 0
            ]);

            $pageId = $this->db->lastInsertId();

            // Insert Czech translation
            $this->pageModel->execute("
                INSERT INTO page_translations (page_id, language, title)
                VALUES (?, 'cs', ?)
            ", [
                $pageId,
                $titleCs
            ]);

            // Insert English translation if enabled
            if ($hasEnglishVersion) {
                $this->pageModel->execute("
                    INSERT INTO page_translations (page_id, language, title)
                    VALUES (?, 'en', ?)
                ", [
                    $pageId,
                    $titleEn ?: $titleCs // Use Czech title as fallback
                ]);
            }

            $this->db->commit();

            Session::flash('success', 'Page created successfully');
            redirect(adminUrl("pages/{$pageId}/edit"));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error creating page: ' . $e->getMessage());
            redirect(adminUrl('pages/create'));
        }
    }

    /**
     * Update page
     */
    public function update(int $id): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl("pages/{$id}/edit"));
        }

        $hasEnglishVersion = isset($_POST['has_english_version']);
        $slugEn = trim($_POST['slug_en'] ?? '');

        // Validate slug_en format if provided
        if ($hasEnglishVersion && !empty($slugEn) && !preg_match('/^[a-z0-9\-]+$/', $slugEn)) {
            Session::flash('error', 'English slug must contain only lowercase letters, numbers, and hyphens');
            redirect(adminUrl("pages/{$id}/edit"));
        }

        try {
            $this->db->beginTransaction();

            // Check if slug_en already exists (if provided and different from current)
            if ($hasEnglishVersion && !empty($slugEn)) {
                $existingEn = $this->pageModel->query("SELECT id FROM pages WHERE slug_en = ? AND id != ?", [$slugEn, $id]);
                if (!empty($existingEn)) {
                    throw new \Exception('Page with this English slug already exists');
                }
            }

            // Update page
            $this->pageModel->update($id, [
                'slug_en' => ($hasEnglishVersion && !empty($slugEn)) ? $slugEn : null,
                'published' => isset($_POST['published']) ? 1 : 0,
                'meta_description' => $_POST['meta_description'] ?? null,
                'meta_keywords' => $_POST['meta_keywords'] ?? null
            ]);

            // Update Czech translation
            $this->pageModel->execute("
                UPDATE page_translations
                SET title = ?
                WHERE page_id = ? AND language = 'cs'
            ", [
                $_POST['title_cs'],
                $id
            ]);

            // Update English translation if enabled
            if ($hasEnglishVersion) {
                $titleEn = trim($_POST['title_en'] ?? '');

                // Check if English translation exists
                $existingEn = $this->pageModel->query("
                    SELECT id FROM page_translations WHERE page_id = ? AND language = 'en'
                ", [$id]);

                if (!empty($existingEn)) {
                    // Update existing English translation
                    $this->pageModel->execute("
                        UPDATE page_translations
                        SET title = ?
                        WHERE page_id = ? AND language = 'en'
                    ", [
                        $titleEn,
                        $id
                    ]);
                } else {
                    // Insert new English translation
                    $this->pageModel->execute("
                        INSERT INTO page_translations (page_id, language, title)
                        VALUES (?, 'en', ?)
                    ", [
                        $id,
                        $titleEn
                    ]);
                }
            }

            $this->db->commit();

            Session::flash('success', 'Page updated successfully');
            redirect(adminUrl('pages'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error updating page: ' . $e->getMessage());
            redirect(adminUrl("pages/{$id}/edit"));
        }
    }

    /**
     * Delete page
     */
    public function delete(int $id): void
    {
        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('pages'));
        }

        // Don't allow deleting homepage
        $page = $this->pageModel->find($id);
        if ($page && $page->slug === 'home') {
            Session::flash('error', 'Cannot delete homepage');
            redirect(adminUrl('pages'));
        }

        try {
            // Delete page (CASCADE will delete translations and widgets)
            $this->pageModel->delete($id);

            Session::flash('success', 'Page deleted successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error deleting page: ' . $e->getMessage());
        }

        redirect(adminUrl('pages'));
    }
}
