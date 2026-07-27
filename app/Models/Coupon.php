<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'discount_percent', 'expiry_date', 'max_redemptions',
        'times_redeemed', 'status', 'created_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'discount_percent' => 'float',
    ];

    public function isRedeemable(): bool
    {
        return $this->status === 'active'
            && ! $this->expiry_date->copy()->endOfDay()->isPast()
            && $this->times_redeemed < $this->max_redemptions;
    }
}
