-- Theme Settings tabulka pro centralizovanou správu designu
-- Umožňuje editaci všech design parametrů z admin rozhraní

CREATE TABLE IF NOT EXISTS theme_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    category ENUM('colors', 'typography', 'spacing', 'effects') NOT NULL,
    type ENUM('color', 'font', 'size', 'number', 'text') NOT NULL,
    label VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA - Výchozí Hygge/Severský design
-- ============================================

-- ====== BARVY (Colors) ======

-- Primární barvy (Brand colors)
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
('color_primary', '#00792E', 'colors', 'color', 'Primární zelená', 'Hlavní zelená barva z loga'),
('color_secondary', '#2C323A', 'colors', 'color', 'Sekundární tmavě šedá', 'Tmavě šedá barva pro text a tmavé sekce'),

-- Doplňkové hygge barvy
('color_brown', '#9C8672', 'colors', 'color', 'Hnědá (písková)', 'Teplá přírodní hnědá jako len nebo dřevo'),
('color_yellow', '#D4A574', 'colors', 'color', 'Žlutá (medová)', 'Teplá medová žlutá'),
('color_blue', '#8BA5B2', 'colors', 'color', 'Modrá (fjordová)', 'Chladná šedomodrá jako severské nebe'),
('color_red', '#B8664D', 'colors', 'color', 'Červená (terakota)', 'Teplá cihlová/rezavá červená'),

-- Sémantické barvy
('color_success', '#4A7C59', 'colors', 'color', 'Success (úspěch)', 'Barva pro pozitivní zprávy a úspěšné akce'),
('color_error', '#B8664D', 'colors', 'color', 'Error (chyba)', 'Barva pro chybové hlášky'),
('color_warning', '#D4A574', 'colors', 'color', 'Warning (varování)', 'Barva pro varování'),
('color_info', '#8BA5B2', 'colors', 'color', 'Info (informace)', 'Barva pro informační zprávy'),

-- Textové barvy
('color_text', '#2C323A', 'colors', 'color', 'Text primární', 'Hlavní barva textu'),
('color_text_light', '#6B7280', 'colors', 'color', 'Text světlý', 'Světlejší text (popisky, meta info)'),
('color_text_muted', '#9CA3AF', 'colors', 'color', 'Text ztlumený', 'Velmi světlý text (deaktivované prvky)'),

-- Pozadí
('color_bg_light', '#F5F1EB', 'colors', 'color', 'Pozadí světlé', 'Světle béžová pro pozadí sekcí'),
('color_bg_white', '#FDFBF7', 'colors', 'color', 'Pozadí bílé', 'Krémově bílá pro karty a kontejnery'),
('color_bg_dark', '#2C323A', 'colors', 'color', 'Pozadí tmavé', 'Tmavé pozadí pro footer a tmavé sekce'),

-- Bordery a oddělovače
('color_border_light', '#E5E7EB', 'colors', 'color', 'Border světlý', 'Světlý border pro karty'),
('color_border_medium', '#D1D5DB', 'colors', 'color', 'Border střední', 'Střední border'),
('color_border_dark', '#9CA3AF', 'colors', 'color', 'Border tmavý', 'Tmavý border pro zvýraznění'),

-- Overlay a tóny
('color_overlay_dark', 'rgba(44, 50, 58, 0.6)', 'colors', 'color', 'Overlay tmavý', 'Tmavý overlay pro hero sekce přes obrázky'),
('color_overlay_light', 'rgba(255, 255, 255, 0.9)', 'colors', 'color', 'Overlay světlý', 'Světlý overlay');

-- ====== TYPOGRAFIE (Typography) ======
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
-- Font rodiny
('font_family_base', '"Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif', 'typography', 'font', 'Font Base', 'Základní font pro text - Nunito (hygge rounded sans-serif)'),
('font_family_heading', '"Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif', 'typography', 'font', 'Font Nadpisy', 'Font pro nadpisy - Nunito (hygge rounded sans-serif)'),
('font_family_mono', 'Menlo, Monaco, Consolas, "Courier New", monospace', 'typography', 'font', 'Font Monospace', 'Monospace font pro kód'),

-- Font velikosti
('font_size_base', '16px', 'typography', 'size', 'Font Size Base', 'Základní velikost textu'),
('font_size_sm', '14px', 'typography', 'size', 'Font Size Small', 'Malý text'),
('font_size_xs', '12px', 'typography', 'size', 'Font Size Extra Small', 'Extra malý text'),
('font_size_lg', '18px', 'typography', 'size', 'Font Size Large', 'Větší text'),
('font_size_xl', '20px', 'typography', 'size', 'Font Size Extra Large', 'Extra velký text'),

-- Nadpisy
('font_size_h1', '48px', 'typography', 'size', 'H1 velikost', 'Velikost H1 nadpisu'),
('font_size_h2', '36px', 'typography', 'size', 'H2 velikost', 'Velikost H2 nadpisu'),
('font_size_h3', '28px', 'typography', 'size', 'H3 velikost', 'Velikost H3 nadpisu'),
('font_size_h4', '24px', 'typography', 'size', 'H4 velikost', 'Velikost H4 nadpisu'),
('font_size_h5', '20px', 'typography', 'size', 'H5 velikost', 'Velikost H5 nadpisu'),
('font_size_h6', '16px', 'typography', 'size', 'H6 velikost', 'Velikost H6 nadpisu'),

