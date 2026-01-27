<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Page;
use App\Models\PageWidget;
use App\Models\Blog;

/**
 * Home Controller - Homepage with widget support
 */
class HomeController extends Controller
{
    public function index(string $language): void
    {
        $this->setLanguage($language);

        // Get homepage data
        $pageModel = new Page();
        $page = $pageModel->getWithTranslation('home', $language);

        if (!$page) {
            // Fallback if homepage not found
            http_response_code(404);
            $this->view('errors/404', ['title' => '404 - Homepage Not Found']);
            return;
        }

        // Load widgets for homepage
        $pageWidgetModel = new PageWidget();
        $widgets = $pageWidgetModel->getByPage($page->id, $language);

        // Get recent blog posts (for dynamic blog widget)
        $blogModel = new Blog();
        $recentPosts = $blogModel->getRecent($language, 3);

        // Always use dynamic template (widget-based)
        $this->view('pages/dynamic', [
            'title' => $page->title ?? 'Škola Labyrint',
            'page' => $page,
            'widgets' => $widgets,
            'recentPosts' => $recentPosts,
            'metaDescription' => $page->meta_description ?? ''
        ]);
    }
}
