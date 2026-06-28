<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticTdIndustries extends Model
{
    protected $table = 'td_industries_view'; // or 'td_industries' if no view
    public $timestamps = false;
}
