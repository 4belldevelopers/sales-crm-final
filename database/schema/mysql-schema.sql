/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `add_job_wise_condidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_job_wise_condidates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `job_application_id` int(11) DEFAULT NULL,
  `rec_candidate_assign_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `master_status_id` int(11) DEFAULT NULL,
  `child_status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `thirdparty_company_id` int(11) NOT NULL DEFAULT 0,
  `company_id` int(11) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `placed_end_date` date DEFAULT NULL,
  `existing` int(11) DEFAULT 0,
  `skill_match` double(15,1) DEFAULT 0.0,
  PRIMARY KEY (`id`),
  KEY `idx_awjc_job_status` (`job_id`,`master_status_id`),
  KEY `idx_awjc_company_job` (`company_id`,`job_id`),
  KEY `idx_awjc_thirdparty_job` (`thirdparty_company_id`,`job_id`),
  KEY `idx_awjc_app` (`job_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `applicant_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applicant_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `job_application_id` int(10) unsigned NOT NULL,
  `note_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_app_notes_app_id` (`job_application_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_additional_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_additional_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `job_detail_value` varchar(255) DEFAULT NULL,
  `application_detail_value` varchar(255) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_counters` (
  `job_id` int(10) unsigned NOT NULL,
  `total_apps` int(10) unsigned NOT NULL DEFAULT 0,
  `interview_apps` int(10) unsigned NOT NULL DEFAULT 0,
  `offer_apps` int(10) unsigned NOT NULL DEFAULT 0,
  `rejected_apps` int(10) unsigned NOT NULL DEFAULT 0,
  `last_event_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `idx_ac_last_event` (`last_event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(25) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `legal_term` longtext DEFAULT NULL,
  `mail_setting` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `application_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `position` smallint(6) DEFAULT NULL,
  `color` varchar(12) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `basic_offer_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `basic_offer_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poins` text NOT NULL,
  `Percentage` int(11) NOT NULL,
  `condition_s` int(11) NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `view_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_additional_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_additional_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bench_id` int(11) DEFAULT NULL,
  `custom_keys` varchar(55) DEFAULT NULL,
  `custom_keys_value` varchar(255) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `full_name` varchar(191) DEFAULT NULL,
  `current_designation` varchar(55) DEFAULT NULL,
  `outsourcing_type` int(11) NOT NULL DEFAULT 1,
  `phone` varchar(191) DEFAULT NULL,
  `secondary_mobile` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `secondrary_email` varchar(55) DEFAULT NULL,
  `qualification` int(11) DEFAULT NULL,
  `total_exp` tinyint(4) DEFAULT NULL,
  `rel_exp` tinyint(4) DEFAULT NULL,
  `serving_notice_period_mode` int(11) NOT NULL DEFAULT 0,
  `np_id` varchar(55) DEFAULT NULL,
  `pay_rate_currency` int(11) DEFAULT NULL,
  `pay_rate_amount` int(11) DEFAULT NULL,
  `pay_rate_mode` int(11) DEFAULT NULL,
  `bill_rate_currency` int(11) DEFAULT NULL,
  `bill_rate_amount` int(11) DEFAULT NULL,
  `bill_rate_mode` int(11) DEFAULT NULL,
  `comission_type` int(11) DEFAULT NULL COMMENT '1: For Free, 2: For Paid',
  `country` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `status` int(10) unsigned DEFAULT 1,
  `offer_amount` int(11) DEFAULT NULL,
  `joined_date` datetime DEFAULT NULL,
  `employee_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `rating` enum('1','2','3','4','5') DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `current_company` varchar(55) DEFAULT NULL,
  `process_id` int(11) DEFAULT NULL,
  `master_status_id` int(11) DEFAULT 1,
  `child_status_id` int(11) DEFAULT NULL,
  `proposed_ctc` int(11) DEFAULT NULL,
  `any_offer` int(11) NOT NULL DEFAULT 0,
  `offer_approved` int(11) NOT NULL DEFAULT 0,
  `offer_release` int(11) NOT NULL DEFAULT 0,
  `offer_acceptance` int(11) NOT NULL DEFAULT 0,
  `joined` int(11) NOT NULL DEFAULT 0,
  `offer_currency` int(11) DEFAULT NULL,
  `offer_mode` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_bench_company_status` (`company_id`,`status`),
  KEY `idx_bench_job` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_assign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `bench_applications_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_bench_assign_app` (`bench_applications_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_candidate_skill_experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_candidate_skill_experience` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint(20) unsigned NOT NULL,
  `skill_key` varchar(100) NOT NULL COMMENT 'java, php, html',
  `year` tinyint(3) unsigned DEFAULT NULL,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `total_months` smallint(5) unsigned GENERATED ALWAYS AS (`year` * 12 + `month`) STORED,
  `created_at` timestamp NULL DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_candidate_skill` (`candidate_id`,`skill_key`),
  KEY `idx_candidate` (`candidate_id`),
  KEY `idx_skill_key` (`skill_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_master_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_master_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `css_style` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bench_place`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bench_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_comapny_id` int(11) NOT NULL,
  `bench_id` int(11) NOT NULL,
  `client_company_id` int(11) DEFAULT NULL,
  `client_job_id` int(11) NOT NULL,
  `client_application_id` int(11) NOT NULL,
  `full_name` varchar(55) DEFAULT NULL,
  `current_designation` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_exp` varchar(10) DEFAULT NULL,
  `rel_exp` varchar(10) DEFAULT NULL,
  `pay_rate_currency` varchar(10) DEFAULT NULL,
  `pay_rate_amount` varchar(10) DEFAULT NULL,
  `pay_rate_mode` varchar(10) DEFAULT NULL,
  `bill_rate_currency` varchar(10) DEFAULT NULL,
  `bill_rate_amount` varchar(10) DEFAULT NULL,
  `bill_rate_mode` varchar(10) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `company_name` varchar(55) DEFAULT NULL,
  `qualification` varchar(55) DEFAULT NULL,
  `skills` varchar(55) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `assign_to` varchar(255) DEFAULT NULL,
  `placed_end_date` varchar(20) DEFAULT NULL,
  `offer_amount` varchar(10) DEFAULT NULL,
  `joined_date` varchar(25) DEFAULT NULL,
  `application_stage` varchar(20) DEFAULT NULL,
  `application_child_stage` varchar(20) DEFAULT NULL,
  `bench_status` varchar(10) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `offer_currency` int(11) DEFAULT NULL,
  `offer_mode` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `benchboostkeywordcandidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benchboostkeywordcandidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `skill_name` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_boost_cand` (`candidate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billing_address_by_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_address_by_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_transaction_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `first_name` text DEFAULT NULL,
  `last_name` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `building` text DEFAULT NULL,
  `street` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `organization` text DEFAULT NULL,
  `gstin` text DEFAULT NULL,
  `sold_country_id` int(11) DEFAULT NULL,
  `sold_first_name` text DEFAULT NULL,
  `sold_last_name` text DEFAULT NULL,
  `sold_phone` varchar(255) DEFAULT NULL,
  `sold_building` text DEFAULT NULL,
  `sold_street` text DEFAULT NULL,
  `sold_state_id` int(11) DEFAULT NULL,
  `sold_city` int(11) DEFAULT NULL,
  `sold_postal_code` varchar(255) DEFAULT NULL,
  `sold_organization` text DEFAULT NULL,
  `sold_gstin` text DEFAULT NULL,
  `sold_pan_no` text DEFAULT NULL,
  `sold_logo` varchar(255) DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billing_address_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_address_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `first_name` text DEFAULT NULL,
  `last_name` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `building` text DEFAULT NULL,
  `street` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `organization` text DEFAULT NULL,
  `gstin` text DEFAULT NULL,
  `pan_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `block_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `block_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_type` varchar(20) NOT NULL COMMENT 'user | company',
  `target_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `block_log_target` (`target_type`,`target_id`),
  KEY `block_log_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bonus_offer_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonus_offer_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poins` text NOT NULL,
  `Percentage` int(11) NOT NULL,
  `condition_s` int(11) NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `view_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boostkeywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boostkeywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `skill_name` text DEFAULT NULL,
  `campaign_name` varchar(255) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `campaign_type` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `clickCount` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_job_skill_exp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_job_skill_exp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_skill_id` int(11) NOT NULL,
  `skill_value` text DEFAULT NULL,
  `job_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cjse_candidate_job` (`candidate_id`,`job_id`),
  KEY `idx_cjse_job` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_skills_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_skills_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `skill_value` varchar(55) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `name` varchar(55) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delete_status` int(11) NOT NULL DEFAULT 1 COMMENT '0: For not deleted, 1:For deleted',
  PRIMARY KEY (`id`),
  KEY `idx_cs_parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidates_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidates_notify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `candidate_name` varchar(255) DEFAULT NULL,
  `candidate_email` varchar(255) DEFAULT NULL,
  `candidate_phone` varchar(255) DEFAULT NULL,
  `candidate_total_exp` double(15,1) DEFAULT NULL,
  `candidate_rel_exp` double(15,1) DEFAULT NULL,
  `candidate_currency` int(11) DEFAULT NULL,
  `candidate_sal_mode` int(11) DEFAULT NULL,
  `candidate_ctc` double(15,2) DEFAULT NULL,
  `candidate_ectc` double(15,2) DEFAULT NULL,
  `candidate_skills` text DEFAULT NULL,
  `candidate_location` text DEFAULT NULL,
  `candidate_edication` varchar(255) DEFAULT NULL,
  `candidate_cv` text DEFAULT NULL,
  `candidate_type` enum('bench_condidate','other') DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1-pending, 0-done',
  `send_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cart_transaction_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `subscription_type` enum('yes','no') NOT NULL DEFAULT 'yes',
  `pck_top_id` int(11) DEFAULT NULL,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `product_name` text DEFAULT NULL,
  `duration` text DEFAULT NULL,
  `credit` double(15,2) NOT NULL,
  `currency` text DEFAULT NULL,
  `price` double(15,2) NOT NULL,
  `gst` double(15,2) DEFAULT NULL,
  `gst_amount` double DEFAULT NULL,
  `total_price` double(15,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('created','authorized','captured','failed','refunded','partially_refunded') NOT NULL COMMENT 'created -Payment has been authorized but not yet captured, authorized -Payment has been successfully captured, captured-Payment has been successfully captured, failed- Payment has failed, refunded-Payment has been refunded, partially_refunde- Payment has been partially refunded',
  `payment_orderId` text DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `international` int(11) DEFAULT 0 COMMENT '0-false, 1-true',
  `bank` varchar(255) DEFAULT NULL,
  `payment_Id` varchar(255) DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `payment_mode` enum('Cash','Cheque','Bank Transfer','Online','Payment Link') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `chequenumber` varchar(255) DEFAULT NULL,
  `chequedate` date DEFAULT NULL,
  `bankname` varchar(255) NOT NULL,
  `transactionid` varchar(255) NOT NULL,
  `bankreference` varchar(255) NOT NULL,
  `payment_link_id` text DEFAULT NULL,
  `invoiceId` text DEFAULT NULL,
  `invoice_date` datetime DEFAULT NULL,
  `invoice_approval` enum('Yes','No') NOT NULL DEFAULT 'No',
  `approval_invoice_no` text DEFAULT NULL,
  `approval_invoice_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id_id` (`company_id`,`id`),
  KEY `idx_company_gateway_payment` (`company_id`,`payment_gateway`,`payment_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `subscription_type` enum('yes','no') NOT NULL DEFAULT 'yes',
  `pck_top_id` int(11) DEFAULT NULL,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `product_name` text DEFAULT NULL,
  `duration` text DEFAULT NULL,
  `credit` double(15,2) NOT NULL,
  `currency` text DEFAULT NULL,
  `price` double(15,2) NOT NULL,
  `gst` double(15,2) DEFAULT NULL,
  `gst_amount` double DEFAULT NULL,
  `total_price` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL DEFAULT 0.00,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_company_status` (`company_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ccustom_queue_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ccustom_queue_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  `state_code` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) DEFAULT 1,
  `wikidataid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `city_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city_list` (
  `city_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `pincode` int(11) NOT NULL,
  `latitude` varchar(11) NOT NULL,
  `longitude` varchar(11) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'city.jpg',
  `top_ten_display_order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `flag` int(11) NOT NULL DEFAULT 1,
  `wikidataid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_city_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_city_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_contact_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `contact_person_name` varchar(55) DEFAULT NULL,
  `contact_person_email` varchar(55) DEFAULT NULL,
  `calling_code` varchar(11) DEFAULT NULL,
  `contact_person_number` varchar(55) DEFAULT NULL,
  `contact_person_designation` varchar(55) DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_contact_info_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_contact_info_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `client_contact_info_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_feedbacks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `feedback` text NOT NULL,
  `client_title` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_logical_path` varchar(255) NOT NULL,
  `file_physical_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_type` varchar(30) NOT NULL,
  `file_size` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `city` varchar(55) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `address` varchar(55) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_loc_client` (`client_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client_location_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_location_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `client_location_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `self_company` int(11) NOT NULL DEFAULT 0,
  `name` varchar(55) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `logo_physical_path` varchar(255) DEFAULT NULL,
  `logo_logical_path` varchar(255) DEFAULT NULL,
  `website` varchar(255) NOT NULL,
  `industry` int(11) DEFAULT 0,
  `company_size_from` int(11) DEFAULT NULL,
  `company_sizes` int(11) DEFAULT NULL,
  `company_type` int(11) DEFAULT 0,
  `founded` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(55) NOT NULL,
  `contact_person_name` varchar(55) NOT NULL,
  `contact_number` varchar(55) NOT NULL,
  `commission` varchar(10) NOT NULL,
  `payout` varchar(10) NOT NULL,
  `detail` text DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_clients_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` enum('operational','sales') NOT NULL DEFAULT 'sales',
  `title` varchar(255) DEFAULT NULL,
  `default_rate` double(15,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `deduction_type` enum('daily','onetime') DEFAULT NULL,
  `minimum_required` double(15,2) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_order_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_order_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `coin_event_id` int(11) NOT NULL,
  `deductions` double(15,2) NOT NULL DEFAULT 0.00,
  `deduction_type` enum('daily','onetime') NOT NULL,
  `coin_order_id` int(11) NOT NULL,
  `required_coin` double NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_package_id` int(11) NOT NULL,
  `price` double NOT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `coins` double NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  `company_id` int(11) NOT NULL,
  `discount` double NOT NULL,
  `status` int(11) NOT NULL COMMENT '1-completed,2-pending',
  `days` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_orderId` text DEFAULT NULL,
  `payment_Id` text DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `payment_link_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_package_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_package_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_package_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_package_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_package_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `days` int(11) NOT NULL DEFAULT 0,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_package_type_id` int(11) NOT NULL,
  `price` double NOT NULL COMMENT 'use yearly',
  `price_usd` double(15,2) DEFAULT NULL COMMENT 'use yearly',
  `total_price_monthly` double(15,2) NOT NULL DEFAULT 0.00,
  `total_price_days` double(15,2) NOT NULL DEFAULT 0.00,
  `total_price_rate` double(15,2) NOT NULL DEFAULT 0.00,
  `total_price_usd_monthly` double(15,2) NOT NULL DEFAULT 0.00,
  `total_price_usd_days` double(15,2) NOT NULL DEFAULT 0.00,
  `total_price_usd_rate` double(15,2) NOT NULL DEFAULT 0.00,
  `visibility_show` int(11) NOT NULL DEFAULT 2,
  `coins` double NOT NULL,
  `coins_auto` double DEFAULT 0,
  `discount` int(11) NOT NULL DEFAULT 0,
  `days` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `custome_pck` int(11) DEFAULT 0,
  `event_type` enum('operational','sales') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coin_transection_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_transection_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_company_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `event_by` int(11) NOT NULL,
  `event` text DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `discount` double(15,2) DEFAULT 0.00,
  `coins` double(15,2) NOT NULL DEFAULT 0.00,
  `transection_type` enum('credit','debit') NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  `settlement_coins` double(15,2) DEFAULT 0.00,
  `balance_coins` double(15,2) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `coin_order_id` int(11) DEFAULT NULL,
  `remain_coins` double(15,2) DEFAULT NULL,
  `actual_coins` double(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_orderId` text DEFAULT NULL,
  `payment_Id` text DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `payment_link_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comp_event_created` (`company_id`,`event_type`,`created_at`),
  KEY `idx_company_event_type_id` (`company_id`,`event_type`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commercial_experience_bands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commercial_experience_bands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `label` varchar(100) NOT NULL,
  `min_experience` smallint(5) unsigned NOT NULL,
  `max_experience` smallint(5) unsigned NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commercial_experience_bands_code_unique` (`code`),
  UNIQUE KEY `commercial_experience_bands_sort_order_unique` (`sort_order`),
  KEY `commercial_experience_bands_active_sort_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commericial_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commericial_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commericial_id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `commtype` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commericial_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commericial_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commericial_id` int(11) NOT NULL,
  `exp` int(11) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `commtype` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comm_details_comp_type` (`company_id`,`commtype`,`rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commericials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commericials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `rateType` int(11) DEFAULT NULL COMMENT '1-Fixed Price ,2-Perecentage ',
  `commtype` int(11) DEFAULT NULL COMMENT '''2-fulltime'',''1-contract''',
  `status` int(11) DEFAULT 1,
  `sal_mode` int(11) DEFAULT NULL,
  `salary_currency` int(11) DEFAULT NULL,
  `replacement_period_days` smallint(6) DEFAULT NULL,
  `replacement_type` varchar(32) DEFAULT NULL,
  `replacement_eligibility` varchar(32) DEFAULT NULL,
  `billing_frequency` varchar(24) DEFAULT NULL,
  `billing_trigger` varchar(32) DEFAULT NULL,
  `payment_terms_days` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_comm_comp_type` (`company_id`,`commtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_domain` varchar(191) DEFAULT NULL,
  `package_id` int(10) unsigned DEFAULT NULL,
  `company_name` varchar(191) NOT NULL,
  `company_email` varchar(191) NOT NULL,
  `calling_code` varchar(11) DEFAULT NULL,
  `company_phone` varchar(191) DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `domain_key` varchar(255) DEFAULT NULL,
  `locale` varchar(191) NOT NULL,
  `timezone` varchar(191) NOT NULL DEFAULT 'Asia/Kolkata',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `show_in_frontend` enum('true','false') NOT NULL DEFAULT 'true',
  `career_page_link` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `package_type` varchar(191) DEFAULT NULL,
  `licence_expire_on` date DEFAULT NULL,
  `job_opening_title` text NOT NULL,
  `job_opening_text` text NOT NULL,
  `linkedin` enum('enable','disable') NOT NULL DEFAULT 'disable',
  `stripe_id` varchar(191) DEFAULT NULL,
  `card_brand` varchar(191) DEFAULT NULL,
  `card_last_four` varchar(191) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `featured_start_date` date DEFAULT NULL,
  `featured_end_date` date DEFAULT NULL,
  `login_background` varchar(191) DEFAULT NULL,
  `delete_account_at` datetime DEFAULT NULL,
  `app_duplicate` int(11) NOT NULL DEFAULT 0 COMMENT '0: None, 1: For current job, 2: For overall application',
  `app_duplicate_days` int(11) DEFAULT NULL,
  `zoom_status` int(11) DEFAULT NULL,
  `zoom_api_key` varchar(255) DEFAULT NULL,
  `zoom_api_secret` varchar(255) DEFAULT NULL,
  `zoom_email` varchar(55) NOT NULL,
  `zoom_access_token` text DEFAULT NULL,
  `google_status` int(11) DEFAULT NULL,
  `google_client_id` varchar(255) DEFAULT NULL,
  `google_client_secret` varchar(255) DEFAULT NULL,
  `google_access_token` varchar(255) NOT NULL,
  `google_expires_in` int(11) NOT NULL,
  `google_refresh_token` varchar(255) NOT NULL,
  `google_scope` varchar(255) NOT NULL,
  `google_token_type` varchar(10) NOT NULL,
  `google_verify` int(11) NOT NULL DEFAULT 0,
  `microsoft_status` int(11) DEFAULT NULL,
  `microsoft_api_key` varchar(255) DEFAULT NULL,
  `microsoft_api_secret` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `company_size_from` int(11) DEFAULT NULL,
  `company_size` int(11) DEFAULT NULL,
  `company_type` int(11) DEFAULT 2 COMMENT '  1 => "Staffing Company",         2 => "Service Company",         3 => "Product Company"',
  `founded` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `industries` int(11) DEFAULT NULL,
  `acount_type` int(11) NOT NULL COMMENT '1: Public, 2:Private',
  `language` int(11) DEFAULT NULL,
  `currency` int(11) DEFAULT NULL,
  `time_format` int(11) DEFAULT 1,
  `date_format` int(11) DEFAULT NULL,
  `industries_type` int(11) DEFAULT 0,
  `success_per` int(11) NOT NULL DEFAULT 10,
  `terms` text DEFAULT NULL,
  `offer_terms` text DEFAULT NULL,
  `internship_offer_terms` longtext DEFAULT NULL,
  `contract_offer_terms` longtext DEFAULT NULL,
  `agency_offer_terms` longtext DEFAULT NULL,
  `last_login` datetime DEFAULT current_timestamp(),
  `leadCount` int(11) NOT NULL DEFAULT 0,
  `bench_show_mode` int(11) NOT NULL DEFAULT 0,
  `accountVerify` int(11) NOT NULL DEFAULT 0 COMMENT '0-Pending, 1-verified',
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_sub_domain_unique` (`sub_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_career_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_career_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `primary_color` varchar(20) DEFAULT '#2563EB',
  `secondary_color` varchar(20) DEFAULT '#10B981',
  `font_family` varchar(50) DEFAULT 'Poppins',
  `default_language` varchar(10) DEFAULT 'en',
  `currency` varchar(10) DEFAULT 'INR',
  `page_layout` varchar(20) DEFAULT 'modern',
  `jobs_display` varchar(20) DEFAULT 'card',
  `career_page_status` tinyint(1) DEFAULT 1,
  `td_template` varchar(30) DEFAULT 'modern',
  `td_color_primary` varchar(20) DEFAULT '#2563EB',
  `td_color_secondary` varchar(20) DEFAULT '#1F2937',
  `td_color_accent` varchar(20) DEFAULT '#10B981',
  `td_color_bg` varchar(20) DEFAULT '#FFFFFF',
  `td_theme_mode` varchar(10) DEFAULT 'light',
  `td_layout` varchar(20) DEFAULT 'wide',
  `td_logo_path` varchar(500) DEFAULT NULL,
  `td_favicon_path` varchar(500) DEFAULT NULL,
  `td_hero_img1_path` varchar(500) DEFAULT NULL,
  `td_hero_img2_path` varchar(500) DEFAULT NULL,
  `td_opt_life` tinyint(1) DEFAULT 1,
  `td_opt_testi` tinyint(1) DEFAULT 1,
  `td_opt_animation` tinyint(1) DEFAULT 1,
  `td_custom_css` text DEFAULT NULL,
  `stat_departments` int(11) DEFAULT 15,
  `stat_countries` int(11) DEFAULT 5,
  `stat_employees` varchar(20) DEFAULT '220+',
  `stat_retention` varchar(20) DEFAULT '95%',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hero_layout` varchar(30) DEFAULT 'left-right',
  `hero_badge` varchar(255) DEFAULT NULL,
  `hero_heading` varchar(255) DEFAULT NULL,
  `hero_description` text DEFAULT NULL,
  `hero_cta1_text` varchar(100) DEFAULT NULL,
  `hero_cta1_link` varchar(255) DEFAULT NULL,
  `hero_cta1_newtab` tinyint(1) NOT NULL DEFAULT 0,
  `hero_cta2_text` varchar(100) DEFAULT NULL,
  `hero_cta2_link` varchar(255) DEFAULT NULL,
  `hero_cta2_newtab` tinyint(1) NOT NULL DEFAULT 0,
  `hero_bg_style` varchar(30) DEFAULT 'gradient',
  `hero_bg_gradient` varchar(255) DEFAULT NULL,
  `ci_industry` varchar(100) DEFAULT NULL,
  `ci_company_name` varchar(150) DEFAULT NULL,
  `ci_headquarters` varchar(255) DEFAULT NULL,
  `ci_tagline` varchar(255) DEFAULT NULL,
  `ci_website` varchar(255) DEFAULT NULL,
  `ci_linkedin` varchar(255) DEFAULT NULL,
  `ci_founded_year` int(11) DEFAULT NULL,
  `ci_company_size` varchar(50) DEFAULT NULL,
  `ci_twitter` varchar(255) DEFAULT NULL,
  `ci_about` text DEFAULT NULL,
  `ci_mission` text DEFAULT NULL,
  `ci_vision` text DEFAULT NULL,
  `benefits_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `benefits_title` varchar(150) DEFAULT NULL,
  `benefits_description` text DEFAULT NULL,
  `benefits_display_style` varchar(30) DEFAULT 'grid',
  `benefits_count` int(11) NOT NULL DEFAULT 8,
  `js_display_style` varchar(30) DEFAULT 'card',
  `js_per_page` int(11) NOT NULL DEFAULT 12,
  `js_sort_by` varchar(30) DEFAULT 'newest',
  `js_show_posted_date` tinyint(1) NOT NULL DEFAULT 1,
  `js_show_experience` tinyint(1) NOT NULL DEFAULT 1,
  `js_show_salary` tinyint(1) NOT NULL DEFAULT 1,
  `js_show_tags` tinyint(1) NOT NULL DEFAULT 1,
  `js_apply_option` varchar(30) DEFAULT 'resume',
  `seo_meta_title` varchar(255) DEFAULT NULL,
  `seo_meta_desc` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `seo_robots` varchar(50) DEFAULT 'index,follow',
  `seo_canonical` varchar(500) DEFAULT NULL,
  `seo_linkedin` varchar(255) DEFAULT NULL,
  `seo_twitter` varchar(255) DEFAULT NULL,
  `seo_facebook` varchar(255) DEFAULT NULL,
  `seo_instagram` varchar(255) DEFAULT NULL,
  `seo_youtube` varchar(255) DEFAULT NULL,
  `adv_caching` tinyint(1) NOT NULL DEFAULT 1,
  `adv_cache_ttl` int(11) NOT NULL DEFAULT 86400,
  `adv_cdn` tinyint(1) NOT NULL DEFAULT 1,
  `adv_img_opt` tinyint(1) NOT NULL DEFAULT 1,
  `adv_maintenance` tinyint(1) NOT NULL DEFAULT 0,
  `adv_maintenance_msg` text DEFAULT NULL,
  `adv_ga4_id` varchar(50) DEFAULT NULL,
  `adv_fb_pixel` varchar(50) DEFAULT NULL,
  `adv_linkedin_tag` varchar(100) DEFAULT NULL,
  `adv_hotjar_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_career_settings_company_id_unique` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_coins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_coins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_order_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `event_type` enum('operational','sales') NOT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `amount` double(15,2) NOT NULL,
  `discount` double(15,2) DEFAULT 0.00,
  `coins` double(15,2) NOT NULL DEFAULT 0.00,
  `actual_coins` double(15,2) DEFAULT NULL,
  `balance_coin` double(15,2) DEFAULT 0.00,
  `status` enum('valid','expired') NOT NULL,
  `free_pack_status` enum('active','block') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `free_update_date` date DEFAULT NULL,
  `auto_team_coins` date DEFAULT NULL,
  `auto_job_coins` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_event` (`company_id`,`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `stripe_id` int(11) DEFAULT NULL,
  `paypal_id` int(11) DEFAULT NULL,
  `paypal_environment` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `package_type` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `tag_name` varchar(55) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_user_points_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_user_points_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `candidate_id` bigint(20) unsigned DEFAULT NULL,
  `profileUpload` decimal(10,2) DEFAULT 0.00,
  `interviewScheduled` decimal(10,2) DEFAULT 0.00,
  `interviewNoShow` decimal(10,2) DEFAULT 0.00,
  `offerReleased` decimal(10,2) DEFAULT 0.00,
  `candidateJoined` decimal(10,2) DEFAULT 0.00,
  `candidateJoinedContract` decimal(10,2) DEFAULT 0.00,
  `jobsPosted` decimal(10,2) DEFAULT 0.00,
  `feedbackGivenOnCVs` decimal(10,2) DEFAULT 0.00,
  `postJobToVendorMarket` decimal(10,2) DEFAULT 0.00,
  `outsourceJobsToVendors` decimal(10,2) DEFAULT 0.00,
  `listBenchCandidates` decimal(10,2) DEFAULT 0.00,
  `respondToBenchRequests` decimal(10,2) DEFAULT 0.00,
  `interestedCandidates` decimal(10,2) DEFAULT 0.00,
  `notInterestedCandidates` decimal(10,2) DEFAULT 0.00,
  `teamCaptainInterviewScheduled` decimal(10,2) DEFAULT 0.00,
  `teamCaptainInterviewNoShow` decimal(10,2) DEFAULT 0.00,
  `teamCaptainOfferReleased` decimal(10,2) DEFAULT 0.00,
  `teamCaptainCandidateJoined` decimal(10,2) DEFAULT 0.00,
  `teamCaptainCandidateJoinedContract` decimal(10,2) DEFAULT 0.00,
  `sourcing` decimal(10,2) DEFAULT 0.00,
  `screening` decimal(10,2) DEFAULT 0.00,
  `evaluation` decimal(10,2) DEFAULT 0.00,
  `negotiation` decimal(10,2) DEFAULT 0.00,
  `backout` decimal(10,2) DEFAULT 0.00,
  `backoutContract` decimal(10,2) DEFAULT 0.00,
  `backoutTeam` decimal(10,2) DEFAULT 0.00,
  `backoutTeamContract` decimal(10,2) DEFAULT 0.00,
  `total_points` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consultancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consultancy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `consultancy_id` int(11) NOT NULL,
  `contact_person_name` varchar(55) NOT NULL,
  `website` varchar(55) NOT NULL,
  `commission` float(5,2) NOT NULL,
  `payout` int(11) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `feedback` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consultancy_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consultancy_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `consultancy_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_logical_path` varchar(255) NOT NULL,
  `file_physical_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_type` varchar(30) NOT NULL,
  `file_size` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversation_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversation_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `from_company_id` int(11) DEFAULT NULL,
  `to_company_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `from_company_id` int(11) DEFAULT NULL,
  `to_company_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(5) NOT NULL,
  `country_name` varchar(191) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numeric_code` char(3) DEFAULT NULL,
  `iso2` char(2) DEFAULT NULL,
  `phonecode` varchar(255) DEFAULT NULL,
  `capital` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `currency_name` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(255) DEFAULT NULL,
  `tld` varchar(255) DEFAULT NULL,
  `native` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `subregion` varchar(255) DEFAULT NULL,
  `subregion_id` int(11) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `timezones` text DEFAULT NULL,
  `translations` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `emoji` varchar(191) DEFAULT NULL,
  `emojiu` varchar(191) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikidataid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `expiry_date` date NOT NULL,
  `max_redemptions` int(10) unsigned NOT NULL,
  `times_redeemed` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(191) NOT NULL,
  `currency_symbol` varchar(191) NOT NULL,
  `currency_code` varchar(191) NOT NULL,
  `usd_based_rates` double(15,2) DEFAULT NULL,
  `inr_based_rates` double(15,2) DEFAULT NULL,
  `getKey` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currency_exchange_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency_exchange_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_currency` varchar(3) DEFAULT NULL,
  `to_currency` varchar(3) DEFAULT NULL,
  `rate` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_candidate_display_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_candidate_display_field` (
  `custom_field_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `schema_name` varchar(20) NOT NULL,
  `display_name` varchar(20) NOT NULL,
  `display_order` int(11) NOT NULL,
  `display_status` int(11) NOT NULL,
  PRIMARY KEY (`custom_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_queue_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_queue_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cv_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cv_check` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `cv_name` text NOT NULL,
  `cv_extension` text NOT NULL,
  `count_cv` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `default_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `default_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rates_fixed` double(15,2) DEFAULT NULL COMMENT 'contract',
  `rates_perecentage` double(15,2) DEFAULT NULL COMMENT 'fulltime',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `designations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `documentable_id` int(11) NOT NULL,
  `documentable_type` varchar(191) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hashname` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_count_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_count_companies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `mailBody` text DEFAULT NULL,
  `emailType` int(11) NOT NULL COMMENT '1-Mass Mail,2-Candidate mail & Forward Resume,3-Interview Remainder Mail,4-Notification Mail,5-Accounts & Offer Mail',
  `company_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purpose` text NOT NULL,
  `subject` text NOT NULL,
  `from_email_id` text NOT NULL,
  `body_description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `mailType` int(11) NOT NULL DEFAULT 1 COMMENT '1-Recruitment Notification\r\n2-Sales Notification\r\n3-Mass Mail Template\r\n4-Candidate Send Email Template\r\n5-Client Send Email Template',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `days` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0->OFF, 1-ON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `footer_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footer_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `footer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footer_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `social_links` text DEFAULT NULL,
  `footer_copyright_text` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_cms_headers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `front_cms_headers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `header_image` varchar(191) DEFAULT NULL,
  `header_background_color` varchar(191) NOT NULL,
  `header_backround_image` varchar(191) DEFAULT NULL,
  `show_login_in_menu` tinyint(1) NOT NULL,
  `show_register_in_menu` tinyint(1) NOT NULL,
  `show_login_in_header` tinyint(1) NOT NULL,
  `show_register_in_header` tinyint(1) NOT NULL,
  `custom_css` longtext DEFAULT NULL,
  `call_to_action_title` text NOT NULL,
  `call_to_action_button` enum('login','register') NOT NULL,
  `contact_text` mediumtext NOT NULL,
  `meta_details` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `login_background` varchar(191) DEFAULT NULL,
  `register_background` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_icon_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `front_icon_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `title` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `icon` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `front_image_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `front_image_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_settings_id` int(10) unsigned NOT NULL DEFAULT 18,
  `title` text NOT NULL,
  `description` mediumtext NOT NULL,
  `image` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `global_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(10) unsigned NOT NULL,
  `company_name` varchar(191) NOT NULL,
  `company_email` varchar(191) NOT NULL,
  `company_phone` varchar(191) NOT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `address` text NOT NULL,
  `website` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locale` varchar(191) NOT NULL DEFAULT 'en',
  `purchase_code` varchar(100) DEFAULT NULL,
  `supported_until` timestamp NULL DEFAULT NULL,
  `google_recaptcha_key` text DEFAULT NULL,
  `system_update` tinyint(1) NOT NULL DEFAULT 1,
  `show_review_modal` tinyint(1) NOT NULL DEFAULT 1,
  `delete_account_in` int(11) DEFAULT NULL,
  `delete_account_hour_day` enum('day','hour') NOT NULL DEFAULT 'hour',
  `bench_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-manual process, 1-auto approved',
  `terms` longtext DEFAULT NULL COMMENT 'vendor terms ',
  `offer_terms` longtext DEFAULT NULL,
  `internship_offer_terms` longtext DEFAULT NULL,
  `contract_offer_terms` longtext DEFAULT NULL,
  `agency_offer_terms` longtext DEFAULT NULL,
  `privacy_policy` longtext DEFAULT NULL,
  `terms_and_conditions` longtext DEFAULT NULL,
  `payment_policy` longtext DEFAULT NULL,
  `fraud_alery` longtext DEFAULT NULL,
  `trust_and_safety` longtext DEFAULT NULL,
  `report_issue` longtext DEFAULT NULL,
  `grievances` longtext DEFAULT NULL,
  `public_market_job_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-manual process, 1-auto approved',
  `vendor_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `google_meeting_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_meeting_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` text DEFAULT NULL,
  `meeting_link` text NOT NULL,
  `interview_date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_link` (`meeting_link`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `google_meeting_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_meeting_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_link` text NOT NULL,
  `interview_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `interview_job_wise_con_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `google_sheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_sheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `sheet_id` varchar(255) DEFAULT NULL,
  `cand_sheet_id` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `insurence_offer_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `insurence_offer_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poins` text NOT NULL,
  `Percentage` int(11) NOT NULL,
  `condition_s` int(11) NOT NULL,
  `monthly` double(15,2) DEFAULT NULL,
  `yearly` double(15,2) DEFAULT NULL,
  `offer_create_main_td_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `view_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` int(10) unsigned DEFAULT NULL,
  `application_id` int(10) unsigned NOT NULL,
  `feedback_name` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `update_by` int(11) NOT NULL,
  `available_status` int(11) DEFAULT NULL,
  `feedback_id` int(11) DEFAULT NULL,
  `star_ratings` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `interview_result` varchar(30) DEFAULT NULL,
  `overall_recommendation` varchar(30) DEFAULT NULL,
  `current_ctc` varchar(50) DEFAULT NULL,
  `expected_ctc` varchar(50) DEFAULT NULL,
  `offered_ctc` varchar(50) DEFAULT NULL,
  `max_budget` varchar(50) DEFAULT NULL,
  `notice_period` varchar(20) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `compensation_fit` varchar(40) DEFAULT NULL,
  `joining_probability` varchar(20) DEFAULT NULL,
  `risk_factors` text DEFAULT NULL,
  `compensation_comments` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_hiring_process`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_hiring_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `schedule_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_mode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_mode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_mode_lavel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_mode_lavel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `interview_mode_id` int(11) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'N',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` bigint(20) DEFAULT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `questions` text DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_schedule_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_schedule_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `interview_schedule_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_schedule_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_schedule_employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `interview_schedule_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_accept_status` enum('accept','refuse','waiting') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_schedules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `job_application_id` int(10) unsigned NOT NULL,
  `schedule_date` datetime NOT NULL,
  `status` enum('rejected','hired','pending','canceled') NOT NULL DEFAULT 'pending',
  `user_accept_status` enum('accept','refuse','waiting') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `schedule_from_time` varchar(10) DEFAULT NULL,
  `schedule_to_time` varchar(10) DEFAULT NULL,
  `reschedule` datetime DEFAULT NULL,
  `stage` varchar(55) DEFAULT NULL,
  `substage` varchar(55) DEFAULT NULL,
  `interviewers` varchar(55) DEFAULT NULL,
  `url_link` varchar(255) DEFAULT NULL,
  `other_email` varchar(255) DEFAULT NULL,
  `interview_mode` int(11) DEFAULT NULL,
  `interview_mode_lavel` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `available_status` tinyint(4) DEFAULT NULL,
  `feedback` tinyint(4) DEFAULT NULL,
  `interview_type` tinyint(4) DEFAULT NULL,
  `interview_created_by` int(11) DEFAULT NULL,
  `average_star_rating` int(11) DEFAULT NULL,
  `interview_job_id` int(11) DEFAULT NULL,
  `interview_job_wise_con_id` int(11) DEFAULT NULL,
  `mailstatus` int(11) NOT NULL DEFAULT 0,
  `email_template` int(11) NOT NULL DEFAULT 0,
  `timezone_abbreviation` varchar(11) DEFAULT NULL,
  `reminder_15_sent` tinyint(1) DEFAULT 0,
  `reminder_60_sent` tinyint(1) DEFAULT 0,
  `followup_status` varchar(20) DEFAULT NULL,
  `followup_status_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_isched_company` (`company_id`),
  KEY `idx_comp_sched_status` (`company_id`,`schedule_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_skills_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_skills_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interview_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `update_by` int(11) DEFAULT NULL,
  `candidate_status` int(11) DEFAULT NULL,
  `feedback_status` int(11) DEFAULT NULL,
  `feedback_id` int(11) DEFAULT NULL,
  `star_ratings` int(11) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `skills_exp` text DEFAULT NULL,
  `skills_ratings` int(11) DEFAULT NULL,
  `skills_remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text DEFAULT NULL,
  `classname` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_sub_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_sub_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interview_status_id` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `classname` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invitation_consultancy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invitation_consultancy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `working_user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0: pending, 1:Approved, 2: Rejected ',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invitation_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invitation_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0:request send',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(255) NOT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jd_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jd_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `jd` text DEFAULT NULL,
  `minimum_exp` double(15,2) DEFAULT NULL,
  `maxmum_exp` double(15,2) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0-Pending, 1-approved, 2-decline',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_additional_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_additional_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `detail_value` varchar(255) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_agency_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_agency_counter` (
  `job_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `bench_total` int(10) unsigned NOT NULL DEFAULT 0,
  `rec_sent` int(10) unsigned NOT NULL DEFAULT 0,
  `rec_accepted` int(10) unsigned NOT NULL DEFAULT 0,
  `rec_rejected` int(10) unsigned NOT NULL DEFAULT 0,
  `last_event_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `idx_jac_company_event` (`company_id`,`last_event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_application_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_application_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `job_application_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `answer` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_application_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_application_skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_application_id` int(11) DEFAULT NULL,
  `skills` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_jas_app` (`job_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `submitted_by` int(11) NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `thirdparty_company_id` int(11) NOT NULL DEFAULT 0,
  `full_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone_code` varchar(11) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `np_id` varchar(55) DEFAULT NULL,
  `serving_notice_period_mode` int(11) NOT NULL DEFAULT 0,
  `last_working_date` date DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `secondrary_email` varchar(55) DEFAULT NULL,
  `second_phone_code` varchar(11) DEFAULT NULL,
  `secondary_mobile` varchar(20) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `rating` enum('1','2','3','4','5') DEFAULT NULL,
  `skype_id` varchar(191) DEFAULT NULL,
  `cover_letter` mediumtext DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `column_priority` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `total_exp` text DEFAULT NULL,
  `rel_exp` text DEFAULT NULL,
  `passport` char(1) NOT NULL DEFAULT 'N',
  `ctc` int(11) DEFAULT NULL,
  `current_ctc_cur` int(11) DEFAULT 0,
  `current_ctc_mode` int(11) DEFAULT 0,
  `ectc` int(11) DEFAULT NULL,
  `current_ectc_cur` int(11) DEFAULT 0,
  `current_ectc_mode` int(11) DEFAULT 0,
  `current_location` varchar(55) DEFAULT NULL,
  `current_designation` varchar(55) DEFAULT NULL,
  `current_company` varchar(55) DEFAULT NULL,
  `process_id` int(11) DEFAULT NULL,
  `qualification` int(11) DEFAULT NULL,
  `master_status_id` int(11) DEFAULT 1,
  `child_status_id` int(11) DEFAULT NULL,
  `proposed_ctc` int(11) DEFAULT NULL,
  `any_offer` int(11) NOT NULL DEFAULT 0,
  `document` int(11) NOT NULL DEFAULT 0,
  `sub_for_approval` int(11) NOT NULL DEFAULT 0,
  `offer_approved` int(11) NOT NULL DEFAULT 0,
  `offer_release` int(11) NOT NULL DEFAULT 0,
  `offer_acceptance` int(11) NOT NULL DEFAULT 0,
  `joined` int(11) NOT NULL DEFAULT 0,
  `joined_date` datetime DEFAULT NULL,
  `duplicate` int(11) NOT NULL DEFAULT 0 COMMENT '0: Fresh Application, 1: For Duplicate Application',
  `percentage_hike` float DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `offer_currency` int(11) DEFAULT NULL,
  `offer_mode` int(11) DEFAULT NULL,
  `offer_amount` int(11) DEFAULT NULL,
  `outsourcing_type` int(11) DEFAULT NULL,
  `pay_rate_currency` int(11) DEFAULT NULL,
  `pay_rate_amount` int(11) DEFAULT NULL,
  `pay_rate_mode` int(11) DEFAULT NULL,
  `bill_rate_currency` int(11) DEFAULT NULL,
  `bill_rate_amount` int(11) DEFAULT NULL,
  `bill_rate_mode` int(11) DEFAULT NULL,
  `comission_type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `employee_number` varchar(191) DEFAULT NULL,
  `condidate_type` enum('bench_condidate','other') NOT NULL DEFAULT 'other',
  `ctc_hourly` double(15,2) DEFAULT NULL,
  `ctc_monthly` double(15,2) DEFAULT NULL,
  `ctc_yearly` double(15,2) DEFAULT NULL,
  `ectc_hourly` double(15,2) DEFAULT NULL,
  `ectc_monthly` double(15,2) DEFAULT NULL,
  `ectc_yearly` double(15,2) DEFAULT NULL,
  `superadminstatus` int(11) NOT NULL DEFAULT 0 COMMENT '0-pending,1-approved,2-reject',
  `countforadd` int(11) NOT NULL DEFAULT 0,
  `notice_comments` text DEFAULT NULL,
  `ads_switch` enum('Yes','No') NOT NULL DEFAULT 'No',
  `linkedin` text DEFAULT NULL,
  `github` text DEFAULT NULL,
  `behance` text DEFAULT NULL,
  `otherLink` text DEFAULT NULL,
  `interviewDateOne` datetime DEFAULT NULL,
  `interview_mode_one` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `interviewDateTwo` datetime DEFAULT NULL,
  `interview_mode_two` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `interviewDateThree` datetime DEFAULT NULL,
  `interview_mode_three` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `interviewDateFour` datetime DEFAULT NULL,
  `interview_mode_four` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `interviewDateFive` datetime DEFAULT NULL,
  `interview_mode_five` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `interviewDateSix` datetime DEFAULT NULL,
  `interview_mode_six` enum('Telecon','Video','Face to Face') DEFAULT NULL,
  `status_type` enum('JERO','DIRECT') DEFAULT NULL,
  `careersummary` text DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `wm` enum('remote','hybrid','office') DEFAULT NULL,
  `employmentType` varchar(255) DEFAULT NULL,
  `oh` enum('yes','no') DEFAULT NULL,
  `visastatu` text DEFAULT NULL,
  `referencename` text DEFAULT NULL,
  `recruiter_note` mediumtext DEFAULT NULL,
  `reason_for_job_change` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_job_app_count` (`job_id`,`master_status_id`,`deleted_at`),
  KEY `idx_job_app_company` (`company_id`,`master_status_id`),
  KEY `idx_job_app_created` (`job_id`,`created_at`),
  KEY `idx_job_app_deleted` (`deleted_at`),
  KEY `idx_japp_status` (`job_id`,`master_status_id`),
  KEY `idx_japp_email` (`company_id`,`email`(40)),
  KEY `idx_japp_phone` (`company_id`,`phone`(20)),
  KEY `idx_thirdparty_created` (`thirdparty_company_id`,`created_at`),
  KEY `idx_company_created` (`company_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_assign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `spok_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_job_assign_job_user` (`job_id`,`user_id`),
  KEY `idx_job_assign_job_company` (`job_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_counters` (
  `company_id` int(10) unsigned NOT NULL,
  `total_jobs` int(10) unsigned NOT NULL DEFAULT 0,
  `hiring_jobs` int(10) unsigned NOT NULL DEFAULT 0,
  `on_hold_jobs` int(10) unsigned NOT NULL DEFAULT 0,
  `closed_jobs` int(10) unsigned NOT NULL DEFAULT 0,
  `removed_jobs` int(10) unsigned NOT NULL DEFAULT 0,
  `last_job_id` int(10) unsigned NOT NULL DEFAULT 0,
  `last_event_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  KEY `idx_jc_last_event` (`last_event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_job_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_job_application` (
  `job_application_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `location` varchar(191) NOT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_metronics_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_metronics_skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `skill_name` varchar(55) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_qualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_qualifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `qualification_id` int(11) DEFAULT NULL,
  `qualification_name` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `skill_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_status_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `from_status` varchar(20) DEFAULT NULL,
  `to_status` varchar(20) NOT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `job_status_logs_job_id_index` (`job_id`),
  KEY `job_status_logs_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `job_description` mediumtext NOT NULL,
  `job_requirement` mediumtext NOT NULL,
  `total_positions` int(11) NOT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','hold','inactive') DEFAULT NULL,
  `required_columns` text NOT NULL,
  `meta_details` text NOT NULL,
  `section_visibility` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `client_id` int(11) DEFAULT NULL,
  `min_exp` int(11) NOT NULL DEFAULT 0,
  `max_exp` int(11) NOT NULL DEFAULT 0,
  `min_salary` int(11) NOT NULL DEFAULT 0,
  `max_salary` int(11) NOT NULL DEFAULT 0,
  `qualification` int(11) DEFAULT NULL,
  `employment_type` int(11) NOT NULL DEFAULT 0,
  `min_salary_currency` int(11) DEFAULT NULL,
  `max_salary_currency` int(11) DEFAULT NULL,
  `min_sal_mode` int(11) DEFAULT NULL,
  `max_sal_mode` int(11) DEFAULT NULL,
  `carrier_website` int(11) DEFAULT NULL,
  `work_type` varchar(50) NOT NULL DEFAULT 'no',
  `show_vendor` varchar(50) NOT NULL DEFAULT 'no',
  `closeType` int(11) NOT NULL DEFAULT 0 COMMENT '0->vendor close, 1-auto close',
  `min_yearly` double(15,2) DEFAULT 0.00,
  `min_monthly` double(15,2) DEFAULT 0.00,
  `min_hourly` double(15,2) DEFAULT 0.00,
  `max_yearly` double(15,2) DEFAULT 0.00,
  `max_monthly` double(15,2) DEFAULT 0.00,
  `max_hourly` double(15,2) DEFAULT 0.00,
  `auto_date_job` date DEFAULT NULL,
  `public_market_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-All Jobs, 1-public market Pending, 2- Approved, 3- rejected 4-Vendor Limit',
  `work_shifts` enum('Day Shift','Night Shift','Rotational shift','UK Timezone','US Timezone') NOT NULL DEFAULT 'Day Shift',
  PRIMARY KEY (`id`),
  KEY `idx_jobs_company_status_created` (`company_id`,`status`,`created_at`),
  KEY `idx_jobs_priority_created` (`priority`,`created_at`),
  KEY `idx_comp_status_created` (`company_id`,`status`,`created_at`),
  FULLTEXT KEY `ft_jobs_title_desc` (`title`,`job_description`,`job_requirement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `join_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `join_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `master_status_id` int(11) NOT NULL,
  `jobApplication` int(11) NOT NULL,
  `addJobWiseCondidate` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `child_status_id` int(11) DEFAULT NULL,
  `offer_amount` int(11) NOT NULL DEFAULT 0,
  `joined_date` datetime DEFAULT NULL,
  `offer_currency` int(11) NOT NULL DEFAULT 0,
  `offer_mode` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `language_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_code` varchar(191) NOT NULL,
  `language_name` varchar(191) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `link_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `link_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `coins` int(11) DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `update_coins` double DEFAULT NULL,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `coin_order_id` int(11) DEFAULT NULL,
  `subscription_type` enum('yes','no') DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `payment_orderId` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_link_id` text DEFAULT NULL,
  `payment_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `linked_in_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linked_in_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(191) DEFAULT NULL,
  `client_secret` varchar(191) DEFAULT NULL,
  `status` enum('enable','disable') NOT NULL DEFAULT 'disable',
  `callback_url` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ltm_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ltm_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT 0,
  `locale` varchar(191) NOT NULL,
  `group` varchar(191) NOT NULL,
  `key` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mail_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `notification_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `market_job_added_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_job_added_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0-new added, 1-old added ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(100) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `symbol` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_date_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_date_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `code` varchar(55) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_email_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_email_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `sort_desc` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(49) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `iso_639-1` char(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notifications_name` varchar(255) NOT NULL,
  `notifications_email` int(11) NOT NULL DEFAULT 1,
  `notifications_phone` int(11) NOT NULL DEFAULT 1,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `email` text DEFAULT NULL,
  `mobile` text DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `css_style` varchar(50) NOT NULL,
  `text_name` varchar(55) NOT NULL,
  `divname` varchar(55) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `direct_client` int(11) NOT NULL DEFAULT 0,
  `direct_client_priority` int(11) NOT NULL,
  `custom_client` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_time_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_time_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) DEFAULT NULL,
  `code` varchar(55) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_timezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_timezone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(12) DEFAULT NULL,
  `country_name` varchar(44) DEFAULT NULL,
  `time_zone` varchar(30) DEFAULT NULL,
  `gmt_offse` varchar(10) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `timezone_abbreviation` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meeting_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_schedule_id` int(11) NOT NULL,
  `comment_by` int(11) NOT NULL,
  `comments` text NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `meeting_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_date` datetime NOT NULL,
  `interview_type` varchar(191) NOT NULL,
  `interview_mode` bigint(20) unsigned NOT NULL,
  `interview_mode_level` bigint(20) unsigned DEFAULT NULL,
  `from_company_id` bigint(20) unsigned NOT NULL,
  `to_company_id` bigint(20) unsigned NOT NULL,
  `url_address` varchar(191) DEFAULT NULL,
  `company_team` text DEFAULT NULL,
  `our_team` text DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notice_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notice_periods` (
  `np_id` int(11) NOT NULL AUTO_INCREMENT,
  `np_title` varchar(55) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`np_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(36) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_create_main_tds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_create_main_tds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `employment_type` int(11) NOT NULL DEFAULT 0,
  `currency` int(11) DEFAULT NULL,
  `salary_ctc` double(15,2) DEFAULT NULL,
  `salary_mode` int(11) NOT NULL,
  `designation` text NOT NULL,
  `joined_date` datetime DEFAULT NULL,
  `joined` int(11) DEFAULT NULL,
  `monthly_gross` double(15,2) DEFAULT NULL,
  `yearly_gross` double(15,2) DEFAULT NULL,
  `monthly_bounus_gross` double(15,2) DEFAULT NULL,
  `yearly_bonus_gross` double(15,2) DEFAULT NULL,
  `monthly_insurence_gross` double(15,2) DEFAULT NULL,
  `yearly_insurence_gross` double(15,2) DEFAULT NULL,
  `yearly_final_gross` double(15,2) DEFAULT NULL,
  `monthly_final_gross` double(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `candidate_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-created, 1-release,2-accept,3-reject,4-expired',
  `management_status` int(11) NOT NULL DEFAULT 0 COMMENT '1-approved, 2-not approved, 3-In process',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-new, 2-old, 3-join',
  `accept_date_time` datetime DEFAULT NULL,
  `signature_candidate` text DEFAULT NULL,
  `signature_type` int(11) NOT NULL DEFAULT 0,
  `approved_date_time` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_approved_date_time` datetime DEFAULT NULL,
  `send_accept_date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_job_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_job_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(11) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `np_id` varchar(55) DEFAULT NULL,
  `full_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `secondrary_email` varchar(55) DEFAULT NULL,
  `secondary_mobile` varchar(20) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `photo` varchar(191) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `rating` enum('1','2','3','4','5') DEFAULT NULL,
  `skype_id` varchar(191) DEFAULT NULL,
  `cover_letter` mediumtext DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned DEFAULT NULL,
  `is_viewed` tinyint(1) DEFAULT NULL,
  `column_priority` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `total_exp` tinyint(4) DEFAULT NULL,
  `rel_exp` tinyint(4) DEFAULT NULL,
  `passport` char(1) NOT NULL DEFAULT 'N',
  `ctc` int(11) DEFAULT NULL,
  `ectc` int(11) DEFAULT NULL,
  `current_location` varchar(55) DEFAULT NULL,
  `current_designation` varchar(55) DEFAULT NULL,
  `current_company` varchar(55) DEFAULT NULL,
  `process_id` int(11) DEFAULT NULL,
  `qualification` int(11) DEFAULT NULL,
  `master_status_id` int(11) DEFAULT 1,
  `child_status_id` int(11) DEFAULT NULL,
  `serving_notice_period_mode` int(11) NOT NULL DEFAULT 0,
  `proposed_ctc` int(11) DEFAULT NULL,
  `any_offer` int(11) NOT NULL DEFAULT 0,
  `document` int(11) NOT NULL DEFAULT 0,
  `sub_for_approval` int(11) NOT NULL DEFAULT 0,
  `offer_approved` int(11) NOT NULL DEFAULT 0,
  `offer_release` int(11) NOT NULL DEFAULT 0,
  `offer_acceptance` int(11) NOT NULL DEFAULT 0,
  `joined` int(11) NOT NULL DEFAULT 0,
  `joined_date` datetime DEFAULT NULL,
  `duplicate` int(11) NOT NULL DEFAULT 0 COMMENT '0: Fresh Application, 1: For Duplicate Application',
  `offer_amount` int(11) DEFAULT NULL,
  `offer_currency` int(11) DEFAULT NULL,
  `offer_mode` int(11) DEFAULT NULL,
  `confidential` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comp_created_status` (`company_id`,`created_at`,`master_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_point_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_point_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` text DEFAULT NULL,
  `sorting` int(11) DEFAULT NULL,
  `percentage` double(15,2) DEFAULT NULL,
  `view_type` int(11) DEFAULT NULL COMMENT '0-Yearly, 1-Monthly, 2-Both',
  `condition_check` int(11) DEFAULT NULL COMMENT '0-CTC, 1-Basic',
  `section_type` int(11) NOT NULL DEFAULT 0 COMMENT '0-components, 1- other components',
  `company_id` int(11) DEFAULT NULL,
  `deleteded` int(11) DEFAULT 1,
  `section_divided` int(11) NOT NULL DEFAULT 0,
  `show_default` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` text DEFAULT NULL,
  `percentage` double(15,2) NOT NULL DEFAULT 0.00,
  `view_type` int(11) DEFAULT NULL COMMENT '0-Yearly, 1-Monthly, 2-Both',
  `condition_check` int(11) DEFAULT NULL COMMENT '0-CTC, 1-Basic',
  `section_type` int(11) NOT NULL DEFAULT 0 COMMENT ' 0-components, 1- other components',
  `sorting` int(11) NOT NULL DEFAULT 0,
  `deleteded` int(11) NOT NULL DEFAULT 1,
  `section_divided` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offer_terms_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer_terms_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `terms_condition` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `old_city_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_city_list` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `pincode` int(11) NOT NULL,
  `latitude` varchar(11) NOT NULL,
  `longitude` varchar(11) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'city.jpg',
  `top_ten_display_order` int(11) NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `old_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(5) NOT NULL,
  `country_name` varchar(191) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `old_state_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_state_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(50) NOT NULL,
  `image` varchar(55) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `on_board_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `on_board_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `job_application_id` int(10) unsigned DEFAULT NULL,
  `designation_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `reports_to_id` int(10) unsigned DEFAULT NULL,
  `salary_offered` int(11) NOT NULL,
  `joining_date` date NOT NULL,
  `accept_last_date` date NOT NULL,
  `offer_code` varchar(20) DEFAULT NULL,
  `sign` varchar(191) DEFAULT NULL,
  `reject_reason` varchar(191) DEFAULT NULL,
  `hired_status` enum('offered','accepted','rejected','canceled') NOT NULL,
  `employee_status` enum('part_time','full_time','on_contract') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `on_board_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `on_board_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `on_board_detail_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `file` varchar(191) NOT NULL,
  `hash_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_master_id` int(11) NOT NULL,
  `package_name` varchar(55) DEFAULT NULL,
  `no_of_jobs` int(11) NOT NULL,
  `no_of_response` int(11) NOT NULL,
  `package_price` double(15,2) NOT NULL,
  `package_discount` float(7,2) NOT NULL,
  `discounted_price` float(7,2) NOT NULL,
  `valid_upto` int(11) NOT NULL COMMENT 'In Days',
  `display_order` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_active`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_active` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_invoice_id` int(11) DEFAULT 0,
  `company_id` int(11) DEFAULT NULL,
  `company_type` int(11) DEFAULT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int(11) DEFAULT NULL,
  `cvaccess_quantity` int(11) DEFAULT NULL,
  `cvaccess_used` int(11) DEFAULT NULL,
  `cvaccess_balance` int(11) DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int(11) DEFAULT NULL,
  `users_quantity` int(11) DEFAULT NULL,
  `users_used` int(11) DEFAULT NULL,
  `users_balance` int(11) DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_enddate` date DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_status` int(11) DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_quantity` int(11) DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_used` int(11) DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newclientrequest_balance` int(11) DEFAULT NULL COMMENT 'CONTRACT_RECRUITMENT_LEAD',
  `newjobrequest_startdate` date DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_enddate` date DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_status` int(11) DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_quantity` int(11) DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_used` int(11) DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newjobrequest_balance` int(11) DEFAULT NULL COMMENT 'FULLTIME_RECRUITMENT_LEAD',
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int(11) DEFAULT NULL,
  `newbenchclientrequest_quantity` int(11) DEFAULT NULL,
  `newbenchclientrequest_used` int(11) DEFAULT NULL,
  `newbenchclientrequest_balance` int(11) DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int(11) DEFAULT NULL,
  `emails_quantity` int(11) DEFAULT NULL,
  `emails_used` int(11) DEFAULT NULL,
  `emails_balance` int(11) DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int(11) DEFAULT NULL,
  `oldcvaccess_quantity` int(11) DEFAULT NULL,
  `oldcvaccess_used` int(11) DEFAULT NULL,
  `oldcvaccess_balance` int(11) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int(11) DEFAULT 0,
  `boostkeywords_text` varchar(255) DEFAULT NULL,
  `boostkeywords_quantity` int(11) DEFAULT NULL,
  `boostkeywords_used` int(11) DEFAULT NULL,
  `boostkeywords_balance` int(11) DEFAULT NULL,
  `internaldatabaseaccess_status` int(11) DEFAULT NULL,
  `internaldatabaseaccess_quantity` int(11) DEFAULT NULL,
  `internaldatabaseaccess_used` int(11) DEFAULT NULL,
  `internaldatabaseaccess_balance` int(11) DEFAULT NULL,
  `interviewtracker_status` int(11) DEFAULT NULL,
  `interviewtracker_quantity` int(11) DEFAULT NULL,
  `interviewtracker_used` int(11) DEFAULT NULL,
  `interviewtracker_balance` int(11) DEFAULT NULL,
  `offertracker_status` int(11) DEFAULT NULL,
  `offertracker_quantity` int(11) DEFAULT NULL,
  `offertracker_used` int(11) DEFAULT NULL,
  `offertracker_balance` int(11) DEFAULT NULL,
  `joiningtracker_status` int(11) DEFAULT NULL,
  `joiningtracker_quantity` int(11) DEFAULT NULL,
  `joiningtracker_used` int(11) DEFAULT NULL,
  `joiningtracker_balance` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_status` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_used` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_balance` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_used` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_balance` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_used` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_balance` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_used` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_balance` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_used` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_balance` int(11) DEFAULT NULL,
  `BenchSalesIndia_status` int(11) DEFAULT NULL,
  `BenchSalesIndia_quantity` int(11) DEFAULT NULL,
  `BenchSalesIndia_used` int(11) DEFAULT NULL,
  `BenchSalesIndia_balance` int(11) DEFAULT NULL,
  `BenchSalesInternational_status` int(11) DEFAULT NULL,
  `BenchSalesInternational_quantity` int(11) DEFAULT NULL,
  `BenchSalesInternational_used` int(11) DEFAULT NULL,
  `BenchSalesInternational_balance` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_status` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_used` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_balance` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_status` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_used` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_balance` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_status` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_used` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_balance` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_status` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_used` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_balance` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_used` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_balance` int(11) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int(11) DEFAULT 0 COMMENT '0-inactive,1-active',
  `payment_status` int(11) DEFAULT 0 COMMENT '0-pending,1-paid',
  `package_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_default`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cvaccess_quantity` int(11) DEFAULT NULL,
  `cvaccess_validity` int(11) DEFAULT NULL,
  `cvaccess_status` int(11) DEFAULT NULL,
  `users_quantity` int(11) DEFAULT NULL,
  `users_validity` int(11) DEFAULT NULL,
  `users_status` int(11) DEFAULT NULL,
  `newclientrequest_quantity` int(11) DEFAULT NULL,
  `newclientrequest_validity` int(11) DEFAULT NULL,
  `newclientrequest_status` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `newbenchclientrequest_quantity` int(11) DEFAULT NULL,
  `newbenchclientrequest_validity` int(11) DEFAULT NULL,
  `newbenchclientrequest_status` int(11) DEFAULT NULL,
  `newjobrequest_quantity` int(11) DEFAULT NULL,
  `newjobrequest_validity` int(11) DEFAULT NULL,
  `newjobrequest_status` int(11) DEFAULT NULL,
  `emails_quantity` int(11) DEFAULT NULL,
  `emails_validity` int(11) DEFAULT NULL,
  `emails_status` int(11) DEFAULT NULL,
  `oldcvaccess_quantity` int(11) DEFAULT NULL,
  `oldcvaccess_validity` int(11) DEFAULT NULL,
  `oldcvaccess_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) DEFAULT NULL,
  `package_feature_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `sub_total` double(15,2) DEFAULT NULL,
  `status` enum('1','0') DEFAULT NULL COMMENT '''Active'',''Inactive''',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `features_type` enum('1','2') NOT NULL COMMENT '1-Operations,2-Sales',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-active, 0-Inactive',
  `field_name` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(255) DEFAULT NULL,
  `package_order_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `company_type` int(11) NOT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int(11) DEFAULT NULL,
  `cvaccess_quantity` int(11) DEFAULT NULL,
  `cvaccess_price` double(15,2) DEFAULT NULL,
  `cvaccess_totalprice` double(15,2) DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int(11) DEFAULT NULL,
  `users_quantity` int(11) DEFAULT NULL,
  `users_price` double(15,2) DEFAULT NULL,
  `users_totalprice` double(15,2) DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL,
  `newclientrequest_enddate` date DEFAULT NULL,
  `newclientrequest_status` int(11) DEFAULT NULL,
  `newclientrequest_quantity` int(11) DEFAULT NULL,
  `newclientrequest_price` double(15,2) DEFAULT NULL,
  `newclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `newjobrequest_startdate` date DEFAULT NULL,
  `newjobrequest_enddate` date DEFAULT NULL,
  `newjobrequest_status` int(11) DEFAULT NULL,
  `newjobrequest_quantity` int(11) DEFAULT NULL,
  `newjobrequest_price` double(15,2) DEFAULT NULL,
  `newjobrequest_totalprice` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int(11) DEFAULT NULL,
  `newbenchclientrequest_quantity` int(11) DEFAULT NULL,
  `newbenchclientrequest_price` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int(11) DEFAULT NULL,
  `emails_quantity` int(11) DEFAULT NULL,
  `emails_price` double(15,2) DEFAULT NULL,
  `emails_totalprice` double(15,2) DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int(11) DEFAULT NULL,
  `oldcvaccess_quantity` int(11) DEFAULT NULL,
  `oldcvaccess_price` double(15,2) DEFAULT NULL,
  `oldcvaccess_totalprice` double(15,2) DEFAULT NULL,
  `subtotal` double(15,2) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `payment_mode` varchar(20) DEFAULT NULL,
  `grand_total` double(15,2) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int(11) DEFAULT NULL,
  `boostkeywords_price` double(15,2) DEFAULT NULL,
  `boostkeywords_totalprice` double(15,2) DEFAULT NULL,
  `boostkeywords_text` varchar(255) DEFAULT NULL,
  `boostkeywords_quantity` int(11) DEFAULT NULL,
  `internaldatabaseaccess_status` int(11) DEFAULT NULL,
  `internaldatabaseaccess_quantity` int(11) DEFAULT NULL,
  `internaldatabaseaccess_price` double(15,2) DEFAULT NULL,
  `internaldatabaseaccess_totalprice` double(15,2) DEFAULT NULL,
  `interviewtracker_status` int(11) DEFAULT NULL,
  `interviewtracker_quantity` int(11) DEFAULT NULL,
  `interviewtracker_price` double(15,2) DEFAULT NULL,
  `interviewtracker_totalprice` double(15,2) DEFAULT NULL,
  `offertracker_status` int(11) DEFAULT NULL,
  `offertracker_quantity` int(11) DEFAULT NULL,
  `offertracker_price` double(15,2) DEFAULT NULL,
  `offertracker_totalprice` double(15,2) DEFAULT NULL,
  `joiningtracker_status` int(11) DEFAULT NULL,
  `joiningtracker_quantity` int(11) DEFAULT NULL,
  `joiningtracker_price` double(15,2) NOT NULL,
  `joiningtracker_totalprice` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_status` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_price` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_status` int(11) DEFAULT NULL,
  `BenchSalesIndia_quantity` int(11) DEFAULT NULL,
  `BenchSalesIndia_price` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_status` int(11) DEFAULT NULL,
  `BenchSalesInternational_quantity` int(11) DEFAULT NULL,
  `BenchSalesInternational_price` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_status` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_price` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_status` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_price` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_status` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_status` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_totalprice` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_price` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_totalprice` double(15,2) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int(11) DEFAULT 0 COMMENT '0-inactive,1-active',
  `payment_status` int(11) DEFAULT 0 COMMENT '0-pending,1-paid',
  `package_id` int(11) DEFAULT NULL,
  `invoicePrimaryId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_invoice_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_invoice_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_invoice_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_invoice_tax` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) DEFAULT NULL,
  `tax_name` varchar(55) DEFAULT NULL,
  `tax_percentage` int(11) DEFAULT NULL,
  `tax_amount` float(10,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_title` varchar(55) DEFAULT NULL,
  `mp_job_title` varchar(55) NOT NULL,
  `mp_job_response` varchar(55) NOT NULL,
  `mp_response_price` int(11) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_master_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_master_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(55) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `company_type` int(11) NOT NULL,
  `cvaccess_startdate` date DEFAULT NULL,
  `cvaccess_enddate` date DEFAULT NULL,
  `cvaccess_status` int(11) DEFAULT NULL,
  `cvaccess_quantity` int(11) DEFAULT NULL,
  `cvaccess_price` double(15,2) DEFAULT NULL,
  `cvaccess_totalprice` double(15,2) DEFAULT NULL,
  `users_startdate` date DEFAULT NULL,
  `users_enddate` date DEFAULT NULL,
  `users_status` int(11) DEFAULT NULL,
  `users_quantity` int(11) DEFAULT NULL,
  `users_price` double(15,2) DEFAULT NULL,
  `users_totalprice` double(15,2) DEFAULT NULL,
  `newclientrequest_startdate` date DEFAULT NULL,
  `newclientrequest_enddate` date DEFAULT NULL,
  `newclientrequest_status` int(11) DEFAULT NULL,
  `newclientrequest_quantity` int(11) DEFAULT NULL,
  `newclientrequest_price` double(15,2) DEFAULT NULL,
  `newclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `newjobrequest_startdate` date DEFAULT NULL,
  `newjobrequest_enddate` date DEFAULT NULL,
  `newjobrequest_status` int(11) DEFAULT NULL,
  `newjobrequest_quantity` int(11) DEFAULT NULL,
  `newjobrequest_price` double(15,2) DEFAULT NULL,
  `newjobrequest_totalprice` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_startdate` date DEFAULT NULL,
  `newbenchclientrequest_enddate` date DEFAULT NULL,
  `newbenchclientrequest_status` int(11) DEFAULT NULL,
  `newbenchclientrequest_quantity` int(11) DEFAULT NULL,
  `newbenchclientrequest_price` double(15,2) DEFAULT NULL,
  `newbenchclientrequest_totalprice` double(15,2) DEFAULT NULL,
  `emails_startdate` date DEFAULT NULL,
  `emails_enddate` date DEFAULT NULL,
  `emails_status` int(11) DEFAULT NULL,
  `emails_quantity` int(11) DEFAULT NULL,
  `emails_price` double(15,2) DEFAULT NULL,
  `emails_totalprice` double(15,2) DEFAULT NULL,
  `oldcvaccess_startdate` date DEFAULT NULL,
  `oldcvaccess_enddate` date DEFAULT NULL,
  `oldcvaccess_status` int(11) DEFAULT NULL,
  `oldcvaccess_quantity` int(11) DEFAULT NULL,
  `oldcvaccess_price` double(15,2) DEFAULT NULL,
  `oldcvaccess_totalprice` double(15,2) DEFAULT NULL,
  `order_status` int(11) NOT NULL DEFAULT 0,
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime NOT NULL DEFAULT current_timestamp(),
  `boostkeywords_startdate` date DEFAULT NULL,
  `boostkeywords_enddate` date DEFAULT NULL,
  `boostkeywords_status` int(11) DEFAULT NULL,
  `boostkeywords_price` double(15,2) DEFAULT NULL,
  `boostkeywords_totalprice` double(15,2) DEFAULT NULL,
  `boostkeywords_text` varchar(255) DEFAULT NULL,
  `boostkeywords_quantity` int(11) DEFAULT NULL,
  `internaldatabaseaccess_status` int(11) DEFAULT NULL,
  `internaldatabaseaccess_quantity` int(11) DEFAULT NULL,
  `internaldatabaseaccess_price` double(15,2) DEFAULT NULL,
  `internaldatabaseaccess_totalprice` double(15,2) DEFAULT NULL,
  `interviewtracker_status` int(11) DEFAULT NULL,
  `interviewtracker_quantity` int(11) DEFAULT NULL,
  `interviewtracker_price` double(15,2) DEFAULT NULL,
  `interviewtracker_totalprice` double(15,2) DEFAULT NULL,
  `offertracker_status` int(11) DEFAULT NULL,
  `offertracker_quantity` int(11) DEFAULT NULL,
  `offertracker_price` double(15,2) DEFAULT NULL,
  `offertracker_totalprice` double(15,2) DEFAULT NULL,
  `joiningtracker_status` int(11) DEFAULT NULL,
  `joiningtracker_quantity` int(11) DEFAULT NULL,
  `joiningtracker_price` double(15,2) DEFAULT NULL,
  `joiningtracker_totalprice` double(15,2) NOT NULL,
  `PermissionforSHaringCVtoclienys_status` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_quantity` int(11) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_price` double(15,2) DEFAULT NULL,
  `PermissionforSHaringCVtoclienys_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `PermamentRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadIndia_totalprice` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_status` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_quantity` int(11) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_price` double(15,2) DEFAULT NULL,
  `ContractRecruitmentLeadInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_status` int(11) DEFAULT NULL,
  `BenchSalesIndia_quantity` int(11) DEFAULT NULL,
  `BenchSalesIndia_price` double(15,2) DEFAULT NULL,
  `BenchSalesIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_status` int(11) DEFAULT NULL,
  `BenchSalesInternational_quantity` int(11) DEFAULT NULL,
  `BenchSalesInternational_price` double(15,2) DEFAULT NULL,
  `BenchSalesInternational_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_status` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_quantity` int(11) DEFAULT NULL,
  `BenchClientAcccessIndia_price` double(15,2) DEFAULT NULL,
  `BenchClientAcccessIndia_totalprice` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_status` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_quantity` int(11) DEFAULT NULL,
  `BenchClientAccessInternational_price` double(15,2) DEFAULT NULL,
  `BenchClientAccessInternational_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_status` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsIndia_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsIndia_totalprice` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_status` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_quantity` int(11) DEFAULT NULL,
  `BoostKeywordsInternational_price` double(15,2) DEFAULT NULL,
  `BoostKeywordsInternational_totalprice` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_status` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_quantity` int(11) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_price` double(15,2) DEFAULT NULL,
  `Limitperdayforleadsconsumptions_totalprice` double(15,2) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `packages_status` int(11) DEFAULT 0 COMMENT '0-inactive,1-active',
  `payment_status` int(11) DEFAULT 0 COMMENT '0-pending,1-paid',
  `package_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `package_order_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_invoice_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `package_master_item` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_name` varchar(55) DEFAULT NULL,
  `event_inserted_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `monthly_price` double(8,2) DEFAULT NULL COMMENT 'unuse',
  `annual_price` double(8,2) DEFAULT NULL COMMENT 'unuse',
  `no_of_job_openings` int(11) DEFAULT NULL COMMENT 'unuse',
  `no_of_candidate_access` int(11) DEFAULT NULL COMMENT 'unuse',
  `career_website` tinyint(1) DEFAULT NULL COMMENT 'unuse',
  `multiple_roles` tinyint(1) DEFAULT NULL COMMENT 'unuse',
  `recommended` tinyint(1) DEFAULT NULL,
  `is_trial` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL,
  `trial_duration` int(11) NOT NULL COMMENT 'package all duration',
  `stripe_annual_plan_id` varchar(255) DEFAULT NULL COMMENT 'unuse',
  `razorpay_annual_plan_id` varchar(191) DEFAULT NULL COMMENT 'unuse',
  `razorpay_monthly_plan_id` varchar(191) DEFAULT NULL COMMENT 'unuse',
  `stripe_monthly_plan_id` varchar(255) DEFAULT NULL COMMENT 'unuse',
  `billing_cycle` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'unuse',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `sub_total` double(15,2) DEFAULT NULL,
  `amount_total_paid` double(15,2) DEFAULT NULL,
  `custome_pck` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `packages_is_trial_status` (`is_trial`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paypal_client_id` varchar(191) DEFAULT NULL,
  `paypal_secret` varchar(191) DEFAULT NULL,
  `api_key` varchar(191) DEFAULT NULL,
  `api_secret` varchar(191) DEFAULT NULL,
  `webhook_key` varchar(191) DEFAULT NULL,
  `paypal_status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `stripe_status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `razorpay_key` varchar(191) DEFAULT NULL,
  `razorpay_secret` varchar(191) DEFAULT NULL,
  `razorpay_webhook_secret` varchar(191) DEFAULT NULL,
  `razorpay_status` enum('active','deactive') NOT NULL DEFAULT 'deactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `paypal_mode` enum('sandbox','live') NOT NULL DEFAULT 'sandbox',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paypal_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paypal_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `currency_id` int(10) unsigned DEFAULT NULL,
  `package_id` int(10) unsigned DEFAULT NULL,
  `sub_total` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `remarks` varchar(191) DEFAULT NULL,
  `billing_frequency` varchar(191) DEFAULT NULL,
  `billing_interval` int(11) DEFAULT NULL,
  `paid_on` datetime DEFAULT NULL,
  `next_pay_date` datetime DEFAULT NULL,
  `recurring` enum('yes','no') DEFAULT 'no',
  `status` enum('paid','unpaid','pending') DEFAULT 'pending',
  `plan_id` varchar(191) DEFAULT NULL,
  `event_id` varchar(191) DEFAULT NULL,
  `end_on` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(255) DEFAULT 'web',
  `group_name` varchar(255) DEFAULT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `product_based` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `product_based` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pipeline_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pipeline_counters` (
  `job_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `sourcing` int(10) unsigned NOT NULL DEFAULT 0,
  `screening` int(10) unsigned NOT NULL DEFAULT 0,
  `evaluation` int(10) unsigned NOT NULL DEFAULT 0,
  `negotiation` int(10) unsigned NOT NULL DEFAULT 0,
  `offered` int(10) unsigned NOT NULL DEFAULT 0,
  `joined` int(10) unsigned NOT NULL DEFAULT 0,
  `rejected` int(10) unsigned NOT NULL DEFAULT 0,
  `fresh_data` tinyint(4) NOT NULL DEFAULT 1,
  `last_event_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `idx_pc_job_company` (`job_id`,`company_id`),
  KEY `idx_pc_fresh` (`fresh_data`,`last_event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pre_fixed_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pre_fixed_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pre_fixed` varchar(255) NOT NULL,
  `type` enum('invoice','other') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qualification_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualification_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `display_order` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `question` varchar(191) NOT NULL,
  `status` enum('enable','disable') NOT NULL DEFAULT 'enable',
  `required` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `range_display`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `range_display` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lower_limit` int(10) unsigned DEFAULT NULL,
  `upper_limit` int(11) NOT NULL,
  `display_text` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `razorpay_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `razorpay_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `invoice_id` varchar(191) NOT NULL,
  `subscription_id` varchar(191) NOT NULL,
  `order_id` varchar(191) DEFAULT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `transaction_id` varchar(191) NOT NULL,
  `amount` decimal(12,2) unsigned NOT NULL,
  `pay_date` date NOT NULL,
  `next_pay_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `razorpay_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `razorpay_subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `subscription_id` varchar(191) DEFAULT NULL,
  `customer_id` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `razorpay_id` varchar(191) NOT NULL,
  `razorpay_plan` varchar(191) NOT NULL,
  `quantity` int(11) NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rec_candidate_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rec_candidate_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `source_from_company_id` int(11) DEFAULT NULL,
  `source_from_user_id` int(11) DEFAULT NULL,
  `source_from_id` int(11) DEFAULT NULL,
  `source_from_type` int(11) NOT NULL DEFAULT 0 COMMENT '0 for applicant, 1 for bench, 2 : For Public Bench',
  `source_company_id` int(11) DEFAULT NULL,
  `source_user_id` int(11) DEFAULT NULL,
  `source_application_id` int(11) DEFAULT NULL,
  `source_jobid` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ac_status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recommendation_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recommendation_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_company_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL DEFAULT 0,
  `from_status` int(11) DEFAULT 0,
  `from_created_at` timestamp NULL DEFAULT current_timestamp(),
  `to_company_id` int(11) DEFAULT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `to_status` int(11) DEFAULT NULL,
  `to_created_at` timestamp NULL DEFAULT NULL,
  `final_status` int(11) DEFAULT 0,
  `conversation_id` int(11) DEFAULT NULL,
  `add_status` int(11) NOT NULL DEFAULT 0 COMMENT '0-recommendation, 1-market_in_added',
  `accepterms` blob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rec_from_to` (`from_company_id`,`to_company_id`,`final_status`),
  KEY `idx_rec_to_company_final` (`to_company_id`,`final_status`),
  KEY `idx_rec_job_user` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recruiter_note_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recruiter_note_master` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rnm_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(255) DEFAULT NULL,
  `display_name` varchar(191) DEFAULT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `manual_add` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sendmailforcandidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sendmailforcandidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1 pending, 0-done',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seo_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `footer_menu_id` bigint(20) unsigned NOT NULL,
  `seo_title` varchar(191) DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `seo_description` varchar(191) DEFAULT NULL,
  `seo_author` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nexmo_status` enum('active','deactive') NOT NULL DEFAULT 'deactive',
  `nexmo_key` varchar(191) DEFAULT NULL,
  `nexmo_secret` varchar(191) DEFAULT NULL,
  `nexmo_from` varchar(191) DEFAULT 'NEXMO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `smtp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mail_driver` varchar(191) NOT NULL DEFAULT 'smtp',
  `mail_host` varchar(191) NOT NULL DEFAULT 'smtp.gmail.com',
  `mail_port` varchar(191) NOT NULL DEFAULT '587',
  `mail_username` varchar(191) NOT NULL DEFAULT 'youremail@gmail.com',
  `mail_password` varchar(191) NOT NULL DEFAULT 'your password',
  `mail_from_name` varchar(191) NOT NULL DEFAULT 'Recruit Saas',
  `mail_from_email` varchar(191) NOT NULL DEFAULT 'admin@example.com',
  `mail_encryption` enum('tls','ssl') DEFAULT NULL,
  `mass_mail_host` varchar(255) DEFAULT NULL,
  `mass_mail_port` varchar(255) DEFAULT NULL,
  `mass_mail_username` varchar(255) DEFAULT NULL,
  `mass_mail_password` varchar(255) DEFAULT NULL,
  `mass_mail_encryption` varchar(255) DEFAULT NULL,
  `notification_mail_host` varchar(255) DEFAULT NULL,
  `notification_mail_port` varchar(255) DEFAULT NULL,
  `notification_mail_username` varchar(255) DEFAULT NULL,
  `notification_mail_password` varchar(255) DEFAULT NULL,
  `notification_mail_encryption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `contact_no` varchar(255) DEFAULT NULL,
  `email_id` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `dailyDate` date DEFAULT NULL,
  `dailyCount` int(11) DEFAULT 0,
  `mailtype` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sold_by_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sold_by_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `first_name` text DEFAULT NULL,
  `last_name` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `building` text DEFAULT NULL,
  `street` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `organization` text DEFAULT NULL,
  `gstin` text DEFAULT NULL,
  `pan_no` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `staffing_company_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staffing_company_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_company_id` int(11) NOT NULL,
  `from_client_id` int(11) DEFAULT NULL,
  `from_user_id` int(11) NOT NULL DEFAULT 0,
  `from_status` int(11) DEFAULT 0,
  `from_created_at` timestamp NULL DEFAULT current_timestamp(),
  `to_company_id` int(11) DEFAULT NULL,
  `to_email` varchar(55) DEFAULT NULL,
  `to_status` int(11) DEFAULT NULL COMMENT 'Registration status 0:For newly invitation, 1:Already Register  ',
  `job_id` int(11) DEFAULT NULL,
  `to_invitation_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 : Pending , 1	: accept ,  -1 : ignore',
  PRIMARY KEY (`id`),
  KEY `idx_from_invstatus` (`from_company_id`,`to_invitation_status`),
  KEY `idx_to_email` (`to_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `staffing_company_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staffing_company_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_company_id` int(11) NOT NULL,
  `from_client_id` int(11) DEFAULT NULL,
  `from_user_id` int(11) NOT NULL DEFAULT 0,
  `from_status` int(11) DEFAULT 0,
  `from_created_at` timestamp NULL DEFAULT current_timestamp(),
  `to_company_id` int(11) DEFAULT NULL,
  `to_client_id` int(11) DEFAULT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `to_status` int(11) DEFAULT NULL,
  `to_created_at` timestamp NULL DEFAULT NULL,
  `final_status` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_from_final` (`from_company_id`,`final_status`),
  KEY `idx_to_final` (`to_company_id`,`final_status`),
  KEY `idx_pair` (`from_company_id`,`to_company_id`,`final_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stage_point_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stage_point_tb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stage_points` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `state_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `state_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `image` varchar(3) NOT NULL,
  `fips_code` varchar(10) DEFAULT NULL,
  `iso2` varchar(10) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) DEFAULT 1,
  `wikidataid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sticky_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sticky_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `note_text` mediumtext NOT NULL,
  `colour` enum('blue','yellow','red','gray','purple','green') NOT NULL DEFAULT 'blue',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stripe_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stripe_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `invoice_id` varchar(191) DEFAULT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `amount` decimal(12,2) unsigned NOT NULL,
  `pay_date` date NOT NULL,
  `next_pay_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sub_domain_module_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_domain_module_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_code` varchar(191) DEFAULT NULL,
  `supported_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `schema` varchar(10) NOT NULL DEFAULT 'http',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `stripe_id` varchar(191) NOT NULL,
  `stripe_plan` varchar(191) NOT NULL,
  `quantity` int(11) NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_status` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `table 99`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `table 99` (
  `COL 1` varchar(76) DEFAULT NULL,
  `COL 2` varchar(27) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tablename`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tablename` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(512) DEFAULT NULL,
  `country_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` mediumtext NOT NULL,
  `due_date` datetime NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tb_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '0-new, 1-other',
  `job_id` int(11) DEFAULT NULL,
  `application_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `td_industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `td_industries` (
  `ind_id` int(11) NOT NULL,
  `ind_name` varchar(255) DEFAULT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `team_meeting_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_meeting_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` text DEFAULT NULL,
  `meeting_link` text NOT NULL,
  `interview_date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_link` (`meeting_link`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `team_meeting_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_meeting_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_link` text NOT NULL,
  `interview_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `interview_job_wise_con_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `testapplications_original_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testapplications_original_backup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `testing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `th_email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `th_email_template` (
  `e_temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `template_subject` varchar(255) DEFAULT NULL,
  `template_description` text NOT NULL,
  `template_message` varchar(250) DEFAULT NULL,
  `mail_to` varchar(250) DEFAULT NULL,
  `mail_cc` varchar(250) DEFAULT NULL,
  `mail_bcc` varchar(250) DEFAULT NULL,
  `mail_from` varchar(200) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `curr_date` datetime NOT NULL,
  `temptype` int(11) NOT NULL DEFAULT 1 COMMENT '1- fwd_resume, 2- mass-mail, 3- interview, 4-send mail , 5-bench reject',
  `tempForInterview` int(11) NOT NULL DEFAULT 1,
  `company_id` bigint(20) DEFAULT NULL,
  `orderByShow` int(11) DEFAULT NULL,
  PRIMARY KEY (`e_temp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `theme_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `primary_color` varchar(191) NOT NULL,
  `admin_custom_css` longtext DEFAULT NULL,
  `front_custom_css` longtext DEFAULT NULL,
  `home_background_image` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_no` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `company_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tickets_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `application_email` varchar(55) NOT NULL,
  `job_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  `status_name` varchar(20) DEFAULT NULL,
  `process_child_name` varchar(55) NOT NULL,
  `note` varchar(100) NOT NULL,
  `visible` int(11) NOT NULL DEFAULT 0,
  `insert_type` int(11) NOT NULL DEFAULT 0 COMMENT '0:only process/status update',
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_schedule_date` varchar(20) DEFAULT NULL,
  `interview_from_time` varchar(10) DEFAULT NULL,
  `interview_to_time` varchar(10) DEFAULT NULL,
  `interview_mode` varchar(10) DEFAULT NULL,
  `interview_mode_child_level` varchar(20) DEFAULT NULL,
  `interview_mention_link` varchar(255) DEFAULT NULL,
  `interviewers` varchar(255) DEFAULT NULL,
  `interview_additional_email` varchar(55) DEFAULT NULL,
  `interview_message` varchar(255) DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `timeline_type` int(11) NOT NULL DEFAULT 0 COMMENT '0:For Status update, 1: For  Interview Schedule',
  `prev_process_name` varchar(55) DEFAULT NULL,
  `prev_process_child_name` varchar(55) DEFAULT NULL,
  `interview_schedules_id` int(11) DEFAULT NULL,
  `average_star_rating` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `event_by_company_id` int(11) DEFAULT NULL,
  `event_by_user_id` int(11) DEFAULT NULL,
  `master_process_name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `todo_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `todo_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(191) NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `top_up_coin_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `top_up_coin_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` double(15,2) DEFAULT NULL,
  `price_usd` double(15,2) DEFAULT NULL,
  `coins` int(11) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '1-active, 0-block',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custome_chk` int(11) DEFAULT 0,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `per_coin_rate` double(15,6) DEFAULT NULL,
  `per_coin_rate_usd` double(15,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `top_up_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `top_up_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `price` double(15,2) DEFAULT NULL,
  `coins` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `custome_chk` int(11) NOT NULL DEFAULT 0,
  `event_type` enum('operational','sales') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `payment_orderId` text DEFAULT NULL,
  `payment_Id` text DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `payment_link_id` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_event` (`company_id`,`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `updatetimeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updatetimeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `event_by_company_id` int(11) DEFAULT NULL,
  `event_by_user_id` int(11) DEFAULT NULL,
  `master_process_name` varchar(55) DEFAULT NULL,
  `child_process_name` varchar(55) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `application_email` varchar(55) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_schedule_date` varchar(20) DEFAULT NULL,
  `interview_from_time` varchar(10) DEFAULT NULL,
  `interview_to_time` varchar(10) DEFAULT NULL,
  `interview_mode` varchar(10) DEFAULT NULL,
  `interview_mode_child_level` varchar(20) DEFAULT NULL,
  `interview_mention_link` varchar(255) DEFAULT NULL,
  `interviewers` varchar(255) DEFAULT NULL,
  `interview_additional_email` varchar(55) DEFAULT NULL,
  `interview_message` varchar(555) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `interview_schedules_id` int(11) DEFAULT NULL,
  `average_star_rating` int(11) DEFAULT NULL,
  `prev_process_name` varchar(55) DEFAULT NULL,
  `prev_process_child_name` varchar(55) DEFAULT NULL,
  `offer_currency` int(11) DEFAULT NULL,
  `offer_mode` int(11) DEFAULT NULL,
  `offer_amount` varchar(10) DEFAULT NULL,
  `joined_date` datetime DEFAULT NULL,
  `candidate_status` int(11) DEFAULT NULL,
  `management_status` int(11) DEFAULT NULL,
  `accept_date_time` datetime DEFAULT NULL,
  `approved_date_time` datetime DEFAULT NULL,
  `send_approved_date_time` datetime DEFAULT NULL,
  `send_accept_date_time` datetime DEFAULT NULL,
  `email_id_hr` text DEFAULT NULL,
  `name_hr` text DEFAULT NULL,
  `yearly_gross` double(15,2) DEFAULT NULL,
  `yearly_final_gross` double(15,2) DEFAULT NULL,
  `mtypeSection` int(11) DEFAULT NULL,
  `designation_offer` text DEFAULT NULL,
  `timezone_abbreviation` varchar(11) DEFAULT NULL,
  `confidential` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(55) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `root_user` int(11) NOT NULL DEFAULT 0,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `calling_code` varchar(191) DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `mobile_verified` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verification_code` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `email_verification_expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `working_status` int(11) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '0: For Employer user , 1 : For Consultancy',
  `active_session` varchar(55) NOT NULL,
  `session_time` int(11) NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `auto_date_team` date DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `super_password` text DEFAULT NULL,
  `super_admin_login_code` varchar(244) NOT NULL DEFAULT '1111',
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `linkedin` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_verification_code_unique` (`email_verification_code`),
  KEY `users_company_id_foreign` (`company_id`),
  KEY `users_company_email` (`company_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_profile` (
  `id` int(11) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `designation` varchar(55) NOT NULL,
  `country` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `city` varchar(55) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `whatsapp_count_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_count_companies` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) DEFAULT NULL,
  `messageBody` text DEFAULT NULL,
  `messageType` int(11) DEFAULT NULL COMMENT '1-custom send,2-reminder',
  `company_id` bigint(20) DEFAULT NULL,
  `instance_name` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `whatsapp_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_instances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `instance_name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-active,0-inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zoomsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zoomsettings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `redirect_uri` text DEFAULT NULL,
  `client_id` text DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `zoom_access_token` text DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_04_02_193005_create_translations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2018_06_03_132522_create_job_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2018_06_04_152857_create_skills_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2018_06_04_154802_create_countries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2018_06_04_155025_create_job_location_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2018_06_04_162742_create_company_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2018_06_04_171244_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2018_06_04_172111_create_job_skills_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2018_06_04_174729_create_application_status_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2018_06_04_174936_create_job_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2018_06_05_165900_create_tasks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2018_06_28_191256_create_language_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2018_07_03_054511_entrust_setup_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2018_07_08_103701_create_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2018_07_09_071111_add_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2018_07_11_070438_add_skills_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2018_07_11_084527_add_locations_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2018_07_11_102717_add_jobs_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2018_07_12_190134_add_slug_column_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2018_07_13_190220_add_job_application_permission',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2018_07_16_120833_add_column_priority_column_job_applications',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2018_07_17_053002_add_image_column_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2018_07_17_061732_add_team_permission',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2018_07_17_113611_create_theme_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2018_07_19_064526_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2018_09_11_195258_fix_user_roles',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2018_10_12_120216_create_smtp_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2018_10_15_120216_create_questions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2018_10_15_130216_create_job_questions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2018_10_15_140216_create_job_question_answer_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2018_10_15_261732_add_question_permission',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2018_10_17_051239_alter_smtp_setting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2018_10_29_140216_create_interview_schedule_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2018_10_31_061732_add_interview_schedule_permission',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2018_11_01_140216_create_schedule_employee_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2018_12_08_051239_alter_interview_schedule_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2018_12_08_840216_create_schedule_comment_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2018_12_13_104131_change_cover_letter_column_nullable_job_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2018_12_14_251239_alter_job_application_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2018_12_21_251239_alter_job_application_rating_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2019_03_14_113901_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2019_03_14_114823_add_company_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2019_03_14_115620_add_company_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2019_03_14_131217_add_status_column_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2019_03_16_061717_add_show_in_frontend_column_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2019_04_25_115014_add_company_id_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2019_04_26_084317_remove_company_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2019_05_02_074330_remove_roles_unique',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2019_05_03_184939_add_columns_company_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2019_05_07_122806_add_is_superadmin_column_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2019_05_07_124323_create_currencies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2019_05_07_130617_create_global_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2019_05_07_140719_add_superadmin_theme_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2019_05_15_114458_create_front_cms_headers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2019_05_20_103754_create_front_image_features_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2019_05_20_122429_create_front_icon_features_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2019_05_22_071733_create_client_feedbacks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2019_05_23_124330_create_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2019_05_28_074135_add_career_page_link_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2019_05_28_111636_create_company_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2019_05_29_115008_add_is_viewed_column_job_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2019_06_02_1165158_create_payment_setting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2019_06_02_1265158_paypal_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2019_06_03_115008_alter_company_package_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2019_06_14_034518_create_stripe_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2019_06_14_1365158_alter_payment_setting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2019_06_21_121411_add_package_type_column_company_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2019_07_02_110331_add_locale_column_global_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2019_07_02_111453_add_job_opening_columns_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2019_07_16_133145_add_email_verification_code_column_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2019_07_23_110712_check_verify_purchase_file',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2019_07_23_111456_create_linked_in_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2019_07_23_111459_add_linkedin_columns_to_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2019_08_02_060100_create_applicant_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2019_08_13_073129_update_settings_add_envato_key',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2019_08_13_073129_update_settings_add_support_key',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2019_08_28_081847_update_smtp_setting_verified',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2019_09_05_061905_add_google_recaptcha_key_column_global_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2019_09_16_081847_add_image_theme_setting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2019_09_17_095950_create_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2019_09_17_5121902_alter_company_stripe_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2019_09_17_5221902_alter_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2019_09_19_5221902_alter_company_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2019_09_24_5221902_check_paypal_detail_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2019_10_01_062707_create_sms_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2019_10_03_061505_create_sticky_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2019_10_03_084612_add_mobile_related_columns_in_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2019_10_21_084622_add_paypal_mode_columns_in_payment_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2019_10_22_085707_create_new_job_email_status_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2019_11_05_1534518_create_razorpay_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2019_11_05_191818_add_razorpay_detail_in_payment_gateway_credentials_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2019_11_05_295950_create_razorpay_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2019_11_12_054145_add_system_update_column_in_global_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2019_11_29_0974848_create_departments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2019_11_29_1074848_create_designation_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2019_11_29_1174848_create_onboard_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2019_11_29_1274848_create_onboard_files_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2019_12_02_115133_alter_onboard_status_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2019_12_20_115133_alter_company_feature_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2019_12_23_081452_create_todo_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2019_12_27_095246_add_english_row_in_language_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2020_01_02_085443_add_gender_age_country_city_columns_to_job_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2020_01_02_090553_add_required_columns_column_in_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2020_01_10_102309_add_skills_column_in_job_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2020_01_29_123548_create_application_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2020_02_01_101914_update_settings_review',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2020_04_29_052705_add_mail_setting_column_in_application_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2020_04_29_084637_add_meta_details_and_section_visibility_columns_in_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2020_04_29_084733_remove_profile_image_show_column_from_application_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2020_04_29_084804_create_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2020_05_06_063651_add_meta_details_column_in_front_cms_headers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2020_05_07_063651_add_login_image_in_front_cms_headers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2020_05_08_124243_add_paypal_environment_column_in_company_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2020_05_22_052635_add_position_and_color_columns_in_application_status_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2020_05_27_065948_create_footer_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2020_05_27_104734_create_footer_menu_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2020_05_28_081932_create_seo_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2020_06_03_121224_add_language_id_column_in_different_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2020_07_04_121224_add_delete_account_company_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2020_07_29_112558_add_default_timezone_company',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2020_08_18_093240_modify_jobs_status_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2020_09_30_093614_add_slug_column_application_status_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2020_10_07_112558_add_default_timezone_again_company',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2020_10_08_121750_add_stripe_active_subscription',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2020_10_14_112558_add_default_timezone_last_company',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2020_04_09_054504_add_sub_domain_in_companies_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2020_04_09_121040_subdomain_settings',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2020_04_16_121040_subdomain_schema_settings',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2024_01_03_184949_add_new_country_allow_to_jobs',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2024_01_04_114201_create_client_city_jobs_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2024_01_05_104101_create_job_countries_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2024_01_11_104047_add_columns_to_bench_applications_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2024_01_11_105007_add_columns_to_job_applications_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2024_01_17_101508_create_add_job_wise_condidates_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2024_12_31_120331_create_custom_queue_jobs_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2025_10_07_132806_create_permission_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2026_07_15_000001_create_coupons_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2026_07_17_000001_create_job_status_logs_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2026_07_21_090003_create_ownership_engine_shadow_comparisons_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2026_07_26_000001_create_resume_uploads_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2026_07_26_000002_create_resume_parse_results_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2026_07_26_000003_create_upload_session_audits_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2026_07_28_000001_create_candidate_resume_uploads_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2026_08_04_000001_create_job_counters_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2026_08_04_000002_create_pipeline_counters_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2026_08_04_000003_create_application_counters_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2026_08_04_000004_add_jobs_listing_v2_indexes',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2026_08_04_000005_create_job_agency_counter_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2026_08_05_000001_add_apptracker_performance_indexes',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2026_08_05_000002_create_pipeline_counters_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2026_08_05_160805_add_v2_dashboard_compound_indexes',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2026_06_28_000001_add_jero_columns_to_users_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2026_06_28_000002_create_company_core_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2026_06_28_000003_create_billing_base_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2026_06_28_000004_create_ats_core_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2026_06_28_000005_create_client_staffing_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2026_06_28_000006_create_interview_onboard_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2026_06_28_000007_create_coin_offer_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2026_06_28_000008_create_auth_role_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2026_06_28_000009_create_settings_lookup_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2026_06_28_000010_create_bench_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2026_06_28_000011_create_notification_comm_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2026_06_28_000012_create_saas_front_misc_tables',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2026_08_07_000001_add_v2_registration_indexes',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2026_08_07_000002_add_companies_domain_key',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2026_08_07_000003_create_block_log_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2026_08_07_000004_add_email_verified_columns_to_users',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2026_08_17_000002_create_company_career_settings_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2026_08_17_000003_add_hero_settings_to_company_career_settings_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2026_08_17_000004_add_all_tabs_settings_to_company_career_settings_table',20);

-- V2_Perf-10K Plan: Composite covering indexes for high-concurrency candidate stage listings
ALTER TABLE `job_applications` ADD INDEX `idx_japp_tenant_job_status` (`company_id`, `job_id`, `master_status_id`, `deleted_at`);
ALTER TABLE `add_job_wise_condidates` ADD INDEX `idx_ajwc_tenant_job_status` (`company_id`, `job_id`, `master_status_id`);
ALTER TABLE `add_job_wise_condidates` ADD INDEX `idx_ajwc_thirdparty_job_status` (`thirdparty_company_id`, `job_id`, `master_status_id`);
ALTER TABLE `rec_candidate_assign` ADD INDEX `idx_rca_source_from` (`source_from_id`, `source_application_id`);
ALTER TABLE `rec_candidate_assign` ADD INDEX `idx_rca_source_company` (`source_company_id`);
ALTER TABLE `job_metronics_skills` ADD INDEX `idx_jms_job_id` (`job_id`, `skill_name`);
