<?php

namespace App\Services\Interview\DTO;

/**
 * Class InterviewStatusTransitionResult
 * 
 * Intent: Data transfer object encapsulating the result of an interview feedback attendance status transition.
 */
class InterviewStatusTransitionResult
{
    public bool $success;
    public string $message;
    public int $interviewId;
    public int $previousStatus;
    public int $newStatus;
    public float $recruiterScheduledDelta;
    public float $recruiterNoShowDelta;
    public float $teamCaptainScheduledDelta;
    public ?int $feedbackId;

    public function __construct(
        bool $success,
        string $message,
        int $interviewId = 0,
        int $previousStatus = 0,
        int $newStatus = 0,
        float $recruiterScheduledDelta = 0.0,
        float $recruiterNoShowDelta = 0.0,
        float $teamCaptainScheduledDelta = 0.0,
        ?int $feedbackId = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->interviewId = $interviewId;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->recruiterScheduledDelta = $recruiterScheduledDelta;
        $this->recruiterNoShowDelta = $recruiterNoShowDelta;
        $this->teamCaptainScheduledDelta = $teamCaptainScheduledDelta;
        $this->feedbackId = $feedbackId;
    }
}
