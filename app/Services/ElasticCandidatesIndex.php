<?php

namespace App\Services;

use App\Models\JobApplication;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElasticCandidatesIndex
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function candidateIndexElastic($comid)
    {

        try {

            $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                ->where('id', '=', $comid)
                ->select('add_job_wise_condidates.id', 'add_job_wise_condidates.job_id')
                ->get();

            foreach ($add_job_wise_condidates as $add_job_wise) {

                $id = $add_job_wise->id;
                $jobId = $add_job_wise->job_id;
                $applications = JobApplication::select(
                    'job_applications.id',
                    'job_applications.thirdparty_company_id',
                    'job_applications.full_name',
                    'job_applications.email',
                    'job_applications.phone',
                    'job_applications.np_id',
                    'job_applications.last_working_date',
                    'job_applications.serving_notice_period_mode',
                    'job_applications.country',
                    'job_applications.state',
                    'job_applications.city',
                    'job_applications.city_id',
                    'job_applications.skills',
                    'job_applications.photo',
                    'job_applications.job_id',
                    'job_applications.total_exp',
                    'job_applications.rel_exp',
                    'job_applications.ctc',
                    'job_applications.current_ctc_cur',
                    'job_applications.current_ctc_mode',
                    'job_applications.current_designation',
                    'job_applications.ectc',
                    'job_applications.current_ectc_cur',
                    'job_applications.current_ectc_mode',
                    'job_applications.current_company',
                    'job_applications.ctc_hourly',
                    'job_applications.ctc_monthly',
                    'job_applications.ctc_yearly',
                    'job_applications.ectc_hourly',
                    'job_applications.ectc_monthly',
                    'job_applications.ectc_yearly',
                    'job_applications.status',
                    'add_job_wise_condidates.skill_match',
                    'add_job_wise_condidates.existing',
                    'add_job_wise_condidates.placed_end_date',
                    'add_job_wise_condidates.rec_candidate_assign_id',
                    'job_applications.duplicate',
                    'job_applications.bill_rate_currency',
                    'job_applications.bill_rate_amount',
                    'job_applications.bill_rate_mode',
                    'job_applications.condidate_type',
                    'job_applications.master_status_id',
                    'job_applications.is_viewed',
                    'job_applications.column_priority',
                    'job_applications.phone_code',
                    'job_applications.second_phone_code',
                    'job_applications.secondary_mobile',
                    'job_applications.resume',
                    'job_applications.rating',
                    'job_applications.skype_id',
                    'job_applications.cover_letter',
                    'job_applications.passport',
                    'job_applications.process_id',
                    'job_applications.qualification',
                    'job_applications.secondrary_email',
                    'job_applications.city',
                    'job_applications.pincode',
                    'job_applications.percentage_hike',
                    'job_applications.comment',
                    'job_applications.dob',
                    'job_applications.company_id as other_company',
                    'jobs.title',
                    'jobs.employment_type',
                    'jobs.company_id as client_comapny_id',
                    'job_locations.location',
                    'clients.name as client_name',
                    'users.name as posted_by',
                    'users.image as posted_by_image',
                    'submitter_com.company_name as submitted_by_company_name',
                    'master_status.name',
                    'master_status.css_style',
                    'qualification_master.name as qualification_name',
                    'state_list.state as state_name',
                    'city_list.city as city_name',
                    'countries.country_name',
                    'recommendation_relation.final_status',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.to_company_id',
                    'companies.company_name',
                    'documents.hashname',
                    'app_com.company_name as apl_company_name',
                    'owncom.company_name as post_company_name',
                    'cur_com.company_name as cur_company_name',
                    'jobs.status as job_status',
                    'add_job_wise_condidates.child_status_id as childStatus',
                    'add_job_wise_condidates.id as wiseId',
                    'add_job_wise_condidates.master_status_id as job_master_status_id',
                    'offer_job_applications.proposed_ctc',
                    'offer_job_applications.any_offer as of_any_offer',
                    'offer_job_applications.offer_approved as of_offer_approved',
                    'offer_job_applications.offer_release as of_offer_release',
                    'offer_job_applications.offer_acceptance as of_offer_acceptance',
                    'offer_job_applications.joined as of_joined',
                    'offer_job_applications.joined_date as of_joined_date',
                    'offer_job_applications.offer_currency as of_offer_currency',
                    'offer_job_applications.offer_mode as of_offer_mode',
                    'offer_job_applications.offer_amount as of_offer_amount',
                    'offer_job_applications.id as offer_id',
                    'offer_job_applications.document',
                    'offer_job_applications.sub_for_approval',
                    'offer_job_applications.commercial_service_type',
                    'offer_job_applications.commercial_model',
                    'offer_job_applications.commercial_fee_method',
                    'offer_job_applications.commercial_fee_base',
                    'offer_job_applications.commercial_fee_pct',
                    'offer_job_applications.commercial_fee_fixed',
                    'offer_job_applications.assignment_status',
                    'offer_job_applications.assignment_expected_end_date',
                    'offer_job_applications.assignment_client_location',
                    'offer_job_applications.assignment_exit_reason',
                    'offer_job_applications.assignment_project_outcome',
                    'offer_job_applications.assignment_remarks',
                    'add_job_wise_condidates.thirdparty_company_id as ClientComID',
                    'jobs.min_salary',
                    'jobs.max_salary',
                    'add_job_wise_condidates.updated_at',
                    'add_job_wise_condidates.created_at',
                    'add_job_wise_condidates.company_id',
                    'add_job_wise_condidates.submitted_by',
                    'job_applications.deleted_at',
                    'add_job_wise_condidates.job_id as jobId'
                )
                    ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                    ->join('master_status', function ($join) {
                        $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                    })
                    ->leftJoin('jobs', function ($join) {
                        $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                    })
                    ->leftJoin('recommendation_relation', function ($join) {
                        $join->on('recommendation_relation.job_id', 'jobs.id');
                    })
                    // ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                    ->leftJoin('companies', function ($join) {
                        $join->on('companies.id', 'add_job_wise_condidates.company_id');
                    })
                    ->leftJoin('companies as owncom', function ($join) {
                        $join->on('owncom.id', 'job_applications.thirdparty_company_id');
                    })
                    ->leftjoin('companies as app_com', function ($join) {
                        $join->on('app_com.id', 'job_applications.company_id');
                    })
                    ->leftjoin('companies as cur_com', function ($join) {
                        $join->on('cur_com.id', 'jobs.company_id');
                    })
                    ->leftJoin('offer_job_applications', function ($join) use ($jobId) {
                        $join->on('offer_job_applications.application_id', 'job_applications.id');
                        $join->where('offer_job_applications.job_id', $jobId);
                        // $join->where('offer_job_applications.company_id', $company_id);
                    })
                    // ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
                    ->leftjoin('clients', 'clients.id', 'jobs.client_id')
                    // ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
                    ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
                    ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
                    ->leftjoin('companies as submitter_com', function ($join) {
                        $join->on('submitter_com.id', 'users.company_id');
                    })
                    ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
                    ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                    // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
                    ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                    ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                    ->where('add_job_wise_condidates.id', $id)
                    ->orderByDesc('documents.id')->get();

                $bulkData = [];

                foreach ($applications as $application) {
                    $wiseId = $application->wiseId;

                    $intRow = DB::table('interview_schedules')
                        ->select('*')
                        ->where('job_application_id', $application->id)
                        ->where('interview_job_id', $application->jobId)
                        ->orderBy('interview_schedules.id', 'DESC')
                        ->first();

                    $job_application_skills = DB::table('job_application_skills')
                        ->where('job_application_id', $application->id)
                        ->get()
                        ->map(function ($s) {
                            return [
                                'id' => $s->id,
                                'job_application_id' => $s->id,
                                'skills' => strtolower($s->skills),
                            ];
                        })
                        ->toArray();

                    // Denormalized lookups for Phase 1 V2 Zero-N+1 rendering
                    $spokperson_name = $application->posted_by;
                    if ($application->company_id != $application->ClientComID && $application->ClientComID != 0 && $application->ClientComID > 0) {
                        $spokperson = DB::table('job_assign')
                            ->select('users.name')
                            ->join('users', 'users.id', 'job_assign.spok_user_id')
                            ->where('job_assign.job_id', $application->jobId)
                            ->where('job_assign.company_id', $application->ClientComID)
                            ->first();
                        if ($spokperson && !empty($spokperson->name)) {
                            $spokperson_name = $spokperson->name;
                        }
                    }

                    $offer_released_at = null;
                    if (!empty($application->id)) {
                        $offerAppId = DB::table('offer_job_applications')->where('application_id', $application->id)->value('id');
                        if ($offerAppId) {
                            $rawRelease = DB::table('offer_create_main_tds')
                                ->where('offer_id', $offerAppId)
                                ->where('candidate_status', 1)
                                ->orderByDesc('id')
                                ->value('created_at');
                            if ($rawRelease) {
                                $offer_released_at = Carbon::parse($rawRelease)->toIso8601String();
                            }
                        }
                    }

                    $sourcing_date = null;
                    $evaluation_date = null;
                    if (!empty($application->id) && !empty($application->jobId)) {
                        $tlRows = DB::table('updatetimeline')
                            ->where('application_id', $application->id)
                            ->where('job_id', $application->jobId)
                            ->orderByDesc('id')
                            ->get(['master_process_name', 'create_at']);

                        $sourcingRaw = optional($tlRows->firstWhere('master_process_name', 'Sourcing'))->create_at;
                        $evalRaw = optional($tlRows->first(fn($r) => in_array($r->master_process_name, ['Evaluation', 'InReview', 'Screening'])))->create_at;

                        if ($sourcingRaw) {
                            $sourcing_date = Carbon::parse($sourcingRaw)->toIso8601String();
                        }
                        if ($evalRaw) {
                            $evaluation_date = Carbon::parse($evalRaw)->toIso8601String();
                        }
                    }

                    $bulkData[] = [
                        'index' => [
                            '_index' => ELASTIC_CANDIDATES,
                            '_id' => $wiseId,
                        ],
                    ];
                    $npRaw = trim((string) $application->np_id);
                    $npDays = 999999;
                    $npDate = null;
                    $noticePeriodEpoch = 999999999;
                    if ($npRaw === 'Immediate') {
                        $npDays = 0;
                        $noticePeriodEpoch = -999999998;
                    } elseif ($npRaw !== '') {
                        $parts = explode(',', $npRaw);
                        if (isset($parts[0]) && is_numeric(trim($parts[0]))) {
                            $npDays = (int) trim($parts[0]);
                        }
                        $v = str_replace('/', '-', explode('T', trim($parts[0]))[0]);
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                            $npDate = $v;
                            try {
                                $noticePeriodEpoch = Carbon::parse($v)->timestamp;
                            } catch (\Exception $e) {}
                        }
                    }

                    $bulkData[] = [
                        'id' => $wiseId,
                        'candidate_id' => $application->id,
                        'thirdparty_company_id' => $application->thirdparty_company_id,
                        'full_name' => $application->full_name,
                        'email' => $application->email,
                        'phone' => $application->phone,
                        'np_id' => $application->np_id,
                        'last_working_date' => $application->last_working_date,
                        'np_days' => $npDays,
                        'np_date' => $npDate,
                        'notice_period_days' => $npDays,
                        'notice_period_epoch' => $noticePeriodEpoch,
                        'immediate_join' => $application->np_id,
                        'serving_notice_period_mode' => $application->serving_notice_period_mode,
                        'country_name' => $application->country_name,
                        'state_name' => $application->state_name,
                        'city_name' => $application->city_name,
                        'city_id' => $application->city_id,
                        'skills' => $application->skills,
                        'photo' => $application->photo,
                        'job_id' => $application->job_id,
                        'jobId' => $application->jobId,
                        'total_exp' => $application->total_exp,
                        'rel_exp' => $application->rel_exp,
                        'ctc' => $application->ctc,
                        'job_application_skills' => $job_application_skills,
                        'current_ctc_cur' => $application->current_ctc_cur,
                        'current_ctc_mode' => $application->current_ctc_mode,
                        'current_designation' => $application->current_designation,
                        'ectc' => $application->ectc,
                        'current_ectc_cur' => $application->current_ectc_cur,
                        'current_ectc_mode' => $application->current_ectc_mode,
                        'current_company' => $application->current_company,
                        'duplicate' => $application->duplicate,
                        'is_duplicate' => (bool) ($application->duplicate ?? 0),
                        'bill_rate_currency' => $application->bill_rate_currency,
                        'bill_rate_amount' => $application->bill_rate_amount,
                        'bill_rate_mode' => $application->bill_rate_mode,
                        'condidate_type' => $application->condidate_type,
                        'master_status_id' => $application->master_status_id,
                        'title' => $application->title,
                        'employment_type' => $application->employment_type,
                        'client_comapny_id' => $application->client_comapny_id,
                        'location' => $application->location,
                        'client_name' => $application->client_name,
                        'posted_by' => $application->posted_by,
                        'spokperson_name' => $spokperson_name,
                        'offer_released_at' => $offer_released_at,
                        'sourcing_date' => $sourcing_date,
                        'evaluation_date' => $evaluation_date,
                        'posted_by_image' => $application->posted_by_image,
                        'submitted_by_company_name' => $application->submitted_by_company_name,
                        'name' => $application->name,
                        'post_company_name' => $application->post_company_name,
                        'css_style' => $application->css_style,
                        'qualification_name' => $application->qualification_name,
                        'final_status' => $application->final_status,
                        'recommendation_id' => $application->recommendation_id,
                        'from_company_id' => $application->from_company_id,
                        'to_company_id' => $application->to_company_id,
                        'company_id' => $application->company_id,
                        'company_name' => $application->company_name,
                        'apl_company_name' => $application->apl_company_name,
                        'cur_company_name' => $application->cur_company_name,
                        'job_status' => $application->job_status,
                        'childStatus' => $application->childStatus,
                        'wiseId' => $application->wiseId,
                        'job_master_status_id' => $application->job_master_status_id,
                        'of_any_offer' => $application->of_any_offer,
                        'of_offer_approved' => $application->of_offer_approved,
                        'of_offer_release' => $application->of_offer_release,
                        'of_offer_acceptance' => $application->of_offer_acceptance,
                        'of_joined' => $application->of_joined,
                        'of_joined_date' => $application->of_joined_date,
                        'of_offer_currency' => $application->of_offer_currency,
                        'of_offer_mode' => $application->of_offer_mode,
                        'of_offer_amount' => $application->of_offer_amount,
                        'offer_id' => $application->offer_id,
                        'ClientComID' => $application->ClientComID,
                        'min_salary' => $application->min_salary,
                        'max_salary' => $application->max_salary,
                        'is_viewed' => $application->is_viewed,
                        'column_priority' => $application->column_priority,
                        'phone_code' => $application->phone_code,
                        'second_phone_code' => $application->second_phone_code,
                        'secondary_mobile' => $application->secondary_mobile,
                        'ctc_hourly' => $application->ctc_hourly,
                        'ctc_monthly' => $application->ctc_monthly,
                        'ctc_yearly' => $application->ctc_yearly,
                        'ectc_hourly' => $application->ectc_hourly,
                        'ectc_monthly' => $application->ectc_monthly,
                        'ectc_yearly' => $application->ectc_yearly,
                        'status' => $application->status,
                        'skill_match' => $application->skill_match,
                        'existing' => $application->existing,
                        'placed_end_date' => $application->placed_end_date,
                        'rec_candidate_assign_id' => $application->rec_candidate_assign_id,
                        'resume' => $application->resume,
                        'rating' => $application->rating,
                        'skype_id' => $application->skype_id,
                        'cover_letter' => $application->cover_letter,
                        'passport' => $application->passport,
                        'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                        'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                        'deleted_at' => ($application->deleted_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                        'process_id' => $application->process_id,
                        'qualification' => $application->qualification,
                        'secondrary_email' => $application->secondrary_email,
                        'city' => $application->city,
                        'country' => $application->country,
                        'pincode' => $application->pincode,
                        'state' => $application->state,
                        'percentage_hike' => $application->percentage_hike,
                        'comment' => $application->comment,
                        'dob' => $application->dob,
                        'other_company' => $application->other_company,
                        'master_status_name' => $application->master_status_name,
                        'proposed_ctc' => $application->proposed_ctc,
                        'document' => $application->document,
                        'sub_for_approval' => $application->sub_for_approval,
                        'own_company_name' => $application->own_company_name,
                        'rjobid' => $application->rjobid,
                        'submitted_by' => $application->submitted_by,
                        'hashname' => $application->hashname,
                        'schedule_date' => ($intRow) ? Carbon::parse($intRow->schedule_date)->toIso8601String() : null,
                        'timezone_abbreviation' => ($intRow) ? $intRow->timezone_abbreviation : null,
                        'schedule_to_time' => ($intRow) ? $intRow->schedule_to_time : null,
                        // Commercial configuration
                        'commercial_service_type' => $application->commercial_service_type ?? null,
                        'commercial_model' => $application->commercial_model ?? null,
                        'commercial_fee_method' => $application->commercial_fee_method ?? null,
                        'commercial_fee_base' => $application->commercial_fee_base ?? null,
                        'commercial_fee_pct' => $application->commercial_fee_pct ?? null,
                        'commercial_fee_fixed' => $application->commercial_fee_fixed ?? null,
                        // Assignment details
                        'assignment_status' => $application->assignment_status ?? null,
                        'assignment_expected_end_date' => $application->assignment_expected_end_date ?? null,
                        'assignment_client_location' => $application->assignment_client_location ?? null,
                        'assignment_exit_reason' => $application->assignment_exit_reason ?? null,
                        'assignment_project_outcome' => $application->assignment_project_outcome ?? null,
                        'assignment_remarks' => $application->assignment_remarks ?? null,
                    ];
                }

                // dd($bulkData);

                $response = $this->elasticService->getClient()->bulk([
                    'body' => $bulkData,
                ]);
            }

            return true;
        } catch (Exception $e) {
            Log::error('ElasticCompanyIndex queue failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
