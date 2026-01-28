-- Migration 010: Add sessions table for DB-backed PHP sessions
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `data` MEDIUMBLOB NOT NULL,
    `last_activity` INT UNSIGNED NOT NULL,
    INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
