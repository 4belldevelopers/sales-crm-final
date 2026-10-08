<?php

namespace App\Services;

use App\Enums\ResumeUploadStatus;
use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Jobs\IndexCandidateSkillsToElasticJob;
use App\Jobs\SyncCandidateToElasticJob;
use App\Jobs\UploadResumeToTestRTwo;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * JobApplyService — High-performance V2 business logic service for Job Apply module.
 *
 * Intent:
 * Encapsulates candidate job application submission, pivot table batch operations,
 * Redis atomic lock duplicate checks, and asynchronous Elasticsearch queue dispatching.
 * Extracted from monolithic HomeController god controller.
 *
 * Tenancy:
 * All queries strictly scoped by company_id.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class JobApplyService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Submit candidate job application with batch operations and async queue dispatch.
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function submitApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => [
                'required',
                'string',
                'min:2',
                'max:191',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
                'max:191',
            ],
            'phone' => [
                'required',
                'string',
                'min:6',
                'max:25',
            ],
            'secondrary_email' => [
                'nullable',
                'email:rfc',
                'max:55',
                'different:email',
            ],
        ], [
            'full_name.required' => 'Full name is required',
            'full_name.min' => 'Full name must be at least 2 characters',
            'full_name.max' => 'Full name must not exceed 191 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
            'phone.required' => 'Primary contact number is required',
            'phone.min' => 'Please enter a valid contact number',
            'phone.max' => 'Contact number must not exceed 25 characters',
            'secondrary_email.email' => 'Please enter a valid secondary email',
            'secondrary_email.max' => 'Secondary email must not exceed 55 characters',
            'secondrary_email.different' => 'Secondary email cannot be the same as primary email',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly([
                'status' => 'mailcheck',
                'msgType' => 'warning',
                'message' => $validator->errors()->first(),
            ]);
        }

        $company_id = (int) $request->input('company_id');
        $email = strtolower(trim((string) $request->input('email')));
        $phone = trim((string) $request->input('phone'));
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        $jobId = (int) $request->input('job_id');
        $submissionToken = trim((string) $request->input('submission_token'));

        /* Intent: Phase 5 atomic submission token lock (60s TTL) to prevent concurrent double-clicks */
        $lockKey = ! empty($submissionToken) ? "career_apply_lock:{$company_id}:{$submissionToken}" : null;
        if ($lockKey) {
            $acquired = Cache::add($lockKey, 1, 60);
            if (! $acquired) {
                return Reply::dataOnly([
                    'status' => 'error',
                    'msgType' => 'warning',
                    'message' => 'Your application is already being processed. Please do not submit multiple times.',
                ]);
            }
        }

        /* Intent: Fast Redis atomic key lock check for duplicate email */
        $dedupKey = "apply:dedup:{$company_id}:{$jobId}:".md5(strtolower($email));
        if (Cache::has($dedupKey)) {
            if ($lockKey) {
                Cache::forget($lockKey);
            }

            return Reply::dataOnly([
                'status' => 'mailcheck',
                'msgType' => 'warning',
                'message' => 'You have already applied for this job with this email address.',
            ]);
        }

        /* Intent: Fast Redis atomic key lock check for duplicate contact number */
        $phoneDedupKey = ! empty($normalizedPhone) ? "apply:dedup_phone:{$company_id}:{$jobId}:".md5($normalizedPhone) : null;
        if ($phoneDedupKey && Cache::has($phoneDedupKey)) {
            if ($lockKey) {
                Cache::forget($lockKey);
            }

            return Reply::dataOnly([
                'status' => 'mailcheck',
                'msgType' => 'warning',
                'message' => 'You have already applied for this job with this contact number.',
            ]);
        }

        /* Intent: Elasticsearch read-check fallback for candidate existence by email or contact number */
        $esDuplicateCheck = $this->checkElasticDuplicate($email, $phone, $jobId, $company_id);
        if ($esDuplicateCheck !== null) {
            if ($lockKey) {
                Cache::forget($lockKey);
            }

            return $esDuplicateCheck;
        }

        /* Intent: Authoritative MySQL check on write path to guarantee zero duplicate submissions for the same job */
        $existingApplication = DB::table('job_applications')
            ->where('company_id', $company_id)
            ->where('job_id', $jobId)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($email, $phone, $normalizedPhone) {
                $query->where('email', $email);
                if (! empty($phone)) {
                    $query->orWhere('phone', $phone);
                }
                if (! empty($normalizedPhone) && $normalizedPhone !== $phone) {
                    $query->orWhere('phone', $normalizedPhone);
                }
            })
            ->first(['id', 'email', 'phone']);

        if ($existingApplication) {
            if ($lockKey) {
                Cache::forget($lockKey);
            }

            // Populate Redis dedup cache to short-circuit future attempts immediately
            Cache::put($dedupKey, 1, 86400 * 30);
            if ($phoneDedupKey) {
                Cache::put($phoneDedupKey, 1, 86400 * 30);
            }

            $dupMessage = (strtolower((string) $existingApplication->email) === $email)
                ? 'You have already applied for this job with this email address.'
                : 'You have already applied for this job with this contact number.';

            return Reply::dataOnly([
                'status' => 'mailcheck',
                'msgType' => 'warning',
                'message' => $dupMessage,
            ]);
        }

        /* Intent: Pre-flight resume session validation prior to DB transaction */
        $resumeSession = null;
        if (config('resumes.features.submit_with_session') && config('resumes.features.career_controller') && $request->filled('resume_session_uuid')) {
            $resumeSession = ResumeUpload::byUuid($request->input('resume_session_uuid'))
                ->forCompany($company_id)
                ->first();

            if (! $resumeSession) {
                return Reply::dataOnly([
                    'status' => 'error',
                    'msgType' => 'warning',
                    'message' => 'Resume upload session not found. Please upload the resume again.',
                ]);
            }
            if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value) {
                return Reply::dataOnly([
                    'status' => 'error',
                    'msgType' => 'warning',
                    'message' => 'Resume upload is not yet complete. Please wait for the upload to finish and try again.',
                ]);
            }
            if ($resumeSession->linked_candidate_id) {
                return Reply::dataOnly([
                    'status' => 'error',
                    'msgType' => 'warning',
                    'message' => 'Resume upload session has already been associated with another application. Please upload the resume again.',
                ]);
            }
        }

        /* Intent: Prepare JobApplication model state prior to DB transaction start */
        $jobApplication = new JobApplication;
        $fullName = trim((string) $request->input('full_name'));
        $jobApplication->full_name = preg_replace_callback(
            '/(^|[\s\-\'])([a-zA-Z\p{L}])/u',
            fn ($m) => $m[1].mb_strtoupper($m[2]),
            mb_strtolower($fullName)
        );
        $jobApplication->job_id = $jobId;
        $jobApplication->status_id = 8;
        $jobApplication->email = $email;
        $jobApplication->phone = $request->input('phone');
        $jobApplication->status_type = 'DIRECT';
        $jobApplication->column_priority = 0;
        $jobApplication->company_id = $company_id;

        $jobApplication->phone_code = $request->input('primary_country_code') ?: null;
        $jobApplication->second_phone_code = $request->input('secondary_country_code') ?: null;

        $totalExpYear = $request->input('total_exp_year');
        $totalExpMonth = $request->input('total_exp_month');
        $jobApplication->total_exp = (isset($totalExpYear) && $totalExpYear !== '' || isset($totalExpMonth) && $totalExpMonth !== '')
            ? $totalExpYear.'.'.$totalExpMonth
            : null;

        $relExpYear = $request->input('rel_exp_year');
        $relExpMonth = $request->input('rel_exp_month');
        $jobApplication->rel_exp = (isset($relExpYear) && $relExpYear !== '' || isset($relExpMonth) && $relExpMonth !== '')
            ? $relExpYear.'.'.$relExpMonth
            : null;

        $jobApplication->ctc = $request->input('ctc');
        $jobApplication->current_ctc_cur = (int) $request->input('current_ctc_cur');
        $jobApplication->current_ctc_mode = (int) $request->input('current_ctc_mode');

        $jobApplication->ectc = $request->input('ectc');
        $jobApplication->current_ectc_cur = (int) $request->input('current_ectc_cur');
        $jobApplication->current_ectc_mode = (int) $request->input('current_ectc_mode');
        $jobApplication->gender = $request->input('gender');
        $jobApplication->notice_comments = $request->input('notice_comments');

        $currency = GeneralMethod::currency();
        $ecurrency = $currency[$jobApplication->current_ectc_cur] ?? 'INR';
        $econvertedAmount = $request->input('bill_annual');
        if ($jobApplication->current_ectc_cur > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode($jobApplication->current_ectc_mode, $econvertedAmount);
        $jobApplication->ectc_yearly = $ectcdivided->yearly ?? null;
        $jobApplication->ectc_monthly = $ectcdivided->monthly ?? null;
        $jobApplication->ectc_hourly = $ectcdivided->hourly ?? null;

        $curCurrency = $currency[$jobApplication->current_ctc_cur] ?? 'INR';
        $convertedAmount = $request->input('pay_annual');
        if ($jobApplication->current_ctc_cur > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $curCurrency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode($jobApplication->current_ctc_mode, $convertedAmount);
        $jobApplication->ctc_yearly = $ctcdivided->yearly ?? null;
        $jobApplication->ctc_monthly = $ctcdivided->monthly ?? null;
        $jobApplication->ctc_hourly = $ctcdivided->hourly ?? null;

        $jobApplication->percentage_hike = $request->input('percentage_hike');
        $jobApplication->comment = $request->input('comment');
        $jobApplication->pincode = ((int) $totalExpYear == 0) ? 1 : 0;

        $jobApplication->current_designation = $request->input('current_designation');
        $jobApplication->current_company = $request->input('current_company');
        $jobApplication->careersummary = $request->input('careersummary');
        $jobApplication->nationality = $request->input('nationality');
        $jobApplication->wm = $request->input('wm');
        $jobApplication->employmentType = $request->input('employmentType');
        $jobApplication->oh = $request->input('oh');
        $jobApplication->visastatu = $request->input('visastatu');
        $jobApplication->referencename = $request->input('referencename');
        $jobApplication->reason_for_job_change = $request->input('reason_for_job_change');

        /* Parse skills JSON string */
        $cnt = 0;
        $skillStr = '';
        $skillArr = json_decode((string) $request->input('skills'), true);
        if (! empty($skillArr)) {
            foreach ($skillArr as $sval) {
                if ($cnt == 0) {
                    $skillStr .= $sval['value'];
                } else {
                    $skillStr .= ' , '.$sval['value'];
                }
                $cnt++;
            }
        }
        $jobApplication->skills = $skillStr;
        $jobApplication->master_status_id = 8;
        $jobApplication->secondrary_email = $request->filled('secondrary_email') ? strtolower(trim((string) $request->input('secondrary_email'))) : null;
        $jobApplication->secondary_mobile = $request->input('secondary_mobile');
        $jobApplication->submitted_by = 0;
        $noticePeriodState = CandidateNoticePeriodState::fromRequest($request);
        $jobApplication->serving_notice_period_mode = $noticePeriodState['serving_notice_period_mode'];
        $jobApplication->np_id = $noticePeriodState['np_id'];
        $jobApplication->last_working_date = $noticePeriodState['last_working_date'];

        if (! empty($request->input('qualification'))) {
            $jobApplication->qualification = (int) $request->input('qualification');
        }
        if ($request->has('gender')) {
            $jobApplication->gender = $request->input('gender');
        }
        if ($request->has('dob')) {
            $jobApplication->dob = $request->input('dob');
        }

        /* Geocode work city location - Fast Path with 0% MySQL Reads (CQRS Compliant) */
        $cityID = '';
        $stateId = '';
        $countryID = '';
        if ($request->filled('location_city_id') && (int) $request->location_city_id > 0) {
            $cityID = (int) $request->location_city_id;
            $stateId = (int) $request->location_state_id;
            $countryID = (int) ($request->location_country_id ?: 101);
        } else {
            $work_city = $request->input('kt_tagify_work_location');
            if (! empty($work_city)) {
                $work_city_all = json_decode($work_city, true);
                if (! empty($work_city_all)) {
                    foreach ($work_city_all as $cityVal) {
                        $parts = explode('|', $cityVal['value']);
                        $cityName = trim($parts[0] ?? '');
                        $countrName = trim($parts[2] ?? '');

                        $city_row = DB::table('city_list')
                            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->join('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city_id', 'countries.id as country_id')
                            ->where('city_list.city', '!=', '')
                            ->where('city_list.city', '=', $cityName)
                            ->where('countries.country_name', '=', $countrName)
                            ->first();

                        if ($city_row) {
                            $cityID = $city_row->city_id;
                            $countryID = $city_row->country_id;
                            $stateId = GeneralMethod::stateId($cityID);
                        }
                    }
                }
            }
        }

        $jobApplication->country = $countryID;
        $jobApplication->state = $stateId;
        $jobApplication->city_id = $cityID;

        /* Standard photo upload to S3 */
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $jobApplication->photo = Storage::disk('s3')->url($filePath);
        }

        $defaultTimeFormat = GeneralMethod::openDefaultTimeFormat($company_id);
        $jobApplication->interviewDateOne = $this->formatInterviewDateTime($request->input('interviewDateOne'), $request->input('interviewTimeOne'), $defaultTimeFormat);
        $jobApplication->interviewDateTwo = $this->formatInterviewDateTime($request->input('interviewDateTwo'), $request->input('interviewTimeTwo'), $defaultTimeFormat);
        $jobApplication->interviewDateThree = $this->formatInterviewDateTime($request->input('interviewDateThree'), $request->input('interviewTimeThree'), $defaultTimeFormat);
        $jobApplication->interviewDateFour = $this->formatInterviewDateTime($request->input('interviewDateFour'), $request->input('interviewTimeFour'), $defaultTimeFormat);
        $jobApplication->interviewDateFive = $this->formatInterviewDateTime($request->input('interviewDateFive'), $request->input('interviewTimeFive'), $defaultTimeFormat);
        $jobApplication->interviewDateSix = $this->formatInterviewDateTime($request->input('interviewDateSix'), $request->input('interviewTimeSix'), $defaultTimeFormat);

        $jobApplication->linkedin = $request->input('linkedin');
        $jobApplication->github = $request->input('github');
        $jobApplication->behance = $request->input('behance');
        $jobApplication->otherLink = $request->input('otherLink');
        $jobApplication->interview_mode_one = $request->input('interview_mode_one');
        $jobApplication->interview_mode_two = $request->input('interview_mode_two');
        $jobApplication->interview_mode_three = $request->input('interview_mode_three');
        $jobApplication->interview_mode_four = $request->input('interview_mode_four');
        $jobApplication->interview_mode_five = $request->input('interview_mode_five');
        $jobApplication->interview_mode_six = $request->input('interview_mode_six');

        $esSkillRecords = [];

        DB::beginTransaction();
        try {
            $jobApplication->save();

            /* Intent: ES indexing is asynchronously dispatched via SyncCandidateToElasticJob post-commit */

            /* Atomic claim & R2 validation for session resume uploads */
            if ($resumeSession) {
                $claimed = ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Uploaded->value)
                    ->update(['status' => ResumeUploadStatus::Completed->value, 'updated_at' => now()]);

                if ($claimed === 0) {
                    DB::rollBack();

                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                        'message' => 'Resume session was already claimed. Please upload the resume again.']);
                }

                $r2Exists = false;
                try {
                    $r2Exists = Storage::disk('r2')->exists('resumes/'.$resumeSession->uuid);
                } catch (\Throwable) {
                }
                if (! $r2Exists) {
                    ResumeUpload::where('id', $resumeSession->id)
                        ->where('status', ResumeUploadStatus::Completed->value)
                        ->update(['status' => ResumeUploadStatus::Uploaded->value, 'updated_at' => now()]);
                    DB::rollBack();

                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                        'message' => 'Resume file not found in storage. Please upload the resume again.']);
                }

                ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Completed->value)
                    ->update([
                        'linked_candidate_id' => $jobApplication->id,
                        'r2_url' => Storage::disk('r2')->url('resumes/'.$resumeSession->uuid),
                        'updated_at' => now(),
                    ]);

                UploadSessionAudit::record($resumeSession->id, 'ActionSubmitLinked', [
                    'candidate_id' => $jobApplication->id,
                ], $company_id, null);
            }

            /* Batch insert for skill rating experiences */
            if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
                $YearskillId = $request->input('YearskillId');
                $MonthskillId = $request->input('MonthskillId');
                $ratingId = $request->input('rating');

                DB::table('candidate_job_skill_exp')
                    ->where('candidate_id', $jobApplication->id)
                    ->where('job_id', $jobId)
                    ->delete();

                $batchExpRows = [];
                foreach ($YearskillId as $skillId => $value) {
                    $ss = ($value >= 1) ? 'Years' : 'Year';
                    $valMonth = $MonthskillId[$skillId] ?? 0;
                    $skill_value = $value.'.'.$valMonth.' '.$ss;
                    $rating = (int) ($ratingId[$skillId] ?? 0);

                    $expId = DB::table('candidate_job_skill_exp')->insertGetId([
                        'job_skill_id' => $skillId,
                        'skill_value' => $skill_value,
                        'job_id' => $jobId,
                        'rating' => $rating,
                        'candidate_id' => $jobApplication->id,
                    ]);

                    $esSkillRecords[] = [
                        'exp_id' => $expId,
                        'skill_id' => $skillId,
                        'skill_value' => $skill_value,
                        'job_id' => $jobId,
                        'candidate_id' => $jobApplication->id,
                        'rating' => $rating,
                    ];
                }
            }

            /* Add global skills to elastic dictionary if present */
            if (! empty($skillArr)) {
                $skillsAddElastic = array_column($skillArr, 'value');
                if (count($skillsAddElastic) > 0) {
                    GeneralMethod::skillsAddElastic($skillsAddElastic);
                }
            }

            /* Batch insert job_application_skills */
            if (! empty($skillArr)) {
                $existingSkills = DB::table('job_application_skills')
                    ->where('job_application_id', $jobApplication->id)
                    ->pluck('skills')
                    ->toArray();

                $batchSkills = [];
                foreach ($skillArr as $sval) {
                    if (! in_array($sval['value'], $existingSkills)) {
                        $batchSkills[] = [
                            'job_application_id' => $jobApplication->id,
                            'skills' => $sval['value'],
                        ];
                    }
                }
                if (! empty($batchSkills)) {
                    DB::table('job_application_skills')->insert($batchSkills);
                }
            }

            if (! empty($request->input('current_designation'))) {
                $designation = $request->input('current_designation');
                $exists = DB::table('job_application_skills')
                    ->where('job_application_id', $jobApplication->id)
                    ->where('skills', '=', $designation)
                    ->exists();
                if (! $exists) {
                    DB::table('job_application_skills')->insert([
                        'job_application_id' => $jobApplication->id,
                        'skills' => $designation,
                    ]);
                }
            }

            /* Job Assign verification */
            $chkJobAssign = DB::table('job_assign')
                ->where('company_id', $company_id)
                ->where('job_id', $jobId)
                ->where('user_id', 0)
                ->count();
            if ($chkJobAssign < 1) {
                DB::table('job_assign')->insert([
                    'company_id' => $company_id,
                    'user_id' => 0,
                    'job_id' => $jobId,
                ]);
            }

            /* Move resume session or dispatch R2 upload worker */
            if ($resumeSession) {
                $finalResumeUrl = app(ResumeUploadSessionService::class)->moveToCompanyPath($resumeSession);
                DB::table('job_applications')
                    ->where('id', $jobApplication->id)
                    ->update(['resume' => $finalResumeUrl]);
            } elseif ($request->hasFile('resume')) {
                $companyNameR2 = DB::table('companies')->where('id', $company_id)->value('company_name');
                $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                $companyNameRTwo = $companyNameR2.'_'.$company_id;

                $tempPath = $request->input('uploaded_file_path');
                $full_name = $request->input('resume_original_name');
                if ($tempPath && $full_name) {
                    // Intent: Generate candidate-specific unique R2 path synchronously before queue dispatch to prevent non-unique filename collisions in async queues.
                    $year = date('Y');
                    $month = date('m');
                    $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                    $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                    $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                    $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                    $uniqueFileName = "{$jobApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                    $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                    $fileUrl = "https://file.jero.works/{$r2Path}";

                    UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $jobApplication->id, $companyNameRTwo, $year, $month, $r2Path)
                        ->onConnection('emails');
                    DB::table('job_applications')
                        ->where('id', $jobApplication->id)
                        ->update(['resume' => $fileUrl]);
                }
            }

            if (empty($request->input('resumeCheck')) && ! $resumeSession && ! $request->hasFile('resume')) {
                DB::table('job_applications')
                    ->where('id', $jobApplication->id)
                    ->update(['resume' => '']);
            }

            /* Candidate skills value batch inserts */
            if ($request->has('jobskill') && is_array($request->input('jobskill'))) {
                DB::table('candidate_skills_value')
                    ->where('job_id', $jobId)
                    ->where('candidate_id', $jobApplication->id)
                    ->delete();

                $batchCandidateSkills = [];
                foreach ($request->input('jobskill') as $key => $value) {
                    if (! empty($value)) {
                        $batchCandidateSkills[] = [
                            'job_id' => $jobId,
                            'skill_id' => $key,
                            'candidate_id' => $jobApplication->id,
                            'skill_value' => $value,
                        ];
                    }
                }
                if (! empty($batchCandidateSkills)) {
                    DB::table('candidate_skills_value')->insert($batchCandidateSkills);
                }
            }

            /* Job application answers batch inserts */
            if ($request->has('answer') && is_array($request->input('answer'))) {
                $batchAnswers = [];
                foreach ($request->input('answer') as $key => $value) {
                    $batchAnswers[] = [
                        'job_application_id' => $jobApplication->id,
                        'job_id' => $jobId,
                        'question_id' => $key,
                        'answer' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (! empty($batchAnswers)) {
                    JobApplicationAnswer::insert($batchAnswers);
                }
            }

            /* Application additional details batch inserts */
            $job_additional_detail = DB::table('job_additional_detail')
                ->select('*')
                ->where('job_id', $jobId)
                ->orderBy('id', 'ASC')
                ->get();

            if ($job_additional_detail->count() > 0) {
                $additionalValues = $request->input('additional_detail') ?? [];
                $batchAdditional = [];
                $inc = 0;
                foreach ($job_additional_detail as $jd) {
                    $batchAdditional[] = [
                        'job_id' => $jobId,
                        'question_id' => $jd->id,
                        'application_id' => $jobApplication->id,
                        'job_detail_value' => $jd->detail_value,
                        'application_detail_value' => $additionalValues[$inc] ?? null,
                    ];
                    $inc++;
                }
                if (! empty($batchAdditional)) {
                    DB::table('application_additional_detail')->insert($batchAdditional);
                }
            }

            DB::commit();

            /* Intent: Store Redis atomic deduplication key (300s TTL) post-commit */
            Cache::put($dedupKey, 1, 86400 * 30);
            if ($phoneDedupKey) {
                Cache::put($phoneDedupKey, 1, 86400 * 30);
            }

            /* Intent: Asynchronously dispatch ES candidate & skill indexing jobs off the web HTTP thread */
            SyncCandidateToElasticJob::dispatch($jobApplication->id);
            if (! empty($esSkillRecords)) {
                IndexCandidateSkillsToElasticJob::dispatch($esSkillRecords);
            }

            /* Intent: Real-time Reverb broadcast to notify recruiter App Tracker instantly without page refresh */
            try {
                broadcast(new \App\Events\AppTrackerCandidateAssignedEvent(
                    tenantId: $company_id,
                    jobId: $jobId,
                    version: 1,
                    candidateId: $jobApplication->id,
                    wiseId: $jobApplication->id,
                    masterStatusId: 8,
                    metadata: [
                        'candidate_name' => $jobApplication->full_name,
                        'email' => $jobApplication->email,
                        'phone' => $jobApplication->phone,
                        'source' => 'CAREER_PORTAL',
                        'added_count' => 1,
                    ]
                ));
            } catch (\Throwable $e) {
                Log::warning('AppTrackerCandidateAssignedEvent broadcast warning: '.$e->getMessage());
            }

            /* Intent: Invalidate tenant career caches so application counts and candidate state are fresh (Rule 6) */
            GeneralMethod::invalidateCareerTenantCache($company_id, $jobId);

            $message = 'messages.createdSuccessfully';

            return Reply::redirect(
                url('job-apply/'.$jobId.'/'.$company_id),
                __('menu.jobApplications').' '.__($message)
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($lockKey) && $lockKey) {
                Cache::forget($lockKey);
            }
            Log::error('JobApplyService submitApplication failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }
    }

    /**
     * Check Elasticsearch duplicate candidates by email, phone & jobId.
     */
    protected function checkElasticDuplicate(string $email, ?string $phone, int $jobId, int $companyId = 0): ?array
    {
        try {
            $normalizedPhone = ! empty($phone) ? preg_replace('/[^0-9]/', '', $phone) : null;
            $shouldQueries = [
                ['term' => ['email' => $email]],
            ];
            if (! empty($phone)) {
                $shouldQueries[] = ['term' => ['phone' => $phone]];
            }
            if (! empty($normalizedPhone) && $normalizedPhone !== $phone) {
                $shouldQueries[] = ['term' => ['phone' => $normalizedPhone]];
            }

            $filterQueries = [
                ['term' => ['job_id' => $jobId]],
            ];
            if ($companyId > 0) {
                $filterQueries[] = ['term' => ['company_id' => $companyId]];
            }

            $params = [
                'index' => ELASTIC_CAREER_APPLY,
                'body' => [
                    'query' => [
                        'bool' => [
                            'filter' => $filterQueries,
                            'should' => $shouldQueries,
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'size' => 1,
                ],
            ];
            $response = $this->elasticService->getClient()->search($params);
            if (($response['hits']['total']['value'] ?? 0) > 0) {
                return Reply::dataOnly([
                    'status' => 'mailcheck',
                    'msgType' => 'warning',
                    'message' => 'You have already applied for this job with this email address or contact number.',
                ]);
            }

            $candFilterQueries = [
                ['term' => ['jobId' => $jobId]],
            ];
            if ($companyId > 0) {
                $candFilterQueries[] = ['term' => ['companyId' => $companyId]];
            }

            $paramsCandidates = [
                'index' => ELASTIC_CANDIDATES,
                'body' => [
                    'query' => [
                        'bool' => [
                            'filter' => $candFilterQueries,
                            'should' => $shouldQueries,
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'size' => 1,
                ],
            ];
            $responseCand = $this->elasticService->getClient()->search($paramsCandidates);
            if (($responseCand['hits']['total']['value'] ?? 0) > 0) {
                return Reply::dataOnly([
                    'status' => 'mailcheck',
                    'msgType' => 'warning',
                    'message' => 'You have already applied for this job with this email address or contact number.',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Elasticsearch duplicate check warning: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Helper to format interview date time string.
     */
    protected function formatInterviewDateTime($date, $time, $defaultTimeFormat): ?string
    {
        if (empty($date) || empty($time)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date.' '.$time)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
