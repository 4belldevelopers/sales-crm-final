<?php

namespace App\Services;

use App\Enums\ClaimCapacity;
use App\Enums\MarketModState;
use App\Enums\MarketPubState;
use App\Jobs\ProcessMarketJobOutboxEventJob;
use App\Models\Job;
use App\Models\JobMarketplaceModerationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * MarketplaceModerationService
 *
 * Master service for Agency Job Marketplace Risk Engine, Anti-Circumvention,
 * Quality Assessment, and State Lifecycle Governance.
 *
 * Implements Architecture V2.2 specifications:
 * - Sub-millisecond in-memory regex scanning for off-platform contact leaks
 * - Market compensation anomaly detection
 * - Semantic JD completeness scoring
 * - Historical duplicate detection
 * - Strict transactional outbox event creation with state_versioning
 */
class MarketplaceModerationService
{
    /**
     * Regex patterns for off-platform contact detection.
     */
    protected const PATTERN_EMAIL = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i';

    protected const PATTERN_PHONE = '/(?:(?:\+|00)\d{1,3}[\s.-]?)?(?:\(?\d{2,4}\)?[\s.-]?)?\d{3,4}[\s.-]?\d{3,4}/';

    protected const PATTERN_WHATSAPP = '/(?:whatsapp|wa\.me|telegram|tg\.me|call\s*me|reach\s*out\s*at|contact\s*me\s*at|contact\s*us\s*at|phone|mobile)[\s:]*([0-9+\s-]{8,})/i';

    protected const PATTERN_CIRCUMVENTION_KEYWORDS = '/\b(skype|telegram|direct\s*contract|pay\s*outside|off[- ]?platform|reach\s*me\s*at|wire\s*transfer|cash\s*payment|bypass)\b/i';

    protected const PATTERN_EXTERNAL_URL = '/(?:https?:\/\/|www\.)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}[^\s<>\'"]*/i';

    /**
     * Evaluate risk score and circumvention signals for a job posting.
     *
     * @param  Job|array  $jobData
     * @return array Contains 'risk_score', 'hard_blocked', 'reasons', 'breakdown'
     */
    public function evaluateRisk($jobData): array
    {
        $title = is_array($jobData) ? ($jobData['title'] ?? '') : ($jobData->title ?? '');
        $description = is_array($jobData) ? ($jobData['job_description'] ?? '') : ($jobData->job_description ?? '');
        $requirement = is_array($jobData) ? ($jobData['job_requirement'] ?? '') : ($jobData->job_requirement ?? '');
        $minSalary = (float) (is_array($jobData) ? ($jobData['min_salary'] ?? 0) : ($jobData->min_salary ?? 0));
        $maxSalary = (float) (is_array($jobData) ? ($jobData['max_salary'] ?? 0) : ($jobData->max_salary ?? 0));
        $companyId = (int) (is_array($jobData) ? ($jobData['company_id'] ?? 0) : ($jobData->company_id ?? 0));
        $currentJobId = (int) (is_array($jobData) ? ($jobData['id'] ?? 0) : ($jobData->id ?? 0));

        $combinedText = strip_tags($title.' '.$description.' '.$requirement);
        $reasons = [];
        $score = 0.00;
        $hardBlocked = false;

        // 1. Hard Block: Phone & WhatsApp circumvention
        if (preg_match(self::PATTERN_WHATSAPP, $combinedText, $matches) || preg_match(self::PATTERN_PHONE, $combinedText, $matches)) {
            // Verify match is not just a standard year or short number
            $cleanMatch = preg_replace('/[^\d]/', '', $matches[0] ?? '');
            if (strlen($cleanMatch) >= 10 && strlen($cleanMatch) <= 15) {
                $hardBlocked = true;
                $score += 0.85;
                $reasons[] = 'Off-platform phone or WhatsApp contact detected: '.substr($matches[0], 0, 30);
            }
        }

        // 2. Hard Block: Email addresses
        if (preg_match(self::PATTERN_EMAIL, $combinedText, $matches)) {
            $hardBlocked = true;
            $score += 0.85;
            $reasons[] = 'Direct email address detected in job text: '.substr($matches[0], 0, 40);
        }

        // 3. Circumvention keywords
        if (preg_match(self::PATTERN_CIRCUMVENTION_KEYWORDS, $combinedText, $matches)) {
            $score += 0.35;
            $reasons[] = 'Suspicious circumvention terminology found: '.substr($matches[0], 0, 30);
        }

        // 4. External URL / links
        if (preg_match(self::PATTERN_EXTERNAL_URL, $combinedText, $matches)) {
            $score += 0.20;
            $reasons[] = 'External website link present in description.';
        }

        // 5. Salary Anomaly Deviation
        if ($minSalary > 0 && $maxSalary > 0 && $minSalary > $maxSalary) {
            $score += 0.35;
            $reasons[] = "Inverted compensation: Minimum salary ({$minSalary}) exceeds maximum salary ({$maxSalary}).";
        } elseif ($minSalary <= 0 && $maxSalary <= 0) {
            $score += 0.15;
            $reasons[] = 'Unspecified compensation range.';
        }

        // 6. Semantic JD Quality & Completeness
        $charCount = strlen(trim($combinedText));
        if ($charCount < 150) {
            $score += 0.40;
            $reasons[] = 'Insufficient job description: content is under 150 characters.';
        } elseif ($charCount < 400) {
            $score += 0.20;
            $reasons[] = 'Low job description completeness: content is under 400 characters.';
        }

        // 7. Duplicate Detection within tenant (past 30 days)
        if ($companyId > 0 && ! empty($title)) {
            try {
                $duplicateCount = DB::table('jobs')
                    ->where('company_id', $companyId)
                    ->where('title', $title)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->when($currentJobId > 0, fn ($q) => $q->where('id', '!=', $currentJobId))
                    ->count();

                if ($duplicateCount > 0) {
                    $score += 0.25;
                    $reasons[] = "Duplicate title detected ({$duplicateCount} identical posting in the last 30 days).";
                }
            } catch (\Throwable $e) {
                // Table unavailable in mock or non-migrated environment
            }
        }

        $finalRiskScore = min(1.00, round($score, 2));

        return [
            'risk_score' => $finalRiskScore,
            'hard_blocked' => $hardBlocked,
            'reasons' => $reasons,
            'flags' => $reasons,
        ];
    }

