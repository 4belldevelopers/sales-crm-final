<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishMonthlyLeaderboardCommand
 *
 * Intent: Calculate, persist, and publish authoritative previous calendar month leaderboard
 * data for all active companies by the configured evening cutoff (target: 6:00 PM IST).
 *
 * Prevents live recalculations on user page loads, guarantees strict data synchronization
 * between Team Member and Company Profile pages, and ensures safe, idempotent monthly execution.
 */
class PublishMonthlyLeaderboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:publish-monthly
                            {--month= : The target period month in YYYY-MM format (e.g. 2026-10; defaults to previous calendar month)}
                            {--company_id= : Target specific company ID}
                            {--force : Force recalculation and overwrite even if already published for this month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and publish authoritative previous calendar month leaderboard data';

    /**
     * Execute the console command.
     */
    public function handle(LeaderboardService $leaderboardService): int
    {
        $targetMonth = $this->option('month');
        $companyId = $this->option('company_id') ? (int) $this->option('company_id') : null;
        $force = (bool) $this->option('force');

        $range = $leaderboardService->getPreviousMonthRange();
        if (empty($targetMonth)) {
            $targetMonth = $range['period'];
        }

        $nowIst = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s T');
        $this->info("=================================================================");
        $this->info(" JERO ATS - Monthly Leaderboard Publisher");
        $this->info(" Reporting Period: {$targetMonth} (Previous Calendar Month)");
        $this->info(" Execution Time:   {$nowIst} (Evening Target: 18:00 IST)");
        $this->info(" Target Scope:     " . ($companyId ? "Company #{$companyId}" : "All Active Companies"));
        $this->info(" Force Overwrite:  " . ($force ? "YES" : "NO (Idempotent Duplicate Guard Active)"));
        $this->info("=================================================================");

        Log::info("PublishMonthlyLeaderboardCommand started", [
            'period_month' => $targetMonth,
            'company_id' => $companyId,
            'force' => $force,
            'time' => $nowIst,
        ]);

        try {
            $result = $leaderboardService->publishMonthlyLeaderboard($targetMonth, $companyId, $force);

            if (isset($result['error'])) {
                $this->error("Failed to execute leaderboard publishing: {$result['error']}");
                return Command::FAILURE;
            }

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Reporting Period', $result['period_month']],
                    ['Total Companies Evaluated', $result['total_companies']],
                    ['Published (Processed)', $result['processed']],
                    ['Skipped (Already Published)', $result['skipped']],
                    ['Failed', $result['failed']],
                    ['Execution Duration', ($result['duration_ms'] ?? 0) . ' ms'],
                ]
            );

            if (!empty($result['details'])) {
                $rows = [];
                foreach ($result['details'] as $cId => $info) {
                    $rows[] = [
                        $cId,
                        $info['members'],
                        $info['top_user'],
                        $info['top_score'],
                    ];
                }
                $this->info("\nPublished Company Sample:");
                $this->table(['Company ID', 'Members Evaluated', 'Rank #1 Recruiter', 'Top Score'], array_slice($rows, 0, 10));
            }

            $this->info("Leaderboard publishing workflow completed successfully.");
            Log::info("PublishMonthlyLeaderboardCommand finished successfully", $result);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Fatal error during monthly leaderboard processing: " . $e->getMessage());
            Log::error("PublishMonthlyLeaderboardCommand fatal error: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Command::FAILURE;
        }
    }
}
