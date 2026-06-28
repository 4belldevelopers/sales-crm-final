<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticJobApplicationSkills extends Model
{
    protected $table = 'job_application_skills_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
