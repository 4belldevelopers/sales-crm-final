<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: all billing, subscription and payment tables.
 * carts is created WITHOUT coupon_id/coupon_code — 2026_07_15_000001 adds those.
 * All CREATE TABLE statements are guarded with hasTable() — safe on production.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('packages')) {
            DB::statement(<<<'SQL'
CREATE TABLE `packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_price` double(8,2) DEFAULT NULL COMMENT 'unuse',
  `annual_price` double(8,2) DEFAULT NULL COMMENT 'unuse',
  `no_of_job_openings` int DEFAULT NULL COMMENT 'unuse',
  `no_of_candidate_access` int DEFAULT NULL COMMENT 'unuse',
  `career_website` tinyint(1) DEFAULT NULL COMMENT 'unuse',
  `multiple_roles` tinyint(1) DEFAULT NULL COMMENT 'unuse',
  `recommended` tinyint(1) DEFAULT NULL,
  `is_trial` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `trial_duration` int NOT NULL COMMENT 'package all duration',
  `stripe_annual_plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unuse',
  `razorpay_annual_plan_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unuse',
  `razorpay_monthly_plan_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unuse',
  `stripe_monthly_plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unuse',
  `billing_cycle` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'unuse',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `sub_total` double(15,2) DEFAULT NULL,
  `amount_total_paid` double(15,2) DEFAULT NULL,
  `custome_pck` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('subscriptions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_plan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Note: coupon_id and coupon_code are intentionally omitted here;
        // migration 2026_07_15_000001 adds them via Schema::table('carts').
        if (!Schema::hasTable('carts')) {
            DB::statement(<<<'SQL'
CREATE TABLE `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `subscription_type` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'yes',
  `pck_top_id` int DEFAULT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `duration` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `credit` double(15,2) NOT NULL,
  `currency` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `price` double(15,2) NOT NULL,
  `gst` double(15,2) DEFAULT NULL,
  `gst_amount` double DEFAULT NULL,
  `total_price` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL DEFAULT '0.00',
  `user_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('cart_transaction_history')) {
            DB::statement(<<<'SQL'
CREATE TABLE `cart_transaction_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `subscription_type` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'yes',
  `pck_top_id` int DEFAULT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `duration` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `credit` double(15,2) NOT NULL,
  `currency` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `price` double(15,2) NOT NULL,
  `gst` double(15,2) DEFAULT NULL,
  `gst_amount` double DEFAULT NULL,
  `total_price` double(15,2) NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('created','authorized','captured','failed','refunded','partially_refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_orderId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `international` int DEFAULT '0' COMMENT '0-false, 1-true',
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_Id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cart_id` int DEFAULT NULL,
  `payment_gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_mode` enum('Cash','Cheque','Bank Transfer','Online','Payment Link') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `chequenumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chequedate` date DEFAULT NULL,
  `bankname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `transactionid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bankreference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_link_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `invoiceId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `invoice_date` datetime DEFAULT NULL,
  `invoice_approval` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'No',
  `approval_invoice_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `approval_invoice_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('billing_address_by_orders')) {
            DB::statement(<<<'SQL'
CREATE TABLE `billing_address_by_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_transaction_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `street` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `state_id` int DEFAULT NULL,
  `city` int DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organization` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `pan_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gstin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_country_id` int DEFAULT NULL,
  `sold_first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sold_building` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_street` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_state_id` int DEFAULT NULL,
  `sold_city` int DEFAULT NULL,
  `sold_postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sold_organization` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_gstin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_pan_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sold_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('billing_address_companies')) {
            DB::statement(<<<'SQL'
CREATE TABLE `billing_address_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `street` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `state_id` int DEFAULT NULL,
  `city` int DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organization` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `pan_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gstin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('sold_by_address')) {
            DB::statement(<<<'SQL'
CREATE TABLE `sold_by_address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `building` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `street` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `state_id` int DEFAULT NULL,
  `city` int DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organization` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gstin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `pan_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('payment_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `payment_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `paypal_client_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `stripe_status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `razorpay_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_webhook_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_status` enum('active','deactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `paypal_mode` enum('sandbox','live') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sandbox',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_features')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `features_type` enum('1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1-Operations,2-Sales',
  `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `field_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_details')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_id` int DEFAULT NULL,
  `package_feature_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `sub_total` double(15,2) DEFAULT NULL,
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Active,Inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_master')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_master` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mp_title` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mp_job_title` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mp_job_response` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mp_response_price` int NOT NULL,
  `display_order` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_master_item')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_master_item` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_default')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_default` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cvaccess_quantity` int DEFAULT NULL,
  `cvaccess_validity` int DEFAULT NULL,
  `cvaccess_status` int DEFAULT NULL,
  `users_quantity` int DEFAULT NULL,
  `users_validity` int DEFAULT NULL,
  `users_status` int DEFAULT NULL,
  `newclientrequest_quantity` int DEFAULT NULL,
  `newclientrequest_validity` int DEFAULT NULL,
  `newclientrequest_status` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `newbenchclientrequest_quantity` int DEFAULT NULL,
  `newbenchclientrequest_validity` int DEFAULT NULL,
  `newbenchclientrequest_status` int DEFAULT NULL,
  `newjobrequest_quantity` int DEFAULT NULL,
  `newjobrequest_validity` int DEFAULT NULL,
  `newjobrequest_status` int DEFAULT NULL,
  `emails_quantity` int DEFAULT NULL,
  `emails_validity` int DEFAULT NULL,
  `emails_status` int DEFAULT NULL,
  `oldcvaccess_quantity` int DEFAULT NULL,
  `oldcvaccess_validity` int DEFAULT NULL,
  `oldcvaccess_status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_active')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_active` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `package_invoice_id` int DEFAULT '0',
  `company_id` int DEFAULT NULL,
  `company_type` int DEFAULT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int DEFAULT NULL,
  `cvaccess_quantity` int DEFAULT NULL,
  `cvaccess_used` int DEFAULT NULL,
  `cvaccess_balance` int DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int DEFAULT NULL,
  `users_quantity` int DEFAULT NULL,
  `users_used` int DEFAULT NULL,
  `users_balance` int DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_enddate` date DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_status` int DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_quantity` int DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_used` int DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_balance` int DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newjobrequest_startdate` date DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_enddate` date DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_status` int DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_quantity` int DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_used` int DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_balance` int DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int DEFAULT NULL,
  `newbenchclientrequest_quantity` int DEFAULT NULL,
  `newbenchclientrequest_used` int DEFAULT NULL,
  `newbenchclientrequest_balance` int DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int DEFAULT NULL,
  `emails_quantity` int DEFAULT NULL,
  `emails_used` int DEFAULT NULL,
  `emails_balance` int DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int DEFAULT NULL,
  `oldcvaccess_quantity` int DEFAULT NULL,
  `oldcvaccess_used` int DEFAULT NULL,
  `oldcvaccess_balance` int DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int DEFAULT '0',
  `boostkeywords_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boostkeywords_quantity` int DEFAULT NULL,
  `boostkeywords_used` int DEFAULT NULL,
  `boostkeywords_balance` int DEFAULT NULL,
  `internaldatabaseaccess_status` int DEFAULT NULL,
  `internaldatabaseaccess_quantity` int DEFAULT NULL,
  `internaldatabaseaccess_used` int DEFAULT NULL,
  `internaldatabaseaccess_balance` int DEFAULT NULL,
  `interviewtracker_status` int DEFAULT NULL,
  `interviewtracker_quantity` int DEFAULT NULL,
  `interviewtracker_used` int DEFAULT NULL,
  `interviewtracker_balance` int DEFAULT NULL,
  `offertracker_status` int DEFAULT NULL,
  `offertracker_quantity` int DEFAULT NULL,
  `offertracker_used` int DEFAULT NULL,
  `offertracker_balance` int DEFAULT NULL,
  `joiningtracker_status` int DEFAULT NULL,
  `joiningtracker_quantity` int DEFAULT NULL,
  `joiningtracker_used` int DEFAULT NULL,
  `joiningtracker_balance` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_status` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_used` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_balance` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_used` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_balance` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_used` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_balance` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_used` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_balance` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_used` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_balance` int DEFAULT NULL,
  `BenchSalesIndia_status` int DEFAULT NULL,
  `BenchSalesIndia_quantity` int DEFAULT NULL,
  `BenchSalesIndia_used` int DEFAULT NULL,
  `BenchSalesIndia_balance` int DEFAULT NULL,
  `BenchSalesInternational_status` int DEFAULT NULL,
  `BenchSalesInternational_quantity` int DEFAULT NULL,
  `BenchSalesInternational_used` int DEFAULT NULL,
  `BenchSalesInternational_balance` int DEFAULT NULL,
  `BenchClientAcccessIndia_status` int DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int DEFAULT NULL,
  `BenchClientAcccessIndia_used` int DEFAULT NULL,
  `BenchClientAcccessIndia_balance` int DEFAULT NULL,
  `BenchClientAccessInternational_status` int DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int DEFAULT NULL,
  `BenchClientAccessInternational_used` int DEFAULT NULL,
  `BenchClientAccessInternational_balance` int DEFAULT NULL,
  `BoostKeywordsIndia_status` int DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int DEFAULT NULL,
  `BoostKeywordsIndia_used` int DEFAULT NULL,
  `BoostKeywordsIndia_balance` int DEFAULT NULL,
  `BoostKeywordsInternational_status` int DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int DEFAULT NULL,
  `BoostKeywordsInternational_used` int DEFAULT NULL,
  `BoostKeywordsInternational_balance` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_used` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_balance` int DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int DEFAULT '0' COMMENT '0-inactive,1-active',
  `payment_status` int DEFAULT '0' COMMENT '0-pending,1-paid',
  `package_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_order')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_order` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `company_type` int NOT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int DEFAULT NULL,
  `cvaccess_quantity` int DEFAULT NULL,
  `cvaccess_price` double(15,2) DEFAULT NULL,
  `cvaccess_totalprice` double(15,2) DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int DEFAULT NULL,
  `users_quantity` int DEFAULT NULL,
  `users_price` double(15,2) DEFAULT NULL,
  `users_totalprice` double(15,2) DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL,
  `newclientrequest_enddate` date DEFAULT NULL,
  `newclientrequest_status` int DEFAULT NULL,
  `newclientrequest_quantity` int DEFAULT NULL,
  `newclientrequest_price` double(15,2) DEFAULT NULL,
  `newclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `newjobrequest_startdate` date DEFAULT NULL,
  `newjobrequest_enddate` date DEFAULT NULL,
  `newjobrequest_status` int DEFAULT NULL,
  `newjobrequest_quantity` int DEFAULT NULL,
  `newjobrequest_price` double(15,2) DEFAULT NULL,
  `newjobrequest_totalprice` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int DEFAULT NULL,
  `newbenchclientrequest_quantity` int DEFAULT NULL,
  `newbenchclientrequest_price` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int DEFAULT NULL,
  `emails_quantity` int DEFAULT NULL,
  `emails_price` double(15,2) DEFAULT NULL,
  `emails_totalprice` double(15,2) DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int DEFAULT NULL,
  `oldcvaccess_quantity` int DEFAULT NULL,
  `oldcvaccess_price` double(15,2) DEFAULT NULL,
  `oldcvaccess_totalprice` double(15,2) DEFAULT NULL,
  `order_status` int NOT NULL DEFAULT '0',
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int DEFAULT NULL,
  `boostkeywords_price` double(15,2) DEFAULT NULL,
  `boostkeywords_totalprice` double(15,2) DEFAULT NULL,
  `boostkeywords_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boostkeywords_quantity` int DEFAULT NULL,
  `internaldatabaseaccess_status` int DEFAULT NULL,
  `internaldatabaseaccess_quantity` int DEFAULT NULL,
  `internaldatabaseaccess_price` double(15,2) DEFAULT NULL,
  `internaldatabaseaccess_totalprice` double(15,2) DEFAULT NULL,
  `interviewtracker_status` int DEFAULT NULL,
  `interviewtracker_quantity` int DEFAULT NULL,
  `interviewtracker_price` double(15,2) DEFAULT NULL,
  `interviewtracker_totalprice` double(15,2) DEFAULT NULL,
  `offertracker_status` int DEFAULT NULL,
  `offertracker_quantity` int DEFAULT NULL,
  `offertracker_price` double(15,2) DEFAULT NULL,
  `offertracker_totalprice` double(15,2) DEFAULT NULL,
  `joiningtracker_status` int DEFAULT NULL,
  `joiningtracker_quantity` int DEFAULT NULL,
  `joiningtracker_price` double(15,2) DEFAULT NULL,
  `joiningtracker_totalprice` double(15,2) NOT NULL,
  `PermissionforSHaringCVtoclienys_status` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_price` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_status` int DEFAULT NULL,
  `BenchSalesIndia_quantity` int DEFAULT NULL,
  `BenchSalesIndia_price` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_status` int DEFAULT NULL,
  `BenchSalesInternational_quantity` int DEFAULT NULL,
  `BenchSalesInternational_price` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_status` int DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int DEFAULT NULL,
  `BenchClientAcccessIndia_price` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_status` int DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int DEFAULT NULL,
  `BenchClientAccessInternational_price` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_status` int DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int DEFAULT NULL,
  `BoostKeywordsIndia_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_status` int DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int DEFAULT NULL,
  `BoostKeywordsInternational_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_totalprice` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_price` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_totalprice` double(15,2) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int DEFAULT '0' COMMENT '0-inactive,1-active',
  `payment_status` int DEFAULT '0' COMMENT '0-pending,1-paid',
  `package_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_invoice')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_invoice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_order_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `company_type` int NOT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int DEFAULT NULL,
  `cvaccess_quantity` int DEFAULT NULL,
  `cvaccess_price` double(15,2) DEFAULT NULL,
  `cvaccess_totalprice` double(15,2) DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int DEFAULT NULL,
  `users_quantity` int DEFAULT NULL,
  `users_price` double(15,2) DEFAULT NULL,
  `users_totalprice` double(15,2) DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL,
  `newclientrequest_enddate` date DEFAULT NULL,
  `newclientrequest_status` int DEFAULT NULL,
  `newclientrequest_quantity` int DEFAULT NULL,
  `newclientrequest_price` double(15,2) DEFAULT NULL,
  `newclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `newjobrequest_startdate` date DEFAULT NULL,
  `newjobrequest_enddate` date DEFAULT NULL,
  `newjobrequest_status` int DEFAULT NULL,
  `newjobrequest_quantity` int DEFAULT NULL,
  `newjobrequest_price` double(15,2) DEFAULT NULL,
  `newjobrequest_totalprice` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int DEFAULT NULL,
  `newbenchclientrequest_quantity` int DEFAULT NULL,
  `newbenchclientrequest_price` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int DEFAULT NULL,
  `emails_quantity` int DEFAULT NULL,
  `emails_price` double(15,2) DEFAULT NULL,
  `emails_totalprice` double(15,2) DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int DEFAULT NULL,
  `oldcvaccess_quantity` int DEFAULT NULL,
  `oldcvaccess_price` double(15,2) DEFAULT NULL,
  `oldcvaccess_totalprice` double(15,2) DEFAULT NULL,
  `subtotal` double(15,2) DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `payment_mode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total` double(15,2) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int DEFAULT NULL,
  `boostkeywords_price` double(15,2) DEFAULT NULL,
  `boostkeywords_totalprice` double(15,2) DEFAULT NULL,
  `boostkeywords_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boostkeywords_quantity` int DEFAULT NULL,
  `internaldatabaseaccess_status` int DEFAULT NULL,
  `internaldatabaseaccess_quantity` int DEFAULT NULL,
  `internaldatabaseaccess_price` double(15,2) DEFAULT NULL,
  `internaldatabaseaccess_totalprice` double(15,2) DEFAULT NULL,
  `interviewtracker_status` int DEFAULT NULL,
  `interviewtracker_quantity` int DEFAULT NULL,
  `interviewtracker_price` double(15,2) DEFAULT NULL,
  `interviewtracker_totalprice` double(15,2) DEFAULT NULL,
  `offertracker_status` int DEFAULT NULL,
  `offertracker_quantity` int DEFAULT NULL,
  `offertracker_price` double(15,2) DEFAULT NULL,
  `offertracker_totalprice` double(15,2) DEFAULT NULL,
  `joiningtracker_status` int DEFAULT NULL,
  `joiningtracker_quantity` int DEFAULT NULL,
  `joiningtracker_price` double(15,2) NOT NULL,
  `joiningtracker_totalprice` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_status` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_price` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int DEFAULT NULL,
  `ContractRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_status` int DEFAULT NULL,
  `BenchSalesIndia_quantity` int DEFAULT NULL,
  `BenchSalesIndia_price` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_status` int DEFAULT NULL,
  `BenchSalesInternational_quantity` int DEFAULT NULL,
  `BenchSalesInternational_price` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_status` int DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int DEFAULT NULL,
  `BenchClientAcccessIndia_price` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_status` int DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int DEFAULT NULL,
  `BenchClientAccessInternational_price` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_status` int DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int DEFAULT NULL,
  `BoostKeywordsIndia_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_status` int DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int DEFAULT NULL,
  `BoostKeywordsInternational_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_totalprice` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int DEFAULT NULL,
  `Limitperdayforleadsconsumptions_price` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_totalprice` double(15,2) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int DEFAULT '0' COMMENT '0-inactive,1-active',
  `payment_status` int DEFAULT '0' COMMENT '0-pending,1-paid',
  `package_id` int DEFAULT NULL,
  `invoicePrimaryId` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_invoice_comment')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_invoice_comment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_invoice_tax')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_invoice_tax` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `tax_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percentage` int DEFAULT NULL,
  `tax_amount` float(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_order_items')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_id` int NOT NULL,
  `package_order_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `feature_id` int NOT NULL,
  `qty` int DEFAULT NULL,
  `price` double DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('package_transactions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `package_invoice_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `package_master_item` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `event_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_inserted_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('invoices')) {
            DB::statement(<<<'SQL'
CREATE TABLE `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('stripe_invoices')) {
            DB::statement(<<<'SQL'
CREATE TABLE `stripe_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `invoice_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` int unsigned NOT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) unsigned NOT NULL,
  `pay_date` date NOT NULL,
  `next_pay_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stripe_invoices_company_id_foreign` (`company_id`),
  KEY `stripe_invoices_package_id_foreign` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('paypal_invoices')) {
            DB::statement(<<<'SQL'
CREATE TABLE `paypal_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `currency_id` int unsigned DEFAULT NULL,
  `package_id` int unsigned DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_frequency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_interval` int DEFAULT NULL,
  `paid_on` datetime DEFAULT NULL,
  `next_pay_date` datetime DEFAULT NULL,
  `recurring` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `status` enum('paid','unpaid','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `plan_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_on` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paypal_invoices_company_id_foreign` (`company_id`),
  KEY `paypal_invoices_currency_id_foreign` (`currency_id`),
  KEY `paypal_invoices_package_id_foreign` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('razorpay_invoices')) {
            DB::statement(<<<'SQL'
CREATE TABLE `razorpay_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `currency_id` int DEFAULT NULL,
  `invoice_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscription_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` int unsigned NOT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) unsigned NOT NULL,
  `pay_date` date NOT NULL,
  `next_pay_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `razorpay_invoices_company_id_foreign` (`company_id`),
  KEY `razorpay_invoices_package_id_foreign` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('razorpay_subscriptions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `razorpay_subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `subscription_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_plan` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('link_payments')) {
            DB::statement(<<<'SQL'
CREATE TABLE `link_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `coins` int DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `update_coins` double DEFAULT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `coin_order_id` int DEFAULT NULL,
  `subscription_type` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cart_id` int DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_orderId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_link_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `payment_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('package')) {
            DB::statement(<<<'SQL'
CREATE TABLE `package` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `package_master_id` int NOT NULL,
  `package_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_jobs` int NOT NULL,
  `no_of_response` int NOT NULL,
  `package_price` double(15,2) NOT NULL,
  `package_discount` float(7,2) NOT NULL,
  `discounted_price` float(7,2) NOT NULL,
  `valid_upto` int NOT NULL COMMENT 'In Days',
  `display_order` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '0',
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
            'package', 'link_payments', 'razorpay_subscriptions', 'razorpay_invoices',
            'paypal_invoices', 'stripe_invoices', 'invoices', 'package_transactions',
            'package_order_items', 'package_invoice_tax', 'package_invoice_comment',
            'package_invoice', 'package_order', 'package_active', 'package_default',
            'package_master_item', 'package_master', 'package_details', 'package_features',
            'payment_settings', 'sold_by_address', 'billing_address_companies',
            'billing_address_by_orders', 'cart_transaction_history', 'carts',
            'subscriptions', 'packages',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
