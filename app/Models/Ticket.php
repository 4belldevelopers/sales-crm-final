<?php

// app/Models/Ticket.php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
   
    protected $fillable = ['user_id', 'title', 'description', 'status_id','ticket_no','company_id'];

    /**
     * Get the user who created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Get the status of the ticket.
     */
    public function ticketstatus()
    {
        return $this->belongsTo(TicketStatus::class,'status_id');
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    /**
     * Get the comments for the ticket.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

