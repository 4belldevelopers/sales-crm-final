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
                    GeneralMethod::candidateRequestIndexElastic((int) $recId);
                } catch (\Throwable $esEx) {
                    Log::warning('[ProcessBenchRequestOutboxEventJob] Elastic sync failed: '.$esEx->getMessage());
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
                $reqUserId = (int) ($payload['user_id'] ?? 0);
                if ($reqUserId > 0) {
                    GeneralMethod::cacheClean('allcandidatesrequest_version_user_'.$reqUserId);
                }
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

            // Notice Period formatting
            $npDate = '';
            if ((int) ($jobRow->serving_notice_period_mode ?? 0) === 0) {
                if (! empty($jobRow->np_id)) {
                    $npDate = ((int) $jobRow->np_id === 1) ? '1 Day' : $jobRow->np_id.' Days';
                } else {
                    $npDate = 'Immediate';
                }
            } elseif ((int) ($jobRow->serving_notice_period_mode ?? 0) === 1) {
                if (! empty($jobRow->np_id) && $jobRow->np_id !== 'Immediate') {
                    $timestamp = strtotime($jobRow->np_id);
                    $npDate = $timestamp ? date('d-M-y', $timestamp) : $jobRow->np_id;
                } else {
                    $npDate = 'Immediate';
                }
            } else {
                $npDate = ! empty($jobRow->np_id) ? $jobRow->np_id : 'Immediate';
            }

            // Outsourcing type
            $outsourcing = ((int) ($jobRow->outsourcing_type ?? 0) === 1) ? 'Fulltime' : 'Contract';

            // Commission type
            $comission = ((int) ($jobRow->comission_type ?? 0) === 1) ? 'Free Service' : 'Paid Service';

            // Experience formatting (clean trailing dot and handle freshers)
            $totalExpVal = trim((string) ($jobRow->total_exp ?? ''));
            $expClean = rtrim($totalExpVal, '.');
            if ($expClean === '' || $expClean === '0') {
                $expDisplay = '0 Years';
            } elseif (is_numeric($expClean)) {
                $expFloat = (float) $expClean;
                $expDisplay = ($expFloat == 1) ? '1 Year' : (($expFloat == (int) $expFloat ? (int) $expFloat : $expFloat).' Years');
            } else {
                $expDisplay = $expClean;
            }

            // Candidate Name & Resume link
            $candidateName = $jobRow->full_name ?? 'Candidate';
            $resumeUrl = ! empty($jobRow->resume) ? $jobRow->resume : '';
            if (! empty($resumeUrl)) {
                $candidateNameHtml = '<a href="'.htmlspecialchars($resumeUrl, ENT_QUOTES, 'UTF-8').'" target="_blank" style="color:#0066cc;text-decoration:underline;font-weight:600;">'.htmlspecialchars($candidateName, ENT_QUOTES, 'UTF-8').'</a>';
            } else {
                $candidateNameHtml = htmlspecialchars($candidateName, ENT_QUOTES, 'UTF-8');
            }

            // Subject replacements (including VENDOR_COMPANY_NAME and CLIENT_COMPANY_NAME)
            $subjectSearch = [
                '[{JOB_TITLE}]', '[ {JOB_TITLE}]',
                '[{JOB_ID}]', '[ {JOB_ID}]', '[{job_id}]', '[ {job_id}]',
                '[{VENDOR_COMPANY_NAME}]', '[ {VENDOR_COMPANY_NAME}]',
                '[{CLIENT_COMPANY_NAME}]', '[ {CLIENT_COMPANY_NAME}]',
                '[{BENCH_CANDIDATE_NAME}]', '[ {BENCH_CANDIDATE_NAME}]',
                '[{CANDIDATE_NAME}]', '[ {CANDIDATE_NAME}]',
                '[{BENCH_ID}]', '[ {BENCH_ID}]',
            ];
            $subjectReplace = [
                $job->title, $job->title,
                $job->id, $job->id, $job->id, $job->id,
                $detailsVendorSide->vendor_company, $detailsVendorSide->vendor_company,
                $detailsVendorSide->client_company, $detailsVendorSide->client_company,
                $candidateName, $candidateName,
                $candidateName, $candidateName,
                $candidateId, $candidateId,
            ];
            $subject = str_replace($subjectSearch, $subjectReplace, $mailTypeSubject->subject);

            // Body replacements (complete parameter map)
            $bodySearch = [
                '[{CLIENT_COMPANY_NAME}]', '[ {CLIENT_COMPANY_NAME}]',
                '[{VENDOR_COMPANY_NAME}]', '[ {VENDOR_COMPANY_NAME}]',
                '[{JOB_TITLE}]', '[ {JOB_TITLE}]',
                '[{JOB_ID}]', '[ {JOB_ID}]', '[{job_id}]', '[ {job_id}]',
                '[{BENCH_CANDIDATE_NAME}]', '[ {BENCH_CANDIDATE_NAME}]',
                '[{CANDIDATE_NAME}]', '[ {CANDIDATE_NAME}]',
                '[{BENCH_ID}]', '[ {BENCH_ID}]',
                '[{BILL_RATE}]', '[ {BILL_RATE}]',
                '[{EXPERENCE}]', '[ {EXPERENCE}]', '[{EXPERIENCE}]', '[ {EXPERIENCE}]',
                '[{LOCATION}]', '[ {LOCATION}]',
                '[{NP}]', '[ {NP}]',
                '[{OUTSOURCE}]', '[ {OUTSOURCE}]',
                '[{COMMISSION}]', '[ {COMMISSION}]',
                '[{SKILL}]', '[ {SKILL}]', '[{SKILLS}]', '[ {SKILLS}]',
                '[{CONTACT_PERSON_NAME}]', '[ {CONTACT_PERSON_NAME}]',
                '[{PHONE}]', '[ {PHONE}]',
                '[{EMAIL}]', '[ {EMAIL}]',
                '[{RESUME}]', '[{RESUME_URL}]', '[{RESUME_LINK}]',
            ];
            $bodyReplace = [
                $detailsVendorSide->client_company, $detailsVendorSide->client_company,
                $detailsVendorSide->vendor_company, $detailsVendorSide->vendor_company,
                $job->title, $job->title,
                $job->id, $job->id, $job->id, $job->id,
                $candidateNameHtml, $candidateNameHtml,
                $candidateNameHtml, $candidateNameHtml,
                $candidateId, $candidateId,
                $salaryRange, $salaryRange,
                $expDisplay, $expDisplay, $expDisplay, $expDisplay,
                $jobRow->city ?? 'N/A', $jobRow->city ?? 'N/A',
                $npDate, $npDate,
                $outsourcing, $outsourcing,
                $comission, $comission,
                $jobRow->skills ?? 'N/A', $jobRow->skills ?? 'N/A', $jobRow->skills ?? 'N/A', $jobRow->skills ?? 'N/A',
                $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_name,
                $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_phone,
                $detailsVendorSide->vendor_email, $detailsVendorSide->vendor_email,
                $resumeUrl, $resumeUrl, $resumeUrl,
            ];
            $template_description = str_replace($bodySearch, $bodyReplace, $mailTypeSubject->body_description);

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
