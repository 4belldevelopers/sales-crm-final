<?php

namespace App\Services\Analytics;

use App\Services\Analytics\Contracts\PerformanceStatusMap;
use App\Services\Interview\InterviewTenantScope;

/**
 * ClientPerformanceQueryBuilder
 *
 * Constructs optimized Elasticsearch aggregation queries for client performance analytics.
 * Enforces dual candidate dimensions (submissions count vs unique candidates),
 * canonical shared job scoping, positive milestone allow-lists, and event-based bucketing.
 *
 * (c) 2026 JERO.WORKS. All rights reserved.
 */
class ClientPerformanceQueryBuilder
{
    /**
     * Build candidate pipeline aggregation query.
     */
    public function buildCandidateQuery(array $scope, array $boundaries): array
    {
        $mustClauses = $scope['candidate_filter']['bool']['must'] ?? [];
        $mustNotClauses = $scope['candidate_filter']['bool']['must_not'] ?? [];

        // Apply date range filter on created_at / sourcing_date
        if (! empty($boundaries['current_start']) && ! empty($boundaries['days'])) {
            $mustClauses[] = [
                'range' => [
                    'created_at' => [
                        'gte' => $boundaries['current_start'],
                        'lte' => $boundaries['current_end'],
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                ],
            ];
        }

        $interval = (! empty($boundaries['days']) && $boundaries['days'] <= 35) ? 'week' : 'month';

        return [
            'size' => 100, // Retrieve up to 100 joined hits for exact Time-to-Hire calculation
            'query' => [
                'bool' => [
                    'must' => $mustClauses,
                    'must_not' => $mustNotClauses,
                ],
            ],
            'aggs' => [
                // 1. Dual Candidate Dimensions (P0 Contract: strictly statuses 1..6, excluding In Review 8 and Archived 9)
                'eligible_submissions' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => PerformanceStatusMap::SHARED_PIPELINE_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],
                'total_submissions' => [
                    'value_count' => [
                        'field' => 'id',
                    ],
                ],
                'unique_candidates' => [
                    'cardinality' => [
                        'field' => 'candidate_id',
                        'missing' => 0,
                    ],
                ],

                // Hiring Pipeline Exception: In Review candidates reflected as part of the pipeline
                'in_review_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => [8, '8'],
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],

                // 2. Screened milestone (statuses 2, 3, 4, 5, 6)
                'screened_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => PerformanceStatusMap::MILESTONE_SCREENED_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],

                // 3. Interview milestone (statuses 3, 4, 5, 6)
                'interviewed_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => PerformanceStatusMap::MILESTONE_INTERVIEW_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],

                // 4. Offer milestone (statuses 5, 6)
                'offered_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => PerformanceStatusMap::MILESTONE_OFFER_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],

                // 5. Joined milestone (status 6 + valid placed_end_date / of_joined_date)
                'joined_candidates' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['job_master_status_id' => PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0]]],
                            ],
                            'should' => [
                                ['exists' => ['field' => 'placed_end_date']],
                                ['exists' => ['field' => 'of_joined_date']],
                                ['term' => ['of_joined' => 1]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],

                // 6. Temporal Event Histogram (Event-based Sub-Aggregations)
                'velocity_trend' => [
                    'date_histogram' => [
                        'field' => 'created_at',
                        'calendar_interval' => $interval,
                        'format' => 'yyyy-MM-dd',
                        'min_doc_count' => 0,
                    ],
                    'aggs' => [
                        'eligible_candidates' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::SHARED_PIPELINE_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'submissions' => ['value_count' => ['field' => 'id']],
                                'unique_candidates' => ['cardinality' => ['field' => 'candidate_id']],
                            ],
                        ],
                        'submissions' => [
                            'value_count' => ['field' => 'id'],
                        ],
                        'unique_candidates' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                        'interviewed' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::MILESTONE_INTERVIEW_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                                'submissions' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                        // Real Offers in this temporal bucket
                        'monthly_offers' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::MILESTONE_OFFER_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'count' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                        // Real Joins in this temporal bucket
                        'monthly_joins' => [
                            'filter' => [
                                'term' => [
                                    'job_master_status_id' => PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0],
                                ],
                            ],
                            'aggs' => [
                                'count' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                    ],
                ],

                // 7. Top Roles breakdown strictly on jobId in ELASTIC_CANDIDATES
                'job_roles_bucket' => [
                    'terms' => [
                        'field' => 'jobId',
                        'size' => 5,
                        'order' => ['submissions' => 'desc'],
                    ],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_candidates' => ['cardinality' => ['field' => 'candidate_id']],
                        'interviews' => [
                            'filter' => [
                                'terms' => ['job_master_status_id' => PerformanceStatusMap::MILESTONE_INTERVIEW_STATUSES],
                            ],
                            'aggs' => ['count' => ['value_count' => ['field' => 'id']]],
                        ],
                        'offers' => [
                            'filter' => [
                                'terms' => ['job_master_status_id' => PerformanceStatusMap::MILESTONE_OFFER_STATUSES],
                            ],
                            'aggs' => ['count' => ['value_count' => ['field' => 'id']]],
                        ],
                        'joins' => [
                            'filter' => [
                                'term' => ['job_master_status_id' => PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0]],
                            ],
                            'aggs' => ['count' => ['value_count' => ['field' => 'id']]],
                        ],
                        'role_info' => [
                            'top_hits' => [
                                'size' => 1,
                                '_source' => ['jobId', 'title', 'employment_type'],
                            ],
                        ],
                    ],
                ],

                // 8. Recruiter Performance Breakdown with Denormalized Metadata (0% MySQL reads)
                'recruiters_bucket' => [
                    'terms' => [
                        'field' => 'submitted_by',
                        'size' => 5,
                    ],
                    'aggs' => [
                        'eligible_candidates' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::SHARED_PIPELINE_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'submissions' => ['value_count' => ['field' => 'id']],
                                'unique_candidates' => ['cardinality' => ['field' => 'candidate_id']],
                            ],
                        ],
                        'submissions' => [
                            'value_count' => ['field' => 'id'],
                        ],
                        'unique_candidates' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                        'interviews' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::MILESTONE_INTERVIEW_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'count' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                        'offers' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => PerformanceStatusMap::MILESTONE_OFFER_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'count' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                        'joins' => [
                            'filter' => [
                                'term' => ['job_master_status_id' => PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0]],
                            ],
                            'aggs' => [
                                'count' => ['value_count' => ['field' => 'id']],
                            ],
                        ],
                        'recruiter_info' => [
                            'top_hits' => [
                                'size' => 1,
                                '_source' => ['submitted_by', 'submitted_by_name', 'posted_by', 'posted_by_image'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build previous period summary query for baseline comparison.
     */
    public function buildPreviousPeriodQuery(array $scope, array $boundaries): ?array
    {
        if (empty($boundaries['previous_start'])) {
            return null;
        }

        $mustClauses = $scope['candidate_filter']['bool']['must'] ?? [];
        $mustNotClauses = $scope['candidate_filter']['bool']['must_not'] ?? [];

        $mustClauses[] = [
            'range' => [
                'created_at' => [
                    'gte' => $boundaries['previous_start'],
                    'lte' => $boundaries['previous_end'],
                    'format' => 'strict_date_optional_time||epoch_millis',
                ],
            ],
        ];

        return [
            'size' => 50,
            'query' => [
                'bool' => [
                    'must' => $mustClauses,
                    'must_not' => $mustNotClauses,
                ],
            ],
            'aggs' => [
                'eligible_submissions' => [
                    'filter' => ['terms' => ['job_master_status_id' => PerformanceStatusMap::SHARED_PIPELINE_STATUSES]],
                    'aggs' => [
                        'submissions' => ['value_count' => ['field' => 'id']],
                        'unique_count' => ['cardinality' => ['field' => 'candidate_id']],
                    ],
                ],
                'total_submissions' => ['value_count' => ['field' => 'id']],
                'unique_candidates' => ['cardinality' => ['field' => 'candidate_id']],
                'screened_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => PerformanceStatusMap::MILESTONE_SCREENED_STATUSES]],
                    'aggs' => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'interviewed_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => PerformanceStatusMap::MILESTONE_INTERVIEW_STATUSES]],
                    'aggs' => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'offered_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => PerformanceStatusMap::MILESTONE_OFFER_STATUSES]],
                    'aggs' => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'joined_candidates' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['job_master_status_id' => PerformanceStatusMap::STATUS_ID_MAP[PerformanceStatusMap::STAGE_JOINED][0]]],
                            ],
                            'should' => [
                                ['exists' => ['field' => 'placed_end_date']],
                                ['exists' => ['field' => 'of_joined_date']],
                                ['term' => ['of_joined' => 1]],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'aggs' => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
            ],
        ];
    }

    /**
     * Build jobs summary query.
     */
    public function buildJobQuery(array $scope, array $boundaries): array
    {
        $mustClauses = $scope['job_filter']['bool']['must'] ?? [];

        return [
            'size' => 100, // Retrieve up to 100 jobs for direct inspection
            'query' => [
                'bool' => [
                    'must' => $mustClauses,
                ],
            ],
            'aggs' => [
                'status_breakdown' => [
                    'terms' => [
                        'field' => 'status',
                        'size' => 5,
                    ],
                ],
                'employment_type_breakdown' => [
                    'terms' => [
                        'field' => 'employment_type',
                        'size' => 10,
                        'missing' => -1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Build interview tracker scheduled count query using canonical InterviewTenantScope.
     */
    public function buildInterviewTrackerQuery(array $scope, array $boundaries, array $jobIds = []): array
    {
        $authCompanyId = (int) ($scope['auth_company_id'] ?? 0);
        $clientId = (int) ($scope['client_id'] ?? 0);
        $clientType = (string) ($scope['client_type'] ?? 'direct');

        // Use canonical InterviewTenantScope
        $tenantQuery = InterviewTenantScope::forClient($authCompanyId, $clientId, $clientType);

        $mustClauses = $tenantQuery['bool']['should'] ?? [];

        $rootBool = [
            'must' => [
                $tenantQuery,
            ],
            'must_not' => [
                ['terms' => ['master_status_id' => [8, '8']]],
                ['terms' => ['job_master_status_id' => [8, '8']]],
            ],
        ];

        // Strict Shared Job Scoping
        if (! empty($jobIds)) {
            $rootBool['must'][] = [
                'terms' => [
                    'jobId' => $jobIds,
                ],
            ];
        }

        // Event-Based Date Filtering on schedule_date
        if (! empty($boundaries['current_start']) && ! empty($boundaries['days'])) {
            $rootBool['must'][] = [
                'range' => [
                    'schedule_date' => [
                        'gte' => $boundaries['current_start'],
                        'lte' => $boundaries['current_end'],
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                ],
            ];
        }

        return [
            'size' => 0,
            'track_total_hits' => true,
            'query' => [
                'bool' => $rootBool,
            ],
            'aggs' => [
                'scheduled_count' => [
                    'value_count' => ['field' => 'id'],
                ],
            ],
        ];
    }
}
