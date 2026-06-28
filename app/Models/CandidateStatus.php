<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CandidateStatus extends Model
{
	protected $table = 'candidate_status';
	
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->is_superadmin) {
                $builder->where('candidate_status.company_id', user()->company_id);
            }
        });
    }

	/*public function children()
	{
		return $this->hasMany('candidate_status', 'parent', 'id');
	}
	public function parent()
	{
		return $this->belongsTo('candidate_status', 'parent');
	}*/

	public function parent()
    {
        return $this->belongsTo('App\CandidateStatus','parent')->where('parent',0);
    }
    public function children()
    {
        return $this->hasMany('App\CandidateStatus','parent');
    }
	
   /* public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }*/

    protected $guarded = ['id'];
}
