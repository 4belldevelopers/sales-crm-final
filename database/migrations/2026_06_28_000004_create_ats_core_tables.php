<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: core ATS tables — jobs (recruitment, not queue), job_applications,
 * and all related ATS lookup/pivot/state tables.
 *
 * IMPORTANT — jobs naming conflict:
 *   Queue migration 0001_01_01_000002 creates a 7-column queue `jobs` table.
 *   Production `jobs` is a 42-column recruitment table — irreconcilable without
 *   modifying the base migration. The hasTable guard here means this is skipped
 *   when the queue migration has already created `jobs`. On a true fresh install
 *   where both migrations run, 0001_01_01_000002 runs first (earlier timestamp)
 *   and creates the 7-col queue table; this migration then skips it. The full
 *   recruitment schema must be applied via a separate alter migration if needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('add_job_wise_condidates')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('applicant_notes')) {
            DB::statement(<<<'SQL'
CREATE TABLE `applicant_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `job_application_id` int unsigned NOT NULL,
  `note_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicant_notes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('application_additional_detail')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci
SQL);
        }

        if (!Schema::hasTable('application_event')) {
            DB::statement(<<<'SQL'
CREATE TABLE `application_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci
SQL);
        }

        // FK constraint stripped (CONSTRAINT application_settings_company_id_foreign) to avoid ordering issues
        if (!Schema::hasTable('application_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `application_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `legal_term` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mail_setting` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_settings_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('application_status')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('master_status')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('candidate_status')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('candidates_notify')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('candidate_job_skill_exp')) {
            DB::statement(<<<'SQL'
CREATE TABLE `candidate_job_skill_exp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_skill_id` int NOT NULL,
  `skill_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `job_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL);
        }

        if (!Schema::hasTable('candidate_skills_value')) {
            DB::statement(<<<'SQL'
CREATE TABLE `candidate_skills_value` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `skill_id` int NOT NULL,
  `candidate_id` int NOT NULL,
  `skill_value` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('documents')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_applications')) {
            DB::statement(<<<'SQL'
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
  `recruiter_note` mediumtext COLLATE utf8mb4_unicode_ci,
  `reason_for_job_change` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `job_applications_company_id_foreign` (`company_id`),
  KEY `job_applications_job_id_foreign` (`job_id`),
  KEY `idx_job_id` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_application_answers')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_application_skills')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_application_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_application_id` int DEFAULT NULL,
  `skills` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('job_assign')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_categories')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_categories_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_countries')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `country_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_job_application')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_job_application` (
  `job_application_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  KEY `job_job_application_job_application_id_foreign` (`job_application_id`),
  KEY `job_job_application_job_id_foreign` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_locations')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_metronics_skills')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_qualifications')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_qualifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int DEFAULT NULL,
  `qualification_id` int DEFAULT NULL,
  `qualification_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('job_questions')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_skills')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Production `jobs` is a 42-col recruitment table. Queue migration
        // 0001_01_01_000002 creates a 7-col queue table with the same name
        // and runs first (earlier timestamp). The hasTable guard here prevents
        // overwriting that table. On fresh installs the queue schema takes
        // precedence; the recruitment columns must be managed separately.
        if (!Schema::hasTable('jobs')) {
            DB::statement(<<<'SQL'
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
  KEY `idx_company_id` (`company_id`),
  KEY `idx_comp_status_created` (`company_id`,`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_additional_detail')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_additional_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `job_id` int NOT NULL,
  `detail_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci
SQL);
        }

        if (!Schema::hasTable('join_applications')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('questions')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('rec_candidate_assign')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('recommendation_relation')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('stage_point_tb')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL);
        }

        if (!Schema::hasTable('tb_ranks')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
SQL);
        }

        if (!Schema::hasTable('timeline')) {
            DB::statement(<<<'SQL'
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
  `timeline_type` int NOT NULL DEFAULT '0' COMMENT '0:For Status update, 1: For Interview Schedule',
  `prev_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prev_process_child_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_schedules_id` int DEFAULT NULL,
  `average_star_rating` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `event_by_company_id` int DEFAULT NULL,
  `event_by_user_id` int DEFAULT NULL,
  `master_process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('updatetimeline')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('job_status_logs')) {
            DB::statement(<<<'SQL'
CREATE TABLE `job_status_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int unsigned NOT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `from_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_status_logs_job_id_index` (`job_id`),
  KEY `job_status_logs_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    public function down(): void
    {
        $tables = [
            'job_status_logs', 'updatetimeline', 'timeline', 'tb_ranks', 'stage_point_tb',
            'recommendation_relation', 'rec_candidate_assign', 'questions', 'join_applications',
            'job_additional_detail', 'jobs', 'job_skills', 'job_questions', 'job_qualifications',
            'job_metronics_skills', 'job_locations', 'job_job_application', 'job_countries',
            'job_categories', 'job_assign', 'job_application_skills', 'job_application_answers',
            'job_applications', 'documents', 'candidate_skills_value', 'candidate_job_skill_exp',
            'candidates_notify', 'candidate_status', 'master_status', 'application_status',
            'application_settings', 'application_event', 'application_additional_detail',
            'applicant_notes', 'add_job_wise_condidates',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
