<?php
/**
 * Blog Posts Grid (3 sloupce)
 * Dynamický widget - zobrazení nejnovějších blog postů
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce
 * @var int $limit - Počet článků k zobrazení (default: 3)
 * @var bool $showExcerpt - Zobrazit perex (default: true)
 * @var bool $showAuthor - Zobrazit autora (default: true)
 * @var bool $showDate - Zobrazit datum (default: true)
 * @var bool $viewAllButton - Zobrazit tlačítko "Všechny články" (default: true)
 * @var string $viewAllButtonText - Text tlačítka (default: "Všechny články")
 */

// Default values
$sectionTitle = $sectionTitle ?? __('homepage.recent_posts', 'Novinky');
$limit = $limit ?? 3;
$showExcerpt = $showExcerpt ?? true;
$showAuthor = $showAuthor ?? true;
$showDate = $showDate ?? true;
$viewAllButton = $viewAllButton ?? true;
$viewAllButtonText = $viewAllButtonText ?? __('homepage.view_all_posts', 'Všechny články');

// Load blog posts dynamically
$blogModel = new \App\Models\Blog();
$language = $_SESSION['language'] ?? 'cs';
$recentPosts = $blogModel->getRecent($language, $limit);
?>

<?php if (!empty($recentPosts)): ?>
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title"><?= e($sectionTitle) ?></h2>

        <div class="grid grid-cols-3 grid-gap-lg">
            <?php foreach ($recentPosts as $post): ?>
            <?php
            $cardData = [
                'title' => $post->title,
                'image' => $post->featured_image ? upload($post->featured_image) : null,
                'content' => $showExcerpt ? '<p>' . e(truncate($post->excerpt ?? $post->content, 120)) . '</p>' : '',
                'url' => url('blog/' . $post->slug),
                'footer' => ''
            ];

            // Build footer with author and date
            if ($showAuthor || $showDate) {
                $footerParts = [];
                if ($showAuthor) {
                    $footerParts[] = '<span>' . e($post->author_name ?? 'Admin') . '</span>';
                }
                if ($showDate) {
                    $footerParts[] = '<span>' . formatDate($post->published_at) . '</span>';
                }
                $cardData['footer'] = '<div class="post-meta">' . implode(' • ', $footerParts) . '</div>';
            }

            echo renderCard($cardData, ['shadow' => true, 'hover' => true]);
            ?>
            <?php endforeach; ?>
        </div>

        <?php if ($viewAllButton): ?>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= url('blog') ?>" class="btn btn-outline">
                <?= e($viewAllButtonText) ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
