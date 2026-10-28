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
     * @param  array  $source  Raw Elasticsearch _source document
     * @param  int|null  $authCompanyId  Authenticated tenant company ID
     * @return array Transformed UI projection array
     */
    public static function fromElasticSource(array $source, ?int $authCompanyId = null): array
    {
        $id = (int) ($source['id'] ?? 0);
        $fullName = trim((string) ($source['full_name'] ?? ($source['candidate_name'] ?? ($source['name'] ?? trim((@$source['first_name'] ?? '').' '.(@$source['last_name'] ?? '')) ?: 'Candidate'))));
        $initial = strtoupper(mb_substr($fullName, 0, 1) ?: 'C');
        $designation = trim((string) ($source['current_designation'] ?? ($source['designation_name'] ?? ($source['designation'] ?? '—'))));

        // Candidate Avatar Color
        $firstChar = strtolower(mb_substr($fullName, 0, 1) ?: 'a');
        $ord = ord($firstChar);
        if ($ord >= 97 && $ord <= 101) {
            $avatarColorClass = 'it-avatar-red';
        } elseif ($ord >= 102 && $ord <= 106) {
            $avatarColorClass = 'it-avatar-orange';
        } elseif ($ord >= 107 && $ord <= 111) {
            $avatarColorClass = 'it-avatar-blue';
        } elseif ($ord >= 112 && $ord <= 116) {
            $avatarColorClass = 'it-avatar-green';
        } elseif ($ord >= 117 && $ord <= 121) {
            $avatarColorClass = 'it-avatar-purple';
        } else {
            $avatarColorClass = 'it-avatar-gray';
        }

        // Candidate Follow-up (view-only)
        $fuStatus = $source['followup_status'] ?? null;
        $fuAt = $source['followup_status_at'] ?? null;
        $fuFormattedAt = null;
        if ($fuAt) {
            try {
                $fuFormattedAt = Carbon::parse($fuAt)->format('d M, h:i A');
            } catch (Throwable $e) {
                $fuFormattedAt = (string) $fuAt;
            }
        }
        $fuLabels = ['confirmed' => 'Confirmed', 'no-response' => 'No Response', 'rescheduled' => 'Rescheduled'];
        $fuLabel = $fuLabels[$fuStatus] ?? null;
        $fuStyleClass = $fuStatus === 'confirmed' ? 'confirmed' : ($fuStatus === 'no-response' ? 'danger' : ($fuStatus === 'rescheduled' ? 'info' : ''));

        // Job details & Classification
        $jobId = (int) ($source['jobId'] ?? ($source['job_id'] ?? 0));
        $jobTitle = trim((string) ($source['job_title'] ?? 'Job Position'));
        $clientName = trim((string) ($source['client_name'] ?? ($source['company_name'] ?? '')));

        $recommendationId = (int) ($source['recommendation_id'] ?? 0);
        $toCompanyId = (int) ($source['to_company_id'] ?? 0);
        $isDirect = ($recommendationId > 0 && $authCompanyId && $toCompanyId === (int) $authCompanyId);

        $jobTypeLabel = $isDirect ? 'Direct Client' : 'Internal';
        $jobTypeClass = $isDirect ? 'it-job-type-direct' : 'it-job-type-internal';
        $displayCompName = $isDirect
            ? ($source['company_name'] ?? ($source['uc_company_name'] ?? ($source['client_name'] ?? '')))
            : ($source['client_name'] ?? ($source['company_name'] ?? ''));

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
            $timeFormatted = $timeTo.($tz ? " ({$tz})" : '');
        } elseif ($timeFrom) {
            $timeFormatted = $timeFrom.($tz ? " ({$tz})" : '');
        }

        // Mode and Link/Address
        $modeName = trim((string) ($source['mode_name'] ?? 'Video Call'));
        $interviewMode = (int) ($source['interview_mode'] ?? ($source['mode'] ?? 2));
        $modeLower = strtolower($modeName);
        $modeIconType = 'video';
        $modeDisplayLabel = $modeName ?: 'Video Call';
        $modeIconLegacy = 'fa-video';

        if ($interviewMode == 1 || str_contains($modeLower, 'person') || str_contains($modeLower, 'face') || str_contains($modeLower, 'office') || str_contains($modeLower, 'offline')) {
            $modeIconType = 'map-marker';
            $modeIconLegacy = 'fa-building';
            $modeDisplayLabel = $modeName ?: 'Face to Face';
        } elseif ($interviewMode == 3 || str_contains($modeLower, 'phone') || str_contains($modeLower, 'call')) {
            $modeIconType = 'phone';
            $modeIconLegacy = 'fa-phone-alt';
            $modeDisplayLabel = $modeName ?: 'Phone Call';
        }

        $roundName = trim((string) ($source['round_name'] ?? ($source['mode_lavel_name'] ?? 'Interview')));
        $meetingLink = trim((string) ($source['interview_mention_link'] ?? ($source['meeting_link'] ?? ($source['url_link'] ?? ''))));

        $isUrl = (bool) preg_match('#^https?://#i', $meetingLink);
        $ilType = null;
        $ilLabel = '';
        if ($modeIconType === 'map-marker' && $meetingLink !== '') {
            $ilType = 'address';
            $ilLabel = 'View Address';
        } elseif ($isUrl) {
            $ilType = 'link';
            $ilLabel = 'Interview Link';
        }

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

        // Feedback View Presentation (Read-Only)
        $resultKey = $source['interview_result'] ?? null;
        $recommendationKey = $source['overall_recommendation'] ?? null;

        $feedbackText = 'Pending';
        $feedbackColor = '#f59e0b'; // orange/amber
        $feedbackIcon = 'clock';

        if ($availableStatus === 1) { // Attended
            if ($resultKey === 'selected' || $feedbackCode === 1) {
                $feedbackText = 'Selected';
                $feedbackColor = '#10b981';
                $feedbackIcon = 'check';
            } elseif ($resultKey === 'rejected' || $resultKey === 'fake_proxy' || in_array($feedbackCode, [3, 4], true)) {
                $feedbackText = ($resultKey === 'fake_proxy' || $feedbackCode === 4) ? 'Fake / Proxy' : 'Rejected';
                $feedbackColor = '#ef4444';
                $feedbackIcon = 'times';
            } elseif ($resultKey === 'hold' || $feedbackCode === 2) {
                $feedbackText = 'Hold';
                $feedbackColor = '#f59e0b';
                $feedbackIcon = 'exclamation-circle';
            } else {
                $feedbackText = 'Feedback Pending';
                $feedbackColor = '#f59e0b';
                $feedbackIcon = 'clock';
            }
        } elseif ($availableStatus === 2) {
            $feedbackText = 'No Show';
            $feedbackColor = '#f59e0b';
            $feedbackIcon = 'exclamation-triangle';
        } elseif ($availableStatus === 3) {
            $feedbackText = 'Rescheduled';
            $feedbackColor = '#3b82f6';
            $feedbackIcon = 'history';
        } elseif ($availableStatus === 4 || $availableStatus === 7) {
            $feedbackText = 'Cancelled';
            $feedbackColor = '#ef4444';
            $feedbackIcon = 'times-circle';
        } else {
            $feedbackText = 'Pending';
            $feedbackColor = '#f59e0b';
            $feedbackIcon = 'clock';
        }

        $recLabels = [
            'strong_hire' => ['label' => 'Strong Hire', 'bg' => '#dcfce7', 'color' => '#16a34a'],
            'hire' => ['label' => 'Hire', 'bg' => '#dcfce7', 'color' => '#16a34a'],
            'consider' => ['label' => 'Consider', 'bg' => '#fef3c7', 'color' => '#d97706'],
            'no_hire' => ['label' => 'No Hire', 'bg' => '#fee2e2', 'color' => '#dc2626'],
            'strong_no_hire' => ['label' => 'Strong No Hire', 'bg' => '#fee2e2', 'color' => '#dc2626'],
        ];
        $recommendation = $recommendationKey && isset($recLabels[$recommendationKey]) ? $recLabels[$recommendationKey] : null;

        // Action Eligibility Computation (Server-Authoritative Time Windows)
        $now = Carbon::now('UTC');
        $joinAllowed = false;
        $joinWindowState = 'JOIN_WINDOW_CLOSED';
        $joinWindow = null;

        if ($scheduleCarbon && ! empty($meetingLink) && in_array($availableStatus, [0, 3], true)) {
            $windowStart = $scheduleCarbon->copy()->subMinutes(15);
            $windowEnd = $scheduleCarbon->copy()->addMinutes(60);

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
        $leadName = 'N/A';
        $leadCompany = trim((string) ($source['uc_company_name'] ?? ($source['company_name'] ?? 'Panel')));
        $extraCount = 0;

        if (! empty($rawInterviewers)) {
            $rawList = is_array($rawInterviewers)
                ? $rawInterviewers
                : array_filter(array_map('trim', explode(',', (string) $rawInterviewers)));

            foreach ($rawList as $idx => $intvItem) {
                if (empty($intvItem)) {
                    continue;
                }
                $nameStr = '';
                $itemComp = $companyNameDefault;
                if (is_array($intvItem)) {
                    $nameStr = trim((string) ($intvItem['name'] ?? ($intvItem['interviewer_name'] ?? '')));
                    $itemComp = trim((string) ($intvItem['company_name'] ?? ($intvItem['company'] ?? $companyNameDefault)));
                } else {
                    $nameStr = trim((string) $intvItem);
                }

                if (empty($nameStr)) {
                    continue;
                }

                $interviewers[] = [
                    'id' => $idx + 1,
                    'name' => ucwords($nameStr),
                    'company_name' => $itemComp ?: $companyNameDefault,
                ];
            }

            if (! empty($interviewers)) {
                $leadName = $interviewers[0]['name'];
                $leadCompany = $interviewers[0]['company_name'] ?: $leadCompany;
                $extraCount = max(0, count($interviewers) - 1);
            }
        }

        // Source (Recruiter / Spokperson)
        $sourceName = trim((string) ($source['submitted_by_name'] ?? ($source['posted_by'] ?? ($source['spokperson_name'] ?? ($source['source'] ?? '—')))));
        $sourceCompany = trim((string) ($source['submitted_by_company_name'] ?? ($source['post_company_name'] ?? ($source['from_company_name'] ?? ($source['uc_company_name'] ?? ($source['company_name'] ?? ''))))));
        $createdAtRaw = $source['created_at'] ?? null;
        $submittedAt = 'Recently';
        if ($createdAtRaw) {
            try {
                $submittedAt = Carbon::parse($createdAtRaw)->diffForHumans();
            } catch (Throwable $e) {
                $submittedAt = (string) $createdAtRaw;
            }
        }

        // Master & Child Status / Stage
        $masterStatusName = trim((string) ($source['master_status_name'] ?? 'In Review'));
        $childStatusName = trim((string) ($source['child_status_name'] ?? ''));
        $masterStatusId = (int) ($source['master_status_id'] ?? 0);
        if ($masterStatusId === 8 && empty($childStatusName)) {
            $childStatusName = 'Private';
        }
        $cssStyle = trim((string) ($source['css_style'] ?? 'badge-light-primary'));

        $stageLower = strtolower($masterStatusName);
        $stageBadgeClass = 'cd-stage-interview it-badge-blue';
        if (str_contains($stageLower, 'review') || str_contains($stageLower, 'inw')) {
            $stageBadgeClass = 'cd-stage-in-review it-badge-yellow';
        } elseif (str_contains($stageLower, 'sourc') || str_contains($stageLower, 'sou')) {
            $stageBadgeClass = 'cd-stage-sourcing it-badge-gray';
        } elseif (str_contains($stageLower, 'screen') || str_contains($stageLower, 'scr')) {
            $stageBadgeClass = 'cd-stage-screening it-badge-purple';
        } elseif (str_contains($stageLower, 'evaluat') || str_contains($stageLower, 'eva')) {
            $stageBadgeClass = 'cd-stage-interview it-badge-blue';
        } elseif (str_contains($stageLower, 'negot') || str_contains($stageLower, 'neg')) {
            $stageBadgeClass = 'cd-stage-screening it-badge-pink';
        } elseif (str_contains($stageLower, 'offer') || str_contains($stageLower, 'off')) {
            $stageBadgeClass = 'cd-stage-offer it-badge-orange';
        } elseif (str_contains($stageLower, 'join') || str_contains($stageLower, 'joi')) {
            $stageBadgeClass = 'cd-stage-joined it-badge-green';
        } elseif (str_contains($stageLower, 'reject') || str_contains($stageLower, 'rej')) {
            $stageBadgeClass = 'cd-stage-rejected it-badge-red';
        }

        $candidateCurrentCompany = trim((string) ($source['current_company'] ?? ($source['candidate_company'] ?? ($source['candidate_current_company'] ?? ''))));

        return [
            'id' => $id,
            'candidate' => [
                'id' => (int) ($source['candidate_id'] ?? ($source['condidate_id'] ?? 0)),
                'application_id' => (int) ($source['job_application_id'] ?? ($source['application_id'] ?? ($source['wiseId'] ?? 0))),
                'name' => $fullName,
                'initial' => $initial,
                'designation' => $designation,
                'avatar_color_class' => $avatarColorClass,
                'current_company' => $candidateCurrentCompany ?: null,
                'email' => trim((string) ($source['email'] ?? '')),
                'phone' => trim((string) ($source['phone'] ?? '')),
            ],
            'followup' => [
                'status' => $fuStatus,
                'label' => $fuLabel,
                'style_class' => $fuStyleClass,
                'formatted_at' => $fuFormattedAt,
            ],
            'job' => [
                'id' => $jobId,
                'title' => $jobTitle,
                'client_name' => $clientName,
                'display_company_name' => $displayCompName,
                'job_type_label' => $jobTypeLabel,
                'job_type_class' => $jobTypeClass,
                'job_id_display' => $jobId ? "#{$jobId}" : '',
            ],
            'schedule' => [
                'date_raw' => $scheduleDateRaw,
                'date_formatted' => $dateFormatted,
                'time_formatted' => $timeFormatted,
                'mode_icon' => $modeIconLegacy,
                'mode_icon_type' => $modeIconType,
                'mode_label' => $modeDisplayLabel,
                'round_name' => $roundName,
                'meeting_link' => $meetingLink,
                'link_type' => $ilType,
                'link_label' => $ilLabel,
            ],
            'interviewers' => $interviewers,
            'interviewers_summary' => [
                'lead_name' => $leadName,
                'lead_company' => $leadCompany,
                'extra_count' => $extraCount,
                'extra_label' => $extraCount > 0 ? "+{$extraCount}" : '',
            ],
            'source_info' => [
                'name' => $sourceName,
                'company_name' => $sourceCompany,
                'submitted_at' => $submittedAt,
            ],
            'attendance' => $attendance,
            'evaluation' => $evaluation,
            'feedback_view' => [
                'status_text' => $feedbackText,
                'status_color' => $feedbackColor,
                'status_icon' => $feedbackIcon,
                'recommendation' => $recommendation,
            ],
            'actions' => [
                'join_allowed' => $joinAllowed,
                'join_window_state' => $joinWindowState,
                'join_window' => $joinWindow,
                'feedback_view_allowed' => $feedbackViewAllowed,
                'feedback_submit_allowed' => $feedbackSubmitAllowed,
                'reschedule_allowed' => $rescheduleAllowed,
            ],
            'stage' => [
                'master_status_name' => $masterStatusName,
                'child_status_name' => $childStatusName,
                'css_style' => $cssStyle,
                'badge_class' => $stageBadgeClass,
            ],
        ];
    }

    /**
     * Batch transform an array of Elasticsearch hits into a collection of Read Models.
     *
     * @param  array  $hits  Elasticsearch hits array
     * @param  int|null  $authCompanyId  Authenticated tenant company ID
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
