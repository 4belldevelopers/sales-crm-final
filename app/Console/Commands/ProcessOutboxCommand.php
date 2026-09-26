<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOutboxEventJob;
use Illuminate\Console\Command;

/**
 * Intent: Command line trigger to process pending candidate outbox events.
 */
class ProcessOutboxCommand extends Command
{
    protected $signature = 'candidates:process-outbox {--limit=50}';
    protected $description = 'Process pending candidate transactional outbox events';

    public function handle(): int
    {
        $this->info('Processing candidate outbox events...');
        dispatch_sync(new ProcessOutboxEventJob());
        $this->info('Candidate outbox events processed successfully.');
        return 0;
    }
}
