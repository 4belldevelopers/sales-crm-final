<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use BelongsToTenant;

    protected $fillable = ['name', 'guard_name', 'company_id','display_name','description','priority','manual_add'];

    public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    // }

    // public static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('company', function (Builder $builder) {
    //         if (auth()->check() && !auth()->user()->is_superadmin) {
    //             $builder->where('roles.company_id', user()->company_id);
    //         }
    //     });
    // }

    // public function permissions()
    // {
    //     return $this->hasMany(PermissionRole::class, 'role_id');
    // }

    // public function permissionsList()
    // {
    //     return $this->hasMany(Permission::class, 'role_id');
    // }

    // public function roleuser()
    // {
    //     return $this->hasMany(RoleUser::class, 'role_id');
    // }
}
