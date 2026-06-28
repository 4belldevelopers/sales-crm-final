<?php

namespace App\Observers;

use App\Models\Designation;

class DesignationObserver
{
    public function saving(Designation $designation)
    {
        if (company()) {
            $designation->company_id = company()->id;
        }
    }
}
