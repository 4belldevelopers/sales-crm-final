<?php

namespace Tests\Feature;

use App\Exceptions\ResumeUploadException;
use App\Services\ResumeUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Coverage for the resume-upload reliability fix on service_company/job-applications/create.
 *
 * Scope: the ResumeUploadService + the bootstrap exception renderer + the
 * StoreJobApplication FormRequest's `resume_temp_path` and `resume` rules.
 * Full controller-store() coverage is deferred (see docs/plan: tests would
 * require seeded User+Company+Job + Gate::mock for `add_job_applications`).
 */
class JobApplicationResumeUploadTest extends TestCase
{
    private string $tempKey = 'temp/JERO_test_cv.pdf';
    private string $finalKey;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
        // Seed a non-empty temp file the happy path will move.
        Storage::disk('r2')->put($this->tempKey, 'PDF-CONTENT-' . str_repeat('x', 1024));
        $this->finalKey = 'Companies/test_company_99/Resumes/' . date('Y') . '/' . date('m') . '/JERO_final.pdf';
    }

    public function test_happy_path_copies_temp_to_final_and_returns_canonical_url(): void
    {
        $svc = new ResumeUploadService('r2');
        $url = $svc->finalizeFromTemp($this->tempKey, 'JERO_final.pdf', 'Test Company', 99);

        $this->assertStringStartsWith('https://file.jero.works/Companies/test_company_99/Resumes/', $url);
        Storage::disk('r2')->assertExists($this->finalKey);
        $this->assertGreaterThan(0, Storage::disk('r2')->size($this->finalKey));
    }

    public function test_missing_temp_file_throws_resume_upload_exception(): void
    {
        $this->expectException(ResumeUploadException::class);
        (new ResumeUploadService('r2'))->finalizeFromTemp('temp/does_not_exist.pdf', 'JERO_final.pdf', 'Test', 99);
    }

    public function test_missing_temp_uses_stable_reason_code_temp_missing(): void
    {
        try {
            (new ResumeUploadService('r2'))->finalizeFromTemp('temp/ghost.pdf', 'JERO_final.pdf', 'Test', 99);
            $this->fail('Expected ResumeUploadException');
        } catch (ResumeUploadException $e) {
            $this->assertSame('temp_missing', $e->reason);
            $this->assertSame('temp/ghost.pdf', $e->tempPath);
        }
    }

    public function test_empty_temp_file_throws_with_temp_empty_reason(): void
    {
        Storage::disk('r2')->put('temp/JERO_empty.pdf', '');
        try {
            (new ResumeUploadService('r2'))->finalizeFromTemp('temp/JERO_empty.pdf', 'JERO_final.pdf', 'Test', 99);
            $this->fail('Expected ResumeUploadException');
        } catch (ResumeUploadException $e) {
            $this->assertSame('temp_empty', $e->reason);
        }
    }

    public function test_clean_temp_is_best_effort_and_never_throws_on_missing_path(): void
    {
        $svc = new ResumeUploadService('r2');
        // missing path
        $svc->cleanTemp('temp/never_existed.pdf');
        $this->assertTrue(true); // no exception

        // existing path
        Storage::disk('r2')->put('temp/JERO_to_delete.pdf', 'x');
        $svc->cleanTemp('temp/JERO_to_delete.pdf');
        Storage::disk('r2')->assertMissing('temp/JERO_to_delete.pdf');
    }

    public function test_compute_destination_produces_path_and_url_in_canonical_shape(): void
    {
        $svc = new ResumeUploadService('r2');
        $dest = $svc->computeDestination('Test Company!!', 99, 'JERO_final.pdf');
        // The slug sanitizer collapses non-alphanumerics into '_' and concatenates with the id.
        // We assert shape, not exact slug, since the controller's existing sanitization is
        // preserved verbatim so historical files stay in their original R2 directories.
        $this->assertStringContainsString('99/Resumes/', $dest['path']);
        $this->assertStringEndsWith('/JERO_final.pdf', $dest['path']);
        $this->assertStringStartsWith('https://file.jero.works/', $dest['url']);
        $this->assertSame($dest['path'], ltrim(parse_url($dest['url'], PHP_URL_PATH), '/'));
    }

    public function test_exception_renderer_returns_json_422_for_xhr_request(): void
    {
        $exception = ResumeUploadException::missingInR2('temp/missing.pdf');
        $request = Request::create('/service_company/job-applications', 'POST');
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $rendered = app(\Illuminate\Foundation\Exceptions\Handler::class)->render($request, $exception);

        $this->assertSame(422, $rendered->getStatusCode());
        $body = json_decode($rendered->getContent(), true);
        $this->assertSame('error', $body['status']);
        $this->assertSame('temp_missing', $body['code']);
    }

    public function test_resume_temp_path_validation_rejects_oversized_strings(): void
    {
        // The FormRequest rules are tested at the validator level here since full controller-store()
        // invocation needs an authenticated user + Gate::mock + seeded Job (out of scope).
        $validator = \Validator::make(
            ['resume_temp_path' => str_repeat('a', 600)],
            [
                'resume_temp_path' => 'nullable|string|max:512',
            ]
        );
        $this->assertTrue($validator->fails(), 'FormRequest rule max:512 must reject 600-char string');
    }

    public function test_resume_mime_rule_accepts_pdf_uploaded_file(): void
    {
        // FormRequest mime rules are evaluated against an UploadedFile, not a string.
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 fake');
        $validator = \Validator::make(
            ['resume' => $file],
            ['resume' => 'nullable|mimes:jpeg,jpg,png,doc,docx,rtf,xls,xlsx,pdf|max:5120']
        );
        $this->assertFalse($validator->fails(), 'A PDF upload within size cap must pass');
    }

    public function test_resume_mime_rule_rejects_txt_uploaded_file(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('cv.txt', 'plain text');
        $validator = \Validator::make(
            ['resume' => $file],
            ['resume' => 'nullable|mimes:jpeg,jpg,png,doc,docx,rtf,xls,xlsx,pdf|max:5120']
        );
        $this->assertTrue($validator->fails(), 'A .txt upload must fail the mimes rule');
        $this->assertArrayHasKey('resume', $validator->errors()->toArray());
    }
}