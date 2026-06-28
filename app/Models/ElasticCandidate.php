<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class ElasticCandidate extends Model
{
    // use Searchable;

    protected $table = 'candidates_view'; // or 'job_applications' if no view
    public $timestamps = false;

    // public function toSearchableArray()
    // {
    //     return [
    //         'wise_id' => $this->wise_id,
    //         'candidate_id' => $this->candidate_id,
    //         'full_name' => $this->full_name,
    //         'email' => $this->email,
    //         'phone' => $this->phone,
    //         'skills' => $this->skills,
    //         'total_exp' => $this->total_exp,
    //         'rel_exp' => $this->rel_exp,
    //         'ctc' => $this->ctc,
    //         'ectc' => $this->ectc,
    //         'current_company' => $this->current_company,
    //         'current_designation' => $this->current_designation,
    //         'qualification' => $this->qualification,
    //         'qualification_name' => $this->qualification_name,
    //         'condidate_type' => $this->condidate_type,
    //         'ctc_hourly' => $this->ctc_hourly,
    //         'ctc_monthly' => $this->ctc_monthly,
    //         'ctc_yearly' => $this->ctc_yearly,
    //         'ectc_hourly' => $this->ectc_hourly,
    //         'ectc_monthly' => $this->ectc_monthly,
    //         'ectc_yearly' => $this->ectc_yearly,
    //         'status' => $this->status,
    //         'current_ctc_cur' => $this->current_ctc_cur,
    //         'current_ctc_mode' => $this->current_ctc_mode,
    //         'is_viewed' => $this->is_viewed,
    //         'ads_switch' => $this->ads_switch,
    //         'city_id' => $this->city_id,
    //         'city_name' => $this->city_name,
    //         'state_name' => $this->state_name,
    //         'country_name' => $this->country_name,
    //         'job_id' => $this->job_id,
    //         'job_title' => $this->job_title,
    //         'employment_type' => $this->employment_type,
    //         'client_name' => $this->client_name,
    //         'client_company_name' => $this->client_company_name,
    //         'master_status_id' => $this->master_status_id,
    //         'child_status_id' => $this->child_status_id,
    //         'status_id' => $this->status_id,
    //         'rec_candidate_assign_id' => $this->rec_candidate_assign_id,
    //         'submitted_by' => $this->submitted_by,
    //         'company_id' => $this->company_id,
    //         'thirdparty_company_id' => $this->thirdparty_company_id,
    //         'placed_end_date' => $this->placed_end_date,
    //         'existing' => $this->existing,
    //         'skill_match' => $this->skill_match,
    //         'submitted_by_name' => $this->submitted_by_name,
    //         'created_at' => $this->created_at,
    //         'updated_at' => $this->updated_at,
    //         'company_name' => $this->company_name,            
    //     ];
    // }
}
