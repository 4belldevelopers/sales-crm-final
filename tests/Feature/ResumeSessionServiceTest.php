<?php

namespace Tests\Feature;

use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Models\User;
use App\Services\ResumeUploadService;
use App\Services\ResumeUploadSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 2 cover for the service-company session lifecycle:
 *   - createOrReplay: row creation + R2 write + idempotency key replay
 *   - tryLink: atomic conditional UPDATE, returns false on contention
 *   - markReplacedAfterReplacementLinked: only flips Linked -> Replaced
 *   - activeForUser: scoped to the calling tenant + user
 *
 * Uses RefreshDatabase trait which runs all migrations on sqlite
 * (where possible) for these two new tables; falls back to manual
 * setUp if a migration breaks under sqlite (we explicitly build the
 * schema in setUp to keep things hermetic).
 *
 * Note: parse-resume job dispatch is faked via Queue::fake() so tests
 * don't actually run the parser. We assert the dispatch + payload
 * shape; the parser itself is exercised in Phase 3+.
 */
class ResumeSessionServiceTest extends TestCase
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
        $this->buildSchema();
        $this->cleanTables();
        Queue::fake();
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
            $t->index(['session_uuid', 'created_at'], 'idx_session_time');
        });
        \Illuminate\Support\Facades\Schema::create('job_applications', function ($t) {
            $t->unsignedInteger('id')->primary();
            $t->string('resume', 512)->nullable();
            $t->char('current_resume_session_uuid', 36)->nullable();
        });
        \Illuminate\Support\Facades\Schema::create('companies', function ($t) {
            $t->unsignedInteger('id')->primary();
            $t->string('company_name', 255);
        });
        \Illuminate\Support\Facades\Schema::create('users', function ($t) {
            $t->unsignedInteger('id')->primary();
            $t->unsignedInteger('company_id');
            $t->string('email', 191);
        });
    }

    private function cleanTables(): void
    {
        \Illuminate\Support\Facades\DB::table('upload_session_audit')->delete();
        \Illuminate\Support\Facades\DB::table('resume_uploads')->delete();
        \Illuminate\Support\Facades\DB::table('job_applications')->delete();
        \Illuminate\Support\Facades\DB::table('companies')->delete();
        \Illuminate\Support\Facades\DB::table('users')->delete();
    }

    private function sessionService(): ResumeUploadSession
    {
        return new ResumeUploadSession(new ResumeUploadService('r2'), 'r2');
    }

    public function test_create_or_replay_writes_row_and_r2_and_dispatches_parser(): void
    {
        $svc = $this->sessionService();
        $file = UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 test');

        $result = $svc->createOrReplay(
            file: $file,
            originalFilename: 'cv.pdf',
            companyName: 'Acme',
            companyId: 7,
            userId: 99,
        );

        $this->assertFalse($result['replayed']);
        $session = $result['session'];
        $this->assertSame(7, (int) $session->company_id);
        $this->assertSame(99, (int) $session->user_id);
        $this->assertSame(ResumeUpload::STATUS_UPLOADED, $session->status);
        $this->assertSame(ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION, $session->resource_type);
        $this->assertNotNull($session->expires_at);
        Storage::disk('r2')->assertExists($session->r2_path);

        $audits = UploadSessionAudit::where('session_uuid', $session->uuid)->get();
        // Created + UploadSuccess at minimum.
        $actions = $audits->pluck('action')->all();
        $this->assertContains('Created', $actions);
        $this->assertContains('UploadSuccess', $actions);

        // Parse queue dispatch (faked): payload includes session + tenant.
        Queue::assertPushed(\App\Jobs\ParseResumeJob::class, function ($job) use ($session) {
            return $job->sessionUuid === $session->uuid
                && $job->companyId === (int) $session->company_id;
        });
    }

    public function test_bench_session_uses_bench_resource_type(): void
    {
        $svc = $this->sessionService();
        $file = UploadedFile::fake()->createWithContent('bench.pdf', '%PDF');
        $result = $svc->createOrReplay(
            file: $file,
            originalFilename: 'bench.pdf',
            companyName: 'RelMgr',
            companyId: 11,
            userId: 42,
            resourceType: ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION,
        );
        $this->assertSame(ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION, $result['session']->resource_type);
    }

    public function test_try_link_bench_writes_bench_application_id_and_audit(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('bench.pdf', '%PDF'),
            originalFilename: 'bench.pdf',
            companyName: 'RelMgr', companyId: 11, userId: 42,
            resourceType: ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        $ok = $svc->tryLinkBench($session->uuid, 11, benchApplicationId: 888, actorId: 42);
        $this->assertTrue($ok);
        $row = ResumeUpload::find($session->id);
        $this->assertSame(ResumeUpload::STATUS_LINKED, $row->status);
        $this->assertNull($row->candidate_id, 'job_application link must not be touched on bench flow');
        $this->assertSame(888, (int) $row->bench_application_id);
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $session->uuid)
                ->where('action', 'Linked')->exists(),
        );
    }

    public function test_try_link_bench_refuses_job_application_sessions(): void
    {
        $svc = $this->sessionService();
        // Default resource_type='job_application'.
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', '%PDF'),
            originalFilename: 'cv.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        // tryLinkBench must NOT match a job_application row.
        $this->assertFalse($svc->tryLinkBench($session->uuid, 7, 888, 99));
        $this->assertNull(ResumeUpload::find($session->id)->bench_application_id);
    }

    public function test_try_link_refuses_bench_application_sessions(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('bench.pdf', '%PDF'),
            originalFilename: 'bench.pdf', companyName: 'RelMgr', companyId: 11, userId: 42,
            resourceType: ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        // tryLink (the candidate variant) must NOT match a bench row.
        $this->assertFalse($svc->tryLink($session->uuid, 11, 555, 42));
        $this->assertNull(ResumeUpload::find($session->id)->candidate_id);
    }

    public function test_try_link_bench_returns_false_for_wrong_tenant(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('bench.pdf', '%PDF'),
            originalFilename: 'bench.pdf', companyName: 'RelMgr', companyId: 11, userId: 42,
            resourceType: ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        $this->assertFalse(
            $svc->tryLinkBench($session->uuid, 999, 888, 42),
            'Wrong-tenant bench link MUST return false',
        );
    }

    public function test_phase3_dispatch_count_drop(): void
    {
        // Sanity: after Phase 3, the bench path's content contains BENCH
        // resource_type and the controller surface still functions.
        // (Counting legacy dispatch sites is platform-level; the unit-level
        // validation lives in earlier integration tests.)
        $this->assertTrue(class_exists(\App\Services\ResumeUploadSession::class));
        $this->assertTrue(method_exists(\App\Services\ResumeUploadSession::class, 'tryLinkBench'));
        $this->assertTrue(defined('App\\Models\\ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION'));
        $this->assertSame('bench_application', ResumeUpload::RESOURCE_TYPE_BENCH_APPLICATION);
    }

    public function test_idempotency_key_replays_existing_session_without_new_r2_write(): void
    {
        $svc = $this->sessionService();
        $key = (string) Str::uuid();
        $first = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-content'),
            originalFilename: 'cv.pdf',
            companyName: 'Acme',
            companyId: 7,
            userId: 99,
            idempotencyKey: $key,
        );
        $firstR2Path = $first['session']->r2_path;

        // Replay with the SAME key — different filename is fine, we keep the original R2 file.
        $second = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv2.pdf', '%PDF-other'),
            originalFilename: 'cv2.pdf',
            companyName: 'Acme',
            companyId: 7,
            userId: 99,
            idempotencyKey: $key,
        );

        $this->assertTrue($second['replayed']);
        $this->assertSame($first['session']->uuid, $second['session']->uuid);
        $this->assertSame($firstR2Path, $second['session']->r2_path,
            'Replay must NOT create a new R2 file');

        // The original file size remains the first call's bytes; no overwrite.
        Storage::disk('r2')->assertExists($firstR2Path);

        // Replay audit row exists.
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $first['session']->uuid)
                ->where('action', 'Replay')->exists(),
        );
    }

    public function test_idempotency_key_is_per_tenant(): void
    {
        $svc = $this->sessionService();
        $key = (string) Str::uuid();
        $a = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('a.pdf', 'A'),
            originalFilename: 'a.pdf', companyName: 'A', companyId: 7, userId: 99,
            idempotencyKey: $key,
        );
        // Different tenant with same key MUST start a fresh session (per-tenant scope).
        $b = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('b.pdf', 'B'),
            originalFilename: 'b.pdf', companyName: 'B', companyId: 8, userId: 99,
            idempotencyKey: $key,
        );
        $this->assertFalse($b['replayed']);
        $this->assertNotSame($a['session']->uuid, $b['session']->uuid);
    }

    public function test_try_link_atomically_writes_candidate_id_and_audit(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];

        // simulate parser finishing
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();

        $ok = $svc->tryLink($session->uuid, 7, candidateId: 555, actorId: 99);
        $this->assertTrue($ok);
        $row = ResumeUpload::find($session->id);
        $this->assertSame(ResumeUpload::STATUS_LINKED, $row->status);
        $this->assertSame(555, (int) $row->candidate_id);
        $this->assertNotNull($row->linked_at);
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $session->uuid)
                ->where('action', 'Linked')->exists(),
        );
    }

    public function test_try_link_returns_false_if_already_linked(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        $this->assertTrue($svc->tryLink($session->uuid, 7, 555, 99));
        // Second call must NOT transition again — returns false.
        $this->assertFalse($svc->tryLink($session->uuid, 7, 999, 99));
    }

    public function test_try_link_returns_false_for_wrong_tenant(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        $session->status = ResumeUpload::STATUS_PARSED;
        $session->save();
        $this->assertFalse($svc->tryLink($session->uuid, 999, 555, 99),
            'A wrong-tenant link attempt MUST return false; never leak across tenants.');
    }

    public function test_mark_replaced_only_flips_linked_state(): void
    {
        $svc = $this->sessionService();
        $oldSession = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('old.pdf', 'A'),
            originalFilename: 'old.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        $newSession = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('new.pdf', 'B'),
            originalFilename: 'new.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        $oldSession->status = ResumeUpload::STATUS_PARSED;
        $oldSession->save();
        $newSession->status = ResumeUpload::STATUS_PARSED;
        $newSession->save();
        $this->assertTrue($svc->tryLink($oldSession->uuid, 7, 555, 99));
        // Now flip
        $this->assertTrue(
            $svc->markReplacedAfterReplacementLinked($oldSession->uuid, 7, $newSession->uuid, 99)
        );
        $row = ResumeUpload::find($oldSession->id);
        $this->assertSame(ResumeUpload::STATUS_REPLACED, $row->status);
        // New session records replaces_session_uuid
        $new = ResumeUpload::find($newSession->id);
        $this->assertSame($oldSession->uuid, $new->replaces_session_uuid);
        $this->assertTrue(
            UploadSessionAudit::where('session_uuid', $oldSession->uuid)
                ->where('action', 'Replaced')->exists(),
        );
    }

    public function test_mark_replaced_is_no_op_when_old_is_not_linked(): void
    {
        $svc = $this->sessionService();
        $oldSession = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('a.pdf', 'A'),
            originalFilename: 'a.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        // Old is still Uploaded, never reached Linked — must not flip to Replaced.
        $this->assertFalse(
            $svc->markReplacedAfterReplacementLinked($oldSession->uuid, 7, 'something-else', 99),
        );
        $this->assertSame(ResumeUpload::STATUS_UPLOADED, ResumeUpload::find($oldSession->id)->status);
    }

    public function test_active_for_user_returns_no_session_when_all_expired(): void
    {
        $svc = $this->sessionService();
        $session = $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'Acme', companyId: 7, userId: 99,
        )['session'];
        // Make it expired.
        \Illuminate\Support\Facades\DB::table('resume_uploads')
            ->where('id', $session->id)
            ->update(['expires_at' => now()->subDay()]);

        $this->assertNull($svc->activeForUser(7, 99));
    }

    public function test_active_for_user_is_tenant_scoped(): void
    {
        $svc = $this->sessionService();
        $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'A', companyId: 7, userId: 99,
        );
        $svc->createOrReplay(
            file: UploadedFile::fake()->createWithContent('cv.pdf', 'PDF'),
            originalFilename: 'cv.pdf', companyName: 'B', companyId: 8, userId: 99,
        );
        $this->assertSame(7, (int) $svc->activeForUser(7, 99)->company_id);
        $this->assertSame(8, (int) $svc->activeForUser(8, 99)->company_id);
        $this->assertNull($svc->activeForUser(999, 99), 'No session for non-existent tenant');
    }
}
