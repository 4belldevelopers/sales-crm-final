<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enforce storage-level corporate domain uniqueness for custom domains.
     * Nullable domain_key permits multiple free webmails (gmail.com, yahoo.com) with NULL domain_key.
     */
    public function up(): void
    {
        $existing = DB::select("SHOW INDEXES FROM companies WHERE Key_name = 'uq_companies_domain_key'");
        if (empty($existing)) {
            // Deduplicate existing records: keep earliest company per domain_key, suffix duplicates with -dup-{id}
            $duplicates = DB::table('companies')
                ->select('domain_key')
                ->whereNotNull('domain_key')
                ->where('domain_key', '!=', '')
                ->groupBy('domain_key')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('domain_key');

            foreach ($duplicates as $domain) {
                $companiesWithDomain = DB::table('companies')
                    ->where('domain_key', $domain)
                    ->orderBy('id', 'asc')
                    ->pluck('id');

                $isFirst = true;
                foreach ($companiesWithDomain as $companyId) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }
                    DB::table('companies')
                        ->where('id', $companyId)
                        ->update(['domain_key' => $domain.'-dup-'.$companyId]);
                }
            }

            Schema::table('companies', function (Blueprint $table) {
                $table->unique('domain_key', 'uq_companies_domain_key');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existing = DB::select("SHOW INDEXES FROM companies WHERE Key_name = 'uq_companies_domain_key'");
        if (! empty($existing)) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropUnique('uq_companies_domain_key');
            });
        }
    }
};
