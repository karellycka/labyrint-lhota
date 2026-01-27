<?php

/**
 * Route definice
 *
 * Formát: $router->get('/cesta', 'Controller@method');
 * Parametry v cestě: :param
 * Jazyk je automaticky detekován a přidán jako první parametr do controlleru
 */

use App\Core\Router;

return function (Router $router) {
    // Homepage
    $router->get('/', 'HomeController@index');

    // Static pages
    $router->get('/:slug', 'PageController@show');

    // Blog
    $router->get('/blog', 'BlogController@index');
    $router->get('/blog/:slug', 'BlogController@show');
    $router->get('/blog/category/:categorySlug', 'BlogController@category');
    $router->get('/blog/feed', 'BlogController@feed');

    // Events
    $router->get('/events', 'EventController@index');
    $router->get('/events/:slug', 'EventController@show');
    $router->get('/events/calendar/:year/:month', 'EventController@calendar');

    // Gallery
    $router->get('/gallery', 'GalleryController@index');
    $router->get('/gallery/:folder', 'GalleryController@folder');

    // Contact
    $router->get('/contact', 'ContactController@index');
    $router->post('/contact/submit', 'ContactController@submit');

    // Sitemap & Robots
    $router->get('/sitemap.xml', 'SitemapController@index');

    // Admin routes (without language prefix)
    // Note: Admin controllers don't have $language parameter

    // Admin - Auth & Dashboard
    $router->get('/admin', 'Admin/DashboardController@index');
    $router->get('/admin/login', 'Admin/AuthController@showLogin');
    $router->post('/admin/login', 'Admin/AuthController@login');
    $router->get('/admin/logout', 'Admin/AuthController@logout');

    // Admin - Blog
    $router->get('/admin/blog', 'Admin/BlogAdminController@index');
    $router->get('/admin/blog/create', 'Admin/BlogAdminController@create');
    $router->post('/admin/blog/store', 'Admin/BlogAdminController@store');
    $router->get('/admin/blog/:id/edit', 'Admin/BlogAdminController@edit');
    $router->post('/admin/blog/:id/update', 'Admin/BlogAdminController@update');
    $router->post('/admin/blog/:id/delete', 'Admin/BlogAdminController@delete');

    // Admin - Pages
    $router->get('/admin/pages', 'Admin/PageAdminController@index');
    $router->get('/admin/pages/create', 'Admin/PageAdminController@create');
    $router->post('/admin/pages/store', 'Admin/PageAdminController@store');
    $router->get('/admin/pages/:id/edit', 'Admin/PageAdminController@edit');
    $router->post('/admin/pages/:id/update', 'Admin/PageAdminController@update');
    $router->post('/admin/pages/:id/delete', 'Admin/PageAdminController@delete');

    // Admin - Widgets (AJAX API)
    $router->get('/admin/widgets/schema/:typeKey', 'Admin/WidgetAdminController@getSchema');
    $router->get('/admin/widgets/:id', 'Admin/WidgetAdminController@getWidget');
    $router->post('/admin/pages/:pageId/widgets/create', 'Admin/WidgetAdminController@create');
    $router->post('/admin/widgets/:id/update', 'Admin/WidgetAdminController@update');
    $router->post('/admin/widgets/:id/delete', 'Admin/WidgetAdminController@delete');
    $router->post('/admin/widgets/:id/move-up', 'Admin/WidgetAdminController@moveUp');
    $router->post('/admin/widgets/:id/move-down', 'Admin/WidgetAdminController@moveDown');
    $router->post('/admin/pages/:pageId/widgets/reorder', 'Admin/WidgetAdminController@reorder');

    // Admin - Events
    $router->get('/admin/events', 'Admin/EventAdminController@index');
    $router->get('/admin/events/create', 'Admin/EventAdminController@create');
    $router->post('/admin/events/store', 'Admin/EventAdminController@store');
    $router->get('/admin/events/:id/edit', 'Admin/EventAdminController@edit');
    $router->post('/admin/events/:id/update', 'Admin/EventAdminController@update');
    $router->post('/admin/events/:id/delete', 'Admin/EventAdminController@delete');

    // Admin - Media
    $router->get('/admin/media', 'Admin/MediaAdminController@index');
    $router->get('/admin/media/api/all', 'Admin/MediaAdminController@getAll');
    $router->post('/admin/media/upload', 'Admin/MediaAdminController@upload');
    $router->get('/admin/media/:id/edit', 'Admin/MediaAdminController@edit');
    $router->post('/admin/media/:id/update', 'Admin/MediaAdminController@update');
    $router->post('/admin/media/:id/delete', 'Admin/MediaAdminController@delete');

    // Admin - Contact submissions
    $router->get('/admin/contact', 'Admin/ContactAdminController@index');
    $router->get('/admin/contact/:id/view', 'Admin/ContactAdminController@view');
    $router->post('/admin/contact/:id/mark-read', 'Admin/ContactAdminController@markRead');

    // Admin - Theme Settings
    $router->get('/admin/theme', 'Admin/ThemeController@index');
    $router->post('/admin/theme/update', 'Admin/ThemeController@update');
    $router->post('/admin/theme/regenerate', 'Admin/ThemeController@regenerateCSS');
    $router->get('/admin/theme/export', 'Admin/ThemeController@export');
    $router->post('/admin/theme/import', 'Admin/ThemeController@import');
    $router->get('/admin/theme/preview', 'Admin/ThemeController@preview');
    $router->get('/admin/theme/setting', 'Admin/ThemeController@getSetting');
};
