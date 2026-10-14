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
        'state_version' => 'integer',
    ];

    protected $appends = [
        'active',
        'public_market_status',
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

    /**
     * Intent: Dynamic projection for backward compatibility with legacy views.
     * Computes single legacy integer from orthogonal state dimensions.
     */
    public function getPublicMarketStatusAttribute($value): int
    {
        return \App\Enums\MarketplaceState::computeLegacyStatus(
            $this->market_pub_state ?? null,
            $this->market_mod_state ?? null,
            $this->claim_capacity ?? null,
            $this->status ?? 'active',
            $this->commercial_state ?? 'configured'
        );
    }

    /**
     * Intent: Mutator guard preventing direct writes to legacy public_market_status.
     * Automatically maps legacy integer updates into orthogonal states to prevent drift.
     */
    public function setPublicMarketStatusAttribute($value): void
    {
        $intVal = (int) $value;
        switch ($intVal) {
            case 0: // Private
                $this->attributes['market_pub_state'] = \App\Enums\MarketPubState::PRIVATE->value;
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::NOT_REQUIRED->value;
                break;
            case 1: // Pending
                $this->attributes['market_pub_state'] = \App\Enums\MarketPubState::PENDING_REVIEW->value;
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::PENDING->value;
                break;
            case 2: // Approved
                $this->attributes['market_pub_state'] = \App\Enums\MarketPubState::PUBLISHED->value;
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::APPROVED->value;
                $this->attributes['claim_capacity'] = \App\Enums\ClaimCapacity::OPEN->value;
                break;
            case 3: // Rejected
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::REJECTED->value;
                break;
            case 4: // Vendor Limit
                $this->attributes['market_pub_state'] = \App\Enums\MarketPubState::PUBLISHED->value;
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::APPROVED->value;
                $this->attributes['claim_capacity'] = \App\Enums\ClaimCapacity::LIMIT_REACHED->value;
                break;
            case 5: // Suspended
                $this->attributes['market_mod_state'] = \App\Enums\MarketModState::SUSPENDED->value;
                break;
            case 6: // Unpublished
                $this->attributes['market_pub_state'] = \App\Enums\MarketPubState::UNPUBLISHED->value;
                break;
        }

        $this->attributes['public_market_status'] = $intVal;
    }

    /**
     * Intent: Relationship to immutable dimensional moderation history logs.
     */
    public function moderationLogs()
    {
        return $this->hasMany(JobMarketplaceModerationLog::class, 'job_id');
    }
}

