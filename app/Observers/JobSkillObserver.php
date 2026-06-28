<?php

namespace App\Observers;

use App\Models\JobSkill;

class JobSkillObserver
{
    public function saving(JobSkill $jobSkill)
    {
        if (company()) {
            $jobSkill->company_id = company()->id;
        }
    }
}
