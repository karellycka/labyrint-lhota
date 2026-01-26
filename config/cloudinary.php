<?php

/**
 * Cloudinary Configuration
 * Used for image uploads and CDN delivery
 */

// Load local config first if exists
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

return [
    'cloud_name' => $localConfig['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME') ?: $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
    'api_key' => $localConfig['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY') ?: $_ENV['CLOUDINARY_API_KEY'] ?? '',
    'api_secret' => $localConfig['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET') ?: $_ENV['CLOUDINARY_API_SECRET'] ?? '',
    'folder' => $localConfig['CLOUDINARY_FOLDER'] ?? getenv('CLOUDINARY_FOLDER') ?: $_ENV['CLOUDINARY_FOLDER'] ?? 'labyrint',
    'secure' => true,

    // Upload settings
    'upload_preset' => null,
    'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],

    // Transformation settings
    'default_transformations' => [
        'quality' => 'auto',
        'fetch_format' => 'auto', // Auto-convert to WebP if browser supports it
    ],

    // Max file size (in bytes) - 10MB default
    'max_file_size' => 10 * 1024 * 1024,

    // Responsive breakpoints
    'responsive_breakpoints' => [
        ['width' => 320],
        ['width' => 640],
        ['width' => 1024],
        ['width' => 1920],
    ],
];
