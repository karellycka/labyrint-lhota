-- Databázové schéma pro web školy Labyrint
-- Verze: 1.0
-- Datum: 2026-01-20

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- USERS TABLE
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor') DEFAULT 'editor',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PAGES TABLE (Statické stránky)
-- =====================================================
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL,
    `template` VARCHAR(50) DEFAULT 'default',
    `published` BOOLEAN DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_slug` (`slug`),
    INDEX `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PAGE TRANSLATIONS
-- =====================================================
DROP TABLE IF EXISTS `page_translations`;
CREATE TABLE `page_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `meta_description` VARCHAR(320),
    `meta_keywords` VARCHAR(255),
    `content` TEXT,
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_page_lang` (`page_id`, `language`),
    INDEX `idx_language` (`language`),
    FULLTEXT INDEX `idx_content` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG POSTS
-- =====================================================
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(150) NOT NULL,
    `author_id` INT UNSIGNED NOT NULL,
    `featured_image` VARCHAR(255),
    `published` BOOLEAN DEFAULT 0,
    `published_at` TIMESTAMP NULL,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_slug` (`slug`),
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`),
    INDEX `idx_published` (`published`, `published_at`),
    INDEX `idx_author` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG TRANSLATIONS
-- =====================================================
DROP TABLE IF EXISTS `blog_translations`;
CREATE TABLE `blog_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `excerpt` TEXT,
    `content` MEDIUMTEXT NOT NULL,
    `meta_description` VARCHAR(320),
    FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_post_lang` (`post_id`, `language`),
    INDEX `idx_language` (`language`),
    FULLTEXT INDEX `idx_search` (`title`, `excerpt`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG CATEGORIES
-- =====================================================
DROP TABLE IF EXISTS `blog_categories`;
CREATE TABLE `blog_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL,
    UNIQUE KEY `unique_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG CATEGORY TRANSLATIONS
-- =====================================================
DROP TABLE IF EXISTS `blog_category_translations`;
CREATE TABLE `blog_category_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_cat_lang` (`category_id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG POST CATEGORIES (Pivot)
-- =====================================================
DROP TABLE IF EXISTS `blog_post_categories`;
CREATE TABLE `blog_post_categories` (
    `post_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`post_id`, `category_id`),
    FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE CASCADE,
    INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EVENTS
-- =====================================================
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(150) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE,
    `start_time` TIME,
    `end_time` TIME,
    `location` VARCHAR(200),
    `featured_image` VARCHAR(255),
    `published` BOOLEAN DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_slug` (`slug`),
    INDEX `idx_dates` (`start_date`, `end_date`),
    INDEX `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EVENT TRANSLATIONS
-- =====================================================
DROP TABLE IF EXISTS `event_translations`;
CREATE TABLE `event_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_event_lang` (`event_id`, `language`),
    INDEX `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDIA
-- =====================================================
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `width` INT UNSIGNED,
    `height` INT UNSIGNED,
    `type` ENUM('image', 'document', 'video') DEFAULT 'image',
    `folder` VARCHAR(100) DEFAULT 'general',
    `uploaded_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_type` (`type`),
    INDEX `idx_folder` (`folder`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDIA TRANSLATIONS
-- =====================================================
DROP TABLE IF EXISTS `media_translations`;
CREATE TABLE `media_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `media_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `alt_text` VARCHAR(200),
    `caption` TEXT,
    FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_media_lang` (`media_id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- I18N TRANSLATIONS (Statické řetězce)
-- =====================================================
DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `value` TEXT NOT NULL,
    `context` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_key_lang` (`key_name`, `language`),
    INDEX `idx_language` (`language`),
    INDEX `idx_context` (`context`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CONTACT FORM SUBMISSIONS
-- =====================================================
DROP TABLE IF EXISTS `contact_submissions`;
CREATE TABLE `contact_submissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(200),
    `message` TEXT NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(255),
    `status` ENUM('new', 'read', 'archived') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SEO METADATA
-- =====================================================
DROP TABLE IF EXISTS `seo_metadata`;
CREATE TABLE `seo_metadata` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(5) NOT NULL,
    `meta_title` VARCHAR(70),
    `meta_description` VARCHAR(320),
    `og_title` VARCHAR(100),
    `og_description` VARCHAR(200),
    `og_image` VARCHAR(255),
    `canonical_url` VARCHAR(255),
    `robots` VARCHAR(50) DEFAULT 'index,follow',
    UNIQUE KEY `unique_entity` (`entity_type`, `entity_id`, `language`),
    INDEX `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CACHE TABLE
-- =====================================================
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
    `cache_key` VARCHAR(255) PRIMARY KEY,
    `cache_value` MEDIUMTEXT NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- SEED DATA - Výchozí admin uživatel
-- Heslo: admin123 (ZMĚNIT PO PRVNÍM PŘIHLÁŠENÍ!)
-- =====================================================
-- Seed Data: Admin User
-- Default credentials: admin / admin123
-- ⚠️ CHANGE PASSWORD AFTER FIRST LOGIN!
-- =====================================================
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@labyrint.cz', '$2y$10$Qa8LbgMVUl4jDQjaWu8VseQOLbzMYB83XpZ52vrAxRrJiKFZbbLPG', 'admin');

-- =====================================================
-- SEED DATA - Základní kategorie blogu
-- =====================================================
INSERT INTO `blog_categories` (`slug`) VALUES
('aktuality'),
('akce'),
('uspechy'),
('oznameni');

INSERT INTO `blog_category_translations` (`category_id`, `language`, `name`) VALUES
(1, 'cs', 'Aktuality'),
(1, 'en', 'News'),
(2, 'cs', 'Akce'),
(2, 'en', 'Events'),
(3, 'cs', 'Úspěchy žáků'),
(3, 'en', 'Student Achievements'),
(4, 'cs', 'Oznámení'),
(4, 'en', 'Announcements');

-- =====================================================
-- SEED DATA - Základní i18n řetězce
-- =====================================================
INSERT INTO `translations` (`key_name`, `language`, `value`, `context`) VALUES
-- Navigation
('nav.home', 'cs', 'Domů', 'navigation'),
('nav.home', 'en', 'Home', 'navigation'),
('nav.about', 'cs', 'O škole', 'navigation'),
('nav.about', 'en', 'About Us', 'navigation'),
('nav.blog', 'cs', 'Blog', 'navigation'),
('nav.blog', 'en', 'Blog', 'navigation'),
('nav.events', 'cs', 'Události', 'navigation'),
('nav.events', 'en', 'Events', 'navigation'),
('nav.gallery', 'cs', 'Galerie', 'navigation'),
('nav.gallery', 'en', 'Gallery', 'navigation'),
('nav.contact', 'cs', 'Kontakt', 'navigation'),
('nav.contact', 'en', 'Contact', 'navigation'),

-- Common
('common.read_more', 'cs', 'Číst více', 'general'),
('common.read_more', 'en', 'Read more', 'general'),
('common.submit', 'cs', 'Odeslat', 'general'),
('common.submit', 'en', 'Submit', 'general'),
('common.cancel', 'cs', 'Zrušit', 'general'),
('common.cancel', 'en', 'Cancel', 'general'),
('common.save', 'cs', 'Uložit', 'general'),
('common.save', 'en', 'Save', 'general'),
('common.delete', 'cs', 'Smazat', 'general'),
('common.delete', 'en', 'Delete', 'general'),
('common.edit', 'cs', 'Upravit', 'general'),
('common.edit', 'en', 'Edit', 'general'),

-- Contact form
('contact.name', 'cs', 'Jméno', 'forms'),
('contact.name', 'en', 'Name', 'forms'),
('contact.email', 'cs', 'E-mail', 'forms'),
('contact.email', 'en', 'Email', 'forms'),
('contact.subject', 'cs', 'Předmět', 'forms'),
('contact.subject', 'en', 'Subject', 'forms'),
('contact.message', 'cs', 'Zpráva', 'forms'),
('contact.message', 'en', 'Message', 'forms'),
('contact.send', 'cs', 'Odeslat zprávu', 'forms'),
('contact.send', 'en', 'Send message', 'forms'),
('contact.success', 'cs', 'Zpráva byla úspěšně odeslána', 'forms'),
('contact.success', 'en', 'Message sent successfully', 'forms');
