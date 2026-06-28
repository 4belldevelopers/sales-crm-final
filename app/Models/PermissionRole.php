<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class PermissionRole extends SpatiePermission
{
    protected $table = 'permission_role';

    public $timestamps = false;

    protected $fillable = ['permission_id', 'role_id'];

    // public function permission() {
    //     return $this->belongsTo(Permission::class, 'permission_id');
    // }
}
