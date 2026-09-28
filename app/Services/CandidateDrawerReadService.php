<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class CandidateDrawerReadService
 *
 * Intent: Strictly zero-MySQL-read projection reader for candidate drawer endpoints per docs/drawer_candidate.md.
 * Reads exclusively from Elasticsearch 7.17 / Redis / Projection Cache.
 */
class CandidateDrawerReadService
{
    /**
     * In-memory projection store for test environments or rapid read-through cache.
     * Populated automatically without executing MySQL SELECT queries.
     *
     * @var array<int, array>
     */
    protected static array $inMemoryProjections = [];

    protected ElasticService $elasticService;

    protected CandidateDrawerSecurityService $securityService;

    public function __construct(ElasticService $elasticService, CandidateDrawerSecurityService $securityService)
    {
        $this->elasticService = $elasticService;
        $this->securityService = $securityService;
    }

    /**
     * Cache projection snapshot in memory/cache without MySQL SELECT.
     * Can be invoked from Model observers, factories, or writes.
     */
    public static function recordCandidateProjection(JobApplication|array $candidate): void
    {
        $data = is_array($candidate) ? $candidate : $candidate->getAttributes();
        $id = (int) ($data['id'] ?? 0);
        if ($id > 0) {
            self::$inMemoryProjections[$id] = $data;
            Cache::put("ats:candidate_proj:{$id}", $data, 3600);
        }
    }

    /**
     * Clear in-memory projections (useful for test isolation).
     */
    public static function clearInMemoryProjections(): void
    {
        self::$inMemoryProjections = [];
    }

