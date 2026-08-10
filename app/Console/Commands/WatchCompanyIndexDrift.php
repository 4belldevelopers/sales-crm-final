<?php

/**
 * WatchCompanyIndexDrift — V2 Registration Sprint 5, task 5.7.
 *
 * ES drift watcher: compares MySQL companies.id against ats_companies ES ids and
 * re-queues any rows missing from ES. Intended to run every 5 minutes via Horizon
 * scheduler or artisan schedule:run.
 *
 * Schedule in app/Console/Kernel.php (if using the old scheduler):
 *   $schedule->command('drift:watch-companies')->everyFiveMinutes()->withoutOverlapping();
 */

namespace App\Console\Commands;

use App\Jobs\ElasticCompanyIndex;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WatchCompanyIndexDrift extends Command
{
    protected $signature   = 'drift:watch-companies
                                {--dry-run : List missing companies without re-queuing}
                                {--limit=1000 : Maximum companies to check per run}';

    protected $description = 'Compare MySQL companies against ES ats_companies and re-index any missing rows';

    public function handle(ElasticService $es): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        // Fetch all company IDs from MySQL (active companies only).
        $mysqlIds = DB::table('companies')
            ->whereNull('delete_account_at')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id')
            ->all();

        if (empty($mysqlIds)) {
            $this->info('No companies to check.');
            return self::SUCCESS;
        }

        // Fetch matching IDs from ES using a terms query.
        try {
            $esResponse = $es->getClient()->search([
                'index' => defined('ELASTIC_COMPANIES') ? ELASTIC_COMPANIES : 'ats_companies',
                'body'  => [
                    'size'  => count($mysqlIds),
                    'query' => ['terms' => ['id' => $mysqlIds]],
                    '_source' => false,
                ],
            ]);

            $esIds = array_column($esResponse['hits']['hits'], '_id');
            $esIds = array_map('intval', $esIds);
        } catch (\Throwable $e) {
            $this->error('ES query failed: ' . $e->getMessage());
            Log::error('[drift:watch-companies] ES query failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        $missing = array_values(array_diff($mysqlIds, $esIds));

        if (empty($missing)) {
            $this->info('No drift found — all ' . count($mysqlIds) . ' companies are indexed.');
            return self::SUCCESS;
        }

        $this->warn(count($missing) . ' company/ies missing from ES: ' . implode(', ', array_slice($missing, 0, 20))
            . (count($missing) > 20 ? '…' : ''));

        if ($dryRun) {
            $this->info('[dry-run] Would re-queue ' . count($missing) . ' company/ies.');
            return self::SUCCESS;
        }

        foreach ($missing as $id) {
            ElasticCompanyIndex::dispatch($id)->onConnection('emails');
        }

        Log::warning('[drift:watch-companies] Re-queued missing companies', ['ids' => $missing]);
        $this->info('Re-queued ' . count($missing) . ' company/ies for ES indexing.');

        return self::SUCCESS;
    }
}
