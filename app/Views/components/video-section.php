<?php
/**
 * Video Section Component
 * Sekce s YouTube video embedem
 *
 * Props:
 * - $videoId (string) - YouTube video ID
 * - $title (string) - Nadpis sekce (volitelný)
 * - $description (string) - Popis pod nadpisem (volitelný)
 * - $aspectRatio (string) - Poměr stran (16:9, 4:3, 1:1) (default: 16:9)
 * - $thumbnail (string) - Custom thumbnail obrázek (volitelný)
 * - $autoplay (bool) - Automatické přehrávání (default: false)
 */

$aspectRatio = $aspectRatio ?? '16:9';
$autoplay = $autoplay ?? false;
$thumbnail = $thumbnail ?? null;

$aspectClass = 'aspect-' . str_replace(':', '-', $aspectRatio);
$autoplayParam = $autoplay ? '&autoplay=1' : '';
?>

<div class="video-section">
    <?php if (isset($title)): ?>
        <h3 class="video-title"><?= e($title) ?></h3>
    <?php endif; ?>

    <?php if (isset($description)): ?>
        <p class="video-description"><?= e($description) ?></p>
    <?php endif; ?>

    <div class="video-wrapper <?= $aspectClass ?>">
        <?php if ($thumbnail): ?>
            <!-- Lazy load video s custom thumbnailem -->
            <div class="video-thumbnail" onclick="loadVideo(this)" style="background-image: url('<?= e($thumbnail) ?>')">
                <button class="video-play-btn" aria-label="Přehrát video">
                    <svg width="68" height="48" viewBox="0 0 68 48" fill="none">
                        <path d="M66.5 24c0 13.255-10.745 24-24 24H25.5C12.245 48 1.5 37.255 1.5 24S12.245 0 25.5 0h17c13.255 0 24 10.745 24 24z" fill="#FF0000"/>
                        <path d="M45 24L27 14v20l18-10z" fill="#fff"/>
                    </svg>
                </button>
            </div>
            <div class="video-iframe-container" style="display: none">
                <iframe
                    data-src="https://www.youtube.com/embed/<?= e($videoId) ?>?rel=0<?= $autoplayParam ?>"
                    title="<?= e($title ?? 'YouTube video') ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        <?php else: ?>
            <!-- Direct embed -->
            <iframe
                src="https://www.youtube.com/embed/<?= e($videoId) ?>?rel=0<?= $autoplayParam ?>"
                title="<?= e($title ?? 'YouTube video') ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy">
            </iframe>
        <?php endif; ?>
    </div>
</div>

<?php if ($thumbnail): ?>
<script>
function loadVideo(element) {
    const container = element.nextElementSibling;
    const iframe = container.querySelector('iframe');
    iframe.src = iframe.dataset.src;
    container.style.display = 'block';
    element.style.display = 'none';
}
</script>
<?php endif; ?>
