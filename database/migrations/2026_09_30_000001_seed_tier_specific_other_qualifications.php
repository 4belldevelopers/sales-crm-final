<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Intent: Seed tier-specific "Other" qualifications into qualification_master (IDs 61-64)
     * to provide accurate categorization for unlisted international Bachelor's, Master's,
     * Diplomas, and Doctorate degrees while preserving search recall on JERO talent marketplace.
     */
    public function up(): void
    {
        if (Schema::hasTable('qualification_master')) {
            $records = [
                [
                    'id' => 61,
                    'name' => "Other Bachelor's Degree",
                    'display_order' => 56,
                ],
                [
                    'id' => 62,
                    'name' => "Other Master's Degree",
                    'display_order' => 57,
                ],
                [
                    'id' => 63,
                    'name' => 'Other Associate / Diploma',
                    'display_order' => 58,
                ],
                [
                    'id' => 64,
                    'name' => 'Other Doctorate / Post-Doctoral',
                    'display_order' => 59,
                ],
            ];

            foreach ($records as $rec) {
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

            // Invalidate master qualification caches across tenant Redis clusters
            Cache::forget('qualification_master_list');
            Cache::forget('global_qualification_master');
            Cache::forget('qualification_master');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('qualification_master')) {
            DB::table('qualification_master')->whereIn('id', [61, 62, 63, 64])->delete();

            Cache::forget('qualification_master_list');
            Cache::forget('global_qualification_master');
            Cache::forget('qualification_master');
        }
    }
};
