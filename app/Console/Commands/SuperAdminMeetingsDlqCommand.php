<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMeetingOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMeetingsDlqCommand
 *
 * Intent: Inspect, retry, or purge Dead-Letter Queue (DLQ) transactional outbox deliveries
 * for the Super Admin Meeting Tracker subsystem.
 *
 * Signature: php artisan superadmin:meetings:dlq {--retry : Retry failed DLQ deliveries} {--purge : Purge dead-letter records} {--id= : Specific outbox delivery ID}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingsDlqCommand extends Command
{
    protected $signature = 'superadmin:meetings:dlq {--retry : Retry failed DLQ deliveries} {--purge : Purge dead-letter records} {--id= : Specific outbox delivery ID}';

    protected $description = 'Inspect, retry, or purge Dead-Letter Queue (DLQ) outbox deliveries for meetings';

    public function handle(): int
    {
        $id = $this->option('id');
        $retry = (bool) $this->option('retry');
        $purge = (bool) $this->option('purge');

        $query = DB::table('sys_outbox_deliveries')
            ->where('status', 'DEAD_LETTER')
            ->whereIn('outbox_id', function ($q) {
                $q->select('id')->from('sys_outbox_events')->where('aggregate_type', 'MeetingSchedule');
            });

        if ($id) {
            $query->where('id', $id);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info('No DLQ records found for Super Admin Meetings.');

            return Command::SUCCESS;
        }

        $this->warn("Found {$records->count()} Dead-Letter Queue deliveries.");

        if ($purge) {
            $deleted = $query->delete();
            $this->info("Purged {$deleted} DLQ delivery records.");

            return Command::SUCCESS;
        }

        if ($retry) {
            $requeued = 0;
            foreach ($records as $rec) {
                DB::table('sys_outbox_deliveries')
                    ->where('id', $rec->id)
                    ->update([
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'last_error' => null,
                        'updated_at' => now(),
                    ]);

                dispatch(new ProcessMeetingOutboxEventJob((int) $rec->outbox_id));
                $requeued++;
            }

            $this->info("Re-queued {$requeued} outbox deliveries for reprocessing.");

            return Command::SUCCESS;
        }

        // Display inspection table
        $rows = [];
        foreach ($records as $r) {
            $rows[] = [
                $r->id,
                $r->outbox_id,
                $r->destination,
                $r->attempts,
                substr((string) $r->last_error, 0, 50),
                $r->created_at,
            ];
        }

        $this->table(['ID', 'Outbox ID', 'Destination', 'Attempts', 'Last Error', 'Created At'], $rows);

        return Command::SUCCESS;
    }
}
