<?php

/***########################################################
Controller    	: 	AdminTeamcontroller
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Jobs\QueueMailService;
use App\Jobs\SyncUserToElasticJob;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyTeamController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $elasticService;

    public function __construct(MailService $mailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'menu.team';
        $this->pageIcon = 'icon-people';
        view()->share('calling_codes', $this->getCallingCodes());
        $this->mailService = $mailService;
        $this->elasticService = $elasticService;
    }

    public function index()
    {
        // Why: the redesigned team list shows summary stat cards (Total / Active /
        // Inactive / Captain·TRM) and status tabs. These aggregate counts are cached
        // briefly so the landing load stays cheap — the row data itself is fetched and
        // cached separately via data_ajax(). Tenancy: User::company() is company-scoped;
        // the raw DB::table() counts are filtered by company_id explicitly.
        $companyId = $this->user->company_id;

        $this->teamCounts = cache()->remember(
            'ats:teamcounts:'.$companyId,
            now()->addMinutes(ALL_TEAM_TIME),
            function () use ($companyId) {
                $status = User::query()->company()
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw("SUM(status = 'active' AND (email_verification_code IS NULL OR email_verification_code = '')) as active")
                    ->selectRaw("SUM(status = 'inactive' AND (email_verification_code IS NULL OR email_verification_code = '')) as inactive")
                    ->selectRaw("SUM((email_verification_code IS NOT NULL AND email_verification_code != '') OR (password = '' OR password IS NULL)) as invited")
                    ->first();

                // Captain = members acting as SPOC/captain on at least one assigned job.
                $captain = DB::table('job_assign')
                    ->where('company_id', $companyId)
                    ->where('spok_user_id', '>', 0)
                    ->distinct()
                    ->count('spok_user_id');

                // TRM = members who relate/manage at least one talent (submitted_by).
                $trm = DB::table('job_applications')
                    ->where('company_id', $companyId)
                    ->where('submitted_by', '>', 0)
                    ->distinct()
                    ->count('submitted_by');

                return [
                    'total' => (int) ($status->total ?? 0),
                    'active' => (int) ($status->active ?? 0),
                    'inactive' => (int) ($status->inactive ?? 0),
                    'invited' => (int) ($status->invited ?? 0),
                    'suspended' => 0, // users.status enum has no "suspended" state yet
                    'captain' => (int) $captain,
                    'trm' => (int) $trm,
                ];
            }
        );

        $this->usersCount = $this->teamCounts['total'];

        //  GeneralMethod::userIndexingupload((int) 6);
        // V2 Phase 1: Removed dead query `$this->users = User::with('company')->company()->get();`
        // because row data is fetched asynchronously via data_ajax().
        return view('service_company.team.index', $this->data);
    }

    public function teamDelete(Request $request)
    {

        if (count($request->arr) > 0) {
            /**
                foreach($request->arr as $userID){
                    // Soft delete a user
                    User::find($userID)->delete();

                    // Check if a user is soft deleted
                    User::withTrashed()->find($userID);

                    // Restore a user
                    User::withTrashed()->find($userID)->restore();

                    // Permanently delete a user
                    User::withTrashed()->find($userID)->forceDelete();

                    // Get only soft deleted users
                    User::onlyTrashed()->get();
                }
             */
            DB::table('users')->whereIn('id', $request->arr)->update(['status' => 'inactive', 'working_status' => 0]);
            // DB::table('users_profile')->whereIn('user_id', $request->arr)->delete();
            // DB::table('client_contact_info')->whereIn('user_id', $request->arr)->delete();
            // Phase 3.4: bump epoch for each deactivated user so their sessions are kicked.
            foreach ((array) $request->arr as $uid) {
                Cache::put('user:epoch:'.(int) $uid, now()->timestamp, 7 * 24 * 3600);
            }
            // V2 Phase 4: Invalidate cached team counts and team listing for the company
            $this->clearTeamCache($this->user->company_id);

            $message = 'Team member inactive successfully.';
            $type = 'success';

            return Reply::dataOnly(['status' => 'success', 'msgType' => $type, 'message' => $message]);
        }
    }

    /**
     * Clears cached team counts and increments the tenant team version key
     * to instantly invalidate all cached team listings across the company.
     *
     * @param  int  $companyId
     * @return void
     */
    protected function clearTeamCache($companyId)
    {
        if (empty($companyId)) {
            return;
        }

        try {
            Cache::forget('ats:teamcounts:'.$companyId);
            $versionKey = 'ats:teamversion:company_'.$companyId;
            if (Cache::has($versionKey)) {
                Cache::increment($versionKey);
            } else {
                Cache::put($versionKey, 2, now()->addDays(30));
            }
            $userId = auth()->id() ?? ($this->user->id ?? null);
            if ($userId) {
                GeneralMethod::cacheClean('allteam_version_user_'.$userId);
            }
        } catch (\Throwable $e) {
            \Log::warning("clearTeamCache failed for company {$companyId}: ".$e->getMessage());
        }
    }

    public function data_ajax(Request $request)
    {

        // abort_if(Gate::denies('view_team'), 403);
        abort_if(Gate::denies('view_team'), 403);
        $company_id = $this->user->company_id;
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);
        // Status tab on the redesigned list: all|active|inactive|invited|suspended
        $statusTab = request('statusTab');

        $reportMonth = Carbon::now()->startOfMonth()->subMonth();
        $rankstartOfMonthForReport = $reportMonth->copy()->startOfMonth()->toDateTimeString();      // 2025-06-01
        $rankendOfMonthForReport = $reportMonth->copy()->endOfMonth()->toDateTimeString();

        $rankstartOfMonthForReport = Carbon::parse($rankstartOfMonthForReport)->toIso8601String();
        $rankendOfMonthForReport = Carbon::parse($rankendOfMonthForReport)->toIso8601String();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'custom_search' => $custom_search,
            'user_id' => $this->user->id,
            'sortingfilter' => request('sortingfilter'),
            'statusTab' => $statusTab,
        ];

        // V2 Phase 4: Tenant-scoped version key so all company team members see fresh updates instantly
        $versionKey = 'ats:teamversion:company_'.$company_id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allteam:company:'.$company_id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        // 🔹 Sorting defaults
        $sortField = 'id';
        $sortOrder = 'desc';

        // 🔹 Must conditions
        $must = [];

        if (! empty($custom_search)) {
            $must[] = [
                'bool' => [
                    'must' => [[
                        'bool' => [
                            'should' => [
                                ['match_phrase_prefix' => ['name' => '*'.strtolower($custom_search).'*']],
                                ['wildcard' => ['email' => '*'.strtolower($custom_search).'*']],
                                ['match_phrase_prefix' => ['designation' => '*'.strtolower($custom_search).'*']],
                                ['match_phrase_prefix' => ['display_name' => '*'.strtolower($custom_search).'*']],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ]],
                ],
            ];
        }

        // company access filter
        $must[] = [
            'bool' => [
                'should' => [['term' => ['company_id' => $company_id]]],
                'minimum_should_match' => 1,
            ],
        ];

        // Status tab filter. "invited" = members who have not set a password yet
        // (empty/absent password). "suspended" has no backing status today, so it
        // resolves to an empty set. "all" applies no extra constraint.
        if (! empty($statusTab) && $statusTab !== 'all') {
            if ($statusTab === 'invited') {
                $must[] = [
                    'bool' => [
                        'should' => [
                            ['wildcard' => ['email_verification_code' => '?*']],
                            ['bool' => ['must_not' => [['wildcard' => ['password' => '?*']]]]],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];
            } elseif ($statusTab === 'active') {
                $must[] = ['term' => ['status' => 'active']];
                $must[] = ['bool' => ['must_not' => [['wildcard' => ['email_verification_code' => '?*']]]]];
            } elseif ($statusTab === 'inactive') {
                $must[] = ['term' => ['status' => 'inactive']];
                $must[] = ['bool' => ['must_not' => [['wildcard' => ['email_verification_code' => '?*']]]]];
            } elseif ($statusTab === 'suspended') {
                $must[] = ['term' => ['status' => 'suspended']];
            } else {
                $must[] = ['term' => ['status' => $statusTab]];
            }
        }

        // handle sorting filter request
        if (request('sortingfilter') != '') {
            $valCheck = request('sortingfilter');
            if ($valCheck == 1) {
                $sortField = 'created_at';
                $sortOrder = 'desc';
            } elseif ($valCheck == 2) {
                $sortField = 'created_at';
                $sortOrder = 'asc';
            } else {
                $sortField = 'id';
                $sortOrder = 'asc';
            }
        } else {
            $sortField = 'id';
            $sortOrder = 'asc';

        }

        // -------------------------
        // 1. Fetch ALL users
        // -------------------------
        $userSearchPayload_rank = [
            'index' => ELASTIC_USERS_LIST,
            'request_cache' => true,
            'body' => [
                'size' => 10000, // fetch all (for ranking)
                '_source' => [
                    'id', 'company_id', 'company_name', 'root_user', 'name', 'email', 'calling_code',
                    'mobile', 'password', 'status', 'working_status', 'display_name',
                    'role_name', 'designation', 'image', 'last_login', 'created_at',
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'asc']],
                    ['status' => ['order' => 'asc']],
                    [$sortField => ['order' => $sortOrder]],
                ],
            ],
        ];

        $userResponse_rank = $this->elasticService->getClient()->search($userSearchPayload_rank);
        $usersteam = collect($userResponse_rank['hits']['hits'])->pluck('_source');
        $total = $userResponse_rank['hits']['total']['value'] ?? 0;

        // -------------------------
        // 2. RANK QUERY (Scores Aggregation)
        // -------------------------
        $rankQuery = [
            'index' => ELASTIC_TEAM_RANK,
            'body' => [
                'size' => 0, // only aggregations
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['doc.company_id' => $company_id]],
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $rankstartOfMonthForReport,
                                    'lte' => $rankendOfMonthForReport,
                                ],
                            ]],
                        ],
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($rankQuery);
        $buckets = $response['aggregations']['user_scores']['buckets'] ?? [];

        // -------------------------
        // 3. Prepare Score Map
        // -------------------------
        $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
            $score =
                ($item['profileUpload_sum']['value'] ?? 0) +
                ($item['interviewScheduled_sum']['value'] ?? 0) +
                ($item['interviewNoShow_sum']['value'] ?? 0) +
                ($item['offerReleased_sum']['value'] ?? 0) +
                ($item['candidateJoined_sum']['value'] ?? 0) +
                ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0) +
                ($item['teamCaptainOfferReleased_sum']['value'] ?? 0) +
                ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0) +
                ($item['sourcing_sum']['value'] ?? 0) +
                ($item['screening_sum']['value'] ?? 0) +
                ($item['evaluation_sum']['value'] ?? 0) +
                ($item['negotiation_sum']['value'] ?? 0) +
                ($item['backout_sum']['value'] ?? 0) +
                ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'score' => $score,
                'profileUpload' => $item['profileUpload_sum']['value'] ?? 0,
                'interviewScheduled' => $item['interviewScheduled_sum']['value'] ?? 0,
                'interviewNoShow' => $item['interviewNoShow_sum']['value'] ?? 0,
                'offerReleased' => $item['offerReleased_sum']['value'] ?? 0,
                'candidateJoined' => $item['candidateJoined_sum']['value'] ?? 0,
                'interestedCandidates' => $item['interestedCandidates_sum']['value'] ?? 0,
                'notInterestedCandidates' => $item['notInterestedCandidates_sum']['value'] ?? 0,
                'teamCaptainInterviewScheduled' => $item['teamCaptainInterviewScheduled_sum']['value'] ?? 0,
                'teamCaptainInterviewNoShow' => $item['teamCaptainInterviewNoShow_sum']['value'] ?? 0,
                'teamCaptainOfferReleased' => $item['teamCaptainOfferReleased_sum']['value'] ?? 0,
                'teamCaptainCandidateJoined' => $item['teamCaptainCandidateJoined_sum']['value'] ?? 0,
                'sourcing' => ($item['sourcing_sum']['value'] ?? 0),
                'screening' => ($item['screening_sum']['value'] ?? 0),
                'evaluation' => ($item['evaluation_sum']['value'] ?? 0),
                'negotiation' => ($item['negotiation_sum']['value'] ?? 0),
                'backout' => ($item['backout_sum']['value'] ?? 0),
                'backoutTeam' => ($item['backoutTeam_sum']['value'] ?? 0),
                'candidateJoinedContract' => ($item['candidateJoinedContract_sum']['value'] ?? 0),
                'teamCaptainCandidateJoinedContract' => ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0),
                'backoutContract' => ($item['backoutContract_sum']['value'] ?? 0),
                'backoutTeamContract' => ($item['backoutTeamContract_sum']['value'] ?? 0),
            ]];
        });

        // -------------------------
        // 4. Merge + Ranking
        // -------------------------
        $allRankedUsers = $usersteam->map(function ($userteam) use ($scoreMap) {
            $user_id = $userteam['id'];

            return array_merge($userteam, [
                'score' => $scoreMap[$user_id]['score'] ?? 0,
                'profileUpload' => $scoreMap[$user_id]['profileUpload'] ?? 0,
                'interviewScheduled' => $scoreMap[$user_id]['interviewScheduled'] ?? 0,
                'interviewNoShow' => $scoreMap[$user_id]['interviewNoShow'] ?? 0,
                'offerReleased' => $scoreMap[$user_id]['offerReleased'] ?? 0,
                'candidateJoined' => $scoreMap[$user_id]['candidateJoined'] ?? 0,
                'interestedCandidates' => $scoreMap[$user_id]['interestedCandidates'] ?? 0,
                'notInterestedCandidates' => $scoreMap[$user_id]['notInterestedCandidates'] ?? 0,
                'teamCaptainInterviewScheduled' => $scoreMap[$user_id]['teamCaptainInterviewScheduled'] ?? 0,
                'teamCaptainInterviewNoShow' => $scoreMap[$user_id]['teamCaptainInterviewNoShow'] ?? 0,
                'teamCaptainOfferReleased' => $scoreMap[$user_id]['teamCaptainOfferReleased'] ?? 0,
                'teamCaptainCandidateJoined' => $scoreMap[$user_id]['teamCaptainCandidateJoined'] ?? 0,
                'candidateJoinedContract' => $scoreMap[$user_id]['candidateJoinedContract'] ?? 0,
                'teamCaptainCandidateJoinedContract' => $scoreMap[$user_id]['teamCaptainCandidateJoinedContract'] ?? 0,
                'backoutContract' => $scoreMap[$user_id]['backoutContract'] ?? 0,
                'backoutTeamContract' => $scoreMap[$user_id]['backoutTeamContract'] ?? 0,
                'sourcing' => ($scoreMap['sourcing']['value'] ?? 0),
                'screening' => ($scoreMap['screening']['value'] ?? 0),
                'evaluation' => ($scoreMap['evaluation']['value'] ?? 0),
                'negotiation' => ($scoreMap['negotiation']['value'] ?? 0),
                'backout' => ($scoreMap['backout']['value'] ?? 0),
                'backoutTeam' => ($scoreMap['backoutTeam']['value'] ?? 0),
            ]);
        })
            ->sort(function ($a, $b) {

                // 1️⃣ Active users first
                $aStatus = strtolower($a['status'] ?? 'inactive');
                $bStatus = strtolower($b['status'] ?? 'inactive');

                if ($aStatus !== $bStatus) {
                    return ($aStatus === 'active') ? -1 : 1;
                }

                // 2️⃣ Higher score first
                if (($a['score'] ?? 0) !== ($b['score'] ?? 0)) {
                    return ($b['score'] ?? 0) <=> ($a['score'] ?? 0);
                }

                // 3️⃣ User ID: ASC
                if (($a['id'] ?? 0) !== ($b['id'] ?? 0)) {
                    return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
                }

                // 3️⃣ Latest created
                // return strtotime($b['created_at']) <=> strtotime($a['created_at']);
            })
            ->values();

        // Add rank for fully active recruiters only (status == 'active' AND password set)
        // Invited users (no password) & inactive users get 'N/A'
        $activeRankIndex = 1;
        $allRankedUsers = $allRankedUsers->map(function ($user) use (&$activeRankIndex) {
            $isInvited = ! empty($user['email_verification_code']) || empty($user['password']);
            $isFullyActive = strtolower($user['status'] ?? '') === 'active' && ! $isInvited;
            if ($isFullyActive) {
                $user['rank'] = $activeRankIndex++;
            } else {
                $user['rank'] = 'N/A';
            }

            return $user;
        });

        // -------------------------
        // 5. Paginate
        // -------------------------

        $data = $allRankedUsers->slice($from, $size)->values();

        // V2 Phase 2: Batch pre-fetch workload and rank metrics for sliced page IDs
        // to eliminate N+1 queries inside Blade view rendering.
        $userIds = $data->pluck('id')->filter()->all();
        $rankMonth = Carbon::now()->startOfMonth()->subMonth()->format('M Y');

        $activeJobsMap = ! empty($userIds) ? DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->where('job_assign.company_id', $company_id)
            ->whereIn('job_assign.user_id', $userIds)
            ->where('jobs.status', 'active')
            ->select('job_assign.user_id', DB::raw('COUNT(DISTINCT job_assign.job_id) as total'))
            ->groupBy('job_assign.user_id')
            ->pluck('total', 'job_assign.user_id') : collect();

        $captainJobsMap = ! empty($userIds) ? DB::table('job_assign')
            ->where('company_id', $company_id)
            ->whereIn('spok_user_id', $userIds)
            ->select('spok_user_id', DB::raw('COUNT(DISTINCT job_id) as total'))
            ->groupBy('spok_user_id')
            ->pluck('total', 'spok_user_id') : collect();

        $trmTalentsMap = ! empty($userIds) ? DB::table('bench_assign')
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id') : collect();

        $data = $data->map(function ($item) use ($activeJobsMap, $captainJobsMap, $trmTalentsMap, $rankMonth) {
            $uid = $item['id'];
            $item['pre_active_jobs'] = (int) ($activeJobsMap[$uid] ?? 0);
            $item['pre_captain_jobs'] = (int) ($captainJobsMap[$uid] ?? 0);
            $item['pre_trm_talents'] = (int) ($trmTalentsMap[$uid] ?? 0);
            $item['pre_rank'] = is_numeric($item['rank'] ?? null) ? (int) $item['rank'] : 'N/A';
            $item['pre_score'] = (int) ($item['score'] ?? 0);
            $item['pre_rank_month'] = is_numeric($item['rank'] ?? null) ? $rankMonth : '';

            return $item;
        });

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.team-list-ajax', compact('data', 'userPermissions', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.team-pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_TEAM_TIME));

        return response()->json($responseData);

    }

    public function viewMore($id)
    {
        // Phase 0/2 Performance Telemetry: Baseline timing start for Team View More Shell
        $startTime = microtime(true);

        $cachePayload = [
            'team_id' => $id,
            'user_id' => $this->user->id,
        ];

        $versionKey = 'teamviewmore_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:teamviewmore:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));

        /* ----------------------------
        ⚡ Check DragonflyDB / Cache first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $this->countryName = '';
        $this->stateName = '';
        $this->userId = $id;
        $this->users = User::join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'users_profile.city_id')
            ->select('users.id', 'users.company_id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status', 'companies.company_name', 'companies.website', 'companies.zoom_status', 'companies.google_status', 'companies.microsoft_status', 'users_profile.country', 'users_profile.state', 'city_list.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills')
            ->with('company')
            ->where('users.id', '=', $id)
            ->company()
            ->first();

        if (! $this->users) {
            abort(404, 'Team member not found.');
        }

        if ($this->users->country > 0) {
            $countryArr = GeneralMethod::getCountryName($this->users->country);
            foreach ($countryArr as $key => $val) {
                $this->countryName = $val['name'];
            }
        }

        if ($this->users->state > 0) {
            $stateArr = GeneralMethod::getStateName($this->users->country, $this->users->state);
            foreach ($stateArr as $key => $val) {
                $this->stateName = $val['state'];
            }
        }

        // Intent: Cache countries reference list with explicit column projection for ultra-fast shell loading.
        // Tenancy: Public reference data shared across tenant views.
        $this->countries = Cache::remember('ref:countries_list_simple', 86400, function () {
            return DB::table('countries')->select('id', 'country_name', 'country_name as name', 'country_code as code', 'phonecode as calling_code')->get();
        });

        // Default lightweight shell placeholders for V2 true lazy loading
        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();
        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        $this->rankings = [
            'months' => collect([]),
            'internalRanks' => [],
            'globalRanks' => [],
        ];

        // Intent: Fetch exact team member header stat card counts (Active Jobs, Candidates Added, Interviews, Joinings, Rank) with 5-minute Redis cache.
        // Tenancy: Filtered by company_id and target team member user ID.
        $companyId = $this->user->company_id;
        $headerStats = Cache::remember("ats:team_header_stats:comp_{$companyId}:user_{$id}", 300, function () use ($id, $companyId) {
            $profileService = app(\App\Services\ServiceCompanyProfileService::class);
            $kpis = $profileService->getKpiMetrics($id, $companyId);
            $ranks = $profileService->getMonthlyRanks($id, $companyId);

            $curM = Carbon::now()->format('Y-m');
            $rankVal = $ranks['internalRanks'][$curM]['rank'] ?? '-';
            $scoreVal = $ranks['internalRanks'][$curM]['score'] ?? 0;

            return [
                'active_count' => (int) ($kpis['counts']->get('active', 0)),
                'total_candidates' => (int) ($kpis['scpCandidatesTotal'] ?? 0),
                'interviewsTrackerTotal' => (int) ($kpis['scpInterviewsTotal'] ?? 0),
                'total_candidates_join' => (int) ($kpis['scpJoiningsTotal'] ?? 0),
                'curMonthStr' => $curM,
                'rankVal' => $rankVal,
                'scoreVal' => $scoreVal,
            ];
        });

        $curM = $headerStats['curMonthStr'] ?? Carbon::now()->format('Y-m');
        $this->rankings = [
            'months' => collect([$curM]),
            'internalRanks' => [$curM => ['rank' => $headerStats['rankVal'] ?? '-', 'score' => $headerStats['scoreVal'] ?? 0]],
            'globalRanks' => [],
        ];

        $this->joined_count = 0;
        $this->sourcing_count = 0;
        $this->screening_count = 0;
        $this->evaluation_count = 0;
        $this->negotiation_count = 0;
        $this->offer_count = 0;
        $this->Rejected_count = 0;
        $this->inreview_count = 0;
        $this->total_candidates = $headerStats['total_candidates'] ?? 0;
        $this->active_count = $headerStats['active_count'] ?? 0;
        $this->hold_count = 0;
        $this->inactive_count = 0;

        $this->labels = [];
        $this->candidateValues = [];
        $this->candidateValuesTotal = 0;

        $this->labelsCommoffer = [];
        $this->candidateValuesCommoffer = [];
        $this->candidateValuesTotaloffer = 0;

        $this->total_candidates_join = $headerStats['total_candidates_join'] ?? 0;
        $this->data_list = [];
        $this->labelsCommjoin = [];
        $this->candidateValuesCommjoin = [];
        $this->candidateValuesTotaljoin = 0;

        $this->upcomingData = [];
        $this->interviewsTrackerTotal = $headerStats['interviewsTrackerTotal'] ?? 0;
        $this->scpOffersTotal = 0;

        $this->interviewlabelsMonth = [];
        $this->interviewValuesMonth = [];
        $this->interviewValuesTotalMonth = 0;

        $this->interviewlabels = [];
        $this->interviewValues = [];
        $this->interviewValuesTotal = 0;

        $durMs = round((microtime(true) - $startTime) * 1000, 2);
        \Log::info('TEAM_VIEWMORE_SHELL_APM: viewMore lightweight shell load completed', [
            'team_id' => $id,
            'user_id' => $this->user->id,
            'dur_ms' => $durMs,
        ]);

        $view = view('service_company.team.show', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view, '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.viewmore']]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ELASTIC_COMPANIES_INDEX));

        return $responseData;
    }

    private function getRankData($company_id, $loggedInUserId, $startDate, $endDate, $isInternal = true)
    {

        // dd($company_id, $loggedInUserId, $startDate, $endDate, $isInternal);
        $userQuery = [
            'index' => ELASTIC_USERS_LIST,
            'request_cache' => true,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => array_values(array_filter([
                            $isInternal ? ['term' => ['company_id' => $company_id]] : null,
                            // ['term' => ['status' => 'active']],
                        ])),
                    ],
                ],
                '_source' => ['id', 'name', 'email', 'designation', 'status'],
                'size' => 10000,
                'sort' => [
                    ['id' => ['order' => 'asc']],
                ],
            ],
        ];

        $userResponse = $this->elasticService->getClient()->search($userQuery);
        $users = collect($userResponse['hits']['hits'])->pluck('_source');

        // Get ranking stats
        $rankQuery = [
            'index' => ELASTIC_TEAM_RANK,
            'request_cache' => true,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => array_values(array_filter([
                            $isInternal ? ['term' => ['doc.company_id' => $company_id]] : null,
                            ['range' => [
                                'doc.created_at' => [
                                    'gte' => $startDate,
                                    'lte' => $endDate,
                                ],
                            ]],
                        ])),
                    ],
                ],
                'aggs' => [
                    'user_scores' => [
                        'terms' => [
                            'field' => 'doc.user_id',
                            'size' => 10000,
                        ],
                        'aggs' => [
                            'profileUpload_sum' => ['sum' => ['field' => 'doc.profileUpload']],
                            'interviewScheduled_sum' => ['sum' => ['field' => 'doc.interviewScheduled']],
                            'interviewNoShow_sum' => ['sum' => ['field' => 'doc.interviewNoShow']],
                            'offerReleased_sum' => ['sum' => ['field' => 'doc.offerReleased']],
                            'candidateJoined_sum' => ['sum' => ['field' => 'doc.candidateJoined']],
                            'interestedCandidates_sum' => ['sum' => ['field' => 'doc.interestedCandidates']],
                            'notInterestedCandidates_sum' => ['sum' => ['field' => 'doc.notInterestedCandidates']],
                            'teamCaptainInterviewScheduled_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewScheduled']],
                            'teamCaptainInterviewNoShow_sum' => ['sum' => ['field' => 'doc.teamCaptainInterviewNoShow']],
                            'teamCaptainOfferReleased_sum' => ['sum' => ['field' => 'doc.teamCaptainOfferReleased']],
                            'teamCaptainCandidateJoined_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoined']],
                            'sourcing_sum' => ['sum' => ['field' => 'doc.sourcing']],
                            'screening_sum' => ['sum' => ['field' => 'doc.screening']],
                            'evaluation_sum' => ['sum' => ['field' => 'doc.evaluation']],
                            'negotiation_sum' => ['sum' => ['field' => 'doc.negotiation']],
                            'backout_sum' => ['sum' => ['field' => 'doc.backout']],
                            'backoutTeam_sum' => ['sum' => ['field' => 'doc.backoutTeam']],
                            'candidateJoinedContract_sum' => ['sum' => ['field' => 'doc.candidateJoinedContract']],
                            'teamCaptainCandidateJoinedContract_sum' => ['sum' => ['field' => 'doc.teamCaptainCandidateJoinedContract']],
                            'backoutContract_sum' => ['sum' => ['field' => 'doc.backoutContract']],
                            'backoutTeamContract_sum' => ['sum' => ['field' => 'doc.backoutTeamContract']],
                            'attachments_sum' => ['sum' => ['field' => 'doc.attachmentCount']],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($rankQuery);
        $buckets = $response['aggregations']['user_scores']['buckets'];

        $scoreMap = collect($buckets)->mapWithKeys(function ($item) {
            $score = ($item['profileUpload_sum']['value'] ?? 0)
                + ($item['interviewScheduled_sum']['value'] ?? 0)
                + ($item['interviewNoShow_sum']['value'] ?? 0)
                + ($item['offerReleased_sum']['value'] ?? 0)
                + ($item['candidateJoined_sum']['value'] ?? 0)
                + ($item['teamCaptainInterviewScheduled_sum']['value'] ?? 0)
                + ($item['teamCaptainOfferReleased_sum']['value'] ?? 0)
                + ($item['teamCaptainCandidateJoined_sum']['value'] ?? 0)
                + ($item['sourcing_sum']['value'] ?? 0)
                + ($item['screening_sum']['value'] ?? 0)
                + ($item['evaluation_sum']['value'] ?? 0)
                + ($item['negotiation_sum']['value'] ?? 0)
                + ($item['backout_sum']['value'] ?? 0)
                + ($item['backoutTeam_sum']['value'] ?? 0) + ($item['candidateJoinedContract_sum']['value'] ?? 0) + ($item['teamCaptainCandidateJoinedContract_sum']['value'] ?? 0) + ($item['backoutContract_sum']['value'] ?? 0) + ($item['backoutTeamContract_sum']['value'] ?? 0);

            return [$item['key'] => [
                'user_id' => $item['key'],
                'profileUpload' => $item['profileUpload_sum']['value'] ?? 0,
                'interviewScheduled' => $item['interviewScheduled_sum']['value'] ?? 0,
                'interviewNoShow' => $item['interviewNoShow_sum']['value'] ?? 0,
                'offerReleased' => $item['offerReleased_sum']['value'] ?? 0,
                'candidateJoined' => $item['candidateJoined_sum']['value'] ?? 0,
                'teamCaptainInterviewScheduled' => $item['teamCaptainInterviewScheduled_sum']['value'] ?? 0,
                'teamCaptainOfferReleased' => $item['teamCaptainOfferReleased_sum']['value'] ?? 0,
                'teamCaptainCandidateJoined' => $item['teamCaptainCandidateJoined_sum']['value'] ?? 0,
                'score' => $score,
            ]];
        });

        $ranked = $users->map(function ($user) use ($scoreMap) {
            $id = $user['id'];

            return [
                'id' => $id,
                'status' => strtolower($user['status'] ?? 'inactive'),
                'score' => $scoreMap[$id]['score'] ?? 0,
            ];
        })
            ->sort(function ($a, $b) {

                // 1️⃣ Active users first
                if ($a['status'] !== $b['status']) {
                    return ($a['status'] === 'active') ? -1 : 1;
                }

                // 2️⃣ Higher score first
                if ($a['score'] !== $b['score']) {
                    return $b['score'] <=> $a['score'];
                }

                // 3️⃣ User ID ASC
                return $a['id'] <=> $b['id'];
            })
            ->values();

        $ranked = $ranked->map(function ($user, $index) {
            $user['rank'] = $index + 1;

            return $user;
        });

        return $ranked->firstWhere('id', $loggedInUserId) ?? ['score' => 0, 'rank' => 'N/A'];
    }

    /**
     * AJAX endpoint for Team Jobs tab data.
     * Intent: Paginated ES search & retrieval of jobs for target recruiter.
     * Tenancy: Scoped to auth user company and authorized target recruiter.
     */
    public function jobdata_ajax(Request $request)
    {
        // Phase 0 Performance Telemetry: Baseline timing start
        $startTime = microtime(true);
        // abort_if(!$this->user->can('view_jobs'), 403);
        abort_if(Gate::denies('view_jobs'), 403);

        // dd("djd");
        $company_id = auth()->user()->company_id;
        $userId = (int) ($request->input('userId') ?? $this->user->id);
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $limit = '';

        // dd($userId);
        $chkAdminRole = $this->user->hasRole('admin');
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $custom_status = request('custom_status');
        $custom_hiringtype = request('custom_hiringtype');
        $custom_search = trim(request('custom_search'));

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];

        $sort = [];

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // 1. STATUS FILTERING
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];

        // 2. HIRING TYPE
        if ($custom_hiringtype !== '' && $custom_hiringtype !== 'All') {
            if ($custom_hiringtype == 7) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 8) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 3. SEARCH CONDITIONS (similar to LIKE, IN, etc.)
        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['comId' => $company_id]],
                                    [
                                        'nested' => [
                                            'path' => 'recommendation_relation',
                                            'query' => [
                                                'term' => ['recommendation_relation.to_company_id' => $company_id],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    ['match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*']],
                                    ['term' => ['id' => (int) $custom_search]],
                                    // ['wildcard' => ['client_name' => '*' . strtolower($custom_search) . '*']],
                                    ['wildcard' => ['client_or_company_name' => '*'.strtolower($custom_search).'*']],
                                    [
                                        'nested' => [
                                            'path' => 'job_assign',
                                            'query' => [
                                                'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_contact_info_jobs',
                                            'query' => [
                                                'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // 4. COMPANY ACCESS CONDITIONS
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['comId' => $company_id]],
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'term' => ['recommendation_relation.to_company_id' => $company_id],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        // 5. RESTRICT TO ASSIGNED USERS (if not admin)

        $must[] = [
            'nested' => [
                'path' => 'job_assign',
                'query' => [
                    'term' => ['job_assign.user_id' => $userId],
                ],
            ],
        ];

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                '_source' => [
                    'id', 'title', 'status', 'created_at', 'min_salary', 'max_salary',
                    'min_sal_mode', 'max_sal_mode', 'min_salary_currency', 'max_salary_currency',
                    'comId', 'client_name', 'company_name', 'category_id', 'job_assign',
                    'logo_logical_path', 'logo', 'self_company', 'recommendation_relation', 'employment_type',
                    'work_type', 'job_metronics_skills', 'client_city_jobs', 'total_positions', 'client_contact_info_jobs',
                ],
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;

        $durMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'html' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team-jobs-ajax', compact('data', 'userPermissions', 'company_id', 'limit', 'salaryMode', 'currencySymbole'))->render()),
            'total' => $total,
            'pagination' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.pagination', compact('pagination'))->render()),
            '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.jobdata_ajax'],
        ])->header('Server-Timing', "team_jobdata;dur={$durMs};desc=\"Team Job Data AJAX\"");
    }

    public function edit($id)
    {

        // abort_if(! $this->user->can('edit_team'), 403);

        /*if($id == $this->user->id){
                                        abort(403);
            }*/
        // $this->roles = Role::all();
        // $this->team = User::find($id);

        // Phase 2 Optimization: Wrap ES lookup in try/catch to prevent page load delays or failure if ES is unreachable
        try {
            $params = [
                'index' => ELASTIC_USERS_LIST,
                'id' => $id,
            ];
            $response = $this->elasticService->getClient()->get($params);
            $team_details = (object) ($response['_source'] ?? []);
            $this->city_name = $team_details->city_name ?? '';
            $this->state_name = $team_details->state_name ?? '';
            $this->country_name = $team_details->country_name ?? '';
        } catch (\Throwable $e) {
            $this->city_name = '';
            $this->state_name = '';
            $this->country_name = '';
        }

        $this->statelist = [];
        $this->team = User::join('companies', 'companies.id', '=', 'users.company_id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('users.id', 'users.company_id', 'users.name', 'users.email', 'users.mobile', 'users.created_at', 'users.image', 'users.status',
                'companies.company_name', 'companies.website', 'users.whatsapp_number', 'users.linkedin',
                'users_profile.country', 'users_profile.state', 'users_profile.city', 'users_profile.city_id', 'users_profile.designation', 'users_profile.skills'
            )
            ->with('company')
            ->where('users.id', '=', $id)
            ->company()
            ->first();

        abort_if(! $this->team, 404);

        // Phase 2 Optimization: Cache static reference lists in Redis (24 hour TTL)
        $this->categories = cache()->remember('ats:job_categories', 86400, function () {
            return JobCategory::all();
        });
        $this->companyType = GeneralMethod::companyType();
        $this->department = GeneralMethod::department();
        $this->countryArr = cache()->remember('ats:country_list', 86400, function () {
            return GeneralMethod::getAllCountryList();
        });

        if ($this->team->country > 0) {
            $this->statelist = GeneralMethod::statelist($this->team->country);
        }

        $this->cityListArr = '';
        if ($this->team->country == 101) {
            $statelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $this->statelist = json_decode(json_encode($statelist), true);

            $citylist = DB::table('city_list')
                ->select('city_list.city_id as id', 'city_list.city as name')
                ->where('city_list.state_id', $this->team->state)
                ->orderBy('city_list.city')
                ->get()->toArray();
            $this->cityListArr = json_decode(json_encode($citylist), true);
        }

        $cities = DB::table('city_list')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city as city_name', 'state_list.state as state_name', 'countries.country_name')
            ->where('city_list.city_id', '=', $this->team->city_id)
            ->orderBy('city_list.city')
            ->limit(10)
            ->cursor()
            ->map(function ($city) {
                return $city->city_name.' | '.$city->state_name.' | '.$city->country_name;
            });

        if ($cities && count($cities) > 0) {
            $this->cities = $cities;
        } else {
            $this->cities = GeneralMethod::allCities();
        }

        return view('service_company.profile.edit', $this->data);
    }

    public function WorkingStatus(Request $request)
    {
        $packageStatus = GeneralMethod::validatePackage('USERS');

        $company_id = auth()->user()->company_id;
        $chkStatus = $request->chkStatus;
        $userId = $request->userId;

        // Phase 11 Optimization: Strict tenant scoping validation
        User::where('id', $userId)->where('company_id', $company_id)->firstOrFail();

        if ($chkStatus == 'true') {

            $eventId = 1;
            $coinsDeductions = GeneralMethod::coinsValidations((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            } else {
                GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '0');
                DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['working_status' => 1, 'auto_date_team' => Carbon::today()]);
                $activeUserCount = DB::table('users')->select('users.id')->where('users.company_id', @$company_id)->where('users.root_user', '!=', 1)->where('users.working_status', '=', 1)->count();
                $users_balance = $this->customActivePackage['users_quantity'] - $activeUserCount;
                DB::table('package_active')->where('package_active.company_id', @$company_id)->limit(1)
                    ->update(['users_used' => $activeUserCount, 'users_balance' => $users_balance]);
                $this->clearTeamCache($company_id);

                return Reply::dataOnly(['status' => 'success']);

            }

            // if($packageStatus['STATUS']==1){
            // 	if($packageStatus['EXPIRE']== 'false'){
            // 		if($packageStatus['BALANCE'] > 0 ){

            // 	DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1) ->update(array('working_status' => 1));
            // 	$activeUserCount	=	DB::table('users')->select('users.id')->where('users.company_id', @$company_id)->where('users.root_user', '!=', 1)->where('users.working_status', '=', 1)->count();
            // 	$users_balance		=	$this->customActivePackage['users_quantity']-$activeUserCount;
            // 	DB::table('package_active')->where('package_active.company_id', @$company_id)->limit(1)
            // 	->update(array('users_used' 	=> $activeUserCount, 'users_balance' => $users_balance) );
            // 	return Reply::dataOnly(['status' => 'success']);

            // 		}else{
            // 			return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package balance max limit reached , please contact to Administrator.']);
            // 		}
            // 	}else{
            // 		return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package expired , please contact to Administrator.']);
            // 	}
            // }else{
            // 	return Reply::dataOnly(['status' => 'info', 'message'=> 'Team user working status can not update because your package status is Inactive , please contact to Administrator.']);
            // }

        } else {

            DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['working_status' => 0]);
            $activeUserCount = DB::table('users')->select('users.id')->where('users.company_id', @$company_id)->where('users.root_user', '!=', 1)->where('users.working_status', '=', 1)->count();
            $users_balance = $this->customActivePackage['users_quantity'] - $activeUserCount;
            if ($users_balance < 0) {
                $users_balance = 0;
            }
            DB::table('package_active')->where('package_active.company_id', @$company_id)->limit(1)
                ->update(['users_used' => $activeUserCount, 'users_balance' => $users_balance]);
            $this->clearTeamCache($company_id);

            return Reply::dataOnly(['status' => 'success']);

        }

    }

    public function statusUpdate(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $chkStatus = $request->chkStatus;
        $userId = $request->userId;

        // Phase 11 Optimization: Strict tenant scoping validation
        User::where('id', $userId)->where('company_id', $company_id)->firstOrFail();

        $status_text = '';
        if ($chkStatus == 'true') {
            DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['status' => 'active', 'working_status' => 1, 'auto_date_team' => Carbon::today()]);
            $status_text = 'Avtive';
        } else {

            DB::table('users')->where('company_id', @$company_id)->where('id', @$userId)->limit(1)->update(['status' => 'inactive', 'working_status' => 0]);
            // Phase 3.4: bump epoch so active sessions are kicked on next request.
            Cache::put('user:epoch:'.(int) $userId, now()->timestamp, 7 * 24 * 3600);
            $status_text = 'Avtive';
        }

        GeneralMethod::userIndexingupload($userId);

        // V2 Phase 4: Invalidate cached team counts and tenant team listing
        $this->clearTeamCache($company_id);

        return Reply::dataOnly(['status' => 'success', 'status_text' => $status_text]);
    }

    public function reSendmail(Request $request)
    {

        $user = User::withoutGlobalScopes()->find($request->userId);
        $Companies = Company::withoutGlobalScopes()->find($user->company_id);
        $company_id = $user->company_id;

        $Url = url('/teamReg/'.base64_encode($company_id).'/'.base64_encode($user->id));
        $to = $user->email;

        $data = [
            'to' => $user->email,
            'name' => $user->name,
            'url' => $Url,
            'company_name' => $Companies->company_name,
        ];
        // dd($data);
        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];
        $subject = 'Account Credential';
        $cc = [];

        // QueueMailService::dispatch(
        //     $user->email,
        //     $subject,
        //     'email.team.create-account',
        //     (object) $data,
        //     $attachments,
        //     $fromAddress,
        //     $senderName,
        //     $cc,
        //     $company_id
        // )->onConnection('emails');
        QueueMailService::dispatch(
            $user->email,
            $subject,
            'email.team.create-account',
            (object) $data,
            $attachments,
            $fromAddress,
            $senderName,
            $cc,
            $company_id
        )
            ->onConnection('emails')
            ->onQueue('email');

        // $result = $this->mailService->sendEmail($user->email, $subject,'email.team.create-account', (object) $data, $attachments, $fromAddress, $senderName,$cc);
        $message = 'Team member created successfully. set password link has been sent to team member email';
        $type = 'success';

        return Reply::dataOnly(['status' => 'success', 'msgType' => $type, 'message' => $message]);

        //

    }

    public function store(StoreTeam $request)
    {
        $company_id = auth()->user()->company_id;

        // Phase 9 Optimization: Redis Atomic Lock against rapid duplicate invitation submissions
        $lockKey = 'ats:team:invite:'.md5(strtolower((string) $request->email));
        $lock = Cache::lock($lockKey, 10);
        if (! $lock->get()) {
            return Reply::dataOnly([
                'status' => 'success',
                'msgType' => 'warning',
                'message' => 'An invitation request for this email is currently in progress. Please wait a moment.',
            ]);
        }

        try {
            $count = DB::table('users')->select('users.id')->where('email', '=', $request->email)->count();

            $eventId = 1;
            $coinsDeductions = GeneralMethod::coinsValidations((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            }

            if ($count == 0) {
                if (! empty($request->name)) {
                    DB::beginTransaction();
                    try {
                        $user = new User;
                        $user->name = $request->name;
                        $user->email = $request->email;
                        $user->company_id = $company_id;
                        $user->email_verification_code = str_random(40);

                        $user->status = 'inactive';
                        $user->working_status = 0;

                        if ($request->hasFile('logo')) {
                            $file = $request->file('logo');
                            $filename = time().'.'.$file->getClientOriginalExtension();
                            $filePath = 'Images/'.$filename;

                            Storage::disk('s3')->put($filePath, file_get_contents($file));

                            $fileUrl = Storage::disk('s3')->url($filePath);
                            $user->image = $fileUrl;
                        }

                        $user->save();

                        // Phase 3 Optimization: Deduplicate role attachment (single Spatie assignRole + pivot sync)
                        if ($request->role_id > 0) {
                            $role_row = DB::table('roles')->select('roles.name')->where('roles.id', $request->role_id)->first();
                            if ($role_row) {
                                $user->assignRole($role_row->name);
                            }
                            DB::table('role_user')->updateOrInsert([
                                'user_id' => $user->id,
                                'role_id' => $request->role_id,
                            ]);
                        }

                        $users_profile = ['company_id' => $company_id, 'user_id' => $user->id, 'country' => 101, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                        DB::table('users_profile')->insert($users_profile);

                        DB::commit();

                        $Url = url('/teamReg/'.base64_encode($company_id).'/'.base64_encode($user->id));
                        $to = $request->email;
                        $Companies = Company::withoutGlobalScopes()->find(auth()->user()->company_id);
                        $data = [
                            'to' => $request->email,
                            'name' => $request->name,
                            'url' => $Url,
                            'company_name' => $Companies->company_name ?? '',
                        ];

                        $basecompany = GeneralMethod::baseCompany();
                        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                        $attachments = [];
                        $cc = [];
                        $subject = 'Account Credential';

                        QueueMailService::dispatch(
                            $request->email,
                            $subject,
                            'email.team.create-account',
                            (object) $data,
                            $attachments,
                            $fromAddress,
                            $senderName,
                            $cc,
                            auth()->user()->company_id
                        )->onConnection('emails')->onQueue('email');

                        $message = 'Team member created successfully. set password link has been sent to team member email';
                        $type = 'success';

                        // Update Elasticsearch document synchronously post-commit and dispatch queue job as fallback
                        GeneralMethod::userIndexingupload((int) $user->id);
                        SyncUserToElasticJob::dispatch((int) $user->id)->afterCommit();
                        $this->clearTeamCache($company_id);
                    } catch (\Throwable $e) {
                        DB::rollBack();

                        return Reply::dataOnly(['status' => 'success', 'msgType' => 'warning', 'message' => 'Failed to create team member: '.$e->getMessage()]);
                    }
                } else {
                    $message = 'User detail can not be null !';
                    $type = 'warning';
                }

            } else {
                $count = 0;
                $count = DB::table('users')->select('users.id')->where('email', '=', $request->email)->where('status', '=', 'inactive')->count();
                if ($count == 1) {
                    $message = 'Already user register & account not activated';
                    $type = 'warning';
                } else {
                    $message = 'Already user register';
                    $type = 'warning';
                }
            }

            return Reply::dataOnly(['status' => 'success', 'msgType' => $type, 'message' => $message]);
        } finally {
            optional($lock)->release();
        }

    }

    public function update(UpdateTeam $request, $id)
    {
        $company_id = auth()->user()->company_id;

        $user = User::where('id', $id)->where('company_id', $company_id)->firstOrFail();

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time().'.'.$file->getClientOriginalExtension();
                $filePath = 'Images/'.$filename;

                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $fileUrl = Storage::disk('s3')->url($filePath);
                $user->image = $fileUrl;
            }
            $user->save();

            $users_profile_count = DB::table('users_profile')->where('user_id', $id)->where('company_id', $company_id)->count();

            $values = [
                'company_id' => $company_id,
                'user_id' => $id,
                'designation' => $request->designation,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
            ];
            if ($users_profile_count > 0) {
                DB::table('users_profile')->where('company_id', $company_id)->where('user_id', $id)->limit(1)->update($values);
            } else {
                DB::table('users_profile')->insert($values);
            }

            DB::commit();

            // Update Elasticsearch document synchronously post-commit and dispatch queue job as fallback
            GeneralMethod::userIndexingupload((int) $id);
            SyncUserToElasticJob::dispatch((int) $id)->afterCommit();
            $this->clearTeamCache($company_id);

            $userId = auth()->id();
            $cachekeys = [
                'allteam_version_user_'.$userId,
                'teamviewmore_version_user_'.$userId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            $message = 'update successfully';

            return Reply::dataOnly(['status' => 'success', 'message' => $message]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return Reply::dataOnly(['status' => 'error', 'message' => 'Failed to update team member: '.$e->getMessage()]);
        }
    }

    public function editrole($id)
    {
        $company_id = auth()->user()->company_id;

        $targetUser = User::where('id', $id)->where('company_id', $company_id)->first();
        abort_if(! $targetUser, 404);

        $this->roleslist = DB::table('roles')
            ->select('roles.id', 'roles.display_name', 'roles.name', 'roles.priority', 'roles.description')
            ->where('roles.company_id', '=', $company_id)
            ->orderBy('roles.name', 'ASC')
            ->get();

        $this->selrole = DB::table('role_user')
            ->select('role_user.role_id')
            ->where('role_user.user_id', '=', $id)
            ->first();

        // Current role name for the badge
        $currentRoleName = '';
        if ($this->selrole) {
            $cr = DB::table('roles')->where('id', $this->selrole->role_id)->value('display_name');
            $currentRoleName = $cr ? ucwords($cr) : '';
        }
        $this->currentRoleName = $currentRoleName;

        // User details for the drawer header
        $this->editUser = DB::table('users')
            ->select('id', 'name', 'email', 'image', 'created_at', 'status')
            ->where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        $this->id = $id;

        $view = view('service_company.team.editrole', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function roleupdate(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $cuser_id = $request->cuser_id;
        $user_role = $request->user_role;

        if (! empty($cuser_id) && ! empty($user_role)) {
            // Phase 11 Optimization: Strict tenant scoping validation
            $targetUser = User::where('id', $cuser_id)->where('company_id', $company_id)->firstOrFail();
            DB::beginTransaction();
            try {
                DB::table('role_user')
                    ->where('user_id', $cuser_id)
                    ->limit(1)
                    ->update(['role_id' => $user_role]);

                $rolesAs = DB::table('roles')
                    ->join('role_user', 'role_user.role_id', '=', 'roles.id')
                    ->select('roles.name')
                    ->where('role_user.user_id', '=', $cuser_id)
                    ->first();
                if ($rolesAs) {
                    $user = User::withoutGlobalScopes()->find($cuser_id);
                    if ($user) {
                        $user->assignRole($rolesAs->name);
                    }
                }

                DB::commit();

                $userId = auth()->id();
                $cachekeys = [
                    'allteam_version_user_'.$userId,
                    'teamviewmore_version_user_'.$userId,
                ];
                foreach ($cachekeys as $key) {
                    GeneralMethod::cacheClean($key);
                }

                // Update Elasticsearch document synchronously post-commit and dispatch queue job as fallback
                GeneralMethod::userIndexingupload((int) $cuser_id);
                SyncUserToElasticJob::dispatch((int) $cuser_id)->afterCommit();
                $this->clearTeamCache($company_id);

                return Reply::dataOnly(['status' => 'success']);
            } catch (\Throwable $e) {
                DB::rollBack();

                return Reply::dataOnly(['status' => 'error', 'message' => 'Failed to update role: '.$e->getMessage()]);
            }
        } else {
            return Reply::dataOnly(['status' => 'warning', 'message' => 'something worng, please try again one more time ']);
        }

    }

    public function teamScoreboard(Request $request)
    {
        $userId = (int) $request->get('user_id');
        $companyId = $this->user->company_id;

        $reportMonth = \Carbon\Carbon::now()->startOfMonth()->subMonth();
        $start = $reportMonth->copy()->startOfMonth()->toIso8601String();
        $end = $reportMonth->copy()->endOfMonth()->toIso8601String();

        try {
            // Internal rank (within company) — full result includes leaders + total
            $internal = \App\Helper\GeneralMethod::getTeamRankData($companyId, $userId, $start, $end, true, true);
            $userData = $internal['user'] ?? ['score' => 0, 'rank' => 'N/A'];
            $leaders = $internal['leaders'] ?? collect();
            $total = $internal['total'] ?? 0;

            // Team page shows internal ranks only — no cross-company global rank
            $view = view('service_company.dashboard._scoreboard', [
                'U' => is_array($userData) ? $userData : (array) $userData,
                'totalCount' => $total,
                'mode' => 'internal',
                'otherRank' => null,
                'month' => date('M Y', strtotime($start)),
                'leaders' => $leaders,
            ])->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        } catch (\Throwable $e) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Scoreboard data could not be loaded.']);
        }
    }

    public function create()
    {
        // abort_if(! $this->user->can('add_team'), 403);
        // abort_if(Gate::denies('add_team'), 403);

        $this->roles = Role::all();

        return view('service_company.team.create', $this->data);
    }

    /**
     * AJAX endpoint for Team Candidates tab data.
     * Intent: Paginated ES search & status filtering of candidates for target recruiter.
     * Tenancy: Scoped to auth user company and authorized target recruiter.
     */
    public function appdata_ajax(Request $request)
    {
        // Phase 0 Performance Telemetry: Baseline timing start
        $startTime = microtime(true);

        $userId = (int) ($request->input('userId') ?? $this->user->id);
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $mstatus_id = $feedback_status = '';

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $mstatus_id = $request->mstatus_id;
        // Intent: Cache master_status reference list with targeted column projection for high concurrency.
        // Tenancy: System reference table.
        $interviewStatus = Cache::remember('ref:master_status_list', 86400, function () {
            return DB::table('master_status')->select('id', 'name', 'css_style', 'priority')->orderBy('priority', 'ASC')->get();
        });

        $chkAdminRole = ($this->user->id == $userId && isset($this->user->root_user))
            ? (int) $this->user->root_user
            : Cache::remember("ref:user_root:{$userId}", 3600, function () use ($userId) {
                return DB::table('users')->where('id', $userId)->where('root_user', 1)->count();
            });
        $feedback_status = GeneralMethod::feedback_status();
        $company_id = auth()->user()->company_id;

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');
        $app_int_status = request('app_int_status');
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);
        $country = '';
        $sort = [];
        switch ($valCheck) {
            case 1:
                $sort[] = ['created_at' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['ctc' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 4:
                $sort[] = ['ctc' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5: // Notice period ASC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
											if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
												return 999999; // Put missing/Immediate at the end
											}
											try {
												// Extract first number from comma-separated values
												String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
												if (parts.length > 0) {
													return Double.parseDouble(parts[0].trim());
												}
											} catch (Exception e) {
												// If parsing fails
											}
											return 999999; // Invalid values at the end
										",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 6: // Notice period DESC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
											if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
												return -1; // Put missing/Immediate at the end
											}
											try {
												// Extract first number from comma-separated values
												String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
												if (parts.length > 0) {
													return Double.parseDouble(parts[0].trim());
												}
											} catch (Exception e) {
												// If parsing fails
											}
											return -1; // Invalid values at the end
										",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 21: // Notice period Date ASC
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
										if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
											return 999999999; // null/empty -> last
										}

										def raw = doc['np_id.keyword'].value;
										if (raw == null || raw.trim().length() == 0) { return 999999999; }
										def v = raw.trim();

										// Immediate should come first
										if (v.equals('Immediate')) { return -999999998; }

										// Handle multi-value
										int comma = v.indexOf(',');
										if (comma > -1) { v = v.substring(0, comma).trim(); }

										// Remove time
										int t = v.indexOf('T');
										if (t > -1) { v = v.substring(0, t); }
										v = v.replace('/', '-');

										try {
											def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
											return d.toEpochDay();
										} catch (Exception e1) {
											try {
												def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
												return d.toEpochDay();
											} catch (Exception e2) {
												try {
													def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
													return d.toEpochDay();
												} catch (Exception e3) {
													return 999999999; // not a date -> last
												}
											}
										}
									",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;

            case 22: // Notice period Date DESC
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
										if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
											return -999999999; // null/empty -> last
										}

										def raw = doc['np_id.keyword'].value;
										if (raw == null || raw.trim().length() == 0) { return -999999999; }
										def v = raw.trim();

										// Immediate should come after valid dates (but before null/non-date)
										if (v.equals('Immediate')) { return -888888888; }

										// Handle multi-value
										int comma = v.indexOf(',');
										if (comma > -1) { v = v.substring(0, comma).trim(); }

										// Remove time
										int t = v.indexOf('T');
										if (t > -1) { v = v.substring(0, t); }
										v = v.replace('/', '-');

										try {
											def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
											return d.toEpochDay();
										} catch (Exception e1) {
											try {
												def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
												return d.toEpochDay();
											} catch (Exception e2) {
												try {
													def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
													return d.toEpochDay();
												} catch (Exception e3) {
													return -999999999; // not a date -> last
												}
											}
										}
									",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9: // Immediate joiners first (original functionality)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        if ($valCheck == 22) {
            $today = date('Y-m-d');
            $next30 = date('Y-m-d', strtotime('+30 days'));

            // Serving candidates filter
            $servingCandidateFilter = [
                'script' => [
                    'script' => [
                        'lang' => 'painless',
                        'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
                        'params' => [
                            'today' => $today,
                            'next30' => $next30,
                        ],
                    ],
                ],
            ];
            // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['submitted_by' => $userId]],
                            ], $this->getSearchConditions($custom_search, $country)),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];
        // Status filter
        if (! empty($app_int_status) && (int) $app_int_status > 0) {
            $boolQuery['bool']['filter'][] = [
                'term' => [
                    'job_master_status_id' => (int) $app_int_status,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                '_source' => [
                    'id', 'wiseId', 'full_name', 'email', 'phone', 'current_designation', 'ctc', 'ectc',
                    'notice_period', 'np_id', 'created_at', 'jobId', 'title', 'client_name', 'posted_by',
                    'company_name', 'job_master_status_id', 'name', 'master_status_name', 'child_status_id',
                    'css_style', 'country', 'state', 'city', 'city_id', 'thirdparty_company_id',
                    'client_comapny_id', 'duplicate', 'is_duplicate', 'qualification_name', 'skills', 'total_exp', 'rel_exp',
                    'condidate_type', 'ctc_yearly', 'ectc_yearly', 'serving_notice_period_mode',
                    'current_company', 'current_ctc_mode', 'current_ctc_cur',
                ],

                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;

        $durMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'html' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team_candidate_ajax', compact('data', 'userPermissions', 'salaryMode', 'currencySymbole'))->render()),
            'total' => $total,
            'pagination' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.pagination', compact('pagination'))->render()),
            '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.appdata_ajax'],
        ])->header('Server-Timing', "team_appdata;dur={$durMs};desc=\"Team Candidate Data AJAX\"");
    }

    protected function getSearchConditions($custom_search, $country)
    {
        $conditions = [];

        // Apply custom search if it's not empty
        if (! empty($custom_search)) {
            $conditions[] = [
                'bool' => [
                    'should' => [
                        [
                            'match_phrase_prefix' => ['full_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['email' => $custom_search],
                        ],
                        [
                            'wildcard' => ['phone' => $custom_search],
                        ],
                        [
                            'match_phrase_prefix' => ['title' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['country_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'match_phrase_prefix' => ['qualification_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['state_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['city_name' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['posted_by' => '*'.strtolower($custom_search).'*'],
                        ],
                        [
                            'wildcard' => ['client_or_company_name' => '*'.strtolower($custom_search).'*'],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        return $conditions;

    }

    public function appdata(Request $request)
    {

        $userId = $mstatus_id = $feedback_status = '';
        $userId = $request->userId;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $mstatus_id = $request->mstatus_id;
        $interviewStatus = [];
        $interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();

        $chkAdminRole = DB::table('users')->select('*')->where('id', $userId)->where('root_user', 1)->count();
        $feedback_status = GeneralMethod::feedback_status();
        $company_id = auth()->user()->company_id;

        // 'application_status.status',
        $jobApplications = DB::table('job_applications')
            ->select(
                'job_applications.*', 'jobs.title', 'jobs.id as jobId', 'jobs.company_id as client_comapny_id', 'job_locations.location', 'clients.name as client_name', 'users.name as posted_by',
                'qualification_master.name as qualification_name', 'master_status.name', 'master_status.css_style', 'companies.company_name', 'add_job_wise_condidates.id as wiseId', 'state_list.state as state_name'
            )
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
                                            // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
                                            // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('companies', 'companies.id', 'jobs.company_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state');

        // dd($chkAdminRole,$userId);

        if ($chkAdminRole == 0) {
            $jobApplications = $jobApplications->join('job_assign', 'job_assign.job_id', 'jobs.id');
            $jobApplications = $jobApplications->where('job_assign.user_id', $userId);
        }

        //  if (\request('search') != "")
        // 	{
        // 		$search					=	request('search');
        // 		$jobApplications		=	$jobApplications->where('job_applications.full_name', 'LIKE', "%$search%");
        // 	}

        $jobApplications = $jobApplications->where('add_job_wise_condidates.submitted_by', '=', $userId);
        // $jobApplications 			=	$jobApplications->orWhere(function ($query) use ($company_id) {
        // 									return
        // 											$query->where('recommendation_relation.final_status', '=', 1)
        // 											->where('recommendation_relation.to_company_id', '=', $company_id)
        // 											->where('add_job_wise_condidates.thirdparty_company_id', '=', $company_id);
        // 								});
        $jobApplications = $jobApplications->groupBy('add_job_wise_condidates.id');
        $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.id', 'DESC');
        // dd($jobApplications->get());
        $dataTable = DataTables::of($jobApplications)
            ->editColumn('chkbox', function ($row) {

                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" />
						  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                if (! empty(@$row->full_name)) {
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0]);
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101) {
                        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                    } elseif ($str >= 102 && $str <= 106) {
                        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                    } elseif ($str >= 107 && $str <= 111) {
                        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                    } elseif ($str >= 112 && $str <= 116) {
                        $bgTextDecore = 'fs-3 bg-light-success text-success';
                    } elseif ($str >= 117 && $str <= 121) {
                        $bgTextDecore = 'fs-3 bg-dark text-secondary';
                    } else {
                        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                    }

                    $profile_sidebar_css = 'show-application-detail';
                    if ($row->thirdparty_company_id > 0) {
                        $profile_sidebar_css = 'client-show-application-detail';
                    }

                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);"   data-row-id="'.@$row->wiseId.'" class="'.$profile_sidebar_css.'">
                      <div class="symbol-label '.@$bgTextDecore.' border-radius-5">'.strtoupper(@$strVal).'</div>
                    </a>
                  </div>                  
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_sidebar_css.'" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 30)).'</a>
                    <a href="javascript:void(0);"  data-row-id="'.@$row->wiseId.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary '.$profile_sidebar_css.'" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 30)).'</a>
                  </div>';

                } else {
                    return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">&nbsp;</div>';
                }

            })
            ->editColumn('Contacts', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$row->phone.'</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.@$row->email.'">'.substr(@$row->email, 0, 7).'</a>
                  </div>';
            })
            ->editColumn('Exp', function ($row) {

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$row->total_exp.'+</a>
                    <span class="text-muted fw-bold d-block fs-7">'.@$row->rel_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $current_ctc_cur = '';
                $current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];

                $current_ectc_cur = '';
                $current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];

                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_cur = $currencySymbole[@$row->bill_rate_currency];
                }

                $ctc = '';
                $ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
                $ectc = '';
                $ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);
                if ($row->condidate_type == 'bench_condidate') {
                    $ectc = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
                }

                $current_ctc_mode = '';
                $current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
                $current_ctc_mode = substr($salaryMode[0], 0, 1);

                $current_ectc_mode = '';
                $current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);
                $current_ectc_mode = substr($salaryMode[0], 0, 1);
                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_mode = substr($salaryMode[@$row->bill_rate_mode], 0, 1);
                }
                $dnone = '';
                if ($row->condidate_type == 'bench_condidate') {
                    $dnone = 'd-none';
                }

                return '<div class="d-flex flex-column">
			<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$current_ectc_cur.@$ectc.'/'.$current_ectc_mode.'</a>
				<span class="text-muted fw-bold d-block fs-7 '.$dnone.'">'.@$current_ctc_cur.@$ctc.'/'.$current_ctc_mode.'</span>
			  </div>';
            })
            ->editColumn('NoticePeriod', function ($row) {
                $np = $row->np_id;
                if (empty(@$np)) {
                    $np = 'N/A';
                }
                if (@$row->serving_notice_period_mode == 0) {
                    if (! empty(@$row->np_id)) {
                        $np = $row->np_id.'  Days';
                    }
                } elseif (@$row->serving_notice_period_mode == 1) {

                    $np = $row->np_id;
                    if ($np != 'Immediate') {
                        $np = GeneralMethod::ymdTodmyNotTime(@$row->np_id); // date("d-M-Y",strtotime(@$row->np_id));
                    }

                } else {
                    $np = $row->np_id;
                }

                // $row->current_company = (@$row->current_company) ? @$row->apl_company_name : @$row->apl_company_name;
                return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.@$np.'</a>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.@$row->current_company.'">'.substr(@$row->current_company, 0, 20).'</span>
					  </div>';
            })
            ->editColumn('Qualifaction', function ($row) {
                return ' <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.ucwords(substr(@$row->qualification_name, 0, 10)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.strtoupper(@$row->skills).'">'.strtoupper(substr(@$row->skills, 0, 15)).'</span> 
                  </div>';
            })
            ->editColumn('Interview', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" />
						  </div>';
            })
            ->editColumn('Location', function ($row) {

                $country = '';
                $row_country_name = '';

                if (empty($row->country)) {
                    $row_country_name = '----';
                } else {
                    if (! empty(@$row->country)) {
                        if (is_numeric(@$row->country)) {
                            $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                            foreach ($country_name_Arr as $cval) {
                                $row_country_name = ucwords($cval['name']);
                            }
                        }
                    }
                }

                $state = '';
                $row_state_name = '';
                if (empty($row->state)) {
                    $row_state_name = '----';
                } else {
                    if (@$row->country == 101) {
                        $row_state_name = @$row->state_name;
                    } else {
                        $state = @$row->state;
                        if (is_numeric(@$row->country) && is_numeric($state)) {
                            if ($state > 0) {
                                $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                                foreach ($state_name_Arr as $sval) {
                                    $row_state_name = ucwords($sval['name']);
                                }
                            }
                        }

                    }

                }

                $city = '';
                $row_city_name = '';

                if (@$row->country == 101) {
                    // Intent: Eliminate N+1 DB lookup by pulling city name from cached in-memory map.
                    // Tenancy: Public reference table.
                    $cityListMap = Cache::remember('ref:city_list_map', 86400, function () {
                        return DB::table('city_list')->pluck('city', 'city_id')->toArray();
                    });
                    $row_city_name = $cityListMap[@$row->city_id] ?? '';
                } else {
                    $row_city_name = @$row->city;
                }

                return '<div class="d-flex flex-column">
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row_city_name).'">'.ucwords(substr($row_city_name, 0, 10)).'</a>
					<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row_state_name).' | '.strtoupper($row_country_name).'">'.ucfirst(substr($row_state_name, 0, 10)).' | '.strtoupper(substr($row_country_name, 0, 2)).'</span>
				</div>';

            })
            ->editColumn('Source', function ($row) {
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->posted_by);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                return ' <div class="d-flex flex-column">
                  				<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->posted_by.'">'.substr($row->posted_by, 0, 10).'</a>
                  			</div>
							<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.@$row->company_name.'">'.ucwords(substr(@$row->company_name, 0, 25)).'</span>';
            })
            ->editColumn('UploadDate', function ($row) {
                $UploadDate = $className = '';
                $UploadDate = @$row->created_at;
                $UploadDate = date('d M, Y', strtotime($UploadDate));
                $recommendation_text = 'Client';
                if ((@$row->client_comapny_id == auth()->user()->company_id)) {
                    $recommendation_text = 'Custom';
                }

                return '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.@$UploadDate.'</a>
			<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="">R'.$row->jobId.'-'.$recommendation_text.'</span> 
			 </div>';
            })
            ->editColumn('Requisitions', function ($row) {
                $jobtitle = $client_name = '';
                $jobtitle = ucwords(substr(@$row->title, 0, 10));
                $client_name = ucwords(substr(@$row->client_name, 0, 10));

                return '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($jobtitle).'">'.ucwords(substr($jobtitle, 0, 6)).'</a> 
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($client_name).'">'.ucwords(substr($client_name, 0, 6)).'</span>  
					</div>';
            })
            ->editColumn('Stage', function ($row) {

                $stage_default_css = 'stage_sub_stage';
                if ($row->thirdparty_company_id > 0) {
                    $stage_default_css = 'client_stage_sub_stage';
                }
                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }

                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    // Intent: Eliminate N+1 DB lookup by pulling child status name from cached in-memory map.
                    // Tenancy: System reference table.
                    $candidateStatusMap = Cache::remember('ref:candidate_status_map', 86400, function () {
                        return DB::table('candidate_status')->pluck('name', 'id')->toArray();
                    });
                    $name = $candidateStatusMap[$row->child_status_id] ?? '';
                }

                return '<div class="d-flex flex-column">
								<a href="javascript.void(0);" data-row-id="'.@$row->wiseId.'" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal" data-target="#'.$stage_default_css.'">
									<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.ucwords(substr($row->name, 0, 10)).'</span>
									<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
								</a>
							</div>';
            });

        $rawColumn = ['chkbox', 'Name', 'Contacts', 'Exp', 'Salary', 'NoticePeriod', 'Qualifaction', 'Interview', 'Location', 'Source', 'Stage', 'UploadDate', 'Requisitions'];
        $dataTable = $dataTable->rawColumns($rawColumn);
        $dataTable = $dataTable->addIndexColumn()->make(true);

        return $dataTable;
    }

    public function intdata_ajax(Request $request)
    {
        // Phase 0 Performance Telemetry: Baseline timing start
        $startTime = microtime(true);

        $userId = (int) ($request->input('userId') ?? $this->user->id);
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $available_status = $feedback_status = $interview_mode_arr = '';

        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();

        $searchQuery = $request->input('q');
        $searchTerm = trim(request('custom_search') ?? $request->input('q'));
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
        $endOfDay = Carbon::now()->endOfDay()->toDateTimeString();
        $valCheck_date = request('custom_hiringtype');
        $sort = [];
        $filter = [];

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['created_at' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['schedule_date' => ['order' => 'desc']];
                break;
            case 4:
                $sort[] = ['schedule_date' => ['order' => 'asc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // 2. Feedback pending first (nulls first)

        if ($valCheck == 'Feedback Pending') {
            // 1. Sort by feedback pending (feedback is null)
            array_unshift($sort, [
                '_script' => [
                    'type' => 'number',
                    'script' => [
                        'source' => "doc['feedback'].size() == 0 ? 0 : 1",
                        'lang' => 'painless',
                    ],
                    'order' => 'asc',
                ],
            ]);
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'lt' => now()->subHour()->toIso8601String(),
                        'format' => 'strict_date_optional_time',
                    ],
                ],
            ];
        }

        // Custom search logic
        if (! empty($searchTerm)) {
            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['phone' => $searchTerm]],
                                    ['term' => ['email' => $searchTerm]],
                                    ['match_phrase_prefix' => ['job_title' => $searchTerm]],
                                    ['match_phrase_prefix' => ['client_name' => $searchTerm]],
                                    ['match_phrase_prefix' => ['full_name' => $searchTerm]],
                                    ['wildcard' => ['city_name' => strtolower($searchTerm).'*']],
                                    ['wildcard' => ['country_name' => strtolower($searchTerm).'*']],
                                    ['wildcard' => ['state_name' => strtolower($searchTerm).'*']],
                                    ['wildcard' => ['posted_by' => strtolower($searchTerm).'*']],
                                    ['match_phrase_prefix' => ['company_name' => $searchTerm]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['submitted_by' => $userId]],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        //   $boolQuery = [
        // 	'bool' => [
        // 		'should' => [
        // 			// First condition
        // 			[
        // 				'bool' => [
        // 					'must' => array_merge([
        // 						['term' => ['submitted_by' => $userId]],
        // 					])
        // 				]
        // 			]
        // 		],
        // 		'minimum_should_match' => 1,
        // 		'filter' => [] // We'll add all filters here
        // 	]
        //   ];

        if ($valCheck_date == 1) {
            $filter[] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => Carbon::parse($startOfDay)->startOfDay()->toIso8601String(),
                        'lte' => Carbon::parse($endOfDay)->endOfDay()->toIso8601String(),
                        'format' => 'strict_date_optional_time', // optional based on your index mapping
                    ],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                '_source' => [
                    'id', 'wiseID', 'job_application_id', 'jobId', 'job_id', 'client_name', 'recommendation_id',
                    'to_company_id', 'thirdparty_company_id', 'full_name', 'current_designation', 'job_title', 'company_name',
                    'interview_mode', 'interview_mention_link', 'schedule_date', 'schedule_to_time',
                    'timezone_abbreviation', 'mode_name', 'mode_lavel_name', 'interviewers',
                    'current_company_id', 'feedback', 'master_status_id', 'master_status_name',
                    'css_style', 'child_status_id', 'child_status_name', 'posted_by', 'ClientComID',
                    'uc_company_name', 'rec_candidate_assign_id', 'average_star_rating',
                    'phone', 'email', 'city_name', 'country_name', 'state_name',
                ],
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        // dd($pagination);

        $userPermissions = $this->userPermissions;
        $company_id = $this->user->company_id;
        $chkAdminRole = $this->user->hasRole('admin');

        $durMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'html' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team-interview-tracker-ajax', compact('data', 'userPermissions', 'feedback_status', 'available_status', 'company_id', 'chkAdminRole'))->render()),
            'total' => $total,
            'pagination' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.pagination', compact('pagination'))->render()),
            '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.intdata_ajax'],
        ])->header('Server-Timing', "team_intdata;dur={$durMs};desc=\"Team Interview Data AJAX\"");
    }

    public function intdata(Request $request)
    {

        $userId = $available_status = $feedback_status = $interview_mode_arr = '';
        $userId = $request->userId;

        $available_status = GeneralMethod::available_status();
        $feedback_status = GeneralMethod::feedback_status();
        $interview_mode_arr = GeneralMethod::interview_mode();

        $shedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.status', 'interview_schedules.schedule_date', 'interview_schedules.available_status', 'interview_schedules.reschedule', 'interview_schedules.feedback', 'interview_schedules.interview_mode', 'interview_schedules.schedule_from_time', 'interview_schedules.interviewers', 'interview_schedules.interview_type', 'interview_schedules.job_application_id', 'interview_mode.name as mode_name', 'interview_mode_lavel.name as mode_lavel_name', 'interview_schedules.average_star_rating',
            'job_applications.current_designation', 'job_applications.company_id as current_company_id', 'job_applications.full_name', 'job_applications.thirdparty_company_id',
            'job_applications.master_status_id', 'job_applications.child_status_id',
            'users.name as source',
            'master_status.name as master_status_name',
            'candidate_status.name as child_status_name',
            'jobs.title as job_title', 'jobs.id as job_id',
            'companies.company_name',
            'master_status.css_style'
        )
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
        // ->leftjoin('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('companies', 'companies.id', 'jobs.company_id')
        // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage')
            ->leftjoin('interview_mode', 'interview_mode.id', 'interview_schedules.interview_mode')
            ->leftjoin('interview_mode_lavel', 'interview_mode_lavel.id', 'interview_schedules.interview_mode_lavel')
            ->leftjoin('candidate_status', 'candidate_status.id', 'interview_schedules.substage');
        $shedule = $shedule->where('add_job_wise_condidates.submitted_by', '=', @$userId);
        $shedule->orderBy('interview_schedules.schedule_date', 'DESC');
        $shedule->get();

        return DataTables::of($shedule)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->full_name);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 custom-show-application-detail" style="float:left" data-row-id="'.$row->job_application_id.'" >
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="custom-show-application-detail">
                      <div data-row-id="'.$row->job_application_id.'"  class="symbol-label '.$bgTextDecore.' border-radius-5 custom-show-application-detail">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->job_application_id.'" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 8)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 8)).'</span>
                  </div>';
            })
            ->editColumn('Requisitions', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->job_title).'">'.ucwords(substr($row->job_title, 0, 8)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->company_name).'">'.ucwords(substr($row->company_name, 0, 8)).'</span>
                  </div>';
            })
            ->editColumn('InterviewMode', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.ucwords($row->mode_name).'">'.ucwords(substr($row->mode_name, 0, 8)).'</a> 
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords($row->mode_lavel_name).'">'.ucwords(substr($row->mode_lavel_name, 0, 8)).'</span> 
                  </div>';
            })
            ->editColumn('Interviewers', function ($row) {

                $html = '';
                $interviewers = $row->interviewers;
                if (! empty($interviewers)) {
                    $interviewersArr = explode(',', $interviewers);
                    // Intent: Eliminate N+1 DB lookups by caching company users map in memory per request/company.
                    // Tenancy: Scoped to company_id.
                    $companyId = $row->current_company_id ?? auth()->user()->company_id;
                    $companyUsersMap = Cache::remember("ref:company_users_map_{$companyId}", 3600, function () use ($companyId) {
                        return DB::table('users')
                            ->where('company_id', $companyId)
                            ->get(['id', 'name', 'image'])
                            ->keyBy('name');
                    });
                    foreach ($interviewersArr as $intName) {
                        $intName = trim($intName);
                        if (isset($companyUsersMap[$intName])) {
                            $intVal = $companyUsersMap[$intName];
                            $name = $image = '';
                            $name = $intVal->name;
                            $image = $intVal->image;

                            $imagePath = '';
                            $imagePath = url('public/user-uploads/team_profile_img/'.@$image);
                            $html .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$name.'">
													<img alt="image" src="'.@$imagePath.'">
												</div>';
                        }
                    }
                }

                return ' <div class="d-flex flex-column"><div class="symbol-group symbol-hover">'.$html.'</div>';
            })

            ->editColumn('Source', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->source);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                return ' <div class="d-flex flex-column">
				 			 <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->source.'">'.substr($row->source, 0, 10).'</a>
				  
                  			</div>
                    		<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->company_name).'">'.ucwords(substr(@$row->company_name, 0, 25)).'</span>';
            })
            ->editColumn('InterviewDate', function ($row) {
                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.Carbon::parse($row->schedule_date)->format('d M, Y').'</a>
                    <span class="text-muted fw-bold d-block fs-7">'.Carbon::parse($row->schedule_date)->format('H:i a').'</span>
                  </div>';
            })
            ->editColumn('Feedback', function ($row) use ($feedback_status, $available_status) {

                $available_status_name = '';
                if ($row->available_status > 0) {
                    $available_status_name = @$available_status[@$row->available_status]['name'];
                } else {
                    $available_status_name = @$available_status[6]['name'];
                }

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 show-feedback" data-toggle="tooltip" title="'.ucwords(@$feedback_status[@$row->feedback]['name']).'">'.ucwords(substr(@$feedback_status[@$row->feedback]['name'], 0, 12)).'</a>
                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$available_status_name).'">'.ucwords(substr(@$available_status_name, 0, 12)).'</span>
                  </div>';
            })
            ->editColumn('Rating', function ($row) {
                $imagePath = $text = '';
                $average_star_rating = '';
                $average_star_rating = $row->average_star_rating;
                if ($average_star_rating == 1) {
                    $imagePath = url('public/img/star/1.png');
                    $text = 'Bad';
                } elseif ($average_star_rating == 2) {
                    $imagePath = url('public/img/star/2.png');
                    $text = 'Poor';
                } elseif ($average_star_rating == 3) {
                    $imagePath = url('public/img/star/3.png');
                    $text = 'Average';
                } elseif ($average_star_rating == 4) {
                    $imagePath = url('public/img/star/4.png');
                    $text = 'Best';
                } elseif ($average_star_rating == 5) {
                    $imagePath = url('public/img/star/5.png');
                    $text = 'Very Best';
                } else {
                    $imagePath = url('public/img/star/5-blank.png');
                    $text = 'Blank';
                }

                return ' <img src="'.str_replace('index.php', '', @$imagePath).'" alt="image" style="width: 5.5rem">
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$text.'</span>';
            })
            ->editColumn('Stage', function ($row) {

                $stage_default_css = 'interview_stage_sub_stage';
                if ($row->thirdparty_company_id > 0) {
                    $stage_default_css = 'interview_client_stage_sub_stage';
                }
                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    $candidateStatusMap = Cache::remember('ref:candidate_status_map', 86400, function () {
                        return DB::table('candidate_status')->pluck('name', 'id')->toArray();
                    });
                    $name = $candidateStatusMap[$row->child_status_id] ?? '';
                }

                return '<div class="d-flex flex-column">
					<a href="javascript.void(0);" data-row-id="'.@$row->job_application_id.'" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal">
						<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.substr(@$row->master_status_name, 0, 10).'</span>
						<span class="text-muted fw-bold d-block fs-7">'.@$name.'</span>
					</a>
				</div>';
            })
            ->rawColumns(['chkbox', 'Name', 'Requisitions', 'InterviewMode', 'Interviewers', 'Source', 'InterviewDate', 'Feedback', 'Rating', 'Stage'])
            ->make(true);
    }

    public function joiningdata_ajax(Request $request)
    {
        // Phase 0 Performance Telemetry: Baseline timing start
        $startTime = microtime(true);
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $job_status = GeneralMethod::job_status();
        $userId = (int) ($request->input('userId') ?? $this->user->id);
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $company_id = auth()->user()->company_id;

        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);
        $country = request('country');
        $valCheck = $request->input('sortingfilter');
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $sort = [];

        $sort = [];
        switch ($valCheck) {
            case 12:
                $sort[] = ['of_joined_date' => ['order' => 'desc']];
                break;
            case 13:
                $sort[] = ['of_joined_date' => ['order' => 'asc']];
                break;
            case 14:
                $sort[] = ['of_offer_amount' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 15:
                $sort[] = ['of_offer_amount' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 5: // Notice period ASC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return 999999; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return 999999; // Invalid values at the end
							",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            case 6: // Notice period DESC (ignore Immediate)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
								if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0 || doc['np_id.keyword'].value == 'Immediate') {
									return -1; // Put missing/Immediate at the end
								}
								try {
									// Extract first number from comma-separated values
									String[] parts = doc['np_id.keyword'].value.splitOnToken(',');
									if (parts.length > 0) {
										return Double.parseDouble(parts[0].trim());
									}
								} catch (Exception e) {
									// If parsing fails
								}
								return -1; // Invalid values at the end
							",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 21: // Notice period Date ASC
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return 999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return 999999999; }
							def v = raw.trim();

							// Immediate should come first
							if (v.equals('Immediate')) { return -999999998; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return 999999999; // not a date -> last
									}
								}
							}
						",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;

            case 22: // Notice period Date DESC
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) {
								return -999999999; // null/empty -> last
							}

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) { return -999999999; }
							def v = raw.trim();

							// Immediate should come after valid dates (but before null/non-date)
							if (v.equals('Immediate')) { return -888888888; }

							// Handle multi-value
							int comma = v.indexOf(',');
							if (comma > -1) { v = v.substring(0, comma).trim(); }

							// Remove time
							int t = v.indexOf('T');
							if (t > -1) { v = v.substring(0, t); }
							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								return d.toEpochDay();
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									return d.toEpochDay();
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										return d.toEpochDay();
									} catch (Exception e3) {
										return -999999999; // not a date -> last
									}
								}
							}
						",
                        ],
                        'order' => 'desc',
                    ],
                ];
                break;
            case 7:
                $sort[] = ['total_exp' => ['order' => 'desc', 'unmapped_type' => 'float']];
                break;
            case 8:
                $sort[] = ['total_exp' => ['order' => 'asc', 'unmapped_type' => 'float']];
                break;
            case 9: // Immediate joiners first (original functionality)
                $sort[] = [
                    '_script' => [
                        'type' => 'number',
                        'script' => [
                            'lang' => 'painless',
                            'source' => "params['_source']['immediate_join'] == 'Immediate' ? 0 : 1",
                        ],
                        'order' => 'asc',
                    ],
                ];
                break;
            default:
                $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
                break;
        }

        if ($valCheck == 22) {
            $today = date('Y-m-d');
            $next30 = date('Y-m-d', strtotime('+30 days'));

            // Serving candidates filter
            $servingCandidateFilter = [
                'script' => [
                    'script' => [
                        'lang' => 'painless',
                        'source' => "
							if (!doc.containsKey('np_id.keyword') || doc['np_id.keyword'].size() == 0) return false;

							def raw = doc['np_id.keyword'].value;
							if (raw == null || raw.trim().length() == 0) return false;

							def v = raw.trim();

							if (v.equals('Immediate')) return false; // exclude 'Immediate'

							int comma = v.indexOf(',');
							if (comma > -1) v = v.substring(0, comma).trim();

							int t = v.indexOf('T');
							if (t > -1) v = v.substring(0, t);

							v = v.replace('/', '-');

							try {
								def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ISO_LOCAL_DATE);
								long epoch = d.toEpochDay();
								long today = java.time.LocalDate.parse(params.today).toEpochDay();
								long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

								return epoch >= today && epoch <= next30;
							} catch (Exception e1) {
								try {
									def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('d-M-uuuu'));
									long epoch = d.toEpochDay();
									long today = java.time.LocalDate.parse(params.today).toEpochDay();
									long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

									return epoch >= today && epoch <= next30;
								} catch (Exception e2) {
									try {
										def d = java.time.LocalDate.parse(v, java.time.format.DateTimeFormatter.ofPattern('uuuu-M-d'));
										long epoch = d.toEpochDay();
										long today = java.time.LocalDate.parse(params.today).toEpochDay();
										long next30 = java.time.LocalDate.parse(params.next30).toEpochDay();

										return epoch >= today && epoch <= next30;
									} catch (Exception e3) {
										return false; // not a valid date
									}
								}
							}
						",
                        'params' => [
                            'today' => $today,
                            'next30' => $next30,
                        ],
                    ],
                ],
            ];
            // Attach serving candidate condition along with others
            $boolQuery['bool']['filter'][] = $servingCandidateFilter;
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['submitted_by' => $userId]],
                                ['term' => ['job_master_status_id' => 6]],
                            ], $this->getSearchConditions($custom_search, $country)),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        if (\request('country') != '') {
            $boolQuery['bool']['filter'][] = [
                'terms' => [
                    'country' => [$country],
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                '_source' => [
                    'id', 'wiseId', 'full_name', 'email', 'phone', 'current_designation', 'ctc', 'ectc',
                    'notice_period', 'np_id', 'created_at', 'jobId', 'title', 'client_name', 'posted_by',
                    'company_name', 'job_master_status_id', 'name', 'master_status_id', 'master_status_name', 'child_status_id',
                    'css_style', 'country', 'state', 'city', 'city_id', 'thirdparty_company_id',
                    'client_comapny_id', 'of_joined_date', 'of_offer_amount', 'of_currency', 'of_mode', 'employment_type',

                ],
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;

        $durMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'html' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team-candidate-join-ajax', compact('data', 'userPermissions', 'company_id', 'currency', 'salaryMode', 'job_status'))->render()),
            'total' => $total,
            'pagination' => $this->sanitizeHtmlPayload(view('service_company.ajax_pages.pagination', compact('pagination'))->render()),
            '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.joiningdata_ajax'],
        ])->header('Server-Timing', "team_joiningdata;dur={$durMs};desc=\"Team Joining Data AJAX\"");
    }

    public function joiningdata(Request $request)
    {
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $userId = '';
        $job_status = GeneralMethod::job_status();
        $userId = $request->userId;
        $company_id = auth()->user()->company_id;

        $jobApplications = JobApplication::select('job_applications.id', 'job_applications.full_name', 'jobs.title', 'job_applications.current_designation', 'job_applications.ctc',
            'job_applications.ectc', 'offer_job_applications.joined_date', 'offer_job_applications.offer_currency', 'offer_job_applications.offer_mode', 'offer_job_applications.offer_amount',
            'master_status.name as master_status_name', 'job_applications.phone', 'job_applications.email', 'users.name as source', 'master_status.css_style', 'companies.company_name', 'job_company.company_name as job_company_name',
            'job_applications.thirdparty_company_id', 'job_applications.master_status_id', 'job_applications.child_status_id', 'jobs.id as jobID', 'add_job_wise_condidates.id as wiseId', 'jobs.company_id as comId', 'jobs.status as job_status', 'jobs.employment_type', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id', 'clients.name as clients_name');
        $jobApplications = $jobApplications->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            });
        $jobApplications = $jobApplications->join('jobs', function ($join) {
            $join->on('jobs.id', 'add_job_wise_condidates.job_id');
        });
        $jobApplications = $jobApplications->join('companies', function ($join) {
            $join->on('companies.id', 'add_job_wise_condidates.company_id');
        })
            ->join('offer_job_applications', function ($join) {
                $join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
            })
                            // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->join('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('companies as job_company', 'job_company.id', 'jobs.company_id')
                            // ->join('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('add_job_wise_condidates.master_status_id', 6)
            ->where('add_job_wise_condidates.submitted_by', $userId)
            ->groupBy('add_job_wise_condidates.id');

        // dd($jobApplications->get());

        return DataTables::of($jobApplications)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';
            })
            ->editColumn('Name', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->full_name);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="custom-show-application-detail">
                      <div class="symbol-label fs-3 '.$bgTextDecore.' text-warning border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->full_name).'">'.ucwords(substr(@$row->full_name, 0, 20)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'"  class="text-muted fw-bold d-block fs-7 text-hover-primary custom-show-application-detail" data-toggle="tooltip" title="'.ucwords(@$row->current_designation).'">'.ucwords(substr(@$row->current_designation, 0, 20)).'</a>
                  </div>';
            })
            ->editColumn('Contact', function ($row) use ($company_id, $job_status) {

                $recommendation_text = 'Client';
                if ($row->comId == $company_id) {
                    $recommendation_text = 'Custom';
                }

                $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                $status = ucwords($row->job_status);
                $class = $job_status[$row->job_status];
                $recommendation_id = $row->recommendation_id;
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                $final_status = '';
                if (($row->final_status == 2 || $row->final_status == 3) && @$row->to_company_id == auth()->user()->company_id) {
                    $status = 'Removed';
                    $class = 'closed-bg';
                }

                $ulink = '<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.@$class.'  text-hover-success" data-toggle="modal" data-target="">'.$status.'</a>';

                $recommendation_text = 'Custom';
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $recommendation_text = 'Client';
                    $ulink = '<a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>';
                }

                // dd($recommendation_text);

                return '<div class="d-flex flex-column">
                   '.$ulink.'  
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="R'.$row->jobID.'-'.$recommendation_text.'">R'.$row->jobID.'-'.$recommendation_text.'</a>
                  </div>';
            })
            ->editColumn('Requisitions', function ($row) {

                $jobtitle = $row->title;
                $jobtitle = substr($jobtitle, 0, 15);
                $client_name = $row->clients_name;
                $client_name = substr($client_name, 0, 15);

                $recommendation_id = $row->recommendation_id;
                if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $client_name = ucwords(substr(@$row->company_name, 0, 30));
                }

                // title="'.$row->client_name.'">'.$client_name.'</a>
                // title="'.$row->job_company_name.'">'.$row->job_company_name.'</a>

                return '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="'.$row->title.'">'.$jobtitle.'</a> 
                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.$row->client_name.'">'.$client_name.'</a>
                  </div>';
            })

            ->editColumn('Source', function ($row) {

                $bgTextDecore = $strVal = '';
                $str = trim(@$row->source);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                return '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="'.@$row->source.'">'.substr($row->source, 0, 10).'</a>
							
						</div>
						<span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="'.ucwords(@$row->company_name).'">'.ucwords(substr(@$row->company_name, 0, 25)).'</span>';
            })
            ->editColumn('Offer', function ($row) use ($currency, $salaryMode) {

                $offer_currency = $offer_mode = '';
                // if(!empty($row->offer_currency)){
                $offer_currency = $currency[(@$row->offer_currency) ? $row->offer_currency : 0];
                // }

                // if(!empty($row->offer_mode)){
                $offer_mode = $salaryMode[(@$row->offer_mode) ? $row->offer_mode : 0];
                // }

                // dd("dkj");

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($row->offer_amount > 0) {
                    return '<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">'.$offer_currency.' '.$row->offer_amount.'/'.substr($offer_mode, 0, 1).'</span>
						<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="'.$employment_type.'">'.$employment_type.'</a>
					</div>';
                } else {
                    return '<div class="d-flex flex-column">
                         <span class="text-dark fw-bolder fs-6">'.$employment_type.'</span>
                     </div>';
                }

            })->editColumn('Joining', function ($row) {

                $joined_date = $joined_time = $date = '';
                $date = @$row->joined_date;
                if (! empty($date)) {
                    $joined_date = date('d-M-y', strtotime($date));
                    $joined_time = date('H:i a', strtotime($date));
                }

                return '<div class="d-flex flex-column">
							 <span class="text-dark fw-bolder fs-6">'.$joined_date.'</span>
							 <span class="text-muted fw-bold d-block fs-7">'.$joined_time.'</span>
						 </div>';
            })
            /*->editColumn('Stage', function ($row) {
                return '<div class="d-flex flex-column">
                        <span class="fw-bolder joined-bg fs-6 px-2">Joined</span>
                    </div>';
            })*/
            ->editColumn('Stage', function ($row) {

                $stage_default_css = 'joining_stage_sub_stage';
                if ($row->thirdparty_company_id > 0) {
                    $stage_default_css = 'joining_client_stage_sub_stage';
                }
                $name = '';
                if ($row->master_status_id == 8) {
                    $name = '';
                }
                if (! empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                    $candidateStatusMap = Cache::remember('ref:candidate_status_map', 86400, function () {
                        return DB::table('candidate_status')->pluck('name', 'id')->toArray();
                    });
                    $name = $candidateStatusMap[$row->child_status_id] ?? '';
                }

                return '<div class="d-flex flex-column">
					<a href="javascript.void(0);" data-row-id="'.@$row->id.'" class="d-flex flex-column '.$stage_default_css.'" data-toggle="modal">
						<span class="badge fw-bolder fs-6 '.@$row->css_style.' px-2 py-1">'.substr(@$row->master_status_name, 0, 10).'</span>
						<span class="text-muted fw-bold d-block fs-7">'.$name.'</span>
					</a>
				</div>';
            })
            ->rawColumns(['chkbox', 'Name', 'Contact', 'Requisitions', 'Source',  'Offer', 'Joining', 'Stage'])
            // ->rawColumns(['full_name', 'title', 'current_designation', 'ctc', 'ectc', 'name', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function resetpassword(Request $request)
    {
        // dd($request->all());

        $user = Auth::user();

        // dd($user);

        // Check if current password matches
        if (! Hash::check($request->currentpassword, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password does not match',
            ]);
        }

        // Update the password
        $user->super_password = $request->mteamcpassword;
        $user->password = bcrypt($request->mteamcpassword);
        $user->save();
        GeneralMethod::userIndexingupload($user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Password successfully changed',
        ]);
    }

    /**
     * Sanitizes rendered Blade HTML string buffer to minimize JSON payload byte size over network.
     * Intent: Strips redundant whitespace/newlines without breaking HTML formatting.
     */
    protected function sanitizeHtmlPayload(string $html): string
    {
        return preg_replace(['/>\s+</s', '/\s+/'], ['><', ' '], trim($html));
    }

    /**
     * Guard helper ensuring target user belongs to authenticated user's company.
     * Intent: Enforces strict multi-tenant isolation against parameter tampering.
     */
    protected function authorizeTeamMemberAccess(int $targetUserId): User
    {
        if ($targetUserId <= 0) {
            $targetUserId = auth()->id();
        }

        // Intent: Authorize team member access with minimal column selection for optimal query performance.
        // Tenancy: Strict tenant isolation via company() scope.
        $targetUser = User::select('id', 'company_id', 'name', 'email', 'root_user')
            ->where('id', $targetUserId)
            ->company()
            ->first();

        if (! $targetUser) {
            $targetUser = auth()->user();
        }

        return $targetUser;
    }

    /**
     * AJAX endpoint for My Rank tab data.
     * Intent: Computes monthly rank history and leaderboard stats on demand.
     * Tenancy: Scoped to auth user company and authorized target team member.
     */
    public function rankdata_ajax(Request $request)
    {
        $startTime = microtime(true);
        $userId = (int) ($request->input('userId') ?? $request->input('id'));
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $company_id = auth()->user()->company_id;

        $cacheKey = "ats:teamrank:comp_{$company_id}:user_{$userId}";
        if (Cache::has($cacheKey)) {
            $cachedPayload = Cache::get($cacheKey);
            $durMs = round((microtime(true) - $startTime) * 1000, 2);
            $cachedPayload['_telemetry']['dur_ms'] = $durMs;
            $cachedPayload['_telemetry']['cache_hit'] = true;

            return response()->json($cachedPayload)
                ->header('Server-Timing', "team_rankdata;dur={$durMs};desc=\"Team Rank Data AJAX (Redis Cache Hit)\"");
        }

        $lockKey = "lock:{$cacheKey}";
        $lock = Cache::lock($lockKey, 10);

        try {
            if ($lock->get()) {
                // Acquired lock, proceed with computation
            } elseif ($lock->block(5)) {
                // Waited for concurrent worker to populate cache
                if (Cache::has($cacheKey)) {
                    $cachedPayload = Cache::get($cacheKey);
                    $durMs = round((microtime(true) - $startTime) * 1000, 2);
                    $cachedPayload['_telemetry']['dur_ms'] = $durMs;
                    $cachedPayload['_telemetry']['cache_hit'] = true;

                    return response()->json($cachedPayload)
                        ->header('Server-Timing', "team_rankdata;dur={$durMs};desc=\"Team Rank Data AJAX (Redis Cache Hit post-lock)\"");
                }
            }

            $months = collect();
            $reportMonth = Carbon::now()->startOfMonth()->subMonth();
            $currentMonth = $reportMonth->copy()->format('Y-m');
            $startYear = 2025;
            $currentYear = Carbon::now()->year;

            for ($year = $startYear; $year <= $currentYear; $year++) {
                for ($i = 1; $i <= 12; $i++) {
                    $month = Carbon::create($year, $i, 1)->format('Y-m');
                    if ($month > $currentMonth) {
                        break 2;
                    }
                    $months->push($month);
                }
            }
            $months = $months->sortDesc()->values();
            $internalRankedUsers = [];
            $globalRankedUsers = [];

            foreach ($months as $month) {
                $reportMonth = Carbon::parse($month.'-01');
                $rankstartOfMonthForReport = Carbon::parse($reportMonth->copy()->startOfMonth()->toDateTimeString())->toIso8601String();
                $rankendOfMonthForReport = Carbon::parse($reportMonth->copy()->endOfMonth()->toDateTimeString())->toIso8601String();

                $internalScore = $this->getRankData($company_id, $userId, $rankstartOfMonthForReport, $rankendOfMonthForReport, true);
                $internalRankedUsers[$month] = $internalScore;

                $globalScore = $this->getRankData(null, $userId, $rankstartOfMonthForReport, $rankendOfMonthForReport, false);
                $globalRankedUsers[$month] = $globalScore;
            }

            $rankings = [
                'months' => $months,
                'internalRanks' => $internalRankedUsers,
                'globalRanks' => $globalRankedUsers,
            ];

            // Overall Candidate Uploads Count
            $total_candidates = 0;
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $candResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $company_id]],
                                    ['term' => ['submitted_by' => $userId]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $total_candidates = $candResp['hits']['total']['value'] ?? 0;
            }

            // Overall Interviews Count
            $interviewsTrackerTotal = 0;
            if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
                $ivCntResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    ['term' => ['submitted_by' => $userId]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $interviewsTrackerTotal = $ivCntResp['hits']['total']['value'] ?? 0;
            }

            // Overall Offers Count
            $scpOffersTotal = 0;
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                try {
                    $ofCntResp = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_CANDIDATES,
                        'request_cache' => true,
                        'body' => [
                            'size' => 0,
                            'track_total_hits' => true,
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['submitted_by' => $userId]],
                                        ['exists' => ['field' => 'of_created_date']],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $scpOffersTotal = $ofCntResp['hits']['total']['value'] ?? 0;
                } catch (\Throwable $e) {
                    $scpOffersTotal = 0;
                }
            }

            // Overall Joinings Count
            $total_candidates_join = 0;
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $joinCntResp = $this->elasticService->getClient()->search([
                    'index' => ELASTIC_CANDIDATES,
                    'request_cache' => true,
                    'body' => [
                        'size' => 0,
                        'track_total_hits' => true,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $company_id]],
                                    ['term' => ['submitted_by' => $userId]],
                                    ['term' => ['job_master_status_id' => 6]],
                                ],
                            ],
                        ],
                    ],
                ]);
                $total_candidates_join = $joinCntResp['hits']['total']['value'] ?? 0;
            }

            $users = $targetUser;
            $durMs = round((microtime(true) - $startTime) * 1000, 2);

            $html = $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team-rank-ajax', compact(
                'rankings', 'users', 'userId', 'company_id',
                'total_candidates', 'interviewsTrackerTotal', 'scpOffersTotal', 'total_candidates_join'
            ))->render());

            $responseData = [
                'html' => $html,
                '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.rankdata_ajax', 'cache_hit' => false],
            ];
            Cache::put($cacheKey, $responseData, now()->addMinutes(10));

            return response()->json($responseData)
                ->header('Server-Timing', "team_rankdata;dur={$durMs};desc=\"Team Rank Data AJAX\"");
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * AJAX endpoint for My Dashboard tab data.
     * Intent: Computes recruiter performance dashboard stats (active/hold/closed jobs, candidate status funnel breakdown, 7-day upload trend, 6-month offer, joining, and interview trends) on demand.
     * Tenancy: Scoped to auth user company and authorized target team member.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboarddata_ajax(Request $request)
    {
        $startTime = microtime(true);
        $userId = (int) ($request->input('userId') ?? $request->input('id'));
        $targetUser = $this->authorizeTeamMemberAccess($userId);
        $company_id = auth()->user()->company_id;

        $cacheKey = "ats:teamdashboard:comp_{$company_id}:user_{$userId}";
        if (Cache::has($cacheKey)) {
            $cachedPayload = Cache::get($cacheKey);
            $durMs = round((microtime(true) - $startTime) * 1000, 2);
            $cachedPayload['_telemetry']['dur_ms'] = $durMs;
            $cachedPayload['_telemetry']['cache_hit'] = true;

            return response()->json($cachedPayload)
                ->header('Server-Timing', "team_dashboarddata;dur={$durMs};desc=\"Team Dashboard Data AJAX (Redis Cache Hit)\"");
        }

        $lockKey = "lock:{$cacheKey}";
        $lock = Cache::lock($lockKey, 10);

        try {
            if ($lock->get()) {
                // Acquired lock, proceed with computation
            } elseif ($lock->block(5)) {
                if (Cache::has($cacheKey)) {
                    $cachedPayload = Cache::get($cacheKey);
                    $durMs = round((microtime(true) - $startTime) * 1000, 2);
                    $cachedPayload['_telemetry']['dur_ms'] = $durMs;
                    $cachedPayload['_telemetry']['cache_hit'] = true;

                    return response()->json($cachedPayload)
                        ->header('Server-Timing', "team_dashboarddata;dur={$durMs};desc=\"Team Dashboard Data AJAX (Redis Cache Hit post-lock)\"");
                }
            }

            // Intent: Fetch recruiter-scoped KPI metrics & pipeline status counts using ServiceCompanyProfileService.
            // Tenancy: Filtered by company_id and target team member user ID.
            $profileService = app(\App\Services\ServiceCompanyProfileService::class);
            $kpis = $profileService->getKpiMetrics($userId, $company_id);

            $scp_active = (int) ($kpis['counts']->get('active', 0));
            $counts = $kpis['counts'];

            $total_candidates = (int) ($kpis['scpCandidatesTotal'] ?? 0);
            $interviewsTrackerTotal = (int) ($kpis['scpInterviewsTotal'] ?? 0);
            $joined_count = (int) ($kpis['scpJoiningsTotal'] ?? $kpis['joined_count'] ?? 0);
            $scpOffersTotal = (int) ($kpis['scpOffersTotal'] ?? $kpis['offer_count'] ?? 0);

            $sourcing_count = (int) ($kpis['sourcing_count'] ?? 0);
            $screening_count = (int) ($kpis['screening_count'] ?? 0);
            $evaluation_count = (int) ($kpis['evaluation_count'] ?? 0);
            $negotiation_count = (int) ($kpis['negotiation_count'] ?? 0);
            $offer_count = (int) ($kpis['offer_count'] ?? 0);
            $Rejected_count = (int) ($kpis['Rejected_count'] ?? 0);
            $inreview_count = (int) ($kpis['inreview_count'] ?? 0);

            // Intent: Fetch candidate performance & offer/interview trend graph metrics using ServiceCompanyProfileService
            // Tenancy: Filtered by company_id and recruiter user_id
            $graphMetrics = $profileService->getGraphData($userId, $company_id);

            $labels = $graphMetrics['labels'] ?? [];
            $candidateValues = $graphMetrics['candidateValues'] ?? [];
            $labelsCommoffer = $graphMetrics['labelsCommoffer'] ?? [];
            $candidateValuesCommoffer = $graphMetrics['candidateValuesCommoffer'] ?? [];
            $labelsCommjoin = $graphMetrics['labelsCommjoin'] ?? [];
            $candidateValuesCommjoin = $graphMetrics['candidateValuesCommjoin'] ?? [];
            $interviewlabelsMonth = $graphMetrics['interviewlabelsMonth'] ?? [];
            $interviewValuesMonth = $graphMetrics['interviewValuesMonth'] ?? [];

            $durMs = round((microtime(true) - $startTime) * 1000, 2);

            $html = $this->sanitizeHtmlPayload(view('service_company.ajax_pages.team-dashboard-ajax', compact(
                'targetUser', 'userId', 'company_id',
                'counts', 'scp_active', 'total_candidates', 'scpOffersTotal', 'joined_count',
                'interviewsTrackerTotal', 'inreview_count', 'sourcing_count', 'screening_count',
                'evaluation_count', 'negotiation_count', 'offer_count', 'Rejected_count',
                'labels', 'candidateValues', 'labelsCommoffer', 'candidateValuesCommoffer',
                'labelsCommjoin', 'candidateValuesCommjoin', 'interviewlabelsMonth', 'interviewValuesMonth'
            ))->render());

            $responseData = [
                'html' => $html,
                '_telemetry' => ['dur_ms' => $durMs, 'endpoint' => 'team.dashboarddata_ajax', 'cache_hit' => false],
            ];
            Cache::put($cacheKey, $responseData, now()->addMinutes(5));

            return response()->json($responseData)
                ->header('Server-Timing', "team_dashboarddata;dur={$durMs};desc=\"Team Dashboard Data AJAX\"");
        } finally {
            optional($lock)->release();
        }
    }
}
