<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Media;
use App\Services\CloudinaryService;

/**
 * Admin Media Controller - Media library management
 */
class MediaAdminController extends Controller
{
    private Media $mediaModel;
    private CloudinaryService $cloudinary;

    public function __construct()
    {
        parent::__construct();
        $this->mediaModel = new Media();
        $this->cloudinary = new CloudinaryService();
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
            // Upload to Cloudinary
            $cloudinaryResult = $this->cloudinary->upload($file['tmp_name'], [
                'folder' => 'labyrint/media',
                'public_id' => pathinfo($file['name'], PATHINFO_FILENAME) . '_' . time(),
            ]);

            if (ENVIRONMENT === 'development') {
                error_log("Image uploaded to Cloudinary: {$file['name']}");
                error_log("  Public ID: {$cloudinaryResult['public_id']}");
                error_log("  URL: {$cloudinaryResult['url']}");
                error_log("  Size: " . round($cloudinaryResult['bytes'] / 1024, 1) . " KB");
                error_log("  Dimensions: {$cloudinaryResult['width']}x{$cloudinaryResult['height']}");
            }

            // Save to database
            $this->db->beginTransaction();

            $this->mediaModel->execute("
                INSERT INTO media (filename, original_name, mime_type, file_size, width, height, type, folder, uploaded_by, cloudinary_public_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $cloudinaryResult['url'], // Store Cloudinary URL as filename
                $file['name'],
                $file['type'],
                $cloudinaryResult['bytes'],
                $cloudinaryResult['width'],
                $cloudinaryResult['height'],
                'image',
                'cloudinary',
                $_SESSION['user_id'] ?? null,
                $cloudinaryResult['public_id']
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
                'filename' => $cloudinaryResult['url'],
                'file_path' => $cloudinaryResult['url'], // For backwards compatibility
                'cloudinary_public_id' => $cloudinaryResult['public_id']
            ]);

        } catch (\Exception $e) {
            // Rollback transaction if active
            try {
                $this->db->rollBack();
            } catch (\Exception $rollbackException) {
                // Ignore if no active transaction
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
            // Get media record first
            $media = $this->mediaModel->find($id);

            if (!$media) {
                Session::flash('error', 'Media not found');
                redirect(adminUrl('media'));
                return;
            }

            // Delete from Cloudinary
            if (!empty($media->cloudinary_public_id)) {
                $deleted = $this->cloudinary->delete($media->cloudinary_public_id);

                if (!$deleted && ENVIRONMENT === 'development') {
                    error_log("Warning: Failed to delete image from Cloudinary: {$media->cloudinary_public_id}");
                }
            } else {
                // Media without Cloudinary ID - log warning
                if (ENVIRONMENT === 'development') {
                    error_log("Warning: Deleting media without Cloudinary public_id: {$media->id}");
                }
            }

            // Delete from database
            $this->mediaModel->delete($id);
            Session::flash('success', 'Media deleted successfully');
        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }

        redirect(adminUrl('media'));
    }
}
