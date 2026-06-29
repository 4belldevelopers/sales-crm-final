-- Authoritative schema for `dbmaster` (MySQL 8.0.45)
-- Generated 2026-06-28 00:38 via SHOW CREATE TABLE (structure only, read-only).
-- Source of truth for docs/DB_SCHEMA.md. Do NOT execute blindly against production.

-- ============================================================
-- TABLE: add_job_wise_condidates
-- ============================================================
CREATE TABLE `add_job_wise_condidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int DEFAULT NULL,
  `job_application_id` int DEFAULT NULL,
  `rec_candidate_assign_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `master_status_id` int DEFAULT NULL,
  `child_status_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `thirdparty_company_id` int NOT NULL DEFAULT '0',
  `company_id` int DEFAULT NULL,
  `submitted_by` int DEFAULT NULL,
  `placed_end_date` date DEFAULT NULL,
  `existing` int DEFAULT '0',
  `skill_match` double(15,1) DEFAULT '0.0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63804 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: applicant_notes
-- ============================================================
CREATE TABLE `applicant_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `job_application_id` int unsigned NOT NULL,
  `note_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicant_notes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6454 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: application_additional_detail
-- ============================================================
CREATE TABLE `application_additional_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `application_id` int NOT NULL,
  `question_id` int DEFAULT NULL,
  `job_detail_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `application_detail_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=213726 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: application_event
