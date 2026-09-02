<?php

// Intent: Unit tests for ResumeUploadSession core service.
// Tests INV-1 (new pick = new session) and INV-4 (same key = replay),
// plus finalize, cancel, and idempotency guarantees.
//
// DB isolation: overrides the default connection to SQLite :memory: and
// creates only the 3 tables the service needs — avoids the full MySQL
// migration stack (which is MySQL-specific and can't run in the test env).
//
// Storage isolation: Mockery-mocks Storage::disk('r2') so temporaryUploadUrl()
// returns a fake URL without requiring real R2 credentials.

namespace Tests\Unit;

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use App\Services\ResumeUploadSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeUploadSessionServiceTest extends TestCase
{
    private ResumeUploadSession $service;
    private int $companyId = 1;
    private int $userId    = 10;

    protected function setUp(): void
    {
        parent::setUp();

        // ── 1. DB: route Eloquent to an in-memory SQLite DB ─────────────────
        config([
            'database.default'                                    => 'sqlite',
            'database.connections.sqlite.driver'                  => 'sqlite',
            'database.connections.sqlite.database'                => ':memory:',
            'database.connections.sqlite.prefix'                  => '',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);

        // Purge any cached connection so we start with a fresh :memory: DB
        DB::purge('sqlite');

        // Create exactly the tables this service uses
        DB::connection('sqlite')->statement('
            CREATE TABLE resume_uploads (
                id                  INTEGER PRIMARY KEY AUTOINCREMENT,
                uuid                TEXT    NOT NULL UNIQUE,
                company_id          INTEGER NOT NULL,
                user_id             INTEGER,
                original_filename   TEXT,
                stored_filename     TEXT,
                sha256_hex          TEXT,
                mime_type           TEXT,
                file_size_bytes     INTEGER,
                r2_temp_key         TEXT,
                r2_temp_url         TEXT,
                r2_url              TEXT,
                idempotency_key     TEXT,
                status              TEXT    NOT NULL DEFAULT "Pending",
                candidate_id        INTEGER,
                bench_application_id INTEGER,
                linked_candidate_id INTEGER,
                last_error          TEXT,
                upload_completed_at TEXT,
                expires_at          TEXT,
                cleanup_reason      TEXT,
                created_at          TEXT,
                updated_at          TEXT
            )
        ');
        DB::connection('sqlite')->statement('
            CREATE TABLE upload_session_audits (
                id                INTEGER PRIMARY KEY AUTOINCREMENT,
                resume_upload_id  INTEGER NOT NULL,
                company_id        INTEGER,
                user_id           INTEGER,
                action            TEXT NOT NULL,
                payload           TEXT,
                request_id        TEXT,
                pod_id            TEXT,
                error_code        TEXT,
                message_safe      TEXT,
                ip_hash           TEXT,
                created_at        TEXT,
                updated_at        TEXT
            )
        ');
        DB::connection('sqlite')->statement('
            CREATE TABLE companies (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                company_name TEXT
            )
        ');
        DB::connection('sqlite')->table('companies')->insert(['id' => 1, 'company_name' => 'Acme Corp']);

        // ── 2. Storage: mock r2 disk so temporaryUploadUrl() works ──────────
        // FakeStorage / LocalAdapter throws on temporaryUploadUrl — no callback hook
        // exists for it (unlike temporaryUrl). Use Mockery on the facade.
        $mockDisk = \Mockery::mock();
        $mockDisk->shouldReceive('temporaryUploadUrl')
                 ->andReturnUsing(fn ($path) => 'https://fake.r2.dev/' . $path);
        $mockDisk->shouldReceive('exists')->andReturn(false);
        $mockDisk->shouldReceive('delete')->andReturn(true);
        $mockDisk->shouldReceive('copy')->andReturn(true);

        Storage::shouldReceive('disk')
               ->with('r2')
               ->andReturn($mockDisk);

        // ── 3. Instantiate the service under test ────────────────────────────
        $this->service = app(ResumeUploadSession::class);
    }

    protected function tearDown(): void
    {
        // Delete before parent::tearDown() destroys the app/connection
        DB::connection('sqlite')->statement('DELETE FROM upload_session_audits');
        DB::connection('sqlite')->statement('DELETE FROM resume_uploads');
        parent::tearDown(); // calls Mockery::close(), flushes app
    }

    // ─── INV-1: every new file pick (new Idempotency-Key) → new session ──────

    public function test_new_header_creates_new_session(): void
    {
        $result = $this->service->createOrPresign(
            $this->companyId, $this->userId,
            str_repeat('a', 64),
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );

        $this->assertIsString($result['uuid']);
        $this->assertSame('Pending', $result['status']);
        $this->assertFalse($result['replayed']);
        $this->assertStringContainsString('http', $result['upload_url']);
    }

    public function test_different_keys_create_different_sessions_for_same_company(): void
    {
        $first = $this->service->createOrPresign(
            $this->companyId, $this->userId, str_repeat('a', 64),
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );

        $second = $this->service->createOrPresign(
            $this->companyId, $this->userId, str_repeat('b', 64),
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );

        $this->assertNotSame($first['uuid'], $second['uuid']);
        $this->assertFalse($second['replayed']);
        $this->assertSame(2, ResumeUpload::count());
    }

    // ─── INV-4: same key on retry → replay same session ──────────────────────

    public function test_same_key_on_retry_returns_same_session(): void
    {
        $key = str_repeat('a', 64);

        $first  = $this->service->createOrPresign(
            $this->companyId, $this->userId, $key,
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );
        $second = $this->service->createOrPresign(
            $this->companyId, $this->userId, $key,
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );

        $this->assertSame($first['uuid'], $second['uuid']);
        $this->assertTrue($second['replayed']);
        $this->assertSame(1, ResumeUpload::where('idempotency_key', $key)->count());
    }

    public function test_same_key_across_different_companies_creates_different_sessions(): void
    {
        $key = str_repeat('a', 64);

        $a = $this->service->createOrPresign(1, 10, $key, 'r.pdf', 1_000, 'application/pdf', '');
        $b = $this->service->createOrPresign(2, 20, $key, 'r.pdf', 1_000, 'application/pdf', '');

        $this->assertNotSame($a['uuid'], $b['uuid']);
        $this->assertFalse($b['replayed']);
    }

    public function test_replay_skips_terminal_sessions(): void
    {
        $key = str_repeat('a', 64);

        $first = $this->service->createOrPresign(
            $this->companyId, $this->userId, $key,
            'resume.pdf', 1_000, 'application/pdf', ''
        );

        // Mark session as terminal
        ResumeUpload::where('uuid', $first['uuid'])
            ->update(['status' => ResumeUploadStatus::Completed->value]);

        // Same key — should NOT replay the terminal session
        $second = $this->service->createOrPresign(
            $this->companyId, $this->userId, $key,
            'resume.pdf', 1_000, 'application/pdf', ''
        );

        $this->assertNotSame($first['uuid'], $second['uuid']);
        $this->assertFalse($second['replayed']);
    }

    // ─── Finalize ─────────────────────────────────────────────────────────────

    public function test_finalize_transitions_pending_to_uploaded(): void
    {
        $result    = $this->presignHelper();
        $finalized = $this->service->finalize($result['uuid'], $this->companyId);

        $this->assertSame(ResumeUploadStatus::Uploaded->value, $finalized['status']);
    }

    public function test_double_finalize_is_idempotent(): void
    {
        $result = $this->presignHelper();

        $this->service->finalize($result['uuid'], $this->companyId);
        $second = $this->service->finalize($result['uuid'], $this->companyId);

        $this->assertSame(ResumeUploadStatus::Uploaded->value, $second['status']);
    }

    public function test_finalize_nonexistent_session_throws_404(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->service->finalize('00000000-0000-0000-0000-000000000000', $this->companyId);
    }

    public function test_finalize_cross_tenant_throws_404(): void
    {
        $result = $this->presignHelper(companyId: 1);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->service->finalize($result['uuid'], companyId: 2);
    }

    // ─── Cancel ───────────────────────────────────────────────────────────────

    public function test_cancel_transitions_pending_to_cancelled(): void
    {
        $result = $this->presignHelper();
        $this->service->cancel($result['uuid'], $this->companyId);

        $this->assertSame(
            ResumeUploadStatus::Cancelled->value,
            ResumeUpload::where('uuid', $result['uuid'])->value('status')
        );
    }

    public function test_double_cancel_is_idempotent(): void
    {
        $result = $this->presignHelper();
        $this->service->cancel($result['uuid'], $this->companyId);
        $this->service->cancel($result['uuid'], $this->companyId);

        $this->assertSame(
            ResumeUploadStatus::Cancelled->value,
            ResumeUpload::where('uuid', $result['uuid'])->value('status')
        );
    }

    // ─── Idempotency-Key validation ───────────────────────────────────────────

    public function test_malformed_key_throws_invalid_argument(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->createOrPresign(
            $this->companyId, $this->userId,
            'not-valid!',
            'r.pdf', 1_000, 'application/pdf', ''
        );
    }

    public function test_uuid_format_key_is_accepted(): void
    {
        $result = $this->service->createOrPresign(
            $this->companyId, $this->userId,
            '550e8400-e29b-41d4-a716-446655440000',
            'r.pdf', 1_000, 'application/pdf', ''
        );

        $this->assertSame('Pending', $result['status']);
    }

    public function test_move_to_company_path_generates_collision_proof_key(): void
    {
        $result = $this->presignHelper();
        $session = ResumeUpload::where('uuid', $result['uuid'])->first();
        $session->linked_candidate_id = 99;

        $url = $this->service->moveToCompanyPath($session);
        $this->assertStringContainsString('99_resume_' . $session->uuid . '.pdf', $url);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function presignHelper(int $companyId = 0): array
    {
        return $this->service->createOrPresign(
            $companyId ?: $this->companyId,
            $this->userId,
            str_repeat('a', 64),
            'resume.pdf', 1_000, 'application/pdf', '127.0.0.1'
        );
    }
}
