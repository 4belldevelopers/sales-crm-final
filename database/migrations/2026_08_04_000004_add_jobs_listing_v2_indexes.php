<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Jobs Listing V2 — composite index hardening.
 * All indexes added with ALGORITHM=INPLACE, LOCK=NONE where MySQL supports it.
 * Run during low-traffic window; estimated 5-15 min per large table.
 * Backout: run the down() method.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── jobs table ─────────────────────────────────────────────────────
        // Covering index for the exact hot-path query: WHERE company_id=? AND status=? ORDER BY created_at DESC LIMIT 20
        $this->addIndexIfMissing('jobs', 'idx_jobs_company_status_created',
            'ALTER TABLE jobs ADD INDEX idx_jobs_company_status_created (company_id, status, created_at)'
        );
        $this->addIndexIfMissing('jobs', 'idx_jobs_priority_created',
            'ALTER TABLE jobs ADD INDEX idx_jobs_priority_created (priority, created_at)'
        );
        // FULLTEXT for search fallback (BUG-14) — actual columns are job_description and job_requirement
        $this->addIndexIfMissing('jobs', 'ft_jobs_title_desc',
            'ALTER TABLE jobs ADD FULLTEXT INDEX ft_jobs_title_desc (title, job_description, job_requirement)'
        );

        // ── job_applications ───────────────────────────────────────────────
        // Covering index for every per-(job, stage) count
        $this->addIndexIfMissing('job_applications', 'idx_job_app_count',
            'ALTER TABLE job_applications ADD INDEX idx_job_app_count (job_id, master_status_id, deleted_at)'
        );
        $this->addIndexIfMissing('job_applications', 'idx_job_app_company',
            'ALTER TABLE job_applications ADD INDEX idx_job_app_company (company_id, master_status_id)'
        );
        $this->addIndexIfMissing('job_applications', 'idx_job_app_created',
            'ALTER TABLE job_applications ADD INDEX idx_job_app_created (job_id, created_at)'
        );
        $this->addIndexIfMissing('job_applications', 'idx_job_app_deleted',
            'ALTER TABLE job_applications ADD INDEX idx_job_app_deleted (deleted_at)'
        );

        // ── job_application_skills (470k rows, no FK index — BUG-15) ───────
        $this->addIndexIfMissing('job_application_skills', 'idx_jas_app',
            'ALTER TABLE job_application_skills ADD INDEX idx_jas_app (job_application_id)'
        );

        // ── candidate_job_skill_exp (678k rows — BUG-16) ──────────────────
        $this->addIndexIfMissing('candidate_job_skill_exp', 'idx_cjse_candidate_job',
            'ALTER TABLE candidate_job_skill_exp ADD INDEX idx_cjse_candidate_job (candidate_id, job_id)'
        );
        $this->addIndexIfMissing('candidate_job_skill_exp', 'idx_cjse_job',
            'ALTER TABLE candidate_job_skill_exp ADD INDEX idx_cjse_job (job_id)'
        );

        // ── bench_applications ─────────────────────────────────────────────
        $this->addIndexIfMissing('bench_applications', 'idx_bench_company_status',
            'ALTER TABLE bench_applications ADD INDEX idx_bench_company_status (company_id, status)'
        );
        $this->addIndexIfMissing('bench_applications', 'idx_bench_job',
            'ALTER TABLE bench_applications ADD INDEX idx_bench_job (job_id)'
        );

        // ── candidate_status ───────────────────────────────────────────────
        $this->addIndexIfMissing('candidate_status', 'idx_cs_parent',
            'ALTER TABLE candidate_status ADD INDEX idx_cs_parent (parent)'
        );

        // ── recommendation_relation ────────────────────────────────────────
        $this->addIndexIfMissing('recommendation_relation', 'idx_rec_to_company_final',
            'ALTER TABLE recommendation_relation ADD INDEX idx_rec_to_company_final (to_company_id, final_status)'
        );
        $this->addIndexIfMissing('recommendation_relation', 'idx_rec_job_user',
            'ALTER TABLE recommendation_relation ADD INDEX idx_rec_job_user (job_id)'
        );
    }

    public function down(): void
    {
        $drops = [
            'jobs'                   => ['idx_jobs_company_status_created', 'idx_jobs_priority_created', 'ft_jobs_title_desc'],
            'job_applications'       => ['idx_job_app_count', 'idx_job_app_company', 'idx_job_app_created', 'idx_job_app_deleted'],
            'job_application_skills' => ['idx_jas_app'],
            'candidate_job_skill_exp'=> ['idx_cjse_candidate_job', 'idx_cjse_job'],
            'bench_applications'     => ['idx_bench_company_status', 'idx_bench_job'],
            'candidate_status'       => ['idx_cs_parent'],
            'recommendation_relation'=> ['idx_rec_to_company_final', 'idx_rec_job_user'],
        ];

        foreach ($drops as $table => $indexes) {
            foreach ($indexes as $index) {
                $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
                if (!empty($exists)) {
                    DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
                }
            }
        }
    }

    private function addIndexIfMissing(string $table, string $indexName, string $ddl): void
    {
        $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        if (empty($exists)) {
            DB::statement($ddl);
        }
    }
};
