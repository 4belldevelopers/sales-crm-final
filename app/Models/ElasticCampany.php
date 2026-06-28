<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticCampany extends Model
{
    protected $table = 'companies_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
