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
        // 1. Remove 'Commercial & Margin Assessment' template
        DB::table('bench_rm_note_master')
            ->where('title', 'Commercial & Margin Assessment')
            ->delete();

        // 2. Rename 'Experienced Talent (Workforce Restructuring / Immediate)' to 'Laid-Off Talents'
        DB::table('bench_rm_note_master')
            ->where('title', 'Experienced Talent (Workforce Restructuring / Immediate)')
            ->update([
                'title'      => 'Laid-Off Talents',
                'updated_at' => now(),
            ]);

        // 3. Update 'Notice Period & Work Authorization' template to include dynamic prefill placeholders
        DB::table('bench_rm_note_master')
            ->where('title', 'Notice Period & Work Authorization')
            ->update([
                'content' => "Deployment logistics:\n* Availability / Notice Period: [Availability / Notice Period].\n* Work Authorization / Visa: [Work Authorization / Visa Status].\n* Technical competencies and client communication thoroughly assessed.\n* Prepared to initiate client interviews upon requisition approval.",
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-insert Commercial & Margin Assessment
        DB::table('bench_rm_note_master')->insert([
            'company_id' => null,
            'title'      => 'Commercial & Margin Assessment',
            'content'    => "Commercial evaluation completed:\n• Base candidate cost (pay rate) and client bill rate confirmed.\n• Gross margins align with partner placement standards.\n• Commercial terms open to negotiation for long-term contract engagements.",
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Revert Laid-Off Talents title
        DB::table('bench_rm_note_master')
            ->where('title', 'Laid-Off Talents')
            ->update([
                'title'      => 'Experienced Talent (Workforce Restructuring / Immediate)',
                'updated_at' => now(),
            ]);

        // Revert Notice Period & Work Authorization content
        DB::table('bench_rm_note_master')
            ->where('title', 'Notice Period & Work Authorization')
            ->update([
                'content' => "Deployment logistics:\n• Serving notice period: Immediate / 0 days (bench resource).\n• Work authorization / visa status verified and compliant.\n• Prepared to initiate client interviews upon requisition approval.",
                'updated_at' => now(),
            ]);
    }
};
