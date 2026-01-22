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
 * Generate upload URL
 */
function upload(string $path): string
{
    return BASE_URL . '/uploads/' . ltrim($path, '/');
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
