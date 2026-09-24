<?php

/**
 * Handles all heavy post-save ES indexing and secondary table work for update().
 * Runs on the 'default' queue so ES failures don't block the HTTP response.
 */

namespace App\Jobs;

use App\Helper\GeneralMethod;
use App\Models\JobApplication;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCandidateUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public int $timeout = 120;

    public function __construct(
        private readonly int $jobApplicationId,
        private readonly int $addJobWiseCandidateId,
        private readonly int $jobId,
        private readonly int $companyId,
        private readonly array $requestData,
    ) {
        $this->afterCommit = true;
        $this->onQueue('indexing');
    }

    public function handle(): void
    {
        try {
            $elasticService = app(ElasticService::class);
            $client = $elasticService->getClient();
            $appId = $this->jobApplicationId;
            $jobId = $this->jobId;
            $data = $this->requestData;

            // 1. YearskillId — delete old rows + ES docs, reinsert
            $yearSkillId = $data['YearskillId'] ?? [];
            $monthSkillId = $data['MonthskillId'] ?? [];
            $ratingArr = $data['rating'] ?? [];

            if (! empty($yearSkillId) && is_array($yearSkillId)) {
                DB::table('candidate_job_skill_exp')
                    ->where('candidate_id', $appId)
                    ->where('job_id', $jobId)
                    ->delete();

                $client->deleteByQuery([
                    'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                    'conflicts' => 'proceed',
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['candidate_id' => $appId]],
                                    ['term' => ['job_id' => $jobId]],
                                ],
                            ],
                        ],
                    ],
                ]);

                foreach ($yearSkillId as $skillId => $value) {
                    $ss = $value >= 1 ? 'Years' : 'Year';
                    $valMonth = $monthSkillId[$skillId] ?? 0;
                    $skillValue = $value.'.'.$valMonth.' '.$ss;
                    $rating = (int) ($ratingArr[$skillId] ?? 0);

                    $expId = DB::table('candidate_job_skill_exp')->insertGetId([
                        'job_skill_id' => $skillId,
                        'skill_value' => $skillValue,
                        'job_id' => $jobId,
                        'rating' => $rating,
                        'candidate_id' => $appId,
                    ]);

                    $skillRow = DB::table('job_metronics_skills')
                        ->select('skill_name')
                        ->where('id', $skillId)
                        ->first();

                    $client->index([
                        'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                        'id' => $expId,
                        'body' => [
                            'skill_name' => $skillRow->skill_name ?? '',
                            'job_skill_id' => $skillId,
                            'skill_value' => $skillValue,
                            'job_id' => $jobId,
                            'rating' => $rating,
                            'candidate_id' => $appId,
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                    ]);
                }
            }

            // 2. application_additional_detail — delete + ES delete, reinsert
            $jobApplication = JobApplication::withoutGlobalScopes()->find($appId);
            if (! $jobApplication) {
                Log::warning("ProcessCandidateUpdateJob: JobApplication #{$appId} not found");

                return;
            }

            DB::table('application_additional_detail')
                ->where('application_id', $appId)
                ->where('job_id', $jobApplication->job_id)
                ->delete();

            $client->deleteByQuery([
                'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                'conflicts' => 'proceed',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['application_id' => (int) $appId]],
                                ['term' => ['job_id' => (int) $jobApplication->job_id]],
                            ],
                        ],
                    ],
                ],
            ]);

            $additionalDetails = DB::table('job_additional_detail')
                ->where('job_id', $jobApplication->job_id)
                ->orderBy('id', 'ASC')
                ->get();

            $additionalInput = $data['additional_detail'] ?? [];
            $inc = 0;
            foreach ($additionalDetails as $jd) {
                $expId = DB::table('application_additional_detail')->insertGetId([
                    'job_id' => $jobApplication->job_id,
                    'question_id' => $jd->id,
                    'application_id' => $appId,
                    'job_detail_value' => $jd->detail_value,
                    'application_detail_value' => $additionalInput[$inc] ?? null,
                ]);
                $client->update([
                    'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                    'id' => $expId,
                    'body' => [
                        'doc' => [
                            'job_id' => $jobApplication->job_id,
                            'question_id' => $jd->id,
                            'application_id' => $appId,
                            'job_detail_value' => $jd->detail_value,
                            'application_detail_value' => $additionalInput[$inc] ?? null,
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                        'doc_as_upsert' => true,
                    ],
                ]);
                $inc++;
            }

            // 3. Main ELASTIC_CANDIDATES update (if we have an add_job_wise_condidates row)
            if ($this->addJobWiseCandidateId > 0) {
                $addJobWiseCandidateId = $this->addJobWiseCandidateId;

                $exists = $client->exists([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $addJobWiseCandidateId,
                ]);

                if ($exists) {
                    $applications_up = JobApplication::select(
                        'job_applications.*',
                        'state_list.state as state_name',
                        'city_list.city as city_name',
                        'countries.country_name',
                        'qualification_master.name as qualification_name'
                    )
                        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                        ->leftJoin('countries', 'countries.id', '=', 'job_applications.country')
                        ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                        ->leftJoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
                        ->where('add_job_wise_condidates.id', $addJobWiseCandidateId)
                        ->first();
                       
                    if ($applications_up) {
                        // Sync skills from job_metronics_skills that have exp data
                        $jobSkill = DB::table('job_metronics_skills')
                            ->leftJoin('candidate_job_skill_exp', function ($join) use ($appId) {
                                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                                    ->where('candidate_job_skill_exp.candidate_id', '=', $appId);
                            })
                            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
                            ->where('job_metronics_skills.job_id', '=', $jobId)
                            ->get();

                        foreach ($jobSkill as $sval) {
                            $skillValue = str_replace(['Years', 'Year', ' '], '', $sval->skill_value);
                            if (! is_null($skillValue) && ! empty($skillValue) && $skillValue !== '0.') {
                                if (! DB::table('job_application_skills')
                                    ->where('job_application_id', $appId)
                                    ->where('skills', 'LIKE', '%'.$sval->skill_name.'%')
                                    ->exists()
                                ) {
                                    DB::table('job_application_skills')->insert([
                                        'job_application_id' => $appId,
                                        'skills' => $sval->skill_name,
                                    ]);
                                }
                            }
                        }

                        $job_application_skills = DB::table('job_application_skills')
                            ->where('job_application_id', $appId)
                            ->get()
                            ->map(fn ($s) => [
                                'id' => $s->id,
                                'job_application_id' => $s->id,
                                'skills' => strtolower($s->skills),
                            ])
                            ->toArray();

                        $application_skills = DB::table('job_application_skills')
                            ->where('job_application_id', $appId)
                            ->pluck('skills')
                            ->map(fn ($skill) => strtolower($skill))
                            ->implode(',');

                        // Format interview dates
                        $defaultTimeFormat = GeneralMethod::defaultTimeFormat($this->companyId);
                        $formatDt = function ($date, $time) {
                            if (empty($date) || empty($time)) {
                                return null;
                            }

                            return date('Y-m-d H:i:s', strtotime($date.' '.$time));
                        };

                        $interviewDateOne = $formatDt($data['interviewDateOne'] ?? null, $data['interviewTimeOne'] ?? null);
                        $interviewDateTwo = $formatDt($data['interviewDateTwo'] ?? null, $data['interviewTimeTwo'] ?? null);
                        $interviewDateThree = $formatDt($data['interviewDateThree'] ?? null, $data['interviewTimeThree'] ?? null);
                        $interviewDateFour = $formatDt($data['interviewDateFour'] ?? null, $data['interviewTimeFour'] ?? null);
                        $interviewDateFive = $formatDt($data['interviewDateFive'] ?? null, $data['interviewTimeFive'] ?? null);
                        $interviewDateSix = $formatDt($data['interviewDateSix'] ?? null, $data['interviewTimeSix'] ?? null);

                        $updateParams = [
                            'index' => ELASTIC_CANDIDATES,
                            'id' => $addJobWiseCandidateId,
                            'retry_on_conflict' => 5,
                            'body' => [
                                'doc' => [
                                    'thirdparty_company_id' => $applications_up->thirdparty_company_id,
                                    'full_name' => $applications_up->full_name,
                                    'email' => $applications_up->email,
                                    'phone' => $applications_up->phone,
                                    'np_id' => $applications_up->np_id,
                                    'last_working_date' => $applications_up->last_working_date,
                                    'serving_notice_period_mode' => $applications_up->serving_notice_period_mode,
                                    'country' => $applications_up->country,
                                    'state' => $applications_up->state,
                                    'gender' => $applications_up->gender,
                                    'city' => $applications_up->city,
                                    'city_id' => $applications_up->city_id,
                                    'skills' => $application_skills,
                                    'photo' => $applications_up->photo,
                                    'pincode' => $applications_up->pincode,
                                    'total_exp' => $applications_up->total_exp,
                                    'rel_exp' => $applications_up->rel_exp,
                                    'careersummary' => $applications_up->careersummary,
                                    'nationality' => $applications_up->nationality,
                                    'wm' => $applications_up->wm,
                                    'employmentType' => $applications_up->employmentType,
                                    'oh' => $applications_up->oh,
                                    'visastatu' => $applications_up->visastatu,
                                    'referencename' => $applications_up->referencename,
                                    'reason_for_job_change' => $applications_up->reason_for_job_change,
                                    'ctc' => $applications_up->ctc,
                                    'job_application_skills' => $job_application_skills,
                                    'current_ctc_cur' => $applications_up->current_ctc_cur,
                                    'current_ctc_mode' => $applications_up->current_ctc_mode,
                                    'current_designation' => $applications_up->current_designation,
                                    'ectc' => $applications_up->ectc,
                                    'current_ectc_cur' => $applications_up->current_ectc_cur,
                                    'current_ectc_mode' => $applications_up->current_ectc_mode,
                                    'current_company' => $applications_up->current_company,
                                    'ctc_hourly' => $applications_up->ctc_hourly,
                                    'ctc_monthly' => $applications_up->ctc_monthly,
                                    'ctc_yearly' => $applications_up->ctc_yearly,
                                    'ectc_hourly' => $applications_up->ectc_hourly,
                                    'ectc_monthly' => $applications_up->ectc_monthly,
                                    'ectc_yearly' => $applications_up->ectc_yearly,
                                    'status' => $applications_up->status,
                                    'duplicate' => (int) ($applications_up->duplicate ?? 0),
                                    'is_duplicate' => (bool) (($applications_up->duplicate ?? 0) == 1),
                                    'bill_rate_currency' => $applications_up->bill_rate_currency,
                                    'bill_rate_amount' => $applications_up->bill_rate_amount,
                                    'bill_rate_mode' => $applications_up->bill_rate_mode,
                                    'condidate_type' => $applications_up->condidate_type,
                                    'master_status_id' => $applications_up->master_status_id,
                                    'is_viewed' => $applications_up->is_viewed,
                                    'column_priority' => $applications_up->column_priority,
                                    'phone_code' => $applications_up->phone_code,
                                    'second_phone_code' => $applications_up->second_phone_code,
                                    'secondary_mobile' => $applications_up->secondary_mobile,
                                    'resume' => $applications_up->resume,
                                    'qualification_name' => $applications_up->qualification_name,
                                    'qualification' => $applications_up->qualification,
                                    'country_name' => $applications_up->country_name,
                                    'state_name' => $applications_up->state_name,
                                    'city_name' => $applications_up->city_name,
                                    'dob' => $applications_up->dob,
                                    'percentage_hike' => $applications_up->percentage_hike,
                                    'linkedin' => $data['linkedin'] ?? null,
                                    'github' => $data['github'] ?? null,
                                    'behance' => $data['behance'] ?? null,
                                    'otherLink' => $data['otherLink'] ?? null,
                                    'interviewDateOne' => $interviewDateOne ? Carbon::parse($interviewDateOne)->toIso8601String() : null,
                                    'interview_mode_one' => $data['interview_mode_one'] ?? null,
                                    'interviewDateTwo' => $interviewDateTwo ? Carbon::parse($interviewDateTwo)->toIso8601String() : null,
                                    'interview_mode_two' => $data['interview_mode_two'] ?? null,
                                    'interviewDateThree' => $interviewDateThree ? Carbon::parse($interviewDateThree)->toIso8601String() : null,
                                    'interview_mode_three' => $data['interview_mode_three'] ?? null,
                                    'interviewDateFour' => $interviewDateFour ? Carbon::parse($interviewDateFour)->toIso8601String() : null,
                                    'interview_mode_four' => $data['interview_mode_four'] ?? null,
                                    'interviewDateFive' => $interviewDateFive ? Carbon::parse($interviewDateFive)->toIso8601String() : null,
                                    'interview_mode_five' => $data['interview_mode_five'] ?? null,
                                    'interviewDateSix' => $interviewDateSix ? Carbon::parse($interviewDateSix)->toIso8601String() : null,
                                    'interview_mode_six' => $data['interview_mode_six'] ?? null,
                                    'comment' => $applications_up->comment,
                                    'recruiter_note' => $applications_up->recruiter_note,
                                ],
                                'doc_as_upsert' => true,
                                'detect_noop' => false,
                            ],
                            'refresh' => false,
                        ];

                        $client->update($updateParams);
                    }
                }

                GeneralMethod::elasticBenchCandidateAddEditIndex($appId);
                GeneralMethod::addCompanyCandidate($this->companyId, $addJobWiseCandidateId);
                GeneralMethod::addCompanyBenchCandidate($this->companyId, $appId);
            }

        } catch (\Throwable $e) {
            Log::error('ProcessCandidateUpdateJob failed', [
                'job_application_id' => $this->jobApplicationId,
                'add_job_wise_candidate_id' => $this->addJobWiseCandidateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
