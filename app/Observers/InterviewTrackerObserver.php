<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

/**
 * InterviewTrackerObserver
 *
 * INTENT: Invalidates company summary counter cache and user drawer cache on interview schedule events.
 * TENANCY: Scoped by from_company_id and to_company_id.
 */
class InterviewTrackerObserver
{
    public function saved($interview): void
    {
        $this->bustCache($interview);
    }

    public function deleted($interview): void
    {
        $this->bustCache($interview);
    }

    protected function bustCache($interview): void
    {
        $companyIds = array_filter([
            $interview->from_company_id ?? null,
            $interview->to_company_id ?? null,
        ]);

        foreach ($companyIds as $cid) {
            Cache::forget("ats:spartner_summary_cnt:comp_{$cid}");
        }
    }
}