-- Font weights
('font_weight_normal', '400', 'typography', 'number', 'Font Weight Normal', 'Normální tloušťka písma'),
('font_weight_medium', '500', 'typography', 'number', 'Font Weight Medium', 'Střední tloušťka písma'),
('font_weight_semibold', '600', 'typography', 'number', 'Font Weight Semibold', 'Polo-tučné písmo'),
('font_weight_bold', '700', 'typography', 'number', 'Font Weight Bold', 'Tučné písmo'),

-- Line heights
('line_height_tight', '1.25', 'typography', 'number', 'Line Height Tight', 'Těsný řádkový proklad (nadpisy)'),
('line_height_normal', '1.5', 'typography', 'number', 'Line Height Normal', 'Normální řádkový proklad'),
('line_height_relaxed', '1.75', 'typography', 'number', 'Line Height Relaxed', 'Uvolněný řádkový proklad'),
('line_height_loose', '2', 'typography', 'number', 'Line Height Loose', 'Volný řádkový proklad');

-- ====== SPACING & LAYOUT (Spacing) ======
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
-- Border radius
('border_radius', '16px', 'spacing', 'size', 'Border Radius', 'Základní zaoblení rohů (flat design)'),
('border_radius_sm', '8px', 'spacing', 'size', 'Border Radius Small', 'Malé zaoblení'),
('border_radius_lg', '24px', 'spacing', 'size', 'Border Radius Large', 'Velké zaoblení'),
('border_radius_full', '9999px', 'spacing', 'size', 'Border Radius Full', 'Plné zaoblení (kruhy)'),

-- Container
('container_width', '1200px', 'spacing', 'size', 'Container Width', 'Maximální šířka kontejneru'),
('container_padding', '20px', 'spacing', 'size', 'Container Padding', 'Padding kontejneru na mobilu'),

-- Spacing scale
('spacing_xs', '4px', 'spacing', 'size', 'Spacing XS', 'Extra malý odstup'),
('spacing_sm', '8px', 'spacing', 'size', 'Spacing Small', 'Malý odstup'),
('spacing_md', '16px', 'spacing', 'size', 'Spacing Medium', 'Střední odstup'),
('spacing_lg', '24px', 'spacing', 'size', 'Spacing Large', 'Velký odstup'),
('spacing_xl', '32px', 'spacing', 'size', 'Spacing XL', 'Extra velký odstup'),
('spacing_2xl', '48px', 'spacing', 'size', 'Spacing 2XL', '2x extra velký odstup'),
('spacing_3xl', '64px', 'spacing', 'size', 'Spacing 3XL', '3x extra velký odstup'),
('spacing_4xl', '80px', 'spacing', 'size', 'Spacing 4XL', '4x extra velký odstup'),

-- Section spacing
('section_padding_mobile', '60px', 'spacing', 'size', 'Section Padding Mobile', 'Vertikální padding sekcí na mobilu'),
('section_padding_desktop', '80px', 'spacing', 'size', 'Section Padding Desktop', 'Vertikální padding sekcí na desktopu'),

-- Grid gaps
('grid_gap_sm', '16px', 'spacing', 'size', 'Grid Gap Small', 'Malá mezera v gridu'),
('grid_gap_md', '24px', 'spacing', 'size', 'Grid Gap Medium', 'Střední mezera v gridu'),
('grid_gap_lg', '32px', 'spacing', 'size', 'Grid Gap Large', 'Velká mezera v gridu');

-- ====== EFEKTY (Effects) ======
INSERT INTO theme_settings (setting_key, setting_value, category, type, label, description) VALUES
-- Shadows
('shadow_sm', '0 1px 2px 0 rgba(0, 0, 0, 0.05)', 'effects', 'text', 'Shadow Small', 'Malý stín'),
('shadow_md', '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)', 'effects', 'text', 'Shadow Medium', 'Střední stín'),
('shadow_lg', '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)', 'effects', 'text', 'Shadow Large', 'Velký stín'),
('shadow_xl', '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)', 'effects', 'text', 'Shadow XL', 'Extra velký stín'),

-- Transitions
('transition_speed_fast', '150ms', 'effects', 'text', 'Transition Fast', 'Rychlá animace'),
('transition_speed_normal', '300ms', 'effects', 'text', 'Transition Normal', 'Normální animace'),
('transition_speed_slow', '500ms', 'effects', 'text', 'Transition Slow', 'Pomalá animace'),
('transition_timing', 'cubic-bezier(0.4, 0, 0.2, 1)', 'effects', 'text', 'Transition Timing', 'Timing funkce pro animace'),

-- Gradients
('gradient_angle', '135deg', 'effects', 'text', 'Gradient Angle', 'Úhel gradientu'),
('gradient_primary', 'linear-gradient(135deg, var(--color-primary) 0%, var(--color-blue) 100%)', 'effects', 'text', 'Gradient Primary', 'Primární gradient (zelená-modrá)'),
('gradient_warm', 'linear-gradient(135deg, var(--color-yellow) 0%, var(--color-red) 100%)', 'effects', 'text', 'Gradient Warm', 'Teplý gradient (žlutá-červená)'),
('gradient_earth', 'linear-gradient(135deg, var(--color-brown) 0%, var(--color-yellow) 100%)', 'effects', 'text', 'Gradient Earth', 'Zemitý gradient (hnědá-žlutá)'),

-- Opacity
('opacity_disabled', '0.5', 'effects', 'number', 'Opacity Disabled', 'Průhlednost pro deaktivované prvky'),
('opacity_hover', '0.8', 'effects', 'number', 'Opacity Hover', 'Průhlednost při hoveru');
