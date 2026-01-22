<?php
/**
 * Feature Cards Universal
 * Univerzální widget pro zobrazení karet s flexibilním počtem sloupců
 *
 * Očekávané proměnné:
 * @var string $sectionTitle - Nadpis sekce (volitelné)
 * @var string $sectionSubtitle - Podnadpis sekce (volitelné)
 * @var int $columns - Počet sloupců (1-4), povinné
 * @var array $cards - Pole karet [{'image', 'title', 'subtitle', 'text', 'backgroundColor'}]
 *                     - image: URL obrázku (volitelné)
 *                     - title: Nadpis karty (povinné)
 *                     - subtitle: Podnadpis karty (volitelné)
 *                     - text: Text karty (povinné)
 *                     - backgroundColor: CSS barva pozadí (povinné)
 */

// Default values
$sectionTitle = $sectionTitle ?? '';
$sectionSubtitle = $sectionSubtitle ?? '';
$columns = $columns ?? 3; // Fallback na 3 sloupce, pokud není nastaveno
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
        <div class="grid grid-cols-<?= (int)$columns ?> grid-gap-lg">
            <?php foreach ($cards as $card): ?>
            <?php
            // Handle image URL - if relative path, prepend BASE_URL
            $cardImageUrl = '';
            if (!empty($card['image'])) {
                $cardImageUrl = str_starts_with($card['image'], 'http')
                    ? $card['image']
                    : BASE_URL . $card['image'];
            }
            $hasImage = !empty($cardImageUrl);
            ?>
            <div class="feature-card-universal" style="background: <?= e($card['backgroundColor'] ?? 'var(--color-primary)') ?>; border-radius: var(--border-radius, 16px); overflow: hidden; color: var(--color-bg-white, #FDFBF7); display: flex; flex-direction: column;">
                <?php if ($hasImage): ?>
                <div class="feature-card-image" style="width: 100%; height: 250px; overflow: hidden; flex-shrink: 0;">
                    <img src="<?= e($cardImageUrl) ?>" alt="<?= e($card['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <?php endif; ?>

                <div class="feature-card-content" style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
                    <div style="flex: 1;">
                        <?php if (!empty($card['title'])): ?>
                            <h3 class="feature-card-title"><?= e($card['title']) ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($card['subtitle'])): ?>
                            <h4 class="feature-card-subtitle"><?= e($card['subtitle']) ?></h4>
                        <?php endif; ?>

                        <?php if (!empty($card['text'])): ?>
                            <div class="feature-card-text" style="font-size: var(--font-size-base, 16px); line-height: 1.6; opacity: 0.95;">
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
