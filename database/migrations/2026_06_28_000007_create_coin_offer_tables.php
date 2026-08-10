<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: coin economy, offer/incentive, and market job tables.
 * Note: coin_transection_logs and insurence_offer_points preserve baked-in typos
 * that map to real production tables/columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('coin_events')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_coins` double(15,2) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active, 2-inactive',
  `type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operational',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('coin_order_events')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_order_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coin_order_id` int NOT NULL,
  `company_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `event_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_coins` double(15,2) DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_coin_order_events_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('coin_orders')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `order_type` enum('buy','earn','spend','expire','adjust','refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(15,2) NOT NULL DEFAULT '0.00',
  `coins` double(15,2) NOT NULL DEFAULT '0.00',
  `coin_balance` double(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_coin_orders_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('coin_package_points')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_package_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_type_id` int NOT NULL,
  `points` int NOT NULL DEFAULT '0',
  `bonus_points` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('coin_package_types')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_package_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('coin_packages')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_type_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `coins` int NOT NULL DEFAULT '0',
  `bonus_coins` int NOT NULL DEFAULT '0',
  `price` double(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active, 0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Preserved typo: coin_transection_logs (not coin_transaction_logs)
        if (!Schema::hasTable('coin_transection_logs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `coin_transection_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `coin_id` int NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tran_type` enum('cr','dr') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `coin_amount` double(15,2) NOT NULL,
  `remain_coin` double(15,2) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_coin_transection_logs_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('basic_offer_points')) {
            DB::statement(<<<'SQL'
CREATE TABLE `basic_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `source_from_company_id` int NOT NULL,
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `points` double(15,2) NOT NULL DEFAULT '0.00',
  `type` int NOT NULL DEFAULT '1' COMMENT '1-pending, 2-released',
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('bonus_offer_points')) {
            DB::statement(<<<'SQL'
CREATE TABLE `bonus_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `source_from_company_id` int NOT NULL,
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `points` double(15,2) NOT NULL DEFAULT '0.00',
  `type` int NOT NULL DEFAULT '1' COMMENT '1-pending, 2-released',
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Preserved typo: insurence_offer_points (not insurance_offer_points)
        if (!Schema::hasTable('insurence_offer_points')) {
            DB::statement(<<<'SQL'
CREATE TABLE `insurence_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `source_from_company_id` int NOT NULL,
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `points` double(15,2) NOT NULL DEFAULT '0.00',
  `type` int NOT NULL DEFAULT '1' COMMENT '1-pending, 2-released',
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('offer_create_main_tds')) {
            DB::statement(<<<'SQL'
CREATE TABLE `offer_create_main_tds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `source_from_company_id` int NOT NULL,
  `tds_points` double(15,2) NOT NULL DEFAULT '0.00',
  `type` int NOT NULL DEFAULT '1' COMMENT '1-pending, 2-released',
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('offer_job_applications')) {
            DB::statement(<<<'SQL'
CREATE TABLE `offer_job_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offer_id` int NOT NULL,
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `company_id` int NOT NULL,
  `offer_type` int NOT NULL DEFAULT '0',
  `offer_amount` int DEFAULT NULL,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-pending, 1-accepted, 2-rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('offer_point_companies')) {
            DB::statement(<<<'SQL'
CREATE TABLE `offer_point_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `points` double(15,2) NOT NULL DEFAULT '0.00',
  `point_type` int NOT NULL DEFAULT '0' COMMENT '0: debit, 1: credit',
  `type` int NOT NULL DEFAULT '1' COMMENT '1-pending, 2-released',
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('offer_points')) {
            DB::statement(<<<'SQL'
CREATE TABLE `offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `success_per` double(15,2) NOT NULL DEFAULT '10.00',
  `tds` double(15,2) NOT NULL DEFAULT '10.00',
  `basic` double(15,2) NOT NULL DEFAULT '10.00',
  `bonus` double(15,2) NOT NULL DEFAULT '10.00',
  `insurance` double(15,2) NOT NULL DEFAULT '10.00',
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active, 0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('offer_terms_conditions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `offer_terms_conditions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('market_job_added_count')) {
            DB::statement(<<<'SQL'
CREATE TABLE `market_job_added_count` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `job_id` int NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('top_up_coin_packages')) {
            DB::statement(<<<'SQL'
CREATE TABLE `top_up_coin_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `coins` int NOT NULL DEFAULT '0',
  `bonus_coins` int NOT NULL DEFAULT '0',
  `price` double(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active, 0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('top_up_logs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `top_up_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `package_id` int DEFAULT NULL,
  `coins` double(15,2) NOT NULL DEFAULT '0.00',
  `amount` double(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_top_up_logs_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    public function down(): void
    {
        $tables = [
            'top_up_logs', 'top_up_coin_packages', 'market_job_added_count',
            'offer_terms_conditions', 'offer_points', 'offer_point_companies',
            'offer_job_applications', 'offer_create_main_tds', 'insurence_offer_points',
            'bonus_offer_points', 'basic_offer_points', 'coin_transection_logs',
            'coin_packages', 'coin_package_types', 'coin_package_points',
            'coin_orders', 'coin_order_events', 'coin_events',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
