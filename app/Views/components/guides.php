<?php
/**
 * Guides (Průvodci) Widget Component
 * Widget pro zobrazení týmu průvodců s fotkou, pozicí a citátem
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce (volitelné)
 * @var string $sectionSubtitle - Podnadpis sekce (volitelné)
 * @var array $guides - Pole průvodců [{'photo', 'name', 'position', 'quote'}]
 */

// Default values
$sectionTitle = $sectionTitle ?? '';
$sectionSubtitle = $sectionSubtitle ?? '';
$guides = $guides ?? [];
?>

<section class="guides-widget">
    <div class="container">
        <?php if (!empty($sectionTitle)): ?>
            <h2 class="section-title"><?= e($sectionTitle) ?></h2>
        <?php endif; ?>

        <?php if (!empty($sectionSubtitle)): ?>
            <h3 class="guides-subtitle"><?= e($sectionSubtitle) ?></h3>
        <?php endif; ?>

        <?php if (!empty($guides)): ?>
        <div class="guides-grid">
            <?php foreach ($guides as $guide): ?>
            <?php
            // Handle image URL - full URL (Cloudinary) or asset path
            $photoUrl = '';
            if (!empty($guide['photo'])) {
                $photoUrl = str_starts_with($guide['photo'], 'http')
                    ? $guide['photo']
                    : asset($guide['photo']);
            }
            ?>
            <div class="guide-card">
                <?php if (!empty($photoUrl)): ?>
                <div class="guide-photo">
                    <img src="<?= e($photoUrl) ?>" alt="<?= e($guide['name'] ?? $guide['position'] ?? 'Průvodce') ?>">
                </div>
                <?php endif; ?>

                <?php if (!empty($guide['name'])): ?>
                    <h4 class="guide-name"><?= e($guide['name']) ?></h4>
                <?php endif; ?>

                <?php if (!empty($guide['position'])): ?>
                    <p class="guide-position"><?= e($guide['position']) ?></p>
                <?php endif; ?>

                <?php if (!empty($guide['quote'])): ?>
                    <blockquote class="guide-quote">
                        <span class="quote-mark">"</span><?= e($guide['quote']) ?><span class="quote-mark">"</span>
                    </blockquote>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.guides-widget {
    background-color: var(--color-bg-light, #F5F1EB);
    padding: var(--section-padding-mobile, 60px) 0;
}

.guides-widget .container {
    max-width: var(--container-width, 1200px);
    margin: 0 auto;
    padding: 0 var(--container-padding, 20px);
}

.guides-subtitle {
    font-size: var(--font-size-h3, 24px);
    font-weight: var(--font-weight-normal, 400);
    color: var(--color-text-light, #6B7280);
    text-align: center;
    margin-bottom: var(--spacing-2xl, 48px);
}

.guides-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-xl, 32px);
}

.guide-card {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.guide-photo {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: var(--spacing-md, 16px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.guide-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.guide-name {
    font-size: var(--font-size-lg, 18px);
    font-weight: var(--font-weight-bold, 700);
    color: var(--color-text, #2C323A);
    margin: 0 0 var(--spacing-xs, 4px) 0;
}

.guide-position {
    font-size: var(--font-size-base, 16px);
    font-weight: var(--font-weight-medium, 500);
    color: var(--color-primary, #00792E);
    margin: 0 0 var(--spacing-sm, 8px) 0;
}

.guide-quote {
    font-size: var(--font-size-sm, 14px);
    font-style: italic;
    color: var(--color-text-light, #6B7280);
    line-height: var(--line-height-relaxed, 1.6);
    margin: 0;
    padding: 0;
    max-width: 220px;
}

.guide-quote .quote-mark {
    color: var(--color-primary, #00792E);
    font-size: 1.2em;
    font-weight: var(--font-weight-bold, 700);
}

/* Responsive design */
@media (min-width: 769px) {
    .guides-widget {
        padding: var(--section-padding-desktop, 80px) 0;
    }

    .guide-photo {
        width: 180px;
        height: 180px;
    }
}

@media (max-width: 1024px) {
    .guides-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .guides-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg, 24px);
    }

    .guide-photo {
        width: 120px;
        height: 120px;
    }

    .guides-title {
        font-size: clamp(24px, 5vw, 36px);
    }

    .guides-subtitle {
        font-size: clamp(18px, 4vw, 24px);
        margin-bottom: var(--spacing-xl, 32px);
    }

    .guide-quote {
        font-size: 13px;
        max-width: none;
    }
}

@media (max-width: 480px) {
    .guide-photo {
        width: 100px;
        height: 100px;
    }

    .guide-name {
        font-size: var(--font-size-base, 16px);
    }

    .guide-position {
        font-size: var(--font-size-sm, 14px);
    }
}

/* Animation on scroll (optional) */
@media (prefers-reduced-motion: no-preference) {
    .guides-widget .guides-grid {
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
