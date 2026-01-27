-- =====================================================
-- MIGRATION: Add slug_en column to pages table
-- =====================================================
-- Adds English slug support for bilingual pages
-- =====================================================

ALTER TABLE `pages`
ADD COLUMN `slug_en` VARCHAR(100) NULL AFTER `slug`,
ADD INDEX `idx_slug_en` (`slug_en`);

-- Update existing pages: set slug_en to same as slug if they have English translation
UPDATE `pages` p
INNER JOIN `page_translations` pt ON p.id = pt.page_id AND pt.language = 'en'
SET p.slug_en = p.slug
WHERE p.slug_en IS NULL AND pt.title IS NOT NULL AND pt.title != '';
