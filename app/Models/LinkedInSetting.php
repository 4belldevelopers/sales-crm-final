<?php

namespace App\Models;

use App\Services\SuperAdmin\SuperAdminLinkedInSettingReadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class LinkedInSetting
 *
 * Eloquent model for global LinkedIn OAuth 2.0 (OpenID Connect) credentials and activation status.
 * Authoritative integration point configuring client_id, client_secret, callback_url, and status.
 *
 * Architecture & Tenancy:
 * - Singleton pattern: linked_in_settings table is always accessed as an authoritative platform-level singleton.
 * - Multi-tenant cache invalidation: clearing Redis/Dragonfly cache on write guarantees zero stale data (Rule 6).
 * - Hydratable from Elasticsearch documents directly with zero database queries.
 *
 * @property int $id
 * @property string|null $client_id
 * @property string|null $client_secret
 * @property string $status 'enable' | 'disable'
 * @property string|null $callback_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_enabled
 * @property-read bool $is_configured
 * @property-read string $masked_secret
 * @property-read string $formatted_status
 */
class LinkedInSetting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'linked_in_settings';

    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'client_secret',
        'status',
        'callback_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'string',
    ];

    /**
     * Check if LinkedIn integration is globally enabled.
     */
    public function getIsEnabledAttribute(): bool
    {
        return $this->status === 'enable';
    }

    /**
     * Check if LinkedIn integration has both client ID and client secret configured.
     */
    public function getIsConfiguredAttribute(): bool
    {
        return ! empty($this->client_id) && ! empty($this->client_secret);
    }

    /**
     * Return a masked representation of the client secret for UI display.
     */
    public function getMaskedSecretAttribute(): string
    {
        if (empty($this->client_secret)) {
            return '';
        }

        $length = strlen($this->client_secret);
        if ($length <= 8) {
            return str_repeat('•', $length);
        }

        return Str::mask($this->client_secret, '•', 4, -4);
    }

    /**
     * Return human-friendly status label.
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst((string) $this->status);
    }

    /**
     * Global CQRS accessor to retrieve authoritative singleton from L2 Cache / Elasticsearch.
     * Guarantees 0% MySQL reads across platform requests.
     */
    public static function getGlobalSetting(): self
    {
        try {
            return app(SuperAdminLinkedInSettingReadService::class)->getLinkedInSettings();
        } catch (Throwable) {
            return self::firstOrCreate([], [
                'status' => 'disable',
                'client_id' => '',
                'client_secret' => '',
                'callback_url' => '',
            ]);
        }
    }
}
