<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Add monotonic pipeline_version to jobs table for AppTracker real-time delta sequencing
     * and client gap detection under 10K concurrency.
     */
    public function up(): void
    {
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (! Schema::hasColumn('jobs', 'pipeline_version')) {
                    $table->unsignedBigInteger('pipeline_version')->default(1)->after('status')
                        ->comment('Monotonically incremented pipeline state version for real-time Reverb delta sequencing');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                if (Schema::hasColumn('jobs', 'pipeline_version')) {
                    $table->dropColumn('pipeline_version');
                }
            });
        }
    }
};
