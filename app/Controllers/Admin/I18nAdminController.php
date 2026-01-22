<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;

/**
 * Admin Translation Controller - Manage i18n translations
 */
class I18nAdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!Session::isLoggedIn()) {
            redirect(adminUrl('login'));
        }
    }

    /**
     * List all translations
     */
    public function index(): void
    {
        $translations = $this->db->query("
            SELECT
                t_cs.key_name,
                t_cs.value as value_cs,
                t_en.value as value_en
            FROM translations t_cs
            LEFT JOIN translations t_en ON t_cs.key_name = t_en.key_name AND t_en.language = 'en'
            WHERE t_cs.language = 'cs'
            ORDER BY t_cs.key_name
        ");

        $this->view('admin/translations/index', [
            'title' => 'Translations',
            'translations' => $translations
        ], 'admin');
    }

    /**
     * Show edit form for a translation key
     */
    public function edit(string $key): void
    {
        $translations = $this->db->query("
            SELECT language, value
            FROM translations
            WHERE key_name = ?
        ", [$key]);

        if (empty($translations)) {
            Session::flash('error', 'Translation key not found');
            redirect(adminUrl('translations'));
        }

        $translationsByLang = [];
        foreach ($translations as $trans) {
            $translationsByLang[$trans->language] = $trans->value;
        }

        $this->view('admin/translations/edit', [
            'title' => 'Edit Translation',
            'key' => $key,
            'translations' => $translationsByLang
        ], 'admin');
    }

    /**
     * Update translation
     */
    public function update(string $key): void
    {
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl("translations/" . urlencode($key) . "/edit"));
        }

        try {
            foreach (['cs', 'en'] as $lang) {
                $this->db->execute("
                    UPDATE translations
                    SET value = ?
                    WHERE key_name = ? AND language = ?
                ", [
                    $_POST["value_{$lang}"],
                    $key,
                    $lang
                ]);
            }

            // Clear i18n cache
            $cacheFiles = glob(STORAGE_PATH . '/cache/i18n_*.php');
            foreach ($cacheFiles as $file) {
                @unlink($file);
            }

            Session::flash('success', 'Translation updated successfully');
            redirect(adminUrl('translations'));

        } catch (\Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            redirect(adminUrl("translations/" . urlencode($key) . "/edit"));
        }
    }
}
