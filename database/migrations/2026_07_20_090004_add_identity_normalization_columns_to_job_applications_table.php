<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ownership Engine Phase 1 (schema only — see docs/OWNERSHIP_ENGINE_DESIGN.md §7.4, §22, §28).
// Stored generated columns give the engine one authoritative normalization, computed by
// MySQL itself, instead of normalizing in PHP at query time (which risked drifting out of
// sync with whatever the query itself normalized). Deliberately NOT indexed here (§22/§28
// correction): the Ownership Engine always starts from a known add_job_wise_condidates row
// and reads that row's own normalized value — it never searches job_applications broadly by
// email/phone, so a secondary index on these columns would be defensive, not justified.
// (checkDuplicateCandidate(), a separate, untouched function, is unaffected either way.)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('email_normalized', 190)
                ->storedAs('LOWER(TRIM(email))')
                ->nullable()
                ->after('email');

            $table->string('phone_normalized', 32)
                ->storedAs("REGEXP_REPLACE(phone, '[^0-9]', '')")
                ->nullable()
                ->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['email_normalized', 'phone_normalized']);
        });
    }
};
