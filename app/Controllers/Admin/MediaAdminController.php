<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Media;

/**
 * Admin Media Controller - Media library management
 */
class MediaAdminController extends Controller
{
    private Media $mediaModel;

    public function __construct()
    {
        parent::__construct();
        $this->mediaModel = new Media();
    }

    /**
     * Check auth and return JSON 401 if not logged in
     */
    private function requireAuth(): bool
    {
        if (!Session::isLoggedIn()) {
            // For AJAX/API requests, return JSON
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                strpos($_SERVER['REQUEST_URI'], '/api/') !== false ||
                isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            // For normal requests, redirect to login
            redirect(adminUrl('login'));
        }
        return true;
    }

    /**
     * List all media
     */
    public function index(): void
    {
        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }

        $media = $this->mediaModel->query("
            SELECT
                m.*,
                mt_cs.caption as title_cs,
                mt_cs.alt_text as alt_cs
            FROM media m
            LEFT JOIN media_translations mt_cs ON m.id = mt_cs.media_id AND mt_cs.language = 'cs'
            ORDER BY m.created_at DESC
        ");

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'media' => $media
        ], 'admin');
    }

    /**
     * Get all media (JSON API for modal)
     */
    public function getAll(): void
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        $media = $this->mediaModel->query("
            SELECT
                m.*,
                mt_cs.caption as title_cs,
                mt_cs.alt_text as alt_cs
            FROM media m
            LEFT JOIN media_translations mt_cs ON m.id = mt_cs.media_id AND mt_cs.language = 'cs'
            ORDER BY m.created_at DESC
        ");

        echo json_encode(['media' => $media]);
    }

    /**
     * Upload media
     */
    public function upload(): void
    {
        try {
            $this->requireAuth();
            header('Content-Type: application/json');

        $providedToken = $_POST['csrf_token'] ?? '';

        if (ENVIRONMENT === 'development') {
            error_log("=== UPLOAD REQUEST ===");
            error_log("Upload - File name: " . ($_FILES['file']['name'] ?? 'NONE'));
            error_log("Upload - CSRF token provided: " . ($providedToken ? substr($providedToken, 0, 20) . "..." : 'EMPTY'));
            error_log("Upload - Session CSRF: " . (isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 20) . "..." : 'NOT SET'));
            error_log("Upload - Token match: " . (Session::validateCSRFToken($providedToken) ? 'YES' : 'NO'));
        }

        if (!Session::validateCSRFToken($providedToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.']);
            return;
        }

        // Validate file size (max 32MB to match upload_max_filesize)
        if ($file['size'] > 32 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large. Maximum size is 32MB.']);
            return;
        }

        try {
            // Create upload directory if it doesn't exist
            $uploadDir = PUBLIC_PATH . '/uploads/media/' . date('Y/m');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Get original size before optimization
            $originalSize = filesize($filepath);

            // Optimize image (resize + compression)
            $optimized = $this->optimizeImage($filepath, $file['type']);
            $width = $optimized['width'] ?? null;
            $height = $optimized['height'] ?? null;
            $finalSize = $optimized['size'] ?? $file['size'];

            // Log optimization results
            if (ENVIRONMENT === 'development') {
                $savedBytes = $originalSize - $finalSize;
                $savedPercent = $originalSize > 0 ? round(($savedBytes / $originalSize) * 100, 1) : 0;
                error_log("Image optimized: {$file['name']}");
                error_log("  Original: " . round($originalSize / 1024, 1) . " KB");
                error_log("  Optimized: " . round($finalSize / 1024, 1) . " KB");
                error_log("  Saved: " . round($savedBytes / 1024, 1) . " KB ({$savedPercent}%)");
                error_log("  Dimensions: {$width}x{$height}");
            }

            // Save to database
            $this->db->beginTransaction();

            $this->mediaModel->execute("
                INSERT INTO media (filename, original_name, mime_type, file_size, width, height, type, folder, uploaded_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                '/uploads/media/' . date('Y/m') . '/' . $filename,
                $file['name'],
                $file['type'],
                $finalSize, // Optimized file size
                $width,
                $height,
                'image',
                'uploads',
                $_SESSION['user_id'] ?? null
            ]);

            $mediaId = $this->db->lastInsertId();

            // Insert translations
            foreach (['cs', 'en'] as $lang) {
                $this->mediaModel->execute("
                    INSERT INTO media_translations (media_id, language, alt_text, caption)
                    VALUES (?, ?, ?, ?)
                ", [$mediaId, $lang, '', pathinfo($file['name'], PATHINFO_FILENAME)]);
            }

            $this->db->commit();

            echo json_encode([
                'success' => true,
                'media_id' => $mediaId,
                'filename' => '/uploads/media/' . date('Y/m') . '/' . $filename,
                'file_path' => '/uploads/media/' . date('Y/m') . '/' . $filename // For backwards compatibility
            ]);

        } catch (\Exception $e) {
            // Rollback transaction if active
            try {
                $this->db->rollBack();
            } catch (\Exception $rollbackException) {
                // Ignore if no active transaction
            }

            // Delete uploaded file if database insert failed
            if (isset($filepath) && file_exists($filepath)) {
                unlink($filepath);
            }

            http_response_code(500);
            echo json_encode(['error' => 'Upload failed: ' . $e->getMessage()]);
        }

        } catch (\Throwable $outerException) {
            // Catch any other errors (like PHP errors, type errors, etc.)
            if (ENVIRONMENT === 'development') {
                error_log("Upload outer exception: " . $outerException->getMessage());
                error_log($outerException->getTraceAsString());
            }
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $outerException->getMessage()]);
        }
    }

    /**
     * Optimize image (resize + compression)
     * Conservative approach: max 1920px, JPEG 85%, maintain format
     */
    private function optimizeImage(string $filepath, string $mimeType): array
    {
        // Get original dimensions
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return ['width' => null, 'height' => null, 'size' => filesize($filepath)];
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $maxDimension = 1920; // Conservative: Full HD

        // Calculate new dimensions if needed
        $needsResize = ($originalWidth > $maxDimension || $originalHeight > $maxDimension);

        if ($needsResize) {
            if ($originalWidth > $originalHeight) {
                $newWidth = $maxDimension;
                $newHeight = (int) ($originalHeight * ($maxDimension / $originalWidth));
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) ($originalWidth * ($maxDimension / $originalHeight));
            }
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        // Load image based on type
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = @imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($filepath);
                break;
            case 'image/webp':
                $sourceImage = @imagecreatefromwebp($filepath);
                break;
            default:
                return ['width' => $originalWidth, 'height' => $originalHeight, 'size' => filesize($filepath)];
        }

        if (!$sourceImage) {
            error_log("Failed to load image for optimization: {$filepath}");
            return ['width' => $originalWidth, 'height' => $originalHeight, 'size' => filesize($filepath)];
        }

        // Create new image if resize needed
        if ($needsResize) {
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize with high quality
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagedestroy($sourceImage);
            $sourceImage = $newImage;
        }

        // Save optimized image
        $success = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $success = imagejpeg($sourceImage, $filepath, 85); // 85% quality
                break;
            case 'image/png':
                imagealphablending($sourceImage, false);
                imagesavealpha($sourceImage, true);
                $success = imagepng($sourceImage, $filepath, 9); // Max compression
                break;
            case 'image/gif':
                $success = imagegif($sourceImage, $filepath);
                break;
            case 'image/webp':
                $success = imagewebp($sourceImage, $filepath, 85); // 85% quality
                break;
        }

        imagedestroy($sourceImage);

        if (!$success) {
            error_log("Failed to save optimized image: {$filepath}");
        }

        // Return final dimensions and size
        return [
            'width' => $newWidth,
            'height' => $newHeight,
            'size' => filesize($filepath),
            'optimized' => $needsResize || $mimeType === 'image/jpeg' || $mimeType === 'image/webp'
        ];
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $media = $this->mediaModel->find($id);
        if (!$media) {
            Session::flash('error', 'Media not found');
            redirect(adminUrl('media'));
        }

        $translations = $this->mediaModel->query("
            SELECT language, caption, alt_text
            FROM media_translations WHERE media_id = ?
        ", [$id]);

        $translationsByLang = [];
        foreach ($translations as $trans) {
            $translationsByLang[$trans->language] = $trans;
        }

        $this->view('admin/media/edit', [
            'title' => 'Edit Media',
            'media' => $media,
            'translations' => $translationsByLang
        ], 'admin');
    }

    /**
     * Update media metadata
     */
    public function update(int $id): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl("media/{$id}/edit"));
        }

        try {
            $this->db->beginTransaction();

            foreach (['cs', 'en'] as $lang) {
                $this->mediaModel->execute("
                    UPDATE media_translations
                    SET caption = ?, alt_text = ?
                    WHERE media_id = ? AND language = ?
                ", [
                    $_POST["caption_{$lang}"] ?? '',
                    $_POST["alt_{$lang}"] ?? '',
                    $id,
                    $lang
                ]);
            }

            $this->db->commit();
            Session::flash('success', 'Media updated successfully');
            redirect(adminUrl('media'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            redirect(adminUrl("media/{$id}/edit"));
        }
    }

    /**
     * Delete media
     */
    public function delete(int $id): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('media'));
        }

        try {
            // TODO: Also delete physical file
            $this->mediaModel->delete($id);
            Session::flash('success', 'Media deleted successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        redirect(adminUrl('media'));
    }
}
