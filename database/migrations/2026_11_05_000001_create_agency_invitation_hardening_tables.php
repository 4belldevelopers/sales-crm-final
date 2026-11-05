<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CreateAgencyInvitationHardeningTables
 *
 * Implements God-Level forensic hardening for Agency Partnerships:
 * - agency_daily_quotas (20/day quota tracking with row locks)
 * - staffing_company_relation_guards (Read-First serialization table)
 * - staffing_company_invitation additions (token_hash, STORED active_invite_slot unique index)
 * - staffing_company_relation additions (STORED active_relation_slot unique index)
 * - coin_ledger additions (refund_reference_id unique index)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. agency_daily_quotas
        if (!Schema::hasTable('agency_daily_quotas')) {
            Schema::create('agency_daily_quotas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('company_id');
                $table->date('quota_date');
                $table->unsignedInteger('invitations_sent')->default(0);
                $table->timestamps();

                $table->unique(['company_id', 'quota_date'], 'uk_company_date');
                $table->index('company_id', 'idx_quota_company');
            });
        }

        // 2. staffing_company_relation_guards
        if (!Schema::hasTable('staffing_company_relation_guards')) {
            Schema::create('staffing_company_relation_guards', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('company_pair_key', 64);
                $table->timestamps();

                $table->unique('company_pair_key', 'uk_company_pair_key');
            });
        }

        // 3. staffing_company_invitation
        if (Schema::hasTable('staffing_company_invitation')) {
            Schema::table('staffing_company_invitation', function (Blueprint $table) {
                if (!Schema::hasColumn('staffing_company_invitation', 'token_hash')) {
                    $table->string('token_hash', 64)->nullable()->after('to_invitation_status');
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'expires_at')) {
                    $table->dateTime('expires_at')->nullable()->after('token_hash');
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'consumed_at')) {
                    $table->dateTime('consumed_at')->nullable()->after('expires_at');
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'delivery_status')) {
                    $table->enum('delivery_status', ['PENDING', 'SENT', 'DELIVERED', 'BOUNCED'])->default('PENDING')->after('consumed_at');
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'normalized_recipient')) {
                    $table->string('normalized_recipient', 191)->nullable()->after('delivery_status');
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
                if (!Schema::hasColumn('staffing_company_invitation', 'updated_at')) {
                    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                }
            });

            // Backfill normalized_recipient for historical rows
            DB::statement("UPDATE staffing_company_invitation SET normalized_recipient = LOWER(TRIM(to_email)) WHERE normalized_recipient IS NULL AND to_email IS NOT NULL");

            // Add STORED generated column and unique index if not present
            $hasActiveSlot = DB::select("SHOW COLUMNS FROM staffing_company_invitation LIKE 'active_invite_slot'");
            if (empty($hasActiveSlot)) {
                DB::statement("
                    ALTER TABLE staffing_company_invitation 
                    ADD COLUMN active_invite_slot VARCHAR(255) 
                    GENERATED ALWAYS AS (
                        CASE WHEN to_invitation_status = 0 THEN CONCAT(from_company_id, ':', normalized_recipient) ELSE NULL END
                    ) STORED AFTER normalized_recipient
                ");
            }

            $indexes = collect(DB::select("SHOW INDEX FROM staffing_company_invitation"))->pluck('Key_name')->all();
            if (!in_array('uk_tenant_active_slot', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation ADD UNIQUE KEY uk_tenant_active_slot (active_invite_slot)");
            }
            if (!in_array('idx_invitation_token_hash', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation ADD INDEX idx_invitation_token_hash (token_hash)");
            }
            if (!in_array('idx_invitation_from_status', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation ADD INDEX idx_invitation_from_status (from_company_id, to_invitation_status)");
            }
        }

        // 4. staffing_company_relation
        if (Schema::hasTable('staffing_company_relation')) {
            $hasActiveRelSlot = DB::select("SHOW COLUMNS FROM staffing_company_relation LIKE 'active_relation_slot'");
            if (empty($hasActiveRelSlot)) {
                DB::statement("
                    ALTER TABLE staffing_company_relation 
                    ADD COLUMN active_relation_slot VARCHAR(64) 
                    GENERATED ALWAYS AS (
                        CASE WHEN final_status = 1 THEN CONCAT(from_company_id, ':', to_company_id) ELSE NULL END
                    ) STORED AFTER final_status
                ");
            }

            $relIndexes = collect(DB::select("SHOW INDEX FROM staffing_company_relation"))->pluck('Key_name')->all();
            if (!in_array('uk_active_relation_slot', $relIndexes)) {
                DB::statement("ALTER TABLE staffing_company_relation ADD UNIQUE KEY uk_active_relation_slot (active_relation_slot)");
            }
        }

        // 5. coin_ledger
        if (Schema::hasTable('coin_ledger')) {
            if (!Schema::hasColumn('coin_ledger', 'refund_reference_id')) {
                Schema::table('coin_ledger', function (Blueprint $table) {
                    $table->string('refund_reference_id', 64)->nullable()->after('idempotency_key');
                });
            }

            $ledgerIndexes = collect(DB::select("SHOW INDEX FROM coin_ledger"))->pluck('Key_name')->all();
            if (!in_array('uk_single_refund', $ledgerIndexes)) {
                DB::statement("ALTER TABLE coin_ledger ADD UNIQUE KEY uk_single_refund (refund_reference_id)");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('coin_ledger')) {
            $ledgerIndexes = collect(DB::select("SHOW INDEX FROM coin_ledger"))->pluck('Key_name')->all();
            if (in_array('uk_single_refund', $ledgerIndexes)) {
                DB::statement("ALTER TABLE coin_ledger DROP INDEX uk_single_refund");
            }
            if (Schema::hasColumn('coin_ledger', 'refund_reference_id')) {
                Schema::table('coin_ledger', function (Blueprint $table) {
                    $table->dropColumn('refund_reference_id');
                });
            }
        }

        if (Schema::hasTable('staffing_company_relation')) {
            $relIndexes = collect(DB::select("SHOW INDEX FROM staffing_company_relation"))->pluck('Key_name')->all();
            if (in_array('uk_active_relation_slot', $relIndexes)) {
                DB::statement("ALTER TABLE staffing_company_relation DROP INDEX uk_active_relation_slot");
            }
            $hasActiveRelSlot = DB::select("SHOW COLUMNS FROM staffing_company_relation LIKE 'active_relation_slot'");
            if (!empty($hasActiveRelSlot)) {
                DB::statement("ALTER TABLE staffing_company_relation DROP COLUMN active_relation_slot");
            }
        }

        if (Schema::hasTable('staffing_company_invitation')) {
            $indexes = collect(DB::select("SHOW INDEX FROM staffing_company_invitation"))->pluck('Key_name')->all();
            if (in_array('uk_tenant_active_slot', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation DROP INDEX uk_tenant_active_slot");
            }
            if (in_array('idx_invitation_token_hash', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation DROP INDEX idx_invitation_token_hash");
            }
            if (in_array('idx_invitation_from_status', $indexes)) {
                DB::statement("ALTER TABLE staffing_company_invitation DROP INDEX idx_invitation_from_status");
            }
            $hasActiveSlot = DB::select("SHOW COLUMNS FROM staffing_company_invitation LIKE 'active_invite_slot'");
            if (!empty($hasActiveSlot)) {
                DB::statement("ALTER TABLE staffing_company_invitation DROP COLUMN active_invite_slot");
            }
            Schema::table('staffing_company_invitation', function (Blueprint $table) {
                $cols = ['token_hash', 'expires_at', 'consumed_at', 'delivery_status', 'normalized_recipient'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('staffing_company_invitation', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('staffing_company_relation_guards');
        Schema::dropIfExists('agency_daily_quotas');
    }
};
