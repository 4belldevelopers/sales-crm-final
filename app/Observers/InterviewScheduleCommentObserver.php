<?php

namespace App\Observers;

use App\Models\ScheduleComments;

class InterviewScheduleCommentObserver
{
    public function saving(ScheduleComments $scheduleComments)
    {
        if (company()) {
            $scheduleComments->company_id = company()->id;
        }
    }
}
