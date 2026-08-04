<?php

namespace App\Observers;

use App\Jobs\UpdateJobAgencyCounterJob;
use App\Models\RecommendationRelation;

/**
 * Keeps job_agency_counter rec_sent/accepted/rejected in sync with recommendation_relation changes.
 */
class RecommendationRelationObserver
{
    public function saved(RecommendationRelation $rec): void
    {
        UpdateJobAgencyCounterJob::dispatch($rec->job_id, $rec->to_company_id ?? $rec->from_company_id);
    }

    public function deleted(RecommendationRelation $rec): void
    {
        UpdateJobAgencyCounterJob::dispatch($rec->job_id, $rec->to_company_id ?? $rec->from_company_id);
    }
}
