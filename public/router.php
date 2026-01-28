<?php
/**
 * Router for PHP Built-in Server
 * Handles static files and routes other requests to index.php
 */

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Define paths relative to public directory
$publicPath = __DIR__;
$requestedFile = $publicPath . $uri;

// If the file exists and is not a PHP file, serve it directly
if ($uri !== '/' && file_exists($requestedFile) && !is_dir($requestedFile)) {
    // Get the file extension
    $extension = pathinfo($requestedFile, PATHINFO_EXTENSION);

    // Set appropriate Content-Type headers
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'pdf' => 'application/pdf',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }

    // Return the file
    return false; // Let PHP built-in server handle the file serving
}

// For all other requests, route to index.php
require_once $publicPath . '/index.php';
