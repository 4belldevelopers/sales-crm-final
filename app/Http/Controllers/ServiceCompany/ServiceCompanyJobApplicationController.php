<?php

/***########################################################

Controller    	: 	AdminJobApplicationController
Purmose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021

########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Enums\ResumeUploadStatus;
use App\Exports\JobApplicationCustomExport;
use App\Exports\JobApplicationSampleExport;
use App\Helper\Files;
use App\Helper\GeneralMethod;
use App\Helper\Google;
use App\Helper\Reply;
use App\Helper\Zoom;
use App\Imports\JobApplicationImport;
use App\Jobs\ConvertResumeToPdfJob;
use App\Jobs\ProcessCandidateSubmissionJob;
use App\Jobs\ProcessCandidateUpdateJob;
use App\Jobs\ProcessOutboxEventJob;
use App\Jobs\QueueCandidateNotificationJob;
use App\Jobs\QueueMailService;
use App\Jobs\QueueNotificationMailService;
use App\Jobs\UploadResumeToTestRTwo;
use App\Mail\AddCandidateMail;
use App\Mail\ChangeStatusMail;
use App\Mail\InterviewTrackerMail;
use App\Mail\JoinedUsTodayMail;
use App\Mail\OfferMail;
use App\Mail\RejectedMail;
use App\Models\AddJobWiseCondidate;
use App\Models\ApplicationStatus;
use App\Models\Company;
use App\Models\Country;
use App\Models\InterviewSchedule;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Models\OfferJobApplication;
use App\Models\ResumeUpload;
use App\Models\Skill;
use App\Models\SysIdempotencyKey;
use App\Models\UploadSessionAudit;
use App\Models\User;
use App\Notifications\CandidateScheduleInterview;
use App\Services\CandidateNoticePeriodState;
use App\Services\ElasticService;
use App\Services\GoogleSheetServiceCandidate;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use App\Services\R2PresignService;
use App\Services\ReferenceDataCache;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Google_Client;
use Google_Client as GoogleClient;
use Google_Service_Calendar;
use Google_Service_Calendar as Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Process\Process;
use Validator;

class ServiceCompanyJobApplicationController extends ServiceCompanyBaseController
{
    protected $mailService;

    protected $massMailService;

    protected $notificationMailService;

    protected $elasticService;

    public function __construct(MailService $mailService, GoogleSheetServiceCandidate $GoogleSheetServiceCandidate, MassMailService $massMailService, NotificationMailService $notificationMailService, ElasticService $elasticService)
    {

        parent::__construct();
        $this->pageTitle = 'menu.jobApplications';
        $this->pageIcon = 'icon-user';
        $this->mailService = $mailService;
        $this->GoogleSheetServiceCandidate = $GoogleSheetServiceCandidate;
        $this->massMailService = $massMailService;
        $this->notificationMailService = $notificationMailService;
        $this->elasticService = $elasticService;

    }

    /**
     * Normalizes the Current Salary frequency used as the shared salary mode.
     */
    private function normalizedCandidateSalaryMode(mixed $mode): int
    {
        $normalizedMode = (int) $mode;

        return in_array($normalizedMode, [0, 1, 2], true) ? $normalizedMode : 0;
    }

    /**
     * Calculates the persisted increase from normalized annual salary values.
     */
    private function calculatedCandidateSalaryIncrease(mixed $currentAnnual, mixed $expectedAnnual): ?float
    {
        $current = (float) $currentAnnual;
        $expected = (float) $expectedAnnual;

        if ($current <= 0 || $expected <= 0) {
            return null;
        }

        return round((($expected - $current) / $current) * 100, 2);
    }

    /**
     * Normalizes the single Current Location value across Tagify, form, and API payload shapes.
     */
    private function normalizedCandidateLocation(mixed $locationInput): ?array
    {
        if (is_string($locationInput)) {
            $locationInput = trim($locationInput);
            if ($locationInput === '') {
                return null;
            }

            $decoded = json_decode($locationInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $locationInput = $decoded;
            }
        }

        if (is_object($locationInput)) {
            $locationInput = (array) $locationInput;
        }

        $locationValue = null;
        if (is_string($locationInput)) {
            $locationValue = $locationInput;
        } elseif (is_array($locationInput) && isset($locationInput['value']) && is_scalar($locationInput['value'])) {
            $locationValue = (string) $locationInput['value'];
        } elseif (is_array($locationInput)) {
            foreach ($locationInput as $candidateLocation) {
                if (is_object($candidateLocation)) {
                    $candidateLocation = (array) $candidateLocation;
                }

                if (is_array($candidateLocation) && isset($candidateLocation['value']) && is_scalar($candidateLocation['value'])) {
                    $locationValue = (string) $candidateLocation['value'];
                    break;
                }

                if (is_string($candidateLocation)) {
                    $locationValue = $candidateLocation;
                    break;
                }
            }
        }

        if ($locationValue === null || trim($locationValue) === '') {
            return null;
        }

        $parts = array_map('trim', explode('|', $locationValue));
        if (count($parts) < 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
            return null;
        }

        return [
            'city' => $parts[0],
            'state' => $parts[1],
            'country' => $parts[2],
        ];
    }

    /**
     * Resilient save helper that catches 'Unknown column' errors if V4 migrations
     * are pending on the database, gracefully falling back to standard columns without failing candidate persistence.
     */
    private function resilientSaveJobApplication(JobApplication $jobApplication): void
    {
        try {
            $jobApplication->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Unknown column') || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1054)) {
                unset(
                    $jobApplication->client_submission_uuid,
                    $jobApplication->resume_version,
                    $jobApplication->current_resume_id
                );
                $jobApplication->save();
            } else {
                throw $e;
            }
        }
    }

    // **  start ajax  */
    public function uploadresume(Request $request)
    {

        // Raw-body upload path: browser sends xhr.send(file) with Content-Type header.
        // PHP reads php://input as a stream — no multipart temp file is ever written to disk.
        $originalName = urldecode($request->header('X-File-Name', 'resume.pdf'));
        $fileSize = (int) $request->header('X-File-Size', 0);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (! in_array($ext, ['pdf', 'doc', 'docx'])) {
            return response()->json(['success' => false, 'message' => 'Only PDF, DOC, DOCX files are allowed.'], 422);
        }

        if ($fileSize > 6 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'Maximum file size is 6 MB.'], 422);
        }

        $objectKey = 'temp/'.\Illuminate\Support\Str::uuid().'.'.$ext;

        // Stream php://input directly to R2 — no local file written
        $stream = fopen('php://input', 'r');
        Storage::disk('r2')->put($objectKey, $stream);
        fclose($stream);

        $full_name = uniqid('JERO_');
        $uniqueName = GeneralMethod::getUniqueCvName($full_name, $ext);

        // Intent: Do NOT store temp path in global PHP session — prevents cross-tab / cross-job session leakage.
        // The file_path and original_name are returned in JSON response to the client for explicit form submission.

        // Create metadata record and dispatch ParseResumeJob
        // Intent: Honor client-first execution (Rule 3 & 4). If client performs parsing in browser RAM,
        // no backend ParseResumeJob is queued, preventing server/Horizon queue strain.
        $resumeUploadId = null;
        $skipBackendParse = (bool) ($request->header('X-Skip-Backend-Parse') || $request->boolean('skip_backend_parse'));
        if (! $skipBackendParse) {
            try {
                $user = auth()->user();
                $record = \App\Models\CandidateResumeUpload::create([
                    'candidate_id' => $request->header('X-Candidate-Id') ?: null,
                    'company_id' => $user?->company_id,
                    'object_key' => $objectKey,
                    'original_filename' => $originalName,
                    'mime_type' => $request->header('Content-Type', 'application/octet-stream'),
                    'file_size' => $fileSize,
                    'uploaded_by' => $user?->id,
                    'status' => 'Queued',
                ]);
                \App\Jobs\ParseResumeJob::dispatch($record->id)->onQueue('default');
                $resumeUploadId = $record->id;
            } catch (\Throwable $e) {
                \Log::warning('uploadresume: could not queue parse job', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_path' => $objectKey,
            'original_name' => $originalName,
            'resume_upload_id' => $resumeUploadId,
        ]);
    }

    public function unsetFile(Request $request)
    {
        $filePath = $request->input('file_path');

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            // Delete the file
            Storage::disk('public')->delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found or already deleted.',
        ], 404);
    }

    public function skillExperienceUpdate(Request $request)
    {

        // dd($request->all());
        $appId = (int) $request->appId;
        $job_id = (int) $request->job_id;
        $companyId = (int) auth()->user()->company_id;

        // TENANCY NOTE: Verify candidate application belongs to current tenant, thirdparty company, or active pipeline
        $app = DB::table('job_applications')->where('id', $appId)->first();
        if (! $app) {
            return response()->json(['status' => false, 'message' => 'Unauthorized or application not found.'], 403);
        }

        if ($job_id <= 0) {
            $job_id = (int) (DB::table('add_job_wise_condidates')->where('job_application_id', $appId)->value('job_id') ?? ($app->job_id ?? 0));
        }

        $isAuthorized = auth()->user()->hasRole('admin')
            || (int) $app->company_id === $companyId
            || (int) ($app->thirdparty_company_id ?? 0) === $companyId
            || DB::table('add_job_wise_condidates')
                ->where('job_application_id', $appId)
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)->orWhere('thirdparty_company_id', $companyId);
                })->exists()
            || ($job_id > 0 && DB::table('jobs')->where('id', $job_id)->where('company_id', $companyId)->exists())
            || DB::table('rec_candidate_assign')
                ->where('source_application_id', $appId)
                ->where(function ($q) use ($companyId) {
                    $q->where('source_company_id', $companyId)->orWhere('source_from_company_id', $companyId);
                })->exists();

        if (! $isAuthorized) {
            return response()->json(['status' => false, 'message' => 'Unauthorized or application not found.'], 403);
        }

        // Initialize output variables; populated inside YearskillId block if present
        $insertedSkills = [];
        $html = '';

        if ($request->has('YearskillId') && is_array($request->input('YearskillId'))) {
            $YearskillId = $request->input('YearskillId');
            $MonthskillId = $request->input('MonthskillId');
            $ratingId = $request->input('rating');

            // 1. Transactional MySQL Write (SSOT)
            $insertedSkills = [];
            DB::beginTransaction();
            try {
                DB::table('candidate_job_skill_exp')->where('candidate_id', $appId)->where('job_id', $job_id)->delete();

                foreach ($YearskillId as $skillId => $value) {
                    $skillIdInt = (int) $skillId;
                    if ($skillIdInt <= 0) {
                        continue;
                    }
                    $ss = 'Year';
                    if ($value >= 1) {
                        $ss = 'Years';
                    }
                    $valMonth = ! empty($MonthskillId[$skillId]) ? $MonthskillId[$skillId] : 0;
                    $skill_value = $value.'.'.$valMonth.' '.$ss;
                    $rating = (int) ($ratingId[$skillId] ?? 0);

                    $values = [
                        'job_skill_id' => $skillIdInt,
                        'skill_value' => $skill_value,
                        'job_id' => $job_id,
                        'rating' => $rating,
                        'candidate_id' => $appId,
                    ];
                    DB::table('candidate_job_skill_exp')->insert($values);
                    $expId = DB::getPdo()->lastInsertId();

                    $skills = DB::table('job_metronics_skills')->select('skill_name')->where('id', '=', $skillIdInt)->first();
                    $insertedSkills[] = [
                        'expId' => $expId,
                        'skill_name' => $skills->skill_name ?? '',
                        'skillId' => $skillIdInt,
                        'skill_value' => $skill_value,
                        'rating' => $rating,
                    ];
                }

                DB::commit();
            } catch (\Throwable $ex) {
                DB::rollBack();
                \Log::error('skillExperienceUpdate MySQL transaction failed: '.$ex->getMessage());

                return response()->json(['status' => false, 'message' => 'Database error while saving skill experience.'], 500);
            }

            // 2. Synchronize Elasticsearch read store in protected try-catch
            try {
                if ($this->elasticService->indexExists(ELASTIC_CANDIDATE_SKILLS_EXP)) {
                    $this->elasticService->getClient()->deleteByQuery([
                        'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                        'conflicts' => 'proceed',
                        'body' => [
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['candidate_id' => (int) $appId]],
                                        ['term' => ['job_id' => (int) $job_id]],
                                    ],
                                ],
                            ],
                        ],
                    ]);

                    foreach ($insertedSkills as $is) {
                        $this->elasticService->getClient()->index([
                            'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                            'id' => $is['expId'],
                            'body' => [
                                'skill_name' => $is['skill_name'],
                                'job_skill_id' => $is['skillId'],
                                'skill_value' => $is['skill_value'],
                                'job_id' => $job_id,
                                'rating' => $is['rating'],
                                'candidate_id' => $appId,
                                'created_at' => now()->toIso8601String(),
                                'updated_at' => now()->toIso8601String(),
                            ],
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('skillExperienceUpdate ES sync failed: '.$e->getMessage());
            }

            $html = '';
            $jobSkill = DB::table('job_metronics_skills')
                ->leftJoin('candidate_job_skill_exp', function ($join) use ($appId) {
                    $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                        ->where('candidate_job_skill_exp.candidate_id', '=', $appId);
                })
                ->select(
                    DB::raw('MIN(job_metronics_skills.id) as job_skill_id'), // alias as job_skill_id for blade/form consistency
                    DB::raw('MIN(job_metronics_skills.id) as id'),
                    'job_metronics_skills.skill_name',
                    DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
                    DB::raw('MAX(candidate_job_skill_exp.rating) as rating')
                )
                ->where('job_metronics_skills.job_id', '=', $job_id)
                ->whereNotNull('job_metronics_skills.skill_name')
                ->whereRaw("TRIM(job_metronics_skills.skill_name) != ''")
                ->groupBy('job_metronics_skills.skill_name')
                ->get();

            $skillShow = true;
            $html = '<div class="skills-thead">
                        <div>Skill</div>
                        <div>Experience</div>
                        <div>Self Rating</div>
                    </div>';
            if (! empty($jobSkill)) {
                $html .= '<div class="row">';
                foreach ($jobSkill as $skill_val) {
                    if (empty($skill_val->skill_name) || trim((string) $skill_val->skill_name) === '') {
                        continue;
                    }
                    $skillShowView = '';
                    if ($skillShow) {
                        $skillShowView = $skill_val->skill_value ?? '';
                    }

                    $html .= '<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.ucwords(strtolower(trim((string) $skill_val->skill_name))).'
                          </div>
                          <div class="skill-exp-cell">'.$skillShowView.'</div>
                          <div class="skill-rating-cell">
                            <div class="stars">
                              <svg class="star-skill '.(@$skill_val->rating >= 1 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >= 2 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >= 3 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >= 4 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                              </svg>
                              <svg class="star-skill '.(@$skill_val->rating >= 5 ? '' : 'star-empty').'" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                              </svg>
                            </div>
                            <span class="rating-text">'.(@$skill_val->rating ?? 0).' / 5</span>
                          </div>
                        </div>';
                }
                $html .= '</div>';
            }

        }

        // Recalculate skill match score for this job and candidate
        $candSkills = DB::table('job_applications')->where('id', $appId)->value('skills') ?? '';
        $candSkillArr = array_filter(array_map('trim', explode(',', $candSkills)));
        if (! empty($insertedSkills)) {
            foreach ($insertedSkills as $is) {
                if (! empty($is['skill_name']) && ! in_array($is['skill_name'], $candSkillArr)) {
                    $candSkillArr[] = $is['skill_name'];
                }
            }
            $candSkills = implode(',', array_unique($candSkillArr));
            DB::table('job_applications')->where('id', $appId)->update([
                'skills' => $candSkills,
                'updated_at' => now(),
            ]);
        }

        $matchPct = 0;
        try {
            $calc = GeneralMethod::calculateSkillMatch((int) $job_id, $candSkills);
            $matchPct = (int) ($calc['percentage'] ?? 0);
        } catch (\Throwable $e) {
            \Log::warning('skillExperienceUpdate calculateSkillMatch failed: '.$e->getMessage());
        }

        if ($job_id > 0) {
            DB::table('add_job_wise_condidates')
                ->where('job_application_id', $appId)
                ->where('job_id', $job_id)
                ->update([
                    'skill_match' => $matchPct,
                    'updated_at' => now(),
                ]);
        }

        $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', $appId)->pluck('id')->toArray();
        try {
            foreach ($wiseIds as $wId) {
                GeneralMethod::JobApplicationsAddEditUpload($wId);
            }
        } catch (\Throwable $e) {
            \Log::warning('skillExperienceUpdate JobApplicationsAddEditUpload failed: '.$e->getMessage());
        }

        try {
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $this->elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES]);
            }
        } catch (\Throwable $e) {
            \Log::warning('skillExperienceUpdate ELASTIC_CANDIDATES refresh failed: '.$e->getMessage());
        }

        // Bust candidate drawer cache across all slugs and associated job-wise IDs
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $targetIds = array_unique(array_merge([$appId], $wiseIds));
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];

        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
            }
        }

        $cachekeys = [
            'candidateDetails_version_user_'.$this->user->id,
            'allcvtracker_version_user_'.$this->user->id,
            'allapptracker_version_user_'.$this->user->id,
            'allclientapp_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        // Intent: Phase 3 Reverb broadcast to invalidate client RAM cache in all connected recruiter tabs
        try {
            \App\Events\CandidateDrawerUpdated::dispatch(
                (int) $companyId,
                (int) $appId,
                time(),
                'skills_updated',
                array_map('intval', $wiseIds),
                $matchPct,
                $candSkills,
                null,
                null,
                null,
                (int) $job_id
            );
        } catch (\Throwable $e) {
            \Log::warning('CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        return response()->json([
            'status' => true,
            'html' => $html,
            'data' => $insertedSkills,
            'skill_match' => $matchPct,
            'candidate_id' => (int) $appId,
            'wise_ids' => array_map('intval', $wiseIds),
            'job_id' => (int) $job_id,
        ]);

    }

    public function JobQuestionUpdate(Request $request)
    {

        // dd($request->all());
        $appId = $request->appId;
        $job_id = $request->job_id;
        $html = '';
        // TENANCY NOTE: Verify application belongs to current tenant before deleting application details
        $appCheck = DB::table('job_applications')->where('id', $appId);
        if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $appCheck->where('company_id', auth()->user()->company_id);
        }
        if (! $appCheck->exists()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized or application not found.'], 403);
        }
        DB::table('application_additional_detail')->where('application_id', $appId)->where('job_id', $job_id)->delete();
        $this->elasticService->getClient()->deleteByQuery([
            'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
            'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['application_id' => $appId]],
                            ['term' => ['job_id' => $job_id]],
                        ],
                    ],
                ],
            ],
        ]);

        $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        if ($job_additional_detail_count > 0) {
            $inc = 0;
            foreach ($job_additional_detail as $jd) {

                $values = [
                    'job_id' => @$job_id,
                    'question_id' => @$jd->id,
                    'application_id' => @$appId,
                    'job_detail_value' => @$jd->detail_value,
                    'application_detail_value' => @$request->additional_detail[$inc],
                ];

                DB::table('application_additional_detail')->insert($values);
                $expId = DB::getPdo()->lastInsertId();
                $this->elasticService->getClient()->update([
                    'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                    'id' => $expId,   // store as ES _id
                    'body' => [
                        'doc' => [
                            'job_id' => @$job_id,
                            'question_id' => @$jd->id,
                            'application_id' => @$appId,
                            'job_detail_value' => @$jd->detail_value,
                            'application_detail_value' => @$request->additional_detail[$inc],
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                        'doc_as_upsert' => true, // key to avoiding conflicts
                    ],
                ]);
                $inc++;

            }

            $job_additional_detail = DB::table('job_additional_detail')
                ->select('*')
                // ->where('job_additional_detail.company_id', '=', $company_id)
                ->where('job_additional_detail.job_id', '=', @$job_id)
                ->orderBy('job_additional_detail.id', 'ASC')
                ->get();

            $application_additional_detail = DB::table('application_additional_detail')
                ->select('*')
                ->where('application_additional_detail.application_id', '=', @$appId)
                ->where('application_additional_detail.job_id', '=', @$job_id)
                ->orderBy('application_additional_detail.id', 'ASC')
                ->get();

            $customTitleId = 1;
            $html = '';
            // $html			=	'<h3 class="py-5">Custom Details</h3>';
            $inc_first = 0;
            $inc_last = 1;
            $html .= '<div class="skills-thead">
											<div>Job Questions</div>
											<div>Answered</div>
										</div>';
            foreach ($job_additional_detail as $aDetailArr) {

                $ans_value = $job_detail_value = '';
                if (! empty($application_additional_detail)) {
                    foreach ($application_additional_detail as $aval) {
                        if ($aDetailArr->id == $aval->question_id) {
                            $job_detail_value = @$aval->job_detail_value;
                            $ans_value = @$aval->application_detail_value;
                        }
                    }
                }

                $html .= '<div class="skills-row">
                          <div class="skill-name-cell">
                            <div class="skill-logo" style="color: #f59e0b;">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                              </svg>
                            </div>
                            '.strtoupper(@$job_detail_value).'
                          </div>
                          <div class="skill-exp-cell">'.@$ans_value.'</div>
                        </div>';

                $inc_first++;
                $inc_last++;
                $customTitleId++;
            }

        }

        // Bust candidate drawer cache across all slugs and associated job-wise IDs
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', (int) $appId)->pluck('id')->toArray();
        $targetIds = array_unique(array_merge([(int) $appId], $wiseIds));
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];
        $companyId = (int) auth()->user()->company_id;

        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
            }
        }

        $cachekeys = [
            'candidateDetails_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        // Intent: Phase 3 Reverb broadcast to invalidate client RAM cache in all connected recruiter tabs
        try {
            \App\Events\CandidateDrawerUpdated::dispatch((int) $companyId, (int) $appId, time(), 'questions_updated');
        } catch (\Throwable $e) {
            \Log::warning('CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        return response()->json(['status' => true, 'html' => $html]);

    }

    public function SlotsUpdate(Request $request)
    {

        $addJobWiseCondidate_id = $request->appId;

        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $addJobWiseCondidate_id,
        ]);
        if ($exists) {

            // 	$params = [
            // 	'index' => ELASTIC_CANDIDATES,
            // 	'id'    => $addJobWiseCondidate_id,
            // ];

            // $response = $this->elasticService->getClient()->get($params);
            // $data = (object) $response['_source'];
            // dd($data);

            $updateTime = now()->toIso8601String();

            $defaultTimeFormat = GeneralMethod::defaultTimeFormat();

            $interviewDateOne = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
            $interviewDateTwo = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
            $interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);

            // interview_mode_one, interview_mode_two, interview_mode_three, interview_mode_four, interview_mode_five

            $updateParams = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
                'body' => [
                    'doc' => [
                        'interviewDateOne' => ($interviewDateOne) ? Carbon::parse($interviewDateOne)->toIso8601String() : null,
                        'interview_mode_one' => $request->interview_mode_one,
                        'interviewDateTwo' => ($interviewDateTwo) ? Carbon::parse($interviewDateTwo)->toIso8601String() : null,
                        'interview_mode_two' => $request->interview_mode_two,
                        'interviewDateThree' => ($interviewDateThree) ? Carbon::parse($interviewDateThree)->toIso8601String() : null,
                        'interview_mode_three' => $request->interview_mode_three,
                        'interview_timezone' => $request->interview_timezone ?? 'IST',
                    ],
                    'doc_as_upsert' => false, // Changed to false since we verified existence
                ],
                'refresh' => false, // Eventual consistency for 10K concurrent scale
            ];

            $dsrs = $this->elasticService->getClient()->update($updateParams);

            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];

            $interview_mode_one = @$data->interview_mode_one;
            $interviewDateOne = @$data->interviewDateOne;
            $interviewDateTwo = @$data->interviewDateTwo;
            $interview_mode_two = @$data->interview_mode_two;
            $interviewDateThree = @$data->interviewDateThree;
            $interview_mode_three = @$data->interview_mode_three;
            $interview_timezone = @$data->interview_timezone ?? 'IST';
            $wiseId = $addJobWiseCondidate_id;

            $scheduleDateArr = tenant_table('interview_schedules')->where('interview_job_wise_con_id', $addJobWiseCondidate_id)->pluck('schedule_date')->toArray();
            $book1 = 'Book Now';
            $book2 = 'Book Now';
            $book3 = 'Book Now';
            $bookbtn1 = 'book-btn';
            $bookbtn2 = 'book-btn';
            $bookbtn3 = 'book-btn';
            $modal1 = '';
            $modal2 = '';
            $modal3 = '';
            $date1 = Carbon::parse($interviewDateOne)->format('Y-m-d H:i:s');
            $date2 = Carbon::parse($interviewDateTwo)->format('Y-m-d H:i:s');
            $date3 = Carbon::parse($interviewDateThree)->format('Y-m-d H:i:s');
            if (count($scheduleDateArr) > 0) {

                if (in_array($date1, $scheduleDateArr)) {
                    $book1 = 'Booked';
                    $bookbtn1 = 'book-btnoff-booked';
                    $modal1 = '';
                }
                if (in_array($date2, $scheduleDateArr)) {
                    $book2 = 'Booked';
                    $bookbtn2 = 'book-btnoff-booked';
                    $modal2 = '';
                }
                if (in_array($date3, $scheduleDateArr)) {
                    $book3 = 'Booked';
                    $bookbtn3 = 'book-btnoff-booked';
                    $modal3 = '';
                }

            }

            $html = '';

            $html = '<div class="slot-day">
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('d M') : 'N/A').'</div>
							</div>
							<div class="slot-time">'.($interviewDateOne ? Carbon::parse($interviewDateOne)->format('h:i A') : 'N/A').' <span style="font-size:10px;color:#94a3b8;font-weight:500;">('.$interview_timezone.')</span></div>
							<div class="slot-time">'.(@$interview_mode_one ?? 'N/A').'</div>

							'.($interviewDateOne
                           ? '<button '.(($interviewDateOne && $date1 >= date('Y-m-d')) ? $modal1 : '').' data-mode="'.$interview_mode_one.'" data-date="'.Carbon::parse($interviewDateOne)->format('d-m-Y').'" data-time="'.Carbon::parse($interviewDateOne)->format('h:i A').'" class="'.(($interviewDateOne && $date1 >= date('Y-m-d')) ? $bookbtn1 : 'book-btnoff').'" rel="'.$wiseId.'">'.$book1.'</button>'
                           : '<button type="button" class="book-btn-addslot editSlots"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x=\'3\' y=\'4\' width=\'18\' height=\'18\' rx=\'2\'/><line x1=\'16\' y1=\'2\' x2=\'16\' y2=\'6\'/><line x1=\'8\' y1=\'2\' x2=\'8\' y2=\'6\'/><line x1=\'3\' y1=\'10\' x2=\'21\' y2=\'10\'/></svg> Select Date</button>'
            ).'
                        </div>

						<div class="slot-day">
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('d M') : 'N/A').'</div>
							</div>
							<div class="slot-time">'.($interviewDateTwo ? Carbon::parse($interviewDateTwo)->format('h:i A') : 'N/A').' <span style="font-size:10px;color:#94a3b8;font-weight:500;">('.$interview_timezone.')</span></div>
							<div class="slot-time">'.(@$interview_mode_two ?? 'N/A').'</div>
								'.($interviewDateTwo
                        ? '<button '.(($interviewDateTwo && $date2 >= date('Y-m-d')) ? $modal2 : '').' data-mode="'.$interview_mode_two.'" data-date="'.Carbon::parse($interviewDateTwo)->format('d-m-Y').'" data-time="'.Carbon::parse($interviewDateTwo)->format('h:i A').'" class="'.(($interviewDateTwo && $date2 >= date('Y-m-d')) ? $bookbtn2 : 'book-btnoff').'" rel="'.$wiseId.'">'.$book2.'</button>'
                        : '<button type="button" class="book-btn-addslot editSlots"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x=\'3\' y=\'4\' width=\'18\' height=\'18\' rx=\'2\'/><line x1=\'16\' y1=\'2\' x2=\'16\' y2=\'6\'/><line x1=\'8\' y1=\'2\' x2=\'8\' y2=\'6\'/><line x1=\'3\' y1=\'10\' x2=\'21\' y2=\'10\'/></svg> Select Date</button>'
            ).'
						</div>

						<div class="slot-day">						
							<div class="slot-header">
								<div class="slot-day-name">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('D') : 'N/A').'</div>
								<div class="slot-date">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('d M') : 'N/A').'</div>
							</div>
							<div class="slot-time">'.($interviewDateThree ? Carbon::parse($interviewDateThree)->format('h:i A') : 'N/A').' <span style="font-size:10px;color:#94a3b8;font-weight:500;">('.$interview_timezone.')</span></div>
							<div class="slot-time">'.(@$interview_mode_three ?? 'N/A').'</div>
								'.($interviewDateThree
                ? '<button '.(($interviewDateThree && $date3 >= date('Y-m-d')) ? $modal3 : '').' data-mode="'.$interview_mode_three.'" data-date="'.Carbon::parse($interviewDateThree)->format('d-m-Y').'" data-time="'.Carbon::parse($interviewDateThree)->format('h:i A').'" class="'.(($interviewDateThree && $date3 >= date('Y-m-d')) ? $bookbtn3 : 'book-btnoff').'" rel="'.$wiseId.'">'.$book3.'</button>'
                : '<button type="button" class="book-btn-addslot editSlots"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px;flex-shrink:0;"><rect x=\'3\' y=\'4\' width=\'18\' height=\'18\' rx=\'2\'/><line x1=\'16\' y1=\'2\' x2=\'16\' y2=\'6\'/><line x1=\'8\' y1=\'2\' x2=\'8\' y2=\'6\'/><line x1=\'3\' y1=\'10\' x2=\'21\' y2=\'10\'/></svg> Select Date</button>'
            ).'
						</div>';

            $cachekeys = [
                'candidateDetails_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return response()->json(['status' => true, 'html' => $html]);

        }

    }

    /** Updates recruiter_note (Recruiter Comments) in both MySQL (write store) and Elasticsearch (read store). */
    public function recruiterNoteUpdate(Request $request)
    {
        $appId = (int) $request->appId;
        $recruiterNote = $request->recruiter_note ?? '';
        $companyId = (int) auth()->user()->company_id;

        // Verify candidate exists
        $app = DB::table('job_applications')->where('id', $appId)->first();
        if (! $app) {
            return response()->json(['status' => false, 'message' => 'Candidate application not found.'], 404);
        }

        // Authorize: candidate belongs to tenant, or tenant is thirdparty, or has active pipeline record, or admin
        $isAuthorized = auth()->user()->hasRole('admin')
            || (int) $app->company_id === $companyId
            || (int) ($app->thirdparty_company_id ?? 0) === $companyId
            || DB::table('add_job_wise_condidates')
                ->where('job_application_id', $appId)
                ->where(function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)->orWhere('thirdparty_company_id', $companyId);
                })->exists();

        if (! $isAuthorized) {
            return response()->json(['status' => false, 'message' => 'Unauthorized candidate update.'], 403);
        }

        // 1. Single Source of Truth: Update MySQL transactional write store
        DB::table('job_applications')
            ->where('id', $appId)
            ->update([
                'recruiter_note' => $recruiterNote,
                'updated_at' => now(),
            ]);

        // 2. Synchronize Elasticsearch read store (ELASTIC_CANDIDATES via updateByQuery)
        try {
            if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                $this->elasticService->getClient()->updateByQuery([
                    'index' => ELASTIC_CANDIDATES,
                    'conflicts' => 'proceed',
                    'body' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => ['candidate_id' => (int) $appId]],
                                    ['term' => ['id' => (int) $appId]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                        'script' => [
                            'source' => 'ctx._source.recruiter_note = params.note',
                            'lang' => 'painless',
                            'params' => ['note' => $recruiterNote],
                        ],
                    ],
                ]);
                $this->elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES]);
            }
        } catch (\Throwable $e) {
            \Log::warning('recruiterNoteUpdate ELASTIC_CANDIDATES update failed: '.$e->getMessage());
        }

        // Also synchronize ELASTIC_CANDIDATES_BENCH if present
        try {
            if (defined('ELASTIC_CANDIDATES_BENCH') && $this->elasticService->indexExists(ELASTIC_CANDIDATES_BENCH)) {
                if ($this->elasticService->getClient()->exists(['index' => ELASTIC_CANDIDATES_BENCH, 'id' => (int) $appId])) {
                    $this->elasticService->getClient()->update([
                        'index' => ELASTIC_CANDIDATES_BENCH,
                        'id' => (int) $appId,
                        'body' => [
                            'doc' => ['recruiter_note' => $recruiterNote],
                        ],
                    ]);
                    $this->elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES_BENCH]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('recruiterNoteUpdate ELASTIC_CANDIDATES_BENCH update failed: '.$e->getMessage());
        }

        // 3. Bust candidate drawer cache across all slugs and associated job-wise IDs
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', $appId)->pluck('id')->toArray();
        $targetIds = array_unique(array_merge([$appId], $wiseIds));
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];

        foreach ($targetIds as $tid) {
            foreach ($slugs as $slug) {
                cache()->forget('tenant:'.$companyId.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
            }
        }

        $cachekeys = ['candidateDetails_version_user_'.$this->user->id];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        // Intent: Broadcast to invalidate client RAM & IndexedDB cache across all recruiter tabs with full recruiter note payload
        try {
            \App\Events\CandidateDrawerUpdated::dispatch(
                (int) $companyId,
                (int) $appId,
                time(),
                'notes_updated',
                array_map('intval', $wiseIds),
                null,
                null,
                $recruiterNote
            );
        } catch (\Throwable $e) {
            \Log::warning('CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        return response()->json([
            'status' => true,
            'candidate_id' => (int) $appId,
            'wise_ids' => array_map('intval', $wiseIds),
            'recruiter_note' => $recruiterNote,
        ]);
    }

    public function clientCandidateList(Request $request)
    {
        $startTime = microtime(true);
        $company_id = (int) auth()->user()->company_id;
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['client_id' => $request->clientid]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        // Phase 5 & 10 Optimization: Pre-batch relational lookup maps to eliminate N+1 queries in Blade loop
        $jobIds = array_filter(array_column($data, 'jobId'));
        $cityIds = array_filter(array_column($data, 'city_id'));

        $jobsMap = [];
        if (! empty($jobIds)) {
            $jobsMap = DB::table('jobs')
                ->select('id', 'max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')
                ->whereIn('id', array_unique($jobIds))
                ->get()
                ->keyBy('id')
                ->toArray();
        }

        $citiesMap = [];
        if (! empty($cityIds)) {
            $citiesMap = DB::table('city_list')
                ->whereIn('city_id', array_unique($cityIds))
                ->pluck('city', 'city_id')
                ->toArray();
        }

        $statusMap = cache()->remember('global_candidate_status_name_map', 86400, function () {
            return DB::table('candidate_status')->pluck('name', 'id')->toArray();
        });

        $jobAssignMap = [];
        foreach ($data as $candidateRow) {
            $cObj = (object) $candidateRow;
            if ($company_id != @$cObj->ClientComID && @$cObj->ClientComID > 0) {
                $spok = DB::table('job_assign')
                    ->select('job_assign.job_id', 'job_assign.company_id', 'users.name')
                    ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
                    ->where('job_assign.job_id', @$cObj->jobId)
                    ->where('job_assign.company_id', @$cObj->ClientComID)
                    ->first();
                if ($spok) {
                    $jobAssignMap[$cObj->jobId.'_'.$cObj->ClientComID] = $spok->name;
                }
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        \Log::info('CLIENT_DETAILS_APM: clientCandidateList completed', [
            'client_id' => $request->clientid,
            'company_id' => $company_id,
            'page' => $page,
            'item_count' => count($data),
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data', 'company_id', 'jobsMap', 'citiesMap', 'statusMap', 'jobAssignMap'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function clientCandidateJoinList(Request $request)
    {
        // $candidates = JobApplication::query();
        $company_id = (int) auth()->user()->company_id;
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['client_id' => $request->clientid]],
                                ['term' => ['job_master_status_id' => 6]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [],
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        // Phase 5 & 10 Optimization: Pre-batch relational lookup maps to eliminate N+1 queries in Blade loop
        $jobIds = array_filter(array_column($data, 'jobId'));
        $cityIds = array_filter(array_column($data, 'city_id'));

        $jobsMap = [];
        if (! empty($jobIds)) {
            $jobsMap = DB::table('jobs')
                ->select('id', 'max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')
                ->whereIn('id', array_unique($jobIds))
                ->get()
                ->keyBy('id')
                ->toArray();
        }

        $citiesMap = [];
        if (! empty($cityIds)) {
            $citiesMap = DB::table('city_list')
                ->whereIn('city_id', array_unique($cityIds))
                ->pluck('city', 'city_id')
                ->toArray();
        }

        $statusMap = cache()->remember('global_candidate_status_name_map', 86400, function () {
            return DB::table('candidate_status')->pluck('name', 'id')->toArray();
        });

        $jobAssignMap = [];
        foreach ($data as $candidateRow) {
            $cObj = (object) $candidateRow;
            if ($company_id != @$cObj->ClientComID && @$cObj->ClientComID > 0) {
                $spok = DB::table('job_assign')
                    ->select('job_assign.job_id', 'job_assign.company_id', 'users.name')
                    ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
                    ->where('job_assign.job_id', @$cObj->jobId)
                    ->where('job_assign.company_id', @$cObj->ClientComID)
                    ->first();
                if ($spok) {
                    $jobAssignMap[$cObj->jobId.'_'.$cObj->ClientComID] = $spok->name;
                }
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data', 'company_id', 'jobsMap', 'citiesMap', 'statusMap', 'jobAssignMap'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function clientCandidateInterviewTracker(Request $request)
    {
        $startTime = microtime(true);
        $company_id = (int) auth()->user()->company_id;

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 10 results per page
        $from = ($page - 1) * $size;

        $client_id = (int) $request->clientid;
        $sort = [
            ['schedule_date' => ['order' => 'desc']],
            ['id' => ['order' => 'desc']],
        ];

        $must = [
            [
                'bool' => [
                    'should' => [
                        ['term' => ['job_company_id' => $company_id]],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['ClientComID' => $company_id]],
                                    ['term' => ['candidate_type' => 'other']],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            [
                'bool' => [
                    'should' => [
                        ['term' => ['client_id' => $client_id]],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['from_company_id' => $client_id]],
                                    ['term' => ['to_company_id' => $company_id]],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $boolQuery = [
            'bool' => [
                'must' => $must,
                'filter' => [],
            ],
        ];

        // Phase 6 Optimization: Restrict _source fields to payload attributes required for Interview Tracker view
        $sourceFields = [
            'id', 'full_name', 'current_designation', 'candidate_id', 'jobId', 'job_id', 'job_title', 'client_id', 'comId',
            'company_id', 'job_company_id', 'company_name', 'client_name', 'recommendation_id', 'to_company_id', 'from_company_id',
            'schedule_date', 'schedule_time', 'schedule_to_time', 'timezone_abbreviation', 'end_time', 'interviewer_name',
            'interviewers', 'mode', 'mode_name', 'mode_lavel_name', 'meeting_link', 'status', 'available_status', 'feedback',
            'source', 'round_name', 'comments', 'master_status_id', 'master_status_name', 'child_status_id', 'css_style',
            'average_star_rating', 'created_at',
            'posted_by', 'submitted_by', 'submitted_by_name', 'submitted_by_company_name', 'post_company_name',
            'uc_company_name', 'from_company_name', 'spokperson_name', 'upload_by_name', 'ClientComID', 'thirdparty_company_id',
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                '_source' => $sourceFields,
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        \Log::info('CLIENT_DETAILS_APM: clientCandidateInterviewTracker completed', [
            'client_id' => $request->clientid,
            'company_id' => $company_id,
            'page' => $page,
            'item_count' => count($data),
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates_interview_tracker', compact('data', 'company_id'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function directclientCandidateList(Request $request)
    {
        $from_company_id = (int) $request->clientid;
        $to_company_id = (int) auth()->user()->company_id;
        $company_id = (int) auth()->user()->company_id;

        $searchQuery = $request->input('search', $request->input('q'));
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [
            [
                'bool' => [
                    'should' => [
                        ['term' => ['from_company_id' => $from_company_id]],
                        ['term' => ['company_id' => $from_company_id]],
                        ['term' => ['client_id' => $from_company_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            [
                'bool' => [
                    'should' => [
                        ['term' => ['ClientComID' => $to_company_id]],
                        ['term' => ['to_company_id' => $to_company_id]],
                        ['term' => ['company_id' => $to_company_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $mustNot = [
            ['terms' => ['job_master_status_id' => [8, 9, '8', '9']]],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['current_designation' => $searchQuery]],
                        ['match_phrase_prefix' => ['email' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $boolQuery = [
            'bool' => [
                'must' => $must,
                'must_not' => $mustNot,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['created_at' => ['order' => 'desc', 'unmapped_type' => 'long']],
                ],
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates', compact('data', 'company_id'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    public function directclientCandidateJoinList(Request $request)
    {
        $from_company_id = (int) $request->clientid;
        $to_company_id = (int) auth()->user()->company_id;
        $company_id = (int) auth()->user()->company_id;

        $searchQuery = $request->input('search', $request->input('q'));
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [
            [
                'bool' => [
                    'should' => [
                        ['term' => ['from_company_id' => $from_company_id]],
                        ['term' => ['company_id' => $from_company_id]],
                        ['term' => ['client_id' => $from_company_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            [
                'bool' => [
                    'should' => [
                        ['term' => ['ClientComID' => $to_company_id]],
                        ['term' => ['to_company_id' => $to_company_id]],
                        ['term' => ['company_id' => $to_company_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            [
                'bool' => [
                    'should' => [
                        ['term' => ['job_master_status_id' => 6]],
                        ['term' => ['job_master_status_id' => '6']],
                        ['term' => ['master_status_id' => 6]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $mustNot = [
            ['terms' => ['job_master_status_id' => [8, 9, '8', '9']]],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['current_designation' => $searchQuery]],
                        ['match_phrase_prefix' => ['email' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $boolQuery = [
            'bool' => [
                'must' => $must,
                'must_not' => $mustNot,
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['created_at' => ['order' => 'desc', 'unmapped_type' => 'long']],
                ],
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates_join', compact('data', 'company_id'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    public function directclientCandidateInterviewTracker(Request $request)
    {
        $startTime = microtime(true);
        $client_id = (int) $request->clientid;
        $company_id = (int) auth()->user()->company_id;

        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $sort = [
            ['schedule_date' => ['order' => 'desc']],
            ['id' => ['order' => 'desc']],
        ];

        // Scoped by company_id tenancy AND client_id (direct client's jobs)
        $must = [
            [
                'bool' => [
                    'should' => [
                        ['term' => ['ClientComID' => $company_id]],
                        ['term' => ['thirdparty_company_id' => $company_id]],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['job_company_id' => $company_id]],
                                    ['term' => ['candidate_type' => 'other']],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['company_id' => $company_id]],
                                    ['term' => ['candidate_type' => 'other']],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            [
                'bool' => [
                    'should' => [
                        ['term' => ['client_id' => $client_id]],
                        ['term' => ['from_company_id' => $client_id]],
                        ['term' => ['company_id' => $client_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $sourceFields = [
            'id', 'full_name', 'current_designation', 'candidate_id', 'jobId', 'job_id', 'job_title', 'client_id', 'comId',
            'company_id', 'job_company_id', 'company_name', 'client_name', 'recommendation_id', 'to_company_id', 'from_company_id',
            'schedule_date', 'schedule_time', 'schedule_to_time', 'timezone_abbreviation', 'end_time', 'interviewer_name',
            'interviewers', 'mode', 'mode_name', 'mode_lavel_name', 'meeting_link', 'status', 'available_status', 'feedback',
            'source', 'round_name', 'comments', 'master_status_id', 'master_status_name', 'child_status_id', 'css_style',
            'average_star_rating', 'created_at',
            'posted_by', 'submitted_by', 'submitted_by_name', 'submitted_by_company_name', 'post_company_name',
            'uc_company_name', 'from_company_name', 'spokperson_name', 'upload_by_name', 'ClientComID', 'thirdparty_company_id',
        ];

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                '_source' => $sourceFields,
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [],
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        \Log::info('CLIENT_DETAILS_APM: directclientCandidateInterviewTracker completed', [
            'client_id' => $client_id,
            'company_id' => $company_id,
            'page' => $page,
            'item_count' => count($data),
            'latency_ms' => round((microtime(true) - $startTime) * 1000, 2),
        ]);

        return response()->json([
            'html' => view('service_company.ajax_pages.custom_client_candidates_interview_tracker', compact('data', 'company_id'))->render(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    /**
     * Resolves and verifies that the authenticated company has an active relation with target agency (by client_id or company_id).
     */
    protected function resolveAuthorizedAgencyCompanyId(int $authCompanyId, $rawId): ?int
    {
        if (empty($rawId)) {
            return null;
        }

        $rawId = (int) $rawId;

        // 1. Direct check: is $rawId ALREADY a company_id with active relation?
        $isCompanyRelation = DB::table('staffing_company_relation')
            ->where('final_status', 1)
            ->where(function ($q) use ($authCompanyId, $rawId) {
                $q->where(function ($inner) use ($authCompanyId, $rawId) {
                    $inner->where('from_company_id', $authCompanyId)->where('to_company_id', $rawId);
                })->orWhere(function ($inner) use ($authCompanyId, $rawId) {
                    $inner->where('from_company_id', $rawId)->where('to_company_id', $authCompanyId);
                });
            })
            ->exists();

        if ($isCompanyRelation) {
            return $rawId;
        }

        // 2. Client ID check: is $rawId a clients.id mapping to a company_id with active relation?
        $mappedCompanyId = (int) DB::table('clients')->where('id', $rawId)->value('company_id');
        if ($mappedCompanyId > 0) {
            $isMappedRelation = DB::table('staffing_company_relation')
                ->where('final_status', 1)
                ->where(function ($q) use ($authCompanyId, $mappedCompanyId) {
                    $q->where(function ($inner) use ($authCompanyId, $mappedCompanyId) {
                        $inner->where('from_company_id', $authCompanyId)->where('to_company_id', $mappedCompanyId);
                    })->orWhere(function ($inner) use ($authCompanyId, $mappedCompanyId) {
                        $inner->where('from_company_id', $mappedCompanyId)->where('to_company_id', $authCompanyId);
                    });
                })
                ->exists();

            if ($isMappedRelation) {
                return $mappedCompanyId;
            }
        }

        return null;
    }

    public function staffingclientCandidateList(Request $request)
    {
        $rawClientId = $request->clientid ?? $request->input('id');
        $from_company_id = (int) auth()->user()->company_id;
        $company_id = (int) auth()->user()->company_id;

        $to_company_id = $this->resolveAuthorizedAgencyCompanyId($from_company_id, $rawClientId);

        if (! $to_company_id) {
            return response()->json(['status' => 'fail', 'message' => 'Unauthorized partner.'], 403);
        }

        $searchQuery = trim($request->input('search') ?? $request->input('q') ?? '');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [];
        $must[] = ['term' => ['from_company_id' => $from_company_id]];
        $must[] = ['term' => ['final_status' => 1]];
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['ClientComID' => $to_company_id]],
                    ['term' => ['to_company_id' => $to_company_id]],
                    ['term' => ['thirdparty_company_id' => $to_company_id]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['email' => $searchQuery]],
                        ['match_phrase_prefix' => ['phone' => $searchQuery]],
                        ['match_phrase_prefix' => ['skills' => $searchQuery]],
                        ['match_phrase_prefix' => ['current_designation' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [['id' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'must_not' => [
                            ['terms' => ['job_master_status_id' => [8, 9]]],
                        ],
                    ],
                ],
            ],
        ];

        $total = 0;
        $data = [];

        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
            }
        }

        // DB Fallback if ES is empty or unreachable
        if ($total == 0 && empty($searchQuery)) {
            $dbCandQuery = DB::table('add_job_wise_condidates')
                ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                ->where('add_job_wise_condidates.company_id', $from_company_id)
                ->where('add_job_wise_condidates.thirdparty_company_id', $to_company_id)
                ->whereNotIn('add_job_wise_condidates.master_status_id', [8, 9]);

            $total = $dbCandQuery->count();
            if ($total > 0) {
                $dbHits = $dbCandQuery
                    ->select('job_applications.*', 'add_job_wise_condidates.master_status_id', 'add_job_wise_condidates.job_id as job_id')
                    ->skip($from)
                    ->take($size)
                    ->get();
                $data = $dbHits->map(function ($c) {
                    return (array) $c;
                })->toArray();
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => $total > 0 ? ceil($total / $size) : 1,
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.agency_client_candidates', compact('data', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    public function staffingclientCandidateJoinList(Request $request)
    {
        $rawClientId = $request->clientid ?? $request->input('id');
        $from_company_id = (int) auth()->user()->company_id;
        $company_id = (int) auth()->user()->company_id;

        $to_company_id = $this->resolveAuthorizedAgencyCompanyId($from_company_id, $rawClientId);

        if (! $to_company_id) {
            return response()->json(['status' => 'fail', 'message' => 'Unauthorized partner.'], 403);
        }

        $searchQuery = trim($request->input('search') ?? $request->input('q') ?? '');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [];
        $must[] = ['term' => ['from_company_id' => $from_company_id]];
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['job_master_status_id' => 6]],
                    ['term' => ['master_status_id' => 6]],
                ],
                'minimum_should_match' => 1,
            ],
        ];
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['ClientComID' => $to_company_id]],
                    ['term' => ['to_company_id' => $to_company_id]],
                    ['term' => ['thirdparty_company_id' => $to_company_id]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['email' => $searchQuery]],
                        ['match_phrase_prefix' => ['phone' => $searchQuery]],
                        ['match_phrase_prefix' => ['skills' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [['id' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        $total = 0;
        $data = [];

        if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
            }
        }

        // DB Fallback if ES is empty or unreachable
        if ($total == 0 && empty($searchQuery)) {
            $dbCandQuery = DB::table('add_job_wise_condidates')
                ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                ->where('add_job_wise_condidates.company_id', $from_company_id)
                ->where('add_job_wise_condidates.thirdparty_company_id', $to_company_id)
                ->where('add_job_wise_condidates.master_status_id', 6);

            $total = $dbCandQuery->count();
            if ($total > 0) {
                $dbHits = $dbCandQuery
                    ->select('job_applications.*', 'add_job_wise_condidates.master_status_id')
                    ->skip($from)
                    ->take($size)
                    ->get();
                $data = $dbHits->map(function ($c) {
                    return (array) $c;
                })->toArray();
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => $total > 0 ? ceil($total / $size) : 1,
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.agency_client_candidates_join', compact('data', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    /**
     * Intent: Fetch candidate interview tracker list for specific agency (consultancy view).
     * Tenancy: Restricted by from_company_id (auth tenant) AND agency company ID (to_company_id / ClientComID) to ensure only agency-shared candidate interviews are displayed.
     */
    public function staffingclientCandidateInterviewTracker(Request $request)
    {
        $rawClientId = $request->clientid ?? $request->input('id');
        $from_company_id = (int) auth()->user()->company_id;
        $company_id = (int) auth()->user()->company_id;

        $to_company_id = $this->resolveAuthorizedAgencyCompanyId($from_company_id, $rawClientId);

        if (! $to_company_id) {
            return response()->json(['status' => 'fail', 'message' => 'Unauthorized partner.'], 403);
        }

        $searchQuery = trim($request->input('search') ?? $request->input('q') ?? '');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10);
        $from = ($page - 1) * $size;

        $must = [];
        $must[] = ['term' => ['from_company_id' => $from_company_id]];
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['ClientComID' => $to_company_id]],
                    ['term' => ['thirdparty_company_id' => $to_company_id]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        if (! empty($searchQuery)) {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['full_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['job_title' => $searchQuery]],
                        ['match_phrase_prefix' => ['company_name' => $searchQuery]],
                        ['match_phrase_prefix' => ['mode_name' => $searchQuery]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [['id' => ['order' => 'desc']]],
                'query' => [
                    'bool' => [
                        'must' => $must,
                    ],
                ],
            ],
        ];

        $total = 0;
        $data = [];

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            try {
                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;
                $data = array_map(function ($hit) {
                    return $hit['_source'];
                }, $response['hits']['hits']);
            } catch (\Exception $e) {
                $total = 0;
                $data = [];
            }
        }

        // DB Fallback if ES is empty or unreachable
        if ($total == 0 && empty($searchQuery)) {
            $dbInterviewQuery = DB::table('interview_schedules')
                ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
                ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
                ->where('job_applications.company_id', $from_company_id)
                ->where('job_applications.thirdparty_company_id', $to_company_id);

            $total = $dbInterviewQuery->count();
            if ($total > 0) {
                $dbHits = $dbInterviewQuery
                    ->select(
                        'interview_schedules.*',
                        'job_applications.full_name',
                        'job_applications.email',
                        'job_applications.phone',
                        'job_applications.id as job_application_id',
                        'jobs.title as job_title'
                    )
                    ->skip($from)
                    ->take($size)
                    ->get();
                $data = $dbHits->map(function ($i) {
                    return (array) $i;
                })->toArray();
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => $total > 0 ? ceil($total / $size) : 1,
            'listType' => 'allcandidate',
        ];

        return response()->json([
            'html' => view('service_company.ajax_pages.agency_client_candidates_interview_tracker', compact('data', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);
    }

    /** Fetch résumé bytes from r2 / full URL / local public path. Returns string|null. */
    /**
     * Intent: Fetch resume binary bytes from R2 or fallback storage.
     * Tenancy note: Checks candidate_resume_uploads table if main R2 path is pending queue move.
     */
    private function fetchResumeBytes($resume, $appId = null)
    {
        $bytes = null;
        try {
            if (strpos($resume, 'file.jero.works') !== false) {
                $key = str_replace('https://file.jero.works/', '', $resume);
                if (Storage::disk('r2')->exists($key)) {
                    $bytes = Storage::disk('r2')->get($key);
                }
            }
            // Fallback: If destination object does not exist in R2 yet (queue pending), check candidate_resume_uploads for this candidate
            if ($bytes === null && ! empty($appId)) {
                $tempUpload = DB::table('candidate_resume_uploads')
                    ->where('candidate_id', $appId)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($tempUpload && ! empty($tempUpload->object_key) && Storage::disk('r2')->exists($tempUpload->object_key)) {
                    $bytes = Storage::disk('r2')->get($tempUpload->object_key);
                }
            }
            if ($bytes === null && filter_var($resume, FILTER_VALIDATE_URL)) {
                $resp = \Illuminate\Support\Facades\Http::timeout(15)->withOptions(['verify' => false])->get($resume);
                if ($resp->successful()) {
                    $bytes = $resp->body();
                }
            }
            if ($bytes === null) {
                $local = public_path($resume);
                if (file_exists($local)) {
                    $bytes = file_get_contents($local);
                }
            }
        } catch (\Throwable $e) {
            $bytes = null;
        }

        return $bytes;
    }

    /**
     * Intent: Resolve Resume Center preview requests without streaming heavy files
     * through Laravel; PDF/DOCX bytes are served by R2 whenever possible.
     * Tenancy: scoped preview tokens include candidate_id and must match DB resume truth.
     */
    public function resumeConvertPdf($token)
    {
        $payload = $this->resolveResumePreviewPayload($token);
        $resume = $payload['resume'] ?? '';
        if (empty($resume)) {
            abort(404);
        }

        if (! empty($payload['candidate_id'])) {
            $this->assertResumePreviewAllowed((int) $payload['candidate_id'], (string) $resume);
        }

        $ext = strtolower(pathinfo(parse_url($resume, PHP_URL_PATH) ?: $resume, PATHINFO_EXTENSION));
        if (! in_array($ext, ['pdf', 'doc', 'docx'])) {
            abort(404);
        }

        if ($ext === 'pdf') {
            // Intent: Resume/CV preview & client-side highlighter require readable same-origin PDF bytes.
            if (request()->boolean('preview') || request()->expectsJson() === false) {
                $r2Key = $this->resumeR2KeyFromValue((string) $resume);
                try {
                    if ($r2Key !== '' && Storage::disk('r2')->exists($r2Key)) {
                        return Storage::disk('r2')->response(
                            $r2Key,
                            str_replace('"', '', basename($r2Key)),
                            ['Cache-Control' => 'private, max-age=300'],
                            'inline'
                        );
                    }
                } catch (\Throwable $e) {
                    Log::warning('R2 resume preview read error', ['error' => $e->getMessage()]);
                }

                $bytes = $this->fetchResumeBytes($resume, $payload['candidate_id'] ?? null);
                if ($bytes !== null) {
                    return response($bytes, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Cache-Control', 'private, max-age=300');
                }
            }

            return redirect()->away($this->temporaryResumePreviewUrl((string) $resume));
        }

        $cacheDir = storage_path('app/resume-pdf-cache');
        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }
        $hash = md5($resume);
        $pdfCache = $cacheDir.DIRECTORY_SEPARATOR.$hash.'.pdf';
        $r2CacheKey = 'Resumes/converted_'.$hash.'.pdf';

        try {
            if (Storage::disk('r2')->exists($r2CacheKey)) {
                // Intent: Serve converted preview bytes same-origin so existing
                // PDF.js keyword highlighting can run on cached DOC/DOCX output.
                if (request()->boolean('preview') || request()->expectsJson() === false) {
                    return Storage::disk('r2')->response(
                        $r2CacheKey,
                        'converted_'.$hash.'.pdf',
                        ['Cache-Control' => 'private, max-age=300'],
                        'inline'
                    );
                }

                return redirect()->away($this->temporaryR2Url($r2CacheKey, null));
            }
        } catch (\Throwable $e) {
            Log::warning('R2 converted PDF cache read error', ['error' => $e->getMessage()]);
        }

        if (file_exists($pdfCache) && filesize($pdfCache) > 100) {
            // Intent: Local converter cache is preview-only here; download flows
            // continue through R2 once the converted artifact is uploaded.
            if (request()->boolean('preview') || request()->expectsJson() === false) {
                return response()->file($pdfCache, [
                    'Content-Type' => 'application/pdf',
                    'Cache-Control' => 'private, max-age=300',
                ]);
            }

            try {
                Storage::disk('r2')->put($r2CacheKey, file_get_contents($pdfCache));

                return redirect()->away($this->temporaryR2Url($r2CacheKey, null));
            } catch (\Throwable $e) {
                Log::warning('R2 converted PDF cache store error', ['error' => $e->getMessage()]);
            }
        }

        // Intent: Synchronous fallback conversion for preview/download requests when
        // cached PDF is not present yet, ensuring client never receives 202 JSON.
        $bytes = $this->fetchResumeBytes($resume, $payload['candidate_id'] ?? null);
        $soffice = $this->sofficeBinary();
        if ($bytes !== null && $soffice) {
            $srcFile = $cacheDir.DIRECTORY_SEPARATOR.$hash.'.'.$ext;
            file_put_contents($srcFile, $bytes);
            $profile = 'file:///'.str_replace('\\', '/', $cacheDir.DIRECTORY_SEPARATOR.'lo_'.$hash);
            $this->runResumePdfConversion($soffice, $profile, $cacheDir, $srcFile);
            @unlink($srcFile);

            if (file_exists($pdfCache) && filesize($pdfCache) > 100) {
                try {
                    Storage::disk('r2')->put($r2CacheKey, file_get_contents($pdfCache));
                } catch (\Throwable $e) {
                    Log::warning('R2 converted PDF cache store error', ['error' => $e->getMessage()]);
                }

                if (request()->boolean('preview') || request()->expectsJson() === false) {
                    return response()->file($pdfCache, [
                        'Content-Type' => 'application/pdf',
                        'Cache-Control' => 'private, max-age=300',
                    ]);
                }

                return redirect()->away($this->temporaryR2Url($r2CacheKey, null));
            }
        }

        $statusKey = 'resume_pdf_convert_status:'.$hash;
        $status = Cache::get($statusKey);

        if ($status !== 'processing') {
            Cache::put($statusKey, 'processing', now()->addMinutes(30));
            ConvertResumeToPdfJob::dispatch((string) $resume, $hash, $ext);
        }

        return response()->json([
            'status' => 'processing',
            'message' => 'Resume preview is being prepared.',
        ], 202)
            ->header('Retry-After', '3')
            ->header('Cache-Control', 'no-store, max-age=0');
    }

    /**
     * Intent: Support both legacy encrypted resume strings and scoped JSON tokens
     * emitted by the Bench Resume Center lazy tab.
     */
    private function resolveResumePreviewPayload($token): array
    {
        $decrypted = GeneralMethod::value_decrypt($token);
        if (empty($decrypted)) {
            $safeToken = strtr((string) $token, '-_', '+/');
            $safeToken .= str_repeat('=', (4 - strlen($safeToken) % 4) % 4);
            $decodedToken = base64_decode($safeToken, true);
            if (is_string($decodedToken) && $decodedToken !== '') {
                $decrypted = GeneralMethod::value_decrypt($decodedToken);
            }
        }

        $json = is_string($decrypted) ? json_decode($decrypted, true) : null;
        if (is_array($json) && ! empty($json['resume'])) {
            return [
                'resume' => (string) $json['resume'],
                'candidate_id' => isset($json['candidate_id']) ? (int) $json['candidate_id'] : null,
            ];
        }

        return ['resume' => (string) $decrypted, 'candidate_id' => null];
    }

    /**
     * Intent: Prevent preview-token replay across candidates or tenants by
     * requiring the token resume to match the stored candidate resume.
     */
    private function assertResumePreviewAllowed(int $candidateId, string $resume): void
    {
        $query = DB::table('job_applications')
            ->select('id', 'company_id', 'resume')
            ->where('id', $candidateId);

        if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $candidate = $query->first();
        if (! $candidate || empty($candidate->resume)) {
            abort(404);
        }

        $storedKey = $this->resumeR2KeyFromValue((string) $candidate->resume);
        $requestedKey = $this->resumeR2KeyFromValue($resume);
        if ($storedKey === '' || $requestedKey === '' || ! hash_equals($storedKey, $requestedKey)) {
            abort(403);
        }
    }

    /**
     * Intent: Convert supported stored resume values into R2 object keys for
     * exact authorization comparisons and signed URL generation.
     */
    private function resumeR2KeyFromValue(string $resume): string
    {
        if (strpos($resume, 'https://file.jero.works/') === 0) {
            return ltrim((string) parse_url($resume, PHP_URL_PATH), '/');
        }

        if (filter_var($resume, FILTER_VALIDATE_URL)) {
            return '';
        }

        return ltrim($resume, '/');
    }

    /**
     * Intent: Serve preview files from R2/CDN directly so web workers only
     * authorize access and never carry resume bytes at 10K concurrency.
     */
    private function temporaryResumePreviewUrl(string $resume): string
    {
        $r2Key = $this->resumeR2KeyFromValue($resume);
        if ($r2Key !== '') {
            return $this->temporaryR2Url($r2Key, null);
        }

        return $resume;
    }

    private function temporaryR2Url(string $r2Key, ?string $downloadName): string
    {
        try {
            $options = [];
            if ($downloadName !== null) {
                $options['ResponseContentDisposition'] = $this->resumeContentDisposition($downloadName);
            }

            return Storage::disk('r2')->temporaryUrl($r2Key, now()->addMinutes(15), $options);
        } catch (\Throwable $e) {
            return 'https://file.jero.works/'.$r2Key;
        }
    }

    /**
     * Intent: Provide browser-safe attachment names for R2 downloads, including
     * candidate names with spaces, without changing stored object keys.
     */
    private function resumeContentDisposition(string $downloadName): string
    {
        $asciiName = trim(str_replace('"', '', $downloadName)) ?: 'resume.pdf';

        return 'attachment; filename="'.$asciiName.'"; filename*=UTF-8\'\''.rawurlencode($asciiName);
    }

    /**
     * Intent: create short-lived R2 download URLs only when the Download Resume
     * Center opens. The app returns metadata only; resume bytes are still served
     * directly by R2 to keep web workers free under high concurrency.
     */
    public function resumeR2DownloadUrl(Request $request, $id)
    {
        // TENANCY NOTE: same company guard as resumeAsPdf to avoid cross-tenant URL minting.
        $query = DB::table('job_applications')->select('id', 'company_id', 'full_name', 'resume')->where('id', $id);
        if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $app = $query->first();
        if (! $app || empty($app->resume)) {
            return response()->json(['status' => 'error', 'message' => 'No resume uploaded for this candidate.'], 404);
        }

        $resume = (string) $app->resume;
        $r2Key = '';
        if (strpos($resume, 'https://file.jero.works/') === 0) {
            $r2Key = ltrim((string) parse_url($resume, PHP_URL_PATH), '/');
        } elseif (! filter_var($resume, FILTER_VALIDATE_URL)) {
            $r2Key = ltrim($resume, '/');
        }

        if ($r2Key === '') {
            return response()->json(['status' => 'error', 'message' => 'Original resume is not available on R2.'], 404);
        }

        $resolvedKey = $r2Key;
        $downloadName = $this->resumeCenterDownloadName((string) ($app->full_name ?? ''), $resolvedKey);

        if (in_array(strtolower(pathinfo($resolvedKey, PATHINFO_EXTENSION)), ['htm', 'html'])) {
            try {
                $fallbackKey = preg_replace('/\.[^.\/]+$/', '.pdf', $resolvedKey);
                if ($fallbackKey !== $resolvedKey && Storage::disk('r2')->exists($fallbackKey)) {
                    $resolvedKey = $fallbackKey;
                    $downloadName = $this->resumeCenterDownloadName((string) ($app->full_name ?? ''), $resolvedKey);
                }
            } catch (\Throwable $e) {
                Log::warning('R2 original resume fallback check error', ['application_id' => $id, 'error' => $e->getMessage()]);
            }
        }

        $publicUrl = 'https://file.jero.works/'.$r2Key;
        $originalDirectUrl = $this->temporaryR2Url($r2Key, $this->resumeCenterDownloadName((string) ($app->full_name ?? ''), $r2Key));
        $resolvedPublicUrl = 'https://file.jero.works/'.$resolvedKey;
        $resolvedDirectUrl = $this->temporaryR2Url($resolvedKey, $downloadName);

        if ($request->boolean('redirect')) {
            return redirect()->away($resolvedDirectUrl);
        }

        return response()->json([
            'status' => 'success',
            'r2_direct_url' => $resolvedDirectUrl,
            'r2_public_url' => $resolvedPublicUrl,
            'original_r2_direct_url' => $originalDirectUrl,
            'original_r2_public_url' => $publicUrl,
            'original_r2_available' => true,
            'download_url' => $resolvedDirectUrl,
        ])->header('Cache-Control', 'no-store, max-age=0');
    }

    /**
     * Intent: Keep Resume Center direct-R2 downloads named for the candidate while
     * leaving the stored object key and billing/download decisions unchanged.
     */
    private function resumeCenterDownloadName(string $candidateName, string $r2Key): string
    {
        $name = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $candidateName);
        $name = trim((string) preg_replace('/\s+/', ' ', (string) $name));
        $ext = strtolower(pathinfo($r2Key, PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = 'file';
        }

        return 'JEROWORKS_'.($name !== '' ? $name : 'Candidate').'.'.$ext;
    }

    /**
     * Download Resume Center: Generates pre-signed PUT URLs so the browser can upload
     * client-compiled PDFs directly to Cloudflare R2 without passing bytes through PHP/Octane.
     */
    public function rdc_attachment_initiate(Request $request, R2PresignService $r2PresignService)
    {
        try {
            $companyId = (int) auth()->user()->company_id;
            $userId = (int) auth()->user()->id;

            $filename = (string) $request->input('filename', 'Jeroworks_Resume.pdf');
            $contentType = (string) $request->input('contentType', 'application/pdf');
            $candidateId = (int) $request->input('candidate_id', 0);

            // Tenancy check on candidate
            if ($candidateId > 0) {
                $exists = DB::table('job_applications')
                    ->where('id', $candidateId)
                    ->where('company_id', $companyId)
                    ->exists();
                if (! $exists && ! auth()->user()->is_superadmin) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized candidate scope.'], 403);
                }
            }

            $sanitizedFilename = preg_replace('/[^\w\s.-]/', '_', $filename);
            $result = $r2PresignService->generateUploadUrl($sanitizedFilename, $contentType, 900);

            return response()->json([
                'status' => 'success',
                'upload_url' => $result['upload_url'],
                'object_key' => $result['object_key'],
                'public_url' => $result['public_url'],
                'expires_in' => $result['expires_in'],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('[rdc_attachment_initiate] Cloud initiation fallback: '.$e->getMessage());

            return response()->json([
                'status' => 'fallback',
                'message' => 'Cloud storage initiation unavailable: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Same-origin proxy for the logged-in company's own logo so it can be embedded
     * into the résumé-banner canvas without CORS taint. No URL param -> no SSRF.
     */
    public function companyLogoProxy(Request $request)
    {
        $cid = auth()->user()->company_id ?? (int) $request->get('cid', 0);
        if (empty($cid)) {
            abort(404);
        }
        $row = DB::table('companies')->select('logo')->where('id', $cid)->first();
        $logo = $row->logo ?? '';
        if (empty($logo)) {
            abort(404);
        }
        try {
            $payload = Cache::remember('company_logo_proxy:'.$cid.':'.md5($logo), 86400, function () use ($logo) {
                // 1. Authoritative Cloudflare R2 disk read
                $r2Key = '';
                if (str_starts_with($logo, 'https://file.jero.works/')) {
                    $r2Key = ltrim((string) parse_url($logo, PHP_URL_PATH), '/');
                } elseif (! filter_var($logo, FILTER_VALIDATE_URL)) {
                    $r2Key = ltrim($logo, '/');
                }

                if (! empty($r2Key)) {
                    try {
                        if (\Storage::disk('r2')->exists($r2Key)) {
                            $body = \Storage::disk('r2')->get($r2Key);
                            $ext = strtolower(pathinfo($r2Key, PATHINFO_EXTENSION));
                            $map = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp'];
                            $ct = $map[$ext] ?? 'image/png';
                            if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
                                $ct = 'image/png';
                            } elseif (str_starts_with($body, "\xFF\xD8\xFF")) {
                                $ct = 'image/jpeg';
                            }

                            return ['body' => $body, 'content_type' => $ct];
                        }
                    } catch (\Throwable $r2Err) {
                        \Log::warning('R2 logo fetch warning: '.$r2Err->getMessage());
                    }
                }

                // 2. Local public storage read
                if (! filter_var($logo, FILTER_VALIDATE_URL)) {
                    $localPath = public_path(ltrim($logo, '/'));
                    if (file_exists($localPath) && is_readable($localPath)) {
                        $body = file_get_contents($localPath);
                        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                        $map = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp'];
                        $ct = $map[$ext] ?? 'image/png';
                        if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
                            $ct = 'image/png';
                        } elseif (str_starts_with($body, "\xFF\xD8\xFF")) {
                            $ct = 'image/jpeg';
                        }

                        return ['body' => $body, 'content_type' => $ct];
                    }
                }

                // 3. Remote HTTP fetch fallback
                $resp = \Illuminate\Support\Facades\Http::timeout(8)->withOptions(['verify' => false])->get($logo);
                if (! $resp->successful() || strlen($resp->body()) > 1024 * 1024) {
                    return null;
                }

                $body = $resp->body();
                $ct = $resp->header('Content-Type');
                if (! $ct || strpos($ct, 'image/') !== 0) {
                    $ext = strtolower(pathinfo(parse_url($logo, PHP_URL_PATH) ?: $logo, PATHINFO_EXTENSION));
                    $map = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'webp' => 'image/webp'];
                    $ct = $map[$ext] ?? 'image/png';
                }
                if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
                    $ct = 'image/png';
                } elseif (str_starts_with($body, "\xFF\xD8\xFF")) {
                    $ct = 'image/jpeg';
                }

                return ['body' => $body, 'content_type' => $ct];
            });

            if (! $payload || empty($payload['body'])) {
                abort(404);
            }

            return response($payload['body'], 200)
                ->header('Content-Type', $payload['content_type'])
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Cache-Control', 'public, max-age=86400');
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    private function sofficeBinary()
    {
        $candidates = [
            config('services.soffice.path'),
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            '/usr/bin/soffice', '/usr/bin/libreoffice', '/opt/libreoffice/program/soffice',
        ];
        foreach ($candidates as $c) {
            if ($c && @file_exists($c)) {
                return $c;
            }
        }

        return null;
    }

    /**
     * Intent: convert one uploaded Word resume to PDF with a bounded LibreOffice
     * process so a malformed DOC/DOCX cannot pin a web worker indefinitely.
     */
    private function runResumePdfConversion($soffice, $profile, $cacheDir, $srcFile)
    {
        $process = new Process([
            $soffice,
            '--headless',
            '--norestore',
            '--nofirststartwizard',
            '-env:UserInstallation='.$profile,
            '--convert-to',
            'pdf',
            '--outdir',
            $cacheDir,
            $srcFile,
        ]);
        $process->setTimeout(45);
        try {
            $process->run();
        } catch (\Throwable $e) {
            Log::warning('Resume PDF conversion process failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Intent: Backward-compatible candidate-id preview route. It now delegates to
     * the same queue-backed converter used by the Resume Center.
     */
    public function resumeAsPdf($id)
    {
        // TENANCY NOTE: Guard candidate application fetch with company_id to prevent cross-tenant IDOR
        $query = DB::table('job_applications')->where('id', $id);
        if (auth()->check() && ! auth()->user()->is_superadmin && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }
        $app = $query->first();
        if (! $app || empty($app->resume)) {
            abort(404);
        }
        $resume = $app->resume;
        $ext = strtolower(pathinfo(parse_url($resume, PHP_URL_PATH) ?: $resume, PATHINFO_EXTENSION));
        if (! in_array($ext, ['doc', 'docx'])) {
            abort(404);
        }

        $token = GeneralMethod::value_encrypt(json_encode([
            'scope' => 'candidate_resume_preview',
            'candidate_id' => (int) $app->id,
            'resume' => (string) $resume,
        ]));

        return $this->resumeConvertPdf($token);
    }

    /**
     * Dedicated Online ATS Resume Studio & Editor Workspace
     * Tenancy-guarded full-page document editor and preview.
     */
    public function resumeStudio(Request $request, $id)
    {
        $tenantCompanyId = (int) (auth()->user()->company_id ?? 0);
        $query = DB::table('job_applications')->where('id', $id);
        if ($tenantCompanyId > 0 && ! auth()->user()->is_superadmin) {
            $query->where(function ($q) use ($tenantCompanyId) {
                $q->where('company_id', $tenantCompanyId)
                    ->orWhere('thirdparty_company_id', $tenantCompanyId);
            });
        }
        $application = $query->first();
        if (! $application) {
            abort(404, 'Candidate application not found.');
        }

        $job = null;
        $jobSkills = [];
        if (! empty($application->job_id)) {
            $job = DB::table('jobs')->where('id', $application->job_id)->first();
            // 1. Check job_metronics_skills (primary in JERO)
            $metronicSkills = DB::table('job_metronics_skills')
                ->where('job_id', $application->job_id)
                ->pluck('skill_name')
                ->toArray();

            // 2. Check job_skills joined with skills
            $standardSkills = DB::table('job_skills')
                ->join('skills', 'job_skills.skill_id', '=', 'skills.id')
                ->where('job_skills.job_id', $application->job_id)
                ->pluck('skills.name')
                ->toArray();

            $combined = array_merge($metronicSkills, $standardSkills);
            if (! empty($combined)) {
                $jobSkills = array_values(array_unique(array_filter(array_map('trim', $combined))));
            }
        }

        // Candidate skills from job_application_skills table + application->skills column
        $candSkillsList = DB::table('job_application_skills')
            ->where('job_application_id', $application->id)
            ->pluck('skills')
            ->toArray();
        if (! empty($application->skills)) {
            foreach (explode(',', $application->skills) as $cs) {
                $cs = trim($cs);
                if ($cs !== '') {
                    $candSkillsList[] = $cs;
                }
            }
        }
        $candSkills = array_values(array_unique(array_filter(array_map('trim', $candSkillsList))));

        // Fallback: If jobSkills is empty, utilize candidate skills so keywords panel always has skills to match & highlight
        if (empty($jobSkills) && ! empty($candSkills)) {
            $jobSkills = $candSkills;
        }

        // Prescreen Dossier Details
        $noticePeriod = 'Immediate / Serving';
        if (! empty($application->np_id)) {
            $npRow = DB::table('notice_periods')->where('id', $application->np_id)->first();
            if ($npRow && ! empty($npRow->notice_period)) {
                $noticePeriod = $npRow->notice_period;
            } elseif (! empty($application->np_id)) {
                $noticePeriod = (string) $application->np_id;
            }
        }

        $cityName = 'Verified';
        if (! empty($application->city_id)) {
            $cityRow = DB::table('cities')->where('id', $application->city_id)->first();
            if ($cityRow && ! empty($cityRow->name)) {
                $cityName = $cityRow->name;
            }
        }

        $prescreenNotes = (string) (@$application->comment ?: @$application->cover_letter ?: @$application->career_summary ?: 'Candidate thoroughly pre-screened and assessed for technical competency, cultural fit, and delivery readiness.');
        $recruiterName = ucwords(auth()->user()->name ?? 'Senior Technical Recruiter');

        $company = null;
        $companyLogo = '';
        $companyName = '';
        if ($tenantCompanyId > 0) {
            $company = DB::table('companies')->select('id', 'company_name', 'logo')->where('id', $tenantCompanyId)->first();
            if ($company) {
                $companyName = ucwords((string) $company->company_name);
                if (! empty($company->logo) && strlen($company->logo) > 5) {
                    $companyLogo = route('service_company.job-applications.company-logo-proxy');
                }
            }
        }

        $resumePath = parse_url((string) @$application->resume, PHP_URL_PATH) ?: (string) @$application->resume;
        $resumeExt = strtolower(pathinfo($resumePath, PATHINFO_EXTENSION));

        $rcvFetchUrl = '';
        $rcvDownloadUrl = '';
        if (! empty($application->resume)) {
            $__r = $application->resume;
            if (strpos($__r, 'file.jero.works') !== false || strpos($__r, 'r2.cloudflarestorage.com') !== false) {
                $__rcvEncodedPath = rtrim(strtr(base64_encode($__r), '+/', '-_'), '=');
                $rcvFetchUrl = route('download.resume', ['id' => $application->id, 'path' => $__rcvEncodedPath, 'preview' => 1]);
                $rcvDownloadUrl = route('download.resume', ['id' => $application->id, 'path' => $__rcvEncodedPath]);
            } elseif (filter_var($__r, FILTER_VALIDATE_URL)) {
                $rcvFetchUrl = $__r;
                $rcvDownloadUrl = $__r;
            } else {
                $rcvFetchUrl = url($__r);
                $rcvDownloadUrl = url($__r);
            }
        }
        $rcvPublicUrl = (string) @$application->resume;
        $r2SignUrl = route('service_company.job-applications.r2-download-url', ['id' => $application->id]);
        $optId = (int) $request->query('opt', 61);

        // Full Pre-Screen Report Dossier Data (matching buildDefinition from showdetail.blade.php)
        $rsSkills = [];
        $candidateSkillsCombined = array_values(array_unique(array_filter(array_merge($candSkills, $jobSkills))));
        $idx = 0;
        foreach ($candidateSkillsCombined as $sk) {
            $expNum = max(1, 6 - ($idx % 4));
            $rating = max(3, 5 - ($idx % 3));
            $rsSkills[] = [
                'name' => $sk,
                'expText' => $expNum.' Years',
                'exp' => $expNum,
                'rating' => $rating,
            ];
            $idx++;
            if ($idx >= 7) {
                break;
            }
        }

        $rsEducation = 'Graduate';
        if (! empty($application->qualification_name)) {
            $rsEducation = $application->qualification_name;
        } elseif (! empty($application->qualification_id)) {
            $qRow = DB::table('qualifications')->where('id', $application->qualification_id)->first();
            if ($qRow && ! empty($qRow->name)) {
                $rsEducation = $qRow->name;
            }
        }

        $rsTotalExp = trim((string) (@$application->total_exp ?? ''));
        $rsExperience = ($rsTotalExp !== '' && (float) $rsTotalExp > 0) ? $rsTotalExp.' Years' : '5 Years';

        $rsWm = trim((string) (@$application->work_mode ?: (@$application->wm ?: 'Hybrid')));
        $rsWorkMode = ucwords(str_replace(['_', '-'], ' ', strtolower($rsWm)));

        $rsMatchPct = (int) round((float) (@$application->skill_match ?? 0));
        if ($rsMatchPct <= 0) {
            $rsMatchPct = ! empty($application->rating) ? (int) ($application->rating * 20) : 88;
        }

        $rsCareerSummary = trim(strip_tags(html_entity_decode((string) (@$application->careersummary ?: (@$application->career_summary ?: (@$application->cover_letter ?: ''))), ENT_QUOTES, 'UTF-8')));
        if (empty($rsCareerSummary)) {
            $rsCareerSummary = 'Experienced professional with demonstrated background in designing, developing, and deploying robust software architectures, driving end-to-end delivery and engineering excellence.';
        }

        $rsRecruiterNote = trim(strip_tags(html_entity_decode((string) (@$application->recruiter_note ?? ''), ENT_QUOTES, 'UTF-8')));

        $rsCurrentCompany = ucwords((string) (@$application->current_company ?: (@$application->cur_company_name ?: 'Hiring Partner Network')));
        $rsCurrentDesignation = ucwords((string) (@$application->current_designation ?: (@$job->title ?: 'Verified Professional')));

        $rsValidation = [
            ['label' => 'RESUME', 'status' => 'Verified'],
            ['label' => 'EMAIL ID', 'status' => 'Verified'],
            ['label' => 'PHONE NUMBER', 'status' => 'Verified'],
            ['label' => 'LINKEDIN ID', 'status' => ! empty($application->linkedin) ? 'Verified' : 'Discussed'],
            ['label' => 'JOB DESCRIPTION', 'status' => 'Shared with candidate'],
        ];

        return view('service_company.job-applications.resume-studio', compact(
            'application',
            'job',
            'jobSkills',
            'candSkills',
            'noticePeriod',
            'cityName',
            'prescreenNotes',
            'recruiterName',
            'companyName',
            'companyLogo',
            'resumeExt',
            'rcvFetchUrl',
            'rcvDownloadUrl',
            'rcvPublicUrl',
            'r2SignUrl',
            'optId',
            'rsSkills',
            'rsEducation',
            'rsExperience',
            'rsWorkMode',
            'rsMatchPct',
            'rsCareerSummary',
            'rsRecruiterNote',
            'rsCurrentCompany',
            'rsCurrentDesignation',
            'rsValidation'
        ));
    }

    // **  end ajax  */
    public function create($jobId = null, $appId = null)
    {

        $jobId = GeneralMethod::value_decrypt($jobId);
        // Step 4: decrypt $appId symmetrically with $jobId — supports both plain integers
        // (the common case) and encrypted hashes that some templates may produce.
        $appId = GeneralMethod::value_decrypt($appId);
        // dd($jobId);
        $this->pageTitle = __('Add Candidate');
        abort_if(Gate::denies('add_job_applications'), 403);
        $eventId = 4;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        // Step 2: coins are consumed only when creating a new application, not when
        // editing an existing one — $appId > 0 means an existing record is being edited.
        $isEdit = ! empty($appId) && (int) $appId !== 0;
        if (! $isEdit) {
            $packageStatus = Cache::remember("company:coins_val:{$companyId}:{$eventId}:{$coinsRequ}", 60, function () use ($companyId, $eventId, $coinsRequ) {
                return GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
            });
            if ($packageStatus->status) {
                session()->flash('packageStatus', $packageStatus->message);
                $url = url('service_company/apptracker/'.@$jobId.'/all');

                return redirect($url);
            }
        }

        $company_id = '';
        $company_id = auth()->user()->company_id;

        abort_if(Gate::denies('add_job_applications'), 403);
        $candidateCount = JobApplication::count();
        // if ($candidateCount > 0 && $this->activePackage->package->no_of_candidate_access !== null && $this->activePackage->package->no_of_candidate_access <= $candidateCount) {
        // 	$this->message = __('modules.subscription.candidatePurchase');
        // 	return view('service_company.subscription.ask_purchase', $this->data);
        // }

        $this->jobs = Job::activeJobs();
        $this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
        $chkAdminRole = $this->user->hasRole('admin');
        if ($chkAdminRole > 0) {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
        } else {
            $this->jobs = Job::select('jobs.id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city')->join('job_assign', 'job_assign.job_id', 'jobs.id')->join('clients', 'clients.id', 'jobs.client_id')->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')->where('jobs.status', '=', 'active')->where('job_assign.user_id', '=', $this->user->id)->where('jobs.company_id', '=', $this->user->company_id)->groupBy('jobs.id')->get();
        }

        $this->qualification = Cache::rememberForever('qualification_master_list', function () {
            return DB::table('qualification_master')->select('*')->whereNotIn('id', [57, 58, 59])->orderBy('qualification_master.display_order', 'ASC')->get();
        });
        $this->jobId = $jobId;
        $this->appId = $appId;
        $this->currentSlug = request()->get('currentSlug') ?: request()->get('slug') ?: '';

        if (! empty($appId) && (int) $appId !== 0) {
            $this->application = JobApplication::withoutGlobalScopes()->find($appId);
        }

        $this->jobDetail = Job::withoutGlobalScopes()
            ->select('jobs.id', 'jobs.company_id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'jobs.total_positions', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $jobId)
            ->first();

        $this->page_event = 'ADD';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->interview_mode = ReferenceDataCache::interviewModes();
        $this->statelist = GeneralMethod::statelist(101);
        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->jobLocation = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
            ->where('client_location_jobs.job_id', $this->jobId)
            ->orderBy('client_location_jobs.id', 'DESC')
            ->first();

        $this->cities = [];

        $selectedCityIds = ! empty($this->application) && ! empty($this->application->city_id)
            ? [(int) $this->application->city_id]
            : [];
        $this->default_loc = GeneralMethod::allCitiesSelected($selectedCityIds);

        $candidateId = ! empty($appId) ? (int) $appId : (! empty($this->appId) ? (int) $this->appId : 0);
        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) use ($candidateId) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $candidateId);
            })
            ->select(
                DB::raw('MIN(job_metronics_skills.id) as id'),
                'job_metronics_skills.skill_name',
                DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
                DB::raw('MAX(candidate_job_skill_exp.rating) as rating')
            )
            ->where('job_metronics_skills.job_id', $this->jobId)
            ->groupBy('job_metronics_skills.skill_name')
            ->get();

        // dd($this->jobSkill);
        $this->skillShow = false;

        $this->countries = Cache::rememberForever('country_list_json', function () {
            return GeneralMethod::getAllCountryList();
        });
        $this->statelist = '';
        $this->cityListArr = '';
        $this->currentPage = 1;
        $this->resumeAwsPath = false;
        $this->currencyConverter = Cache::remember('currency_converter_rates', 3600, function () {
            return GeneralMethod::currencyConverter();
        });
        $this->recruiterNoteTemplates = DB::table('recruiter_note_master')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('company_id')->orWhere('company_id', auth()->user()->company_id);
            })
            ->orderBy('id')->get();

        return response()->view('service_company.job-applications.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function statelist($cid)
    {
        // $statelist = GeneralMethod::statelist($cid);

        // if ($cid == 101) {

        $Rawstatelist = DB::table('state_list')
            ->select('state_list.id', 'state_list.state as name', 'state_list.country_id')
            ->where('state_list.country_id', $cid)
            ->orderBy('state_list.state')
            ->get()->toArray();
        $statelist = json_decode(json_encode($Rawstatelist), true);
        // }

        $custom = '<option>Select State</option>';
        foreach ($statelist as $sval) {
            $custom .= '<option value="'.$sval['id'].'">'.$sval['name'].'</option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $custom]);
        // echo '<pre>'; print_r($statelist);
    }

    public function store(Request $request)
    {

        abort_if(Gate::denies('add_job_applications'), 403);

        // $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
        // if ($packageStatus != 1) {
        // 	$url = url('service_company/apptracker/' . GeneralMethod::value_encrypt(@$request->job_id) . '/all');
        // 	return redirect($url);
        // }

        $validator = Validator::make($request->all(), array_merge([
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], CandidateNoticePeriodState::validationRules($request->input('serving_notice_period'))), array_merge([
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
        ], CandidateNoticePeriodState::validationMessages()));

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'mailcheck', 'msgType' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $company_id = (int) auth()->user()->company_id;

        $clientSubmissionUuid = $request->input('client_submission_uuid') ?: ($request->header('X-Idempotency-Key') ?: null);
        $requestPayloadForHash = $request->except(['_token', 'resume', 'client_submission_uuid', '_idempotency_token']);
        $requestHash = hash('xxh128', json_encode($requestPayloadForHash));
        $redisLockKey = null;
        $redisLockAcquired = false;

        if (! empty($clientSubmissionUuid)) {
            $redisLockKey = "idemp:cand:{$company_id}:{$clientSubmissionUuid}";
            try {
                $acquired = Redis::set($redisLockKey, 'PROCESSING', 'EX', 120, 'NX');
                if ($acquired) {
                    $redisLockAcquired = true;
                } else {
                    $existingRecord = SysIdempotencyKey::where('tenant_id', $company_id)
                        ->where('idempotency_key', $clientSubmissionUuid)
                        ->first();

                    // INVARIANT G-53: Request Hash Mismatch Defense
                    if ($existingRecord && $existingRecord->request_hash !== $requestHash) {
                        return response()->json([
                            'status' => 'conflict',
                            'error' => 'IDEMPOTENCY_KEY_REUSE',
                            'message' => 'Idempotency key has already been used for a conflicting payload.',
                        ], 409);
                    }

                    if ($existingRecord && $existingRecord->status === 'COMPLETED') {
                        $body = json_decode($existingRecord->response_body, true) ?? [];

                        return response()->json($body, $existingRecord->response_status ?? 200);
                    }

                    // Check if lease has expired (Gate G-52)
                    if ($existingRecord && $existingRecord->status === 'PROCESSING' && $existingRecord->processing_until < now()) {
                        // Lease expired -> allow recovery
                    } else {
                        return response()->json([
                            'status' => 'conflict',
                            'error' => 'IN_FLIGHT',
                            'message' => 'Candidate submission is currently processing. Please wait...',
                        ], 409);
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('store(): Redis idempotency lock failed open: '.$e->getMessage());
            }
        }

        $eventId = 4;
        $coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
        if ($coinsDeductions->status) {
            if (! empty($clientSubmissionUuid)) {
                SysIdempotencyKey::where('tenant_id', $company_id)->where('idempotency_key', $clientSubmissionUuid)->delete();
            }
            if ($redisLockKey && $redisLockAcquired) {
                try {
                    Redis::del($redisLockKey);
                } catch (\Throwable) {
                }
            }

            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        $applicationStatus = ApplicationStatus::first();
        $jobApplication = new JobApplication;
        $jobApplication->client_submission_uuid = $clientSubmissionUuid ?: null;
        $jobApplication->resume_version = 1;
        $jobApplication->full_name = ucwords(strtolower($request->full_name));
        $jobApplication->job_id = $request->job_id;
        // $jobApplication->status_id 			=	$applicationStatus->id;
        $jobApplication->status_id = 8;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        // $jobApplication->cover_letter 		=	$request->cover_letter;
        $jobApplication->column_priority = 0;

        $jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
        $jobApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

        // $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

        $total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '') ? $request->total_exp_year.'.'.$request->total_exp_month : null;

        $rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '') ? $request->rel_exp_year.'.'.$request->rel_exp_month : null;

        // $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

        $jobApplication->total_exp = $total_exp; // $request->total_exp;
        // $jobApplication->total_exp 			=	$request->total_exp;
        $jobApplication->rel_exp = $rel_exp; // $request->rel_exp;
        // $jobApplication->passport				=	$request->passport;
        $salaryMode = $this->normalizedCandidateSalaryMode($request->input('current_ctc_mode'));
        $jobApplication->ctc = $request->ctc;
        $jobApplication->current_ctc_cur = $request->current_ctc_cur;
        $jobApplication->current_ctc_mode = $salaryMode;

        $jobApplication->ectc = $request->ectc;
        $jobApplication->current_ectc_cur = $request->current_ectc_cur;
        $jobApplication->current_ectc_mode = $salaryMode;
        $jobApplication->gender = $request->gender;
        $jobApplication->notice_comments = @$request->notice_comments;

        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$request->current_ectc_cur];
        $econvertedAmount = $request->bill_annual;
        if ($request->current_ectc_cur > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
        $jobApplication->ectc_yearly = @$ectcdivided->yearly;
        $jobApplication->ectc_monthly = @$ectcdivided->monthly;
        $jobApplication->ectc_hourly = @$ectcdivided->hourly;

        $currency = $currency[$request->current_ctc_cur];
        $convertedAmount = $request->pay_annual;
        if ($request->current_ctc_cur > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);

        $jobApplication->ctc_yearly = @$ctcdivided->yearly;
        $jobApplication->ctc_monthly = @$ctcdivided->monthly;
        $jobApplication->ctc_hourly = @$ctcdivided->hourly;

        $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
        $jobApplication->percentage_hike = $this->calculatedCandidateSalaryIncrease(
            $jobApplication->ctc_yearly,
            $jobApplication->ectc_yearly
        );
        $jobApplication->comment = $request->comment;
        $jobApplication->pincode = $experienceGet;

        // $jobApplication->current_location 	=	$request->current_location;
        $jobApplication->current_designation = $request->current_designation;
        $jobApplication->current_company = $request->current_company;
        $jobApplication->careersummary = $request->careersummary;
        $jobApplication->nationality = $request->nationality;
        $jobApplication->wm = $request->wm;
        $jobApplication->employmentType = $request->employmentType;
        $jobApplication->oh = $request->oh;
        $jobApplication->visastatu = $request->visastatu;
        $jobApplication->referencename = $request->referencename;
        $jobApplication->recruiter_note = $request->recruiter_note;
        $jobApplication->reason_for_job_change = $request->reason_for_job_change;

        $parsedSkillList = [];
        $skillsInput = $request->skills;

        if (is_array($skillsInput)) {
            foreach ($skillsInput as $sval) {
                if (is_array($sval) && isset($sval['value'])) {
                    $parsedSkillList[] = trim($sval['value']);
                } elseif (is_string($sval)) {
                    $parsedSkillList[] = trim($sval);
                }
            }
        } elseif (is_string($skillsInput) && ! empty($skillsInput)) {
            $decoded = json_decode($skillsInput, true);
            if (is_array($decoded)) {
                foreach ($decoded as $sval) {
                    if (is_array($sval) && isset($sval['value'])) {
                        $parsedSkillList[] = trim($sval['value']);
                    } elseif (is_string($sval)) {
                        $parsedSkillList[] = trim($sval);
                    }
                }
            } else {
                $parsedSkillList = array_filter(array_map('trim', explode(',', $skillsInput)));
            }
        }

        $parsedSkillList = array_values(array_unique(array_filter($parsedSkillList)));
        if (! empty($parsedSkillList)) {
            GeneralMethod::skillsAddElastic($parsedSkillList);
        }
        $skillStr = implode(' , ', $parsedSkillList);
        $jobApplication->skills = $skillStr;

        // $jobApplication->master_status_id 	=	$applicationStatus->id;
        $jobApplication->master_status_id = 8;
        $jobApplication->secondrary_email = $request->secondrary_email;
        $jobApplication->secondary_mobile = $request->secondary_mobile;
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $jobApplication->submitted_by = $submitted_by;
        }

        $noticePeriodState = CandidateNoticePeriodState::fromRequest($request);
        $jobApplication->np_id = $noticePeriodState['np_id'];
        $jobApplication->last_working_date = $noticePeriodState['last_working_date'];

        if (! empty($request->qualification)) {
            $jobApplication->qualification = $request->qualification;
        }
        $jobApplication->company_id = $this->user->company_id;
        $jobApplication->serving_notice_period_mode = $noticePeriodState['serving_notice_period_mode'];
        if ($request->has('gender')) {
            $jobApplication->gender = $request->gender;
        }
        if ($request->has('dob')) {
            $jobApplication->dob = $request->dob;
        }

        $stateId = '';
        $cityID = '';
        $countryID = '';
        $location_find = '';
        /* Intent: Canonical Location Resolution Engine Fast-Path (0% MySQL Reads, CQRS Compliant) */
        if ($request->filled('location_city_id') && (int) $request->location_city_id > 0) {
            $cityID = (int) $request->location_city_id;
            $stateId = (int) $request->location_state_id;
            $countryID = (int) ($request->location_country_id ?: 101);
            $location_find = (string) $request->location_canonical_label;
        } else {
            $workLocation = $this->normalizedCandidateLocation($request->input('kt_tagify_work_location'));
            if ($workLocation !== null) {
                $cityName = $workLocation['city'];
                $stateName = $workLocation['state'];
                $countrName = $workLocation['country'];
                $location_find = $cityName.'_'.$stateName.'_'.$countrName;
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

        $jobApplication->country = $countryID;
        $jobApplication->state = $stateId;

        if ($jobApplication->country == 101) {
            $jobApplication->city_id = $cityID;
        } else {
            $jobApplication->city_id = $cityID;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;

            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $fileUrl = Storage::disk('s3')->url($filePath);
            $jobApplication->photo = $fileUrl;
            // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }

        $returnCount = 0;
        $message = 'messages.createdSuccessfully';
        $company_id = $this->user->company_id;
        $app_duplicate = $this->user->company->app_duplicate;
        $app_duplicate_days = $this->user->company->app_duplicate_days;
        $applicationEmail = $request->email;
        $applicationPhone = $request->phone;

        if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
            $returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days, $applicationPhone);
            // dd($returnCount);
            if ($returnCount > 0 && $app_duplicate == 1) {
                $message = 'Duplicate :: application already in this job';
                $jobApplication->duplicate = 1;
            } elseif ($returnCount > 0 && $app_duplicate == 2) {
                $message = 'Duplicate :: application already in system';
                $jobApplication->duplicate = 1;
            }
            // dd($jobApplication->duplicate,$returnCount,$app_duplicate,$message);
        }
        // dd($jobApplication->duplicate);

        $chkApplication = DB::table('job_applications')
            ->select('job_applications.id')
            ->where('company_id', $company_id)
            ->where(function ($query) use ($applicationEmail, $applicationPhone) {
                $query->where('email', $applicationEmail)
                    ->orWhere('phone', $applicationPhone);
            })
            ->count();
        if ($chkApplication > 0 && $returnCount > 0) {
            $jobApplication->duplicate = 1;
        }
        // dd("k");
        $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
        $interviewDateOne = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
        $interviewDateTwo = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
        $interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);
        $interviewDateFour = $this->formatInterviewDateTime($request->interviewDateFour, $request->interviewTimeFour, $defaultTimeFormat);
        $interviewDateFive = $this->formatInterviewDateTime($request->interviewDateFive, $request->interviewTimeFive, $defaultTimeFormat);
        $interviewDateSix = $this->formatInterviewDateTime($request->interviewDateSix, $request->interviewTimeSix, $defaultTimeFormat);

        $jobApplication->interviewDateOne = $interviewDateOne;
        $jobApplication->interviewDateTwo = $interviewDateTwo;
        $jobApplication->interviewDateThree = $interviewDateThree;
        $jobApplication->interviewDateFour = $interviewDateFour;
        $jobApplication->interviewDateFive = $interviewDateFive;
        $jobApplication->interviewDateSix = $interviewDateSix;
        $jobApplication->linkedin = $request->linkedin;
        $jobApplication->github = $request->github;
        $jobApplication->behance = $request->behance;
        $jobApplication->otherLink = $request->otherLink;
        $jobApplication->interview_mode_one = $request->interview_mode_one;
        $jobApplication->interview_mode_two = $request->interview_mode_two;
        $jobApplication->interview_mode_three = $request->interview_mode_three;
        $jobApplication->interview_mode_four = $request->interview_mode_four;
        $jobApplication->interview_mode_five = $request->interview_mode_five;
        $jobApplication->interview_mode_six = $request->interview_mode_six;

        // ── v2 upload session pre-flight (feature-flagged, runs before transaction) ─
        // Validates the session is Uploaded BEFORE we open the DB transaction so we
        // avoid a wasted BEGIN + ROLLBACK on the common path. Tenancy: session must
        // belong to this company (byUuid + forCompany composite — CLAUDE.md rule 3).
        $resumeSession = null;
        $finalResumeUrl = null;  // set in claim block; reused in resume URL block
        if (config('resumes.features.submit_with_session') && $request->filled('resume_session_uuid')) {
            $resumeSession = ResumeUpload::byUuid($request->input('resume_session_uuid'))
                ->forCompany($company_id)
                ->first();

            if (! $resumeSession) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session not found. Please upload the resume again.']);
            }

            if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload is not yet complete. Please wait for the upload to finish and try again.']);
            }

            // Ownership check: session must belong to this tenant and not be linked to another candidate/application
            if ($resumeSession->linked_candidate_id && (int) $resumeSession->linked_candidate_id !== (int) $jobApplication->id) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session has already been associated with another application. Please upload the resume again.']);
            }

            // ── R2 pre-flight: verify file exists BEFORE opening the DB transaction ──
            // Eliminates network I/O from inside the DB row lock (V2 Step 5).
            // Race window (file disappears post-check, pre-claim) is negligible for R2.
            $r2Exists = false;
            try {
                $r2Exists = Storage::disk('r2')->exists('resumes/'.$resumeSession->uuid);
            } catch (\Throwable) {
            }
            if (! $r2Exists) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Uploaded file not found in storage. Please upload the resume again.']);
            }
            // ─────────────────────────────────────────────────────────────────────────
        }
        // ─────────────────────────────────────────────────────────────────────────────

        DB::beginTransaction();
        try {
            if (! empty($clientSubmissionUuid)) {
                $idempotencyRow = SysIdempotencyKey::where('tenant_id', $company_id)
                    ->where('idempotency_key', $clientSubmissionUuid)
                    ->lockForUpdate()
                    ->first();

                if ($idempotencyRow) {
                    if ($idempotencyRow->request_hash !== $requestHash) {
                        DB::rollBack();

                        return response()->json([
                            'status' => 'conflict',
                            'error' => 'IDEMPOTENCY_KEY_REUSE',
                            'message' => 'Idempotency key has already been used for a conflicting payload.',
                        ], 409);
                    }

                    if ($idempotencyRow->status === 'COMPLETED') {
                        DB::rollBack();
                        $cachedBody = json_decode($idempotencyRow->response_body, true) ?? [];

                        return response()->json($cachedBody, $idempotencyRow->response_status ?? 200);
                    }

                    if ($idempotencyRow->status === 'PROCESSING' && $idempotencyRow->processing_until < now()) {
                        // Gate G-52: Lease recovery
                        $idempotencyRow->update([
                            'processing_until' => now()->addSeconds(60),
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json([
                            'status' => 'conflict',
                            'error' => 'IN_FLIGHT',
                            'message' => 'A submission with this UUID is currently being processed. Please wait.',
                        ], 409);
                    }
                } else {
                    SysIdempotencyKey::create([
                        'tenant_id' => $company_id,
                        'idempotency_key' => $clientSubmissionUuid,
                        'request_hash' => $requestHash,
                        'status' => 'PROCESSING',
                        'processing_until' => now()->addSeconds(60),
                    ]);
                }
            }

            $this->resilientSaveJobApplication($jobApplication);

            $finalResumeUrl = null;
            $resumeAssetId = null;
            $resumeIntent = $request->input('resume_intent', 'NONE');

            // ── v2 atomic claim / resume asset creation ──────────────────────────────
            if ($resumeIntent === 'DISCARD') {
                if ($resumeSession) {
                    ResumeUpload::where('id', $resumeSession->id)
                        ->where('company_id', $company_id)
                        ->update(['status' => ResumeUploadStatus::Cancelled->value, 'updated_at' => now()]);
                }
                $jobApplication->current_resume_id = null;
                $jobApplication->resume = '';
                $this->resilientSaveJobApplication($jobApplication);
            } else {
                if ($resumeSession) {
                    $claimed = ResumeUpload::where('id', $resumeSession->id)
                        ->where('company_id', $company_id)
                        ->where('status', ResumeUploadStatus::Uploaded->value)
                        ->update(['status' => ResumeUploadStatus::Completed->value, 'updated_at' => now()]);

                    if ($claimed === 0) {
                        DB::rollBack();
                        if ($redisLockKey && $redisLockAcquired) {
                            try {
                                Redis::del($redisLockKey);
                            } catch (\Throwable) {
                            }
                        }
                        $existing = ResumeUpload::byUuid($resumeSession->uuid)->forCompany($company_id)->first();
                        if ($existing && $existing->linked_candidate_id) {
                            return Reply::redirect(
                                url('/service_company/apptracker/'.GeneralMethod::value_encrypt($request->job_id).'/all'),
                                'Candidate already saved from this upload.'
                            );
                        }

                        return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                            'message' => 'Resume upload session is no longer valid. Please upload again.']);
                    }

                    $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
                    $r2ResumeUrl = $finalResumeUrl;

                    try {
                        $resumeAssetId = DB::table('resume_assets')->insertGetId([
                            'company_id' => (int) $company_id,
                            'candidate_id' => (int) $jobApplication->id,
                            'upload_session_id' => $resumeSession->uuid,
                            'object_key' => (string) $finalResumeUrl,
                            'original_filename' => (string) ($request->input('resume_original_name') ?: ($resumeSession->original_filename ?: 'resume.pdf')),
                            'mime_type' => (string) ($resumeSession->mime_type ?: 'application/pdf'),
                            'file_size' => (int) ($resumeSession->file_size_bytes ?: 0),
                            'checksum_sha256' => $resumeSession->sha256_hex ?: null,
                            'version' => 1,
                            'status' => 'ACTIVE',
                            'parser_status' => $request->input('parser_status', 'BYPASSED'),
                            'parser_version' => $request->input('parser_version', null),
                            'created_at' => now(),
                            'attached_at' => now(),
                        ]);
                    } catch (\Throwable $rae) {
                        \Log::warning('store(): resume_assets insert fallback: '.$rae->getMessage());
                    }

                    if ($resumeAssetId) {
                        $jobApplication->current_resume_id = $resumeAssetId;
                    }
                    $jobApplication->resume = $r2ResumeUrl;
                    $this->resilientSaveJobApplication($jobApplication);

                    ResumeUpload::where('id', $resumeSession->id)
                        ->where('company_id', $company_id)
                        ->where('status', ResumeUploadStatus::Completed->value)
                        ->update([
                            'linked_candidate_id' => $jobApplication->id,
                            'r2_url' => $finalResumeUrl,
                            'updated_at' => now(),
                        ]);
                    UploadSessionAudit::record($resumeSession->id, 'ActionSubmitLinked', [
                        'candidate_id' => $jobApplication->id,
                        'resume_asset_id' => $resumeAssetId,
                    ], $company_id, $submitted_by);
                } elseif ($request->hasFile('resume') || ($request->filled('uploaded_file_path') && $request->filled('resume_original_name'))) {
                    $companyNameR2 = DB::table('companies')
                        ->where('id', auth()->user()->company_id)
                        ->value('company_name');
                    $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                    $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                    $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;
                    $year = date('Y');
                    $month = date('m');

                    $tempPath = $request->input('uploaded_file_path');
                    $full_name = $request->input('resume_original_name');
                    if ($tempPath && $full_name) {
                        $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                        $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                        $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                        $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                        $uniqueFileName = "{$jobApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                        $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                        $fileUrl = "https://file.jero.works/{$r2Path}";

                        UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $jobApplication->id, $companyNameRTwo, $year, $month, $r2Path)->onConnection('emails');

                        try {
                            $resumeAssetId = DB::table('resume_assets')->insertGetId([
                                'company_id' => (int) $company_id,
                                'candidate_id' => (int) $jobApplication->id,
                                'upload_session_id' => null,
                                'object_key' => (string) $fileUrl,
                                'original_filename' => (string) $full_name,
                                'mime_type' => 'application/pdf',
                                'file_size' => 0,
                                'checksum_sha256' => null,
                                'version' => 1,
                                'status' => 'ACTIVE',
                                'parser_status' => $request->input('parser_status', 'BYPASSED'),
                                'parser_version' => $request->input('parser_version', null),
                                'created_at' => now(),
                                'attached_at' => now(),
                            ]);
                        } catch (\Throwable $rae) {
                            \Log::warning('store(): resume_assets insert fallback (v1): '.$rae->getMessage());
                        }

                        if ($resumeAssetId) {
                            $jobApplication->current_resume_id = $resumeAssetId;
                        }
                        $jobApplication->resume = $fileUrl;
                        $this->resilientSaveJobApplication($jobApplication);
                    } else {
                        \Log::warning('v1 upload: missing tempPath or filename on form submit — resume not saved', [
                            'company_id' => auth()->user()->company_id,
                            'job_application_id' => $jobApplication->id ?? null,
                        ]);
                    }
                }
            }

            // Guard: do not wipe the resume URL when v2 or file upload already set it above.
            if (empty($request->resumeCheck) && ! $resumeSession && ! $request->hasFile('resume') && ! $request->filled('uploaded_file_path')) {
                $jobApplication->current_resume_id = null;
                $jobApplication->resume = '';
                $this->resilientSaveJobApplication($jobApplication);
            }

            // Insert job_application_skills synchronously so primary ES indexer has skill rows available
            if (! empty($parsedSkillList)) {
                foreach ($parsedSkillList as $skillVal) {
                    if (! empty($skillVal)) {
                        if (! DB::table('job_application_skills')
                            ->where('job_application_id', '=', $jobApplication->id)
                            ->where('skills', 'LIKE', '%'.$skillVal.'%')
                            ->exists()) {
                            DB::table('job_application_skills')->insert([
                                'job_application_id' => $jobApplication->id,
                                'skills' => $skillVal,
                            ]);
                        }
                    }
                }
            }

            $percentage = GeneralMethod::skill_match_percentage($request->job_id, $parsedSkillList);

            $now = now();
            $insertArr = [
                'job_id' => $request->job_id,
                'job_application_id' => $jobApplication->id,
                'status_id' => $jobApplication->status_id,
                'master_status_id' => $jobApplication->master_status_id,
                'child_status_id' => $jobApplication->child_status_id,
                'company_id' => $company_id,
                'submitted_by' => $submitted_by,
                'skill_match' => $percentage,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $add_job_wise_condidates = DB::table('add_job_wise_condidates')->insert($insertArr);
            $lastInsertedId = DB::getPdo()->lastInsertId();

            $submittedByEmail = auth()->user()->email;

            // ── Transactional Outbox: candidate.created event ─────────────────────────
            try {
                DB::table('candidate_outbox_events')->insert([
                    'company_id' => (int) $company_id,
                    'dedupe_key' => "candidate:{$jobApplication->id}:created",
                    'aggregate_type' => 'Candidate',
                    'aggregate_id' => (int) $jobApplication->id,
                    'event_type' => 'candidate.created',
                    'event_version' => 1,
                    'payload' => json_encode([
                        'candidate_id' => (int) $jobApplication->id,
                        'job_id' => (int) $request->job_id,
                        'company_id' => (int) $company_id,
                        'submitted_by' => (int) $submitted_by,
                        'resume_asset_id' => $resumeAssetId,
                    ]),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $oe) {
                \Log::warning('store(): candidate_outbox_events insert fallback: '.$oe->getMessage());
            }

            // Commit MySQL transaction — pure local database writes with zero remote network calls
            DB::commit();

            // ── Post-Commit Operations (0% DB lock holding time) ──────────────────────
            // Primary ES indexing runs post-commit: guarantees ES failures never rollback MySQL candidate
            try {
                GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId);
            } catch (\Throwable $e) {
                \Log::warning('store(): Post-commit primary ES indexing failed: '.$e->getMessage());
            }

            // Post-commit atomic cache invalidation
            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
                'allcvtracker_version_user_'.$this->user->id,
                'allapptracker_version_user_'.$this->user->id,
                'candidate:'.$jobApplication->id,
                'candidate:'.$jobApplication->id.':resume',
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
            $this->currencyConverter = GeneralMethod::currencyConverter();

            // Dispatch ProcessOutboxEventJob to dedicated candidate-index queue
            try {
                ProcessOutboxEventJob::dispatch()->onQueue('candidate-index');
            } catch (\Throwable $e) {
                \Log::warning('store(): ProcessOutboxEventJob dispatch warning: '.$e->getMessage());
            }

            // V2 async secondary jobs — dispatched after commit so MySQL rows are visible
            try {
                $requestData = array_filter(
                    $request->except(['resume', 'photo', 'documents', 'file', 'files']),
                    fn ($val) => ! ($val instanceof \Illuminate\Http\UploadedFile)
                );

                ProcessCandidateSubmissionJob::dispatch(
                    (int) $jobApplication->id,
                    (int) $lastInsertedId,
                    (int) $request->job_id,
                    (int) $company_id,
                    (int) $submitted_by,
                    (int) $company_id,
                    (int) $jobApplication->status_id,
                    (string) $jobApplication->email,
                    $requestData
                )->onConnection('emails');

                QueueCandidateNotificationJob::dispatch(
                    (int) $lastInsertedId,
                    (int) $request->job_id,
                    (int) $company_id,
                    (string) $submittedByEmail
                )->onConnection('emails');
            } catch (\Throwable $qe) {
                \Log::warning('store(): secondary job dispatch warning: '.$qe->getMessage());
            }

            $rawJobId = GeneralMethod::value_decrypt($request->job_id);
            $currentSlug = (string) ($request->input('currentSlug') ?: $request->input('slug') ?: '');
            if ($currentSlug === 'cvtracker' || empty($rawJobId)) {
                $redirectUrl = route('service_company.cvtracker.index');
            } elseif ($currentSlug === 'clientapp') {
                $redirectUrl = route('service_company.apptracker.clientapp', [
                    'id' => GeneralMethod::value_encrypt($rawJobId),
                    'mstatus_id' => 'all',
                ]);
            } else {
                $redirectUrl = route('service_company.apptracker.index', [
                    'id' => GeneralMethod::value_encrypt($rawJobId),
                    'mstatus_id' => 'all',
                ]);
            }

            $responseData = [
                'action' => 'redirect',
                'url' => $redirectUrl,
                'candidate_id' => (int) $jobApplication->id,
                'resume' => (string) ($jobApplication->resume ?? ''),
            ];

            if (! empty($clientSubmissionUuid)) {
                SysIdempotencyKey::where('tenant_id', $company_id)
                    ->where('idempotency_key', $clientSubmissionUuid)
                    ->update([
                        'status' => 'COMPLETED',
                        'response_status' => 200,
                        'response_body' => json_encode($responseData),
                    ]);
            }

            if ($redisLockKey && $redisLockAcquired) {
                try {
                    Redis::set($redisLockKey, 'COMPLETED:'.json_encode($responseData), 'EX', 300);
                } catch (\Throwable) {
                }
            }

            return Reply::successWithData(__('messages.createdSuccessfully'), $responseData);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($redisLockKey && $redisLockAcquired) {
                try {
                    Redis::del($redisLockKey);
                } catch (\Throwable) {
                }
            }

            // Layer 3 Idempotent replay on duplicate submission UUID (MySQL error 1062)
            if ((isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) || str_contains($e->getMessage(), '1062 Duplicate entry')) {
                if (! empty($clientSubmissionUuid)) {
                    $completedRecord = SysIdempotencyKey::where('tenant_id', $company_id)
                        ->where('idempotency_key', $clientSubmissionUuid)
                        ->first();
                    if ($completedRecord && $completedRecord->response_body) {
                        $body = json_decode($completedRecord->response_body, true) ?? [];

                        return response()->json($body, $completedRecord->response_status ?? 200);
                    }

                    $existing = DB::table('job_applications')
                        ->where('company_id', $company_id)
                        ->where('client_submission_uuid', $clientSubmissionUuid)
                        ->first();

                    if ($existing) {
                        $rawJobId = GeneralMethod::value_decrypt($request->job_id);
                        $currentSlug = (string) ($request->input('currentSlug') ?: $request->input('slug') ?: '');
                        if ($currentSlug === 'cvtracker' || empty($rawJobId)) {
                            $redirectUrl = route('service_company.cvtracker.index');
                        } elseif ($currentSlug === 'clientapp') {
                            $redirectUrl = route('service_company.apptracker.clientapp', [
                                'id' => GeneralMethod::value_encrypt($rawJobId),
                                'mstatus_id' => 'all',
                            ]);
                        } else {
                            $redirectUrl = route('service_company.apptracker.index', [
                                'id' => GeneralMethod::value_encrypt($rawJobId),
                                'mstatus_id' => 'all',
                            ]);
                        }

                        return Reply::successWithData(__('messages.createdSuccessfully'), [
                            'action' => 'redirect',
                            'url' => $redirectUrl,
                            'candidate_id' => (int) $existing->id,
                            'resume' => (string) ($existing->resume ?? ''),
                        ]);
                    }
                }
            }

            if (! empty($clientSubmissionUuid)) {
                SysIdempotencyKey::where('tenant_id', $company_id)
                    ->where('idempotency_key', $clientSubmissionUuid)
                    ->where('status', 'PROCESSING')
                    ->delete();
            }

            \Log::error('store() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            if (! empty($clientSubmissionUuid)) {
                SysIdempotencyKey::where('tenant_id', $company_id)
                    ->where('idempotency_key', $clientSubmissionUuid)
                    ->where('status', 'PROCESSING')
                    ->delete();
            }
            if ($redisLockKey && $redisLockAcquired) {
                try {
                    Redis::del($redisLockKey);
                } catch (\Throwable) {
                }
            }
            \Log::error('store() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }

    }

    public function googleSheetCandidateData(Request $request)
    {

        // dd($request->all());

        $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');

        $range = 'Sheet1!A1:W1'; // Specify the range of data you want to fetch
        $values = $this->GoogleSheetServiceCandidate->getSpreadsheetValues($range, 3);
        unset($values['Sheet1'][0]);

        //  dd($values);

        if ($packageStatus != 1) {
            $url = url('service_company/apptracker/'.@$request->job_id.'/all');

            return redirect($url);
        }

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $applicationStatus = ApplicationStatus::first();
        $jobApplication = new JobApplication;
        $jobApplication->full_name = $request->full_name;
        $jobApplication->job_id = $request->job_id;
        // $jobApplication->status_id 			=	$applicationStatus->id;
        $jobApplication->status_id = 8;
        $jobApplication->email = $request->email;
        $jobApplication->phone = $request->phone;
        // $jobApplication->cover_letter 		=	$request->cover_letter;
        $jobApplication->column_priority = 0;
        $jobApplication->total_exp = $request->total_exp;
        // $jobApplication->total_exp 			=	$request->total_exp;
        $jobApplication->rel_exp = $request->rel_exp;
        // $jobApplication->passport				=	$request->passport;
        $jobApplication->ctc = $request->ctc;
        $jobApplication->current_ctc_cur = $request->current_ctc_cur;
        $jobApplication->current_ctc_mode = $request->current_ctc_mode;

        $jobApplication->ectc = $request->ectc;
        $jobApplication->current_ectc_cur = $request->current_ectc_cur;
        $jobApplication->current_ectc_mode = $request->current_ectc_mode;

        $jobApplication->percentage_hike = $request->percentage_hike;
        $jobApplication->comment = $request->comment;
        $jobApplication->pincode = $request->pincode;

        // $jobApplication->current_location 	=	$request->current_location;
        $jobApplication->current_designation = $request->current_designation;
        $jobApplication->current_company = $request->current_company;

        $cnt = 0;
        $skillStr = '';
        $skillArr = json_decode($request->skills, true);
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

        // $jobApplication->master_status_id 	=	$applicationStatus->id;
        $jobApplication->master_status_id = 8;
        $jobApplication->secondrary_email = $request->secondrary_email;
        $jobApplication->secondary_mobile = $request->secondary_mobile;
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $jobApplication->submitted_by = $submitted_by;
        }

        $serving_notice_period = '';
        $serving_notice_period = $request->serving_notice_period;
        if ($serving_notice_period == 1) {
            $jobApplication->np_id = $request->np_id;
        } elseif ($serving_notice_period == 2) {
            $jobApplication->np_id = 'Immediate';
        } else {
            $jobApplication->np_id = $request->ddl_np_id;
        }

        if (! empty($request->qualification)) {
            $jobApplication->qualification = $request->qualification;
        }
        $jobApplication->company_id = $this->user->company_id;
        $jobApplication->serving_notice_period_mode = $request->serving_notice_period;
        if ($request->has('gender')) {
            $jobApplication->gender = $request->gender;
        }
        if ($request->has('dob')) {
            $jobApplication->dob = $request->dob;
        }

        $jobApplication->country = $request->country;
        $jobApplication->state = $request->state;

        if ($jobApplication->country == 101) {
            $jobApplication->city_id = $request->city_id;
        } else {
            $jobApplication->city = $request->city;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;

            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $fileUrl = Storage::disk('s3')->url($filePath);
            $jobApplication->photo = $fileUrl;
            // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }

        $returnCount = 0;
        $message = 'messages.createdSuccessfully';
        $company_id = $this->user->company_id;
        $app_duplicate = $this->user->company->app_duplicate;
        $app_duplicate_days = $this->user->company->app_duplicate_days;
        $applicationEmail = $request->email;
        $applicationPhone = $request->phone;
        if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
            $returnCount = GeneralMethod::checkDuplicateCandidate($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days, $applicationPhone);
            if ($returnCount > 0 && $app_duplicate == 1) {
                $message = 'Duplicate :: application already in this job';
                $jobApplication->duplicate = 1;
            } elseif ($returnCount > 0 && $app_duplicate == 2) {
                $message = 'Duplicate :: application already in system';
                $jobApplication->duplicate = 1;
            }
        }

        $chkApplication = DB::table('job_applications')
            ->select('job_applications.id')
            ->where('company_id', $company_id)
            ->where(function ($query) use ($applicationEmail, $request) {
                if (! empty($applicationEmail) && ! empty($request->phone)) {
                    $query->where('email', $applicationEmail)->orWhere('phone', $request->phone);
                } elseif (! empty($applicationEmail)) {
                    $query->where('email', $applicationEmail);
                } elseif (! empty($request->phone)) {
                    $query->where('phone', $request->phone);
                }
            })
            ->count();
        if ($chkApplication > 0 && $returnCount > 0) {
            $jobApplication->duplicate = 1;
        }

        $jobApplication->save();

        $skillArr = explode(',', $jobApplication->skills);
        $percentage = GeneralMethod::skill_match_percentage($request->job_id, $skillArr);
        $now = now();
        $insertArr = ['job_id' => $request->job_id, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by, 'skill_match' => $percentage, 'created_at' => $now, 'updated_at' => $now];
        $add_job_wise_condidates = DB::table('add_job_wise_condidates')->insert($insertArr);
        $lastInsertedId = DB::getPdo()->lastInsertId();

        $cachekeys = [
            'allcvtracker_version_user_'.$this->user->id,
            'alljobslist_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $teamRankElastic = [
            'company_id' => $company_id,
            'user_id' => $submitted_by,
            'candidate_id' => $jobApplication->id,
            'profileUpload' => 1,
            'interviewScheduled' => 0,
            'interviewNoShow' => 0,
            'offerReleased' => 0,
            'candidateJoined' => 0,
            'jobsPosted' => 0,
            'feedbackGivenOnCVs' => 0,
            'postJobToVendorMarket' => 0,
            'outsourceJobsToVendors' => 0,
            'listBenchCandidates' => 0,
            'respondToBenchRequests' => 0,
        ];
        GeneralMethod::teamRankElastic($teamRankElastic);

        $jobId = $request->job_id;
        $wise_c = DB::table('job_applications')
            ->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.client_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('add_job_wise_condidates.id', '=', $lastInsertedId)->where('add_job_wise_condidates.job_id', '=', $jobId)->first();

        $partnersData = tenant_table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->where('job_assign.job_id', '=', $jobId)->pluck('users.email')->toArray();

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
            $join->on('job_applications.job_id', '=', 'jobs.id');
        });

        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
        $recommendedDataList = $recommendedDataList->toArray();

        $company_or_client = GeneralMethod::companyDetailsByClient($jobId);

        $name = '--------';
        if (@$wise_c->master_status_id == 8) {
            $name = '';
        }
        if (! empty(@$wise_c->childStatus) && @$wise_c->childStatus > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $wise_c->childStatus)->first();
            if (! empty($childStatus)) {
                $name = @$childStatus->name;
            }
        }

        $np = $jobApplication->np_id;
        if (empty($np)) {
            $np = 'N/A';
        }
        if ($jobApplication->serving_notice_period_mode == 0) {
            $np = $jobApplication->np_id.' Days';
            if ($jobApplication->np_id == 1) {
                $np = $jobApplication->np_id.'  Day';
            }
        } elseif ($jobApplication->serving_notice_period_mode == 1) {
            $np = $jobApplication->np_id;
            if ($np != 'Immediate') {
                $np = date('d-m-Y', strtotime($jobApplication->np_id));
            }
        }

        $strAdded = DB::table('clients')->select('companies.company_name', 'clients.name', 'clients.self_company')
            ->join('companies', 'companies.id', '=', 'clients.company_id')
            ->where('clients.id', @$wise_c->client_id)->first();

        $contact_person_name = '';
        if ($strAdded->self_company == 1) {
            $contact_person_name = $strAdded->company_name;
        } else {
            $contact_person_name = $strAdded->name;
        }

        $data = [
            'VendorTeam' => @$wise_c->company_name,
            'ReqID' => 'J'.@$wise_c->job_id,
            'Reqname' => @$wise_c->title,
            'CompanyNameClientname' => $company_or_client,
            'CandidateStatus' => $jobApplication->duplicate,
            'CurrentStage' => @$wise_c->name,
            'SubStage' => $name,
            'CandidateName' => $jobApplication->full_name,
            'Experience' => $jobApplication->total_exp,
            'Position' => $jobApplication->current_designation,
            'NoticePeriod' => $np,
            'RefferedBy' => @$wise_c->posted_by,
            'SubmittedOn' => date('d-M-Y H:i:s', strtotime(@$wise_c->created_at)),
        ];

        // config(['mail.from.name' => auth()->user()->name]);

        $notification = DB::table('clients')->where('id', '=', @$wise_c->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();
        $email_to_remove = auth()->user()->email;
        $index = array_search($email_to_remove, $notification);
        if ($index !== false) {
            unset($notification[$index]);
            $notification = array_values($notification);
        }
        $cc = []; // $notification; //array_merge($partnersData, $recommendedDataList);

        // $cc = array_merge($partnersData, $recommendedDataList);
        $subject = "New Candidate Added | $wise_c->title | $contact_person_name | R-$wise_c->job_id";

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];
        $checkMailPermision = GeneralMethod::mailPermission(1);
        if ($checkMailPermision) {
            $mailTypeSubject = GeneralMethod::mailTypeSubject(1, 1);
            $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
            $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.candidate.add-candidate',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.add-candidate', (object) $data, $attachments, $fromAddress, $senderName,$cc);

        }

        // 	Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data,$subject));

        // Mail::to(auth()->user()->email)->send(new AddCandidateMail((object)$data));

        // Check job Assign Or Not
        $chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $request->job_id)->where('user_id', $submitted_by)->count();
        if ($chkJobAssign < 1) {
            $values = ['company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => @$request->job_id];
            DB::table('job_assign')->insert($values);
        }
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            //   $filename = $file->getClientOriginalName();
            $filename = 'Jero_'.substr($request->full_name, 0, 10).'.'.$file->getClientOriginalExtension();
            $filePath = 'Resumes/'.$filename;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fileUrl = Storage::disk('s3')->url($filePath);
            // 			$hashname = Files::upload($request->resume, 'documents/' . $jobApplication->id, null, null, false);
            $jobApplication->documents()->create(['name' => 'Resume', 'hashname' => $fileUrl]);
        }

        if (isset($request->jobskill) && ! empty($request->jobskill)) {
            DB::table('candidate_skills_value')->where('job_id', $request->job_id)->where('candidate_id', $jobApplication->id)->delete();
            foreach ($request->jobskill as $key => $value) {
                if (! empty(@$value)) {
                    $values = ['job_id' => @$request->job_id, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value];
                    DB::table('candidate_skills_value')->insert($values);
                }
            }
        }
        $status_name = $note = '';
        $status_cnt = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)
            // ->orderBy('priority', 'ASC')
            ->count();
        if ($status_cnt > 0) {
            $status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('parent', $applicationStatus->id)->orderBy('priority', 'ASC')->first();
            $status_name = $status_row->name;
        }

        $insert_type = 0;

        $timelineArr = [
            'event_id' => 1,
            'event_by_company_id' => auth()->user()->company_id,
            'event_by_user_id' => auth()->user()->id,
            'master_process_name' => 'InReview',
            // 'child_process_name'	=>

            'application_id' => $jobApplication->id,
            'application_email' => $jobApplication->email,
            'job_id' => @$request->job_id,
            'note' => 'Successfully Added',
        ];
        GeneralMethod::UpdateTimeline($timelineArr);
        $tb_ranks = ['company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$request->job_id, 'status' => 0, 'application_id' => $jobApplication->id];
        DB::table('tb_ranks')->insert($tb_ranks);
        // Job Application Answer save
        if (isset($request->answer) && ! empty($request->answer)) {
            foreach ($request->answer as $key => $value) {
                $answer = new JobApplicationAnswer;
                $answer->job_application_id = $jobApplication->id;
                $answer->job_id = $request->job_id;
                $answer->question_id = $key;
                $answer->answer = $value;
                $answer->save();
            }
        }

        $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->count();
        $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $request->job_id)->orderBy('job_additional_detail.id', 'ASC')->get();
        if ($job_additional_detail_count > 0) {
            $inc = 0;
            foreach ($job_additional_detail as $jd) {

                $values = [
                    'job_id' => @$request->job_id,
                    'question_id' => @$jd->id,
                    'application_id' => @$jobApplication->id,
                    'job_detail_value' => @$jd->detail_value,
                    'application_detail_value' => @$request->additional_detail[$inc],
                ];
                DB::table('application_additional_detail')->insert($values);
                $expId = DB::getPdo()->lastInsertId();
                $this->elasticService->getClient()->update([
                    'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                    'id' => $expId,   // store as ES _id
                    'body' => [
                        'doc' => [
                            'job_id' => @$request->job_id,
                            'question_id' => @$jd->id,
                            'application_id' => @$jobApplication->id,
                            'job_detail_value' => @$jd->detail_value,
                            'application_detail_value' => @$request->additional_detail[$inc],
                            'created_at' => now()->toIso8601String(),
                            'updated_at' => now()->toIso8601String(),
                        ],
                        'doc_as_upsert' => true,
                    ],
                ]);
                $inc++;

            }
        }

        if (@$jobApplication->id > 0) {
            $event_name = 'CVACCESS';
            $event_table_name = 'job_applications';
            $event_table_id = @$jobApplication->id;
            $packageStatus = GeneralMethod::packageRedeem($event_name, $event_table_name, $event_table_id);
        }

        if (! empty($request->job_id)) {
            return Reply::redirect(url('/service_company/apptracker/'.$request->job_id.'/all'), __('menu.jobApplications').' '.__($message));
        } else {
            return Reply::redirect(route('service_company.cvtracker.index'), __('menu.jobApplications').' '.__($message));
        }
    }

    public function show($id)
    {
        $startTime = microtime(true);
        // $currentSlug = request()->segment(4);

        $company_id = '';
        $company_id = auth()->user()->company_id;
        $userId = $this->user->id;
        $this->editComman = true;

        $currentSlug = '';
        if (isset($_GET['slug'])) {
            $currentSlug = $_GET['slug'];
        }

        // Intent: Phase 7 Tenant-isolated cache key pattern (FINDING-PERF-04 - P1)
        $tenantId = (int) ($company_id ?: 0);
        // Intent: Auto-bust cached drawer HTML when showdetail Blade changes, so UI fixes reflect without flushing all app cache.
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $cacheKeySet = 'tenant:'.$tenantId.':candidate_details:'.$id.':'.md5($currentSlug).':view:'.$drawerViewVersion;

        $cachedResponse = cache()->get($cacheKeySet);
        if ($cachedResponse !== null) {
            return response()->json($cachedResponse);
        }

        $this->notVisiableBench = true;

        // dd(auth()->user());
        $this->CaptainCompanyid = auth()->user()->company_id;
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);

        // Intent: Verify candidate record belongs to the user's company or authorized thirdparty company (FINDING-SEC-02 - P0).
        if ($AddJobWiseCondidate) {
            $tenantCompanyId = (int) auth()->user()->company_id;
            $candCompanyId = (int) ($AddJobWiseCondidate->company_id ?? 0);
            $thirdPartyCompId = (int) ($AddJobWiseCondidate->thirdparty_company_id ?? 0);
            if ($tenantCompanyId > 0 && $candCompanyId !== $tenantCompanyId && $thirdPartyCompId !== $tenantCompanyId && ! auth()->user()->hasRole('admin')) {
                return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized candidate details access.']);
            }
        }

        $jobId = @$AddJobWiseCondidate->job_id;
        // dd($id,$jobId,$AddJobWiseCondidate);
        $this->intModeArr = GeneralMethod::interview_mode();

        if ($AddJobWiseCondidate) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $this->application = (object) $response['_source'];

            // dd($this->application);

            $this->application->id = $this->application->candidate_id;
            $this->application->master_status_name = $this->application->name;
            $this->application->offer_currency = $this->application->of_offer_currency;
            $this->application->offer_mode = $this->application->of_offer_mode;
            $this->application->offer_amount = $this->application->of_offer_amount;
            $this->application->joined_date = $this->application->of_joined_date;
            $this->application->hashname = ($this->application->hashname) ? $this->application->hashname : $this->application->resume;
            $this->application->child_status_id = $this->application->childStatus;
            $this->application->master_status_id = $this->application->job_master_status_id;

            // dd($this->application->master_status_id,$this->application->from_company_id,auth()->user()->company_id);

        } else {

            $params = [
                'index' => ELASTIC_CANDIDATES_BENCH,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $this->application = (object) $response['_source'];

        }

        $params = [
            'index' => ELASTIC_JOBS,
            'id' => $jobId,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $this->job = (object) $response['_source'];

        // Intent: V4 CQRS Read Optimization - Eradicate legacy write-on-read loops during candidate drawer GET request.
        // Un-scored skills and un-answered questions are handled as virtual in-memory projections on the read path.
        // Database mutations are strictly deferred until a recruiter explicitly scores skills or submits answers.

        $this->client_contuser_arr = ($this->job->client_contact_info_jobs);
        $this->clientjobAssignListCaptain = '';
        if ($this->job->comId != $company_id) {
            $job_assign = $this->job->job_assign;
            $jobAssign = collect($job_assign);
            $this->jobAssignListCaptain = $jobAssign
                ->where('spok_user_id', '>', 0)
                ->where('company_id', $this->job->comId)
                ->filter()
                ->values()
                ->all();
            // Check array exists
            if (! empty($this->jobAssignListCaptain)) {

                $this->clientjobAssignListCaptain = ucfirst(
                    $this->jobAssignListCaptain[0]['user_name'] ?? ''
                );
            }

            // $this->jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $this->job->comId)
            // ->filter()
            // ->map(fn ($c) => ($c))
            // ->values()
            // ->all();
            // $this->clientjobAssignListCaptain = ucfirst($this->jobAssignListCaptain[0]['user_name']);

        }

        $this->job_additional_detail = '';
        // Intent: Defer heavy 100-item ES notes query for lightweight initial drawer shell (Phase 1). Notes are lazy-loaded via API on tab click (Phase 2).
        $this->notesArr = collect([]);

        // dd($this->notesArr);

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                '_source' => ['schedule_date'],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['interview_job_wise_con_id' => (int) $id]],
                            ['term' => ['job_application_id' => (int) $this->application->id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'asc']],
                ],
                'size' => 100, // optimized size limit
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->scheduleDateArr = collect($response['hits']['hits'] ?? [])->pluck('_source.schedule_date')->filter()->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        })
            ->toArray();

        // dd($this->scheduleDateArr);

        // dd($this->schedule);

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $jobId;
        $this->job_id = $job_id;

        $job_details = Job::withoutGlobalScopes()->find($this->job_id);

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
            })
            ->select(
                DB::raw('MIN(job_metronics_skills.id) as job_skill_id'), // alias as job_skill_id for blade form name keys
                DB::raw('MIN(job_metronics_skills.id) as id'),
                'job_metronics_skills.skill_name',
                DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
                DB::raw('MAX(candidate_job_skill_exp.rating) as rating')
            )
            ->where('job_metronics_skills.job_id', '=', $this->job_id)
            ->whereNotNull('job_metronics_skills.skill_name')
            ->whereRaw("TRIM(job_metronics_skills.skill_name) != ''")
            ->groupBy('job_metronics_skills.skill_name')
            ->get();

        // Intent: Virtual in-memory defaults for non-evaluated skills (V4 CQRS read path — no DB write on GET).
        // Use string defaults so the blade's if($skill_val->skill_value) check correctly detects no experience.
        if ($this->jobSkill) {
            foreach ($this->jobSkill as $sk) {
                if ($sk->rating === null) {
                    $sk->rating = 0;
                }
                // Keep skill_value as null (not int 0) so blade falsy check works correctly
                // Blade uses: if($skill_val->skill_value) — null is falsy, 0 is falsy, both work
            }
        }
        // dd($this->jobSkill);

        $this->skillShow = true;
        if ($this->jobSkill) {
            if (count($this->jobSkill) < 0) {
                $this->skillShow = false;
            }
        } else {
            $this->skillShow = false;
        }

        $params = [
            'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['application_id' => $this->application->id]],
                            ['term' => ['job_id' => $this->job_id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'desc']],
                ],
                'size' => 100, // optimized size limit
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        if (! empty($response['hits']['hits'])) {
            $this->job_additional_detail = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        } else {
            // Intent: V4 CQRS - Virtual in-memory projection for screening questions; prevents SQL insert on GET.
            $jobAdditionalDetail = DB::table('job_additional_detail')->where('job_id', $this->job_id)->orderBy('id', 'ASC')->get();
            $this->job_additional_detail = $jobAdditionalDetail->map(function ($jd) {
                return (object) [
                    'job_id' => (int) $this->job_id,
                    'question_id' => (int) $jd->id,
                    'application_id' => (int) $this->application->id,
                    'job_detail_value' => $jd->detail_value,
                    'application_detail_value' => null,
                ];
            })->all();
        }

        // dd($this->job_id,$this->application->id,$this->job_additional_detail);

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        // Intent: Defer heavy 200-item ES timeline search & 7 bulk map DB queries for fast initial drawer shell (Phase 1). Timeline is lazy-loaded via API on tab click (Phase 3).
        $this->updateTimeLine = [];
        $companyOrClientMap = [];
        $confidentialAllowedJobsMap = [];
        $offerJobApplicationsMap = [];
        $userCompanyMap = [];
        $spokpersonMap = [];
        $interviewFeedbackMap = [];
        $interviewSkillsFeedbackCountMap = [];

        $this->companyOrClientMap = $companyOrClientMap;
        $this->confidentialAllowedJobsMap = $confidentialAllowedJobsMap;
        $this->offerJobApplicationsMap = $offerJobApplicationsMap;
        $this->userCompanyMap = $userCompanyMap;
        $this->spokpersonMap = $spokpersonMap;
        $this->interviewFeedbackMap = $interviewFeedbackMap;
        $this->interviewSkillsFeedbackCountMap = $interviewSkillsFeedbackCountMap;

        $sel_app_id = $id;
        $sel_job_id = $job_id;
        $this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
        if ($this->benchStatusCnt > 0) {
            $benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();
            $source_from_company_id = @$benchAssignRow->source_from_company_id;
            $source_from_user_id = @$benchAssignRow->source_from_user_id;
            $source_from_id = @$benchAssignRow->source_from_id;
            $source_from_type = @$benchAssignRow->source_from_type;
            $source_company_id = @$benchAssignRow->source_company_id;

            $source_user_id = @$benchAssignRow->source_user_id;
            $source_application_id = @$benchAssignRow->source_application_id;
            $source_jobid = @$benchAssignRow->source_jobid;

            // dd($this->userPermissions);

            if ($company_id != $this->application->other_company && $source_from_type == 2) {
                $this->application->phone = '2xxx-xxx-xxxx';
                $this->application->secondary_mobile = '2xxx-xxx-xxxx';
                $this->application->email = '2xxx-xxx-xxxx';
                $this->application->secondrary_email = '2xxx-xxx-xxxx';
            }

            // if ($source_from_type == 2) {

            // }

            $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users_profile.designation', 'users_profile.designation')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
            // dd("djdjdjd");
            $this->bench_additional_detail = DB::table('bench_additional_detail')
                ->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
                ->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

        }

        if (! in_array('salary_view', $this->userPermissions)) {
            $this->application->ctc = 'N/A';
            $this->application->ectc = 'N/A';
            $this->application->pay_rate_amount = 'N/A';
            $this->application->bill_rate_amount = 'N/A';
            $this->application->percentage_hike = 'N/A';
        }

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;

        // $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

        $this->bench_assign_count = 0;
        $this->bench_assign_row = collect([]);
        try {
            $params = [
                'index' => ELASTIC_BENCH_ASSIGN,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['bench_applications_id' => (int) $this->application->id]],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['id' => ['order' => 'desc']],
                    ],
                    'size' => 100, // optimized size limit
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);
            $this->bench_assign_count = $response['hits']['total']['value'] ?? 0;
            $this->bench_assign_row = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);
        } catch (\Throwable $e) {
            $this->bench_assign_count = 0;
            $this->bench_assign_row = collect([]);
        }

        $this->bench_additional_detail = collect([]);
        try {
            $params = [
                'index' => ELASTIC_BENCH_ADDITIONAL_DETAIL,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['bench_id' => (int) $this->application->id]],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['id' => ['order' => 'asc']],
                    ],
                    'size' => 100, // optimized size limit
                ],
            ];

            $response = $this->elasticService->getClient()->search($params);
            $this->bench_additional_detail = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);
        } catch (\Throwable $e) {
            $this->bench_additional_detail = collect([]);
        }

        $this->skillExperiencesCount = 0;
        $this->skillExperiences = [];

        if (@$this->application->condidate_type == 'bench_condidate') {

            $this->skillExperiences = DB::table('bench_candidate_skill_experience')
                ->where('candidate_id', $this->application->candidate_id)
                ->orderBy('skill_key')
                ->get();
            $this->skillExperiencesCount = count($this->skillExperiences);
        }

        // $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile','users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
        // ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

        $this->jobWiseCanId = $id;
        $this->editprofile = false;
        $this->skillAndQuestions = true;
        $this->currentSlug = '';
        if (isset($_GET['slug'])) {
            $currentSlug = $_GET['slug'];

            // dd($currentSlug);
            if ($currentSlug === 'apptracker') {
                $this->skillAndQuestions = true;
            }
            if ($currentSlug === 'apptracker' || $currentSlug === 'cvtracker' || $currentSlug === 'interview-schedule') {
                $this->editprofile = true;
                $this->currentSlug = $_GET['slug'];
            }
        }

        $this->currentPage = 1;
        if (isset($_GET['currentPage'])) {
            $currentPage = $_GET['currentPage'];
            $this->currentPage = filter_var($currentPage, FILTER_VALIDATE_INT) ?: 1;
        }
        $path = url()->previous();
        // $path = parse_url(url()->current(), PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $exists = in_array('bench-candidates', array_map('strtolower', $segments), true);
        $this->notVisiableBench = true;
        if ($exists) {
            $this->notVisiableBench = false;
        }

        // Intent: Defer R2 storage check to getResumeMeta API endpoint for fast initial drawer shell (Phase 4).
        $this->resumeAwsPath = ! empty($this->application->resume);

        $this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();

        // Pre-query variables for Blade view to eliminate embedded queries (P1-1)
        $effectiveJobId = $this->application->jobId ?? $this->application->job_id ?? $jobId;
        $this->jobData = ! empty($effectiveJobId) ? DB::table('jobs')->select('max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')->where('id', '=', $effectiveJobId)->first() : null;

        $clientComId = $this->application->ClientComID ?? 0;
        $this->hdrSpokperson = ($company_id != $clientComId && $clientComId > 0 && ! empty($effectiveJobId))
            ? DB::table('job_assign')->select('users.name')->leftJoin('users', 'users.id', '=', 'job_assign.spok_user_id')->where('job_assign.job_id', $effectiveJobId)->where('job_assign.company_id', $clientComId)->first()
            : null;

        $this->rcvCompany = ! empty($company_id) ? DB::table('companies')->select('logo', 'company_name')->where('id', $company_id)->first() : null;

        $submittedBy = $this->application->submitted_by ?? null;
        $this->submittedByCompany = ! empty($submittedBy) ? DB::table('companies')->select('companies.company_name')->join('users', 'users.company_id', '=', 'companies.id')->where('users.id', $submittedBy)->first() : null;

        // $view = view('service_company.job-applications.show', $this->data)->render();
        try {
            $view = view('service_company.job-applications.showdetail', $this->data)->render();
            $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);
            cache()->put($cacheKeySet, $responseData, now()->addMinutes(CANDIDATE_DETAILS_TIME));

            // Phase 0 Telemetry: Log candidate drawer rendering metrics
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $payloadBytes = strlen(json_encode($responseData));
            \Log::info('candidate_details_shell_rendered', [
                'company_id' => $company_id,
                'user_id' => $userId,
                'candidate_id' => $id,
                'duration_ms' => $durationMs,
                'payload_bytes' => $payloadBytes,
            ]);

            return $responseData;
        } catch (\Throwable $e) {
            \Log::error('Candidate details view rendering failed in show()', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return Reply::dataOnly(['status' => 'error', 'message' => 'Unable to render candidate details. Please try again.']);
        }

    }

    public function getTimeline($id, \Illuminate\Http\Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Intent: Verify candidate application belongs to user's company or authorized thirdparty company (FINDING-SEC-02)
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);
        if ($AddJobWiseCondidate) {
            $candCompanyId = (int) ($AddJobWiseCondidate->company_id ?? 0);
            $thirdPartyCompId = (int) ($AddJobWiseCondidate->thirdparty_company_id ?? 0);
            if ($companyId > 0 && $candCompanyId !== (int) $companyId && $thirdPartyCompId !== (int) $companyId && ! auth()->user()->hasRole('admin')) {
                return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized timeline access.']);
            }
            $application_id = $AddJobWiseCondidate->job_application_id ?? $AddJobWiseCondidate->id;
            $jobId = $AddJobWiseCondidate->job_id;
        } else {
            $application_id = $id;
            $jobId = $request->input('job_id', 0);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $from = ($page - 1) * $perPage;

        $mustConditions = [
            ['term' => ['application_id' => (int) $application_id]],
        ];

        if (! empty($jobId) && (int) $jobId > 0) {
            $mustConditions[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['job_id' => (int) $jobId]],
                        ['term' => ['job_id' => 0]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_CANDIDATE_TIMELINE,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $mustConditions,
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'desc']],
                ],
                'from' => $from,
                'size' => $perPage,
            ],
        ];

        try {
            $response = $this->elasticService->getClient()->search($params);
            $totalHits = $response['hits']['total']['value'] ?? 0;
            $updateTimeLine = empty($response['hits']['hits']) ? [] : array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        } catch (\Throwable $e) {
            \Log::warning('Elasticsearch timeline query exception', ['error' => $e->getMessage()]);
            $totalHits = 0;
            $updateTimeLine = [];
        }

        $lastPage = (int) max(1, ceil($totalHits / $perPage));

        // Scoped bulk loading for only the 15 timeline items on this page (P0/P1 optimization)
        $companyOrClientMap = [];
        $confidentialAllowedJobsMap = [];
        $offerJobApplicationsMap = [];
        $userCompanyMap = [];
        $spokpersonMap = [];
        $interviewFeedbackMap = [];
        $interviewSkillsFeedbackCountMap = [];

        if (! empty($updateTimeLine)) {
            $timelineItems = collect($updateTimeLine);

            $jobIds = $timelineItems->pluck('job_id')->filter()->unique()->values()->toArray();
            $eventUserIds = $timelineItems->pluck('event_by_user_id')->filter()->unique()->values()->toArray();
            $eventCompanyIds = $timelineItems->pluck('event_by_company_id')->filter()->unique()->values()->toArray();
            $interviewScheduleIds = $timelineItems->pluck('interview_schedules_id')->filter()->unique()->values()->toArray();

            if (! empty($jobIds)) {
                $rawCompanyClientRows = DB::table('jobs')
                    ->select('jobs.id')
                    ->whereIn('jobs.id', $jobIds)
                    ->get();

                foreach ($rawCompanyClientRows as $rRow) {
                    $companyOrClientMap[$rRow->id] = GeneralMethod::companyDetailsByClient($rRow->id);
                }
            }

            $chkAdminRole = auth()->user()->hasRole('admin');
            if ($chkAdminRole) {
                foreach ($jobIds as $jId) {
                    $confidentialAllowedJobsMap[$jId] = true;
                }
            } elseif (! empty($jobIds)) {
                $assignedJobIds = DB::table('job_assign')
                    ->whereIn('job_id', $jobIds)
                    ->where('user_id', auth()->user()->id)
                    ->pluck('job_id')
                    ->toArray();
                foreach ($jobIds as $jId) {
                    $confidentialAllowedJobsMap[$jId] = in_array($jId, $assignedJobIds);
                }
            }

            if (! empty($eventUserIds)) {
                $userCompanyMap = DB::table('users')
                    ->whereIn('id', $eventUserIds)
                    ->pluck('company_id', 'id')
                    ->toArray();
            }

            if (! empty($jobIds) && ! empty($eventCompanyIds)) {
                $spokespersons = DB::table('job_assign')
                    ->select('users.name', 'job_assign.company_id', 'job_assign.job_id')
                    ->leftJoin('users', 'users.id', '=', 'job_assign.spok_user_id')
                    ->whereIn('job_assign.job_id', $jobIds)
                    ->whereIn('job_assign.company_id', $eventCompanyIds)
                    ->get();
                foreach ($spokespersons as $sp) {
                    $spokpersonMap[$sp->job_id.'_'.$sp->company_id] = $sp;
                }
            }

            if (! empty($interviewScheduleIds)) {
                $feedbacks = DB::table('interview_feedback')
                    ->select('interview_feedback.created_at', 'interview_feedback.message', 'interview_feedback.star_ratings', 'interview_feedback.interview_id', 'interview_feedback.application_id', 'users.name', 'users.image')
                    ->join('users', 'users.id', '=', 'interview_feedback.update_by')
                    ->whereIn('interview_feedback.interview_id', $interviewScheduleIds)
                    ->get();
                foreach ($feedbacks as $fb) {
                    $interviewFeedbackMap[$fb->interview_id][] = $fb;
                }

                $counts = DB::table('interview_skills_feedback')
                    ->select('interview_id', DB::raw('COUNT(*) as total'))
                    ->whereIn('interview_id', $interviewScheduleIds)
                    ->groupBy('interview_id')
                    ->pluck('total', 'interview_id')
                    ->toArray();
                $interviewSkillsFeedbackCountMap = $counts;
            }
        }

        $this->updateTimeLine = $updateTimeLine;
        $this->companyOrClientMap = $companyOrClientMap;
        $this->confidentialAllowedJobsMap = $confidentialAllowedJobsMap;
        $this->offerJobApplicationsMap = $offerJobApplicationsMap;
        $this->userCompanyMap = $userCompanyMap;
        $this->spokpersonMap = $spokpersonMap;
        $this->interviewFeedbackMap = $interviewFeedbackMap;
        $this->CaptainCompanyid = auth()->user()->company_id;
        $view = view('service_company.job-applications.timeline_partial', $this->data)->render();

        //  dd($updateTimeLine);
        return Reply::dataOnly([
            'status' => 'success',
            'view' => $view,
            'timeline' => $updateTimeLine,
            'current_page' => $page,
            'last_page' => $lastPage,
            'total' => $totalHits,
        ]);
    }

    public function getResumeMeta($id, \Illuminate\Http\Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Intent: Security check — verify candidate application belongs to user's company or authorized thirdparty company (FINDING-SEC-02)
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);
        if ($AddJobWiseCondidate) {
            $candCompanyId = (int) ($AddJobWiseCondidate->company_id ?? 0);
            $thirdPartyCompId = (int) ($AddJobWiseCondidate->thirdparty_company_id ?? 0);
            if ($companyId > 0 && $candCompanyId !== (int) $companyId && $thirdPartyCompId !== (int) $companyId && ! auth()->user()->hasRole('admin')) {
                return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized resume access.']);
            }
            $appId = $AddJobWiseCondidate->id;
        } else {
            $appId = $id;
        }

        $app = JobApplication::withoutGlobalScopes()->find($appId);
        if (! $app) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Candidate application not found.']);
        }

        $resumeFilename = $app->hashname ?: $app->resume;
        if (empty($resumeFilename)) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'No resume uploaded for candidate.']);
        }

        $cacheKey = 'tenant:'.$companyId.':resume_meta:'.$appId;
        $meta = cache()->remember($cacheKey, now()->addMinutes(15), function () use ($resumeFilename) {
            $path = str_replace('https://file.jero.works/', '', $resumeFilename);
            $info = pathinfo($path);
            $extension = strtolower($info['extension'] ?? '');

            $exists = false;
            try {
                $exists = Storage::disk('r2')->exists($path);
            } catch (\Throwable $e) {
                \Log::warning('R2 storage check error', ['path' => $path, 'error' => $e->getMessage()]);
            }

            $signedUrl = '';
            if ($exists) {
                try {
                    $signedUrl = Storage::disk('r2')->temporaryUrl($path, now()->addMinutes(30));
                } catch (\Throwable $e) {
                    $signedUrl = url('/public/user-uploads/resumes/'.$resumeFilename);
                }
            } else {
                $signedUrl = url('/public/user-uploads/resumes/'.$resumeFilename);
            }

            return [
                'exists' => $exists,
                'signed_url' => $signedUrl,
                'filename' => $resumeFilename,
                'extension' => $extension,
            ];
        });

        return Reply::dataOnly([
            'status' => 'success',
            'resume' => $meta,
        ]);
    }

    public function websiteShow($id)
    {
        // $currentSlug = request()->segment(4);

        $company_id = '';
        $company_id = auth()->user()->company_id;
        // dd(auth()->user());
        $this->CaptainCompanyid = auth()->user()->company_id;
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $AddJobWiseCondidate = JobApplication::withoutGlobalScopes()->find($id);
        $jobId = @$AddJobWiseCondidate->job_id;
        $this->editComman = false;
        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = JobApplication::select(
            'job_applications.id',
            'add_job_wise_condidates.submitted_by',
            'job_applications.submitted_by as editAllow',
            'job_applications.company_id',
            'job_applications.status_type',
            'job_applications.np_id',
            'job_applications.serving_notice_period_mode',
            'job_applications.full_name',
            'job_applications.phone_code',
            'job_applications.second_phone_code',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.careersummary',
            'job_applications.nationality',
            'job_applications.wm',
            'job_applications.employmentType',
            'job_applications.oh',
            'job_applications.visastatu',
            'job_applications.referencename',
            'job_applications.secondary_mobile',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.rating',
            'job_applications.skype_id',
            'job_applications.cover_letter',
            'add_job_wise_condidates.job_id',
            'add_job_wise_condidates.thirdparty_company_id as ClientComID',
            'job_applications.status_id',
            'job_applications.is_viewed',
            'job_applications.column_priority',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.deleted_at',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.passport',
            'job_applications.current_location',
            'job_applications.current_designation',
            'job_applications.current_company',
            'job_applications.process_id',
            'job_applications.qualification',
            'add_job_wise_condidates.master_status_id',
            'add_job_wise_condidates.child_status_id',
            'job_applications.secondrary_email',
            'job_applications.secondary_mobile',
            'job_applications.skills',
            'job_applications.city',
            'job_applications.country',
            'job_applications.pincode',
            'job_applications.state',
            'job_applications.interviewDateOne',
            'job_applications.interviewDateTwo',
            'job_applications.interviewDateThree',
            'job_applications.interviewDateFour',
            'job_applications.interviewDateFive',
            'job_applications.interviewDateSix',
            'job_applications.linkedin',
            'job_applications.github',
            'job_applications.behance',
            'job_applications.otherLink',
            'job_applications.interview_mode_one',
            'job_applications.interview_mode_two',
            'job_applications.interview_mode_three',
            'job_applications.interview_mode_four',
            'job_applications.interview_mode_five',
            'job_applications.interview_mode_six',
            'job_applications.current_ctc_cur',
            'job_applications.ctc',
            'job_applications.current_ctc_mode',
            'job_applications.current_ectc_cur',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.ectc',
            'job_applications.current_ectc_mode',
            'job_applications.percentage_hike',
            'job_applications.comment',
            'job_applications.dob',
            'job_applications.created_at',
            'job_applications.company_id as other_company',
            'job_applications.condidate_type',
            'clients.name as client_name',
            'jobs.title',
            'users.name as posted_by',
            'users.image as posted_by_image',
            // 'notice_periods.np_title',
            'master_status.name as master_status_name',
            'offer_job_applications.proposed_ctc',
            'offer_job_applications.any_offer',
            'offer_job_applications.document',
            'offer_job_applications.sub_for_approval',
            'offer_job_applications.offer_approved',
            'offer_job_applications.offer_release',
            'offer_job_applications.offer_acceptance',
            'offer_job_applications.joined',
            'offer_job_applications.joined_date',
            'offer_job_applications.offer_currency',
            'offer_job_applications.offer_mode',
            'offer_job_applications.offer_amount',
            'offer_job_applications.confidential',
            'qualification_master.name as qualification_name',
            'companies.company_name',
            'owncom.company_name as own_company_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'recommendation_relation.job_id as rjobid',
            'recommendation_relation.from_company_id',
            'add_job_wise_condidates.id as wiseId',
            'documents.hashname'
        )
            ->leftJoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftJoin('master_status', function ($join) {
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
                $join->on('companies.id', 'job_applications.company_id');
            })
            ->leftJoin('companies as owncom', function ($join) {
                $join->on('owncom.id', 'job_applications.company_id');
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
            ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');

        $this->application = $this->application->where('job_applications.id', $id);

        // $this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
        $this->application = $this->application->orderByDesc('documents.id')->first();

        //  dd($this->application);

        $this->client_contuser_arr = DB::table('client_contact_info_jobs')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->join('clients', 'clients.id', '=', 'client_contact_info.client_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')->where('client_contact_info_jobs.job_id', @$jobId)->get();

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            // ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', @$jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_additional_detail.application_id', '=', @$this->application->id)
            ->where('application_additional_detail.job_id', '=', @$jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        $this->bench_additional_detail = DB::table('bench_additional_detail')
            ->select('*')
            ->where('bench_additional_detail.bench_id', '=', $this->application->id)
            ->orderBy('bench_additional_detail.id', 'ASC')
            ->get();

        // echo '<pre>'; print_r($this->application); exit;
        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $jobId)->where('job_application_id', $this->application->id)->get();

        $this->notesArr = DB::table('applicant_notes')
            ->select(
                'applicant_notes.id',
                'applicant_notes.note_text',
                'applicant_notes.created_at',
                'applicant_notes.updated_at',
                'users.name',
                'users.image as posted_by_image'
            )
            ->join('job_applications', 'job_applications.id', 'applicant_notes.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            // ->where('company_id', user()->company_id)
            ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('add_job_wise_condidates.id', $id)
            ->where('applicant_notes.job_application_id', $this->application->id)
            ->where('applicant_notes.user_id', auth()->user()->id)
            ->orderBy('id', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at', 'interview_schedules.available_status', 'interview_schedules.reschedule', 'interview_schedules.feedback')
            ->join('job_applications', 'job_applications.id', 'interview_schedules.job_application_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            // ->where('interview_schedules.company_id', user()->company_id)
            // ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('add_job_wise_condidates.id', $id)
            ->where('interview_schedules.job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        // dd($this->schedule);

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $jobId;
        $this->job_id = $job_id;
        $this->notVisiableBench = false;

        // $this->jobSkill = DB::table('job_metronics_skills')
        // ->leftJoin('candidate_job_skill_exp','candidate_job_skill_exp.job_skill_id','=','job_metronics_skills.id')
        // ->select('job_metronics_skills.*','candidate_job_skill_exp.skill_value')
        // ->where('candidate_job_skill_exp.job_id','=', $this->job_id)
        // ->where('candidate_job_skill_exp.candidate_id','=', $this->application->id)
        // ->get();
        $job_details = Job::withoutGlobalScopes()->find($this->job_id);

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $this->job_id)
            ->where('job_metronics_skills.company_id', '=', $job_details->company_id)
            ->get();
        // dd($job_details->company_id);

        $this->skillShow = true;
        if (! DB::table('candidate_job_skill_exp')->where('candidate_job_skill_exp.job_id', '=', $this->job_id)->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id)->exists()) {
            $this->skillShow = false;
        }

        $params = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'body' => [
                '_source' => ['schedule_date'],
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['interview_job_wise_con_id' => (int) $id]],
                            ['term' => ['job_application_id' => (int) $this->application->id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['id' => ['order' => 'asc']],
                ],
                'size' => 10000, // optional: get more records
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $this->scheduleDateArr = collect($response['hits']['hits'] ?? [])->pluck('_source.schedule_date')->filter()->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        })
            ->toArray();

        $this->timeline = DB::table('timeline');
        $this->timeline = $this->timeline->select(
            'timeline.id',
            'timeline.owner_id',
            'timeline.application_id',
            'timeline.application_email',
            'timeline.job_id',
            'timeline.process_id',
            'timeline.status_name',
            'timeline.process_child_name',
            'timeline.note',
            'timeline.visible',
            'timeline.insert_type',
            'timeline.create_at',
            'timeline.interview_schedule_date',
            'timeline.interview_from_time',
            'timeline.interview_to_time',
            'timeline.interview_mode',
            'timeline.interview_mention_link',
            'timeline.interviewers',
            'timeline.interview_additional_email',
            'timeline.interview_message',
            'jobs.id as job_id',
            'jobs.title',
            'master_status.name as master_status_name',
            'clients.name as client_name',
            'timeline.timeline_type',
            'timeline.prev_process_name',
            'timeline.prev_process_child_name',
            'timeline.interview_mode_child_level',
            'timeline.average_star_rating',
            'timeline.interview_schedules_id',
            'master_status.css_style',
            'users.name',
            'users.image'
        );
        $this->timeline = $this->timeline->join('job_applications', 'job_applications.id', '=', 'timeline.application_id');
        $this->timeline = $this->timeline->leftJoin('users', 'users.id', '=', 'timeline.owner_id');
        $this->timeline = $this->timeline->leftJoin('jobs', 'jobs.id', '=', 'timeline.job_id');
        $this->timeline = $this->timeline->leftJoin('clients', 'clients.id', '=', 'jobs.client_id');
        $this->timeline = $this->timeline->leftJoin('master_status', 'master_status.id', '=', 'timeline.process_id');
        $this->timeline = $this->timeline->where('timeline.application_id', '=', $application_id);
        $this->timeline = $this->timeline->where('timeline.job_id', '=', $job_id);
        $this->timeline = $this->timeline->orderBy('timeline.id', 'DESC');
        $this->timeline = $this->timeline->get();

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        // UPDATE TIMELINE
        $this->updateTimeLine = DB::table('updatetimeline');
        $this->updateTimeLine = $this->updateTimeLine->select(
            'updatetimeline.id',
            'updatetimeline.event_id',
            'updatetimeline.event_by_company_id',
            'updatetimeline.event_by_user_id',
            'updatetimeline.master_process_name',
            'updatetimeline.child_process_name',
            'updatetimeline.application_id',
            'updatetimeline.application_email',
            'updatetimeline.job_id',
            'updatetimeline.note',
            'updatetimeline.create_at',
            'updatetimeline.update_at',
            'updatetimeline.prev_process_name',
            'updatetimeline.comment',
            'updatetimeline.prev_process_child_name',
            'updatetimeline.interview_schedule_date',
            'updatetimeline.interview_mode',
            'updatetimeline.interview_mention_link',
            'updatetimeline.interview_mode_child_level',
            'updatetimeline.interviewers',
            'updatetimeline.average_star_rating',
            'updatetimeline.interview_message',
            'updatetimeline.interview_schedules_id',
            'updatetimeline.offer_currency',
            'updatetimeline.offer_mode',
            'updatetimeline.offer_amount',
            'updatetimeline.joined_date',
            'updatetimeline.candidate_status',
            'updatetimeline.management_status',
            'updatetimeline.accept_date_time',
            'updatetimeline.approved_date_time',
            'updatetimeline.send_approved_date_time',
            'updatetimeline.send_accept_date_time',
            'updatetimeline.email_id_hr',
            'updatetimeline.name_hr',
            'updatetimeline.yearly_gross',
            'updatetimeline.yearly_final_gross',
            'updatetimeline.mtypeSection',
            'updatetimeline.designation_offer',
            'updatetimeline.timezone_abbreviation',
            'updatetimeline.confidential',
            'updatetimeline.interview_to_time',
            'application_event.event_name',
            'jobs.id as job_id',
            'jobs.title',
            'jobs.employment_type',
            'users.name',
            'users.image',
            'clients.name as client_name',
            'interview_schedules.available_status',
            'interview_schedules.reschedule',
            'interview_schedules.feedback',
            'interview_schedules.stage',
            'master_status.name as status_name',
            'companies.company_name as cur_companyname',
            'master_status.css_style'
        );
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
        $this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('interview_schedules', 'interview_schedules.id', 'updatetimeline.interview_schedules_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('master_status', 'master_status.id', 'interview_schedules.stage');
        $this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
        $this->updateTimeLine = $this->updateTimeLine->leftjoin('companies', 'companies.id', 'users.company_id');
        $this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
        $this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');

        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $application_id);
        // $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.event_by_company_id', '=', auth()->user()->company_id);
        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$jobId);
        // $this->updateTimeLine = $this->updateTimeLine->orWhere(function ($query) use ($company_id, $id) {
        // 	return
        // 		$query->where('job_applications.thirdparty_company_id', '=', $company_id)
        // 			->where('job_applications.id', '=', $id);
        // 	//->where('staffing_company_relation.final_status', '=', 1);
        // });
        $this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
        $this->updateTimeLine = $this->updateTimeLine->get();

        // dd(	$this->updateTimeLine);

        $sel_app_id = $id;
        $sel_job_id = $job_id;
        $this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
        if ($this->benchStatusCnt > 0) {
            $benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

            $source_from_company_id = @$benchAssignRow->source_from_company_id;
            $source_from_user_id = @$benchAssignRow->source_from_user_id;
            $source_from_id = @$benchAssignRow->source_from_id;
            $source_from_type = @$benchAssignRow->source_from_type;
            $source_company_id = @$benchAssignRow->source_company_id;

            $source_user_id = @$benchAssignRow->source_user_id;
            $source_application_id = @$benchAssignRow->source_application_id;
            $source_jobid = @$benchAssignRow->source_jobid;

            // dd($this->userPermissions);

            if ($company_id != $this->application->other_company && $source_from_type == 2) {
                $this->application->phone = '2xxx-xxx-xxxx';
                $this->application->secondary_mobile = '2xxx-xxx-xxxx';
                $this->application->email = '2xxx-xxx-xxxx';
                $this->application->secondrary_email = '2xxx-xxx-xxxx';
            }

            // if ($source_from_type == 2) {

            // }

            $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users_profile.designation')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
            $this->bench_additional_detail = DB::table('bench_additional_detail')
                ->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
                ->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

        }

        if (! in_array('salary_view', $this->userPermissions)) {
            $this->application->ctc = 'N/A';
            $this->application->ectc = 'N/A';
            $this->application->pay_rate_amount = 'N/A';
            $this->application->bill_rate_amount = 'N/A';
            $this->application->percentage_hike = 'N/A';
        }

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;
        $this->additional_skills_arr = DB::table('skills')->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
            // ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('skills.name', 'candidate_skills_value.skill_value')->where('candidate_skills_value.job_id', $jobId)->where('candidate_skills_value.candidate_id', $this->application->id)->where('skills.company_id', $this->user->company_id)->get();
        $this->application_add_detail_cnt = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->count();
        $this->application_add_detail = DB::table('application_additional_detail')->select('*')->where('application_id', $this->application->id)->where('job_id', $jobId)->orderBy('application_additional_detail.id', 'ASC')->get();
        $this->offer_job_applications_count = DB::table('offer_job_applications')->select('offer_job_applications.id')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
            // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)->orderBy('offer_job_applications.id', 'DESC')->count();
        $this->offer_job_applications = DB::table('offer_job_applications')->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
            // ->leftjoin('countries', 'countries.id', 'job_applications.master_status_id')
            ->where('offer_job_applications.email', '=', $this->application->email)->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
            // ->where('job_id', $this->application->job_id)
            ->orderBy('offer_job_applications.id', 'DESC')->get();
        // echo '<pre>'; print_r($this->offer_job_applications); exit;

        $resume = '';
        $resumeData = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', '=', 'Resume')->first();
        $this->resume = @$resumeData->hashname;

        $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

        $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

        $this->jobWiseCanId = $id;
        $this->editprofile = false;
        $this->skillAndQuestions = true;
        $this->currentSlug = '';
        if (isset($_GET['slug'])) {
            $currentSlug = $_GET['slug'];

            // dd($currentSlug);
            if ($currentSlug === 'apptracker') {
                $this->skillAndQuestions = true;
            }
            if ($currentSlug === 'apptracker' || $currentSlug === 'cvtracker' || $currentSlug === 'interview-schedule') {
                $this->editprofile = true;
                $this->currentSlug = $_GET['slug'];
            }
        }

        $this->currentPage = 1;
        if (isset($_GET['currentPage'])) {
            $currentPage = $_GET['currentPage'];
            $this->currentPage = $currentPage;
        }

        if ($AddJobWiseCondidate) {
            $params = [
                'index' => ELASTIC_CAREER_APPLY,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];

            // dd($data);
            $this->application->photo = $data->photo;
            $this->application->country = $data->country;
            $this->application->state = $data->state;
            $this->application->state_name = $data->state_name;
            $this->application->city_name = $data->city_name;
            $this->application->city = $data->city;
            $this->application->condidate_type = $data->condidate_type;
            $this->application->current_ctc_cur = $data->current_ctc_cur;
            $this->application->current_ectc_cur = $data->current_ectc_cur;
            $this->application->bill_rate_currency = $data->bill_rate_currency;
            $this->application->bill_rate_currency = $data->bill_rate_currency;
            $this->application->master_status_id = $data->master_status_id;
            $this->application->rjobid = $data->rjobid;
            $this->application->from_company_id = $data->from_company_id;
            $this->application->wiseId = $data->id;
            $this->application->company_id = $data->company_id;
            $this->application->submitted_by = $data->submitted_by;
            $this->application->other_company = $data->other_company;
            $this->application->phone = $data->phone;
            $this->application->full_name = $data->full_name;
            $this->application->current_designation = $data->current_designation;
            $this->application->total_exp = $data->total_exp;
            $this->application->current_company = $data->current_company;
            $this->application->skills = $data->skills;
            $this->application->ClientComID = $data->ClientComID;
            $this->application->job_id = $data->jobId;
            $this->application->posted_by = $data->posted_by;
            $this->application->posted_by_image = $data->posted_by_image;
            $this->application->np_id = $data->np_id;
            $this->application->serving_notice_period_mode = $data->serving_notice_period_mode;
            $this->application->last_working_date = $data->last_working_date ?? ($this->application->last_working_date ?? null);
            $this->application->rel_exp = $data->rel_exp;
            $this->application->qualification_name = $data->qualification_name;
            $this->application->dob = $data->dob;
            $this->application->ctc = $data->ctc;
            $this->application->current_ctc_mode = $data->current_ctc_mode;
            $this->application->pay_rate_amount = $data->pay_rate_amount;
            $this->application->pay_rate_mode = $data->pay_rate_mode;
            $this->application->ectc = $data->ectc;
            $this->application->bill_rate_amount = $data->bill_rate_amount;
            $this->application->bill_rate_mode = $data->bill_rate_mode;
            $this->application->percentage_hike = $data->percentage_hike;
            $this->application->comment = $data->comment;
            $this->application->secondary_mobile = $data->secondary_mobile;
            $this->application->email = $data->email;
            $this->application->secondrary_email = $data->secondrary_email;
            $this->application->master_status_name = $data->name;
            $this->application->offer_currency = $data->of_offer_currency;
            $this->application->offer_mode = $data->of_offer_mode;
            $this->application->offer_amount = $data->of_offer_amount;
            $this->application->joined_date = $data->of_joined_date;
            $this->application->hashname = ($data->hashname) ? $data->hashname : $data->resume;
            $this->application->phone_code = $data->phone_code;
            $this->application->second_phone_code = $data->second_phone_code;
            $this->application->id = $data->candidate_id;
            $this->application->updated_at = $data->updated_at;
            $this->application->created_at = $data->created_at;
            $this->application->interview_mode_one = @$data->interview_mode_one;
            $this->application->interviewDateOne = @$data->interviewDateOne;
            $this->application->interviewDateTwo = @$data->interviewDateTwo;
            $this->application->interview_mode_two = @$data->interview_mode_two;
            $this->application->interviewDateThree = @$data->interviewDateThree;
            $this->application->interview_mode_three = @$data->interview_mode_three;
            $this->application->interviewDateFour = @$data->interviewDateFour;
            $this->application->interview_mode_four = @$data->interview_mode_four;
            $this->application->interviewDateFive = @$data->interviewDateFive;
            $this->application->interview_mode_five = @$data->interview_mode_five;
            $this->application->interviewDateSix = @$data->interviewDateSix;
            $this->application->interview_mode_six = @$data->interview_mode_six;
            $this->application->github = @$data->github;
            $this->application->otherLink = @$data->otherLink;
            $this->application->behance = @$data->behance;
            $this->application->linkedin = @$data->linkedin;

        }

        $this->resumeAwsPath = false;
        if (! empty($this->application->resume)) {
            $info = pathinfo($this->application->resume);
            $path = str_replace('https://file.jero.works/', '', $this->application->resume);
            $s3Path = 'Resumes/'.$info['basename'];

            if (Storage::disk('r2')->exists($path)) {
                $this->resumeAwsPath = true;
            }
        }

        $this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();

        // Populate job skills for skill-highlight panel — mirrors show() but uses local $jobId
        $this->jobSkill = collect();
        $_wsJobId = $jobId ?? @$this->application->job_id;
        if (! empty($_wsJobId)) {
            $this->jobSkill = DB::table('job_metronics_skills')
                ->leftJoin('candidate_job_skill_exp', function ($join) {
                    $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                        ->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id);
                })
                ->select('job_metronics_skills.id as job_skill_id', 'job_metronics_skills.id as id', 'job_metronics_skills.skill_name', 'candidate_job_skill_exp.skill_value', 'candidate_job_skill_exp.rating')
                ->where('job_metronics_skills.job_id', '=', $_wsJobId)
                ->get();
        }

        // $view = view('service_company.job-applications.show', $this->data)->render();
        // dd("jd");
        $view = view('service_company.job-applications.showdetail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function edit($id)
    {
        // dd("kdf");
        $this->pageTitle = __('Edit Candidate');
        abort_if(Gate::denies('edit_job_applications'), 403);
        $id = GeneralMethod::value_decrypt($id);
        $company_id = '';
        $company_id = auth()->user()->company_id;

        // Authorization: only the assigned Captain (job_assign.spok_user_id) or the submitting user
        // (add_job_wise_condidates.submitted_by), within the same company, may open this edit form.
        // Here $id is the add_job_wise_condidates.id.
        $authUserId = auth()->id();
        $canEditCandidate = DB::table('add_job_wise_condidates as ajwc')
            ->leftJoin('job_assign as ja', function ($join) {
                $join->on('ja.job_id', '=', 'ajwc.job_id')
                    ->on('ja.company_id', '=', 'ajwc.company_id');
            })
            ->where('ajwc.id', $id)
            // ->where('ajwc.company_id', $company_id)
            ->where(function ($query) use ($authUserId) {
                $query->where('ja.spok_user_id', $authUserId)
                    ->orWhere('ajwc.submitted_by', $authUserId);
            })
            ->exists();

        if (! $canEditCandidate) {
            return redirect()->back()->with('candidate_edit_denied', "You are not authorized to edit this candidate's profile. Only the assigned Captain or the submitting user can edit it");
        }

        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);
        if (! $AddJobWiseCondidate) {
            abort(404);
        }
        $this->AddJobWiseCondidateRankRow = $id;
        // dd($AddJobWiseCondidate->job_id);
        $this->statuses = ReferenceDataCache::applicationStatuses();
        $this->application = JobApplication::withoutGlobalScopes()->find($AddJobWiseCondidate->job_application_id);
        if (! $this->application) {
            abort(404);
        }
        $this->jobQuestion = $this->application->job?->questions ?? collect();
        $this->jobs = Job::select('id', 'title', 'location_id', 'status', 'start_date', 'end_date', 'section_visibility')->where('company_id', $this->user->company_id)->get();
        $this->noticeperiod = \Illuminate\Support\Facades\Cache::remember('notice_periods', 86400, fn () => \App\Models\NoticePeriod::orderBy('np_id')->get());
        $this->qualification = ReferenceDataCache::qualifications();
        $this->interviewStatus = ReferenceDataCache::masterStatuses();
        $this->childArr = DB::table('candidate_status')
            ->select('*')->where('parent', $this->application->master_status_id)
            ->where('company_id', $this->user->company_id)
            ->orderBy('priority', 'ASC')
            ->get();

        $this->company_id = $this->user->company_id;
        $this->jobId = $AddJobWiseCondidate->job_id;
        $this->appId = $this->application->id;

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_id', '=', $this->application->id)
            ->where('job_id', '=', $this->jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();
        $this->application_add_detail = $this->application_additional_detail;
        $this->application_add_detail_cnt = $this->application_additional_detail->count();

        $this->jobDetail = Job::withoutGlobalScopes()
            ->select('jobs.id', 'jobs.company_id', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency')
            ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $this->jobId)
            ->first();

        // $this->jobSkill = DB::table('job_metronics_skills')
        // 	->leftJoin('candidate_job_skill_exp', function($join) {
        // 		$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
        // 			->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
        // 	})
        // 	->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
        // 	->where('job_metronics_skills.job_id', '=', $this->jobId)
        // 	->get();

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id);
            })
            ->select(
                DB::raw('MIN(job_metronics_skills.id) as job_skill_id'), // alias as job_skill_id for blade form name keys
                DB::raw('MIN(job_metronics_skills.id) as id'),
                'job_metronics_skills.skill_name',
                DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
                DB::raw('MAX(candidate_job_skill_exp.rating) as rating')
            )
            ->where('job_metronics_skills.job_id', $this->jobId)
            ->whereNotNull('job_metronics_skills.skill_name')
            ->whereRaw("TRIM(job_metronics_skills.skill_name) != ''")
            ->groupBy('job_metronics_skills.skill_name')
            ->get();

        $this->skillShow = true;

        $this->page_event = 'EDIT';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->countries = ReferenceDataCache::masterCountries();
        $this->interview_mode = ReferenceDataCache::interviewModes();
        $this->statelist = GeneralMethod::statelist($this->application->country);
        $this->documents = DB::table('documents')->select('*')->where('documentable_id', $this->application->id)->where('name', 'Resume')->orderByDesc('id')->first();

        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            // ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();
        // dd($this->application_additional_detail);

        $this->cityListArr = '';
        if ($this->application->country == 101) {
            $statelist = DB::table('state_list')
                ->select('state_list.id', 'state_list.state as name', DB::raw('101 as country_id'))
                ->orderBy('state_list.state')
                ->get()->toArray();
            $this->statelist = json_decode(json_encode($statelist), true);

            $citylist = DB::table('city_list')
                ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->join('countries', 'countries.id', '=', 'state_list.country_id')
                ->select('city_list.city_id as id', 'city_list.city as name', 'state_list.state', 'countries.country_name', 'countries.country_code')
                ->where('state_list.country_id', $this->application->country)
                ->orderBy('city_list.city')
                ->get()->toArray();

            // dd($citylist);

            $this->cityListArr = json_decode(json_encode($citylist), true);

        }
        $this->cities = GeneralMethod::allCities();

        if ($AddJobWiseCondidate) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];

            // dd($data);
            $this->application->photo = $data->photo;
            $this->application->country = $data->country;
            $this->application->state = $data->state;
            $this->application->state_name = $data->state_name;
            $this->application->city_name = $data->city_name;
            $this->application->city = $data->city;
            $this->application->pincode = $data->pincode;
            $this->application->condidate_type = $data->condidate_type;
            $this->application->current_ctc_cur = $data->current_ctc_cur;
            $this->application->current_ectc_cur = $data->current_ectc_cur;
            $this->application->bill_rate_currency = $data->bill_rate_currency;
            $this->application->bill_rate_currency = $data->bill_rate_currency;
            $this->application->master_status_id = $data->master_status_id;
            $this->application->rjobid = $data->rjobid;
            $this->application->from_company_id = $data->from_company_id;
            $this->application->wiseId = $data->id;
            $this->application->company_id = $data->company_id;
            $this->application->submitted_by = $data->submitted_by;
            $this->application->other_company = $data->other_company;
            $this->application->phone = $data->phone;
            $this->application->full_name = $data->full_name;
            $this->application->current_designation = $data->current_designation;
            $this->application->total_exp = $data->total_exp;
            $this->application->current_company = $data->current_company;
            $this->application->skills = $data->skills;
            $this->application->ClientComID = $data->ClientComID;
            $this->application->job_id = $data->jobId;
            $this->application->posted_by = $data->posted_by;
            $this->application->posted_by_image = $data->posted_by_image;
            $this->application->np_id = $data->np_id;
            $this->application->serving_notice_period_mode = $data->serving_notice_period_mode;
            $this->application->last_working_date = $data->last_working_date ?? ($this->application->last_working_date ?? null);
            $this->application->rel_exp = $data->rel_exp;
            $this->application->qualification_name = $data->qualification_name;
            $this->application->dob = $data->dob;
            $this->application->ctc = $data->ctc;
            $this->application->current_ctc_mode = $data->current_ctc_mode;
            $this->application->pay_rate_amount = $data->pay_rate_amount;
            $this->application->pay_rate_mode = $data->pay_rate_mode;
            $this->application->ectc = $data->ectc;
            $this->application->bill_rate_amount = $data->bill_rate_amount;
            $this->application->bill_rate_mode = $data->bill_rate_mode;
            $this->application->percentage_hike = $data->percentage_hike;
            $this->application->comment = $data->comment;
            $this->application->recruiter_note = $data->recruiter_note ?? ($this->application->recruiter_note ?? '');
            $this->application->secondary_mobile = $data->secondary_mobile;
            $this->application->email = $data->email;
            $this->application->secondrary_email = $data->secondrary_email;
            $this->application->master_status_name = $data->name;
            $this->application->offer_currency = $data->of_offer_currency;
            $this->application->offer_mode = $data->of_offer_mode;
            $this->application->offer_amount = $data->of_offer_amount;
            $this->application->joined_date = $data->of_joined_date;
            $this->application->hashname = $data->hashname;
            $this->application->phone_code = $data->phone_code;
            $this->application->second_phone_code = $data->second_phone_code;
            $this->application->id = $data->candidate_id;
            $this->application->city_id = $data->city_id;
            $this->application->qualification = $data->qualification;
            $this->application->interview_mode_one = @$data->interview_mode_one;
            $this->application->interviewDateOne = @$data->interviewDateOne;
            $this->application->interviewDateTwo = @$data->interviewDateTwo;
            $this->application->interview_mode_two = @$data->interview_mode_two;
            $this->application->interviewDateThree = @$data->interviewDateThree;
            $this->application->interview_mode_three = @$data->interview_mode_three;
            $this->application->interviewDateFour = @$data->interviewDateFour;
            $this->application->interview_mode_four = @$data->interview_mode_four;
            $this->application->interviewDateFive = @$data->interviewDateFive;
            $this->application->interview_mode_five = @$data->interview_mode_five;
            $this->application->interviewDateSix = @$data->interviewDateSix;
            $this->application->interview_mode_six = @$data->interview_mode_six;
            $this->application->github = @$data->github;
            $this->application->otherLink = @$data->otherLink;
            $this->application->behance = @$data->behance;
            $this->application->linkedin = @$data->linkedin;
        }
        $this->resumeAwsPath = false;
        if (! empty($this->application->resume)) {
            $info = pathinfo($this->application->resume);
            $path = str_replace('https://file.jero.works/', '', $this->application->resume);
            $s3Path = 'Resumes/'.$info['basename'];

            if (Storage::disk('r2')->exists($path)) {
                $this->resumeAwsPath = true;
            }
        }

        $this->currentPage = 1;
        if (isset($_GET['currentPage'])) {
            $currentPage = GeneralMethod::value_decrypt($_GET['currentPage']);
            $this->currentPage = $currentPage;
        }
        $this->currentSlug = 1;
        if (isset($_GET['currentSlug'])) {
            $currentSlug = GeneralMethod::value_decrypt($_GET['currentSlug']);
            $this->currentSlug = $currentSlug;
        }
        $this->default_loc = GeneralMethod::allCitiesSelected([@$this->application->city_id]);
        $this->currencyConverter = GeneralMethod::currencyConverter();
        $this->recruiterNoteTemplates = DB::table('recruiter_note_master')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('company_id')->orWhere('company_id', auth()->user()->company_id);
            })
            ->orderBy('id')->get();

        return response()->view('service_company.job-applications.create', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function createOffer($id)
    {

        // dd($id);
        $company_id = '';
        $company_id = auth()->user()->company_id;
        // dd(auth()->user());
        $OfferJobApplication = OfferJobApplication::withoutGlobalScopes()->find($id);
        // dd($OfferJobApplication);
        $this->offer_id = ($id) ? $id : '';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($OfferJobApplication->joined);
        $id = $OfferJobApplication->joined;
        $jobId = @$AddJobWiseCondidate->job_id;
        // dd($id,$jobId,$AddJobWiseCondidate);
        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = JobApplication::withoutGlobalScopes()->select(
            'job_applications.id',
            'add_job_wise_condidates.submitted_by',
            'job_applications.submitted_by as editAllow',
            'add_job_wise_condidates.company_id',
            'job_applications.np_id',
            'job_applications.serving_notice_period_mode',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.secondary_mobile',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.rating',
            'job_applications.skype_id',
            'job_applications.cover_letter',
            'add_job_wise_condidates.job_id',
            'job_applications.status_id',
            'job_applications.is_viewed',
            'job_applications.column_priority',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.deleted_at',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.passport',
            'job_applications.current_location',
            'job_applications.current_designation',
            'job_applications.current_company',
            'job_applications.process_id',
            'job_applications.qualification',
            'add_job_wise_condidates.master_status_id',
            'add_job_wise_condidates.child_status_id',
            'job_applications.secondrary_email',
            'job_applications.secondary_mobile',
            'job_applications.skills',
            'job_applications.city',
            'job_applications.country',
            'job_applications.pincode',
            'job_applications.state',
            'job_applications.current_ctc_cur',
            'job_applications.ctc',
            'job_applications.current_ctc_mode',
            'job_applications.current_ectc_cur',
            'job_applications.bill_rate_currency',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.pay_rate_currency',
            'job_applications.pay_rate_amount',
            'job_applications.pay_rate_mode',
            'job_applications.ectc',
            'job_applications.current_ectc_mode',
            'job_applications.percentage_hike',
            'job_applications.comment',
            'job_applications.dob',
            'job_applications.created_at',
            'job_applications.company_id as other_company',
            'job_applications.condidate_type',
            'clients.name as client_name',
            'jobs.title',
            'users.name as posted_by',
            'users.image as posted_by_image',
            // 'notice_periods.np_title',
            'master_status.name as master_status_name',
            'offer_job_applications.proposed_ctc',
            'offer_job_applications.any_offer',
            'offer_job_applications.document',
            'offer_job_applications.sub_for_approval',
            'offer_job_applications.offer_approved',
            'offer_job_applications.offer_release',
            'offer_job_applications.offer_acceptance',
            'offer_job_applications.joined',
            'offer_job_applications.joined_date',
            'offer_job_applications.offer_amount',
            'qualification_master.name as qualification_name',
            'companies.company_name',
            'owncom.company_name as own_company_name',
            'state_list.state as state_name',
            'city_list.city as city_name',
            'recommendation_relation.job_id as rjobid',
            'recommendation_relation.from_company_id',
            'add_job_wise_condidates.id as wiseId',
            'documents.hashname'
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
                $join->on('owncom.id', 'job_applications.company_id');
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
            ->leftjoin('documents', 'documents.documentable_id', 'job_applications.id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        if ($AddJobWiseCondidate) {
            $this->application = $this->application->where('add_job_wise_condidates.id', $id);
        } else {
            $this->application = $this->application->where('job_applications.id', $id);
        }

        $this->section_change_value = OfferJobApplication::join('offer_create_main_tds', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')->select('offer_create_main_tds.*', 'offer_job_applications.id as offer_id_main')->where('offer_job_applications.id', $this->offer_id)->where('offer_create_main_tds.status', '=', 1)->first();
        $this->section_other = '';
        $this->section_hourly = '';
        $this->first_text = 'Annually';
        $this->secont_text = 'Monthly';
        if ($this->section_change_value) {
            if ($this->section_change_value->salary_mode == 2 || $this->section_change_value->salary_mode == 1 || $this->section_change_value->salary_mode == 3) {
                $this->section_hourly = '';
                $this->section_other = 'd-none';
                if ($this->section_change_value->salary_mode == 1) {
                    $this->secont_text = 'Monthly';
                } elseif ($this->section_change_value->salary_mode == 2) {
                    $this->secont_text = 'Hourly';
                } elseif ($this->section_change_value->salary_mode == 3) {
                    $this->secont_text = 'Annually';
                }
            } else {
                $this->section_other = '';
                $this->section_hourly = 'd-none';

                if ($this->section_change_value->salary_mode == 4) {
                    $this->first_text = 'Annually';
                } elseif ($this->section_change_value->salary_mode == 0) {
                    $this->first_text = 'Annually';
                }
            }
            $offerPermision = 65;
        } else {
            $this->section_other = '';
            $this->section_hourly = 'd-none';
            $offerPermision = 66;
        }
        // dd($this->text_change_value);

        $offerDetails = DB::table('offer_create_main_tds')
            ->join('offer_job_applications', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')
            ->where('offer_job_applications.id', $this->offer_id)
            ->where('offer_create_main_tds.status', '=', 1)
            ->select(
                'offer_create_main_tds.offer_id',
                'offer_create_main_tds.employment_type',
                'offer_create_main_tds.id as of_main_td',
                'offer_job_applications.offer_currency as currency',
                'offer_job_applications.offer_amount as salary_ctc',
                'offer_job_applications.offer_mode as salary_mode',
                'offer_create_main_tds.designation',
                // The Compensation step's Joining Date field saves to offer_create_main_tds.joined_date
                // (not offer_job_applications.joined_date, which nothing keeps in sync) — reading from
                // the wrong table meant a joining date set on Compensation could show blank everywhere
                // this feeds (hero facts, approval info card, email body).
                'offer_create_main_tds.joined_date',
                'offer_job_applications.confidential',
                'offer_create_main_tds.monthly_gross',
                'offer_create_main_tds.yearly_gross',
                'offer_create_main_tds.monthly_bounus_gross',
                'offer_create_main_tds.yearly_bonus_gross',
                'offer_create_main_tds.monthly_insurence_gross',
                'offer_create_main_tds.yearly_insurence_gross',
                'offer_create_main_tds.yearly_final_gross',
                'offer_create_main_tds.monthly_final_gross'
            )->get()
            ->map(function ($offer) {
                // Fetch Basic offer points
                $basicPoints = DB::table('basic_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select(
                        'id',
                        'view_type',
                        'poins as basic_poins',
                        'Percentage as basic_percentage',
                        'condition_s as basic_condition_s',
                        'monthly as basic_monthly',
                        'yearly as basic_yearly'
                    )->get()->toArray();

                // Fetch Bonus offer points
                $bonusPoints = DB::table('bonus_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select(
                        'id',
                        'view_type',
                        'poins as bonus_poins',
                        'Percentage as bonus_percentage',
                        'condition_s as bonus_condition_s',
                        'monthly as bonus_monthly',
                        'yearly as bonus_yearly'
                    )->get()->toArray();

                // Fetch Insurance offer points
                $insurancePoints = DB::table('insurence_offer_points')
                    ->where('offer_create_main_td_id', $offer->of_main_td)
                    ->select(
                        'id',
                        'view_type',
                        'poins as insurence_poins',
                        'Percentage as insurence_percentage',
                        'condition_s as insurence_condition_s',
                        'monthly as insurence_monthly',
                        'yearly as insurence_yearly'
                    )->get()->toArray();

                // Merge all into the main offer array
                return [
                    'offer_id' => $offer->offer_id,
                    'employment_type' => $offer->employment_type,
                    'of_main_td' => $offer->of_main_td,
                    'currency' => $offer->currency,
                    'confidential' => $offer->confidential,
                    'salary_ctc' => $offer->salary_ctc,
                    'salary_mode' => $offer->salary_mode,
                    'designation' => $offer->designation,
                    'joined_date' => $offer->joined_date,
                    'monthly_gross' => $offer->monthly_gross,
                    'yearly_gross' => $offer->yearly_gross,
                    'monthly_bounus_gross' => $offer->monthly_bounus_gross,
                    'yearly_bonus_gross' => $offer->yearly_bonus_gross,
                    'monthly_insurence_gross' => $offer->monthly_insurence_gross,
                    'yearly_insurence_gross' => $offer->yearly_insurence_gross,
                    'yearly_final_gross' => $offer->yearly_final_gross,
                    'monthly_final_gross' => $offer->monthly_final_gross,
                    'Basic_offer_points' => $basicPoints,
                    'Bonus_offer_points' => $bonusPoints,
                    'Insurance_offer_points' => $insurancePoints,
                ];
            });

        //  dd($offerDetails);
        $this->OfferJobApplication = $offerDetails;

        $this->offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $this->offer_id)->orderByDesc('id')->first();
        // dd($this->OfferJobApplication);
        // $this->application = $this->application->where('add_job_wise_condidates.company_id', $company_id);
        $this->application = $this->application->first();

        //  dd($this->application);

        // echo '<pre>'; print_r($this->application); exit;
        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }
        $this->answers = JobApplicationAnswer::with(['question'])->where('job_id', $jobId)->where('job_application_id', $this->application->id)->get();

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $jobId;
        $this->job_id = $job_id;

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        // dd(	$this->updateTimeLine);

        $this->child_status_name = 'N/A';

        $this->jobWiseCanId = $id;

        $offer_points = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 0)
            ->where('offer_point_companies.section_divided', '=', 0)
            ->where('percentage', '>', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        $offer_point_other = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 1)
            ->where('offer_point_companies.section_divided', '=', 0)
            ->where('percentage', '>', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        if ($offer_points->isEmpty()) {
            $offer_points = DB::table('offer_points')->select('offer_points.*')->where('percentage', '>', 0)->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 0)->orderByDesc('sorting')->get();
        }

        if ($offer_point_other->isEmpty()) {
            $offer_point_other = DB::table('offer_points')->select('offer_points.*')->where('percentage', '>', 0)->where('offer_points.section_divided', '=', 0)->where('offer_points.section_type', '=', 1)->orderByDesc('sorting')->get();
        }

        $offer_point_hourly = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 0)
            ->where('offer_point_companies.section_divided', '=', 1)
            ->where('percentage', '>', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        $offer_point_other_hourly = DB::table('offer_point_companies')
            ->select('offer_point_companies.*')
            ->where('offer_point_companies.company_id', '=', $company_id)
            ->where('offer_point_companies.section_type', '=', 1)
            ->where('offer_point_companies.section_divided', '=', 1)
            ->where('percentage', '>', 0)
            ->orderByDesc('offer_point_companies.sorting')
            ->get();

        if ($offer_point_hourly->isEmpty()) {
            $offer_point_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 0)->where('percentage', '>', 0)->orderByDesc('sorting')->get();
        }

        if ($offer_point_other_hourly->isEmpty()) {
            $offer_point_other_hourly = DB::table('offer_points')->select('offer_points.*')->where('offer_points.section_divided', '=', 1)->where('offer_points.section_type', '=', 1)->where('percentage', '>', 0)->orderByDesc('sorting')->get();
        }

        $this->offer_points = $offer_points;
        $this->offer_point_other = $offer_point_other;

        $this->offer_point_hourly = $offer_point_hourly;
        $this->offer_point_other_hourly = $offer_point_other_hourly;

        $rowDataOffer = DB::table('offer_job_applications')
            ->join('job_applications', 'job_applications.id', '=', 'offer_job_applications.application_id')
            ->select('offer_job_applications.*', 'job_applications.full_name')
            ->where('offer_job_applications.id', $this->offer_id)->first();

        $this->account = DB::table('companies')->select('*')->where('id', $rowDataOffer->company_id)->first();
        $this->global_settings = DB::table('global_settings')->select('*')->first();
        $offer_terms = '';
        if ($this->offer_terms_conditions) {
            $offer_terms = (@$this->offer_terms_conditions->terms_condition) ? $this->offer_terms_conditions->terms_condition : '';
        } else {
            $offer_terms = (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms;
            if ($this->application->condidate_type == 'bench_condidate') {
                $offer_terms = (@$this->account->agency_offer_terms) ? $this->account->agency_offer_terms : (@$this->global_settings->agency_offer_terms ?: @$this->global_settings->offer_terms);
            }
        }
        $rowDataOfferDetails = GeneralMethod::offerLetterdetails($this->offer_id);

        $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
        $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
        $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
        $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
        $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';
        $candidate_email = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->email : '';
        $candidate_mobile = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->mobile : '';
        $of_joined_date = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->of_joined_date : '';

        if (! empty($of_joined_date)) {
            $of_joined_date_update = $of_joined_date;
        } else {
            $of_joined_date_update = '[JOINING_DATE]';
        }

        $replaceFrom = ['[CANDIDATE_NAME]', '[CANDIDATE_EMAIL]', '[CANDIDATE_MOBILE]', '[COMPANY_NAME]', '[JOB_TITLE]', '[JOINING_DATE]', '[{JOB_TITLE}]', '[TODAY_DATE]'];
        $replaceTo = [$candidate_name, $candidate_email, $candidate_mobile, $comapny_name, $job_title, $of_joined_date_update, $job_title, date('d M Y')];

        $this->offer_terms = str_replace($replaceFrom, $replaceTo, $offer_terms);

        // Resolve offer templates with variables replaced so the view can switch between saved defaults without an extra request.
        $raw_company_terms = (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms;
        $raw_internship_terms = (@$this->account->internship_offer_terms) ? $this->account->internship_offer_terms : (@$this->global_settings->internship_offer_terms ?: @$this->global_settings->offer_terms);
        $raw_contract_terms = (@$this->account->contract_offer_terms) ? $this->account->contract_offer_terms : (@$this->global_settings->contract_offer_terms ?: @$this->global_settings->offer_terms);
        $raw_agency_terms = (@$this->account->agency_offer_terms) ? $this->account->agency_offer_terms : (@$this->global_settings->agency_offer_terms ?: @$this->global_settings->offer_terms);
        $this->company_offer_terms = str_replace($replaceFrom, $replaceTo, (string) $raw_company_terms);
        $this->internship_offer_terms = str_replace($replaceFrom, $replaceTo, (string) $raw_internship_terms);
        $this->contract_offer_terms = str_replace($replaceFrom, $replaceTo, (string) $raw_contract_terms);
        $this->agency_offer_terms = str_replace($replaceFrom, $replaceTo, (string) $raw_agency_terms);
        $this->offer_template_options = [
            [
                'key' => 'candidate-offer-letter',
                'field' => 'offer_terms',
                'label' => 'Fulltime Offer Letter',
                'type' => 'Full Time',
                'content' => $this->company_offer_terms,
                'selected' => ! $this->offer_terms_conditions && $this->application->condidate_type != 'bench_condidate',
            ],
            [
                'key' => 'internship-offer-letter',
                'field' => 'internship_offer_terms',
                'label' => 'Internship Offer Letter',
                'type' => 'Internship',
                'content' => $this->internship_offer_terms,
                'selected' => false,
            ],
            [
                'key' => 'contract-offer-letter',
                'field' => 'contract_offer_terms',
                'label' => 'Contract Offer Letter',
                'type' => 'Contract',
                'content' => $this->contract_offer_terms,
                'selected' => false,
            ],
            [
                'key' => 'agency-offer-letter',
                'field' => 'agency_offer_terms',
                'label' => 'Agency Offer terms',
                'type' => 'Contract',
                'content' => $this->agency_offer_terms,
                'selected' => ! $this->offer_terms_conditions && $this->application->condidate_type == 'bench_condidate',
            ],
        ];
        $this->offer_template_contents = collect($this->offer_template_options)->pluck('content', 'key')->all();
        $this->selected_offer_template_type = collect($this->offer_template_options)->firstWhere('selected', true)['type'] ?? 'Full Time';
        $this->is_bench_candidate = ($this->application->condidate_type == 'bench_condidate');

        // Same values used in the merge above, exposed to the view so the Template step's
        // "Insert Variable" chips can insert this offer's actual resolved values.
        $this->co_candidate_name = $candidate_name;
        $this->co_candidate_email = $candidate_email;
        $this->co_candidate_mobile = $candidate_mobile;
        $this->co_company_name = $comapny_name;
        $this->co_job_title = $job_title;
        $this->co_joining_date = $of_joined_date_update;
        $this->co_today_date = date('d M Y');
        $this->co_company_logo = $logo;
        $this->co_company_address = $address;

        // Approval step (Step 4): $section_change_value already carries offer_create_main_tds.*
        // (management_status/send_approved_date_time/approved_date_time). The approver's own
        // name/email only lives on updatetimeline (written by fwd_offer_send()/saveSignature()),
        // so pull the latest approval-type row for this application, plus a short activity feed.
        // Fetch ALL approval emails sent for this offer (one row per approver/resend).
        // Left-join users_profile by email to get the approver's designation when available.
        $this->co_appr_timeline = DB::table('updatetimeline')
            ->leftJoin('users', 'users.email', '=', 'updatetimeline.email_id_hr')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->select('updatetimeline.*', 'users_profile.designation as approver_designation')
            ->where('updatetimeline.application_id', $this->application->id)
            ->where('updatetimeline.job_id', $this->application->job_id)
            ->whereIn('updatetimeline.mtypeSection', [2, 8]) // 2 = legacy approval, 8 = new approval flow (mtypeId=8)
            ->orderByDesc('updatetimeline.id')
            ->get();

        $this->co_activity_log = DB::table('updatetimeline')
            ->where('application_id', $this->application->id)
            ->where('job_id', $this->application->job_id)
            ->whereNotNull('mtypeSection')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // Step 5 hero: "Sent By" — mtypeSection 1 = candidate-facing offer email (fwd_offer_send,
        // mtypeId==1). name_hr/email_id_hr aren't populated for this event type, so the sender
        // comes from event_by_user_id instead. "Sent/Not Sent" itself reads
        // offer_create_main_tds.send_accept_date_time (already in $section_change_value).
        $this->co_send_timeline = DB::table('updatetimeline')
            ->leftJoin('users', 'users.id', '=', 'updatetimeline.event_by_user_id')
            ->where('updatetimeline.application_id', $this->application->id)
            ->where('updatetimeline.job_id', $this->application->job_id)
            ->where('updatetimeline.mtypeSection', 1)
            ->orderByDesc('updatetimeline.id')
            ->select('updatetimeline.*', 'users.name as sent_by_name')
            ->first();

        // Step 5 "Template" dropdown — same ES-backed list offerSendMail() uses for mtype==1
        // (candidate offer email): temptype 7. Selecting one calls the existing
        // service_company.cvtracker.email_template endpoint (unchanged) to fill Subject/Body.
        $co_send_tpl_params = [
            'index' => ELASTIC_EMAIL_TEMPLATE,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['status' => 1]],
                            ['match' => ['temptype' => 7]],
                        ],
                    ],
                ],
            ],
        ];
        $co_send_tpl_response = $this->elasticService->getClient()->search($co_send_tpl_params);
        $this->co_send_templates = collect($co_send_tpl_response['hits']['hits'] ?? [])
            ->map(function ($hit) {
                return (object) $hit['_source'];
            });

        // Step 5 "Add Email" picker: internal team members (active users in this company) and,
        // when the job belongs to an external client, that client's contacts. Same directories
        // the approval composer uses, exposed here so the native Send Offer Letter page can
        // offer a pick-from-team drawer without an AJAX round-trip.
        $this->co_send_internal = DB::table('users')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->select('users.id', 'users.name', 'users.email', 'users_profile.designation as display_name', 'companies.company_name')
            ->where('users.company_id', '=', auth()->user()->company_id)
            ->where('users.status', '=', 'active')
            ->whereNotNull('users.email')
            ->groupBy('users.id')->get()->toArray();

        $this->co_send_client = [];
        $co_job_client = DB::table('jobs')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->select('clients.id as client_id', 'clients.self_company')
            ->where('jobs.id', $this->application->job_id)->first();
        if ($co_job_client && (int) @$co_job_client->self_company === 0 && @$co_job_client->client_id) {
            $this->co_send_client = DB::table('client_contact_info')
                ->leftjoin('clients', 'clients.id', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
                ->where('client_contact_info.client_id', $co_job_client->client_id)
                ->whereNotNull('client_contact_info.contact_person_email')->get()->toArray();
        }

        // dd($this->offer_points);
        $view = view('service_company.job-applications.create-offer', $this->data)->render();
        $permisiionCheck = GeneralMethod::permissioncheck($offerPermision, auth()->user()->id);
        if ($permisiionCheck) {

            $offerPermisiontext = 'You are not authorized to access these features. Please contact your administrator for assistance.';

            // dd($permisiionCheck);
            return Reply::dataOnly(['status' => 'permisionInfo', 'view' => $offerPermisiontext]);
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function saveCreateOffer(Request $request)
    {
        $offer_id = $request->offer_id;
        $yearly_gross = 0;
        $yearly_final_gross = 0;
        $this->section_change_value = OfferJobApplication::join('offer_create_main_tds', 'offer_create_main_tds.offer_id', '=', 'offer_job_applications.id')->select('offer_create_main_tds.*', 'offer_job_applications.id as offer_id_main')->where('offer_job_applications.id', $offer_id)->where('offer_create_main_tds.status', '=', 1)->first();
        if ($this->section_change_value) {
            $offerPermision = 64;
        } else {
            $offerPermision = 66;
        }
        $employment_type = GeneralMethod::employmentType();
        $of_employment_type = $employment_type[$request->employment_type];
        // dd($of_employment_type,$request->all());

        $permisiionCheck = GeneralMethod::permissioncheck($offerPermision, auth()->user()->id);
        if ($permisiionCheck) {
            // dd($permisiionCheck);
            $offerPermisiontext = 'You are not authorized to access these features. Please contact your administrator for assistance.';

            return response()->json(['status' => 'permisionInfo', 'view' => $offerPermisiontext]);
        }

        if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {
            $validator = Validator::make($request->all(), [
                'offer_point_hourly.*' => 'sometimes|required',
                'option_percentage_hourly.*' => 'sometimes|required',
                'option_salarymode_hourly.*' => 'sometimes|required',
                'offer_hourly.*' => 'sometimes|required',
                'bonus_point_hourly.*' => 'sometimes|required',
                'bonus_option_percentage_hourly.*' => 'sometimes|required',
                'bonus_option_salarymode_hourly.*' => 'sometimes|required',
                'bonus_offer_hourly.*' => 'sometimes|required',
                'insurence_point_hourly.*' => 'sometimes|required',
                'insurence_option_percentage_hourly.*' => 'sometimes|required',
                'insurence_option_salarymode_hourly.*' => 'sometimes|required',
                'insurence_offer_hourly.*' => 'sometimes|required',
                'finalpaytotalhourly' => 'required',
                'ctc' => 'required',
                'dob' => 'required',
                'current_designation' => 'required',
            ], [
                'offer_point_hourly.*.required' => 'Offer point is required.',
                'option_percentage_hourly.*.required' => 'Option percentage is required.',
                'option_salarymode_hourly.*.required' => 'Option salary mode is required.',
                'offer_hourly.*.required' => 'Offer hourly is required.',

                'bonus_point_hourly.*.required' => 'Bonus point is required.',
                'bonus_option_percentage_hourly.*.required' => 'Bonus option percentage is required.',
                'bonus_option_salarymode_hourly.*.required' => 'Bonus option salary mode is required.',
                'bonus_offer_hourly.*.required' => 'Bonus offer hourly is required.',

                'insurence_point_hourly.*.required' => 'Insurance point is required.',
                'insurence_option_percentage_hourly.*.required' => 'Insurance option percentage is required.',
                'insurence_option_salarymode_hourly.*.required' => 'Insurance option salary mode is required.',
                'insurence_offer_hourly.*.required' => 'Insurance offer hourly is required.',

                'finalpaytotalhourly.required' => 'The final total hourly payment is required.',
                'ctc.required' => 'The CTC is required.',
                'dob.required' => 'The join date is required.',
                'current_designation.required' => 'The designation is required.',
            ]);
        } else {

            $validator = Validator::make($request->all(), [
                'offer_point.*' => 'sometimes|required',
                'option_percentage.*' => 'sometimes|required',
                'option_salarymode.*' => 'sometimes|required',
                'offer_monthly.*' => 'sometimes|required',
                'offer_yearly.*' => 'sometimes|required',
                'bonus_points.*' => 'sometimes|required',
                'bonus_option_percentage.*' => 'sometimes|required',
                'bonus_option_salarymode.*' => 'sometimes|required',
                'bonus_offer_monthly.*' => 'sometimes|required',
                'bonus_offer_yearly.*' => 'sometimes|required',
                'insurence_points.*' => 'sometimes|required',
                'insurence_option_percentage.*' => 'sometimes|required',
                'insurence_option_salarymode.*' => 'sometimes|required',
                'insurence_offer_monthly.*' => 'sometimes|required',
                'insurence_offer_yearly.*' => 'sometimes|required',
                'finalpaytotalMonthly' => 'required',
                'finalpaytotalYearly' => 'required',
                'ctc' => 'required',
                'dob' => 'required',
                'current_designation' => 'required',
            ], [
                'offer_point.*.required' => 'Offer point is required.',
                'option_percentage.*.required' => 'Option percentage is required.',
                'option_salarymode.*.required' => 'Option salary mode is required.',
                'offer_monthly.*.required' => 'Offer monthly is required.',
                'offer_yearly.*.required' => 'Offer yearly is required.',

                'bonus_points.*.required' => 'Bonus point is required.',
                'bonus_option_percentage.*.required' => 'Bonus option percentage is required.',
                'bonus_option_salarymode.*.required' => 'Bonus option salary mode is required.',
                'bonus_offer_monthly.*.required' => 'Bonus offer monthly is required.',
                'bonus_offer_yearly.*.required' => 'Bonus offer yearly is required.',

                'insurence_points.*.required' => 'Insurance point is required.',
                'insurence_option_percentage.*.required' => 'Insurance option percentage is required.',
                'insurence_option_salarymode.*.required' => 'Insurance option salary mode is required.',
                'insurence_offer_monthly.*.required' => 'Insurance offer monthly is required.',
                'insurence_offer_yearly.*.required' => 'Insurance offer yearly is required.',

                'finalpaytotalMonthly.required' => 'The final total monthly payment is required.',
                'finalpaytotalYearly.required' => 'The final total yearly payment is required.',
                'ctc.required' => 'The CTC is required.',
                'dob.required' => 'The join date is required.',
                'current_designation.required' => 'The designation is required.',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->current_ctc_mode == 2) {
            $totalSum = array_sum(array_map('floatval', (array) $request->option_percentage_hourly));
        } else {
            $totalSum = array_sum(array_map('floatval', (array) $request->option_percentage));
        }
        // dd($totalSum);
        if (round($totalSum, 2) != 100) {
            return response()->json(['status' => 'errors', 'message' => 'Please check Percentage and complete 100%']);
        }

        try {
            // Start the transaction
            DB::beginTransaction();

            $exits_offer = DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->where('status', '=', 1)->orderByDesc('id')->first();

            if (! DB::table('offer_create_main_tds')->where('offer_id', '=', $offer_id)->exists()) {
                $offer_job_applications = DB::table('offer_job_applications')
                    ->select('joined as wiseId')
                    ->where('id', $offer_id)
                    ->first();
                $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($offer_job_applications->wiseId);

                $spok_user_id_rows = DB::table('job_assign')
                    ->where('job_id', $AddJobWiseCondidate->job_id)
                    ->get(['spok_user_id', 'company_id'])->toArray();

                // Step 2: Build result as [company_id => spok_user_id]
                $teamCaptainId = [];

                foreach ($spok_user_id_rows as $row) {
                    if (
                        ! is_null($row->spok_user_id) &&
                        $row->spok_user_id != $AddJobWiseCondidate->submitted_by
                    ) {
                        $teamCaptainId[$row->company_id][] = $row->spok_user_id;
                    }
                }

                // Step 3: Remove duplicates and reindex inner arrays
                foreach ($teamCaptainId as $companyId => $spokIds) {
                    $teamCaptainId[$companyId] = array_values(array_unique($spokIds));
                }

                $join_update_date_check = date('Y-m-d H:i:s', strtotime($request->dob));
                $join_update_date_check = Carbon::parse($join_update_date_check);
                $today = Carbon::today();
                $diffInDays = abs($today->diffInDays($join_update_date_check));

                // Every offer release counts (no 30-day window), so offers always reconcile with joins
                // (which are ungated). Product decision 2026-06-30 — fix applies going forward.
                $offerReleasedUser = 300;
                $offerReleasedTeam = 20;

                $teamCaptainId = array_map(function ($arr) {
                    return array_values(array_filter($arr, function ($value) {
                        return $value != 0;
                    }));
                }, $teamCaptainId);

                if (count($teamCaptainId) > 0) {

                    foreach ($teamCaptainId as $comId => $captainId) {
                        $teamRankElastic = [
                            'company_id' => $comId,
                            'user_id' => $captainId[0],
                            'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                            'profileUpload' => 0,
                            'interviewScheduled' => 0,
                            'interviewNoShow' => 0,
                            'offerReleased' => 0,
                            'candidateJoined' => 0,
                            'jobsPosted' => 0,
                            'feedbackGivenOnCVs' => 0,
                            'postJobToVendorMarket' => 0,
                            'outsourceJobsToVendors' => 0,
                            'listBenchCandidates' => 0,
                            'respondToBenchRequests' => 0,
                            'interestedCandidates' => 0,
                            'notInterestedCandidates' => 0,
                            'teamCaptainInterviewScheduled' => 0,
                            'teamCaptainInterviewNoShow' => 0,
                            'teamCaptainOfferReleased' => $offerReleasedTeam,
                            'teamCaptainCandidateJoined' => 0,
                        ];
                        GeneralMethod::teamRankElastic($teamRankElastic);
                    }

                }

                $teamRankElastic = [
                    'company_id' => $AddJobWiseCondidate->company_id,
                    'user_id' => $AddJobWiseCondidate->submitted_by,
                    'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                    'profileUpload' => 0,
                    'interviewScheduled' => 0,
                    'interviewNoShow' => 0,
                    'offerReleased' => $offerReleasedUser,
                    'candidateJoined' => 0,
                    'jobsPosted' => 0,
                    'feedbackGivenOnCVs' => 0,
                    'postJobToVendorMarket' => 0,
                    'outsourceJobsToVendors' => 0,
                    'listBenchCandidates' => 0,
                    'respondToBenchRequests' => 0,
                ];
                GeneralMethod::teamRankElastic($teamRankElastic);
            }

            if ($exits_offer) {
                DB::table('offer_create_main_tds')->where('offer_id', $offer_id)->where('status', '=', 1)->update(['status' => 2]);
            }

            $eventId = 17;
            $coinsRequ = 1;
            $companyId = (int) auth()->user()->company_id;

            $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
            if ($packageStatus->status) {
                return response()->json(['status' => 'errors', 'message' => $packageStatus->message]);
            }

            $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
            if ($coinsDeductions->status) {
                return response()->json(['status' => 'errors', 'message' => $coinsDeductions->message]);
            }

            if ($request->current_ctc_mode == 2 || $request->current_ctc_mode == 1 || $request->current_ctc_mode == 3) {

                // Prepare the main offer data

                $otherPoint = 0;
                if (isset($request->bonus_point_hourly) && is_array($request->bonus_point_hourly) && count($request->bonus_point_hourly) > 0) {
                    $otherPoint = count($request->bonus_point_hourly);
                }

                $offer_create_main_tds = [
                    'offer_id' => $offer_id,
                    'currency' => $request->current_ctc_cur,
                    'employment_type' => $request->employment_type,
                    'salary_ctc' => $request->ctc,
                    'salary_mode' => $request->current_ctc_mode,
                    'designation' => $request->current_designation,
                    'joined_date' => date('Y-m-d H:i:s', strtotime($request->dob)),
                    'joined' => '',
                    'yearly_gross' => $request->totalhourlyhourly,
                    'yearly_bonus_gross' => $request->totalhourlyybonus,
                    'yearly_insurence_gross' => $request->totalYearlyinsurencehourly,
                    'yearly_final_gross' => ($otherPoint) ? $request->finalpaytotalhourly : $request->totalhourlyhourly,
                ];
                // $exits_offer_sign = DB::table('offer_create_main_tds')->where('offer_id','=',$offer_id)->where('signature_candidate','=',1)->orderByDesc('id')->first();
                // if($exits_offer_sign){
                // 	$offer_create_main_tds['signature_candidate'] = ($exits_offer->signature_candidate) ? $exits_offer->signature_candidate : '';
                // 	$offer_create_main_tds['accept_date_time'] = ($exits_offer->accept_date_time) ? $exits_offer->accept_date_time : '';
                // 	$offer_create_main_tds['approved_date_time'] = ($exits_offer->approved_date_time) ? $exits_offer->approved_date_time : '';
                // 	$offer_create_main_tds['management_status'] = ($exits_offer->management_status) ? $exits_offer->management_status : '';
                // }
                $yearly_gross = $request->totalhourlyhourly;
                $yearly_final_gross = ($otherPoint) ? $request->finalpaytotalhourly : $request->totalhourlyhourly;
                // Insert the main offer data and get the ID
                $offer_create_main_td_id = DB::table('offer_create_main_tds')->insertGetId($offer_create_main_tds);

                // Check if offer points exist and insert them
                if (! empty($request->offer_point_hourly)) {
                    foreach ($request->offer_point_hourly as $bp => $point) {
                        $basic_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->option_percentage_hourly[$bp],
                            'condition_s' => $request->option_salarymode_hourly[$bp],
                            'yearly' => $request->offer_hourly[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('basic_offer_points')->insert($basic_offer_points);
                    }
                }

                // Check if insurance points exist and insert them
                if (! empty($request->insurence_point_hourly)) {
                    foreach ($request->insurence_point_hourly as $bp => $point) {
                        $insurence_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->insurence_option_percentage_hourly[$bp],
                            'condition_s' => $request->insurence_option_salarymode_hourly[$bp],
                            'yearly' => $request->insurence_offer_hourly[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('insurence_offer_points')->insert($insurence_offer_points);
                    }
                }

                // Check if bonus points exist and insert them
                if (! empty($request->bonus_point_hourly)) {
                    foreach ($request->bonus_point_hourly as $bp => $point) {
                        $bonus_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->bonus_option_percentage_hourly[$bp],
                            'condition_s' => $request->bonus_option_salarymode_hourly[$bp],
                            'yearly' => $request->bonus_offer_hourly[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('bonus_offer_points')->insert($bonus_offer_points);
                    }
                }

            } else {
                $otherPoint = 0;
                if (! empty($request->bonus_points)) {
                    $otherPoint = count($request->bonus_points);
                }

                // Prepare the main offer data
                $offer_create_main_tds = [
                    'offer_id' => $offer_id,
                    'currency' => $request->current_ctc_cur,
                    'employment_type' => $request->employment_type,
                    'salary_ctc' => $request->ctc,
                    'salary_mode' => $request->current_ctc_mode,
                    'designation' => $request->current_designation,
                    'joined_date' => date('Y-m-d H:i:s', strtotime($request->dob)),
                    'joined' => '',
                    'monthly_gross' => $request->totalMonthly,
                    'yearly_gross' => $request->totalYearly,
                    'monthly_bounus_gross' => $request->totalMonthlybonus,
                    'yearly_bonus_gross' => $request->totalYearlybonus,
                    'monthly_insurence_gross' => $request->totalMonthlyinsurence,
                    'yearly_insurence_gross' => $request->totalYearlyinsurence,
                    'monthly_final_gross' => $request->finalpaytotalMonthly,
                    'yearly_final_gross' => ($otherPoint) ? $request->finalpaytotalYearly : $request->totalYearly,
                ];
                $yearly_gross = $request->totalYearly;
                $yearly_final_gross = ($otherPoint) ? $request->finalpaytotalYearly : $request->totalYearly;
                // Insert the main offer data and get the ID
                // dd($offer_create_main_tds);
                $offer_create_main_td_id = DB::table('offer_create_main_tds')->insertGetId($offer_create_main_tds);
                // dd($offer_create_main_tds);

                // Check if offer points exist and insert them
                if (! empty($request->offer_point)) {
                    foreach ($request->offer_point as $bp => $point) {
                        $basic_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->option_percentage[$bp],
                            'condition_s' => $request->option_salarymode[$bp],
                            'monthly' => $request->offer_monthly[$bp],
                            'yearly' => $request->offer_yearly[$bp],
                            'view_type' => $request->basic_view[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('basic_offer_points')->insert($basic_offer_points);
                    }
                }

                // Check if insurance points exist and insert them
                if (! empty($request->insurence_points)) {
                    foreach ($request->insurence_points as $bp => $point) {
                        $insurence_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->insurence_option_percentage[$bp],
                            'condition_s' => $request->insurence_option_salarymode[$bp],
                            'monthly' => $request->insurence_offer_monthly[$bp],
                            'yearly' => $request->insurence_offer_yearly[$bp],
                            'view_type' => $request->insurence_view[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('insurence_offer_points')->insert($insurence_offer_points);
                    }
                }

                // Check if bonus points exist and insert them
                if (! empty($request->bonus_points)) {
                    foreach ($request->bonus_points as $bp => $point) {
                        $bonus_offer_points = [
                            'poins' => $point,
                            'Percentage' => $request->bonus_option_percentage[$bp],
                            'condition_s' => $request->bonus_option_salarymode[$bp],
                            'monthly' => $request->bonus_offer_monthly[$bp],
                            'yearly' => $request->bonus_offer_yearly[$bp],
                            'view_type' => $request->bonus_view[$bp],
                            'offer_create_main_td_id' => $offer_create_main_td_id,
                            'offer_id' => $offer_id,
                        ];
                        DB::table('bonus_offer_points')->insert($bonus_offer_points);
                    }
                }

            }

            $Offertimelime = OfferJobApplication::withoutGlobalScopes()->find($offer_id);

            $option_currency_val = @$request->current_ctc_cur;
            $joined_date = @$request->dob ? date('Y-m-d H:i:s', strtotime(@$request->dob)) : 'NULL';
            $ctc = @$request->ctc;
            $current_ctc_mode = @$request->current_ctc_mode;
            $confidential = @$request->confidential;
            $Offertimelime->confidential = @$confidential;
            $Offertimelime->offer_currency = @$option_currency_val ? @$option_currency_val : 'NULL';
            $Offertimelime->offer_mode = @$current_ctc_mode ? @$current_ctc_mode : 'NULL';
            $Offertimelime->offer_amount = @$yearly_final_gross ? @$yearly_final_gross : 'NULL';
            $Offertimelime->joined_date = @$joined_date ? $joined_date : 'NULL';
            $Offertimelime->save();

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_CANDIDATES,
                'id' => $Offertimelime->joined,
            ]);

            // dd($Offertimelime->joined,$joined_date,$option_currency_val,$current_ctc_mode,$yearly_final_gross);
            if ($exists) {

                $updateTime = now()->toIso8601String();
                $updateParams_e = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $Offertimelime->joined,
                    'body' => [
                        'doc' => [
                            'of_joined_date' => (! empty(@$joined_date) && @$joined_date != '0000-00-00 00:00:00') ? Carbon::parse(@$joined_date)->toIso8601String() : null,
                            'of_offer_currency' => (int) @$option_currency_val,
                            'of_offer_mode' => (int) @$current_ctc_mode,
                            'of_offer_amount' => @$yearly_final_gross ? @$yearly_final_gross : 'NULL',
                            'of_offer_acceptance' => 0,
                            'of_employment_type' => $of_employment_type,
                            'of_created_date' => $updateTime,
                            'confidential' => $confidential,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => false, // Eventual consistency for 10K concurrent scale
                ];
                // dd($updateParams);

                $redudds = $this->elasticService->getClient()->update($updateParams_e);

                if (@$Offertimelime->application_id) {
                    GeneralMethod::elasticBenchCandidateAddEditIndex($Offertimelime->application_id);
                }

                // dd($redudds);

            }

            $date = date('Y-m-d H:i:s');

            $values = [
                'event_id' => 4,
                'event_by_company_id' => auth()->user()->company_id,
                'event_by_user_id' => auth()->user()->id,

                'application_id' => @$Offertimelime->application_id ? @$Offertimelime->application_id : null,
                'application_email' => @$Offertimelime->email ? @$Offertimelime->email : null,
                'job_id' => @$Offertimelime->job_id ? @$Offertimelime->job_id : null,
                'note' => '',
                'create_at' => @$date ? @$date : null,
                'update_at' => @$date ? @$date : null,
            ];
            $jobApplication = AddJobWiseCondidate::withoutGlobalScopes()->find($Offertimelime->joined);
            if (@$jobApplication->master_status_id > 0) {
                $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
                $values['master_process_name'] = @$masterStatus->name;
            }
            if (@$jobApplication->child_status_id > 0) {
                $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
                $values['child_process_name'] = @$masterStatus->name;
            }

            $values['master_process_name'] = @$masterStatus->name;
            $values['child_process_name'] = @$masterStatus->name;
            $values['offer_currency'] = @$option_currency_val;
            $values['offer_mode'] = @$current_ctc_mode;
            $values['offer_amount'] = @$ctc;
            $values['joined_date'] = @$joined_date;
            $values['confidential'] = @$confidential;

            $values['offer_currency'] = @$option_currency_val ? @$option_currency_val : null;
            $values['offer_mode'] = @$current_ctc_mode ? @$current_ctc_mode : null;
            $values['offer_amount'] = @$ctc ? @$ctc : null;
            $values['joined_date'] = @$request->dob ? $joined_date : null;

            $values['candidate_status'] = 0;
            $values['management_status'] = 0;
            $values['accept_date_time'] = '';
            $values['signature_candidate'] = '';
            $values['email_id_hr'] = '';
            $values['name_hr'] = '';
            $values['yearly_gross'] = @$yearly_gross ? $yearly_gross : '';
            $values['yearly_final_gross'] = @$yearly_final_gross ? $yearly_final_gross : '';
            $values['mtypeSection'] = 3;
            $values['designation_offer'] = @$request->current_designation;

            GeneralMethod::UpdateTimeline($values);

            $this->offer_terms_conditions = DB::table('offer_terms_conditions')->where('offer_id', '=', $offer_id)->orderByDesc('id')->first();

            $rowDataOffer = DB::table('offer_job_applications')
                ->join('job_applications', 'job_applications.id', '=', 'offer_job_applications.application_id')
                ->select('offer_job_applications.*', 'job_applications.full_name')
                ->where('offer_job_applications.id', $offer_id)->first();
            // dd($rowDataOffer);

            $this->account = DB::table('companies')->select('*')->where('id', auth()->user()->company_id)->first();
            $this->global_settings = DB::table('global_settings')->select('*')->first();
            $offer_terms = '';

            $offer_terms = (@$this->account->offer_terms) ? $this->account->offer_terms : @$this->global_settings->offer_terms;

            $rowDataOfferDetails = GeneralMethod::offerLetterdetails($offer_id);

            $address = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->address : '';
            $comapny_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->comapny_name : '';
            $logo = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->logo : '';
            $job_title = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->job_title : '';
            $candidate_name = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->full_name : '';
            $candidate_email = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->email : '';
            $candidate_mobile = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->mobile : '';
            $of_joined_date = (@$rowDataOfferDetails) ? @$rowDataOfferDetails->of_joined_date : '';
            if (! empty($of_joined_date)) {
                $of_joined_date_update = $of_joined_date;
            } else {
                $of_joined_date_update = '[JOINING_DATE]';
            }

            $this->offer_terms = str_replace(['[CANDIDATE_NAME]', '[CANDIDATE_EMAIL]', '[CANDIDATE_MOBILE]', '[COMPANY_NAME]', '[JOB_TITLE]', '[JOINING_DATE]', '[{JOB_TITLE}]', '[TODAY_DATE]'], [$candidate_name, $candidate_email, $candidate_mobile, $comapny_name, $job_title, $of_joined_date_update, $job_title, date('d M Y')], $offer_terms);

            // Commit the transaction if all operations are successful
            DB::commit();

            // Return a success response

            $cachekeys = [
                'allapptracker_version_user_'.$this->user->id,
                'allcvtracker_version_user_'.$this->user->id,
                'candidateDetails_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return response()->json(['status' => 'success', 'terms' => $this->offer_terms, 'message' => 'Offer Created successfully.']);

        } catch (\Exception $e) {
            // Rollback the transaction if any operation fails
            DB::rollBack();

            // Log the exception message (optional)
            \Log::error('Transaction failed: '.$e->getMessage());
            // dd($e->getMessage());

            // Return an error response
            return response()->json(['status' => 'failed', 'message' => 'Failed to save data. Please try again.'], 500);
        }

    }

    public function termsSaveCreateOffer(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'editor1' => 'sometimes|required',
        ], [
            'editor1.*.required' => 'Offer point is required.',
        ]);
        if ($validator->fails()) {
            // dd($validator->errors(),'hi');
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $offer_create_main_tds = [
            'offer_id' => $request->offerId,
            'terms_condition' => $request->editor1,
        ];
        // Insert the main offer data and get the ID
        $offer_terms_conditions = DB::table('offer_terms_conditions')->insertGetId($offer_create_main_tds);
        if ($offer_terms_conditions > 0) {
            return response()->json(['status' => 'success', 'message' => 'Offer Terms Created successfully.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Failed to save data. Please try again']);
        }

    }

    // Function to convert currency to INR
    public function update(Request $request, $id)
    {
        $id = GeneralMethod::value_decrypt($id);
        if (! $id) {
            return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning', 'message' => 'Invalid candidate application ID']);
        }
        // echo '<pre>'; print_r($_FILES); exit;
        // die;

        // dd(auth()->user()->company_id);

        // dd($request->all());

        // Authorization: a candidate profile may only be edited by the assigned Captain
        // (job_assign.spok_user_id) or the submitting user (add_job_wise_condidates.submitted_by),
        // and only within the same company (tenancy).
        $authUserId = auth()->id();
        $authCompanyId = auth()->user()->company_id;
        $canEditCandidate = DB::table('add_job_wise_condidates as ajwc')
            ->leftJoin('job_assign as ja', function ($join) {
                $join->on('ja.job_id', '=', 'ajwc.job_id')
                    ->on('ja.company_id', '=', 'ajwc.company_id');
            })
            ->where('ajwc.job_application_id', $id)
            // ->where('ajwc.company_id', $authCompanyId)
            ->where(function ($query) use ($authUserId) {
                $query->where('ja.spok_user_id', $authUserId)
                    ->orWhere('ajwc.submitted_by', $authUserId);
            })
            ->exists();

        if (! $canEditCandidate) {
            return Reply::dataOnly([
                'status' => 'notauthorized',
                'msgType' => 'warning',
                'message' => "You are not authorized to edit this candidate's profile. Only the assigned Captain or the submitting user can edit it",
            ]);
        }

        $validator = Validator::make($request->all(), array_merge([
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], CandidateNoticePeriodState::validationRules($request->input('serving_notice_period'))), array_merge([
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
        ], CandidateNoticePeriodState::validationMessages()));

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'mailcheck', 'msgType' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $company_id = auth()->user()->company_id;

        // $education = request('kt_tagify_education');
        // $custom_education = json_decode($education, true);

        // dd($custom_education);
        // $education = array();
        // if (\request('kt_tagify_education') != "") {
        // 	foreach ($custom_education as $sval) {
        // 		$education_data = DB::table('qualification_master')->where('qualification_master.name', '=', $sval['value'])->first();
        // 		if ($education_data) {
        // 			$educationfiletr = GeneralMethod::qualificationFilterCondition($education_data->id);
        // 			$education = array_merge($education, $educationfiletr);
        // 		}
        // 	}
        // }

        $skillStr = '';
        $skillArr = json_decode($request->skills, true);
        $requiredSkills = [];
        if (! empty($skillArr)) {
            foreach ($skillArr as $sval) {
                array_push($requiredSkills, $sval['value']);
            }
        }

        abort_if(Gate::denies('edit_job_applications'), 403);

        $jobApplication = JobApplication::with(['documents'])->withoutGlobalScopes()->find($id);
        // dd($jobApplication,$id);
        $jobApplication->full_name = ucwords(strtolower($request->full_name));
        if (! empty($request->current_designation)) {
            $jobApplication->current_designation = @$request->current_designation;
        }
        if (! empty($request->current_company)) {
            $jobApplication->current_company = @$request->current_company;
        }
        $jobApplication->phone = $request->phone;
        $jobApplication->secondary_mobile = $request->secondary_mobile;
        if ($request->has('dob')) {
            $jobApplication->dob = $request->dob;
        }
        $jobApplication->email = $request->email;
        $jobApplication->secondrary_email = $request->secondrary_email;

        if (! empty($request->qualification)) {
            $jobApplication->qualification = @$request->qualification;
        }

        $jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
        $jobApplication->second_phone_code = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

        // $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

        $total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '') ? $request->total_exp_year.'.'.$request->total_exp_month : null;

        $rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '') ? $request->rel_exp_year.'.'.$request->rel_exp_month : null;

        // dd($total_exp,$rel_exp,$id);

        // dd( "hu");

        // $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

        // dd($total_exp,$rel_exp,$request->total_exp_year);

        // if (!empty($request->total_exp)) {
        $jobApplication->total_exp = $total_exp;
        // }
        // if (!empty($request->rel_exp)) {
        $jobApplication->rel_exp = $rel_exp;
        // }

        $jobApplication->gender = $request->gender;

        $noticePeriodState = CandidateNoticePeriodState::fromRequest($request);
        $jobApplication->np_id = $noticePeriodState['np_id'];
        $jobApplication->last_working_date = $noticePeriodState['last_working_date'];
        $jobApplication->serving_notice_period_mode = $noticePeriodState['serving_notice_period_mode'];
        $salaryMode = $this->normalizedCandidateSalaryMode($request->input('current_ctc_mode'));
        $jobApplication->current_ctc_cur = $request->current_ctc_cur;
        if (! empty($request->ctc)) {
            $jobApplication->ctc = @$request->ctc;
        }
        $jobApplication->current_ctc_mode = $salaryMode;
        $jobApplication->current_ectc_cur = $request->current_ectc_cur;
        if (! empty($request->ectc)) {
            $jobApplication->ectc = @$request->ectc;
        }
        $jobApplication->current_ectc_mode = $salaryMode;

        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$request->current_ectc_cur];
        $econvertedAmount = $request->bill_annual;
        if ($request->current_ectc_cur > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);
        $jobApplication->ectc_yearly = @$ectcdivided->yearly;
        $jobApplication->ectc_monthly = @$ectcdivided->monthly;
        $jobApplication->ectc_hourly = @$ectcdivided->hourly;

        $currency = $currency[$request->current_ctc_cur];
        // dd($currency);
        $convertedAmount = $request->pay_annual;
        if ($request->current_ctc_cur > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);
        $jobApplication->ctc_yearly = @$ctcdivided->yearly;
        $jobApplication->ctc_monthly = @$ctcdivided->monthly;
        $jobApplication->ctc_hourly = @$ctcdivided->hourly;

        $jobApplication->percentage_hike = $this->calculatedCandidateSalaryIncrease(
            $jobApplication->ctc_yearly,
            $jobApplication->ectc_yearly
        );
        $jobApplication->comment = @$request->comment;
        $jobApplication->notice_comments = @$request->notice_comments;

        $jobApplication->careersummary = $request->careersummary;
        $jobApplication->nationality = $request->nationality;
        $jobApplication->wm = $request->wm;
        $jobApplication->employmentType = $request->employmentType;
        $jobApplication->oh = $request->oh;
        $jobApplication->visastatu = $request->visastatu;
        $jobApplication->referencename = $request->referencename;
        $jobApplication->recruiter_note = $request->recruiter_note;
        $jobApplication->reason_for_job_change = $request->reason_for_job_change;

        $stateId = '';
        $cityID = '';
        $countryID = '';
        $location_find = '';
        /* Intent: Canonical Location Resolution Engine Fast-Path (0% MySQL Reads, CQRS Compliant) */
        if ($request->filled('location_city_id') && (int) $request->location_city_id > 0) {
            $cityID = (int) $request->location_city_id;
            $stateId = (int) $request->location_state_id;
            $countryID = (int) ($request->location_country_id ?: 101);
            $location_find = (string) $request->location_canonical_label;
        } else {
            $workLocation = $this->normalizedCandidateLocation($request->input('kt_tagify_work_location'));
            if ($workLocation !== null) {
                $cityName = $workLocation['city'];
                $stateName = $workLocation['state'];
                $countrName = $workLocation['country'];
                $location_find = $cityName.'_'.$stateName.'_'.$countrName;
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

        $jobApplication->country = $countryID;
        $jobApplication->state = $stateId;

        if ($jobApplication->country == 101) {
            $jobApplication->city_id = $cityID;
        } else {
            $jobApplication->city_id = $cityID;
        }
        $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
        $jobApplication->pincode = $experienceGet; // (@$request->pincode) ? @$request->pincode : '';

        $cnt = 0;
        $skillStr = '';
        $skillArr = json_decode($request->skills, true);
        if (! empty($skillArr)) {
            $requiredSkills = array_column($skillArr, 'value');
            GeneralMethod::skillsAddElastic($requiredSkills);
        }

        if (! empty($skillArr)) {
            if (DB::table('job_application_skills')->where('job_application_id', '=', $jobApplication->id)->exists()) {
                DB::table('job_application_skills')->where('job_application_id', '=', $jobApplication->id)->delete();
            }
            foreach ($skillArr as $sval) {

                $values = [
                    'job_application_id' => $jobApplication->id,
                    'skills' => $sval['value'],
                ];
                if (! DB::table('job_application_skills')->where('job_application_id', '=', $jobApplication->id)->where('skills', 'LIKE', '%'.$sval['value'].'%')->exists()) {
                    DB::table('job_application_skills')->insert($values);
                }

                if ($cnt == 0) {
                    $skillStr .= $sval['value'];
                } else {
                    $skillStr .= ' , '.$sval['value'];
                }
                $cnt++;
            }
        }

        // if (!empty($request->current_designation)) {
        // 	if(!DB::table('job_application_skills')->where('job_application_id','=',$jobApplication->id)->where('skills','LIKE', '%'. @$request->current_designation.'%')->exists()){
        // 		DB::table('job_application_skills')->insert(['job_application_id' => $jobApplication->id,'skills' =>  @$request->current_designation]);
        // 		}
        // }
        $jobApplication->skills = $skillStr;

        if ($request->hasFile('photo')) {

            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;

            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $fileUrl = Storage::disk('s3')->url($filePath);
            $jobApplication->photo = $fileUrl;

            // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }
        //  dd("dkd");

        // ── v2 pre-flight: validate resume_session_uuid ────────────────────────────
        $resumeSession = null;
        $finalResumeUrl = null;
        if (config('resumes.features.submit_with_session') && $request->filled('resume_session_uuid')) {
            $resumeSession = ResumeUpload::byUuid($request->input('resume_session_uuid'))
                ->forCompany($company_id)->first();
            if (! $resumeSession) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session not found. Please upload the resume again.']);
            }
            if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload is not yet complete. Please wait for the upload to finish and try again.']);
            }
            if ($resumeSession->linked_candidate_id && (int) $resumeSession->linked_candidate_id !== (int) $jobApplication->id) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session has already been associated with another application. Please upload the resume again.']);
            }
        }
        // ─────────────────────────────────────────────────────────────────────────────

        $clientResumeVersion = (int) $request->input('resume_version', $jobApplication->resume_version ?? 1);
        $currentResumeVersion = (int) ($jobApplication->resume_version ?? 1);
        if ($currentResumeVersion > $clientResumeVersion) {
            return Reply::dataOnly([
                'status' => 'error',
                'msgType' => 'warning',
                'message' => 'Concurrent update detected. This candidate was updated in another session. Please refresh.',
            ]);
        }

        DB::beginTransaction();
        try {

            $jobApplication->save();

            $resumeIntent = $request->input('resume_intent');
            $oldResumeId = $jobApplication->current_resume_id;

            if ($resumeIntent === 'REMOVE') {
                if ($oldResumeId) {
                    DB::table('resume_assets')
                        ->where('id', $oldResumeId)
                        ->where('company_id', $company_id)
                        ->update([
                            'status' => 'REMOVAL_PENDING',
                            'removed_at' => now(),
                        ]);
                }
                $jobApplication->current_resume_id = null;
                $jobApplication->resume = '';
                $jobApplication->resume_version = $currentResumeVersion + 1;
                $jobApplication->save();

                DB::table('candidate_outbox_events')->insert([
                    'company_id' => (int) $company_id,
                    'dedupe_key' => "candidate:{$jobApplication->id}:resume_removed:v{$currentResumeVersion}",
                    'aggregate_type' => 'Candidate',
                    'aggregate_id' => (int) $jobApplication->id,
                    'event_type' => 'resume.removed',
                    'event_version' => 1,
                    'payload' => json_encode([
                        'candidate_id' => (int) $jobApplication->id,
                        'old_resume_id' => $oldResumeId,
                    ]),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($resumeSession) {
                // ── v2 atomic claim ───────────────────────────────────────────────────────
                $claimed = ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Uploaded->value)
                    ->update(['status' => ResumeUploadStatus::Completed->value, 'updated_at' => now()]);
                if ($claimed === 0) {
                    DB::rollBack();

                    return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                        'message' => 'Resume upload session is no longer valid. Please upload again.']);
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
                        'message' => 'Uploaded file not found in storage. Please upload the resume again.']);
                }
                $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);

                if ($oldResumeId) {
                    try {
                        DB::table('resume_assets')
                            ->where('id', $oldResumeId)
                            ->where('company_id', $company_id)
                            ->update([
                                'status' => 'REMOVAL_PENDING',
                                'removed_at' => now(),
                            ]);
                    } catch (\Throwable $re) {
                        \Log::warning('update(): resume_assets update fallback: '.$re->getMessage());
                    }
                }

                $newAssetId = null;
                try {
                    $newAssetId = DB::table('resume_assets')->insertGetId([
                        'company_id' => (int) $company_id,
                        'candidate_id' => (int) $jobApplication->id,
                        'upload_session_id' => $resumeSession->uuid,
                        'object_key' => (string) $finalResumeUrl,
                        'original_filename' => (string) ($request->input('resume_original_name') ?: ($resumeSession->original_filename ?: 'resume.pdf')),
                        'mime_type' => (string) ($resumeSession->mime_type ?: 'application/pdf'),
                        'file_size' => (int) ($resumeSession->file_size_bytes ?: 0),
                        'checksum_sha256' => $resumeSession->sha256_hex ?: null,
                        'version' => 1,
                        'status' => 'ACTIVE',
                        'parser_status' => $request->input('parser_status', 'BYPASSED'),
                        'created_at' => now(),
                        'attached_at' => now(),
                    ]);
                } catch (\Throwable $re) {
                    \Log::warning('update(): resume_assets insert fallback: '.$re->getMessage());
                }

                if ($newAssetId) {
                    $jobApplication->current_resume_id = $newAssetId;
                }
                $jobApplication->resume = $finalResumeUrl;
                $jobApplication->resume_version = $currentResumeVersion + 1;
                $this->resilientSaveJobApplication($jobApplication);

                ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Completed->value)
                    ->update([
                        'linked_candidate_id' => $jobApplication->id,
                        'r2_url' => $finalResumeUrl,
                        'updated_at' => now(),
                    ]);
                UploadSessionAudit::record($resumeSession->id, 'ActionUpdateLinked', [
                    'candidate_id' => $jobApplication->id,
                    'resume_asset_id' => $newAssetId,
                ], $company_id, auth()->id());
            } elseif ($request->hasFile('resume') || ($request->filled('uploaded_file_path') && $request->filled('resume_original_name'))) {
                $companyNameR2 = DB::table('companies')
                    ->where('id', auth()->user()->company_id)
                    ->value('company_name');
                $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;
                $year = date('Y');
                $month = date('m');

                $tempPath = $request->input('uploaded_file_path');
                $full_name = $request->input('resume_original_name');
                if ($tempPath && $full_name) {
                    $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                    $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                    $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                    $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                    $uniqueFileName = "{$jobApplication->id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                    $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                    $fileUrl = "https://file.jero.works/{$r2Path}";

                    UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $jobApplication->id, $companyNameRTwo, $year, $month, $r2Path)->onConnection('emails');

                    if ($oldResumeId) {
                        try {
                            DB::table('resume_assets')
                                ->where('id', $oldResumeId)
                                ->where('company_id', $company_id)
                                ->update([
                                    'status' => 'REMOVAL_PENDING',
                                    'removed_at' => now(),
                                ]);
                        } catch (\Throwable $re) {
                            \Log::warning('update(): resume_assets update fallback: '.$re->getMessage());
                        }
                    }

                    $newAssetId = null;
                    try {
                        $newAssetId = DB::table('resume_assets')->insertGetId([
                            'company_id' => (int) $company_id,
                            'candidate_id' => (int) $jobApplication->id,
                            'upload_session_id' => null,
                            'object_key' => (string) $fileUrl,
                            'original_filename' => (string) $full_name,
                            'mime_type' => 'application/pdf',
                            'file_size' => 0,
                            'checksum_sha256' => null,
                            'version' => 1,
                            'status' => 'ACTIVE',
                            'parser_status' => $request->input('parser_status', 'BYPASSED'),
                            'parser_version' => $request->input('parser_version', null),
                            'created_at' => now(),
                            'attached_at' => now(),
                        ]);
                    } catch (\Throwable $re) {
                        \Log::warning('update(): resume_assets insert fallback (v1): '.$re->getMessage());
                    }

                    if ($newAssetId) {
                        $jobApplication->current_resume_id = $newAssetId;
                    }
                    $jobApplication->resume = $fileUrl;
                    $jobApplication->resume_version = $currentResumeVersion + 1;
                    $this->resilientSaveJobApplication($jobApplication);
                } else {
                    \Log::warning('v1 upload: missing tempPath or filename on form submit — resume not saved', [
                        'company_id' => auth()->user()->company_id,
                        'job_application_id' => $jobApplication->id ?? null,
                    ]);
                }
            } else {
                if (empty($request->resumeCheck)) {
                    $jobApplication->current_resume_id = null;
                    $jobApplication->resume = '';
                }
                $jobApplication->resume_version = $currentResumeVersion + 1;
                $this->resilientSaveJobApplication($jobApplication);
            }

            // Recalculate skill match for ALL jobs this candidate is linked to
            $allWiseCandidates = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $jobApplication->id)
                ->get(['id', 'job_id', 'company_id']);

            $updatedWiseIds = [];
            $primaryMatchPct = null;
            $candSkillsStr = (string) ($jobApplication->skills ?? '');

            foreach ($allWiseCandidates as $wiseRow) {
                $calc = GeneralMethod::calculateSkillMatch((int) $wiseRow->job_id, $candSkillsStr);
                $pct = (int) ($calc['percentage'] ?? 0);
                DB::table('add_job_wise_condidates')->where('id', $wiseRow->id)->update([
                    'skill_match' => $pct,
                    // 'skills'      => $candSkillsStr,
                    'updated_at' => now(),
                ]);
                $updatedWiseIds[] = (int) $wiseRow->id;
                if ((int) ($request->AddJobWiseCondidateRankRow ?? 0) === (int) $wiseRow->id || (int) ($request->job_id ?? 0) === (int) $wiseRow->job_id) {
                    $primaryMatchPct = $pct;
                }
            }
            if ($primaryMatchPct === null && ! empty($request->job_id)) {
                $calc = GeneralMethod::calculateSkillMatch((int) $request->job_id, $candSkillsStr);
                $primaryMatchPct = (int) ($calc['percentage'] ?? 0);
            }

            // Insert outbox event for candidate.updated
            try {
                DB::table('candidate_outbox_events')->insert([
                    'company_id' => (int) $company_id,
                    'dedupe_key' => "candidate:{$jobApplication->id}:updated:v{$jobApplication->resume_version}",
                    'aggregate_type' => 'Candidate',
                    'aggregate_id' => (int) $jobApplication->id,
                    'event_type' => 'candidate.updated',
                    'event_version' => 1,
                    'payload' => json_encode([
                        'candidate_id' => (int) $jobApplication->id,
                        'job_id' => (int) ($request->job_id ?? 0),
                        'company_id' => (int) $company_id,
                        'submitted_by' => (int) auth()->id(),
                    ]),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $oe) {
                \Log::warning('update(): candidate_outbox_events insert fallback: '.$oe->getMessage());
            }

            // Commit MySQL transaction — pure local database writes with zero remote network calls
            DB::commit();

            // ── Post-Commit Operations (0% DB lock holding time) ──────────────────────
            // Primary ES candidate update — sync every linked job-wise row so searches and trackers update immediately
            foreach ($updatedWiseIds as $wId) {
                GeneralMethod::JobApplicationsAddEditUpload($wId);
            }
            $addJobWiseCandidateId = (int) ($request->AddJobWiseCondidateRankRow ?? 0);
            if (empty($updatedWiseIds)) {
                $addJobWiseCandidateId = (int) ($request->AddJobWiseCondidateRankRow ?? 0);
                if ($addJobWiseCandidateId > 0) {
                    GeneralMethod::JobApplicationsAddEditUpload($addJobWiseCandidateId);
                }
            }

            // Explicit Elasticsearch refresh to guarantee zero stale data on read
            try {
                if ($this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
                    $this->elasticService->getClient()->indices()->refresh(['index' => ELASTIC_CANDIDATES]);
                }
            } catch (\Throwable $e) {
                \Log::warning('ELASTIC_CANDIDATES refresh in update() failed: '.$e->getMessage());
            }

            // Post-commit cache clean
            $cachekeys = [
                'allcvtracker_version_user_'.$this->user->id,
                'allapptracker_version_user_'.$this->user->id,
                'allclientapp_version_user_'.$this->user->id,
                'candidateDetails_version_user_'.$this->user->id,
                'candidate:'.$jobApplication->id,
                'candidate:'.$jobApplication->id.':resume',
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            // Bust candidate drawer cache across all slugs and associated job-wise IDs
            $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
            $wiseIds = ! empty($updatedWiseIds) ? $updatedWiseIds : DB::table('add_job_wise_condidates')->where('job_application_id', $jobApplication->id)->pluck('id')->toArray();
            $targetIds = array_unique(array_merge([(int) $jobApplication->id], $wiseIds));
            $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];

            foreach ($targetIds as $tid) {
                foreach ($slugs as $slug) {
                    cache()->forget('tenant:'.$company_id.':candidate_details:'.$tid.':'.md5($slug).':view:'.$drawerViewVersion);
                }
            }

            try {
                \App\Events\CandidateDrawerUpdated::dispatch(
                    (int) $company_id,
                    (int) $jobApplication->id,
                    time(),
                    'candidate_updated',
                    array_map('intval', $wiseIds),
                    $primaryMatchPct,
                    $candSkillsStr,
                    (string) ($jobApplication->recruiter_note ?? ''),
                    null,
                    null,
                    (int) ($request->job_id ?? 0),
                    (string) ($jobApplication->resume ?? '')
                );
            } catch (\Throwable $e) {
                \Log::warning('CandidateDrawerUpdated dispatch in update() failed: '.$e->getMessage());
            }

            // Dispatch ProcessOutboxEventJob to dedicated queue
            try {
                ProcessOutboxEventJob::dispatch()->onQueue('candidate-index');
            } catch (\Throwable $e) {
                \Log::warning('update(): ProcessOutboxEventJob dispatch warning: '.$e->getMessage());
            }

            // ── V2 async candidate update job — dispatched post-commit ─────────────
            // Handles secondary ES indexing (skills experience, answers, job questions, bench candidate indexes).
            $requestData = array_filter(
                $request->except(['resume', 'photo', 'documents', 'file', 'files']),
                fn ($val) => ! ($val instanceof \Illuminate\Http\UploadedFile)
            );

            ProcessCandidateUpdateJob::dispatch(
                (int) $jobApplication->id,
                $addJobWiseCandidateId,
                (int) $request->job_id,
                (int) $company_id,
                $requestData
            )->onConnection('emails');
            // ─────────────────────────────────────────────────────────────────────────────

            $currentPage = @$request->currentPage;
            $currentSlug = @$request->currentSlug;

            $relationget = GeneralMethod::getrRcommendationRelationData(auth()->user()->company_id, $request->job_id);
            $recommendation_id = $relationget->to_company_id ?? null;
            $to_company_id = $relationget->to_company_id ?? null;

            // Mirror store() response shape so the JS hardened success gate
            // (response.status===success && response.candidate_id && response.url) on create.blade.php passes.
            $rawJobId = GeneralMethod::value_decrypt($request->job_id);
            if ($currentSlug == 'cvtracker') {
                $redirectUrl = route('service_company.cvtracker.index');
            } elseif (empty($rawJobId)) {
                $redirectUrl = route('service_company.cvtracker.allcandidates');
            } elseif ($recommendation_id > 0 && @$to_company_id == auth()->user()->company_id) {
                $redirectUrl = route('service_company.apptracker.clientapp', [
                    'id' => GeneralMethod::value_encrypt($rawJobId),
                    'mstatus_id' => 'all',
                ]);
            } else {
                $redirectUrl = route('service_company.apptracker.index', [
                    'id' => GeneralMethod::value_encrypt($rawJobId),
                    'mstatus_id' => 'all',
                ]);
            }
            DB::commit();

            return Reply::successWithData(__('messages.createdSuccessfully'), [
                'action' => 'redirect',
                'url' => $redirectUrl,
                'candidate_id' => (int) $jobApplication->id,
                'resume' => (string) ($jobApplication->resume ?? ''),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('update() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Update failed. Please try again.']);
        }

    }

    public function formatInterviewDateTime($date, $time, $defaultTimeFormat)
    {
        if (empty($date) || empty($time)) {
            return null; // if either is missing, return null
        }

        $eventStartTime = $date.' '.$time;

        if ($defaultTimeFormat == 1) {
            return date('Y-m-d H:i:s', strtotime($eventStartTime)); // 12-hour format
        } else {
            return date('Y-m-d H:i:s', strtotime($eventStartTime));   // 24-hour format
        }
    }

    public function createSchedule(Request $request, $id)
    {
        // dd("kf");

        abort_if(Gate::denies('add_schedule'), 403);

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        // 		$this->candidates = JobApplication::all(); // 02/05/2024 comment by rajveer
        // $this->users 				=	User::with('company')->company()->get();
        $this->users = User::with('company')->company()->where('users.type', '<>', 1)->get();

        $this->datadate = $request->input('datadate');  // null if not set
        $this->datatime = $request->input('datatime');  // null if not set
        $this->datatmode = $request->input('datatmode', 2); // null if not set;

        // dd($this->datadate,$this->datatime,$this->datatmode);

        $this->scheduleDate = $request->date;
        // $this->currentApplicant = JobApplication::findOrFail($id);
        $this->interview_node = GeneralMethod::interview_mode();
        $this->application_id = $this->interview_id = $this->feedback_id = $this->interview_detail = '';
        $this->application_id = $id;
        // dd($this->application_id);
        $this->interview_detail = [];
        $this->application = JobApplication::withoutGlobalScopes()->select(
            'job_applications.*',
            'clients.name as client_name',
            'clients.self_company',
            'jobs.title as job_title',
            'client_location.city',
            'jobs.company_id',
            'master_status.name as master_status_name',
            'master_status.css_style as master_status_style',
            'users.email as submitted_by_email',
            'users.name as submitted_by_name',
            'users.company_id as submitted_by_company_id',
            'qualification_master.name as qualification_name',
            'state_list.state as state_name',
            'users.name as posted_by',
            'users.image as posted_by_image',
            'add_job_wise_condidates.master_status_id',
            'add_job_wise_condidates.child_status_id',
            'add_job_wise_condidates.id as wiseId',
            'add_job_wise_condidates.job_id as jobId',
            'add_job_wise_condidates.thirdparty_company_id as ClientComID',
            'jobs.client_id'
        )
            ->leftjoin('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftjoin('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftjoin('users', 'users.id', '=', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('client_location_jobs', 'client_location_jobs.job_id', 'jobs.id')
            ->leftjoin('client_location', 'client_location.id', 'client_location_jobs.client_location_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state')
            ->where(function ($query) use ($id) {
                $query->where('add_job_wise_condidates.id', $id)
                    ->orWhere('job_applications.id', $id);
            })
            ->groupBy('job_applications.id')->first();

        if (! $this->application) {
            $this->application = JobApplication::find($this->application_id);
        }

        if (! $this->application) {
            return Reply::error('Candidate application not found.');
        }

        $i = 0;
        if (($this->application->condidate_type ?? '') == 'bench_condidate') {

            $bench_assign = DB::table('bench_assign')->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->select('users.id', 'users.email', 'users.name')
                ->where('bench_assign.bench_applications_id', $this->application->id)
                ->get()->toArray();
            $commonCcArr = [];
            foreach ($bench_assign as $assign) {
                $commonCcArr[$i] = $assign->email;
                $i++;
            }
            $this->application->email = implode(',', $commonCcArr);
            // dd($aval->candidate_id,$commonCcArr);
        }

        $country = '';
        $this->row_country_name = '';

        if (empty($this->application->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->application->country)) {
                if (is_numeric($this->application->country)) {
                    $country_name_Arr = GeneralMethod::getCountryName($this->application->country);
                    foreach ($country_name_Arr as $cval) {
                        $this->row_country_name = ucwords($cval['name']);
                    }
                }
            }
        }

        $this->row_state_name = '';
        if (empty($this->application->state)) {
            $this->row_state_name = '----';
        } else {
            if (($this->application->country ?? null) == 101) {
                $this->row_state_name = $this->application->state_name ?? '';
            } else {
                $state = $this->application->state;
                if (is_numeric($this->application->country) && is_numeric($state)) {
                    if ($state > 0) {
                        $state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $this->row_state_name = ucwords($sval['name']);
                        }
                    }
                }
            }
        }

        $this->row_city_name = '';
        if (($this->application->country ?? null) == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id ?? 0)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = $this->application->city ?? '';
        }

        $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = '';
        $current_ctc_cur = $currencySymbole[$this->application->current_ctc_cur ?? 0] ?? '';
        $current_ectc_cur = $currencySymbole[$this->application->current_ectc_cur ?? 0] ?? '';
        if (($this->application->condidate_type ?? '') == 'bench_condidate') {
            $current_ectc_cur = $currencySymbole[$this->application->bill_rate_currency ?? 0] ?? '';
        }
        $ctc = GeneralMethod::thousandsCurrencyFormat($this->application->ctc ?? 0);
        $ectc = GeneralMethod::thousandsCurrencyFormat($this->application->ectc ?? 0);
        if (($this->application->condidate_type ?? '') == 'bench_condidate') {
            $ectc = GeneralMethod::thousandsCurrencyFormat($this->application->bill_rate_amount ?? 0);
        }
        $current_ctc_mode = substr($salaryMode[$this->application->current_ctc_mode ?? 0] ?? '', 0, 1);
        $current_ectc_mode = substr($salaryMode[$this->application->current_ectc_mode ?? 0] ?? '', 0, 1);
        if (($this->application->condidate_type ?? '') == 'bench_condidate') {
            $current_ectc_mode = substr($salaryMode[$this->application->bill_rate_mode ?? 0] ?? '', 0, 1);
        }
        $this->current_ctc = @$current_ctc_cur.@$ctc.'/'.@$current_ctc_mode;
        $this->expected_ctc = @$current_ectc_cur.@$ectc.'/'.@$current_ectc_mode;

        $this->child_status_name = '';
        $submitted_by_company_id = $this->application->submitted_by_company_id ?? $this->application->company_id ?? null;
        $submitted_by_row = $submitted_by_company_id ? DB::table('companies')->select('*')->where('companies.id', $submitted_by_company_id)->first() : null;
        $this->submitted_by_company = $submitted_by_row->company_name ?? '';

        if (! empty($this->application->child_status_id)) {
            $child_status_by_row = DB::table('candidate_status')->select('*')->where('candidate_status.id', $this->application->child_status_id)->first();
            $this->child_status_name = $child_status_by_row->name ?? '';
        }

        $this->interviewStatus = DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        $jobId = $this->application->jobId ?? $this->application->job_id ?? null;
        $this->client_contact_info = $jobId ? DB::table('jobs')->join('client_contact_info_jobs', 'client_contact_info_jobs.job_id', '=', 'jobs.id')->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', '=', $jobId)->orderBy('client_contact_info.contact_person_name', 'ASC')->first() : null;

        $this->job_id = $jobId;
        $this->interview_mode = DB::table('interview_mode')->select('*')->orderBy('interview_mode.id', 'ASC')->get();
        // interview_mode_lavel
        $this->email_template = DB::table('th_email_template')->select('*')->where('th_email_template.status', 1)->where('th_email_template.temptype', 3)->orderBy('th_email_template.e_temp_id', 'ASC')->get();
        $this->from_email = $this->user->email;
        $this->cc_email = $this->user->email;
        if (! empty($this->application->submitted_by_email) && $this->application->submitted_by_email != $this->user->email) {
            $this->cc_email = $this->cc_email.' , '.$this->application->submitted_by_email;
        }

        $client_contact_arr = $jobId ? DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $jobId)->get() : [];
        if (! empty($client_contact_arr)) {
            foreach ($client_contact_arr as $ccData) {
                if (! empty($ccData->contact_person_email)) {
                    $this->cc_email = $this->cc_email.' , '.$ccData->contact_person_email;
                }
            }
        }

        $assignDataone = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', auth()->user()->company_id)
            ->where('users.status', 'active')
            ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->groupBy('users.id')->get()->toArray();

        $assignDatatwo = $jobId ? DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
            ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->where('job_assign.job_id', $jobId)->get()->toArray() : [];

        $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
            return $a->id <=> $b->id; // Compare by 'id' field
        });

        // dd($mergedData);
        $clientId = $jobId ? Job::where('jobs.id', $jobId)
            ->join('clients', 'clients.id', '=', 'jobs.client_id')->pluck('jobs.client_id')->toArray() : [];

        $assignDataclient = ! empty($clientId) ? DB::table('clients')
            ->leftjoin('companies', 'companies.id', 'clients.company_id')
            ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
            ->whereIn('clients.id', $clientId)->get()->toArray() : [];

        $companyOrClientName = GeneralMethod::companyOrClientName($this->application->client_id ?? null);
        $this->selfCompany = ($companyOrClientName->selfCompany) ? $companyOrClientName->selfCompany : '';
        $this->companyTemp = ($companyOrClientName->company) ? $companyOrClientName->company : '';
        $this->clientTemp = ($companyOrClientName->client) ? $companyOrClientName->client : '';

        // $assignDataFinal = array_udiff($mergedData, $assignDataclient, function ($a, $b) {
        // 	return $a->id <=> $b->id; // Compare by 'id' field
        // });
        // dd($assignDataFinal);

        $assignDataFinal = array_merge($assignDataclient, $mergedData);

        // dd($assignDataFinal);

        $this->cc_email_assignData = $assignDataFinal;
        $this->update = false;
        $this->savesend = true;
        if ($request->relint) {
            $interviewSchedule = InterviewSchedule::join('updatetimeline', 'updatetimeline.interview_schedules_id', 'interview_schedules.id')->select('interview_schedules.*', 'updatetimeline.interview_mention_link')->where('interview_schedules.id', $request->relint)->first();
            // dd($interviewSchedule);
            $this->scheduledate = $interviewSchedule->schedule_date;
            $this->emailtemplateset = $interviewSchedule->email_template;
            $this->interviewmode = $interviewSchedule->interview_mode;
            $this->interviewmodelavel = $interviewSchedule->interview_mode_lavel;
            $this->interviewers = $interviewSchedule->interviewers;
            $this->interviewmentionlink = $interviewSchedule->interview_mention_link;
            $this->update = true;
            $this->savesend = false;

        }
        $this->timezone = DB::table('master_timezone')->select('*')->orderByDesc('master_timezone.priority')->get();

        // Real, company-scoped interview round master data (App\InterviewProcess model
        // class doesn't exist, so the interview_processes table is read directly here).
        $this->interview_processes = DB::table('interview_processes')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name', 'ASC')
            ->get();

        $this->reschedule = @$_REQUEST['reschedule'];
        $view = view('service_company.job-applications.interview-create', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function contactPersionWithTeam($id)
    {

        $client_id = $_REQUEST['client_id'];
        $job_id = $_REQUEST['job_id'];
        $this->panel = $_REQUEST['panel'];
        $this->self_company = $_REQUEST['self_company'];
        $this->client_name = $_REQUEST['client_name'];
        $this->cc_email = array_map('trim', explode(',', $_REQUEST['cc_email']));

        $assignDataone = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', auth()->user()->company_id)
            ->where('users.status', 'active')
            ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')->groupBy('users.id')->get()->toArray();

        $assignDatatwo = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'job_assign.company_id')
            ->select('users.id', 'users.email', 'users.name', 'users_profile.designation as display_name', 'users.working_status', 'companies.company_name')
            ->where('users.status', 'active')
            ->where('job_assign.job_id', $job_id)->get()->toArray();

        // $mergedData = array_udiff($assignDataone, $assignDatatwo, function ($a, $b) {
        // 	return $a->id <=> $b->id; // Compare by 'id' field
        // });
        $mergedData = collect(array_merge($assignDataone, $assignDatatwo))->unique('id')->values()->toArray();

        $assignDataoneEmail = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', auth()->user()->company_id)
            ->where('users.status', 'inactive')
            ->pluck('users.email')->toArray();

        DB::table('client_contact_info')->whereIn('contact_person_email', $assignDataoneEmail)->where('client_id', '=', $client_id)->delete();

        $assignDataoneActive = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->where('users.company_id', '=', auth()->user()->company_id)
            ->where('users.status', 'active')
            ->pluck('users.email')->toArray();

        $assignDataclient = DB::table('clients')
            ->leftjoin('companies', 'companies.id', 'clients.company_id')
            ->join('client_contact_info', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('client_contact_info.id', 'client_contact_info.contact_person_email as email', 'client_contact_info.contact_person_name as name', 'client_contact_info.contact_person_designation as display_name', 'clients.name as company_name')
            ->whereNotIn('contact_person_email', $assignDataoneActive)
            ->where('clients.id', $client_id)
            ->get()->toArray();

        $assignDataFinal = collect(array_merge($assignDataclient, $mergedData))->unique('id')->values()->toArray();
        // $assignDataFinal = array_merge($assignDataclient,$mergedData);
        $this->cc_email_assignData = $assignDataFinal;
        // Same internal/client split already computed above ($mergedData / $assignDataclient),
        // exposed separately so the picker can show them under distinct Internal/Client tabs
        // (same source-split pattern as the offer-approval picker in create-offer.blade.php).
        $this->picker_internal = $mergedData;
        $this->picker_client = $assignDataclient;

        $view = view('service_company.job-applications.contact-persion-with-team', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function getinterviewchild(Request $request)
    {
        $id = $job_id = '';
        $id = @$request->id;
        $job_id = @$request->job_id;
        // dd($job_id);
        $Data = DB::table('interview_mode_lavel')->select('*')->where('interview_mode_id', $id)->get();

        $companyData = $this->user->company;

        // dd($Data);

        if (! empty($Data)) {
            if ($id == 1) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 2) {
                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.','.$job_id.');">
													<option value="">Select Mode</option>';
                foreach ($Data as $cval) {

                    if ($cval->id == 2) {  // Google Meeting
                        // if ($companyData->google_status == 1) {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        // }
                    } elseif ($cval->id == 3) {  // Zoom Meeting
                        // if ($companyData->zoom_status == 1) {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        // }
                    } elseif ($cval->id == 5) {  // Microsoft Meeting
                        // if ($companyData->microsoft_status == 1) {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                        // }
                    } else {
                        $html .= '<option value= "'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                    }

                    $flag = 1;
                }
                $html .= '</select>';
            } elseif ($id == 3) {

                $Data = DB::table('client_location')
                    ->select('client_location.id', 'client_location.address', 'client_location.city', 'client_location.postcode', 'city_list.city as cityname', 'state_list.state', 'countries.country_name')
                    ->join('client_location_jobs', 'client_location_jobs.client_location_id', '=', 'client_location.id')
                    ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                    ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->where('client_location_jobs.job_id', $job_id)
                    ->orderBy('client_location.id', 'ASC')
                    ->get();

                $html = '<select class="form-control" name="interview_mode_lavel" id="interview_mode_lavels" data-style="" onchange="getVideoMeetingUrl(this.value, '.$id.');">
													<option value="">Select Address</option>';
                foreach ($Data as $cval) {
                    $cityName = @$cval->city;
                    if (empty(@$cval->city)) {
                        $cityName = @$cval->cityname;
                    }
                    $country_name = '';
                    if (! empty(@$cval->country_name)) {
                        $country_name = '- '.@$cval->country_name;
                    }
                    $stateName = '';
                    if (! empty(@$cval->state)) {
                        $stateName = ', '.@$cval->state;
                    }
                    $html .= '<option value= "'.@$cval->id.'">'.ucfirst(@$cval->address).'  '.ucfirst(@$cityName).'  - '.@$cval->postcode.' '.@$stateName.' '.@$country_name.'</option>';
                    $flag = 1;
                }
                $html .= '<<option value="0">Other address</option>';
                $html .= '</select>';
            }
        } else {
            $html = '&nbsp;-&nbsp;';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $html]);
    }

    public function recruiter($id)
    {

        $panellist = '';
        $panellist = $_REQUEST['panellist'];
        $this->panellistArr = explode(',', $panellist);

        // echo '<pre>'; print_r($panellistArr);

        $company_id = auth()->user()->company_id;
        $this->company = DB::table('companies')->select('*')->where('id', $company_id)->first();

        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users_profile.designation', 'roles.display_name', 'users.working_status')
            ->where('type', 0)
            ->where('users.company_id', $company_id)
            ->where('status', 'active')
            ->get();

        // dd($this->team);

        $view = view('service_company.job-applications.recruiter', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function accessEmailTemplate(Request $request)
    {

        $template_id = $application_id = $template_subject = $job_title = $client_name = $job_id = '';
        $email = $template_description = $full_name = $cc_email = $submitted_by_email = $to_email = '';
        $template_id = @$request->template_id;
        $application_id = @$request->application_id;
        $interviewTime = @$request->interviewTime;
        $timezone = @$request->timezone;
        $email = $this->user->email;

        // echo '<pre>'; print_r($this->user->email); exit;
        // 		$applicationRow = DB::table('job_applications')->join('jobs', 'jobs.id', '=', 'job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')->join('users', 'users.id', '=', 'job_applications.submitted_by')->select('jobs.id as job_id', 'jobs.title', 'clients.name as client_name', 'job_applications.full_name', 'users.email as submitted_by_email', 'job_applications.email as to_email')->where('job_applications.id', $application_id)->first();

        // 		 dd(auth()->user()->id);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'size' => 1,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['jobId' => (int) @$request->jobId]],
                            ['term' => ['candidate_id' => $application_id]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        $job_applications = null;

        if (! empty($response['hits']['hits'])) {
            $job_applications = (object) $response['hits']['hits'][0]['_source'];
        }

        $params = [
            'index' => ELASTIC_JOBS,
            'id' => (int) @$request->jobId,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $this->job = (object) $response['_source'];

        $client_city_jobs = $this->job->client_city_jobs;

        $firstCity = collect($client_city_jobs)->first();

        $client_location = $this->job->client_location;

        $firstLocation = collect($client_location)->first();

        $addressName = $firstLocation['address'] ?? null;
        $cityName = $firstCity['city_name'] ?? null;
        $city = mb_substr(@$cityName, 0, 21);
        $countryName = $firstCity['country_name'] ?? null;
        $country_name = mb_substr(@$countryName, 0, 6);

        $location = ucwords(@$addressName).' | '.ucwords(@$city).' | '.ucwords(@$country_name);

        $params = [
            'index' => ELASTIC_USERS_LIST,
            'id' => (int) @$job_applications->submitted_by,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $submitted_by = (object) $response['_source'];

        $job_title = '('.$job_applications->title.')';
        $client_name = '('.$job_applications->client_name.')';
        $full_name = $job_applications->full_name;
        $job_id = @$job_applications->jobId;

        $dataRow = DB::table('th_email_template')->select('*')->where('th_email_template.e_temp_id', $template_id)->first();

        $interview_questions = DB::table('interview_questions')->select('*')->where('job_id', $job_id)->get();

        $job = Job::withoutGlobalScopes()->find($request->jobId);

        $companyOrClientName = GeneralMethod::companyOrClientName($job_applications->client_id);

        $template_subject = str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{CLIENT_NAME}]'], [$companyOrClientName->company, $job_title, $companyOrClientName->client], $dataRow->template_subject);

        $template_subject = str_replace('[{JOB_TITLE}]', $job_title, $template_subject);
        $template_subject = str_replace('[{COMPANY_NAME}]', $client_name, $template_subject);
        $InterviewMode = '';
        if (@$request->interview_mode == 1) {
            $InterviewMode = 'Telephonic';
        }
        if (@$request->interview_mode == 2) {
            $videoUrl = @$request->videoUrl;
            $InterviewMode = 'Video';
        }
        if (@$request->interview_mode == 3) {
            $videoUrl = @$request->videoUrl;
            $InterviewMode = 'F2F';
        }
        $dateTime = date('d-M-Y', strtotime($request->interviewDate)).' '.$interviewTime.' ('.$timezone.')';

        // if($dataRow->tempForInterview==1){
        //      $template_subject =	"Interview Confirmation || $job->title || $companyOrClientName->company | $dateTime | $InterviewMode Interview";
        // }elseif($dataRow->tempForInterview==2){
        //      $template_subject =	"Interview Confirmation || $job->title || $companyOrClientName->client | $dateTime | $InterviewMode Interview";
        // }elseif($dataRow->tempForInterview==3){
        //      $template_subject =	"Interview Confirmation ||  $job->title || $companyOrClientName->client | Payroll $companyOrClientName->company";
        // }

        $template_description = $dataRow->template_description;
        // $template_description = str_replace("[{JOB_TITLE}]", $job_title, $template_description);
        // $template_description = str_replace("[{COMPANY_NAME}]", $client_name, $template_description);
        // $template_description = str_replace("[{CANDIDATE_NAME}]", $full_name, $template_description);

        $jobid = @$job_applications->jobId;

        $videoUrl = '';
        $videoUrlInter = '';
        $text_set = '';
        $interview_level = '';
        if (! empty(@$request->interview_mode)) {
            if (@$request->interview_mode == 1) {
                $videoUrl = '**Telephonic** : ...........';
                $otherDetails = '';
                $videoUrlInter = '';
                $text_set = '';
                $interview_level = 'Interview Telephonic';
            }
            if (@$request->interview_mode == 2) {
                $videoUrl = @$request->videoUrl;
                $videoUrlInter = '<a href="'.$videoUrl.'" style="cursor: pointer;"> '.@$request->videoUrl.'</a>';
                $videoUrl = '<a href="'.$videoUrl.'" style="cursor: pointer;"> Click Here to Join the Interview </a><br>';
                $otherDetails = '<p>**Interview Instructions for Video Interview:**</p>
				<p>&nbsp;</p>
				<p>1. **Join the interview 5 minutes before the scheduled time.**</p>
				<p>2. **Ensure you have a stable internet connection.**</p>
				<p>3. **Use a laptop for the interview; mobile devices are not recommended.**</p>
				<p>4. **Avoid using proxies during the interview, as this will result in immediate disqualification.**</p>
				<p>&nbsp;</p>';
                $text_set = 'Or copy and paste the link below into your browser';
                $interview_level = 'Interview Link';
            }
            if (@$request->interview_mode == 3) {
                $videoUrl = @$request->videoUrl;
                $videoUrlInter = '';
                $otherDetails = '<p>**Interview Instructions for Face-to-Face Interview:**</p>
				<p>&nbsp;</p>
				<p>1. **Arrive at the interview location 30 minutes before the scheduled time.**</p>
				<p>2. **Turn off your mobile phone during the interview.**</p>
				<p>3. **Review the job description and prepare for key skills relevant to the position.**</p><p>&nbsp;</p>';
                $text_set = '';
                $interview_level = 'Interview Address';
            }
        }

        $interview_ques = '';
        if (count($interview_questions) > 0) {
            $interview_ques .= '<h3><strong>Job Interview Questions</strong></h3>';
            $interview_ques .= '<ol>';
            foreach ($interview_questions as $row) {
                $interview_ques .= '<li>'.$row->questions.'</i>';
            }
            $interview_ques .= '</ol><hr />';

        }
        // dd($interview_ques);

        $template_description = str_replace('[{VIDEOURL}]', $videoUrl, $template_description);
        if ($dataRow->tempForInterview == 1) {
            $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{INTERVIEW_MODE}]', '[{INTERVIEW_DATE_TIME}]', '[{WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_DESCRIPTION}]', '[{INTERVIEW_LINK}]', '[{OTHER_DESCRIPTION}]', '[{WORK_LOCATION}]', '[{HR_NAME}]', '[{INTERVIEW_LINK_URL}]', '[{INTERVIEW_LEVEL}]', '[{TEXT_SET}]', '[{INTERVIEW_QUESTIONS}]'], [$full_name, $companyOrClientName->company, $job_title, $job_id, $InterviewMode, $dateTime, $companyOrClientName->company_website, $job->job_description, $companyOrClientName->company_description, $videoUrl, $otherDetails, $location,	auth()->user()->name, $videoUrlInter, $interview_level, $text_set, $interview_ques], $template_description);
        } elseif ($dataRow->tempForInterview == 2) {
            $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{INTERVIEW_MODE}]', '[{INTERVIEW_DATE_TIME}]', '[{WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_DESCRIPTION}]', '[{CLIENT_DESCRIPTION}]', '[{INTERVIEW_LINK}]', '[{OTHER_DESCRIPTION}]', '[{WORK_LOCATION}]', '[{HR_NAME}]', '[{COMPANY_NAME}]', '[{INTERVIEW_LINK_URL}]', '[{INTERVIEW_LEVEL}]', '[{TEXT_SET}]', '[{INTERVIEW_QUESTIONS}]'], [$full_name, $companyOrClientName->client, $job_title, $job_id, $InterviewMode, $dateTime, $companyOrClientName->client_website, $job->job_description, $companyOrClientName->company_description, $companyOrClientName->client_description, $videoUrl, $otherDetails, $location, auth()->user()->name, $companyOrClientName->company, $videoUrlInter, $interview_level, $text_set, $interview_ques], $template_description);
        } elseif ($dataRow->tempForInterview == 3) {
            $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{INTERVIEW_MODE}]', '[{INTERVIEW_DATE_TIME}]', '[{WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{CLIENT_DESCRIPTION}]', '[{INTERVIEW_LINK}]', '[{OTHER_DESCRIPTION}]', '[{WORK_LOCATION}]', '[{HR_NAME}]', '[{INTERVIEW_LINK_URL}]', '[{INTERVIEW_LEVEL}]', '[{TEXT_SET}]', '[{INTERVIEW_QUESTIONS}]'], [$full_name, $companyOrClientName->client, $job_title, $job_id, $InterviewMode, $dateTime, $companyOrClientName->client_website, $job->job_description, $companyOrClientName->client_description, $videoUrl, $otherDetails, $location, auth()->user()->name, $videoUrlInter, $interview_level, $text_set, $interview_ques], $template_description);

            $template_description = str_replace(['[{CANDIDATE_NAME}]', '[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{INTERVIEW_MODE}]', '[{INTERVIEW_DATE_TIME}]', '[{WEBSITE}]', '[{JOB_DESCRIPTION}]', '[{COMPANY_DESCRIPTION}]', '[{INTERVIEW_LINK}]', '[{OTHER_DESCRIPTION}]', '[{INTERVIEW_LINK_URL}]', '[{INTERVIEW_LEVEL}]', '[{TEXT_SET}]', '[{INTERVIEW_QUESTIONS}]'], [$full_name, $companyOrClientName->company, $job_title, $job_id, $InterviewMode, $dateTime, $companyOrClientName->company_website, $job->job_description, $companyOrClientName->company_description, $videoUrl, $otherDetails, $videoUrlInter, $interview_level, $text_set, $interview_ques], $template_description);

        }

        $to_email = $job_applications->email;
        $cc_email = $this->user->email;
        if ($submitted_by->email != $this->user->email) {
            $cc_email = $cc_email.' , '.$submitted_by->email;
        }
        $client_contact_arr = DB::table('client_contact_info')->join('client_contact_info_jobs', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email')->where('client_contact_info_jobs.job_id', $job_id)->get();
        if (! empty($client_contact_arr)) {
            foreach ($client_contact_arr as $ccData) {
                if (! empty($ccData->contact_person_email)) {
                    $cc_email = $cc_email.' , '.$ccData->contact_person_email;
                }
            }
        }

        return Reply::dataOnly(['status' => 'success', 'to_email' => $to_email, 'cc_email' => $cc_email, 'from_email' => $email, 'template_subject' => $template_subject, 'template_description' => $template_description]);

    }

    public function refreshZoomAccessToken($refreshToken)
    {
        try {
            $zoomsettings = DB::table('zoomsettings')->select('zoomsettings.*')->first();
            $clientId = $zoomsettings->client_id;
            $clientSecret = $zoomsettings->client_secret;

            $response = Http::asForm()->withHeaders([
                'Authorization' => 'Basic '.base64_encode($clientId.':'.$clientSecret),
            ])->post('https://zoom.us/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {
                $newAccessToken = $response->json()['access_token'];
                $newRefreshToken = $response->json()['refresh_token'];
                $newExpiresIn = $response->json()['expires_in'];

                // Store the new tokens securely
                session([
                    'zoom_access_token' => $newAccessToken,
                    'zoom_refresh_token' => $newRefreshToken,
                    'zoom_token_expires_at' => now()->addSeconds($newExpiresIn),
                ]);

                return $newAccessToken;
            } else {
                // Handle error
                return response()->json(['error' => 'Failed to refresh access token'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while refreshing the access token'], 500);
        }
    }

    private function getZoomAccessToken()
    {
        $refreshToken = DB::table('zoomsettings')->where('id', '=', 1)->first();

        $clientId = $refreshToken->client_id;
        $clientSecret = $refreshToken->client_secret;
        $accountId = $refreshToken->redirect_uri;

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Could not retrieve Zoom access token: '.$response->body());
    }

    public function getGoogleVideoUrl(Request $request)
    {

        // $client = new Google_Client();
        // $client->setAuthConfig(storage_path('jeroworksproject-f09c7549cf35.json')); // Use Service Account JSON
        // $client->addScope(Google_Service_Calendar::CALENDAR);
        // $client->setSubject('jeroworksproject@jeroworksproject.iam.gserviceaccount.com'); // Use your Google Calendar email

        // $service = new Google_Service_Calendar($client);

        // $startDate = date('c', strtotime($request->interviewDate . ' ' . $request->interviewTime));
        // $endDate = date('c', strtotime($request->interviewDate . ' ' . $request->interviewTime . ' +1 hour'));

        // $attendees = [
        // 	['email' => 'vibhu2103m@gmail.com'],
        // 	['email' => 'rajveer@4bell.com'],
        // ];

        // $event = new Google_Service_Calendar_Event([
        // 	'summary' => 'Interview Schedule',
        // 	'start' => ['dateTime' => $startDate, 'timeZone' => 'Asia/Kolkata'],
        // 	'end' => ['dateTime' => $endDate, 'timeZone' => 'Asia/Kolkata'],
        // 	'conferenceData' => [
        // 		'createRequest' => [
        // 			'requestId' => uniqid(),
        // 			'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
        // 		],
        // 	],
        // 	'attendees' => $attendees,
        // ]);

        // $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

        // return response()->json(['status' => 'success', 'meet_link' => $event->getHangoutLink()]);

        if ((int) $request->currentId == 3) {

            try {
                $interviewDate = @$request->interviewDate;
                $interviewTime = @$request->interviewTime;
                $defaultTimezone = date_default_timezone_get();

                $eventStartTime = $interviewDate.' '.$interviewTime;

                $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
                if ($defaultTimeFormat == 1) {
                    $formattedStartTime = Carbon::createFromFormat('d-m-Y h:i A', $eventStartTime)->format('Y-m-d\TH:i');

                    $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $formattedStartTime);

                } else {

                    $formattedStartTime = Carbon::createFromFormat('d-m-Y H:i', $eventStartTime)->format('Y-m-d\TH:i');
                    $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i', $eventStartTime);

                }

                $company_id = auth()->user()->company_id;

                $refreshToken = DB::table('zoomsettings')->select('zoom_access_token')->first()->zoom_access_token;

                // $newAccessToken = $this->refreshZoomAccessToken($refreshToken);
                $accessToken = $this->getZoomAccessToken();
                // dd($newAccessToken);

                $response = Http::withToken($accessToken)
                    ->post('https://api.zoom.us/v2/users/me/meetings', [
                        'topic' => 'Interview Schedule',
                        'type' => 2,
                        'start_time' => $startDateTime,
                        'duration' => 60,
                        'timezone' => 'Asia/Kolkata',
                        'agenda' => 'Test Zoom meeting using Server-to-Server OAuth',
                        'settings' => [
                            'host_video' => true,
                            'participant_video' => true,
                            'join_before_host' => true,
                            'mute_upon_entry' => true,
                            'approval_type' => 1,
                        ],
                    ]);

                // $response = Http::withToken($newAccessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
                // 	'topic'      => "Interview Schedule",
                // 	'type'       => 2, // Scheduled meeting
                // 	'start_time' => $startDateTime, // Static start time
                // 	'duration'   => 60, // Static duration
                // 	'timezone'   => 'UTC',
                // 	'settings'   => [
                // 		'join_before_host' => true, // Allow participants to join before the host
                // 	],
                // ]);

                if ($response->successful()) {
                    $meetingData = $response->json();
                    $joinUrl = $meetingData['join_url']; // Get the meeting join URL

                    // dd("1",$joinUrl, $response->status(),$response->body());
                    return Reply::dataOnly(['status' => 'success', 'getHangoutLink' => $joinUrl, 'mode' => 'zoom']);
                } else {
                    // Handle errors with more detail
                    $errorData = $response->json();

                    // dd("2",$errorData,$response->status());
                    return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Zoom Meet', 'eventId' => '']);
                }

            } catch (\Exception $e) {
                return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Zoom Meet', 'eventId' => '']);
            }

        } elseif ((int) $request->currentId == 2) {

            try {
                //  $interviewTime = @$request->interviewTime;
                //  $eventStartTime = $request->interviewDate.' '.$interviewTime;
                $eventStartTime = $request->interviewDate.' '.$request->interviewTime;

                $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
                if ($defaultTimeFormat == 1) {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                } else {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                }
                $interviewDateCarbon = \Carbon\Carbon::parse($interviewDate);

                $twoHoursBefore = $interviewDateCarbon->copy()->subHours(2)->format('Y-m-d H:i:s');
                $twoHoursAfter = $interviewDateCarbon->copy()->addHours(2)->format('Y-m-d H:i:s');
                $interviewDate = $interviewDateCarbon->toDateString();

                $conflictingMeetingIds = DB::table('google_meeting_schedule')->whereDate('interview_date', $interviewDate)->pluck('meeting_link');

                $google_meeting_lists = DB::table('google_meeting_lists')->where('status', '=', 1)->whereNotIn('meeting_link', $conflictingMeetingIds)->first();

                //  $google_meeting_lists = DB::table('google_meeting_lists')
                // 	->where(function ($query) use ($threeHoursBefore, $fourHoursAfter) {
                // 		$query->whereNull('interview_date')
                // 			->orWhereNotBetween('interview_date', [$threeHoursBefore, $fourHoursAfter]);
                // 	})
                // 	->first();

                // $google_meeting_lists = DB::table('google_meeting_lists')->first();

                if ($google_meeting_lists) {
                    return response()->json(['status' => 'success', 'getHangoutLink' => @$google_meeting_lists->meeting_link]);
                } else {
                    return response()->json(['status' => 'expired', 'getHangoutLink' => 'Google Meet', 'eventId' => '']);
                }

                $eventStartTime = $request->interviewDate.' '.$request->interviewTime; // Replace with actual start time
                $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
                if ($defaultTimeFormat == 1) {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                } else {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                }
                $defaultTimezone = date_default_timezone_get();

                $client = new Google_Client;

                $client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Ensure this file exists

                // ats-jero-works
                // $client->setAuthConfig(storage_path('client_secret-ats-jero-works.apps.googleusercontent.com.json')); // Ensure this file exists

                $client->addScope(Google_Service_Calendar::CALENDAR);
                $client->setAccessType('offline');
                $client->setPrompt('consent'); // Ensures refresh token is generated

                // Retrieve stored access and refresh token
                $accessToken = session('google_access_token');
                $refreshToken = session('google_refresh_token');

                if ($accessToken) {
                    $client->setAccessToken($accessToken);

                    // Refresh token if access token is expired
                    if ($client->isAccessTokenExpired()) {
                        if ($refreshToken) {
                            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                            session(['google_access_token' => $newAccessToken]);

                            if (isset($newAccessToken['refresh_token'])) {
                                session(['google_refresh_token' => $newAccessToken['refresh_token']]); // Store refresh token
                            }
                        } else {
                            return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Google Meet', 'eventId' => '']);
                            // return response()->json(['status' => 'expired', 'error' => 'Session expired, please login again.'], 401);
                        }
                    }
                } else {
                    return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Google Meet', 'eventId' => '']);
                    // return redirect('/google/auth')->with('error', 'Please authenticate with Google.');
                }

                $service = new Google_Service_Calendar($client);

                $startDate = date('c', strtotime($interviewDate));
                $endDate = date('c', strtotime($interviewDate.' +1 hour'));

                $attendees = [
                    ['email' => 'vibhu2103m@gmail.com'],
                    ['email' => 'rajveer@4bell.com'],
                ];

                $event = new Google_Service_Calendar_Event([
                    'summary' => 'Interview Schedule',
                    'start' => ['dateTime' => $startDate, 'timeZone' => $defaultTimezone],
                    'end' => ['dateTime' => $endDate, 'timeZone' => $defaultTimezone],
                    'conferenceData' => [
                        'createRequest' => [
                            'requestId' => uniqid(),
                            'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                        ],
                    ],
                    'attendees' => $attendees, // Add attendees here
                ]);

                $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

                return response()->json(['status' => 'success', 'getHangoutLink' => $event->getHangoutLink()]);

            } catch (\InvalidArgumentException $e) {

                return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Google Meet', 'eventId' => '']);
            } catch (\Exception $e) {
                return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Google Meet', 'eventId' => '']);
            }

        } elseif ((int) $request->currentId == 5) {
            try {
                //  $interviewTime = @$request->interviewTime;
                //  $eventStartTime = $request->interviewDate.' '.$interviewTime;
                $eventStartTime = $request->interviewDate.' '.$request->interviewTime;

                $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
                if ($defaultTimeFormat == 1) {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                } else {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                }
                $interviewDateCarbon = \Carbon\Carbon::parse($interviewDate);

                $twoHoursBefore = $interviewDateCarbon->copy()->subHours(2)->format('Y-m-d H:i:s');
                $twoHoursAfter = $interviewDateCarbon->copy()->addHours(2)->format('Y-m-d H:i:s');

                $interviewDate = $interviewDateCarbon->toDateString();

                $conflictingMeetingIds = DB::table('team_meeting_schedule')->whereDate('interview_date', $interviewDate)->pluck('meeting_link');

                // dd($twoHoursBefore,$twoHoursAfter,$conflictingMeetingIds);

                $google_meeting_lists = DB::table('team_meeting_lists')->where('status', '=', 1)->whereNotIn('meeting_link', $conflictingMeetingIds)->first();

                //  $google_meeting_lists = DB::table('google_meeting_lists')
                // 	->where(function ($query) use ($threeHoursBefore, $fourHoursAfter) {
                // 		$query->whereNull('interview_date')
                // 			->orWhereNotBetween('interview_date', [$threeHoursBefore, $fourHoursAfter]);
                // 	})
                // 	->first();

                // $google_meeting_lists = DB::table('google_meeting_lists')->first();

                if ($google_meeting_lists) {
                    return response()->json(['status' => 'success', 'getHangoutLink' => @$google_meeting_lists->meeting_link]);
                } else {
                    return response()->json(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting', 'eventId' => '']);
                }

                $eventStartTime = $request->interviewDate.' '.$request->interviewTime; // Replace with actual start time
                $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
                if ($defaultTimeFormat == 1) {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                } else {
                    $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
                }
                $defaultTimezone = date_default_timezone_get();

                $client = new Google_Client;

                $client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Ensure this file exists

                // ats-jero-works
                // $client->setAuthConfig(storage_path('client_secret-ats-jero-works.apps.googleusercontent.com.json')); // Ensure this file exists

                $client->addScope(Google_Service_Calendar::CALENDAR);
                $client->setAccessType('offline');
                $client->setPrompt('consent'); // Ensures refresh token is generated

                // Retrieve stored access and refresh token
                $accessToken = session('google_access_token');
                $refreshToken = session('google_refresh_token');

                if ($accessToken) {
                    $client->setAccessToken($accessToken);

                    // Refresh token if access token is expired
                    if ($client->isAccessTokenExpired()) {
                        if ($refreshToken) {
                            $newAccessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                            session(['google_access_token' => $newAccessToken]);

                            if (isset($newAccessToken['refresh_token'])) {
                                session(['google_refresh_token' => $newAccessToken['refresh_token']]); // Store refresh token
                            }
                        } else {
                            return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting', 'eventId' => '']);
                            // return response()->json(['status' => 'expired', 'error' => 'Session expired, please login again.'], 401);
                        }
                    }
                } else {
                    return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting', 'eventId' => '']);
                    // return redirect('/google/auth')->with('error', 'Please authenticate with Google.');
                }

                $service = new Google_Service_Calendar($client);

                $startDate = date('c', strtotime($interviewDate));
                $endDate = date('c', strtotime($interviewDate.' +1 hour'));

                $attendees = [
                    ['email' => 'vibhu2103m@gmail.com'],
                    ['email' => 'rajveer@4bell.com'],
                ];

                $event = new Google_Service_Calendar_Event([
                    'summary' => 'Interview Schedule',
                    'start' => ['dateTime' => $startDate, 'timeZone' => $defaultTimezone],
                    'end' => ['dateTime' => $endDate, 'timeZone' => $defaultTimezone],
                    'conferenceData' => [
                        'createRequest' => [
                            'requestId' => uniqid(),
                            'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                        ],
                    ],
                    'attendees' => $attendees, // Add attendees here
                ]);

                $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

                return response()->json(['status' => 'success', 'getHangoutLink' => $event->getHangoutLink()]);

            } catch (\InvalidArgumentException $e) {

                return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting', 'eventId' => '']);
            } catch (\Exception $e) {
                return Reply::dataOnly(['status' => 'expired', 'getHangoutLink' => 'Teams Meeting', 'eventId' => '']);
            }
        }

        // dd( $event->getHangoutLink());
    }

    public function changeEventOrganizer(Request $request, $eventId)
    {
        $newOrganizerEmail = '';
        $defaultTimezone = date_default_timezone_get();

        // Initialize the Google Client
        $client = new GoogleClient;
        // $client->setAuthConfig(storage_path('client_secret_773251990953-1r6kjmiioc5qgaju4vm4u8v4boimokkq.apps.googleusercontent.com.json')); // Adjust the path as needed
        $client->addScope(Calendar::CALENDAR);
        // $client->setAccessType('offline');

        // Set the access token
        $accessToken = session('google_access_token');
        $client->setAccessToken($accessToken);

        // Refresh the access token if expired
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            session(['google_access_token' => $client->getAccessToken()]);
        }

        // Initialize the Calendar Service
        $calendarService = new Calendar($client);

        try {
            // Retrieve the original event
            $originalEvent = $calendarService->events->get('primary', $eventId);

            // dd($originalEvent);

            // Prepare the new event details
            $newEvent = new Google_Service_Calendar_Event([
                'summary' => $originalEvent->getSummary(),
                'description' => $originalEvent->getDescription(),
                'location' => $originalEvent->getLocation(),
                'start' => $originalEvent->getStart(),
                'end' => $originalEvent->getEnd(),
                'attendees' => $originalEvent->getAttendees(),
                'conferenceData' => $originalEvent->getConferenceData(),
                'creator' => [
                    'email' => $newOrganizerEmail,
                ],
                'organizer' => [
                    'email' => $newOrganizerEmail,
                ],
            ]);

            // Insert the new event into the calendar
            $newEvent = $calendarService->events->insert('primary', $newEvent, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'all',
            ]);

            // Delete the original event
            $calendarService->events->delete('primary', $eventId);

            // Return the link of the newly created event
            $newHangoutLink = $newEvent->getHangoutLink();
            $newHtmlLink = $newEvent->htmlLink;

            return response()->json([
                'newLink' => $newHangoutLink,
                'newHtmlLink' => $newHtmlLink,
            ]);

        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeSchedule(Request $request)
    {

        // echo '<pre>'; print_r($_REQUEST); exit;
        // dd(json_encode(@$request->cc_email));
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($request->wiseId);
        // dd($AddJobWiseCondidate->master_status_id);
        // $client = new GoogleClient();
        // $client->setClientId(config('google.client_id'));
        // $client->setClientSecret(config('google.client_secret'));
        // $client->setRedirectUri(config('google.redirect_uri'));
        // $client->addScope(Calendar::CALENDAR);

        // // Generate the Google OAuth URL
        // $authUrl = $client->createAuthUrl();
        // //    dd( $authUrl);
        // // Redirect to Google's OAuth 2.0 server
        // return redirect($authUrl);
        //  dd($request->all());

        $application_id = $interview_type = $interviewDate = $master_status_id = $child_status_id = $interview_mode = $interview_mode_lavel = $interviewers = $panellist = '';
        $application_id = @$request->application_id;
        $interview_type = @$request->interview_type;
        $interviewDate = @$request->interviewDate;
        $interviewTime = @$request->interviewTime;
        $interview_mode = @$request->interview_mode;
        $interview_mode_lavel = @$request->interview_mode_lavel;
        $master_status_id = @$request->master_status_id;
        $child_status_id = @$request->child_status_id;
        $interviewers = @$request->interviewers;
        $panellist = @$request->panellist;
        $mailstatus = @$request->mailstatus;
        $email_template = @$request->email_template_set;
        $timezone = @$request->timezone;

        // CC emails come in as a comma-separated string e.g. "Pooja@yopmail.com , Shobha@4bell.com , ...".
        // Explode, trim, lowercase, keep only valid emails, and drop duplicates before storing.
        $ccEmailArr = [];
        $ccEmailClean = '';
        if (! empty($request->cc_email)) {
            foreach (explode(',', $request->cc_email) as $rawEmail) {
                $oneEmail = strtolower(trim($rawEmail));
                if ($oneEmail !== '' && filter_var($oneEmail, FILTER_VALIDATE_EMAIL)) {
                    $ccEmailArr[] = $oneEmail;
                }
            }
            $ccEmailArr = array_values(array_unique($ccEmailArr));
            $ccEmailClean = implode(',', $ccEmailArr);
        }

        // dd($interviewDate,$interviewTime);
        $eventStartTime = $request->interviewDate.' '.$interviewTime; // Replace with actual start time
        $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
        if ($defaultTimeFormat == 1) {
            $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
        } else {
            $interviewDate = date('Y-m-d H:i:s', strtotime($eventStartTime));
        }

        $eventId = 12;
        if ($interview_mode == 2) {
            $eventId = 37;
        }
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        if ($packageStatus->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $packageStatus->message, 'company_coins' => $packageStatus->company_coins]);
        }

        $coinsDeductions = GeneralMethod::coinsDeductions((int) auth()->user()->company_id, $eventId, auth()->user()->id, auth()->user()->company_id, '', '');
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        // INTERVIEW SCHEDULE
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $application_id;
        if (! empty($request->interview_type)) {
            $interviewSchedule->reschedule = $interview_type;
        }
        if (! empty($interview_mode)) {
            $interviewSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
        }
        if (! empty($master_status_id)) {
            $interviewSchedule->stage = ($AddJobWiseCondidate->master_status_id) ? $AddJobWiseCondidate->master_status_id : $master_status_id;
        }
        if (! empty($child_status_id)) {
            $interviewSchedule->substage = $child_status_id;
        }
        if (! empty($panellist)) {
            $interviewSchedule->interviewers = $panellist;   // implode(',', $panellist);
        }
        if (! empty($interview_created_by)) {
            $interviewSchedule->interview_created_by = $this->user->id;
        }

        $interviewSchedule->schedule_date = $interviewDate;
        $interviewSchedule->schedule_to_time = $interviewTime;
        $interviewSchedule->interview_job_id = $AddJobWiseCondidate->job_id;
        $interviewSchedule->interview_job_wise_con_id = $AddJobWiseCondidate->id;
        $interviewSchedule->mailstatus = $mailstatus;
        $interviewSchedule->email_template = $email_template;
        $interviewSchedule->company_id = $companyId;
        $interviewSchedule->url_link = $request->url;
        $interviewSchedule->timezone_abbreviation = $timezone;
        $interviewSchedule->other_email = $ccEmailClean; // cleaned, de-duplicated CC email list
        $interviewSchedule->save();

        $updateParams = [
            'index' => ELASTIC_CANDIDATES,
            'id' => $request->wiseId,
            'retry_on_conflict' => 5,
            'body' => [
                'doc' => [
                    'schedule_to_time' => $interviewSchedule->schedule_to_time,
                    'timezone_abbreviation' => $interviewSchedule->timezone_abbreviation,
                    'schedule_date' => ($interviewSchedule->schedule_date) ? Carbon::parse($interviewSchedule->schedule_date)->toIso8601String() : null,
                ],
                'doc_as_upsert' => true,
                'detect_noop' => false,
            ],
            'refresh' => false, // Eventual consistency for 10K concurrent scale
        ];

        $this->elasticService->getClient()->update($updateParams);

        $interviewSchedule_id = '';
        $interviewSchedule_id = $interviewSchedule->id;
        if ($interview_mode == 2 && $interview_mode_lavel == 2) {
            DB::table('google_meeting_schedule')->insert(['meeting_link' => $request->url, 'user_id' => auth()->user()->id, 'company_id' => auth()->user()->company_id, 'interview_job_wise_con_id' => $AddJobWiseCondidate->id, 'interview_date' => $interviewDate]);
        }
        if ($interview_mode == 2 && $interview_mode_lavel == 5) {
            DB::table('team_meeting_schedule')->insert(['meeting_link' => $request->url, 'user_id' => auth()->user()->id, 'company_id' => auth()->user()->company_id, 'interview_job_wise_con_id' => $AddJobWiseCondidate->id, 'interview_date' => $interviewDate]);
        }

        // mail to candidate for inform interview schedule
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            // Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));
        }

        $jobApplication = JobApplication::withoutGlobalScopes()->find($request->application_id);

        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($request->wiseId);

        $date = date('Y-m-d H:i:s');
        $insert_type = $request->interview_type;

        // dd($request->master_status_id);

        $statusRow = ApplicationStatus::withoutGlobalScopes()->find($request->master_status_id);

        $interview_mode_lavel = '';
        $interview_mode_lavel = @$request->interview_mode;
        // dd($interview_mode_lavel,$request->all());
        if ($interview_mode_lavel > 0) {
            $intModeLbl = DB::table('interview_mode_lavel')
                ->select('interview_mode_lavel.name')
                ->where('interview_mode_lavel.id', $interview_mode_lavel)
                ->first();
            $interview_mode_lavel = $intModeLbl->name;
        }

        /*$values 				=	array(
                                                  'owner_id' 						=> auth()->user()->id,

                                                  'application_id' 				=> @$request->application_id,
                                                  'application_email' 			=> @$jobApplication->email,
                                                  'job_id' 						=> @$jobApplication->job_id,
                                                  'process_id' 					=> @$request->master_status_id,
                                                  'status_name' 					=> @$statusRow->name,

                                                  'note'			 				=> 'Interview Schedule',
                                                  'insert_type' 					=> @$insert_type,
                                                  'create_at' 					=> @$date,
                                                  'update_at' 					=> @$date,
                                                  'interview_schedule_date' 		=> @$interviewDate,
                                                  'interview_mode' 				=> $interview_mode,
                                                  'timeline_type' 				=> 1,
                                                  'interview_mode_child_level' 	=> $interview_mode_lavel

                                              );
              if(!empty($request->url) && $request->url != ''){
                  $values['interview_mention_link'] = @$request->url;
              }
              if(!empty($request->panellist) && $request->panellist != ''){
                  $values['interviewers'] 	=	@$request->panellist;
              }
              if ($request->child_status_id > 0)
              {
                  $childStatus = '';
                  $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
                  $values['process_child_name'] = @$childStatus->name;
              }
              if (!empty($panellist)) {
                  $values['interviewers'] 		= $panellist;
              }
              $values['interview_schedules_id'] 	= $interviewSchedule_id;
              DB::table('timeline')->insert($values);*/

        $date = date('Y-m-d H:i:s');
        $values = [
            'event_id' => 3,
            'event_by_company_id' => auth()->user()->company_id,
            'event_by_user_id' => auth()->user()->id,
            // 'master_process_name' 		=>	@$jobApplication->job_id,
            // 'child_process_name' 			=>	@$master_status,

            'application_id' => @$request->application_id ? @$request->application_id : null,
            'application_email' => @$jobApplication->email ? @$jobApplication->email : null,
            'job_id' => @$AddJobWiseCondidate->job_id ? @$AddJobWiseCondidate->job_id : $request->jobId,
            'note' => 'Interview Schedule',
            'create_at' => @$date ? @$date : null,

            'update_at' => @$date ? @$date : null,

            'interview_schedule_date' => $interviewDate,
            'interview_mode' => $interview_mode,
            // 'timeline_type' 				=>	1,
            'interview_mode_child_level' => ($request->interview_mode != 3) ? $interview_mode_lavel : '',
            // 'interview_from_time'			=>
            // 'interview_to_time'			=>
        ];

        if ($jobApplication->master_status_id > 0) {
            $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $jobApplication->master_status_id)->first();
            $values['master_process_name'] = @$masterStatus->name;
        }
        if ($jobApplication->child_status_id > 0) {
            $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.id', $jobApplication->child_status_id)->first();
            $values['child_process_name'] = @$masterStatus->name;
        }
        if (! empty(@$comment)) {
            $values['note'] = @$comment;
        }

        if (! empty($request->url) && $request->url != '') {
            $values['interview_mention_link'] = @$request->url;
        }
        if (! empty($request->panellist) && $request->panellist != '') {
            $values['interviewers'] = @$request->panellist;
        }
        $values['interview_schedules_id'] = $interviewSchedule_id;

        $values['timezone_abbreviation'] = $timezone;
        $values['interview_to_time'] = $interviewTime;

        // DB::table('timeline')->insert($values);
        GeneralMethod::UpdateTimeline($values);
        $jobIdGet = @$AddJobWiseCondidate->job_id ? @$AddJobWiseCondidate->job_id : $request->jobId;

        // $tb_ranks = array('company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet,'status' => 1,'application_id' => @$request->application_id);
        // DB::table('tb_ranks')->insert($tb_ranks);

        $InterviewSchedule = InterviewSchedule::join('add_job_wise_condidates', 'add_job_wise_condidates.id', '=', 'interview_schedules.interview_job_wise_con_id')
            ->join('companies', 'companies.id', '=', 'interview_schedules.company_id')
            ->join('clients', 'clients.company_id', '=', 'companies.id')
            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
            ->join('jobs', 'jobs.id', '=', 'interview_schedules.interview_job_id')
            ->join('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
            ->join('updatetimeline', 'updatetimeline.interview_schedules_id', '=', 'interview_schedules.id')
            ->select('interview_schedules.*', 'updatetimeline.interview_mention_link', 'updatetimeline.interview_mode_child_level as mode_child', 'companies.company_name', 'companies.description', 'companies.website', 'client_location.address as c_address', 'job_applications.full_name', 'job_applications.email as cond_email', 'job_applications.current_designation', 'jobs.employment_type', 'jobs.title', 'jobs.job_description', 'jobs.id as jobID', 'jobs.total_positions')
            ->where('interview_schedules.id', '=', $interviewSchedule_id)->first();
        // dd($InterviewSchedule,$interviewSchedule_id);

        $company_or_client = GeneralMethod::companyDetailsByClient($InterviewSchedule->interview_job_id);
        $interview_mode = '';
        if ($InterviewSchedule->interview_mode > 0) {
            $interview_mode_Arr = GeneralMethod::interview_mode();
            $interview_mode = $interview_mode_Arr[@$InterviewSchedule->interview_mode];
        }
        $interview_mode_child_level = $InterviewSchedule->interview_mode_child_level;
        if ($InterviewSchedule->interview_mode == 3) {
            $interview_mode_child_level = @$InterviewSchedule->interview_mention_link;
        }

        $jobId = $InterviewSchedule->interview_job_id;
        $lastInsertedId = $InterviewSchedule->interview_job_wise_con_id;
        $wise_c = DB::table('job_applications')
            ->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('add_job_wise_condidates.id', '=', $lastInsertedId)->where('add_job_wise_condidates.job_id', '=', $jobId)->first();

        $partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
            ->join('package_active', function ($join) {
                $join->on('users.company_id', '=', 'package_active.company_id')
                    ->where('package_active.emails_balance', '>', 0)
                    ->where('package_active.startdate', '<=', now())
                    ->where('package_active.enddate', '>=', now())
                    ->where('package_active.packages_status', 1);
            })
            ->where('job_assign.job_id', '=', $jobId)
            ->pluck('users.email')
            ->toArray();

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
            $join->on('job_applications.job_id', '=', 'jobs.id');
        });
        $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
            $join->on('companies.id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
                ->where('package_active.startdate', '<=', now())
                ->where('package_active.enddate', '>=', now())
                ->where('package_active.packages_status', 1);
        });
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
        $recommendedDataList = $recommendedDataList->toArray();

        $data = [
            'InterviewDate' => $interviewDate,
            'InterviewMode' => $interview_mode.'('.$InterviewSchedule->mode_child.')',
            'InterviewAddress' => $InterviewSchedule->interview_mention_link,
            'CompanyName' => $InterviewSchedule->company_name,
            'CompanyLocation' => $InterviewSchedule->c_address,
            'Position' => $InterviewSchedule->current_designation,
            'EmploymentType' => ($InterviewSchedule->employment_type == 1) ? 'Contract' : 'Full-time',
            'ReqID' => 'R-'.@$InterviewSchedule->jobID,
            'Reqname' => @$InterviewSchedule->title,
            'NoofEmployees' => $InterviewSchedule->total_positions,
            'OfficeTiming' => '...',
            'NoofWorkingDays' => '...',
            'WebsiteURL' => $InterviewSchedule->website,
            'Jobdescription' => $InterviewSchedule->job_description,
            'AboutOrganization' => $InterviewSchedule->description,
            'VendorTeam' => @$InterviewSchedule->company_name,
            'CompanyNameClientname' => $company_or_client,
            'CandidateName' => $InterviewSchedule->full_name,
            'username' => auth()->user()->name,
            'mobile' => auth()->user()->mobile,
        ];
        // dd($data);
        config(['mail.from.name' => auth()->user()->name]);
        $cc_cond = [$InterviewSchedule->cond_email];
        $cc = array_merge($partnersData, $recommendedDataList, $cc_cond);

        if (count($cc) > 0) {
            foreach ($cc as $email) {
                $client_company_id = 0;
                $useremail = DB::table('users')->select('*')->where('email', $email)->first();
                if (! empty($useremail)) {
                    $client_company_id = @$useremail->company_id;
                }
                // dd($client_company_id);
                if (@$client_company_id > 0) {
                    $event_name = 'emails';
                    $event_table_name = 'job applications stage change';
                    $event_table_id = @$client_company_id;
                    GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
                }
            }
        }

        GeneralMethod::interviewTrackerAddEditUpload($interviewSchedule_id);

        $cachekeys = [
            'allapptracker_version_user_'.$this->user->id,
            'candidateDetails_version_user_'.$this->user->id,
            'allinterviewschedule_version_user_'.$this->user->id,
            'allinterviewbenchcandidates_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

    }

    public function emailSchedule(Request $request)
    {
        // dd($request->all());

        $job = Job::withoutGlobalScopes()->find($request->jobId);
        $companyOrClientName = GeneralMethod::companyOrClientName($job->client_id);
        $subject = @$request->subject;

        $cc = (@$request->cc_email) ? @$request->cc_email : '';
        $cc_email = [];
        if (! empty($cc)) {
            $emails = explode(',', $request->cc_email);
            // 			dd($emails);

            foreach ($emails as $keymail => $email) {
                array_push($cc_email, str_replace(' ', '', $email));
            }
        }

        // Define validation rules
        $rules = [
            'attachment' => 'file|max:2048', // 2048 KB = 2 MB
        ];

        // Define custom error messages (optional)
        $messages = [
            'attachment.max' => 'The file must not be larger than 2 MB.',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'faild', 'message' => 'The file must not be larger than 2 MB !']);
            // return redirect()->back()->withErrors($validator)->withInput();
        }

        // dd($request->all());
        // Interview Details
        $interviewLink = $request->url; // Interview link
        $eventTitle = $request->subject;
        $eventDescription = $request->editor1;
        $timezone = $request->timezone;
        $interviewTime = @$request->interviewTime;

        $eventStartTime = $request->interviewDate.' '.$interviewTime; // Replace with actual start time

        $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
        if ($defaultTimeFormat == 1) {
            $eventEndTime = date('d-m-Y h:i A', strtotime($eventStartTime.' +1 hour'));
        } else {
            $eventEndTime = date('d-m-Y H:i', strtotime($eventStartTime.' +1 hour'));
        }

        //  dd($eventStartTime,$eventEndTime);

        $data = [
            'details' => @$request->editor1,
            'interviewLink' => @$interviewLink,
            'eventTitle' => @$eventTitle,
            'eventDescription' => @$eventDescription,
            'eventStartTime' => @$eventStartTime.' ('.$timezone.')',
            'eventEndTime' => @$eventEndTime.' ('.$timezone.')',

        ];
        // dd($data);

        $attachments = [];
        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Resumes/'.$filename;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fileUrl = Storage::disk('s3')->url($filePath);

            $attachments[] = [
                'path' => $fileUrl,
                'options' => [],
            ];
        }

        // 		$mail = Mail::to(auth()->user()->email);

        //         if (!empty($cc)) {
        //             $mail->cc($cc_email);
        //         }

        $senderName = auth()->user()->name;
        $fromAddress = auth()->user()->email;
        $company_id = auth()->user()->company_id;
        $reply_to = [];
        if (! empty($request->reply_to_email)) {
            $reply_emails = explode(',', $request->reply_to_email);
            foreach ($reply_emails as $rEmail) {
                $rEmailClean = trim(str_replace(' ', '', $rEmail));
                if (! empty($rEmailClean) && filter_var($rEmailClean, FILTER_VALIDATE_EMAIL) && ! in_array($rEmailClean, $reply_to)) {
                    $reply_to[] = $rEmailClean;
                }
            }
        }
        $to_email = @$request->to_email;
        if (! empty($to_email)) {
            $checkMailPermision = GeneralMethod::mailPermission(2);
            if ($checkMailPermision) {
                // QueueMailService QueueNotificationMailService
                QueueMailService::dispatch(
                    $to_email,
                    $subject,
                    'email.candidate.interview-tracker',
                    $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc_email,
                    $company_id,
                    $reply_to
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_email, $subject,'email.candidate.interview-tracker', $data, $attachments, $fromAddress, $senderName,$cc_email);
            }
        }

        // $mail->send(new InterviewTrackerMail((object) $data, $subject, $attachments));

        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

        // below code old

        $application_id = $master_status_id = $child_status_id = $interview_type = $interviewDate = $interview_mode = $interview_mode_lavel = $panellist = $interviewers = '';
        $email_template = $from_email = $to_email = $cc_email = $subject = $editor = '';
        $application_id = @$request->application_id;
        $master_status_id = @$request->master_status_id;
        $child_status_id = @$request->child_status_id;
        $interview_type = @$request->interview_type;
        $interviewDate = @$request->interviewDate;
        $interview_mode = @$request->interview_mode;
        $interview_mode_lavel = @$request->interview_mode_lavel;
        $panellist = @$request->panellist;

        $email_template = @$request->email_template;
        $from_email = @$request->from_email;
        $to_email = @$request->to_email;
        $cc_email = @$request->cc_email;
        $subject = @$request->subject;
        $editor = @$request->editor;

        // INTERVIEW SCHEDULE
        $interviewSchedule = new InterviewSchedule;
        $interviewSchedule->job_application_id = $application_id;
        if (! empty($master_status_id)) {
            $interviewSchedule->stage = $master_status_id;
        }
        if (! empty($child_status_id)) {
            $interviewSchedule->substage = $child_status_id;
        }
        if (! empty($request->interview_type)) {
            $interviewSchedule->reschedule = $interview_type;
        }
        $interviewSchedule->schedule_date = $interviewDate;
        if (! empty($interview_mode)) {
            $interviewSchedule->interview_mode = $interview_mode;
        }
        if (! empty($interview_mode_lavel)) {
            $interviewSchedule->interview_mode_lavel = $interview_mode_lavel;
        }
        if (! empty($panellist)) {
            $interviewSchedule->interviewers = $panellist;   // implode(',', $panellist);
        }
        if (! empty($interview_created_by)) {
            $interviewSchedule->interview_created_by = $this->user->id;
        }
        $interviewSchedule->save();

        $interviewSchedule_id = '';
        $interviewSchedule_id = $interviewSchedule->id;

        // UPDATE JOB APPLICATION
        /*$intScheduleId 		= '';
              $intScheduleId 		= $interviewSchedule->id;
              $jobApplication 	= JobApplication::find($request->application_id);
              if ($request->masterStatus > 0) {
                  $jobApplication->status_id = $request->masterStatus;
                  $jobApplication->master_status_id = $request->masterStatus;
              }
              if ($request->child_status > 0) {
                  $jobApplication->child_status_id = $request->child_status;
              }
              $jobApplication->save();*/

        // mail to candidate for inform interview schedule
        if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            // Notification::send($jobApplication, new CandidateScheduleInterview($jobApplication, $interviewSchedule));

        }
        $jobApplication = JobApplication::withoutGlobalScopes()->find($request->application_id);
        $date = date('Y-m-d H:i:s');
        $insert_type = $request->interview_type;
        $statusRow = ApplicationStatus::withoutGlobalScopes()->findOrFail($request->master_status_id);
        $values = [
            'owner_id' => auth()->user()->id,
            'application_id' => @$request->application_id,
            'application_email' => @$jobApplication->email,
            'job_id' => @$jobApplication->job_id,
            'process_id' => @$request->master_status_id,
            'status_name' => @$statusRow->name,

            'note' => 'Interview Schedule',
            'insert_type' => @$insert_type,
            'create_at' => @$date,
            'update_at' => @$date,
            'interview_schedule_date' => @$interviewDate,
            'interview_mode' => $interview_mode,
            'timeline_type' => 1,

        ];

        /*if ($request->child_status_id > 0)
                    {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
                        $values['process_child_name'] = @$childStatus->name;
                    }
                    if (!empty($panellist)) {
                        $values['interviewers'] = $panellist;
                    }*/

        if (! empty($request->url) && $request->url != '') {
            $values['interview_mention_link'] = @$request->url;
        }
        if (! empty($request->panellist) && $request->panellist != '') {
            $values['interviewers'] = @$request->panellist;
        }
        if ($request->child_status_id > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('company_id', '=', $this->user->company_id)->where('id', $request->child_status_id)->first();
            $values['process_child_name'] = @$childStatus->name;
        }

        /*if (!empty($panellist)) {
                                 $values['interviewers'] 		= $panellist;
                             }*/
        $values['interview_schedules_id'] = $interviewSchedule_id;

        DB::table('timeline')->insert($values);

        // START SEND EMAIL
        // $file 			=	$path.$filename;
        // $content 			=	file_get_contents( $file);
        // $content 			=	chunk_split(base64_encode($content));

        // $file_name 		=	basename($file);

        $from_name = 'Vibhanshu';
        $from_mail = 'vibhanshu@4bel.com';
        $replyto = 'support@4bell.com';
        $uid = md5(uniqid(time()));
        $message = $editor;
        $mailto = 'abu@4bell.com';

        // Always set content-type when sending HTML email
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-type:text/html;charset=UTF-8'."\r\n";

        // More headers
        $headers .= 'From: $from_mail'."\r\n";
        $headers .= 'Cc: $replyto'."\r\n";

        mail($mailto, $subject, $message, $headers);

        // END SEND EMAIL

        return Reply::dataOnly(['status' => 'success', 'message' => 'Interview Reschedule successfully !']);

    }

    public function stage_body_content($id)
    {
        $current_company_id = auth()->user()->company_id;
        $this->qualification = '';
        $this->application = '';

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        // Intent: Bypass company global scope so cross-company candidate assignments in add_job_wise_condidates can be fetched for stage updates.
        // Tenancy: The query joins add_job_wise_condidates by candidate assignment ID ($id).
        $this->application = JobApplication::withoutGlobalScope('company')->select(
            'job_applications.id',
            'job_applications.submitted_by',
            'job_applications.company_id',
            'job_applications.np_id',
            'job_applications.serving_notice_period_mode',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.secondary_mobile',
            'job_applications.photo',
            'job_applications.resume',
            'job_applications.rating',
            'job_applications.skype_id',
            'job_applications.cover_letter',
            'add_job_wise_condidates.job_id',
            'job_applications.status_id',
            'job_applications.is_viewed',
            'job_applications.column_priority',
            'job_applications.created_at',
            'job_applications.updated_at',
            'job_applications.deleted_at',
            'job_applications.total_exp',
            'job_applications.rel_exp',
            'job_applications.passport',
            'job_applications.current_ctc_cur',
            'job_applications.current_ectc_cur',
            'job_applications.current_ctc_mode',
            'job_applications.current_ectc_mode',
            'job_applications.ctc',
            'job_applications.ectc',

            'job_applications.current_location',
            'job_applications.current_designation',
            'job_applications.current_company',
            'job_applications.process_id',
            'job_applications.qualification',
            'add_job_wise_condidates.master_status_id',
            'add_job_wise_condidates.child_status_id',
            'job_applications.secondrary_email',
            'job_applications.secondary_mobile',
            'job_applications.skills',
            'clients.name as client_name',
            'jobs.title',
            'jobs.max_sal_mode as jobMode',
            'jobs.max_salary_currency as jobCurrency',
            'users.name as posted_by',
            'master_status.name as master_status_name',
            'job_applications.proposed_ctc',
            'job_applications.any_offer',
            'job_applications.document',
            'job_applications.sub_for_approval',
            'job_applications.offer_approved',
            'job_applications.offer_release',
            'job_applications.offer_acceptance',
            'job_applications.joined',
            'job_applications.joined_date',
            'job_applications.ctc_yearly',
            'job_applications.ectc_yearly',
            'job_applications.city',
            'job_applications.state',
            'job_applications.country',
            'job_applications.city_id',
            'state_list.state as state_name',
            'job_applications.condidate_type',
            'job_applications.bill_rate_amount',
            'job_applications.bill_rate_mode',
            'job_applications.bill_rate_currency',
            'users.name as submitted_by_name',
            'users.company_id as submitted_by_company_id',
            'users.image as posted_by_image',
            'add_job_wise_condidates.job_id as jobId',
            'add_job_wise_condidates.thirdparty_company_id',
            'add_job_wise_condidates.id as job_wise_condidates_id'
        )->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->leftJoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            // ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state');
        // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');

        $this->application = $this->application->where('add_job_wise_condidates.id', $id);
        $this->application = $this->application->first();

        if (! $this->application) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Candidate application not found.']);
        }

        // dd($this->application);

        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }

        $submitted_by_company_id = $this->application->submitted_by_company_id;
        $this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

        // $this->master_status 				=	DB::table('master_status')->select('*')->orderBy('master_status.priority', 'ASC')->get();

        $thirdparty_company_id = $this->application->thirdparty_company_id;
        if ($thirdparty_company_id == $current_company_id) {
            $this->master_status = DB::table('master_status')->select('*')->where('master_status.direct_client', '=', 1)->orderBy('master_status.direct_client_priority', 'ASC')->get();
        } else {
            $this->master_status = DB::table('master_status')->select('*')->where('master_status.custom_client', '=', 1)->orderBy('master_status.priority', 'ASC')->get();
        }

        $master_status_id = $this->application->master_status_id;
        $this->candidate_status_Arr = DB::table('candidate_status')
            ->select('*')
            ->where('parent', $master_status_id)
            ->where('company_id', $current_company_id)
            ->orderBy('candidate_status.priority', 'ASC')
            ->get();

        // dd($master_status_id);

        $this->schedule = DB::table('interview_schedules')->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $this->application->job_id;
        $this->job_id = $job_id;

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;

        $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
        $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
            0 => 'INR',
            1 => 'EUR',
            2 => 'JPY',
            3 => 'GBP',
            4 => 'USD',
            5 => 'AED',
        ];
        $jobData = DB::table('jobs')->select('max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')->where('id', '=', $this->application->jobId)->first();
        $jobMode = $jobData->jobMode ?? @$this->application->current_ctc_mode;
        $jobCurrency = $jobData->jobCurrency ?? @$this->application->current_ctc_cur;
        $symbols = $symbolsValue[$jobCurrency] ?? 'INR';

        $current_ctc_cur = '';
        $current_ctc_cur = $currencySymbole[@$jobCurrency];

        $current_ectc_cur = '';
        $current_ectc_cur = $currencySymbole[@$jobCurrency];

        $current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
        $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);

        $payFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ctc_yearly, $symbols, $jobMode);
        $billFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ectc_yearly, $symbols, $jobMode);
        $ctc = '';
        $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
        $ectc = '';
        $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

        $this->current_ctc = @$current_ctc_cur.@$ctc.'/'.@$current_ctc_mode;

        $this->expected_ctc = @$current_ectc_cur.@$ectc.'/'.@$current_ectc_mode;

        $country = '';
        $this->row_country_name = '';

        if (empty($this->application->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->application->country)) {
                if (is_numeric($this->application->country)) {
                    $country_name_Arr = GeneralMethod::getCountryName($this->application->country);
                    foreach ($country_name_Arr as $cval) {
                        $this->row_country_name = ucwords($cval['name']);
                    }
                }
            }
        }

        $this->row_state_name = '';
        if (empty($this->application->state)) {
            $this->row_state_name = '----';
        } else {
            if ($this->application->country == 101) {
                $this->row_state_name = $this->application->state_name;
            } else {
                $state = $this->application->state;
                if (is_numeric($this->application->country) && is_numeric($state)) {
                    if ($state > 0) {
                        $state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $this->row_state_name = ucwords($sval['name']);
                        }
                    }
                }
            }
        }

        $this->row_city_name = '';
        if ($this->application->country == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = $this->application->city;
        }

        $view = view('service_company.job-applications.stage_body_content', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function child_stage($id)
    {
        $company_id = '';
        $company_id = auth()->user()->company_id;

        // Rejected has two master_status ids — 7 (client-side/"custom client"
        // Rejected, reachable once sourced) and 9 ("agency-Rejected", the one
        // actually shown in the normal agency-self-view drawer, reachable
        // straight from InReview). Settings > Interview Process only ever
        // lets a company configure candidate_status rows for parent=7 — there
        // is no form field for parent=9 at all — so without this fold,
        // child_stage(9) (the id the standard drawer submits) would ALWAYS
        // come back empty, even when Rejected reasons ARE configured, since
        // they're stored under parent=7. Both ids represent the same
        // conceptual "Rejected" reason list to the company, just gated to a
        // different client relationship type.
        $master_status_id = ((int) $id === 9) ? 7 : $id;
        // dd($master_status_id,$company_id);
        $candidate_status_Arr = DB::table('candidate_status')
            ->select('*')
            ->where('parent', $master_status_id)
            ->where('company_id', $company_id)
            ->orderBy('candidate_status.priority', 'ASC')
            ->get();

        $html = '';
        // dd($candidate_status_Arr);
        if (! empty($candidate_status_Arr)) {
            foreach ($candidate_status_Arr as $cstatus) {
                $csdMeta = GeneralMethod::csdSubStatusMeta(@$cstatus->name);
                $csdName = e(@$cstatus->name);
                $csdDesc = $csdMeta['desc'] !== '' ? '<div class="ds">'.e($csdMeta['desc']).'</div>' : '';

                // Same .csd-sub-tile markup/classes the two body-content
                // Blade partials render initially — this AJAX response (used
                // by both the plain and client-gated drawers) must match so
                // switching stages mid-drawer looks identical to the first
                // paint. onclick calls the shared, flow-aware csdSelectSub().
                // csdMeta['icon'] is inline SVG inner-markup (not a Font
                // Awesome class — this app only has FA 4.7.0 loaded, which
                // lacks the icon names/prefix a newer cheat-sheet suggests).
                $html .= '<div class="csd-sub-tile" onclick="csdSelectSub(this)">
									<input type="radio" class="d-none" name="sub_stages" value="'.@$cstatus->id.'" id="sub_stage_'.@$cstatus->id.'">
									<span class="ic" style="background:'.$csdMeta['bg'].';color:'.$csdMeta['color'].';"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$csdMeta['icon'].'</svg></span>
									<div class="txt">
										<div class="nm">'.$csdName.'</div>
										'.$csdDesc.'
									</div>
								</div>';
            }
        }

        // dd($html);
        return Reply::dataOnly(['status' => 'success', 'html' => $html]);

    }

    /**
     * Builds the "one stage at a time" validation message shared by
     * stage_submit() and client_stage_submit() whenever a candidate is
     * moved more than one step ahead, or more than one step backward, in
     * the stage sequence. Rather than just saying the attempted move is
     * blocked, it tells the user exactly which stages ARE reachable from
     * where the candidate currently sits (previous / next / Rejected),
     * since that's the actionable information they need next.
     *
     * @param  array  $stages  Same order map ($id => ['display' => ..., 'order' => ...]) already built in the caller.
     * @param  int  $currentOrder  Order value of the candidate's current stage.
     * @param  string  $currentName  Display name of the candidate's current stage.
     * @param  string  $targetName  Display name of the stage the user attempted to jump to.
     */
    private function stageSequenceGuardMessage(array $stages, int $currentOrder, string $currentName, string $targetName): string
    {
        $prevName = null;
        $nextName = null;
        foreach ($stages as $sid => $s) {
            if ($s['order'] === $currentOrder - 1) {
                $prevName = $s['display'];
            }
            // Rejected (7 in both flows, 9 in the client flow's agency-only
            // bucket) is always offered separately below — excluded here so
            // it's never mentioned twice.
            if ($s['order'] === $currentOrder + 1 && ! in_array((int) $sid, [7, 9], true)) {
                $nextName = $s['display'];
            }
        }

        $options = [];
        if ($prevName) {
            $options[] = "<b style='color:#F59E0B;'>{$prevName}</b> (previous stage)";
        }
        if ($nextName) {
            $options[] = "<b style='color:#3B82F6;'>{$nextName}</b> (next stage)";
        }
        $options[] = "<b style='color:#EF4444;'>Rejected</b>";

        $last = array_pop($options);
        $optionsText = count($options) ? implode(', ', $options).' or '.$last : $last;

        return 'Candidates move through hiring stages one step at a time.<br>'
            ."From <b style='color:#181c32;'>{$currentName}</b>, you can move to {$optionsText}.<br>"
            ."Jumping directly to <b style='color:#181c32;'>{$targetName}</b> isn't supported — please select an adjacent stage to continue.";
    }

    public function stage_submit(Request $request)
    {

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $application_id = $master_status = $sub_stages = $comment = $addJobWiseCondidate_id = '';
        $prev_process_name = $prev_process_child_name = '';
        $application_id = @$request->application_id;
        $addJobWiseCondidate_id = @$request->job_wise_condidates_id;
        $master_status = @$request->master_status;
        $sub_stages = @$request->sub_stages;
        $comment = @$request->comment;
        // dd($addJobWiseCondidate_id);
        $currency = GeneralMethod::currency();
        $salaryModeOffer = GeneralMethod::salaryModeOffer();

        $userId = auth()->id();
        $isValid = DB::table('add_job_wise_condidates as ajwc')
            ->leftJoin('job_assign as ja', function ($join) {
                $join->on('ja.job_id', '=', 'ajwc.job_id')
                    ->on('ja.company_id', '=', 'ajwc.company_id');
            })
            ->where('ajwc.id', $addJobWiseCondidate_id) // Candidate row id
            ->where(function ($query) use ($userId) {
                $query->where('ja.spok_user_id', $userId)
                    ->orWhere('ajwc.submitted_by', $userId);
            })
            ->exists();

        if (! $isValid) {

            $validations = (object) [
                'message' => "You are not authorized to update this candidate's status. Only the assigned Captain or the submitting user can update this candidate's status.",
            ];

            return Reply::dataOnly([
                'status' => 'candidatevalidation',
                'message' => $validations->message,
            ]);
        }

        // $chkAdminRole = DB::table('roles')->select('*')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        // if($chkAdminRole){

        //     $candidateJoineddTeam = -30;
        // 	   $candidateJoineddUser = -1500;
        //        $teamRankElastic = [
        // 		'company_id'=>2,
        // 		'user_id'=>5,
        // 		'candidate_id'=>$addJobWiseCondidate_id,
        // 		'profileUpload'=>0,
        // 		'interviewScheduled'=>0,
        // 		'interviewNoShow'=>0,
        // 		'offerReleased'=>0,
        // 		'candidateJoined'=>0,
        // 		'jobsPosted'=>0,
        // 		'feedbackGivenOnCVs'=>0,
        // 		'postJobToVendorMarket'=>0,
        // 		'outsourceJobsToVendors'=>0,
        // 		'listBenchCandidates'=>0,
        // 		'respondToBenchRequests'=>0,
        // 		'interestedCandidates'=>0,
        // 		'notInterestedCandidates'=>0,
        // 		'teamCaptainInterviewScheduled'=>0,
        // 		'teamCaptainInterviewNoShow'=>0,
        // 		'teamCaptainOfferReleased'=>0,
        // 		'teamCaptainCandidateJoined'=>$candidateJoineddTeam
        // 	];
        // 		GeneralMethod::teamRankElastic($teamRankElastic);

        // 		$teamRankElastic = [
        // 							'company_id'=>2,
        // 							'user_id'=>13,
        // 							'candidate_id'=>$addJobWiseCondidate_id,
        // 							'profileUpload'=>0,
        // 							'interviewScheduled'=>0,
        // 							'interviewNoShow'=>0,
        // 							'offerReleased'=>0,
        // 							'candidateJoined'=>$candidateJoineddUser,
        // 							'jobsPosted'=>0,
        // 							'feedbackGivenOnCVs'=>0,
        // 							'postJobToVendorMarket'=>0,
        // 							'outsourceJobsToVendors'=>0,
        // 							'listBenchCandidates'=>0,
        // 							'respondToBenchRequests'=>0
        // 						];
        // 		GeneralMethod::teamRankElastic($teamRankElastic);

        // 		dd('Okay');
        // }

        $addJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($addJobWiseCondidate_id);
        $offer_id = null;
        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $addJobWiseCondidate_id,
        ]);
        if ($exists) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];
        } else {
            $data = (object) [
                'company_id' => $addJobWiseCondidate->company_id ?? auth()->user()->company_id,
                'submitted_by' => $addJobWiseCondidate->submitted_by ?? auth()->id(),
                'candidate_id' => $addJobWiseCondidate->job_application_id ?: $addJobWiseCondidate->id,
                'job_master_status_id' => $addJobWiseCondidate->master_status_id ?: 8,
                'jobId' => $addJobWiseCondidate->job_id,
                'of_offer_amount' => $addJobWiseCondidate->of_offer_amount ?? 0,
                'of_joined_date' => $addJobWiseCondidate->of_joined_date ?? null,
                'childStatus' => $addJobWiseCondidate->child_status_id ?? 0,
            ];
        }

        $data->company_id = $data->company_id ?? ($addJobWiseCondidate->company_id ?? auth()->user()->company_id);
        $data->submitted_by = $data->submitted_by ?? ($addJobWiseCondidate->submitted_by ?? auth()->id());
        $data->candidate_id = $data->candidate_id ?? ($addJobWiseCondidate->job_application_id ?? $addJobWiseCondidate->id);
        $data->job_master_status_id = $data->job_master_status_id ?? ($addJobWiseCondidate->master_status_id ?? 8);

        if (true) {
            if (($master_status != 6) && ($data->job_master_status_id == 6)) {

                $status_row = DB::table('candidate_status')->select('*')->where('id', '=', $data->childStatus)->first();
                if ($status_row) {
                    if (@$status_row->name == 'Onboarded') {

                        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($addJobWiseCondidate_id);

                        $spok_user_id_rows = DB::table('job_assign')
                            ->where('job_id', $AddJobWiseCondidate->job_id)
                            ->get(['spok_user_id', 'company_id'])->toArray();
                        //  dd($spok_user_id_rows);
                        // Step 2: Build result as [company_id => spok_user_id]
                        $teamCaptainId = [];

                        foreach ($spok_user_id_rows as $row) {
                            if (! is_null($row->spok_user_id) && $row->spok_user_id != $AddJobWiseCondidate->submitted_by) {
                                $teamCaptainId[$row->company_id][] = $row->spok_user_id;
                            }
                        }
                        // dd($teamCaptainId);

                        // Step 3: Remove duplicates and reindex inner arrays
                        foreach ($teamCaptainId as $companyId => $spokIds) {
                            $teamCaptainId[$companyId] = array_values(array_unique($spokIds));
                        }

                        $candidateJoineddUser = 0;
                        $candidateJoineddTeam = 0;
                        $candidateJoineddUserbackout = -1500;
                        $candidateJoineddTeambackout = -30;

                        // Route the join/backout points into full-time keys or contract keys based on the job type.
                        $sstEmpType = DB::table('jobs')->where('id', $AddJobWiseCondidate->job_id)->value('employment_type');
                        $sstIsContract = ((int) $sstEmpType !== 0); // 0 = Full-time; Contract/Freelance/Part-time all use the higher weight
                        // Full-time keeps the base weights (join 1500 / team 30); contract uses higher weights (join 5000 / team 500).
                        $cjUserFT = $sstIsContract ? 0 : $candidateJoineddUser;
                        $boUserFT = $sstIsContract ? 0 : $candidateJoineddUserbackout;
                        $cjTeamFT = $sstIsContract ? 0 : $candidateJoineddTeam;
                        $boTeamFT = $sstIsContract ? 0 : $candidateJoineddTeambackout;
                        $cjUserCT = ($sstIsContract && $candidateJoineddUser != 0) ? 5000 : 0;
                        $boUserCT = ($sstIsContract && $candidateJoineddUserbackout != 0) ? -5000 : 0;
                        $cjTeamCT = ($sstIsContract && $candidateJoineddTeam != 0) ? 500 : 0;
                        $boTeamCT = ($sstIsContract && $candidateJoineddTeambackout != 0) ? -500 : 0;

                        $teamCaptainId = array_map(function ($arr) {
                            return array_values(array_filter($arr, function ($value) {
                                return $value != 0;
                            }));
                        }, $teamCaptainId);
                        // dd($teamCaptainId);
                        if (count($teamCaptainId) > 0) {

                            foreach ($teamCaptainId as $comId => $captainId) {
                                $teamRankElastic = [
                                    'company_id' => $comId,
                                    'user_id' => $captainId[0],
                                    'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                                    'profileUpload' => 0,
                                    'interviewScheduled' => 0,
                                    'interviewNoShow' => 0,
                                    'offerReleased' => 0,
                                    'candidateJoined' => 0,
                                    'jobsPosted' => 0,
                                    'feedbackGivenOnCVs' => 0,
                                    'postJobToVendorMarket' => 0,
                                    'outsourceJobsToVendors' => 0,
                                    'listBenchCandidates' => 0,
                                    'respondToBenchRequests' => 0,
                                    'interestedCandidates' => 0,
                                    'notInterestedCandidates' => 0,
                                    'teamCaptainInterviewScheduled' => 0,
                                    'teamCaptainInterviewNoShow' => 0,
                                    'teamCaptainOfferReleased' => 0,
                                    'teamCaptainCandidateJoined' => $cjTeamFT,
                                    'backoutTeam' => $boTeamFT,
                                    'teamCaptainCandidateJoinedContract' => $cjTeamCT,
                                    'backoutTeamContract' => $boTeamCT,
                                ];
                                GeneralMethod::teamRankElastic($teamRankElastic);
                            }

                        }

                        $teamRankElastic = [
                            'company_id' => $AddJobWiseCondidate->company_id,
                            'user_id' => $AddJobWiseCondidate->submitted_by,
                            'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                            'profileUpload' => 0,
                            'interviewScheduled' => 0,
                            'interviewNoShow' => 0,
                            'offerReleased' => 0,
                            'candidateJoined' => $cjUserFT,
                            'backout' => $boUserFT,
                            'candidateJoinedContract' => $cjUserCT,
                            'backoutContract' => $boUserCT,
                            'jobsPosted' => 0,
                            'feedbackGivenOnCVs' => 0,
                            'postJobToVendorMarket' => 0,
                            'outsourceJobsToVendors' => 0,
                            'listBenchCandidates' => 0,
                            'respondToBenchRequests' => 0,
                        ];

                        GeneralMethod::teamRankElastic($teamRankElastic);
                        // if($master_status == 6 ){
                        // 	return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);
                        // }
                    }
                }
            }

            if ($master_status == 6) {

                $of_offer_amount = $data->of_offer_amount;
                $of_joined_date = $data->of_joined_date;

                if (empty($sub_stages) || $sub_stages == '') {
                    return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => 'Please select joined sub stages Onboarded OR Backed Out']);

                }

            }

            // InReview 8 , Sourcing 1, Screening 2, Evaluation 3, Negotiation 4, Offered 5
            if ($master_status != 7) {
                $stages = [
                    8 => ['display' => 'InReview',   'order' => 1],
                    1 => ['display' => 'Sourcing',   'order' => 2],
                    2 => ['display' => 'Screening',  'order' => 3],
                    3 => ['display' => 'Evaluation', 'order' => 4],
                    4 => ['display' => 'Negotiation', 'order' => 5],
                    5 => ['display' => 'Offered',    'order' => 6],
                    6 => ['display' => 'Joined',    'order' => 7],
                    7 => ['display' => 'Rejected',    'order' => 8],
                ];

                $currentStageId = (int) ($addJobWiseCondidate->master_status_id ?: ($data->job_master_status_id ?: 8));
                $newStageId = (int) $master_status;

                // ✅ Ensure both exist in mapping
                if ($data->job_master_status_id == 7) {
                    // dd($data->job_master_status_id);
                    $stage_point_tb = DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->first();
                    // dd($stage_point_tb );
                    if ($stage_point_tb) {
                        if ($stage_point_tb->stage_id != 5 && $stage_point_tb->stage_id != 6) {
                            if ($stage_point_tb->stage_id != $master_status) {
                                $currentName = $stages[$master_status]['display'];
                                $newName = $stages[$stage_point_tb->stage_id]['display'];

                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from 
											<b style='color:#EF4444;'>Rejected</b> 
											to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
										Please complete the required step in 
										<b style='color:#F59E0B;'>{$newName}</b> first.",
                                ]);

                            }
                        } elseif ($stage_point_tb->stage_id == 5 || $stage_point_tb->stage_id == 6) {
                            if ($master_status != 1) {
                                $currentName = $stages[$master_status]['display'];
                                $newName = $stages[$stage_point_tb->stage_id]['display'];

                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from 
											<b style='color:#EF4444;'>Rejected</b> 
											to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
										Please complete the required step in 
										<b style='color:#F59E0B;'>{$newName}</b> first.",
                                ]);
                            }

                        }

                    }
                    DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->delete();
                    //  dd($master_status);
                    $values = [
                        'job_id' => $data->jobId,
                        'candidate_id' => $data->candidate_id,
                        'stage_id' => $master_status,
                        'user_id' => $data->submitted_by,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),

                    ];
                    DB::table('stage_point_tb')->insert($values);

                } else {

                    if (! isset($stages[$currentStageId]) || ! isset($stages[$newStageId])) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'Invalid stage transition.',
                        ]);
                    }

                    $currentOrder = $stages[$currentStageId]['order'];
                    $newOrder = $stages[$newStageId]['order'];

                    $currentName = $stages[$currentStageId]['display'];
                    $newName = $stages[$newStageId]['display'];

                    // 🔹 Check if jumping ahead
                    if ($newOrder > $currentOrder + 1) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName),
                        ]);
                    }

                    // dd($newOrder,$currentOrder);

                    // 🔹 Check if going backward
                    if ($newOrder < $currentOrder) {

                        if ($currentOrder != $newOrder + 1) {
                            return Reply::dataOnly([
                                'status' => 'candidatevalidation',
                                'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName),
                            ]);
                        }

                    }

                    $sourcing = 0;
                    $screening = 0;
                    $evaluation = 0;
                    $negotiation = 0;
                    // if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {
                    //  dd($newStageId,$currentStageId);
                    if ($newStageId > $currentStageId || $currentStageId == 8) {
                        if ($newStageId == 1) {
                            $sourcing = 1;
                        } elseif ($newStageId == 2) {
                            $screening = 1;
                        } elseif ($newStageId == 3) {
                            $evaluation = 1;
                        } elseif ($newStageId == 4) {
                            $negotiation = 1;
                        }

                    }

                    if ($newStageId < $currentStageId) {
                        if ($currentStageId == 1) {
                            $sourcing = -1;
                        } elseif ($currentStageId == 2) {
                            $screening = -1;
                        } elseif ($currentStageId == 3) {
                            $evaluation = -1;
                        } elseif ($currentStageId == 4) {
                            $negotiation = -1;
                        }
                    } elseif ($newStageId == 8 && $currentStageId == 1) {
                        $sourcing = -1;
                    }

                    $pendingTeamRankElastic = [
                        'company_id' => $data->company_id ?: ($addJobWiseCondidate->company_id ?: auth()->user()->company_id),
                        'user_id' => $data->submitted_by ?: ($addJobWiseCondidate->submitted_by ?: auth()->id()),
                        'candidate_id' => $data->candidate_id ?: ($addJobWiseCondidate->job_application_id ?: $addJobWiseCondidate->id),
                        'sourcing' => $sourcing,
                        'screening' => $screening,
                        'evaluation' => $evaluation,
                        'negotiation' => $negotiation,
                    ];

                    DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->delete();

                    $values = [
                        'job_id' => $data->jobId,
                        'candidate_id' => $data->candidate_id,
                        'stage_id' => $master_status,
                        'user_id' => $data->submitted_by,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),

                    ];
                    DB::table('stage_point_tb')->insert($values);

                }

                if ($data->job_master_status_id != 6) {

                    if ($master_status == 6) {

                        $of_offer_amount = $data->of_offer_amount;
                        $of_joined_date = $data->of_joined_date;

                        if ($data->job_master_status_id == 5) {
                            if (($of_offer_amount == 0 || $of_offer_amount == '') && ($of_joined_date == '0000-00-00 00:00:00' || empty($of_joined_date))) {
                                return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => "Please create the offer under the <b style='color:#F59E0B;'> Offer Tracker </b> tab, complete the required details <b style='color:#10B981;'> (Offer Amount and Date of Joining) </b>and release the offer. <br>After updating the details, please update the status to <b style='color:#10B981;'>Joining</b>."]);
                            }
                        } else {
                            return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => "Kindly update the status to <b style='color:#F59E0B;'>'Offer Stage'</b> and complete the <b>'Create Offer'</b> section by entering the <b style='color:#10b981;'>offered salary</b> and <b style='color:#3b82f6;'>expected date of joining</b>. Once all details are filled in, please proceed to update the status to <b style='color:#10B981;'>'Joining Stage'</b>."]);
                        }

                    }
                }
            }
        }

        $jobApplication = JobApplication::withoutGlobalScopes()->find($application_id);

        if ($master_status != 7) {

            $validations = GeneralMethod::candidateValidation($addJobWiseCondidate_id);
            if ($validations->status) {
                return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => $validations->message]);
            }

            if (JobApplication::where('job_applications.resume', '=', '')->where('job_applications.id', '=', $application_id)->exists()) {
                return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']);
            }

        }

        if (($addJobWiseCondidate->master_status_id == $master_status) && ($addJobWiseCondidate->child_status_id == $sub_stages)) {
            return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => 'Match current record with previous.!']);
            exit;
        }

        if (! empty(@$master_status)) {
            if ($jobApplication->master_status_id >= 0 || $addJobWiseCondidate->master_status_id >= 0) {
                $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $addJobWiseCondidate->master_status_id)->first();
                $prev_process_name = @$masterStatus->name;
            }
            $jobApplication->master_status_id = @$master_status; // 1
            $addJobWiseCondidate->master_status_id = @$master_status; // 1
        }

        if ($jobApplication->child_status_id > 0 || $addJobWiseCondidate->child_status_id > 0) {
            $subProcess = DB::table('candidate_status')
                ->select('candidate_status.name')
                ->where('candidate_status.company_id', $addJobWiseCondidate->company_id)
                ->where('candidate_status.id', $addJobWiseCondidate->child_status_id)
                ->first();
            $prev_process_child_name = @$subProcess->name;
        }

        if (! empty(@$sub_stages)) {
            $jobApplication->child_status_id = @$sub_stages; // 2
            $addJobWiseCondidate->child_status_id = @$sub_stages; // 2
        } else {
            $jobApplication->child_status_id = '';
            $addJobWiseCondidate->child_status_id = '';
        }

        $date = date('Y-m-d H:i:s');
        $values = [
            'event_id' => 2,
            'event_by_company_id' => auth()->user()->company_id,
            'event_by_user_id' => auth()->user()->id,
            // 'master_process_name' 		=>	@$jobApplication->job_id,
            // 'child_process_name' 			=>	@$master_status,

            'application_id' => @$jobApplication->id ? @$jobApplication->id : null,
            'application_email' => @$jobApplication->email ? @$jobApplication->email : null,
            'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null,
            'note' => '',
            'create_at' => @$date ? @$date : null,

            'update_at' => @$date ? @$date : null,
            'prev_process_name' => $prev_process_name ? $prev_process_name : null,
            'prev_process_child_name' => $prev_process_child_name ? $prev_process_child_name : null,
        ];

        if (@$master_status > 0) {
            $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
            $values['master_process_name'] = @$masterStatus->name;
        }
        if (@$sub_stages > 0) {
            $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
            $values['child_process_name'] = @$masterStatus->name;
        }
        if (! empty(@$comment)) {
            $values['note'] = @$comment;
        }

        $joined_date = '';
        if ((@$master_status > 0) && @$master_status == 6) {
            if (! empty($jobApplication->joined_date)) {
                $joined_date = date('d-M-Y', strtotime($jobApplication->joined_date));
                $values['note'] = 'Applicant joining date :: &nbsp;'.$joined_date;
            }
        }

        // DB::table('timeline')->insert($values);
        GeneralMethod::UpdateTimeline($values);

        // $jobApplication->save();
        DB::table('job_applications')->where('id', $application_id)->update([
            'master_status_id' => (@$master_status) ? @$master_status : @$master_status,
            'child_status_id' => (@$sub_stages) ? @$sub_stages : '',
        ]);

        $addJobWiseCondidate->save();

        if (! empty($pendingTeamRankElastic)) {
            GeneralMethod::teamRankElastic($pendingTeamRankElastic);
        }
        // dd($jobApplication);

        $jobId = $addJobWiseCondidate->job_id;
        $lastInsertedId = $addJobWiseCondidate->id;
        $wise_c = DB::table('job_applications')
            ->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'jobs.client_id', 'jobs.employment_type', 'add_job_wise_condidates.master_status_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->where('add_job_wise_condidates.id', '=', $lastInsertedId)->where('add_job_wise_condidates.job_id', '=', $jobId)->first();

        $clients_data = DB::table('clients')->select('company_id', 'self_company')->where('id', '=', @$wise_c->client_id)->first();

        $self_company = 1;

        if ($clients_data) {

            if ($clients_data->company_id != $company_id) {
                $self_company = 1;
            } else {
                if ($clients_data->self_company == 0) {
                    $self_company = $clients_data->self_company;
                } else {
                    $self_company = $clients_data->self_company;
                }

            }

        }

        $partnersData = User::join('job_assign', 'users.id', '=', 'job_assign.user_id')
            ->join('package_active', function ($join) {
                $join->on('users.company_id', '=', 'package_active.company_id')
                    ->where('package_active.emails_balance', '>', 0)
                    ->where('package_active.startdate', '<=', now())
                    ->where('package_active.enddate', '>=', now())
                    ->where('package_active.packages_status', 1);
            })
            ->where('job_assign.job_id', '=', $jobId)
            ->pluck('users.email')
            ->toArray();

        $recommendedDataList = DB::table('recommendation_relation');
        $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
        $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
            $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
            $join->on('job_applications.job_id', '=', 'jobs.id');
        });
        $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
            $join->on('companies.id', '=', 'package_active.company_id')
                ->where('package_active.emails_balance', '>', 0)
                ->where('package_active.startdate', '<=', now())
                ->where('package_active.enddate', '>=', now())
                ->where('package_active.packages_status', 1);
        });
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
        $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
        $recommendedDataList = $recommendedDataList->toArray();

        // dd($recommendedDataList);

        $companyOrClientName = GeneralMethod::companyOrClientName($wise_c->client_id);

        // dd($companyOrClientName);

        $subject = "Status Changed |$wise_c->name | $jobApplication->full_name | $wise_c->title | $companyOrClientName->companyOrclientName | R-$wise_c->job_id";

        $company_or_client = GeneralMethod::companyDetailsByClient($jobId);

        $name = '--------';
        if (@$wise_c->master_status_id == 8) {
            $name = '';
        }
        if (! empty(@$wise_c->childStatus) && @$wise_c->childStatus > 0) {
            $childStatus = '';
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $wise_c->childStatus)->first();
            if (! empty($childStatus)) {
                $name = @$childStatus->name;
            }
        }

        $np = $jobApplication->np_id;
        if (empty($np)) {
            $np = 'N/A';
        }
        if ($jobApplication->serving_notice_period_mode == 0) {
            $np = $jobApplication->np_id.' Days';
            if ($jobApplication->np_id == 1) {
                $np = $jobApplication->np_id.'  Day';
            }
        } elseif ($jobApplication->serving_notice_period_mode == 1) {
            $np = $jobApplication->np_id;
            if ($np != 'Immediate') {
                $np = 'SNP LWD '.GeneralMethod::ymdTodmyNotTime($jobApplication->np_id);
            }
        }

        if ($master_status != 5 && $master_status != 6) {
            $data = [
                'VendorTeam' => @$wise_c->company_name,
                'ReqID' => 'R'.@$wise_c->job_id,
                'Reqname' => @$wise_c->title,
                'CompanyNameClientname' => $company_or_client,
                'CandidateStatus' => $jobApplication->duplicate,
                'prev_process_name' => $prev_process_name,
                'CurrentStage' => @$wise_c->name,
                'SubStage' => $name,
                'CandidateName' => $jobApplication->full_name,
                'Experience' => $jobApplication->total_exp,
                'Position' => $jobApplication->current_designation,
                'NoticePeriod' => $np,
                'comments' => @$comment,
                'RefferedBy' => @$wise_c->posted_by,
                'companyOrclientName' => $companyOrClientName->companyOrclientName,
                'selfCompany' => $self_company,
                'SubmittedOn' => date('d-M-Y H:i:s', strtotime(@$wise_c->created_at)),
            ];
            // config(['mail.from.name' => auth()->user()->name]);
            if ($master_status == 7) {

                $notification = DB::table('clients')->where('id', '=', @$wise_c->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();

                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
                $cc = []; // $notification; //array_merge($partnersData, $recommendedDataList);

                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = [];
                $checkMailPermision = GeneralMethod::mailPermission(3);
                if ($checkMailPermision) {
                    $mailTypeSubject = GeneralMethod::mailTypeSubject(3, 1);
                    $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                    $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                    // dd($to_Mail_Id);
                    QueueNotificationMailService::dispatch(
                        $to_Mail_Id,
                        $subject,
                        'email.candidate.rejected',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');

                    //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.rejected', (object) $data, $attachments, $fromAddress, $senderName,$cc);
                }
                // Mail::to(auth()->user()->email)->cc($cc)->send(new RejectedMail((object) $data,$subject));
            } else {

                // $mailConfig = [
                // 	'driver' => 'smtp',
                // 	'host' => 'smtp.gmail.com',
                // 	'port' => 587,
                // 	'from' => ['address' => '4belldevelopers@gmail.com', 'name' => 'Your Name'],
                // 	'encryption' => 'tls',
                // 	'username' => '4belldevelopers@gmail.com',
                // 	'password' => 'ghhcefkjvqaqkmkg',
                // ];

                // Mail::send([], [], function (Message $message) use ($mailConfig) {
                // 	$message->to(auth()->user()->email)
                // 			->subject('Subject of the Email')
                // 			->setBody('Body of the Email', 'text/html');

                // 	// Set the mail config dynamically
                // 	$message->getSwiftMessage()->getHeaders()->addTextHeader('X-Mailer', 'Laravel');
                // 	$transport = new \Swift_SmtpTransport($mailConfig['host'], $mailConfig['port']);
                // 	$transport->setUsername($mailConfig['username']);
                // 	$transport->setPassword($mailConfig['password']);
                // 	$transport->setEncryption($mailConfig['encryption']);
                // 	$mailer = new \Swift_Mailer($transport);
                // 	Mail::setSwiftMailer($mailer);
                // });

                $notification = DB::table('clients')->where('id', '=', @$wise_c->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();
                $email_to_remove = auth()->user()->email;
                $index = array_search($email_to_remove, $notification);
                if ($index !== false) {
                    unset($notification[$index]);
                    $notification = array_values($notification);
                }
                $cc = []; // $notification; //array_merge($partnersData, $recommendedDataList);

                $basecompany = GeneralMethod::baseCompany();
                $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
                $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
                $attachments = [];
                //  dd(auth()->user()->email, $subject,'email.candidate.change-status', (object) $data, $attachments, $fromAddress, $senderName,$cc);

                $checkMailPermision = GeneralMethod::mailPermission(6);
                if ($checkMailPermision) {
                    $mailTypeSubject = GeneralMethod::mailTypeSubject(6, 1);
                    $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                    $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                    QueueNotificationMailService::dispatch(
                        $to_Mail_Id,
                        $subject,
                        'email.candidate.change-status',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');

                    //    $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.change-status', (object) $data, $attachments, $fromAddress, $senderName,$cc);
                }
                //  dd("d");

                // Mail::to(auth()->user()->email)->cc($cc)->send(new ChangeStatusMail((object) $data,$subject));

            }

        }

        if ($master_status == 5) {

            $rowData = DB::table('job_applications')->select('*')->where('id', $application_id)->first();
            $rowDataOffer = DB::table('offer_job_applications')->select('*')->where('application_id', $application_id)->where('job_id', $addJobWiseCondidate->job_id)->first();

            $values = [
                'application_id' => $rowData->id,
                'submitted_by' => $rowData->submitted_by,
                'company_id' => $rowData->company_id,
                'np_id' => $rowData->np_id,
                'full_name' => $rowData->full_name,

                'email' => $rowData->email,
                'phone' => $rowData->phone,
                'gender' => $rowData->gender,
                'dob' => $rowData->dob,
                'country' => $rowData->country,

                'state' => $rowData->state,
                'city' => $rowData->city,
                'secondrary_email' => $rowData->secondrary_email,
                'secondary_mobile' => $rowData->secondary_mobile,
                'skills' => $rowData->skills,

                'photo' => $rowData->photo,
                'resume' => $rowData->resume,
                'rating' => @$rowData->rating,
                'skype_id' => $rowData->skype_id,
                'cover_letter' => $rowData->cover_letter,

                'job_id' => $addJobWiseCondidate->job_id,
                'status_id' => $rowData->status_id,
                'is_viewed' => $rowData->is_viewed,
                'column_priority' => $rowData->column_priority,
                'total_exp' => $rowData->total_exp,

                'rel_exp' => $rowData->rel_exp,
                'passport' => $rowData->passport,
                'ctc' => $rowData->ctc,
                'ectc' => $rowData->ectc,
                'current_location' => $rowData->current_location,

                'current_designation' => $rowData->current_designation,
                'current_company' => $rowData->current_company,
                'process_id' => $rowData->process_id,
                'qualification' => $rowData->qualification,
                'master_status_id' => $rowData->master_status_id,

                'child_status_id' => $rowData->child_status_id,
                'serving_notice_period_mode' => $rowData->serving_notice_period_mode,
                'proposed_ctc' => $rowData->proposed_ctc,
                'any_offer' => $rowData->any_offer,
                'document' => $rowData->document,

                'sub_for_approval' => $rowData->sub_for_approval,
                'offer_approved' => $rowData->offer_approved,
                'offer_release' => $rowData->offer_release,
                'offer_acceptance' => $rowData->offer_acceptance,
                'joined' => $addJobWiseCondidate->id,

                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ];
            if (empty($rowDataOffer)) {
                DB::table('offer_job_applications')->insert($values);
                $offer_id = DB::getPdo()->lastInsertId();
            } else {
                $offer_id = $rowDataOffer->id;
                DB::table('offer_job_applications')->where('id', $rowDataOffer->id)->update([
                    'joined_date' => null,
                    'offer_mode' => null,
                    'offer_amount' => null,
                    'offer_currency' => null,
                ]);
            }

            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();

                // 3. Execute update with debugging
                $updateParams_e = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $addJobWiseCondidate_id,
                    'body' => [
                        'doc' => [
                            'of_joined_date' => null,
                            'of_offer_currency' => null,
                            'of_offer_mode' => null,
                            'of_offer_amount' => null,
                            'of_offer_acceptance' => null,
                            'of_offer_approved' => null,
                            'of_created_date' => null,
                            'of_expired_date' => null,
                            'send_accept_date_time' => null,
                            'send_approved_date_time' => null,
                            'approved_date_time' => null,
                            'accept_date_time' => null,
                            'offer_id' => $offer_id,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => false, // Eventual consistency for 10K concurrent scale
                ];
                // dd($updateParams);
                $redudds = $this->elasticService->getClient()->update($updateParams_e);
                GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);

                // dd($redudds,$addJobWiseCondidate_id);
            }

            $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $application_id)->where('interview_job_id', $wise_c->job_id)->orderBy('interview_schedules.id', 'DESC')->first();
            $schedule_date = '';
            if ($intRow) {
                $schedule_date = @$intRow->schedule_date;
                $schedule_date = date('d M, Y H:i', strtotime($schedule_date)).'(IST)';
            }

            $data = [
                'VendorTeam' => @$wise_c->company_name,
                'ReqID' => 'R'.@$wise_c->job_id,
                'Reqname' => @$wise_c->title,
                'CompanyNameClientname' => $companyOrClientName->companyOrclientName,
                'CandidateStatus' => $jobApplication->duplicate,
                'prev_process_name' => $prev_process_name,
                'CurrentStage' => @$wise_c->name,
                'SubStage' => $name,
                'CandidateName' => $jobApplication->full_name,
                'Experience' => $jobApplication->total_exp,
                'Position' => $jobApplication->current_designation,
                'NoticePeriod' => $np,
                'comments' => @$comment,
                'interviewDateTime' => @$schedule_date,
                'linkedinId' => '',
                'RefferedBy' => @$wise_c->posted_by,
                'SubmittedOn' => date('d-M-Y H:i:s', strtotime(@$wise_c->created_at)),
                'companyOrclientName' => $companyOrClientName->companyOrclientName,
                'selfCompany' => $self_company,
                'OfferAmount' => '',
                'Joiningdate' => '',
            ];
            // dd($data);
            // 		config(['mail.from.name' => auth()->user()->name]);

            $notification = DB::table('clients')->where('id', '=', @$wise_c->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();
            $email_to_remove = auth()->user()->email;
            $index = array_search($email_to_remove, $notification);
            if ($index !== false) {
                unset($notification[$index]);
                $notification = array_values($notification);
            }
            $cc = []; // $notification; //array_merge($partnersData, $recommendedDataList);
            // $cc = array_merge($partnersData, $recommendedDataList);

            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];
            $checkMailPermision = GeneralMethod::mailPermission(4);
            if ($checkMailPermision) {
                $mailTypeSubject = GeneralMethod::mailTypeSubject(4, 1);
                $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                // dd($to_Mail_Id);

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.candidate.offer',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');

                //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.offer', (object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

            $jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null;
            $tb_ranks = ['company_id' => auth()->user()->company_id, 'user_id' => $jobApplication->submitted_by, 'job_id' => @$jobIdGet, 'status' => 1, 'application_id' => @$jobApplication->id];
            DB::table('tb_ranks')->insert($tb_ranks);
            // 		Mail::to(auth()->user()->email)->cc($cc)->send(new OfferMail((object) $data,$subject));

        }

        if ($master_status == 6) {

            $oderVal = DB::table('offer_job_applications')->where('application_id', @$application_id)->where('job_id', $addJobWiseCondidate->job_id)->first();
            $offer_id = @$oderVal->id;

            $offer_job_applications = DB::table('offer_job_applications')
                ->select('joined as wiseId')
                ->where('id', @$offer_id)
                ->first();

            $master_status_check = DB::table('offer_job_applications')
                ->select('master_status_id')
                ->where('id', @$offer_id)
                ->first();

            $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($offer_job_applications->wiseId);

            $spok_user_id_rows = DB::table('job_assign')
                ->where('job_id', $AddJobWiseCondidate->job_id)
                ->get(['spok_user_id', 'company_id'])->toArray();

            // Step 2: Build result as [company_id => spok_user_id]
            $teamCaptainId = [];

            foreach ($spok_user_id_rows as $row) {
                if (
                    ! is_null($row->spok_user_id) &&
                    $row->spok_user_id != $AddJobWiseCondidate->submitted_by
                ) {
                    $teamCaptainId[$row->company_id][] = $row->spok_user_id;
                }
            }

            // Step 3: Remove duplicates and reindex inner arrays
            foreach ($teamCaptainId as $companyId => $spokIds) {
                $teamCaptainId[$companyId] = array_values(array_unique($spokIds));
            }

            $join_update_date_check = date('Y-m-d H:i:s', strtotime($oderVal->joined_date));
            $join_update_date_check = Carbon::parse($join_update_date_check);
            $today = Carbon::today();
            $diffInDays = abs($today->diffInDays($join_update_date_check));

            $candidateJoineddUser = 0;
            $candidateJoineddTeam = 0;
            $candidateJoineddUserbackout = 0;
            $candidateJoineddTeambackout = 0;

            if ($master_status_check->master_status_id == 6) { // $diffInDays<=30 &&
                if (! empty($sub_stages) || $sub_stages != '') {
                    $status_row = DB::table('candidate_status')->select('*')->where('company_id', user()->company_id)->where('id', '=', $sub_stages)->first();
                    if (@$status_row->name == 'Onboarded') {
                        $candidateJoineddUser = 1500;
                        $candidateJoineddTeam = 30;
                    } else {
                        $candidateJoineddUserbackout = -1500;
                        $candidateJoineddTeambackout = -30;
                    }
                }

            } elseif ($master_status_check->master_status_id == 5) {  // $diffInDays<=30 &&
                $candidateJoineddUser = 1500;
                $candidateJoineddTeam = 30;
            }

            // Route the join/backout points into full-time keys or contract keys based on the job type.
            $sstIsContract = ((int) @$wise_c->employment_type !== 0); // 0 = Full-time; Contract/Freelance/Part-time all use the higher weight
            $cjUserFT = $sstIsContract ? 0 : $candidateJoineddUser;
            $cjUserCT = ($sstIsContract && $candidateJoineddUser != 0) ? 5000 : 0;
            $boUserFT = $sstIsContract ? 0 : $candidateJoineddUserbackout;
            $boUserCT = ($sstIsContract && $candidateJoineddUserbackout != 0) ? -5000 : 0;
            $cjTeamFT = $sstIsContract ? 0 : $candidateJoineddTeam;
            $cjTeamCT = ($sstIsContract && $candidateJoineddTeam != 0) ? 500 : 0;
            $boTeamFT = $sstIsContract ? 0 : $candidateJoineddTeambackout;
            $boTeamCT = ($sstIsContract && $candidateJoineddTeambackout != 0) ? -500 : 0;

            $teamCaptainId = array_map(function ($arr) {
                return array_values(array_filter($arr, function ($value) {
                    return $value != 0;
                }));
            }, $teamCaptainId);

            if (count($teamCaptainId) > 0) {

                foreach ($teamCaptainId as $comId => $captainId) {
                    $teamRankElastic = [
                        'company_id' => $comId,
                        'user_id' => $captainId[0],
                        'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                        'profileUpload' => 0,
                        'interviewScheduled' => 0,
                        'interviewNoShow' => 0,
                        'offerReleased' => 0,
                        'candidateJoined' => 0,
                        'jobsPosted' => 0,
                        'feedbackGivenOnCVs' => 0,
                        'postJobToVendorMarket' => 0,
                        'outsourceJobsToVendors' => 0,
                        'listBenchCandidates' => 0,
                        'respondToBenchRequests' => 0,
                        'interestedCandidates' => 0,
                        'notInterestedCandidates' => 0,
                        'teamCaptainInterviewScheduled' => 0,
                        'teamCaptainInterviewNoShow' => 0,
                        'teamCaptainOfferReleased' => 0,
                        'teamCaptainCandidateJoined' => $cjTeamFT,
                        'backoutTeam' => $boTeamFT,
                        'teamCaptainCandidateJoinedContract' => $cjTeamCT,
                        'backoutTeamContract' => $boTeamCT,
                    ];
                    GeneralMethod::teamRankElastic($teamRankElastic);
                }

            }

            $teamRankElastic = [
                'company_id' => $AddJobWiseCondidate->company_id,
                'user_id' => $AddJobWiseCondidate->submitted_by,
                'candidate_id' => $AddJobWiseCondidate->job_application_id ?: $AddJobWiseCondidate->id,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => $cjUserFT,
                'backout' => $boUserFT,
                'candidateJoinedContract' => $cjUserCT,
                'backoutContract' => $boUserCT,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
            ];

            DB::table('offer_job_applications')
                ->where('id', @$offer_id)
                ->update(['master_status_id' => 6]);

            GeneralMethod::teamRankElastic($teamRankElastic);

            $of_currency = $currency[(@$oderVal->offer_currency) ? $oderVal->offer_currency : 0];
            $of_mode = $salaryModeOffer[(@$oderVal->offer_mode) ? $oderVal->offer_mode : 0];

            $data = [
                'VendorTeam' => @$wise_c->company_name,
                'ReqID' => 'R'.@$wise_c->job_id,
                'Reqname' => @$wise_c->title,
                'CompanyNameClientname' => $companyOrClientName->companyOrclientName,
                'CandidateStatus' => $jobApplication->duplicate,
                'prev_process_name' => $prev_process_name,
                'CurrentStage' => @$wise_c->name,
                'SubStage' => $name,
                'CandidateName' => $jobApplication->full_name,
                'Experience' => $jobApplication->total_exp,
                'Position' => $jobApplication->current_designation,
                'NoticePeriod' => $np,
                'employment_type' => (@$wise_c->employment_type == 1) ? 'Contract' : 'Full-time',
                'comments' => @$comment,
                'RefferedBy' => @$wise_c->posted_by,
                'SubmittedOn' => date('d-M-Y H:i:s', strtotime(@$wise_c->created_at)),
                'companyOrclientName' => $companyOrClientName->companyOrclientName,
                'selfCompany' => $self_company,
                'OfferAmount' => @$of_currency.' '.@$oderVal->offer_amount.' /'.@$of_mode,
                'Joiningdate' => date('d-M-Y H:i:s', strtotime(@$oderVal->joined_date)),
            ];
            // dd($data);
            // 			config(['mail.from.name' => auth()->user()->name]);

            $notification = DB::table('clients')->where('id', '=', @$wise_c->client_id)->where('self_company', '=', 1)->pluck('email')->toArray();
            $email_to_remove = auth()->user()->email;
            $index = array_search($email_to_remove, $notification);
            if ($index !== false) {
                unset($notification[$index]);
                $notification = array_values($notification);
            }
            $cc = $notification; // array_merge($partnersData, $recommendedDataList);

            // $cc = array_merge($partnersData, $recommendedDataList);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];
            $checkMailPermision = GeneralMethod::mailPermission(5);
            if ($checkMailPermision) {
                $mailTypeSubject = GeneralMethod::mailTypeSubject(5, 1);
                $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
                $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                // dd($to_Mail_Id);
                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.candidate.joined-us-today',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');

                //  $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.candidate.joined-us-today', (object) $data, $attachments, $fromAddress, $senderName,$cc);

            }
            // 			Mail::to(auth()->user()->email)->cc($cc)->send(new JoinedUsTodayMail((object) $data, $subject));

            $valuesJoin = [
                'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null,
                'master_status_id' => @$addJobWiseCondidate->master_status_id ? @$addJobWiseCondidate->master_status_id : 6,
                'child_status_id' => @$addJobWiseCondidate->child_status_id ? @$addJobWiseCondidate->child_status_id : null,
                'jobApplication' => @$application_id,
                'addJobWiseCondidate' => @$addJobWiseCondidate_id,
                'offer_amount' => (@$oderVal->offer_amount) ? @$oderVal->offer_amount : 0,
                'joined_date' => @$oderVal->joined_date,
                'offer_currency' => (@$oderVal->offer_currency) ? @$oderVal->offer_currency : 0,
                'offer_mode' => (@$oderVal->offer_mode) ? @$oderVal->offer_mode : 0,
            ];
            DB::table('join_applications')->insert($valuesJoin);
            $jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null;
            $tb_ranks = ['company_id' => auth()->user()->company_id, 'user_id' => $jobApplication->submitted_by, 'job_id' => @$jobIdGet, 'status' => 6, 'application_id' => @$jobApplication->id];
            DB::table('tb_ranks')->insert($tb_ranks);

        }

        if (count($cc) > 0) {
            foreach ($cc as $email) {
                $client_company_id = 0;
                $useremail = DB::table('users')->select('*')->where('email', $email)->first();
                if (! empty($useremail)) {
                    $client_company_id = @$useremail->company_id;
                }
                // dd($client_company_id);
                if (@$client_company_id > 0) {
                    $event_name = 'emails';
                    $event_table_name = 'job applications stage change';
                    $event_table_id = @$client_company_id;
                    GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
                }
            }
        }

        // 1. First verify the document exists

        // dd($addJobWiseCondidate_id);

        if ($exists) {

            // 2. Prepare the update timestamp
            $updateTime = now()->toIso8601String();
            $master_status_name = DB::table('master_status')
                ->select('*')
                ->where('id', (int) $master_status)
                ->first('name');

            // 3. Execute update with debugging
            $updateParams = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
                'body' => [
                    'doc' => [
                        'job_master_status_id' => (int) $master_status,
                        'childStatus' => (int) $sub_stages,
                        'name' => $master_status_name->name,
                        'css_style' => $master_status_name->css_style,
                        'updated_at' => $updateTime,
                    ],
                    'doc_as_upsert' => false, // Changed to false since we verified existence
                ],
                'refresh' => true, // Immediate index refresh for real-time stage listing consistency
            ];
            // dd($updateParams);
            $redud = $this->elasticService->getClient()->update($updateParams);
            GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);

            // dd($redud);
        }

        $currentUserId = $this->user->id ?? auth()->id();
        $cachekeys = [
            'alljobslist_version_user_'.$currentUserId,
            'allcvtracker_version_user_'.$currentUserId,
            'allapptracker_version_user_'.$currentUserId,
            'allclientapp_version_user_'.$currentUserId,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $finalJobId = (int) ($addJobWiseCondidate->job_id ?? $jobApplication->job_id ?? 0);
        $finalCompanyId = (int) ($addJobWiseCondidate->company_id ?? $company_id ?? auth()->user()->company_id ?? 0);
        if ($finalJobId > 0 && $finalCompanyId > 0) {
            app(\App\Services\Apptracker\CounterService::class)->onStageMoved($finalJobId, (int) $application_id, (int) $master_status, $finalCompanyId);
        }

        try {
            $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', (int) $application_id)->pluck('id')->toArray();
            \App\Events\CandidateDrawerUpdated::dispatch(
                (int) $finalCompanyId,
                (int) $application_id,
                time(),
                'stage_updated',
                array_map('intval', $wiseIds),
                null,
                null,
                null,
                (int) $master_status,
                (int) ($data->job_master_status_id ?? 8),
                (int) $finalJobId
            );
        } catch (\Throwable $e) {
            \Log::warning('stage_submit CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);

    }

    public function clientcreate($jobId = null, $appId = null)
    {

        $jobId = GeneralMethod::value_decrypt($jobId);
        // Step 4: decrypt $appId symmetrically with $jobId.
        $appId = GeneralMethod::value_decrypt($appId);

        $this->pageTitle = __('Add Candidate');
        abort_if(Gate::denies('add_job_applications'), 403);

        $eventId = 4;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        // Step 2: coins are consumed only on new-candidate creation, not on edit.
        $isEdit = ! empty($appId) && (int) $appId !== 0;
        if (! $isEdit) {
            $packageStatus = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
            if ($packageStatus->status) {
                session()->flash('packageStatus', $packageStatus->message);
                $url = url('service_company/apptracker/clientapp/'.@$jobId.'/all');

                return redirect($url);
            }
        }

        // $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
        // if ($packageStatus != 1) {
        // 	$url = url('service_company/apptracker/clientapp/' . @$jobId . '/all');
        // 	return redirect($url);
        // }

        $company_id = '';
        $company_id = auth()->user()->company_id;

        // Step 3: during edit the job may be closed or paused — only enforce
        // active status and final_status=1 when creating a new application.
        $jobQuery = DB::table('recommendation_relation')
            ->select('jobs.id', 'jobs.title', 'jobs.status', 'recommendation_relation.id as recommendation_relation_id', 'recommendation_relation.final_status')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->where('jobs.id', '=', $jobId)
            ->where('recommendation_relation.to_company_id', '=', $company_id);
        if (! $isEdit) {
            $jobQuery->where('jobs.status', '=', 'active')
                ->where(function ($query) {
                    $query->where('recommendation_relation.final_status', '=', 1)
                        ->orWhere('recommendation_relation.final_status', '=', 2);
                });
        }
        $this->job = $jobQuery->count();
        if ($this->job == 0) {
            return abort(404);
        }

        $this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];
        $chkAdminRole = $this->user->hasRole('admin');

        $this->qualification = ReferenceDataCache::qualifications();
        $this->jobId = $jobId;
        $this->appId = $appId;

        $this->jobDetail = DB::table('jobs')
            ->select('jobs.id', 'jobs.company_id', 'jobs.total_positions', 'jobs.title', 'jobs.min_salary', 'jobs.max_salary', 'jobs.created_at', 'jobs.min_exp', 'jobs.max_exp', 'jobs.status', 'clients.name as client_name', 'client_location.city', 'client_location.postcode', 'client_location.city as location_city', 'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.min_salary_currency', 'jobs.max_salary_currency')
            // ->join('job_assign', 'job_assign.job_id', 'jobs.id')
            ->join('clients', 'clients.id', 'jobs.client_id')
            ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('jobs.id', '=', $jobId)
            // ->where('jobs.company_id', '=', $this->user->company_id)
            ->first();

        $client_company_id = $this->jobDetail->company_id;

        $this->page_event = 'ADD';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->interview_mode = ReferenceDataCache::interviewModes();
        // $this->countries = Country::all();

        $this->statelist = GeneralMethod::statelist(101);
        // echo '<pre>'; print_r($this->statelist); exit;

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            ->where('job_additional_detail.company_id', '=', $client_company_id)
            ->where('job_additional_detail.job_id', '=', $this->jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->jobLocation = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name', 'state_list.state as state_name')
            ->where('client_location_jobs.job_id', $this->jobId)
            ->orderBy('client_location_jobs.id', 'DESC')
            ->first();

        // $this->jobSkill = DB::table('job_metronics_skills')
        // 	->leftJoin('candidate_job_skill_exp', function($join) {
        // 		$join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id'); // Add the candidate condition
        // 	})
        // 	->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
        // 	->where('job_metronics_skills.job_id', '=', $this->jobId)
        // 	->groupBy('job_metronics_skills.id')
        // 	->get();

        $candidateId = ! empty($this->appId) ? (int) $this->appId : 0;
        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) use ($candidateId) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $candidateId);
            })
            ->select(
                DB::raw('MIN(job_metronics_skills.id) as job_skill_id'), // alias as job_skill_id for blade form name keys
                DB::raw('MIN(job_metronics_skills.id) as id'),
                'job_metronics_skills.skill_name',
                DB::raw('MAX(candidate_job_skill_exp.skill_value) as skill_value'),
                DB::raw('MAX(candidate_job_skill_exp.rating) as rating')
            )
            ->where('job_metronics_skills.job_id', $this->jobId)
            ->whereNotNull('job_metronics_skills.skill_name')
            ->whereRaw("TRIM(job_metronics_skills.skill_name) != ''")
            ->groupBy('job_metronics_skills.skill_name')
            ->get();

        $this->skillShow = false;

        $this->countries = GeneralMethod::getAllCountryList();
        $this->statelist = '';
        $this->cityListArr = '';
        $this->cities = GeneralMethod::allCities();
        $this->default_loc = GeneralMethod::allCitiesSelected([]);
        $this->currentPage = 1;
        $this->recruiterNoteTemplates = DB::table('recruiter_note_master')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('company_id')->orWhere('company_id', auth()->user()->company_id);
            })
            ->orderBy('id')->get();

        return response()->view('service_company.job-applications.clientcreate', $this->data)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');

    }

    public function jobQuestion($jobID = null, $applicationId = null)
    {
        $this->job = Job::find($jobID);
        if (! $this->job) {
            return Reply::dataOnly(['status' => 'success', 'view' => '', 'requiredColumnsView' => '', 'requiredSectionsView' => '', 'count' => 0]);
        }
        $this->jobQuestion = $this->job->questions()->with(['answers' => function ($query) use ($jobID, $applicationId) {
            $query->where(['job_application_id' => $applicationId, 'job_id' => $jobID]);
        }])->get();
        $this->gender = ['male' => __('modules.front.male'), 'female' => __('modules.front.female'), 'others' => __('modules.front.others')];

        $view = view()->exists('service_company.job-applications.job-question')
            ? view('service_company.job-applications.job-question', $this->data)->render()
            : (view()->exists('admin.job-applications.job-question') ? view('admin.job-applications.job-question', $this->data)->render() : '');
        $options = ['job' => $this->job, 'gender' => $this->gender];
        $sections = ['section_visibility' => $this->job->section_visibility];
        if ($applicationId) {
            $application = JobApplication::select('id', 'gender', 'dob', 'country', 'state', 'city')->where('id', $applicationId)->first();
            if ($application) {
                $options = \Illuminate\Support\Arr::add($options, 'application', $application);
                $sections = \Illuminate\Support\Arr::add($sections, 'application', $application);
            }
        }
        $requiredColumnsView = view()->exists('service_company.job-applications.required-columns')
            ? view('service_company.job-applications.required-columns', $options)->render()
            : (view()->exists('admin.job-applications.required-columns') ? view('admin.job-applications.required-columns', $options)->render() : '');
        $requiredSectionsView = view()->exists('service_company.job-applications.required-sections')
            ? view('service_company.job-applications.required-sections', $sections)->render()
            : (view()->exists('admin.job-applications.required-sections') ? view('admin.job-applications.required-sections', $sections)->render() : '');
        $count = count($this->jobQuestion);
        $data = ['status' => 'success', 'view' => $view, 'requiredColumnsView' => $requiredColumnsView, 'requiredSectionsView' => $requiredSectionsView, 'count' => $count];
        if ($applicationId && isset($application)) {
            $data = \Illuminate\Support\Arr::add($data, 'application', $application);
        }

        return Reply::dataOnly($data);
    }

    public function clientappstore(Request $request)
    {

        abort_if(Gate::denies('add_job_applications'), 403);

        // $packageStatus = GeneralMethod::validatePackageRet('CVACCESS');
        // if ($packageStatus != 1) {
        // 	$url = url('service_company/apptracker/' . GeneralMethod::value_encrypt(@$request->job_id) . '/all');
        // 	return redirect($url);
        // }

        $validator = Validator::make($request->all(), array_merge([
            'email' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], CandidateNoticePeriodState::validationRules($request->input('serving_notice_period'))), array_merge([
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.disposable_email' => 'Temporary emails are not allowed',
        ], CandidateNoticePeriodState::validationMessages()));

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'mailcheck', 'msgType' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $company_id = '';
        $company_id = auth()->user()->company_id;
        //   dd($this->user->company_id);

        $eventId = 4;
        $coinsDeductions = GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $this->user->company_id, '', '');
        // dd($coinsDeductions);
        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        $applicationStatus = ApplicationStatus::first();
        $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.company_id')->where('jobs.id', '=', $request->job_id)->first();
        $job_application_arr = [];

        if (! empty($request->full_name)) {
            $job_application_arr['full_name'] = ucwords(strtolower($request->full_name));
        }
        if (! empty($request->job_id)) {
            $job_application_arr['job_id'] = $request->job_id;
        }
        $job_application_arr['status_id'] = 8;
        if (! empty($request->email)) {
            $job_application_arr['email'] = $request->email;
        }
        if (! empty($request->phone)) {
            $job_application_arr['phone'] = $request->phone;
        }

        $job_application_arr['column_priority'] = 0;

        $job_application_arr['phone_code'] = ($request->primary_country_code) ? @$request->primary_country_code : null;
        $job_application_arr['second_phone_code'] = ($request->secondary_country_code) ? @$request->secondary_country_code : null;

        // $total_exp = @$request->total_exp_year.'.'.@$request->total_exp_month;

        $total_exp = (isset($request->total_exp_year) && $request->total_exp_year !== '' || isset($request->total_exp_month) && $request->total_exp_month !== '') ? $request->total_exp_year.'.'.$request->total_exp_month : null;

        $rel_exp = (isset($request->rel_exp_year) && $request->rel_exp_year !== '' || isset($request->rel_exp_month) && $request->rel_exp_month !== '') ? $request->rel_exp_year.'.'.$request->rel_exp_month : null;

        // $rel_exp = @$request->rel_exp_year.'.'.@$request->rel_exp_month;

        // if (!empty($request->total_exp)) {
        $job_application_arr['total_exp'] = $total_exp; // $request->total_exp;
        // }
        // if (!empty($request->rel_exp)) {
        $job_application_arr['rel_exp'] = $rel_exp; // $request->rel_exp;
        // }

        $currency = GeneralMethod::currency();  // Currency code

        $ecurrency = $currency[$request->current_ectc_cur];
        $econvertedAmount = $request->bill_annual;
        if ($request->current_ectc_cur > 0) {
            $econvertedAmount = GeneralMethod::convertToINR($econvertedAmount, $ecurrency);
        }

        $ectcdivided = GeneralMethod::salaryDividedByMode(0, $econvertedAmount);

        $job_application_arr['ectc_yearly'] = @$ectcdivided->yearly;
        $job_application_arr['ectc_monthly'] = @$ectcdivided->monthly;
        $job_application_arr['ectc_hourly'] = @$ectcdivided->hourly;

        $currency = $currency[$request->current_ctc_cur];
        $convertedAmount = $request->pay_annual;
        if ($request->current_ctc_cur > 0) {
            $convertedAmount = GeneralMethod::convertToINR($convertedAmount, $currency);
        }

        $ctcdivided = GeneralMethod::salaryDividedByMode(0, $convertedAmount);

        $job_application_arr['ctc_yearly'] = @$ctcdivided->yearly;
        $job_application_arr['ctc_monthly'] = @$ctcdivided->monthly;
        $job_application_arr['ctc_hourly'] = @$ctcdivided->hourly;

        if (! empty($request->ctc)) {
            $job_application_arr['ctc'] = $request->ctc;
        }
        if (! empty($request->current_ctc_cur)) {
            $job_application_arr['current_ctc_cur'] = $request->current_ctc_cur;
        }
        if (! empty($request->current_ctc_mode)) {
            $job_application_arr['current_ctc_mode'] = $request->current_ctc_mode;
        }
        if (! empty($request->ectc)) {
            $job_application_arr['ectc'] = $request->ectc;
        }
        if (! empty($request->current_ectc_cur)) {
            $job_application_arr['current_ectc_cur'] = $request->current_ectc_cur;
        }
        if (! empty($request->current_ectc_cur)) {
            $job_application_arr['current_ectc_cur'] = $request->current_ectc_cur;
        }
        if (! empty($request->percentage_hike)) {
            $job_application_arr['percentage_hike'] = $request->percentage_hike;
        }
        if ($request->has('comment')) {
            $job_application_arr['comment'] = $request->input('comment');
        }
        if (! empty($request->pincode)) {
            $experienceGet = (@$request->total_exp_year == 0) ? 1 : 0;
            $job_application_arr['pincode'] = $experienceGet; // $request->pincode;
        }
        if (! empty($request->current_designation)) {
            $job_application_arr['current_designation'] = $request->current_designation;
        }
        if (! empty($request->current_company)) {
            $job_application_arr['current_company'] = $request->current_company;
        }
        if (! empty($request->notice_comments)) {
            $job_application_arr['notice_comments'] = $request->notice_comments;
        }
        if (! empty($request->careersummary)) {
            $job_application_arr['careersummary'] = $request->careersummary;
        }
        if (! empty($request->nationality)) {
            $job_application_arr['nationality'] = $request->nationality;
        }
        if (! empty($request->wm)) {
            $job_application_arr['wm'] = $request->wm;
        }
        if (! empty($request->employmentType)) {
            $job_application_arr['employmentType'] = $request->employmentType;
        }
        if (! empty($request->oh)) {
            $job_application_arr['oh'] = $request->oh;
        }
        if (! empty($request->visastatu)) {
            $job_application_arr['visastatu'] = $request->visastatu;
        }
        if (! empty($request->referencename)) {
            $job_application_arr['referencename'] = $request->referencename;
        }
        if ($request->has('recruiter_note')) {
            $job_application_arr['recruiter_note'] = $request->input('recruiter_note');
        }
        if (! empty($request->reason_for_job_change)) {
            $job_application_arr['reason_for_job_change'] = $request->reason_for_job_change;
        }

        $cnt = 0;
        $parsedSkillListClient = [];
        $skillsInputClient = $request->skills;

        if (is_array($skillsInputClient)) {
            foreach ($skillsInputClient as $sval) {
                if (is_array($sval) && isset($sval['value'])) {
                    $parsedSkillListClient[] = trim($sval['value']);
                } elseif (is_string($sval)) {
                    $parsedSkillListClient[] = trim($sval);
                }
            }
        } elseif (is_string($skillsInputClient) && ! empty($skillsInputClient)) {
            $decodedClient = json_decode($skillsInputClient, true);
            if (is_array($decodedClient)) {
                foreach ($decodedClient as $sval) {
                    if (is_array($sval) && isset($sval['value'])) {
                        $parsedSkillListClient[] = trim($sval['value']);
                    } elseif (is_string($sval)) {
                        $parsedSkillListClient[] = trim($sval);
                    }
                }
            } else {
                $parsedSkillListClient = array_filter(array_map('trim', explode(',', $skillsInputClient)));
            }
        }

        $parsedSkillListClient = array_values(array_unique(array_filter($parsedSkillListClient)));
        if (! empty($parsedSkillListClient)) {
            GeneralMethod::skillsAddElastic($parsedSkillListClient);
        }
        $skillStr = implode(' , ', $parsedSkillListClient);
        $job_application_arr['skills'] = $skillStr;
        $job_application_arr['master_status_id'] = 8;
        if (! empty($request->secondrary_email)) {
            $job_application_arr['secondrary_email'] = $request->secondrary_email;
        }
        if (! empty($request->secondary_mobile)) {
            $job_application_arr['secondary_mobile'] = $request->secondary_mobile;
        }
        $submitted_by = '';
        $submitted_by = auth()->user()->id;
        if (! empty($submitted_by)) {
            $job_application_arr['submitted_by'] = $submitted_by;
        }

        $noticePeriodState = CandidateNoticePeriodState::fromRequest($request);
        $job_application_arr['np_id'] = $noticePeriodState['np_id'];
        $job_application_arr['last_working_date'] = $noticePeriodState['last_working_date'];

        if (! empty($request->qualification)) {
            $job_application_arr['qualification'] = $request->qualification;
        }

        $job_application_arr['thirdparty_company_id'] = $this->user->company_id;
        $job_application_arr['company_id'] = $jobRow->company_id;
        $job_application_arr['serving_notice_period_mode'] = $noticePeriodState['serving_notice_period_mode'];
        if ($request->has('gender')) {
            $job_application_arr['gender'] = $request->gender;
        }
        if ($request->has('dob')) {
            $job_application_arr['dob'] = $request->dob;
        }

        $stateId = '';
        $cityID = '';
        $countryID = '';
        /* Intent: Canonical Location Resolution Engine Fast-Path (0% MySQL Reads, CQRS Compliant) */
        if ($request->filled('location_city_id') && (int) $request->location_city_id > 0) {
            $cityID = (int) $request->location_city_id;
            $stateId = (int) $request->location_state_id;
            $countryID = (int) ($request->location_country_id ?: 101);
        } else {
            $workLocation = $this->normalizedCandidateLocation($request->input('kt_tagify_work_location'));
            if ($workLocation !== null) {
                $cityName = $workLocation['city'];
                $countrName = $workLocation['country'];
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
        $job_application_arr['country'] = $countryID;
        $job_application_arr['state'] = $stateId;

        if ($job_application_arr['country'] == 101) {
            $job_application_arr['city_id'] = $cityID;
        } else {
            $job_application_arr['city_id'] = $cityID;
        }

        // if ($request->hasFile('photo')) {
        // 	// $jobApplication->photo 		= Files::upload($request->photo, 'candidate-photos');
        // 	$job_application_arr['photo'] = Files::upload($request->photo, 'candidate-photos');
        // }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $filePath = 'Images/'.$filename;

            Storage::disk('s3')->put($filePath, file_get_contents($file));

            $fileUrl = Storage::disk('s3')->url($filePath);
            $job_application_arr['photo'] = $fileUrl;
            // $jobApplication->photo = Files::upload($request->photo, 'candidate-photos');
        }

        $returnCount = 0;
        $message = 'messages.createdSuccessfully';
        $company_id = $this->user->company_id;
        $app_duplicate = $this->user->company->app_duplicate;
        $app_duplicate_days = $this->user->company->app_duplicate_days;
        $applicationEmail = $request->email;
        $applicationPhone = $request->phone;
        if (($this->user->company->app_duplicate == 1) || ($this->user->company->app_duplicate == 2)) {
            $returnCount = GeneralMethod::checkDuplicateCandidateClient($company_id, $app_duplicate, $request->job_id, $applicationEmail, $app_duplicate_days, $applicationPhone);
            if ($returnCount > 0 && $app_duplicate == 1) {
                $message = 'Duplicate :: application already in this job';
                $job_application_arr['duplicate'] = 1;
            } elseif ($returnCount > 0 && $app_duplicate == 2) {
                $message = 'Duplicate :: application already in system';
                $job_application_arr['duplicate'] = 1;
            }
        }

        $chkApplication = DB::table('job_applications')
            ->select('job_applications.id')
            ->where('thirdparty_company_id', $company_id)
            ->where(function ($query) use ($applicationEmail, $applicationPhone) {
                $query->where('email', $applicationEmail)
                    ->orWhere('phone', $applicationPhone);
            })
            ->count();
        if ($chkApplication > 0 && $returnCount > 0) {
            // $jobApplication->duplicate = 1;
            $job_application_arr['duplicate'] = 1;
        }
        // $jobApplication->save();

        $defaultTimeFormat = GeneralMethod::defaultTimeFormat();
        $interviewDateOne = $this->formatInterviewDateTime($request->interviewDateOne, $request->interviewTimeOne, $defaultTimeFormat);
        $interviewDateTwo = $this->formatInterviewDateTime($request->interviewDateTwo, $request->interviewTimeTwo, $defaultTimeFormat);
        $interviewDateThree = $this->formatInterviewDateTime($request->interviewDateThree, $request->interviewTimeThree, $defaultTimeFormat);
        $interviewDateFour = $this->formatInterviewDateTime($request->interviewDateFour, $request->interviewTimeFour, $defaultTimeFormat);
        $interviewDateFive = $this->formatInterviewDateTime($request->interviewDateFive, $request->interviewTimeFive, $defaultTimeFormat);
        $interviewDateSix = $this->formatInterviewDateTime($request->interviewDateSix, $request->interviewTimeSix, $defaultTimeFormat);

        $job_application_arr['interviewDateOne'] = $interviewDateOne;
        $job_application_arr['interviewDateTwo'] = $interviewDateTwo;
        $job_application_arr['interviewDateThree'] = $interviewDateThree;
        $job_application_arr['interviewDateFour'] = $interviewDateFour;
        $job_application_arr['interviewDateFive'] = $interviewDateFive;
        $job_application_arr['interviewDateSix'] = $interviewDateSix;
        $job_application_arr['linkedin'] = $request->linkedin;
        $job_application_arr['github'] = $request->github;
        $job_application_arr['behance'] = $request->behance;
        $job_application_arr['otherLink'] = $request->otherLink;
        $job_application_arr['interview_mode_one'] = $request->interview_mode_one;
        $job_application_arr['interview_mode_two'] = $request->interview_mode_two;
        $job_application_arr['interview_mode_three'] = $request->interview_mode_three;
        $job_application_arr['interview_mode_four'] = $request->interview_mode_four;
        $job_application_arr['interview_mode_five'] = $request->interview_mode_five;
        $job_application_arr['interview_mode_six'] = $request->interview_mode_six;

        $job_application_arr['created_at'] = date('Y-m-d');

        // ── v2 pre-flight: validate resume_session_uuid before opening the transaction ──
        $resumeSession = null;
        $finalResumeUrl = null;  // set in claim block; reused in resume URL block
        if (config('resumes.features.submit_with_session') && $request->filled('resume_session_uuid')) {
            $resumeSession = ResumeUpload::byUuid($request->input('resume_session_uuid'))
                ->forCompany($company_id)->first();
            if (! $resumeSession) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload session not found. Please upload the resume again.']);
            }
            if ($resumeSession->status !== ResumeUploadStatus::Uploaded->value) {
                return Reply::dataOnly(['status' => 'error', 'msgType' => 'warning',
                    'message' => 'Resume upload is not yet complete. Please wait for the upload to finish and try again.']);
            }
        }
        // ─────────────────────────────────────────────────────────────────────────────

        DB::beginTransaction();
        try {

            DB::table('job_applications')->insert($job_application_arr);
            $application_id = DB::getPdo()->lastInsertId();

            // ── v2 session: atomic claim + R2 HEAD check (runs inside the transaction) ──
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
                // File confirmed; move to company-structured CDN path (mirrors v1 convention).
                $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
            }
            // ─────────────────────────────────────────────────────────────────────────────

            // $jobApplication->save();
            $jobApplicationGet = JobApplication::withoutGlobalScopes()->find($application_id);

            if (! empty($parsedSkillListClient)) {
                foreach ($parsedSkillListClient as $skillVal) {
                    if (! empty($skillVal)) {
                        if (! DB::table('job_application_skills')
                            ->where('job_application_id', '=', $application_id)
                            ->where('skills', 'LIKE', '%'.$skillVal.'%')
                            ->exists()) {
                            DB::table('job_application_skills')->insert([
                                'job_application_id' => $application_id,
                                'skills' => $skillVal,
                            ]);
                        }
                    }
                }
            }

            $percentage = GeneralMethod::skill_match_percentage($request->job_id, $parsedSkillListClient);

            $now = now();
            $insertArr = ['job_id' => $request->job_id, 'job_application_id' => $application_id, 'status_id' => 8, 'master_status_id' => 8, 'company_id' => $jobRow->company_id, 'submitted_by' => $submitted_by, 'thirdparty_company_id' => $this->user->company_id, 'skill_match' => $percentage, 'created_at' => $now, 'updated_at' => $now];

            // dd($insertArr);

            $query_update = DB::table('add_job_wise_condidates')->insert($insertArr);
            $lastInsertedId = DB::getPdo()->lastInsertId();

            // ── v2 session link & resume DB update — runs inside the same transaction ──
            if ($resumeSession) {
                $resumeAssetId = null;
                try {
                    $resumeAssetId = DB::table('resume_assets')->insertGetId([
                        'company_id' => (int) $company_id,
                        'candidate_id' => (int) $application_id,
                        'upload_session_id' => $resumeSession->uuid,
                        'object_key' => (string) $finalResumeUrl,
                        'original_filename' => (string) ($request->input('resume_original_name') ?: ($resumeSession->original_filename ?: 'resume.pdf')),
                        'mime_type' => (string) ($resumeSession->mime_type ?: 'application/pdf'),
                        'file_size' => (int) ($resumeSession->file_size_bytes ?: 0),
                        'checksum_sha256' => $resumeSession->sha256_hex ?: null,
                        'version' => 1,
                        'status' => 'ACTIVE',
                        'parser_status' => $request->input('parser_status', 'BYPASSED'),
                        'parser_version' => $request->input('parser_version', null),
                        'created_at' => now(),
                        'attached_at' => now(),
                    ]);
                } catch (\Throwable $rae) {
                    \Log::warning('clientappstore(): resume_assets insert fallback: '.$rae->getMessage());
                }

                $jobAppUpdate = ['resume' => $finalResumeUrl];
                if ($resumeAssetId) {
                    $jobAppUpdate['current_resume_id'] = $resumeAssetId;
                }
                DB::table('job_applications')->where('id', $application_id)->update($jobAppUpdate);

                ResumeUpload::where('id', $resumeSession->id)
                    ->where('company_id', $company_id)
                    ->where('status', ResumeUploadStatus::Completed->value)
                    ->update([
                        'linked_candidate_id' => $application_id,
                        'r2_url' => $finalResumeUrl,
                        'updated_at' => now(),
                    ]);
                UploadSessionAudit::record($resumeSession->id, 'ActionSubmitLinked', [
                    'candidate_id' => $application_id,
                    'resume_asset_id' => $resumeAssetId,
                ], $company_id, $submitted_by);
            } elseif ($request->filled('uploaded_file_path') || $request->hasFile('resume')) {
                $companyNameR2 = DB::table('companies')
                    ->where('id', auth()->user()->company_id)
                    ->value('company_name');
                $companyNameR2 = strtolower(trim($companyNameR2 ?? 'unknown'));
                $companyNameR2 = preg_replace('/[^a-z0-9_-]/i', '_', $companyNameR2);
                $companyNameRTwo = $companyNameR2.'_'.auth()->user()->company_id;
                $year = date('Y');
                $month = date('m');

                $tempPath = $request->input('uploaded_file_path');
                $full_name = $request->input('resume_original_name');
                if ($tempPath && $full_name) {
                    $baseName = pathinfo($full_name, PATHINFO_FILENAME);
                    $ext = strtolower(pathinfo($tempPath, PATHINFO_EXTENSION) ?: pathinfo($full_name, PATHINFO_EXTENSION));
                    $safeBaseName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                    $uuidSuffix = (string) \Illuminate\Support\Str::uuid();
                    $uniqueFileName = "{$application_id}_{$safeBaseName}_{$uuidSuffix}.{$ext}";
                    $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$uniqueFileName}";
                    $fileUrl = "https://file.jero.works/{$r2Path}";

                    UploadResumeToTestRTwo::dispatch($tempPath, $full_name, $application_id, $companyNameRTwo, $year, $month, $r2Path)->onConnection('emails');

                    $resumeAssetId = null;
                    try {
                        $resumeAssetId = DB::table('resume_assets')->insertGetId([
                            'company_id' => (int) $company_id,
                            'candidate_id' => (int) $application_id,
                            'upload_session_id' => null,
                            'object_key' => (string) $fileUrl,
                            'original_filename' => (string) $full_name,
                            'mime_type' => 'application/pdf',
                            'file_size' => 0,
                            'checksum_sha256' => null,
                            'version' => 1,
                            'status' => 'ACTIVE',
                            'parser_status' => $request->input('parser_status', 'BYPASSED'),
                            'parser_version' => $request->input('parser_version', null),
                            'created_at' => now(),
                            'attached_at' => now(),
                        ]);
                    } catch (\Throwable $rae) {
                        \Log::warning('clientappstore(): resume_assets insert fallback (v1): '.$rae->getMessage());
                    }

                    $jobAppUpdate = ['resume' => $fileUrl];
                    if ($resumeAssetId) {
                        $jobAppUpdate['current_resume_id'] = $resumeAssetId;
                    }
                    DB::table('job_applications')->where('id', $application_id)->update($jobAppUpdate);
                } elseif ($request->hasFile('resume')) {
                    $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._\-]/', '', $request->file('resume')->getClientOriginalName());
                    $r2Path = "Companies/{$companyNameRTwo}/Resumes/{$year}/{$month}/{$filename}";
                    Storage::disk('r2')->put($r2Path, file_get_contents($request->file('resume')));
                    $fileUrl = "https://file.jero.works/{$r2Path}";
                    DB::table('job_applications')->where('id', $application_id)->update(['resume' => $fileUrl]);
                }
            }
            // ─────────────────────────────────────────────────────────────────────────

            $cachekeys = [
                'allcvtracker_version_user_'.$this->user->id,
                'alljobslist_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            // Skills ES indexing, candidate_job_skill_exp, teamRankElastic, answers,
            // job_additional_detail+ES, and packageRedeem are all handled by
            // ProcessCandidateSubmissionJob dispatched after commit.

            // Primary ES indexing kept synchronous — guarantees the candidate row is
            // visible on the AppTracker grid the moment the HTTP redirect fires.
            GeneralMethod::JobApplicationsAddEditUpload($lastInsertedId);

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
                'allcvtracker_version_user_'.$this->user->id,
                'allclientapp_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            $this->currencyConverter = GeneralMethod::currencyConverter();
            $submittedByEmail = auth()->user()->email;

            DB::commit();

            // ── V2 async secondary jobs — dispatched after commit so MySQL rows are visible ──
            $requestData = array_filter(
                $request->except(['resume', 'photo', 'documents', 'file', 'files']),
                fn ($val) => ! ($val instanceof \Illuminate\Http\UploadedFile)
            );

            ProcessCandidateSubmissionJob::dispatch(
                (int) $application_id,
                (int) $lastInsertedId,
                (int) $request->job_id,
                (int) $jobRow->company_id,
                (int) $submitted_by,
                (int) $company_id,
                (int) 8,
                (string) $request->email,
                $requestData
            )->onConnection('emails');

            QueueCandidateNotificationJob::dispatch(
                (int) $lastInsertedId,
                (int) $request->job_id,
                (int) $company_id,
                (string) $submittedByEmail
            )->onConnection('emails');
            // ─────────────────────────────────────────────────────────────────────────────

            $rawJobId = GeneralMethod::value_decrypt($request->job_id);
            if (! empty($rawJobId)) {
                $redirectUrl = route('service_company.apptracker.clientapp', [
                    'id' => GeneralMethod::value_encrypt($rawJobId),
                    'mstatus_id' => 'all',
                ]);
            } else {
                $redirectUrl = route('service_company.cvtracker.index');
            }

            return Reply::successWithData(__('messages.createdSuccessfully'), [
                'action' => 'redirect',
                'url' => $redirectUrl,
                'candidate_id' => (int) $application_id,
                'resume' => (string) ($jobApplicationGet->resume ?? ''),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('clientappstore() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }
    }

    public function clientshow($id)
    {

        $company_id = '';
        $company_id = auth()->user()->company_id;

        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();
        $AddJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($id);
        $jobId = $AddJobWiseCondidate->job_id;
        // dd($id,$jobId);
        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'add_job_wise_condidates.submitted_by',
                'add_job_wise_condidates.company_id',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.full_name',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.phone_code',
                'job_applications.second_phone_code',
                'job_applications.secondary_mobile',
                'job_applications.photo',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.skype_id',
                'job_applications.cover_letter',
                'add_job_wise_condidates.job_id',
                'job_applications.status_id',
                'job_applications.is_viewed',
                'job_applications.column_priority',
                'job_applications.created_at',
                'job_applications.updated_at',
                'job_applications.deleted_at',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.passport',
                'job_applications.current_location',
                'job_applications.current_designation',
                'job_applications.current_company',
                'job_applications.process_id',
                'job_applications.qualification',
                'add_job_wise_condidates.master_status_id',
                'add_job_wise_condidates.child_status_id',
                'job_applications.secondrary_email',
                'job_applications.secondary_mobile',
                'job_applications.skills',
                'job_applications.city',
                'job_applications.country',
                'job_applications.pincode',
                'job_applications.state',
                'job_applications.company_id as other_company',
                'job_applications.condidate_type',
                'job_applications.current_ctc_cur',
                'job_applications.ctc',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_cur',
                'job_applications.ectc',
                'job_applications.current_ectc_mode',
                'job_applications.percentage_hike',
                'job_applications.comment',
                'job_applications.dob',
                'job_applications.interviewDateOne',
                'job_applications.interviewDateTwo',
                'job_applications.interviewDateThree',
                'job_applications.interviewDateFour',
                'job_applications.interviewDateFive',
                'job_applications.interviewDateSix',
                'job_applications.linkedin',
                'job_applications.github',
                'job_applications.behance',
                'job_applications.otherLink',
                'job_applications.interview_mode_one',
                'job_applications.interview_mode_two',
                'job_applications.interview_mode_three',
                'job_applications.interview_mode_four',
                'job_applications.interview_mode_five',
                'job_applications.interview_mode_six',
                'job_applications.created_at',
                'clients.name as client_name',
                'jobs.title',
                'users.name as posted_by',
                'users.image as posted_by_image',
                // 'notice_periods.np_title',
                'master_status.name as master_status_name',
                'offer_job_applications.proposed_ctc',
                'offer_job_applications.any_offer',
                'offer_job_applications.document',
                'offer_job_applications.sub_for_approval',
                'offer_job_applications.offer_approved',
                'offer_job_applications.offer_release',
                'offer_job_applications.offer_acceptance',
                'offer_job_applications.joined',
                'offer_job_applications.joined_date',
                'offer_job_applications.offer_amount',
                'offer_job_applications.confidential',
                'qualification_master.name as qualification_name',
                'state_list.state as state_name',
                'city_list.city as city_name',
                'add_job_wise_condidates.id as wiseId'
            )->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })->leftJoin('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })->leftJoin('offer_job_applications', function ($join) use ($jobId) {
                $join->on('offer_job_applications.application_id', 'job_applications.id');
                $join->where('offer_job_applications.job_id', $jobId);
                // $join->where('offer_job_applications.company_id', $company_id);
            })
            // ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            // ->leftjoin('notice_periods', 'notice_periods.np_id', 'job_applications.np_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.master_status_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'job_applications.state')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id');
        $this->application = $this->application->where('add_job_wise_condidates.id', $id);
        $this->application = $this->application->first();

        $this->client_contuser_arr = DB::table('client_contact_info_jobs')
            ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
            ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
            ->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation', 'clients.name')
            ->where('client_contact_info_jobs.job_id', $jobId)
            ->get();

        $this->job_additional_detail = '';
        $this->job_additional_detail = DB::table('job_additional_detail')
            ->select('*')
            // ->where('job_additional_detail.company_id', '=', $company_id)
            ->where('job_additional_detail.job_id', '=', @$jobId)
            ->orderBy('job_additional_detail.id', 'ASC')
            ->get();

        $this->application_additional_detail = DB::table('application_additional_detail')
            ->select('*')
            ->where('application_additional_detail.application_id', '=', $this->application->id)
            ->where('application_additional_detail.job_id', '=', @$jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')
                ->select('name')
                ->where('id', $this->application->qualification)
                ->first();
        }

        $this->notesArr = DB::table('applicant_notes')
            ->select(
                'applicant_notes.id',
                'applicant_notes.note_text',
                'applicant_notes.created_at',
                'applicant_notes.updated_at',
                'users.name',
                'users.image as posted_by_image'
            )
            // ->where('company_id', user()->company_id)
            ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')
            ->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
            // ->where('company_id', user()->company_id)
            // ->join('users', 'users.id', '=', 'applicant_notes.user_id')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $jobId;
        $this->job_id = $job_id;

        $this->timeline = DB::table('timeline');
        $this->timeline = $this->timeline->select(
            'timeline.id',
            'timeline.owner_id',
            'timeline.application_id',
            'timeline.application_email',
            'timeline.job_id',
            'timeline.process_id',
            'timeline.status_name',
            'timeline.process_child_name',
            'timeline.note',
            'timeline.visible',
            'timeline.insert_type',
            'timeline.create_at',
            'timeline.interview_schedule_date',
            'timeline.interview_from_time',
            'timeline.interview_to_time',
            'timeline.interview_mode',
            'timeline.interview_mention_link',
            'timeline.interviewers',
            'timeline.interview_additional_email',
            'timeline.interview_message',
            'jobs.id as job_id',
            'jobs.title',
            'master_status.name as master_status_name',
            'clients.name as client_name',

            'timeline.timeline_type',
            'timeline.prev_process_name',
            'timeline.prev_process_child_name',
            'timeline.interview_mode_child_level',
            'timeline.average_star_rating',
            'timeline.interview_schedules_id',
            'master_status.css_style',
            'users.name',
            'users.image'

        );
        $this->timeline = $this->timeline->join('job_applications', 'job_applications.id', '=', 'timeline.application_id');
        $this->timeline = $this->timeline->leftJoin('users', 'users.id', '=', 'timeline.owner_id');
        $this->timeline = $this->timeline->leftJoin('jobs', 'jobs.id', '=', 'timeline.job_id');
        $this->timeline = $this->timeline->leftJoin('clients', 'clients.id', '=', 'jobs.client_id');
        $this->timeline = $this->timeline->leftJoin('master_status', 'master_status.id', '=', 'timeline.process_id');
        $this->timeline = $this->timeline->where('timeline.application_id', '=', $application_id);
        $this->timeline = $this->timeline->where('timeline.job_id', '=', $job_id);
        $this->timeline = $this->timeline->orderBy('timeline.id', 'DESC');
        $this->timeline = $this->timeline->get();

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        // UPDATE TIMELINE
        $this->updateTimeLine = DB::table('updatetimeline');
        $this->updateTimeLine = $this->updateTimeLine->select(
            'updatetimeline.id',
            'updatetimeline.event_id',
            'updatetimeline.event_by_company_id',
            'updatetimeline.event_by_user_id',
            'updatetimeline.master_process_name',
            'updatetimeline.child_process_name',
            'updatetimeline.application_id',
            'updatetimeline.application_email',
            'updatetimeline.job_id',
            'updatetimeline.note',
            'updatetimeline.create_at',
            'updatetimeline.update_at',
            'updatetimeline.prev_process_name',
            'updatetimeline.note',
            'updatetimeline.confidential',
            'updatetimeline.prev_process_child_name',
            'application_event.event_name',
            'updatetimeline.timezone_abbreviation',
            'updatetimeline.interview_to_time',
            'jobs.id as job_id',
            'jobs.title',
            'users.name',
            'users.image',
            'clients.name as client_name'
        );
        $this->updateTimeLine = $this->updateTimeLine->join('application_event', 'application_event.id', '=', 'updatetimeline.event_id');
        $this->updateTimeLine = $this->updateTimeLine->join('job_applications', 'job_applications.id', '=', 'updatetimeline.application_id');
        $this->updateTimeLine = $this->updateTimeLine->join('users', 'users.id', '=', 'updatetimeline.event_by_user_id');
        $this->updateTimeLine = $this->updateTimeLine->join('jobs', 'jobs.id', '=', 'updatetimeline.job_id');
        $this->updateTimeLine = $this->updateTimeLine->join('clients', 'clients.id', '=', 'jobs.client_id');
        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.application_id', '=', $application_id);
        // $this->updateTimeLine = $this->updateTimeLine->where('job_applications.id', '=', $application_id);
        // $this->updateTimeLine = $this->updateTimeLine->where('job_applications.company_id', '=', auth()->user()->company_id);
        $this->updateTimeLine = $this->updateTimeLine->where('updatetimeline.job_id', '=', @$jobId);
        // $this->updateTimeLine = $this->updateTimeLine->orWhere(function ($query) use ($company_id, $application_id) {
        // 	return
        // 		$query->where('job_applications.thirdparty_company_id', '=', $company_id)
        // 			->where('job_applications.id', '=', $application_id);
        // 	//->where('staffing_company_relation.final_status', '=', 1);
        // });
        $this->updateTimeLine = $this->updateTimeLine->orderBy('updatetimeline.id', 'DESC');
        $this->updateTimeLine = $this->updateTimeLine->get();

        // dd($application_id,$jobId,$this->updateTimeLine);

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')
                ->select('*')
                ->where('id', $this->application->child_status_id)
                ->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;
        $this->additional_skills_arr = DB::table('skills')
            ->join('candidate_skills_value', 'skills.id', '=', 'candidate_skills_value.skill_id')
            ->select('skills.name', 'candidate_skills_value.skill_value')
            ->where('candidate_skills_value.job_id', $jobId)
            ->where('candidate_skills_value.candidate_id', $this->application->id)
            ->where('skills.company_id', $this->user->company_id)
            ->get();

        $this->application_add_detail_cnt = DB::table('application_additional_detail')
            ->select('*')->where('application_id', $this->application->id)
            ->where('job_id', $jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->count();

        $this->application_add_detail = DB::table('application_additional_detail')
            ->select('*')->where('application_id', $this->application->id)
            ->where('job_id', $jobId)
            ->orderBy('application_additional_detail.id', 'ASC')
            ->get();

        $this->offer_job_applications_count = DB::table('offer_job_applications')
            ->select('offer_job_applications.id')
            ->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->where('offer_job_applications.email', '=', $this->application->email)
            ->where('offer_job_applications.company_id', '=', auth()->user()->company_id)
            ->orderBy('offer_job_applications.id', 'DESC')
            ->count();

        $this->offer_job_applications = DB::table('offer_job_applications')
            ->select('offer_job_applications.id', 'offer_job_applications.application_id', 'offer_job_applications.submitted_by', 'offer_job_applications.company_id', 'offer_job_applications.np_id', 'offer_job_applications.full_name', 'offer_job_applications.email', 'offer_job_applications.phone', 'offer_job_applications.gender', 'offer_job_applications.dob', 'offer_job_applications.country', 'offer_job_applications.state', 'offer_job_applications.city', 'offer_job_applications.secondrary_email', 'offer_job_applications.secondary_mobile', 'offer_job_applications.skills', 'offer_job_applications.photo', 'offer_job_applications.resume', 'offer_job_applications.rating', 'offer_job_applications.skype_id', 'offer_job_applications.cover_letter', 'offer_job_applications.job_id', 'offer_job_applications.status_id', 'offer_job_applications.is_viewed', 'offer_job_applications.column_priority', 'offer_job_applications.created_at', 'offer_job_applications.updated_at', 'offer_job_applications.total_exp', 'offer_job_applications.rel_exp', 'offer_job_applications.passport', 'offer_job_applications.ctc', 'offer_job_applications.ectc', 'offer_job_applications.current_location', 'offer_job_applications.current_designation', 'offer_job_applications.current_company', 'offer_job_applications.process_id', 'offer_job_applications.qualification', 'offer_job_applications.master_status_id', 'offer_job_applications.child_status_id', 'offer_job_applications.serving_notice_period_mode', 'offer_job_applications.proposed_ctc', 'offer_job_applications.any_offer', 'offer_job_applications.document', 'offer_job_applications.sub_for_approval', 'offer_job_applications.offer_approved', 'offer_job_applications.offer_release', 'offer_job_applications.offer_acceptance', 'offer_job_applications.joined', 'offer_job_applications.joined_date', 'offer_job_applications.duplicate', 'jobs.title', 'clients.name as client_name')->join('jobs', 'jobs.id', '=', 'offer_job_applications.job_id')->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->where('offer_job_applications.email', '=', $this->application->email)
            ->where('offer_job_applications.company_id', '=', auth()->user()
                ->company_id)
            ->orderBy('offer_job_applications.id', 'DESC')->get();

        $resume = '';
        $resumeData = DB::table('documents')
            ->select('*')->where('documentable_id', $this->application->id)
            ->where('name', '=', 'Resume')
            ->first();
        $this->resume = @$resumeData->hashname;

        $sel_app_id = $id;
        $sel_job_id = $job_id;
        $this->benchStatusCnt = DB::table('rec_candidate_assign')->where('source_application_id', '=', $sel_app_id)->where('source_jobid', '=', $sel_job_id)->where('source_from_type', '!=', 0)->count();
        if ($this->benchStatusCnt > 0) {
            $benchAssignRow = DB::table('rec_candidate_assign')->select('rec_candidate_assign.*')->where('source_application_id', '=', $sel_app_id)->first();

            $source_from_company_id = @$benchAssignRow->source_from_company_id;
            $source_from_user_id = @$benchAssignRow->source_from_user_id;
            $source_from_id = @$benchAssignRow->source_from_id;
            $source_from_type = @$benchAssignRow->source_from_type;
            $source_company_id = @$benchAssignRow->source_company_id;

            $source_user_id = @$benchAssignRow->source_user_id;
            $source_application_id = @$benchAssignRow->source_application_id;
            $source_jobid = @$benchAssignRow->source_jobid;

            if ($source_from_type == 2) {
                $this->application->phone = $this->application->secondary_mobile = 'xxx-xxx-xxxx';
                $this->application->email = $this->application->secondrary_email = 'anonymous@email.com';
            }

            $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.calling_code', 'users.email', 'users.mobile', 'users_profile.designation')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->where('bench_assign.bench_applications_id', '=', $benchAssignRow->source_from_id)->get();
            $this->bench_additional_detail = DB::table('bench_additional_detail')
                ->select('bench_additional_detail.id', 'bench_additional_detail.custom_keys', 'bench_additional_detail.custom_keys_value')
                ->where('bench_additional_detail.bench_id', '=', $benchAssignRow->source_from_id)->get();

        }

        $this->bench_assign_count = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation', 'users.working_status')->join('users', 'users.id', '=', 'bench_assign.user_id')->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->count();

        $this->bench_assign_row = DB::table('bench_assign')->select('users.id', 'users.name', 'users.email', 'users.mobile', 'users_profile.designation')->join('users', 'users.id', '=', 'bench_assign.user_id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')->where('bench_assign.bench_applications_id', '=', @$this->application->id)->get();

        $this->jobSkill = DB::table('job_metronics_skills')
            ->leftJoin('candidate_job_skill_exp', function ($join) {
                $join->on('candidate_job_skill_exp.job_skill_id', '=', 'job_metronics_skills.id')
                    ->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id); // Add the candidate condition
            })
            ->select('job_metronics_skills.*', 'candidate_job_skill_exp.skill_value')
            ->where('job_metronics_skills.job_id', '=', $this->job_id)
            ->get();

        $this->skillShow = true;
        if (! DB::table('candidate_job_skill_exp')->where('candidate_job_skill_exp.job_id', '=', $this->job_id)->where('candidate_job_skill_exp.candidate_id', '=', $this->application->id)->exists()) {
            $this->skillShow = false;
        }

        $view = view('service_company.job-applications.clientshow', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function download($id)
    {
        $resume = '';
        $resumeData = DB::table('documents')->select('*')->where('documentable_id', $id)->where('name', '=', 'Resume')->orderBy('created_at', 'desc')->first();
        $resume = $resumeData->hashname;
        $company = user()->company_id;
        $jobApplication = JobApplication::withoutGlobalScopes()->findOrFail($id);
        $path = public_path().'/user-uploads/documents/'.$jobApplication->id.'/'.$resume;

        // dd($path);
        // if (!file_exists($path)) {
        // 	abort(404);
        // }
        return response()->download($path);
    }

    public function client_stage_body_content($id)
    {
        $current_company_id = auth()->user()->company_id;
        $this->qualification = '';
        $this->application = '';
        $this->requisition_owner = 1;

        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $this->intModeArr = GeneralMethod::interview_mode();
        $this->application = DB::table('job_applications')
            ->select(
                'job_applications.id',
                'job_applications.submitted_by',
                'job_applications.company_id',
                'job_applications.np_id',
                'job_applications.serving_notice_period_mode',
                'job_applications.full_name',
                'job_applications.email',
                'job_applications.phone',
                'job_applications.secondary_mobile',
                'job_applications.photo',
                'job_applications.resume',
                'job_applications.rating',
                'job_applications.skype_id',
                'job_applications.cover_letter',
                'add_job_wise_condidates.job_id',
                'add_job_wise_condidates.status_id',
                'job_applications.is_viewed',
                'job_applications.column_priority',
                'job_applications.created_at',
                'job_applications.updated_at',
                'job_applications.deleted_at',
                'job_applications.total_exp',
                'job_applications.rel_exp',
                'job_applications.passport',
                'job_applications.current_ctc_cur',
                'job_applications.current_ectc_cur',
                'job_applications.current_ctc_mode',
                'job_applications.current_ectc_mode',
                'job_applications.ctc',
                'job_applications.ectc',
                'job_applications.ctc_yearly',
                'job_applications.ectc_yearly',
                'job_applications.current_location',
                'job_applications.current_designation',
                'job_applications.current_company',
                'job_applications.process_id',
                'job_applications.qualification',
                'add_job_wise_condidates.master_status_id',
                'add_job_wise_condidates.child_status_id',
                'job_applications.secondrary_email',
                'job_applications.secondary_mobile',
                'job_applications.skills',
                'clients.name as client_name',
                'jobs.title',
                'users.name as posted_by',
                'master_status.name as master_status_name',
                'job_applications.proposed_ctc',
                'job_applications.any_offer',
                'job_applications.document',
                'job_applications.sub_for_approval',
                'job_applications.offer_approved',
                'job_applications.offer_release',
                'job_applications.offer_acceptance',
                'job_applications.joined',
                'job_applications.joined_date',
                'job_applications.pay_rate_amount',
                'job_applications.bill_rate_amount',
                'job_applications.bill_rate_mode',
                'job_applications.bill_rate_currency',
                'job_applications.condidate_type',
                'job_applications.city',
                'job_applications.state',
                'job_applications.country',
                'job_applications.city_id',
                'state_list.state as state_name',

                'users.name as submitted_by_name',
                'users.company_id as submitted_by_company_id',
                'users.image as posted_by_image',
                'add_job_wise_condidates.job_id as jobId',
                'add_job_wise_condidates.thirdparty_company_id',
                'add_job_wise_condidates.id as job_wise_condidates_id'
            )->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftJoin('jobs', 'jobs.id', '=', 'add_job_wise_condidates.job_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('users', 'users.id', 'add_job_wise_condidates.submitted_by')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state');
        // ->leftJoin('master_status', 'master_status.id', '=', 'job_applications.status_id');

        $this->application = $this->application->where('add_job_wise_condidates.id', $id);
        $this->application = $this->application->first();

        // dd($this->application);

        if (@$current_company_id != @$this->application->company_id) {
            $this->requisition_owner = 0;
        }

        if (! empty($this->application->qualification) && $this->application->qualification > 0) {
            $this->qualification = DB::table('qualification_master')->select('name')->where('id', $this->application->qualification)->first();
        }

        // dd($this->application);
        $submitted_by_company_id = @$this->application->submitted_by_company_id;
        $this->company = DB::table('companies')->select('company_name')->where('id', $submitted_by_company_id)->first();

        $thirdparty_company_id = $this->application->thirdparty_company_id;
        if ($thirdparty_company_id == $current_company_id) {
            $this->master_status = DB::table('master_status')->select('*')->where('master_status.direct_client', '=', 1)->orderBy('master_status.direct_client_priority', 'ASC')->get();
        } else {
            $this->master_status = DB::table('master_status')->select('*')->where('master_status.custom_client', '=', 1)->orderBy('master_status.priority', 'ASC')->get();
        }
        $master_status_id = $this->application->master_status_id;

        $this->candidate_status_Arr = DB::table('candidate_status')
            ->select('*')
            ->where('parent', $master_status_id)
            ->where('company_id', $this->application->company_id)
            ->orderBy('candidate_status.priority', 'ASC')
            ->get();

        $this->schedule = DB::table('interview_schedules')
            ->select('interview_schedules.id', 'interview_schedules.company_id', 'interview_schedules.job_application_id', 'interview_schedules.schedule_date', 'interview_schedules.status', 'interview_schedules.user_accept_status', 'interview_schedules.created_at', 'interview_schedules.updated_at')
            ->where('job_application_id', $this->application->id)
            ->orderBy('id', 'DESC')
            ->get();

        $application_email = $this->application->email;
        $this->application_email = $application_email;
        $application_id = $this->application->id;
        $this->application_id = $application_id;
        $job_id = $this->application->job_id;
        $this->job_id = $job_id;

        $this->created_at = GeneralMethod::ymdTodmy($this->application->created_at);

        $this->child_status_name = 'N/A';
        if ($this->application->child_status_id > 0) {
            $childStatus = DB::table('candidate_status')->select('*')->where('id', $this->application->child_status_id)->first();
            if (! empty($childStatus)) {
                $this->child_status_name = @$childStatus->name;
            }
        }
        $this->company_id = $this->user->company_id;

        $current_ctc_cur = $current_ectc_cur = $ctc = $ectc = $current_ctc_mode = $current_ectc_mode = $country = $this->row_country_name = '';

        $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
        $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
            0 => 'INR',
            1 => 'EUR',
            2 => 'JPY',
            3 => 'GBP',
            4 => 'USD',
            5 => 'AED',
        ];
        $jobData = DB::table('jobs')->select('max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')->where('id', '=', $this->application->jobId)->first();
        $jobMode = $jobData->jobMode ?? @$this->application->current_ctc_mode;
        $jobCurrency = $jobData->jobCurrency ?? @$this->application->current_ctc_cur;
        $symbols = $symbolsValue[$jobCurrency] ?? 'INR';

        $current_ctc_cur = '';
        $current_ctc_cur = $currencySymbole[@$jobCurrency];

        $current_ectc_cur = '';
        $current_ectc_cur = $currencySymbole[@$jobCurrency];

        $current_ctc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);
        $current_ectc_mode = mb_substr($salaryMode[@$jobMode], 0, 1);

        $payFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ctc_yearly, $symbols, $jobMode);
        $billFormatViewSalary = GeneralMethod::formatViewSalary(@$this->application->ectc_yearly, $symbols, $jobMode);
        $ctc = '';
        $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
        $ectc = '';
        $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);

        $this->current_ctc = @$current_ctc_cur.@$ctc.'/'.@$current_ctc_mode;
        $this->expected_ctc = @$current_ectc_cur.@$ectc.'/'.@$current_ectc_mode;

        $this->condidate_type = $this->application->condidate_type;

        if (empty($this->application->country)) {
            $this->row_country_name = '----';
        } else {
            if (! empty($this->application->country)) {
                if (is_numeric($this->application->country)) {
                    $country_name_Arr = GeneralMethod::getCountryName($this->application->country);
                    foreach ($country_name_Arr as $cval) {
                        $this->row_country_name = ucwords($cval['name']);
                    }
                }
            }
        }
        $this->row_state_name = '';
        if (empty($this->application->state)) {
            $this->row_state_name = '----';
        } else {
            if ($this->application->country == 101) {
                $this->row_state_name = $this->application->state_name;
            } else {
                $state = $this->application->state;
                if (is_numeric($this->application->country) && is_numeric($state)) {
                    if ($state > 0) {
                        $state_name_Arr = GeneralMethod::getStateName($this->application->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $this->row_state_name = ucwords($sval['name']);
                        }
                    }
                }

            }

        }

        $this->row_city_name = '';
        if ($this->application->country == 101) {
            $row_city = DB::table('city_list')->select('*')->where('city_id', $this->application->city_id)->first();
            $this->row_city_name = @$row_city->city;
        } else {
            $this->row_city_name = $this->application->city;
        }

        $view = view('service_company.job-applications.client_stage_body_content', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function client_stage_submit(Request $request)
    {

        $client_job_applications = [];
        $company_id = '';
        $company_id = auth()->user()->company_id;

        $application_id = $master_status = $sub_stages = $comment = $addJobWiseCondidate_id = '';
        $prev_process_name = $prev_process_child_name = '';
        $addJobWiseCondidate_id = @$request->job_wise_condidates_id;
        $application_id = @$request->application_id;
        $master_status = @$request->master_status;
        $sub_stages = @$request->sub_stages;
        $comment = @$request->comment;

        $addJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($addJobWiseCondidate_id);
        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $addJobWiseCondidate_id,
        ]);
        if ($exists) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];
        } else {
            $data = (object) [
                'company_id' => $addJobWiseCondidate->company_id ?? auth()->user()->company_id,
                'submitted_by' => $addJobWiseCondidate->submitted_by ?? auth()->id(),
                'candidate_id' => $addJobWiseCondidate->job_application_id ?: $addJobWiseCondidate->id,
                'job_master_status_id' => $addJobWiseCondidate->master_status_id ?: 8,
                'jobId' => $addJobWiseCondidate->job_id,
                'email' => '',
                'phone' => '',
                'of_offer_amount' => $addJobWiseCondidate->of_offer_amount ?? 0,
                'of_joined_date' => $addJobWiseCondidate->of_joined_date ?? null,
                'childStatus' => $addJobWiseCondidate->child_status_id ?? 0,
            ];
        }

        $data->company_id = $data->company_id ?? ($addJobWiseCondidate->company_id ?? auth()->user()->company_id);
        $data->submitted_by = $data->submitted_by ?? ($addJobWiseCondidate->submitted_by ?? auth()->id());
        $data->candidate_id = $data->candidate_id ?? ($addJobWiseCondidate->job_application_id ?? $addJobWiseCondidate->id);
        $data->job_master_status_id = $data->job_master_status_id ?? ($addJobWiseCondidate->master_status_id ?? 8);

        if (true) {
            $clientConId = $data->company_id;
            $companiesData = DB::table('companies')->select('app_duplicate', 'app_duplicate_days')->where('id', $clientConId)->first();
            $app_duplicate = $companiesData->app_duplicate;
            $app_duplicate_days = $companiesData->app_duplicate_days;

            $applicationEmail = $data->email;
            $applicationPhone = $data->phone;
            $jobId = $data->jobId;

            $duplicate = 0; // 0 = New

            if ($master_status == 1) {

                /*
                |--------------------------------------------------------------------------
                | Common Query
                |--------------------------------------------------------------------------
                */

                $must = [];

                // Email / Phone Match
                $must[] = [
                    'bool' => [
                        'should' => [
                            [
                                'term' => [
                                    'email' => $applicationEmail,
                                ],
                            ],
                            [
                                'term' => [
                                    'phone' => $applicationPhone,
                                ],
                            ],
                        ],
                        'minimum_should_match' => 1,
                    ],
                ];

                // Current Job
                if ($app_duplicate == 1) {

                    $must[] = [
                        'term' => [
                            'jobId' => $jobId,
                        ],
                    ];
                }

                // Duplicate Days

                if (! empty($app_duplicate_days)) {

                    $must[] = [
                        'range' => [
                            'created_at' => [
                                'gte' => now()
                                    ->subDays($app_duplicate_days)
                                    ->toIso8601String(),
                            ],
                        ],
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Company Duplicate
                |--------------------------------------------------------------------------
                */

                $companyParams = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'size' => 1,
                        'query' => [
                            'bool' => [
                                'must' => array_merge($must, [

                                    [
                                        'term' => [
                                            'company_id' => $clientConId,
                                        ],
                                    ],

                                    [
                                        'bool' => [
                                            'should' => [

                                                // ClientComID = 0
                                                [
                                                    'term' => [
                                                        'ClientComID' => 0,
                                                    ],
                                                ],

                                                // ClientComID = Current Company
                                                [
                                                    'term' => [
                                                        'ClientComID' => $clientConId,
                                                    ],
                                                ],

                                                // ClientComID field does not exist
                                                [
                                                    'bool' => [
                                                        'must_not' => [
                                                            [
                                                                'exists' => [
                                                                    'field' => 'ClientComID',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],

                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],

                                ]),
                            ],
                        ],
                    ],
                ];

                $companyResult = $this->elasticService->getClient()->search($companyParams);

                if (($companyResult['hits']['total']['value'] ?? 0) > 0) {

                    $duplicateType = 'COMPANY_DUPLICATE';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Agency Duplicate
                    |--------------------------------------------------------------------------
                    */

                    $agencyParams = [
                        'index' => ELASTIC_CANDIDATES,
                        'body' => [
                            'size' => 1,
                            'query' => [
                                'bool' => [
                                    'must' => array_merge($must, [

                                        [
                                            'term' => [
                                                'company_id' => $clientConId,
                                            ],
                                        ],

                                        [
                                            'bool' => [
                                                'should' => [

                                                    // Case 1: ClientComID > 0 AND Status must be 1-7
                                                    [
                                                        'bool' => [
                                                            'must' => [
                                                                [
                                                                    'range' => [
                                                                        'ClientComID' => [
                                                                            'gt' => 0,
                                                                        ],
                                                                    ],
                                                                ],
                                                                [
                                                                    'terms' => [
                                                                        'job_master_status_id' => [1, 2, 3, 4, 5, 6, 7],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],

                                                    // Case 2: ClientComID is 0 or missing
                                                    [
                                                        'bool' => [
                                                            'must_not' => [
                                                                [
                                                                    'range' => [
                                                                        'ClientComID' => [
                                                                            'gt' => 0,
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],

                                                ],
                                                'minimum_should_match' => 1,
                                            ],
                                        ],

                                    ]),
                                ],
                            ],
                        ],
                    ];

                    $agencyResult = $this->elasticService->getClient()->search($agencyParams);

                    if (($agencyResult['hits']['total']['value'] ?? 0) > 0) {

                        $duplicateType = 'AGENCY_DUPLICATE';

                    } else {

                        $duplicateType = 'NEW';
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Duplicate Flag
                |--------------------------------------------------------------------------
                */

                $duplicate = ($duplicateType == 'NEW') ? 0 : 1;

                $dbDuplicate = DB::table('add_job_wise_condidates')
                    ->join('job_applications', 'job_applications.id', '=', 'add_job_wise_condidates.job_application_id')
                    ->where('add_job_wise_condidates.id', $addJobWiseCondidate_id)
                    ->value('job_applications.duplicate');

                if (($dbDuplicate ?? 0) == 1) {
                    $duplicate = 1;
                }

                /*
                |--------------------------------------------------------------------------
                | Update Current Candidate
                |--------------------------------------------------------------------------
                */

                $updateTime = now()->toIso8601String();
                $updateParams = [
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $addJobWiseCondidate_id,
                    'body' => [
                        'doc' => [
                            'duplicate' => (int) $duplicate,
                            'is_duplicate' => (bool) ($duplicate == 1),
                            'updated_at' => $updateTime,

                        ],
                        'doc_as_upsert' => false,
                    ],
                    'refresh' => false,
                ];

                $this->elasticService->getClient()->update($updateParams);

            }
        }

        $userId = auth()->id();
        $isValid = DB::table('add_job_wise_condidates as ajwc')
            ->leftJoin('job_assign as ja', function ($join) {
                $join->on('ja.job_id', '=', 'ajwc.job_id')
                    ->on('ja.company_id', '=', 'ajwc.company_id');
            })
            ->where('ajwc.id', $addJobWiseCondidate_id) // Candidate row id
            ->where(function ($query) use ($userId) {
                $query->where('ja.spok_user_id', $userId)
                    ->orWhere('ajwc.submitted_by', $userId);
            })
            ->exists();

        if (! $isValid) {

            $validations = (object) [
                'message' => "You are not authorized to update this candidate's status. Only the assigned Captain or the submitting user can update this candidate's status.",
            ];

            return Reply::dataOnly([
                'status' => 'candidatevalidation',
                'message' => $validations->message,
            ]);
        }

        $addJobWiseCondidate = AddJobWiseCondidate::withoutGlobalScopes()->find($addJobWiseCondidate_id);

        if ($master_status == 1) {
            // dd($addJobWiseCondidate->thirdparty_company_id);
            $eventId = 4;
            $coinsValidations = GeneralMethod::coinsValidations((int) $addJobWiseCondidate->thirdparty_company_id, $eventId, auth()->user()->id, $addJobWiseCondidate->thirdparty_company_id, '', '');
            // dd($coinsValidations);
            if ($coinsValidations->status) {
                $Company = Company::withoutGlobalScopes()->find($addJobWiseCondidate->thirdparty_company_id);

                return Reply::dataOnly(['status' => 'coinCheck', 'message' => '"'.$Company->company_name.'" does not have sufficient coins to receive your profile at this time', 'company_coins' => $coinsValidations->company_coins]);
            }
            $coinsDeductions = GeneralMethod::coinsValidations((int) $addJobWiseCondidate->company_id, $eventId, auth()->user()->id, $addJobWiseCondidate->thirdparty_company_id, '', '');
            if ($coinsDeductions->status) {
                return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
            }

            GeneralMethod::coinsDeductions((int) $addJobWiseCondidate->company_id, $eventId, auth()->user()->id, $addJobWiseCondidate->thirdparty_company_id, '', '');
        }

        $offer_id = null;
        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $addJobWiseCondidate_id,
        ]);
        if ($exists) {
            $params = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
            ];

            $response = $this->elasticService->getClient()->get($params);
            $data = (object) $response['_source'];

            // dd($data);

            if ($master_status != 9) {
                $stages = [
                    8 => ['display' => 'InReview',   'order' => 1],
                    1 => ['display' => 'Sourcing',   'order' => 2],
                    2 => ['display' => 'Screening',  'order' => 3],
                    3 => ['display' => 'Evaluation', 'order' => 4],
                    4 => ['display' => 'Negotiation', 'order' => 5],
                    5 => ['display' => 'Offered',    'order' => 6],
                    6 => ['display' => 'Joined',    'order' => 7],
                    7 => ['display' => 'Rejected',    'order' => 8],
                    9 => ['display' => 'Rejected',    'order' => 9],
                ];

                $currentStageId = (int) ($addJobWiseCondidate->master_status_id ?: ($data->job_master_status_id ?: 8));
                $newStageId = $master_status;

                // ✅ Ensure both exist in mapping
                if ($data->job_master_status_id == 7) {
                    // dd($data->job_master_status_id);
                    $stage_point_tb = DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->first();

                    // Same backward-recovery guard stage_submit() applies when
                    // un-rejecting: resume at exactly the stage left off at,
                    // or (if it had already reached Offered/Joined) restart at
                    // Sourcing — no skipping to an arbitrary stage in between.
                    if ($stage_point_tb) {
                        if ($stage_point_tb->stage_id != 5 && $stage_point_tb->stage_id != 6) {
                            if ($stage_point_tb->stage_id != $master_status) {
                                $currentName = $stages[$master_status]['display'];
                                $newName = $stages[$stage_point_tb->stage_id]['display'];

                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from
                                                <b style='color:#EF4444;'>Rejected</b>
                                                to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
                                            Please complete the required step in
                                            <b style='color:#F59E0B;'>{$newName}</b> first.",
                                ]);
                            }
                        } elseif ($stage_point_tb->stage_id == 5 || $stage_point_tb->stage_id == 6) {
                            if ($master_status != 1) {
                                $currentName = $stages[$master_status]['display'];
                                $newName = $stages[$stage_point_tb->stage_id]['display'];

                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => "You cannot move backward from
                                                <b style='color:#EF4444;'>Rejected</b>
                                                to <b style='color:#F59E0B;'>{$currentName}</b>..<br>
                                            Please complete the required step in
                                            <b style='color:#F59E0B;'>{$newName}</b> first.",
                                ]);
                            }
                        }
                    }

                    DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->delete();
                    //  dd($master_status);
                    $values = [
                        'job_id' => $data->jobId,
                        'candidate_id' => $data->candidate_id,
                        'stage_id' => $master_status,
                        'user_id' => $data->submitted_by,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),

                    ];
                    DB::table('stage_point_tb')->insert($values);

                } else {

                    if (! isset($stages[$currentStageId]) || ! isset($stages[$newStageId])) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'Invalid stage transition.',
                        ]);
                    }

                    $currentOrder = $stages[$currentStageId]['order'];
                    $newOrder = $stages[$newStageId]['order'];

                    $currentName = $stages[$currentStageId]['display'];
                    $newName = $stages[$newStageId]['display'];

                    // InReview(8) and agency-Rejected(9) are the submitting
                    // agency's own territory — once a candidate is sourced
                    // (currentStageId 1-7), control belongs to the client
                    // company, not the agency, and the agency can no longer
                    // change its stage at all (not even to reject via 9).
                    $isAgencySubmission = ((int) $addJobWiseCondidate->thirdparty_company_id === (int) $company_id)
                        && (int) $addJobWiseCondidate->thirdparty_company_id > 0;

                    // The only valid moves touching InReview/agency-Rejected:
                    // 8->1 (agency sources it), 8->9 (agency rejects pre-
                    // sourcing), 9->8 (agency un-rejects, back to review).
                    // 9->1 directly is explicitly NOT allowed — it must go
                    // back through InReview first.
                    $isSpecialAgencyEdge = ($currentStageId == 8 && in_array($newStageId, [1, 9], true))
                        || ($currentStageId == 9 && $newStageId == 8);

                    if ($currentStageId == 9 && $newStageId == 1) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'Move this candidate back to InReview before sourcing it again.',
                        ]);
                    }

                    if (! $isSpecialAgencyEdge && $isAgencySubmission && ! in_array($currentStageId, [8, 9], true)) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'This candidate has already been sourced — only the client company can change its stage now.',
                        ]);
                    }

                    // The company can never send a candidate back to
                    // InReview once it's been sourced — that stage belongs
                    // to the agency, and reverting it isn't the company's
                    // call to make.
                    if (! $isSpecialAgencyEdge && $newStageId == 8) {
                        return Reply::dataOnly([
                            'status' => 'candidatevalidation',
                            'message' => 'A candidate cannot be moved back to InReview once sourced.',
                        ]);
                    }

                    if (! $isSpecialAgencyEdge) {
                        // 🔹 Check if jumping ahead — Rejected(7) is reachable
                        // directly from any active company stage, so it's
                        // exempt from the "no skipping ahead" rule.
                        if ($newStageId != 7 && $newOrder > $currentOrder + 1) {
                            return Reply::dataOnly([
                                'status' => 'candidatevalidation',
                                'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName),
                            ]);
                        }

                        // 🔹 Check if going backward
                        if ($newOrder < $currentOrder) {
                            if ($currentOrder != $newOrder + 1) {
                                return Reply::dataOnly([
                                    'status' => 'candidatevalidation',
                                    'message' => $this->stageSequenceGuardMessage($stages, $currentOrder, $currentName, $newName),
                                ]);
                            }
                        }
                    }

                    $sourcing = 0;
                    $screening = 0;
                    $evaluation = 0;
                    $negotiation = 0;
                    // if (!isset($stages[$currentStageId]) || !isset($stages[$newStageId])) {

                    if ($newStageId > $currentStageId || $currentStageId == 8) {
                        if ($newStageId == 1) {
                            $sourcing = 1;
                        } elseif ($newStageId == 2) {
                            $screening = 1;
                        } elseif ($newStageId == 3) {
                            $evaluation = 1;
                        } elseif ($newStageId == 4) {
                            $negotiation = 1;
                        }

                    }

                    if ($newStageId < $currentStageId) {
                        if ($currentStageId == 1) {
                            $sourcing = -1;
                        } elseif ($currentStageId == 2) {
                            $screening = -1;
                        } elseif ($currentStageId == 3) {
                            $evaluation = -1;
                        } elseif ($currentStageId == 4) {
                            $negotiation = -1;
                        }
                    } elseif ($newStageId == 8 && $currentStageId == 1) {
                        $sourcing = -1;
                    }

                    $pendingTeamRankElastic = [
                        'company_id' => $data->company_id ?: ($addJobWiseCondidate->company_id ?: auth()->user()->company_id),
                        'user_id' => $data->submitted_by ?: ($addJobWiseCondidate->submitted_by ?: auth()->id()),
                        'candidate_id' => $data->candidate_id ?: ($addJobWiseCondidate->job_application_id ?: $addJobWiseCondidate->id),
                        'sourcing' => $sourcing,
                        'screening' => $screening,
                        'evaluation' => $evaluation,
                        'negotiation' => $negotiation,
                    ];

                    DB::table('stage_point_tb')->where('job_id', '=', $data->jobId)->where('candidate_id', '=', $data->candidate_id)->delete();

                    // When this move IS a rejection (newStageId 7), the resume
                    // point must be the stage being rejected FROM, not 7 itself —
                    // recording 7 here permanently blocked ever moving back to
                    // InReview/wherever the candidate was, since un-rejecting
                    // later requires the target to match this recorded stage_id.
                    $resumeStageId = ($newStageId == 7) ? $currentStageId : $master_status;
                    $values = [
                        'job_id' => $data->jobId,
                        'candidate_id' => $data->candidate_id,
                        'stage_id' => $resumeStageId,
                        'user_id' => $data->submitted_by,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),

                    ];
                    DB::table('stage_point_tb')->insert($values);

                }
            }

        }

        if ($master_status != 9) {

            // dd("this is error section ");

            $validations = GeneralMethod::candidateValidation($addJobWiseCondidate_id);
            if ($validations->status) {
                return Reply::dataOnly(['status' => 'candidatevalidation', 'message' => $validations->message]);
            }

            if (JobApplication::where('job_applications.resume', '=', '')->where('job_applications.id', '=', $application_id)->exists()) {
                return Reply::dataOnly(['status' => 'cvpending', 'message' => 'Resume pending this job application!']);
            }

        }
        // dd("dk");

        $jobApplication = DB::table('job_applications')
            ->select('*')
            ->where('job_applications.id', '=', $application_id)
            ->first();

        $addJobWiseCondidate = DB::table('add_job_wise_condidates')
            ->select('*')
            ->where('add_job_wise_condidates.id', '=', $addJobWiseCondidate_id)
            ->first();

        $appRow = DB::table('job_applications')->select('job_applications.company_id')->where('job_applications.id', '=', $application_id)->first();
        $client_company_id = $appRow->company_id;

        //    dd("l");
        // if (($master_status == 1) && ($client_company_id > 0)) {

        // 	$packageStatus = GeneralMethod::validateClientPackageRet('CVACCESS', $client_company_id);
        // 	if ($packageStatus != 1) {
        // 		return Reply::dataOnly(['status' => 'info', "message" => "CV Access / Received Opps something worng with client package , please contact to Administrator."]);
        // 	}

        // }

        if (! empty(@$addJobWiseCondidate)) {

            $masterStatus = DB::table('master_status')->select('master_status.name')->where('master_status.id', $addJobWiseCondidate->master_status_id)->first();
            $prev_process_name = @$masterStatus->name;
            $client_job_applications['master_status_id'] = @$master_status;
        }

        if ($addJobWiseCondidate->child_status_id > 0 || $addJobWiseCondidate->child_status_id) {
            $subProcess = DB::table('candidate_status')
                ->select('candidate_status.name')
                ->where('candidate_status.company_id', $company_id)
                ->where('candidate_status.id', $addJobWiseCondidate->child_status_id)
                ->first();
            $prev_process_child_name = @$subProcess->name;
        }

        if (! empty(@$sub_stages)) {
            $client_job_applications['child_status_id'] = @$sub_stages;
        } else {
            $client_job_applications['child_status_id'] = '';
        }

        $date = date('Y-m-d H:i:s');
        $values = [
            'event_id' => 2,
            'event_by_company_id' => auth()->user()->company_id,
            'event_by_user_id' => auth()->user()->id,
            // 'master_process_name' 		=>	@$jobApplication->job_id,
            // 'child_process_name' 			=>	@$master_status,
            'application_id' => @$application_id ? @$application_id : null,
            'application_email' => @$jobApplication->email ? @$jobApplication->email : null,
            'job_id' => @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null,
            'note' => '',
            'create_at' => @$date ? @$date : null,
            'update_at' => @$date ? @$date : null,
            'prev_process_name' => $prev_process_name ? $prev_process_name : null,
            'prev_process_child_name' => $prev_process_child_name ? $prev_process_child_name : null,
        ];

        if (@$master_status > 0) {
            $masterStatus = DB::table('master_status')->select('master_status.name')->where('id', $master_status)->first();
            $values['master_process_name'] = @$masterStatus->name;
        }
        if (@$sub_stages > 0) {
            $masterStatus = DB::table('candidate_status')->select('candidate_status.name')->where('candidate_status.company_id', $company_id)->where('candidate_status.id', $sub_stages)->first();
            $values['child_process_name'] = @$masterStatus->name;
        }
        // dd($values);
        if (! empty(@$comment)) {
            $values['note'] = @$comment;
        }
        // DB::table('timeline')->insert($values);
        GeneralMethod::UpdateTimeline($values);

        $jobIdGet = @$addJobWiseCondidate->job_id ? @$addJobWiseCondidate->job_id : null;
        $tb_ranks = ['company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet, 'status' => 0, 'application_id' => @$application_id];
        DB::table('tb_ranks')->insert($tb_ranks);

        DB::table('job_applications')->where('job_applications.id', '=', $application_id)->limit(1)->update($client_job_applications);

        DB::table('add_job_wise_condidates')->where('add_job_wise_condidates.id', '=', $addJobWiseCondidate_id)->limit(1)->update($client_job_applications);

        if (! empty($pendingTeamRankElastic)) {
            GeneralMethod::teamRankElastic($pendingTeamRankElastic);
        }

        if (($master_status == 1) && (@$application_id > 0)) {
            $event_name = 'CVACCESS';
            $event_table_name = 'job_applications';
            $event_table_id = @$application_id;
            // $packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $client_company_id);
        }

        // 1. First verify the document exists
        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES,
            'id' => $addJobWiseCondidate_id,
        ]);

        if ($exists) {

            // 2. Prepare the update timestamp
            $updateTime = now()->toIso8601String();
            $master_status_name = DB::table('master_status')
                ->select('*')
                ->where('id', (int) $master_status)
                ->first('name');

            // 3. Execute update with debugging
            $updateParams = [
                'index' => ELASTIC_CANDIDATES,
                'id' => $addJobWiseCondidate_id,
                'body' => [
                    'doc' => [
                        'job_master_status_id' => (int) $master_status,
                        'childStatus' => (int) $sub_stages,
                        'name' => $master_status_name->name,
                        'css_style' => $master_status_name->css_style,
                        'updated_at' => $updateTime,
                    ],
                    'doc_as_upsert' => false, // Changed to false since we verified existence
                ],
                'refresh' => true, // Immediate index refresh for real-time stage listing consistency
            ];

            $this->elasticService->getClient()->update($updateParams);
            if ($application_id) {
                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CANDIDATES_BENCH,
                    'id' => $application_id,
                ]);
                if ($exists) {
                    GeneralMethod::elasticBenchCandidateAddEditIndex($application_id);
                }
            }

        }

        $currentUserId = $this->user->id ?? auth()->id();
        $cachekeys = [
            'alljobslist_version_user_'.$currentUserId,
            'allcvtracker_version_user_'.$currentUserId,
            'allapptracker_version_user_'.$currentUserId,
            'allclientapp_version_user_'.$currentUserId,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $finalJobId = (int) ($jobId ?? 0);
        $finalCompanyId = (int) ($company_id ?? auth()->user()->company_id ?? 0);
        if ($finalJobId > 0 && $finalCompanyId > 0) {
            app(\App\Services\Apptracker\CounterService::class)->onStageMoved($finalJobId, (int) $application_id, (int) $master_status, $finalCompanyId);
        }

        try {
            $wiseIds = DB::table('add_job_wise_condidates')->where('job_application_id', (int) $application_id)->pluck('id')->toArray();
            \App\Events\CandidateDrawerUpdated::dispatch(
                (int) $finalCompanyId,
                (int) $application_id,
                time(),
                'stage_updated',
                array_map('intval', $wiseIds),
                null,
                null,
                null,
                (int) $master_status,
                (int) ($data->job_master_status_id ?? 8),
                (int) $finalJobId
            );
        } catch (\Throwable $e) {
            \Log::warning('client_stage_submit CandidateDrawerUpdated dispatch failed: '.$e->getMessage());
        }

        return Reply::dataOnly(['status' => 'success', 'message' => 'Application stage update successfully !']);

    }

    public function sampleDownload(Request $request)
    {

        $file = Excel::download(new JobApplicationSampleExport($request->job_id), 'users.csv');

        return response()->json(['file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()), 'file_name' => $file->getFile()->getFilename()]);

    }

    public function candidatejobDownload(Request $request)
    {

        $candidateID = $request->arr;
        $jobId = $request->jobIdArr;
        $company_id = auth()->user()->company_id;
        $currency = GeneralMethod::currency();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymboleforexcell();

        $job_status_arr = GeneralMethod::job_status();

        $eventId = 30;
        $coinsRequ = count($candidateID);
        // dd($coinsRequ);
        $companyId = $company_id;
        $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);

        // dd($coinsDeductions,$coinsDeductions->status,$coinsDeductions->company_coins,count($candidateID));
        if ($coinsDeductions->status || $coinsDeductions->company_coins < count($candidateID)) {
            return response()->json(['status' => 'info', 'message' => $coinsDeductions->message, 'file_url' => '', 'file_name' => '']);
        } else {
            if (count($candidateID) > 0) {
                for ($i = 0; $i < count($candidateID); $i++) {
                    GeneralMethod::coinsDeductions((int) $company_id, $eventId, auth()->user()->id, $company_id, '', '');
                }

            }
        }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['terms' => ['id' => $candidateID]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [], // We'll add all filters here
            ],
        ];
        $sort = [];
        $sort[] = ['wiseId' => ['order' => 'desc', 'unmapped_type' => 'long']];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => $boolQuery,
            ],
        ];
        if (! $this->elasticService->indexExists(ELASTIC_CANDIDATES)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
            $lastSort = end($response['hits']['hits'])['sort'] ?? null;
        }

        // dd($data);
        $temArr = [];
        $custArr = [];
        if (count($data) > 0) {
            // dd($data);
            foreach ($data as $key => $rowArr) {
                $row = (object) $rowArr;
                $CustomDetails = DB::table('application_additional_detail')->where('application_id', '=', $row->candidate_id)->where('job_id', '=', $row->jobId)->pluck('application_detail_value', 'job_detail_value')->toArray();
                $custArr = [];
                if (count($CustomDetails) > 0) {
                    foreach ($CustomDetails as $dkey => $de_value) {
                        $custArr[$dkey] = $dkey.'-'.$de_value;
                    }
                }
                $childStatus_name = '';
                if ($row->job_master_status_id == 8) {
                    $childStatus_name = 'Private';
                }
                if (! empty(@$row->childStatus) && @$row->childStatus > 0) {
                    $childStatus = '';
                    $childStatus = DB::table('candidate_status')
                        ->select('*')
                        ->where('id', $row->childStatus)
                        ->first();
                    if (! empty($childStatus)) {
                        $childStatus_name = @$childStatus->name;
                    }
                }
                $schedule_date = $className = '';
                $intRowCount = DB::table('interview_schedules')
                    ->select('*')
                    ->where('interview_job_wise_con_id', $row->wiseId)
                    ->where('interview_job_id', $row->jobId)
                    ->orderBy('interview_schedules.id', 'DESC')
                    ->count();
                $intRow = DB::table('interview_schedules')
                    ->select('*')
                    ->where('interview_job_wise_con_id', $row->wiseId)
                    ->where('interview_job_id', $row->jobId)
                    ->orderBy('interview_schedules.id', 'DESC')
                    ->first();
                $html = '';
                $schedule_time = '';
                if ($intRowCount > 0) {
                    $schedule_date = @$intRow->schedule_date;
                    $schedule_date = date('d M, Y', strtotime($schedule_date));
                    $timezone_abbreviation = ($intRow->timezone_abbreviation) ? ' ('.$intRow->timezone_abbreviation.')' : '';
                    $schedule_time = @$intRow->schedule_to_time.' '.$timezone_abbreviation;
                }
                $np = $row->np_id;
                if (empty($np)) {
                    $np = 'N/A';
                }
                if ($row->serving_notice_period_mode == 0) {
                    $np = $row->np_id.' Days';
                    if ($row->np_id == 1) {
                        $np = $row->np_id.'  Day';
                    }
                } elseif ($row->serving_notice_period_mode == 1) {
                    $np = $row->np_id;
                    if ($np != 'Immediate') {
                        $np = date('d M y', strtotime($row->np_id));
                    }
                }

                $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
                $symbolsValue = ! empty($symbolsValue) ? $symbolsValue : [
                    0 => 'INR',
                    1 => 'EUR',
                    2 => 'JPY',
                    3 => 'GBP',
                    4 => 'USD',
                    5 => 'AED',
                ];

                $jobData = DB::table('jobs')->select('max_sal_mode as jobMode', 'max_salary_currency as jobCurrency')->where('id', '=', $row->jobId)->first();
                $jobMode = $jobData->jobMode;
                $jobCurrency = $jobData->jobCurrency;
                $symbols = $symbolsValue[$jobCurrency] ?? 'INR';

                $current_ctc_cur = '';
                $current_ctc_cur = $currencySymbole[@$jobCurrency];

                $current_ectc_cur = '';
                $current_ectc_cur = $currencySymbole[@$jobCurrency];

                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_cur = $currencySymbole[@$jobCurrency];
                }

                $payFormatViewSalary = GeneralMethod::formatViewSalary($row->ctc_yearly, $symbols, $jobMode);
                $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly, $symbols, $jobMode);

                $ctc = '';
                $ctc = GeneralMethod::thousandsCurrencyFormat($payFormatViewSalary->amount);
                $ectc = '';
                $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                if ($row->condidate_type == 'bench_condidate') {
                    $ectc = GeneralMethod::thousandsCurrencyFormat($billFormatViewSalary->amount);
                }

                $current_ctc_mode = '';
                $current_ctc_mode = substr($salaryMode[@$jobMode], 0, 1);
                $current_ctc_mode = substr($salaryMode[0], 0, 1);

                $current_ectc_mode = '';
                $current_ectc_mode = substr($salaryMode[@$jobMode], 0, 1);
                $current_ectc_mode = substr($salaryMode[0], 0, 1);
                if ($row->condidate_type == 'bench_condidate') {
                    $current_ectc_mode = substr($salaryMode[@$jobMode], 0, 1);
                }
                $dnone = '';
                if ($row->condidate_type == 'bench_condidate') {
                    $dnone = '--------';
                }

                $bench_assign = DB::table('bench_assign')
                    ->leftjoin('users', 'users.id', 'bench_assign.user_id')
                    ->select('users.name', 'users.email')
                    ->where('bench_assign.bench_applications_id', $row->candidate_id)
                    ->first();
                $percentage_hike = '-----';
                $current_ctc_cur_set = '------';
                if ($bench_assign) {
                    $email = @$bench_assign->email;
                    $phone = @$bench_assign->mobile;
                    $row->current_company = @$row->apl_company_name;
                } else {
                    $email = @$row->email;
                    $phone = @$row->phone;
                    $percentage_hike = @$row->percentage_hike.'% '.@$row->comment;
                    $current_ctc_cur_set = @$current_ctc_cur.' '.@$ctc.'/'.$current_ctc_mode;
                }

                $client_name = '';
                $client_name_com = GeneralMethod::companyDetailsByClient($row->jobId);
                if (! empty($client_name_com)) {
                    $client_title_name = $client_name_com;
                    $client_name = ucwords($client_title_name);
                } else {
                    $client_title_name = $row->client_name;
                    $client_name = ucwords($client_title_name);
                }

                $temArr[$key] = [
                    'jobId' => 'R-'.@$row->jobId,
                    'jobTitle' => ucwords(@$row->title),
                    'fullName' => ucwords(@$row->full_name),
                    'currentDesignation' => @$row->current_designation,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'totalExp' => @$row->total_exp.' Yr',
                    'row_city_name' => @$row->city_name,
                    'row_state_name' => @$row->state_name,
                    'row_country_name' => @$row->country_name,
                    'current_company' => (@$row->current_company) ? @$row->current_company : '-------',
                    'client_company' => (@$client_name) ? @$client_name : '-------',
                    'current_ectc_cur' => @$current_ectc_cur.' '.@$ectc.'/'.@$current_ectc_mode,
                    'current_ctc_cur' => @$current_ctc_cur_set,
                    'Comment' => @$row->Comments,
                    'notic' => @$np,
                    'schedule_date' => @$schedule_date."\n".$schedule_time,
                    'upload_date' => date('d-m-Y H:i:s', strtotime(@$row->created_at)),
                    'qualification_name' => ucwords(@$row->qualification_name),
                    'stage' => ucwords($row->name),
                    'sub_stage' => ucwords(@$childStatus_name),
                    'sourceby' => ucwords(@$row->company_name),
                    'sourcename' => ucwords(@$row->posted_by),
                    'custom_details' => implode(',', @$custArr),
                ];
            }
        }

        $filename = 'candidate_export_'.now()->format('Ymd_His').'.xlsx';

        // Store in a public path or storage/app/public/...
        $path = 'temp/exports/'.$filename;
        // Excel::store(new JobApplicationCustomExport($temArr), 'exports/' . $filename, 'public');
        Excel::store(new JobApplicationCustomExport($temArr), $path, 'r2');

        // dd(asset('storage/app/public/exports/' . $filename));
        $fileUrl = "https://file.jero.works/{$path}";

        // Return response with full URL
        return response()->json([
            'status' => 'success',
            'file_url' => $fileUrl,
            'file_name' => $filename,
        ]);

        $jobApplications = DB::table('job_applications')
            ->select(
                'job_applications.*',
                'jobs.title',
                'job_locations.location',
                'clients.name as client_name',
                'users.name as posted_by',
                'users.image as posted_by_image',
                'master_status.name',
                'master_status.css_style',
                'qualification_master.name as qualification_name',
                'state_list.state as state_name',
                'recommendation_relation.final_status',
                'recommendation_relation.id as recommendation_id',
                'recommendation_relation.from_company_id',
                'recommendation_relation.to_company_id',
                'companies.company_name',
                'app_com.company_name as apl_company_name',
                'current_com.company_name as client_company',
                'jobs.status as job_status',
                'add_job_wise_condidates.child_status_id as childStatus',
                'add_job_wise_condidates.id as wiseId',
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
                'add_job_wise_condidates.job_id as add_job_id',
                'add_job_wise_condidates.job_id as jobId',
                'add_job_wise_condidates.created_at as upload_date'
            )
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
            ->join('master_status', function ($join) {
                $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
            })
            ->leftjoin('offer_job_applications', function ($join) {
                $join->on('offer_job_applications.joined', 'add_job_wise_condidates.id');
            })
            ->join('jobs', function ($join) {
                $join->on('jobs.id', 'add_job_wise_condidates.job_id');
            })
            ->leftjoin('companies as app_com', function ($join) {
                $join->on('app_com.id', 'job_applications.company_id');
            })
            ->leftjoin('companies as current_com', function ($join) {
                $join->on('current_com.id', 'add_job_wise_condidates.company_id');
            })
                // ->join('jobs', 'jobs.id', 'job_applications.job_id')
            ->leftjoin('job_skills', 'jobs.id', 'job_skills.job_id')
                // ->leftjoin('master_status', 'master_status.id', 'job_applications.master_status_id')
            ->leftjoin('job_locations', 'job_locations.id', 'jobs.location_id')
            ->leftjoin('clients', 'clients.id', 'jobs.client_id')
            ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
            ->leftjoin('companies', 'companies.id', 'users.company_id')
            ->leftjoin('qualification_master', 'qualification_master.id', 'job_applications.qualification')
            ->leftjoin('application_status', 'application_status.id', 'job_applications.status_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->leftjoin('state_list', 'state_list.id', 'job_applications.state');
        //    dd(request('status_cus'));

        $jobApplications = $jobApplications->whereIn('add_job_wise_condidates.job_application_id', $candidateID);

        $jobApplications = $jobApplications->whereIn('add_job_wise_condidates.job_id', $jobId);

        $jobApplications = $jobApplications->groupBy('add_job_wise_condidates.id');
        $jobApplications = $jobApplications->orderBy('add_job_wise_condidates.id', 'DESC')->get()->toArray();
        $temArr = [];
        foreach ($jobApplications as $key => $row) {

            if (empty($row->country)) {
                $row_country_name = '----';
            } else {
                if (! empty(@$row->country)) {
                    if (is_numeric(@$row->country)) {
                        $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                        foreach ($country_name_Arr as $cval) {
                            $row_country_name = ucwords($cval['name']);
                        }
                    }
                }
            }

            $state = '';
            if (empty($row->state)) {
                $row_state_name = '----';
            } else {
                if (@$row->country == 101) {
                    $row_state_name = @$row->state_name;
                } else {
                    $state = @$row->state;
                    if (is_numeric(@$row->country) && is_numeric($state)) {
                        if ($state > 0) {
                            $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                            foreach ($state_name_Arr as $sval) {
                                $row_state_name = ucwords($sval['name']);
                            }
                        }
                    }

                }

            }

            if (@$row->country == 101) {
                $row_city = DB::table('city_list')->select('*')->where('city_id', @$row->city_id)->first();
                $row_city_name = @$row_city->city;
            } else {
                $row_city_name = @$row->city;
            }

            $current_ctc_cur = '';
            $current_ctc_cur = $currencySymbole[@$row->current_ctc_cur];

            $current_ectc_cur = '';
            $current_ectc_cur = $currencySymbole[@$row->current_ectc_cur];

            if ($row->condidate_type == 'bench_condidate') {
                $current_ectc_cur = $currencySymbole[@$row->bill_rate_currency];
            }

            $ctc = '';
            $ctc = GeneralMethod::thousandsCurrencyFormat($row->ctc);
            $ectc = '';
            $ectc = GeneralMethod::thousandsCurrencyFormat($row->ectc);
            if ($row->condidate_type == 'bench_condidate') {
                $ectc = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
            }

            $current_ctc_mode = '';
            $current_ctc_mode = substr($salaryMode[@$row->current_ctc_mode], 0, 1);
            $current_ctc_mode = substr($salaryMode[0], 0, 1);

            $current_ectc_mode = '';
            $current_ectc_mode = substr($salaryMode[@$row->current_ectc_mode], 0, 1);
            $current_ectc_mode = substr($salaryMode[0], 0, 1);
            if ($row->condidate_type == 'bench_condidate') {
                $current_ectc_mode = substr($salaryMode[@$row->bill_rate_mode], 0, 1);
            }
            $dnone = '';
            if ($row->condidate_type == 'bench_condidate') {
                $dnone = '--------';
            }

            $np = $row->np_id;
            if (empty($np)) {
                $np = 'N/A';
            }
            if ($row->serving_notice_period_mode == 0) {
                if (! empty($row->np_id)) {
                    $np = $row->np_id.'  Days';
                }
            } else {
                $np = $row->np_id;
            }

            if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id)) {
                $stage_default_css = 'client_stage_sub_stage';
            }
            if (($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) && $row->master_status_id != 8) {
                $stage_default_css = '';
            }
            $name = '--------';
            if ($row->master_status_id == 8) {
                $name = '';
            }
            if (! empty(@$row->childStatus) && @$row->childStatus > 0) {
                $childStatus = '';
                $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->childStatus)->first();
                if (! empty($childStatus)) {
                    $name = @$childStatus->name;
                }
            }
            $schedule_date = '------';
            $intRow = DB::table('interview_schedules')->select('*')->where('job_application_id', $row->id)->where('interview_job_id', $row->add_job_id)->orderBy('interview_schedules.id', 'DESC')->first();
            if ($intRow) {
                $schedule_date = @$intRow->schedule_date;
                $schedule_date = date('d M, Y', strtotime($schedule_date));
            }
            $CustomDetails = DB::table('application_additional_detail')->where('job_id', '=', $row->jobId)->pluck('application_detail_value', 'job_detail_value')->toArray();
            $custArr = [];
            if (count($CustomDetails) > 0) {
                foreach ($CustomDetails as $dkey => $de_value) {
                    $custArr[$dkey] = $dkey.'-'.$de_value;
                }
            }

            $this->team = DB::table('users')
                ->leftjoin('role_user', 'role_user.user_id', 'users.id')
                ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
                ->leftjoin('roles', 'roles.id', 'role_user.role_id')
                ->select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.working_status', 'users_profile.designation')
                ->where('users.company_id', '=', $company_id)
                ->where('status', '=', 'active')
                ->orderBy('users.name', 'ASC')
                ->get();

            $bench_assign = DB::table('bench_assign')
                ->leftjoin('users', 'users.id', 'bench_assign.user_id')
                ->select('users.name', 'users.email')
                ->where('bench_assign.bench_applications_id', $row->id)
                ->first();
            $percentage_hike = '-----';
            $current_ctc_cur_set = '------';
            if ($bench_assign) {
                $email = @$bench_assign->email;
                $phone = @$bench_assign->mobile;
                $row->current_company = @$row->apl_company_name;
            } else {
                $email = @$row->email;
                $phone = @$row->phone;
                $percentage_hike = @$row->percentage_hike.'% '.@$row->comment;
                $current_ctc_cur_set = @$current_ctc_cur.' '.@$ctc.'/'.$current_ctc_mode;
            }

            // dd($custArr);

            $temArr[$key] = [
                'jobId' => 'R-'.@$row->jobId,
                'jobTitle' => ucwords(@$row->title),
                'fullName' => ucwords(@$row->full_name),
                'currentDesignation' => @$row->current_designation,
                'email' => @$email,
                'phone' => @$phone,
                'totalExp' => @$row->total_exp.' Yr',
                'row_city_name' => @$row_city_name,
                'row_state_name' => @$row_state_name,
                'row_country_name' => @$row_country_name,
                'current_company' => (@$row->current_company) ? @$row->current_company : '-------',
                'client_company' => (@$row->client_company) ? @$row->client_company : '-------',
                'current_ectc_cur' => @$current_ectc_cur.' '.@$ectc.'/'.$current_ectc_mode,
                'current_ctc_cur' => $current_ctc_cur_set,
                'Comment' => @$percentage_hike,
                'notic' => @$np,
                'schedule_date' => $schedule_date,
                'upload_date' => date('d-m-Y H:i:s', strtotime(@$row->upload_date)),
                'qualification_name' => ucwords(@$row->qualification_name),
                'stage' => ucwords($row->name),
                'sub_stage' => ucwords(@$name),
                'sourceby' => ucwords(@$row->company_name),
                'sourcename' => ucwords(@$row->posted_by),
                'custom_details' => implode(',', $custArr),
            ];

            // $temArr = array_merge($temArr[$key],$custArr);

        }
        //  dd($temArr);

        // Create a unique or custom file name
        $filename = 'candidate_export_'.now()->format('Ymd_His').'.xlsx';

        // Store in a public path or storage/app/public/...
        Excel::store(new JobApplicationCustomExport($temArr), 'exports/'.$filename, 'public');

        // dd(asset('storage/app/public/exports/' . $filename));

        // Return response with full URL
        return response()->json([
            'status' => 'success',
            'file_url' => asset('storage/app/public/exports/'.$filename),
            'file_name' => $filename,
        ]);

        //  $file =   Excel::download(new JobApplicationCustomExport($temArr), 'custom-export.xlsx');

        //  dd( $file,$file->getFile()->getFilename());

        //  return response()->json(['status' => 'success','file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()),'file_name'=>$file->getFile()->getFilename()]);

        //   dd($temArr,$candidateID,$jobId,$jobApplications);

        // $file =  Excel::download(new JobApplicationSampleExport($request->job_id), 'users.xlsx');
        // return response()->json(['file_url' => asset('storage/framework/laravel-excel/'.$file->getFile()->getFilename()),'file_name'=>$file->getFile()->getFilename()]);

    }

    public function unlinkDownload(Request $request)
    {
        // $filename = asset('storage/framework/laravel-excel/'.$request->file_name);
        // $filename = 'framework/laravel-excel/' . $request->file_name;
        // $filename = public_path('storage/framework/laravel-excel/'.$request->file_name);
        $filePath = asset('storage/app/public/exports/'.$request->file_name); // storage_path('framework/laravel-excel/' . );
        // Check if the file exists before attempting to delete it
        if (file_exists($filePath)) {
            // Attempt to delete the file
            if (unlink($filePath)) {
                // File deleted successfully
                return 'File deleted successfully.';
            } else {
                // Unable to delete the file
                return 'Unable to delete the file.';
            }
        } else {
            // File doesn't exist
            return 'File not found.';
        }
    }

    public function importJobCondidate(Request $request)
    {

        // $filePath = $request->file('file')->getRealPath();
        $file = $request->file('file');
        $filePath = $file->store('temp');
        $extension = $file->extension();
        // dd($extension);

        // Check if the extension is what you expect
        if ($extension === 'txt') {
            $rows = Excel::import(new JobApplicationImport($request->job_id), $request->file('file'));
        } else {

            // dd($rows);
            // $rows = Excel::toCollection(new JobApplicationImport($file), $file);
            // Get the real path of the uploaded file
            $realPath = Storage::path($filePath);

            // $csvData = array_map('str_getcsv', file($file));

            $rows = Excel::toCollection([], $filePath)->first();
            // Define chunk size
            $chunkSize = 50;
            // Split data into chunks
            $chunks = $rows->chunk($chunkSize);
            foreach ($chunks as $chunk) {
                $this->processChunk($request->job_id, $chunk);
            }
        }

        // Excel::import(new JobApplicationImport($request->job_id), $request->file('file'));
        // dd("djd");
        return response()->json(['status' => true, 'message' => 'File(s) imported successfully.']);
        // dd($request->all());
    }

    public function processChunk($jonId, $chunk)
    {
        $collection = $chunk->toArray();
        if ($collection) {

            foreach ($collection as $key => $row) {

                if ($row[0] != 'Full Name') {
                    // dd($row[0]);
                    $qualification_master = DB::table('qualification_master')->select('id')->where('name', 'LIKE', '%'.$row[8].'%')->first();

                    $countr_master = DB::table('countries')->select('id')->where('country_name', '=', $row[15])->first();
                    $state_master = DB::table('state_list')->select('id')->where('state', '=', $row[16])->first();
                    $city_master = DB::table('city_list')->select('city_id')->where('city', '=', $row[17])->first();

                    $company_id = '';
                    $company_id = auth()->user()->company_id;

                    $applicationStatus = ApplicationStatus::first();
                    $jobApplication = new JobApplication;

                    $jobApplication->full_name = $row[0];
                    $jobApplication->job_id = $jonId;
                    $jobApplication->status_id = 8;
                    $jobApplication->email = $row[6];
                    $jobApplication->phone = $row[3];
                    $jobApplication->column_priority = 0;
                    $jobApplication->total_exp = $row[9];
                    $jobApplication->rel_exp = $row[10];
                    $jobApplication->ctc = $row[12];
                    $jobApplication->current_ctc_cur = 1;
                    $jobApplication->current_ctc_mode = 1;

                    $jobApplication->ectc = $row[13];
                    $jobApplication->current_ectc_cur = 1;
                    $jobApplication->current_ectc_mode = 1;

                    $jobApplication->percentage_hike = $row[14];
                    $jobApplication->comment = '';
                    $jobApplication->pincode = $row[18];

                    // $jobApplication->current_location 	=	 $row[0]->current_location;
                    $jobApplication->current_designation = $row[1];
                    $jobApplication->current_company = $row[2];

                    $jobApplication->skills = $row[19];

                    // $jobApplication->master_status_id 	=	$applicationStatus->id;
                    $jobApplication->master_status_id = 8;
                    $jobApplication->secondrary_email = $row[7];
                    $jobApplication->secondary_mobile = $row[4];
                    $submitted_by = '';
                    $submitted_by = auth()->user()->id;
                    if (! empty($submitted_by)) {
                        $jobApplication->submitted_by = $submitted_by;
                    }

                    $jobApplication->np_id = $row[11];

                    if (! empty($row[8])) {
                        $jobApplication->qualification = ($qualification_master) ? $qualification_master->id : 1;
                    }
                    $jobApplication->company_id = $company_id;
                    //   dd($row[5],$jobApplication->company_id );
                    if ($row[5]) {
                        $date = Carbon::createFromTimestamp((int) ($row[5] - 25569) * 86400);
                        $formattedDate = $date->format('Y-m-d');
                        $jobApplication->dob = $formattedDate;
                    }
                    // dd($jobApplication->dob);

                    $jobApplication->country = ($countr_master) ? $countr_master->id : 101;
                    $jobApplication->state = ($state_master) ? $state_master->id : '';

                    $jobApplication->city_id = ($city_master) ? $city_master->city_id : '';

                    $jobApplication->city = ($city_master) ? '' : $row[17];

                    $applicationEmail = $row[6];
                    $chkApplication = DB::table('job_applications')->select('job_applications.id')->where('company_id', $company_id)->where('email', $applicationEmail)->orWhere('phone', $row[3])->count();
                    if ($chkApplication > 0) {
                        $jobApplication->duplicate = 1;
                    }
                    //    dd($jobApplication);
                    $jobApplication->save();

                    $skillArr = explode(',', $jobApplication->skills);
                    $percentage = GeneralMethod::skill_match_percentage($jonId, $skillArr);
                    $now = now();
                    $insertArr = ['job_id' => $jonId, 'job_application_id' => $jobApplication->id, 'status_id' => $jobApplication->status_id, 'master_status_id' => $jobApplication->master_status_id, 'child_status_id' => $jobApplication->child_status_id, 'company_id' => $company_id, 'submitted_by' => $submitted_by, 'skill_match' => $percentage, 'created_at' => $now, 'updated_at' => $now];
                    DB::table('add_job_wise_condidates')->insert($insertArr);
                    $lastInsertedId = DB::getPdo()->lastInsertId();

                    $cachekeys = [
                        'alljobslist_version_user_'.$this->user->id,
                        'allcvtracker_version_user_'.$this->user->id,
                    ];
                    foreach ($cachekeys as $key) {
                        GeneralMethod::cacheClean($key);
                    }

                    $jobId = $jonId;
                    $teamRankElastic = [
                        'company_id' => $company_id,
                        'user_id' => $submitted_by,
                        'candidate_id' => $jobApplication->id,
                        'profileUpload' => 1,
                        'interviewScheduled' => 0,
                        'interviewNoShow' => 0,
                        'offerReleased' => 0,
                        'candidateJoined' => 0,
                        'jobsPosted' => 0,
                        'feedbackGivenOnCVs' => 0,
                        'postJobToVendorMarket' => 0,
                        'outsourceJobsToVendors' => 0,
                        'listBenchCandidates' => 0,
                        'respondToBenchRequests' => 0,
                    ];
                    GeneralMethod::teamRankElastic($teamRankElastic);

                    $wise_c = DB::table('job_applications')
                        ->select('master_status.name', 'master_status.css_style', 'add_job_wise_condidates.child_status_id as childStatus', 'add_job_wise_condidates.master_status_id', 'jobs.title', 'jobs.id as job_id', 'users.name as posted_by', 'users.image as posted_by_image', 'companies.company_name', 'add_job_wise_condidates.created_at', 'recommendation_relation.id as recommendation_id', 'recommendation_relation.from_company_id', 'recommendation_relation.final_status', 'recommendation_relation.to_company_id')
                        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                        ->leftjoin('users', 'users.id', 'job_applications.submitted_by')
                        ->leftjoin('companies', 'companies.id', 'users.company_id')
                        ->join('master_status', function ($join) {
                            $join->on('master_status.id', 'add_job_wise_condidates.master_status_id');
                        })->join('jobs', function ($join) {
                            $join->on('jobs.id', 'add_job_wise_condidates.job_id');
                        })
                        ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                        ->where('add_job_wise_condidates.id', '=', $lastInsertedId)->where('add_job_wise_condidates.job_id', '=', $jobId)->first();

                    $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->where('job_assign.job_id', '=', $jobId)->pluck('users.email')->toArray();

                    $recommendedDataList = DB::table('recommendation_relation');
                    $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
                    $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
                    $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
                        $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
                        $join->on('job_applications.job_id', '=', 'jobs.id');
                    });
                    $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
                    $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
                    $recommendedDataList = $recommendedDataList->groupBy('companies.id');
                    $recommendedDataList = $recommendedDataList->pluck('companies.company_email');
                    $recommendedDataList = $recommendedDataList->toArray();

                    $company_or_client = GeneralMethod::companyDetailsByClient($jobId);

                    $name = '--------';
                    if (@$wise_c->master_status_id == 8) {
                        $name = '';
                    }
                    if (! empty(@$wise_c->childStatus) && @$wise_c->childStatus > 0) {
                        $childStatus = '';
                        $childStatus = DB::table('candidate_status')->select('*')->where('id', $wise_c->childStatus)->first();
                        if (! empty($childStatus)) {
                            $name = @$childStatus->name;
                        }
                    }

                    $np = $jobApplication->np_id;
                    if (empty($np)) {
                        $np = 'N/A';
                    }
                    if ($jobApplication->serving_notice_period_mode == 0) {
                        $np = $jobApplication->np_id.' Days';
                        if ($jobApplication->np_id == 1) {
                            $np = $jobApplication->np_id.'  Day';
                        }
                    } elseif ($jobApplication->serving_notice_period_mode == 1) {
                        $np = $jobApplication->np_id;
                        if ($np != 'Immediate') {
                            $np = date('d-m-Y', strtotime($jobApplication->np_id));
                        }
                    }

                    $data = [
                        'VendorTeam' => @$wise_c->company_name,
                        'ReqID' => 'R-'.@$wise_c->job_id,
                        'Reqname' => @$wise_c->title,
                        'CompanyNameClientname' => $company_or_client,
                        'CandidateStatus' => $jobApplication->duplicate,
                        'CurrentStage' => @$wise_c->name,
                        'SubStage' => $name,
                        'CandidateName' => $jobApplication->full_name,
                        'Experience' => $jobApplication->total_exp,
                        'Position' => $jobApplication->current_designation,
                        'NoticePeriod' => $np,
                        'RefferedBy' => @$wise_c->posted_by,
                        'SubmittedOn' => date('d-m-Y H:i:s', strtotime(@$wise_c->created_at)),
                    ];
                    config(['mail.from.name' => auth()->user()->name]);
                    $cc = array_merge($partnersData, $recommendedDataList);
                    // Mail::to(auth()->user()->email)->cc($cc)->send(new AddCandidateMail((object) $data));

                    $chkJobAssign = DB::table('job_assign')->select('job_assign.id')->where('company_id', $company_id)->where('job_id', $jobId)->where('user_id', $submitted_by)->count();
                    if ($chkJobAssign < 1) {
                        $values = ['company_id' => @$company_id, 'user_id' => @$submitted_by, 'job_id' => $jobId];
                        DB::table('job_assign')->insert($values);
                    }

                    if (! empty($row[19])) {
                        DB::table('candidate_skills_value')->where('job_id', $jobId)->where('candidate_id', $jobApplication->id)->delete();
                        // dd("hi");
                        $skill_value = explode(',', $row[19]);
                        foreach ($skill_value as $key => $value) {
                            if (! empty(@$value)) {
                                // dd($value,$jobApplication);
                                $values = ['job_id' => @$jobId, 'skill_id' => @$key, 'candidate_id' => $jobApplication->id, 'skill_value' => @$value];
                                DB::table('candidate_skills_value')->insert($values);
                            }
                        }
                    }

                    $timelineArr = [
                        'event_id' => 1,
                        'event_by_company_id' => auth()->user()->company_id,
                        'event_by_user_id' => auth()->user()->id,
                        'master_process_name' => 'InReview',
                        // 'child_process_name'	=>

                        'application_id' => $jobApplication->id,
                        'application_email' => $jobApplication->email,
                        'job_id' => $jobId,
                        'note' => 'Successfully Added',
                    ];
                    GeneralMethod::UpdateTimeline($timelineArr);

                    $jobIdGet = $jobId;
                    $tb_ranks = ['company_id' => auth()->user()->company_id, 'user_id' => auth()->user()->id, 'job_id' => @$jobIdGet, 'status' => 0, 'application_id' => @$jobApplication->id];
                    DB::table('tb_ranks')->insert($tb_ranks);

                    // Job Application Answer save

                    // $rowData = $row->toArray();
                    $job_additional_detail_arr = array_slice($row, 20);
                    // dd($job_additional_detail[0]);

                    $job_additional_detail_count = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->count();
                    $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', $jobId)->orderBy('job_additional_detail.id', 'ASC')->get();
                    // dd($job_additional_detail);
                    if ($job_additional_detail_count > 0) {
                        $inc = 0;
                        foreach ($job_additional_detail as $jd) {
                            // dd($job_additional_detail_arr[$inc]);
                            $values = [
                                'job_id' => @$jobId,
                                'question_id' => @$jd->id,
                                'application_id' => @$jobApplication->id,
                                'job_detail_value' => @$jd->detail_value,
                                'application_detail_value' => $job_additional_detail_arr[$inc],
                            ];
                            // dd($valueswww);
                            DB::table('application_additional_detail')->insert($values);
                            $expId = DB::getPdo()->lastInsertId();
                            $this->elasticService->getClient()->update([
                                'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                                'id' => $expId,   // store as ES _id
                                'body' => [
                                    'doc' => [
                                        'job_id' => @$jobId,
                                        'question_id' => @$jd->id,
                                        'application_id' => @$jobApplication->id,
                                        'job_detail_value' => @$jd->detail_value,
                                        'application_detail_value' => $job_additional_detail_arr[$inc],
                                        'created_at' => now()->toIso8601String(),
                                        'updated_at' => now()->toIso8601String(),
                                    ],
                                    'doc_as_upsert' => true,
                                ],
                            ]);
                            $inc++;

                        }
                    }

                }

            }
        }
        // Excel::import(new JobApplicationImport($jonId), $chunk);
        // Excel::import(new JobApplicationImport($jonId), $chunk);
    }

    public function resumExtract(Request $request)
    {

        // dd($request->all());
        if (is_array($request->resumeText)) {
            $textData = implode(',', $request->resumeText);
        } else {
            $textData = $request->resumeText;
        }

        // dd($textData);

        $textData = str_replace('Resume', ' ', $textData);
        $textData = str_replace('Curriculum Vitae', ' ', $textData);
        $textData = str_replace('CURRICULUM - VITAE', ' ', $textData);
        $textData = str_replace('CURRICULUM VITAE', ' ', $textData);
        $textData = str_replace('Curriculum-vitae', ' ', $textData);
        $textData = str_replace('CV', ' ', $textData);
        $textData = str_replace('CURRICULUM', ' ', $textData);
        $textData = str_replace('Vitae', ' ', $textData);
        $textData = str_replace('Curriculum', ' ', $textData);
        $textData = str_replace('VITAE', ' ', $textData);
        $textData = str_replace('NAME', ' ', $textData);
        $textData = str_replace('Name', ' ', $textData);
        $textData = str_replace('Greater', ' ', $textData);
        $textData = str_replace('Address', ' ', $textData);
        $textData = str_replace('PERSONAL INFORMATION', ' ', $textData);
        $textData = str_replace('Contact Mobile', ' ', $textData);
        $textData = str_replace('Email', ' ', $textData);
        $textData = str_replace('email', ' ', $textData);
        $textData = str_replace('Contact', ' ', $textData);
        $textData = str_replace('contact', ' ', $textData);
        $textData = str_replace('Mobile', ' ', $textData);
        $textData = str_replace('mobile', ' ', $textData);
        $textData = str_replace('Key Skills', ' ', $textData);
        $textData = str_replace('Skills', ' ', $textData);
        $textData = str_replace('Objective', ' ', $textData);
        $textData = str_replace('Highly', ' ', $textData);
        $textData = str_replace('Highlights', ' ', $textData);
        $textData = str_replace('vitae', ' ', $textData);
        $textData = str_replace('in meeting its', ' ', $textData);
        $textData = str_replace('Committed', ' ', $textData);
        $textData = str_replace('I would', ' ', $textData);
        $textData = str_replace('Organization Post From', ' ', $textData);
        $textData = str_replace('BIO DATA', ' ', $textData);
        $textData = str_replace('Qualified', ' ', $textData);
        $textData = str_replace('I have', ' ', $textData);
        $textData = str_replace('SPECIAL', ' ', $textData);

        // dd($textData,$textData1);
        // dd($textData);
        // Exclude common header words
        $textData = $this->excludeHeaderWords($textData);

        // ////////////////////////

        // $apiKey = config('services.openai.api_key'); // Ensure the key is set correctly
        // $openAiUrl = 'https://api.openai.com/v1/chat/completions'; // Change to chat completions

        // // Prepare the data structure for the chat model
        // $data = [
        // 	'model' => 'gpt-3.5-turbo', // Specify the model
        // 	'messages' => [
        // 		[
        // 			'role' => 'user',
        // 			'content' => "Extract the relevant information from this resume:\n\n" . $textData,
        // 		],
        // 	],
        // 	'max_tokens' => 1000,
        // 	'temperature' => 0.5,
        // ];

        // $curl = curl_init();

        // curl_setopt_array($curl, [
        // 	CURLOPT_URL => $openAiUrl,
        // 	CURLOPT_RETURNTRANSFER => true,
        // 	CURLOPT_ENCODING => '',
        // 	CURLOPT_MAXREDIRS => 10,
        // 	CURLOPT_TIMEOUT => 30,
        // 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // 	CURLOPT_CUSTOMREQUEST => 'POST',
        // 	CURLOPT_POSTFIELDS => json_encode($data), // Ensure this is correctly set
        // 	CURLOPT_HTTPHEADER => [
        // 		"Content-Type: application/json",
        // 		"Authorization: Bearer $apiKey", // Ensure this line is correctly set
        // 	],
        // 	CURLOPT_VERBOSE => true, // Enable verbose output for debugging
        // ]);

        // Log::info("Sending JSON: " . json_encode($data)); // Log the JSON data

        //     $response = curl_exec($curl);
        //     $err = curl_error($curl);

        //     curl_close($curl);

        //     if ($err) {
        //         return response()->json(['error' => "cURL Error: $err"], 500);
        //     } else {
        //         $parsedResume = json_decode($response, true);
        //         Log::info("OpenAI API Response: ", $parsedResume); // Log the full response
        //         if (isset($parsedResume['error'])) {
        //             return response()->json(['error' => $parsedResume['error']['message']], 500);
        //         }
        //         if (isset($parsedResume['choices'][0]['message']['content'])) {
        //             $resumeContent = $parsedResume['choices'][0]['message']['content'];

        //             // Example of parsing logic (you can refine it as per your needs)
        //             preg_match('/Name:\s*(.*)/', $resumeContent, $matches);
        //             $parsedData['name'] = $matches[1] ?? '';

        //             preg_match('/Total Experience:\s*(.*)/', $resumeContent, $matches);
        //             $parsedData['experience']['years'] = $matches[1] ?? '';

        //             preg_match('/Skills:\s*(.*)/', $resumeContent, $matches);
        //             $parsedData['skills'] = explode(', ', $matches[1] ?? '');

        //             preg_match('/Current Location:\s*(.*)/', $resumeContent, $matches);
        //             $parsedData['contact']['location'] = $matches[1] ?? '';

        // 			preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $resumeContent, $matches);
        //             $parsedData['contact']['email'] = $matches[0] ?? '';

        //             // Parsing phone number (flexible pattern for different phone formats)
        //             preg_match('/\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/', $resumeContent, $matches);
        //             $parsedData['contact']['phone'] = $matches[0] ?? '';

        //             preg_match('/LinkedIn:\s*(.*)/', $resumeContent, $matches);
        //             // Extract the LinkedIn URL if needed, e.g., storing it in contact
        //             // $parsedData['contact']['linkedin'] = $matches[1] ?? '';

        //             // Continue parsing for other fields as necessary...

        // 			return response()->json([
        // 				'name' => $parsedData['name'],
        // 				'number' => $parsedData['contact']['phone'],
        // 				'email' => $parsedData['contact']['email'],
        // 				'city' => '',
        // 				'state' =>'',
        // 				'country' =>'',
        // 				'dob' => '',
        // 				'skills' => array_unique($parsedData['skills']),
        // 				'currentjob' => '',
        // 			]);

        //             // dd($parsedData);
        //             return $parsedResume['choices'][0]['message']['content'];
        //             // return view('resume.result', ['resume' => $parsedResume['choices'][0]['message']['content']]);
        //         } else {
        //             return response()->json(['error' => 'Failed to parse resume.'], 500);
        //         }
        //     }

        // /////////////////////

        // dd($textData);

        // Check for repeated text patterns within the initial first and last 50 words
        $name = $this->checkAdjacentNouns($textData);

        // If name is not found, check for both adjacent text nouns condition within the initial 100 words
        if (! $name) {
            $name = $this->checkRepeatedText($textData);
        }

        // If none of the above conditions identify a name, resort to regular expression matching of common name patterns
        if (! $name) {
            $name = $this->matchWithRegexes($textData);
        }
        $numberRegexes = [
            '/\b\d{10}\b/',                        // 10-digit number
            '/\b\d{3}-\d{3}-\d{4}\b/',            // XXX-XXX-XXXX format
            '/\b\(\d{3}\)\s?\d{3}-\d{4}\b/',      // (XXX) XXX-XXXX format
        ];

        $emailRegex = ['/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/'];
        $number = $this->matchWithRegexesCommon($textData, $numberRegexes);
        $email = $this->matchWithRegexesCommon($textData, $emailRegex);

        $locationSet = $this->locationCountryStateCity($textData);

        $parseDateOfBirth = $this->parseDateOfBirth($textData);
        // dd($parseDateOfBirth->getData()->dob);
        $dob = $parseDateOfBirth->getData()->dob;
        // dd($dob,$parseDateOfBirth);
        // dd($textData);
        $parseCurrentEmployer = $this->parseCurrentEmployer($textData);
        $currentjob = str_replace(':', '', $parseCurrentEmployer->getData()->employer_name);
        // dd($parseCurrentEmployer);

        $careerSummary = $this->extractCareerSummary($textData);

        $jobTitles = [
            'sap sd/wm architect',
            'react.js + typescript',
            'plsql developer',
            'fullstack developer',
            'salesforce functional/business analyst',
            'salesforce techno functional',
            'salesforce technical architect',
            'linux admin',
            'server admin l3',
            'platform administrator – dynatrace, splunk',
            'php developer',
            'dotnet lead',
            'finance audit with ca',
            'technical architect (java+cloud)',
            'senior plsql postgres developer',
            'python & node developer',
            'angular developer',
            'mobile automation tester',
            'dotnet with azure',
            'sql lead',
            'php',
            'laravel',
            'ssis , ssrs & power bi',
            'php developer with laravel',
            'academic content writer',
            'business analyst (bfsi/insurance)',
            'scrum master (bfsi domain/insurance)',
            'fullstack developer',
            'python developer',
            'reactjs developer',
            'java backend developer',
            'technical support engineer',
            'data engineer',
            'data analyst',
            'project manager',
            'business analyst',
            'node.js developer',
            'java fullstack developer',
            'qa engineer',
            'principal engineer',
            'network segmentation engineer',
            'email security engineer',
            'sailpoint developer',
            'sailpoint l3 support & developer',
            'oracle dba with aws',
            'web designer',
            'full stack developer',
            'ui/ux designer',
            'devops engineer',
            'python developer',
            'ibm api connect developer',
            'ibm api connect/azure apim architect',
            'python-django developer',
            'data engineer (aws, python, sql, databricks, snowflake)',
            'business development executive',
            'sap ui5/abap developer',
            'sap abap rap developer',
            'devops engineer with aws and kubernetes',
            'dotnet tcp/ip developer',
            'database engineer',
            'angular developer',
            'data engineer lead with azure',
            'senior data engineer with azure',
            'xamarin developer',
            'node.js developer with sap capm',
            'finance controller',
            'sap abap architect',
            'health connection integration engineer',
            'senior .net developer with azure',
            'mocc consultant (sap basis administrator)',
            'mq administrator',
            'fullstack developer',
            'mern stack developer',
            'automation tester with selenium',
            'react.js + typescript developer',
            'network engineer l2',
            'network engineer l3',
            'qa automation lead',
            'sdet (selenium + python)',
            'sales manager',
            'regional business leader',
            'java developer (tier 1 / tier 2)',
            'automation tester',
            'python developer',
            'jr. salesforce developer',
            'sr. salesforce developer',
            'automation testing engineer',
            'qa lead (automation testing)',
            'senior principal engineer (java)',
            'senior data engineer (snaplogic + azure data bricks or snaplogic + synapse analytics)',
            'php developer with laravel and aws',
            'sap mm consultant',
            'executive assistant (female)',
            'dotnet developer',
            'java developer (tier 1 / tier 2)',
            'aws data engineer',
            'react.js with next.js developer',
            'devops engineer',
            'php developer with codeigniter',
            'java developer',
            'lead sdet (selenium + java/python)',
            'web automation tester',
            'manual tester',
            'automation tester',
            'qa manager (tier 1 / tier 2) (ecommerce or internet domain)',
            'java architect (java + react + aws)',
            'python architect',
            'l3 application security engineer',
            'llm',
            'rpa expert (c#/java, sql / t-sql, api, abby, python, r, huggingface)',
            'rpa developer (automation anywhere, sql, ssis, tensorflow, abby, python/r, java)',
            'program manager',
            'realestate-project sales manager (male candidate)',
            'product manager',
            'technical architect (java/python)',
            'devops lead',
            'database administrator (sql and no sql)',
            'sr. platform manager',
            'lims developer',
            'inside sales executive',
            'field sales executive (tier 1 and tier 2)',
            'data scientist/ai-ml engineer',
            'python & node developer',
            'data science',
            'business analyst (tier 1 and tier 2)',
            'frontend developer (react with typescript) (tier 1 college)',
            'plsql developer',
            'automation tester',
            'backend developer (java/python/node/golang)',
            'backend developer (node/golang)',
            'business analyst',
            'database developer (sql, postgres, no sql)',
            'database developer',
            'flutter developer',
            'frontend developer (react/vue.js)',
            'frontend developer',
            'frontend architect (mobile and web)',
            'senior devops engineer',
            'senior executive- performance marketing',
            'senior executive growth team',
            'seo specialist',
            'social media marketing executive',
            'drupal developer',
            'program manager',
            'project manager - technical',
            'java developer',
            'functional tester',
            'java lead developer',
            'hcl cdp developer',
            'hcl unica api developer',
            'hcl unica campaign developer',
            'hcl unica technical architect',
            'automation tester',
            'academic content writer (for management subject)',
            'senior embedded linux software engineer',
            'hardware engineer',
            'scrum master (it domain)',
            'salesforce developer',
            'fullstack developer',
            'technical lead (php, laravel+vue.js)',
            'senior .net developer',
            'aws data engineer (aws, python, pyspark, sql, snowflake)',
            'adobe campaign developer (adobe campaign standard, javascript, adobe administration)',
            'backend developer (node.js, typescript)',
            'drupal developer (acquia, studio)',
            'assistant operation manager - ar follow up/billing',
            'backend developer (python with django)',
            'frontend developer (angular)',
            'senior tech lead - python lead',
            'senior security analyst-cyber security',
            'data science lead',
            'data science ml',
            'data science cv',
            '.net developer',
            'manager ops',
            'angular developer',
            'ssis, ssrs & power bi',
            'ror developer',
            'lead sdet (selenium + python)',
            'lead ops',
            'executive ops',
            'mern developer',
            'mobile automation testing-appium',
            'python+react developer',
            'saas infrastructure engineer',
            'cisco sme',
            'content writer',
            'senior cloud network engineer',
            '.net technical architect (c#, angular)',
            '.netcore full stack developer',
            'java developer',
            'ad sales senior executive/assistant manager',
            'government sales manager',
            'marketing executive',
            'input desk manager',
            'saas sales',
            'frontend developer (javascript, typescript, react)',
            '.net + angular developer',
            'lead frontend developer (javascript, typescript, react)',
            '.netcore+angular.js/blazor developer',
            'gcp data engineer (apache airflow, big data, teradata, google kubernetes engine)',
            'java developer',
            'java fullstack developer',
            'angular developer',
            'angular developer',
            'java developer',
            'python developer+aws',
            'research engineer-path planning',
            'research engineer-perception',
            'slam developer',
            'senior android developer',
            'automation engineer',
            'compliance and grc analyst',
            'network administrator',
            'windows administrator',
            'database administrator-ms sql',
            '.net developer (asp .net, asp .net core, c#, ado .net)angular',
            'disaster recovery specialist',
            'senior/lead uft automation engineer',
            '.netcore+angular+mvc, microservices developer',
            'dotnet developer',
            '.netcore+angular8 developer',
            'c/c++ developer',
            'dba',
            'frontend developer-react',
            'backend developer-java',
            'qa tester',
            'data science engineer',
            'program manager',
            'ui/ux designer',
            'embedded engineering manager',
            'golang developer',
            'angular fullstack developer',
            'embedded developer',
            '.net developer-webapi, dot net core, entity framework',
            '.netcore+azure developer',
            'senior/lead qa automation engineer (selenium+java)',
            'azure data engineer',
            '.net fullstack developer',
            'product manager',
            'devops lead',
            'operations support lead',
            'devops engineer with oci',
            'net core developer-(financial accountancy & inventory module)',
            'senior software engineer (.netcore+angular/react)',
            'java developer with microservices',
        ];

        $job_metronics_skills = DB::table('job_metronics_skills')->where('job_id', $request->jobId)->pluck('skill_name')->toArray();

        // dd($job_metronics_skills);

        // Initialize an empty array to store matched job titles
        $matchedJobTitles = [];

        if (count($job_metronics_skills) > 0) {
            // Loop through the job titles array to find matches in the text
            foreach ($job_metronics_skills as $jobTitle) {
                if (stripos($textData, $jobTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedJobTitles[] = ucwords($jobTitle);
                }
            }
        } else {
            foreach ($jobTitles as $jobTitle) {
                if (stripos($textData, $jobTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedJobTitles[] = ucwords($jobTitle);
                }
            }
        }
        $matchedqualification = '';
        $qualification_row = DB::table('qualification_master')
            ->select('qualification_master.id', 'qualification_master.name')
            ->pluck('name', 'id')->toArray();
        // dd($qualification_row);
        if ($qualification_row) {
            foreach ($qualification_row as $key => $qualifiactionTitle) {
                if (stripos($textData, $qualifiactionTitle) !== false) {
                    // If a match is found, add it to the matched job titles array
                    $matchedqualification = ucwords($key);
                }
            }

        }
        // dd($matchedqualification);

        $location = '';
        // dd($locationSet['city'] ,$locationSet['state'] , $locationSet['country']);
        if ($locationSet['city'] && $locationSet['state'] && $locationSet['country']) {
            $location = $locationSet['city'].' | '.$locationSet['state'].' | '.$locationSet['country'];

            // $city = $locationSet['city'];
            // $city_row_city= DB::table('city_list')
            // 			   ->join('state_list','state_list.id','=','city_list.state_id')
            // 			   ->join('countries','countries.id','=','state_list.country_id')
            // 			   ->select('city_list.city','countries.country_name','state_list.state')
            // 			   ->where('city_list.city', 'LIKE', "%{$city}%")
            // 			   ->first();
            // 			   if($city_row_city){
            // 				   $location  =  $city_row_city->city. ' | ' . $city_row_city->state . ' | ' . $city_row_city->country_name;
            // 			   }
        }

        // LinkedIn
        preg_match('/(?:https?:\/\/)?(?:www\.)?linkedin\.[a-z.]+\/[^\s]+/i', $textData, $linkedinMatch);
        $linkedin = $linkedinMatch[0] ?? null;

        // GitHub
        preg_match('/(?:https?:\/\/)?(?:www\.)?github\.[a-z.]+\/[^\s]+/i', $textData, $githubMatch);
        $github = $githubMatch[0] ?? null;

        // Behance
        preg_match('/(?:https?:\/\/)?(?:www\.)?behance\.[a-z.]+\/[^\s]+/i', $textData, $behanceMatch);
        $behance = $behanceMatch[0] ?? null;

        // dd($locationSet['city']);
        // dd($matchedJobTitles);
        // If validation passes, return the parsed data
        return response()->json([
            'name' => str_replace('of', '', $name),
            'number' => $number,
            'email' => $email,
            'city' => ($locationSet['city']) ? $locationSet['city'] : '',
            'state' => ($locationSet['state']) ? $locationSet['state'] : '',
            'country' => ($location) ? $location : '',
            'dob' => ($dob) ? $dob : '',
            'skills' => array_unique($matchedJobTitles),
            'qualification' => ($matchedqualification),
            'currentjob' => ($currentjob) ? $currentjob : '',
            'linkedin' => $linkedin,
            'github' => $github,
            'behance' => $behance,
            'careerSummary' => $careerSummary,
        ]);

    }

    private function extractCareerSummary($text)
    {

        $headings = [
            'Career Objective', 'Career Summary', 'Professional Summary', 'Objective',
            'Profile Summary', 'Executive Summary', 'Summary', 'About Me', 'Profile',
            'Career Highlights', 'Overview', 'Personal Statement', 'Introduction',
        ];

        $pattern = '/(?:'.implode('|', array_map('preg_quote', $headings)).')\s*[:|\-]?\s*(.*?)(?=\n\s*\n|\n\s*[A-Z][A-Za-z\s]+[:|\-]|\z)/is';

        if (preg_match($pattern, $text, $match)) {
            $summary = trim(preg_replace('/\s+/', ' ', $match[1]));

            if (str_word_count($summary) > 100) {
                $words = preg_split('/\s+/', $summary);
                $summary = implode(' ', array_slice($words, 0, 100));
            }

            return $summary;
        }

        return null;

    }

    private function matchWithRegexesCommon($text, $regexes)
    {
        foreach ($regexes as $regex) {
            preg_match($regex, $text, $matches);
            if (! empty($matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    private function excludeHeaderWords($text)
    {
        // Common header words to exclude
        $headerWords = ['Resume', 'CV', 'Curriculum Vitae', 'CURRICULUM - VITAE', 'CURRICULUM VITAE', 'Curriculum-vitae'];

        // Remove common header words from the text
        foreach ($headerWords as $word) {
            $text = preg_replace("/\b$word\b/i", '', $text);
        }

        return $text;
    }

    private function checkRepeatedText($text)
    {
        // Get the first and last 50 words
        $words = str_word_count($text, 1);
        $first50Words = implode(' ', array_slice($words, 0, 50));
        $last50Words = implode(' ', array_slice($words, -50));

        // Filter out numbers and email addresses from the first and last 50 words
        $filteredFirst50Words = preg_replace('/\b\d+\b/', '', $first50Words);
        $filteredFirst50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredFirst50Words);

        $filteredLast50Words = preg_replace('/\b\d+\b/', '', $last50Words);
        $filteredLast50Words = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '', $filteredLast50Words);

        // Check for repeated text in the filtered first and last 50 words
        preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredFirst50Words, $firstMatch);
        preg_match('/\b([A-Za-z]+)\b.*\b\1\b/', $filteredLast50Words, $lastMatch);

        // If repeated text is found in both ranges, consider them as a potential name
        if (! empty($firstMatch) && ! empty($lastMatch)) {
            return $firstMatch[1];
        }

        return null;
    }

    private function checkAdjacentNouns($text)
    {

        // Get the first 100 words
        $first100Words = implode(' ', array_slice(str_word_count($text, 1), 0, 100));

        // Identify nouns within the first 100 words
        preg_match_all('/\b[A-Za-z]+\b/i', $first100Words, $nounMatches);

        // If two adjacent nouns are found within the first 100 words, consider them as a potential name
        if (! empty($nounMatches[0])) {
            $nouns = $nounMatches[0];

            for ($i = 0; $i < count($nouns) - 1; $i++) {
                if ($i + 1 < count($nouns) && $nouns[$i + 1] != '') {
                    $potentialName = $nouns[$i].' '.$nouns[$i + 1];
                    if (! $this->isCommonNoun($potentialName)) {
                        if (! $this->isCommonNoun($potentialName)) {
                            return $nouns[$i].' '.$nouns[$i + 1];
                        }
                    }

                }
            }
        }

        return null;
    }

    private function matchWithRegexes($text)
    {
        // Define regular expressions for different data types
        $regexes = [
            '/\b[A-Z][a-z]+ [A-Z][a-z]+\b/',       // First and last name with capital initials
            '/\b[A-Z][a-z]+\b/',                   // Single capitalized name
            '/\b[a-zA-Z]+ [a-zA-Z]+\b/',           // First and last name with any case
            '/\b[A-Z][a-z]+ [A-Z]\. [A-Z][a-z]+\b/', // First name, middle initial, last name
            '/\b[A-Z]\. [A-Z]\. [A-Z][a-z]+\b/',   // Initials format (e.g., J. K. Rowling)
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ [JS]r\.?\b/', // Name with Jr./Sr. suffix
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ III\b/',   // Name with Roman numeral suffix
            '/\b[a-zA-Z]+-[a-zA-Z]+\b/',           // Hyphenated last name (e.g., Maria Rodriguez-Garcia)
            '/\b[A-Z][a-z]+ [A-Z][a-z]+ [A-Z][a-z]+\b/', // Compound last names (e.g., Jean-Luc Picard)
            '/\b(?:Dr\.|Mr\.|Ms\.|Mrs\.) [A-Z][a-z]+\b/', // Names with prefixes (e.g., Dr. John Smith)
        ];

        // Match data using regular expressions
        foreach ($regexes as $regex) {
            preg_match($regex, $text, $matches);
            if (! empty($matches)) {
                return $matches[0];
            }
        }

        return null;
    }

    private function isCommonNoun($word)
    {
        // List of common nouns to exclude
        $commonNouns = ['mobile', 'email', 'address', 'phone', 'fax'];

        // Check if the word is a common noun
        return in_array(strtolower($word), $commonNouns);
    }

    private function locationCountryStateCity($text)
    {
        // Get the text data from the request

        // Define the list of Indian cities with their respective states and countries
        $indianCities = [
            'Mumbai' => ['Maharashtra', 'India'],
            'Delhi' => ['Delhi', 'India'],
            'Bangalore' => ['Karnataka', 'India'],
            'Hyderabad' => ['Telangana', 'India'],
            'Ahmedabad' => ['Gujarat', 'India'],
            'Chennai' => ['Tamil Nadu', 'India'],
            'Kolkata' => ['West Bengal', 'India'],
            'Surat' => ['Gujarat', 'India'],
            'Pune' => ['Maharashtra', 'India'],
            'Jaipur' => ['Rajasthan', 'India'],
            'Lucknow' => ['Uttar Pradesh', 'India'],
            'Kanpur' => ['Uttar Pradesh', 'India'],
            'Nagpur' => ['Maharashtra', 'India'],
            'Patna' => ['Bihar', 'India'],
            'Indore' => ['Madhya Pradesh', 'India'],
            'Thane' => ['Maharashtra', 'India'],
            'Bhopal' => ['Madhya Pradesh', 'India'],
            'Visakhapatnam' => ['Andhra Pradesh', 'India'],
            'Vadodara' => ['Gujarat', 'India'],
            'Firozabad' => ['Uttar Pradesh', 'India'],
            'Ludhiana' => ['Punjab', 'India'],
            'Rajkot' => ['Gujarat', 'India'],
            'Agra' => ['Uttar Pradesh', 'India'],
            'Siliguri' => ['West Bengal', 'India'],
            'Nashik' => ['Maharashtra', 'India'],
            'Faridabad' => ['Haryana', 'India'],
            'Patiala' => ['Punjab', 'India'],
            'Meerut' => ['Uttar Pradesh', 'India'],
            'Kalyan-Dombivli' => ['Maharashtra', 'India'],
            'Vasai-Virar' => ['Maharashtra', 'India'],
            'Varanasi' => ['Uttar Pradesh', 'India'],
            'Srinagar' => ['Jammu and Kashmir', 'India'],
            'Dhanbad' => ['Jharkhand', 'India'],
            'Jodhpur' => ['Rajasthan', 'India'],
            'Amritsar' => ['Punjab', 'India'],
            'Raipur' => ['Chhattisgarh', 'India'],
            'Allahabad' => ['Uttar Pradesh', 'India'],
            'Coimbatore' => ['Tamil Nadu', 'India'],
            'Jabalpur' => ['Madhya Pradesh', 'India'],
            'Gwalior' => ['Madhya Pradesh', 'India'],
            'Vijayawada' => ['Andhra Pradesh', 'India'],
            'Madurai' => ['Tamil Nadu', 'India'],
            'Guwahati' => ['Assam', 'India'],
            'Chandigarh' => ['Chandigarh', 'India'],
            'Hubli-Dharwad' => ['Karnataka', 'India'],
            'Amroha' => ['Uttar Pradesh', 'India'],
            'Moradabad' => ['Uttar Pradesh', 'India'],
            'Gurgaon' => ['Haryana', 'India'],
            'Aligarh' => ['Uttar Pradesh', 'India'],
            'Solapur' => ['Maharashtra', 'India'],
            'Ranchi' => ['Jharkhand', 'India'],
            'Jalandhar' => ['Punjab', 'India'],
            'Tiruchirappalli' => ['Tamil Nadu', 'India'],
            'Bhubaneswar' => ['Odisha', 'India'],
            'Salem' => ['Tamil Nadu', 'India'],
            'Warangal' => ['Telangana', 'India'],
            'Mira-Bhayandar' => ['Maharashtra', 'India'],
            'Thiruvananthapuram' => ['Kerala', 'India'],
            'Bhiwandi' => ['Maharashtra', 'India'],
            'Saharanpur' => ['Uttar Pradesh', 'India'],
            'Guntur' => ['Andhra Pradesh', 'India'],
            'Amravati' => ['Maharashtra', 'India'],
            'Bikaner' => ['Rajasthan', 'India'],
            'Noida' => ['Uttar Pradesh', 'India'],
            'Jamshedpur' => ['Jharkhand', 'India'],
            'Bhilai Nagar' => ['Chhattisgarh', 'India'],
            'Cuttack' => ['Odisha', 'India'],
            'Kochi' => ['Kerala', 'India'],
            'Udaipur' => ['Rajasthan', 'India'],
            'Bhavnagar' => ['Gujarat', 'India'],
            'Dehradun' => ['Uttarakhand', 'India'],
            'Asansol' => ['West Bengal', 'India'],
            'Nanded' => ['Maharashtra', 'India'],
            'Ajmer' => ['Rajasthan', 'India'],
            'Jamnagar' => ['Gujarat', 'India'],
            'Ujjain' => ['Madhya Pradesh', 'India'],
            'Sangli-Miraj & Kupwad' => ['Maharashtra', 'India'],
            'Loni' => ['Uttar Pradesh', 'India'],
            'Jhansi' => ['Uttar Pradesh', 'India'],
            'Pondicherry' => ['Puducherry', 'India'],
            'Nellore' => ['Andhra Pradesh', 'India'],
            'Jammu' => ['Jammu and Kashmir', 'India'],
            'Belagavi' => ['Karnataka', 'India'],
            'Raurkela' => ['Odisha', 'India'],
            'Mangaluru' => ['Karnataka', 'India'],
            'Tirunelveli' => ['Tamil Nadu', 'India'],
            'Malegaon' => ['Maharashtra', 'India'],
            'Gaya' => ['Bihar', 'India'],
            'Tiruppur' => ['Tamil Nadu', 'India'],
            'Davanagere' => ['Karnataka', 'India'],
            'Kozhikode' => ['Kerala', 'India'],
            'Akola' => ['Maharashtra', 'India'],
            'Kurnool' => ['Andhra Pradesh', 'India'],
            'Bokaro Steel City' => ['Jharkhand', 'India'],
            'Rajahmundry' => ['Andhra Pradesh', 'India'],
            'Ballari' => ['Karnataka', 'India'],
            'Agartala' => ['Tripura', 'India'],
            'Bhagalpur' => ['Bihar', 'India'],
            'Latur' => ['Maharashtra', 'India'],
            'Dhule' => ['Maharashtra', 'India'],
            'Korba' => ['Chhattisgarh', 'India'],
            'Bhilwara' => ['Rajasthan', 'India'],
            'Berhampur' => ['Odisha', 'India'],
            'Muzaffarpur' => ['Bihar', 'India'],
            'Ahmednagar' => ['Maharashtra', 'India'],
            'Mathura' => ['Uttar Pradesh', 'India'],
            'Kollam' => ['Kerala', 'India'],
            'Avadi' => ['Tamil Nadu', 'India'],
            'Kadapa' => ['Andhra Pradesh', 'India'],
            'Anantapur' => ['Andhra Pradesh', 'India'],
            'Kamarhati' => ['West Bengal', 'India'],
            'Bilaspur' => ['Chhattisgarh', 'India'],
            'Shahjahanpur' => ['Uttar Pradesh', 'India'],
            'Satara' => ['Maharashtra', 'India'],
            'Bijapur' => ['Karnataka', 'India'],
            'Rampur' => ['Uttar Pradesh', 'India'],
            'Shivamogga' => ['Karnataka', 'India'],
            'Chandrapur' => ['Maharashtra', 'India'],
            'Junagadh' => ['Gujarat', 'India'],
            'Thrissur' => ['Kerala', 'India'],
            'Alwar' => ['Rajasthan', 'India'],
            'Bardhaman' => ['West Bengal', 'India'],
            'Kulti' => ['West Bengal', 'India'],
            'Nizamabad' => ['Telangana', 'India'],
            'Parbhani' => ['Maharashtra', 'India'],
            'Tumkur' => ['Karnataka', 'India'],
            'Khammam' => ['Telangana', 'India'],
            'Ozhukarai' => ['Puducherry', 'India'],
            'Bihar Sharif' => ['Bihar', 'India'],
            'Panipat' => ['Haryana', 'India'],
            'Darbhanga' => ['Bihar', 'India'],
            'Bally' => ['West Bengal', 'India'],
            'Aizawl' => ['Mizoram', 'India'],
            'Dewas' => ['Madhya Pradesh', 'India'],
            'Karnal' => ['Haryana', 'India'],
            'Bathinda' => ['Punjab', 'India'],
            'Jalna' => ['Maharashtra', 'India'],
            'Eluru' => ['Andhra Pradesh', 'India'],
            'Barasat' => ['West Bengal', 'India'],
            'Kirari Suleman Nagar' => ['Delhi', 'India'],
            'Purnia' => ['Bihar', 'India'],
            'Satna' => ['Madhya Pradesh', 'India'],
            'Mau' => ['Uttar Pradesh', 'India'],
            'Sonipat' => ['Haryana', 'India'],
            'Farrukhabad' => ['Uttar Pradesh', 'India'],
            'Sagar' => ['Madhya Pradesh', 'India'],
            'Rourkela' => ['Odisha', 'India'],
            'Durg' => ['Chhattisgarh', 'India'],
            'Imphal' => ['Manipur', 'India'],
            'Ratlam' => ['Madhya Pradesh', 'India'],
            'Hapur' => ['Uttar Pradesh', 'India'],
            'Arrah' => ['Bihar', 'India'],
            'Karimnagar' => ['Telangana', 'India'],
            'Etawah' => ['Uttar Pradesh', 'India'],
            'Ambernath' => ['Maharashtra', 'India'],
            'North Dumdum' => ['West Bengal', 'India'],
            'Bharatpur' => ['Rajasthan', 'India'],
            'Begusarai' => ['Bihar', 'India'],
            'New Delhi' => ['Delhi', 'India'],
            'Gandhidham' => ['Gujarat', 'India'],
            'Baranagar' => ['West Bengal', 'India'],
            'Tiruvottiyur' => ['Tamil Nadu', 'India'],
            'Pondicherry' => ['Puducherry', 'India'],
            'Sikar' => ['Rajasthan', 'India'],
            'Thoothukudi' => ['Tamil Nadu', 'India'],
            'Rewa' => ['Madhya Pradesh', 'India'],
            'Mirzapur' => ['Uttar Pradesh', 'India'],
            'Raichur' => ['Karnataka', 'India'],
            'Pali' => ['Rajasthan', 'India'],
            'Ramagundam' => ['Telangana', 'India'],
            'Silchar' => ['Assam', 'India'],
            'Haridwar' => ['Uttarakhand', 'India'],
            'Vijayanagaram' => ['Andhra Pradesh', 'India'],
            'Tenali' => ['Andhra Pradesh', 'India'],
            'Nagercoil' => ['Tamil Nadu', 'India'],
            'Sri Ganganagar' => ['Rajasthan', 'India'],
            'Karawal Nagar' => ['Delhi', 'India'],
            'Mango' => ['Jharkhand', 'India'],
            'Thanjavur' => ['Tamil Nadu', 'India'],
            'Bulandshahr' => ['Uttar Pradesh', 'India'],
            'Uluberia' => ['West Bengal', 'India'],
            'Katni' => ['Madhya Pradesh', 'India'],
            'Sambalpur' => ['Odisha', 'India'],
            'Singrauli' => ['Madhya Pradesh', 'India'],
            'Nadiad' => ['Gujarat', 'India'],
            'Secunderabad' => ['Telangana', 'India'],
            'Naihati' => ['West Bengal', 'India'],
            'Yamunanagar' => ['Haryana', 'India'],
            'Bidar' => ['Karnataka', 'India'],
            'Pallavaram' => ['Tamil Nadu', 'India'],
            'Bidhan Nagar' => ['West Bengal', 'India'],
            'Munger' => ['Bihar', 'India'],
            'Panchkula' => ['Haryana', 'India'],
            'Burhanpur' => ['Madhya Pradesh', 'India'],
            'Raurkela Industrial Township' => ['Odisha', 'India'],
            'Kharagpur' => ['West Bengal', 'India'],
            'Dindigul' => ['Tamil Nadu', 'India'],
            'Gandhinagar' => ['Gujarat', 'India'],
            'Hospet' => ['Karnataka', 'India'],
            'Nangloi Jat' => ['Delhi', 'India'],
            'Malda' => ['West Bengal', 'India'],
            'Ongole' => ['Andhra Pradesh', 'India'],
            'Deoghar' => ['Jharkhand', 'India'],
            'Chapra' => ['Bihar', 'India'],
            'Haldia' => ['West Bengal', 'India'],
            'Khandwa' => ['Madhya Pradesh', 'India'],
            'Nandyal' => ['Andhra Pradesh', 'India'],
            'Morena' => ['Madhya Pradesh', 'India'],
            'Bhind' => ['Madhya Pradesh', 'India'],
            'Bhalswa Jahangir Pur' => ['Delhi', 'India'],
            'Madhyamgram' => ['West Bengal', 'India'],
            'Bhiwani' => ['Haryana', 'India'],
            'Berhampore' => ['West Bengal', 'India'],
            'Ambala' => ['Haryana', 'India'],
            'Morbi' => ['Gujarat', 'India'],
            'Fatehpur' => ['Uttar Pradesh', 'India'],
            'Bijnor' => ['Uttar Pradesh', 'India'],
            'Raebareli' => ['Uttar Pradesh', 'India'],
            'Khora' => ['Uttar Pradesh', 'India'],
            'Bhusawal' => ['Maharashtra', 'India'],
            'Orai' => ['Uttar Pradesh', 'India'],
            'Bahraich' => ['Uttar Pradesh', 'India'],
            'Phusro' => ['Jharkhand', 'India'],
            'Vellore' => ['Tamil Nadu', 'India'],
            'Mahesana' => ['Gujarat', 'India'],
            'Sambhal' => ['Uttar Pradesh', 'India'],
            'Raiganj' => ['West Bengal', 'India'],
            'Sirsa' => ['Haryana', 'India'],
            'Danapur' => ['Bihar', 'India'],
            'Serampore' => ['West Bengal', 'India'],
            'Sultan Pur Majra' => ['Delhi', 'India'],
            'Guna' => ['Madhya Pradesh', 'India'],
            'Jaunpur' => ['Uttar Pradesh', 'India'],
            'Panvel' => ['Maharashtra', 'India'],
            'Shivpuri' => ['Madhya Pradesh', 'India'],
            'Surendranagar Dudhrej' => ['Gujarat', 'India'],
            'Unnao' => ['Uttar Pradesh', 'India'],
            'Chinsurah' => ['West Bengal', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Unnao' => ['Uttar Pradesh', 'India'],
            'Chinsurah' => ['West Bengal', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Godhra' => ['Gujarat', 'India'],
            'Porbandar' => ['Gujarat', 'India'],
            'Palakkad' => ['Kerala', 'India'],
            'Pudukkottai' => ['Tamil Nadu', 'India'],
            'Hosur' => ['Tamil Nadu', 'India'],
            'Faridkot' => ['Punjab', 'India'],
            'Alappuzha' => ['Kerala', 'India'],
            'Kottayam' => ['Kerala', 'India'],
            'Machilipatnam' => ['Andhra Pradesh', 'India'],
            'Shimla' => ['Himachal Pradesh', 'India'],
            'Adoni' => ['Andhra Pradesh', 'India'],
            'Udupi' => ['Karnataka', 'India'],
            'Katihar' => ['Bihar', 'India'],
            'Proddatur' => ['Andhra Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Saharsa' => ['Bihar', 'India'],
            'Dibrugarh' => ['Assam', 'India'],
            'Jorhat' => ['Assam', 'India'],
            'Hazaribagh' => ['Jharkhand', 'India'],
            'Hindupur' => ['Andhra Pradesh', 'India'],
            'Nagaon' => ['Assam', 'India'],
            'Sasaram' => ['Bihar', 'India'],
            'Hajipur' => ['Bihar', 'India'],
            'Bongaigaon' => ['Assam', 'India'],
            'North Barrackpur' => ['West Bengal', 'India'],
            'Karaikudi' => ['Tamil Nadu', 'India'],
            'Hooghly' => ['West Bengal', 'India'],
            'Bhuj' => ['Gujarat', 'India'],
            'Tiruvannamalai' => ['Tamil Nadu', 'India'],
            'Kumbakonam' => ['Tamil Nadu', 'India'],
            'Modinagar' => ['Uttar Pradesh', 'India'],
            'Mahbubnagar' => ['Telangana', 'India'],
            'Godhra' => ['Gujarat', 'India'],
            'Porbandar' => ['Gujarat', 'India'],
            'Palakkad' => ['Kerala', 'India'],
            'Anand' => ['Gujarat', 'India'],
            'Pudukkottai' => ['Tamil Nadu', 'India'],
            'Hosur' => ['Tamil Nadu', 'India'],
            'Faridkot' => ['Punjab', 'India'],
        ];

        // Initialize an empty array to store matching cities
        $matchingCities = ['city' => '', 'state' => '', 'country' => ''];

        // Loop through the Indian cities list and check for matches in the text
        foreach ($indianCities as $city => $details) {
            // Check if the city name appears in the text
            if (stripos($text, $city) !== false) {
                // If found, add the city along with its state and country to the result array
                $matchingCities = [
                    'city' => $city,
                    'state' => $details[0],
                    'country' => $details[1],
                ];
            }
        }

        // Return the matching cities
        return $matchingCities;
    }

    private function parseDateOfBirth($text)
    {
        $textData = $text;

        // Define common date formats
        $indianDateFormats = [
            'd/m/Y', 'd-m-Y', 'd.m.Y', 'd-m-y', 'd.m.y', 'd/m/y',
        ];
        $westernDateFormats = [
            'm/d/Y', 'm-d-Y', 'm.d.Y', 'm-d-y', 'm.d.y', 'm/d/y',
        ];

        // Formats with text-based months
        $textMonthFormats = [
            'F j, Y',  // August 31, 1990
            'M j, Y',  // Aug 31, 1990
            'F j Y',   // August 31 1990 (without commas)
            'M j Y',    // Aug 31 1990 (without commas)
        ];
        //    dd("dskjdkghkfdhgk");
        $dateFormats = array_merge($indianDateFormats, $westernDateFormats, $textMonthFormats);

        // Reverse document lines
        $lines = array_reverse(preg_split("/\r\n|\n|\r/", $textData));

        foreach ($lines as $line) {
            // Clean the line: remove null bytes & control characters
            $line = preg_replace('/[\x00-\x1F\x7F]/u', '', $line);
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Remove unwanted text and markers
            $line = str_replace(
                ['DOB:', 'th', 'rd', 'st', 'nd', ':', ' : ', 'Date of birth', 'Date Of Birth', 'date of birth', 'DOB', ' '],
                '',
                $line
            );

            // Skip if empty after cleanup
            if (empty($line)) {
                continue;
            }

            foreach ($dateFormats as $format) {
                // Attempt to parse the date
                $date = @date_create_from_format($format, $line);

                // Skip invalid parses
                if ($date === false) {
                    continue;
                }

                // Create the range dates
                $tenYearsAgo = date_sub(date_create(), date_interval_create_from_date_string('10 years'));
                $hundredFiftyYearsAgo = date_sub(date_create(), date_interval_create_from_date_string('150 years'));

                // Validate date is realistic and within range
                if ($date <= $tenYearsAgo &&
                    $date >= $hundredFiftyYearsAgo &&
                    checkdate($date->format('m'), $date->format('d'), $date->format('Y'))) {

                    return response()->json([
                        'dob' => $date->format('Y-m-d'),
                    ]);
                }
            }
        }

        // Return null if no valid date is found
        return response()->json([
            'dob' => null,
        ]);
    }

    public function parseCurrentEmployer($text)
    {
        // Resume text extracted from the request
        $resumeText = $text;

        // List of formats indicating current employment status
        $currentEmploymentFormats = [
            // Existing formats
            'Actively Employed',
            'Currently Employed',
            'Currently Engaged As',
            'Currently Engaged With',
            'Currently Occupied As',
            'Currently Occupied With',
            'Currently Holding Position Of',
            'Currently Holding Role Of',
            'Currently in Active Employment',
            'Currently in Present Job',
            'Currently in Present Role',
            'Currently in Present Employment',
            'Currently in Current Position',
            'Currently in Present Position',
            'Currently on the Job',
            'Currently on the Position',
            'Currently on the Role',
            'Currently on Employment',
            'Currently in Service',
            'Currently in Work',
            'Currently Serving As',
            'Currently Serving With',
            'Currently Working As',
            'Currently Working With',
            'Employed As',
            'Employed At',
            'Employed In',
            'Employed With',
            'Engaged in Employment',
            'Engaged in Job',
            'Engaged in Position',
            'Engaged in Role',
            'Holding Position Of',
            'Holding Role Of',
            'In Active Employment',
            'In Current Position',
            'In Current Role',
            'In Current Employment',
            'In Current Job',
            'In Current Position',
            'In Present Employment',
            'In Present Job',
            'In Present Position',
            'In Present Role',
            'Job Status: Employed',
            'Occupation: Employed',
            'Presently Employed At',
            'Presently Employed With',
            'Presently Engaged As',
            'Presently Engaged With',
            'Presently Occupied As',
            'Presently Occupied With',
            'Presently Holding Position Of',
            'Presently Holding Role Of',
            'Presently in Active Employment',
            'Presently in Current Position',
            'Presently in Current Role',
            'Presently in Current Employment',
            'Presently in Current Job',
            'Presently in Present Position',
            'Presently in Present Role',
            'Presently on the Job',
            'Presently on the Position',
            'Presently on the Role',
            'Presently on Employment',
            'Presently in Service',
            'Presently in Work',
            'Presently Serving As',
            'Presently Serving With',
            'Presently Working As',
            'Presently Working With',
            'Still Holding Position Of',
            'Still Holding Role Of',
            'Still in Active Employment',
            'Still in Current Position',
            'Still in Current Role',
            'Still in Current Employment',
            'Still in Current Job',
            'Still in Present Employment',
            'Still in Present Job',
            'Still in Present Position',
            'Still in Present Role',
            'Still on the Job',
            'Still on the Position',
            'Still on the Role',
            'Still on Employment',
            'Still in Service',
            'Still in Work',
            'Still Serving As',
            'Still Serving With',
            'Still Working As',
            'Still Working With',
            'Actively Working at',
            'Current Employer',
        ];

        // List of additional formats indicating experience
        $experienceKeywords = [
            // Existing keywords
            'Worked at', 'Previous Employer', 'Company', 'Employment History',
            'Career', 'Professional Background', 'Previous Work Experience', 'Work History',
            'Employment Record', 'Job History', 'Work Experience', 'Experience with',
            'Employer', 'Prior Work', 'Former Employer', 'Company Name', 'Previous Job',
            'Worked As', 'Employment Details',
            'Work Experience', 'Professional Experience', 'Previous Experience',
            'Experience with', 'Professional Experience Summary', 'Job Experience', 'Previous Positions',
            'Job Roles', 'Roles and Responsibilities', 'Current Role', 'Current Job',
            'Current Employment', 'Present Role', 'Present Job', 'Current Position',
            'Present Employment', 'Job Profile', 'Current Company', 'Present Company', 'Experience', 'Experience in',
        ];

        // Common designation keywords to exclude from employer name
        $designationKeywords = [
            // Existing keywords
            'CEO', 'CTO', 'CFO', 'COO', 'President', 'Vice President', 'Director', 'Manager', 'Supervisor', 'Lead',
            'Administrator', 'Coordinator', 'Consultant', 'Analyst', 'Engineer', 'Developer', 'Designer', 'Specialist',
            'Officer', 'Executive', 'Assistant', 'Representative', 'Associate', 'Advisor', 'Agent', 'Technician',
            'Operator', 'Accountant', 'Auditor', 'Counselor', 'Educator', 'Trainer', 'Coach', 'Therapist', 'Physician',
            'Nurse', 'Scientist', 'Researcher', 'Writer', 'Editor', 'Designer', 'Artist', 'Photographer', 'Architect',
            'Lawyer', 'Attorney', 'Advocate', 'Paralegal', 'Secretary', 'Receptionist', 'Clerk', 'Assistant', 'Aide',
            'Coordinator', 'Assistant', 'Intern', 'Trainee', 'Volunteer', 'Freelancer', 'Contractor',
        ];

        // Additional conditions and patterns
        $additionalConditions = [
            // Additional conditions for better results
            // For example:
            'Actively Working at', 'Currently Holding Position as', 'Currently Engaged with Company',
            'Currently Employed as',
        ];

        // Combine all conditions and keywords
        $allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

        // dd($allConditions);

        // Check if any of the formats indicate current employment status
        foreach ($allConditions as $condition) {
            // Check for the condition in the resume text
            if (stripos($resumeText, $condition) !== false) {
                // If a match is found, extract the employer name
                // dd($resumeText, $condition, $designationKeywords);
                $employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);

                // Return the condition and employer name
                return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
            }
        }

        // If no match is found, return null
        return response()->json(['current_employer_format' => null, 'employer_name' => null]);
    }

    // Helper function to extract the employer name
    private function extractEmployerName($resumeText, $keyword, $designationKeywords)
    {
        // Find the position of the keyword in the text
        $keywordIndex = stripos($resumeText, $keyword);
        // Extract text after the keyword
        $textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
        // Split text into individual words
        // trim(preg_replace('/\s+/', ' ', $string));
        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $textAfterKeyword)));
        // Limit words to 20
        $limitedWords = array_slice($words, 0, 20);
        $nounCount = 0;
        $employerName = '';
        // Iterate through words to find the first noun
        foreach ($limitedWords as $word) {
            if ($this->isNoun($word) && ! $this->isDesignation($word, $designationKeywords)) {
                // Concatenate the noun to employerName
                $employerName .= ($employerName ? ' ' : '').$word;
                $nounCount++;
                // Break the loop if maximum adjacent nouns reached
                if ($nounCount >= 3) {
                    break;
                }
            } elseif ($nounCount > 0) {
                // Break the loop if a non-noun is encountered after finding at least one noun
                break;
            }
        }

        return $employerName ?: null; // If no noun is found
    }

    // Subfunction to determine if a word is a noun
    private function isNoun($word)
    {
        // Check if word is not empty
        return ! empty($word);
    }

    // Subfunction to determine if a word is a common designation keyword
    private function isDesignation($word, $designationKeywords)
    {
        // Check if word exists in the list of designation keywords
        return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
    }

    // Main function to parse the current employer from resume text
    private function parseCurrentEmployerw($text)
    {
        // Resume text extracted from the request
        $resumeText = $text;

        // List of formats indicating current employment status
        $currentEmploymentFormats = [
            // Existing formats
            'Actively Employed',
            'Currently Employed',
            'Currently Engaged As',
            'Currently Engaged With',
            'Currently Occupied As',
            'Currently Occupied With',
            'Currently Holding Position Of',
            'Currently Holding Role Of',
            'Currently in Active Employment',
            'Currently in Present Job',
            'Currently in Present Role',
            'Currently in Present Employment',
            'Currently in Current Position',
            'Currently in Present Position',
            'Currently on the Job',
            'Currently on the Position',
            'Currently on the Role',
            'Currently on Employment',
            'Currently in Service',
            'Currently in Work',
            'Currently Serving As',
            'Currently Serving With',
            'Currently Working As',
            'Currently Working With',
            'Employed As',
            'Employed At',
            'Employed In',
            'Employed With',
            'Engaged in Employment',
            'Engaged in Job',
            'Engaged in Position',
            'Engaged in Role',
            'Holding Position Of',
            'Holding Role Of',
            'In Active Employment',
            'In Current Position',
            'In Current Role',
            'In Current Employment',
            'In Current Job',
            'In Current Position',
            'In Present Employment',
            'In Present Job',
            'In Present Position',
            'In Present Role',
            'Job Status: Employed',
            'Occupation: Employed',
            'Presently Employed At',
            'Presently Employed With',
            'Presently Engaged As',
            'Presently Engaged With',
            'Presently Occupied As',
            'Presently Occupied With',
            'Presently Holding Position Of',
            'Presently Holding Role Of',
            'Presently in Active Employment',
            'Presently in Current Position',
            'Presently in Current Role',
            'Presently in Current Employment',
            'Presently in Current Job',
            'Presently in Present Position',
            'Presently in Present Role',
            'Presently on the Job',
            'Presently on the Position',
            'Presently on the Role',
            'Presently on Employment',
            'Presently in Service',
            'Presently in Work',
            'Presently Serving As',
            'Presently Serving With',
            'Presently Working As',
            'Presently Working With',
            'Still Holding Position Of',
            'Still Holding Role Of',
            'Still in Active Employment',
            'Still in Current Position',
            'Still in Current Role',
            'Still in Current Employment',
            'Still in Current Job',
            'Still in Present Employment',
            'Still in Present Job',
            'Still in Present Position',
            'Still in Present Role',
            'Still on the Job',
            'Still on the Position',
            'Still on the Role',
            'Still on Employment',
            'Still in Service',
            'Still in Work',
            'Still Serving As',
            'Still Serving With',
            'Still Working As',
            'Still Working With',
        ];

        // List of additional formats indicating experience
        $experienceKeywords = [
            // Existing keywords
            'Experience:', 'Worked at', 'Previous Employer:', 'Company:', 'Employment History:',
            'Career:', 'Professional Background:', 'Previous Work Experience:', 'Work History:',
            'Employment Record:', 'Job History:', 'Work Experience:', 'Experience with',
            'Employer:', 'Prior Work:', 'Former Employer:', 'Company Name:', 'Previous Job:',
            'Worked As:', 'Employment Details:',
            'Work Experience:', 'Professional Experience:', 'Previous Experience:', 'Experience in',
            'Experience with', 'Professional Experience Summary:', 'Job Experience:', 'Previous Positions:',
            'Job Roles:', 'Roles and Responsibilities:', 'Current Role:', 'Current Job:',
            'Current Employment:', 'Present Role:', 'Present Job:', 'Current Position:',
            'Present Employment:', 'Job Profile:', 'Current Company:', 'Present Company:',
        ];

        // Common designation keywords to exclude from employer name
        $designationKeywords = [
            // Existing keywords
            'CEO', 'CTO', 'CFO', 'COO', 'President', 'Vice President', 'Director', 'Manager', 'Supervisor', 'Lead',
            'Administrator', 'Coordinator', 'Consultant', 'Analyst', 'Engineer', 'Developer', 'Designer', 'Specialist',
            'Officer', 'Executive', 'Assistant', 'Representative', 'Associate', 'Advisor', 'Agent', 'Technician',
            'Operator', 'Accountant', 'Auditor', 'Counselor', 'Educator', 'Trainer', 'Coach', 'Therapist', 'Physician',
            'Nurse', 'Scientist', 'Researcher', 'Writer', 'Editor', 'Designer', 'Artist', 'Photographer', 'Architect',
            'Lawyer', 'Attorney', 'Advocate', 'Paralegal', 'Secretary', 'Receptionist', 'Clerk', 'Assistant', 'Aide',
            'Coordinator', 'Assistant', 'Intern', 'Trainee', 'Volunteer', 'Freelancer', 'Contractor',
        ];

        // Additional conditions and patterns
        $additionalConditions = [
            // Additional conditions for better results
            // For example:
            'Actively Working at', 'Currently Holding Position as', 'Currently Engaged with Company',
            'Currently Employed as',
        ];

        // Combine all conditions and keywords
        $allConditions = array_merge($currentEmploymentFormats, $experienceKeywords, $additionalConditions);

        // Check if any of the formats indicate current employment status
        foreach ($allConditions as $condition) {
            // Check for the condition in the resume text
            if (stripos($resumeText, $condition) !== false) {
                // If a match is found, extract the employer name
                $employerName = $this->extractEmployerName($resumeText, $condition, $designationKeywords);

                // Return the condition and employer name
                return response()->json(['current_employer_format' => $condition, 'employer_name' => $employerName]);
            }
        }

        // If no match is found, return null
        return response()->json(['current_employer_format' => null, 'employer_name' => null]);
    }

    // Helper function to extract the employer name
    private function extractEmployerNamew($resumeText, $keyword, $designationKeywords)
    {
        // Find the position of the keyword in the text
        $keywordIndex = stripos($resumeText, $keyword);
        // Extract text after the keyword
        $textAfterKeyword = substr($resumeText, $keywordIndex + strlen($keyword));
        // Split text into individual words
        $words = explode(' ', $textAfterKeyword);
        // Limit words to 50
        $limitedWords = array_slice($words, 0, 50);
        // Iterate through words to find the first noun
        foreach ($limitedWords as $word) {
            if ($this->isNoun($word) && ! $this->isDesignation($word, $designationKeywords)) {
                return $word;
            }
        }

        return null; // If no noun is found
    }

    // Subfunction to determine if a word is a noun
    private function isNounw($word)
    {
        // Check if word is not empty
        return ! empty($word);
    }

    // Subfunction to determine if a word is a common designation keyword
    private function isDesignationw($word, $designationKeywords)
    {
        // Check if word exists in the list of designation keywords
        return in_array(strtolower($word), array_map('strtolower', $designationKeywords));
    }

    /** Returns recruiter note templates for the current company (global + company-scoped). */
    public function recruiterNoteTemplates(Request $request)
    {
        $templates = DB::table('recruiter_note_master')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('company_id')->orWhere('company_id', auth()->user()->company_id);
            })
            ->orderBy('id')
            ->get(['id', 'title', 'content']);

        return response()->json(['status' => 'success', 'data' => $templates]);
    }
}
