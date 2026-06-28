<?php

// app/Models/TicketStatus.php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
  

    protected $fillable = ['name'];

    /**
     * Get the tickets associated with this status.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

