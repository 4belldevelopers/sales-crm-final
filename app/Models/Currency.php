<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'currency_name',
        'currency_symbol',
        'currency_code',
        'usd_based_rates',
        'inr_based_rates',
        'getKey',
    ];

    protected $casts = [
        'usd_based_rates' => 'float',
        'inr_based_rates' => 'float',
        'getKey' => 'integer',
    ];
}
