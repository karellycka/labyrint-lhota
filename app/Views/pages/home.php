<?php
/**
 * Homepage - Škola Labyrint
 * Nový design s hero sekcí a dvousloupcovým layoutem
 */

// Hero component s rotujícím textem
$title = 'Připravujeme děti na';
// $backgroundImage = asset('images/hero/hero-bg.jpg'); // Již nepotřebujeme - používá se slideshow
$rotatingText = [
    'reálný život',
    'střední školu',
    'zodpovědnou dospělost',
    'celoživotní zápal pro učení'
];
$ctaButtons = [
    [
        'text' => 'Zjistit více',
        'url' => '#about',
        'variant' => 'primary'
    ]
];
$overlay = true;
$height = 'full';
?>

<?php include __DIR__ . '/../components/hero.php'; ?>

<!-- Asymetrická sekce: Text + Video s překryvem -->
<section class="section" id="about">
    <div class="container">
        <div class="overlapping-layout">
            <!-- Barevný blok s textem (nástřel) -->
            <div class="color-block-skewed">
                <div class="color-block-content">
                    <h2>Tvoříme bezpečný a podporující prostor, kde děti mohou rozvíjet svou přirozenou zvídavost a touhu učit se.</h2>
                </div>
            </div>

            <!-- Video (překrývá barevný blok) -->
            <div class="video-overlay">
                <?php
                // Vyčistit proměnné z hero sekce
                unset($title, $backgroundImage, $rotatingText, $ctaButtons, $overlay, $height);

                // Nastavení proměnných pro video komponentu
                $videoId = 'SOGz3Vz9aNs';
                $aspectRatio = '16:9';
                // $title není nastavený - video bude bez nadpisu

                include __DIR__ . '/../components/video-section.php';
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Sekce: Vracíme do školy smysluplnost -->
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title">Vracíme do školy smysluplnost</h2>
        <h3>Učení je život a život je učení.</h3>

        <div class="features-grid">
            <!-- Karta 1 - Písková -->
            <div class="feature-card" style="background: var(--color-brown, #9C8672);">
                <div class="feature-card-content">
                    <h3 class="feature-card-title">Jsme malá škola, velká duchem</h3>
                    <p class="feature-card-text">
                        Tvoříme školu, kde se dobře učí – a dobře žije. Je nás dohromady přes 120 – od 1. do 9. ročníku. Místo autoritářství a hierarchie pěstujeme vztahy, osobní důvěru a lidskost.
                    </p>
                </div>
            </div>

            <!-- Karta 2 - Medová -->
            <div class="feature-card" style="background: var(--color-yellow, #D4A574);">
                <div class="feature-card-content">
                    <h3 class="feature-card-title">10+ let děláme Montessori pedagogiku</h3>
                    <p class="feature-card-text">
                        Vedeme děti k tomu, aby samy věděly, co dělají a proč – a převzaly za to odpovědnost. Montessori není jen o dřevěných pomůckách a slovním hodnocení. Je to vnitřní postoj plný respektu, jasných hranic a smysluplné práce.
                    </p>
                </div>
            </div>

            <!-- Karta 3 - Fjordová -->
            <div class="feature-card" style="background: var(--color-blue, #8BA5B2);">
                <div class="feature-card-content">
                    <h3 class="feature-card-title">Naši absolventi jdou příkladem</h3>
                    <p class="feature-card-text">
                        Absolventi Labyrintu mají vědomosti i charakter. Jsou samostatné, odolné, ukotvené a otevřené. Své schopnosti dokáží využít ve prospěch svůj, ale i pro dobro společnosti.
                    </p>
                </div>
            </div>

            <!-- Karta 4 - Terakota -->
            <div class="feature-card" style="background: var(--color-red, #B8664D);">
                <div class="feature-card-content">
                    <h3 class="feature-card-title">Pracovitost a smysluplnost máme v krvi</h3>
                    <p class="feature-card-text">
                        Klademe důraz na samostatnou práci vlastním tempem i spolupráci napříč školou. Pěstujeme hard i soft skills, máme hodně angličtiny a milujeme vše, co můžeme rovnou převést do praxe.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($recentPosts)): ?>
<!-- Recent Blog Posts (volitelné - zobrazí se pokud jsou data) -->
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title"><?= __('homepage.recent_posts', 'Novinky') ?></h2>

        <div class="grid grid-cols-3 grid-gap-lg">
            <?php foreach (array_slice($recentPosts, 0, 3) as $post): ?>
            <?php
            $cardData = [
                'title' => $post->title,
                'image' => $post->featured_image ? upload($post->featured_image) : null,
                'content' => '<p>' . e(truncate($post->excerpt ?? $post->content, 120)) . '</p>',
                'url' => url('blog/' . $post->slug),
                'footer' => '<div class="post-meta"><span>' . e($post->author_name ?? 'Admin') . '</span> • <span>' . formatDate($post->published_at) . '</span></div>'
            ];

            echo renderCard($cardData, ['shadow' => true, 'hover' => true]);
            ?>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= url('blog') ?>" class="btn btn-outline">
                <?= __('homepage.view_all_posts', 'Všechny články') ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($quote)): ?>
