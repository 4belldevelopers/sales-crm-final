<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Intent: One-off safe installer for the agency-facing commercial terms table.
 * Tenancy: The table is company-scoped by company_id and uses a unique company row.
 */
require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (Schema::hasTable('agency_commercial_terms')) {
    if (! Schema::hasColumn('agency_commercial_terms', 'settings_json')) {
        DB::statement("ALTER TABLE `agency_commercial_terms` ADD COLUMN `settings_json` longtext COLLATE utf8mb4_unicode_ci NULL AFTER `agency_can_reject`");
        echo "agency_commercial_terms settings_json column added.\n";
        return;
    }

    echo "agency_commercial_terms already exists.\n";
    return;
}

DB::statement("
    CREATE TABLE `agency_commercial_terms` (
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `company_id` int unsigned NOT NULL,
      `status` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
      `version` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
      `fulltime_fee_model` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
      `fulltime_success_fee_percentage` decimal(8,2) NOT NULL DEFAULT '8.33',
      `fulltime_payment_terms_days` smallint unsigned NOT NULL DEFAULT '30',
      `fulltime_replacement_period_days` smallint unsigned NOT NULL DEFAULT '90',
      `contract_billing_model` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
      `contract_rate_amount` decimal(12,2) NOT NULL DEFAULT '80000.00',
      `contract_rate_currency` smallint unsigned NOT NULL DEFAULT '4',
      `contract_payment_terms_days` smallint unsigned NOT NULL DEFAULT '30',
      `contract_min_duration_months` smallint unsigned NOT NULL DEFAULT '3',
      `contract_replacement_policy` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'As per Contract Terms',
      `applies_to` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All Agencies',
      `acceptance_required` tinyint(1) NOT NULL DEFAULT '1',
      `acceptance_trigger` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Before Candidate Submission',
      `submission_before_acceptance` tinyint(1) NOT NULL DEFAULT '0',
      `effective_from` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Agency Acceptance Date',
      `agreement_duration_months` smallint unsigned NOT NULL DEFAULT '12',
      `auto_renewal` tinyint(1) NOT NULL DEFAULT '1',
      `agency_can_reject` tinyint(1) NOT NULL DEFAULT '1',
      `settings_json` longtext COLLATE utf8mb4_unicode_ci,
      `updated_by` int unsigned DEFAULT NULL,
      `updated_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `agency_commercial_terms_company_unique` (`company_id`),
      KEY `agency_commercial_terms_company_status_index` (`company_id`,`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

echo "agency_commercial_terms created.\n";
