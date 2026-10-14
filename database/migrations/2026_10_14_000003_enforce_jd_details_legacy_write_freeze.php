<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Intent: Phase 9 Legacy Write Freeze Gate.
     * Blocks all new INSERT and UPDATE writes to legacy jd_details table while preserving
     * 100% read access and foreign key integrity for historical records.
     */
    public function up(): void
    {
        // 1. Drop existing triggers if present
        DB::unprepared("DROP TRIGGER IF EXISTS trg_freeze_jd_details_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_freeze_jd_details_update");

        // 2. Create INSERT freeze trigger
        DB::unprepared("
            CREATE TRIGGER trg_freeze_jd_details_insert
            BEFORE INSERT ON jd_details
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Legacy jd_details table is FROZEN under Talent Taxonomy V2 architecture. Mutations must target taxonomy_designations and taxonomy_jd_templates.';
            END
        ");

        // 3. Create UPDATE freeze trigger
        DB::unprepared("
            CREATE TRIGGER trg_freeze_jd_details_update
            BEFORE UPDATE ON jd_details
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Legacy jd_details table is FROZEN under Talent Taxonomy V2 architecture. Mutations must target taxonomy_designations and taxonomy_jd_templates.';
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_freeze_jd_details_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_freeze_jd_details_update");
    }
};
