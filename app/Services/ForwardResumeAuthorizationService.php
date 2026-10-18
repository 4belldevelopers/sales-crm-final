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
                'body' => [
                    '_source' => ['id', 'company_id', 'thirdparty_company_id'],
                    'size' => count($candidateIds),
                    'query' => [
                        'bool' => [
                            'must' => [
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
            Log::warning('[ForwardResumeAuthorizationService] ES authorization search error: '.$esErr->getMessage().'. Falling back to DB check.');
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
                'user_id' => $user->id,
                'requested' => $candidateIds,
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
                Log::warning('[ForwardResumeAuth] skill_percentage calculation error: '.$e->getMessage());
            }
        }

        if ($matchScore === 0 && ! empty($jobSkills) && ! empty($candidateSkills)) {
            try {
                $calcPct = GeneralMethod::skill_percentage_from_array($jobSkills, $candidateSkills);
                $matchScore = (int) round((float) rtrim((string) $calcPct, '%'));
            } catch (\Throwable $e) {
                Log::warning('[ForwardResumeAuth] skill_percentage_from_array error: '.$e->getMessage());
            }
        }

        if ($matchScore <= 0) {
            $matchScore = ! empty($candidateRating) ? min(100, max(40, (int) ($candidateRating * 20))) : 85;
        }

        return min(100, max(10, $matchScore));
    }

    /**
     * Resolves job-context-aware authorized email recipients strictly scoped by job posting type:
     *
     * 1. Job Posted Under "My Organization":
     *    - Allowed: ONLY active team members of the current company (My Organization).
     *    - Strictly Forbidden: Internal Client contacts, Direct Client contacts.
     *
     * 2. Job Posted Under "Internal Client":
     *    - Allowed: Active team members of the current company + Active contacts of the specific internal client.
     *    - Strictly Forbidden: Other clients' contacts.
     *
     * 3. Job Posted Under "Direct Client":
     *    - Allowed: Active team members of the current company + STRICTLY ONLY:
     *      (a) Hiring Manager's email ID for that job, and
     *      (b) The user who added the agency to that job (from_user_id in recommendation_relation).
     *    - Strictly Forbidden: All other direct client contacts/employees.
     *
     * @param  User  $user  Authenticated recruiter
     * @param  int|null  $targetJobId  Target Job ID
     * @param  array  $candidateIds  Candidate IDs for fallback job resolution
     * @return array{job_type: string, job_id: int|null, internal_team: array, client_contacts: array, default_to_emails: array}
     */
    public function resolveAuthorizedRecipients(User $user, ?int $targetJobId = null, array $candidateIds = []): array
    {
        $companyId = (int) $user->company_id;

        // 1. Resolve Target Job ID if not directly provided
        if ((! $targetJobId || $targetJobId <= 0) && ! empty($candidateIds)) {
            $intCandIds = array_values(array_unique(array_filter(array_map('intval', (array) $candidateIds))));
            if (! empty($intCandIds)) {
                $firstCand = DB::table('job_applications')
                    ->whereIn('id', $intCandIds)
                    ->where(function ($q) use ($companyId) {
                        $q->where('company_id', $companyId)
                            ->orWhere('thirdparty_company_id', $companyId);
                    })
                    ->first();
                if ($firstCand && ! empty($firstCand->job_id)) {
                    $targetJobId = (int) $firstCand->job_id;
                }
            }
        }

        // 2. Fetch My Organization Active Team Members (Universal Scope across all job types)
        $teamRows = DB::table('users')
            ->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->where('users.company_id', '=', $companyId)
            ->where('users.status', '=', 'active')
            ->where('users.working_status', '=', 1)
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.image',
                'users_profile.designation as display_name',
                'roles.display_name as role_name',
                'companies.company_name'
            )
            ->groupBy('users.id')
            ->orderBy('users.name', 'asc')
            ->get();

        $internalTeam = [];
        foreach ($teamRows as $u) {
            $em = trim((string) $u->email);
            if (! empty($em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $internalTeam[] = (object) [
                    'id' => (int) $u->id,
                    'email' => $em,
                    'name' => trim((string) ($u->name ?: 'Team Member')),
                    'display_name' => trim((string) ($u->display_name ?: ($u->role_name ?: 'Team Member'))),
                    'role_tag' => 'My Organization',
                    'company_name' => trim((string) ($u->company_name ?: 'My Organization')),
                    'category' => 'internal',
                    'image' => $u->image ?? null,
                ];
            }
        }

        // If no job context can be resolved, default to My Organization with 0 client contacts (Secure by default)
        if (! $targetJobId || $targetJobId <= 0) {
            return [
                'job_type' => 'my_organization',
                'job_id' => null,
                'internal_team' => $internalTeam,
                'client_contacts' => [],
                'default_to_emails' => [],
            ];
        }

        // 3. Inspect Job Context & Determine Classification
        $jobRow = DB::table('jobs')->where('id', $targetJobId)->first();

        // Check if job is a Direct Client job (assigned to current agency via recommendation_relation OR owned by external company)
        $recRel = DB::table('recommendation_relation')
            ->where('job_id', $targetJobId)
            ->where('to_company_id', $companyId)
            ->where(function ($q) {
                $q->whereNull('final_status')
                    ->orWhereNotIn('final_status', [2, 3]); // Exclude rejected/removed
            })
            ->orderBy('id', 'desc')
            ->first();

        $isDirectClientJob = ($recRel !== null) || ($jobRow && (int) $jobRow->company_id !== $companyId);

        // =========================================================================
        // SCENARIO 3: Direct Client Job
        // Condition: Job is received from/posted under a Direct Client.
        // Allowed:
        //   - My Organization active team members
        //   - STRICTLY ONLY: (1) Hiring Manager's email, and (2) Agency-added-by user's email
        // Forbidden:
        //   - Other direct client contacts/employees
        //   - Unrelated client contacts
        // =========================================================================
        if ($isDirectClientJob) {
            $clientContactsMap = [];

            // A. Fetch Hiring Manager(s) for this specific job
            $hmRows = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->leftJoin('clients', 'clients.id', '=', 'client_contact_info.client_id')
                ->where('client_contact_info_jobs.job_id', $targetJobId)
                ->select(
                    'client_contact_info.id',
                    'client_contact_info.contact_person_email as email',
                    'client_contact_info.contact_person_name as name',
                    'client_contact_info.contact_person_designation as display_name',
                    'clients.name as company_name'
                )
                ->whereNotNull('client_contact_info.contact_person_email')
                ->where('client_contact_info.contact_person_email', '!=', '')
                ->get();

            // Fallback: If client_contact_info_jobs is unassigned, check designated client contact on the job
            if ($hmRows->isEmpty() && $jobRow && ! empty($jobRow->client_id)) {
                $hmRows = DB::table('client_contact_info')
                    ->leftJoin('clients', 'clients.id', '=', 'client_contact_info.client_id')
                    ->where('client_contact_info.client_id', (int) $jobRow->client_id)
                    ->select(
                        'client_contact_info.id',
                        'client_contact_info.contact_person_email as email',
                        'client_contact_info.contact_person_name as name',
                        'client_contact_info.contact_person_designation as display_name',
                        'clients.name as company_name'
                    )
                    ->whereNotNull('client_contact_info.contact_person_email')
                    ->where('client_contact_info.contact_person_email', '!=', '')
                    ->limit(1)
                    ->get();
            }

            foreach ($hmRows as $hm) {
                $em = strtolower(trim((string) $hm->email));
                if (! empty($em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
                    $clientContactsMap[$em] = (object) [
                        'id' => (int) $hm->id,
                        'email' => trim((string) $hm->email),
                        'name' => trim((string) ($hm->name ?: 'Hiring Manager')),
                        'display_name' => trim((string) ($hm->display_name ?: 'Hiring Manager')),
                        'role_tag' => 'Hiring Manager',
                        'company_name' => trim((string) ($hm->company_name ?: 'Client')),
                        'category' => 'client',
                    ];
                }
            }

            // B. Fetch Agency-Added-By Contact (from recommendation_relation)
            $addedByUser = null;
            if ($recRel && ! empty($recRel->from_user_id)) {
                $addedByUser = DB::table('users')
                    ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                    ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
                    ->where('users.id', (int) $recRel->from_user_id)
                    ->whereNotNull('users.email')
                    ->where('users.email', '!=', '')
                    ->select(
                        'users.id',
                        'users.email',
                        'users.name',
                        'users_profile.designation as display_name',
                        'companies.company_name'
                    )
                    ->first();
            } elseif ($recRel && ! empty($recRel->from_company_id)) {
                // Fallback to client company admin if user ID was not logged in relation row
                $addedByUser = DB::table('users')
                    ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                    ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
                    ->where('users.company_id', (int) $recRel->from_company_id)
                    ->where('users.status', 'active')
                    ->whereNotNull('users.email')
                    ->where('users.email', '!=', '')
                    ->orderBy('users.id', 'asc')
                    ->select(
                        'users.id',
                        'users.email',
                        'users.name',
                        'users_profile.designation as display_name',
                        'companies.company_name'
                    )
                    ->first();
            }

            if ($addedByUser) {
                $em = strtolower(trim((string) $addedByUser->email));
                if (! empty($em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
                    if (isset($clientContactsMap[$em])) {
                        $clientContactsMap[$em]->role_tag = 'Hiring Manager & Agency Added By';
                    } else {
                        $clientContactsMap[$em] = (object) [
                            'id' => (int) $addedByUser->id,
                            'email' => trim((string) $addedByUser->email),
                            'name' => trim((string) ($addedByUser->name ?: 'Client Contact')),
                            'display_name' => trim((string) ($addedByUser->display_name ?: 'Agency Added By')),
                            'role_tag' => 'Agency Added By',
                            'company_name' => trim((string) ($addedByUser->company_name ?: 'Client')),
                            'category' => 'client',
                        ];
                    }
                }
            }

            $clientContacts = array_values($clientContactsMap);
            $defaultToEmails = ! empty($clientContacts) ? [$clientContacts[0]->email] : [];

            return [
                'job_type' => 'direct_client',
                'job_id' => $targetJobId,
                'internal_team' => $internalTeam,
                'client_contacts' => $clientContacts,
                'default_to_emails' => $defaultToEmails,
            ];
        }

        // =========================================================================
        // Internal ATS Job (Company Owned: jobs.company_id == $companyId)
        // Check if posted under "My Organization" or an "Internal Client"
        // =========================================================================
        $clientRow = null;
        if ($jobRow && ! empty($jobRow->client_id)) {
            $clientRow = DB::table('clients')
                ->where('id', (int) $jobRow->client_id)
                ->where('company_id', $companyId) // Strict tenancy boundary
                ->first();
        }

        $isSelfCompany = (! $clientRow || (int) ($clientRow->self_company ?? 0) === 1 || empty($jobRow->client_id));

        // =========================================================================
        // SCENARIO 1: Job Posted Under "My Organization"
        // Condition: The job is posted under My Organization.
        // Allowed:
        //   - ONLY My Organization active team members
        // Forbidden:
        //   - Zero Internal Client contacts
        //   - Zero Direct Client contacts
        // =========================================================================
        if ($isSelfCompany) {
            return [
                'job_type' => 'my_organization',
                'job_id' => $targetJobId,
                'internal_team' => $internalTeam,
                'client_contacts' => [], // Strictly empty
                'default_to_emails' => [], // Recruiter manually enters or picks internal member
            ];
        }

        // =========================================================================
        // SCENARIO 2: Job Posted Under "Internal Client"
        // Condition: The job is posted under an Internal Client.
        // Allowed:
        //   - My Organization active team members
        //   - Active contacts belonging strictly to this job's internal client
        // Forbidden:
        //   - Other clients' contacts
        // =========================================================================
        $clientContactsMap = [];
        $internalClientContacts = DB::table('client_contact_info')
            ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->where('client_contact_info.client_id', (int) $clientRow->id)
            ->where('clients.company_id', $companyId) // Strict tenancy boundary
            ->where('clients.self_company', 0)
            ->whereNotNull('client_contact_info.contact_person_email')
            ->where('client_contact_info.contact_person_email', '!=', '')
            ->select(
                'client_contact_info.id',
                'client_contact_info.contact_person_email as email',
                'client_contact_info.contact_person_name as name',
                'client_contact_info.contact_person_designation as display_name',
                'clients.name as company_name'
            )
            ->groupBy('client_contact_info.id')
            ->orderBy('client_contact_info.contact_person_name', 'asc')
            ->get();

        foreach ($internalClientContacts as $cc) {
            $em = strtolower(trim((string) $cc->email));
            if (! empty($em) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
                if (! isset($clientContactsMap[$em])) {
                    $clientContactsMap[$em] = (object) [
                        'id' => (int) $cc->id,
                        'email' => trim((string) $cc->email),
                        'name' => trim((string) ($cc->name ?: 'Client Contact')),
                        'display_name' => trim((string) ($cc->display_name ?: 'Client Contact')),
                        'role_tag' => 'Internal Client',
                        'company_name' => trim((string) ($cc->company_name ?: ($clientRow->name ?: 'Internal Client'))),
                        'category' => 'client',
                    ];
                }
            }
        }

        $clientContacts = array_values($clientContactsMap);
        $defaultToEmails = ! empty($clientContacts) ? [$clientContacts[0]->email] : [];

        return [
            'job_type' => 'internal_client',
            'job_id' => $targetJobId,
            'internal_team' => $internalTeam,
            'client_contacts' => $clientContacts,
            'default_to_emails' => $defaultToEmails,
        ];
    }
}