-- ============================================================
CREATE TABLE `application_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: application_settings
-- ============================================================
CREATE TABLE `application_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `legal_term` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mail_setting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `application_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=875 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: application_status
-- ============================================================
CREATE TABLE `application_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` smallint DEFAULT NULL,
  `color` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_status_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4766 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: basic_offer_points
-- ============================================================
CREATE TABLE `basic_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poins` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Percentage` int NOT NULL,
  `condition_s` int NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `view_type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: bench_additional_detail
-- ============================================================
CREATE TABLE `bench_additional_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bench_id` int DEFAULT NULL,
  `custom_keys` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_keys_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: bench_applications
-- ============================================================
CREATE TABLE `bench_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `submitted_by` int DEFAULT NULL,
  `full_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_designation` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `outsourcing_type` int NOT NULL DEFAULT '1',
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondrary_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` int DEFAULT NULL,
  `total_exp` tinyint DEFAULT NULL,
  `rel_exp` tinyint DEFAULT NULL,
  `serving_notice_period_mode` int NOT NULL DEFAULT '0',
  `np_id` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_rate_currency` int DEFAULT NULL,
  `pay_rate_amount` int DEFAULT NULL,
  `pay_rate_mode` int DEFAULT NULL,
  `bill_rate_currency` int DEFAULT NULL,
  `bill_rate_amount` int DEFAULT NULL,
  `bill_rate_mode` int DEFAULT NULL,
  `comission_type` int DEFAULT NULL COMMENT '1: For Free, 2: For Paid',
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  `skills` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` int unsigned DEFAULT '1',
  `offer_amount` int DEFAULT NULL,
  `joined_date` datetime DEFAULT NULL,
  `employee_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` enum('1','2','3','4','5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `current_company` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `process_id` int DEFAULT NULL,
  `master_status_id` int DEFAULT '1',
  `child_status_id` int DEFAULT NULL,
  `proposed_ctc` int DEFAULT NULL,
  `any_offer` int NOT NULL DEFAULT '0',
  `offer_approved` int NOT NULL DEFAULT '0',
  `offer_release` int NOT NULL DEFAULT '0',
  `offer_acceptance` int NOT NULL DEFAULT '0',
  `joined` int NOT NULL DEFAULT '0',
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_applications_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: bench_assign
-- ============================================================
CREATE TABLE `bench_assign` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `bench_applications_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: bench_candidate_skill_experience
-- ============================================================
CREATE TABLE `bench_candidate_skill_experience` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `skill_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'java, php, html',
  `year` tinyint unsigned DEFAULT NULL,
  `month` tinyint unsigned DEFAULT NULL,
  `total_months` smallint unsigned GENERATED ALWAYS AS (((`year` * 12) + `month`)) STORED,
  `created_at` timestamp NULL DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_candidate_skill` (`candidate_id`,`skill_key`),
  KEY `idx_candidate` (`candidate_id`),
  KEY `idx_skill_key` (`skill_key`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: bench_count_view_download
-- ============================================================
CREATE TABLE `bench_count_view_download` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `type` int NOT NULL COMMENT '1-view, 2- download',
  `status` int NOT NULL DEFAULT '0' COMMENT '0-new, 1- old',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: bench_master_status
-- ============================================================
CREATE TABLE `bench_master_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_style` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: bench_place
-- ============================================================
CREATE TABLE `bench_place` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_comapny_id` int NOT NULL,
  `bench_id` int NOT NULL,
  `client_company_id` int DEFAULT NULL,
  `client_job_id` int NOT NULL,
  `client_application_id` int NOT NULL,
  `full_name` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `current_designation` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `total_exp` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rel_exp` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pay_rate_currency` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pay_rate_amount` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pay_rate_mode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bill_rate_currency` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bill_rate_amount` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bill_rate_mode` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_name` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `qualification` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `skills` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `country` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `state` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `city` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `assign_to` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `placed_end_date` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `offer_amount` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `joined_date` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `application_stage` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `application_child_stage` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bench_status` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: benchboostkeywordcandidates
-- ============================================================
CREATE TABLE `benchboostkeywordcandidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `skill_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: billing_address_by_orders
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: billing_address_companies
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: bonus_offer_points
-- ============================================================
CREATE TABLE `bonus_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poins` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Percentage` int NOT NULL,
  `condition_s` int NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `view_type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: boostkeywords
-- ============================================================
CREATE TABLE `boostkeywords` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `skill_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `campaign_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `campaign_type` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `clickCount` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: cache
-- ============================================================
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cache_locks
-- ============================================================
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: candidate_job_skill_exp
-- ============================================================
CREATE TABLE `candidate_job_skill_exp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_skill_id` int NOT NULL,
  `skill_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `job_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=678869 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- TABLE: candidate_skills_value
-- ============================================================
CREATE TABLE `candidate_skills_value` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `skill_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `skill_value` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: candidate_status
-- ============================================================
CREATE TABLE `candidate_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `parent` int NOT NULL DEFAULT '0',
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delete_status` int NOT NULL DEFAULT '1' COMMENT '0: For not deleted, 1:For deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11585 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: candidates_notify
-- ============================================================
CREATE TABLE `candidates_notify` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `candidate_id` int DEFAULT NULL,
  `candidate_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_total_exp` double(15,1) DEFAULT NULL,
  `candidate_rel_exp` double(15,1) DEFAULT NULL,
  `candidate_currency` int DEFAULT NULL,
  `candidate_sal_mode` int DEFAULT NULL,
  `candidate_ctc` double(15,2) DEFAULT NULL,
  `candidate_ectc` double(15,2) DEFAULT NULL,
  `candidate_skills` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `candidate_location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `candidate_edication` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `candidate_cv` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `candidate_type` enum('bench_condidate','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '1-pending, 0-done',
  `send_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- VIEW: candidates_view
-- ============================================================
CREATE ALGORITHM=UNDEFINED DEFINER=`dbmasteruser`@`%` SQL SECURITY DEFINER VIEW `candidates_view` AS select `aj`.`id` AS `id`,`aj`.`id` AS `wiseId`,`ja`.`id` AS `candidate_id`,`ja`.`full_name` AS `full_name`,`ja`.`np_id` AS `np_id`,`ja`.`np_id` AS `immediate_join`,`ja`.`serving_notice_period_mode` AS `serving_notice_period_mode`,`ja`.`email` AS `email`,`ja`.`phone` AS `phone`,`ja`.`skills` AS `skills`,`ja`.`total_exp` AS `total_exp`,`ja`.`rel_exp` AS `rel_exp`,`ja`.`ctc` AS `ctc`,`ja`.`ectc` AS `ectc`,`ja`.`condidate_type` AS `condidate_type`,`ja`.`ctc_hourly` AS `ctc_hourly`,`ja`.`ctc_monthly` AS `ctc_monthly`,`ja`.`ctc_yearly` AS `ctc_yearly`,`ja`.`ectc_hourly` AS `ectc_hourly`,`ja`.`ectc_monthly` AS `ectc_monthly`,`ja`.`duplicate` AS `duplicate`,`ja`.`ectc_yearly` AS `ectc_yearly`,`ja`.`status` AS `status`,`ja`.`current_ctc_cur` AS `current_ctc_cur`,`ja`.`current_ctc_mode` AS `current_ctc_mode`,`ja`.`is_viewed` AS `is_viewed`,`ja`.`ads_switch` AS `ads_switch`,`ja`.`outsourcing_type` AS `outsourcing_type`,`ja`.`pay_rate_currency` AS `pay_rate_currency`,`ja`.`pay_rate_amount` AS `pay_rate_amount`,`ja`.`pay_rate_mode` AS `pay_rate_mode`,`ja`.`bill_rate_currency` AS `bill_rate_currency`,`ja`.`bill_rate_amount` AS `bill_rate_amount`,`ja`.`bill_rate_mode` AS `bill_rate_mode`,`ja`.`comission_type` AS `comission_type`,`ja`.`current_company` AS `current_company`,`ja`.`current_designation` AS `current_designation`,`ja`.`qualification` AS `qualification`,`q`.`name` AS `qualification_name`,`ja`.`city_id` AS `city_id`,`city`.`city` AS `city_name`,`state`.`state` AS `state_name`,`country`.`country_name` AS `country_name`,`jobs`.`id` AS `job_id`,`jobs`.`title` AS `title`,`jobs`.`status` AS `job_status`,`aj`.`master_status_id` AS `job_master_status_id`,`aj`.`child_status_id` AS `childStatus`,`jobs`.`employment_type` AS `employment_type`,`clients`.`name` AS `client_name`,`companies`.`company_name` AS `client_company_name`,`rr`.`final_status` AS `final_status`,`rr`.`id` AS `recommendation_id`,`rr`.`from_company_id` AS `from_company_id`,`rr`.`to_company_id` AS `to_company_id`,`ja`.`master_status_id` AS `master_status_id`,`ja`.`child_status_id` AS `child_status_id`,`aj`.`status_id` AS `status_id`,`aj`.`rec_candidate_assign_id` AS `rec_candidate_assign_id`,`aj`.`submitted_by` AS `submitted_by`,`aj`.`company_id` AS `company_id`,`ja`.`thirdparty_company_id` AS `thirdparty_company_id`,`aj`.`thirdparty_company_id` AS `ClientComID`,`aj`.`job_id` AS `jobId`,`aj`.`placed_end_date` AS `placed_end_date`,`master_status`.`name` AS `name`,`jobs`.`company_id` AS `client_comapny_id`,`master_status`.`css_style` AS `css_style`,`aj`.`existing` AS `existing`,`aj`.`skill_match` AS `skill_match`,`u`.`name` AS `posted_by`,`u`.`image` AS `posted_by_image`,`aj`.`created_at` AS `created_at`,`aj`.`updated_at` AS `updated_at`,`comp`.`company_name` AS `company_name` from ((((((((((((`add_job_wise_condidates` `aj` join `job_applications` `ja` on((`ja`.`id` = `aj`.`job_application_id`))) left join `qualification_master` `q` on((`q`.`id` = `ja`.`qualification`))) left join `city_list` `city` on((`city`.`city_id` = `ja`.`city_id`))) left join `state_list` `state` on((`state`.`id` = `ja`.`state`))) left join `countries` `country` on((`country`.`id` = `ja`.`country`))) left join `jobs` on((`jobs`.`id` = `aj`.`job_id`))) left join `master_status` on((`master_status`.`id` = `aj`.`master_status_id`))) left join `recommendation_relation` `rr` on((`rr`.`job_id` = `jobs`.`id`))) left join `clients` on((`clients`.`id` = `jobs`.`client_id`))) left join `companies` on((`companies`.`id` = `jobs`.`company_id`))) left join `users` `u` on((`u`.`id` = `aj`.`submitted_by`))) left join `companies` `comp` on((`comp`.`id` = `aj`.`thirdparty_company_id`)));

-- ============================================================
-- TABLE: cart_transaction_history
-- ============================================================
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
  `status` enum('created','authorized','captured','failed','refunded','partially_refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'created -Payment has been authorized but not yet captured, authorized -Payment has been successfully captured, captured-Payment has been successfully captured, failed- Payment has failed, refunded-Payment has been refunded, partially_refunde- Payment has been partially refunded',
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: carts
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: cities
-- ============================================================
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` int NOT NULL,
  `state_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int NOT NULL,
  `country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) DEFAULT '1',
  `wikidataid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: city_list
-- ============================================================
CREATE TABLE `city_list` (
  `city_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` int NOT NULL,
  `latitude` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'city.jpg',
  `top_ten_display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `flag` int NOT NULL DEFAULT '1',
  `wikidataid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`city_id`),
  KEY `idx_state_id` (`state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=153662 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_city_jobs
-- ============================================================
CREATE TABLE `client_city_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `city_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2726 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_contact_info
-- ============================================================
CREATE TABLE `client_contact_info` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `contact_person_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calling_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_number` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_designation` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1811 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_contact_info_jobs
-- ============================================================
CREATE TABLE `client_contact_info_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_contact_info_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_feedbacks
-- ============================================================
CREATE TABLE `client_feedbacks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_feedbacks_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_file
-- ============================================================
CREATE TABLE `client_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_logical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_physical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_location
-- ============================================================
CREATE TABLE `client_location` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `client_id` int NOT NULL,
  `city` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `address` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int NOT NULL,
  `state` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2532 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: client_location_jobs
-- ============================================================
CREATE TABLE `client_location_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_location_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2060 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: clients
-- ============================================================
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `self_company` int NOT NULL DEFAULT '0',
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_physical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_logical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry` int DEFAULT '0',
  `company_size_from` int DEFAULT NULL,
  `company_sizes` int DEFAULT NULL,
  `company_type` int DEFAULT '0',
  `founded` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payout` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: coin_events
-- ============================================================
CREATE TABLE `coin_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'sales',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `default_rate` double(15,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `deduction_type` enum('daily','onetime') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `minimum_required` double(15,2) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_order_events
-- ============================================================
CREATE TABLE `coin_order_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `coin_event_id` int NOT NULL,
  `deductions` double(15,2) NOT NULL DEFAULT '0.00',
  `deduction_type` enum('daily','onetime') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `coin_order_id` int NOT NULL,
  `required_coin` double NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_orders
-- ============================================================
CREATE TABLE `coin_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coin_package_id` int NOT NULL,
  `price` double NOT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `coins` double NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `company_id` int NOT NULL,
  `discount` double NOT NULL,
  `status` int NOT NULL COMMENT '1-completed,2-pending',
  `days` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_orderId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `payment_Id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cart_id` int DEFAULT NULL,
  `payment_link_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2045 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_package_points
-- ============================================================
CREATE TABLE `coin_package_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coin_package_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=825 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_package_types
-- ============================================================
CREATE TABLE `coin_package_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `days` int NOT NULL DEFAULT '0',
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_packages
-- ============================================================
CREATE TABLE `coin_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coin_package_type_id` int NOT NULL,
  `price` double NOT NULL,
  `price_usd` double(15,2) DEFAULT NULL,
  `total_price_monthly` double(15,2) NOT NULL DEFAULT '0.00',
  `total_price_days` double(15,2) NOT NULL DEFAULT '0.00',
  `total_price_rate` double(15,2) NOT NULL DEFAULT '0.00',
  `total_price_usd_monthly` double(15,2) NOT NULL DEFAULT '0.00',
  `total_price_usd_days` double(15,2) NOT NULL DEFAULT '0.00',
  `total_price_usd_rate` double(15,2) NOT NULL DEFAULT '0.00',
  `visibility_show` int NOT NULL DEFAULT '2',
  `coins` double NOT NULL,
  `coins_auto` double DEFAULT '0',
  `discount` int NOT NULL DEFAULT '0',
  `days` int NOT NULL,
  `status` int NOT NULL,
  `custome_pck` int DEFAULT '0',
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: coin_transection_logs
-- ============================================================
CREATE TABLE `coin_transection_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_company_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `event_by` int NOT NULL,
  `event` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `discount` double(15,2) DEFAULT '0.00',
  `coins` double(15,2) NOT NULL DEFAULT '0.00',
  `transection_type` enum('credit','debit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `settlement_coins` double(15,2) DEFAULT '0.00',
  `balance_coins` double(15,2) DEFAULT NULL,
  `team_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `coin_order_id` int DEFAULT NULL,
  `remain_coins` double(15,2) DEFAULT NULL,
  `actual_coins` double(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_orderId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `payment_Id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cart_id` int DEFAULT NULL,
  `payment_link_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: comments
-- ============================================================
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: commericial_countries
-- ============================================================
CREATE TABLE `commericial_countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commericial_id` int NOT NULL,
  `country_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `commtype` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1348 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: commericial_details
-- ============================================================
CREATE TABLE `commericial_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commericial_id` int NOT NULL,
  `exp` int DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `commtype` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13586 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: commericials
-- ============================================================
CREATE TABLE `commericials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `rateType` int DEFAULT NULL COMMENT '1-Fixed Price ,2-Perecentage ',
  `commtype` int DEFAULT NULL COMMENT '''2-fulltime'',''1-contract''',
  `status` int DEFAULT '1',
  `sal_mode` int DEFAULT NULL,
  `salary_currency` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_commericials_company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: companies
-- ============================================================
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
  `company_type` int DEFAULT '2' COMMENT '  1 => "Staffing Company",         2 => "Service Company",         3 => "Product Company"',
  `founded` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `industries` int DEFAULT NULL,
  `acount_type` int NOT NULL COMMENT '1: Public, 2:Private',
  `language` int DEFAULT NULL,
  `currency` int DEFAULT NULL,
  `time_format` int DEFAULT '1',
  `date_format` int DEFAULT NULL,
  `industries_type` int DEFAULT '0',
  `success_per` int NOT NULL DEFAULT '10',
  `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `offer_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_login` datetime DEFAULT CURRENT_TIMESTAMP,
  `leadCount` int NOT NULL DEFAULT '0',
  `bench_show_mode` int NOT NULL DEFAULT '0',
  `accountVerify` int NOT NULL DEFAULT '0' COMMENT '0-Pending, 1-verified',
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_career_page_link_unique` (`career_page_link`),
  KEY `companies_package_id_foreign` (`package_id`),
  KEY `idx_companies_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=893 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: company_coins
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=1783 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: company_packages
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=875 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: company_tags
-- ============================================================
CREATE TABLE `company_tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `tag_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_skills_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: company_user_points_logs
-- ============================================================
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
  `sourcing` decimal(10,2) DEFAULT '0.00',
  `screening` decimal(10,2) DEFAULT '0.00',
  `evaluation` decimal(10,2) DEFAULT '0.00',
  `negotiation` decimal(10,2) DEFAULT '0.00',
  `backout` decimal(10,2) DEFAULT '0.00',
  `backoutTeam` decimal(10,2) DEFAULT '0.00',
  `total_points` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59552 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- TABLE: consultancy
-- ============================================================
CREATE TABLE `consultancy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `consultancy_id` int NOT NULL,
  `contact_person_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission` float(5,2) NOT NULL,
  `payout` int NOT NULL,
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feedback` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: consultancy_file
-- ============================================================
CREATE TABLE `consultancy_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `consultancy_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_logical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_physical_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: conversation_log
-- ============================================================
CREATE TABLE `conversation_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `job_id` int DEFAULT NULL,
  `from_company_id` int DEFAULT NULL,
  `to_company_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: conversations
-- ============================================================
CREATE TABLE `conversations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `job_id` int DEFAULT NULL,
  `from_company_id` int DEFAULT NULL,
  `to_company_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: countries
-- ============================================================
CREATE TABLE `countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso3` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numeric_code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso2` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tld` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `native` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `subregion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subregion_id` int DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `translations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `emoji` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emojiu` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `wikidataid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: currencies
-- ============================================================
CREATE TABLE `currencies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_symbol` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usd_based_rates` double(15,2) NOT NULL,
  `inr_based_rates` double(15,2) NOT NULL,
  `getKey` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: currency_exchange_rates
-- ============================================================
CREATE TABLE `currency_exchange_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rate` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: custom_candidate_display_field
-- ============================================================
CREATE TABLE `custom_candidate_display_field` (
  `custom_field_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `schema_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int NOT NULL,
  `display_status` int NOT NULL,
  PRIMARY KEY (`custom_field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: custom_queue_jobs
-- ============================================================
CREATE TABLE `custom_queue_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=140715 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cv_check
-- ============================================================
CREATE TABLE `cv_check` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cv_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cv_extension` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `count_cv` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36526 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: default_rates
-- ============================================================
CREATE TABLE `default_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rates_fixed` double(15,2) DEFAULT NULL COMMENT 'contract',
  `rates_perecentage` double(15,2) DEFAULT NULL COMMENT 'fulltime',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- TABLE: departments
-- ============================================================
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: designations
-- ============================================================
CREATE TABLE `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designations_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: documents
-- ============================================================
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `documentable_id` int NOT NULL,
  `documentable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_document` (`company_id`,`documentable_id`,`documentable_type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=56285 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: email_count_companies
-- ============================================================
CREATE TABLE `email_count_companies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` text COLLATE utf8mb4_general_ci,
  `mailBody` text COLLATE utf8mb4_general_ci,
  `emailType` int NOT NULL COMMENT '1-Mass Mail,2-Candidate mail & Forward Resume,3-Interview Remainder Mail,4-Notification Mail,5-Accounts & Offer Mail',
  `company_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=937 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: email_notifications
-- ============================================================
CREATE TABLE `email_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_email_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '1',
  `mailType` int NOT NULL DEFAULT '1' COMMENT '1-Recruitment Notification\r\n2-Sales Notification\r\n3-Mass Mail Template\r\n4-Candidate Send Email Template\r\n5-Client Send Email Template',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: failed_jobs
-- ============================================================
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=28266 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: features
-- ============================================================
CREATE TABLE `features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `days` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '0' COMMENT '0->OFF, 1-ON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: footer_menu
-- ============================================================
CREATE TABLE `footer_menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_menu_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: footer_settings
-- ============================================================
CREATE TABLE `footer_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `social_links` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `footer_copyright_text` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_settings_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: front_cms_headers
-- ============================================================
CREATE TABLE `front_cms_headers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_background_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_backround_image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_login_in_menu` tinyint(1) NOT NULL,
  `show_register_in_menu` tinyint(1) NOT NULL,
  `show_login_in_header` tinyint(1) NOT NULL,
  `show_register_in_header` tinyint(1) NOT NULL,
  `custom_css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `call_to_action_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_to_action_button` enum('login','register') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `login_background` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `register_background` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_cms_headers_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: front_icon_features
-- ============================================================
CREATE TABLE `front_icon_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_icon_features_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: front_image_features
-- ============================================================
CREATE TABLE `front_image_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int unsigned NOT NULL DEFAULT '18',
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `front_image_features_language_settings_id_foreign` (`language_settings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: global_settings
-- ============================================================
CREATE TABLE `global_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int unsigned NOT NULL,
  `company_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locale` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `purchase_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supported_until` timestamp NULL DEFAULT NULL,
  `google_recaptcha_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `system_update` tinyint(1) NOT NULL DEFAULT '1',
  `show_review_modal` tinyint(1) NOT NULL DEFAULT '1',
  `delete_account_in` int DEFAULT NULL,
  `delete_account_hour_day` enum('day','hour') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hour',
  `bench_status` int NOT NULL DEFAULT '0' COMMENT '0-manual process, 1-auto approved',
  `terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'vendor terms ',
  `offer_terms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `privacy_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms_and_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fraud_alery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `trust_and_safety` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `report_issue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `grievances` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `public_market_job_status` int NOT NULL DEFAULT '0' COMMENT '0-manual process, 1-auto approved',
  `vendor_limit` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `global_settings_currency_id_foreign` (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: google_meeting_lists
-- ============================================================
CREATE TABLE `google_meeting_lists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `meeting_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `interview_date` datetime DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_link` (`meeting_link`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: google_meeting_schedule
-- ============================================================
CREATE TABLE `google_meeting_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meeting_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `interview_date` datetime NOT NULL,
  `user_id` int NOT NULL,
  `company_id` int NOT NULL,
  `interview_job_wise_con_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=527 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: google_sheets
-- ============================================================
CREATE TABLE `google_sheets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `sheet_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cand_sheet_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: insurence_offer_points
-- ============================================================
CREATE TABLE `insurence_offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poins` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Percentage` int NOT NULL,
  `condition_s` int NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int NOT NULL,
  `offer_id` int NOT NULL,
  `view_type` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: interview_feedback
-- ============================================================
CREATE TABLE `interview_feedback` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` int unsigned DEFAULT NULL,
  `application_id` int unsigned NOT NULL,
  `feedback_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `update_by` int NOT NULL,
  `available_status` int DEFAULT NULL,
  `feedback_id` int DEFAULT NULL,
  `star_ratings` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `interview_result` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overall_recommendation` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_ctc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_ctc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offered_ctc` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_budget` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice_period` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `compensation_fit` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_probability` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `risk_factors` text COLLATE utf8mb4_unicode_ci,
  `compensation_comments` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3517 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_hiring_process
-- ============================================================
CREATE TABLE `interview_hiring_process` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `process_id` int NOT NULL,
  `application_id` int NOT NULL,
  `schedule_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_mode
-- ============================================================
CREATE TABLE `interview_mode` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_categories_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_mode_lavel
-- ============================================================
CREATE TABLE `interview_mode_lavel` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_mode_id` int DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_categories_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_processes
-- ============================================================
CREATE TABLE `interview_processes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_questions
-- ============================================================
CREATE TABLE `interview_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` bigint DEFAULT NULL,
  `company_id` bigint DEFAULT NULL,
  `questions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `user_id` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: interview_schedule_comments
-- ============================================================
CREATE TABLE `interview_schedule_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_schedule_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_schedule_comments_interview_schedule_id_foreign` (`interview_schedule_id`),
  KEY `interview_schedule_comments_user_id_foreign` (`user_id`),
  KEY `interview_schedule_comments_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_schedule_employees
-- ============================================================
CREATE TABLE `interview_schedule_employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_schedule_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `user_accept_status` enum('accept','refuse','waiting') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_schedule_employees_interview_schedule_id_foreign` (`interview_schedule_id`),
  KEY `interview_schedule_employees_user_id_foreign` (`user_id`),
  KEY `interview_schedule_employees_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_schedules
-- ============================================================
CREATE TABLE `interview_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `job_application_id` int unsigned NOT NULL,
  `schedule_date` datetime NOT NULL,
  `status` enum('rejected','hired','pending','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_accept_status` enum('accept','refuse','waiting') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `schedule_from_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule_to_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reschedule` datetime DEFAULT NULL,
  `stage` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `substage` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewers` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode` int DEFAULT NULL,
  `interview_mode_lavel` int DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `available_status` tinyint DEFAULT NULL,
  `feedback` tinyint DEFAULT NULL,
  `interview_type` tinyint DEFAULT NULL,
  `interview_created_by` int DEFAULT NULL,
  `average_star_rating` int DEFAULT NULL,
  `interview_job_id` int DEFAULT NULL,
  `interview_job_wise_con_id` int DEFAULT NULL,
  `mailstatus` int NOT NULL DEFAULT '0',
  `email_template` int NOT NULL DEFAULT '0',
  `timezone_abbreviation` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_15_sent` tinyint NOT NULL DEFAULT '0',
  `reminder_60_sent` tinyint NOT NULL DEFAULT '0',
  `followup_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `followup_status_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2777 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: interview_skills_feedback
-- ============================================================
CREATE TABLE `interview_skills_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interview_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `update_by` int DEFAULT NULL,
  `candidate_status` int DEFAULT NULL,
  `feedback_status` int DEFAULT NULL,
  `feedback_id` int DEFAULT NULL,
  `star_ratings` int DEFAULT NULL,
  `skills` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `skills_exp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `skills_ratings` int DEFAULT NULL,
  `skills_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16045 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: interview_status
-- ============================================================
CREATE TABLE `interview_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `classname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: interview_sub_status
-- ============================================================
CREATE TABLE `interview_sub_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interview_status_id` int DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `classname` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: invitation_consultancy
-- ============================================================
CREATE TABLE `invitation_consultancy` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `child_id` int NOT NULL,
  `working_user_id` int NOT NULL,
  `company_id` int NOT NULL,
  `reference_id` int NOT NULL,
  `status` int NOT NULL COMMENT '0: pending, 1:Approved, 2: Rejected ',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: invitation_request
-- ============================================================
CREATE TABLE `invitation_request` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `fullname` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0:request send',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: invoices
-- ============================================================
CREATE TABLE `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: jd_details
-- ============================================================
CREATE TABLE `jd_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `jd` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `minimum_exp` double(15,2) DEFAULT NULL,
  `maxmum_exp` double(15,2) DEFAULT NULL,
  `skills` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1' COMMENT '0-Pending, 1-approved, 2-decline',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1054 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: job_additional_detail
-- ============================================================
CREATE TABLE `job_additional_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `job_id` int NOT NULL,
  `detail_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3973 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: job_application_answers
-- ============================================================
CREATE TABLE `job_application_answers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `job_application_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  `question_id` int unsigned NOT NULL,
  `answer` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_application_answers_job_application_id_foreign` (`job_application_id`),
  KEY `job_application_answers_job_id_foreign` (`job_id`),
  KEY `job_application_answers_question_id_foreign` (`question_id`),
  KEY `job_application_answers_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_application_skills
-- ============================================================
CREATE TABLE `job_application_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_application_id` int DEFAULT NULL,
  `skills` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=470706 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: job_applications
-- ============================================================
CREATE TABLE `job_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `submitted_by` int NOT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `thirdparty_company_id` int NOT NULL DEFAULT '0',
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
  `job_id` int unsigned DEFAULT NULL,
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
  `process_id` int DEFAULT NULL,
  `qualification` int DEFAULT NULL,
  `master_status_id` int DEFAULT '1',
  `child_status_id` int DEFAULT NULL,
  `proposed_ctc` int DEFAULT NULL,
  `any_offer` int NOT NULL DEFAULT '0',
  `document` int NOT NULL DEFAULT '0',
  `sub_for_approval` int NOT NULL DEFAULT '0',
  `offer_approved` int NOT NULL DEFAULT '0',
  `offer_release` int NOT NULL DEFAULT '0',
  `offer_acceptance` int NOT NULL DEFAULT '0',
  `joined` int NOT NULL DEFAULT '0',
  `joined_date` datetime DEFAULT NULL,
  `duplicate` int NOT NULL DEFAULT '0' COMMENT '0: Fresh Application, 1: For Duplicate Application',
  `percentage_hike` float DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` int DEFAULT NULL,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  `offer_amount` int DEFAULT NULL,
  `outsourcing_type` int DEFAULT NULL,
  `pay_rate_currency` int DEFAULT NULL,
  `pay_rate_amount` int DEFAULT NULL,
  `pay_rate_mode` int DEFAULT NULL,
  `bill_rate_currency` int DEFAULT NULL,
  `bill_rate_amount` int DEFAULT NULL,
  `bill_rate_mode` int DEFAULT NULL,
  `comission_type` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `employee_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condidate_type` enum('bench_condidate','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `ctc_hourly` double(15,2) DEFAULT NULL,
  `ctc_monthly` double(15,2) DEFAULT NULL,
  `ctc_yearly` double(15,2) DEFAULT NULL,
  `ectc_hourly` double(15,2) DEFAULT NULL,
  `ectc_monthly` double(15,2) DEFAULT NULL,
  `ectc_yearly` double(15,2) DEFAULT NULL,
  `superadminstatus` int NOT NULL DEFAULT '0' COMMENT '0-pending,1-approved,2-reject',
  `countforadd` int NOT NULL DEFAULT '0',
  `notice_comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ads_switch` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No',
  `linkedin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `github` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `behance` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `otherLink` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `interviewDateOne` datetime DEFAULT NULL,
  `interview_mode_one` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewDateTwo` datetime DEFAULT NULL,
  `interview_mode_two` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewDateThree` datetime DEFAULT NULL,
  `interview_mode_three` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewDateFour` datetime DEFAULT NULL,
  `interview_mode_four` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewDateFive` datetime DEFAULT NULL,
  `interview_mode_five` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewDateSix` datetime DEFAULT NULL,
  `interview_mode_six` enum('Telecon','Video','Face to Face') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_type` enum('JERO','DIRECT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `careersummary` text COLLATE utf8mb4_unicode_ci,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wm` enum('remote','hybrid','office') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employmentType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oh` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visastatu` text COLLATE utf8mb4_unicode_ci,
  `referencename` text COLLATE utf8mb4_unicode_ci,
  `reason_for_job_change` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `job_applications_company_id_foreign` (`company_id`),
  KEY `job_applications_job_id_foreign` (`job_id`),
  KEY `idx_job_id` (`job_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63619 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_assign
-- ============================================================
CREATE TABLE `job_assign` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `spok_user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_skills_skill_id_foreign` (`user_id`),
  KEY `job_skills_job_id_foreign` (`job_id`),
  KEY `job_skills_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9765 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_batches
-- ============================================================
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_categories
-- ============================================================
CREATE TABLE `job_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_categories_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13300 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_countries
-- ============================================================
CREATE TABLE `job_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `country_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2058 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_job_application
-- ============================================================
CREATE TABLE `job_job_application` (
  `job_application_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  KEY `job_job_application_job_application_id_foreign` (`job_application_id`),
  KEY `job_job_application_job_id_foreign` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_locations
-- ============================================================
CREATE TABLE `job_locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_locations_country_id_foreign` (`country_id`),
  KEY `job_locations_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_metronics_skills
-- ============================================================
CREATE TABLE `job_metronics_skills` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `skill_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_skills_job_id_foreign` (`job_id`),
  KEY `job_skills_company_id_foreign` (`company_id`),
  KEY `idx_job_metronics_skills_company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10238 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_qualifications
-- ============================================================
CREATE TABLE `job_qualifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int DEFAULT NULL,
  `qualification_id` int DEFAULT NULL,
  `qualification_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2292 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: job_questions
-- ============================================================
CREATE TABLE `job_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `question_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_questions_question_id_foreign` (`question_id`),
  KEY `job_questions_job_id_foreign` (`job_id`),
  KEY `job_questions_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: job_skills
-- ============================================================
CREATE TABLE `job_skills` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `skill_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_skills_skill_id_foreign` (`skill_id`),
  KEY `job_skills_job_id_foreign` (`job_id`),
  KEY `job_skills_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: jobs
-- ============================================================
CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `job_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_requirement` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_positions` int NOT NULL,
  `location_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','hold','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `required_columns` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_visibility` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `client_id` int DEFAULT NULL,
  `min_exp` int NOT NULL DEFAULT '0',
  `max_exp` int NOT NULL DEFAULT '0',
  `min_salary` int NOT NULL DEFAULT '0',
  `max_salary` int NOT NULL DEFAULT '0',
  `qualification` int DEFAULT NULL,
  `employment_type` int NOT NULL DEFAULT '0',
  `min_salary_currency` int DEFAULT NULL,
  `max_salary_currency` int DEFAULT NULL,
  `min_sal_mode` int DEFAULT NULL,
  `max_sal_mode` int DEFAULT NULL,
  `carrier_website` int DEFAULT NULL,
  `work_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `show_vendor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `closeType` int NOT NULL DEFAULT '0' COMMENT '0->vendor close, 1-auto close',
  `min_yearly` double(15,2) DEFAULT '0.00',
  `min_monthly` double(15,2) DEFAULT '0.00',
  `min_hourly` double(15,2) DEFAULT '0.00',
  `max_yearly` double(15,2) DEFAULT '0.00',
  `max_monthly` double(15,2) DEFAULT '0.00',
  `max_hourly` double(15,2) DEFAULT '0.00',
  `auto_date_job` date DEFAULT NULL,
  `public_market_status` int NOT NULL DEFAULT '0' COMMENT '0-All Jobs, 1-public market Pending, 2- Approved, 3- rejected 4-Vendor Limit',
  `work_shifts` enum('Day Shift','Night Shift','Rotational shift','UK Timezone','US Timezone') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Day Shift',
  PRIMARY KEY (`id`),
  KEY `jobs_location_id_foreign` (`location_id`),
  KEY `jobs_category_id_foreign` (`category_id`),
  KEY `jobs_company_id_foreign` (`company_id`),
  KEY `idx_jobs_company_id` (`company_id`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1411 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: join_applications
-- ============================================================
CREATE TABLE `join_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `master_status_id` int NOT NULL,
  `jobApplication` int NOT NULL,
  `addJobWiseCondidate` int NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `child_status_id` int DEFAULT NULL,
  `offer_amount` int NOT NULL DEFAULT '0',
  `joined_date` datetime DEFAULT NULL,
  `offer_currency` int NOT NULL DEFAULT '0',
  `offer_mode` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: language_settings
-- ============================================================
CREATE TABLE `language_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `language_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `language_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: link_payments
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: linked_in_settings
-- ============================================================
CREATE TABLE `linked_in_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disable',
  `callback_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: ltm_translations
-- ============================================================
CREATE TABLE `ltm_translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL DEFAULT '0',
  `locale` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1681 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: mail_permissions
-- ============================================================
CREATE TABLE `mail_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notification_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: market_job_added_count
-- ============================================================
CREATE TABLE `market_job_added_count` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `company_id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-new added, 1-old added ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: master_currency
-- ============================================================
CREATE TABLE `master_currency` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `symbol` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: master_date_format
-- ============================================================
CREATE TABLE `master_date_format` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `code` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `priority` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: master_email_preferences
-- ============================================================
CREATE TABLE `master_email_preferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort_desc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `priority` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: master_languages
-- ============================================================
CREATE TABLE `master_languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` char(49) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `iso_639-1` char(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- ============================================================
-- TABLE: master_notifications
-- ============================================================
CREATE TABLE `master_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notifications_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `notifications_email` int NOT NULL DEFAULT '1',
  `notifications_phone` int NOT NULL DEFAULT '1',
  `priority` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `mobile` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `company_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: master_status
-- ============================================================
CREATE TABLE `master_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_style` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `divname` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `direct_client` int NOT NULL DEFAULT '0',
  `direct_client_priority` int NOT NULL,
  `custom_client` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: master_time_format
-- ============================================================
CREATE TABLE `master_time_format` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `priority` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: master_timezone
-- ============================================================
CREATE TABLE `master_timezone` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_code` varchar(12) DEFAULT NULL,
  `country_name` varchar(44) DEFAULT NULL,
  `time_zone` varchar(30) DEFAULT NULL,
  `gmt_offse` varchar(10) DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `timezone_abbreviation` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8mb3;

-- ============================================================
-- TABLE: meeting_comments
-- ============================================================
CREATE TABLE `meeting_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meeting_schedule_id` int NOT NULL,
  `comment_by` int NOT NULL,
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: meeting_schedules
-- ============================================================
CREATE TABLE `meeting_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_date` datetime NOT NULL,
  `interview_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `interview_mode` bigint unsigned NOT NULL,
  `interview_mode_level` bigint unsigned DEFAULT NULL,
  `from_company_id` bigint unsigned NOT NULL,
  `to_company_id` bigint unsigned NOT NULL,
  `url_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_team` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `our_team` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: migrations
-- ============================================================
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: model_has_permissions
-- ============================================================
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: model_has_roles
-- ============================================================
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: modules
-- ============================================================
CREATE TABLE `modules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notice_periods
-- ============================================================
CREATE TABLE `notice_periods` (
  `np_id` int NOT NULL AUTO_INCREMENT,
  `np_title` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`np_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: offer_create_main_tds
-- ============================================================
CREATE TABLE `offer_create_main_tds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offer_id` int NOT NULL,
  `employment_type` int NOT NULL DEFAULT '0',
  `currency` int DEFAULT NULL,
  `salary_ctc` double(15,2) DEFAULT NULL,
  `salary_mode` int NOT NULL,
  `designation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `joined_date` datetime DEFAULT NULL,
  `joined` int DEFAULT NULL,
  `monthly_gross` double(15,2) DEFAULT NULL,
  `yearly_gross` double(15,2) DEFAULT NULL,
  `monthly_bounus_gross` double(15,2) DEFAULT NULL,
  `yearly_bonus_gross` double(15,2) DEFAULT NULL,
  `monthly_insurence_gross` double(15,2) DEFAULT NULL,
  `yearly_insurence_gross` double(15,2) DEFAULT NULL,
  `yearly_final_gross` double(15,2) DEFAULT NULL,
  `monthly_final_gross` double(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `candidate_status` int NOT NULL DEFAULT '0' COMMENT '0-created, 1-release,2-accept,3-reject,4-expired',
  `management_status` int NOT NULL DEFAULT '0' COMMENT '1-approved, 2-not approved, 3-In process',
  `status` int NOT NULL DEFAULT '1' COMMENT '1-new, 2-old, 3-join',
  `accept_date_time` datetime DEFAULT NULL,
  `signature_candidate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `signature_type` int NOT NULL DEFAULT '0',
  `approved_date_time` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_approved_date_time` datetime DEFAULT NULL,
  `send_accept_date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=357 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: offer_job_applications
-- ============================================================
CREATE TABLE `offer_job_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int DEFAULT NULL,
  `submitted_by` int DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `np_id` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondrary_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rating` enum('1','2','3','4','5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skype_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_letter` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `job_id` int unsigned DEFAULT NULL,
  `status_id` int unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `column_priority` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `total_exp` tinyint DEFAULT NULL,
  `rel_exp` tinyint DEFAULT NULL,
  `passport` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `ctc` int DEFAULT NULL,
  `ectc` int DEFAULT NULL,
  `current_location` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_designation` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_company` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `process_id` int DEFAULT NULL,
  `qualification` int DEFAULT NULL,
  `master_status_id` int DEFAULT '1',
  `child_status_id` int DEFAULT NULL,
  `serving_notice_period_mode` int NOT NULL DEFAULT '0',
  `proposed_ctc` int DEFAULT NULL,
  `any_offer` int NOT NULL DEFAULT '0',
  `document` int NOT NULL DEFAULT '0',
  `sub_for_approval` int NOT NULL DEFAULT '0',
  `offer_approved` int NOT NULL DEFAULT '0',
  `offer_release` int NOT NULL DEFAULT '0',
  `offer_acceptance` int NOT NULL DEFAULT '0',
  `joined` int NOT NULL DEFAULT '0',
  `joined_date` datetime DEFAULT NULL,
  `duplicate` int NOT NULL DEFAULT '0' COMMENT '0: Fresh Application, 1: For Duplicate Application',
  `offer_amount` int DEFAULT NULL,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  `confidential` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_applications_company_id_foreign` (`company_id`),
  KEY `job_applications_job_id_foreign` (`job_id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: offer_point_companies
-- ============================================================
CREATE TABLE `offer_point_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `points` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sorting` int DEFAULT NULL,
  `percentage` double(15,2) DEFAULT NULL,
  `view_type` int DEFAULT NULL COMMENT '0-Yearly, 1-Monthly, 2-Both',
  `condition_check` int DEFAULT NULL COMMENT '0-CTC, 1-Basic',
  `section_type` int NOT NULL DEFAULT '0' COMMENT '0-components, 1- other components',
  `company_id` int DEFAULT NULL,
  `deleteded` int DEFAULT '1',
  `section_divided` int NOT NULL DEFAULT '0',
  `show_default` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: offer_points
-- ============================================================
CREATE TABLE `offer_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `points` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `percentage` double(15,2) NOT NULL DEFAULT '0.00',
  `view_type` int DEFAULT NULL COMMENT '0-Yearly, 1-Monthly, 2-Both',
  `condition_check` int DEFAULT NULL COMMENT '0-CTC, 1-Basic',
  `section_type` int NOT NULL DEFAULT '0' COMMENT ' 0-components, 1- other components',
  `sorting` int NOT NULL DEFAULT '0',
  `deleteded` int NOT NULL DEFAULT '1',
  `section_divided` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: offer_terms_conditions
-- ============================================================
CREATE TABLE `offer_terms_conditions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offer_id` int NOT NULL,
  `terms_condition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: old_city_list
-- ============================================================
CREATE TABLE `old_city_list` (
  `city_id` int NOT NULL AUTO_INCREMENT,
  `state_id` int NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` int NOT NULL,
  `latitude` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'city.jpg',
  `top_ten_display_order` int NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47735 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: old_countries
-- ============================================================
CREATE TABLE `old_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: old_state_list
-- ============================================================
CREATE TABLE `old_state_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `state` varchar(50) NOT NULL,
  `image` varchar(55) DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4045 DEFAULT CHARSET=latin1;

-- ============================================================
-- TABLE: on_board_details
-- ============================================================
CREATE TABLE `on_board_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `job_application_id` int unsigned DEFAULT NULL,
  `designation_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `reports_to_id` int unsigned DEFAULT NULL,
  `salary_offered` int NOT NULL,
  `joining_date` date NOT NULL,
  `accept_last_date` date NOT NULL,
  `offer_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sign` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reject_reason` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hired_status` enum('offered','accepted','rejected','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_status` enum('part_time','full_time','on_contract') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `on_board_details_company_id_foreign` (`company_id`),
  KEY `on_board_details_job_application_id_foreign` (`job_application_id`),
  KEY `on_board_details_designation_id_foreign` (`designation_id`),
  KEY `on_board_details_department_id_foreign` (`department_id`),
  KEY `on_board_details_reports_to_id_foreign` (`reports_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: on_board_files
-- ============================================================
CREATE TABLE `on_board_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `on_board_detail_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `on_board_files_company_id_foreign` (`company_id`),
  KEY `on_board_files_on_board_detail_id_foreign` (`on_board_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_active
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=979 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_default
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: package_details
-- ============================================================
CREATE TABLE `package_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `package_id` int DEFAULT NULL,
  `package_feature_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `sub_total` double(15,2) DEFAULT NULL,
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '''Active'',''Inactive''',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1061 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_features
-- ============================================================
CREATE TABLE `package_features` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `features_type` enum('1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1-Operations,2-Sales',
  `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '1-active, 0-Inactive',
  `field_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_invoice
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=892 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_invoice_comment
-- ============================================================
CREATE TABLE `package_invoice_comment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_invoice_tax
-- ============================================================
CREATE TABLE `package_invoice_tax` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `tax_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percentage` int DEFAULT NULL,
  `tax_amount` float(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_master
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_master_item
-- ============================================================
CREATE TABLE `package_master_item` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_order
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=892 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_order_items
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=17821 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: package_transactions
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=110992 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: packages
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_reset_tokens
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_resets
-- ============================================================
CREATE TABLE `password_resets` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: payment_settings
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: paypal_invoices
-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: permission_role
-- ============================================================
CREATE TABLE `permission_role` (
  `permission_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: permissions
-- ============================================================
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` int unsigned NOT NULL,
  `priority` int NOT NULL,
  `status` int NOT NULL,
  `product_based` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  KEY `permissions_module_id_foreign` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: permissions_master
-- ============================================================
CREATE TABLE `permissions_master` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` int unsigned NOT NULL,
  `priority` int NOT NULL,
  `status` int NOT NULL,
  `product_based` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  KEY `permissions_module_id_foreign` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: pre_fixed_settings
-- ============================================================
CREATE TABLE `pre_fixed_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pre_fixed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('invoice','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: qualification_master
-- ============================================================
CREATE TABLE `qualification_master` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: questions
-- ============================================================
CREATE TABLE `questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `question` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable',
  `required` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: range_display
-- ============================================================
CREATE TABLE `range_display` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lower_limit` int unsigned DEFAULT NULL,
  `upper_limit` int NOT NULL,
  `display_text` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_categories_company_id_foreign` (`lower_limit`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: razorpay_invoices
-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: razorpay_subscriptions
-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: rec_candidate_assign
-- ============================================================
CREATE TABLE `rec_candidate_assign` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT '0',
  `source_from_company_id` int DEFAULT NULL,
  `source_from_user_id` int DEFAULT NULL,
  `source_from_id` int DEFAULT NULL,
  `source_from_type` int NOT NULL DEFAULT '0' COMMENT '0 for applicant, 1 for bench, 2 : For Public Bench',
  `source_company_id` int DEFAULT NULL,
  `source_user_id` int DEFAULT NULL,
  `source_application_id` int DEFAULT NULL,
  `source_jobid` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ac_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: recommendation_relation
-- ============================================================
CREATE TABLE `recommendation_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_company_id` int NOT NULL,
  `from_user_id` int NOT NULL DEFAULT '0',
  `from_status` int DEFAULT '0',
  `from_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `to_company_id` int DEFAULT NULL,
  `to_user_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `to_status` int DEFAULT NULL,
  `to_created_at` timestamp NULL DEFAULT NULL,
  `final_status` int DEFAULT '0',
  `conversation_id` int DEFAULT NULL,
  `add_status` int NOT NULL DEFAULT '0' COMMENT '0-recommendation, 1-market_in_added',
  `accepterms` blob,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recommendation_relation` (`to_company_id`,`job_id`),
  KEY `idx_recommendation` (`job_id`,`to_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: role_has_permissions
-- ============================================================
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: role_user
-- ============================================================
CREATE TABLE `role_user` (
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: roles
-- ============================================================
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `manual_add` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `roles_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sendmailforcandidates
-- ============================================================
CREATE TABLE `sendmailforcandidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL DEFAULT '1' COMMENT '1 pending, 0-done',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8793 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: seo_details
-- ============================================================
CREATE TABLE `seo_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_menu_id` bigint unsigned NOT NULL,
  `seo_title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `seo_description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_author` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_details_footer_menu_id_foreign` (`footer_menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sessions
-- ============================================================
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: skills
-- ============================================================
CREATE TABLE `skills` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skills_category_id_foreign` (`category_id`),
  KEY `skills_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sms_settings
-- ============================================================
CREATE TABLE `sms_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nexmo_status` enum('active','deactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'deactive',
  `nexmo_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nexmo_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nexmo_from` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'NEXMO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: smtp_settings
-- ============================================================
CREATE TABLE `smtp_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mail_driver` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'smtp',
  `mail_host` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'smtp.gmail.com',
  `mail_port` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '587',
  `mail_username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'youremail@gmail.com',
  `mail_password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'your password',
  `mail_from_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Recruit Saas',
  `mail_from_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin@example.com',
  `mail_encryption` enum('tls','ssl') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_mail_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_mail_port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_mail_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_mail_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_mail_encryption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_mail_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_mail_port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_mail_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_mail_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_mail_encryption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `contact_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '0',
  `dailyDate` date DEFAULT NULL,
  `dailyCount` int DEFAULT '0',
  `mailtype` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sold_by_address
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: staffing_company_invitation
-- ============================================================
CREATE TABLE `staffing_company_invitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_company_id` int NOT NULL,
  `from_client_id` int DEFAULT NULL,
  `from_user_id` int NOT NULL DEFAULT '0',
  `from_status` int DEFAULT '0',
  `from_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `to_company_id` int DEFAULT NULL,
  `to_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` int DEFAULT NULL COMMENT 'Registration status 0:For newly invitation, 1:Already Register  ',
  `job_id` int DEFAULT NULL,
  `to_invitation_status` int NOT NULL DEFAULT '0' COMMENT '0 : Pending , 1	: accept ,  -1 : ignore',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=911 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: staffing_company_relation
-- ============================================================
CREATE TABLE `staffing_company_relation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_company_id` int NOT NULL,
  `from_client_id` int DEFAULT NULL,
  `from_user_id` int NOT NULL DEFAULT '0',
  `from_status` int DEFAULT '0',
  `from_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `to_company_id` int DEFAULT NULL,
  `to_client_id` int DEFAULT NULL,
  `to_user_id` int DEFAULT NULL,
  `to_status` int DEFAULT NULL,
  `to_created_at` timestamp NULL DEFAULT NULL,
  `final_status` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_staffing_company_relation` (`to_company_id`,`from_company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=880 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: stage_point_tb
-- ============================================================
CREATE TABLE `stage_point_tb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stage_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `job_id` int NOT NULL,
  `user_id` int NOT NULL,
  `stage_points` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9618 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: state_list
-- ============================================================
CREATE TABLE `state_list` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(255) NOT NULL,
  `country_id` int NOT NULL,
  `image` varchar(3) NOT NULL,
  `fips_code` varchar(10) DEFAULT NULL,
  `iso2` varchar(10) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flag` tinyint(1) DEFAULT '1',
  `wikidataid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5228 DEFAULT CHARSET=latin1;

-- ============================================================
-- TABLE: sticky_notes
-- ============================================================
CREATE TABLE `sticky_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `note_text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `colour` enum('blue','yellow','red','gray','purple','green') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blue',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sticky_notes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: stripe_invoices
-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: sub_domain_module_settings
-- ============================================================
CREATE TABLE `sub_domain_module_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supported_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `schema` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'http',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: subscriptions
-- ============================================================
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: table 99
-- ============================================================
CREATE TABLE `table 99` (
  `COL 1` varchar(76) DEFAULT NULL,
  `COL 2` varchar(27) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- ============================================================
-- TABLE: tablename
-- ============================================================
CREATE TABLE `tablename` (
  `id` int DEFAULT NULL,
  `country_code` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tasks
-- ============================================================
CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` datetime NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: tb_ranks
-- ============================================================
CREATE TABLE `tb_ranks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '0-new, 1-other',
  `job_id` int DEFAULT NULL,
  `application_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63628 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- TABLE: td_industries
-- ============================================================
CREATE TABLE `td_industries` (
  `ind_id` int NOT NULL,
  `ind_name` varchar(255) DEFAULT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ============================================================
-- TABLE: team_meeting_lists
-- ============================================================
CREATE TABLE `team_meeting_lists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `meeting_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `interview_date` datetime DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_link` (`meeting_link`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: team_meeting_schedule
-- ============================================================
CREATE TABLE `team_meeting_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meeting_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `interview_date` datetime NOT NULL,
  `user_id` int NOT NULL,
  `company_id` int NOT NULL,
  `interview_job_wise_con_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: testapplications_original_backup
-- ============================================================
CREATE TABLE `testapplications_original_backup` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: testing
-- ============================================================
CREATE TABLE `testing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: th_email_template
-- ============================================================
CREATE TABLE `th_email_template` (
  `e_temp_id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template_subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `template_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `template_message` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mail_to` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mail_cc` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mail_bcc` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mail_from` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `curr_date` datetime NOT NULL,
  `temptype` int NOT NULL DEFAULT '1' COMMENT '1- fwd_resume, 2- mass-mail, 3- interview, 4-send mail , 5-bench reject',
  `tempForInterview` int NOT NULL DEFAULT '1',
  `company_id` bigint DEFAULT NULL,
  `orderByShow` int DEFAULT NULL,
  PRIMARY KEY (`e_temp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: theme_settings
-- ============================================================
CREATE TABLE `theme_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `primary_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_custom_css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `front_custom_css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `home_background_image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `theme_settings_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=955 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: ticket_statuses
-- ============================================================
CREATE TABLE `ticket_statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: tickets
-- ============================================================
CREATE TABLE `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_no` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `company_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: timeline
-- ============================================================
CREATE TABLE `timeline` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `application_id` int NOT NULL,
  `application_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_id` int NOT NULL,
  `process_id` int NOT NULL,
  `status_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `process_child_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible` int NOT NULL DEFAULT '0',
  `insert_type` int NOT NULL DEFAULT '0' COMMENT '0:only process/status update',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `interview_schedule_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_from_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_to_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode_child_level` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mention_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_additional_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timeline_type` int NOT NULL DEFAULT '0' COMMENT '0:For Status update, 1: For  Interview Schedule',
  `prev_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prev_process_child_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_schedules_id` int DEFAULT NULL,
  `average_star_rating` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `event_by_company_id` int DEFAULT NULL,
  `event_by_user_id` int DEFAULT NULL,
  `master_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: todo_items
-- ============================================================
CREATE TABLE `todo_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `position` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `todo_items_company_id_foreign` (`company_id`),
  KEY `todo_items_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: top_up_coin_packages
-- ============================================================
CREATE TABLE `top_up_coin_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `price` double(15,2) DEFAULT NULL,
  `price_usd` double(15,2) DEFAULT NULL,
  `coins` int DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '1-active, 0-block',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custome_chk` int DEFAULT '0',
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `per_coin_rate` double(15,6) DEFAULT NULL,
  `per_coin_rate_usd` double(15,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: top_up_logs
-- ============================================================
CREATE TABLE `top_up_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `coins` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `custome_chk` int NOT NULL DEFAULT '0',
  `event_type` enum('operational','sales') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_orderId` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `payment_Id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cart_id` int DEFAULT NULL,
  `payment_link_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: updatetimeline
-- ============================================================
CREATE TABLE `updatetimeline` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `event_by_company_id` int DEFAULT NULL,
  `event_by_user_id` int DEFAULT NULL,
  `master_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `child_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `application_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `note` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `interview_schedule_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_from_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_to_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode_child_level` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mention_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interviewers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_additional_email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_message` varchar(555) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_schedules_id` int DEFAULT NULL,
  `average_star_rating` int DEFAULT NULL,
  `prev_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prev_process_child_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  `offer_amount` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joined_date` datetime DEFAULT NULL,
  `candidate_status` int DEFAULT NULL,
  `management_status` int DEFAULT NULL,
  `accept_date_time` datetime DEFAULT NULL,
  `approved_date_time` datetime DEFAULT NULL,
  `send_approved_date_time` datetime DEFAULT NULL,
  `send_accept_date_time` datetime DEFAULT NULL,
  `email_id_hr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `name_hr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `yearly_gross` double(15,2) DEFAULT NULL,
  `yearly_final_gross` double(15,2) DEFAULT NULL,
  `mtypeSection` int DEFAULT NULL,
  `designation_offer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `timezone_abbreviation` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidential` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=123094 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: user_token
-- ============================================================
CREATE TABLE `user_token` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `root_user` int NOT NULL DEFAULT '0',
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `calling_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verified` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  `email_verification_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `working_status` int NOT NULL DEFAULT '1',
  `type` int NOT NULL DEFAULT '0' COMMENT '0: For Employer user , 1 : For Consultancy',
  `active_session` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_time` int NOT NULL,
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auto_date_team` date DEFAULT NULL,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `super_password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `super_admin_login_code` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1111',
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5883 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: users_profile
-- ============================================================
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
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: whatsapp_count_companies
-- ============================================================
CREATE TABLE `whatsapp_count_companies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `messageBody` text COLLATE utf8mb4_general_ci,
  `messageType` int DEFAULT NULL COMMENT '1-custom send,2-reminder',
  `company_id` bigint DEFAULT NULL,
  `instance_name` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: whatsapp_instances
-- ============================================================
CREATE TABLE `whatsapp_instances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `company_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `instance_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-active,0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLE: zoomsettings
-- ============================================================
CREATE TABLE `zoomsettings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `redirect_uri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `client_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `client_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `zoom_access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

