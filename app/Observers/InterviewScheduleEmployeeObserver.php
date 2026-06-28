<?php

namespace App\Observers;

use App\Models\InterviewScheduleEmployee;

class InterviewScheduleEmployeeObserver
{
    public function saving(InterviewScheduleEmployee $interviewScheduleEmployee)
    {
        if (company()) {
            $interviewScheduleEmployee->company_id = company()->id;
        }
    }
}
