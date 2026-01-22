<?php

namespace App\Services;

use App\Core\Database;

/**
 * I18n Service - Translation system
 */
class I18n
{
    private string $language;
    private array $translations = [];
    private Database $db;

    public function __construct(string $language = 'cs')
    {
        $this->language = $language;
        $this->db = Database::getInstance();
        $this->loadTranslations();
    }

    /**
     * Load all translations for current language
     */
    private function loadTranslations(): void
    {
        // Try to get from cache first
        $cacheFile = STORAGE_PATH . '/cache/translations_' . $this->language . '.php';

        if (file_exists($cacheFile) && CACHE_ENABLED) {
            $this->translations = require $cacheFile;
            return;
        }

        // Load from database
        $results = $this->db->query(
            "SELECT key_name, value FROM translations WHERE language = ?",
            [$this->language]
        );

        foreach ($results as $row) {
            $this->translations[$row->key_name] = $row->value;
        }

        // Cache translations
        if (CACHE_ENABLED) {
            $this->cacheTranslations($cacheFile);
        }
    }

    /**
     * Cache translations to file
     */
    private function cacheTranslations(string $cacheFile): void
    {
        $content = "<?php\n\nreturn " . var_export($this->translations, true) . ";\n";

        // Create cache directory if it doesn't exist
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        file_put_contents($cacheFile, $content);
    }

    /**
     * Get translation by key
     */
    public function t(string $key, array $params = []): string
    {
        $value = $this->translations[$key] ?? null;

        // If not found and in development, log it
        if ($value === null) {
            if (ENVIRONMENT === 'development') {
                error_log("Missing translation: {$key} for language {$this->language}");
            }

            // Try fallback to default language
            if ($this->language !== DEFAULT_LANGUAGE) {
                return $this->getFallback($key, $params);
            }

            // Return key if no translation found
            return $key;
        }

        // Replace placeholders
        foreach ($params as $param => $replacement) {
            $value = str_replace(':' . $param, $replacement, $value);
        }

        return $value;
    }

    /**
     * Get fallback translation from default language
     */
    private function getFallback(string $key, array $params = []): string
    {
        $result = $this->db->querySingle(
            "SELECT value FROM translations WHERE key_name = ? AND language = ?",
            [$key, DEFAULT_LANGUAGE]
        );

        if ($result) {
            $value = $result->value;

            // Replace placeholders
            foreach ($params as $param => $replacement) {
                $value = str_replace(':' . $param, $replacement, $value);
            }

            return $value;
        }

        return $key;
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage(): string
    {
        return $this->language;
    }

    /**
     * Set language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
        $this->translations = [];
        $this->loadTranslations();
    }

    /**
     * Clear translation cache
     */
    public static function clearCache(): void
    {
        $cacheDir = STORAGE_PATH . '/cache';
        $files = glob($cacheDir . '/translations_*.php');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Add or update translation
     */
    public function set(string $key, string $value, string $language = null, string $context = 'general'): bool
    {
        $language = $language ?? $this->language;

        $result = $this->db->execute(
            "INSERT INTO translations (key_name, language, value, context)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value), context = VALUES(context)",
            [$key, $language, $value, $context]
        );

        // Clear cache after update
        self::clearCache();

        return $result;
    }
}
