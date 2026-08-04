<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Thin model for the recommendation_relation table — exists so observers can be registered.
 * All heavy queries use DB::table('recommendation_relation') directly.
 */
class RecommendationRelation extends Model
{
    protected $table = 'recommendation_relation';
    public $timestamps = false;
}
