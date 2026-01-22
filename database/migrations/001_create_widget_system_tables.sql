-- =====================================================
-- MIGRATION: Widget Page Builder System
-- =====================================================
-- Vytvoření tabulek pro widget systém:
-- - widget_types: Registry dostupných typů widgetů
-- - page_widgets: Widgety přiřazené k pages
-- - page_widget_translations: Přeložený obsah widgetů
-- =====================================================

-- 1. WIDGET TYPES REGISTRY
-- Registry všech dostupných typů widgetů
CREATE TABLE IF NOT EXISTS `widget_types` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type_key` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unikátní klíč widget typu (např. hero, quote)',
    `label` VARCHAR(100) NOT NULL COMMENT 'Zobrazovací název v admin panelu',
    `component_path` VARCHAR(255) NOT NULL COMMENT 'Cesta k PHP komponentě (např. components/hero.php)',
    `icon` VARCHAR(50) DEFAULT NULL COMMENT 'Ikona pro admin UI',
    `category` ENUM('layout', 'content', 'media', 'dynamic') DEFAULT 'content' COMMENT 'Kategorie widgetu',
    `schema` JSON NOT NULL COMMENT 'JSON schema definující formulářová pole',
    `is_active` BOOLEAN DEFAULT 1 COMMENT 'Aktivní/neaktivní widget typ',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_type_key` (`type_key`),
    INDEX `idx_category` (`category`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registry typů widgetů dostupných v page builderu';

-- 2. PAGE WIDGETS
-- Widgety přiřazené ke konkrétním pages
CREATE TABLE IF NOT EXISTS `page_widgets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT UNSIGNED NOT NULL COMMENT 'ID stránky (FK na pages.id)',
    `widget_type_key` VARCHAR(50) NOT NULL COMMENT 'Typ widgetu (FK na widget_types.type_key)',
    `display_order` INT NOT NULL DEFAULT 0 COMMENT 'Pořadí widgetu na stránce (0-N)',
    `is_active` BOOLEAN DEFAULT 1 COMMENT 'Aktivní/skrytý widget',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`widget_type_key`) REFERENCES `widget_types`(`type_key`) ON DELETE RESTRICT,
    INDEX `idx_page_order` (`page_id`, `display_order`),
    INDEX `idx_page_active` (`page_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Widgety přiřazené k jednotlivým stránkám';

-- 3. PAGE WIDGET TRANSLATIONS
-- Překlady obsahu widgetů (CS/EN)
CREATE TABLE IF NOT EXISTS `page_widget_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_widget_id` INT UNSIGNED NOT NULL COMMENT 'ID widgetu (FK na page_widgets.id)',
    `language` VARCHAR(5) NOT NULL COMMENT 'Kód jazyka (cs, en)',
    `settings` JSON NOT NULL COMMENT 'Widget-specific nastavení (texty, obrázky, URL)',
    FOREIGN KEY (`page_widget_id`) REFERENCES `page_widgets`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_widget_lang` (`page_widget_id`, `language`),
    INDEX `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Přeložený obsah widgetů pro multijazyčnost';

-- =====================================================
-- PŘÍKLAD DAT PRO REFERENCI:
-- =====================================================
--
-- INSERT INTO widget_types:
-- {
--   "type_key": "hero",
--   "label": "Hero sekce",
--   "component_path": "components/hero.php",
--   "schema": {
--     "fields": [
--       {"key": "title", "type": "text", "label": "Nadpis", "translatable": true}
--     ]
--   }
-- }
--
-- INSERT INTO page_widgets:
-- page_id=1, widget_type_key='hero', display_order=0
--
-- INSERT INTO page_widget_translations:
-- language='cs', settings='{"title": "Vítejte", "subtitle": "..."}'
-- =====================================================
