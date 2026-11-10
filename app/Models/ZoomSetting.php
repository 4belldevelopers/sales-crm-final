<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ZoomSetting
 *
 * Intent: Eloquent model representing system-level Zoom Video Meeting integration settings.
 * Maps directly to table `zoomsettings` maintaining strict singleton compatibility (id = 1).
 *
 * Tenancy: Global Super Admin configuration (tenant_id = 0).
 * Table Schema Truth: database/schema/mysql-schema.sql (`zoomsettings`).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zoomsettings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     * Note: MySQL table `zoomsettings` has no created_at or updated_at columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'title',
        'redirect_uri',      // Stores Zoom Server-to-Server Account ID
        'client_id',
        'client_secret',
        'zoom_access_token',
        'status',            // 1 = Active, 0 = Inactive / Paused
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'is_configured',
        'is_active',
        'account_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Check if Zoom integration has valid credentials configured.
     */
    public function getIsConfiguredAttribute(): bool
    {
        return ! empty($this->client_id) && ! empty($this->redirect_uri);
    }

    /**
     * Check if Zoom integration is actively enabled.
     */
    public function getIsActiveAttribute(): bool
    {
        return (int) $this->status === 1;
    }

    /**
     * Alias for redirect_uri which authoritatively stores the Zoom Account ID.
     */
    public function getAccountIdAttribute(): ?string
    {
        return $this->redirect_uri;
    }
}
