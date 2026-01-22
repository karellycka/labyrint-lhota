<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Media;
use App\Models\Page;

/**
 * Admin Dashboard Controller
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        // Require authentication
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }
    }

    /**
     * Dashboard index
     */
    public function index(): void
    {

        // Get statistics
        $blogModel = new Blog();
        $eventModel = new Event();
        $mediaModel = new Media();
        $pageModel = new Page();

        $stats = [
            'total_posts' => $blogModel->count(),
            'published_posts' => $blogModel->countPublished('cs'),
            'total_events' => $eventModel->count(),
            'upcoming_events' => count($eventModel->getUpcoming('cs')),
            'total_media' => $mediaModel->count(),
            'total_pages' => $pageModel->count()
        ];

        // Get recent posts
        $recentPosts = $blogModel->query("
            SELECT p.*, bt.title, u.username as author_name
            FROM blog_posts p
            JOIN blog_translations bt ON p.id = bt.post_id AND bt.language = 'cs'
            JOIN users u ON p.author_id = u.id
            ORDER BY p.created_at DESC
            LIMIT 5
        ");

        // Get upcoming events
        $upcomingEvents = $eventModel->getUpcoming('cs', 5);

        $this->view('admin/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'upcomingEvents' => $upcomingEvents
        ], 'admin');
    }
}
