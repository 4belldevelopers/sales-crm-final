<?php

/**
 * MarkUserVerifiedJob — V2 Registration Sprint 5, task 5.14 (audit H4).
 *
 * Moves the slow side effects of email-link verification off the HTTP thread:
 *   - confirmation mail (QueueMailService)
 *   - ES company + user re-index (ElasticCompanyIndex, userIndexingupload)
 *   - staffing-partner cache update (clientStaffingPartner)
 *
 * Dispatched from getEmailVerification() AFTER the DB transaction that marks the
 * user active commits (ADR-V2R-13). Verify HTTP returns in <50ms; mail+ES run here.
 */

namespace App\Jobs;

use App\Helper\GeneralMethod;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarkUserVerifiedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 5;
    public array $backoff = [30, 60, 120, 240];
    public int $timeout = 600;

    public function __construct(public readonly int $userId) {}

    public function handle(): void
    {
        $user = User::withoutGlobalScopes()->find($this->userId);

        if (! $user) {
            Log::warning('[MarkUserVerifiedJob] User not found', ['user_id' => $this->userId]);
            return;
        }

        try {
            $urlLink = getDomainSpecificUrl(route('login'));
            $data    = [
                'name'       => $user->name,
                'to'         => $user->email,
                'subject'    => 'Account Confirmation Team',
                'urlLink'    => $urlLink,
                'company_id' => $user->company_id,
                'message'    => '',
            ];

            $basecompany = GeneralMethod::baseCompany();
            $senderName  = $basecompany->companyName  ?? '';
            $fromAddress = $basecompany->companyEmail ?? '';

            QueueMailService::dispatch(
                $user->email,
                'Company Profile Confirmation',
                'email.company-profile.account-confirmation-team',
                $data,
                [],
                $fromAddress,
                $senderName,
                [],
                $user->company_id,
            )->onConnection('emails');

            ElasticCompanyIndex::dispatch($user->company_id)->onConnection('emails');
            GeneralMethod::userIndexingupload($user->id);

            $client = DB::table('clients')
                ->where('company_id', $user->company_id)
                ->where('self_company', 1)
                ->first();

            if ($client) {
                GeneralMethod::clientStaffingPartner($client->id);
            }
        } catch (\Throwable $e) {
            Log::error('[MarkUserVerifiedJob] Failed', [
                'user_id' => $this->userId,
                'error'   => $e->getMessage(),
            ]);
            throw $e; // re-throw so Laravel re-queues within $tries limit
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('[MarkUserVerifiedJob] Permanently failed after all retries', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
