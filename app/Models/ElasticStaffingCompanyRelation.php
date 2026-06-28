<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticStaffingCompanyRelation extends Model
{
    protected $table = 'staffing_company_relation_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
