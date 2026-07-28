<?php
// Intent: Parser output store — one row per upload session, written by
// ParseResumeJob after the browser upload completes. Append-only after insert.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_parse_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resume_upload_id');
            $table->foreign('resume_upload_id')
                  ->references('id')
                  ->on('resume_uploads')
                  ->cascadeOnDelete();

            // Tenant shadow (denormalised for query safety — CLAUDE.md rule 3)
            $table->unsignedInteger('company_id')->index();

            // Parsed payload: {name,email,phone,skills,experience,keywords,...}
            $table->json('payload')->nullable();
            $table->string('extractor_version', 32)->nullable();
            $table->timestamp('extracted_at')->nullable();

            $table->timestamps();

            $table->index(['resume_upload_id'], 'parse_results_upload_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_parse_results');
    }
};
