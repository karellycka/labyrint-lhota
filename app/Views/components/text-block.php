<?php
/**
 * Text Block Component
 * Blok s textem, nadpisem a volitelným obrázkem
 *
 * Props:
 * - $title (string) - Nadpis
 * - $content (string) - Obsah (může obsahovat HTML)
 * - $alignment (string) - left, center, right (default: left)
 * - $maxWidth (string) - Maximální šířka (sm, md, lg, full) (default: full)
 * - $class (string) - Dodatečné CSS třídy
 */

$alignment = $alignment ?? 'left';
$maxWidth = $maxWidth ?? 'full';
$class = $class ?? '';

$alignmentClass = "text-{$alignment}";
$widthClass = "max-width-{$maxWidth}";
?>

<div class="text-block <?= $alignmentClass ?> <?= $widthClass ?> <?= $class ?>">
    <?php if (isset($title)): ?>
        <h2 class="text-block-title"><?= e($title) ?></h2>
    <?php endif; ?>

    <?php if (isset($content)): ?>
        <div class="text-block-content">
            <?= $content ?>
        </div>
    <?php endif; ?>
</div>
