<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Invoice;
use Stripe\Invoice as StripeInvoice;

class Company extends Model
{
    use Billable, HasFactory, Notifiable;

    protected $appends = [
        'logo_url',
        'login_background_image_url',
    ];

    protected $dates = ['licence_expire_on', 'featured_start_date', 'featured_end_date'];

    public function findInvoice($id)
    {
        try {
            $stripeInvoice = StripeInvoice::retrieve(
                $id,
                $this->getStripeKey()
            );

            $stripeInvoice->lines = StripeInvoice::retrieve($id, $this->getStripeKey())
                ->lines
                ->all(['limit' => 1000]);

            $stripeInvoice->date = $stripeInvoice->created;

            return new Invoice($this, $stripeInvoice);
        } catch (Exception $e) {
            //
        }
    }

    // protected static function boot()
    // {

    //     parent::boot();

    //     if (auth()->check() && auth()->user()->roles->count() > 0) {
    //         static::addGlobalScope('company', function (Builder $builder) {
    //             $builder->where('companies.id', auth()->user()->company_id);
    //         });
    //     }
    // }

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {

            if (app()->runningInConsole()) {
                return;
            }
            $user = auth()->user();

            if ($user && $user->roles->count() > 0) {
                $builder->where('companies.id', $user->company_id);
            }

        });
    }

    public function scopeExcludeSuperAdmin($query)
    {
        return $query->whereNotIn('companies.id', \App\Helper\GeneralMethod::superAdminCompanyIds());
    }

    public function industries_data()
    {
        return $this->hasMany(TdIndustry::class, 'ind_id', 'industries');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            return asset('assets/logo-not-found.png');
        }

        return asset_url('company-logo/'.$this->logo);
    }

    public function application()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function packages()
    {
        return $this->hasMany(CompanyPackage::class, 'company_id');
    }

    public function isFeatured()
    {
        $currentDate = Carbon::now($this->timezone)->format('Y-m-d');
        $startBound = $currentDate.' 23:59:59';
        $endBound = $currentDate.' 00:00:00';

        return Company::where('status', 'active')
            ->where(function ($query) use ($startBound) {
                $query->whereNull('featured_start_date')
                    ->orWhere('featured_start_date', '<=', $startBound);
            })
            ->where(function ($query) use ($endBound) {
                $query->whereNull('featured_end_date')
                    ->orWhere('featured_end_date', '>=', $endBound);
            })
            ->where(function ($query) use ($endBound) {
                $query->whereNull('licence_expire_on')
                    ->orWhere('licence_expire_on', '>=', $endBound);
            })
            ->where('featured', 1)->where('id', $this->id)->first();
    }

    public function setSubDomainAttribute($value)
    {
        // domain is added in the request Class
        $this->attributes['sub_domain'] = strtolower($value);
    }

    public function getLoginBackgroundImageUrlAttribute()
    {
        if (is_null($this->login_background)) {
            $global = GlobalSetting::first();

            return $global->login_background_image_url;
        }

        return asset_url('login-background-image/'.$this->login_background);
    }
}
