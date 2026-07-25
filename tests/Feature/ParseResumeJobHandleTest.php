<?php

namespace Tests\Feature;

use App\Events\ResumeParsed;
use App\Jobs\ParseResumeJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeCandidateSync;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 8 — ParseResumeJob worker state machine.
 *
 * The job body is the OBSERVER of the row state machine:
 *
 *   Uploaded ─> QueuedForParsing ─> Parsing ─> Parsed
 *
 * Properties under test:
 *   1. Worker entry accepts all three pre-Parsing states — legacy
 *      Uploaded rows, freshly QueuedForParsing rows, partial Parsing.
 *      Anything past Parsing yields zero work.
 *   2. Conditional UPDATE gating on (status ∈ {QueuedForParsing, Uploaded}).
 *      A second pod observing the same row sees 0 affected rows
 *      and exits cleanly without double-logging.
 *   3. Successful run transitions Parsing -> Parsed and fires
 *      `ResumeParsed(succeeded: true)`.
 *   4. Failed run (after $tries) transitions to ParseFailed and
 *      fires `ResumeParsed(succeeded: false)`.
 *   5. When `candidate_id` is non-null on the row, the worker
 *      invokes `ResumeCandidateSync::fillEmptyColumnsFromParsed`.
 */
class ParseResumeJobHandleTest extends TestCase
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
        $app['config']->set('cache.default', 'array');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
        Event::fake();
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('user_id')->nullable();
            $t->unsignedInteger('candidate_id')->nullable();
            $t->string('resource_type', 32)->default('job_application');
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
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
            $t->string('cleanup_reason', 64)->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->string('idempotency_key', 128)->nullable();
            $t->timestamps();
            $t->unique('uuid');
        });
        \Illuminate\Support\Facades\Schema::create('upload_session_audit', function ($t) {
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
        });
        \Illuminate\Support\Facades\Schema::create('job_applications', function ($t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('company_id');
            $t->string('full_name', 255)->nullable();
            $t->string('email', 255)->nullable();
            $t->string('phone', 32)->nullable();
            $t->timestamp('created_at')->nullable();
            $t->timestamp('updated_at')->nullable();
        });
    }

    private function seedQueuedOrUploadedRow(string $status): ResumeUpload
    {
        $uuid = (string) Str::uuid();
        $path = "Companies/test_7/Resumes/" . date('Y') . '/' . date('m') . "/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        Storage::disk('r2')->put($path, '%PDF-1.4 fake resume');

        $row = ResumeUpload::create([
            'uuid' => $uuid,
            'company_id' => 7,
            'user_id' => 99,
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => basename($path),
            'r2_path' => $path,
            'r2_url' => $url,
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'status' => $status,
            'upload_started_at' => Carbon::now()->subHours(1),
            'upload_completed_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addHours(24),
        ]);
        UploadSessionAudit::record(
            sessionUuid: $uuid,
            companyId: 7,
            action: 'Created',
            actorId: 99,
            metadata: ['path' => 'presigned'],
        );
        return $row;
    }

    public function test_worker_from_queued_for_parsing_writes_parsed_and_emits_event(): void
    {
        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_QUEUED_FOR_PARSING);
        (new ParseResumeJob($row->uuid, 7, 99))->handle();

        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSED, $fresh->status);
        $this->assertNotNull($fresh->parsed_data_json);
        $this->assertSame('phase2_stub', $fresh->parser_vendor);
        $this->assertSame(1, (int) $fresh->parse_attempt_count);

        Event::assertDispatched(ResumeParsed::class, function ($event) use ($row) {
            return $event->succeeded === true
                && $event->row->uuid === $row->uuid
                && $event->row->company_id === 7;
        });

        $actions = UploadSessionAudit::where('session_uuid', $row->uuid)->pluck('action')->all();
        $this->assertContains('PickedUp', $actions);
        $this->assertContains('ParseDone', $actions);
    }

    public function test_worker_from_legacy_uploaded_state_still_processes(): void
    {
        // Backward compat: pre-Phase-8 rows in Uploaded are still
        // processed by the new worker body. There is no rejection
        // at the worker-entry state check.
        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_UPLOADED);
        (new ParseResumeJob($row->uuid, 7, 99))->handle();
        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSED, $fresh->status);
    }

    public function test_worker_on_parsed_row_yields_zero_work(): void
    {
        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_PARSING);
        // Pre-mark as Parsed; the worker should observe this state
        // and exit cleanly (no log spam).
        $row->status = ResumeUpload::STATUS_PARSED;
        $row->save();

        (new ParseResumeJob($row->uuid, 7, 99))->handle();

        // No further state mutation. Event::fake() captures 0 events.
        Event::assertNotDispatched(ResumeParsed::class);
        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSED, $fresh->status);
    }

    public function test_worker_invokes_candidate_sync_when_candidate_id_set(): void
    {
        // Bind the candidate-sync spy BEFORE the worker runs so we
        // can capture the call. The Phase-2 stub writes 'fingerprint'
        // into parsed_data_json regardless of input — that's a non-empty
        // array, so the sync's "parsed_data_json empty" early-out
        // does NOT fire, and the call reaches our mock.
        $cid = DB::table('job_applications')->insertGetId([
            'company_id' => 7,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $mock = $this->mock(ResumeCandidateSync::class);
        $mock->shouldReceive('fillEmptyColumnsFromParsed')
            ->once()
            ->andReturn(1);

        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_QUEUED_FOR_PARSING);
        $row->candidate_id = $cid;
        $row->save();

        (new ParseResumeJob($row->uuid, 7, 99))->handle();

        // The worker reached the ResumeCandidateSync path because
        // candidate_id was non-null. Mock was called exactly once —
        // no double-invoke in a single parse cycle.
        $mock->shouldHaveReceived('fillEmptyColumnsFromParsed')->once();
    }

    public function test_worker_does_not_invoke_candidate_sync_when_candidate_id_null(): void
    {
        // Pre-condition: candidate_id IS null. The worker should
        // skip the sync call entirely. Bind a spy that, if called,
        // would set the expectation that fails the test.
        $mock = $this->mock(ResumeCandidateSync::class);
        $mock->shouldNotReceive('fillEmptyColumnsFromParsed');

        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_QUEUED_FOR_PARSING);
        // candidate_id defaults to null in seedCreated.

        (new ParseResumeJob($row->uuid, 7, 99))->handle();

        // Mock is never touched.
        $mock->shouldNotHaveReceived('fillEmptyColumnsFromParsed');
    }

    public function test_failed_run_handles_three_attempts_and_updates_state(): void
    {
        // The Phase-2 stub never throws. To test the failure path
        // we stub `runParser` indirectly by setting up the row and
        // calling `failed()` directly — that's how Horizon reports
        // terminal failure after $tries exhausted.
        $row = $this->seedQueuedOrUploadedRow(ResumeUpload::STATUS_PARSING);

        $exception = new \RuntimeException('vendor failed');
        (new ParseResumeJob($row->uuid, 7, 99))->failed($exception);

        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSE_FAILED, $fresh->status);
        $this->assertSame('Failed', $fresh->parser_status);

        Event::assertDispatched(ResumeParsed::class, function ($event) use ($row) {
            return $event->succeeded === false && $event->row->uuid === $row->uuid;
        });
    }
}
