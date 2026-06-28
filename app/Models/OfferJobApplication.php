<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferJobApplication extends Model
{
    use Notifiable;

	protected $appends = ['resume_url', 'photo_url'];
	 
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                // $builder->where('offer_job_applications.company_id', user()->company_id);
            }
        });
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }


    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

	public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function resumeDocument()
    {
        return $this->morphOne(Document::class, 'documentable')->where('name', 'Resume');
    }
	

    public function routeNotificationForNexmo($notification)
    {
        return $this->phone;
    }
	
	 public function getPhotoUrlAttribute()
    {
        if (is_null($this->photo)) {
            return asset('avatar.png');
        }
        return asset_url('candidate-photos/' . $this->photo);
    }

    public function getResumeUrlAttribute()
    {
        if ($this->documents()->where('name', 'Resume')->first()) {
            return asset_url('documents/' . $this->id . '/' . $this->documents()->where('name', 'Resume')->first()->hashname);
        }
        return false;
    }
	

} 