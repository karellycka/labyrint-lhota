-- Migration: Add Cloudinary support to media table
-- Created: 2026-01-25
-- Description: Adds cloudinary_public_id column to media table for CDN integration

-- Add cloudinary_public_id column to media table
ALTER TABLE `media`
ADD COLUMN `cloudinary_public_id` VARCHAR(255) NULL AFTER `uploaded_by`,
ADD INDEX `idx_cloudinary_public_id` (`cloudinary_public_id`);

-- Update folder column comment
ALTER TABLE `media`
MODIFY COLUMN `folder` VARCHAR(100) DEFAULT 'uploads'
COMMENT 'Storage location: uploads (local), cloudinary, or custom folder name';
