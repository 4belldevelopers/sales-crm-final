<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Creates completely isolated tables for Bench Candidate Relationship Manager Comments
 * and Relationship Manager Note Templates, preventing any database conflicts or data
 * leakage when bench candidates are shared across companies in the talent marketplace.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bench_rm_note_master')) {
            Schema::create('bench_rm_note_master', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable()->index('idx_brm_company');
                $table->string('title', 255);
                $table->mediumText('content');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

            // Seed default Relationship Manager templates tailored for bench talent deployment
            DB::table('bench_rm_note_master')->insert([
                [
                    'company_id' => null,
                    'title' => 'Bench Availability & Deployment Readiness',
                    'content' => "Talent is active on bench with immediate availability.\n• Technical competencies, problem-solving, and client communication thoroughly assessed.\n• Ready for full-time deployment across hybrid/remote client engagements.\n• All onboarding compliance and background checks verified.",
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => null,
                    'title' => 'Commercial & Margin Assessment',
                    'content' => "Commercial evaluation completed:\n• Base candidate cost (pay rate) and client bill rate confirmed.\n• Gross margins align with partner placement standards.\n• Commercial terms open to negotiation for long-term contract engagements.",
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => null,
                    'title' => 'Core Strengths & Project Fit',
                    'content' => "Key strengths and project suitability:\n• Proven track record in delivery with enterprise-grade architectures.\n• Recommended for lead or senior developer roles matching primary skill stack.\n• Candidate demonstrates high adaptability and rapid ramp-up capability.",
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'company_id' => null,
                    'title' => 'Notice Period & Work Authorization',
                    'content' => "Deployment logistics:\n• Serving notice period: Immediate / 0 days (bench resource).\n• Work authorization / visa status verified and compliant.\n• Prepared to initiate client interviews upon requisition approval.",
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (!Schema::hasTable('bench_candidate_rm_comments')) {
            Schema::create('bench_candidate_rm_comments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('company_id')->index('idx_bench_rm_company');
                $table->unsignedInteger('candidate_id')->index('idx_bench_rm_cand');
                $table->unsignedInteger('relationship_manager_id')->nullable();
                $table->mediumText('comments');
                $table->timestamps();

                $table->unique(['company_id', 'candidate_id'], 'idx_bench_rm_comp_cand');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bench_candidate_rm_comments');
        Schema::dropIfExists('bench_rm_note_master');
    }
};
