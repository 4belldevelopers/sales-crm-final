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
            'company_id' => null,
            'title'      => 'Contract or Direct Hire',
            'content'    => "* Currently active on bench and open to both contract and full-time/direct-hire engagements.\n* Technical skills, problem-solving ability and communication skills have been assessed.\n* Ready for immediate deployment across flexible engagement models.\n* Background verification and onboarding compliance completed.",
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('title', 'Contract or Direct Hire')
            ->delete();
    }
};
