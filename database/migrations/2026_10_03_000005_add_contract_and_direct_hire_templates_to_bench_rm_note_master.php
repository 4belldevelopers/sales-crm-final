<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('bench_rm_note_master')->insert([
            [
                'company_id' => null,
                'title'      => 'Contract/C2C/C2H Engagement Only',
                'content'    => "* Currently active on bench and available for contract engagements only.\n* Technical skills, problem-solving ability and communication skills have been assessed.\n* Ready for immediate deployment on contract engagements.\n* Background verification and onboarding compliance completed.",
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => null,
                'title'      => 'Direct Hire/Permanent Hiring Engagement Only',
                'content'    => "* Currently active on bench and available for full-time/direct-hire opportunities only.\n* Technical skills, problem-solving ability and communication skills have been assessed.\n* Ready for immediate deployment.\n* Background verification and onboarding compliance completed.",
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->whereIn('title', [
                'Contract/C2C/C2H Engagement Only',
                'Direct Hire/Permanent Hiring Engagement Only',
            ])
            ->delete();
    }
};
