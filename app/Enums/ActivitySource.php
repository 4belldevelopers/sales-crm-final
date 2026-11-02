<?php

namespace App\Enums;

/**
 * ActivitySource
 *
 * Backed Enum representing authoritative Elasticsearch event sources
 * in the Unified Activity Presentation & Read Engine.
 */
enum ActivitySource: string
{
    case CandidateTimeline = 'candidate_timeline';
    case JobsElastic = 'jobs_elastic';
    case InterviewTracker = 'interview_tracker';
    case ApplicantNotes = 'applicant_notes';
}
