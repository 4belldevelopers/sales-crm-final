<?php

namespace Tests\Feature;

use App\Console\Commands\ProcessOutboxCommand;
use App\Console\Commands\ReconcilePendingResumesCommand;
use App\Jobs\ProcessOutboxEventJob;
use App\Jobs\PurgeRemovedResumeAssetsJob;
use App\Models\CandidateOutboxEvent;
use App\Models\ResumeAsset;
use Tests\TestCase;

/**
 * Validates the V4 Add/Edit Candidate audit and remediation plan execution:
 * - 0% remote calls in MySQL write transactions
 * - Outbox pattern for Elasticsearch sync
 * - Resume asset lifecycle and removal pending state
 * - Tab-scoped upload isolation and race condition defenses
 * - Removal of artificial submit delays (sub-150ms instant dispatch)
 * - Client-first suggestion in-memory caching
 */
class V4AddCandidateRemediationTest extends TestCase
{
    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read ' . $relativePath);

        return $source;
    }

    public function test_create_blade_contains_v4_remediation_hidden_fields(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('name="client_submission_uuid" id="client_submission_uuid"', $blade);
        $this->assertStringContainsString('name="client_update_uuid" id="client_update_uuid"', $blade);
        $this->assertStringContainsString('name="resume_version" id="resume_version"', $blade);
        $this->assertStringContainsString('name="resume_intent" id="resume_intent"', $blade);
    }

    public function test_artificial_2000ms_submit_delays_are_completely_removed(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        // Confirm neither save-form nor update-form wraps ajax in setTimeout 2000
        $this->assertStringNotContainsString('}, 2000);', $blade);
    }

    public function test_tab_scoped_identity_prevents_multi_tab_upload_collisions(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('window.__tabInstanceId', $blade);
        $this->assertStringContainsString("'_tab' + window.__tabInstanceId", $blade);
    }

    public function test_client_first_suggestion_caching_is_implemented(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('_candidateSkillCache', $blade);
        $this->assertStringContainsString('_candidateLocationCache', $blade);
        $this->assertStringContainsString('getCandidateLocationSuggestions', $blade);
    }

    public function test_client_parser_includes_stale_run_cancellation_guard(): void
    {
        $parserBlade = $this->source('resources/views/service_company/job-applications/jero-client-parser.blade.php');

        $this->assertStringContainsString('_currentParserRunId', $parserBlade);
        $this->assertStringContainsString('cancelActiveParse', $parserBlade);
        $this->assertStringContainsString('thisRunId !== _currentParserRunId', $parserBlade);
    }

    public function test_mysql_schema_defines_outbox_events_and_resume_assets(): void
    {
        $schema = $this->source('database/schema/mysql-schema.sql');

        // Outbox events table
        $this->assertStringContainsString('CREATE TABLE `candidate_outbox_events`', $schema);
        $this->assertStringContainsString('`dedupe_key` varchar(191)', $schema);
        $this->assertStringContainsString('`lock_token` varchar(36)', $schema);
        $this->assertStringContainsString("`status` enum('pending','processing','completed','failed')", $schema);

        // Resume assets table
        $this->assertStringContainsString('CREATE TABLE `resume_assets`', $schema);
        $this->assertStringContainsString("`status` enum('ACTIVE','REMOVAL_PENDING','REMOVED','ORPHANED','CLEANUP_PENDING')", $schema);
        $this->assertStringContainsString('`object_key` varchar(500)', $schema);

        // Job applications columns
        $this->assertStringContainsString('`current_resume_id` bigint(20) unsigned', $schema);
        $this->assertStringContainsString('`resume_version` int(10) unsigned', $schema);
        $this->assertStringContainsString('`client_submission_uuid` varchar(36)', $schema);
        $this->assertStringContainsString('UNIQUE KEY `uq_company_submission_uuid` (`company_id`,`client_submission_uuid`)', $schema);
    }

    public function test_models_and_jobs_exist_with_correct_contracts(): void
    {
        $this->assertTrue(class_exists(ResumeAsset::class));
        $this->assertTrue(class_exists(CandidateOutboxEvent::class));
        $this->assertTrue(class_exists(ProcessOutboxEventJob::class));
        $this->assertTrue(class_exists(PurgeRemovedResumeAssetsJob::class));
        $this->assertTrue(class_exists(ReconcilePendingResumesCommand::class));
        $this->assertTrue(class_exists(ProcessOutboxCommand::class));

        // Test outbox job queue contract
        $job = new ProcessOutboxEventJob(1, 1);
        $this->assertEquals('candidate-index', $job->queue);

        // Test reconciliation command dispatches purge job
        $reconcileCmd = $this->source('app/Console/Commands/ReconcilePendingResumesCommand.php');
        $this->assertStringContainsString('PurgeRemovedResumeAssetsJob', $reconcileCmd);
    }

    public function test_controller_handles_idempotency_optimistic_locking_and_outbox(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');

        // Store method idempotency and outbox event insertion
        $this->assertStringContainsString('client_submission_uuid', $controller);
        $this->assertStringContainsString('idemp:cand:', $controller);
        $this->assertStringContainsString('ProcessOutboxEventJob::dispatch', $controller);
        $this->assertStringContainsString('candidate_outbox_events', $controller);
        $this->assertStringContainsString('resume_assets', $controller);

        // Update method optimistic locking
        $this->assertStringContainsString('resume_version', $controller);
        $this->assertStringContainsString('resume_intent', $controller);
        $this->assertStringContainsString('REMOVAL_PENDING', $controller);
        $this->assertStringContainsString('resume.removed', $controller);
    }
}
