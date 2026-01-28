<?php
/**
 * Highlights Widget Component
 *
 * Parametry:
 * @var array $highlights - Pole s položkami highlights
 *   - value: string - Velká hodnota (číslo)
 *   - label: string - Popisek pod hodnotou
 */

$highlights = $highlights ?? [];
?>

<section class="highlights-widget">
    <div class="container">
        <div class="highlights-grid">
            <?php foreach ($highlights as $item): ?>
            <div class="highlight-item">
                <span class="highlight-value"><?= e($item['value'] ?? '') ?></span>
                <span class="highlight-label"><?= e($item['label'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.highlights-widget {
    background-color: var(--color-primary, #00792E);
    background: linear-gradient(135deg, var(--color-primary, #00792E) 0%, #005a22 100%);
    padding: var(--section-padding-mobile, 60px) 0;
    position: relative;
}

.highlights-widget .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--container-padding, 20px);
}

.highlights-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-2xl, 48px);
    text-align: center;
}

.highlight-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm, 8px);
}

.highlight-value {
    font-size: var(--font-size-h1, 48px);
    font-weight: var(--font-weight-bold, 700);
    color: white;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.highlight-label {
    font-size: clamp(14px, 2vw, 18px);
    font-weight: var(--font-weight-normal, 400);
    color: rgba(255, 255, 255, 0.9);
    line-height: var(--line-height-relaxed, 1.6);
    max-width: 200px;
}

/* Responsive design */
@media (min-width: 769px) {
    .highlights-widget {
        padding: var(--section-padding-desktop, 80px) 0;
    }
}

@media (max-width: 768px) {
    .highlights-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-xl, 32px);
    }

    .highlight-label {
        font-size: 16px;
        max-width: none;
    }
}

/* Animation on scroll (optional) */
@media (prefers-reduced-motion: no-preference) {
    .highlights-widget .highlights-grid {
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
