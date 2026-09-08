<?php

namespace App\Observers;

use App\Helper\GeneralMethod;
use App\Jobs\UpdateJobAgencyCounterJob;
use App\Models\RecommendationRelation;

/**
 * Keeps job_agency_counter rec_sent/accepted/rejected in sync with recommendation_relation changes.
 * Also invalidates the client/custom requisition list caches for both companies involved.
 */
class RecommendationRelationObserver
{
    public function saved(RecommendationRelation $rec): void
    {
        UpdateJobAgencyCounterJob::dispatch($rec->job_id, $rec->to_company_id ?? $rec->from_company_id);
        $this->bustListCaches($rec);
    }

    public function deleted(RecommendationRelation $rec): void
    {
        UpdateJobAgencyCounterJob::dispatch($rec->job_id, $rec->to_company_id ?? $rec->from_company_id);
        $this->bustListCaches($rec);
    }

    private function bustListCaches(RecommendationRelation $rec): void
    {
        foreach (array_filter([$rec->to_company_id, $rec->from_company_id]) as $cid) {
            GeneralMethod::cacheClean("allclientrequisition_version_company_{$cid}");
            GeneralMethod::cacheClean("allcustomrequisition_version_company_{$cid}");
            \Illuminate\Support\Facades\Cache::forget("ats:spartner_summary_cnt:comp_{$cid}");
        }
    }
}
