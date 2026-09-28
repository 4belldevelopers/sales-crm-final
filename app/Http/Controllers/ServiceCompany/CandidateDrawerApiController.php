<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Http\Controllers\Controller;
use App\Services\CandidateDrawerReadService;
use App\Services\CandidateMutationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CandidateDrawerApiController
 *
 * Intent: High-concurrency zero-MySQL-read API controller for candidate profile drawer
 * per docs/drawer_candidate.md.
 *
 * All read routes operate exclusively on Elasticsearch / Redis / In-memory projections.
 * All mutations execute within atomic MySQL transactions with OCC versioning and outbox events.
 */
class CandidateDrawerApiController extends Controller
{
    public function __construct(
        protected CandidateDrawerReadService $readService,
        protected CandidateMutationService $mutationService
    ) {}

    /**
     * Core candidate JSON endpoint (Hard CI-Enforced <= 15 KB gzip budget).
     */
    public function coreJson(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user && ! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = $user ?: auth()->user();
        $result = $this->readService->getCoreJson((int) $id, $user);

        return response()->json($result, $result['http_status'] ?? 200);
    }

    /**
     * Interviews sub-endpoint with zero MySQL reads.
     */
    public function interviews(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user && ! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = $user ?: auth()->user();
        $result = $this->readService->getInterviews((int) $id, $user);

        return response()->json($result, $result['http_status'] ?? 200);
    }

    /**
     * Questions sub-endpoint with zero MySQL reads.
     */
    public function questions(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user && ! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = $user ?: auth()->user();
        $result = $this->readService->getQuestions((int) $id, $user);

        return response()->json($result, $result['http_status'] ?? 200);
    }

    /**
     * Activity timeline sub-endpoint with zero MySQL reads.
     */
    public function timeline(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user && ! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = $user ?: auth()->user();
        $result = $this->readService->getTimeline((int) $id, $user);

        return response()->json($result, $result['http_status'] ?? 200);
    }

    /**
     * Resume metadata sub-endpoint with zero MySQL reads.
     */
    public function resumeMeta(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user && ! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $user = $user ?: auth()->user();
        $result = $this->readService->getResumeMeta((int) $id, $user);

        return response()->json($result, $result['http_status'] ?? 200);
    }

    /**
     * Candidate mutation endpoint with transactionally coordinated idempotency & OCC.
     */
    public function mutate(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user() ?: auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $tenantId = (int) ($user->company_id ?? 0);
        $idempotencyKey = $request->header('Idempotency-Key') ?: $request->input('idempotency_key');
        if (! $idempotencyKey) {
            return response()->json(['error' => 'Missing Idempotency-Key header.'], 400);
        }

        $expectedVersion = (int) $request->input('expected_version', 1);
        $payload = $request->except(['_token', 'idempotency_key']);

        return $this->mutationService->mutateCandidate(
            $tenantId,
            (int) $id,
            $idempotencyKey,
            $request->input('event_type', 'candidate.note_updated'),
            $payload,
            $expectedVersion,
            function (int $candidateId, int $tenantId, int $newVersion) use ($request) {
                $note = $request->input('recruiter_note');
                $updateData = [
                    'aggregate_version' => $newVersion,
                    'updated_at' => now(),
                ];
                if ($note !== null) {
                    $updateData['recruiter_note'] = $note;
                }

                \Illuminate\Support\Facades\DB::table('job_applications')
                    ->where('id', $candidateId)
                    ->where('company_id', $tenantId)
                    ->update($updateData);

                return [
                    'status' => 'success',
                    'candidate_id' => $candidateId,
                    'aggregate_version' => $newVersion,
                    'message' => 'Candidate mutated successfully.',
                ];
            }
        );
    }
}
