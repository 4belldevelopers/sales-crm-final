<?php

namespace Tests\Feature;

use App\Jobs\ParseResumeJob;
use App\Jobs\RepairStuckSessionsJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeMetrics;
use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 5 cover for the stuck-session repair job.
 *
 * Repair surfaces two stuck states:
 *   - Uploading past uploading_timeout_minutes → orphan recovery.
 *   - Parsing past parsing_timeout_minutes   → bounded re-dispatch, with
 *     cap at repair_dispatch_max attempts before ParseFailed.
 *
 * Idempotency is asserted separately for both branches.
 *
 * Storage::fake + Queue::fake keep this hermetic; we drive transitions
 * by mutating upload_started_at / parse_started_at to past timestamps.
 */
class ResumeRepairTest extends TestCase
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
        // Cache: array driver keeps metric counters hermetic per test run
        // — no Redis, no global state bleed.
        $app['config']->set('cache.default', 'array');
        $app['config']->set('cache.stores.array', ['driver' => 'array']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
        Queue::fake();
        config([
            'resumes.uploading_timeout_minutes' => 30,
            'resumes.parsing_timeout_minutes' => 60,
            'resumes.repair_dispatch_max' => 3,
            'resumes.metrics_enabled' => true,
            'resumes.alerts_enabled' => true,
            'resumes.metrics_require_redis' => false, // test fixture escape hatch
            'resumes.alert_thresholds' => [
                'orphan_uploads' => ['current' => 0],
                'stuck_sessions' => ['current' => 0],
                'delete_failures' => ['last_hour' => 0],
                'finalize_failures' => ['last_hour' => 0],
            ],
        ]);
        $this->buildSchema();
        $this->cleanTables();
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
    }

    private function cleanTables(): void
    {
        \Illuminate\Support\Facades\DB::table('upload_session_audit')->delete();
        \Illuminate\Support\Facades\DB::table('resume_uploads')->delete();
        Cache::store()->flush();
        // Phase 6 — flush in-process metric counters to keep tests
        // independent.
        \App\Services\ResumeMetrics::flushFallback();
    }

    private function makeStuckUploading(int $cid = 7): ResumeUpload
    {
        $ev = Carbon::now()->copy()->subMinutes(45); // past 30m timeout
        $uuid = (string) Str::uuid();
        $path = "Companies/test_{$cid}/Resumes/2026/07/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        Storage::disk('r2')->put($path, '%PDF stub');
        return ResumeUpload::create([
            'uuid' => $uuid,
            'company_id' => $cid,
            'user_id' => 99,
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => basename($path),
            'r2_path' => $path,
            'r2_url' => $url,
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 9,
            'status' => ResumeUpload::STATUS_UPLOADING,
            'upload_started_at' => $ev,
            'expires_at' => $ev->copy()->addDay(),
        ]);
    }

    private function makeStuckParsing(int $cid = 7, int $priorAttempts = 0): ResumeUpload
    {
        $ev = Carbon::now()->copy()->subMinutes(90); // past 60m timeout
        $uuid = (string) Str::uuid();
        $path = "Companies/test_{$cid}/Resumes/2026/07/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        Storage::disk('r2')->put($path, '%PDF stub');
        return ResumeUpload::create([
            'uuid' => $uuid,
            'company_id' => $cid,
            'user_id' => 99,
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => basename($path),
            'r2_path' => $path,
            'r2_url' => $url,
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 9,
            'status' => ResumeUpload::STATUS_PARSING,
            'upload_started_at' => $ev->copy()->subMinutes(10),
            'upload_completed_at' => $ev->copy()->subMinutes(10),
            'parse_started_at' => $ev,
            'parse_attempt_count' => $priorAttempts,
        ]);
    }

    private function runRepair(): void
    {
        $job = new RepairStuckSessionsJob();
        $job->maxRetriesPerRun = 100;
        $job->handle(app(ResumeMetrics::class));
    }

    public function test_repair_deletes_stuck_uploading_rows(): void
    {
        $row = $this->makeStuckUploading();
        $this->runRepair();
        // The row is unconditionally deleted (not transitioned), so a re-find returns null.
        $this->assertNull(ResumeUpload::find($row->id), 'stuck Uploading row must be removed');
        // Audit row recorded.
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $row->uuid)
                ->where('action', 'RepairDeleted')->exists(),
        );
    }

    public function test_repair_clears_orphan_bytes_on_r2(): void
    {
        $row = $this->makeStuckUploading();
        $this->assertTrue(Storage::disk('r2')->exists($row->r2_path));
        $this->runRepair();
        $this->assertFalse(Storage::disk('r2')->exists($row->r2_path),
            'R2 file MUST be removed when row is repaired');
    }

    public function test_repair_requeues_stuck_parsing_with_attempt_increment(): void
    {
        $row = $this->makeStuckParsing();
        $this->runRepair();
        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSING, $fresh->status);
        $this->assertSame(1, (int) $fresh->parse_attempt_count);
        Queue::assertPushed(ParseResumeJob::class, function ($job) use ($row) {
            return $job->sessionUuid === $row->uuid && $job->companyId === 7;
        });
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $row->uuid)
                ->where('action', 'RepairRequeued')->exists(),
        );
    }

    public function test_repair_marks_parse_failed_after_repair_dispatch_max(): void
    {
        $row = $this->makeStuckParsing(priorAttempts: 3); // already at max
        $this->runRepair();
        $fresh = ResumeUpload::find($row->id);
        $this->assertSame(ResumeUpload::STATUS_PARSE_FAILED, $fresh->status);
        $this->assertSame('repair_parse_exhausted', $fresh->cleanup_reason);
        // NO new dispatch should happen.
        Queue::assertNotPushed(ParseResumeJob::class);
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $row->uuid)
                ->where('action', 'RepairExhausted')->exists(),
        );
    }

    public function test_repair_is_idempotent_after_exhausting_dispatch_cap(): void
    {
        // Contract: not "never re-increment" — that traps stuck rows. The
        // contract is "stop dispatching once the cap is hit". Verify:
        // after repair_dispatch_max + 1 runs the row is ParseFailed and
        // parse_attempt_count = max; runs thereafter do nothing.
        $max = (int) config('resumes.repair_dispatch_max', 3);
        $row = $this->makeStuckParsing();

        // First max runs each dispatch (and increment parse_attempt_count);
        // we reset stuck-ness between runs so the time filter keeps picking
        // it up. Skip the reset after the FINAL run so we observe the
        // exhausted state the job wrote.
        for ($i = 0; $i < $max; $i++) {
            $this->runRepair();
            \Illuminate\Support\Facades\DB::table('resume_uploads')
                ->where('id', $row->id)
                ->update([
                    'parse_started_at' => Carbon::now()->copy()->subMinutes(90),
                    'status' => ResumeUpload::STATUS_PARSING,
                ]);
        }
        // The (max+1)-th run is the one that exhausts the cap.
        $this->runRepair();
        $afterCap = ResumeUpload::find($row->id);
        $this->assertSame(
            ResumeUpload::STATUS_PARSE_FAILED,
            $afterCap->status,
            'row must be ParseFailed after dispatch attempts exceed the cap'
        );
        $this->assertSame($max, (int) $afterCap->parse_attempt_count);

        // A subsequent run does no further work: status=ParseFailed, so
        // the filter excludes the row.
        $pushedBefore = \Illuminate\Support\Facades\Queue::pushed(\App\Jobs\ParseResumeJob::class)->count();
        $this->runRepair();
        $this->assertSame($pushedBefore, \Illuminate\Support\Facades\Queue::pushed(\App\Jobs\ParseResumeJob::class)->count(),
            'ParseFailed rows are not re-queued by repair'
        );
    }

    public function test_repair_increments_metrics_for_observed_stuck(): void
    {
        $this->makeStuckUploading();
        $this->makeStuckParsing();
        $this->runRepair();
        $metrics = app(ResumeMetrics::class);
        $this->assertGreaterThanOrEqual(1,
            $metrics->counterValue('stuck_detected_total', labels: ['kind' => 'stuck_uploading'])
        );
        $this->assertGreaterThanOrEqual(1,
            $metrics->counterValue('repair_attempts_total', labels: ['kind' => 'stuck_uploading'])
        );
    }

    public function test_repair_does_not_touch_recent_uploading_rows(): void
    {
        $row = $this->makeStuckUploading();
        // Bring upload_started_at to 5 minutes ago — well within the
        // 30-minute timeout (so this is NOT stuck).
        $row->upload_started_at = Carbon::now()->copy()->subMinutes(5);
        $row->save();
        $this->runRepair();
        $this->assertNotNull(ResumeUpload::find($row->id), 'recent Uploading row must NOT be deleted');
    }

    public function test_repair_dry_run_command_prints_counts(): void
    {
        $this->makeStuckUploading();
        $this->makeStuckParsing();
        $exitCode = $this->artisan('uploads:repair', ['--dry-run' => true])->run();
        $this->assertSame(0, $exitCode);
        // The dry-run MUST NOT mutate anything.
        $this->assertSame(2, ResumeUpload::count());
    }
}
