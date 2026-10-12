<?php

namespace App\Observers;

use App\Events\ClientMutatedEvent;
use App\Jobs\SyncClientToElasticJob;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class ClientObserver
 *
 * Intent: Maintain multi-tenant cache invalidation and Reverb WebSocket notification bus
 * whenever clients are added, updated, renamed, or deleted.
 * Tenancy Note: Scoped strictly by company_id.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ClientObserver
{
    /**
     * Defer observer executions until active database transaction has committed.
     */
    public bool $afterCommit = true;

    public function saved(Client $client): void
    {
        $companyId = (int) $client->company_id;
        $prevVersion = (int) Cache::get('tenant:'.$companyId.':clients_version', 1);
        $newVersion = $prevVersion + 1;
        Cache::put('tenant:'.$companyId.':clients_version', $newVersion, 86400 * 30);

        // Purge Redis cached read manifests
        Cache::forget('tenant:'.$companyId.':clients');
        Cache::forget('tenant:'.$companyId.':clients_bundle');
        Cache::forget('tenant:'.$companyId.':clients_index');
        Cache::forget('tenant:'.$companyId.':dataset:clients:perm:default');
        Cache::forget('tenant:'.$companyId.':client_details:'.$client->id);

        // Sync to Elasticsearch derived read model off-thread
        if (class_exists(SyncClientToElasticJob::class)) {
            SyncClientToElasticJob::dispatch((int) $client->id)->afterCommit();
        }

        // Determine granular operational signal
        $operation = $client->wasRecentlyCreated ? 'created' : ($client->isDirty('name') ? 'renamed' : 'updated');

        // Broadcast lightweight invalidation packet with version fencing
        try {
            event(new ClientMutatedEvent(
                $companyId,
                (int) $client->id,
                $operation,
                $newVersion,
                $prevVersion,
                [
                    'id' => (int) $client->id,
                    'n' => (string) $client->name,
                    't' => (int) $client->self_company,
                    's' => (int) ($client->status ?? 1),
                ]
            ));
        } catch (\Throwable $e) {
            Log::warning('ClientObserver Reverb broadcast notice: '.$e->getMessage());
        }
    }

    public function deleted(Client $client): void
    {
        $companyId = (int) $client->company_id;
        $prevVersion = (int) Cache::get('tenant:'.$companyId.':clients_version', 1);
        $newVersion = $prevVersion + 1;
        Cache::put('tenant:'.$companyId.':clients_version', $newVersion, 86400 * 30);

        Cache::forget('tenant:'.$companyId.':clients');
        Cache::forget('tenant:'.$companyId.':clients_bundle');
        Cache::forget('tenant:'.$companyId.':clients_index');
        Cache::forget('tenant:'.$companyId.':dataset:clients:perm:default');
        Cache::forget('tenant:'.$companyId.':client_details:'.$client->id);

        if (class_exists(SyncClientToElasticJob::class)) {
            SyncClientToElasticJob::dispatch((int) $client->id)->afterCommit();
        }

        try {
            event(new ClientMutatedEvent(
                $companyId,
                (int) $client->id,
                'deleted',
                $newVersion,
                $prevVersion,
                [
                    'id' => (int) $client->id,
                    's' => 0,
                ]
            ));
        } catch (\Throwable $e) {
            Log::warning('ClientObserver Reverb broadcast notice: '.$e->getMessage());
        }
    }
}
