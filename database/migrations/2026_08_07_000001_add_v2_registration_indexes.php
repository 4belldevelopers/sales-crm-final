<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 1 — V2 Registration performance & safety indexes.
 *
 * All ALTER TABLE operations use ALGORITHM=INPLACE, LOCK=NONE (MySQL 8 online DDL)
 * so they can run against the live production table without blocking reads or writes.
 *
 * Audit refs: Finding #6 (packages), §7 schema bottlenecks (subscriptions, users, companies).
 */
return new class extends Migration
{
    public function up(): void
    {
        // packages: speeds up Package::where('is_trial', 1)->first() (Finding #6)
        DB::statement('ALTER TABLE packages ADD KEY packages_is_trial_status (is_trial, status), ALGORITHM=INPLACE, LOCK=NONE');

        // subscriptions: preemptive company-scoped lookup index
        DB::statement('ALTER TABLE subscriptions ADD KEY subscriptions_company_id (company_id), ALGORITHM=INPLACE, LOCK=NONE');

        // users: composite index for company-scoped user lookups during registration checks
        DB::statement('ALTER TABLE users ADD KEY users_company_email (company_id, email), ALGORITHM=INPLACE, LOCK=NONE');

        // companies.sub_domain UNIQUE — treated as unique in code but schema lacked the constraint.
        // Guard: skip if duplicate sub_domain rows exist (should not in production; investigate if this fails).
        $hasDuplicates = DB::table('companies')
            ->select('sub_domain', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('sub_domain')
            ->where('sub_domain', '!=', '')
            ->groupBy('sub_domain')
            ->having('cnt', '>', 1)
            ->exists();

        if (! $hasDuplicates) {
            DB::statement('ALTER TABLE companies ADD UNIQUE KEY companies_sub_domain_unique (sub_domain), ALGORITHM=INPLACE, LOCK=NONE');
        } else {
            \Log::warning('V2Registration migration: skipped companies.sub_domain UNIQUE — duplicate sub_domain values found. Resolve duplicates and re-run.');
        }
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex('packages_is_trial_status');
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_company_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_company_email');
        });
        Schema::table('companies', function (Blueprint $table) {
            if (DB::select("SHOW INDEX FROM companies WHERE Key_name = 'companies_sub_domain_unique'")) {
                $table->dropUnique('companies_sub_domain_unique');
            }
        });
    }
};
