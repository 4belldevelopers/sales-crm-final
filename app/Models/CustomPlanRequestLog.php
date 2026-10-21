<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CustomPlanRequestLog
 *
 * Immutable audit log tracking each status transition and admin note update
 * on a custom plan request.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class CustomPlanRequestLog extends Model
{
    public $timestamps = false;

    protected $table = 'custom_plan_request_logs';

    protected $fillable = [
        'custom_plan_request_id',
        'previous_status',
        'new_status',
        'notes',
        'updated_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(CustomPlanRequest::class, 'custom_plan_request_id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
