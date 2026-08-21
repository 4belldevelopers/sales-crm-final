<?php

namespace App\Console\Commands;

use App\ElasticCandidate;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class IndexCandidatesToElasticsearch extends Command
{
    protected $signature = 'elasticsearch:index-candidates 
                           {--batch-size=500 : Number of records per batch}
                           {--fresh : Delete and recreate the index}';
    
    protected $description = 'Index candidates to Elasticsearch using bulk API';

    public function handle()
    {
        $elastic = new ElasticService();
        $indexName = ELASTIC_CANDIDATES;
        $batchSize = (int)$this->option('batch-size');

        // Fresh index option
        if ($this->option('fresh')) {
            $this->deleteIndex($elastic, $indexName);
            $this->createIndexWithMappings($elastic, $indexName);
            $this->info("Created fresh index: {$indexName}");
        }

        // Verify index exists
        if (!$elastic->indexExists($indexName)) {
            $this->createIndexWithMappings($elastic, $indexName);
        }

        $this->info("Starting bulk indexing (Batch size: {$batchSize})...");
        
        $progressBar = $this->output->createProgressBar(
            ElasticCandidate::query()->count()
        );

        ElasticCandidate::query()
            ->chunkById($batchSize, function ($candidates) use ($elastic, $indexName, $progressBar) {
                $params = ['body' => []];
                
                foreach ($candidates as $candidate) {
                    $params['body'][] = [
                        'index' => [
                            '_index' => $indexName,
                            '_id' => $candidate->id,
                        ]
                    ];
                    
                    $params['body'][] = $this->formatCandidateData($candidate);
                }

                try {
                    $elastic->bulkIndex($params);
                    $progressBar->advance($candidates->count());
                } catch (\Exception $e) {
                    $this->line('');
                    $this->error("Batch failed: " . $e->getMessage());
                }
            });

        $progressBar->finish();
        $this->line('');
        $this->info("Indexing completed successfully!");
    }

    protected function createIndexWithMappings(ElasticService $elastic, string $indexName): void
    {
        // Define the complete index configuration
        $indexConfig = [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
                'analysis' => [
                    'analyzer' => [
                        'comma_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'pattern',
                            'pattern' => '\\s*,\\s*', // Handles spaces around commas
                            'filter' => ['lowercase', 'trim']
                        ],
                        'number_list_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'number_list_tokenizer',
                            'filter' => ['trim']
                        ]
                    ],
                    'tokenizer' => [
                        'number_list_tokenizer' => [
                            'type' => 'pattern',
                            'pattern' => '\\s*,\\s*',
                            'group' => -1
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'long'],
                    'wiseId' => ['type' => 'long'],
                    'candidate_id' => ['type' => 'integer'],
                    'full_name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'np_id' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ],
                            'numeric_sort' => [
                                'type' => 'text',
                                'analyzer' => 'number_list_analyzer'
                            ]
                        ]
                    ],
                    'immediate_join' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ],
                            'numeric_sort' => [
                                'type' => 'text',
                                'analyzer' => 'number_list_analyzer'
                            ]
                        ]
                    ],
                    'serving_notice_period_mode' => ['type' => 'integer'],
                    'email' => ['type' => 'keyword', 'ignore_above' => 256],
                    'phone' => ['type' => 'keyword', 'ignore_above' => 32],
                    'skills' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ],
                        'analyzer' => 'comma_analyzer'
                    ],
                    'total_exp' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'rel_exp' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ctc' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ectc' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'condidate_type' => ['type' => 'keyword', 'ignore_above' => 256],
                    'ctc_hourly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ctc_monthly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ctc_yearly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ectc_hourly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ectc_monthly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'ectc_yearly' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'status' => ['type' => 'integer'],
                    'current_ctc_cur' => ['type' => 'integer'],
                    'current_ctc_mode' => ['type' => 'integer'],
                    'is_viewed' => ['type' => 'boolean'],
                    'ads_switch' => ['type' => 'keyword', 'ignore_above' => 32],
                    'outsourcing_type' => ['type' => 'integer'],
                    'pay_rate_currency' => ['type' => 'integer'],
                    'pay_rate_amount' => ['type' => 'integer'],
                    'pay_rate_mode' => ['type' => 'integer'],
                    'bill_rate_currency' => ['type' => 'integer'],
                    'bill_rate_amount' => ['type' => 'integer'],
                    'client_comapny_id' => ['type' => 'integer'],
                    'bill_rate_mode' => ['type' => 'integer'],
                    'comission_type' => ['type' => 'integer'],
                    'current_company' => ['type' => 'keyword', 'ignore_above' => 256],
                    'current_designation' => ['type' => 'keyword', 'ignore_above' => 256],
                    'qualification' => ['type' => 'integer'],
                    'qualification_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'city_id' => ['type' => 'integer'],
                    'duplicate' => ['type' => 'integer'],
                    'city_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'state_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'country_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'job_id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'css_style' => ['type' => 'text', 'index' => false],
                    'posted_by_image' => ['type' => 'text', 'index' => false],
                    'title' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'job_status' => ['type' => 'keyword', 'ignore_above' => 256],
                    'employment_type' => ['type' => 'integer'],
                    'client_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'client_company_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    'job_master_status_id' => ['type' => 'keyword'],
                    'childStatus' => ['type' => 'integer'],
                    'master_status_id' => ['type' => 'integer'],
                    'child_status_id' => ['type' => 'integer'],
                    'status_id' => ['type' => 'integer'],
                    'rec_candidate_assign_id' => ['type' => 'integer'],
                    'submitted_by' => ['type' => 'integer'],
                    'company_id' => ['type' => 'integer'],
                    'thirdparty_company_id' => ['type' => 'integer'],
                    'ClientComID' => ['type' => 'integer'],
                    'final_status' => ['type' => 'integer'],
                    'recommendation_id' => ['type' => 'integer'],
                    'from_company_id' => ['type' => 'integer'],
                    'to_company_id' => ['type' => 'integer'],
                    'jobId' => ['type' => 'integer'],
                    'placed_end_date' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'existing' => ['type' => 'integer'],
                    'skill_match' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'posted_by' => ['type' => 'keyword', 'ignore_above' => 256],
                    'created_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'updated_at' => [
                        'type' => 'date',
                        'format' => 'strict_date_optional_time||epoch_millis'
                    ],
                    'company_name' => ['type' => 'keyword', 'ignore_above' => 256],
                    // Commercial configuration (from offer_job_applications)
                    'commercial_service_type' => ['type' => 'keyword', 'ignore_above' => 32],
                    'commercial_model'        => ['type' => 'keyword', 'ignore_above' => 32],
                    'commercial_fee_method'   => ['type' => 'keyword', 'ignore_above' => 32],
                    'commercial_fee_base'     => ['type' => 'keyword', 'ignore_above' => 32],
                    'commercial_fee_pct'      => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'commercial_fee_fixed'    => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    // Assignment details (from offer_job_applications)
                    'assignment_status'            => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]],
                    'assignment_expected_end_date' => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis||yyyy-MM-dd'],
                    'assignment_client_location'   => ['type' => 'keyword', 'ignore_above' => 256],
                    'assignment_exit_reason'       => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]],
                    'assignment_project_outcome'   => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]],
                    'assignment_remarks'           => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]],
                    // Phase 2 V2 Denormalized & Pre-computed Sort Fields
                    'spokperson_name'              => ['type' => 'keyword', 'ignore_above' => 256],
                    'offer_released_at'            => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis||yyyy-MM-dd HH:mm:ss'],
                    'sourcing_date'                => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis||yyyy-MM-dd HH:mm:ss'],
                    'evaluation_date'              => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis||yyyy-MM-dd HH:mm:ss'],
                    'child_status_name'            => ['type' => 'keyword', 'ignore_above' => 256],
                    'is_duplicate'                 => ['type' => 'boolean'],
                    'notice_period_days'           => ['type' => 'integer'],
                    'notice_period_epoch'          => ['type' => 'long'],
                    'job_max_sal_mode'             => ['type' => 'integer'],
                    'job_max_salary_currency'      => ['type' => 'integer'],
                ]
            ]
        ];

    
        // Delete existing index if it exists
        if ($elastic->getClient()->indices()->exists(['index' => $indexName])) {
            $elastic->getClient()->indices()->delete(['index' => $indexName]);
        }

        // Create new index with proper configuration
        $response = $elastic->getClient()->indices()->create([
            'index' => $indexName,
            'body' => $indexConfig
        ]);

    }

    protected function deleteIndex(ElasticService $elastic, string $indexName): void
    {
        if ($elastic->indexExists($indexName)) {
            $elastic->getClient()->indices()->delete(['index' => $indexName]);
        }
    }

    protected function formatCandidateData(ElasticCandidate $candidate): array
    {
        // Phase 2: Compute numeric notice period days & epoch for fast Painless-free sorting
        $npId = $candidate->np_id;
        $noticePeriodDays = 999999;
        $noticePeriodEpoch = 999999999;

        if (!empty($npId)) {
            $trimmedNp = trim($npId);
            if (strcasecmp($trimmedNp, 'Immediate') === 0) {
                $noticePeriodDays = 0;
                $noticePeriodEpoch = -999999998;
            } else {
                if (preg_match('/(\d+)/', $trimmedNp, $matches)) {
                    $noticePeriodDays = (int) $matches[1];
                }
                try {
                    $cleanDate = explode(',', $trimmedNp)[0];
                    $cleanDate = explode('T', $cleanDate)[0];
                    $cleanDate = str_replace('/', '-', trim($cleanDate));
                    $parsedDate = Carbon::parse($cleanDate);
                    $noticePeriodEpoch = $parsedDate->timestamp;
                } catch (\Exception $e) {
                    // Retain default fallback
                }
            }
        }

        return [
            'id' => $candidate->id,
            'wiseId' => $candidate->wiseId,
            'candidate_id' => $candidate->candidate_id,
            'full_name' => $candidate->full_name,
            'np_id' => $candidate->np_id,
            'immediate_join' => ($candidate->immediate_join =='Immediate') ? 'Immediate' : null,
            'serving_notice_period_mode' => $candidate->serving_notice_period_mode,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'skills' => $candidate->skills,
            'total_exp' => $candidate->total_exp,
            'rel_exp' => $candidate->rel_exp,
            'ctc' => $candidate->ctc,
            'ectc' => $candidate->ectc,
            'condidate_type' => $candidate->condidate_type,
            'ctc_hourly' => $candidate->ctc_hourly,
            'ctc_monthly' => $candidate->ctc_monthly,
            'duplicate' => $candidate->duplicate,
            'ctc_yearly' => $candidate->ctc_yearly,
            'ectc_hourly' => $candidate->ectc_hourly,
            'ectc_monthly' => $candidate->ectc_monthly,
            'ectc_yearly' => $candidate->ectc_yearly,
            'status' => $candidate->status,
            'name' => $candidate->name,
            'css_style' => $candidate->css_style,
            'posted_by_image' => $candidate->posted_by_image,
            'current_ctc_cur' => $candidate->current_ctc_cur,
            'current_ctc_mode' => $candidate->current_ctc_mode,
            'is_viewed' => $candidate->is_viewed,
            'client_comapny_id' => $candidate->client_comapny_id,
            'ads_switch' => $candidate->ads_switch,
            'outsourcing_type' => $candidate->outsourcing_type,
            'pay_rate_currency' => $candidate->pay_rate_currency,
            'pay_rate_amount' => $candidate->pay_rate_amount,
            'pay_rate_mode' => $candidate->pay_rate_mode,
            'bill_rate_currency' => $candidate->bill_rate_currency,
            'bill_rate_amount' => $candidate->bill_rate_amount,
            'bill_rate_mode' => $candidate->bill_rate_mode,
            'comission_type' => $candidate->comission_type,
            'current_company' => $candidate->current_company,
            'current_designation' => $candidate->current_designation,
            'qualification' => $candidate->qualification,
            'qualification_name' => $candidate->qualification_name,
            'city_id' => $candidate->city_id,
            'city_name' => $candidate->city_name,
            'state_name' => $candidate->state_name,
            'country_name' => $candidate->country_name,
            'job_id' => $candidate->job_id,
            'title' => $candidate->title,
            'job_status' => $candidate->job_status,
            'childStatus' => $candidate->childStatus,
            'job_master_status_id' => $candidate->job_master_status_id,
            'employment_type' => $candidate->employment_type,
            'client_name' => $candidate->client_name,
            'client_company_name' => $candidate->client_company_name,
            'master_status_id' => $candidate->master_status_id,
            'child_status_id' => $candidate->child_status_id,
            'status_id' => $candidate->status_id,
            'rec_candidate_assign_id' => $candidate->rec_candidate_assign_id,
            'submitted_by' => $candidate->submitted_by,
            'company_id' => $candidate->company_id,
            'thirdparty_company_id' => $candidate->thirdparty_company_id,
            'ClientComID' => $candidate->ClientComID,
            'final_status' => $candidate->final_status,
            'recommendation_id' => $candidate->recommendation_id,
            'from_company_id' => $candidate->from_company_id,
            'to_company_id' => $candidate->to_company_id,
            'jobId' => $candidate->jobId,
            'placed_end_date' => $candidate->placed_end_date,
            'existing' => $candidate->existing,
            'skill_match' => $candidate->skill_match,
            'posted_by' => $candidate->posted_by,
            'created_at' => Carbon::parse($candidate->created_at)->toIso8601String(),
            'updated_at' => Carbon::parse($candidate->updated_at)->toIso8601String(),
            'company_name' => ($candidate->company_name) ? $candidate->company_name : $candidate->client_company_name,
            // Commercial configuration
            'commercial_service_type' => $candidate->commercial_service_type ?? null,
            'commercial_model'        => $candidate->commercial_model ?? null,
            'commercial_fee_method'   => $candidate->commercial_fee_method ?? null,
            'commercial_fee_base'     => $candidate->commercial_fee_base ?? null,
            'commercial_fee_pct'      => $candidate->commercial_fee_pct ?? null,
            'commercial_fee_fixed'    => $candidate->commercial_fee_fixed ?? null,
            // Assignment details
            'assignment_status'            => $candidate->assignment_status ?? null,
            'assignment_expected_end_date' => $candidate->assignment_expected_end_date ?? null,
            'assignment_client_location'   => $candidate->assignment_client_location ?? null,
            'assignment_exit_reason'       => $candidate->assignment_exit_reason ?? null,
            'assignment_project_outcome'   => $candidate->assignment_project_outcome ?? null,
            'assignment_remarks'           => $candidate->assignment_remarks ?? null,
            // Phase 2 V2 Denormalized & Pre-computed Fields
            'spokperson_name'              => $candidate->spokperson_name ?? null,
            'child_status_name'            => $candidate->child_status_name ?? null,
            'is_duplicate'                 => (bool) ($candidate->duplicate ?? 0),
            'notice_period_days'           => $noticePeriodDays,
            'notice_period_epoch'          => $noticePeriodEpoch,
            'job_max_sal_mode'             => $candidate->job_max_sal_mode ?? $candidate->current_ctc_mode ?? 0,
            'job_max_salary_currency'      => $candidate->job_max_salary_currency ?? $candidate->current_ctc_cur ?? 0,
        ];
    }


}