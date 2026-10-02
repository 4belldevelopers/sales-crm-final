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
                'title'      => 'Trained & Job-Ready Entry-Level Talent',
                'content'    => "* Job-ready entry-level talent with comprehensive practical training in relevant technologies and industry best practices.\n* Core technical competencies, hands-on project exposure, and professional communication skills have been assessed.\n* Available for full-time or contract engagement opportunities.\n* Background verification completed and ready for immediate deployment.",
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => null,
                'title'      => 'Experienced Talent (Workforce Restructuring / Immediate)',
                'content'    => "* Experienced talent currently available due to organizational workforce restructuring.\n* Relevant skills, domain experience and communication capabilities have been assessed.\n* Available for immediate full-time or contract opportunities.\n* Background verification completed; open to suitable roles and ready for prompt deployment.",
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
                'Trained & Job-Ready Entry-Level Talent',
                'Experienced Talent (Workforce Restructuring / Immediate)',
            ])
            ->delete();
    }
};
