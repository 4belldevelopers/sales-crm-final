<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_offerings') && ! Schema::hasColumn('crm_offerings', 'job_id')) {
            Schema::table('crm_offerings', function (Blueprint $table) {
                $table->unsignedBigInteger('job_id')->nullable()->after('company_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('crm_offerings') && Schema::hasColumn('crm_offerings', 'job_id')) {
            Schema::table('crm_offerings', function (Blueprint $table) {
                $table->dropColumn('job_id');
            });
        }
    }
};
