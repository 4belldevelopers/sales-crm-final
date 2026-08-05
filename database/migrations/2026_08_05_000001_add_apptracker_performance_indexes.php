<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Zero-downtime secondary indexes for the AppTracker hot path.
 *
 * All statements use ALGORITHM=INPLACE, LOCK=NONE so InnoDB adds each index
 * online without blocking reads or writes. If MySQL cannot honour INPLACE
 * (e.g. old engine, corrupted stats) the statement fails fast rather than
 * silently falling back to a full table rebuild.
 *
 * Tables targeted:
 *   add_job_wise_condidates  — 63 K rows, previously only PRIMARY KEY
 *   clients                  — previously only PRIMARY KEY
 *   tickets                  — previously only PRIMARY KEY
 *   interview_schedules      — adds composite on company_id
 *   job_applications         — adds (job_id, master_status_id) + prefix indexes
 *
 * The typo "condidates" is intentional — it matches the real table name.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── add_job_wise_condidates ──────────────────────────────────────────
        // Used by loadcounter() and counter aggregations on every page load.
        // Without indexes every counter query does a full 63 K-row scan.
        $this->addIndexIfMissing(
            'add_job_wise_condidates',
            'idx_awjc_job_status',
            'ALTER TABLE `add_job_wise_condidates`
             ADD INDEX `idx_awjc_job_status` (`job_id`, `master_status_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );
        $this->addIndexIfMissing(
            'add_job_wise_condidates',
            'idx_awjc_company_job',
            'ALTER TABLE `add_job_wise_condidates`
             ADD INDEX `idx_awjc_company_job` (`company_id`, `job_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );
        $this->addIndexIfMissing(
            'add_job_wise_condidates',
            'idx_awjc_thirdparty_job',
            'ALTER TABLE `add_job_wise_condidates`
             ADD INDEX `idx_awjc_thirdparty_job` (`thirdparty_company_id`, `job_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );
        $this->addIndexIfMissing(
            'add_job_wise_condidates',
            'idx_awjc_app',
            'ALTER TABLE `add_job_wise_condidates`
             ADD INDEX `idx_awjc_app` (`job_application_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );

        // ── clients ──────────────────────────────────────────────────────────
        // Loaded on every job-creation form and apptracker page.
        $this->addIndexIfMissing(
            'clients',
            'idx_clients_company',
            'ALTER TABLE `clients`
             ADD INDEX `idx_clients_company` (`company_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );

        // ── tickets ──────────────────────────────────────────────────────────
        // ticketList() and ticketshow() filter by company_id.
        $this->addIndexIfMissing(
            'tickets',
            'idx_tickets_company',
            'ALTER TABLE `tickets`
             ADD INDEX `idx_tickets_company` (`company_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );

        // ── interview_schedules ───────────────────────────────────────────────
        $this->addIndexIfMissing(
            'interview_schedules',
            'idx_isched_company',
            'ALTER TABLE `interview_schedules`
             ADD INDEX `idx_isched_company` (`company_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );

        // ── job_applications ─────────────────────────────────────────────────
        // (job_id, master_status_id) drives all 8 tab counter queries.
        $this->addIndexIfMissing(
            'job_applications',
            'idx_japp_status',
            'ALTER TABLE `job_applications`
             ADD INDEX `idx_japp_status` (`job_id`, `master_status_id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );
        // Prefix indexes for duplicate-email / duplicate-phone checks.
        $this->addIndexIfMissing(
            'job_applications',
            'idx_japp_email',
            'ALTER TABLE `job_applications`
             ADD INDEX `idx_japp_email` (`company_id`, `email`(40))
             ALGORITHM=INPLACE, LOCK=NONE'
        );
        $this->addIndexIfMissing(
            'job_applications',
            'idx_japp_phone',
            'ALTER TABLE `job_applications`
             ADD INDEX `idx_japp_phone` (`company_id`, `phone`(20))
             ALGORITHM=INPLACE, LOCK=NONE'
        );
    }

    public function down(): void
    {
        $drops = [
            ['add_job_wise_condidates', 'idx_awjc_job_status'],
            ['add_job_wise_condidates', 'idx_awjc_company_job'],
            ['add_job_wise_condidates', 'idx_awjc_thirdparty_job'],
            ['add_job_wise_condidates', 'idx_awjc_app'],
            ['clients',                 'idx_clients_company'],
            ['tickets',                 'idx_tickets_company'],
            ['interview_schedules',     'idx_isched_company'],
            ['job_applications',        'idx_japp_status'],
            ['job_applications',        'idx_japp_email'],
            ['job_applications',        'idx_japp_phone'],
        ];

        foreach ($drops as [$table, $index]) {
            if ($this->indexExists($table, $index)) {
                Schema::table($table, function ($t) use ($index) {
                    $t->dropIndex($index);
                });
            }
        }
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function addIndexIfMissing(string $table, string $index, string $sql): void
    {
        if (!$this->indexExists($table, $index)) {
            DB::statement($sql);
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $count = DB::selectOne(
            'SELECT COUNT(*) AS cnt
             FROM information_schema.STATISTICS
             WHERE table_schema = DATABASE()
               AND table_name   = ?
               AND index_name   = ?',
            [$table, $index]
        );
        return ($count->cnt ?? 0) > 0;
    }
};
