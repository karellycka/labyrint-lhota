<?php
/**
 * Widget Helper Functions
 * Funkcionalit pro rendering znovupoužitelných komponent
 */

/**
 * Renderuje tlačítko
 *
 * @param string $text - Text tlačítka
 * @param string $url - URL odkaz
 * @param string $variant - primary, secondary, outline, text
 * @param array $options - Dodatečné možnosti (class, id, target, icon)
 * @return string HTML
 */
function renderButton(string $text, string $url = '#', string $variant = 'primary', array $options = []): string
{
    $classes = ['btn', "btn-{$variant}"];

    if (isset($options['class'])) {
        $classes[] = $options['class'];
    }

    $classStr = implode(' ', $classes);
    $idAttr = isset($options['id']) ? ' id="' . e($options['id']) . '"' : '';
    $targetAttr = isset($options['target']) ? ' target="' . e($options['target']) . '"' : '';
    $icon = isset($options['icon']) ? '<span class="btn-icon">' . $options['icon'] . '</span>' : '';

    return <<<HTML
<a href="{$url}" class="{$classStr}"{$idAttr}{$targetAttr}>
    {$icon}
    <span>{$text}</span>
</a>
HTML;
}

/**
 * Renderuje kartu (card)
 *
 * @param array $data - ['title', 'content', 'image', 'url', 'footer']
 * @param array $options - ['class', 'shadow', 'hover']
 * @return string HTML
 */
function renderCard(array $data, array $options = []): string
{
    $classes = ['card'];

    if ($options['shadow'] ?? false) {
        $classes[] = 'shadow-md';
    }

    if ($options['hover'] ?? true) {
        $classes[] = 'card-hover';
    }

    if (isset($options['class'])) {
        $classes[] = $options['class'];
    }

    $classStr = implode(' ', $classes);

    $image = '';
    if (isset($data['image'])) {
        $image = '<div class="card-image"><img src="' . e($data['image']) . '" alt="' . e($data['title'] ?? '') . '" loading="lazy"></div>';
    }

    $title = '';
    if (isset($data['title'])) {
        $title = '<h3 class="card-title">' . e($data['title']) . '</h3>';
    }

    $content = '';
    if (isset($data['content'])) {
        $content = '<div class="card-content">' . $data['content'] . '</div>';
    }

    $footer = '';
    if (isset($data['footer'])) {
        $footer = '<div class="card-footer">' . $data['footer'] . '</div>';
    }

    $url = $data['url'] ?? null;
    $wrapperStart = $url ? '<a href="' . e($url) . '" class="card-link">' : '';
    $wrapperEnd = $url ? '</a>' : '';

    return <<<HTML
<div class="{$classStr}">
    {$wrapperStart}
    {$image}
    <div class="card-body">
        {$title}
        {$content}
    </div>
    {$footer}
    {$wrapperEnd}
</div>
HTML;
}

/**
 * Renderuje grid kontejner
 *
 * @param array $items - Pole prvků k zobrazení
 * @param int $columns - Počet sloupců (1-4)
 * @param string $gap - Mezera mezi prvky (sm, md, lg)
 * @return string HTML
 */
function renderGrid(array $items, int $columns = 3, string $gap = 'md'): string
{
    $gapClass = "grid-gap-{$gap}";
    $colClass = "grid-cols-{$columns}";

    $itemsHtml = implode("\n", $items);

    return <<<HTML
<div class="grid {$colClass} {$gapClass}">
    {$itemsHtml}
</div>
HTML;
}

/**
 * Renderuje sekci s obsahem
 *
 * @param array $data - ['title', 'content', 'background', 'gradient']
 * @param array $options - ['class', 'id', 'padding']
 * @return string HTML
 */
function renderSection(array $data, array $options = []): string
{
    $classes = ['section'];

    if (isset($data['background'])) {
        $classes[] = 'bg-' . $data['background'];
    }

    if (isset($data['gradient'])) {
        $classes[] = 'gradient-' . $data['gradient'];
    }

    if (isset($options['class'])) {
        $classes[] = $options['class'];
    }

    $classStr = implode(' ', $classes);
    $idAttr = isset($options['id']) ? ' id="' . e($options['id']) . '"' : '';

    $title = '';
    if (isset($data['title'])) {
        $title = '<h2 class="section-title">' . e($data['title']) . '</h2>';
    }

    $content = $data['content'] ?? '';

    return <<<HTML
<section class="{$classStr}"{$idAttr}>
    <div class="container">
        {$title}
        {$content}
    </div>
</section>
HTML;
}

/**
 * Renderuje kontejner pro dvousloupcový layout
 *
 * @param string $leftContent - Obsah levého sloupce
 * @param string $rightContent - Obsah pravého sloupce
 * @param array $options - ['ratio' => '1:1', 'gap' => 'lg', 'reverse' => false]
 * @return string HTML
 */
