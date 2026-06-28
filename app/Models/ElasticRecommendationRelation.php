<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElasticRecommendationRelation extends Model
{
    protected $table = 'recommendation_relation_view'; // or 'job_applications' if no view
    public $timestamps = false;
}
