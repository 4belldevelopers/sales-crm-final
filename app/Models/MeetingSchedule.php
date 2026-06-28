<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingSchedule extends Model
{
    public $timestamps = false;
    //

    public function comments()
    {
        return $this->hasMany(MeetingComment::class);
    }

    public function toCompany()
    {
        return $this->belongsTo(Company::class, 'to_company_id');
    }

    public function fromCompany()
    {
        return $this->belongsTo(Company::class, 'from_company_id');
    }

    public function users_main()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
