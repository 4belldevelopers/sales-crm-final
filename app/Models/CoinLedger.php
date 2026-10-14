<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/**
 * CoinLedger
 *
 * Append-only immutable double-entry ledger tracking all platform coin flows
 * with row-level locking and idempotency protection.
 */
class CoinLedger extends Model
{
    use BelongsToTenant;

    public $timestamps = false;

    protected $table = 'coin_ledger';

    protected $fillable = [
        'transaction_id',
        'company_id',
        'account_type',
        'coin_type',
        'entry_type',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'idempotency_key',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
