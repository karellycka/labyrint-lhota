<?php
/**
 * Asymmetric Text + Video
 * Asymetrický layout s barevným šikmým blokem textu a překrývajícím se videem
 *
 * Očekávané proměnné:
 * @var string $text - HTML obsah textového bloku
 * @var string $textBackgroundColor - Barva pozadí textového bloku (CSS variable)
 * @var string $videoId - YouTube video ID
 * @var string $aspectRatio - Poměr stran videa (16:9, 4:3)
 */

// Default values
$text = $text ?? '';
$textBackgroundColor = $textBackgroundColor ?? 'var(--color-primary, #00792E)';
$videoId = $videoId ?? '';
$aspectRatio = $aspectRatio ?? '16:9';
?>

<!-- Asymetrická sekce: Text + Video s překryvem -->
<section class="section" id="about">
    <div class="container">
        <div class="overlapping-layout">
            <!-- Barevný blok s textem (nástřel) -->
            <div class="color-block-skewed" style="background: <?= e($textBackgroundColor) ?>;">
                <div class="color-block-content">
                    <?= $text ?>
                </div>
            </div>

            <!-- Video (překrývá barevný blok) -->
            <?php if (!empty($videoId)): ?>
            <div class="video-overlay">
                <?php
                // Include video section component
                $title = null; // No title for this layout
                $description = null;
                $autoplay = false;
                include __DIR__ . '/video-section.php';
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
