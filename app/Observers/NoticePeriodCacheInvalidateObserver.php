<?php

namespace App\Observers;

use App\Models\NoticePeriod;
use Illuminate\Support\Facades\Cache;

class NoticePeriodCacheInvalidateObserver
{
    public function saved(NoticePeriod $n): void
    {
        Cache::forget('notice_periods');
    }

    public function deleted(NoticePeriod $n): void
    {
        Cache::forget('notice_periods');
    }
}
