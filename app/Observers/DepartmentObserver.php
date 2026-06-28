<?php

namespace App\Observers;

use App\Models\Department;

class DepartmentObserver
{
    public function saving(Department $department)
    {
        if (company()) {
            $department->company_id = company()->id;
        }
    }
}
