<?php

namespace Tests\Feature;

use App\Jobs\ElasticCandidateIndex;
use App\Models\ResumeUpload;
use App\Services\ResumeCandidateSync;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Phase 8 — Candidate-side empty-fill semantics.
 *
 * The contract — explicitly NOT a generic updater:
 *   1. A pre-existing recruiter-typed value is NEVER overwritten.
 *   2. The first non-empty value seen during a session wins.
 *   3. The phone column is digits-only after sanitization.
 *   4. Tenant guard: a candidate row under a different company
 *      is unreachable from this code path.
 *   5. After a successful UPDATE, ElasticCandidateIndex is
 *      dispatched on the `emails` queue (same convention as the
 *      controller's submit path).
 *   6. When no columns are fillable (recruiter already typed
 *      everything), the method returns 0 and dispatches nothing.
 */
class ResumeCandidateSyncTest extends TestCase
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
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('job_applications', function ($t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('company_id');
            $t->string('full_name', 255)->nullable();
            $t->string('email', 255)->nullable();
            $t->string('phone', 32)->nullable();
            $t->string('current_company', 255)->nullable();
            $t->string('current_designation', 255)->nullable();
            $t->text('careersummary')->nullable();
            $t->timestamp('created_at')->nullable();
            $t->timestamp('updated_at')->nullable();
        });
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
            $t->timestamps();
            $t->unique('uuid');
        });
    }

    private function makeCandidate(int $companyId, array $values): int
    {
        return DB::table('job_applications')->insertGetId(array_merge([
            'company_id' => $companyId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ], $values));
    }

    private function makeResumeUpload(int $companyId, int $candidateId, array $parsed): ResumeUpload
    {
        $uuid = 'aaaaaaaa-bbbb-cccc-dddd-' . random_int(100000000, 999999999);
        $path = "Companies/test_{$companyId}/Resumes/2026/07/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        return ResumeUpload::create([
            'uuid' => $uuid,
            'company_id' => $companyId,
            'candidate_id' => $candidateId,
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => basename($path),
            'r2_path' => $path,
            'r2_url' => $url,
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'status' => ResumeUpload::STATUS_PARSED,
            'parsed_data_json' => $parsed,
            'parser_vendor' => 'phase2_stub',
            'parse_completed_at' => Carbon::now(),
        ]);
    }

    public function test_fills_only_empty_columns_on_candidate_row(): void
    {
        $cid = $this->makeCandidate(7, [
            'full_name' => null,
            'email' => null,
            'phone' => null,
            'current_company' => null,
            'current_designation' => null,
            'careersummary' => null,
        ]);
        $row = $this->makeResumeUpload(7, $cid, [
            'full_name' => 'Smita Sharma',
            'email' => 'smita@example.com',
            'phone' => '+91 98765 43210',
            'current_company' => 'Popular Digital AI',
            'current_designation' => 'Senior Engineer',
            'careersummary' => 'Builds the resume subsystem.',
        ]);

        $filled = app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        $this->assertSame(1, $filled);

        $candidate = DB::table('job_applications')->where('id', $cid)->first();
        $this->assertSame('Smita Sharma', $candidate->full_name);
        $this->assertSame('smita@example.com', $candidate->email);
        $this->assertSame('919876543210', $candidate->phone);    // digits-only
        $this->assertSame('Popular Digital AI', $candidate->current_company);
        $this->assertSame('Senior Engineer', $candidate->current_designation);
        $this->assertSame('Builds the resume subsystem.', $candidate->careersummary);

        // Reindex is dispatched on emails queue, same convention
        // as the controller's submit path. The job's primary key
        // is the company_id passed as `comid` (NOT `id`, which
        // is reserved by the Job base class).
        Queue::assertPushed(ElasticCandidateIndex::class, function ($job) use ($cid) {
            return (int) $job->comid === $cid;
        });
    }

    public function test_never_overwrites_recruiter_typed_values(): void
    {
        $cid = $this->makeCandidate(7, [
            'full_name' => 'Recruiter Typed Name',
            'email' => 'recruiter-typed@example.com',
            'phone' => '9999999999',
            'current_company' => null,                       // empty — parse will fill
            'current_designation' => null,
            'careersummary' => null,
        ]);
        $row = $this->makeResumeUpload(7, $cid, [
            'full_name' => 'Parsed Parsed Sharma',
            'email' => 'parsed@example.com',
            'phone' => '9876543210',
            'current_company' => 'Popular Digital AI',
            'current_designation' => 'Senior Engineer',
            'careersummary' => 'Builds the resume subsystem.',
        ]);

        $filled = app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        // Only the three empty columns are written.
        $this->assertSame(1, $filled);

        $candidate = DB::table('job_applications')->where('id', $cid)->first();
        $this->assertSame('Recruiter Typed Name', $candidate->full_name);       // preserved
        $this->assertSame('recruiter-typed@example.com', $candidate->email);    // preserved
        $this->assertSame('9999999999', $candidate->phone);                     // preserved
        $this->assertSame('Popular Digital AI', $candidate->current_company);   // filled
        $this->assertSame('Senior Engineer', $candidate->current_designation);  // filled
        $this->assertSame('Builds the resume subsystem.', $candidate->careersummary); // filled
    }

    public function test_returns_zero_when_all_columns_already_filled(): void
    {
        $cid = $this->makeCandidate(7, [
            'full_name' => 'A',
            'email' => 'a@example.com',
            'phone' => '1111111111',
            'current_company' => 'B',
            'current_designation' => 'C',
            'careersummary' => 'D',
        ]);
        $row = $this->makeResumeUpload(7, $cid, [
            'full_name' => 'X', 'email' => 'x@example.com', 'phone' => '2222222222',
            'current_company' => 'Y', 'current_designation' => 'Z', 'careersummary' => 'W',
        ]);
        $filled = app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        $this->assertSame(0, $filled);
        // No reindex dispatched.
        Queue::assertNotPushed(ElasticCandidateIndex::class);

        // Recruiter-typed values not touched.
        $candidate = DB::table('job_applications')->where('id', $cid)->first();
        $this->assertSame('A', $candidate->full_name);
        $this->assertSame('a@example.com', $candidate->email);
        $this->assertSame('1111111111', $candidate->phone);
    }

    public function test_phone_normalization_strips_dashes_spaces_country_code_prefix(): void
    {
        $cid = $this->makeCandidate(7, []);
        $row = $this->makeResumeUpload(7, $cid, [
            'phone' => '+91 (98765) 43210 ext.42',
        ]);
        app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        $candidate = DB::table('job_applications')->where('id', $cid)->first();
        $this->assertSame('91987654321042', $candidate->phone);    // digits only
    }

    public function test_no_dispatch_when_session_lacks_candidate_id(): void
    {
        // Session is in Parsed state but candidate_id is null —
        // the recruiter has not yet submitted. The sync is a
        // no-op even when parsed_data_json is rich.
        $cid = $this->makeCandidate(7, []);
        $row = ResumeUpload::create([
            'uuid' => '12345678-1234-1234-1234-123456789012',
            'company_id' => 7,
            'candidate_id' => null,                    // <— key
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => 'cv.pdf',
            'r2_path' => 'Companies/test_7/Resumes/2026/07/JERO_cv.pdf',
            'r2_url' => 'https://file.jero.works/Companies/test_7/Resumes/2026/07/JERO_cv.pdf',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'status' => ResumeUpload::STATUS_PARSED,
            'parsed_data_json' => [
                'full_name' => 'Smita Sharma', 'email' => 'a@b.c', 'phone' => '98765',
            ],
        ]);
        $filled = app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        $this->assertSame(0, $filled);
        Queue::assertNotPushed(ElasticCandidateIndex::class);
    }

    public function test_no_dispatch_when_parsed_data_json_is_empty(): void
    {
        $cid = $this->makeCandidate(7, []);
        $row = $this->makeResumeUpload(7, $cid, []);    // phase2_stub fingerprint-only is non-empty; force empty.
        // Wipe the parsed data — Phase-3 vendor will populate;
        // today's Phase-2 stub fills fingerprint.
        $row->parsed_data_json = [];
        $row->save();
        $filled = app(ResumeCandidateSync::class)->fillEmptyColumnsFromParsed($row);
        $this->assertSame(0, $filled);
        Queue::assertNotPushed(ElasticCandidateIndex::class);
    }
}
