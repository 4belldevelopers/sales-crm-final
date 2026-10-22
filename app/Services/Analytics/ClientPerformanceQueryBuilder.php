<?php

namespace App\Services\Analytics;

/**
 * ClientPerformanceQueryBuilder
 *
 * Constructs optimized Elasticsearch aggregation queries for client performance analytics.
 * Enforces cardinality deduplication, milestone set filtering, and date histogram bucketing.
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

        // Apply date range filter on created_at
        if ($boundaries['current_start'] && $boundaries['days']) {
            $mustClauses[] = [
                'range' => [
                    'created_at' => [
                        'gte'    => $boundaries['current_start'],
                        'lte'    => $boundaries['current_end'],
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                ],
            ];
        }

        $interval = ($boundaries['days'] && $boundaries['days'] <= 35) ? 'week' : 'month';

        return [
            'size' => 50, // Retrieve up to 50 joined hits for exact Time-to-Hire calculation
            'query' => [
                'bool' => [
                    'must'     => $mustClauses,
                    'must_not' => $mustNotClauses,
                ],
            ],
            'aggs' => [
                // 1. Total unique candidates submitted
                'total_candidates' => [
                    'cardinality' => [
                        'field' => 'candidate_id',
                        'missing' => 0,
                    ],
                ],
                // 2. Screened milestone (statuses 2, 3, 4, 5, 6)
                'screened_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => ClientPerformanceConstants::MILESTONE_SCREENED_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'unique_count' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                    ],
                ],
                // 3. Interview milestone (statuses 3, 4, 5, 6)
                'interviewed_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => ClientPerformanceConstants::MILESTONE_INTERVIEW_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'unique_count' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                    ],
                ],
                // 4. Offer milestone (statuses 5, 6)
                'offered_candidates' => [
                    'filter' => [
                        'terms' => [
                            'job_master_status_id' => ClientPerformanceConstants::MILESTONE_OFFER_STATUSES,
                        ],
                    ],
                    'aggs' => [
                        'unique_count' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                    ],
                ],
                // 5. Joined milestone (status 6 + valid placed_end_date)
                'joined_candidates' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['job_master_status_id' => ClientPerformanceConstants::STATUS_JOINED]],
                                ['exists' => ['field' => 'placed_end_date']],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'unique_count' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                    ],
                ],
                // 6. Velocity histogram on created_at
                'velocity_trend' => [
                    'date_histogram' => [
                        'field'             => 'created_at',
                        'calendar_interval' => $interval,
                        'format'            => 'yyyy-MM-dd',
                        'min_doc_count'     => 0,
                    ],
                    'aggs' => [
                        'unique_candidates' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                        'interviewed' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => ClientPerformanceConstants::MILESTONE_INTERVIEW_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'unique_count' => [
                                    'cardinality' => ['field' => 'candidate_id'],
                                ],
                            ],
                        ],
                    ],
                ],
                // 7. Recruiter Performance Breakdown
                'recruiters_bucket' => [
                    'terms' => [
                        'field' => 'submitted_by',
                        'size'  => 10,
                    ],
                    'aggs' => [
                        'submissions' => [
                            'cardinality' => ['field' => 'candidate_id'],
                        ],
                        'interviews' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => ClientPerformanceConstants::MILESTONE_INTERVIEW_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'unique_count' => [
                                    'cardinality' => ['field' => 'candidate_id'],
                                ],
                            ],
                        ],
                        'offers' => [
                            'filter' => [
                                'terms' => [
                                    'job_master_status_id' => ClientPerformanceConstants::MILESTONE_OFFER_STATUSES,
                                ],
                            ],
                            'aggs' => [
                                'unique_count' => [
                                    'cardinality' => ['field' => 'candidate_id'],
                                ],
                            ],
                        ],
                        'joins' => [
                            'filter' => [
                                'term' => ['job_master_status_id' => ClientPerformanceConstants::STATUS_JOINED],
                            ],
                            'aggs' => [
                                'unique_count' => [
                                    'cardinality' => ['field' => 'candidate_id'],
                                ],
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
                    'gte'    => $boundaries['previous_start'],
                    'lte'    => $boundaries['previous_end'],
                    'format' => 'strict_date_optional_time||epoch_millis',
                ],
            ],
        ];

        return [
            'size' => 30,
            'query' => [
                'bool' => [
                    'must'     => $mustClauses,
                    'must_not' => $mustNotClauses,
                ],
            ],
            'aggs' => [
                'total_candidates' => ['cardinality' => ['field' => 'candidate_id']],
                'screened_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => ClientPerformanceConstants::MILESTONE_SCREENED_STATUSES]],
                    'aggs'   => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'interviewed_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => ClientPerformanceConstants::MILESTONE_INTERVIEW_STATUSES]],
                    'aggs'   => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'offered_candidates' => [
                    'filter' => ['terms' => ['job_master_status_id' => ClientPerformanceConstants::MILESTONE_OFFER_STATUSES]],
                    'aggs'   => ['unique_count' => ['cardinality' => ['field' => 'candidate_id']]],
                ],
                'joined_candidates' => [
                    'filter' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['job_master_status_id' => ClientPerformanceConstants::STATUS_JOINED]],
                                ['exists' => ['field' => 'placed_end_date']],
                            ],
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
            'size' => 10,
            'query' => [
                'bool' => [
                    'must' => $mustClauses,
                ],
            ],
            'aggs' => [
                'status_breakdown' => [
                    'terms' => [
                        'field' => 'status',
                        'size'  => 5,
                    ],
                ],
                'employment_type_breakdown' => [
                    'terms' => [
                        'field'   => 'employment_type',
                        'size'    => 10,
                        'missing' => -1,
                    ],
                ],
                'top_roles' => [
                    'terms' => [
                        'field' => 'id',
                        'size'  => 5,
                        'order' => ['_count' => 'desc'],
                    ],
                    'aggs' => [
                        'role_details' => [
                            'top_hits' => [
                                'size'    => 1,
                                '_source' => ['id', 'title', 'total_positions', 'status', 'created_at'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build interview tracker scheduled count query.
     */
    public function buildInterviewTrackerQuery(array $scope, array $boundaries, array $jobIds = []): array
    {
        $mustClauses = [];

        if ($scope['client_type'] === 'internal') {
            $mustClauses[] = ['term' => ['company_id' => $scope['auth_company_id']]];
            if (!empty($jobIds)) {
                $mustClauses[] = ['terms' => ['jobId' => $jobIds]];
            }
        } else {
            $mustClauses[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['ClientComID' => $scope['auth_company_id']]],
                        ['term' => ['thirdparty_company_id' => $scope['auth_company_id']]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            $mustClauses[] = ['term' => ['from_company_id' => $scope['client_id']]];
        }

        if ($boundaries['current_start'] && $boundaries['days']) {
            $mustClauses[] = [
                'range' => [
                    'schedule_date' => [
                        'gte'    => $boundaries['current_start'],
                        'lte'    => $boundaries['current_end'],
                        'format' => 'strict_date_optional_time||epoch_millis',
                    ],
                ],
            ];
        }

        return [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => $mustClauses,
                ],
            ],
            'aggs' => [
                'scheduled_count' => [
                    'value_count' => ['field' => 'id'],
                ],
            ],
        ];
    }
}
