<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MarketplaceRiskRule
 *
 * Intent:
 * Database-configurable rules for the local zero-cloud heuristic risk engine.
 * Allows Super Admin to modify weights, regex patterns, and actions (FLAG, QUARANTINE, REJECT)
 * without requiring code deploys.
 */
class MarketplaceRiskRule extends Model
{
    protected $table = 'marketplace_risk_rules';

    protected $fillable = [
        'rule_code',
        'category',
        'name',
        'description',
        'weight',
        'is_active',
        'action_on_match',
        'regex_pattern',
    ];

    protected $casts = [
        'weight' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
