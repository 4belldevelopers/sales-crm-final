<?php
// Intent: Verify the ResumeUploadStatus state machine transitions are correct.
// All 8 statuses covered; terminal states cannot transition out.

namespace Tests\Unit;

use App\Enums\ResumeUploadStatus;
use PHPUnit\Framework\TestCase;

class ResumeUploadStatusTest extends TestCase
{
    public function test_pending_can_transition_to_valid_targets(): void
    {
        $status = ResumeUploadStatus::Pending;
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Uploading));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Uploaded));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Failed));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Cancelled));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Expired));
    }

    public function test_pending_cannot_transition_to_completed_directly(): void
    {
        $this->assertFalse(ResumeUploadStatus::Pending->canTransitionTo(ResumeUploadStatus::Completed));
        $this->assertFalse(ResumeUploadStatus::Pending->canTransitionTo(ResumeUploadStatus::Reconciled));
    }

    public function test_uploading_transitions(): void
    {
        $status = ResumeUploadStatus::Uploading;
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Uploaded));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Failed));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Cancelled));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Expired));
        $this->assertFalse($status->canTransitionTo(ResumeUploadStatus::Completed));
    }

    public function test_uploaded_transitions(): void
    {
        $status = ResumeUploadStatus::Uploaded;
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Completed));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Failed));
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Expired));
        $this->assertFalse($status->canTransitionTo(ResumeUploadStatus::Cancelled));
    }

    public function test_completed_is_terminal(): void
    {
        $status = ResumeUploadStatus::Completed;
        $this->assertTrue($status->isTerminal());
        foreach (ResumeUploadStatus::cases() as $target) {
            $this->assertFalse($status->canTransitionTo($target));
        }
    }

    public function test_failed_is_terminal(): void
    {
        $status = ResumeUploadStatus::Failed;
        $this->assertTrue($status->isTerminal());
        foreach (ResumeUploadStatus::cases() as $target) {
            $this->assertFalse($status->canTransitionTo($target));
        }
    }

    public function test_cancelled_is_terminal(): void
    {
        $status = ResumeUploadStatus::Cancelled;
        $this->assertTrue($status->isTerminal());
        foreach (ResumeUploadStatus::cases() as $target) {
            $this->assertFalse($status->canTransitionTo($target));
        }
    }

    public function test_expired_can_only_transition_to_reconciled(): void
    {
        $status = ResumeUploadStatus::Expired;
        $this->assertTrue($status->canTransitionTo(ResumeUploadStatus::Reconciled));
        $this->assertFalse($status->canTransitionTo(ResumeUploadStatus::Completed));
        $this->assertFalse($status->canTransitionTo(ResumeUploadStatus::Pending));
    }

    public function test_reconciled_is_fully_terminal(): void
    {
        $status = ResumeUploadStatus::Reconciled;
        $this->assertTrue($status->isTerminal());
        foreach (ResumeUploadStatus::cases() as $target) {
            $this->assertFalse($status->canTransitionTo($target));
        }
    }

    public function test_valid_statuses_returns_all_eight(): void
    {
        $statuses = ResumeUploadStatus::validStatuses();
        $this->assertCount(8, $statuses);
        $this->assertContains('Pending', $statuses);
        $this->assertContains('Completed', $statuses);
        $this->assertContains('Reconciled', $statuses);
    }
}
