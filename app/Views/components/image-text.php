<?php
/**
 * Image + Text Widget
 * Widget pro zobrazení fotky a textu vedle sebe (50/50)
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce H2 (volitelné, centrované)
 * @var string $sectionSubtitle - Podnadpis sekce H3 (volitelné, centrované)
 * @var string $imagePosition - Pozice fotky: 'left' nebo 'right' (default: 'left')
 * @var string $image - URL obrázku (povinné)
 * @var string $textTitle - Nadpis textu H2 (volitelné)
 * @var string $textContent - Obsah textu WYSIWYG (povinné)
 */

// Default values
$sectionTitle = $sectionTitle ?? '';
$sectionSubtitle = $sectionSubtitle ?? '';
$imagePosition = $imagePosition ?? 'left';
$image = $image ?? '';
$textTitle = $textTitle ?? '';
$textContent = $textContent ?? '';
$backgroundColor = $backgroundColor ?? 'transparent';

// Handle image URL - full URL (Cloudinary) or asset path
$imageUrl = '';
if (!empty($image)) {
    $imageUrl = str_starts_with($image, 'http')
        ? $image
        : asset($image);
}

// Determine if we need to reverse the order for mobile
$reverseClass = $imagePosition === 'right' ? '' : 'reverse-mobile';

// Determine if background color is set (not transparent)
$hasBackground = $backgroundColor !== 'transparent' && !empty($backgroundColor);
$sectionClass = 'section bg-light image-text-section';
$cardStyle = $hasBackground ? "background: {$backgroundColor};" : '';
$layoutStyle = trim(($imagePosition === 'right' ? 'direction: rtl;' : '') . ($cardStyle ? ' ' . $cardStyle : ''));
?>

<section class="<?= $sectionClass ?>">
    <div class="container">
        <?php if (!empty($sectionTitle)): ?>
            <h2 class="section-title"><?= e($sectionTitle) ?></h2>
        <?php endif; ?>

        <?php if (!empty($sectionSubtitle)): ?>
            <h3><?= e($sectionSubtitle) ?></h3>
        <?php endif; ?>

        <div class="two-column image-text-layout <?= $reverseClass ?>" style="<?= $layoutStyle ?>">
            <?php if ($imagePosition === 'right'): ?>
                <!-- Text first, then image (RTL reverses to image right, text left) -->
                <div class="text-block" style="direction: ltr;">
                    <?php if (!empty($textTitle)): ?>
                        <h2 class="text-block-title"><?= e($textTitle) ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($textContent)): ?>
                        <div class="text-block-content">
                            <?= $textContent ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="image-text-media" style="direction: ltr;">
                    <?php if (!empty($imageUrl)): ?>
                        <img class="image-text-media-img" src="<?= e($imageUrl) ?>" alt="<?= e($textTitle ?: $sectionTitle) ?>">
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Image first, then text (default left position) -->
                <div class="image-text-media">
                    <?php if (!empty($imageUrl)): ?>
                        <img class="image-text-media-img" src="<?= e($imageUrl) ?>" alt="<?= e($textTitle ?: $sectionTitle) ?>">
                    <?php endif; ?>
                </div>

                <div class="text-block">
                    <?php if (!empty($textTitle)): ?>
                        <h2 class="text-block-title"><?= e($textTitle) ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($textContent)): ?>
                        <div class="text-block-content">
                            <?= $textContent ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
