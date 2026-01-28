-- =====================================================
-- MIGRATION 008: Add Highlights Widget Type
-- =====================================================
-- Nový widget pro zobrazení statistik/highlights
-- Zelený pruh s 3 sloupci - velké číslo + popisek
-- =====================================================

INSERT INTO `widget_types` (`type_key`, `label`, `component_path`, `icon`, `category`, `schema`) VALUES
('highlights', 'Highlights (statistiky)', 'components/highlights.php', '📊', 'content', '{"fields":[{"key":"highlights","type":"repeater","label":"Položky","translatable":true,"required":true,"min":1,"max":6,"fields":[{"key":"value","type":"text","label":"Hodnota (velké číslo)","required":true},{"key":"label","type":"text","label":"Popisek","required":true}]}]}');

-- =====================================================
-- Migration completed - 1 widget type inserted
-- =====================================================
