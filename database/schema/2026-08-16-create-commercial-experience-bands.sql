-- Intent: create the global backend source for commercial experience-range labels and their existing commericial_details exp indexes.
-- Apply once to MySQL 8 before deploying the matching application code.
CREATE TABLE IF NOT EXISTS `commercial_experience_bands` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(64) NOT NULL,
    `label` VARCHAR(100) NOT NULL,
    `min_experience` SMALLINT UNSIGNED NOT NULL,
    `max_experience` SMALLINT UNSIGNED NOT NULL,
    `sort_order` SMALLINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `commercial_experience_bands_code_unique` (`code`),
    UNIQUE KEY `commercial_experience_bands_sort_order_unique` (`sort_order`),
    KEY `commercial_experience_bands_active_sort_index` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed the ranges that the current commercial details indexes already represent.
INSERT IGNORE INTO `commercial_experience_bands`
    (`code`, `label`, `min_experience`, `max_experience`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('fresher', '0 - 2 Years (Fresher)', 0, 2, 10, 1, NOW(), NOW()),
    ('junior', '3 - 5 Years', 3, 5, 20, 1, NOW(), NOW()),
    ('mid_level', '6 - 9 Years', 6, 9, 30, 1, NOW(), NOW()),
    ('senior', '10+ Years', 10, 10, 40, 1, NOW(), NOW());

-- Rollback (data-destructive; run only when explicitly approved):
-- DROP TABLE `commercial_experience_bands`;
