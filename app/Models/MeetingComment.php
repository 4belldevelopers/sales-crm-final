<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingComment extends Model
{
    public $timestamps = false;

    public function meetingschedules()
    {
        return $this->belongsTo(MeetingSchedule::class);
    }

    //
}
