-- =====================================================
-- Migration 007: Add new hygge colors
-- =====================================================
-- Adds 8 hygge colors to the theme palette:
-- Missing from original seed:
-- - Sage green (#A7C7B1)
-- - Coral red (#D3A08F)
-- - Teal (#9EC4C0)
-- New colors:
-- - Moss green (#87A878)
-- - Dark fjord blue (#6B8FA3)
-- - Raspberry (#C2727E)
-- - Sage dark (#849E8F)
-- - Lavender (#B8A9C4)
-- =====================================================

-- 1. Add missing original colors (weren't in production DB)
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
('color_green', '#A7C7B1', 'colors', 'color', 'Zelená (šalvějová)', 'Jemná šalvějová zelená'),
('color_red_alt', '#D3A08F', 'colors', 'color', 'Červená (koralová)', 'Jemnější korálový odstín červené'),
('color_teal', '#9EC4C0', 'colors', 'color', 'Modrozelená', 'Tichá modrozelená v hygge paletě')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 2. Add new extended hygge colors
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
('color_moss', '#87A878', 'colors', 'color', 'Zelená (mechová)', 'Teplá mechová/lesní zelená'),
('color_fjord_dark', '#6B8FA3', 'colors', 'color', 'Modrá (fjordová tmavá)', 'Hlubší modrozelená jako hluboký fjord'),
('color_raspberry', '#C2727E', 'colors', 'color', 'Růžová (malinová)', 'Růžovo-červená malinová'),
('color_sage_dark', '#849E8F', 'colors', 'color', 'Zelená (šedozelená)', 'Tlumená šalvějová zelená'),
('color_lavender', '#B8A9C4', 'colors', 'color', 'Levandulová', 'Jemná levandulová fialová')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 2. Update widget schemas with new color options
-- Note: The new color options need to be added to all widgets that have backgroundColor/tagColor selects

-- 2.1 Update feature_cards_grid
UPDATE widget_types SET schema = '{"fields":[{"key":"sectionTitle","type":"text","label":"Nadpis sekce","translatable":true,"required":false},{"key":"sectionSubtitle","type":"text","label":"Podnadpis sekce","translatable":true,"required":false},{"key":"cards","type":"repeater","label":"Karty","translatable":true,"required":true,"min":2,"max":4,"fields":[{"key":"title","type":"text","label":"Nadpis karty","required":true},{"key":"text","type":"wysiwyg","label":"Text karty","required":true},{"key":"backgroundColor","type":"select","label":"Barva pozadí","required":true,"options":[{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]}]}]}'
WHERE type_key = 'feature_cards_grid';

-- 2.2 Update feature_cards_with_image (has both tagColor and backgroundColor)
UPDATE widget_types SET schema = '{"fields":[{"key":"sectionTitle","type":"text","label":"Nadpis sekce","translatable":true,"required":false},{"key":"sectionSubtitle","type":"text","label":"Podnadpis sekce","translatable":true,"required":false},{"key":"cards","type":"repeater","label":"Karty","translatable":true,"required":true,"min":1,"max":3,"fields":[{"key":"image","type":"image","label":"Obrázek","required":true},{"key":"title","type":"text","label":"Nadpis","required":true},{"key":"tags","type":"repeater","label":"Štítky","required":false,"min":0,"max":8,"fields":[{"key":"text","type":"text","label":"Text štítku","required":true},{"key":"tagColor","type":"select","label":"Barva štítku","required":true,"options":[{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]}]},{"key":"text","type":"wysiwyg","label":"Text","required":true},{"key":"backgroundColor","type":"select","label":"Barva pozadí","required":true,"options":[{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]},{"key":"buttonText","type":"text","label":"Text tlačítka (volitelné)","required":false},{"key":"buttonUrl","type":"text","label":"URL tlačítka (volitelné)","required":false}]}]}'
WHERE type_key = 'feature_cards_with_image';

-- 2.3 Update asymmetric_text_video
UPDATE widget_types SET schema = '{"fields":[{"key":"text","type":"wysiwyg","label":"Text (barevný blok)","translatable":true,"required":true},{"key":"textBackgroundColor","type":"select","label":"Barva textového bloku","translatable":false,"required":false,"default":"var(--color-primary)","options":[{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]},{"key":"videoId","type":"text","label":"YouTube Video ID","translatable":false,"required":true},{"key":"aspectRatio","type":"select","label":"Poměr stran videa","translatable":false,"required":false,"default":"16:9","options":[{"value":"16:9","label":"16:9"},{"value":"4:3","label":"4:3"}]}]}'
WHERE type_key = 'asymmetric_text_video';

-- 2.4 Update feature_cards_universal
UPDATE widget_types SET schema = '{"fields":[{"key":"sectionTitle","type":"text","label":"Nadpis sekce","translatable":true,"required":false},{"key":"sectionSubtitle","type":"text","label":"Podnadpis sekce","translatable":true,"required":false},{"key":"columns","type":"select","label":"Počet sloupců (desktop)","translatable":false,"required":true,"options":[{"value":"1","label":"1 sloupec"},{"value":"2","label":"2 sloupce"},{"value":"3","label":"3 sloupce"},{"value":"4","label":"4 sloupce"}]},{"key":"cards","type":"repeater","label":"Karty","translatable":true,"required":true,"min":1,"fields":[{"key":"image","type":"image","label":"Obrázek (volitelné)","required":false},{"key":"title","type":"text","label":"Nadpis","required":true},{"key":"subtitle","type":"text","label":"Podnadpis (volitelné)","required":false},{"key":"text","type":"wysiwyg","label":"Text","required":true},{"key":"backgroundColor","type":"select","label":"Barva pozadí","required":true,"options":[{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]},{"key":"buttonText","type":"text","label":"Text tlačítka (volitelné)","required":false},{"key":"buttonUrl","type":"text","label":"URL tlačítka (volitelné)","required":false}]}]}'
WHERE type_key = 'feature_cards_universal';

-- 2.5 Update image_text
UPDATE widget_types SET schema = '{"fields":[{"key":"sectionTitle","type":"text","label":"Nadpis sekce (H2)","translatable":true,"required":false},{"key":"sectionSubtitle","type":"text","label":"Podnadpis sekce (H3)","translatable":true,"required":false},{"key":"imagePosition","type":"select","label":"Pozice fotky","translatable":false,"required":false,"default":"left","options":[{"value":"left","label":"Nalevo"},{"value":"right","label":"Napravo"}]},{"key":"image","type":"image","label":"Fotka","translatable":false,"required":true},{"key":"textTitle","type":"text","label":"Nadpis textu (H4)","translatable":true,"required":false},{"key":"textContent","type":"wysiwyg","label":"Text","translatable":true,"required":true},{"key":"backgroundColor","type":"select","label":"Barva pozadí","translatable":false,"required":false,"default":"transparent","options":[{"value":"transparent","label":"Průhledná (výchozí)"},{"value":"var(--color-brown)","label":"Písková"},{"value":"var(--color-yellow)","label":"Medová"},{"value":"var(--color-blue)","label":"Fjordová"},{"value":"var(--color-teal)","label":"Modrozelená"},{"value":"var(--color-red)","label":"Terakota"},{"value":"var(--color-red-alt)","label":"Červená (koralová)"},{"value":"var(--color-primary)","label":"Zelená"},{"value":"var(--color-green)","label":"Zelená (šalvějová)"},{"value":"var(--color-moss)","label":"Zelená (mechová)"},{"value":"var(--color-fjord-dark)","label":"Modrá (fjordová tmavá)"},{"value":"var(--color-raspberry)","label":"Růžová (malinová)"},{"value":"var(--color-sage-dark)","label":"Zelená (šedozelená)"},{"value":"var(--color-lavender)","label":"Levandulová"}]}]}'
WHERE type_key = 'image_text';

-- =====================================================
-- After running this migration:
-- 1. Go to Admin -> Theme Settings
-- 2. Click "Uložit změny a regenerovat CSS" to regenerate theme.css
-- =====================================================
