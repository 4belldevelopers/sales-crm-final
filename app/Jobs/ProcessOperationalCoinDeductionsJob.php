<?php

/**
 * Runs daily coin deductions for ALL active companies.
 *
 * Moved from ServiceCompanyDashboardController::index() (V2 Phase 1).
 * Previously ran synchronously on every dashboard GET — now scheduled every
 * 5 minutes via Kernel so the HTTP handler stays write-free.
 *
 * Two deduction passes per company:
 *   1. Team deductions  — per active user (coin_event_id = 1)
 *   2. Job deductions   — per active job  (coin_event_id = 2)
 *
 * Tenancy: raw DB::table() is used throughout; company_id filter is explicit (Rule #3).
 * ES updates deliberately omit 'refresh' => true — 1-second natural refresh is sufficient.
 */

namespace App\Jobs;

use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOperationalCoinDeductionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public int $timeout = 300;

    public function handle(ElasticService $elasticService): void
    {
        $companies = DB::table('company_coins')
            ->where('event_type', 'operational')
            ->distinct()
            ->pluck('company_id');

        foreach ($companies as $companyId) {
            try {
                $this->deductTeamCoins((int) $companyId, $elasticService);
                $this->deductJobCoins((int) $companyId, $elasticService);
            } catch (\Throwable $e) {
                Log::error('ProcessOperationalCoinDeductionsJob: company failed', [
                    'company_id' => $companyId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /** Pass 1: deduct daily coins per active team member. */
    private function deductTeamCoins(int $companyId, ElasticService $elasticService): void
    {
        DB::transaction(function () use ($companyId, $elasticService) {
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

                DB::table('coin_transection_logs')->insert([
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
            }

            if (! empty($inactiveUserIdsToUpdate)) {
                DB::table('users')
                    ->whereIn('id', $inactiveUserIdsToUpdate)
                    ->update(['working_status' => 0]);

                foreach ($inactiveUserIdsToUpdate as $deactivatedUserId) {
                    $this->updateElasticUserStatus((int) $deactivatedUserId, 0, $elasticService);
                }
            }
        });
    }

    /** Pass 2: deduct daily coins per active job posting. */
    private function deductJobCoins(int $companyId, ElasticService $elasticService): void
    {
        DB::transaction(function () use ($companyId, $elasticService) {
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

                DB::table('coin_transection_logs')->insert([
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
            }

            if (! empty($inactiveJobIdsToUpdate)) {
                DB::table('jobs')
                    ->whereIn('id', $inactiveJobIdsToUpdate)
                    ->update(['status' => 'inactive']);

                foreach ($inactiveJobIdsToUpdate as $deactivatedJobId) {
                    $this->updateElasticJobStatus((int) $deactivatedJobId, 'inactive', $elasticService);
                }
            }
        });
    }

    private function updateElasticUserStatus(int $userId, int $status, ElasticService $elasticService): void
    {
        try {
            if (! $elasticService->getClient()->exists(['index' => ELASTIC_USERS_LIST, 'id' => $userId])) {
                return;
            }
            $elasticService->getClient()->update([
                'index' => ELASTIC_USERS_LIST,
                'id' => $userId,
                'body' => [
                    'doc' => ['working_status' => $status],
                    'doc_as_upsert' => false,
                ],
                // No 'refresh' => true — natural 1-second ES refresh is sufficient.
            ]);
        } catch (\Throwable $e) {
            Log::warning('ProcessOperationalCoinDeductionsJob: ES user update failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateElasticJobStatus(int $jobId, string $status, ElasticService $elasticService): void
    {
        try {
            if (! $elasticService->getClient()->exists(['index' => ELASTIC_JOBS, 'id' => $jobId])) {
                return;
            }
            $elasticService->getClient()->update([
                'index' => ELASTIC_JOBS,
                'id' => $jobId,
                'body' => [
                    'doc' => ['status' => $status],
                    'doc_as_upsert' => false,
                ],
                // No 'refresh' => true — natural 1-second ES refresh is sufficient.
            ]);
        } catch (\Throwable $e) {
            Log::warning('ProcessOperationalCoinDeductionsJob: ES job update failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
