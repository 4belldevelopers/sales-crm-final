<?php

namespace App\Console\Commands;

use App\Services\JobApplicationCapacityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ReconcileJobApplicationCapacityCommand — Authoritative Capacity Synchronization Command.
 *
 * Intent:
 * Reconciles the Redis atomic application capacity counter and circuit-breaker closed flag
 * with authoritative MySQL application counts. Used by scheduled maintenance, operations,
 * or administrators to guarantee counter integrity without manual database patching.
 *
 * Tenancy:
 * Operates per individual job_id across tenants.
 *
 * Author: Antigravity AI
 * Date: October 09, 2026
 */
class ReconcileJobApplicationCapacityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'career:reconcile-capacity 
                            {job_id? : Specific Job ID to reconcile}
                            {--all : Reconcile all active jobs in the system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconciles Redis application capacity counters with authoritative MySQL counts';

    /**
     * Execute the console command.
     */
    public function handle(JobApplicationCapacityService $capacityService): int
    {
        $jobId = $this->argument('job_id');
        $all = $this->option('all');

        if ($jobId) {
            $this->info("Reconciling application capacity for Job ID: {$jobId}...");
            $res = $capacityService->reconcileCount((int) $jobId);

            $this->table(
                ['Job ID', 'MySQL Count', 'Redis Count', 'Status', 'Max Limit'],
                [[
                    $res['job_id'],
                    $res['db_count'],
                    $res['redis_count'] ?? 'N/A',
                    $res['is_closed'] ? 'CLOSED (Full)' : 'OPEN',
                    $res['max'],
                ]]
            );

            return Command::SUCCESS;
        }

        if (! $all && ! $jobId) {
            $this->comment('Please specify a {job_id} or pass --all to reconcile active jobs.');

            return Command::INVALID;
        }

        $this->info('Scanning jobs for capacity reconciliation...');

        // Query active jobs or jobs with existing applications
        $jobIds = DB::table('jobs')
            ->select('id')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        if (empty($jobIds)) {
            $this->warn('No active jobs found to reconcile.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($jobIds));
        $bar->start();

        $rows = [];
        foreach ($jobIds as $id) {
            $res = $capacityService->reconcileCount((int) $id);
            if ($res['db_count'] > 0 || $res['is_closed']) {
                $rows[] = [
                    $res['job_id'],
                    $res['db_count'],
                    $res['redis_count'] ?? 'N/A',
                    $res['is_closed'] ? 'CLOSED (Full)' : 'OPEN',
                    $res['max'],
                ];
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if (! empty($rows)) {
            $this->table(
                ['Job ID', 'MySQL Count', 'Redis Count', 'Status', 'Max Limit'],
                $rows
            );
        }

        $this->info('Capacity reconciliation complete. Total jobs processed: '.count($jobIds));

        return Command::SUCCESS;
    }
}
