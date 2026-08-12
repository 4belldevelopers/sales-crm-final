<?php

namespace App\Console\Commands;

use App\Services\ElasticCandidatesIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ReindexPlacedCandidatesDenormalizedCommand
 *
 * Artisan command to bulk re-index placed and joined candidates (job_master_status_id = 6)
 * into Elasticsearch (ELASTIC_CANDIDATES) with Phase 1 denormalized fields including
 * spokesperson name, offer release date, sourcing/evaluation stage dates, and numeric notice period sort fields.
 */
class ReindexPlacedCandidatesDenormalizedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:reindex-placed-denormalized
                            {--company_id= : Optional company ID filter}
                            {--chunk=100 : Chunk size for batch processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index placed and joined candidates into Elasticsearch with denormalized V2 fields';

    /**
     * Execute the console command.
     *
     * @param ElasticCandidatesIndex $indexer
     * @return int
     */
    public function handle(ElasticCandidatesIndex $indexer)
    {
        $companyId = $this->option('company_id');
        $chunkSize = (int) $this->option('chunk');

        $this->info('Starting placed candidates denormalization re-indexing...');

        // Query placed/joined candidate records (master_status_id = 6 or job_applications.status = 3)
        // Multi-tenant scoped by company_id when company_id option is supplied
        $query = DB::table('add_job_wise_condidates')
            ->select('add_job_wise_condidates.id')
            ->leftJoin('job_applications', 'job_applications.id', 'add_job_wise_condidates.job_application_id')
            ->where(function ($q) {
                $q->where('add_job_wise_condidates.master_status_id', 6)
                  ->orWhere('job_applications.status', 3);
            });

        if (!empty($companyId)) {
            $query->where('add_job_wise_condidates.company_id', $companyId);
        }

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->warn('No placed candidate records found for re-indexing.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalCount} placed candidate records to process.");
        $progressBar = $this->output->createProgressBar($totalCount);
        $progressBar->start();

        $processed = 0;
        $failed = 0;

        $query->orderBy('add_job_wise_condidates.id')->chunk($chunkSize, function ($rows) use ($indexer, $progressBar, &$processed, &$failed) {
            foreach ($rows as $row) {
                try {
                    $indexer->candidateIndexElastic($row->id);
                    $processed++;
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to reindex placed candidate wiseId={$row->id}: {$e->getMessage()}");
                }
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->line('');
        $this->info("Re-indexing completed. Processed: {$processed}, Failed: {$failed}");

        return Command::SUCCESS;
    }
}
