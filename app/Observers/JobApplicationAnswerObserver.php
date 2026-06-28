<?php

namespace App\Observers;

use App\Models\JobApplicationAnswer;
use App\Models\User;

class JobApplicationAnswerObserver
{
    public function saving(JobApplicationAnswer $jobApplicationAnswer)
    {
        if (company()) {
            $jobApplicationAnswer->company_id = company()->id;
        }
    }
}
