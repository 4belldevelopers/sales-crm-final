<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: interview scheduling, onboarding, meeting, and zoom tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('interview_schedules')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `job_application_id` int unsigned DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `from_time` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_time` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notify_interviewer` tinyint(1) DEFAULT NULL,
  `notify_candidate` tinyint(1) DEFAULT NULL,
  `notify_spoc` tinyint(1) DEFAULT NULL,
  `notify_others` tinyint(1) DEFAULT NULL,
  `others_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_calendar` tinyint(1) DEFAULT NULL,
  `zoom_meeting` tinyint(1) DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `mention_link` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interview_mode_id` int unsigned DEFAULT NULL,
  `interview_mode_level_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `candidate_time_zone` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone_abbreviation` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidential` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `interview_schedules_company_id_foreign` (`company_id`),
  KEY `interview_schedules_job_id_foreign` (`job_id`),
  KEY `interview_schedules_job_application_id_foreign` (`job_application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_schedule_employees')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_schedule_employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_schedule_id` int unsigned DEFAULT NULL,
  `employee_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_schedule_employees_interview_schedule_id_foreign` (`interview_schedule_id`),
  KEY `interview_schedule_employees_employee_id_foreign` (`employee_id`),
  KEY `interview_schedule_employees_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_schedule_comments')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_schedule_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_schedule_id` int unsigned DEFAULT NULL,
  `comment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_schedule_comments_interview_schedule_id_foreign` (`interview_schedule_id`),
  KEY `interview_schedule_comments_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_skills_feedback')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_skills_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `interviewer_id` int DEFAULT NULL,
  `interview_feedback_id` int DEFAULT NULL,
  `skill_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skill_rating` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_feedback')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_feedback` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `interview_schedule_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `job_application_id` int unsigned DEFAULT NULL,
  `interviewer_id` int unsigned DEFAULT NULL,
  `star_rating` int DEFAULT NULL,
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `interview_status_id` int DEFAULT NULL,
  `interview_sub_status_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_feedback_interview_schedule_id_foreign` (`interview_schedule_id`),
  KEY `interview_feedback_job_id_foreign` (`job_id`),
  KEY `interview_feedback_job_application_id_foreign` (`job_application_id`),
  KEY `interview_feedback_interviewer_id_foreign` (`interviewer_id`),
  KEY `interview_feedback_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_status')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_sub_status')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_sub_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `interview_status_id` int unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_sub_status_interview_status_id_foreign` (`interview_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_mode')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_mode` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_mode_lavel')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_mode_lavel` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `interview_mode_id` int unsigned NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_mode_lavel_interview_mode_id_foreign` (`interview_mode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_processes')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_processes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_hiring_process')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_hiring_process` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `interview_processes_id` int NOT NULL,
  `process_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sequence_number` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('interview_questions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `interview_questions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `question` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enable','disable') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable',
  `required` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interview_questions_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('on_board_details')) {
            DB::statement(<<<'SQL'
CREATE TABLE `on_board_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `job_id` int unsigned DEFAULT NULL,
  `job_application_id` int unsigned DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `comments` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offer_currency` int DEFAULT NULL,
  `offer_mode` int DEFAULT NULL,
  `offer_amount` int DEFAULT NULL,
  `yearly_gross` double(15,2) DEFAULT NULL,
  `yearly_final_gross` double(15,2) DEFAULT NULL,
  `designation_offer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mtypeSection` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `on_board_details_job_id_foreign` (`job_id`),
  KEY `on_board_details_job_application_id_foreign` (`job_application_id`),
  KEY `on_board_details_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('on_board_files')) {
            DB::statement(<<<'SQL'
CREATE TABLE `on_board_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `on_board_detail_id` int unsigned DEFAULT NULL,
  `file_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `on_board_files_on_board_detail_id_foreign` (`on_board_detail_id`),
  KEY `on_board_files_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('meeting_schedules')) {
            DB::statement(<<<'SQL'
CREATE TABLE `meeting_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `from_time` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_time` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meeting_link` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_schedules_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('meeting_comments')) {
            DB::statement(<<<'SQL'
CREATE TABLE `meeting_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `meeting_schedule_id` int unsigned DEFAULT NULL,
  `comment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_comments_meeting_schedule_id_foreign` (`meeting_schedule_id`),
  KEY `meeting_comments_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('google_meeting_lists')) {
            DB::statement(<<<'SQL'
CREATE TABLE `google_meeting_lists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_event_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_meet_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('google_meeting_schedule')) {
            DB::statement(<<<'SQL'
CREATE TABLE `google_meeting_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `interview_schedule_id` int NOT NULL,
  `google_meeting_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('google_sheets')) {
            DB::statement(<<<'SQL'
CREATE TABLE `google_sheets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_sheet_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_sheet_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('team_meeting_lists')) {
            DB::statement(<<<'SQL'
CREATE TABLE `team_meeting_lists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `teams_meeting_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('team_meeting_schedule')) {
            DB::statement(<<<'SQL'
CREATE TABLE `team_meeting_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `interview_schedule_id` int NOT NULL,
  `team_meeting_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('zoomsettings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `zoomsettings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `interview_schedule_id` int NOT NULL,
  `zoom_meeting_list_id` int NOT NULL,
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
            'zoomsettings', 'team_meeting_schedule', 'team_meeting_lists',
            'google_sheets', 'google_meeting_schedule', 'google_meeting_lists',
            'meeting_comments', 'meeting_schedules', 'on_board_files', 'on_board_details',
            'interview_questions', 'interview_hiring_process', 'interview_processes',
            'interview_mode_lavel', 'interview_mode', 'interview_sub_status',
            'interview_status', 'interview_feedback', 'interview_skills_feedback',
            'interview_schedule_comments', 'interview_schedule_employees',
            'interview_schedules',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
