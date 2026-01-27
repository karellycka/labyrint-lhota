<?php
/**
 * Hero Component
 * Velká uvítací sekce s pozadím (slideshow nebo video) a textem
 *
 * Props:
 * - $title (string) - Hlavní nadpis
 * - $subtitle (string) - Podnadpis
 * - $mediaType (string) - Typ média: 'image' nebo 'video' (default: 'image')
 * - $backgroundImages (array) - Pole URL obrázků pro slideshow (pouze pro mediaType=image)
 * - $videoSource (string) - Zdroj videa: 'external' nebo 'cloudinary' (pouze pro mediaType=video)
 * - $videoUrl (string) - URL videa z YouTube/Vimeo (pouze pro videoSource=external)
 * - $cloudinaryVideoId (string) - Cloudinary video ID (pouze pro videoSource=cloudinary)
 * - $rotatingText (array) - Pole textů pro rotaci (stringy nebo objekty s 'text' property)
 * - $ctaButtons (array) - Pole tlačítek [['text' => '', 'url' => '', 'variant' => '']]
 * - $overlay (bool) - Tmavý overlay přes obrázek/video (default: true)
 * - $height (string) - Výška (sm, md, lg, full) (default: lg)
 */

$mediaType = $mediaType ?? 'image';
$overlay = $overlay ?? true;
$height = $height ?? 'lg';
$rotatingText = $rotatingText ?? [];
$ctaButtons = $ctaButtons ?? [];
$backgroundImages = $backgroundImages ?? [];
$videoSource = $videoSource ?? 'external';
$videoUrl = $videoUrl ?? '';
$cloudinaryVideoId = $cloudinaryVideoId ?? '';
$videoCoverImage = $videoCoverImage ?? '';

// Resolve Cloudinary cloud name safely
$cloudinaryCloudName = '';
if (defined('CLOUDINARY_CLOUD_NAME')) {
    $cloudinaryCloudName = (string)CLOUDINARY_CLOUD_NAME;
} elseif (defined('CONFIG_PATH')) {
    $cloudinaryConfigPath = CONFIG_PATH . '/cloudinary.php';
    if (file_exists($cloudinaryConfigPath)) {
        $cloudinaryConfig = require $cloudinaryConfigPath;
        $cloudinaryCloudName = $cloudinaryConfig['cloud_name'] ?? '';
    }
}

$overlayClass = $overlay ? 'hero-overlay' : '';
$heightClass = "hero-{$height}";
$mediaClass = $mediaType === 'video' ? 'hero-video' : 'hero-slideshow';

$videoCoverUrl = $videoCoverImage ? mediaUrl($videoCoverImage) : '';
$videoCoverStyle = $videoCoverUrl ? ' style="background-image: url(\'' . e($videoCoverUrl) . '\')"' : '';

// Default fallback images if none uploaded (only for image type)
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

<section class="hero <?= $mediaClass ?> <?= $heightClass ?> <?= $overlayClass ?>">
    <?php if ($mediaType === 'video'): ?>
        <!-- VIDEO BACKGROUND -->
        <div class="hero-video-container"<?= $videoCoverStyle ?>>
            <?php if ($videoSource === 'external' && !empty($videoUrl)): ?>
                <!-- External video (YouTube/Vimeo) -->
                <iframe
                    class="hero-video-iframe"
                    src="<?= e($videoUrl) ?>?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen>
                </iframe>
            <?php elseif ($videoSource === 'cloudinary' && !empty($cloudinaryVideoId) && !empty($cloudinaryCloudName)): ?>
                <!-- Cloudinary video -->
                <video
                    class="hero-video-element"
                    autoplay
                    muted
                    loop
                    playsinline
                    <?= $videoCoverUrl ? 'poster="' . e($videoCoverUrl) . '"' : '' ?>>
                    <source src="https://res.cloudinary.com/<?= e($cloudinaryCloudName) ?>/video/upload/<?= e($cloudinaryVideoId) ?>.mp4" type="video/mp4">
                    <source src="https://res.cloudinary.com/<?= e($cloudinaryCloudName) ?>/video/upload/<?= e($cloudinaryVideoId) ?>.webm" type="video/webm">
                </video>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- IMAGE SLIDESHOW -->
        <div class="hero-slideshow-container">
            <?php foreach ($imagesToUse as $index => $imageUrl): ?>
                <?php
                // If it's a full URL (Cloudinary), use directly
                // Otherwise, use asset() helper for local assets
                $imageUrl = str_starts_with($imageUrl, 'http') ? $imageUrl : asset($imageUrl);
                ?>
                <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>"
                     style="background-image: url('<?= e($imageUrl) ?>')"></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
