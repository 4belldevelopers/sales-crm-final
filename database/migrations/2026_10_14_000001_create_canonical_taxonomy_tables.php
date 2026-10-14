<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * JERO Taxonomy V2 — Phase 1: Canonical Normalized Write Schema
     */
    public function up(): void
    {
        // 1. taxonomy_departments
        if (!Schema::hasTable('taxonomy_departments')) {
            Schema::create('taxonomy_departments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100)->unique();
                $table->string('code', 50)->unique();
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 2. taxonomy_role_families
        if (!Schema::hasTable('taxonomy_role_families')) {
            Schema::create('taxonomy_role_families', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('department_id');
                $table->string('name', 100);
                $table->string('code', 50)->unique();
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();

                $table->foreign('department_id')
                    ->references('id')
                    ->on('taxonomy_departments')
                    ->onDelete('restrict');
                $table->index('department_id');
            });
        }

        // 3. taxonomy_skills
        if (!Schema::hasTable('taxonomy_skills')) {
            Schema::create('taxonomy_skills', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 191)->unique();
                $table->string('normalized_key', 191)->unique();
                $table->string('category', 100)->nullable();
                $table->string('subcategory', 100)->nullable();
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 4. taxonomy_technologies
        if (!Schema::hasTable('taxonomy_technologies')) {
            Schema::create('taxonomy_technologies', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 191)->unique();
                $table->string('normalized_key', 191)->unique();
                $table->string('ecosystem', 100)->nullable();
                $table->string('category', 100)->nullable();
                $table->enum('lifecycle_status', ['emerging', 'active', 'stable', 'declining', 'deprecated', 'archived'])->default('active');
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 5. taxonomy_jd_templates
        if (!Schema::hasTable('taxonomy_jd_templates')) {
            Schema::create('taxonomy_jd_templates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('role_family_id')->nullable();
                $table->longText('role_summary');
                $table->json('key_responsibilities');
                $table->json('must_have_skills');
                $table->json('technical_competencies');
                $table->json('nice_to_have_skills')->nullable();
                $table->json('tools_and_platforms')->nullable();
                $table->text('domain_knowledge')->nullable();
                $table->json('success_indicators_first_90_days')->nullable();
                $table->text('education')->nullable();
                $table->json('certifications')->nullable();
                $table->unsignedTinyInteger('quality_score')->default(90);
                $table->string('quality_score_version', 20)->default('v1.2.0');
                $table->string('quality_evaluator', 50)->default('automated_rules');
                $table->enum('status', ['draft', 'review', 'approved', 'active', 'deprecated', 'archived'])->default('active');
                // Zero Experience Invariant: Must remain NULL by default on master templates
                $table->unsignedSmallInteger('minimum_exp')->nullable()->default(null);
                $table->unsignedSmallInteger('maxmum_exp')->nullable()->default(null);
                $table->timestamps();

                $table->foreign('role_family_id')
                    ->references('id')
                    ->on('taxonomy_role_families')
                    ->onDelete('set null');
                $table->index('role_family_id');
            });
        }

        // 6. taxonomy_designations
        if (!Schema::hasTable('taxonomy_designations')) {
            Schema::create('taxonomy_designations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('canonical_title', 191);
                $table->string('identity_normalized_key', 191);
                $table->string('search_normalized_key', 191);
                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('role_family_id');
                $table->enum('seniority_level', [
                    'Intern', 'Entry', 'Junior', 'Mid-Level', 'Senior', 
                    'Lead', 'Staff', 'Principal', 'Director', 'VP', 'C-Level'
                ])->default('Mid-Level');
                $table->string('specialization', 191)->nullable();
                $table->unsignedBigInteger('jd_template_id')->nullable();
                $table->unsignedTinyInteger('status')->default(1); // 0=draft, 1=active, 2=archived
                $table->unsignedTinyInteger('quality_score')->default(90);
                $table->string('taxonomy_version', 32)->default('2.0.0');
                $table->timestamps();

                $table->foreign('department_id')
                    ->references('id')
                    ->on('taxonomy_departments')
                    ->onDelete('restrict');
                $table->foreign('role_family_id')
                    ->references('id')
                    ->on('taxonomy_role_families')
                    ->onDelete('restrict');
                $table->foreign('jd_template_id')
                    ->references('id')
                    ->on('taxonomy_jd_templates')
                    ->onDelete('set null');

                $table->index('search_normalized_key');
                $table->index('role_family_id');
                $table->index('department_id');
            });

            // MySQL 8.0 Stored Generated Column for Active Identity Key Uniqueness
            DB::statement("ALTER TABLE `taxonomy_designations` ADD COLUMN `active_identity_key` VARCHAR(191) AS (IF(`status` = 1, `identity_normalized_key`, NULL)) STORED");
            DB::statement("ALTER TABLE `taxonomy_designations` ADD UNIQUE INDEX `uq_active_designation` (`active_identity_key`)");
        }

        // 7. taxonomy_aliases
        if (!Schema::hasTable('taxonomy_aliases')) {
            Schema::create('taxonomy_aliases', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('alias_string', 191);
                $table->unsignedBigInteger('canonical_designation_id');
                $table->enum('alias_type', [
                    'commercial_stack', 'corporate_grade', 'synonym', 'abbreviation', 'regional'
                ])->default('commercial_stack');
                $table->string('seniority_override', 50)->nullable();
                $table->string('specialization_override', 100)->nullable();
                $table->string('primary_technology_override', 100)->nullable();
                $table->string('search_normalized_key', 191);
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps();

                $table->foreign('canonical_designation_id')
                    ->references('id')
                    ->on('taxonomy_designations')
                    ->onDelete('cascade');
                $table->index('alias_string');
                $table->index('search_normalized_key');
                $table->index('canonical_designation_id');
            });
        }

        // 8. taxonomy_designation_skills
        if (!Schema::hasTable('taxonomy_designation_skills')) {
            Schema::create('taxonomy_designation_skills', function (Blueprint $table) {
                $table->unsignedBigInteger('designation_id');
                $table->unsignedBigInteger('skill_id');
                $table->boolean('is_must_have')->default(true);
                $table->decimal('weight', 3, 2)->default(1.00);

                $table->primary(['designation_id', 'skill_id']);
                $table->foreign('designation_id')
                    ->references('id')
                    ->on('taxonomy_designations')
                    ->onDelete('cascade');
                $table->foreign('skill_id')
                    ->references('id')
                    ->on('taxonomy_skills')
                    ->onDelete('cascade');
            });
        }

        // 9. taxonomy_designation_technologies
        if (!Schema::hasTable('taxonomy_designation_technologies')) {
            Schema::create('taxonomy_designation_technologies', function (Blueprint $table) {
                $table->unsignedBigInteger('designation_id');
                $table->unsignedBigInteger('technology_id');
                $table->boolean('is_primary')->default(true);

                $table->primary(['designation_id', 'technology_id']);
                $table->foreign('designation_id')
                    ->references('id')
                    ->on('taxonomy_designations')
                    ->onDelete('cascade');
                $table->foreign('technology_id')
                    ->references('id')
                    ->on('taxonomy_technologies')
                    ->onDelete('cascade');
            });
        }

        // 10. taxonomy_versions
        if (!Schema::hasTable('taxonomy_versions')) {
            Schema::create('taxonomy_versions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('version', 32)->unique();
                $table->unsignedInteger('schema_version')->default(2);
                $table->string('build_id', 64);
                $table->string('content_hash', 64);
                $table->string('bundle_hash', 64);
                $table->unsignedInteger('canonical_count')->default(0);
                $table->unsignedInteger('alias_count')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomy_designation_technologies');
        Schema::dropIfExists('taxonomy_designation_skills');
        Schema::dropIfExists('taxonomy_aliases');
        Schema::dropIfExists('taxonomy_designations');
        Schema::dropIfExists('taxonomy_jd_templates');
        Schema::dropIfExists('taxonomy_technologies');
        Schema::dropIfExists('taxonomy_skills');
        Schema::dropIfExists('taxonomy_role_families');
        Schema::dropIfExists('taxonomy_departments');
        Schema::dropIfExists('taxonomy_versions');
    }
};
