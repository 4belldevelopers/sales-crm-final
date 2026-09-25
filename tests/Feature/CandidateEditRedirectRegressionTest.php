<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Protects candidate edit updates from malformed tracker redirects and error-response navigation.
 */
class CandidateEditRedirectRegressionTest extends TestCase
{
    public function test_update_uses_named_tracker_routes_with_a_safe_fallback(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $update = $this->between($controller, 'public function update(Request $request, $id)', 'public function formatInterviewDateTime');

        foreach ([
            'service_company.cvtracker.index',
            'service_company.cvtracker.allcandidates',
            'service_company.apptracker.clientapp',
            'service_company.apptracker.index',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), 'Missing redirect route: '.$routeName);
        }

        $this->assertStringContainsString("route('service_company.cvtracker.index')", $update);
        $this->assertStringContainsString("route('service_company.cvtracker.allcandidates')", $update);
        $this->assertStringContainsString("route('service_company.apptracker.clientapp'", $update);
        $this->assertStringContainsString("route('service_company.apptracker.index'", $update);
        $this->assertStringContainsString("'mstatus_id' => 'all'", $update);
        $this->assertStringNotContainsString("url('/service_company/apptracker/", $update);
    }

    public function test_update_page_redirects_only_after_a_success_response_with_a_url(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $updateScript = $this->between($form, 'var KTUPDATECandidate = function ()', 'var KTAddCandidate = function ()');

        $this->assertStringContainsString("response.status !== 'success'", $updateScript);
        $this->assertStringContainsString("typeof response.url !== 'string'", $updateScript);
        $this->assertStringContainsString('window.location.assign(response.url)', $updateScript);
        $this->assertStringNotContainsString('window.location = response.url', $updateScript);
    }

    public function test_candidate_drawer_edit_link_keeps_the_job_wise_candidate_contract(): void
    {
        $drawer = $this->source('resources/views/service_company/job-applications/showdetail.blade.php');

        $this->assertStringContainsString("route('service_company.job-applications.edit'", $drawer);
        $this->assertStringContainsString('GeneralMethod::value_encrypt($jobWiseCanId)', $drawer);
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
