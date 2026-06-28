<?php

namespace App\Observers;

use App\Models\Onboard;

class OnBoardObserver
{
    public function saving(Onboard $onBoard)
    {
        if (company()) {
            $onBoard->company_id = company()->id;
        }
    }
}
