<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bench_rm_note_master')->insert([
            'company_id' => null,
            'title'      => 'Role Alignment & Skills Match Recommendation',
            'content'    => "This candidate appears to be a strong match for the requirement, with relevant experience in [current designation] and hands-on exposure to [3 skills]. The profile aligns well with the key requirements of the position, particularly [most relevant requirement].\n\nAvailability: [Immediate / X days / Serving notice / Available from date].\n\nRecommended to review for this requirement.",
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('title', 'Role Alignment & Skills Match Recommendation')
            ->delete();
    }
};
