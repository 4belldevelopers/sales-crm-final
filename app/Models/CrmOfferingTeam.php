<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CrmOfferingTeam — Sales/Delivery owner & collaborators for a CRM Offering.
 */
class CrmOfferingTeam extends Model
{
    protected $table = 'crm_offering_team';

    protected $guarded = ['id'];

    protected $casts = [
        'is_lead' => 'boolean',
    ];
}
