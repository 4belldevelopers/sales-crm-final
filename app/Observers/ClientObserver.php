<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Cache;

class ClientObserver
{
    /**
     * Defer observer executions until active database transaction has committed.
     */
    public bool $afterCommit = true;

    public function saved(Client $client): void
    {
        Cache::forget('tenant:' . $client->company_id . ':clients');

        if (class_exists(\App\Jobs\SyncClientToElasticJob::class)) {
            \App\Jobs\SyncClientToElasticJob::dispatch((int) $client->id)->afterCommit();
        }
    }

    public function deleted(Client $client): void
    {
        Cache::forget('tenant:' . $client->company_id . ':clients');

        if (class_exists(\App\Jobs\SyncClientToElasticJob::class)) {
            \App\Jobs\SyncClientToElasticJob::dispatch((int) $client->id)->afterCommit();
        }
    }
}
