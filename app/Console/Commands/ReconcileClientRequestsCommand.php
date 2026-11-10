<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ReconcileClientRequestsCommand
 *
 * Forensic reconciliation tool for Client Requests (staffing_company_invitation <-> Elasticsearch ELASTIC_CLIENT_REQUEST).
 * - Identifies and prunes orphan/ghost documents in Elasticsearch not existing in MySQL.
 * - Backfills missing expires_at timestamps on historical MySQL records (from_created_at + 7 days).
 * - Re-indexes authoritative records into Elasticsearch to maintain strict CQRS synchronization (Rule 10).
 * - Atomically invalidates tenant client version caches to eliminate stale ATS pipeline data (Rule 6).
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ReconcileClientRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:reconcile-requests
                            {--prune-orphans : Delete orphan ES documents not present in MySQL}
                            {--sync-all : Full sync of MySQL invitations to ES}
                            {--dry-run : Run in audit mode without modifying database or Elasticsearch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forensic audit, orphan pruning, and reconciliation of client request invitations in Elasticsearch and MySQL.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $pruneOrphans = (bool) $this->option('prune-orphans');
        $syncAll = (bool) $this->option('sync-all');

        $this->info('Starting Client Requests Reconciliation (Dry Run: '.($dryRun ? 'YES' : 'NO').')...');

        if (! Schema::hasTable('staffing_company_invitation')) {
            $this->error('Table staffing_company_invitation does not exist.');

            return 1;
        }

        $elasticService = app(ElasticService::class);
        if (! $elasticService->indexExists(ELASTIC_CLIENT_REQUEST)) {
            $this->warn('Elasticsearch index '.ELASTIC_CLIENT_REQUEST.' does not exist.');

            return 0;
        }

        // 1. Audit Elasticsearch documents
        $search = $elasticService->getClient()->search([
            'index' => ELASTIC_CLIENT_REQUEST,
            'body' => [
                'size' => 1000,
            ],
        ]);

        $hits = $search['hits']['hits'] ?? [];
        $totalEsDocs = count($hits);
        $this->info("Scanning {$totalEsDocs} Elasticsearch document(s) in ".ELASTIC_CLIENT_REQUEST.'...');

        $orphans = [];
        $validDocs = [];
        $affectedTenants = [];

        foreach ($hits as $hit) {
            $docId = (int) $hit['_id'];
            $src = $hit['_source'] ?? [];
            $toCompanyId = (int) ($src['to_company_id'] ?? 0);
            $fromCompanyId = (int) ($src['from_company_id'] ?? 0);

            $existsInDb = DB::table('staffing_company_invitation')->where('id', $docId)->exists();
            if (! $existsInDb) {
                $orphans[] = [
                    'id' => $docId,
                    'from_company_id' => $fromCompanyId,
                    'to_company_id' => $toCompanyId,
                    'company_name' => $src['company_name'] ?? 'Unknown',
                    'to_email' => $src['to_email'] ?? '',
                ];
                if ($toCompanyId > 0) {
                    $affectedTenants[$toCompanyId] = true;
                }
                if ($fromCompanyId > 0) {
                    $affectedTenants[$fromCompanyId] = true;
                }
            } else {
                $validDocs[] = $docId;
                if ($toCompanyId > 0) {
                    $affectedTenants[$toCompanyId] = true;
                }
            }
        }

        $this->info('Found '.count($orphans).' orphan/ghost Elasticsearch document(s) not in MySQL.');
        foreach ($orphans as $o) {
            $this->line("   - Orphan ES ID: {$o['id']} | Client: {$o['company_name']} | From: {$o['from_company_id']} -> To: {$o['to_company_id']} ({$o['to_email']})");
        }

        // 2. Prune orphans if requested
        if (! empty($orphans) && ($pruneOrphans || ! $dryRun)) {
            $this->warn('Pruning '.count($orphans).' orphan document(s) from Elasticsearch index '.ELASTIC_CLIENT_REQUEST.'...');
            foreach ($orphans as $o) {
                try {
                    $elasticService->getClient()->delete([
                        'index' => ELASTIC_CLIENT_REQUEST,
                        'id' => (string) $o['id'],
                    ]);
                    $this->line("   ✓ Deleted orphan ES document {$o['id']}");
                } catch (\Throwable $e) {
                    $this->error("   ✗ Failed to delete {$o['id']}: ".$e->getMessage());
                }
            }
        }

        // 3. Backfill missing expires_at in MySQL
        $missingExpiryRows = DB::table('staffing_company_invitation')
            ->whereNull('expires_at')
            ->whereNotNull('from_created_at')
            ->get(['id', 'from_created_at', 'to_company_id']);

        $this->info('Found '.$missingExpiryRows->count().' MySQL invitation(s) with missing expires_at.');
        if ($missingExpiryRows->count() > 0 && ! $dryRun) {
            foreach ($missingExpiryRows as $row) {
                $computedExpiry = Carbon::parse($row->from_created_at)->addDays(7)->toDateTimeString();
                DB::table('staffing_company_invitation')
                    ->where('id', $row->id)
                    ->update(['expires_at' => $computedExpiry]);

                if (! empty($row->to_company_id)) {
                    $affectedTenants[(int) $row->to_company_id] = true;
                }
            }
            $this->info('✓ Successfully backfilled expires_at on '.$missingExpiryRows->count().' records.');
        }

        // 4. Sync authoritative MySQL records to Elasticsearch
        if ($syncAll || ! $dryRun) {
            $dbInvitations = DB::table('staffing_company_invitation')
                ->whereNotNull('to_company_id')
                ->get(['id', 'to_company_id']);

            $this->info('Re-indexing authoritative invitations to Elasticsearch...');
            $syncedCount = 0;
            foreach ($dbInvitations as $inv) {
                try {
                    GeneralMethod::clientRequestIndexElastic($inv->id);
                    $syncedCount++;
                    if (! empty($inv->to_company_id)) {
                        $affectedTenants[(int) $inv->to_company_id] = true;
                    }
                } catch (\Throwable $e) {
                    // Ignore non-fatal indexing errors on isolated items
                }
            }
            $this->info("✓ Successfully synced {$syncedCount} invitation(s) to Elasticsearch.");
        }

        // 5. Invalidate tenant caches
        if (! $dryRun) {
            foreach (array_keys($affectedTenants) as $tenantId) {
                ServiceCompanyClientController::invalidateTenantClientCache($tenantId);
            }
            $this->info('✓ Invalidated client cache for '.count($affectedTenants).' affected tenant(s).');
        }

        $this->info('Reconciliation complete.');

        return 0;
    }
}
