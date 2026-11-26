<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CrmOfferingTag — Feature/technology/expertise tags for a CRM Offering.
 */
class CrmOfferingTag extends Model
{
    protected $table = 'crm_offering_tags';

    protected $guarded = ['id'];
}
