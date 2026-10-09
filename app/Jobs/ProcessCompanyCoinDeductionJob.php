<?php

namespace App\Jobs;

use App\Services\CoinOutboxService;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProcessCompanyCoinDeductionJob — V2 Coins Engine Per-Tenant Queued Job.
 *
 * Intent: Executes daily team member and job posting operational coin deductions
 * for a single isolated tenant company ($companyId) on the 'coins' queue.
 * Ensures failure or delay in one tenant company does not affect other system tenants.
 *
 * Tenancy Note: Strictly filters all operations by tenant company_id parameter.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class ProcessCompanyCoinDeductionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $companyId;

    public int $tries = 3;

    public int $backoff = 60;

    public int $timeout = 60;

    /**
     * Create a new job instance for a specific tenant company.
     *
     * @param  int  $companyId  Target tenant company ID.
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
        $this->onQueue('coins');
    }

    /**
     * Execute the per-company daily operational coin deduction passes.
     */
    public function handle(ElasticService $elasticService): void
    {
        try {
            $this->deductTeamCoins($this->companyId, $elasticService);
            $this->deductJobCoins($this->companyId, $elasticService);
        } catch (\Throwable $e) {
            Log::error('ProcessCompanyCoinDeductionJob: company deduction failed', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /** Pass 1: deduct daily coins per active team member for this company. */
    private function deductTeamCoins(int $companyId, ElasticService $elasticService): void
    {
        DB::transaction(function () use ($companyId) {
            $companyCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', 'operational')
                ->lockForUpdate()
                ->first();

            if (! $companyCoin) {
                return;
            }

            $coinOrderEvent = DB::table('coin_order_events')
                ->where('company_id', $companyId)
                ->where('coin_event_id', 1)
                ->where('deduction_type', 'daily')
                ->where('event_type', $companyCoin->event_type)
                ->where('coin_order_id', $companyCoin->coin_order_id)
                ->first();

            if (! $coinOrderEvent) {
                return;
            }

            $today = Carbon::today();
            $usersData = DB::table('users')->where('company_id', $companyId)->where('status', 'active')->get();

            $activeUserIdsToUpdate = [];
            $inactiveUserIdsToUpdate = [];
            $totalDeductionRequired = 0;
            $currentBalance = (float) $companyCoin->coins;

            foreach ($usersData as $user) {
                $diffInDays = (int) ceil(abs($today->diffInDays(Carbon::parse($user->auto_date_team))));
                if ($diffInDays <= 0) {
                    continue;
                }

                $deduction = $diffInDays * $coinOrderEvent->deductions;

                if ($currentBalance >= $deduction) {
                    $currentBalance -= $deduction;
                    $totalDeductionRequired += $deduction;
                    $activeUserIdsToUpdate[] = $user->id;
                } else {
                    if ((int) $user->root_user !== 1) {
                        $inactiveUserIdsToUpdate[] = $user->id;
                    }
                }
            }

            if (! empty($activeUserIdsToUpdate)) {
                DB::table('company_coins')
                    ->where('company_id', $companyId)
                    ->where('event_type', 'operational')
                    ->update(['coins' => $currentBalance, 'auto_team_coins' => $today, 'updated_at' => now()]);

                DB::table('users')
                    ->whereIn('id', $activeUserIdsToUpdate)
                    ->update(['working_status' => 1, 'auto_date_team' => $today]);

                $logId = DB::table('coin_transection_logs')->insertGetId([
                    'coin_order_id' => $companyCoin->coin_order_id,
                    'from_company_id' => $companyId,
                    'company_id' => $companyId,
                    'event_by' => 0,
                    'event' => 'Team Manage Auto Deductions',
                    'amount' => '',
                    'discount' => '',
                    'coins' => $totalDeductionRequired,
                    'remain_coins' => $currentBalance,
                    'transection_type' => 'Debit',
                    'settlement_coins' => 0,
                    'balance_coins' => 0,
                    'actual_coins' => $companyCoin->coins,
                    'event_type' => 'operational',
                    'team_id' => '',
                    'job_id' => '',
                    'start_date' => null,
                    'end_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Transactional Outbox: durable outbox log and decoupled Reverb broadcast
                CoinOutboxService::recordAndDispatch(
                    companyId: $companyId,
                    logId: (int) $logId,
                    eventType: 'operational',
                    transectionType: 'debit',
                    coins: (float) $totalDeductionRequired,
                    balanceCoins: (float) $currentBalance,
                    eventDescription: 'Team Manage Auto Deductions'
                );
            }

            if (! empty($inactiveUserIdsToUpdate)) {
                DB::table('users')
                    ->whereIn('id', $inactiveUserIdsToUpdate)
                    ->update(['working_status' => 0]);

                foreach ($inactiveUserIdsToUpdate as $deactivatedUserId) {
                    \App\Events\TenantEntityDeactivatedDueToCoins::dispatch($companyId, (int) $deactivatedUserId, 'user', 0);
                }
            }
        });
    }

    /** Pass 2: deduct daily coins per active job posting for this company. */
    private function deductJobCoins(int $companyId, ElasticService $elasticService): void
    {
        DB::transaction(function () use ($companyId) {
            $companyCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', 'operational')
                ->lockForUpdate()
                ->first();

            if (! $companyCoin) {
                return;
            }

            $coinOrderEvent = DB::table('coin_order_events')
                ->where('deduction_type', 'daily')
                ->where('company_id', $companyId)
                ->where('coin_event_id', 2)
                ->where('event_type', $companyCoin->event_type)
                ->where('coin_order_id', $companyCoin->coin_order_id)
                ->first();

            if (! $coinOrderEvent) {
                return;
            }

            $today = Carbon::today();
            $jobsData = DB::table('jobs')->where('company_id', $companyId)->where('status', 'active')->get();

            $activeJobIdsToUpdate = [];
            $inactiveJobIdsToUpdate = [];
            $totalDeductionRequired = 0;
            $currentBalance = (float) $companyCoin->coins;

            foreach ($jobsData as $job) {
                $diffInDays = (int) ceil(abs($today->diffInDays(Carbon::parse($job->auto_date_job))));
                if ($diffInDays <= 0) {
                    continue;
                }

                $deduction = $diffInDays * $coinOrderEvent->deductions;

                if ($currentBalance >= $deduction) {
                    $currentBalance -= $deduction;
                    $totalDeductionRequired += $deduction;
                    $activeJobIdsToUpdate[] = $job->id;
                } else {
                    $inactiveJobIdsToUpdate[] = $job->id;
                }
            }

            if (! empty($activeJobIdsToUpdate)) {
                DB::table('company_coins')
                    ->where('company_id', $companyId)
                    ->where('event_type', 'operational')
                    ->update(['coins' => $currentBalance, 'auto_job_coins' => $today, 'updated_at' => now()]);

                DB::table('jobs')
                    ->whereIn('id', $activeJobIdsToUpdate)
                    ->update(['auto_date_job' => $today]);

                $logId = DB::table('coin_transection_logs')->insertGetId([
                    'coin_order_id' => $companyCoin->coin_order_id,
                    'from_company_id' => $companyId,
                    'company_id' => $companyId,
                    'event_by' => 0,
                    'event' => 'Jobs Manage Auto Deductions',
                    'amount' => '',
                    'discount' => '',
                    'remain_coins' => $currentBalance,
                    'coins' => $totalDeductionRequired,
                    'transection_type' => 'Debit',
                    'settlement_coins' => 0,
                    'balance_coins' => 0,
                    'actual_coins' => $companyCoin->coins,
                    'event_type' => 'operational',
                    'team_id' => '',
                    'job_id' => '',
                    'start_date' => null,
                    'end_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Transactional Outbox: durable outbox log and decoupled Reverb broadcast
                CoinOutboxService::recordAndDispatch(
                    companyId: $companyId,
                    logId: (int) $logId,
                    eventType: 'operational',
                    transectionType: 'debit',
                    coins: (float) $totalDeductionRequired,
                    balanceCoins: (float) $currentBalance,
                    eventDescription: 'Jobs Manage Auto Deductions'
                );
            }

            if (! empty($inactiveJobIdsToUpdate)) {
                DB::table('jobs')
                    ->whereIn('id', $inactiveJobIdsToUpdate)
                    ->update(['status' => 'inactive']);

                foreach ($inactiveJobIdsToUpdate as $deactivatedJobId) {
                    \App\Events\TenantEntityDeactivatedDueToCoins::dispatch($companyId, (int) $deactivatedJobId, 'job', 'inactive');
                }
            }
        });
    }
}
