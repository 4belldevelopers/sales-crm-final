<?php

namespace App\Services\Interview;

use App\Helper\GeneralMethod;
use App\Models\AddJobWiseCondidate;
use App\Models\InterviewSchedule;
use App\Models\User;
use App\Services\Interview\DTO\InterviewStatusTransitionResult;
use App\Jobs\QueueNotificationMailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class InterviewStatusTransitionEngine
 * 
 * Intent: Central domain engine for managing interview attendance status transitions,
 * calculating net point deltas across all 6 UI attendance statuses, upserting feedback records,
 * enforcing database transaction safety, and triggering asynchronous Elasticsearch team rank updates.
 * 
 * Supported UI Attendance Statuses:
 * - 1: Attended
 * - 2: No Show
 * - 3: Rescheduled
 * - 4: Cancelled by Company
 * - 6: Other Reason
 * - 7: Cancelled by Candidate
 * 
 * Tenancy Note: All queries are tenant-scoped by company_id and interview_id.
 */
class InterviewStatusTransitionEngine
{
    /**
     * Process an interview feedback update and status transition within an atomic DB transaction.
     *
     * @param Request $request
     * @param User $user
     * @return InterviewStatusTransitionResult
     */
    public function process(Request $request, User $user): InterviewStatusTransitionResult
    {
        $interviewId = (int) ($request->interview_id ?? $request->id ?? 0);
        $applicationId = (int) ($request->application_id ?? 0);
        $newAttendanceStatus = (int) ($request->app_int_status ?? 0);
        $newFeedbackStatus = (int) ($request->app_int_feedback_status ?? 0);
        $feedbackComment = (string) ($request->feedback_comment ?? '');
        $rating = (float) ($request->rating ?? 0);

        if (!$interviewId) {
            return new InterviewStatusTransitionResult(false, 'Invalid interview ID.');
        }

        // Map legacy interview_result to feedback_id if present
        $resultToFeedbackId = [
            'selected' => 1,
            'hold' => 2,
            'rejected' => 3,
            'fake_proxy' => 4,
            'feedback_pending' => 5,
        ];
        if (!empty($request->interview_result) && isset($resultToFeedbackId[$request->interview_result])) {
            $newFeedbackStatus = $resultToFeedbackId[$request->interview_result];
        }

        // Use Redis / Cache atomic lock to prevent concurrent requests on the same interview
        $lockKey = "interview:transition:lock:{$interviewId}";
        $lock = Cache::lock($lockKey, 5);

        if (!$lock->get()) {
            return new InterviewStatusTransitionResult(
                false,
                'Another update is currently processing for this interview. Please try again.'
            );
        }

        try {
            return DB::transaction(function () use ($request, $user, $interviewId, $applicationId, $newAttendanceStatus, $newFeedbackStatus, $feedbackComment, $rating) {
                // Tenancy Note: Lock target interview schedule record for update within current company context
                $schedule = DB::table('interview_schedules')
                    ->where('id', $interviewId)
                    ->lockForUpdate()
                    ->first();

                if (!$schedule) {
                    return new InterviewStatusTransitionResult(false, 'Interview schedule not found.');
                }

                $previousAttendanceStatus = (int) ($schedule->available_status ?? 0);
                $previousFeedbackStatus = (int) ($schedule->feedback ?? 0);

                // Calculate Net Point Deltas using status vector difference: Net = E(New) - E(Previous)
                $deltas = $this->calculateNetDeltas(
                    $previousAttendanceStatus,
                    $previousFeedbackStatus,
                    $newAttendanceStatus,
                    $newFeedbackStatus,
                    $schedule->schedule_date
                );

                // Upsert feedback record (single feedback row per interview + user)
                $feedbackValues = [
                    'interview_id' => $interviewId,
                    'application_id' => $applicationId ?: $schedule->job_application_id,
                    'available_status' => $newAttendanceStatus,
                    'feedback_id' => $newFeedbackStatus,
                    'star_ratings' => ceil($rating),
                    'message' => $feedbackComment,
                    'interview_result' => $request->interview_result ?? null,
                    'overall_recommendation' => $request->overall_recommendation ?? null,
                    'current_ctc' => $request->current_ctc ?? null,
                    'expected_ctc' => $request->expected_ctc ?? null,
                    'offered_ctc' => $request->offered_ctc ?? null,
                    'max_budget' => $request->max_budget ?? null,
                    'notice_period' => $request->notice_period ?? null,
                    'joining_date' => (!empty($request->joining_date) ? $request->joining_date : null),
                    'compensation_fit' => $request->compensation_fit ?? null,
                    'joining_probability' => $request->joining_probability ?? null,
                    'risk_factors' => (!empty($request->risk_factors) ? implode(',', (array) $request->risk_factors) : null),
                    'compensation_comments' => $request->compensation_comments ?? null,
                    'update_by' => $user->id,
                    'updated_at' => now(),
                ];

                // Filter column listing to prevent SQL missing column exceptions
                $feedbackCols = \Illuminate\Support\Facades\Schema::getColumnListing('interview_feedback');
                $feedbackValues = array_intersect_key($feedbackValues, array_flip($feedbackCols));

                $existingFeedback = DB::table('interview_feedback')
                    ->where('interview_id', $interviewId)
                    ->where('update_by', $user->id)
                    ->first();

                if ($existingFeedback) {
                    DB::table('interview_feedback')
                        ->where('id', $existingFeedback->id)
                        ->update($feedbackValues);
                    $feedbackId = $existingFeedback->id;
                } else {
                    $feedbackValues['created_at'] = now();
                    $feedbackId = DB::table('interview_feedback')->insertGetId($feedbackValues);
                }

                // Process skills & behavioral assessment inputs if Attended
                if ($newAttendanceStatus == 1 && $newFeedbackStatus != 5) {
                    DB::table('interview_skills_feedback')->where('feedback_id', $feedbackId)->delete();

                    $skillsData = [];
                    foreach ($request->all() as $key => $value) {
                        if (preg_match('/skillsId_(\d+)/', $key, $matches)) {
                            $skillId = $matches[1];
                            $skillsData[] = [
                                'interview_id' => $interviewId,
                                'application_id' => $applicationId ?: $schedule->job_application_id,
                                'update_by' => $user->id,
                                'candidate_status' => $newAttendanceStatus,
                                'feedback_status' => $newFeedbackStatus,
                                'feedback_id' => $feedbackId,
                                'star_ratings' => ceil($rating),
                                'skills' => $request->input("skills_{$skillId}"),
                                'skills_exp' => $request->input("experience_{$skillId}"),
                                'skills_ratings' => $request->input("rating_{$skillId}"),
                                'skills_remarks' => $request->input("remark_{$skillId}"),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    // Add behavioral metrics (Communication, Problem Solving, Leadership, Cultural Fit)
                    $behavioralMetrics = [
                        ['skills_com', 'experience_com', 'rating_com', 'remark_com'],
                        ['skills_ps', 'experience_ps', 'rating_ps', 'remark_ps'],
                        ['skills_lead', 'experience_lead', 'rating_lead', 'remark_lead'],
                        ['skills_culf', 'experience_culf', 'rating_culf', 'remark_culf'],
                    ];
                    foreach ($behavioralMetrics as $bm) {
                        if (!empty($request->input($bm[0]))) {
                            $skillsData[] = [
                                'interview_id' => $interviewId,
                                'application_id' => $applicationId ?: $schedule->job_application_id,
                                'update_by' => $user->id,
                                'candidate_status' => $newAttendanceStatus,
                                'feedback_status' => $newFeedbackStatus,
                                'feedback_id' => $feedbackId,
                                'star_ratings' => ceil($rating),
                                'skills' => $request->input($bm[0]),
                                'skills_exp' => $request->input($bm[1]),
                                'skills_ratings' => $request->input($bm[2]),
                                'skills_remarks' => $request->input($bm[3]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($skillsData)) {
                        DB::table('interview_skills_feedback')->insert($skillsData);
                    }
                }

                // Update interview_schedules and updatetimeline
                $averageRating = ceil($rating);
                DB::table('interview_schedules')
                    ->where('id', $interviewId)
                    ->update([
                        'available_status' => $newAttendanceStatus,
                        'feedback' => $newFeedbackStatus,
                        'average_star_rating' => $averageRating,
                        'updated_at' => now(),
                    ]);

                DB::table('updatetimeline')
                    ->where('interview_schedules_id', $interviewId)
                    ->update([
                        'average_star_rating' => $averageRating,
                        'interview_message' => $feedbackComment,
                        'update_at' => now(),
                    ]);

                // Apply Net Point Deltas if any net change occurred
                if ($deltas['recruiterScheduled'] != 0 || $deltas['recruiterNoShow'] != 0 || $deltas['teamCaptainScheduled'] != 0) {
                    $this->applyPointDeltas($schedule, $deltas);
                }

                // Clean caches post-update
                GeneralMethod::cacheClean('allinterviewschedule_version_user_' . $user->id);

                return new InterviewStatusTransitionResult(
                    true,
                    'Interview feedback updated successfully.',
                    $interviewId,
                    $previousAttendanceStatus,
                    $newAttendanceStatus,
                    $deltas['recruiterScheduled'],
                    $deltas['recruiterNoShow'],
                    $deltas['teamCaptainScheduled'],
                    $feedbackId
                );
            });
        } catch (\Throwable $e) {
            Log::error("InterviewStatusTransitionEngine failed for interview {$interviewId}: " . $e->getMessage(), [
                'interview_id' => $interviewId,
                'exception' => $e,
            ]);

            return new InterviewStatusTransitionResult(false, 'Failed to update interview feedback: ' . $e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Calculate point effect vector E(Status, SubStatus) for a given state.
     *
     * Intent: Determines point allocations for recruiter and team captain based on attendance & feedback status.
     * Supported statuses: 1 = Attended (+10 recruiter, +2 captain), 2 & 7 = No Show / Cancelled by Candidate (-5 recruiter no-show penalty), 3 = Rescheduled.
     * Tenancy Note: Point calculations apply within tenant company context.
     *
     * @param int $attendanceStatus
     * @param int $feedbackStatus
     * @return array [recruiterScheduled, recruiterNoShow, teamCaptainScheduled]
     */
    public function getPointVector(int $attendanceStatus, int $feedbackStatus): array
    {
        $recruiterScheduled = 0.0;
        $recruiterNoShow = 0.0;
        $teamCaptainScheduled = 0.0;

        if ($attendanceStatus == 1) { // Attended
            $recruiterScheduled = 10.0;
            $teamCaptainScheduled = 2.0;
        } elseif ($attendanceStatus == 2 || $attendanceStatus == 7) { // No Show (2) or Cancelled by Candidate (7)
            $recruiterNoShow = -5.0;
        } elseif ($attendanceStatus == 3) { // Rescheduled
            if ($feedbackStatus == 13) { // Cancelled by Candidate sub-status
                $recruiterNoShow = -5.0;
            }
        }

        return [
            'recruiterScheduled' => $recruiterScheduled,
            'recruiterNoShow' => $recruiterNoShow,
            'teamCaptainScheduled' => $teamCaptainScheduled,
        ];
    }

    /**
     * Calculate net deltas Net = E(New) - E(Previous).
     *
     * Intent: Computes net delta between new state vector and previous state vector.
     * Date restriction removal: Allows point deltas for all interviews updated via feedback drawer regardless of schedule date.
     *
     * @param int $prevStatus
     * @param int $prevSubStatus
     * @param int $newStatus
     * @param int $newSubStatus
     * @param string|null $scheduleDate
     * @return array
     */
    public function calculateNetDeltas(int $prevStatus, int $prevSubStatus, int $newStatus, int $newSubStatus, ?string $scheduleDate): array
    {
        $prevVector = $this->getPointVector($prevStatus, $prevSubStatus);
        $newVector = $this->getPointVector($newStatus, $newSubStatus);

        return [
            'recruiterScheduled' => $newVector['recruiterScheduled'] - $prevVector['recruiterScheduled'],
            'recruiterNoShow' => $newVector['recruiterNoShow'] - $prevVector['recruiterNoShow'],
            'teamCaptainScheduled' => $newVector['teamCaptainScheduled'] - $prevVector['teamCaptainScheduled'],
        ];
    }

    /**
     * Apply point deltas to recruiter and team captain via GeneralMethod::teamRankElastic.
     *
     * @param object $schedule
     * @param array $deltas
     * @return void
     */
    protected function applyPointDeltas(object $schedule, array $deltas): void
    {
        $wiseCandidate = AddJobWiseCondidate::withoutGlobalScopes()->find($schedule->interview_job_wise_con_id);
        if (!$wiseCandidate) {
            return;
        }

        // Apply recruiter point delta
        if ($deltas['recruiterScheduled'] != 0 || $deltas['recruiterNoShow'] != 0) {
            $recruiterPointLog = [
                'company_id' => $wiseCandidate->company_id,
                'user_id' => $wiseCandidate->submitted_by,
                'candidate_id' => $wiseCandidate->job_application_id ?: $wiseCandidate->id,
                'interviewScheduled' => $deltas['recruiterScheduled'],
                'interviewNoShow' => $deltas['recruiterNoShow'],
            ];
            GeneralMethod::teamRankElastic($recruiterPointLog);
        }

        // Apply team captain point delta
        if ($deltas['teamCaptainScheduled'] != 0) {
            $spokRows = DB::table('job_assign')
                ->where('job_id', $schedule->interview_job_id)
                ->get(['spok_user_id', 'company_id']);

            foreach ($spokRows as $row) {
                if (!is_null($row->spok_user_id) && $row->spok_user_id != $wiseCandidate->submitted_by) {
                    $captainPointLog = [
                        'company_id' => $row->company_id,
                        'user_id' => $row->spok_user_id,
                        'candidate_id' => $wiseCandidate->job_application_id ?: $wiseCandidate->id,
                        'teamCaptainInterviewScheduled' => $deltas['teamCaptainScheduled'],
                    ];
                    GeneralMethod::teamRankElastic($captainPointLog);
                }
            }
        }
    }
}
