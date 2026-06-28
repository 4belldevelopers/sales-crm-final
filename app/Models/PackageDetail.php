<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    public $timestamps = false;


    public function package()
    {
        return $this->belongsTo(Package::class);
    }


    public function packageFeature()
    {
        return $this->belongsTo(PackageFeature::class);
    }
    
}
