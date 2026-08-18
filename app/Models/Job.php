<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use Sluggable, BelongsToTenant;

    protected $dates = ['end_date', 'start_date'];

    protected $casts = [
        'required_columns' => 'array',
        'meta_details' => 'array',
        'section_visibility' => 'array'
    ];

    protected $appends = [
        'active'
    ];



    public function applications()
    {
        return $this->belongsToMany(JobApplication::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');     			//single 
    }

  	/*public function location()
    {
        return $this->belongsTo(JobLocation::class, 'location_id'); 
    }*/

    public function skills()
    {
        return $this->hasMany(JobSkill::class, 'job_id');						// one to many 
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');					// single
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'location.location']
            ]
        ];
    }

    public static function activeJobs()
    {
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->get();
    }

    public static function frontActiveJobs($companyId)
    {
        return Job::withoutGlobalScope('company')
            //->where('status', 'active')
            //->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            //->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->where('company_id', $companyId)
            ->get();
    }

    public static function activeJobsCount()
    {
        return Job::where('status', 'active')
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
            ->count();
    }

    public function getActiveAttribute()
    {
        return $this->status === 'active' && $this->start_date->lessThanOrEqualTo(Carbon::now()) && $this->end_date->greaterThanOrEqualTo(Carbon::now());
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'job_questions');
    }
}
