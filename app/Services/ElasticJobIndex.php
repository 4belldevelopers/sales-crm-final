<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\JobApplication;
use App\Services\ElasticService;
use Exception;
use Illuminate\Support\Facades\Cache;

class ElasticJobIndex
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function jobIndexElastic($comid)
    {

        try {
            
                $jobs = DB::table('jobs')->select(
                    'jobs.*',
                    'clients.name as clients_name',
                    'clients.logo_logical_path',
                    'clients.self_company',
                    'clients.company_id as client_company_id',
                    'companies.company_name',
                    'companies.logo')
                ->leftJoin('clients', 'clients.id', 'jobs.client_id')
                ->join('companies', 'companies.id', 'jobs.company_id')
                ->where('jobs.id', $comid)
                ->get();

                foreach ($jobs as $row) {
                    $job_metronics_skills = DB::table('job_metronics_skills')
                    ->where('job_id', $row->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                        'id' => $s->id,
                        'company_id' => $s->company_id,
                        'job_id' => $s->job_id,
                        'skill_name' => strtolower($s->skill_name)
                        ];
                    })
                    ->unique('skill_name')
                    ->values()
                    ->toArray();

                    $recommendation_relation = DB::table('recommendation_relation')
                    ->where('job_id', $row->id)
                    ->get()
                    ->map(function ($s){
                        return [
                        'id' => (int) $s->id,
                        'from_company_id' => (int) $s->from_company_id,
                        'from_user_id' => (int) $s->from_user_id,
                        'from_status' => (int) $s->from_status,
                        'from_created_at' => ($s->from_created_at) ? Carbon::parse($s->from_created_at)->toIso8601String() : null,
                        'to_company_id' => (int) $s->to_company_id,
                        'to_user_id' => (int) $s->to_user_id,
                        'job_id' => (int) $s->job_id,
                        'to_status' => (int) $s->to_status,
                        'to_created_at' =>  ($s->to_created_at) ? Carbon::parse($s->to_created_at)->toIso8601String() : null,
                        'final_status' => (int) $s->final_status,
                        'conversation_id' => (int) $s->conversation_id,
                        'add_status' => (int) $s->add_status,
                        'created_at' =>  ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' =>  ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null
                        ];
                    })
                    ->toArray();

                    $job_assign = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('users.name as user_name', 'job_assign.*')
                    ->where('job_assign.job_id', $row->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'spok_user_id' => (int) $s->spok_user_id,
                        'user_id' => (int) $s->user_id,
                        'user_name' => strtolower($s->user_name),
                        'created_at' =>  ($s->created_at) ? Carbon::parse($s->created_at)->toIso8601String() : null,
                        'updated_at' =>  ($s->updated_at) ? Carbon::parse($s->updated_at)->toIso8601String() : null
                        ];
                    })
                    ->toArray();

                    $job_qualifications = DB::table('job_qualifications')
                    ->where('job_id', $row->id)
                    ->get()
                    ->map(function ($row){
                        return (array) $row;
                    })
                    ->toArray();
                    $client_location_jobs = DB::table('client_location_jobs')
                    ->where('job_id', $row->id)
                    ->get()
                    ->map(function ($row){
                        return (array) $row;
                    })
                    ->toArray();
                    $client_city_jobs = DB::table('client_city_jobs')
                    ->leftJoin('countries', 'countries.id', '=', 'client_city_jobs.country_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->select('city_list.city as city_name','countries.country_name','client_city_jobs.*')
                    ->where('job_id', $row->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                        'id' => (int) $s->id,
                        'company_id' => (int) $s->company_id,
                        'job_id' => (int) $s->job_id,
                        'city_id' => (int) $s->city_id,
                        'country_id' => (int) $s->country_id,
                        'city_name' => strtolower($s->city_name),
                        'country_name' => strtolower($s->country_name),
                        ];
                    })
                    ->toArray();

                    
                    if (empty($client_city_jobs)) {
                        $clj = DB::table('client_location_jobs')->where('job_id', $row->id)->first();
                        if ($clj && ! empty($clj->client_location_id)) {
                            $cId = (int) $clj->client_location_id;
                            $cityInfo = DB::table('city_list')
                                ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                ->select('city_list.city_id', 'city_list.city as city_name', 'countries.id as country_id', 'countries.country_name')
                                ->where('city_list.city_id', $cId)
                                ->first();

                            if (! $cityInfo) {
                                $clRow = DB::table('client_location')->where('id', $cId)->first();
                                if ($clRow && ! empty($clRow->city_id)) {
                                    $cityInfo = DB::table('city_list')
                                        ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                        ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                        ->select('city_list.city_id', 'city_list.city as city_name', 'countries.id as country_id', 'countries.country_name')
                                        ->where('city_list.city_id', $clRow->city_id)
                                        ->first();
                                }
                            }

                            if ($cityInfo) {
                                $client_city_jobs[] = [
                                    'id' => (int) $clj->id,
                                    'company_id' => (int) $clj->company_id,
                                    'job_id' => (int) $row->id,
                                    'city_id' => (int) $cityInfo->city_id,
                                    'country_id' => (int) ($cityInfo->country_id ?? 0),
                                    'city_name' => strtolower($cityInfo->city_name),
                                    'country_name' => strtolower($cityInfo->country_name ?? ''),
                                ];
                            }
                        }
                    }

                    $client_contact_info_jobs = DB::table('client_contact_info_jobs')
                    ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                    ->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_designation', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info_jobs.*')
                    ->where('client_contact_info_jobs.job_id', $row->id)
                    ->get()
                    ->map(function ($s) {
                        return [
                            'id' => (int) $s->id,
                            'company_id' => (int) $s->company_id,
                            'job_id' => (int) $s->job_id,
                            'client_contact_info_id' => (int) $s->client_contact_info_id,
                            'contact_person_name' => $s->contact_person_name,
                            'contact_person_designation' => $s->contact_person_designation,
                            'contact_person_email' => $s->contact_person_email,
                            'contact_person_number' => $s->contact_person_number,
                        ];
                    })
                    ->toArray();

                    $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_JOBS,
                        'id'    => $row->id,
                        'body'  => [
                            'doc' => [ 
                                    'id' => $row->id,
                                    'title' => strtolower($row->title),
                                    'comId' => $row->company_id,
                                    'slug' => $row->slug,
                                    'job_description' => $row->job_description,
                                    'job_requirement' => $row->job_requirement,
                                    'total_positions' => $row->total_positions,
                                    'location_id' => $row->location_id,
                                    'category_id' => $row->category_id,//
                                    'start_date' => ($row->start_date) ? Carbon::parse($row->start_date)->toIso8601String() : null,
                                    'end_date' => ($row->end_date) ? Carbon::parse($row->end_date)->toIso8601String() : null,
                                    'status' => $row->status,
                                    'required_columns' => $row->required_columns,
                                    'meta_details' => is_array($row->meta_details) ? $row->meta_details : json_decode($row->meta_details, true),
                                    'section_visibility' => $row->section_visibility,
                                    'created_at' => ($row->created_at) ? Carbon::parse($row->created_at)->toIso8601String() : null,
                                    'updated_at' => ($row->updated_at) ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                                    'priority' => $row->priority,
                                    'client_id' => $row->client_id,
                                    'salary_visibility' => \App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null),
                                    'min_exp' => $row->min_exp,
                                    'max_exp' => $row->max_exp,
                                    'min_salary' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0 : $row->min_salary,
                                    'max_salary' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0 : $row->max_salary,
                                    'qualification' => $row->qualification,
                                    'employment_type' => $row->employment_type,
                                    'min_salary_currency' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? null : $row->min_salary_currency,
                                    'max_salary_currency' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? null : $row->max_salary_currency,
                                    'min_sal_mode' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? null : $row->min_sal_mode,
                                    'max_sal_mode' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? null : $row->max_sal_mode,
                                    'carrier_website' => $row->carrier_website,
                                    'work_type' => $row->work_type,
                                    'show_vendor' => $row->show_vendor,
                                    'closeType' => $row->closeType,
                                    'min_yearly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->min_yearly,
                                    'min_monthly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->min_monthly,
                                    'min_hourly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->min_hourly,
                                    'max_yearly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->max_yearly,
                                    'max_monthly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->max_monthly,
                                    'max_hourly' => (\App\Services\SalaryVisibilityPolicy::normalize($row->salary_visibility ?? null) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL) ? 0.00 : $row->max_hourly,
                                    'auto_date_job' => ($row->auto_date_job) ? Carbon::parse($row->auto_date_job)->toIso8601String() : null,
                                    'public_market_status' => $row->public_market_status,
                                    'work_shifts' => $row->work_shifts,
                                    'job_assign' => $job_assign ?? [],
                                    'job_qualifications' => $job_qualifications ?? [],
                                    'client_location_jobs' => $client_location_jobs ?? [],
                                    'client_city_jobs' => $client_city_jobs ?? [],
                                    'recommendation_relation' => $recommendation_relation ?? [],
                                    'job_metronics_skills' => $job_metronics_skills ?? [],
                                    'client_contact_info_jobs' => $client_contact_info_jobs ?? [],
                                    'client_name' => strtolower($row->clients_name),
                                    'logo_logical_path' => $row->logo_logical_path,
                                    'self_company' => $row->self_company,
                                    'client_company_id' => $row->client_company_id,
                                    'company_name' => strtolower($row->company_name),
                                    'logo' => $row->logo
                                ],
                                'doc_as_upsert' => true
                            ]
                    ]);
                }


            return $response;
        } catch (Exception $e) {
            Log::error('ElasticjobsIndex queue failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    
}


?>