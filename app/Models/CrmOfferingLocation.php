<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CrmOfferingLocation — Delivery coverage locations for a CRM Offering.
 */
class CrmOfferingLocation extends Model
{
    protected $table = 'crm_offering_locations';

    protected $guarded = ['id'];
}
