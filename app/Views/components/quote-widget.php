<?php
/**
 * Quote Widget Component
 *
 * Parametry:
 * @var string $text - Text citace
 * @var string $author - Autor citace
 * @var string $role - Role/pozice autora (volitelné)
 */
?>

<section class="quote-widget">
    <div class="container">
        <div class="quote-content">
            <blockquote class="quote-text">
                <?= e($text) ?>
            </blockquote>
            <div class="quote-author">
                <span class="author-name"><?= e($author) ?></span>
                <?php if (!empty($role)): ?>
                    <span class="author-role"><?= e($role) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.quote-widget {
    background-color: var(--color-primary, #00792E);
    background: linear-gradient(135deg, var(--color-primary, #00792E) 0%, #005a22 100%);
    padding: var(--section-padding-mobile, 60px) 0;
    position: relative;
    overflow: visible;
}

/* Dekorativní uvozovky v levém horním rohu */
.quote-decoration-left {
    display: block;
    position: absolute;
    top: 30px;
    left: 30px;
    width: 150px;
    height: 150px;
    font-size: 150px;
    line-height: 150px;
    color: rgba(255, 255, 255, 0.2);
    font-family: Georgia, serif;
    font-weight: bold;
    pointer-events: none;
    z-index: 1;
}

/* Dekorativní uvozovky v pravém dolním rohu */
.quote-decoration-right {
    display: block;
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 150px;
    height: 150px;
    font-size: 150px;
    line-height: 150px;
    color: rgba(255, 255, 255, 0.2);
    font-family: Georgia, serif;
    font-weight: bold;
    pointer-events: none;
    z-index: 1;
    transform: rotate(180deg);
}

.quote-widget .container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--container-padding, 20px);
    position: relative;
}

.quote-content {
    text-align: center;
    position: relative;
}

.quote-text {
    font-size: clamp(20px, 4vw, 28px);
    line-height: var(--line-height-relaxed, 1.75);
    color: white;
    margin: 0 0 var(--spacing-2xl, 48px) 0;
    padding: 0;
    font-weight: var(--font-weight-normal, 400);
    font-style: italic;
    position: relative;
}

.quote-author {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm, 8px);
    color: white;
}

.author-name {
    font-size: var(--font-size-lg, 18px);
    font-weight: var(--font-weight-semibold, 600);
    color: white;
}

.author-role {
    font-size: var(--font-size-base, 16px);
    font-weight: var(--font-weight-normal, 400);
    color: rgba(255, 255, 255, 0.9);
}

/* Responsive design */
@media (min-width: 769px) {
    .quote-widget {
        padding: var(--section-padding-desktop, 80px) 0;
    }
}

@media (max-width: 768px) {
    .quote-text {
        font-size: 20px;
        margin-bottom: var(--spacing-xl, 32px);
    }

    .author-name {
        font-size: var(--font-size-base, 16px);
    }

    .author-role {
        font-size: var(--font-size-sm, 14px);
    }
}

/* Animation on scroll (optional) */
@media (prefers-reduced-motion: no-preference) {
    .quote-widget .quote-content {
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
