<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_resume_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id')->nullable()->index(); // job_applications.id — null during ADD flow
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('object_key');          // R2 temp path, e.g. temp/JERO_xxx.pdf
            $table->string('original_filename');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedBigInteger('uploaded_by')->nullable(); // user_id
            $table->enum('status', ['Uploaded', 'Queued', 'Processing', 'Completed', 'Failed'])->default('Uploaded');
            $table->text('error_log')->nullable();
            $table->json('parsed_data')->nullable();  // structured output from parser
            $table->text('resume_text')->nullable();  // raw extracted text
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_resume_uploads');
    }
};
