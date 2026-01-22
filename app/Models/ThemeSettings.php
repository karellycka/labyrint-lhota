<?php

namespace App\Models;

use App\Core\Model;

class ThemeSettings extends Model
{
    protected string $table = 'theme_settings';

    /**
     * Načte všechna nastavení
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY category, id";
        return $this->db->query($sql);
    }

    /**
     * Načte nastavení podle kategorie
     *
     * @param string $category - colors, typography, spacing, effects
     * @return array
     */
    public function getByCategory(string $category): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE category = ? ORDER BY id";
        return $this->db->query($sql, [$category]);
    }

    /**
     * Načte jedno nastavení podle klíče
     *
     * @param string $key
     * @return array|null
     */
    public function getByKey(string $key): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE setting_key = ? LIMIT 1";
        $result = $this->db->querySingle($sql, [$key]);
        return $result ? (array) $result : null;
    }

    /**
     * Aktualizuje hodnotu nastavení
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateValue(string $key, string $value): bool
    {
        $sql = "UPDATE {$this->table} SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?";

        try {
            $stmt = $this->db->getPDO()->prepare($sql);
            $stmt->execute([$value, $key]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('ThemeSettings updateValue error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hromadná aktualizace nastavení
     *
     * @param array $settings - ['setting_key' => 'value', ...]
     * @return int - počet aktualizovaných řádků
     */
    public function updateMultiple(array $settings): int
    {
        $count = 0;

        foreach ($settings as $key => $value) {
            if ($this->updateValue($key, $value)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Vrátí všechna nastavení jako asociativní pole
     * Pro jednoduché použití: $settings['color_primary']
     *
     * @return array
     */
    public function getAllAsArray(): array
    {
        $settings = $this->getAll();
        $result = [];

        foreach ($settings as $setting) {
            // $setting je objekt (stdClass), ne array
            $result[$setting->setting_key] = $setting->setting_value;
        }

        return $result;
    }

    /**
     * Vrátí nastavení seskupená podle kategorie
     *
     * @return array ['colors' => [...], 'typography' => [...], ...]
     */
    public function getAllGroupedByCategory(): array
    {
        $settings = $this->getAll();
        $grouped = [
            'colors' => [],
            'typography' => [],
            'spacing' => [],
            'effects' => []
        ];

        foreach ($settings as $setting) {
            // Convert object to array for easier use in views
            $settingArray = (array) $setting;
            $category = $settingArray['category'];
            $grouped[$category][] = $settingArray;
        }

        return $grouped;
    }

    /**
     * Reset všech nastavení na výchozí hodnoty
     * (vyžaduje reload SQL souboru)
     *
     * @return bool
     */
    public function resetToDefaults(): bool
    {
        // Toto je bezpečnější implementace - smaže jen data, ne tabulku
        $sql = "TRUNCATE TABLE {$this->table}";

        try {
            $this->db->getPDO()->exec($sql);
            return true;
        } catch (\PDOException $e) {
            error_log('ThemeSettings resetToDefaults error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ověří, zda existuje nastavení s daným klíčem
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE setting_key = ?";
        $result = $this->db->querySingle($sql, [$key]);
        return $result && $result->count > 0;
    }

    /**
     * Vrátí poslední čas aktualizace nastavení (pro cache invalidaci)
     *
     * @return string|null
     */
    public function getLastUpdated(): ?string
    {
        $sql = "SELECT MAX(updated_at) as last_updated FROM {$this->table}";
        $result = $this->db->querySingle($sql);
        return $result->last_updated ?? null;
    }

    /**
     * Validace hodnoty podle typu
     *
     * @param string $type - color, font, size, number, text
     * @param string $value
     * @return bool
     */
    public function validateValue(string $type, string $value): bool
    {
        switch ($type) {
            case 'color':
                // Validace HEX barvy (#RGB nebo #RRGGBB) nebo rgba()
                return preg_match('/^(#[0-9A-Fa-f]{3,6}|rgba?\([^)]+\))$/', $value);

            case 'size':
                // Validace CSS velikosti (px, rem, em, %, vh, vw)
                return preg_match('/^\d+(\.\d+)?(px|rem|em|%|vh|vw)$/', $value);

            case 'number':
                // Validace číselné hodnoty (int nebo float)
                return is_numeric($value);

            case 'font':
            case 'text':
                // Textové hodnoty - pouze základní validace délky
                return strlen($value) > 0 && strlen($value) <= 1000;

            default:
                return false;
        }
    }

    /**
     * Bezpečná aktualizace s validací
     *
     * @param string $key
     * @param string $value
     * @return array - ['success' => bool, 'message' => string]
     */
    public function safeUpdate(string $key, string $value): array
    {
        // Načtení nastavení pro zjištění typu
        $setting = $this->getByKey($key);

        if (!$setting) {
            return [
                'success' => false,
                'message' => 'Nastavení nebylo nalezeno'
            ];
        }

        // Validace hodnoty
        if (!$this->validateValue($setting['type'], $value)) {
            return [
                'success' => false,
                'message' => 'Neplatná hodnota pro typ ' . $setting['type']
            ];
        }

        // Aktualizace
        $success = $this->updateValue($key, $value);

        return [
            'success' => $success,
            'message' => $success ? 'Nastavení úspěšně aktualizováno' : 'Chyba při aktualizaci'
        ];
    }
}
