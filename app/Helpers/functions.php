<?php

/**
 * Helper funkce dostupné globálně v celé aplikaci
 */

use App\Core\Session;
use App\Services\I18n;

/**
 * Escape HTML output
 */
function e(?string $string): string
{
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get translation
 */
function __(string $key, array $params = []): string
{
    static $i18n = null;

    if ($i18n === null) {
        $language = $_SESSION['language'] ?? DEFAULT_LANGUAGE;
        $i18n = new I18n($language);
    }

    return $i18n->t($key, $params);
}

/**
 * Generate URL for language and path
 */
function url(string $path = '', ?string $language = null): string
{
    if ($language === null) {
        $language = $_SESSION['language'] ?? DEFAULT_LANGUAGE;
    }

    $path = ltrim($path, '/');

    // If path is empty, return base with language
    if (empty($path)) {
        return BASE_URL . "/{$language}/";
    }

    return BASE_URL . "/{$language}/{$path}";
}

/**
 * Generate asset URL with version for cache busting
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $fullPath = PUBLIC_PATH . '/assets/' . $path;

    if (file_exists($fullPath)) {
        $version = filemtime($fullPath);
        return BASE_URL . '/assets/' . $path . '?v=' . $version;
    }

    return BASE_URL . '/assets/' . $path;
}

/**
 * Generate admin URL (no language prefix)
 */
function adminUrl(string $path = ''): string
{
    $path = ltrim($path, '/');

    if (empty($path)) {
        return BASE_URL . '/admin';
    }

    return BASE_URL . '/admin/' . $path;
}

/**
 * Get a short label of the current DB connection (host/db)
 */
function dbConnectionLabel(): string
{
    $config = require CONFIG_PATH . '/database.php';
    $host = $config['host'] ?? 'unknown-host';
    $db = $config['database'] ?? 'unknown-db';
    $port = (int)($config['port'] ?? 0);

    $label = $host;
    if ($port && $port !== 3306) {
        $label .= ':' . $port;
    }
    $label .= '/' . $db;

    return $label;
}

/**
 * Redirect to URL
 */
function redirect(string $url, ?string $message = null): void
{
    if ($message) {
        Session::flash('success', $message);
    }

    header("Location: {$url}");
    exit;
}

/**
 * Get old input value (after validation error)
 */
function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return Session::isLoggedIn();
}

/**
 * Check if user has role
 */
function hasRole(string $role): bool
{
    return Session::getUserRole() === $role || Session::getUserRole() === 'admin';
}

/**
 * Get current user ID
 */
function userId(): ?int
{
    return Session::getUserId();
}

/**
 * Generate slug from text
 */
function generateSlug(string $text, string $language = 'cs'): string
{
    // Transliterations
    $transliterations = [
        'cs' => [
            'á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e',
            'ě' => 'e', 'í' => 'i', 'ň' => 'n', 'ó' => 'o',
            'ř' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u',
            'ů' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E',
            'Ě' => 'E', 'Í' => 'I', 'Ň' => 'N', 'Ó' => 'O',
            'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U',
            'Ů' => 'U', 'Ý' => 'Y', 'Ž' => 'Z'
        ]
    ];

    $text = strtr($text, $transliterations[$language] ?? []);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');

    return $text;
}

/**
 * Format file size to human readable
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Format date
 */
function formatDate(?string $date, string $format = 'd.m.Y'): string
{
    if (!$date) {
        return '';
    }

    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * Format datetime
 */
function formatDateTime(?string $datetime, string $format = 'd.m.Y H:i'): string
{
    if (!$datetime) {
        return '';
    }

    $timestamp = strtotime($datetime);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Debug dump and die
 */
function dd(mixed $data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Debug dump
 */
function dump(mixed $data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Check if current route matches
 */
function isActiveRoute(string $path): bool
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return str_contains($currentPath, $path);
}

/**
 * CSRF token field for forms
 */
function csrfField(): string
{
    $token = Session::generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Sanitize filename
 */
function sanitizeFilename(string $filename): string
{
    // Get filename without path
    $filename = basename($filename);

    // Remove dangerous characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

    return $filename;
}

/**
 * Get media URL (supports both local files and Cloudinary CDN)
 *
 * @param string|null $path Media path or URL
 * @param array $transformations Cloudinary transformations (width, height, quality, etc.)
 * @return string Full URL to media file
 */
function mediaUrl(?string $path, array $transformations = []): string
{
    if (empty($path)) {
        return '';
    }

    // If it's already a full URL (Cloudinary), return as-is or with transformations
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        // If transformations are requested and it's a Cloudinary URL, apply them
        if (!empty($transformations) && str_contains($path, 'cloudinary.com')) {
            return applyCloudinaryTransformations($path, $transformations);
        }
        return $path;
    }

    // Otherwise, it's a local path - return with BASE_URL
    return BASE_URL . $path;
}

/**
 * Apply Cloudinary transformations to an existing Cloudinary URL
 *
 * @param string $url Cloudinary URL
 * @param array $transformations Transformations to apply
 * @return string Modified URL with transformations
 */
function applyCloudinaryTransformations(string $url, array $transformations): string
{
    // Extract parts of Cloudinary URL
    // Format: https://res.cloudinary.com/{cloud_name}/{resource_type}/{type}/v{version}/{public_id}.{format}
    // We need to inject transformations before the version

    $parts = parse_url($url);
    $path = $parts['path'] ?? '';

    // Build transformation string
    $transformParts = [];

    if (isset($transformations['width'])) {
        $transformParts[] = 'w_' . $transformations['width'];
    }
    if (isset($transformations['height'])) {
        $transformParts[] = 'h_' . $transformations['height'];
    }
    if (isset($transformations['crop'])) {
        $transformParts[] = 'c_' . $transformations['crop'];
    }
    if (isset($transformations['quality'])) {
        $transformParts[] = 'q_' . $transformations['quality'];
    }
    if (isset($transformations['fetch_format'])) {
        $transformParts[] = 'f_' . $transformations['fetch_format'];
    }

    if (empty($transformParts)) {
        return $url;
    }

    $transformString = implode(',', $transformParts);

    // Insert transformation into URL path
    // Find the position before /v{version}/ or before the public_id
    if (preg_match('#^(.*?)/(upload|fetch)(/.*?)$#', $path, $matches)) {
        $newPath = $matches[1] . '/' . $matches[2] . '/' . $transformString . $matches[3];
        return $parts['scheme'] . '://' . $parts['host'] . $newPath;
    }

    return $url;
}

/**
 * Get responsive image srcset for Cloudinary images
 *
 * @param string|null $path Media path or URL
 * @param array $breakpoints Array of widths (e.g., [320, 640, 1024, 1920])
 * @return string srcset attribute value
 */
function mediaResponsiveSrcset(?string $path, array $breakpoints = [320, 640, 1024, 1920]): string
{
    if (empty($path)) {
        return '';
    }

    // Only works with Cloudinary URLs
    if (!str_contains($path, 'cloudinary.com')) {
        return mediaUrl($path);
    }

    $srcsetParts = [];
    foreach ($breakpoints as $width) {
        $url = mediaUrl($path, ['width' => $width, 'crop' => 'scale', 'quality' => 'auto', 'fetch_format' => 'auto']);
        $srcsetParts[] = "{$url} {$width}w";
    }

    return implode(', ', $srcsetParts);
}
