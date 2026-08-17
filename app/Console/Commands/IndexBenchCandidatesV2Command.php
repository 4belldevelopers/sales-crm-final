<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * INTENT: Re-index bench candidates into Elasticsearch (ELASTIC_CANDIDATES_BENCH) using V2 mapping.
 * Pre-computes sorting fields (ads_switch_order, np_epoch_day, np_is_immediate) and embeds candidate_skills nested objects to eliminate N+1 queries.
 */
class IndexBenchCandidatesV2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bench:reindex-es-v2
                            {--fresh : Delete and recreate the index with V2 mappings}
                            {--batch-size=500 : Number of candidate records per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index bench candidates to Elasticsearch (ELASTIC_CANDIDATES_BENCH) with V2 mapping and pre-computed sort fields';

    /**
     * Execute the console command.
     */
    public function handle(ElasticService $elastic): int
    {
        $indexName = ELASTIC_CANDIDATES_BENCH;
        $batchSize = (int) $this->option('batch-size');

        if ($this->option('fresh')) {
            if ($elastic->indexExists($indexName)) {
                $elastic->deleteIndex($indexName);
                $this->info("Deleted existing index: {$indexName}");
            }
            $this->createV2IndexMapping($elastic, $indexName);
            $this->info("Created fresh V2 index: {$indexName}");
        }

        if (! $elastic->indexExists($indexName)) {
            $this->createV2IndexMapping($elastic, $indexName);
            $this->info("Created index: {$indexName}");
        }

        $this->info("Starting V2 bulk bench candidate indexing (Batch size: {$batchSize})...");

        // TENANCY NOTE: Raw DB query without global scopes to ensure full administrative re-index across all marketplace candidate entities.
        $totalCandidates = DB::table('bench_applications')->count();
        if ($totalCandidates === 0) {
            $this->warn('No candidate records found in bench_applications.');

            return 0;
        }

        $progressBar = $this->output->createProgressBar($totalCandidates);
        $progressBar->start();

        DB::table('bench_applications')
            ->orderBy('id')
            ->chunk($batchSize, function ($candidates) use ($elastic, $indexName, $progressBar) {
                $candidateIds = $candidates->pluck('id')->toArray();

                // INTENT: Pre-fetch candidate skill experiences in bulk to avoid N+1 queries during document formatting
                $skillsMap = [];
                $skills = DB::table('bench_candidate_skill_experience')
                    ->whereIn('candidate_id', $candidateIds)
                    ->get();
                foreach ($skills as $sk) {
                    $skillsMap[$sk->candidate_id][] = [
                        'skill_key' => strtolower(trim($sk->skill_key)),
                        'rating' => (int) $sk->rating,
                        'year' => (int) $sk->year,
                        'month' => (int) $sk->month,
                        'total_months' => (int) $sk->total_months,
                    ];
                }

                $bulkParams = ['body' => []];

                foreach ($candidates as $candidate) {
                    $doc = $this->formatCandidateV2Document($candidate, $skillsMap[$candidate->id] ?? []);

                    $bulkParams['body'][] = [
                        'index' => [
                            '_index' => $indexName,
                            '_id' => $candidate->id,
                        ],
                    ];
                    $bulkParams['body'][] = $doc;
                }

                try {
                    $elastic->bulkIndex($bulkParams);
                    $progressBar->advance(count($candidates));
                } catch (\Exception $e) {
                    $this->line('');
                    $this->error('Bulk indexing batch failed: '.$e->getMessage());
                }
            });

        $progressBar->finish();
        $this->line('');
        $this->info('V2 Bench candidate indexing completed successfully!');

        return 0;
    }

    /**
     * Create Elasticsearch index mapping for ELASTIC_CANDIDATES_BENCH with pre-computed V2 fields.
     */
    protected function createV2IndexMapping(ElasticService $elastic, string $indexName): void
    {
        $mappingConfig = [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'comma_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'pattern',
                            'pattern' => '\\s*,\\s*',
                            'filter' => ['lowercase', 'trim'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'long'],
                    'company_id' => ['type' => 'long'],
                    'condidate_type' => ['type' => 'keyword'],
                    'full_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'current_designation' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'email' => ['type' => 'keyword'],
                    'phone' => ['type' => 'keyword'],
                    'status' => ['type' => 'integer'],
                    'outsourcing_type' => ['type' => 'integer'],
                    'total_exp' => ['type' => 'float'],
                    'rel_exp' => ['type' => 'float'],
                    'bill_rate_amount' => ['type' => 'float'],
                    'bill_rate_currency' => ['type' => 'integer'],
                    'bill_rate_mode' => ['type' => 'integer'],
                    'ectc_yearly' => ['type' => 'float'],
                    'ectc_monthly' => ['type' => 'float'],
                    'ectc_hourly' => ['type' => 'float'],
                    'np_id' => ['type' => 'keyword'],
                    'serving_notice_period_mode' => ['type' => 'integer'],
                    'city_id' => ['type' => 'integer'],
                    'city_name' => ['type' => 'keyword'],
                    'country_name' => ['type' => 'keyword'],
                    'qualification' => ['type' => 'integer'],
                    'qualification_name' => ['type' => 'keyword'],
                    'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss\|yyyy-MM-dd\'T\'HH:mm:ss.SSSSSS\'Z\'\|strict_date_optional_time'],

                    // V2 Pre-computed sorting & skill fields
                    'candidate_skills' => [
                        'type' => 'nested',
                        'properties' => [
                            'skill_key' => ['type' => 'keyword'],
                            'rating' => ['type' => 'integer'],
                            'total_months' => ['type' => 'integer'],
                        ],
                    ],
                    'skill_experiences' => [
                        'type' => 'nested',
                        'properties' => [
                            'skill_key' => ['type' => 'keyword'],
                            'rating' => ['type' => 'integer'],
                            'total_months' => ['type' => 'integer'],
                        ],
                    ],
                    'ads_switch_order' => ['type' => 'integer'], // 0 for Yes, 1 for No
                    'ads_switch_rank' => ['type' => 'integer'],  // 0 for Yes, 1 for No
                    'np_epoch_day' => ['type' => 'long'], // Integer epoch day for painless-free sorting
                    'np_is_immediate' => ['type' => 'boolean'], // true for Immediate joiners
                    'notice_period_days' => ['type' => 'integer'],
                    'np_days' => ['type' => 'integer'],
                ],
            ],
        ];

        $elastic->createIndex($indexName, $mappingConfig);
    }

    /**
     * Format candidate record into V2 Elasticsearch document array.
     */
    protected function formatCandidateV2Document($candidate, array $skills): array
    {
        $adsSwitchOrder = strtolower($candidate->ads_switch ?? 'no') === 'yes' ? 0 : 1;
        $npEpochDay = $this->calculateNpEpochDay($candidate->np_id ?? '');
        $npIsImmediate = strtolower(trim($candidate->np_id ?? '')) === 'immediate';

        $noticePeriodDays = 0;
        $modeNp = (int) ($candidate->serving_notice_period_mode ?? 0);
        if ($modeNp == 0) {
            $noticePeriodDays = (int) ($candidate->np_id ?? 0);
        } elseif ($modeNp == 1 && ! empty($candidate->np_id)) {
            $lwdTime = strtotime($candidate->np_id);
            if ($lwdTime && $lwdTime > time()) {
                $noticePeriodDays = (int) ceil(($lwdTime - time()) / 86400);
            }
        }

        return [
            'id' => $candidate->id,
            'company_id' => $candidate->company_id,
            'condidate_type' => 'bench_condidate',
            'full_name' => $candidate->full_name,
            'current_designation' => $candidate->current_designation,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'status' => (int) ($candidate->status ?? 2),
            'outsourcing_type' => (int) ($candidate->outsourcing_type ?? 1),
            'total_exp' => (float) ($candidate->total_exp ?? 0),
            'rel_exp' => (float) ($candidate->rel_exp ?? 0),
            'bill_rate_amount' => (float) ($candidate->bill_rate_amount ?? 0),
            'bill_rate_currency' => (int) ($candidate->bill_rate_currency ?? 0),
            'bill_rate_mode' => (int) ($candidate->bill_rate_mode ?? 0),
            'ectc_yearly' => (float) ($candidate->bill_rate_amount ?? 0),
            'np_id' => $candidate->np_id ?? '',
            'serving_notice_period_mode' => $modeNp,
            'city_name' => $candidate->city ?? '',
            'country_name' => $candidate->country ?? '',
            'qualification' => (int) ($candidate->qualification ?? 0),
            'updated_at' => $candidate->updated_at ?? now()->toDateTimeString(),

            // V2 Pre-computed sorting & nested skills fields
            'candidate_skills' => $skills,
            'skill_experiences' => $skills,
            'ads_switch_order' => $adsSwitchOrder,
            'ads_switch_rank' => $adsSwitchOrder,
            'np_epoch_day' => $npEpochDay,
            'np_is_immediate' => $npIsImmediate,
            'notice_period_days' => $noticePeriodDays,
            'np_days' => $noticePeriodDays,
        ];
    }

    /**
     * Helper to compute epoch day for notice period date string.
     */
    protected function calculateNpEpochDay(string $npId): int
    {
        $v = trim($npId);
        if (empty($v)) {
            return 999999999;
        }
        if (strtolower($v) === 'immediate') {
            return -999999998;
        }

        if (($comma = strpos($v, ',')) !== false) {
            $v = trim(substr($v, 0, $comma));
        }

        if (($t = strpos($v, 'T')) !== false) {
            $v = substr($v, 0, $t);
        }

        $v = str_replace('/', '-', $v);

        try {
            return Carbon::parse($v)->getTimestamp() / 86400;
        } catch (\Exception $e) {
            return 999999999;
        }
    }
}
