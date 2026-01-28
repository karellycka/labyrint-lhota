<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?? SITE_NAME ?></title>
    <meta name="description" content="<?= $metaDescription ?? SITE_DESCRIPTION ?>">
    <meta name="keywords" content="<?= $metaKeywords ?? SITE_KEYWORDS ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $ogTitle ?? ($title ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= $ogDescription ?? ($metaDescription ?? SITE_DESCRIPTION) ?>">
    <meta property="og:type" content="<?= $ogType ?? 'website' ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?? url() ?>">
    <?php if (isset($ogImage)): ?>
    <meta property="og:image" content="<?= $ogImage ?>">
    <?php endif; ?>
    <meta property="og:locale" content="<?= $currentLanguage === 'cs' ? 'cs_CZ' : 'en_US' ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= $canonicalUrl ?? url() ?>">

    <!-- Hreflang tags -->
    <?php foreach (SUPPORTED_LANGUAGES as $lang): ?>
    <link rel="alternate" hreflang="<?= $lang ?>" href="<?= str_replace('/' . $currentLanguage . '/', '/' . $lang . '/', url()) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= str_replace('/' . $currentLanguage . '/', '/cs/', url()) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">

    <!-- Google Fonts - Nunito (rounded hygge sans-serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/theme.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="<?= SITE_NAME ?> Blog" href="<?= url('blog/feed') ?>">

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="<?= \App\Core\Session::generateCSRFToken() ?>">
</head>
<?php
// Determine if we have a fullscreen hero (set by dynamic.php or default to false)
$hasFullscreenHero = $hasFullscreenHero ?? false;
$bodyClass = $hasFullscreenHero ? 'has-hero-fullscreen' : 'no-hero-fullscreen';
?>
<body class="<?= $bodyClass ?>">
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?= url() ?>">
                        <svg viewBox="11 10 128 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M135.909 36.1785C134.987 36.1785 134.301 35.9237 133.852 35.4141C133.403 34.9045 133.179 34.134 133.179 33.1027V28.6437H131.85V26.9875H133.179V25.2221L135.199 24.8035V26.9875H137.911V28.6437H135.199V33.1573C135.199 33.6062 135.29 33.9338 135.472 34.1401C135.654 34.3342 135.884 34.4313 136.163 34.4313C136.37 34.4313 136.57 34.4009 136.764 34.3403C136.958 34.2675 137.116 34.2007 137.237 34.1401C137.359 34.0673 137.437 34.0187 137.474 33.9945L138.074 35.5051C137.383 35.954 136.661 36.1785 135.909 36.1785Z" fill="#2C323A"/>
                            <path d="M124.436 26.9875V28.2797C124.764 27.7944 125.176 27.4304 125.674 27.1877C126.171 26.9329 126.729 26.8055 127.348 26.8055C128.513 26.8055 129.344 27.1574 129.841 27.8611C130.339 28.5527 130.588 29.5416 130.588 30.8277V35.9965H128.567V31.0825C128.567 30.2817 128.44 29.6933 128.185 29.3171C127.93 28.941 127.445 28.7529 126.729 28.7529C126.062 28.7529 125.51 28.9895 125.073 29.4627C124.648 29.9359 124.436 30.5426 124.436 31.2827V35.9965H122.416V26.9875H124.436Z" fill="#2C323A"/>
                            <path d="M119.254 25.677C118.914 25.677 118.623 25.5617 118.38 25.3312C118.15 25.0885 118.035 24.7973 118.035 24.4576C118.035 24.1178 118.15 23.8266 118.38 23.584C118.623 23.3413 118.914 23.22 119.254 23.22C119.594 23.22 119.885 23.3413 120.128 23.584C120.37 23.8266 120.492 24.1178 120.492 24.4576C120.492 24.7973 120.37 25.0885 120.128 25.3312C119.885 25.5617 119.594 25.677 119.254 25.677ZM120.273 26.9874V35.9964H118.253V26.9874H120.273Z" fill="#2C323A"/>
                            <path d="M113.514 26.9875V28.7711C113.72 28.2373 114.09 27.7762 114.624 27.3879C115.17 26.9997 115.886 26.8055 116.772 26.8055V29.0077C115.74 29.0077 114.94 29.2747 114.369 29.8085C113.799 30.3424 113.514 31.1796 113.514 32.3201V35.9965H111.494V26.9875H113.514Z" fill="#2C323A"/>
                            <path d="M102.568 36.7792C103.09 36.7792 103.49 36.6821 103.769 36.488C104.061 36.2938 104.322 35.9601 104.552 35.4869L104.734 35.1229L100.839 26.9875H102.914L105.08 31.7923C105.225 32.1078 105.347 32.399 105.444 32.6659C105.553 32.9329 105.626 33.1149 105.662 33.2119L106.245 31.7013L108.192 26.9875H110.23L106.117 36.3969C105.705 37.3191 105.189 37.9258 104.57 38.217C103.964 38.5203 103.296 38.672 102.568 38.672V36.7792Z" fill="#2C323A"/>
                            <path d="M96.0343 36.1785C95.4034 36.1785 94.821 36.0693 94.2871 35.8509C93.7532 35.6203 93.3043 35.2927 92.9403 34.8681V35.9965H91.0111V24.0209H93.0313V28.1341C93.3832 27.7094 93.82 27.3818 94.3417 27.1513C94.8634 26.9207 95.4276 26.8055 96.0343 26.8055C96.8351 26.8055 97.551 27.0178 98.1819 27.4425C98.825 27.855 99.3224 28.4192 99.6743 29.1351C100.026 29.8509 100.202 30.6396 100.202 31.5011C100.202 32.3625 100.026 33.1512 99.6743 33.8671C99.3224 34.5708 98.825 35.135 98.1819 35.5597C97.551 35.9722 96.8351 36.1785 96.0343 36.1785ZM95.5247 34.2311C96.2891 34.2311 96.914 33.9763 97.3993 33.4667C97.8968 32.9449 98.1455 32.2897 98.1455 31.5011C98.1455 30.7124 97.8968 30.0572 97.3993 29.5355C96.914 29.0137 96.2891 28.7529 95.5247 28.7529C94.8088 28.7529 94.2143 29.0137 93.7411 29.5355C93.2679 30.0572 93.0313 30.7124 93.0313 31.5011C93.0313 32.3019 93.2679 32.9571 93.7411 33.4667C94.2143 33.9763 94.8088 34.2311 95.5247 34.2311Z" fill="#2C323A"/>
                            <path d="M83.8894 36.1782C83.0886 36.1782 82.3666 35.9719 81.7236 35.5594C81.0926 35.1347 80.6012 34.5705 80.2494 33.8668C79.8975 33.1509 79.7216 32.3622 79.7216 31.5008C79.7216 30.6393 79.8975 29.8506 80.2494 29.1348C80.6012 28.4189 81.0926 27.8547 81.7236 27.4422C82.3666 27.0175 83.0886 26.8052 83.8894 26.8052C84.496 26.8052 85.0602 26.9204 85.582 27.151C86.1037 27.3815 86.5405 27.7091 86.8924 28.1338V26.9872H88.9126V35.9962H86.8924V34.8678C86.5405 35.2924 86.1037 35.62 85.582 35.8506C85.0602 36.069 84.496 36.1782 83.8894 36.1782ZM84.399 34.2308C85.1148 34.2308 85.7094 33.976 86.1826 33.4664C86.6558 32.9568 86.8924 32.3016 86.8924 31.5008C86.8924 30.7121 86.6558 30.0569 86.1826 29.5352C85.7094 29.0134 85.1148 28.7526 84.399 28.7526C83.6346 28.7526 83.0036 29.0134 82.5062 29.5352C82.0208 30.0569 81.7782 30.7121 81.7782 31.5008C81.7782 32.2894 82.0208 32.9446 82.5062 33.4664C83.0036 33.976 83.6346 34.2308 84.399 34.2308Z" fill="#2C323A"/>
                            <path d="M74.1112 24.2029V34.1765H78.5728V35.9965H72V24.2029H74.1112Z" fill="#2C323A"/>
                            <path d="M17.5295 21.6922L19.7006 19.5211C21.5007 17.7209 24.4218 17.7209 26.222 19.5211L29.4806 22.7797L22.9613 29.2991L20.7881 27.126L25.1344 22.7797L24.0488 21.6942C23.4488 21.0942 22.4737 21.0942 21.8737 21.6942L19.7026 23.8653C17.9025 25.6654 17.9025 28.5865 19.7026 30.3867L33.8268 44.5108L31.6537 46.684L17.5295 32.5598C14.5292 29.5595 14.5292 24.6924 17.5295 21.6922ZM15.3564 15.177C19.5568 10.9766 26.3678 10.9745 30.5682 15.1749L35.9999 20.6066L41.4317 15.1749C45.6321 10.9745 52.443 10.9766 56.6434 15.177L59.9021 18.4356L57.731 20.6066L54.4724 17.348C51.4721 14.3477 46.6051 14.3477 43.6048 17.348L31.6537 29.2991L29.4806 27.126L33.8268 22.7797L28.3951 17.348C25.3948 14.3477 20.5277 14.3477 17.5274 17.348L14.2688 20.6066L12.0978 18.4356L15.3564 15.177ZM42.5193 35.8184L35.9999 42.3377L25.1344 31.4722L27.3075 29.2991L35.9999 37.9915L40.3461 33.6453L42.5193 35.8184ZM45.7779 19.5211C47.5781 17.7209 50.4991 17.7209 52.2993 19.5211L54.4703 21.6922C57.4706 24.6924 57.4706 29.5595 54.4703 32.5598L35.9999 51.0302L33.8268 48.8571L46.8655 35.8184L40.3461 29.2991L35.9999 33.6453L33.8268 31.4722L40.3461 24.9529L49.0386 33.6453L52.2972 30.3867C54.0974 28.5865 54.0974 25.6654 52.2972 23.8653L50.1262 21.6942C49.5261 21.0942 48.551 21.0942 47.951 21.6942L46.8655 22.7797L51.2117 27.126L49.0386 29.2991L42.5193 22.7797L45.7779 19.5211Z" fill="#00792E"/>
                        </svg>
                    </a>
                </div>

                <nav class="main-nav">
                    <ul>
                        <li><a href="<?= url() ?>" class="<?= isActiveRoute('/') ? 'active' : '' ?>">Domů</a></li>
                        <li><a href="<?= url('o-skole') ?>">O škole</a></li>
                        <li><a href="<?= url('jak-ucime') ?>">Jak učíme</a></li>
                        <li><a href="<?= url('blog') ?>" class="<?= isActiveRoute('blog') ? 'active' : '' ?>">Novinky</a></li>
                        <li><a href="<?= url('contact') ?>" class="<?= isActiveRoute('contact') ? 'active' : '' ?>">Kontakt</a></li>
                        <li><a href="<?= url('zapisy') ?>" class="btn-nav-primary">Zápisy</a></li>
                    </ul>
                </nav>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="site-content">
        <?php if ($flashMessage = \App\Core\Session::getFlash('success')): ?>
        <div class="flash-message success">
            <?= e($flashMessage) ?>
        </div>
        <?php endif; ?>

        <?php if ($flashMessage = \App\Core\Session::getFlash('error')): ?>
        <div class="flash-message error">
            <?= e($flashMessage) ?>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h2>Děti jsou naše radost a naděje.</h2>
                    <div class="footer-logo-symbol">
                        <svg viewBox="32 21 50 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M38.5296 33.6922L40.7006 31.5211C42.5008 29.7209 45.4219 29.7209 47.2221 31.5211L50.4807 34.7797L43.9613 41.2991L41.7882 39.126L46.1345 34.7797L45.0489 33.6942C44.4489 33.0942 43.4738 33.0942 42.8738 33.6942L40.7027 35.8653C38.9026 37.6654 38.9025 40.5865 40.7027 42.3867L54.8269 56.5108L52.6538 58.684L38.5296 44.5598C35.5293 41.5595 35.5293 36.6924 38.5296 33.6922ZM36.3565 27.177C40.5569 22.9766 47.3679 22.9745 51.5683 27.1749L57 32.6066L62.4318 27.1749C66.6322 22.9745 73.4431 22.9766 77.6435 27.177L80.9022 30.4356L78.7311 32.6066L75.4725 29.348C72.4722 26.3477 67.6051 26.3477 64.6049 29.348L52.6538 41.2991L50.4807 39.126L54.8269 34.7797L49.3952 29.348C46.3949 26.3477 41.5278 26.3477 38.5275 29.348L35.2689 32.6066L33.0979 30.4356L36.3565 27.177ZM63.5193 47.8184L57 54.3377L46.1345 43.4722L48.3076 41.2991L57 49.9915L61.3462 45.6453L63.5193 47.8184ZM66.778 31.5211C68.5781 29.7209 71.4992 29.7209 73.2994 31.5211L75.4704 33.6922C78.4707 36.6924 78.4707 41.5595 75.4704 44.5598L57 63.0302L54.8269 60.8571L67.8656 47.8184L61.3462 41.2991L57 45.6453L54.8269 43.4722L61.3462 36.9529L70.0387 45.6453L73.2973 42.3867C75.0975 40.5865 75.0975 37.6654 73.2973 35.8653L71.1263 33.6942C70.5262 33.0942 69.5511 33.0942 68.9511 33.6942L67.8656 34.7797L72.2118 39.126L70.0387 41.2991L63.5193 34.7797L66.778 31.5211Z" fill="white"/>
                        </svg>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Zvoňte, pište, volejte.</h4>
                    <p>
                        <a href="tel:+420123123123">+420 123 123 123</a><br>
                        <a href="mailto:email@email.cz">email@email.cz</a><br><br>
                        Komenského 135<br>
                        747 92 Háj ve Slezsku – Lhota
                    </p>
                </div>

                <div class="footer-column">
                    <h4>Nejbližší akce</h4>
                    <?php
                    $eventModel = new \App\Models\Event();
                    $upcomingEvents = $eventModel->getUpcoming($currentLanguage, 5);
                    ?>
                    <?php if (!empty($upcomingEvents)): ?>
                    <ul>
                        <?php foreach ($upcomingEvents as $event): ?>
                        <li>
                            <a href="<?= url('events/' . $event->slug) ?>">
                                <strong><?= date('d.m.Y', strtotime($event->start_date)) ?></strong> - <?= e($event->title) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p>Momentálně nejsou naplánované žádné akce.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. <?= __('footer.rights') ?></p>

                <!-- Language Switcher -->
                <div class="language-switcher">
                    <?php foreach (SUPPORTED_LANGUAGES as $lang): ?>
                        <?php
                        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                        $langUrl = str_replace('/' . $currentLanguage . '/', '/' . $lang . '/', $currentPath);
                        ?>
                        <a href="<?= $langUrl ?>" class="<?= $lang === $currentLanguage ? 'active' : '' ?>">
                            <?= strtoupper($lang) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= asset('js/hero-slideshow.js') ?>"></script>
    <script src="<?= asset('js/hero-video.js') ?>"></script>
    <script src="<?= asset('js/text-rotator.js') ?>"></script>
    <script src="<?= asset('js/mobile-menu.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
