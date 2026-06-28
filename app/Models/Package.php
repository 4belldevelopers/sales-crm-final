<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{


    public function packageDetail()
    {
        return $this->hasOne(PackageDetail::class);
    }
    
    
    public function scopeActive($query) {
        return $query->where('status', 1);
    }
}
