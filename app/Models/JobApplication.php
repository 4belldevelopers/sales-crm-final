<?php
namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Notifications\Notifiable;
//use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use Notifiable, SoftDeletes;

	// BUG-12 fix: removed from $appends to stop N+1 on every hydration (accessors still callable explicitly).
	protected $appends = [];
	 
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            // BUG-13 fix: filter to authenticated company; skip for super admins (is_superadmin = true)
            // who need cross-company access and query without withoutGlobalScope().
            if (auth()->check() && !auth()->user()->is_superadmin) {
                $builder->where('job_applications.company_id', auth()->user()->company_id);
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
        // BUG-12 fix: reuse resumeDocument() relation (1 lazy-loaded query, relation-cached on the instance).
        // Old code called documents()->where('name','Resume')->first() TWICE = 2 DB queries per call.
        $doc = $this->resumeDocument;
        return $doc ? asset_url('documents/' . $this->id . '/' . $doc->hashname) : false;
    }
	

} 