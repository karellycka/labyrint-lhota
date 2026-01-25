<?php

/**
 * Hlavní konfigurační soubor
 * Pro lokální přepsání použijte config.local.php (gitignored)
 */

// Load local config first if exists
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

// Environment
define('ENVIRONMENT', $localConfig['ENVIRONMENT'] ?? getenv('APP_ENV') ?: $_ENV['APP_ENV'] ?? 'production');

// Base URL
define('BASE_URL', $localConfig['BASE_URL'] ?? getenv('BASE_URL') ?: $_ENV['BASE_URL'] ?? 'https://labyrint.cz');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Timezone
date_default_timezone_set('Europe/Prague');

// Error reporting based on environment
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/php-errors.log');
}

// Language settings
define('DEFAULT_LANGUAGE', 'cs');
define('SUPPORTED_LANGUAGES', ['cs', 'en']);

// SEO defaults
define('SITE_NAME', 'Škola Labyrint');
define('SITE_DESCRIPTION', 'Moderní škola s inovativním přístupem k výuce');
define('SITE_KEYWORDS', 'škola, labyrint, vzdělávání, Praha');

// Contact
define('CONTACT_EMAIL', 'info@labyrint.cz');

// Upload settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Pagination
define('POSTS_PER_PAGE', 10);
define('EVENTS_PER_PAGE', 12);

// Cache
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 1 hour

// Session
define('SESSION_TIMEOUT', 31536000); // 365 dní (pro localhost development)

// Rate limiting
define('RATE_LIMIT_CONTACT_FORM', 3); // Max submissions per hour
define('RATE_LIMIT_LOGIN', 5); // Max login attempts per 15 minutes

// reCAPTCHA (optional)
define('RECAPTCHA_ENABLED', false);
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: $_ENV['RECAPTCHA_SITE_KEY'] ?? '');
define('RECAPTCHA_SECRET', getenv('RECAPTCHA_SECRET') ?: $_ENV['RECAPTCHA_SECRET'] ?? '');
