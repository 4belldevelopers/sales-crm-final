<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NoticePeriod extends Model
{
    protected static function boot()
    {
        parent::boot();

       /* static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_superadmin) {
                $builder->where('job_locations.company_id', auth()->user()->company_id);
            }
        });*/
    }

    /*public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }*/
}
