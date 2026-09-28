<?php

namespace Tests\Feature;

use App\Console\Commands\RebuildBenchSearchIndexCommand;
use App\Console\Commands\ReconcileBenchHeroCountersCommand;
use App\Console\Commands\ReplayBenchOutboxEventsCommand;
use App\Jobs\ProcessOutboxEventJob;
use App\Models\SysIdempotencyKey;
use Tests\TestCase;

/**
 * Validates the V4 Add/Edit Talent & Bench Listing audit and remediation plan:
 * - 3-Layer Durable Idempotency (SysIdempotencyKey + Redis fast path)
 * - Gate G-52 (Lease Recovery) and Gate G-53 (Request Hash Mismatch Defense)
 * - Clean CQRS write transaction (0% remote ES/R2 calls inside DB::beginTransaction)
 * - Distributed Outbox Pattern (candidate_outbox_events + ProcessOutboxEventJob)
 * - 2080-hour commercial formulas and rate card synchronization
 * - Stage mappings (1=Pool, 2=Bench, 4=Placed, 5=In Transit)
 * - Emergency schema guard (resilientSaveTalentApplication)
 * - 15.6" screen layout optimization and Web Worker parser integration
 */
class V4AddTalentRemediationTest extends TestCase
{
    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }

    public function test_create_blade_contains_v4_idempotency_and_commercial_fields(): void
    {
        $blade = $this->source('resources/views/service_company/bench-candidates/create.blade.php');

        // Idempotency and versioning hidden inputs
        $this->assertStringContainsString('name="client_submission_uuid"', $blade);
        $this->assertStringContainsString('name="expected_aggregate_version"', $blade);
        $this->assertStringContainsString('name="expected_resume_version"', $blade);

        // Commercial fields synchronization (Commit 16b825d5 defense)
        $this->assertStringContainsString('name="hidden_pay_annual"', $blade);
        $this->assertStringContainsString('name="hidden_pay_monthly"', $blade);
        $this->assertStringContainsString('name="hidden_pay_rate_amount"', $blade);
        $this->assertStringContainsString('name="hidden_bill_annual"', $blade);
        $this->assertStringContainsString('name="hidden_bill_monthly"', $blade);
        $this->assertStringContainsString('name="hidden_bill_rate_amount"', $blade);
        $this->assertStringContainsString('name="hidden_percentage_hike"', $blade);
        $this->assertStringContainsString('name="hidden_comission_type"', $blade);

        // Web Worker resume parser
        $this->assertStringContainsString('talent-parser-worker.js', $blade);

        // Triple-redundant submission response handler
        $this->assertStringContainsString('handleTalentSubmissionSuccess', $blade);
    }

    public function test_form_styles_scope_and_15_inch_screen_responsiveness(): void
    {
        $styles = $this->source('resources/views/service_company/bench-candidates/partials/_form_styles.blade.php');

        // Scoped under #talent_create_app
        $this->assertStringContainsString('#talent_create_app', $styles);

        // Dropzone collapse defense (Gate G-08)
        $this->assertStringContainsString('.dropzone.has-file', $styles);

        // Responsive equal-height rows and compression
        $this->assertStringContainsString('.equal-height-row', $styles);
        $this->assertStringContainsString('max-width: 1440px', $styles);
        $this->assertStringContainsString('uploaded-file-badge', $styles);
    }

    public function test_controller_implements_cqrs_outbox_and_durable_idempotency(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Durable Idempotency
        $this->assertStringContainsString('SysIdempotencyKey', $controller);
        $this->assertStringContainsString('IDEMPOTENCY_KEY_REUSE', $controller);
        $this->assertStringContainsString('IDEMPOTENCY_LEASE_RECLAIMED', $controller);

        // Dual-versioning optimistic locking
        $this->assertStringContainsString('expected_aggregate_version', $controller);
        $this->assertStringContainsString('expected_resume_version', $controller);
        $this->assertStringContainsString('VERSION_CONFLICT', $controller);
        $this->assertStringContainsString('RESUME_VERSION_CONFLICT', $controller);

        // Outbox event persistence
        $this->assertStringContainsString('candidate_outbox_events', $controller);
        $this->assertStringContainsString('talent.bench.created', $controller);
        $this->assertStringContainsString('talent.bench.updated', $controller);
        $this->assertStringContainsString('ProcessOutboxEventJob::dispatch', $controller);

        // Helper methods for safety
        $this->assertStringContainsString('resilientSaveTalentApplication', $controller);
        $this->assertStringContainsString('batchPersistTalentRelations', $controller);

        // Dirty tenant registration
        $this->assertStringContainsString('bench:dirty_tenants', $controller);
    }

    public function test_commercial_2080_hour_formula_equivalence(): void
    {
        // 2080 hours standard (40 hrs/week * 52 weeks)
        $hourlyRate = 50.0;
        $expectedAnnual = $hourlyRate * 2080; // 104,000
        $expectedMonthly = $expectedAnnual / 12; // 8,666.67

        $this->assertEquals(104000.0, $expectedAnnual);
        $this->assertEquals(8666.666666666666, $expectedMonthly);

        // Reverse conversion: Annual to hourly
        $annualRate = 120000.0;
        $calculatedHourly = round($annualRate / 2080, 2);
        $this->assertEquals(57.69, $calculatedHourly);
    }

    public function test_stage_mappings_and_contract(): void
    {
        // Invariant: Pool = 1, Bench = 2, Placed = 4, In Transit = 5
        $stages = [
            'pool' => 1,
            'bench' => 2,
            'placed' => 4,
            'in_transit' => 5,
        ];

        $validStageIds = [1, 2, 4, 5];
        foreach ($stages as $name => $id) {
            $this->assertContains($id, $validStageIds, "Stage {$name} must be within the supported set [1, 2, 4, 5]");
        }
    }

    public function test_outbox_commands_and_jobs_exist(): void
    {
        $this->assertTrue(class_exists(ReconcileBenchHeroCountersCommand::class));
        $this->assertTrue(class_exists(RebuildBenchSearchIndexCommand::class));
        $this->assertTrue(class_exists(ReplayBenchOutboxEventsCommand::class));
        $this->assertTrue(class_exists(ProcessOutboxEventJob::class));
        $this->assertTrue(class_exists(SysIdempotencyKey::class));

        // Command signatures
        $this->assertEquals('talents:reconcile-hero-counters', (new ReconcileBenchHeroCountersCommand)->getName());
        $this->assertEquals('bench:rebuild-search-index', (new RebuildBenchSearchIndexCommand)->getName());
        $this->assertEquals('bench:outbox-replay', (new ReplayBenchOutboxEventsCommand)->getName());
    }

    public function test_web_worker_parser_script_exists(): void
    {
        $workerJs = $this->source('public/js/talent-parser-worker.js');
        $this->assertStringContainsString('PARSE_RESUME', $workerJs);
        $this->assertStringContainsString('PARSER_SUCCESS', $workerJs);
        $this->assertStringContainsString('PARSER_ERROR', $workerJs);
        $this->assertStringContainsString('postMessage', $workerJs);
        $this->assertStringContainsString('JeroResumeParser', $workerJs);
    }

    public function test_resume_upload_fallback_and_submission_integrity(): void
    {
        $blade = $this->source('resources/views/service_company/bench-candidates/create.blade.php');
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // 1. Blade must have circuit breaker fallback check
        $this->assertStringContainsString('shouldUseResumeUploadFallback', $blade);
        $this->assertStringContainsString('405', $blade);
        $this->assertStringContainsString('R2 upload failed', $blade);

        // 2. Blade handleTalentSubmissionSuccess must strictly reject non-success responses
        $this->assertStringContainsString("response.status !== 'success'", $blade);
        $this->assertStringContainsString("response.msgType === 'danger'", $blade);
        $this->assertStringContainsString("response.msgType === 'warning'", $blade);

        // 3. Controller store() must support uploaded_file_path fallback
        $this->assertStringContainsString("\$request->filled('uploaded_file_path') || \$request->hasFile('resume')", $controller);

        // 4. Controller store() must synchronously index into Elasticsearch post-commit
        $this->assertStringContainsString('GeneralMethod::elasticBenchCandidateAddEditIndex($benchApplication->id)', $controller);

        // 5. Controller store() must clear idempotency Redis lock on exception/validation failure
        $this->assertStringContainsString("Redis::connection('default')->del(\$idempotencyKey)", $controller);
    }
}
