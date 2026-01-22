-- Tabulka pro citace
CREATE TABLE IF NOT EXISTS `quotes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabulka pro překlady citací
CREATE TABLE IF NOT EXISTS `quote_translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `quote_id` INT UNSIGNED NOT NULL,
    `language` VARCHAR(2) NOT NULL,
    `text` TEXT NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `role` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`quote_id`) REFERENCES `quotes`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_quote_language` (`quote_id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vložení ukázkové citace Michaely Macháčové
INSERT INTO `quotes` (`is_active`, `display_order`) VALUES (1, 1);

INSERT INTO `quote_translations` (`quote_id`, `language`, `text`, `author`, `role`) VALUES
(1, 'cs', 'Žáka z Labyrintu poznáte snadno. Má o sebe, druhé lidi a svět opravdový zájem – není lhostejný.', 'Michaela Macháčová', 'ředitelka Labyrintu'),
(1, 'en', 'You can easily recognize a student from Labyrinth. They have genuine interest in themselves, other people, and the world – they are not indifferent.', 'Michaela Macháčová', 'Director of Labyrinth');
