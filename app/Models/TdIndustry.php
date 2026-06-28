<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdIndustry extends Model
{
    // Define any other properties or methods

    public function company()
    {
        return $this->belongsTo(Company::class, 'industries', 'ind_id');
    }
}