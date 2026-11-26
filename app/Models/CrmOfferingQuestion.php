<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CrmOfferingQuestion — Pre-sales customer discovery/qualification questions.
 */
class CrmOfferingQuestion extends Model
{
    protected $table = 'crm_offering_questions';

    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];
}
