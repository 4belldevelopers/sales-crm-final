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
