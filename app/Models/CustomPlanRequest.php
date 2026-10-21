<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * CustomPlanRequest
 *
 * Represents an enterprise custom subscription plan or custom top-up enquiry
 * submitted by a tenant via the "Contact Sales Team" workflow.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class CustomPlanRequest extends Model
{
    public const STATUS_NEW = 'New';

    public const STATUS_IN_PROGRESS = 'In Progress';

    public const STATUS_CONTACTED = 'Contacted';

    public const STATUS_RESOLVED = 'Resolved';

    public const STATUS_CLOSED = 'Closed';

    public const ALLOWED_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_CONTACTED,
        self::STATUS_RESOLVED,
        self::STATUS_CLOSED,
    ];

    protected $table = 'custom_plan_requests';

    protected $fillable = [
        'request_reference',
        'company_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'company_name',
        'location',
        'request_type',
        'package_type',
        'package_id',
        'package_title',
        'status',
        'message',
        'admin_notes',
        'assigned_to',
        'updated_by',
        'aggregate_version',
        'last_status_updated_at',
    ];

    protected $casts = [
        'aggregate_version' => 'integer',
        'last_status_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tenant company relationship.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * User who submitted the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Internal sales / account manager assigned to this request.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Admin user who last updated this request status.
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor to guarantee clean, enterprise naming for custom packages.
     * Replaces legacy/raw database entries like "Free" or "Custom Package"
     * with "Operational Custom Package" or "Sales Custom Package".
     */
    public function getPackageTitleAttribute($value): string
    {
        if (empty($value) || strcasecmp(trim((string) $value), 'free') === 0 || strcasecmp(trim((string) $value), 'custom package') === 0) {
            $type = strtolower($this->package_type ?? 'operational');

            return ucfirst($type).' Custom Package';
        }

        return (string) $value;
    }

    /**
     * Status transition audit trail.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CustomPlanRequestLog::class, 'custom_plan_request_id')->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeStatus($query, ?string $status)
    {
        if (! empty($status) && in_array($status, self::ALLOWED_STATUSES, true)) {
            return $query->where('custom_plan_requests.status', $status);
        }

        return $query;
    }

    /**
     * Scope: Universal search by reference, customer name, email, or company.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->where('custom_plan_requests.request_reference', 'like', "%{$term}%")
                ->orWhere('custom_plan_requests.customer_name', 'like', "%{$term}%")
                ->orWhere('custom_plan_requests.customer_email', 'like', "%{$term}%")
                ->orWhere('custom_plan_requests.company_name', 'like', "%{$term}%");
        });
    }

    /**
     * Atomically generate a collision-free, human-readable reference number: CPR-YYYYMM-XXXX.
     */
    public static function generateReference(): string
    {
        $prefix = 'CPR-'.date('Ym').'-';

        $latest = DB::table('custom_plan_requests')
            ->where('request_reference', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value('request_reference');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
