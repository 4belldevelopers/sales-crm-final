<?php

namespace App\Services\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Service: CompanyActivityReadService
 *
 * Dedicated CQRS Read Service for Company Users Activity & Timeline on Company Profile.
 * Aggregates authoritative company-wide events across:
 * 1. ats_candidate_timeline (Stage transitions, pipeline updates, notes)
 * 2. ats_jobs_elastic (Requisitions created/updated by hiring managers & team)
 * 3. ats_interview_tracker_elastic (Interviews scheduled and updated)
 * 4. ats_candidates_elastic (Candidate applications & submissions)
 * 5. team_invitations (Colleagues invited & access changes)
 *
 * Tenancy: Strictly isolated by $companyId.
 * Complies with Rule 1 (0% MySQL writes/heavy queries on read path) & Rule 20.
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
                return
                    str_contains(strtolower($item['title'] ?? ''), $searchLower) ||
                    str_contains(strtolower($item['description'] ?? ''), $searchLower) ||
                    str_contains(strtolower($item['actor_name'] ?? ''), $searchLower) ||
                    str_contains(strtolower($item['action'] ?? ''), $searchLower) ||
                    str_contains(strtolower($item['candidate'] ?? ''), $searchLower);
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

            // 1. Resolve company user dictionary for actor attribution
            $companyUsers = DB::table('users')
                ->where('company_id', $companyId)
                ->select('id', 'name', 'email', 'image')
                ->get()
                ->keyBy('id');

            // 2. Query ats_candidate_timeline (Stage transitions, pipeline actions, notes)
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
                        $rawTime = $src['created_at'] ?? $src['updated_at'] ?? null;
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();

                        $actorId = $src['event_by_user_id'] ?? null;
                        $actorUser = $actorId ? $companyUsers->get($actorId) : null;
                        $actorName = $actorUser->name ?? ($src['name'] ?? 'Team Member');
                        $actorImage = $actorUser->image ?? ($src['image'] ?? null);

                        $category = 'pipeline';
                        $badgeColor = '#3b82f6';
                        $badgeBg = '#eff6ff';
                        $icon = 'fas fa-arrows-split-up-and-left';

                        if (stripos($stage, 'offer') !== false || stripos($stage, 'join') !== false) {
                            $category = 'offers';
                            $badgeColor = '#10b981';
                            $badgeBg = '#ecfdf5';
                            $icon = 'fas fa-award';
                        } elseif (stripos($stage, 'interview') !== false || ! empty($src['interview_schedule_date'])) {
                            $category = 'interviews';
                            $badgeColor = '#8b5cf6';
                            $badgeBg = '#f5f3ff';
                            $icon = 'fas fa-calendar-check';
                        }

                        $desc = ! empty($src['note'])
                            ? $src['note']
                            : "Candidate status transitioned to {$stage}".($subStage ? " ({$subStage})" : '');

                        $appId = $src['application_id'] ?? null;

                        $activities[] = [
                            'id' => 'tl_'.($src['id'] ?? uniqid()),
                            'category' => $category,
                            'action' => $stage.' Stage Transition',
                            'title' => ucwords($jobTitle),
                            'description' => $desc,
                            'candidate' => $candEmail,
                            'actor_name' => ucwords($actorName),
                            'actor_id' => $actorId,
                            'actor_image' => $actorImage,
                            'actor_initials' => $this->getInitials($actorName),
                            'timestamp' => $timestamp,
                            'formatted_date' => date('M d, Y h:i A', $timestamp),
                            'time_ago' => GeneralMethod::timeAgo($timestamp),
                            'badge_text' => $stage,
                            'badge_color' => $badgeColor,
                            'badge_bg' => $badgeBg,
                            'icon' => $icon,
                            'link' => $appId ? url('service_company/cvtracker?id='.$appId) : null,
                            'link_label' => 'View Application',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 3. Query ats_jobs_elastic (Requisitions created and updated)
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

                        // Resolve actor from job_assign or comId
                        $actorName = 'Hiring Manager';
                        $actorImage = null;
                        if (! empty($src['job_assign']) && is_array($src['job_assign']) && ! empty($src['job_assign'][0]['user_name'])) {
                            $actorName = $src['job_assign'][0]['user_name'];
                            $actorId = $src['job_assign'][0]['user_id'] ?? null;
                            if ($actorId && $u = $companyUsers->get($actorId)) {
                                $actorImage = $u->image;
                            }
                        }

                        $activities[] = [
                            'id' => 'job_'.($src['id'] ?? uniqid()),
                            'category' => 'jobs',
                            'action' => 'Requisition Published',
                            'title' => ucwords($jobTitle),
                            'description' => "Published requisition with {$openings} opening".($openings > 1 ? 's' : '').' (Status: '.ucfirst($status).')',
                            'candidate' => null,
                            'actor_name' => ucwords($actorName),
                            'actor_id' => $src['user_id'] ?? null,
                            'actor_image' => $actorImage,
                            'actor_initials' => $this->getInitials($actorName),
                            'timestamp' => $timestamp,
                            'formatted_date' => date('M d, Y h:i A', $timestamp),
                            'time_ago' => GeneralMethod::timeAgo($timestamp),
                            'badge_text' => ucfirst($status),
                            'badge_color' => ($status === 'active' ? '#10b981' : '#64748b'),
                            'badge_bg' => ($status === 'active' ? '#ecfdf5' : '#f1f5f9'),
                            'icon' => 'fas fa-briefcase',
                            'link' => ! empty($src['id']) ? url('service_company/jobs/'.$src['id']) : null,
                            'link_label' => 'View Requisition',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 4. Query ats_interview_tracker_elastic (Interviews scheduled and logged)
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
                        $rawTime = $src['created_at'] ?? $src['schedule_date'] ?? null;
                        $timestamp = ! empty($rawTime) ? strtotime($rawTime) : time();
                        $cand = $src['candidate_name'] ?? $src['cand_name'] ?? 'Candidate';
                        $jtitle = $src['job_title'] ?? 'Requisition';
                        $interviewer = $src['interviewer'] ?? 'Recruiting Team';
                        $schedDate = ! empty($src['schedule_date']) ? date('M d, Y', strtotime($src['schedule_date'])) : 'Upcoming';

                        $activities[] = [
                            'id' => 'int_'.($src['id'] ?? uniqid()),
                            'category' => 'interviews',
                            'action' => 'Interview Scheduled',
                            'title' => 'Interview with '.ucwords($cand),
                            'description' => "Scheduled interview for {$jtitle} on {$schedDate}",
                            'candidate' => $cand,
                            'actor_name' => ucwords($interviewer),
                            'actor_id' => null,
                            'actor_image' => null,
                            'actor_initials' => $this->getInitials($interviewer),
                            'timestamp' => $timestamp,
                            'formatted_date' => date('M d, Y h:i A', $timestamp),
                            'time_ago' => GeneralMethod::timeAgo($timestamp),
                            'badge_text' => 'Interview',
                            'badge_color' => '#8b5cf6',
                            'badge_bg' => '#f5f3ff',
                            'icon' => 'fas fa-calendar-check',
                            'link' => url('service_company/interview-schedule'),
                            'link_label' => 'View Schedule',
                        ];
                    }
                } catch (\Throwable $e) {
                }
            }

            // 5. Query team_invitations (Team Members & Invitations)
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

                    $activities[] = [
                        'id' => 'team_'.$inv->id,
                        'category' => 'team',
                        'action' => 'Team Member Invited',
                        'title' => 'Invitation sent to '.$inv->email,
                        'description' => "Invited colleague to join organization as team member (Status: {$status})",
                        'candidate' => null,
                        'actor_name' => ucwords($actorName),
                        'actor_id' => $inv->invited_by ?? null,
                        'actor_image' => $actorImage,
                        'actor_initials' => $this->getInitials($actorName),
                        'timestamp' => $timestamp,
                        'formatted_date' => date('M d, Y h:i A', $timestamp),
                        'time_ago' => GeneralMethod::timeAgo($timestamp),
                        'badge_text' => 'Team',
                        'badge_color' => '#06b6d4',
                        'badge_bg' => '#ecfeff',
                        'icon' => 'fas fa-user-plus',
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
