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
 * @var string $textTitle - Nadpis textu H4 (volitelné)
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

// Handle image URL - if relative path, prepend BASE_URL
$imageUrl = '';
if (!empty($image)) {
    $imageUrl = str_starts_with($image, 'http')
        ? $image
        : BASE_URL . $image;
}

// Determine if we need to reverse the order for mobile
$reverseClass = $imagePosition === 'right' ? '' : 'reverse-mobile';

// Determine if background color is set (not transparent)
$hasBackground = $backgroundColor !== 'transparent' && !empty($backgroundColor);
$sectionClass = $hasBackground ? 'section' : 'section bg-light';
$sectionStyle = $hasBackground ? "background: {$backgroundColor}; color: var(--color-bg-white, #FDFBF7);" : '';
?>

<section class="<?= $sectionClass ?>" style="<?= $sectionStyle ?>">
    <div class="container">
        <?php if (!empty($sectionTitle)): ?>
            <h2 class="section-title" <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>><?= e($sectionTitle) ?></h2>
        <?php endif; ?>

        <?php if (!empty($sectionSubtitle)): ?>
            <h3 <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>><?= e($sectionSubtitle) ?></h3>
        <?php endif; ?>

        <div class="two-column <?= $reverseClass ?>" style="<?= $imagePosition === 'right' ? 'direction: rtl;' : '' ?>">
            <?php if ($imagePosition === 'right'): ?>
                <!-- Text first, then image (RTL reverses to image right, text left) -->
                <div class="text-block" style="direction: ltr;">
                    <?php if (!empty($textTitle)): ?>
                        <h4 class="text-block-title" <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>><?= e($textTitle) ?></h4>
                    <?php endif; ?>

                    <?php if (!empty($textContent)): ?>
                        <div class="text-block-content" <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>>
                            <?= $textContent ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="direction: ltr;">
                    <?php if (!empty($imageUrl)): ?>
                        <img src="<?= e($imageUrl) ?>" alt="<?= e($textTitle ?: $sectionTitle) ?>" style="width: 100%; height: auto; border-radius: var(--border-radius, 16px); object-fit: cover;">
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Image first, then text (default left position) -->
                <div>
                    <?php if (!empty($imageUrl)): ?>
                        <img src="<?= e($imageUrl) ?>" alt="<?= e($textTitle ?: $sectionTitle) ?>" style="width: 100%; height: auto; border-radius: var(--border-radius, 16px); object-fit: cover;">
                    <?php endif; ?>
                </div>

                <div class="text-block">
                    <?php if (!empty($textTitle)): ?>
                        <h4 class="text-block-title" <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>><?= e($textTitle) ?></h4>
                    <?php endif; ?>

                    <?php if (!empty($textContent)): ?>
                        <div class="text-block-content" <?= $hasBackground ? 'style="color: var(--color-bg-white, #FDFBF7);"' : '' ?>>
                            <?= $textContent ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
