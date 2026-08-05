<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Cache;

class ClientObserver
{
    public function saved(Client $client): void
    {
        Cache::forget('tenant:' . $client->company_id . ':clients');
    }

    public function deleted(Client $client): void
    {
        Cache::forget('tenant:' . $client->company_id . ':clients');
    }
}
