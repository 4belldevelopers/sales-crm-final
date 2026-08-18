<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApplicationStatus extends Model
{
    protected $table = 'master_status';

   /* public function applications()
    {
        return $this->hasMany(JobApplication::class, 'status_id')->orderBy('column_priority');
    }*/

   /* public function scopeCompany($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }*/
}
