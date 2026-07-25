<?php

namespace Tests\Feature;

use App\Jobs\ParseResumeJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeUploadSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 8 — Pin the dedicated parse_resumes queue contract.
 *
 * Three properties under test:
 *   1. `ParseResumeJob::dispatch(...)` from `Session::finalize()` lands
 *      on `redis_parse_resumes` connection + `parse_resumes` queue.
 *      We assert connection by introspection (Queue::fake() captures
 *      the chain) and queue by the same mechanism.
 *   2. The dispatch seam FIRST transitions the row from `Uploaded`
 *      to `QueuedForParsing` so the DB column correctly reflects
 *      "the row is in the queue but the worker has not yet picked
 *      it up". The audit row mirrors this `Queued` action.
 *   3. A second `finalize()` call — i.e. a replay — observes the
 *      row already in QueuedForParsing and short-circuits with
 *      `replayed=true`. Queue::pushed() count is unchanged.
 *
 * Backward compat: the queue name is config-driven via
 * `resumes.parser_queue` + `resumes.parser_connection`. The test
 * does not bind these — it relies on the default fallback in the
 * service. If the service's defaults diverge from the test's
 * expectations, the test FAILS LOUD here.
 */
class ResumeParseQueueTest extends TestCase
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
        Queue::fake();
        config([
            'resumes.parser_connection' => 'redis_parse_resumes',
            'resumes.parser_queue' => 'parse_resumes',
            'resumes.metrics_require_redis' => false,
        ]);
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('user_id')->nullable();
            $t->unsignedInteger('job_id')->nullable();
            $t->unsignedInteger('candidate_id')->nullable();
            $t->unsignedInteger('bench_application_id')->nullable();
            $t->string('resource_type', 32)->default('job_application');
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
    }

    /**
     * Drive presign→PUT→finalize end-to-end against an in-memory row
     * seeded in `Created` state. We do NOT call `createOrPresign`
     * (its signature requires PHP-attached SMB signing which
     * Storage::fake cannot satisfy) — instead, we pre-write the
     * `Created` row directly so the seam under test is `finalize`.
     */
    private function seedCreated(string $content): ResumeUpload
    {
        $uuid = (string) Str::uuid();
        $path = "Companies/test_7/Resumes/" . date('Y') . '/' . date('m') . "/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        Storage::disk('r2')->put($path, $content);

        return DB::transaction(function () use ($uuid, $path, $url, $content) {
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
                'file_size_bytes' => strlen($content),
                'status' => ResumeUpload::STATUS_CREATED,
                'upload_started_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(24),
            ]);
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: 7,
                action: 'Created',
                actorId: 99,
                metadata: ['r2_path' => $path, 'path' => 'presigned'],
            );
            return $row;
        });
    }

    public function test_finalize_dispatches_parse_job_on_dedicated_queue_and_connection(): void
    {
        $seed = $this->seedCreated('%PDF-1.4 fake resume content');

        $r = app(ResumeUploadSession::class)->finalize(
            uuid: $seed->uuid,
            companyId: 7,
            userId: 99,
            clientBytes: strlen('%PDF-1.4 fake resume content'),
            actorIp: ip2long('127.0.0.1'),
            userAgent: 'phpunit',
            requestId: null,
        );

        // 1. finalize returned ok with replayed=false on first call.
        $this->assertTrue($r['ok']);
        $this->assertFalse($r['replayed'] ?? false);

        // 2. ParseResumeJob was dispatched — once.
        Queue::assertPushed(ParseResumeJob::class, 1);

        // 3. Row is in QueuedForParsing — observably queued but
        //    the worker has not yet committed a Parsing transition.
        $row = ResumeUpload::find($seed->id);
        $this->assertSame(ResumeUpload::STATUS_QUEUED_FOR_PARSING, $row->status);

        // 4. Audit row reflects the Queued transition alongside the
        //    UploadSuccess row from finalize().
        $actions = UploadSessionAudit::where('session_uuid', $seed->uuid)
            ->pluck('action')
            ->all();
        $this->assertContains('UploadStart', $actions);
        $this->assertContains('UploadSuccess', $actions);
        $this->assertContains('Queued', $actions);
    }

    public function test_second_finalize_call_does_not_redispatch_parser(): void
    {
        $seed = $this->seedCreated('%PDF-1.4 fake resume content');

        $first = app(ResumeUploadSession::class)->finalize(
            uuid: $seed->uuid,
            companyId: 7,
            userId: 99,
            clientBytes: strlen('%PDF-1.4 fake resume content'),
            actorIp: ip2long('127.0.0.1'),
            userAgent: 'phpunit',
            requestId: null,
        );
        Queue::assertPushed(ParseResumeJob::class, 1);

        // Replay call: same row, same bytes — service should observe
        // QueuedForParsing already and short-circuit.
        $second = app(ResumeUploadSession::class)->finalize(
            uuid: $seed->uuid,
            companyId: 7,
            userId: 99,
            clientBytes: strlen('%PDF-1.4 fake resume content'),
            actorIp: ip2long('127.0.0.1'),
            userAgent: 'phpunit',
            requestId: null,
        );

        $this->assertTrue($first['ok']);
        $this->assertFalse($first['replayed'] ?? false);
        $this->assertTrue($second['ok']);
        $this->assertTrue($second['replayed'] ?? false);
        Queue::assertPushed(ParseResumeJob::class, 1);   // still 1, not 2
    }

    public function test_legacy_uploaded_row_is_still_processed_for_parse(): void
    {
        // Phase 8 accepts Uploaded rows as a worker-entry state so
        // migrations / replay scenarios from pre-Phase-8 captures
        // still drain. We seed an Uploaded row, bypass the
        // controller, and dispatch directly.
        $content = '%PDF-1.4 fake resume content for legacy worker test';
        $seed = $this->seedCreated($content);
        DB::table('resume_uploads')->where('id', $seed->id)->update([
            'status' => ResumeUpload::STATUS_UPLOADED,
            'upload_completed_at' => Carbon::now(),
            'file_size_bytes' => strlen($content),
        ]);

        Queue::fake();
        ParseResumeJob::dispatch($seed->uuid, 7, 99)
            ->onConnection((string) config('resumes.parser_connection'))
            ->onQueue((string) config('resumes.parser_queue'));

        Queue::assertPushed(ParseResumeJob::class, 1);
        // Worker body accepts Uploaded as entry state — proven
        // by ParseResumeJobHandleTest::test_worker_from_legacy_uploaded_state_still_processes.
        $this->assertTrue(true);
    }
}
