<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protects the Add Candidate resume uploader's direct-to-R2 path and its
 * page-local same-origin circuit breaker without changing candidate save logic.
 */
class CandidateResumeUploadFallbackTest extends TestCase
{
    public function test_browser_upload_requires_the_complete_v2_feature_set(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString("asset('js/resume-presign.js') }}?v=20260924-1", $form);
        $this->assertStringContainsString("config('resumes.features.browser_upload')", $form);
        $this->assertStringContainsString("config('resumes.features.presign_endpoint')", $form);
        $this->assertStringContainsString("config('resumes.features.finalize_endpoint')", $form);
        $this->assertStringContainsString("config('resumes.features.submit_with_session')", $form);
        $this->assertStringContainsString("Schema::hasTable('resume_uploads')", $form);
    }

    public function test_direct_r2_transport_failures_use_the_existing_server_fallback(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('function shouldUseResumeUploadFallback(error)', $form);
        $this->assertStringContainsString("message.indexOf('R2 upload failed')", $form);
        $this->assertStringContainsString("message.indexOf('Network error during upload')", $form);
        $this->assertStringContainsString("message.indexOf('Upload timed out')", $form);
        $this->assertStringContainsString('uploadGeneration !== resumeUploadGeneration', $form);
        $this->assertStringContainsString('window.ResumePresign.cancelUpload(getUpContextKey())', $form);
        $this->assertStringContainsString("$('#resume_session_uuid').val('')", $form);
        $this->assertStringContainsString('uploadWithProgress(file)', $form);
    }

    public function test_server_fallback_preserves_the_existing_raw_byte_upload_contract(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $routes = $this->source('routes/service.php');
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $upload = $this->between($controller, 'public function uploadresume(Request $request)', 'public function unsetFile(Request $request)');

        $this->assertStringContainsString('name="uploaded_file_path" id="uploaded_file_path"', $form);
        $this->assertStringContainsString('name="resume_original_name" id="resume_original_name"', $form);
        $this->assertStringContainsString("Route::post('uploadresume'", $routes);
        $this->assertStringContainsString("fopen('php://input', 'r')", $upload);
        $this->assertStringContainsString("Storage::disk('r2')->put(\$objectKey, \$stream)", $upload);
        $this->assertStringContainsString("'file_path' => \$objectKey", $upload);
        $this->assertStringContainsString("'original_name' => \$originalName", $upload);
    }

    public function test_candidate_save_accepts_fallback_fields_when_no_v2_session_exists(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $store = $this->between($controller, 'public function store(Request $request)', 'public function googleSheetCandidateData');

        $this->assertStringContainsString("\$request->filled('resume_session_uuid')", $store);
        $this->assertStringContainsString("\$request->input('uploaded_file_path')", $store);
        $this->assertStringContainsString("\$request->input('resume_original_name')", $store);
        $this->assertStringContainsString('UploadResumeToTestRTwo::dispatch(', $store);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }

    private function between(string $source, string $startMarker, string $endMarker): string
    {
        $start = strpos($source, $startMarker);
        $this->assertNotFalse($start, 'Missing start marker: '.$startMarker);
        $end = strpos($source, $endMarker, $start + strlen($startMarker));
        $this->assertNotFalse($end, 'Missing end marker: '.$endMarker);

        return substr($source, $start, $end - $start);
    }
}
