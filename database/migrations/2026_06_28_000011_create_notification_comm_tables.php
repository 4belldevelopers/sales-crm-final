<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline migration: notifications, conversations, email, SMS, SMTP,
 * WhatsApp, language/translation, and user token tables.
 * Note: th_email_template and user_token use utf8mb3 charset (as in production).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            DB::statement(<<<'SQL'
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('conversations')) {
            DB::statement(<<<'SQL'
CREATE TABLE `conversations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `receiver_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_company_id_foreign` (`company_id`),
  KEY `conversations_user_id_foreign` (`user_id`),
  KEY `conversations_receiver_id_foreign` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('conversation_log')) {
            DB::statement(<<<'SQL'
CREATE TABLE `conversation_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `conversation_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversation_log_conversation_id_foreign` (`conversation_id`),
  KEY `conversation_log_company_id_foreign` (`company_id`),
  KEY `conversation_log_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('email_notifications')) {
            DB::statement(<<<'SQL'
CREATE TABLE `email_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `notification_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_notifications_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('email_count_companies')) {
            DB::statement(<<<'SQL'
CREATE TABLE `email_count_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `email_count` int NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email_count_companies_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('sendmailforcandidates')) {
            DB::statement(<<<'SQL'
CREATE TABLE `sendmailforcandidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `application_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `mail_type` int DEFAULT NULL COMMENT '1-send to candidate, 2-send to employer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('mail_permissions')) {
            DB::statement(<<<'SQL'
CREATE TABLE `mail_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `permission_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1-enabled, 0-disabled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('sms_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `sms_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `driver` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_settings_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('smtp_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `smtp_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `mail_driver` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_host` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_port` int NOT NULL,
  `mail_username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_encryption` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_from_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_from_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `smtp_settings_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Production uses utf8mb3 charset for th_email_template
        if (!Schema::hasTable('th_email_template')) {
            DB::statement(<<<'SQL'
CREATE TABLE `th_email_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int DEFAULT NULL,
  `type` int NOT NULL DEFAULT '0' COMMENT '0-system, 1-custom',
  `template_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `template_key` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `body` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL);
        }

        if (!Schema::hasTable('language_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `language_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disabled',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `is_rtl` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('ltm_translations')) {
            DB::statement(<<<'SQL'
CREATE TABLE `ltm_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` int NOT NULL DEFAULT '0',
  `locale` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('linked_in_settings')) {
            DB::statement(<<<'SQL'
CREATE TABLE `linked_in_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned DEFAULT NULL,
  `client_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_secret` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `linked_in_settings_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('whatsapp_count_companies')) {
            DB::statement(<<<'SQL'
CREATE TABLE `whatsapp_count_companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `whatsapp_count` int NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_whatsapp_count_companies_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        if (!Schema::hasTable('whatsapp_instances')) {
            DB::statement(<<<'SQL'
CREATE TABLE `whatsapp_instances` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `instance_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0-inactive, 1-active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }

        // Production uses utf8mb3 charset for user_token
        if (!Schema::hasTable('user_token')) {
            DB::statement(<<<'SQL'
CREATE TABLE `user_token` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `type` int NOT NULL DEFAULT '0' COMMENT '0-email verification, 1-password reset',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
SQL);
        }
    }

    public function down(): void
    {
        $tables = [
            'user_token', 'whatsapp_instances', 'whatsapp_count_companies',
            'linked_in_settings', 'ltm_translations', 'language_settings',
            'th_email_template', 'smtp_settings', 'sms_settings',
            'mail_permissions', 'sendmailforcandidates', 'email_count_companies',
            'email_notifications', 'conversation_log', 'conversations',
            'notifications',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
