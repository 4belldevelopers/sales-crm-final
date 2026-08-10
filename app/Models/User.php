<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    public function scopeCompany($query)
    {
        return $query->where('users.company_id', auth()->user()->company_id);
    }

    /** Only these 4 fields may be mass-assigned; sensitive fields (status, company_id, super_password…) require explicit property assignment. */
    protected $fillable = [
        'name', 'email', 'password', 'image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends =[
        'profile_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    public function getProfileImageUrlAttribute(){
        if (is_null($this->image)) {
            return asset('avatar.png');
        }
        return asset_url('profile/'.$this->image);
    }

    public function role() {
        return $this->hasOne(RoleUser::class, 'user_id');
    }

    public function todoItems()
    {
        return $this->hasMany(TodoItem::class);
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->id;
    }

    public static function allAdmins($exceptId = NULL)
    {
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.calling_code', 'users.mobile', 'users.mobile_verified', 'users.created_at')
            ->where('roles.name', 'admin')
            ->company();

        if(!is_null($exceptId)){
            $users->where('users.id', '<>', $exceptId);
        }

        return $users->get();
    }

    public static function frontAllAdmins($companyId)
    {
        return User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.calling_code', 'users.mobile', 'users.mobile_verified', 'users.created_at')
            ->where('roles.name', 'admin')
            ->where('users.company_id', $companyId)
            ->get();
    }

    public function getMobileWithCodeAttribute()
    {
        return substr($this->calling_code, 1).$this->mobile;
    }

    public function getFormattedMobileAttribute()
    {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return $this->calling_code.'-'.$this->mobile;
    }

    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_with_code;
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
