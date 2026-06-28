<?php

namespace App\Observers;

use App\Models\JobCategory;

class JobCategoryObserver
{
    public function saving(JobCategory $jobCategory)
    {
        if (company()) {
            $jobCategory->company_id = company()->id;
        }
    }
}
