<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticJobMetronicsSkills extends Model
{
    protected $table = 'job_metronics_skills_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
