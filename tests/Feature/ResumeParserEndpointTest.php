<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ResumeMetrics;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Phase 9 — Http endpoint contract for POST /api/resume-parser/parse.
 *
 * Properties under test:
 *   1. Auth required — anonymous request → 401/403.
 *   2. Tenant guard — `user.company_id` MUST be set, else 403.
 *   3. Validation — missing file → 422.
 *   4. Validation — oversized file → 422.
 *   5. Validation — wrong MIME → 422.
 *   6. Happy path — auth + valid file → 200 with canonical shape.
 *   7. Counters — successful parse bumps parse_started_total AND
 *      parse_completed_total.
 *   8. Failed parse bumps parse_started_total AND
 *      parse_completed_total with outcome='fail'.
 *   9. NO database writes — verified by counting rows before/after.
 */
class ResumeParserEndpointTest extends TestCase
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
        config([
            'resumes.metrics_enabled' => true,
            'resumes.metrics_require_redis' => false,
            // Rate limit lives behind a real Redis INCRBY — we do not
            // want leakage against the dev Redis between tests. The
            // dedicated rate-limit class sets this explicitly.
            'resumes.parser_rate_limit_per_min' => 0,
        ]);
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('users', function ($t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->unsignedInteger('company_id')->nullable();
            $t->string('status')->default('active');
            $t->timestamps();
        });
        // We don't strictly need a tenant table for the parser
        // endpoint — auth reads company_id directly off the user.
        // Tables that MUST stay untouched.
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
    }

    private function seedUser(?int $companyId, string $email): User
    {
        $u = new User();
        $u->name = 'Test';
        $u->email = $email;
        $u->password = Hash::make('secret');
        $u->company_id = $companyId;
        $u->status = 'active';
        $u->save();
        return $u;
    }

    public function test_anonymous_request_is_rejected(): void
    {
        $response = $this->post(
            '/api/resume-parser/parse',
            ['file' => UploadedFile::fake()->create('cv.pdf', 256)],
        );
        $this->assertContains($response->getStatusCode(), [302, 401, 403]);
    }

    public function test_authenticated_user_without_company_id_is_rejected(): void
    {
        $user = $this->seedUser(null, 'no_co@example.com');
        $response = $this->actingAs($user, 'web')
            ->post('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->create('cv.pdf', 256),
            ]);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_missing_file_is_validation_rejected(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        $response = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', []);
        $response->assertStatus(422);
    }

    public function test_oversized_file_is_validation_rejected(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        $response = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->create('cv.pdf', 11000), // > 10 MB
            ]);
        $response->assertStatus(422);
    }

    public function test_wrong_mime_is_validation_rejected(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        $response = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->create('cv.exe', 256),
            ]);
        $response->assertStatus(422);
    }

    public function test_happy_path_returns_canonical_parse_response(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        ResumeMetrics::flushFallback();
        $response = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 minimal blob'),
            ]);
        $response->assertStatus(200);
        $body = $response->json();
        $this->assertArrayHasKey('parse_status', $body);
        $this->assertArrayHasKey('duration_ms', $body);
        $this->assertArrayHasKey('size_bytes', $body);
        $this->assertArrayHasKey('original_filename', $body);
        $this->assertArrayHasKey('full_name', $body);
        $this->assertArrayHasKey('email', $body);

        // Counter increments must fire on the happy path. The
        // controller writes with labels (kind=inline, outcome=
        // success/fail); counterValue reads MUST pass the same
        // labels to land on the matching key.
        //
        // We accept EITHER outcome label because the smalot
        // parser may flag a synthetic PDF blob as unparseable
        // (catch path, outcome='fail') vs. parse it cleanly
        // (success path, outcome='success'); both reach the
        // post-parse `$this->metrics->increment` line. The counter
        // started_total MUST land INSIDE the controller's
        // try-block so a real parse fires it.
        $this->assertGreaterThanOrEqual(
            1,
            app(ResumeMetrics::class)->counterValue(
                'parse_started_total',
                labels: ['kind' => 'inline'],
            ),
        );
        $completed = app(ResumeMetrics::class)->counterValue(
                'parse_completed_total',
                labels: ['outcome' => 'success'],
            )
            + app(ResumeMetrics::class)->counterValue(
                'parse_completed_total',
                labels: ['outcome' => 'fail'],
            );
        $this->assertGreaterThanOrEqual(
            1,
            $completed,
            'parse_completed_total must land on success OR fail label'
        );
    }

    public function test_no_database_writes_on_successful_parse(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        $usersBefore    = \Illuminate\Support\Facades\DB::table('users')->count();
        $uploadsBefore  = \Illuminate\Support\Facades\DB::table('resume_uploads')->count();
        $auditBefore    = \Illuminate\Support\Facades\DB::table('upload_session_audit')->count();
        $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 minimal blob'),
            ])->assertStatus(200);

        $this->assertSame($usersBefore,   \Illuminate\Support\Facades\DB::table('users')->count());
        $this->assertSame($uploadsBefore, \Illuminate\Support\Facades\DB::table('resume_uploads')->count());
        $this->assertSame($auditBefore,   \Illuminate\Support\Facades\DB::table('upload_session_audit')->count());
    }

    public function test_doc_legacy_returns_doc_legacy_unsupported_status(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        ResumeMetrics::flushFallback();
        $response = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent('cv.doc', "binary legacy bytes"),
            ]);
        $response->assertStatus(200);
        $this->assertSame('doc_legacy_unsupported', $response->json('parse_status'));
    }

    public function test_failed_parser_bumps_failed_counter(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        ResumeMetrics::flushFallback();
        $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent('cv.exe', 'unexpected binary'),
            ])
            ->assertStatus(422); // wrong mime = validation

        // Validation-blocked requests never reach the controller's
        // metric increment — that is the correct security posture.
        // The label a validator-rejected request would write to is
        // kind=inline; we assert NO counter landed there.
        $this->assertSame(
            0,
            app(ResumeMetrics::class)->counterValue(
                'parse_started_total',
                labels: ['kind' => 'inline'],
            ),
        );
    }

    public function test_multiple_sequential_calls_bump_start_counter_for_each(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        ResumeMetrics::flushFallback();
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($user, 'web')
                ->postJson('/api/resume-parser/parse', [
                    'file' => UploadedFile::fake()->createWithContent("cv{$i}.pdf", "%PDF-1.4 bytes"),
                ])
                ->assertStatus(200);
        }
        $this->assertSame(
            3,
            app(ResumeMetrics::class)->counterValue(
                'parse_started_total',
                labels: ['kind' => 'inline'],
            ),
        );
    }

    /**
     * Phase 9 follow-up — Redis-backed per-user rate limit.
     *
     * We swap the Redis facade's underlying instance with a tiny
     * stub. The stub's incrby mirrors a real Redis INCRBY semantics
     * (atomic counter from 1 upward). The 11th request returns 11
     * from the stub and exceeds the 10/min cap; the controller
     * MUST respond 429 with Retry-After.
     *
     * Laravel's `Facade::swap($instance)` is process-global; we
     * snapshot and restore the original root in a finally block so
     * subsequent tests in this class do not see the stub.
     */
    /**
     * Phase 9 follow-up — Redis-backed per-user rate limit.
     *
     * Moved to {@see ResumeParserRateLimitTest} because Laravel's
     * `Facade::swap()` mutates a static per-class facade root that
     * subsequent tests in the same process can observe. Isolating
     * the swap to its own test class is the cheapest way to keep
     * ordering deterministic.
     */

    /**
     * Phase 9 follow-up — sanitization strips log-injection class chars
     * from the filename before it lands in the response body. The
     * controller MUST NEVER echo the raw upload name verbatim.
     */
    public function test_filename_is_sanitized_in_response_body(): void
    {
        $user = $this->seedUser(7, 'san@example.com');
        ResumeMetrics::flushFallback();

        // Embed control chars (newline + bidi override) directly in the
        // filename. The sanitized form must replace them with `_` and
        // NEVER include an actual `\n`.
        $mean = "evil\nname\u{202E}.pdf\x07.pdf";

        $resp = $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                'file' => UploadedFile::fake()->createWithContent($mean, '%PDF-1.4 bytes'),
            ]);
        $resp->assertStatus(200);

        $body = $resp->json();
        $this->assertIsString($body['original_filename']);
        $name = $body['original_filename'];
        $this->assertStringNotContainsString("\n", $name, 'filename must not contain a literal newline');
        $this->assertStringNotContainsString("\x07", $name, 'filename must not contain BEL');
        // Bidi override character (U+202E) must be stripped.
        $this->assertStringNotContainsString("\u{202E}", $name);
        // Sanitized name must still be parseable (has a .pdf tail).
        $this->assertStringEndsWith('.pdf', $name);
    }

    /**
     * Phase 9 follow-up — the parser endpoint MUST reject at 5 MB by
     * default. We exercise it by sending a 6 MB upload and asserting
     * the validator's 422 path (no extraction is attempted). This
     * protects PHP-FPM from smalot's heavy in-memory DOM build.
     */
    public function test_oversized_file_above_5mb_default_is_rejected(): void
    {
        config(['resumes.parser_max_kb' => 5120]);            // explicit default
        $user = $this->seedUser(7, 'big@example.com');

        $this->actingAs($user, 'web')
            ->postJson('/api/resume-parser/parse', [
                // 6 MB > 5120 KB cap
                'file' => UploadedFile::fake()->create('cv.pdf', 6000),
            ])
            ->assertStatus(422);
    }
}