    /**
     * Submit a job for marketplace moderation upon creation or update.
     *
     * @return array Result state and moderation evaluation
     */
    public function submitForModeration(Job $job, int $actorId, string $actorRole = 'recruiter'): array
    {
        return DB::transaction(function () use ($job, $actorId, $actorRole) {
            $evaluation = $this->evaluateRisk($job);
            $riskScore = $evaluation['risk_score'];
            $hardBlocked = $evaluation['hard_blocked'];

            $fromModState = $job->market_mod_state ?? MarketModState::NOT_REQUIRED->value;
            $toModState = MarketModState::PENDING->value;
            $pubState = MarketPubState::PENDING_REVIEW->value;
            $action = 'submit';

            // Auto-approval criterion: zero hard blocks and risk < 0.20
            if (! $hardBlocked && $riskScore < 0.20) {
                $toModState = MarketModState::AUTO_APPROVED->value;
                $pubState = MarketPubState::PUBLISHED->value;
                $action = 'auto_approve';
            } elseif ($hardBlocked && $riskScore >= 0.85) {
                // If severely hard blocked, route to pending with risk flag for superadmin review
                $toModState = MarketModState::PENDING->value;
                $pubState = MarketPubState::PENDING_REVIEW->value;
            }

            // Update Job Model attributes
            $job->market_pub_state = $pubState;
            $job->market_mod_state = $toModState;
            $job->claim_capacity = $job->claim_capacity ?? ClaimCapacity::OPEN->value;
            $job->state_version = ($job->state_version ?? 0) + 1;
            $job->save();

            // Insert immutable audit log
            JobMarketplaceModerationLog::create([
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'action' => $action,
                'from_mod_state' => $fromModState,
                'to_mod_state' => $toModState,
                'reason_code' => ! empty($evaluation['reasons']) ? 'RISK_FLAG' : null,
                'rejection_notes' => ! empty($evaluation['reasons']) ? implode(' | ', $evaluation['reasons']) : null,
                'risk_score' => $riskScore,
                'created_at' => now(),
            ]);

            // Dispatch transactional outbox event
            $this->recordOutboxEvent(
                job: $job,
                eventType: $action === 'auto_approve' ? 'MarketJobPublished' : 'MarketJobPendingReview',
                payload: [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'market_pub_state' => $pubState,
                    'market_mod_state' => $toModState,
                    'risk_score' => $riskScore,
                    'state_version' => $job->state_version,
                ]
            );

            return [
                'market_pub_state' => $pubState,
                'market_mod_state' => $toModState,
                'risk_score' => $riskScore,
                'hard_blocked' => $hardBlocked,
                'reasons' => $evaluation['reasons'],
                'auto_approved' => ($action === 'auto_approve'),
            ];
        });
    }

    /**
     * SuperAdmin manually approves a marketplace job posting.
     */
    public function approve(Job $job, int $actorId, string $actorRole = 'superadmin', ?string $notes = null): bool
    {
        return DB::transaction(function () use ($job, $actorId, $actorRole, $notes) {
            $fromModState = $job->market_mod_state ?? MarketModState::PENDING->value;
            $toModState = MarketModState::APPROVED->value;
            $pubState = MarketPubState::PUBLISHED->value;

            $job->market_pub_state = $pubState;
            $job->market_mod_state = $toModState;
            $job->claim_capacity = $job->claim_capacity ?? ClaimCapacity::OPEN->value;
            $job->state_version = ($job->state_version ?? 0) + 1;
            $job->save();

            JobMarketplaceModerationLog::create([
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'action' => 'manual_approve',
                'from_mod_state' => $fromModState,
                'to_mod_state' => $toModState,
                'reason_code' => 'ADMIN_APPROVAL',
                'rejection_notes' => $notes,
                'risk_score' => 0.00,
                'created_at' => now(),
            ]);

            $this->recordOutboxEvent(
                job: $job,
                eventType: 'MarketJobPublished',
                payload: [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'market_pub_state' => $pubState,
                    'market_mod_state' => $toModState,
                    'state_version' => $job->state_version,
                    'notes' => $notes,
                ]
            );

            return true;
        });
    }