<!-- Quote Widget -->
<?php renderQuote($quote->text, $quote->author, $quote->role ?? ''); ?>
<?php endif; ?>

<!-- Sekce: Opravdovost spolu s odpovědností -->
<section class="section bg-light">
    <div class="container">
        <h2 class="section-title">Opravdovost spolu s odpovědností</h2>
        <h3>Propojujeme znalosti, dovednosti a hodnoty. Už od roku 2016.</h3>

        <div class="grid grid-cols-3 grid-gap-lg">
            <!-- Blok 1 - Přípravná třída -->
            <div class="feature-card-with-image" style="background: var(--color-brown, #9C8672); border-radius: var(--border-radius, 16px); overflow: hidden; color: var(--color-bg-white, #FDFBF7);">
                <div class="feature-card-image" style="width: 100%; height: 250px; overflow: hidden;">
                    <img src="<?= asset('images/skola/DSC_7507.jpg') ?>" alt="Přípravná třída: Labyrintíci" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="feature-card-content" style="padding: var(--spacing-2xl, 32px);">
                    <h4 class="feature-card-title">Přípravná třída: „Labyrintíci"</h4>
                    <h5 class="feature-card-subtitle">Přirozenost, praktičnost a hra</h5>
                    <p style="font-size: var(--font-size-base, 16px); line-height: 1.6; opacity: 0.95;">
                        Děti do učení zapojují všechny smysly, pobyt venku a praktické činnosti, které jim přináší radost ze samostatnosti.
                    </p>
                </div>
            </div>

            <!-- Blok 2 - 1.–6. ročník -->
            <div class="feature-card-with-image" style="background: var(--color-yellow, #D4A574); border-radius: var(--border-radius, 16px); overflow: hidden; color: var(--color-bg-white, #FDFBF7);">
                <div class="feature-card-image" style="width: 100%; height: 250px; overflow: hidden;">
                    <img src="<?= asset('images/skola/DSC_7397.jpg') ?>" alt="1.–6. ročník: Mravenečníci a Keprníci" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="feature-card-content" style="padding: var(--spacing-2xl, 32px);">
                    <h4 class="feature-card-title">1.–6. ročník: „Mravenečníci" a „Keprníci"</h4>
                    <h5 class="feature-card-subtitle">Vlastní tempo, kritické myšlení, věkově smíšené třídy</h5>
                    <p style="font-size: var(--font-size-base, 16px); line-height: 1.6; opacity: 0.95;">
                        Učíme děti samostatně přemýšlet a spolupracovat napříč věkem. Díky respektujícímu vedení, smysluplným činnostem a Montessori pomůckám objevují vnitřní zápal pro učení.
                    </p>
                </div>
            </div>

            <!-- Blok 3 - 7.–9. ročník -->
            <div class="feature-card-with-image" style="background: var(--color-blue, #8BA5B2); border-radius: var(--border-radius, 16px); overflow: hidden; color: var(--color-bg-white, #FDFBF7);">
                <div class="feature-card-image" style="width: 100%; height: 250px; overflow: hidden;">
                    <img src="<?= asset('images/skola/DSC_7587.jpg') ?>" alt="7.–9. ročník: Študáci" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="feature-card-content" style="padding: var(--spacing-2xl, 32px);">
                    <h4 class="feature-card-title">7.–9. ročník: „Študáci"</h4>
                    <h5 class="feature-card-subtitle">Teorie v praxi, samostudium, osobní rozvoj</h5>
                    <p style="font-size: var(--font-size-base, 16px); line-height: 1.6; opacity: 0.95;">
                        Děti berou odpovědnost do vlastních rukou – samostatně studují, zkoumají, kým jsou, diskutují o společnosti a tvoří projekty, které je posouvají k osobnostní i ekonomické nezávislosti.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Image Banner - Navštivte nás -->
<?php renderCtaImageBanner(
    asset('images/hero/slide-3.jpg'),
    'Poznejte naši školu',
    'Přijďte se vzdělávat, nahlédnout do výuky nebo s námi slavit.',
    'Navštivte nás',
    url('kontakt'),
    'primary',
    true
); ?>

<!-- Quote Widget - Absolvent -->
<?php renderQuote(
    'Labyrint mě naučil, že zvládnu cokoliv, pokud budu chtít.',
    'Honza',
    'absolvent'
); ?>
