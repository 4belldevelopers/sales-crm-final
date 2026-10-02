<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bench_rm_note_master')->insert([
            'company_id' => null,
            'title'      => 'Good Communication Skills',
            'content'    => "The candidate has relevant experience and a well-rounded professional profile, with good communication skills along with the required technical, functional or domain knowledge. The candidate demonstrates a professional approach, adaptability and practical exposure relevant to the role.\n\nOverall, the profile appears suitable for the current requirement and is worth reviewing based on the candidate’s experience, skills and professional background.\n\nRecommended to review this profile.",
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('title', 'Good Communication Skills')
            ->delete();
    }
};