function renderTwoColumn(string $leftContent, string $rightContent, array $options = []): string
{
    $gap = $options['gap'] ?? 'lg';
    $reverse = $options['reverse'] ?? false;
    $ratio = $options['ratio'] ?? '1:1';

    $classes = ['two-column', "gap-{$gap}"];

    if ($reverse) {
        $classes[] = 'reverse-mobile';
    }

    // Ratio classes pro custom layout
    if ($ratio === '2:1') {
        $classes[] = 'ratio-2-1';
    } elseif ($ratio === '1:2') {
        $classes[] = 'ratio-1-2';
    }

    $classStr = implode(' ', $classes);

    return <<<HTML
<div class="{$classStr}">
    <div class="column column-left">
        {$leftContent}
    </div>
    <div class="column column-right">
        {$rightContent}
    </div>
</div>
HTML;
}

/**
 * Renderuje YouTube video embed
 *
 * @param string $videoId - YouTube video ID
 * @param array $options - ['title', 'aspect' => '16:9', 'autoplay' => false]
 * @return string HTML
 */
function renderYouTubeVideo(string $videoId, array $options = []): string
{
    $title = $options['title'] ?? 'YouTube video';
    $aspect = $options['aspect'] ?? '16:9';
    $autoplay = $options['autoplay'] ?? false;

    $aspectClass = str_replace(':', '-', $aspect);
    $autoplayParam = $autoplay ? '&autoplay=1' : '';

    return <<<HTML
<div class="video-container aspect-{$aspectClass}">
    <iframe
        src="https://www.youtube.com/embed/{$videoId}?rel=0{$autoplayParam}"
        title="{$title}"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
        loading="lazy">
    </iframe>
</div>
HTML;
}

/**
 * Renderuje responsive image s srcset
 *
 * @param string $src - Základní cesta k obrázku
 * @param string $alt - Alt text
 * @param array $sizes - Pole velikostí ['400w', '800w', '1200w']
 * @param string $sizeAttr - sizes attribute (např. '(max-width: 768px) 100vw, 50vw')
 * @return string HTML
 */
function renderResponsiveImage(string $src, string $alt, array $sizes = [], string $sizeAttr = '100vw'): string
{
    if (empty($sizes)) {
        return '<img src="' . e($src) . '" alt="' . e($alt) . '" loading="lazy">';
    }

    // Vytvoření srcset z pole velikostí
    $srcsetParts = [];
    foreach ($sizes as $size) {
        $width = (int) $size;
        $srcsetParts[] = $src . "?w={$width} {$size}";
    }

    $srcset = implode(', ', $srcsetParts);

    return <<<HTML
<img
    src="{$src}"
    srcset="{$srcset}"
    sizes="{$sizeAttr}"
    alt="{$alt}"
    loading="lazy">
HTML;
}

/**
 * Renderuje ikonu
 *
 * @param string $name - Název ikony (pro emoji nebo SVG)
 * @param array $options - ['size' => 'md', 'class' => '']
 * @return string HTML
 */
function renderIcon(string $name, array $options = []): string
{
    $size = $options['size'] ?? 'md';
    $class = $options['class'] ?? '';

    $classes = ['icon', "icon-{$size}"];
    if ($class) {
        $classes[] = $class;
    }

    $classStr = implode(' ', $classes);

    // Jednoduchá emoji ikona (pro hygge styl)
    $icons = [
        'arrow-right' => '→',
        'arrow-left' => '←',
        'check' => '✓',
        'close' => '✕',
        'heart' => '♥',
        'star' => '★',
        'calendar' => '📅',
        'location' => '📍',
        'mail' => '✉',
        'phone' => '📞',
        'menu' => '☰'
    ];

    $iconContent = $icons[$name] ?? $name;

    return '<span class="' . $classStr . '">' . $iconContent . '</span>';
}

/**
 * Renderuje citaci (quote widget)
 *
 * @param string $text - Text citace
 * @param string $author - Autor citace
 * @param string $role - Role/pozice autora (volitelné)
 * @return void Přímo vypisuje HTML
 */
function renderQuote(string $text, string $author, string $role = ''): void
{
    include __DIR__ . '/../Views/components/quote-widget.php';
}

/**
 * Renderuje CTA banner s obrázkem na pozadí
 *
 * @param string $backgroundImage - URL obrázku na pozadí
 * @param string $title - Hlavní nadpis (H2)
 * @param string $subtitle - Podnadpis (H3)
 * @param string $buttonText - Text tlačítka
 * @param string $buttonUrl - URL odkaz tlačítka
 * @param string $buttonVariant - Varianta tlačítka (primary, secondary, outline) - volitelné
 * @param bool $overlay - Tmavý overlay přes obrázek (default: true)
 * @return void Přímo vypisuje HTML
 */
function renderCtaImageBanner(
    string $backgroundImage,
    string $title,
    string $subtitle,
    string $buttonText,
    string $buttonUrl,
    string $buttonVariant = 'primary',
    bool $overlay = true
): void {
    include __DIR__ . '/../Views/components/cta-image-banner.php';
}
