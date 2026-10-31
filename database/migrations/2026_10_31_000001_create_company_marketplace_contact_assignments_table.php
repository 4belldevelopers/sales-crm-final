<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration creating company_marketplace_contact_assignments table and seeding historical data.
 *
 * Intent: Authoritative, normalized persistence for Marketplace Contact Visibility and Representative
 * assignments across JERO Marketplace (Sales & Client Enquiries, Agency Enquiries, Bench Talent Enquiries).
 *
 * Invariants Enforced:
 * 1. Single Active Primary per (company_id, channel) via virtual generated column + unique index.
 * 2. Granular per-assignment visibility (public vs private).
 * 3. Foreign key referential integrity with cascading cleanup when company or user is deleted.
 * 4. Temporal delegation support via effective_from and effective_until.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('company_marketplace_contact_assignments')) {
            Schema::create('company_marketplace_contact_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id');
                $table->unsignedInteger('user_id');
                $table->enum('channel', ['client', 'agency', 'bench'])
                    ->comment('Marketplace communication purpose');
                $table->enum('visibility', ['public', 'private'])
                    ->default('public')
                    ->comment('Granular per-channel PII exposure');
                $table->enum('priority', ['primary', 'secondary', 'backup'])
                    ->default('secondary');
                $table->boolean('is_active')->default(true);
                $table->timestamp('effective_from')->nullable();
                $table->timestamp('effective_until')->nullable();
                $table->timestamps();

                // Unique assignment per user per channel in a company
                $table->unique(['company_id', 'channel', 'user_id'], 'uk_comp_channel_user');
                $table->index(['company_id', 'channel', 'priority', 'is_active'], 'idx_comp_channel_priority');
                $table->index(['user_id', 'is_active'], 'idx_user_lookup');

                $table->foreign('company_id', 'fk_cmca_company')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade');

                $table->foreign('user_id', 'fk_cmca_user')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });

            // Add virtual generated column & unique constraint for database-level Single-Primary invariant
            DB::statement("
                ALTER TABLE `company_marketplace_contact_assignments`
                ADD COLUMN `primary_enforcer` VARCHAR(32) GENERATED ALWAYS AS (
                    CASE WHEN `priority` = 'primary' AND `is_active` = 1 THEN `channel` ELSE NULL END
                ) VIRTUAL AFTER `updated_at`,
                ADD UNIQUE KEY `uk_comp_channel_primary` (`company_id`, `primary_enforcer`)
            ");
        }

        // -------------------------------------------------------------------------
        // Historical Data Backfill: Migrate existing rep:* and rep_prio:* from company_tags
        // -------------------------------------------------------------------------
        $this->seedFromLegacyCompanyTags();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_marketplace_contact_assignments');
    }

    /**
     * Seeds initial records from legacy company_tags safely.
     */
    protected function seedFromLegacyCompanyTags(): void
    {
        $tags = DB::table('company_tags')
            ->where(function ($q) {
                $q->where('tag_name', 'like', 'rep:client:%')
                    ->orWhere('tag_name', 'like', 'rep:agency:%')
                    ->orWhere('tag_name', 'like', 'rep:vendor:%')
                    ->orWhere('tag_name', 'like', 'rep:recruitment:%')
                    ->orWhere('tag_name', 'like', 'rep_prio:%');
            })
            ->get();

        if ($tags->isEmpty()) {
            return;
        }

        // 1. Group priorities: [ companyId => [ uid => [ channel => priority ] ] ]
        $prioMap = [];
        // 2. Group channel tags: [ companyId => [ channel => [ user_ids ] ] ]
        $channelUsers = [];

        foreach ($tags as $row) {
            $companyId = (int) $row->company_id;
            if ($companyId <= 0) {
                continue;
            }

            $tagName = $row->tag_name;

            // Handle rep_prio:{channel}:{uid}:{priority}
            if (str_starts_with($tagName, 'rep_prio:')) {
                $parts = explode(':', $tagName);
                if (count($parts) === 4) {
                    $rawCh = strtolower(trim($parts[1]));
                    $ch = match ($rawCh) {
                        'client' => 'client',
                        'agency', 'vendor' => 'agency',
                        'recruitment', 'bench' => 'bench',
                        default => null,
                    };
                    $uid = (int) $parts[2];
                    $prio = strtolower(trim($parts[3]));
                    $priority = match ($prio) {
                        'primary' => 'primary',
                        'backup' => 'backup',
                        default => 'secondary',
                    };

                    if ($ch && $uid > 0) {
                        $prioMap[$companyId][$uid][$ch] = $priority;
                    }
                }

                continue;
            }

            // Handle rep:{channel}:{uid}
            if (str_starts_with($tagName, 'rep:')) {
                $parts = explode(':', $tagName);
                if (count($parts) === 3) {
                    $rawCh = strtolower(trim($parts[1]));
                    $ch = match ($rawCh) {
                        'client' => 'client',
                        'agency', 'vendor' => 'agency',
                        'recruitment', 'bench' => 'bench',
                        default => null,
                    };
                    $uid = (int) $parts[2];

                    if ($ch && $uid > 0) {
                        $channelUsers[$companyId][$ch][] = $uid;
                    }
                }
            }
        }

        $now = now();
        $primaryClaimed = []; // [ companyId => [ channel => true ] ]

        foreach ($channelUsers as $companyId => $channels) {
            foreach ($channels as $ch => $uids) {
                $uniqueUids = array_unique(array_filter($uids));

                foreach ($uniqueUids as $uid) {
                    // Verify user exists and belongs to company
                    $userExists = DB::table('users')
                        ->where('id', $uid)
                        ->where('company_id', $companyId)
                        ->exists();

                    if (! $userExists) {
                        continue;
                    }

                    $assignedPrio = $prioMap[$companyId][$uid][$ch] ?? 'secondary';

                    // Enforce database Single-Primary invariant during backfill
                    if ($assignedPrio === 'primary') {
                        if (! empty($primaryClaimed[$companyId][$ch])) {
                            // Another user is already primary; demote to secondary
                            $assignedPrio = 'secondary';
                        } else {
                            $primaryClaimed[$companyId][$ch] = true;
                        }
                    }

                    DB::table('company_marketplace_contact_assignments')->insertOrIgnore([
                        'company_id' => $companyId,
                        'user_id' => $uid,
                        'channel' => $ch,
                        'visibility' => 'public',
                        'priority' => $assignedPrio,
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
};
