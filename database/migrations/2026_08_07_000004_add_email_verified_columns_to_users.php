<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5 task 5.11 — Users email-verification hardening (audit C2, M3, L1).
 *
 * Changes:
 *   1. Nullify empty-string email_verification_code rows so the UNIQUE index can be added.
 *   2. Add email_verified_at TIMESTAMP NULL — standard Laravel MustVerifyEmail contract.
 *   3. Add email_verification_expires_at TIMESTAMP NULL — server-side expiry fallback (sprint 5.10).
 *   4. Add UNIQUE index on email_verification_code — prevents token-collision attacks (audit L1).
 *   5. Populate email_verified_at for existing active users whose code is already cleared.
 *
 * Zero-downtime: column adds are instant on InnoDB; UNIQUE index uses ALGORITHM=INPLACE, LOCK=NONE.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: coerce empty-string codes to NULL so the UNIQUE constraint works.
        // (Active verified users get email_verification_code='' from the old controller;
        //  multiple empty strings would violate UNIQUE. MySQL allows multiple NULLs.)
        DB::table('users')
            ->where('email_verification_code', '')
            ->update(['email_verification_code' => null]);

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email_verification_code');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verified_at');
        });

        // Step 2: back-fill email_verified_at for users who are already active with no pending token.
        DB::table('users')
            ->where('status', 'active')
            ->whereNull('email_verification_code')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);

        // Step 3: add UNIQUE index — duplicate guard already resolved in step 1.
        $hasDuplicates = DB::table('users')
            ->select('email_verification_code', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('email_verification_code')
            ->groupBy('email_verification_code')
            ->having('cnt', '>', 1)
            ->exists();

        if (! $hasDuplicates) {
            DB::statement('ALTER TABLE users ADD UNIQUE KEY users_email_verification_code_unique (email_verification_code), ALGORITHM=INPLACE, LOCK=NONE');
        } else {
            \Log::warning('V2Registration 5.11 migration: skipped users.email_verification_code UNIQUE — duplicate tokens found. Resolve and re-run.');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_email_verification_code_unique'")) {
                $table->dropUnique('users_email_verification_code_unique');
            }
            $table->dropColumn(['email_verified_at', 'email_verification_expires_at']);
        });
    }
};
