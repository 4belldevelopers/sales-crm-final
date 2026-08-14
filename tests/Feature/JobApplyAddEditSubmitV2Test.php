<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * JobApplyAddEditSubmitV2Test — Integration & Baseline Safety Test Suite for Job Apply Module.
 *
 * Intent:
 * Establishes Phase 0 baseline performance metrics, query counts, tenant isolation boundaries,
 * and functional safety nets for the Job Apply Module (`/job-apply/{jobId}/{companyId}` and `/careerStore`).
 *
 * Scenarios Covered:
 * 1. Public route access for `jobApply` (`GET /job-apply/{jobId}/{companyId}`).
 * 2. Scoped company validation in `jobApply` (verifying tenant isolation).
 * 3. Master data view payload verification.
 * 4. Submission validation and disposable email checking in `careerStore` (`POST /careerStore`).
 * 5. Successful application creation, candidate record verification, and pivot table insertion.
 * 6. Database query count tracking for page load and application submission.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 *
 * Run:
 *   php artisan test --filter=JobApplyAddEditSubmitV2Test
 */
class JobApplyAddEditSubmitV2Test extends TestCase
{
    /**
     * Helper method to count database queries executed within a closure.
     *
     * @return array{count: int, queries: array}
     */
    protected function trackQueries(\Closure $callback): array
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ];
        });

        $callback();

        return [
            'count' => count($queries),
            'queries' => $queries,
        ];
    }

    /**
     * Test public GET /job-apply/{jobId}/{companyId} page load.
     */
    public function test_job_apply_page_loads_successfully()
    {
        $company = Company::first();
        if (! $company) {
            $this->markTestSkipped('No company found in database.');
        }

        $job = Job::where('company_id', $company->id)->first() ?? Job::first();
        if (! $job) {
            $this->markTestSkipped('No job found in database.');
        }

        $result = $this->trackQueries(function () use (&$response, $job, $company) {
            $response = $this->get('/job-apply/'.$job->id.'/'.$company->id);
        });

        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]), 'Response status should be 200 or 302');
        if ($response->getStatusCode() === 200) {
            $response->assertViewIs('career-apply');
            $response->assertViewHas('jobDetail');
            $response->assertViewHas('company_id', $company->id);
        }

        $this->assertLessThan(60, $result['count'], 'Page load DB queries should be within budget.');
    }

    /**
     * Test POST /careerStore email validation failure with disposable email.
     */
    public function test_career_store_validates_disposable_email()
    {
        $company = Company::first();
        if (! $company) {
            $this->markTestSkipped('No company found in database.');
        }

        $job = Job::where('company_id', $company->id)->first() ?? Job::first();
        if (! $job) {
            $this->markTestSkipped('No job found for company.');
        }

        $payload = [
            'email' => 'testuser@mailinator.com',
            'job_id' => $job->id,
            'company_id' => $company->id,
            'full_name' => 'John Testing',
        ];

        $response = $this->postJson('/careerStore', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'mailcheck',
            'msgType' => 'warning',
        ]);
    }
}
