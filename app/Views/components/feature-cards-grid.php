<?php
/**
 * Feature Cards Grid (2x2)
 * Widget pro zobrazení 2-4 barevných karet s textem
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce (volitelné)
 * @var string $sectionSubtitle - Podnadpis sekce (volitelné)
 * @var array $cards - Pole karet [{'title', 'text', 'backgroundColor'}]
 */

// Default values
$sectionTitle = $sectionTitle ?? '';
$sectionSubtitle = $sectionSubtitle ?? '';
$cards = $cards ?? [];
?>

<section class="section bg-light">
    <div class="container">
        <?php if (!empty($sectionTitle)): ?>
            <h2 class="section-title"><?= e($sectionTitle) ?></h2>
        <?php endif; ?>

        <?php if (!empty($sectionSubtitle)): ?>
            <h3><?= e($sectionSubtitle) ?></h3>
        <?php endif; ?>

        <?php if (!empty($cards)): ?>
        <div class="features-grid">
            <?php foreach ($cards as $card): ?>
            <div class="feature-card" style="background: <?= e($card['backgroundColor'] ?? 'var(--color-primary)') ?>; padding: 40px;">
                <div class="feature-card-content">
                    <h3 class="feature-card-title"><?= e($card['title'] ?? '') ?></h3>
                    <div class="feature-card-text"><?= $card['text'] ?? '' ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
