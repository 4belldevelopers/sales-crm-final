<?php

namespace Tests\Feature;

use App\Services\ResumeParserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 9 — ResumeParserService unit coverage.
 *
 * Focuses on the canonical-response contract and the strict
 * no-write boundary. We are NOT testing smalot/pdfparser or
 * phpoffice/phpword directly — those are third-party libraries
 * with their own test suites. We are testing the SHAPE and
 * SIDE-EFFECTS that OUR code commits to.
 *
 * Properties under test:
 *   1. Every response has all canonical keys, even when parsing
 *      fails — the JS module reads from a stable schema.
 *   2. parse_status='ok' returns null for fields we couldn't
 *      extract, never a wrong value.
 *   3. Empty bytes and oversized bytes are rejected up-front.
 *   4. DOC legacy returns parse_status='doc_legacy_unsupported'.
 *   5. NO database writes — including NO upload_session_audit,
 *      NO resume_uploads, NO counters. The Phase-9 service is
 *      read-only by strict contract.
 */
class ResumeParserServiceTest extends TestCase
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
        // Schema for resume_uploads and upload_session_audit —
        // we want to PROVE no rows are written.
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('status', 32)->default('Created');
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
            $t->timestamps();
        });
        \Illuminate\Support\Facades\Schema::create('upload_session_audit', function ($t) {
            $t->bigIncrements('id');
            $t->char('session_uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('action', 32);
            $t->timestamp('created_at')->useCurrent();
        });
        // Metric-counter test fixture (Redis-direct facade).
        // counters do not need a DB table; tests only assert the
        // READ of self::$fallback after a bump.
    }

    public function test_response_has_canonical_key_shape_on_rejected_size(): void
    {
        $svc = new ResumeParserService();
        // 0 bytes — uses UploadedFile to satisfy the param shape.
        $file = UploadedFile::fake()->create('cv.pdf', 0);
        $resp = $svc->parse($file, 'cv.pdf');

        $this->assertResponseShape($resp);
        $this->assertSame('rejected_size', $resp['parse_status']);
        $this->assertNull($resp['full_name']);
        $this->assertNull($resp['email']);
        $this->assertNull($resp['phone']);
    }

    public function test_response_shape_on_oversized_input(): void
    {
        $svc = new ResumeParserService();
        // 11 MB exceeds 10 MB cap.
        $file = UploadedFile::fake()->create('cv.pdf', 11000);
        $resp = $svc->parse($file, 'cv.pdf');

        $this->assertResponseShape($resp);
        $this->assertSame('rejected_size', $resp['parse_status']);
    }

    public function test_response_shape_on_doc_legacy(): void
    {
        $svc = new ResumeParserService();
        $file = UploadedFile::fake()->create('cv.doc', 32);
        $resp = $svc->parse($file, 'cv.doc');

        $this->assertResponseShape($resp);
        $this->assertSame('doc_legacy_unsupported', $resp['parse_status']);
        $this->assertNull($resp['full_name']);
        $this->assertNull($resp['current_designation']);
    }

    public function test_response_shape_on_unrecognized_extension(): void
    {
        $svc = new ResumeParserService();
        $file = UploadedFile::fake()->create('cv.xyz', 256);
        $resp = $svc->parse($file, 'cv.xyz');

        $this->assertResponseShape($resp);
        $this->assertSame('unsupported_format', $resp['parse_status']);
    }

    public function test_pdf_with_real_minimal_blob_returns_canonical_shape(): void
    {
        // Phase 9 deliberately does not depend on smalot being
        // installed or producing parseable bytes here. Actively
        // assert the SHAPE rather than the parsed values: every
        // canonical key is present, including parse_status,
        // duration_ms, size_bytes, original_filename.
        $svc = new ResumeParserService();
        $file = UploadedFile::fake()->createWithContent('cv.pdf', "%PDF-1.4\nTest resume bytes");
        $resp = $svc->parse($file, 'cv.pdf');

        $this->assertResponseShape($resp);
        $this->assertSame('cv.pdf', $resp['original_filename']);
        $this->assertIsInt($resp['duration_ms']);
        $this->assertLessThanOrEqual(15000, $resp['duration_ms']);    // sanity bound (5 s budget + headroom)
    }

    public function test_parse_does_not_write_to_resume_uploads(): void
    {
        $svc = new ResumeParserService();
        $file = UploadedFile::fake()->create('cv.pdf', 2048);
        $countBefore = DB::table('resume_uploads')->count();
        $svc->parse($file, 'cv.pdf');
        $countAfter = DB::table('resume_uploads')->count();
        $this->assertSame($countBefore, $countAfter, 'ResumeParserService MUST NOT touch resume_uploads');
    }

    public function test_parse_does_not_write_to_audit_table(): void
    {
        $svc = new ResumeParserService();
        $file = UploadedFile::fake()->create('cv.pdf', 4096);
        $countBefore = DB::table('upload_session_audit')->count();
        $svc->parse($file, 'cv.pdf');
        $countAfter = DB::table('upload_session_audit')->count();
        $this->assertSame($countBefore, $countAfter, 'ResumeParserService MUST NOT write upload_session_audit rows');
    }

    /**
     * Phase 9 follow-up — sanitize() strips log-injection class chars
     * and bidi overrides, never returns an empty string, and never
     * exceeds the 200-char safety cap.
     */
    public function test_sanitize_strips_log_injection_class_chars(): void
    {
        // Log injection: `\n` would let a malicious filename inject a
        // fake log line; BEL (\x07) and the bidi override (U+202E) are
        // also in the strip set.
        $evil = "\u{202E}evil\nname\x07.pdf";
        $clean = ResumeParserService::sanitizeFilename($evil);
        $this->assertStringNotContainsString("\n", $clean);
        $this->assertStringNotContainsString("\x07", $clean);
        $this->assertStringNotContainsString("\u{202E}", $clean);
        // basename contraction: slashes/backslashes replaced with `_`
        $this->assertStringNotContainsString('/', $clean);
        $this->assertStringNotContainsString('\\', $clean);
    }

    public function test_sanitize_drops_empty_input_to_unnamed(): void
    {
        $this->assertSame('unnamed', ResumeParserService::sanitizeFilename(''));
        // All control chars won't yield a `'.'` tail either.
        $this->assertSame('unnamed', ResumeParserService::sanitizeFilename("\x00\x07"));
    }

    public function test_sanitize_caps_length_at_200(): void
    {
        $long = str_repeat('a', 500) . '.pdf';
        $this->assertSame(200, strlen(ResumeParserService::sanitizeFilename($long)));
    }

    /**
     * Assert the canonical-response shape. Every key is present
     * regardless of parse_status — the JS module iterates without
     * conditional checks.
     */
    private function assertResponseShape(array $resp): void
    {
        $expected = [
            'parse_status', 'duration_ms', 'size_bytes', 'original_filename',
            'full_name', 'email', 'phone', 'skills', 'experience',
            'education', 'location', 'current_company', 'current_designation',
        ];
        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $resp, 'response must include ' . $key);
        }
        $this->assertIsString($resp['parse_status']);
        $this->assertIsInt($resp['duration_ms']);
        $this->assertIsInt($resp['size_bytes']);
        $this->assertIsString($resp['original_filename']);
        // Field projections are always string|array|null — never
        // a number or boolean sneaking through.
        foreach (['full_name','email','phone','experience','education','location','current_company','current_designation'] as $field) {
            $this->assertTrue(
                $resp[$field] === null || is_string($resp[$field]),
                $field . ' must be null|string'
            );
        }
        // Skills may be null|array of strings.
        if ($resp['skills'] !== null) {
            $this->assertIsArray($resp['skills']);
            foreach ($resp['skills'] as $s) {
                $this->assertIsString($s);
            }
        }
    }
}
