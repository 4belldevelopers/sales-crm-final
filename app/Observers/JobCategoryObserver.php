<?php

namespace App\Observers;

use App\Events\DepartmentMutatedEvent;
use App\Models\JobCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class JobCategoryObserver
 *
 * Intent: Ensures multi-tenant isolation, cache invalidation, and real-time Reverb broadcast
 * whenever a Job Department (JobCategory) is created, updated, or deleted.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class JobCategoryObserver
{
    public function saving(JobCategory $jobCategory): void
    {
        if (company() && empty($jobCategory->company_id)) {
            $jobCategory->company_id = company()->id;
        }
    }

    public function saved(JobCategory $jobCategory): void
    {
        $this->handleInvalidation($jobCategory, $jobCategory->wasRecentlyCreated ? 'created' : 'updated');
    }

    public function deleted(JobCategory $jobCategory): void
    {
        $this->handleInvalidation($jobCategory, 'deleted');
    }

    /**
     * Invalidate tenant and global caches, increment dataset version, and broadcast invalidation event.
     */
    protected function handleInvalidation(JobCategory $jobCategory, string $operation): void
    {
        $companyId = (int) ($jobCategory->company_id ?? 0);

        if ($companyId > 0) {
            Cache::forget('tenant:'.$companyId.':categories');
            Cache::forget('tenant:'.$companyId.':all_categories');
            Cache::forget('global_job_categories_map');

            $versionKey = 'tenant:'.$companyId.':dept_version';
            $previousVersion = (int) Cache::get($versionKey, 1);
            $newVersion = $previousVersion + 1;
            Cache::put($versionKey, $newVersion, 86400 * 30);

            // Invalidate cached bundle
            Cache::forget('tenant:'.$companyId.':dataset:departments_bundle:v'.$previousVersion);
            Cache::forget('tenant:'.$companyId.':dataset:departments_bundle:v'.$newVersion);

            // Dispatch real-time Reverb WebSocket broadcast
            try {
                broadcast(new DepartmentMutatedEvent(
                    $companyId,
                    (int) $jobCategory->id,
                    $operation,
                    $newVersion,
                    $previousVersion,
                    ['name' => $jobCategory->name]
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Reverb DepartmentMutatedEvent broadcast warning: '.$e->getMessage());
            }
        } else {
            // Master department altered (rare)
            Cache::forget('global_job_categories_map');
        }
    }
}
