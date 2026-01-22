<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Blog;

/**
 * Blog Controller
 */
class BlogController extends Controller
{
    /**
     * Blog listing
     */
    public function index(string $language): void
    {
        $this->setLanguage($language);

        $page = $_GET['page'] ?? 1;

        $blogModel = new Blog();
        $posts = $blogModel->getPublished($language, (int) $page);
        $totalPosts = $blogModel->countPublished($language);
        $totalPages = ceil($totalPosts / POSTS_PER_PAGE);

        $this->view('blog/index', [
            'title' => 'Blog',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Blog post detail
     */
    public function show(string $language, string $slug): void
    {
        $this->setLanguage($language);

        $blogModel = new Blog();
        $post = $blogModel->findBySlug($slug, $language);

        if (!$post) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => '404 - Post Not Found'
            ]);
            return;
        }

        // Increment views
        $blogModel->incrementViews((int) $post->id);

        // Get related posts
        $relatedPosts = $blogModel->getRelated((int) $post->id, $language, 3);

        // Get categories
        $categories = $blogModel->getCategories((int) $post->id, $language);

        $this->view('blog/show', [
            'title' => $post->title,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'categories' => $categories,
            'metaDescription' => $post->meta_description
        ]);
    }

    /**
     * Blog category
     */
    public function category(string $language, string $categorySlug): void
    {
        $this->setLanguage($language);

        $page = $_GET['page'] ?? 1;

        $blogModel = new Blog();
        $posts = $blogModel->getByCategory($categorySlug, $language, (int) $page);

        $this->view('blog/category', [
            'title' => 'Blog Category',
            'posts' => $posts,
            'categorySlug' => $categorySlug
        ]);
    }

    /**
     * RSS Feed
     */
    public function feed(string $language): void
    {
        header('Content-Type: application/rss+xml; charset=utf-8');

        $blogModel = new Blog();
        $posts = $blogModel->getPublished($language, 1, 20);

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
        <rss version="2.0">
            <channel>
                <title><?= SITE_NAME ?> - Blog</title>
                <link><?= url('blog', $language) ?></link>
                <description><?= SITE_DESCRIPTION ?></description>
                <language><?= $language ?></language>

                <?php foreach ($posts as $post): ?>
                <item>
                    <title><?= htmlspecialchars($post->title) ?></title>
                    <link><?= url('blog/' . $post->slug, $language) ?></link>
                    <description><?= htmlspecialchars($post->excerpt ?? '') ?></description>
                    <pubDate><?= date('r', strtotime($post->published_at)) ?></pubDate>
                    <author><?= htmlspecialchars($post->author_name) ?></author>
                </item>
                <?php endforeach; ?>
            </channel>
        </rss>
        <?php
        exit;
    }
}