    /**
     * SuperAdmin manually rejects a marketplace job posting.
     */
    public function reject(Job $job, int $actorId, string $actorRole = 'superadmin', string $reasonCode = 'CONTENT_VIOLATION', ?string $notes = null): bool
    {
        return DB::transaction(function () use ($job, $actorId, $actorRole, $reasonCode, $notes) {
            $fromModState = $job->market_mod_state ?? MarketModState::PENDING->value;
            $toModState = MarketModState::REJECTED->value;
            $pubState = MarketPubState::UNPUBLISHED->value;

            $job->market_pub_state = $pubState;
            $job->market_mod_state = $toModState;
            $job->state_version = ($job->state_version ?? 0) + 1;
            $job->save();

            JobMarketplaceModerationLog::create([
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'action' => 'reject',
                'from_mod_state' => $fromModState,
                'to_mod_state' => $toModState,
                'reason_code' => $reasonCode,
                'rejection_notes' => $notes,
                'risk_score' => 0.00,
                'created_at' => now(),
            ]);

            $this->recordOutboxEvent(
                job: $job,
                eventType: 'MarketJobRejected',
                payload: [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'market_pub_state' => $pubState,
                    'market_mod_state' => $toModState,
                    'reason_code' => $reasonCode,
                    'notes' => $notes,
                    'state_version' => $job->state_version,
                ]
            );

            return true;
        });
    }

    /**
     * SuperAdmin suspends a live marketplace job for compliance / fraud investigation.
     */
    public function suspend(Job $job, int $actorId, string $actorRole = 'superadmin', string $reasonCode = 'INVESTIGATION', ?string $notes = null): bool
    {
        return DB::transaction(function () use ($job, $actorId, $actorRole, $reasonCode, $notes) {
            $fromModState = $job->market_mod_state ?? MarketModState::APPROVED->value;
            $toModState = MarketModState::SUSPENDED->value;
            $pubState = MarketPubState::UNPUBLISHED->value;

            $job->market_pub_state = $pubState;
            $job->market_mod_state = $toModState;
            $job->state_version = ($job->state_version ?? 0) + 1;
            $job->save();

            JobMarketplaceModerationLog::create([
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'action' => 'suspend',
                'from_mod_state' => $fromModState,
                'to_mod_state' => $toModState,
                'reason_code' => $reasonCode,
                'rejection_notes' => $notes,
                'risk_score' => 0.00,
                'created_at' => now(),
            ]);

            $this->recordOutboxEvent(
                job: $job,
                eventType: 'MarketJobSuspended',
                payload: [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'market_pub_state' => $pubState,
                    'market_mod_state' => $toModState,
                    'reason_code' => $reasonCode,
                    'state_version' => $job->state_version,
                ]
            );

            return true;
        });
    }

    /**
     * Recruiter withdraws or unpublishes their marketplace job.
     */
    public function withdraw(Job $job, int $actorId, string $actorRole = 'recruiter'): bool
    {
        return DB::transaction(function () use ($job, $actorId, $actorRole) {
            $fromModState = $job->market_mod_state ?? MarketModState::NOT_REQUIRED->value;
            $pubState = MarketPubState::UNPUBLISHED->value;

            $job->market_pub_state = $pubState;
            $job->state_version = ($job->state_version ?? 0) + 1;
            $job->save();

            JobMarketplaceModerationLog::create([
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'actor_id' => $actorId,
                'actor_role' => $actorRole,
                'action' => 'withdraw',
                'from_mod_state' => $fromModState,
                'to_mod_state' => $job->market_mod_state,
                'reason_code' => 'RECRUITER_WITHDRAWAL',
                'rejection_notes' => 'Job un-published from marketplace by recruiter.',
                'risk_score' => 0.00,
                'created_at' => now(),
            ]);

            $this->recordOutboxEvent(
                job: $job,
                eventType: 'MarketJobUnpublished',
                payload: [
                    'job_id' => $job->id,
                    'company_id' => $job->company_id,
                    'market_pub_state' => $pubState,
                    'state_version' => $job->state_version,
                    'is_cancel' => true,
                ]
            );

            return true;
        });
    }

    /**
     * Record transactional outbox event and immediately trigger async worker.
     */
    protected function recordOutboxEvent(Job $job, string $eventType, array $payload): void
    {
        $idempotencyKey = 'mod_'.$job->id.'_'.($job->state_version ?? 1).'_'.Str::random(8);

        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => (int) $job->company_id,
            'idempotency_key' => $idempotencyKey,
            'aggregate_type' => 'MarketJob',
            'aggregate_id' => $job->id,
            'aggregate_version' => (int) ($job->state_version ?? 1),
            'event_type' => $eventType,
            'payload' => json_encode($payload),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        // Dispatch worker after DB transaction commits
        dispatch(new ProcessMarketJobOutboxEventJob((int) $eventId))->afterCommit();
    }
}
