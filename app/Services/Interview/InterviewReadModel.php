<?php

namespace App\Services\Interview;

use Carbon\Carbon;
use Throwable;

/**
 * Class InterviewReadModel
 *
 * Intent: DTO & Projection Transformer for Interview Tracker read operations.
 * Projects Elasticsearch raw _source documents into a pure, UI-ready JSON projection.
 * Enforces zero server-side MySQL queries during rendering (0% MySQL reads).
 * Pre-computes action eligibility, time windows, formatted strings, and CSS classes.
 */
class InterviewReadModel
{
    /**
     * Transform a single Elasticsearch _source document into the canonical Read Projection array.
     *
     * @param array $source Raw Elasticsearch _source document
     * @param int|null $authCompanyId Authenticated tenant company ID
     * @return array Transformed UI projection array
     */
    public static function fromElasticSource(array $source, ?int $authCompanyId = null): array
    {
        $id = (int) ($source['id'] ?? 0);
        $fullName = trim((string) ($source['full_name'] ?? 'Candidate'));
        $initial = strtoupper(mb_substr($fullName, 0, 1) ?: 'C');
        $designation = trim((string) ($source['current_designation'] ?? '—'));

        // Job details
        $jobId = (int) ($source['jobId'] ?? ($source['job_id'] ?? 0));
        $jobTitle = trim((string) ($source['job_title'] ?? 'Job Position'));
        $clientName = trim((string) ($source['client_name'] ?? ($source['company_name'] ?? '')));

        // Schedule & Timing
        $scheduleDateRaw = $source['schedule_date'] ?? null;
        $dateFormatted = '—';
        $timeFormatted = '';
        $scheduleCarbon = null;

        if ($scheduleDateRaw) {
            try {
                $scheduleCarbon = Carbon::parse($scheduleDateRaw);
                $dateFormatted = $scheduleCarbon->format('d M, Y');
            } catch (Throwable $e) {
                $dateFormatted = (string) $scheduleDateRaw;
            }
        }

        $timeFrom = trim((string) ($source['schedule_from_time'] ?? ($source['schedule_time'] ?? '')));
        $timeTo = trim((string) ($source['schedule_to_time'] ?? ''));
        $tz = trim((string) ($source['timezone_abbreviation'] ?? ''));

        if ($timeTo) {
            $timeFormatted = $timeTo . ($tz ? " ({$tz})" : '');
        } elseif ($timeFrom) {
            $timeFormatted = $timeFrom . ($tz ? " ({$tz})" : '');
        }

        // Mode and Round
        $modeName = trim((string) ($source['mode_name'] ?? ($source['interview_mode'] ?? 'Video')));
        $modeLower = strtolower($modeName);
        $modeIcon = 'fa-video';
        $modeLabel = $modeName ?: 'Video';

        if (str_contains($modeLower, 'phone') || str_contains($modeLower, 'call')) {
            $modeIcon = 'fa-phone-alt';
            $modeLabel = 'Phone';
        } elseif (str_contains($modeLower, 'person') || str_contains($modeLower, 'office') || str_contains($modeLower, 'offline')) {
            $modeIcon = 'fa-building';
            $modeLabel = 'In-Person';
        }

        $roundName = trim((string) ($source['round_name'] ?? ($source['mode_lavel_name'] ?? 'Interview')));
        $meetingLink = trim((string) ($source['interview_mention_link'] ?? ($source['meeting_link'] ?? ($source['url_link'] ?? ''))));

        // Attendance / Available Status
        // 0 = Scheduled, 1 = Attended, 2 = No Show, 3 = Rescheduled, 4 = Cancelled by Company, 6 = Other, 7 = Cancelled by Candidate
        $availableStatus = (int) ($source['available_status'] ?? 0);
        $attendanceMap = [
            0 => ['code' => 0, 'label' => 'Scheduled', 'css_class' => 'cit-fb-scheduled'],
            1 => ['code' => 1, 'label' => 'Attended', 'css_class' => 'cit-fb-attended'],
            2 => ['code' => 2, 'label' => 'No Show', 'css_class' => 'cit-fb-noshow'],
            3 => ['code' => 3, 'label' => 'Rescheduled', 'css_class' => 'cit-fb-rescheduled'],
            4 => ['code' => 4, 'label' => 'Cancelled', 'css_class' => 'cit-fb-cancelled'],
            6 => ['code' => 6, 'label' => 'Other', 'css_class' => 'cit-fb-default'],
            7 => ['code' => 7, 'label' => 'Cancelled by Cand.', 'css_class' => 'cit-fb-cancelled'],
        ];
        $attendance = $attendanceMap[$availableStatus] ?? ['code' => $availableStatus, 'label' => 'Scheduled', 'css_class' => 'cit-fb-default'];

        // Evaluation Outcome / Feedback
        // 1 = Selected, 2 = Hold, 3 = Rejected, 4 = Fake/Proxy, 5 = Awaiting Feedback
        $feedbackCode = (int) ($source['feedback'] ?? 0);
        $evaluationMap = [
            1 => ['code' => 1, 'label' => 'Selected', 'css_class' => 'text-success fw-bold'],
            2 => ['code' => 2, 'label' => 'On Hold', 'css_class' => 'text-warning fw-bold'],
            3 => ['code' => 3, 'label' => 'Rejected', 'css_class' => 'text-danger fw-bold'],
            4 => ['code' => 4, 'label' => 'Unverified / Proxy', 'css_class' => 'text-danger fw-bold'],
            5 => ['code' => 5, 'label' => 'Awaiting Feedback', 'css_class' => 'text-primary fw-semibold'],
        ];
        $evaluation = $evaluationMap[$feedbackCode] ?? ['code' => $feedbackCode, 'label' => '—', 'css_class' => 'text-muted'];
        $starRating = (float) ($source['average_star_rating'] ?? 0);
        $evaluation['star_rating'] = $starRating > 0 ? round($starRating, 1) : null;

        // Action Eligibility Computation (Server-Authoritative Time Windows)
        $now = Carbon::now('UTC');
        $joinAllowed = false;
        $joinWindowState = 'JOIN_WINDOW_CLOSED';
        $joinWindow = null;

        if ($scheduleCarbon && !empty($meetingLink) && in_array($availableStatus, [0, 3], true)) {
            $windowStart = $scheduleCarbon->copy()->subMinutes(15);
            $windowEnd = $scheduleCarbon->copy()->addMinutes(60); // 45 min interview + 15 min grace

            $joinWindow = [
                'starts_at' => $windowStart->toIso8601String(),
                'ends_at' => $windowEnd->toIso8601String(),
                'timezone' => $tz ?: 'UTC',
            ];

            if ($now->between($windowStart, $windowEnd)) {
                $joinAllowed = true;
                $joinWindowState = 'JOIN_WINDOW_ACTIVE';
            } elseif ($now->lt($windowStart)) {
                $joinWindowState = 'JOIN_WINDOW_OPEN';
            } else {
                $joinWindowState = 'JOIN_WINDOW_CLOSED';
            }
        }

        $feedbackViewAllowed = in_array($feedbackCode, [1, 2, 3, 4], true) || ($availableStatus === 1 && $feedbackCode > 0);
        $feedbackSubmitAllowed = ($availableStatus === 1 && $feedbackCode === 5);
        $rescheduleAllowed = in_array($availableStatus, [0, 2, 3], true);

        // Interviewers List (Extracted from ES source, 0 MySQL queries)
        $interviewers = [];
        $rawInterviewers = $source['interviewers'] ?? ($source['interviewer_name'] ?? '');
        $companyNameDefault = trim((string) ($source['company_name'] ?? 'Panel'));

        if (!empty($rawInterviewers)) {
            $names = is_array($rawInterviewers)
                ? $rawInterviewers
                : array_filter(array_map('trim', explode(',', (string) $rawInterviewers)));

            foreach ($names as $idx => $intvName) {
                if (empty($intvName)) continue;
                $interviewers[] = [
                    'id' => $idx + 1,
                    'name' => ucwords($intvName),
                    'company_name' => $companyNameDefault,
                ];
            }
        }

        // Master & Child Status
        $masterStatusName = trim((string) ($source['master_status_name'] ?? 'Interview'));
        $childStatusName = trim((string) ($source['child_status_name'] ?? ''));
        $cssStyle = trim((string) ($source['css_style'] ?? 'badge-light-primary'));

        return [
            'id' => $id,
            'candidate' => [
                'id' => (int) ($source['candidate_id'] ?? ($source['condidate_id'] ?? 0)),
                'name' => $fullName,
                'initial' => $initial,
                'designation' => $designation,
                'email' => trim((string) ($source['email'] ?? '')),
                'phone' => trim((string) ($source['phone'] ?? '')),
            ],
            'job' => [
                'id' => $jobId,
                'title' => $jobTitle,
                'client_name' => $clientName,
            ],
            'schedule' => [
                'date_raw' => $scheduleDateRaw,
                'date_formatted' => $dateFormatted,
                'time_formatted' => $timeFormatted,
                'mode_icon' => $modeIcon,
                'mode_label' => $modeLabel,
                'round_name' => $roundName,
                'meeting_link' => $meetingLink,
            ],
            'attendance' => $attendance,
            'evaluation' => $evaluation,
            'actions' => [
                'join_allowed' => $joinAllowed,
                'join_window_state' => $joinWindowState,
                'join_window' => $joinWindow,
                'feedback_view_allowed' => $feedbackViewAllowed,
                'feedback_submit_allowed' => $feedbackSubmitAllowed,
                'reschedule_allowed' => $rescheduleAllowed,
            ],
            'interviewers' => $interviewers,
            'stage' => [
                'master_status_name' => $masterStatusName,
                'child_status_name' => $childStatusName,
                'css_style' => $cssStyle,
            ],
        ];
    }

    /**
     * Batch transform an array of Elasticsearch hits into a collection of Read Models.
     *
     * @param array $hits Elasticsearch hits array
     * @param int|null $authCompanyId Authenticated tenant company ID
     * @return array Transformed array of UI projection read models
     */
    public static function collection(array $hits, ?int $authCompanyId = null): array
    {
        $models = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'] ?? $hit;
            if (is_array($source)) {
                $models[] = self::fromElasticSource($source, $authCompanyId);
            }
        }
        return $models;
    }
}
