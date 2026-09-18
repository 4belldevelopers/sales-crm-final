<?php

/**
 * Handles all heavy post-insert work after a candidate is added via clientappstore().
 * Runs on the 'default' queue so ES failures don't block the HTTP response.
 */

namespace App\Jobs;

use App\Helper\GeneralMethod;
use App\Models\JobApplication;
use App\Models\JobApplicationAnswer;
use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCandidateSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public int $timeout = 120;

    public function __construct(
        private readonly int    $applicationId,
        private readonly int    $addJobWiseCandidateId,
        private readonly int    $jobId,
        private readonly int    $jobOwnerCompanyId,
        private readonly int    $submittedById,
        private readonly int    $userCompanyId,
        private readonly int    $applicationStatusId,
        private readonly string $applicationEmail,
        private readonly array  $requestData,
    ) {}

    public function handle(): void
    {
        try {
            $elasticService = app(ElasticService::class);
            $client         = $elasticService->getClient();
            $appId          = $this->applicationId;
            $jobId          = $this->jobId;
            $data           = $this->requestData;

            // 1. Sync skills to ES skills index (idempotent — safe to call on every submission)
            $skillArr = json_decode($data['skills'] ?? '[]', true) ?? [];
            if (!empty($skillArr)) {
                $skillValues = array_column($skillArr, 'value');
                if (count($skillValues) > 0) {
                    GeneralMethod::skillsAddElastic($skillValues);
                }
            }

            // 3. Team rank ES update
            $teamRankElastic = [
                'company_id'             => $this->userCompanyId,
                'user_id'                => $this->submittedById,
                'candidate_id'           => $this->applicationId,
                'profileUpload'          => 1,
                'interviewScheduled'     => 0,
                'interviewNoShow'        => 0,
                'offerReleased'          => 0,
                'candidateJoined'        => 0,
                'jobsPosted'             => 0,
                'feedbackGivenOnCVs'     => 0,
                'postJobToVendorMarket'  => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates'    => 0,
                'respondToBenchRequests' => 0,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

            // 2. Skill experience rows (YearskillId)
            $yearSkillId  = $data['YearskillId'] ?? [];
            $monthSkillId = $data['MonthskillId'] ?? [];
            $ratingArr    = $data['rating'] ?? [];

            if (!empty($yearSkillId) && is_array($yearSkillId)) {
                $client->deleteByQuery([
                    'index'     => ELASTIC_CANDIDATE_SKILLS_EXP,
                    'conflicts' => 'proceed',
                    'body'      => [
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
                DB::table('candidate_job_skill_exp')
                    ->where('candidate_id', $appId)
                    ->where('job_id', $jobId)
                    ->delete();

                foreach ($yearSkillId as $skillId => $value) {
                    $ss        = $value >= 1 ? 'Years' : 'Year';
                    $valMonth  = $monthSkillId[$skillId] ?? 0;
                    $skillValue = $value . '.' . $valMonth . ' ' . $ss;
                    $rating     = (int)($ratingArr[$skillId] ?? 0);

                    $expId = DB::table('candidate_job_skill_exp')->insertGetId([
                        'job_skill_id' => $skillId,
                        'skill_value'  => $skillValue,
                        'job_id'       => $jobId,
                        'rating'       => $rating,
                        'candidate_id' => $appId,
                    ]);

                    $skillRow = DB::table('job_metronics_skills')
                        ->select('skill_name')
                        ->where('id', $skillId)
                        ->first();

                    $client->index([
                        'index' => ELASTIC_CANDIDATE_SKILLS_EXP,
                        'id'    => $expId,
                        'body'  => [
                            'skill_name'   => $skillRow->skill_name ?? '',
                            'job_skill_id' => $skillId,
                            'skill_value'  => $skillValue,
                            'job_id'       => $jobId,
                            'rating'       => $rating,
                            'candidate_id' => $appId,
                            'created_at'   => now()->toIso8601String(),
                            'updated_at'   => now()->toIso8601String(),
                        ],
                    ]);
                }
            }

            // 3. job_application_skills
            $skillStr = '';
            $skillArr = json_decode($data['skills'] ?? '[]', true) ?? [];
            if (!empty($skillArr)) {
                foreach ($skillArr as $sval) {
                    if (!DB::table('job_application_skills')
                        ->where('job_application_id', $appId)
                        ->where('skills', 'LIKE', '%' . $sval['value'] . '%')
                        ->exists()
                    ) {
                        DB::table('job_application_skills')->insert([
                            'job_application_id' => $appId,
                            'skills'             => $sval['value'],
                        ]);
                    }
                }
            }

            // 4. job_assign
            $chkJobAssign = DB::table('job_assign')
                ->where('company_id', $this->jobOwnerCompanyId)
                ->where('job_id', $jobId)
                ->where('user_id', $this->submittedById)
                ->count();
            if ($chkJobAssign < 1) {
                DB::table('job_assign')->insert([
                    'company_id' => $this->jobOwnerCompanyId,
                    'user_id'    => $this->submittedById,
                    'job_id'     => $jobId,
                ]);
            }

            // 5. candidate_skills_value
            $jobskill = $data['jobskill'] ?? [];
            if (!empty($jobskill) && is_array($jobskill)) {
                DB::table('candidate_skills_value')
                    ->where('job_id', $jobId)
                    ->where('candidate_id', $appId)
                    ->delete();
                foreach ($jobskill as $key => $value) {
                    if (!empty($value)) {
                        DB::table('candidate_skills_value')->insert([
                            'job_id'       => $jobId,
                            'skill_id'     => $key,
                            'candidate_id' => $appId,
                            'skill_value'  => $value,
                        ]);
                    }
                }
            }

            // 6. Timeline
            $timelineArr = [
                'event_id'              => 1,
                'event_by_company_id'   => $this->userCompanyId,
                'event_by_user_id'      => $this->submittedById,
                'master_process_name'   => 'InReview',
                'application_id'        => $appId,
                'application_email'     => $this->applicationEmail,
                'job_id'                => $jobId,
                'note'                  => 'Successfully Added',
            ];
            GeneralMethod::UpdateTimeline($timelineArr);

            // 7. tb_ranks
            DB::table('tb_ranks')->insert([
                'company_id'     => $this->userCompanyId,
                'user_id'        => $this->submittedById,
                'job_id'         => $jobId,
                'status'         => 0,
                'application_id' => $appId,
            ]);

            // 8. JobApplicationAnswer
            $answers = $data['answer'] ?? [];
            if (!empty($answers) && is_array($answers)) {
                foreach ($answers as $questionId => $answerValue) {
                    $answer                      = new JobApplicationAnswer();
                    $answer->job_application_id  = $appId;
                    $answer->job_id              = $jobId;
                    $answer->question_id         = $questionId;
                    $answer->answer              = $answerValue;
                    $answer->save();
                }
            }

            // 9. job_additional_detail + ES
            $additionalDetails = DB::table('job_additional_detail')
                ->where('job_id', $jobId)
                ->orderBy('id', 'ASC')
                ->get();

            $additionalInput = $data['additional_detail'] ?? [];
            $inc             = 0;
            foreach ($additionalDetails as $jd) {
                $expId = DB::table('application_additional_detail')->insertGetId([
                    'job_id'                    => $jobId,
                    'question_id'               => $jd->id,
                    'application_id'            => $appId,
                    'job_detail_value'          => $jd->detail_value,
                    'application_detail_value'  => $additionalInput[$inc] ?? null,
                ]);
                $client->update([
                    'index' => ELASTIC_CANDIDATE_JOB_QUESTION,
                    'id'    => $expId,
                    'body'  => [
                        'doc' => [
                            'job_id'                   => $jobId,
                            'question_id'              => $jd->id,
                            'application_id'           => $appId,
                            'job_detail_value'         => $jd->detail_value,
                            'application_detail_value' => $additionalInput[$inc] ?? null,
                            'created_at'               => now()->toIso8601String(),
                            'updated_at'               => now()->toIso8601String(),
                        ],
                        'doc_as_upsert' => true,
                    ],
                ]);
                $inc++;
            }

            // 10. Package redeem — pass company/user directly so no auth()->user() call is needed in queue context
            GeneralMethod::packageRedeem('CVACCESS', 'job_applications', $appId, $this->userCompanyId, $this->submittedById);

            // 11. Main ES candidate index
            GeneralMethod::JobApplicationsAddEditUpload($this->addJobWiseCandidateId, $this->userCompanyId);

        } catch (\Throwable $e) {
            try {
                Log::error('ProcessCandidateSubmissionJob failed', [
                    'application_id' => $this->applicationId,
                    'error'          => $e->getMessage(),
                    'trace'          => $e->getTraceAsString(),
                ]);
            } catch (\Throwable) {
                // Safeguard against monolog stream/file permission issues on server logs
            }
            throw $e; // allow queue retry
        }
    }
}
