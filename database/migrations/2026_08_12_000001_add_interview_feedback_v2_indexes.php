<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes for Talent Interview Schedules & Feedback V2.
 *
 * Adds non-blocking secondary indexes:
 *  - interview_feedback: (interview_id, id) to eliminate full table scans during feedback lookups.
 *  - interview_schedules: (company_id, stage, substage) for fast tenant & pipeline status filtering.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── interview_feedback ────────────────────────────────────────────────
        $this->addIndexIfMissing(
            'interview_feedback',
            'idx_intfb_interview_id',
            'ALTER TABLE `interview_feedback`
             ADD INDEX `idx_intfb_interview_id` (`interview_id`, `id`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );

        // ── interview_schedules ───────────────────────────────────────────────
        $this->addIndexIfMissing(
            'interview_schedules',
            'idx_isched_comp_stage',
            'ALTER TABLE `interview_schedules`
             ADD INDEX `idx_isched_comp_stage` (`company_id`, `stage`, `substage`)
             ALGORITHM=INPLACE, LOCK=NONE'
        );
    }

    public function down(): void
    {
        $drops = [
            ['interview_feedback',  'idx_intfb_interview_id'],
            ['interview_schedules', 'idx_isched_comp_stage'],
        ];

        foreach ($drops as [$table, $index]) {
            if ($this->indexExists($table, $index)) {
                Schema::table($table, function ($t) use ($index) {
                    $t->dropIndex($index);
                });
            }
        }
    }

    private function addIndexIfMissing(string $table, string $index, string $sql): void
    {
        if (! $this->indexExists($table, $index)) {
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
