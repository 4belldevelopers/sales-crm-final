<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Append-only audit trail for jobs.status transitions, feeds the Job Status
// drawer's "Status Timeline" panel. Written once per status change, never updated.
class JobStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'company_id',
        'from_status',
        'to_status',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
