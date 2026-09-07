<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: bench candidate management tables.
 * Note: bench_candidate_skill_experience.total_months is a GENERATED ALWAYS AS
 * column — requires raw SQL since Laravel Blueprint does not support computed columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bench_applications')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `submitted_by` int NOT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `full_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `np_id` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serving_notice_period_mode` int NOT NULL DEFAULT '0',
  `last_working_date` date DEFAULT NULL,
  `gender` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `secondrary_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_phone_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rating` enum('1','2','3','4','5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skype_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_letter` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status_id` int unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `column_priority` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `total_exp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rel_exp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `passport` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `ctc` int DEFAULT NULL,
  `current_ctc_cur` int DEFAULT '0',
  `current_ctc_mode` int DEFAULT '0',
  `ectc` int DEFAULT NULL,
  `current_ectc_cur` int DEFAULT '0',
  `current_ectc_mode` int DEFAULT '0',
  `current_location` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_designation` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_company` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` int DEFAULT NULL,
  `bench_status` int NOT NULL DEFAULT '0' COMMENT '0-private, 1-public',
  `bench_end_date` date DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `outsourcing_type` int DEFAULT NULL,
  `pay_rate_currency` int DEFAULT NULL,
  `pay_rate_amount` int DEFAULT NULL,
  `pay_rate_mode` int DEFAULT NULL,
  `bill_rate_currency` int DEFAULT NULL,
  `bill_rate_amount` int DEFAULT NULL,
  `bill_rate_mode` int DEFAULT NULL,
  `ctc_hourly` double(15,2) DEFAULT NULL,
  `ctc_monthly` double(15,2) DEFAULT NULL,
  `ctc_yearly` double(15,2) DEFAULT NULL,
  `ectc_hourly` double(15,2) DEFAULT NULL,
  `ectc_monthly` double(15,2) DEFAULT NULL,
  `ectc_yearly` double(15,2) DEFAULT NULL,
  `superadminstatus` int NOT NULL DEFAULT '0' COMMENT '0-pending,1-approved,2-reject',
  `ads_switch` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `linkedin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `github` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `behance` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `otherLink` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `careersummary` text COLLATE utf8mb4_unicode_ci,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wm` enum('remote','hybrid','office') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employmentType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oh` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visastatu` text COLLATE utf8mb4_unicode_ci,
  `referencename` text COLLATE utf8mb4_unicode_ci,
  `maxmum_exp` double(15,1) DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bench_applications_company_id_foreign` (`company_id`),
  KEY `idx_bench_applications_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bench_additional_detail')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_additional_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `bench_id` int NOT NULL,
  `detail_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bench_assign')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_assign` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `bench_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bench_assign_user_id_foreign` (`user_id`),
  KEY `bench_assign_bench_id_foreign` (`bench_id`),
  KEY `bench_assign_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // total_months is GENERATED ALWAYS AS — must use raw SQL
        if (!Schema::hasTable('bench_candidate_skill_experience')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_candidate_skill_experience` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bench_id` int NOT NULL,
  `company_id` int NOT NULL,
  `skill_id` int NOT NULL,
  `skill_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years` int NOT NULL DEFAULT '0',
  `months` int NOT NULL DEFAULT '0',
  `total_months` int GENERATED ALWAYS AS ((`years` * 12) + `months`) STORED,
  `last_used` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bench_skill_exp_bench_id` (`bench_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bench_count_view_download')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_count_view_download` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bench_id` int NOT NULL,
  `company_id` int NOT NULL,
  `viewer_company_id` int NOT NULL,
  `type` int NOT NULL DEFAULT '0' COMMENT '0-view, 1-download',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bench_master_status')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_master_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_style` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `divname` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bench_place')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bench_place` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bench_id` int NOT NULL,
  `company_id` int NOT NULL,
  `job_id` int DEFAULT NULL,
  `place_company_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-pending, 1-placed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('benchboostkeywordcandidates')) {
            DB::statement(<<<'SQL'
CREATE TABLE `benchboostkeywordcandidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bench_id` int NOT NULL,
  `company_id` int NOT NULL,
  `boostkeyword_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('boostkeywords')) {
            DB::statement(<<<'SQL'
CREATE TABLE `boostkeywords` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `keyword` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active, 0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('jd_details')) {
            DB::statement(<<<'SQL'
CREATE TABLE `jd_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `job_id` int NOT NULL,
  `bench_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-pending, 1-interested, 2-not interested',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    public function down(): void
    {
        $tables = [
            'jd_details', 'boostkeywords', 'benchboostkeywordcandidates',
            'bench_place', 'bench_master_status', 'bench_count_view_download',
            'bench_candidate_skill_experience', 'bench_assign',
            'bench_additional_detail', 'bench_applications',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
