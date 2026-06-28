<?php

namespace App\Observers;

use App\Models\JobQuestion;

class JobQuestionObserver
{
    public function saving(JobQuestion $jobQuestion)
    {
        if (company()) {
            $jobQuestion->company_id = company()->id;
        }
    }
}
