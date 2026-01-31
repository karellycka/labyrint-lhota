<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Page;
use App\Models\PageWidget;

/**
 * Page Controller - Static pages with widget support
 */
class PageController extends Controller
{
    /**
     * Show static page by slug
     */
    public function show(string $language, string $slug): void
    {
        $this->setLanguage($language);

        $pageModel = new Page();
        $page = $pageModel->getWithTranslation($slug, $language);

        if (!$page) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => '404 - Page Not Found'
            ]);
            return;
        }

        // Load widgets for this page
        $pageWidgetModel = new PageWidget();
        $widgets = $pageWidgetModel->getByPage($page->id, $language);

        // Use dynamic view if page has widgets, otherwise use static view
        $viewTemplate = !empty($widgets) ? 'pages/dynamic' : 'pages/static';

        $this->view($viewTemplate, [
            'title' => $page->title,
            'page' => $page,
            'widgets' => $widgets,
            'metaDescription' => $page->meta_description,
            'metaKeywords' => $page->meta_keywords
        ]);
    }
}
