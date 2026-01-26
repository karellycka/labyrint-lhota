<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

/**
 * Cloudinary Service - Handle image uploads to Cloudinary CDN
 */
class CloudinaryService
{
    private Cloudinary $cloudinary;
    private array $config;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/cloudinary.php';

        // Initialize Cloudinary
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $this->config['cloud_name'],
                'api_key' => $this->config['api_key'],
                'api_secret' => $this->config['api_secret'],
            ],
            'url' => [
                'secure' => $this->config['secure'] ?? true,
            ]
        ]);
    }

    /**
     * Upload image to Cloudinary
     *
     * @param string $filePath Local file path
     * @param array $options Upload options
     * @return array Upload result with public_id, url, etc.
     * @throws \Exception
     */
    public function upload(string $filePath, array $options = []): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        // Validate file type
        $mimeType = mime_content_type($filePath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mimeType, $allowedTypes)) {
            throw new \Exception("Invalid file type: {$mimeType}");
        }

        // Validate file size
        $fileSize = filesize($filePath);
        if ($fileSize > ($this->config['max_file_size'] ?? 10 * 1024 * 1024)) {
            throw new \Exception("File too large: " . round($fileSize / 1024 / 1024, 2) . "MB");
        }

        // Prepare upload options
        $uploadOptions = array_merge([
            'folder' => $this->config['folder'] ?? 'labyrint',
            'quality' => 'auto',
            'fetch_format' => 'auto',
            'resource_type' => 'image',
            'use_filename' => true,
            'unique_filename' => true,
        ], $options);

        // Upload to Cloudinary
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $result = $uploadApi->upload($filePath, $uploadOptions);

            return [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
                'bytes' => $result['bytes'],
                'version' => $result['version'],
                'resource_type' => $result['resource_type'],
            ];
        } catch (\Exception $e) {
            throw new \Exception("Cloudinary upload failed: " . $e->getMessage());
        }
    }

    /**
     * Delete image from Cloudinary
     *
     * @param string $publicId Cloudinary public_id
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $result = $uploadApi->destroy($publicId);

            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            error_log("Cloudinary delete failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get image URL with transformations
     *
     * @param string $publicId Cloudinary public_id
     * @param array $transformations Transformation options
     * @return string
     */
    public function getUrl(string $publicId, array $transformations = []): string
    {
        if (empty($publicId)) {
            return '';
        }

        // Default transformations
        $defaultTransformations = [
            'quality' => 'auto',
            'fetch_format' => 'auto',
        ];

        $transformations = array_merge($defaultTransformations, $transformations);

        return $this->cloudinary->image($publicId)
            ->resize($transformations['width'] ?? null, $transformations['height'] ?? null)
            ->delivery(
                quality: $transformations['quality'] ?? 'auto',
                format: $transformations['fetch_format'] ?? 'auto'
            )
            ->toUrl();
    }

    /**
     * Get responsive image URLs
     *
     * @param string $publicId
     * @return array Array of URLs for different breakpoints
     */
    public function getResponsiveUrls(string $publicId): array
    {
        $breakpoints = $this->config['responsive_breakpoints'] ?? [
            ['width' => 320],
            ['width' => 640],
            ['width' => 1024],
            ['width' => 1920],
        ];

        $urls = [];
        foreach ($breakpoints as $breakpoint) {
            $width = $breakpoint['width'];
            $urls[$width] = $this->getUrl($publicId, ['width' => $width]);
        }

        return $urls;
    }

    /**
     * Check if Cloudinary is configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->config['cloud_name']) &&
               !empty($this->config['api_key']) &&
               !empty($this->config['api_secret']);
    }

    /**
     * Get Cloudinary configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
