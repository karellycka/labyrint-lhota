<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ThemeSettings;
use App\Services\ThemeService;

class ThemeController extends Controller
{
    private ThemeSettings $model;
    private ThemeService $service;

    public function __construct()
    {
        parent::__construct();

        // Admin kontrola - musí být přihlášený
        if (!isLoggedIn()) {
            header('Location: ' . adminUrl('login'));
            exit;
        }

        $this->model = new ThemeSettings();
        $this->service = new ThemeService();
    }

    /**
     * Zobrazí stránku se všemi theme settings
     */
    public function index(): void
    {
        // Načtení nastavení seskupených podle kategorie
        $settings = $this->model->getAllGroupedByCategory();

        // Informace o CSS souboru
        $cssInfo = [
            'exists' => file_exists($this->service->getCSSPath()),
            'path' => $this->service->getCSSPath(),
            'size' => file_exists($this->service->getCSSPath())
                ? filesize($this->service->getCSSPath())
                : 0,
            'last_modified' => file_exists($this->service->getCSSPath())
                ? date('d.m.Y H:i:s', filemtime($this->service->getCSSPath()))
                : null
        ];

        $this->view('admin/theme/index', [
            'title' => 'Theme Settings',
            'settings' => $settings,
            'cssInfo' => $cssInfo,
            'activeTab' => $_GET['tab'] ?? 'colors'
        ]);
    }

    /**
     * Aktualizace nastavení
     */
    public function update(): void
    {
        // CSRF ochrana
        if (!Session::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Neplatný CSRF token');
            header('Location: ' . adminUrl('theme'));
            exit;
        }

        // Získání dat z formuláře
        $settingsToUpdate = [];
        $errors = [];

        foreach ($_POST as $key => $value) {
            // Přeskočit csrf_token a jiné pomocné fieldy
            if ($key === 'csrf_token' || $key === 'action') {
                continue;
            }

            // Validace a příprava dat
            $setting = $this->model->getByKey($key);

            if ($setting) {
                // Validace hodnoty
                if ($this->model->validateValue($setting['type'], $value)) {
                    $settingsToUpdate[$key] = $value;
                } else {
                    $errors[] = "Neplatná hodnota pro {$setting['label']}: {$value}";
                }
            }
        }

        // Pokud jsou chyby, vrátit zpět
        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            header('Location: ' . adminUrl('theme'));
            exit;
        }

        // Aktualizace v databázi
        $updatedCount = $this->model->updateMultiple($settingsToUpdate);

        // Regenerace CSS souboru
        $cssGenerated = $this->service->forceRegenerate();

        if ($updatedCount > 0 && $cssGenerated) {
            Session::flash('success', "Úspěšně aktualizováno {$updatedCount} nastavení a CSS soubor byl regenerován.");
        } elseif ($updatedCount > 0) {
            Session::flash('warning', "Aktualizováno {$updatedCount} nastavení, ale CSS soubor se nepodařilo vygenerovat.");
        } else {
            Session::flash('info', 'Žádné změny nebyly provedeny.');
        }

        header('Location: ' . adminUrl('theme'));
        exit;
    }

    /**
     * Regenerace CSS souboru (AJAX endpoint)
     */
    public function regenerateCSS(): void
    {
        header('Content-Type: application/json');

        // CSRF ochrana
        if (!Session::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Neplatný CSRF token']);
            exit;
        }

        $success = $this->service->forceRegenerate();

        if ($success) {
            $cssInfo = [
                'size' => filesize($this->service->getCSSPath()),
                'last_modified' => date('d.m.Y H:i:s', filemtime($this->service->getCSSPath()))
            ];

            echo json_encode([
                'success' => true,
                'message' => 'CSS soubor úspěšně regenerován',
                'cssInfo' => $cssInfo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Chyba při generování CSS souboru'
            ]);
        }

        exit;
    }

    /**
     * Preview - otevře frontend v novém okně pro náhled změn
     */
    public function preview(): void
    {
        // Jednoduchý redirect na homepage
        header('Location: ' . url(''));
        exit;
    }

    /**
     * Export nastavení jako JSON
     */
    public function export(): void
    {
        $settings = $this->model->getAllAsArray();

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="theme-settings-' . date('Y-m-d') . '.json"');

        echo json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Import nastavení z JSON
     */
    public function import(): void
    {
        // CSRF ochrana
        if (!Session::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Neplatný CSRF token');
            header('Location: ' . adminUrl('theme'));
            exit;
        }

        // Kontrola nahraného souboru
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Chyba při nahrávání souboru');
            header('Location: ' . adminUrl('theme'));
            exit;
        }

        // Načtení JSON
        $jsonContent = file_get_contents($_FILES['import_file']['tmp_name']);
        $settings = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Session::flash('error', 'Neplatný JSON formát');
            header('Location: ' . adminUrl('theme'));
            exit;
        }

        // Import nastavení
        $updatedCount = $this->model->updateMultiple($settings);

        // Regenerace CSS
        $this->service->forceRegenerate();

        Session::flash('success', "Importováno {$updatedCount} nastavení");
        header('Location: ' . adminUrl('theme'));
        exit;
    }

    /**
     * Načtení jednoho nastavení (AJAX)
     */
    public function getSetting(): void
    {
        header('Content-Type: application/json');

        $key = $_GET['key'] ?? null;

        if (!$key) {
            echo json_encode(['success' => false, 'message' => 'Chybí klíč']);
            exit;
        }

        $setting = $this->model->getByKey($key);

        if ($setting) {
            echo json_encode(['success' => true, 'setting' => $setting]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nastavení nenalezeno']);
        }

        exit;
    }
}
