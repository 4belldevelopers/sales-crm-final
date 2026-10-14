<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * JobMarketplaceModerationLog
 *
 * Immutable dimensional audit log for all recruiter, superadmin, and system actions
 * on marketplace jobs.
 */
class JobMarketplaceModerationLog extends Model
{
    use BelongsToTenant;

    public $timestamps = false;

    protected $table = 'job_marketplace_moderation_logs';

    protected $fillable = [
        'job_id',
        'company_id',
        'actor_id',
        'actor_role',
        'action',
        'from_mod_state',
        'to_mod_state',
        'reason_code',
        'rejection_notes',
        'risk_score',
        'created_at',
    ];

    protected $casts = [
        'risk_score' => 'float',
        'created_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
