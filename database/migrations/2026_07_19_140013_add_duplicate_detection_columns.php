<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Extends duplicate detection beyond the direct Upload form: `duplicate` on
// add_job_wise_condidates lets each job-addition (Similar Candidates/Internal
// Bench/Career Page/Agency Bench/Copy) carry its own per-job duplicate signal
// without overloading job_applications.duplicate, which is shared across every
// job the same candidate profile is later added to. `enable_cross_agency_duplicate`
// is the opt-in toggle for the new job-wide, cross-agency duplicate pool.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_job_wise_condidates', function (Blueprint $table) {
            $table->tinyInteger('duplicate')->nullable()->default(null)->after('skill_match');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->tinyInteger('enable_cross_agency_duplicate')->default(0)->after('app_duplicate_days');
        });
    }

    public function down(): void
    {
        Schema::table('add_job_wise_condidates', function (Blueprint $table) {
            $table->dropColumn('duplicate');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('enable_cross_agency_duplicate');
        });
    }
};
