<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticCampanyCoins extends Model
{
    protected $table = 'company_coins_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
