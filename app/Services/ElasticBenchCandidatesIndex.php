<?php

namespace App\Services;

use App\Models\JobApplication;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElasticBenchCandidatesIndex
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function benchCandidateIndexElastic($comid)
    {

        try {

            $applications = JobApplication::select(
                'job_applications.id',
                'job_applications.thirdparty_company_id',
                'job_applications.outsourcing_type',
                'job_applications.wm',
                'job_applications.full_name',
                'job_applications.company_id',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.np_id',
                    'job_applications.last_working_date',
                'job_applications.serving_notice_period_mode',
                'job_applications.country',
                'job_applications.submitted_by',
                'job_applications.state',
                'job_applications.city',
                'job_applications.city_id',
                'job_applications.skills',
                'job_applications.photo',
                'job_applications.job_id',
                'job_applications.total_exp',
                'job_applications.ads_switch',
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
                'job_applications.duplicate',
                'job_applications.bill_rate_currency',
                'job_applications.bill_rate_amount',
                'job_applications.bill_rate_mode',
                'job_applications.condidate_type',
                'job_applications.master_status_id',
                'job_applications.is_viewed',
                'job_applications.comission_type',
                'job_applications.column_priority',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.secondary_mobile',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.skype_id',
                'job_applications.cover_letter',
                'job_applications.recruiter_note',
                'job_applications.passport',
                'job_applications.process_id',
                'job_applications.qualification',
                'job_applications.secondrary_email',
                'job_applications.city',
                'job_applications.pincode',
                'job_applications.percentage_hike',
                'job_applications.comment',
                'job_applications.countforadd',
                'job_applications.bench_id',
                'job_applications.superadminstatus',
                'job_applications.updated_at',
                'job_applications.updated_at',
                'job_applications.dob',
                'job_applications.deleted_at',
                'companies.company_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'bench_master_status.name as master_status_name',
                'bench_master_status.css_style',
                'qualification_master.name as qualification_name',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'countries.country_name',
                'bench_candidate_rm_comments.comments as rm_comments'
            )->join('bench_master_status', 'bench_master_status.id', 'job_applications.status')
                ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                ->leftjoin('companies', 'companies.id', 'job_applications.company_id')
                ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->leftJoin('bench_candidate_rm_comments', function ($join) {
                    $join->on('bench_candidate_rm_comments.candidate_id', '=', 'job_applications.id')
                        ->on('bench_candidate_rm_comments.company_id', '=', 'job_applications.company_id');
                })
                ->where('condidate_type', '=', 'bench_condidate')
                ->where('job_applications.id', $comid)
                ->get();

            $bulkData = [];
            $ik = 1;
            $snapshot = \App\Helper\GeneralMethod::getLatestFxSnapshot();
            $snapshotVersion = $snapshot['version'] ?? '202609-001';
            foreach ($applications as $application) {

                $add_job_wise_condidates = DB::table('add_job_wise_condidates')
                    ->where('job_application_id', $application->id)
                    ->get()
                    ->map(function ($s) {
                        [
                            'id' => $s->id,
                            'job_application_id' => $s->job_application_id,
                            'job_id' => $s->job_id,
                            'status_id' => $s->status_id,
                            'master_status_id' => $s->master_status_id,
                            'child_status_id' => $s->child_status_id,
                        ];
                    })
                    ->toArray();

                $job_application_skills = DB::table('job_application_skills')
                    ->where('job_application_id', $application->id)
                    ->get()
                // ->map(fn($row) => (array) $row)
                    ->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'job_application_id' => $s->id,
                            'skills' => strtolower($s->skills),
                        ];
                    })
                    ->toArray();

                $viewCount = DB::table('bench_count_view_download')
                    ->where('type', '=', 1)
                    ->where('status', '=', 0)
                    ->where('candidate_id', '=', $application->id)
                    ->count();
                $downloadCount = DB::table('bench_count_view_download')
                    ->where('type', '=', 2)
                    ->where('status', '=', 0)
                    ->where('candidate_id', '=', $application->id)
                    ->count();

                $hmData = DB::table('bench_assign')
                    ->join('users', 'users.id', '=', 'bench_assign.user_id')
                    ->select('users.id', 'users.name', 'users.image')
                    ->where('bench_assign.bench_applications_id', '=', $application->id)
                    ->where('bench_assign.company_id', '=', $application->company_id)
                    ->where('users.company_id', '=', $application->company_id)
                    ->orderBy('users.name', 'ASC')
                    ->get();

                $pipelineCounts = DB::table('add_job_wise_condidates')
                    ->select('master_status_id', DB::raw('COUNT(*) as total'))
                    ->where('job_application_id', $application->id)
                    ->groupBy('master_status_id')
                    ->pluck('total', 'master_status_id')
                    ->toArray();

                /* Intent: Pre-fetch candidate skill ratings from bench_candidate_skill_experience to embed in ES document payload and eliminate Blade N+1 queries */
                /* Tenancy: Scoped by candidate_id */
                $skillExperiences = DB::table('bench_candidate_skill_experience')
                    ->where('candidate_id', $application->id)
                    ->get()
                    ->map(function ($sk) {
                        return [
                            'skill_key' => strtolower(trim($sk->skill_key)),
                            'rating' => (int) ($sk->rating ?? 0),
                            'year' => (int) ($sk->year ?? 0),
                            'month' => (int) ($sk->month ?? 0),
                            'total_months' => (int) ($sk->total_months ?? 0),
                        ];
                    })
                    ->toArray();

                $ads_switch_rank = (strtolower((string) ($application->ads_switch ?? '')) === 'yes') ? 0 : 1;

                $np_days = 0;
                if ($application->serving_notice_period_mode == 0) {
                    $np_days = (int) $application->np_id;
                } elseif ($application->serving_notice_period_mode == 1 && ! empty($application->np_id)) {
                    $lwdTime = strtotime($application->np_id);
                    if ($lwdTime && $lwdTime > time()) {
                        $np_days = (int) ceil(($lwdTime - time()) / 86400);
                    } else {
                        $np_days = 0;
                    }
                } elseif ($application->serving_notice_period_mode == 2) {
                    $np_days = 0;
                }

                $bulkData[] = [
                    'index' => [
                        '_index' => ELASTIC_CANDIDATES_BENCH,
                        '_id' => $application->id,
                    ],
                ];

                $bulkData[] = [
                    'id' => $application->id,
                    'bench_id' => $application->id,
                    'thirdparty_company_id' => $application->thirdparty_company_id,
                    'full_name' => $application->full_name,
                    'company_id' => $application->company_id,
                    'comission_type' => $application->comission_type,
                    'outsourcing_type' => $application->outsourcing_type,
                    'wm' => $application->wm,
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'np_id' => $application->np_id,
                        'last_working_date' => $application->last_working_date,
                    'immediate_join' => $application->np_id,
                    'serving_notice_period_mode' => $application->serving_notice_period_mode,
                    'np_days' => $np_days,
                    'notice_period_days' => $np_days,
                    'ads_switch_rank' => $ads_switch_rank,
                    'candidate_skills' => $skillExperiences,
                    'skill_experiences' => $skillExperiences,
                    /* Intent: Correctly map master_status_id values to pipeline count keys during ES indexing (1=Sourcing, 2=Screening, 3=Evaluation, 4=Negotiation, 5=Offered, 6=Joined, 7/9=Rejected, 8=InReview) */
                    'sourcing_count' => $pipelineCounts[1] ?? 0,
                    'screening_count' => $pipelineCounts[2] ?? 0,
                    'interview_count' => ($pipelineCounts[8] ?? 0) + ($pipelineCounts[3] ?? 0),
                    'evaluation_count' => $pipelineCounts[3] ?? 0,
                    'negotiation_count' => $pipelineCounts[4] ?? 0,
                    'offered_count' => $pipelineCounts[5] ?? 0,
                    'joined_count' => $pipelineCounts[6] ?? 0,
                    'rejected_count' => ($pipelineCounts[7] ?? 0) + ($pipelineCounts[9] ?? 0),
                    'state' => $application->state,
                    'city' => $application->city,
                    'submitted_by' => $application->submitted_by,
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
                    'ctc_hourly' => $application->ctc_hourly,
                    'ctc_monthly' => $application->ctc_monthly,
                    'ctc_yearly' => $application->ctc_yearly,
                    'ectc_hourly' => $application->ectc_hourly,
                    'ectc_monthly' => $application->ectc_monthly,
                    'ectc_yearly' => $application->ectc_yearly,
                    'ads_switch' => $application->ads_switch,
                    'job_application_skills' => $job_application_skills,
                    'recruiter_note' => $application->rm_comments ?? $application->recruiter_note,
                    'rm_comments' => $application->rm_comments ?? $application->recruiter_note,
                    'hiring_hr' => $hmData,
                    'jobs' => $add_job_wise_condidates,
                    'viewCount' => $viewCount,
                    'downloadCount' => $downloadCount,
                    'current_ctc_cur' => $application->current_ctc_cur,
                    'current_ctc_mode' => $application->current_ctc_mode,
                    'current_designation' => $application->current_designation,
                    'ectc' => $application->ectc,
                    'current_ectc_cur' => $application->current_ectc_cur,
                    'current_ectc_mode' => $application->current_ectc_mode,
                    'current_company' => $application->current_company,
                    'duplicate' => $application->duplicate,
                    'bill_rate_currency' => $application->bill_rate_currency,
                    'bill_rate_amount' => $application->bill_rate_amount,
                    'bill_rate_mode' => $application->bill_rate_mode,
                    'bill_rate_usd_hourly_index' => \App\Helper\GeneralMethod::convertToUSDHourly(
                        $application->bill_rate_amount,
                        $application->bill_rate_currency,
                        (int) ($application->bill_rate_mode ?? 0)
                    ),
                    'pay_rate_usd_hourly_index' => \App\Helper\GeneralMethod::convertToUSDHourly(
                        $application->ctc,
                        $application->current_ctc_cur,
                        (int) ($application->current_ctc_mode ?? 0)
                    ),
                    'fx_snapshot_version' => $snapshotVersion,
                    'condidate_type' => $application->condidate_type,
                    'master_status_id' => $application->master_status_id,
                    'posted_by' => $application->posted_by,
                    'is_viewed' => $application->is_viewed,
                    'column_priority' => $application->column_priority,
                    'phone_code' => $application->phone_code,
                    'second_phone_code' => $application->second_phone_code,
                    'secondary_mobile' => $application->secondary_mobile,
                    'rating' => $application->rating,
                    'skype_id' => $application->skype_id,
                    'cover_letter' => $application->cover_letter,
                    'passport' => $application->passport,
                    'process_id' => $application->process_id,
                    'secondrary_email' => $application->secondrary_email,
                    'pincode' => $application->pincode,
                    'comment' => $application->comment,
                    'dob' => $application->dob,
                    'updated_at' => ($application->updated_at) ? Carbon::parse($application->updated_at)->toIso8601String() : null,
                    'created_at' => ($application->created_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                    'deleted_at' => ($application->deleted_at) ? Carbon::parse($application->created_at)->toIso8601String() : null,
                    'posted_by_image' => $application->posted_by_image,
                    'master_status_name' => $application->master_status_name,
                    'css_style' => $application->css_style,
                    'company_name' => $application->company_name,
                    'status' => $application->status,
                    'resume' => $application->resume,
                    'qualification' => $application->qualification,
                    'qualification_name' => $application->qualification_name,
                    'percentage_hike' => $application->percentage_hike,
                    'superadminstatus' => $application->superadminstatus,
                    'countforadd' => $application->countforadd,

                    // V2 Pre-computed sorting & nested skills fields
                    'ads_switch_order' => strtolower($application->ads_switch ?? 'no') === 'yes' ? 0 : 1,
                    'np_epoch_day' => (function ($npId) {
                        $v = trim((string) $npId);
                        if (empty($v)) {
                            return 999999999;
                        }
                        if (strtolower($v) === 'immediate') {
                            return -999999998;
                        }
                        if (($comma = strpos($v, ',')) !== false) {
                            $v = trim(substr($v, 0, $comma));
                        }
                        if (($t = strpos($v, 'T')) !== false) {
                            $v = substr($v, 0, $t);
                        }
                        $v = str_replace('/', '-', $v);
                        try {
                            return Carbon::parse($v)->getTimestamp() / 86400;
                        } catch (\Exception $e) {
                            return 999999999;
                        }
                    })($application->np_id),
                    'np_is_immediate' => strtolower(trim((string) ($application->np_id ?? ''))) === 'immediate',
                    'candidate_skills' => DB::table('bench_candidate_skill_experience')
                        ->where('candidate_id', $application->id)
                        ->get()
                        ->map(function ($sk) {
                            return [
                                'skill_key' => strtolower(trim($sk->skill_key)),
                                'rating' => (int) $sk->rating,
                                'total_months' => (int) $sk->total_months,
                            ];
                        })->toArray(),
                ];
                $ik++;
            }
            $response = $this->elasticService->getClient()->bulk([
                'refresh' => true,
                'body' => $bulkData,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('ElasticCompanyIndex queue failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
