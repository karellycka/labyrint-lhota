<?php
/**
 * Feature Cards with Image (1x3)
 * Widget pro zobrazení 1-3 karet s obrázkem nahoře, textem a barvou pozadí
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce (volitelné)
 * @var string $sectionSubtitle - Podnadpis sekce (volitelné)
 * @var array $cards - Pole karet [{'image', 'title', 'tags', 'text', 'backgroundColor', 'buttonText', 'buttonUrl'}]
 *       tags - Pole štítků [{'text', 'tagColor'}]
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
        <div class="grid grid-cols-3 grid-gap-lg">
            <?php foreach ($cards as $card): ?>
            <?php
            // Handle image URL - full URL (Cloudinary) or asset path
            $cardImageUrl = '';
            if (!empty($card['image'])) {
                $cardImageUrl = str_starts_with($card['image'], 'http')
                    ? $card['image']
                    : asset($card['image']);
            }
            ?>
            <div class="feature-card-with-image" style="background: <?= e($card['backgroundColor'] ?? 'var(--color-primary)') ?>; border-radius: var(--border-radius, 16px); overflow: hidden; color: var(--color-text, #2C323A); display: flex; flex-direction: column;">
                <?php if (!empty($cardImageUrl)): ?>
                <div class="feature-card-image" style="width: 100%; height: 250px; overflow: hidden; flex-shrink: 0;">
                    <img src="<?= e($cardImageUrl) ?>" alt="<?= e($card['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <?php endif; ?>

                <div class="feature-card-content" style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
                    <div style="flex: 1;">
                        <?php if (!empty($card['title'])): ?>
                            <h4 class="feature-card-title"><?= e($card['title']) ?></h4>
                        <?php endif; ?>

                        <?php if (!empty($card['tags']) && is_array($card['tags'])): ?>
                            <div class="feature-card-tags">
                                <?php foreach ($card['tags'] as $tag): ?>
                                    <span class="feature-card-tag" style="background: <?= e($tag['tagColor'] ?? 'var(--color-primary)') ?>;"><?= e($tag['text'] ?? '') ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($card['text'])): ?>
                            <div style="font-size: var(--font-size-base, 16px); line-height: 1.6; opacity: 0.95;">
                                <?= $card['text'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($card['buttonText']) && !empty($card['buttonUrl'])): ?>
                        <div style="margin-top: 20px; text-align: center;">
                            <a href="<?= url($card['buttonUrl']) ?>" class="btn btn-primary">
                                <?= e($card['buttonText']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
