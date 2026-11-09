<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Ensure qualification_master contains standard universal eligibility degrees
     * (especially ID 65 'Any Qualification', ID 57 'Any Graduation', ID 58 'Any Post Graduation',
     * and ID 59 'Any Degree') and auto-repair existing jobs that were misattributed to ID 60 ('Others').
     */
    public function up(): void
    {
        if (Schema::hasTable('qualification_master')) {
            $masterRecords = [
                ['id' => 57, 'name' => 'Any Graduation', 'display_order' => 2],
                ['id' => 58, 'name' => 'Any Post Graduation', 'display_order' => 3],
                ['id' => 59, 'name' => 'Any Degree', 'display_order' => 4],
                ['id' => 60, 'name' => 'Others', 'display_order' => 60],
                ['id' => 61, 'name' => "Other Bachelor's Degree", 'display_order' => 56],
                ['id' => 62, 'name' => "Other Master's Degree", 'display_order' => 57],
                ['id' => 63, 'name' => 'Other Associate / Diploma', 'display_order' => 58],
                ['id' => 64, 'name' => 'Other Doctorate / Post-Doctoral', 'display_order' => 59],
                ['id' => 65, 'name' => 'Any Qualification', 'display_order' => 1],
            ];

            foreach ($masterRecords as $rec) {
                DB::table('qualification_master')->updateOrInsert(
                    ['id' => $rec['id']],
                    [
                        'name' => $rec['name'],
                        'display_order' => $rec['display_order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Auto-repair jobs where qualification column is NULL or 0
            if (Schema::hasTable('jobs')) {
                DB::table('jobs')->whereNull('qualification')->orWhere('qualification', 0)->update(['qualification' => 65]);
            }

            // Auto-repair job_qualifications where qualification_name is 'Any Qualification' but qualification_id was misassigned to 60
            if (Schema::hasTable('job_qualifications')) {
                DB::table('job_qualifications')
                    ->where('qualification_name', 'Any Qualification')
                    ->where('qualification_id', '!=', 65)
                    ->update(['qualification_id' => 65]);

                // Also repair jobs table where job_qualifications has 65 but jobs.qualification is 60
                $jobsWith65 = DB::table('job_qualifications')->where('qualification_id', 65)->pluck('job_id')->toArray();
                if (! empty($jobsWith65)) {
                    DB::table('jobs')->whereIn('id', $jobsWith65)->where('qualification', 60)->update(['qualification' => 65]);
                }
            }

            // Clear all cached qualification master and job details versions
            Cache::forget('global_qualification_master');
            Cache::forget('qualification_master_list');
            Cache::forget('qualification_master');
            Cache::forget('qualification_master_60');
            Cache::forget('qualification_master_65');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op: preserving master taxonomy integrity
    }
};
