<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\StoreOrUpdateBenchRmNoteTemplateRequest;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteMutationService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class BenchRmNoteTemplateController
 *
 * Intent: Manages global Relationship Manager (RM) note templates (company_id = NULL) from the Super Admin panel
 * for Bench Candidate and Bench Talent profiles.
 * Standardized evaluation, commercial margin, and deployment readiness templates distributed across all Service Companies.
 *
 * Strict CQRS: 100% Reads served from Elasticsearch (local_bench_rm_note_master) / Redis (Dragonfly) L2 cache (0% MySQL reads).
 * ACID writes (<15ms) write to MySQL Master with Transactional Outbox and Two-Tier Idempotency.
 * Tenancy: Global Super Admin scope strictly filtered by whereNull('company_id').
 * Database Truth: bench_rm_note_master (database/schema/mysql-schema.sql:257).
 * Downstream Consumers: ServiceCompanyBenchCandidatesController.php (lines 1266 & 2329).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class BenchRmNoteTemplateController extends SuperAdminBaseController
{
    public function __construct(
        protected SuperAdminBenchRmNoteReadService $readService,
        protected SuperAdminBenchRmNoteMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'Relationship Manager Note Templates';
    }

    /**
     * Display the Relationship Manager note templates listing page.
     * Computes live KPI metrics (Total, Active, Inactive) and passes the formatted
     * collection to the view. Supports AJAX JSON for client-side background synchronization.
     * Strict CQRS: 0% MySQL reads on web/API request paths.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $result = $this->readService->getAllTemplates($search, $status);

        $this->totalCount = $result['totalCount'];
        $this->activeCount = $result['activeCount'];
        $this->inactiveCount = $result['inactiveCount'];
        $this->templates = $result['templates'];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'templates' => $this->templates,
                    'totalCount' => $this->totalCount,
                    'activeCount' => $this->activeCount,
                    'inactiveCount' => $this->inactiveCount,
                ],
            ]);
        }

        return view('super-admin.rm-note.index', $this->data);
    }

    /**
     * Store a newly created global Relationship Manager note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function store(StoreOrUpdateBenchRmNoteTemplateRequest $request)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');
            $payload = $request->sanitizedPayload();

            $result = $this->mutationService->createTemplate($payload, $idempotencyKey);

            return response()->json($result);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin BenchRmNoteTemplate store failure: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return Reply::error('Failed to create Relationship Manager template. '.$e->getMessage());
        }
    }

    /**
     * Update the specified global Relationship Manager note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function update(StoreOrUpdateBenchRmNoteTemplateRequest $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');
            $payload = $request->sanitizedPayload();

            $result = $this->mutationService->updateTemplate((int) $id, $payload, $idempotencyKey);

            return response()->json($result);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin BenchRmNoteTemplate update failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to update Relationship Manager template. '.$e->getMessage());
        }
    }

    /**
     * Remove the specified global Relationship Manager note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');

            $result = $this->mutationService->deleteTemplate((int) $id, $idempotencyKey);

            return response()->json($result);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin BenchRmNoteTemplate destroy failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to delete Relationship Manager template.');
        }
    }

    /**
     * Fetch raw template data for modal edit, preview, or clipboard inspection.
     * Strict CQRS: Served from L2 Dragonfly/Redis cache and Elasticsearch (0% MySQL reads).
     */
    public function getData($id)
    {
        $template = $this->readService->getTemplateById($id);

        if (! $template) {
            return Reply::error('Template not found.');
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $template]);
    }

    /**
     * Instant 1-click AJAX status toggle (Active <=> Inactive).
     * Eliminates modal overhead for quick operational adjustments.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');

            $result = $this->mutationService->toggleStatus((int) $id, $idempotencyKey);

            return response()->json($result);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin BenchRmNoteTemplate toggleStatus failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to change template status.');
        }
    }

    /**
     * Instant 1-click duplicate: Clones an existing template as an inactive draft copy.
     */
    public function duplicate(Request $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');

            $result = $this->mutationService->duplicateTemplate((int) $id, $idempotencyKey);

            return response()->json($result);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin BenchRmNoteTemplate duplicate failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to duplicate Relationship Manager template.');
        }
    }
}
