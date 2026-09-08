<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * V2_login Phase 3.3 — audit record for user-suspend and company-block actions.
 *
 * @property string $target_type  'user' | 'company'
 * @property int    $target_id
 * @property int    $company_id
 * @property string $new_status
 * @property int    $actor_id
 * @property string $reason
 */
class BlockLog extends Model
{
    public $timestamps = false;

    protected $table = 'block_log';

    protected $fillable = [
        'target_type',
        'target_id',
        'company_id',
        'new_status',
        'actor_id',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->created_at ??= now();
        });
    }
}
