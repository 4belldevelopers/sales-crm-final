<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Intent: Manages durable database identity and versioned resume attachment.
 * Maps to the resume_assets table.
 */
class ResumeAsset extends Model
{
    public $timestamps = false;

    protected $table = 'resume_assets';

    protected $guarded = ['id'];

    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        'created_at' => 'datetime',
        'attached_at' => 'datetime',
        'removed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->is_superadmin) {
                $builder->where('resume_assets.company_id', auth()->user()->company_id);
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(JobApplication::class, 'candidate_id');
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ACTIVE');
    }
}
