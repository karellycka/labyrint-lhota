<?php
/**
 * Sticky Cards Widget Component
 * 
 * CSS sticky karty, které se na sebe skládají při scrollování.
 * Nadpis zůstává fixní na straně (vlevo/vpravo od karet).
 *
 * Parametry:
 * @var string $sectionTitle - Nadpis sekce
 * @var string $titlePosition - Pozice nadpisu ('left' nebo 'right')
 * @var string $sectionBackgroundColor - Barva pozadí sekce
 * @var array $cards - Pole karet
 *   - title: string - Nadpis karty
 *   - text: string - Text karty (HTML)
 *   - backgroundColor: string - CSS barva pozadí
 *   - rotation: string - Rotace karty (none, slight-left, slight-right)
 *   - buttonText: string - Text tlačítka (volitelné)
 *   - buttonUrl: string - URL tlačítka (volitelné)
 */

$sectionTitle = $sectionTitle ?? '';
$titlePosition = $titlePosition ?? 'right';
$sectionBackgroundColor = $sectionBackgroundColor ?? 'var(--color-dark)';
$cards = $cards ?? [];

// Determine if dark background for text color
$isDarkBg = in_array($sectionBackgroundColor, ['var(--color-dark)', 'var(--color-primary)']);
$titleTextColor = $isDarkBg ? 'white' : 'var(--color-dark)';

// Rotations mapping
$rotations = [
    'none' => '0deg',
    'slight-left' => '-3deg',
    'slight-right' => '3deg'
];
?>

<section class="sticky-cards-section" style="background-color: <?= e($sectionBackgroundColor) ?>;">
    <div class="sticky-cards-container <?= $titlePosition === 'left' ? 'title-left' : 'title-right' ?>">
        
        <!-- Cards Column -->
        <div class="sticky-cards-column">
            <?php foreach ($cards as $index => $card): 
                $rotation = $rotations[$card['rotation'] ?? 'none'] ?? '0deg';
                $zIndex = 10 + $index;
                $hasText = !empty(trim(strip_tags($card['text'] ?? '')));
                $hasButton = !empty($card['buttonText']) && !empty($card['buttonUrl']);
                $titleOnly = !$hasText && !$hasButton;
            ?>
            <figure class="sticky-card-wrapper" style="z-index: <?= $zIndex ?>;">
                <article class="sticky-card<?= $titleOnly ? ' title-only' : '' ?>" style="background-color: <?= e($card['backgroundColor'] ?? 'var(--color-primary)') ?>; transform: rotate(<?= $rotation ?>);">
                    <h3 class="sticky-card-title"><?= e($card['title'] ?? '') ?></h3>
                    <?php if ($hasText): ?>
                    <div class="sticky-card-text"><?= $card['text'] ?? '' ?></div>
                    <?php endif; ?>
                    <?php if ($hasButton): ?>
                    <a href="<?= e($card['buttonUrl']) ?>" class="sticky-card-button">
                        <?= e($card['buttonText']) ?>
                    </a>
                    <?php endif; ?>
                </article>
            </figure>
            <?php endforeach; ?>
        </div>
        
        <!-- Title Column -->
        <?php if (!empty($sectionTitle)): ?>
        <div class="sticky-cards-title-column">
            <div class="sticky-cards-title" style="color: <?= $titleTextColor ?>;">
                <h2><?= e($sectionTitle) ?></h2>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<style>
/* =====================================================
   STICKY CARDS WIDGET
   Karty se skládají na sebe při scrollování pomocí CSS sticky
   ===================================================== */

.sticky-cards-section {
    width: 100%;
    /* DŮLEŽITÉ: žádný overflow:hidden - rozbije sticky! */
}

.sticky-cards-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 var(--container-padding, 20px);
    gap: var(--spacing-lg, 24px);
}

/* Title position variants */
.sticky-cards-container.title-left {
    flex-direction: row-reverse;
}

.sticky-cards-container.title-right {
    flex-direction: row;
}

/* Cards Column - musí mít dostatečnou výšku pro scroll */
.sticky-cards-column {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
    max-width: 600px;
}

