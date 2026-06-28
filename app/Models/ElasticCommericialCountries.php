<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticCommericialCountries extends Model
{
    protected $table = 'commericial_countries_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
