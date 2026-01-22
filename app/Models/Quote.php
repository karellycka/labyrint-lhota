<?php

namespace App\Models;

use App\Core\Model;

/**
 * Quote Model - Citace s překlady
 */
class Quote extends Model
{
    protected string $table = 'quotes';

    /**
     * Získá aktivní citaci s překladem
     */
    public function getActive(string $language): ?object
    {
        $sql = "
            SELECT q.*, qt.text, qt.author, qt.role
            FROM quotes q
            JOIN quote_translations qt ON q.id = qt.quote_id
            WHERE q.is_active = 1
              AND qt.language = ?
            ORDER BY q.display_order ASC
            LIMIT 1
        ";

        return $this->querySingle($sql, [$language]);
    }

    /**
     * Získá všechny citace pro admin
     */
    public function getAll(): array
    {
        $sql = "
            SELECT q.*,
                   qt_cs.text as text_cs, qt_cs.author as author_cs,
                   qt_en.text as text_en, qt_en.author as author_en
            FROM quotes q
            LEFT JOIN quote_translations qt_cs ON q.id = qt_cs.quote_id AND qt_cs.language = 'cs'
            LEFT JOIN quote_translations qt_en ON q.id = qt_en.quote_id AND qt_en.language = 'en'
            ORDER BY q.display_order ASC, q.id DESC
        ";

        return $this->query($sql);
    }

    /**
     * Najde citaci podle ID s překlady
     */
    public function findWithTranslations(int $id): ?array
    {
        $quote = $this->find($id);
        if (!$quote) {
            return null;
        }

        $translations = $this->getTranslations($id);

        return [
            'quote' => $quote,
            'translations' => $translations
        ];
    }

    /**
     * Získá všechny překlady pro citaci
     */
    public function getTranslations(int $quoteId): array
    {
        $sql = "
            SELECT * FROM quote_translations
            WHERE quote_id = ?
            ORDER BY language ASC
        ";

        $results = $this->query($sql, [$quoteId]);

        // Konverze do asociativního pole s jazykem jako klíčem
        $translations = [];
        foreach ($results as $translation) {
            $translations[$translation->language] = $translation;
        }

        return $translations;
    }

    /**
     * Vytvoří novou citaci s překlady
     */
    public function createWithTranslations(array $data, array $translations): ?int
    {
        // Vytvoření záznamu citace
        $quoteId = $this->insert([
            'is_active' => $data['is_active'] ?? 1,
            'display_order' => $data['display_order'] ?? 0
        ]);

        if (!$quoteId) {
            return null;
        }

        // Přidání překladů
        foreach ($translations as $language => $translation) {
            $this->db->query(
                "INSERT INTO quote_translations (quote_id, language, text, author, role)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $quoteId,
                    $language,
                    $translation['text'],
                    $translation['author'],
                    $translation['role'] ?? null
                ]
            );
        }

        return $quoteId;
    }

    /**
     * Aktualizuje citaci s překlady
     */
    public function updateWithTranslations(int $id, array $data, array $translations): bool
    {
        // Aktualizace záznamu citace
        $updated = $this->update($id, [
            'is_active' => $data['is_active'] ?? 1,
            'display_order' => $data['display_order'] ?? 0
        ]);

        if (!$updated) {
            return false;
        }

        // Aktualizace překladů
        foreach ($translations as $language => $translation) {
            // Kontrola existence překladu
            $existing = $this->querySingle(
                "SELECT id FROM quote_translations WHERE quote_id = ? AND language = ?",
                [$id, $language]
            );

            if ($existing) {
                // Aktualizace existujícího překladu
                $this->db->query(
                    "UPDATE quote_translations
                     SET text = ?, author = ?, role = ?
                     WHERE quote_id = ? AND language = ?",
                    [
                        $translation['text'],
                        $translation['author'],
                        $translation['role'] ?? null,
                        $id,
                        $language
                    ]
                );
            } else {
                // Vytvoření nového překladu
                $this->db->query(
                    "INSERT INTO quote_translations (quote_id, language, text, author, role)
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $id,
                        $language,
                        $translation['text'],
                        $translation['author'],
                        $translation['role'] ?? null
                    ]
                );
            }
        }

        return true;
    }

    /**
     * Smaže citaci včetně překladů (CASCADE)
     */
    public function deleteQuote(int $id): bool
    {
        return $this->delete($id);
    }
}
