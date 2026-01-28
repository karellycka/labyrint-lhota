<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?? 'Admin' ?> | <?= SITE_NAME ?></title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= \App\Core\Session::generateCSRFToken() ?>">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
</head>
<body class="admin-body">
    <?php if (isLoggedIn()): ?>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-logo">
                <a href="<?= adminUrl() ?>">
                    <?= SITE_NAME ?> Admin
                </a>
            </div>

            <nav class="admin-nav">
                <ul>
                    <li><a href="<?= adminUrl() ?>" class="<?= isActiveRoute('/admin') && !isActiveRoute('/admin/') ? 'active' : '' ?>">Dashboard</a></li>
                    <li><a href="<?= adminUrl('blog') ?>" class="<?= isActiveRoute('/admin/blog') ? 'active' : '' ?>">Blog</a></li>
                    <li><a href="<?= adminUrl('pages') ?>" class="<?= isActiveRoute('/admin/pages') ? 'active' : '' ?>">Pages</a></li>
                    <li><a href="<?= adminUrl('events') ?>" class="<?= isActiveRoute('/admin/events') ? 'active' : '' ?>">Events</a></li>
                    <li><a href="<?= adminUrl('media') ?>" class="<?= isActiveRoute('/admin/media') ? 'active' : '' ?>">Media</a></li>
                    <li><a href="<?= adminUrl('contact') ?>" class="<?= isActiveRoute('/admin/contact') ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="<?= adminUrl('theme') ?>" class="<?= isActiveRoute('/admin/theme') ? 'active' : '' ?>">Theme</a></li>
                </ul>
            </nav>

            <div class="admin-user">
                <span class="db-info" style="margin-right: 12px; font-size: 12px; color: #aaa;">
                    DB: <?= e(dbConnectionLabel()) ?>
                </span>
                <span class="user-name"><?= e(\App\Core\Session::get('username', 'Admin')) ?></span>
                <a href="<?= adminUrl('logout') ?>" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <!-- Admin Sidebar (optional) -->
    <aside class="admin-sidebar">
        <ul>
            <li><a href="<?= adminUrl() ?>">
                <span class="icon">📊</span> Dashboard
            </a></li>
            <li><a href="<?= adminUrl('blog') ?>">
                <span class="icon">📝</span> Blog Posts
            </a></li>
            <li><a href="<?= adminUrl('pages') ?>">
                <span class="icon">📄</span> Pages
            </a></li>
            <li><a href="<?= adminUrl('events') ?>">
                <span class="icon">📅</span> Events
            </a></li>
            <li><a href="<?= adminUrl('media') ?>">
                <span class="icon">🖼️</span> Media Library
            </a></li>
            <li><a href="<?= adminUrl('contact') ?>">
                <span class="icon">✉️</span> Contact Messages
            </a></li>
            <li><a href="<?= adminUrl('theme') ?>">
                <span class="icon">🎨</span> Theme Settings
            </a></li>
            <li class="divider"></li>
            <li><a href="<?= url() ?>" target="_blank">
                <span class="icon">🌍</span> View Site
            </a></li>
        </ul>
    </aside>
    <?php endif; ?>

    <!-- Admin Content -->
    <main class="admin-content">
        <?php if ($flashMessage = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success">
            <?= e($flashMessage) ?>
        </div>
        <?php endif; ?>

        <?php if ($flashMessage = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-error">
            <?= e($flashMessage) ?>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- JavaScript -->
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