/* Sticky Card Wrapper - KLÍČOVÉ: sticky + 100vh = karty se skládají */
.sticky-card-wrapper {
    position: sticky;
    top: 0;
    height: 100vh;
    display: grid;
    place-content: center;
    padding: var(--spacing-lg, 24px);
    margin: 0;
}

/* Card styling */
.sticky-card {
    width: 100%;
    max-width: 480px;
    min-width: 320px;
    min-height: 280px;
    padding: var(--spacing-xl, 32px) var(--spacing-lg, 24px);
    border-radius: var(--border-radius-lg, 16px);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md, 16px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255,255,255,0.1);
}

.sticky-card-title {
    font-size: var(--font-size-h3, 28px);
    font-weight: var(--font-weight-semibold, 600);
    color: white;
    margin: 0;
    line-height: 1.2;
}

/* Title-only card - vertically centered */
.sticky-card.title-only {
    justify-content: center;
    text-align: center;
}

.sticky-card.title-only .sticky-card-title {
    margin: 0;
}

.sticky-card-text {
    font-size: var(--font-size-body, 16px);
    line-height: var(--line-height-relaxed, 1.6);
    color: rgba(255, 255, 255, 0.9);
    flex: 1;
}

.sticky-card-text p {
    margin: 0 0 var(--spacing-sm, 8px) 0;
}

.sticky-card-text p:last-child {
    margin-bottom: 0;
}

.sticky-card-button {
    display: inline-block;
    margin-top: auto;
    padding: var(--spacing-sm, 12px) var(--spacing-md, 16px);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    text-decoration: none;
    border-radius: var(--border-radius-md, 8px);
    font-weight: var(--font-weight-medium, 500);
    font-size: var(--font-size-small, 14px);
    transition: background-color 0.2s ease, transform 0.2s ease;
    width: fit-content;
}

.sticky-card-button:hover {
    background-color: rgba(0, 0, 0, 1);
    transform: translateY(-2px);
}

/* Title Column - sticky na střed obrazovky */
.sticky-cards-title-column {
    flex: 0 0 390px;
    height: 100vh;
    position: sticky;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sticky-cards-title {
    width: 390px;
    padding: var(--spacing-xl, 32px);
    text-align: left;
}

.sticky-cards-title h2 {
    font-size: var(--font-size-h2, 36px);
    font-weight: var(--font-weight-bold, 700);
    line-height: var(--line-height-tight, 1.25);
    margin: 0;
}

/* =====================================================
   RESPONSIVE DESIGN
   ===================================================== */

@media (max-width: 1024px) {
    .sticky-cards-container {
        flex-direction: column !important;
        padding: var(--spacing-xl, 32px) var(--container-padding, 20px);
    }
    
    /* Nadpis NAD kartami na mobilu */
    .sticky-cards-title-column {
        flex: none;
        height: auto;
        position: relative;
        top: auto;
        order: -1; /* Posune nadpis před karty */
        width: 100%;
    }
    
    .sticky-cards-title {
        padding: var(--spacing-lg, 24px) 0;
        text-align: center;
    }
    
    .sticky-cards-column {
        max-width: 100%;
        width: 100%;
        order: 1;
    }
    
    .sticky-card-wrapper {
        height: 85vh;
    }
    
    .sticky-card {
        max-width: 100%;
        min-width: unset;
    }
}

@media (max-width: 640px) {
    .sticky-card-wrapper {
        height: 75vh;
        padding: var(--spacing-md, 16px);
    }
    
    .sticky-card {
        min-height: 220px;
        padding: var(--spacing-lg, 24px) var(--spacing-md, 16px);
        /* Na mobilu bez rotace pro lepší UX */
        transform: rotate(0deg) !important;
    }
    
    .sticky-card-title {
        font-size: var(--font-size-h4, 24px);
    }
    
    .sticky-cards-title h2 {
        font-size: var(--font-size-h3, 28px);
    }
}

/* Preference pro snížený pohyb */
@media (prefers-reduced-motion: reduce) {
    .sticky-card,
    .sticky-card-button {
        transition: none;
    }
}
</style>
