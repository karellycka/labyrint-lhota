<?php
/**
 * CTA Image Banner Component
 * Banner s obrázkem na pozadí a call-to-action obsahem
 *
 * Parametry:
 * @var string $backgroundImage - URL obrázku na pozadí
 * @var string $title - Hlavní nadpis (H2)
 * @var string $subtitle - Podnadpis (H3)
 * @var string $buttonText - Text tlačítka
 * @var string $buttonUrl - URL odkaz tlačítka
 * @var string $buttonVariant - Varianta tlačítka (primary, secondary, outline) - volitelné
 * @var bool $overlay - Tmavý overlay přes obrázek (default: true)
 */

$overlay = $overlay ?? true;
$buttonVariant = $buttonVariant ?? 'primary';

// Handle image URL - if relative path, prepend BASE_URL
$backgroundImageUrl = isset($backgroundImage)
    ? (str_starts_with($backgroundImage, 'http') ? $backgroundImage : BASE_URL . $backgroundImage)
    : '';
?>

<section class="cta-image-banner">
    <div class="cta-banner-background" style="background-image: url('<?= e($backgroundImageUrl) ?>')"></div>

    <?php if ($overlay): ?>
        <div class="cta-banner-overlay"></div>
    <?php endif; ?>

    <div class="cta-banner-content">
        <div class="container">
            <h2 class="cta-banner-title"><?= e($title) ?></h2>
            <h3 class="cta-banner-subtitle"><?= e($subtitle) ?></h3>
            <div class="cta-banner-action">
                <a href="<?= e($buttonUrl) ?>" class="btn btn-<?= e($buttonVariant) ?>">
                    <?= e($buttonText) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.cta-image-banner {
    position: relative;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: var(--section-padding-mobile, 80px) 0;
}

.cta-banner-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}

.cta-banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2;
}

.cta-banner-content {
    position: relative;
    z-index: 3;
    text-align: center;
    color: white;
    width: 100%;
    padding: 0 var(--container-padding, 20px);
}

.cta-banner-content .container {
    max-width: 900px;
    margin: 0 auto;
}

.cta-banner-title {
    font-size: clamp(28px, 5vw, 42px);
    font-weight: var(--font-weight-bold, 700);
    line-height: var(--line-height-tight, 1.25);
    color: white;
    margin: 0 0 var(--spacing-lg, 24px) 0;
}

.cta-banner-subtitle {
    font-size: clamp(18px, 3vw, 24px);
    font-weight: var(--font-weight-normal, 400);
    line-height: var(--line-height-relaxed, 1.6);
    color: rgba(255, 255, 255, 0.95);
    margin: 0 0 var(--spacing-2xl, 40px) 0;
}

.cta-banner-action {
    display: flex;
    justify-content: center;
    gap: var(--spacing-md, 16px);
}

/* Responsive design */
@media (min-width: 769px) {
    .cta-image-banner {
        min-height: 500px;
        padding: var(--section-padding-desktop, 100px) 0;
    }
}

@media (max-width: 768px) {
    .cta-image-banner {
        min-height: 350px;
        padding: 60px 0;
    }

    .cta-banner-title {
        font-size: 28px;
        margin-bottom: var(--spacing-md, 16px);
    }

    .cta-banner-subtitle {
        font-size: 18px;
        margin-bottom: var(--spacing-xl, 32px);
    }
}

/* Animation on scroll (optional) */
@media (prefers-reduced-motion: no-preference) {
    .cta-banner-content {
        animation: fadeInUp 0.8s ease-out;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
