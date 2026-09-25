<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CoinReservation
 *
 * Intent: Tracks durable two-phase coin reservations for Forward Resume operations.
 * Enforces ACID financial durability: MySQL is the authoritative ledger; Redis is the concurrency mirror.
 * Tenancy: Enforces company_id scoping via scopeForCompany.
 */
class CoinReservation extends Model
{
    protected $table = 'coin_reservations';

    protected $fillable = [
        'reservation_id',
        'company_id',
        'user_id',
        'forward_request_id',
        'amount',
        'status',
        'expires_at',
        'committed_at',
        'released_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'integer',
        'expires_at' => 'datetime',
        'committed_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function scopeForCompany($query, int|string $companyId)
    {
        return $query->where('company_id', (int) $companyId);
    }

    public function scopeByReservationId($query, string $reservationId)
    {
        return $query->where('reservation_id', $reservationId);
    }

    public function forwardRequest(): BelongsTo
    {
        return $this->belongsTo(ForwardRequest::class, 'forward_request_id', 'uuid');
    }
}
