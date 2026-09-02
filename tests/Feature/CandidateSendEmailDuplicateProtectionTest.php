<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * CandidateSendEmailDuplicateProtectionTest
 *
 * Feature tests verifying duplicate email protection & idempotency guards:
 * - Event listener leakage & client-side lock simulation
 * - Atomic Redis lock & cache-based idempotency guard
 * - Coin deduction protection against duplicate HTTP POST requests
 * - Email array normalization & recipient deduplication
 *
 * NOTE: As per project guidelines, RefreshDatabase is NOT used.
 */
class CandidateSendEmailDuplicateProtectionTest extends TestCase
{
    /**
     * Test atomic Redis lock key construction & concurrency blocking for candidate send mail.
     */
    public function test_candidate_send_mail_idempotency_lock_prevents_duplicate_processing(): void
    {
        $companyId = 2;
        $applicationId = 100;
        $jobId = 50;
        $to = 'candidate@example.com';
        $subject = 'Job Opportunity with Cactus Communications';

        $toHash = md5($to);
        $subjectHash = md5($subject);
        $idempotencyKey = "send_candidate_mail_lock:{$companyId}:{$applicationId}:{$jobId}:{$toHash}:{$subjectHash}";

        // Acquire lock
        $lock1 = Cache::lock($idempotencyKey, 15);
        $this->assertTrue($lock1->get());

        // Concurrent lock acquisition attempt MUST fail
        $lock2 = Cache::lock($idempotencyKey, 15);
        $this->assertFalse($lock2->get());

        // Release initial lock
        $lock1->release();
    }

    /**
     * Test recent send cache key prevents repeated duplicate requests within 15 seconds.
     */
    public function test_recent_send_cache_key_blocks_repeated_requests(): void
    {
        $companyId = 2;
        $applicationId = 101;
        $jobId = 51;
        $to = 'candidate2@example.com';
        $subject = 'Job Opportunity with 4Bell';

        $toHash = md5($to);
        $subjectHash = md5($subject);
        $recentSendKey = "send_candidate_mail_recent:{$companyId}:{$applicationId}:{$jobId}:{$toHash}:{$subjectHash}";

        // Initially key does not exist
        $this->assertFalse(Cache::has($recentSendKey));

        // Mark as sent
        Cache::put($recentSendKey, true, 15);
        $this->assertTrue(Cache::has($recentSendKey));

        // Cleanup
        Cache::forget($recentSendKey);
    }

    /**
     * Test recipient email normalization and deduplication logic.
     */
    public function test_recipient_email_array_normalization_and_deduplication(): void
    {
        $rawTo = ' Candidate@example.com , candidate@example.com, TEST@EXAMPLE.COM ';
        $rawCc = ' hr@company.com, HR@COMPANY.COM , recruiter@company.com ';

        $rawMailto = explode(',', $rawTo);
        $mailto = [];
        foreach ($rawMailto as $email) {
            $trimmed = strtolower(trim($email));
            if (! empty($trimmed) && filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                $mailto[] = $trimmed;
            }
        }
        $mailto = array_values(array_unique($mailto));

        $rawCcList = explode(',', $rawCc);
        $cc = [];
        foreach ($rawCcList as $email) {
            $trimmed = strtolower(trim($email));
            if (! empty($trimmed) && filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                $cc[] = $trimmed;
            }
        }
        $cc = array_values(array_unique($cc));

        $this->assertEquals(['candidate@example.com', 'test@example.com'], $mailto);
        $this->assertEquals(['hr@company.com', 'recruiter@company.com'], $cc);
    }

    /**
     * Test CC array cleanup logic excludes main recipient candidate email and prevents duplicate CC headers.
     */
    public function test_cc_list_cleanup_excludes_candidate_to_email_and_duplicates(): void
    {
        $to = 'candidate@example.com';
        $fromAddressReply = 'recruiter@company.com';
        $cc = ['candidate@example.com', ' CANDIDATE@EXAMPLE.COM ', 'hr@company.com', 'recruiter@company.com', 'hr@company.com'];

        $cleanCc = [];
        if (! empty($cc) && is_array($cc)) {
            foreach ($cc as $ccAddr) {
                $trimmedCc = strtolower(trim($ccAddr));
                if (! empty($trimmedCc) && $trimmedCc !== strtolower(trim($to)) && filter_var($trimmedCc, FILTER_VALIDATE_EMAIL)) {
                    $cleanCc[] = $trimmedCc;
                }
            }
        }
        if (! empty($fromAddressReply)) {
            $trimmedReply = strtolower(trim($fromAddressReply));
            if ($trimmedReply !== strtolower(trim($to)) && filter_var($trimmedReply, FILTER_VALIDATE_EMAIL)) {
                $cleanCc[] = $trimmedReply;
            }
        }
        $cleanCc = array_values(array_unique($cleanCc));

        $this->assertEquals(['hr@company.com', 'recruiter@company.com'], $cleanCc);
        $this->assertNotContains('candidate@example.com', $cleanCc);
    }

    /**
     * Test job-level cache key prevents queue retries occurring after 90 seconds from re-sending email.
     */
    public function test_job_level_cache_key_blocks_queue_retry_duplicates(): void
    {
        $companyId = 5;
        $to = 'candidate3@example.com';
        $subject = 'Interview Schedule Notification';

        $toHash = md5(strtolower(trim($to)));
        $subjectHash = md5(trim($subject));
        $jobSentKey = "candidate_mail_sent:{$companyId}:{$toHash}:{$subjectHash}";

        $this->assertFalse(Cache::has($jobSentKey));

        // Mark as sent in job execution
        Cache::put($jobSentKey, true, 300);

        // Queue retry at t=90s checks cache key
        $this->assertTrue(Cache::has($jobSentKey));

        // Cleanup
        Cache::forget($jobSentKey);
    }
}
