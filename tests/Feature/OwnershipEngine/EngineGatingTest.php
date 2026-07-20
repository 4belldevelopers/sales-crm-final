<?php

namespace Tests\Feature\OwnershipEngine;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 3 (Finding 3, docs/OWNERSHIP_ENGINE_DESIGN.md): app_duplicate = 0 must disable the
 * engine entirely for that company — no cluster, no event, no cache write — so the new
 * engine's evaluation conditions stay comparable to the old
 * checkDuplicateCandidate()/resolveDuplicateOwnership() logic for Phase 6.
 *
 * Requires a real MySQL 8 connection (see SchemaTest).
 */
class EngineGatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_reservation_is_a_no_op_when_app_duplicate_is_disabled(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => 'Gating Test Co',
            'company_email' => uniqid().'@example.test',
            'locale' => 'en',
            'app_duplicate' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jobId = DB::table('jobs')->insertGetId([
            'title' => 'Test Job',
            'company_id' => $companyId,
            'job_description' => 'x',
            'job_requirement' => 'x',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'required_columns' => '',
            'meta_details' => '',
            'section_visibility' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $applicationId = DB::table('job_applications')->insertGetId([
            'submitted_by' => 1,
            'email' => 'gating@example.test',
            'phone' => '9990000000',
            'created_at' => now(),
        ]);

        $rowId = DB::table('add_job_wise_condidates')->insertGetId([
            'job_id' => $jobId,
            'job_application_id' => $applicationId,
            'company_id' => $companyId,
            'thirdparty_company_id' => 0,
            'master_status_id' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(OwnershipEngine::class)->attemptCompanyReservation($rowId);

        $this->assertNull($result->state);
        $this->assertSame(0, DB::table('ownership_clusters')->count());
        $this->assertSame(0, DB::table('ownership_events')->count());
        $this->assertNull(DB::table('add_job_wise_condidates')->where('id', $rowId)->value('ownership_state'));
    }
}
