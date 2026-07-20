<?php

namespace Tests\Feature\OwnershipEngine;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase 1 verification: schema only. No engine logic exists yet (Phase 2+) — this test
 * asserts the migrations in database/migrations/2026_07_20_09000{1..5}_*.php produce exactly
 * the structure specified in docs/OWNERSHIP_ENGINE_DESIGN.md §7, as corrected by §22/§28
 * (the two dropped indexes, the binary(16) event_uuid).
 *
 * Requires a real MySQL 8 connection for the `testing` environment, not sqlite: the identity
 * normalization columns use MySQL-specific generated-column expressions (REGEXP_REPLACE),
 * so this suite cannot run against sqlite regardless of how phpunit.xml's DB_CONNECTION is
 * set. Point the testing connection at a real MySQL 8 database before running.
 */
class SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_ownership_events_table_has_the_approved_shape(): void
    {
        $this->assertTrue(Schema::hasTable('ownership_events'));

        $this->assertTrue(Schema::hasColumns('ownership_events', [
            'id', 'event_uuid', 'cluster_id', 'scoping_company_id', 'scope_mode', 'scope_key',
            'normalized_email', 'normalized_phone', 'subject_row_id', 'event_type',
            'decision_rule', 'scenario_ref', 'previous_owner_row_id', 'new_owner_row_id',
            'trigger_source', 'actor_user_id', 'reason', 'event_time', 'created_at', 'updated_at',
        ]));

        $indexes = collect(Schema::getIndexes('ownership_events'))->keyBy('name');
        $this->assertTrue($indexes->has('ownership_events_event_uuid_unique'));
        $this->assertTrue($indexes->has('oe_cluster_replay_idx'));
        $this->assertTrue($indexes->has('oe_subject_row_idx'));

        $this->assertSame(
            ['cluster_id', 'event_time', 'id'],
            $indexes->get('oe_cluster_replay_idx')['columns']
        );
    }

    public function test_ownership_clusters_table_has_the_approved_shape(): void
    {
        $this->assertTrue(Schema::hasTable('ownership_clusters'));

        $this->assertTrue(Schema::hasColumns('ownership_clusters', [
            'id', 'scoping_company_id', 'scope_mode', 'scope_key', 'normalized_email',
            'normalized_phone', 'owner_row_id', 'owner_type', 'decision_rule', 'decided_at',
            'last_event_id', 'created_at', 'updated_at',
        ]));

        $indexes = collect(Schema::getIndexes('ownership_clusters'))->keyBy('name');
        $this->assertTrue($indexes->has('oc_email_identity_unique'));
        $this->assertTrue($indexes->has('oc_phone_identity_unique'));

        // §22/§28 — deliberately not indexed. Asserting its absence keeps this test honest
        // about the "not defensive" decision if the index is ever added without the design
        // doc being updated to justify it first (Implementation Rule #8).
        $this->assertFalse($indexes->has('ownership_clusters_owner_row_id_index'));
    }

    public function test_add_job_wise_condidates_has_the_ownership_cache_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('add_job_wise_condidates', [
            'ownership_state', 'ownership_cluster_id', 'ownership_evaluated_at',
        ]));
    }

    public function test_job_applications_has_identity_columns_but_no_secondary_index(): void
    {
        $this->assertTrue(Schema::hasColumns('job_applications', [
            'email_normalized', 'phone_normalized',
        ]));

        // §22/§28 — the engine never searches job_applications by identity, only reads a
        // known row's own normalized value, so no index is expected on these columns.
        $indexedColumns = collect(Schema::getIndexes('job_applications'))
            ->pluck('columns')
            ->flatten();

        $this->assertFalse($indexedColumns->contains('email_normalized'));
        $this->assertFalse($indexedColumns->contains('phone_normalized'));
    }

    public function test_email_and_phone_normalization_are_computed_by_mysql_itself(): void
    {
        $id = DB::table('job_applications')->insertGetId([
            'submitted_by' => 1,
            'email' => '  Some.User@EXAMPLE.com ',
            'phone' => '+1 (415) 555-0199',
            'created_at' => now(),
        ]);

        $row = DB::table('job_applications')->find($id);

        $this->assertSame('some.user@example.com', $row->email_normalized);
        $this->assertSame('14155550199', $row->phone_normalized);
    }

    public function test_timeline_backfill_index_exists(): void
    {
        $indexes = collect(Schema::getIndexes('timeline'))->keyBy('name');
        $this->assertTrue($indexes->has('timeline_ownership_backfill_idx'));

        $this->assertSame(
            ['application_id', 'job_id', 'master_process_name', 'create_at'],
            $indexes->get('timeline_ownership_backfill_idx')['columns']
        );
    }

    // ---- Phase 8 (Activation Readiness) schema ---------------------------------------

    public function test_ownership_engine_settings_exists_with_a_single_row_defaulting_to_disabled(): void
    {
        $this->assertTrue(Schema::hasTable('ownership_engine_settings'));
        $this->assertTrue(Schema::hasColumns('ownership_engine_settings', [
            'id', 'globally_enabled', 'kill_switch_active', 'kill_switch_reason',
            'kill_switch_activated_at', 'created_at', 'updated_at',
        ]));

        $row = DB::table('ownership_engine_settings')->where('id', 1)->first();
        $this->assertNotNull($row, 'The migration must seed exactly one settings row (id = 1).');

        // Finding 6: the default must be disabled — activation is a deliberate future action.
        $this->assertSame(0, (int) $row->globally_enabled);
        $this->assertSame(0, (int) $row->kill_switch_active);
    }

    public function test_companies_and_jobs_have_nullable_ownership_engine_enabled_override(): void
    {
        $this->assertTrue(Schema::hasColumn('companies', 'ownership_engine_enabled'));
        $this->assertTrue(Schema::hasColumn('jobs', 'ownership_engine_enabled'));

        // Existing rows must default to NULL (inherit), never to a value that silently
        // changes behavior for every pre-existing company/job the moment this migration runs.
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Schema Test Co',
            'company_email' => uniqid().'@example.test',
            'locale' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertNull(DB::table('companies')->where('id', $companyId)->value('ownership_engine_enabled'));
    }

    public function test_shadow_comparisons_table_has_the_approved_shape(): void
    {
        $this->assertTrue(Schema::hasTable('ownership_engine_shadow_comparisons'));
        $this->assertTrue(Schema::hasColumns('ownership_engine_shadow_comparisons', [
            'id', 'subject_row_id', 'scoping_company_id', 'job_id',
            'legacy_wise_duplicate', 'legacy_profile_duplicate',
            'engine_ownership_state', 'engine_owner_row_id', 'engine_decision_rule',
            'category', 'compared_at', 'created_at', 'updated_at',
        ]));
    }
}
