<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 3 task 3.2 — Add companies.domain_key column and UNIQUE index.
 *
 * domain_key stores the normalized domain extracted from website (no protocol, no www, no path).
 * The domainCompanyRegisterChk probe uses this for an O(log n) exact-match instead of a
 * LIKE '%domain%' full-table scan. (Finding #13)
 *
 * Zero-downtime: column add is instant on InnoDB; UNIQUE index uses ALGORITHM=INPLACE, LOCK=NONE.
 * Backfill is done in PHP to avoid MySQL regex dependency.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies') && ! Schema::hasColumn('companies', 'domain_key')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('domain_key', 255)->nullable()->after('website');
            });
        }

        // Backfill domain_key for all existing companies from their website column.
        DB::table('companies')
            ->whereNotNull('website')
            ->where('website', '!=', '')
            ->orderBy('id')
            ->chunk(500, function ($companies) {
                foreach ($companies as $company) {
                    $host = parse_url($company->website, PHP_URL_HOST) ?? $company->website;
                    $normalized = preg_replace('/^www\./i', '', strtolower(trim($host)));
                    if ($normalized) {
                        DB::table('companies')
                            ->where('id', $company->id)
                            ->update(['domain_key' => $normalized]);
                    }
                }
            });

        // Add UNIQUE index — skip if duplicate domain_key values found (log a warning).
        $hasDuplicates = DB::table('companies')
            ->select('domain_key', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('domain_key')
            ->where('domain_key', '!=', '')
            ->groupBy('domain_key')
            ->having('cnt', '>', 1)
            ->exists();

        if (! $hasDuplicates && Schema::hasTable('companies') && ! Schema::hasIndex('companies', 'companies_domain_key_unique')) {
            DB::statement('ALTER TABLE companies ADD UNIQUE KEY companies_domain_key_unique (domain_key), ALGORITHM=INPLACE, LOCK=NONE');
        } else {
            \Log::warning('V2Registration migration: skipped companies.domain_key UNIQUE — duplicate domain_key values found or already exists.');
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (DB::select("SHOW INDEX FROM companies WHERE Key_name = 'companies_domain_key_unique'")) {
                $table->dropUnique('companies_domain_key_unique');
            }
            $table->dropColumn('domain_key');
        });
    }
};
