<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AgencyActivityLog
 *
 * Immutable transactional record of all lifecycle, recruitment, commercial,
 * team, and system operations concerning an Agency in JERO.
 *
 * Scoped by agency_company_id and service_company_id.
 */
class AgencyActivityLog extends Model
{
    protected $table = 'agency_activity_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'previous_value' => 'array',
        'new_value' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
