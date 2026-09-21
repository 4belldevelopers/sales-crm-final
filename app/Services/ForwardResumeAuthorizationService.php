<?php

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ForwardResumeAuthorizationService
 *
 * Intent: Provides bank-grade multi-tenant authorization and authoritative score calculation
 * for the V4.1 Forward Resume Engine. Enforces the security boundary between untrusted browser
 * inputs and privileged candidate dossiers.
 *
 * Tenancy Rule: A recruiter can ONLY access candidates belonging to their active company_id
 * or thirdparty_company_id. Any mismatch raises a security exception.
 */
class ForwardResumeAuthorizationService
{
    protected ElasticService $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Authorizes candidate batch access, verifies multi-tenant isolation,
     * and returns validated candidate ID list.
     *
     * @param  User  $user  Current authenticated user
     * @param  array  $candidateIds  List of candidate application IDs from request
     * @param  array|null  $jobIds  Associated job IDs (if applicable)
     * @return array<int> Validated candidate IDs
     *
     * @throws AuthorizationException
     */
    public function authorizeCandidateBatch(User $user, array $candidateIds, ?array $jobIds = []): array
    {
        $companyId = (int) $user->company_id;
        $candidateIds = array_values(array_unique(array_filter(array_map('intval', $candidateIds))));

        if (empty($candidateIds)) {
            throw new AuthorizationException('No valid candidate IDs provided for authorization.');
        }

        if (count($candidateIds) > 5) {
            throw new AuthorizationException('A maximum of 5 candidates can be forwarded concurrently.');
        }

        $validCandidateIds = [];

        // 1. Primary Authorization Path: Query ELASTIC_CANDIDATES (sub-3ms, 0 MySQL load)
        try {
            $indexName = config('constants.ELASTIC_CANDIDATES');
            $params = [
                'index' => $indexName,
                'body'  => [
                    '_source' => ['id', 'company_id', 'thirdparty_company_id'],
                    'size'    => count($candidateIds),
                    'query'   => [
                        'bool' => [
                            'must'   => [
                                ['terms' => ['id' => $candidateIds]],
                            ],
                            'filter' => [
                                ['bool' => [
                                    'should' => [
                                        ['term' => ['company_id' => $companyId]],
                                        ['term' => ['thirdparty_company_id' => $companyId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ]],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);
            $hits = $response['hits']['hits'] ?? [];
            foreach ($hits as $hit) {
                $cId = (int) ($hit['_source']['id'] ?? $hit['_id']);
                if ($cId > 0) {
                    $validCandidateIds[] = $cId;
                }
            }
        } catch (\Throwable $esErr) {
            Log::warning('[ForwardResumeAuthorizationService] ES authorization search error: ' . $esErr->getMessage() . '. Falling back to DB check.');
        }

        // 2. Fallback Path: In case of ES index sync delay or ES downtime, verify via MySQL
        if (count($validCandidateIds) !== count($candidateIds)) {
            $validCandidateIds = DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                      ->orWhere('thirdparty_company_id', $companyId);
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        // Strict Tenant Boundary Check: All requested candidate IDs must be accounted for
        if (count($validCandidateIds) !== count($candidateIds)) {
            Log::critical('[SECURITY] Forward resume cross-tenant probing detected.', [
                'company_id' => $companyId,
                'user_id'    => $user->id,
                'requested'  => $candidateIds,
                'authorized' => $validCandidateIds,
            ]);
            throw new AuthorizationException('Unauthorized candidate access detected. Operation aborted.');
        }

        return $validCandidateIds;
    }

    /**
     * Calculates server-authoritative hiring confidence percentage.
     * Prevents browser tampering of match scores in client-facing Prescreen Reports.
     *
     * @param  int  $candidateId  Candidate ID
     * @param  int|null  $jobId  Target job ID
     * @param  array  $candidateSkills  Candidate parsed skills
     * @param  array  $jobSkills  Target job skills
     * @param  int|null  $candidateRating  Candidate rating (1-5)
     * @return int Authoritative match score (0-100)
     */
    public function computeAuthoritativeMatch(
        int $candidateId,
        ?int $jobId,
        array $candidateSkills,
        array $jobSkills,
        ?int $candidateRating = 4
    ): int {
        $matchScore = 0;

        if ($jobId && $jobId > 0 && ! empty($candidateSkills)) {
            try {
                $calcPct = GeneralMethod::skill_percentage($jobId, $candidateSkills);
                $matchScore = (int) round((float) rtrim((string) $calcPct, '%'));
            } catch (\Throwable $e) {
                Log::warning('[ForwardResumeAuth] skill_percentage calculation error: ' . $e->getMessage());
            }
        }

        if ($matchScore === 0 && ! empty($jobSkills) && ! empty($candidateSkills)) {
            try {
                $calcPct = GeneralMethod::skill_percentage_from_array($jobSkills, $candidateSkills);
                $matchScore = (int) round((float) rtrim((string) $calcPct, '%'));
            } catch (\Throwable $e) {
                Log::warning('[ForwardResumeAuth] skill_percentage_from_array error: ' . $e->getMessage());
            }
        }

        if ($matchScore <= 0) {
            $matchScore = ! empty($candidateRating) ? min(100, max(40, (int) ($candidateRating * 20))) : 85;
        }

        return min(100, max(10, $matchScore));
    }
}
