<?php

namespace App\Observers;

use App\Models\Skill;

class SkillObserver
{
    public function saving(Skill $skill)
    {
        if (company()) {
            $skill->company_id = company()->id;
        }
    }
}
