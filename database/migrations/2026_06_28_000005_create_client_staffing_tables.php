<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: client management, staffing company, and consultancy relation tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clients')) {
            DB::statement(<<<'SQL'
CREATE TABLE `clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `client_type` int NOT NULL DEFAULT '0' COMMENT '0=All, 1=internal',
  `industries` int DEFAULT NULL,
  `website` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_size_from` int DEFAULT NULL,
  `company_size_to` int DEFAULT NULL,
  `founded` int DEFAULT NULL,
  `source_comapny_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_contact_info')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_contact_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_contact_info_client_id_foreign` (`client_id`),
  KEY `client_contact_info_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_contact_info_jobs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_contact_info_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_contact_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_feedbacks')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_feedbacks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_file')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_id` int unsigned DEFAULT NULL,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_file_client_id_foreign` (`client_id`),
  KEY `client_file_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_location')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_location` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_id` int unsigned DEFAULT NULL,
  `country_id` int unsigned DEFAULT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_location_client_id_foreign` (`client_id`),
  KEY `client_location_company_id_foreign` (`company_id`),
  KEY `client_location_country_id_foreign` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_location_jobs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_location_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_location_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('client_city_jobs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `client_city_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_id` int unsigned DEFAULT NULL,
  `city_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('consultancy')) {
            DB::statement(<<<'SQL'
CREATE TABLE `consultancy` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consultancy_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultancy_company_id_foreign` (`company_id`),
  KEY `consultancy_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('consultancy_file')) {
            DB::statement(<<<'SQL'
CREATE TABLE `consultancy_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `consultancy_id` int unsigned DEFAULT NULL,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultancy_file_consultancy_id_foreign` (`consultancy_id`),
  KEY `consultancy_file_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('staffing_company_invitation')) {
            DB::statement(<<<'SQL'
CREATE TABLE `staffing_company_invitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `staffing_company_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('staffing_company_relation')) {
            DB::statement(<<<'SQL'
CREATE TABLE `staffing_company_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `staffing_company_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('invitation_consultancy')) {
            DB::statement(<<<'SQL'
CREATE TABLE `invitation_consultancy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `consultancy_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('invitation_request')) {
            DB::statement(<<<'SQL'
CREATE TABLE `invitation_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `invitation_company_id` int NOT NULL,
  `from_company_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-pending, 1-accepted, 2-rejected',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    public function down(): void
    {
        $tables = [
            'invitation_request', 'invitation_consultancy', 'staffing_company_relation',
            'staffing_company_invitation', 'consultancy_file', 'consultancy',
            'client_city_jobs', 'client_location_jobs', 'client_location',
            'client_file', 'client_feedbacks', 'client_contact_info_jobs',
            'client_contact_info', 'clients',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
