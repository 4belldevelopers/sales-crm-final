<?php

namespace Tests\Feature;

use App\Services\ResumeUploadService;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

/**
 * Phase 4 cover for presigned R2 PUT URLs.
 *
 * Scope: verify the service-level contract on the only path we can
 * exercise without a live R2 bucket — that presignPut() surfaces a
 * clear, typed error when the disk adapter is not S3-compatible.
 *
 * Live AWS signatures require real R2 credentials + a reachable bucket;
 * those are exercised in the smoke test that runs against staging, not
 * in PHPUnit. The full browser → R2 PUT round-trip test belongs in
 * Playwright/Cypress, not here.
 *
 * Backward compatibility: in environments that don't have an S3 adapter
 * (e.g. local disk fallback in dev), presignPut() throws — and the
 * controller falls back to the multipart uploadrar path. This is the
 * correct behavior: when R2 isn't usable, browsers shouldn't be told
 * a presigned URL that wouldn't work.
 */
class ResumePresignTest extends TestCase
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
        // Override the r2 disk config with a local disk so presignPut() can
        // give back a clean typed error rather than a fatal.
        config([
            'filesystems.disks.r2' => [
                'driver' => 'local',
                'root' => storage_path('framework/testing-disk-r2'),
            ],
        ]);
        // Force the cached FilesystemManager to forget the previous disk
        // binding so the new (local) config is honored by Storage::disk().
        app('filesystem')->forgetDisk('r2');
    }

    public function test_presign_throws_typed_error_when_adapter_is_not_s3(): void
    {
        // Re-set config and forget the cached disk binding BEFORE constructing
        // the service. Earlier phases call Storage::fake('r2') which leaves
        // the disk bound in the manager singleton.
        config(['filesystems.disks.r2' => [
            'driver' => 'local',
            'root' => storage_path('framework/testing-disk-r2'),
        ]]);
        app('filesystem')->forgetDisk('r2');

        $svc = new ResumeUploadService('r2');

        // NOTE: do NOT mix $this->expectException() with a try/catch here.
        // expectException checks the exception stream at tearDown — if the
        // catch block absorbs the throw, tearDown concludes "no exception
        // was thrown" and reports a confusing failure. Use try/catch only,
        // with a precise catch type so we can distinguish the contract
        // from any incidental failure.
        try {
            $svc->presignPut(
                'Companies/test/Resumes/2026/07/JERO_test.pdf',
                mimeType: 'application/pdf',
            );
            $this->fail('presignPut must throw on non-S3 adapter');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString(
                'S3/R2 adapter',
                $e->getMessage(),
                'presignPut() must surface a typed RuntimeException when the disk adapter is not S3-compatible',
            );
        }
    }

    public function test_presign_throws_via_storage_fake_path(): void
    {
        // The earlier test's "fake" disk is the real local adapter under
        // the FilesystemManager. This test exercises a tighter scenario:
        // Storage::fake() which is the more realistic Phase 4 fallback path.
        Storage::fake('r2');
        $svc = new ResumeUploadService('r2');
        try {
            $svc->presignPut('Companies/x/Resumes/2026/07/JERO_x.pdf');
            $this->fail('presignPut must throw when no S3 client is reachable');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString(
                'S3/R2 adapter',
                $e->getMessage(),
                'presignPut() under Storage::fake() must still throw a typed RuntimeException',
            );
        }
    }

    public function test_presign_ttl_constant_is_15_min(): void
    {
        $this->assertSame(900, ResumeUploadService::PRESIGN_TTL_SECONDS);
    }

    public function test_compute_destination_returns_canonical_path_in_canonical_shape(): void
    {
        $svc = new ResumeUploadService('r2');
        $dest = $svc->computeDestination('Acme', 7, 'JERO_test.pdf');
        // Project convention: Companies/ is capitalized, slug preserves
        // case from the company_name; matches the existing uploadDirect
        // output so legacy R2 files and new Phase-4 presigned uploads
        // colocate without DIR drift.
        $this->assertStringStartsWith('Companies/', $dest['path']);
        $this->assertMatchesRegularExpression('#^Companies/[a-z_0-9\-]+_7/Resumes/\d{4}/\d{2}/JERO_test\.pdf$#', $dest['path']);
        $this->assertStringStartsWith('https://file.jero.works/', $dest['url']);
    }
}
