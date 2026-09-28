<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class PruneIdempotencyKeysCommand
 *
 * Intent: Scheduled hourly pruning command per docs/drawer_candidate.md.
 * Deletes records where expires_at < NOW(). Expired keys arriving later are processed as brand-new requests.
 */
class PruneIdempotencyKeysCommand extends Command
{
    protected $signature = 'idempotency:prune';

    protected $description = 'Prune expired sys_idempotency_keys records';

    public function handle(): int
    {
        $deleted = DB::table('sys_idempotency_keys')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Successfully pruned {$deleted} expired idempotency keys.");

        return 0;
    }
}
