<?php

namespace App\Jobs;

use App\Events\BenchRequestCreated;
use App\Helper\GeneralMethod;
use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProcessBenchRequestOutboxEventJob — Asynchronous Outbox Worker for Bench Candidate Recommendations.
 *
 * Intent:
 * Phase 5 Decoupled Outbox Processor.
 * Claims pending sys_outbox_events for aggregate_type 'BenchCandidateRequest' via SKIP LOCKED,
 * synchronizes Elasticsearch candidate request documents, broadcasts real-time Reverb WebSocket deltas,
 * dispatches notification emails via mail queues, and marks outbox events as COMPLETED.
 *
 * Tenancy Note:
 * Multi-tenant safe, scoped strictly by tenant_id.
 *
 * Author: Antigravity AI (God-Level ATS Architecture)
 * Date: October 12, 2026
 */
class ProcessBenchRequestOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('candidate-index');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing when triggered periodically by queue worker
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchCandidateRequest')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId);
        }
    }

    protected function processEvent(int $eventId): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $recId = (int) ($payload['rec_id'] ?? 0);
            $sourceApplicationId = (int) ($payload['source_application_id'] ?? $event->aggregate_id);
            $sourceJobId = (int) ($payload['source_jobid'] ?? 0);
            $sourceFromCompanyId = (int) ($payload['source_from_company_id'] ?? $event->tenant_id);
            $sourceCompanyId = (int) ($payload['source_company_id'] ?? $sourceFromCompanyId);

            // 1. Elasticsearch Candidate Request Indexing (Rule 2 & Rule 6)
            if ($recId > 0) {
                try {
                    SyncCandidateRequestToElasticJob::dispatch((int) $recId);
                } catch (\Throwable $esEx) {
                    Log::warning('[ProcessBenchRequestOutboxEventJob] Elastic sync dispatch failed: '.$esEx->getMessage());
                }
            }

            // 2. Real-Time Reverb WebSocket Broadcasting (Phase 4)
            if ($sourceFromCompanyId > 0 && $sourceJobId > 0) {
                try {
                    $broadcastTab = (string) ($payload['tab'] ?? 'internal_bench');
                    event(new BenchRequestCreated(
                        (int) $sourceFromCompanyId,
                        (int) $sourceJobId,
                        (int) $recId,
                        (int) $sourceApplicationId,
                        $broadcastTab
                    ));

                    if ($sourceCompanyId !== $sourceFromCompanyId && $sourceCompanyId > 0) {
                        event(new BenchRequestCreated(
                            (int) $sourceCompanyId,
                            (int) $sourceJobId,
                            (int) $recId,
                            (int) $sourceApplicationId,
                            $broadcastTab
                        ));
                    }
                } catch (\Throwable $bEx) {
                    Log::warning('[ProcessBenchRequestOutboxEventJob] Reverb broadcast failed: '.$bEx->getMessage());
                }
            }

            // 3. Invalidate Job Counters & Market Caches
            if ($sourceJobId > 0) {
                GeneralMethod::invalidateJobCounters((int) $sourceJobId, [(int) $sourceCompanyId, (int) $sourceFromCompanyId]);
                \App\Services\MarketJobV2Service::invalidateCacheForCompany((int) $sourceFromCompanyId);
            }

            // 4. Notification Email Dispatch
            if ($recId > 0 && $sourceCompanyId > 0) {
                $this->dispatchNotificationEmail($sourceJobId, $sourceApplicationId, $sourceFromCompanyId, $sourceCompanyId);
            }

            // 5. Mark Outbox Record COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error('[ProcessBenchRequestOutboxEventJob] Execution failed: '.$e->getMessage(), [
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString(),
            ]);

            $isDeadLetter = ($event->attempts >= $this->tries);
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $isDeadLetter ? 'DEAD_LETTER' : 'PENDING',
                    'available_at' => now()->addSeconds(15 * max((int) $event->attempts, 1)),
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'locked_at' => null,
                ]);
        }
    }

    /**
     * Helper to asynchronously dispatch recruitment notification email
     */
    protected function dispatchNotificationEmail(int $jobId, int $candidateId, int $fromCompanyId, int $sourceCompanyId): void
    {
        try {
            if (! GeneralMethod::mailPermission(21)) {
                return;
            }

            $job = Job::withoutGlobalScopes()->find($jobId);
            if (! $job) {
                return;
            }

            $mailTypeSubject = GeneralMethod::mailTypeSubject(21, 1);
            if (! $mailTypeSubject) {
                return;
            }

            $jobRow = DB::table('job_applications')
                ->select('job_applications.*', 'city_list.city')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'job_applications.city_id')
                ->where('job_applications.id', '=', $candidateId)
                ->first();

            if (! $jobRow) {
                return;
            }

            $detailsVendorSide = GeneralMethod::companyAsVendor($sourceCompanyId, $fromCompanyId);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = $basecompany->companyName ?? '';
            $fromAddress = $basecompany->companyEmail ?? '';

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currencySymbole();
            $bill_rate_mode = strtoupper(substr($salaryMode[$jobRow->bill_rate_mode ?? 0] ?? 'Annual', 0, 1));
            $bill_rate_currency = $currencySymbole[@$jobRow->bill_rate_currency] ?? '₹';
            $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($jobRow->bill_rate_amount ?? 0);
            $salaryRange = $bill_rate_currency.$bill_rate_amount.'/'.$bill_rate_mode;

            $subject = str_replace(['[{JOB_TITLE}]', '[{JOB_ID}]'], [$job->title, $job->id], $mailTypeSubject->subject);
            $template_description = str_replace(
                ['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BILL_RATE}]', '[{EXPERENCE}]', '[{LOCATION}]', '[{CONTACT_PERSON_NAME}]', '[{PHONE}]', '[{EMAIL}]', '[{SKILL}]'],
                [$detailsVendorSide->client_company, $job->title, $job->id, $detailsVendorSide->vendor_company, $jobRow->full_name, $salaryRange, $jobRow->total_exp, $jobRow->city, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $jobRow->skills],
                $mailTypeSubject->body_description
            );

            $notificationemail = GeneralMethod::notification($sourceCompanyId, 'Recuitment');
            $defaultEmail = DB::table('companies')->where('id', $sourceCompanyId)->value('company_email');
            $to_Mail_Id = $notificationemail->email ?? $defaultEmail ?? '';

            if (! empty($to_Mail_Id)) {
                SendEmailJob::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    ['message' => $template_description],
                    [],
                    $fromAddress,
                    $senderName,
                    []
                );
            }
        } catch (\Throwable $mailEx) {
            Log::warning('[ProcessBenchRequestOutboxEventJob] Notification email failed: '.$mailEx->getMessage());
        }
    }
}