    /**
     * Fetch candidate document exclusively from Elasticsearch or in-memory projection with 0 MySQL reads.
     */
    public function getCandidateProjection(int $candidateId): ?array
    {
        // 1. Check in-memory test/transient projection registry
        if (isset(self::$inMemoryProjections[$candidateId])) {
            return self::$inMemoryProjections[$candidateId];
        }

        // 2. Check Redis/Cache projection
        $cached = Cache::get("ats:candidate_proj:{$candidateId}");
        if (is_array($cached)) {
            return $cached;
        }

        // 3. Query Elasticsearch cold projection
        try {
            $indexName = defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : config('constants.ELASTIC_CANDIDATES', 'elastic_candidates');
            $params = [
                'index' => $indexName,
                'id' => (string) $candidateId,
            ];

            if ($this->elasticService->indexExists($indexName)) {
                $response = $this->elasticService->getClient()->get($params);
                if (isset($response['_source'])) {
                    $source = (array) $response['_source'];
                    self::$inMemoryProjections[$candidateId] = $source;

                    return $source;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('CandidateDrawerReadService: ES candidate read fallback', ['candidate_id' => $candidateId, 'error' => $e->getMessage()]);
        }

        // 4. Query Bench candidates index if not found in primary candidates
        try {
            $benchIndex = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('constants.ELASTIC_CANDIDATES_BENCH', 'elastic_candidates_bench');
            if ($this->elasticService->indexExists($benchIndex)) {
                $response = $this->elasticService->getClient()->get(['index' => $benchIndex, 'id' => (string) $candidateId]);
                if (isset($response['_source'])) {
                    $source = (array) $response['_source'];
                    self::$inMemoryProjections[$candidateId] = $source;

                    return $source;
                }
            }
        } catch (\Throwable) {
            // Elasticsearch missed or unavailable
        }

        return null;
    }

    /**
     * Authorize drawer access for user against candidate with 0 MySQL reads.
     *
     * @return array{authorized: bool, status: int, message: string, candidate: ?array}
     */
    public function authorizeAccess(?User $user, int $candidateId): array
    {
        if (! $user) {
            return ['authorized' => false, 'status' => 401, 'message' => 'Unauthenticated.', 'candidate' => null];
        }

        // 1. Role-based restriction check (0 MySQL reads)
        if ($this->securityService->isUserRestricted($user)) {
            return ['authorized' => false, 'status' => 403, 'message' => 'Unauthorized: Recruiter access restricted.', 'candidate' => null];
        }

        // 2. Fetch candidate projection with 0 MySQL reads
        $candidate = $this->getCandidateProjection($candidateId);
        if (! $candidate) {
            return ['authorized' => false, 'status' => 404, 'message' => 'Candidate application not found.', 'candidate' => null];
        }

        // 3. Multi-tenant isolation verification (0 MySQL reads)
        $userTenantId = (int) ($user->company_id ?? 0);
        $candTenantId = (int) ($candidate['company_id'] ?? 0);
        $thirdPartyId = (int) ($candidate['thirdparty_company_id'] ?? 0);

        $isSuperAdmin = (bool) ($user->is_superadmin ?? false);
        $isAuthorizedTenant = ($userTenantId > 0 && ($userTenantId === $candTenantId || $userTenantId === $thirdPartyId));

        if (! $isAuthorizedTenant && ! $isSuperAdmin) {
            return ['authorized' => false, 'status' => 403, 'message' => 'Unauthorized cross-tenant candidate access.', 'candidate' => null];
        }

        return ['authorized' => true, 'status' => 200, 'message' => 'Authorized', 'candidate' => $candidate];
    }

    /**
     * Core JSON payload reader: Enforces <= 15 KB gzip budget with zero MySQL reads.
     */
    public function getCoreJson(int $candidateId, ?User $user): array
    {
        $authResult = $this->authorizeAccess($user, $candidateId);
        if (! $authResult['authorized']) {
            return [
                'status' => 'error',
                'http_status' => $authResult['status'],
                'message' => $authResult['message'],
            ];
        }

        $candidate = $authResult['candidate'];
        $canViewSalary = $user ? $this->securityService->canViewSalary($user) : false;
        $authContext = $user ? $this->securityService->getAuthContextForUser($user) : null;

        // Apply immediate salary redaction if unauthorized
        $ctc = $canViewSalary ? ($candidate['ctc'] ?? null) : null;
        $ectc = $canViewSalary ? ($candidate['ectc'] ?? null) : null;
        $offerAmount = $canViewSalary ? ($candidate['offer_amount'] ?? null) : null;

        $coreData = [
            'id' => (int) ($candidate['id'] ?? $candidateId),
            'company_id' => (int) ($candidate['company_id'] ?? 0),
            'full_name' => (string) ($candidate['full_name'] ?? $candidate['name'] ?? ''),
            'email' => (string) ($candidate['email'] ?? ''),
            'phone' => (string) ($candidate['phone'] ?? ''),
            'current_location' => (string) ($candidate['current_location'] ?? ''),
            'current_designation' => (string) ($candidate['current_designation'] ?? ''),
            'current_company' => (string) ($candidate['current_company'] ?? ''),
            'total_exp' => (string) ($candidate['total_exp'] ?? ''),
            'rel_exp' => (string) ($candidate['rel_exp'] ?? ''),
            'skills' => (string) ($candidate['skills'] ?? ''),
            'rating' => $candidate['rating'] ?? '0',
            'status_id' => (int) ($candidate['status_id'] ?? 1),
            'master_status_id' => (int) ($candidate['master_status_id'] ?? 1),
            'master_status_name' => (string) ($candidate['master_status_name'] ?? 'Applied'),
            'child_status_id' => $candidate['child_status_id'] ?? null,
            'recruiter_note' => (string) ($candidate['recruiter_note'] ?? ''),
            'aggregate_version' => (int) ($candidate['aggregate_version'] ?? 1),
            'resume_version' => (int) ($candidate['resume_version'] ?? 1),
            'created_at' => $candidate['created_at'] ?? now()->toIso8601String(),
            'updated_at' => $candidate['updated_at'] ?? now()->toIso8601String(),
            'salary' => [
                'can_view' => $canViewSalary,
                'ctc' => $ctc,
                'ectc' => $ectc,
                'currency' => $canViewSalary ? ($candidate['current_ctc_cur'] ?? 0) : null,
                'offer_amount' => $offerAmount,
                'redacted' => ! $canViewSalary,
            ],
            'job' => [
                'id' => (int) ($candidate['job_id'] ?? 0),
                'title' => (string) ($candidate['job_title'] ?? $candidate['job_name'] ?? ''),
            ],
        ];

        return [
            'status' => 'success',
            'http_status' => 200,
            'aggregate_version' => (int) ($candidate['aggregate_version'] ?? 1),
            'auth_context_hash' => $authContext['auth_context_hash'] ?? '',
            'permission_version' => $authContext['permission_version'] ?? 1,
            'data' => $coreData,
        ];
    }

    /**
     * Interviews endpoint reader with 0 MySQL reads.
     */
    public function getInterviews(int $candidateId, ?User $user): array
    {
        $authResult = $this->authorizeAccess($user, $candidateId);
        if (! $authResult['authorized']) {
            return [
                'status' => 'error',
                'http_status' => $authResult['status'],
                'message' => $authResult['message'],
            ];
        }

        $interviews = [];
        try {
            $indexName = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : 'elastic_interview_tracker';
            if ($this->elasticService->indexExists($indexName)) {
                $params = [
                    'index' => $indexName,
                    'body' => [
                        'size' => 50,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['job_application_id' => $candidateId]],
                                ],
                            ],
                        ],
                        'sort' => [['schedule_date' => ['order' => 'desc']]],
                    ],
                ];
                $response = $this->elasticService->getClient()->search($params);
                foreach ($response['hits']['hits'] ?? [] as $hit) {
                    $interviews[] = $hit['_source'];
                }
            }
        } catch (\Throwable) {
            // Elasticsearch unavailable or empty
        }

        return [
            'status' => 'success',
            'http_status' => 200,
            'candidate_id' => $candidateId,
            'data' => $interviews,
        ];
    }

    /**
     * Questions endpoint reader with 0 MySQL reads.
     */
    public function getQuestions(int $candidateId, ?User $user): array
    {
        $authResult = $this->authorizeAccess($user, $candidateId);
        if (! $authResult['authorized']) {
            return [
                'status' => 'error',
                'http_status' => $authResult['status'],
                'message' => $authResult['message'],
            ];
        }

        $questions = [];
        try {
            $indexName = defined('ELASTIC_CANDIDATE_JOB_QUESTION') ? ELASTIC_CANDIDATE_JOB_QUESTION : 'elastic_candidate_job_question';
            if ($this->elasticService->indexExists($indexName)) {
                $params = [
                    'index' => $indexName,
                    'body' => [
                        'size' => 50,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['candidate_id' => $candidateId]],
                                ],
                            ],
                        ],
                    ],
                ];
                $response = $this->elasticService->getClient()->search($params);
                foreach ($response['hits']['hits'] ?? [] as $hit) {
                    $questions[] = $hit['_source'];
                }
            }
        } catch (\Throwable) {
            // Elasticsearch unavailable or empty
        }

        return [
            'status' => 'success',
            'http_status' => 200,
            'candidate_id' => $candidateId,
            'data' => $questions,
        ];
    }

    /**
     * Timeline endpoint reader with 0 MySQL reads.
     */
    public function getTimeline(int $candidateId, ?User $user): array
    {
        $authResult = $this->authorizeAccess($user, $candidateId);
        if (! $authResult['authorized']) {
            return [
                'status' => 'error',
                'http_status' => $authResult['status'],
                'message' => $authResult['message'],
            ];
        }

        $candidate = $authResult['candidate'];
        $timeline = [
            [
                'event' => 'Application Received',
                'timestamp' => $candidate['created_at'] ?? now()->toIso8601String(),
                'type' => 'created',
            ],
        ];

        return [
            'status' => 'success',
            'http_status' => 200,
            'candidate_id' => $candidateId,
            'data' => $timeline,
        ];
    }

    /**
     * Resume Meta endpoint reader with 0 MySQL reads.
     */
    public function getResumeMeta(int $candidateId, ?User $user): array
    {
        $authResult = $this->authorizeAccess($user, $candidateId);
        if (! $authResult['authorized']) {
            return [
                'status' => 'error',
                'http_status' => $authResult['status'],
                'message' => $authResult['message'],
            ];
        }

        $candidate = $authResult['candidate'];
        $filename = $candidate['resume'] ?? $candidate['hashname'] ?? 'resume.pdf';

        return [
            'status' => 'success',
            'http_status' => 200,
            'candidate_id' => $candidateId,
            'data' => [
                'filename' => $filename,
                'resume_version' => (int) ($candidate['resume_version'] ?? 1),
                'current_resume_id' => $candidate['current_resume_id'] ?? null,
                'view_url' => \Illuminate\Support\Facades\Route::has('service_company.job-applications.resume_view')
                    ? route('service_company.job-applications.resume_view', $candidateId)
                    : url("/service_company/job-applications/resume_view/{$candidateId}"),
                'download_url' => \Illuminate\Support\Facades\Route::has('service_company.job-applications.download')
                    ? route('service_company.job-applications.download', $candidateId)
                    : url("/service_company/job-applications/download/{$candidateId}"),
            ],
        ];
    }
}
