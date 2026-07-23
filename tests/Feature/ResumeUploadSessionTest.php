<?php

namespace Tests\Feature;

use App\Jobs\CleanupOrphanUploadsJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Phase-1 test for the Resume Upload Session migration.
 *
 * Connection strategy:
 *   - getEnvironmentSetUp overrides database.default to sqlite + :memory:.
 *   - setUp() rebuilds the Phase 1 schema on this in-memory database and
 *     truncates it before each test.
 *   - No RefreshDatabase trait because the project's existing migration
 *     catalog (e.g. carts table) is MySQL-specific and breaks under sqlite.
 *
 * Each test class in the project gets a fresh app (Laravel creates one per
 * class via the framework), so this override is isolated to ResumeUploadSessionTest.
 */
class ResumeUploadSessionTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildSchema();
        DB::table('upload_session_audit')->delete();
        DB::table('resume_uploads')->delete();
        Storage::fake('r2');
    }

    private function buildSchema(): void
    {
        // Probe to confirm we are talking to SQLite before buildSchema uses
        // SQLite-only features. If the override didn't take effect, this
        // throws and the failure mode is obvious.
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'sqlite') {
            throw new \RuntimeException("ResumeUploadSessionTest expected sqlite, got $driver");
        }

        Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('user_id')->nullable();
            $t->unsignedInteger('job_id')->nullable();
            $t->unsignedInteger('candidate_id')->nullable();
            $t->char('parent_session_uuid', 36)->nullable();
            $t->char('replaces_session_uuid', 36)->nullable();
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
            $t->char('dedupe_hash', 64)->nullable();
            $t->string('status', 32)->default('Created');
            $t->string('parser_vendor', 64)->nullable();
            $t->string('parser_status', 32)->nullable();
            $t->unsignedTinyInteger('parse_attempt_count')->default(0);
            $t->text('parsed_data_json')->nullable();
            $t->timestamp('upload_started_at')->nullable();
            $t->timestamp('upload_completed_at')->nullable();
            $t->timestamp('parse_started_at')->nullable();
            $t->timestamp('parse_completed_at')->nullable();
            $t->timestamp('linked_at')->nullable();
            $t->unsignedTinyInteger('cleanup_attempt_count')->default(0);
            $t->timestamp('expires_at')->nullable();
            $t->string('idempotency_key', 128)->nullable();
            $t->timestamps();
            $t->unique('uuid');
            $t->index(['status', 'expires_at'], 'idx_status_expires');
            $t->index(['company_id', 'user_id', 'job_id'], 'idx_company_user_job');
            $t->index('candidate_id', 'idx_candidate');
            $t->index(['company_id', 'idempotency_key'], 'idx_company_idempotency');
        });

        Schema::create('upload_session_audit', function ($t) {
            $t->bigIncrements('id');
            $t->char('session_uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('action', 32);
            $t->unsignedInteger('actor_id')->nullable();
            $t->string('actor_ip', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->char('request_id', 36)->nullable();
            $t->text('metadata_json')->nullable();
            $t->unsignedInteger('latency_ms')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['session_uuid', 'created_at'], 'idx_session_time');
            $t->index(['company_id', 'action', 'created_at'], 'idx_company_action_time');
        });
    }

    private function seedParsedRow(array $overrides = []): ResumeUpload
    {
        return ResumeUpload::create(array_merge([
            'uuid' => '11111111-1111-1111-1111-111111111111',
            'company_id' => 7,
            'user_id' => 99,
            'job_id' => 1,
            'candidate_id' => null,
            'original_filename' => 'cv.pdf',
            'stored_filename' => 'JERO_test.pdf',
            'r2_path' => 'Companies/test_co_7/Resumes/'.date('Y').'/'.date('m').'/JERO_test.pdf',
            'r2_url' => 'https://file.jero.works/Companies/test_co_7/Resumes/'.date('Y').'/'.date('m').'/JERO_test.pdf',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 12345,
            'status' => ResumeUpload::STATUS_PARSED,
            'upload_started_at' => now()->subHour(),
            'upload_completed_at' => now()->subHour(),
            'expires_at' => now()->subMinutes(5),
        ], $overrides));
    }

    public function test_create_then_transition_to_uploading_is_legal(): void
    {
        $row = ResumeUpload::create([
            'uuid' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
            'company_id' => 1,
            'user_id' => 1,
            'original_filename' => 'cv.pdf',
            'stored_filename' => 'JERO_test.pdf',
            'r2_path' => 'Companies/c_1/Resumes/'.date('Y').'/'.date('m').'/JERO_test.pdf',
            'r2_url' => 'https://file.jero.works/Companies/c_1/Resumes/'.date('Y').'/'.date('m').'/JERO_test.pdf',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1234,
            'status' => ResumeUpload::STATUS_CREATED,
        ]);
        $row->transitionTo(ResumeUpload::STATUS_UPLOADING, actorId: 1);
        $this->assertSame(ResumeUpload::STATUS_UPLOADING, $row->fresh()->status);
        $this->assertSame(1, UploadSessionAudit::where('session_uuid', $row->uuid)->count());
    }

    public function test_illegal_transition_is_rejected_with_audit_dry_run_intact(): void
    {
        $row = $this->seedParsedRow();
        // Parsed -> Completed is NOT a legal transition; you cannot skip Linked.
        $this->expectException(\RuntimeException::class);
        try {
            $row->transitionTo(ResumeUpload::STATUS_COMPLETED);
        } finally {
            $this->assertSame(0, UploadSessionAudit::where('session_uuid', $row->uuid)->count(),
                'No audit row must be written on a failed transition');
            $this->assertSame(ResumeUpload::STATUS_PARSED, $row->fresh()->status,
                'Status must not have changed on a rejected transition');
        }
    }

    public function test_legal_transition_to_linked_writes_audit_and_persists_candidate_id(): void
    {
        $row = $this->seedParsedRow();
        $row->transitionTo(ResumeUpload::STATUS_LINKED, candidateId: 555, actorId: 99,
            metadata: ['job_application_id' => 555]);
        $this->assertSame(ResumeUpload::STATUS_LINKED, $row->fresh()->status);
        $this->assertSame(555, (int) $row->fresh()->candidate_id);
        $audit = UploadSessionAudit::where('session_uuid', $row->uuid)->where('action', 'Linked')->first();
        $this->assertNotNull($audit);
        $this->assertSame(99, (int) $audit->actor_id);
        $this->assertSame(['from' => 'Parsed', 'to' => 'Linked', 'job_application_id' => 555], $audit->metadata_json);
    }

    public function test_audit_table_is_append_only_at_model_level(): void
    {
        $row = $this->seedParsedRow();
        $row->transitionTo(ResumeUpload::STATUS_LINKED, candidateId: 1, actorId: 99);
        $audit = UploadSessionAudit::where('session_uuid', $row->uuid)->first();
        UpdateAppendOnly: {
            try {
                $audit->update(['action' => 'Tampered']);
                $this->fail('Audit update should have been blocked');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('append-only', $e->getMessage());
            }
            try {
                $audit->delete();
                $this->fail('Audit delete should have been blocked');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('append-only', $e->getMessage());
            }
        }
    }

    public function test_tenant_scoped_lookup_throws_for_wrong_company(): void
    {
        $this->seedParsedRow(['company_id' => 7]);
        $this->expectException(\RuntimeException::class);
        ResumeUpload::forCompany('11111111-1111-1111-1111-111111111111', 999);
    }

    public function test_tenant_scoped_lookup_returns_row_for_correct_company(): void
    {
        $this->seedParsedRow(['company_id' => 7]);
        $row = ResumeUpload::forCompany('11111111-1111-1111-1111-111111111111', 7);
        $this->assertSame(7, (int) $row->company_id);
    }

    public function test_can_transition_to_method(): void
    {
        $row = $this->seedParsedRow(['status' => ResumeUpload::STATUS_PARSED]);
        $this->assertTrue($row->canTransitionTo(ResumeUpload::STATUS_LINKED));
        $this->assertTrue($row->canTransitionTo(ResumeUpload::STATUS_REPLACED));
        $this->assertTrue($row->canTransitionTo(ResumeUpload::STATUS_CLEANING));
        $this->assertFalse($row->canTransitionTo(ResumeUpload::STATUS_COMPLETED), 'Parsed must not skip Linked');
        $this->assertFalse($row->canTransitionTo('GarbageState'));
    }

    public function test_terminal_states_have_no_outgoing_transitions(): void
    {
        $expired = ResumeUpload::create([
            'uuid' => '22222222-2222-2222-2222-222222222222',
            'company_id' => 1,
            'original_filename' => 'x',
            'stored_filename' => 'x',
            'r2_path' => 'x',
            'r2_url' => 'x',
            'mime_type' => 'x',
            'file_size_bytes' => 1,
            'status' => ResumeUpload::STATUS_EXPIRED,
        ]);
        $this->assertTrue($expired->isTerminal());
        $this->assertFalse($expired->canTransitionTo(ResumeUpload::STATUS_PARSED));
    }

    public function test_set_expires_from_config_uses_upload_completed_at_when_present(): void
    {
        $row = $this->seedParsedRow([
            'upload_started_at' => now()->subHours(2),
            'upload_completed_at' => now()->subHour(),
        ]);
        $row->status = ResumeUpload::STATUS_UPLOADED;
        $row->setExpiresFromConfig(24);
        $this->assertNotNull($row->expires_at);
        $this->assertEqualsWithDelta(
            23 * 3600,
            now()->diffInSeconds($row->expires_at, false),
            60,
            'expires_at must equal upload_completed_at + 24h',
        );
    }

    // -------------------------------------------------------------
    // CleanupOrphanUploadsJob
    // -------------------------------------------------------------

    public function test_cleanup_job_claims_parsed_row_with_expired_date_and_deletes_row_and_r2(): void
    {
        $row = $this->seedParsedRow();
        Storage::disk('r2')->put($row->r2_path, 'fake-content');

        $job = new CleanupOrphanUploadsJob();
        $job->maxDeletesPerRun = 10;
        $job->handle();

        Storage::disk('r2')->assertMissing($row->r2_path);
        $this->assertNull(
            DB::table('resume_uploads')->where('id', $row->id)->first(),
            'Row must be hard-deleted after CleaningDone'
        );
        $cleaningDone = UploadSessionAudit::where('session_uuid', $row->uuid)
            ->where('action', 'CleaningDone')->first();
        $this->assertNotNull($cleaningDone);
    }

    public function test_cleanup_job_skips_rows_with_no_expiry_or_non_terminal_status(): void
    {
        $row = $this->seedParsedRow(['expires_at' => null]);
        Storage::disk('r2')->put($row->r2_path, 'fake');
        $job = new CleanupOrphanUploadsJob();
        $job->maxDeletesPerRun = 10;
        $job->handle();
        $this->assertNotNull(DB::table('resume_uploads')->where('id', $row->id)->first(),
            'Row without expires_at must NOT be GCd');
        Storage::disk('r2')->assertExists($row->r2_path);
    }

    public function test_cleanup_job_skips_linked_even_if_expired(): void
    {
        $row = $this->seedParsedRow([
            'status' => ResumeUpload::STATUS_LINKED,
            'candidate_id' => 1234,
            'expires_at' => now()->subDay(),
        ]);
        Storage::disk('r2')->put($row->r2_path, 'fake');
        $job = new CleanupOrphanUploadsJob();
        $job->maxDeletesPerRun = 10;
        $job->handle();
        $this->assertNotNull(DB::table('resume_uploads')->where('id', $row->id)->first());
        Storage::disk('r2')->assertExists($row->r2_path);
    }

    public function test_cleanup_job_handles_already_missing_r2_gracefully(): void
    {
        $row = $this->seedParsedRow();
        $job = new CleanupOrphanUploadsJob();
        $job->maxDeletesPerRun = 10;
        $job->handle();
        $this->assertNull(DB::table('resume_uploads')->where('id', $row->id)->first());
    }
}
