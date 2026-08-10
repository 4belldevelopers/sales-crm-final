<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: companies, company_coins, company_packages, company_tags,
 * company_user_points_logs, users_profile.
 * All CREATE TABLE statements are guarded with hasTable() — safe on production.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('companies')) {
            DB::statement(<<<'SQL'
CREATE TABLE `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sub_domain` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` int unsigned DEFAULT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `calling_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Kolkata',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `show_in_frontend` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `career_page_link` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `package_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `licence_expire_on` date DEFAULT NULL,
  `job_opening_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_opening_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `linkedin` enum('enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disable',
  `stripe_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_brand` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_last_four` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `featured_start_date` date DEFAULT NULL,
  `featured_end_date` date DEFAULT NULL,
  `login_background` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_account_at` datetime DEFAULT NULL,
  `app_duplicate` int NOT NULL DEFAULT '0' COMMENT '0: None, 1: For current job, 2: For overall application',
  `app_duplicate_days` int DEFAULT NULL,
  `zoom_status` int DEFAULT NULL,
  `zoom_api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_api_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zoom_access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_status` int DEFAULT NULL,
  `google_client_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_client_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_access_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_expires_in` int NOT NULL,
  `google_refresh_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_token_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_verify` int NOT NULL DEFAULT '0',
  `microsoft_status` int DEFAULT NULL,
  `microsoft_api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `microsoft_api_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `company_size_from` int DEFAULT NULL,
  `company_size` int DEFAULT NULL,
  `company_type` int DEFAULT '2' COMMENT '1=>Staffing Company, 2=>Service Company, 3=>Product Company',
  `founded` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `industries` int DEFAULT NULL,
  `acount_type` int NOT NULL COMMENT '1: Public, 2: Private',
  `language` int DEFAULT NULL,
  `currency` int DEFAULT NULL,
  `time_format` int DEFAULT '1',
  `date_format` int DEFAULT NULL,
  `industries_type` int DEFAULT '0',
  `success_per` int NOT NULL DEFAULT '10',
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `offer_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `agency_offer_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_login` datetime DEFAULT CURRENT_TIMESTAMP,
  `leadCount` int NOT NULL DEFAULT '0',
  `bench_show_mode` int NOT NULL DEFAULT '0',
  `accountVerify` int NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-verified',
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_career_page_link_unique` (`career_page_link`),
  KEY `companies_package_id_foreign` (`package_id`),
  KEY `idx_companies_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('company_coins')) {
            DB::statement(<<<'SQL'
CREATE TABLE `company_coins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coin_order_id` int NOT NULL,
  `company_id` int NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` double(15,2) NOT NULL,
  `discount` double(15,2) DEFAULT '0.00',
  `coins` double(15,2) NOT NULL DEFAULT '0.00',
  `actual_coins` double(15,2) DEFAULT NULL,
  `balance_coin` double(15,2) DEFAULT '0.00',
  `status` enum('valid','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `free_pack_status` enum('active','block') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `free_update_date` date DEFAULT NULL,
  `auto_team_coins` date DEFAULT NULL,
  `auto_job_coins` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_coins_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('company_packages')) {
            DB::statement(<<<'SQL'
CREATE TABLE `company_packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `package_id` int unsigned NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `stripe_id` int DEFAULT NULL,
  `paypal_id` int DEFAULT NULL,
  `paypal_environment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `package_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_packages_company_id_foreign` (`company_id`),
  KEY `company_packages_package_id_foreign` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('company_tags')) {
            DB::statement(<<<'SQL'
CREATE TABLE `company_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `tag_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_skills_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('company_user_points_logs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `company_user_points_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `candidate_id` bigint unsigned DEFAULT NULL,
  `profileUpload` decimal(10,2) DEFAULT '0.00',
  `interviewScheduled` decimal(10,2) DEFAULT '0.00',
  `interviewNoShow` decimal(10,2) DEFAULT '0.00',
  `offerReleased` decimal(10,2) DEFAULT '0.00',
  `candidateJoined` decimal(10,2) DEFAULT '0.00',
  `candidateJoinedContract` decimal(10,2) DEFAULT '0.00',
  `jobsPosted` decimal(10,2) DEFAULT '0.00',
  `feedbackGivenOnCVs` decimal(10,2) DEFAULT '0.00',
  `postJobToVendorMarket` decimal(10,2) DEFAULT '0.00',
  `outsourceJobsToVendors` decimal(10,2) DEFAULT '0.00',
  `listBenchCandidates` decimal(10,2) DEFAULT '0.00',
  `respondToBenchRequests` decimal(10,2) DEFAULT '0.00',
  `interestedCandidates` decimal(10,2) DEFAULT '0.00',
  `notInterestedCandidates` decimal(10,2) DEFAULT '0.00',
  `teamCaptainInterviewScheduled` decimal(10,2) DEFAULT '0.00',
  `teamCaptainInterviewNoShow` decimal(10,2) DEFAULT '0.00',
  `teamCaptainOfferReleased` decimal(10,2) DEFAULT '0.00',
  `teamCaptainCandidateJoined` decimal(10,2) DEFAULT '0.00',
  `teamCaptainCandidateJoinedContract` decimal(10,2) DEFAULT '0.00',
  `sourcing` decimal(10,2) DEFAULT '0.00',
  `screening` decimal(10,2) DEFAULT '0.00',
  `evaluation` decimal(10,2) DEFAULT '0.00',
  `negotiation` decimal(10,2) DEFAULT '0.00',
  `backout` decimal(10,2) DEFAULT '0.00',
  `backoutContract` decimal(10,2) DEFAULT '0.00',
  `backoutTeam` decimal(10,2) DEFAULT '0.00',
  `backoutTeamContract` decimal(10,2) DEFAULT '0.00',
  `total_points` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL);
        }

        if (!Schema::hasTable('users_profile')) {
            DB::statement(<<<'SQL'
CREATE TABLE `users_profile` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int NOT NULL,
  `designation` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` int DEFAULT NULL,
  `state` int DEFAULT NULL,
  `city` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `skills` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users_profile');
        Schema::dropIfExists('company_user_points_logs');
        Schema::dropIfExists('company_tags');
        Schema::dropIfExists('company_packages');
        Schema::dropIfExists('company_coins');
        Schema::dropIfExists('companies');
    }
};
