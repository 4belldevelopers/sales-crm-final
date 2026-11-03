<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration creating God-Level Team Invitations, Universal State Versions, and Idempotency Infrastructure.
 *
 * Invariants Enforced:
 * - INV-01: Zero-IDOR opaque token matching on token_hash.
 * - INV-03: Database-enforced active invitation uniqueness via virtual column `active_token_slot` + unique index.
 * - INV-04: Strict decoupling of business lifecycle `status` from transport `delivery_status`.
 * - INV-08 & INV-16: Database-enforced HTTP Idempotency scoped to (tenant_id, invitation_id, idempotency_key).
 * - INV-11 & INV-12: Fail-hard monotonic Universal Domain Versioning across team, jobs, clients, commercials, candidates.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Team Invitations Table
        if (! Schema::hasTable('team_invitations')) {
            Schema::create('team_invitations', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedInteger('company_id');
                $table->string('email', 255);
                $table->char('token_hash', 64)->unique();
                $table->unsignedInteger('role_id');
                $table->string('context_version', 50)->default('TEAM_V2');
                $table->enum('status', ['PENDING', 'LINK_ACCESSED', 'ACTIVATED', 'EXPIRED', 'REVOKED', 'DECLINED'])
                    ->default('PENDING');
                $table->enum('delivery_status', ['QUEUED', 'SENT', 'DELIVERED', 'BOUNCED_SOFT', 'BOUNCED_HARD', 'FAILED'])
                    ->default('QUEUED');
                $table->unsignedInteger('invited_by');
                $table->timestamp('link_accessed_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->timestamp('expires_at');
                $table->timestamps();

                $table->index(['company_id', 'status'], 'idx_team_inv_comp_status');
                $table->index(['email'], 'idx_team_inv_email');

                $table->foreign('company_id', 'fk_team_inv_company')
                    ->references('id')->on('companies')
                    ->onDelete('cascade');
            });

            // Add virtual generated column and unique constraint for active invitation identity (INV-03)
            DB::statement("
                ALTER TABLE `team_invitations` 
                ADD COLUMN `active_token_slot` TINYINT UNSIGNED 
                GENERATED ALWAYS AS (IF(`status` IN ('PENDING', 'LINK_ACCESSED'), 1, NULL)) STORED 
                AFTER `status`
            ");

            DB::statement("
                ALTER TABLE `team_invitations` 
                ADD UNIQUE KEY `uq_team_invitations_active_email` (`company_id`, `email`, `active_token_slot`)
            ");
        }

        // 2. Registration Idempotency Keys Table
        if (! Schema::hasTable('registration_idempotency_keys')) {
            Schema::create('registration_idempotency_keys', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('tenant_id');
                $table->unsignedBigInteger('invitation_id');
                $table->string('idempotency_key', 64);
                $table->string('endpoint', 100);
                $table->char('request_hash', 64);
                $table->enum('status', ['IN_PROGRESS', 'COMPLETED', 'FAILED'])->default('IN_PROGRESS');
                $table->unsignedSmallInteger('response_code')->nullable();
                $table->json('response_body')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('expires_at');

                $table->unique(['tenant_id', 'invitation_id', 'idempotency_key'], 'uq_idempotency_tenant_key');
                $table->index(['expires_at'], 'idx_idempotency_expires');

                $table->foreign('invitation_id', 'fk_idempotency_invitation')
                    ->references('id')->on('team_invitations')
                    ->onDelete('cascade');
            });
        }

        // 3. Team Invitation Audit Logs Table
        if (! Schema::hasTable('team_invitation_audit_logs')) {
            Schema::create('team_invitation_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invitation_id');
                $table->unsignedInteger('actor_id')->nullable();
                $table->string('action', 50);
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->json('payload_snapshot')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['invitation_id'], 'idx_audit_invitation');
                $table->foreign('invitation_id', 'fk_audit_invitation')
                    ->references('id')->on('team_invitations')
                    ->onDelete('cascade');
            });
        }

        // 4. Universal Tenant State Versions Table (INV-12)
        if (! Schema::hasTable('tenant_state_versions')) {
            Schema::create('tenant_state_versions', function (Blueprint $table) {
                $table->unsignedInteger('company_id');
                $table->string('domain', 50);
                $table->unsignedBigInteger('version')->default(1);
                $table->unsignedBigInteger('last_event_id')->nullable();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->primary(['company_id', 'domain'], 'pk_tenant_state_versions');

                $table->foreign('company_id', 'fk_tsv_company')
                    ->references('id')->on('companies')
                    ->onDelete('cascade');
            });

            // Seed initial version rows for all active companies
            $activeCompanyIds = DB::table('companies')->pluck('id');
            $domains = ['team', 'jobs', 'clients', 'commercials', 'candidates'];
            $now = now();

            $seedData = [];
            foreach ($activeCompanyIds as $cid) {
                foreach ($domains as $domain) {
                    $seedData[] = [
                        'company_id'    => $cid,
                        'domain'        => $domain,
                        'version'       => 1,
                        'last_event_id' => null,
                        'updated_at'    => $now,
                    ];
                }

                // Insert in batches of 500
                if (count($seedData) >= 500) {
                    DB::table('tenant_state_versions')->insertOrIgnore($seedData);
                    $seedData = [];
                }
            }

            if (! empty($seedData)) {
                DB::table('tenant_state_versions')->insertOrIgnore($seedData);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_invitation_audit_logs');
        Schema::dropIfExists('registration_idempotency_keys');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('tenant_state_versions');
    }
};
