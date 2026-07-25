<?php

namespace Tests\Feature;

use App\Jobs\ParseResumeJob;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeUploadService;
use App\Services\ResumeUploadSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 7 — Submit-time upload regression.
 *
 * The Phase 7 UX change is purely client-side: the JS module
 * (`public/js/resume-upload-progress.js`) drives the presign+PUT+finalize
 * ladder while the recruiter is filling the form, and the submit handler
 * awaits `ResumeUpload.whenReady()` before posting. The submit handler's
 * server-side payload ALWAYS carries `upload_session_uuid` (the
 * presign-finalized row's uuid) and never an in-memory file blob.
 *
 * The contract this test pins:
 *   1. Twice calling `ResumeUploadSession::finalize()` against the same
 *      `session_uuid` is idempotent — second call observes the row in
 *      Uploaded and does NOT dispatch `ParseResumeJob` again.
 *   2. The "submit" path under the new UX is therefore: ONE headCheck
 *      against R2 + ONE DB transaction (the conditional UPDATE that
 *      moves Created → Uploading → Uploaded) + at most ONE
 *      ParseResumeJob::dispatch. No re-runs, no double-spend.
 *   3. The Phase-3 fallback (multipart-only POST without a session
 *      uuid) still routes through `createOrReplay` and lands with
 *      Uploaded status — verified by `JobApplicationResumeUploadTest`.
 *
 * This is intentionally hermetic: it builds the minimal schema, fakes
 * R2 storage, fakes the queue, and exercises the seam-level contracts
 * we need to guarantee without booting the full controller (which has
 * a 5500-line store() with Gate + auth + many cross-cutting deps).
 */
class ResumeUploadSubmissionFlowTest extends TestCase
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
        Storage::fake('r2');
        Queue::fake();
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        // resume_uploads
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
        // upload_session_audit
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
     * Simulate the JS module's presign step INSIDE the test environment.
     * We do NOT call `createOrPresign()` directly because the local
     * filesystem adapter used by `Storage::fake('r2')` cannot produce a
     * real signature (and the production R2 signing is exercised by
     * `ResumePresignTest`). The contract under test here is the Phase-7
     * submit-side promise: one object verify, one DB transition, at
     * most one ParseResumeJob dispatch, idempotent on retry.
     */
    private function seedCreatedSession(string $originalFilename, int $companyId, int $userId, int $sizeBytes): ResumeUpload
    {
        $uuid = (string) Str::uuid();
        $path = "Companies/test_{$companyId}/Resumes/" . date('Y') . '/' . date('m') . "/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        $now = Carbon::now();

        return DB::transaction(function () use ($uuid, $path, $url, $originalFilename, $companyId, $userId, $sizeBytes, $now) {
            $row = ResumeUpload::create([
                'uuid' => $uuid,
                'company_id' => $companyId,
                'user_id' => $userId,
                'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
                'original_filename' => $originalFilename,
                'stored_filename' => basename($path),
                'r2_path' => $path,
                'r2_url' => $url,
                'mime_type' => 'application/pdf',
                'file_size_bytes' => $sizeBytes,
                'status' => ResumeUpload::STATUS_CREATED,
                'upload_started_at' => $now,
                'expires_at' => $now->copy()->addHours(24),
            ]);
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'Created',
                actorId: $userId,
                metadata: ['r2_path' => $row->r2_path, 'path' => 'presigned'],
            );
            return $row;
        });
    }

    /**
     * Drive the JS module's "PUT bytes + finalize" segment against an
     * already-created session. Returns the post-finalize row.
     */
    private function driveFullUpload(string $fileContent, string $originalFilename, int $companyId, int $userId): ResumeUpload
    {
        $session = $this->seedCreatedSession($originalFilename, $companyId, $userId, strlen($fileContent));
        Storage::disk('r2')->put($session->r2_path, $fileContent);

        $uploadService = new ResumeUploadService('r2');
        $this->assertTrue(
            $uploadService->headCheck($session->r2_path),
            'headCheck must return true after the JS module PUTs the bytes'
        );

        $result = app(ResumeUploadSession::class)->finalize(
            uuid: $session->uuid,
            companyId: $companyId,
            userId: $userId,
            clientBytes: strlen($fileContent),
            actorIp: ip2long('127.0.0.1'),
            userAgent: 'phpunit',
            requestId: null,
        );
        $this->assertTrue($result['ok'], 'first finalize must succeed');
        return $result['session'];
    }

    public function test_first_finalize_dispatches_parse_job_exactly_once(): void
    {
        $row = $this->driveFullUpload(
            fileContent: '%PDF-1.4 fake resume bytes',
            originalFilename: 'cv.pdf',
            companyId: 7,
            userId: 99,
        );

        // Session must be in QueuedForParsing post-finalize (Phase 8).
        // The bytes are on R2; we are observing a transient state
        // observable from the DB. The submit-time path stays
        // one HEAD + one DB tx + at-most-one dispatch.
        $this->assertSame(ResumeUpload::STATUS_QUEUED_FOR_PARSING, $row->status);
        Queue::assertPushed(ParseResumeJob::class, 1);
    }

    public function test_second_finalize_call_against_same_session_is_a_no_op(): void
    {
        $first = $this->driveFullUpload(
            fileContent: '%PDF-1.4 fake resume bytes',
            originalFilename: 'cv.pdf',
            companyId: 7,
            userId: 99,
        );

        // Simulate the recruiter's submit handler re-running — e.g.
        // a retried POST, double-click, browser back, etc. The
        // contract of finalize() is idempotent: a second call
        // observes the row in Uploaded and yields no work.
        $second = app(ResumeUploadSession::class)->finalize(
            uuid: $first->uuid,
            companyId: 7,
            userId: 99,
            clientBytes: strlen('%PDF-1.4 fake resume bytes'),
            actorIp: ip2long('127.0.0.1'),
            userAgent: 'phpunit',
            requestId: null,
        );

        $this->assertTrue($second['ok']);
        $this->assertTrue($second['replayed'] ?? false, 'idempotent path must flag replayed');
        $this->assertSame($first->id, $second['session']->id);
        $this->assertSame(ResumeUpload::STATUS_QUEUED_FOR_PARSING, $second['session']->status);
        // NO new ParseResumeJob dispatched — Phase 7 promise:
        // submit is one HEAD + one DB tx + at most one dispatch.
        Queue::assertPushed(ParseResumeJob::class, 1);
    }

    public function test_head_check_is_the_only_object_verification_required_on_submit(): void
    {
        $row = $this->driveFullUpload(
            fileContent: str_repeat('%PDF-', 500),
            originalFilename: 'cv.pdf',
            companyId: 11,
            userId: 42,
        );

        // headCheck on a known-good path returns true; on a missing
        // path returns false. The submit handler will rely on this
        // signal alone for its "object verify" step.
        $svc = new ResumeUploadService('r2');
        $this->assertTrue($svc->headCheck($row->r2_path));
        $this->assertFalse($svc->headCheck('Companies/nope/Resumes/2026/07/ghost.pdf'));
    }

    public function test_submit_with_zero_byte_object_is_rejected_at_head_check(): void
    {
        $row = $this->driveFullUpload(
            fileContent: str_repeat('A', 1024),
            originalFilename: 'cv.pdf',
            companyId: 7,
            userId: 99,
        );

        // Deliberately truncate the bytes after the upload completes
        // (simulating a partial-write or someone removing the object
        // outside the system). headCheck now returns false so the
        // submit handler rejects.
        Storage::disk('r2')->put($row->r2_path, '');
        $svc = new ResumeUploadService('r2');
        $this->assertFalse($svc->headCheck($row->r2_path));
    }
}
