<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_offerings') && ! Schema::hasColumn('crm_offerings', 'product_url')) {
            Schema::table('crm_offerings', function (Blueprint $table) {
                $table->string('product_url', 500)->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('crm_offerings') && Schema::hasColumn('crm_offerings', 'product_url')) {
            Schema::table('crm_offerings', function (Blueprint $table) {
                $table->dropColumn('product_url');
            });
        }
    }
};
