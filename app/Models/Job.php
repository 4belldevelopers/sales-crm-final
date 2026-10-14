<?php

namespace App\Models;

use App\Helper\GeneralMethod;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use BelongsToTenant, Sluggable;

    protected static function booted()
    {
        static::updated(function ($job) {
            if (auth()->check()) {
                GeneralMethod::invalidateSpartnerlistingCache(auth()->user()->id);
            }
        });
    }

    protected $dates = ['end_date', 'start_date'];

    protected $casts = [
        'required_columns' => 'array',
        'meta_details' => 'array',
        'section_visibility' => 'array',
    ];

    protected $appends = [
        'active',
    ];

    public function applications()
    {
        return $this->belongsToMany(JobApplication::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');     			// single
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
                'source' => ['title', 'location.location'],
            ],
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
            // ->where('status', 'active')
            // ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            // ->where('end_date', '>=', Carbon::now()->format('Y-m-d'))
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
        if ($this->status !== 'active' || empty($this->start_date) || empty($this->end_date)) {
            return false;
        }

        $now = Carbon::now();
        $start = $this->start_date instanceof Carbon ? $this->start_date : Carbon::parse($this->start_date);
        $end = $this->end_date instanceof Carbon ? $this->end_date : Carbon::parse($this->end_date);

        return $start->lessThanOrEqualTo($now) && $end->greaterThanOrEqualTo($now);
    }

    /**
     * Intent: Authoritative accessor for Agency Acceptance Limit.
     * Retains value in meta_details JSON, defaulting to 20 if unset.
     */
    public function getVendorLimitAttribute(): int
    {
        $meta = $this->meta_details;
        if (is_string($meta)) {
            $meta = json_decode($meta, true);
        }
        $val = (int) ($meta['vendor_limit'] ?? 20);

        return in_array($val, [5, 10, 20, 50], true) ? $val : 20;
    }

    /**
     * Intent: Authoritative accessor for Auto-expire duration in days.
     * Retains value in meta_details JSON, defaulting to 7 if unset.
     */
    public function getAutoExpireAttribute(): int
    {
        $meta = $this->meta_details;
        if (is_string($meta)) {
            $meta = json_decode($meta, true);
        }
        $val = (int) ($meta['auto_expire'] ?? 0);
        if (in_array($val, [7, 14, 30], true)) {
            return $val;
        }

        if (! empty($this->end_date)) {
            $daysLeft = (int) ceil(now()->diffInDays(Carbon::parse($this->end_date), false));
            if ($daysLeft > 14) {
                return 30;
            } elseif ($daysLeft > 7) {
                return 14;
            }
        }

        return 7;
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'job_questions');
    }
}
