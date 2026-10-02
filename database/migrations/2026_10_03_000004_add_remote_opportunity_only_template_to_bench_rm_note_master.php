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
            'title'      => 'Remote Opportunity Only',
            'content'    => "* Currently active on bench and available for remote opportunities only.\n* Technical skills, problem-solving ability and communication skills have been assessed.\n* Ready for immediate deployment on remote engagements.\n* Background verification and onboarding compliance completed.",
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
            ->where('title', 'Remote Opportunity Only')
            ->delete();
    }
};
