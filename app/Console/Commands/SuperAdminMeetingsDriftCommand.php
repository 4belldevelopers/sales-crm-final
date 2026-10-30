<?php

namespace App\Console\Commands;

use App\Services\SuperAdminMeetingScheduleCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMeetingsDriftCommand
 *
 * Intent: Forensic data drift detection and reconciler between MySQL meeting records,
 * comments count, and active Redis caches.
 *
 * Signature: php artisan superadmin:meetings:drift {--dry-run} {--heal} {--limit=100}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingsDriftCommand extends Command
{
    protected $signature = 'superadmin:meetings:drift {--dry-run : Only detect drift without healing} {--heal : Automatically reconcile discovered drifts} {--limit=100 : Maximum records to audit}';

    protected $description = 'Detect and reconcile data drift across Super Admin meeting schedules, comments, and caches';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $heal = (bool) $this->option('heal');
        $dryRun = (bool) $this->option('dry-run') || (! $heal);

        $this->info("Starting Super Admin Meetings Drift Audit (limit: {$limit}, mode: ".($dryRun ? 'DRY-RUN' : 'HEAL').')...');

        $meetings = DB::table('meeting_schedules')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $driftsFound = 0;
        $healedCount = 0;

        foreach ($meetings as $meeting) {
            $meetingId = (int) $meeting->id;
            $actualCommentsCount = DB::table('meeting_comments')
                ->where('meeting_schedule_id', $meetingId)
                ->count();

            // Check if delta in cache has drifted count
            $delta = SuperAdminMeetingScheduleCacheManager::getMutationDelta($meetingId);
            if ($delta && isset($delta['comment_count']) && (int) $delta['comment_count'] !== $actualCommentsCount) {
                $driftsFound++;
                $this->warn("Drift detected on Meeting #{$meetingId}: Cache delta comments = {$delta['comment_count']}, DB truth = {$actualCommentsCount}");

                if ($heal) {
                    SuperAdminMeetingScheduleCacheManager::invalidateAll($meetingId);
                    $healedCount++;
                }
            }
        }

        $this->newLine();
        $this->info("Drift audit complete. Total Audited: {$meetings->count()}, Drifts Found: {$driftsFound}, Healed: {$healedCount}");

        return Command::SUCCESS;
    }
}
