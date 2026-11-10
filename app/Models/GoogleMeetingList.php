<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GoogleMeetingList
 *
 * Eloquent model representing pre-allocated permanent Google Meet conference links
 * stored in the `google_meeting_lists` pool.
 *
 * Tenancy: Global Super Admin pool (tenant_id = 0) consumed platform-wide by ATS recruiters.
 * Database Truth: database/schema/mysql-schema.sql:1738
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class GoogleMeetingList extends Model
{
    protected $table = 'google_meeting_lists';

    public $timestamps = false;

    protected $fillable = [
        'account_name',
        'meeting_link',
        'interview_date',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'interview_date' => 'datetime',
    ];

    /**
     * Check if the meeting link is currently active and available for ATS booking.
     */
    public function getIsActiveAttribute(): bool
    {
        return (int) $this->status === 1;
    }

    /**
     * Get host initial letter for avatar display in table listings.
     */
    public function getHostInitialAttribute(): string
    {
        $name = trim((string) ($this->account_name ?? ''));

        return strtoupper(substr($name !== '' ? $name : 'G', 0, 1));
    }
}
