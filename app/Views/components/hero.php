<?php
/**
 * Hero Component
 * Velká uvítací sekce s pozadím a textem
 *
 * Props:
 * - $title (string) - Hlavní nadpis
 * - $subtitle (string) - Podnadpis
 * - $backgroundImages (array) - Pole URL obrázků pro slideshow (pokud není zadáno, použijí se výchozí)
 * - $rotatingText (array) - Pole textů pro rotaci (stringy nebo objekty s 'text' property)
 * - $ctaButtons (array) - Pole tlačítek [['text' => '', 'url' => '', 'variant' => '']]
 * - $overlay (bool) - Tmavý overlay přes obrázek (default: true)
 * - $height (string) - Výška (sm, md, lg, full) (default: lg)
 */

$overlay = $overlay ?? true;
$height = $height ?? 'lg';
$rotatingText = $rotatingText ?? [];
$ctaButtons = $ctaButtons ?? [];
$backgroundImages = $backgroundImages ?? [];

$overlayClass = $overlay ? 'hero-overlay' : '';
$heightClass = "hero-{$height}";

// Default fallback images if none uploaded
$defaultImages = [
    'images/hero/slide-1.jpg',
    'images/hero/slide-2.jpg',
    'images/hero/slide-3.jpg',
    'images/hero/slide-4.jpg',
    'images/hero/slide-5.jpg'
];

// Use uploaded images if available, otherwise use defaults
$imagesToUse = !empty($backgroundImages) ? $backgroundImages : $defaultImages;
?>

<section class="hero hero-slideshow <?= $heightClass ?> <?= $overlayClass ?>">
    <div class="hero-slideshow-container">
        <?php foreach ($imagesToUse as $index => $imageUrl): ?>
            <?php
            // If it's a default image (doesn't start with /), use asset() helper
            // Otherwise, it's an uploaded image with full path
            $imageUrl = str_starts_with($imageUrl, '/') ? BASE_URL . $imageUrl : asset($imageUrl);
            ?>
            <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>"
                 style="background-image: url('<?= e($imageUrl) ?>')"></div>
        <?php endforeach; ?>
    </div>

    <?php if ($overlay): ?>
        <div class="hero-overlay-layer"></div>
    <?php endif; ?>

    <div class="hero-content">
        <div class="container">
            <div class="hero-logo">
                <img src="<?= asset('images/logo/labyrint_sq_white_cropped.svg') ?>" alt="<?= SITE_NAME ?>">
            </div>

            <h1 class="hero-title">
                <span class="hero-title-main"><?= e($title) ?></span><br>
                <?php if (!empty($rotatingText)): ?>
                    <span class="rotating-text" data-texts='<?= htmlspecialchars(json_encode($rotatingText), ENT_QUOTES, 'UTF-8') ?>'>
                        <?= e($rotatingText[0]['text'] ?? '') ?>
                    </span>
                <?php endif; ?>
            </h1>

            <?php if (isset($subtitle)): ?>
                <p class="hero-subtitle"><?= e($subtitle) ?></p>
            <?php endif; ?>

            <?php if (!empty($ctaButtons)): ?>
                <div class="hero-actions">
                    <?php foreach ($ctaButtons as $button): ?>
                        <?php
                        $variant = $button['variant'] ?? 'primary';
                        $target = $button['target'] ?? '_self';
                        ?>
                        <a href="<?= e($button['url']) ?>"
                           class="btn btn-<?= e($variant) ?>"
                           target="<?= e($target) ?>">
                            <?= e($button['text']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
