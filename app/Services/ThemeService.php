<?php

namespace App\Services;

use App\Models\ThemeSettings;

class ThemeService
{
    private ThemeSettings $model;
    private string $cssPath;
    private string $cachePath;

    public function __construct()
    {
        $this->model = new ThemeSettings();
        $this->cssPath = dirname(__DIR__, 2) . '/public/assets/css/theme.css';
        $this->cachePath = dirname(__DIR__, 2) . '/storage/cache/theme_cache.json';
    }

    /**
     * Vygeneruje CSS soubor s custom properties ze všech nastavení
     *
     * @return bool
     */
    public function generateCSSFile(): bool
    {
        try {
            $settings = $this->model->getAllAsArray();
            $css = $this->buildCSS($settings);

            // Vytvoření složky pokud neexistuje
            $dir = dirname($this->cssPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Zápis CSS souboru
            $result = file_put_contents($this->cssPath, $css);

            if ($result !== false) {
                // Aktualizace cache timestamp
                $this->updateCacheTimestamp();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log('ThemeService CSS generation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sestaví CSS kód z nastavení
     *
     * @param array $settings
     * @return string
     */
    private function buildCSS(array $settings): string
    {
        $css = <<<CSS
/**
 * Theme CSS - Automaticky generováno z databáze
 * Tento soubor je AUTOMATICKY GENEROVÁN - NEUPRAVUJTE RUČNĚ!
 * Změny provádějte v Admin panelu (Theme Settings)
 *
 * Vygenerováno: {$this->getCurrentDateTime()}
 */

:root {

CSS;

        // Přidání CSS custom properties
        $grouped = $this->groupSettingsByCategory($settings);

        foreach ($grouped as $category => $items) {
            $categoryLabel = $this->getCategoryLabel($category);
            $css .= "\n    /* ===== {$categoryLabel} ===== */\n";

            foreach ($items as $key => $value) {
                $cssVar = $this->convertKeyToCSSVariable($key);
                $css .= "    {$cssVar}: {$value};\n";
            }
        }

        $css .= "}\n\n";

        // Přidání utility classes pro gradienty
        $css .= $this->generateGradientUtilities($settings);

        // Přidání helper classes
        $css .= $this->generateHelperClasses();

        return $css;
    }

    /**
     * Seskupí nastavení podle kategorie
     *
     * @param array $settings
     * @return array
     */
    private function groupSettingsByCategory(array $settings): array
    {
        $allSettings = $this->model->getAll();
        $grouped = [
            'colors' => [],
            'typography' => [],
            'spacing' => [],
            'effects' => []
        ];

        foreach ($allSettings as $setting) {
            // $setting je objekt (stdClass)
            $key = $setting->setting_key;
            if (isset($settings[$key])) {
                $grouped[$setting->category][$key] = $settings[$key];
            }
        }

        return $grouped;
    }

    /**
     * Konvertuje setting_key na CSS custom property
     * Např: color_primary -> --color-primary
     *
     * @param string $key
     * @return string
     */
    private function convertKeyToCSSVariable(string $key): string
    {
        return '--' . str_replace('_', '-', $key);
    }

    /**
     * Získá český název kategorie
     *
     * @param string $category
     * @return string
     */
    private function getCategoryLabel(string $category): string
    {
        $labels = [
            'colors' => 'BARVY / COLORS',
            'typography' => 'TYPOGRAFIE / TYPOGRAPHY',
            'spacing' => 'SPACING & LAYOUT',
            'effects' => 'EFEKTY / EFFECTS'
        ];

        return $labels[$category] ?? strtoupper($category);
    }

    /**
     * Generuje utility třídy pro gradienty
     *
     * @param array $settings
     * @return string
     */
    private function generateGradientUtilities(array $settings): string
    {
        $css = "/* ===== Gradient Utilities ===== */\n\n";

        $css .= ".gradient-primary {\n";
        $css .= "    background: var(--gradient-primary, linear-gradient(135deg, var(--color-primary) 0%, var(--color-blue) 100%));\n";
        $css .= "}\n\n";

        $css .= ".gradient-warm {\n";
        $css .= "    background: var(--gradient-warm, linear-gradient(135deg, var(--color-yellow) 0%, var(--color-red) 100%));\n";
        $css .= "}\n\n";

        $css .= ".gradient-earth {\n";
        $css .= "    background: var(--gradient-earth, linear-gradient(135deg, var(--color-brown) 0%, var(--color-yellow) 100%));\n";
        $css .= "}\n\n";

        $css .= ".gradient-overlay-dark {\n";
        $css .= "    position: relative;\n";
        $css .= "}\n\n";

        $css .= ".gradient-overlay-dark::before {\n";
        $css .= "    content: '';\n";
        $css .= "    position: absolute;\n";
        $css .= "    top: 0;\n";
        $css .= "    left: 0;\n";
        $css .= "    right: 0;\n";
        $css .= "    bottom: 0;\n";
        $css .= "    background: var(--color-overlay-dark);\n";
        $css .= "    z-index: 1;\n";
        $css .= "}\n\n";

        return $css;
    }

    /**
     * Generuje helper CSS třídy
     *
     * @return string
     */
    private function generateHelperClasses(): string
    {
        $css = "/* ===== Helper Classes ===== */\n\n";

        // Text colors
        $css .= ".text-primary { color: var(--color-primary); }\n";
        $css .= ".text-secondary { color: var(--color-secondary); }\n";
        $css .= ".text-muted { color: var(--color-text-muted); }\n\n";

        // Background colors
        $css .= ".bg-light { background-color: var(--color-bg-light); }\n";
        $css .= ".bg-white { background-color: var(--color-bg-white); }\n";
        $css .= ".bg-dark { background-color: var(--color-bg-dark); }\n";
        $css .= ".bg-primary { background-color: var(--color-primary); }\n\n";

        // Border radius
        $css .= ".rounded { border-radius: var(--border-radius); }\n";
        $css .= ".rounded-sm { border-radius: var(--border-radius-sm); }\n";
        $css .= ".rounded-lg { border-radius: var(--border-radius-lg); }\n";
        $css .= ".rounded-full { border-radius: var(--border-radius-full); }\n\n";

        // Shadows
        $css .= ".shadow-sm { box-shadow: var(--shadow-sm); }\n";
        $css .= ".shadow-md { box-shadow: var(--shadow-md); }\n";
        $css .= ".shadow-lg { box-shadow: var(--shadow-lg); }\n";
        $css .= ".shadow-xl { box-shadow: var(--shadow-xl); }\n\n";

        // Transitions
        $css .= ".transition { transition: all var(--transition-speed-normal) var(--transition-timing); }\n";
        $css .= ".transition-fast { transition: all var(--transition-speed-fast) var(--transition-timing); }\n";
        $css .= ".transition-slow { transition: all var(--transition-speed-slow) var(--transition-timing); }\n\n";

        return $css;
    }

    /**
     * Ověří, zda je potřeba regenerovat CSS (cache check)
     *
     * @return bool
     */
    public function needsRegeneration(): bool
    {
        // Pokud CSS soubor neexistuje, regenerovat
        if (!file_exists($this->cssPath)) {
            return true;
        }

        // Pokud cache neexistuje, regenerovat
        if (!file_exists($this->cachePath)) {
            return true;
        }

        // Porovnat timestamp poslední aktualizace v DB s cache
        $lastUpdated = $this->model->getLastUpdated();
        $cache = json_decode(file_get_contents($this->cachePath), true);

        return $lastUpdated !== ($cache['last_updated'] ?? null);
    }

    /**
     * Aktualizuje cache timestamp
     *
     * @return void
     */
    private function updateCacheTimestamp(): void
    {
        $cacheDir = dirname($this->cachePath);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cache = [
            'last_updated' => $this->model->getLastUpdated(),
            'generated_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents($this->cachePath, json_encode($cache, JSON_PRETTY_PRINT));
    }

    /**
     * Vrátí aktuální datum a čas pro header CSS
     *
     * @return string
     */
    private function getCurrentDateTime(): string
    {
        return date('d.m.Y H:i:s');
    }

    /**
     * Vrátí CSS variables jako asociativní pole pro použití v PHP
     *
     * @return array
     */
    public function getCSSVariablesArray(): array
    {
        $settings = $this->model->getAllAsArray();
        $variables = [];

        foreach ($settings as $key => $value) {
            $cssVar = $this->convertKeyToCSSVariable($key);
            $variables[$cssVar] = $value;
        }

        return $variables;
    }

    /**
     * Force regenerace CSS (ignoruje cache)
     *
     * @return bool
     */
    public function forceRegenerate(): bool
    {
        return $this->generateCSSFile();
    }

    /**
     * Získá cestu k CSS souboru
     *
     * @return string
     */
    public function getCSSPath(): string
    {
        return $this->cssPath;
    }

    /**
     * Získá URL k CSS souboru (pro použití v layoutu)
     *
     * @return string
     */
    public function getCSSUrl(): string
    {
        $timestamp = file_exists($this->cssPath) ? filemtime($this->cssPath) : time();
        return '/assets/css/theme.css?v=' . $timestamp;
    }
}
