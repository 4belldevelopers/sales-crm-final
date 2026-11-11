<?php

namespace App\Services\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Service: CompanyActivityReadService
 *
 * Dedicated CQRS Read Service for Company Users Activity & Timeline on Company Profile.
 * Aggregates authoritative company-wide events across:
 * 1. ats_jobs_elastic (Requisitions created/updated by hiring managers & team)
 * 2. ats_candidates_elastic (Candidate applications & submissions)
 * 3. ats_candidate_timeline (Stage transitions, pipeline updates, notes)
 * 4. ats_interview_tracker_elastic (Interviews scheduled and updated)
 * 5. team_invitations (Colleagues invited & access changes)
 *
 * Tenancy: Strictly isolated by $companyId.
 * Complies with Rule 1 (0% MySQL writes/heavy queries on read path), Rule 2, and Rule 20.
 */
class CompanyActivityReadService
{
    protected ElasticService $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Resolves paginated, filtered company activity stream with KPI counts.
     */
    public function getActivityStream(int $companyId, array $filters = []): array
    {
        $category = $filters['category'] ?? 'all';
        $timeframe = $filters['timeframe'] ?? 'all';
        $search = trim($filters['search'] ?? '');
        $page = max((int) ($filters['page'] ?? 1), 1);
        $perPage = max((int) ($filters['per_page'] ?? 15), 5);

        // Retrieve or aggregate all authoritative company activities
        $allActivities = $this->resolveAllCompanyActivities($companyId);

        // Calculate KPI category counts before filtering
        $kpiCounts = [
            'all' => count($allActivities),
            'pipeline' => 0,
            'interviews' => 0,
            'offers' => 0,
            'jobs' => 0,
            'team' => 0,
        ];

        foreach ($allActivities as $act) {
            $cat = $act['category'] ?? 'pipeline';
            if (isset($kpiCounts[$cat])) {
                $kpiCounts[$cat]++;
            }
        }

        // Apply Timeframe Filter
        $filtered = $allActivities;
        if ($timeframe !== 'all') {
            $now = time();
            $cutoff = match ($timeframe) {
                'today' => strtotime('today midnight'),
                '7d' => $now - (7 * 86400),
                '30d' => $now - (30 * 86400),
                default => 0,
            };

            if ($cutoff > 0) {
                $filtered = array_values(array_filter($filtered, function ($item) use ($cutoff) {
                    return ($item['timestamp'] ?? 0) >= $cutoff;
                }));
            }
        }

        // Apply Category Filter
        if ($category !== 'all') {
            $filtered = array_values(array_filter($filtered, function ($item) use ($category) {
                return ($item['category'] ?? '') === $category;
            }));
        }

        // Apply Search Filter (300ms debounce in UI)
        if ($search !== '') {
            $searchLower = strtolower($search);
            $filtered = array_values(array_filter($filtered, function ($item) use ($searchLower) {
                $candName = strtolower($item['candidate']['name'] ?? '');
                $jobTitle = strtolower($item['job']['title'] ?? '');
                $jobCode = strtolower($item['job']['code'] ?? '');
                $actorName = strtolower($item['actor']['name'] ?? '');
                $companyName = strtolower($item['actor']['company_name'] ?? '');
                $action = strtolower($item['activity_label'] ?? '');
                $desc = strtolower($item['description'] ?? '');

                return
                    str_contains($candName, $searchLower) ||
                    str_contains($jobTitle, $searchLower) ||
                    str_contains($jobCode, $searchLower) ||
                    str_contains($actorName, $searchLower) ||
                    str_contains($companyName, $searchLower) ||
                    str_contains($action, $searchLower) ||
                    str_contains($desc, $searchLower);
            }));
        }

        $totalFiltered = count($filtered);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($filtered, $offset, $perPage);
        $hasMore = ($offset + $perPage) < $totalFiltered;

        return [
            'items' => $items,
            'kpis' => $kpiCounts,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalFiltered,
                'has_more' => $hasMore,
                'next_page' => $hasMore ? $page + 1 : null,
            ],
            'health' => [
                'score' => count($allActivities) > 0 ? 100 : 0,
                'label' => count($allActivities) > 0 ? 'Active' : 'No Activity',
                'last_activity_at' => ! empty($allActivities[0]['formatted_date']) ? $allActivities[0]['formatted_date'] : null,
            ],
        ];
    }

    /**
     * Aggregates authoritative company activities from Elasticsearch + DB audit trails.
     * Cached with a 60-second TTL to balance freshness and low latency.
     */
    public function resolveAllCompanyActivities(int $companyId): array
    {
        $cacheKey = "company_activity_all:{$companyId}";

        return Cache::remember($cacheKey, 60, function () use ($companyId) {
            $activities = [];

            // Resolve company details
            $company = DB::table('companies')->where('id', $companyId)->first();
            $defaultCompanyName = $company->company_name ?? ($company->name ?? 'Company');
            $defaultCompanyLogo = $company->image ?? '';

            // Tenant Timezone
            $tenantTz = GeneralMethod::defaultTimeZone($companyId) ?: 'Asia/Kolkata';

            // 1. Resolve company user dictionary for actor attribution
            $companyUsers = DB::table('users')
                ->where('company_id', $companyId)
                ->select('id', 'name', 'email', 'image')
                ->get()
                ->keyBy('id');

            // 2. Query ats_jobs_elastic (Requisitions created and updated)
            $jobIndex = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';
            if ($this->elasticService->indexExists($jobIndex)) {
                try {
                    $res = $this->elasticService->getClient()->search([
                        'index' => $jobIndex,
                        'size' => 100,
                        'body' => [
                            'query' => [
                                'term' => ['comId' => (int) $companyId],
                            ],
                            'sort' => [['created_at' => ['order' => 'desc']]],
                        ],
                    ]);

                    foreach ($res['hits']['hits'] ?? [] as $hit) {
                        $src = $hit['_source'];
                        $rawTime = $src['created_at'] ?? $src['updated_at'] ?? null;
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();
                        $status = strtolower($src['status'] ?? 'active');
                        $openings = (int) ($src['total_positions'] ?? 1);
                        $jobTitle = $src['title'] ?? 'Requisition';
                        $jobId = $src['id'] ?? null;

                        // Resolve actor from job_assign or spok_user
                        $actorName = 'Hiring Manager';
                        $actorImage = null;
                        if (! empty($src['job_assign']) && is_array($src['job_assign']) && ! empty($src['job_assign'][0]['user_name'])) {
                            $actorName = $src['job_assign'][0]['user_name'];
                            $actorId = $src['job_assign'][0]['user_id'] ?? null;
                            if ($actorId && $u = $companyUsers->get($actorId)) {
                                $actorImage = $u->image;
                            }
                        }

                        $locationStr = ! empty($src['client_location'][0]['address'])
                            ? ucwords(trim($src['client_location'][0]['address']))
                            : 'Remote';

                        $dt = Carbon::createFromTimestamp($timestamp)->setTimezone($tenantTz);
                        $timeAgo = GeneralMethod::timeAgo(date('Y-m-d H:i:s', $timestamp));

                        $activities[] = [
                            'id' => 'job_'.($jobId ?? uniqid()),
                            'category' => 'jobs',
                            'event_type' => 'job.created',
                            'timestamp' => $timestamp,
                            'occurred_at' => $dt->toIso8601String(),
                            'formatted_date' => $dt->format('M d, Y'),
                            'time_formatted' => $dt->format('h:i A T'),
                            'time_ago' => $timeAgo,
                            'sequence' => $timestamp,
                            'activity_label' => 'Job Requisition Created',
                            'description' => "Published requisition with {$openings} opening".($openings > 1 ? 's' : '').' (Status: '.ucfirst($status).')',
                            'status_text' => ucfirst($status).' Requisition',
                            'status_class' => ($status === 'active' ? 'status-active' : 'status-hold'),
                            'job' => [
                                'id' => $jobId,
                                'code' => '#'.($jobId ?? ''),
                                'title' => ucwords($jobTitle),
                                'status' => $status,
                                'total_positions' => $openings,
                                'company_name' => $src['client_or_company_name'] ?? ($src['company_name'] ?? $defaultCompanyName),
                                'company_logo' => $src['logo'] ?? $defaultCompanyLogo,
                                'location' => $locationStr,
                                'job_type' => $src['work_shifts'] ?? 'Day Shift',
                                'shared_by_name' => null,
                                'shared_by_company' => null,
                            ],
                            'candidate' => null,
                            'actor' => [
                                'name' => ucwords($actorName),
                                'company_name' => $defaultCompanyName,
                                'image' => $actorImage,
                                'initials' => $this->getInitials($actorName),
                            ],
                            'attribution' => [
                                'label' => 'Created by',
                                'name' => ucwords($actorName),
                                'company_name' => $defaultCompanyName,
                            ],
                            'transition' => null,
                            'link' => $jobId ? url('service_company/jobs/'.$jobId) : null,
                            'link_label' => 'View Requisition',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 3. Query ats_candidates_elastic (Candidate Submissions / Applications)
            $candIndex = defined('ELASTIC_CANDIDATE') ? ELASTIC_CANDIDATE : 'ats_candidates_elastic';
            if ($this->elasticService->indexExists($candIndex)) {
                try {
                    $res = $this->elasticService->getClient()->search([
                        'index' => $candIndex,
                        'size' => 100,
                        'body' => [
                            'query' => [
                                'term' => ['company_id' => (int) $companyId],
                            ],
                            'sort' => [['created_at' => ['order' => 'desc']]],
                        ],
                    ]);

                    foreach ($res['hits']['hits'] ?? [] as $hit) {
                        $src = $hit['_source'];
                        $rawTime = $src['created_at'] ?? $src['updated_at'] ?? null;
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();
                        $candName = ucwords($src['full_name'] ?? 'Candidate');
                        $candId = $src['id'] ?? null;
                        $wiseId = $src['wiseId'] ?? $candId;
                        $desig = $src['current_designation'] ?? ($src['title'] ?? 'Candidate');
                        $stage = $src['name'] ?? 'Sourcing';
                        $recruiter = $src['posted_by'] ?? 'Recruiter';
                        $jTitle = $src['title'] ?? 'Requisition';
                        $jId = $src['job_id'] ?? null;

                        $dt = Carbon::createFromTimestamp($timestamp)->setTimezone($tenantTz);
                        $timeAgo = GeneralMethod::timeAgo(date('Y-m-d H:i:s', $timestamp));

                        $activities[] = [
                            'id' => 'cand_'.($candId ?? uniqid()),
                            'category' => 'pipeline',
                            'event_type' => 'candidate.uploaded',
                            'timestamp' => $timestamp,
                            'occurred_at' => $dt->toIso8601String(),
                            'formatted_date' => $dt->format('M d, Y'),
                            'time_formatted' => $dt->format('h:i A T'),
                            'time_ago' => $timeAgo,
                            'sequence' => $timestamp,
                            'activity_label' => 'Candidate Submitted',
                            'description' => "Candidate submitted for {$jTitle} (Stage: {$stage})",
                            'status_text' => 'Submitted to '.ucwords($stage),
                            'status_class' => 'status-active',
                            'job' => [
                                'id' => $jId,
                                'code' => '#'.($jId ?? ''),
                                'title' => ucwords($jTitle),
                                'status' => 'active',
                                'total_positions' => 1,
                                'company_name' => $src['client_or_company_name'] ?? ($src['company_name'] ?? $defaultCompanyName),
                                'company_logo' => $defaultCompanyLogo,
                                'location' => ucwords($src['city_name'] ?? ($src['location'] ?? 'Remote')),
                                'job_type' => 'Full Time',
                                'shared_by_name' => null,
                                'shared_by_company' => null,
                            ],
                            'candidate' => [
                                'id' => $candId,
                                'wise_id' => $wiseId,
                                'name' => $candName,
                                'initials' => $this->getInitials($candName),
                                'designation' => ucwords($desig),
                                'email' => $src['email'] ?? null,
                            ],
                            'actor' => [
                                'name' => ucwords($recruiter),
                                'company_name' => $defaultCompanyName,
                                'image' => null,
                                'initials' => $this->getInitials($recruiter),
                            ],
                            'attribution' => [
                                'label' => 'Submitted by',
                                'name' => ucwords($recruiter),
                                'company_name' => $defaultCompanyName,
                            ],
                            'transition' => [
                                'from_stage_name' => '',
                                'to_stage_name' => $stage,
                                'status' => 'active',
                            ],
                            'link' => $candId ? url('service_company/cvtracker?id='.$candId) : null,
                            'link_label' => 'View Application',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 4. Query ats_candidate_timeline (Stage transitions, pipeline actions, notes)
            $timelineIndex = defined('ELASTIC_CANDIDATE_TIMELINE') ? ELASTIC_CANDIDATE_TIMELINE : 'ats_candidate_timeline';
            if ($this->elasticService->indexExists($timelineIndex)) {
                try {
                    $res = $this->elasticService->getClient()->search([
                        'index' => $timelineIndex,
                        'size' => 150,
                        'body' => [
                            'query' => [
                                'term' => ['event_by_company_id' => (int) $companyId],
                            ],
                            'sort' => [['created_at' => ['order' => 'desc']]],
                        ],
                    ]);

                    foreach ($res['hits']['hits'] ?? [] as $hit) {
                        $src = $hit['_source'];
                        $stage = $src['master_process_name'] ?? 'Pipeline Update';
                        $subStage = $src['child_process_name'] ?? '';
                        $candEmail = $src['application_email'] ?? '';
                        $jobTitle = $src['title'] ?? 'Requisition';
                        $jobId = $src['job_id'] ?? null;
                        $appId = $src['application_id'] ?? null;
                        $rawTime = $src['created_at'] ?? $src['updated_at'] ?? null;
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();

                        $actorId = $src['event_by_user_id'] ?? null;
                        $actorUser = $actorId ? $companyUsers->get($actorId) : null;
                        $actorName = $actorUser->name ?? ($src['name'] ?? 'Team Member');
                        $actorImage = $actorUser->image ?? ($src['image'] ?? null);

                        $category = 'pipeline';
                        $statusClass = 'status-pipeline';

                        if (stripos($stage, 'offer') !== false || stripos($stage, 'join') !== false) {
                            $category = 'offers';
                            $statusClass = 'status-active';
                        } elseif (stripos($stage, 'interview') !== false || ! empty($src['interview_schedule_date'])) {
                            $category = 'interviews';
                            $statusClass = 'status-interview';
                        }

                        $desc = ! empty($src['note'])
                            ? $src['note']
                            : "Candidate status transitioned to {$stage}".($subStage ? " ({$subStage})" : '');

                        $dt = Carbon::createFromTimestamp($timestamp)->setTimezone($tenantTz);
                        $timeAgo = GeneralMethod::timeAgo(date('Y-m-d H:i:s', $timestamp));

                        // Extract name from email or note
                        $candidateName = ! empty($candEmail) ? explode('@', $candEmail)[0] : 'Candidate';
                        $candidateName = ucwords(str_replace(['.', '_', '-'], ' ', $candidateName));

                        $activities[] = [
                            'id' => 'tl_'.($src['id'] ?? uniqid()),
                            'category' => $category,
                            'event_type' => 'candidate.stage_transition',
                            'timestamp' => $timestamp,
                            'occurred_at' => $dt->toIso8601String(),
                            'formatted_date' => $dt->format('M d, Y'),
                            'time_formatted' => $dt->format('h:i A T'),
                            'time_ago' => $timeAgo,
                            'sequence' => $timestamp,
                            'activity_label' => $stage.' Stage Transition',
                            'description' => $desc,
                            'status_text' => $stage,
                            'status_class' => $statusClass,
                            'job' => [
                                'id' => $jobId,
                                'code' => '#'.($jobId ?? ''),
                                'title' => ucwords($jobTitle),
                                'status' => 'active',
                                'total_positions' => 1,
                                'company_name' => $src['cur_companyname'] ?? $defaultCompanyName,
                                'company_logo' => $defaultCompanyLogo,
                                'location' => 'Remote',
                                'job_type' => 'Full Time',
                                'shared_by_name' => null,
                                'shared_by_company' => null,
                            ],
                            'candidate' => [
                                'id' => $appId,
                                'wise_id' => $appId,
                                'name' => $candidateName,
                                'initials' => $this->getInitials($candidateName),
                                'designation' => ucwords($jobTitle),
                                'email' => $candEmail,
                            ],
                            'actor' => [
                                'name' => ucwords($actorName),
                                'company_name' => $src['cur_companyname'] ?? $defaultCompanyName,
                                'image' => $actorImage,
                                'initials' => $this->getInitials($actorName),
                            ],
                            'attribution' => [
                                'label' => 'Updated by',
                                'name' => ucwords($actorName),
                                'company_name' => $src['cur_companyname'] ?? $defaultCompanyName,
                            ],
                            'transition' => [
                                'from_stage_name' => $src['prev_process_name'] ?? '',
                                'to_stage_name' => $stage,
                                'status' => $statusClass,
                            ],
                            'link' => $appId ? url('service_company/cvtracker?id='.$appId) : null,
                            'link_label' => 'View Application',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 5. Query ats_interview_tracker_elastic (Interviews scheduled and logged)
            $intIndex = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : 'ats_interview_tracker_elastic';
            if ($this->elasticService->indexExists($intIndex)) {
                try {
                    $res = $this->elasticService->getClient()->search([
                        'index' => $intIndex,
                        'size' => 50,
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'should' => [
                                        ['term' => ['company_id' => (int) $companyId]],
                                        ['term' => ['job_company_id' => (int) $companyId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                            'sort' => [['created_at' => ['order' => 'desc']]],
                        ],
                    ]);

                    foreach ($res['hits']['hits'] ?? [] as $hit) {
                        $src = $hit['_source'];
                        $rawTime = $src['created_at'] ?? ($src['schedule_date'] ?? null);
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();
                        $cand = $src['candidate_name'] ?? ($src['cand_name'] ?? 'Candidate');
                        $jtitle = $src['job_title'] ?? 'Requisition';
                        $interviewer = $src['interviewer'] ?? 'Recruiting Team';
                        $schedDate = ! empty($src['schedule_date']) ? date('M d, Y', strtotime($src['schedule_date'])) : 'Upcoming';
                        $appId = $src['application_id'] ?? ($src['candidate_id'] ?? null);
                        $jobId = $src['job_id'] ?? null;

                        $dt = Carbon::createFromTimestamp($timestamp)->setTimezone($tenantTz);
                        $timeAgo = GeneralMethod::timeAgo(date('Y-m-d H:i:s', $timestamp));

                        $activities[] = [
                            'id' => 'int_'.($src['id'] ?? uniqid()),
                            'category' => 'interviews',
                            'event_type' => 'interview.scheduled',
                            'timestamp' => $timestamp,
                            'occurred_at' => $dt->toIso8601String(),
                            'formatted_date' => $dt->format('M d, Y'),
                            'time_formatted' => $dt->format('h:i A T'),
                            'time_ago' => $timeAgo,
                            'sequence' => $timestamp,
                            'activity_label' => 'Interview Scheduled',
                            'description' => "Scheduled interview for {$jtitle} on {$schedDate}",
                            'status_text' => 'Interview Scheduled',
                            'status_class' => 'status-interview',
                            'job' => [
                                'id' => $jobId,
                                'code' => '#'.($jobId ?? ''),
                                'title' => ucwords($jtitle),
                                'status' => 'active',
                                'total_positions' => 1,
                                'company_name' => $defaultCompanyName,
                                'company_logo' => $defaultCompanyLogo,
                                'location' => 'Remote',
                                'job_type' => 'Full Time',
                                'shared_by_name' => null,
                                'shared_by_company' => null,
                            ],
                            'candidate' => [
                                'id' => $appId,
                                'wise_id' => $appId,
                                'name' => ucwords($cand),
                                'initials' => $this->getInitials($cand),
                                'designation' => ucwords($jtitle),
                                'email' => null,
                            ],
                            'actor' => [
                                'name' => ucwords($interviewer),
                                'company_name' => $defaultCompanyName,
                                'image' => null,
                                'initials' => $this->getInitials($interviewer),
                            ],
                            'attribution' => [
                                'label' => 'Scheduled by',
                                'name' => ucwords($interviewer),
                                'company_name' => $defaultCompanyName,
                            ],
                            'transition' => null,
                            'link' => url('service_company/interview-schedule'),
                            'link_label' => 'View Schedule',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 6. Query team_invitations (Team Members & Invitations)
            try {
                $invites = DB::table('team_invitations')
                    ->where('company_id', $companyId)
                    ->orderByDesc('created_at')
                    ->limit(25)
                    ->get();

                foreach ($invites as $inv) {
                    $timestamp = ! empty($inv->created_at) ? strtotime($inv->created_at) : time();
                    $actorUser = ! empty($inv->invited_by) ? $companyUsers->get($inv->invited_by) : null;
                    $actorName = $actorUser->name ?? 'Administrator';
                    $actorImage = $actorUser->image ?? null;
                    $status = ucfirst(strtolower($inv->status ?? 'pending'));

                    $dt = Carbon::createFromTimestamp($timestamp)->setTimezone($tenantTz);
                    $timeAgo = GeneralMethod::timeAgo(date('Y-m-d H:i:s', $timestamp));

                    $activities[] = [
                        'id' => 'team_'.$inv->id,
                        'category' => 'team',
                        'event_type' => 'team.invited',
                        'timestamp' => $timestamp,
                        'occurred_at' => $dt->toIso8601String(),
                        'formatted_date' => $dt->format('M d, Y'),
                        'time_formatted' => $dt->format('h:i A T'),
                        'time_ago' => $timeAgo,
                        'sequence' => $timestamp,
                        'activity_label' => 'Team Member Invited',
                        'description' => "Invited colleague to join organization as team member (Status: {$status})",
                        'status_text' => 'Team Member ('.$status.')',
                        'status_class' => 'status-active',
                        'job' => null,
                        'candidate' => [
                            'id' => null,
                            'wise_id' => null,
                            'name' => $inv->email,
                            'initials' => 'TM',
                            'designation' => 'Team Member',
                            'email' => $inv->email,
                        ],
                        'actor' => [
                            'name' => ucwords($actorName),
                            'company_name' => $defaultCompanyName,
                            'image' => $actorImage,
                            'initials' => $this->getInitials($actorName),
                        ],
                        'attribution' => [
                            'label' => 'Invited by',
                            'name' => ucwords($actorName),
                            'company_name' => $defaultCompanyName,
                        ],
                        'transition' => null,
                        'link' => url('service_company/team'),
                        'link_label' => 'Manage Team',
                    ];
                }
            } catch (\Throwable $e) {
            }

            // Deterministic sort: latest timestamp first
            usort($activities, function ($a, $b) {
                return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
            });

            return $activities;
        });
    }

    /**
     * Helper to extract 1-2 character uppercase initials from name.
     */
    protected function getInitials(?string $name): string
    {
        $name = trim($name ?? '');
        if ($name === '') {
            return 'TM';
        }

        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $p) {
            if ($p !== '') {
                $initials .= strtoupper($p[0]);
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }

        return $initials ?: 'TM';
    }

    /**
     * Flushes the cached activity stream for a tenant.
     */
    public function flushActivityCache(int $companyId): void
    {
        Cache::forget("company_activity_all:{$companyId}");
    }
}
