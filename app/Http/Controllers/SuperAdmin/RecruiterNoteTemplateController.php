<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\StoreOrUpdateRecruiterNoteTemplateRequest;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteMutationService;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class RecruiterNoteTemplateController
 *
 * Intent: Manages global recruiter note templates (company_id = NULL) from the Super Admin panel.
 * These standardized templates are consumed across the ATS by recruiters and hiring managers
 * during candidate application screening, interview notes, and relationship evaluations.
 *
 * Strict CQRS: 100% Reads served from Elasticsearch / Redis (Dragonfly) L2 cache (0% MySQL reads).
 * ACID writes (<15ms) write to MySQL Master with Transactional Outbox and Two-Tier Idempotency.
 * Tenancy: Global Super Admin scope strictly filtered by whereNull('company_id').
 * Database Truth: recruiter_note_master (database/schema/mysql-schema.sql:3864).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class RecruiterNoteTemplateController extends SuperAdminBaseController
{
    public function __construct(
        protected SuperAdminRecruiterNoteReadService $readService,
        protected SuperAdminRecruiterNoteMutationService $mutationService
    ) {
        parent::__construct();
        $this->pageTitle = 'Recruiter Note Templates';
    }

    /**
     * Display the recruiter note templates listing page.
     * Computes live KPI metrics (Total, Active, Inactive) and passes the formatted
     * collection to the Modern Card + Sub-Card Enterprise View.
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

        if ($request->ajax() && ! $request->pjax()) {
            return response()->json([
                'status' => 'success',
                'totalCount' => $this->totalCount,
                'activeCount' => $this->activeCount,
                'inactiveCount' => $this->inactiveCount,
                'templates' => $this->templates,
            ]);
        }

        return view('super-admin.recruiter-note.index', $this->data);
    }

    /**
     * Store a newly created global recruiter note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function store(StoreOrUpdateRecruiterNoteTemplateRequest $request)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');
            $payload = $request->sanitizedPayload();

            $result = $this->mutationService->createTemplate($payload, $idempotencyKey);

            return response()->json($result);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpEx) {
            throw $httpEx;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate store failure: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return Reply::error('Failed to create recruiter note template. '.$e->getMessage());
        }
    }

    /**
     * Update the specified global recruiter note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function update(StoreOrUpdateRecruiterNoteTemplateRequest $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');
            $payload = $request->sanitizedPayload();

            $result = $this->mutationService->updateTemplate((int) $id, $payload, $idempotencyKey);

            return response()->json($result);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpEx) {
            throw $httpEx;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate update failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to update recruiter note template. '.$e->getMessage());
        }
    }

    /**
     * Remove the specified global recruiter note template.
     * Uses Two-Tier Idempotency and Transactional Outbox pattern.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('_idempotency_key');

            $result = $this->mutationService->deleteTemplate((int) $id, $idempotencyKey);

            return response()->json($result);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpEx) {
            throw $httpEx;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate destroy failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to delete recruiter note template.');
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
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpEx) {
            throw $httpEx;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate toggleStatus failure: '.$e->getMessage(), [
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
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpEx) {
            throw $httpEx;
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate duplicate failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to duplicate template.');
        }
    }
}
