<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticJobs extends Model
{
    protected $table = 'jobs_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
