<?php

namespace App\Models;

use App\Core\Model;

/**
 * Media Model - File uploads, images, documents
 */
class Media extends Model
{
    protected string $table = 'media';

    /**
     * Get all media by type
     */
    public function getByType(string $type, int $page = 1, int $perPage = 24): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT * FROM media
            WHERE type = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ";

        return $this->query($sql, [$type, $perPage, $offset]);
    }

    /**
     * Get media by folder
     */
    public function getByFolder(string $folder, int $page = 1, int $perPage = 24): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT * FROM media
            WHERE folder = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ";

        return $this->query($sql, [$folder, $perPage, $offset]);
    }

    /**
     * Get all folders (unique)
     */
    public function getFolders(): array
    {
        $sql = "SELECT DISTINCT folder FROM media ORDER BY folder ASC";
        return $this->query($sql);
    }

    /**
     * Get media with translations
     */
    public function getWithTranslations(int $mediaId, string $language): ?object
    {
        $sql = "
            SELECT m.*, mt.alt_text, mt.caption
            FROM media m
            LEFT JOIN media_translations mt ON m.id = mt.media_id AND mt.language = ?
            WHERE m.id = ?
            LIMIT 1
        ";

        return $this->querySingle($sql, [$language, $mediaId]);
    }

    /**
     * Update media translation
     */
    public function updateTranslation(int $mediaId, string $language, array $data): bool
    {
        $sql = "
            INSERT INTO media_translations (media_id, language, alt_text, caption)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                alt_text = VALUES(alt_text),
                caption = VALUES(caption)
        ";

        return $this->execute($sql, [
            $mediaId,
            $language,
            $data['alt_text'] ?? '',
            $data['caption'] ?? ''
        ]);
    }

    /**
     * Create media record
     */
    public function createMedia(array $data): int
    {
        return $this->insert([
            'filename' => $data['filename'],
            'original_name' => $data['original_name'],
            'mime_type' => $data['mime_type'],
            'file_size' => $data['file_size'],
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'type' => $data['type'] ?? 'image',
            'folder' => $data['folder'] ?? 'general',
            'uploaded_by' => $data['uploaded_by'] ?? null
        ]);
    }

    /**
     * Get gallery images
     */
    public function getGalleryImages(string $language, ?string $folder = null, int $limit = null): array
    {
        $sql = "
            SELECT m.*, mt.alt_text, mt.caption
            FROM media m
            LEFT JOIN media_translations mt ON m.id = mt.media_id AND mt.language = ?
            WHERE m.type = 'image'
        ";

        $params = [$language];

        if ($folder) {
            $sql .= " AND m.folder = ?";
            $params[] = $folder;
        }

        $sql .= " ORDER BY m.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        return $this->query($sql, $params);
    }

    /**
     * Get recent uploads
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "
            SELECT * FROM media
            ORDER BY created_at DESC
            LIMIT ?
        ";

        return $this->query($sql, [$limit]);
    }

    /**
     * Count media by type
     */
    public function countByType(string $type): int
    {
        return $this->count('type', $type);
    }

    /**
     * Search media
     */
    public function search(string $query, string $language): array
    {
        $sql = "
            SELECT m.*, mt.alt_text
            FROM media m
            LEFT JOIN media_translations mt ON m.id = mt.media_id AND mt.language = ?
            WHERE m.original_name LIKE ?
               OR m.filename LIKE ?
               OR mt.alt_text LIKE ?
            ORDER BY m.created_at DESC
            LIMIT 50
        ";

        $searchTerm = "%{$query}%";
        return $this->query($sql, [$language, $searchTerm, $searchTerm, $searchTerm]);
    }
}
