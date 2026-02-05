-- =====================================================
-- MIGRATION 011: Add Guides (Průvodci) Widget Type
-- =====================================================
-- Nový widget pro zobrazení týmu průvodců
-- Grid 4x na desktopu, 2x na mobilu
-- Každý průvodce: fotka (kruh), pozice, citát
-- =====================================================

INSERT INTO `widget_types` (`type_key`, `label`, `component_path`, `icon`, `category`, `schema`) VALUES
('guides', 'Průvodci', 'components/guides.php', '👥', 'content', '{"fields":[{"key":"sectionTitle","type":"text","label":"Nadpis sekce","translatable":true,"required":false},{"key":"sectionSubtitle","type":"text","label":"Podnadpis sekce","translatable":true,"required":false},{"key":"guides","type":"repeater","label":"Průvodci","translatable":true,"required":true,"min":1,"max":30,"fields":[{"key":"photo","type":"image","label":"Fotka","required":true},{"key":"name","type":"text","label":"Jméno","required":true},{"key":"position","type":"text","label":"Pozice","required":true},{"key":"quote","type":"textarea","label":"Citát","required":false}]}]}');

-- =====================================================
-- Migration completed - 1 widget type inserted
-- =====================================================
