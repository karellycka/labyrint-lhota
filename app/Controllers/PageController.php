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
        // #region agent log
        error_log('[DEBUG] PageController:show:entry | slug=' . $slug . ' | lang=' . $language);
        // #endregion

        $this->setLanguage($language);

        $pageModel = new Page();
        $page = $pageModel->getWithTranslation($slug, $language);

        // #region agent log
        error_log('[DEBUG] PageController:show:afterDB | pageFound=' . ($page!==null?'yes':'no') . ' | pageId=' . ($page->id??'null') . ' | slug=' . $slug);
        // #endregion

        if (!$page) {
            // #region agent log
            error_log('[DEBUG] PageController:show:404 | Page not found | slug=' . $slug);
            // #endregion
            http_response_code(404);
            $this->view('errors/404', [
                'title' => '404 - Page Not Found'
            ]);
            return;
        }

        // Load widgets for this page
        $pageWidgetModel = new PageWidget();
        $widgets = $pageWidgetModel->getByPage($page->id, $language);

        // Always use dynamic view - it handles both cases (with widgets and empty)
        $viewTemplate = 'pages/dynamic';

        // #region agent log
        error_log('[DEBUG] PageController:show:beforeView | viewTemplate=' . $viewTemplate . ' | widgetCount=' . count($widgets) . ' | pageTitle=' . ($page->title??''));
        // #endregion

        $this->view($viewTemplate, [
            'title' => $page->title,
            'page' => $page,
            'widgets' => $widgets,
            'metaDescription' => $page->meta_description,
            'metaKeywords' => $page->meta_keywords
        ]);
    }
}
