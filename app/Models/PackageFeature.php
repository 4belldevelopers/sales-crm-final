<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    public $timestamps = false;
    public function packageDetails()
    {
        return $this->hasMany(PackageDetail::class);
    }
    
}
