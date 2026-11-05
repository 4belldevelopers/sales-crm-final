<?php

namespace App\Domain\AgencyPartnership\Actions;

use App\Domain\AgencyPartnership\Exceptions\ActiveInvitationExistsException;
use App\Domain\AgencyPartnership\Exceptions\ActivePartnershipExistsException;
use App\Domain\AgencyPartnership\Exceptions\AgencyPartnershipException;
use App\Domain\AgencyPartnership\Exceptions\CoolingOffPeriodException;
use App\Domain\AgencyPartnership\Exceptions\DailyQuotaExceededException;
use App\Domain\AgencyPartnership\Exceptions\InsufficientCoinsException;
use App\Domain\AgencyPartnership\Services\AgencyRecipientNormalizer;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use App\Helper\GeneralMethod;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Models\Company;
use App\Services\QueueNotificationMailService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CreateInvitationAction
 *
 * Implements the atomic creation of an agency partnership invitation:
 * - Universal Relative Lock Hierarchy (F-02)
 * - Single-Transaction Atomic Idempotency with FOR SHARE recovery (F-03)
 * - Daily quota tracking (20/day)
 * - Coin balance deduction (5 coins) and immutable coin_ledger audit
 * - OWASP 256-bit cryptographically secure token generation
 * - Stored generated active_invite_slot unique index enforcement
 * - Read-First relation guard serialization
 * - Transactional Outbox publication
 */
class CreateInvitationAction
{
    protected InvitationTokenService $tokenService;

    public function __construct(InvitationTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Execute the invitation creation.
     *
     * @param  int  $fromCompanyId  Client company ID
     * @param  int  $fromUserId  Recruiter / Admin user ID
     * @param  string  $recipientEmail  Target agency email
     * @param  string|null  $idempotencyKey  Client-supplied or derived UUID
     * @return array Response DTO
     *
     * @throws AgencyPartnershipException
     */
    public function execute(
        int $fromCompanyId,
        int $fromUserId,
        string $recipientEmail,
        ?string $idempotencyKey = null
    ): array {
        $normalizedRecipient = AgencyRecipientNormalizer::normalize($recipientEmail);
        if (empty($normalizedRecipient)) {
            throw new AgencyPartnershipException('Recipient email cannot be empty.', 422);
        }

        $idempotencyKey = $idempotencyKey ?: ('idemp_inv_'.md5($fromCompanyId.':'.$normalizedRecipient.':'.date('YmdHi')));
        $requestFingerprint = hash('sha256', json_encode([
            'from_company_id' => $fromCompanyId,
            'recipient' => $normalizedRecipient,
        ]));

        $today = Carbon::today()->toDateString();
        $tokenData = $this->tokenService->generate();
        $expiresAt = Carbon::now()->addDays(7);

        $outboxId = null;
        $invitationId = null;
        $newBalance = 0.0;
        $quotaUsed = 0;
        $responseDto = [];

        // Single-Transaction Idempotency + Lock Ordering
        try {
            DB::beginTransaction();

            // 1. Sys Idempotency Key Claim (Initial claim as PROCESSING)
            $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => $fromCompanyId,
                'idempotency_key' => $idempotencyKey,
                'request_hash' => $requestFingerprint,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $claimed) {
                // Another thread or previous request claimed or completed this key
                DB::rollBack();

                return $this->handleDuplicateIdempotencyKey($fromCompanyId, $idempotencyKey, $requestFingerprint);
            }

            // 2. Lock Quota: agency_daily_quotas (FOR UPDATE)
            $quotaRow = DB::table('agency_daily_quotas')
                ->where('company_id', $fromCompanyId)
                ->where('quota_date', $today)
                ->lockForUpdate()
                ->first();

            if (! $quotaRow) {
                DB::statement(
                    'INSERT IGNORE INTO agency_daily_quotas (company_id, quota_date, invitations_sent, created_at, updated_at) VALUES (?, ?, 0, NOW(), NOW())',
                    [$fromCompanyId, $today]
                );
                $quotaRow = DB::table('agency_daily_quotas')
                    ->where('company_id', $fromCompanyId)
                    ->where('quota_date', $today)
                    ->lockForUpdate()
                    ->first();
            }

            if ($quotaRow && (int) $quotaRow->invitations_sent >= 20) {
                throw new DailyQuotaExceededException;
            }

            // 3. Lock Coins: company_coins (FOR UPDATE)
            $coinRow = DB::table('company_coins')
                ->where('company_id', $fromCompanyId)
                ->where('status', 'valid')
                ->lockForUpdate()
                ->first();

            if (! $coinRow || (float) $coinRow->balance_coin < 5.00) {
                throw new InsufficientCoinsException;
            }

            // Recipient & Tenancy Checks
            $userRow = DB::table('users')
                ->select('id', 'company_id', 'root_user', 'email')
                ->where('email', $recipientEmail)
                ->first();

            $toCompanyId = null;
            if ($userRow) {
                if ((int) $userRow->company_id === $fromCompanyId) {
                    throw new AgencyPartnershipException('Cannot invite an internal team member.', 422);
                }
                if ((int) $userRow->root_user === 1) {
                    $toCompanyId = (int) $userRow->company_id;
                }
            }

            // 4. Lock Relation Guard (when to_company_id is resolvable)
            if ($toCompanyId !== null) {
                $companyPairKey = "{$fromCompanyId}:{$toCompanyId}";

                // Read-First Lock Pattern
                $guard = DB::table('staffing_company_relation_guards')
                    ->where('company_pair_key', $companyPairKey)
                    ->lockForUpdate()
                    ->first();

                if (! $guard) {
                    DB::statement(
                        'INSERT IGNORE INTO staffing_company_relation_guards (company_pair_key, created_at, updated_at) VALUES (?, NOW(), NOW())',
                        [$companyPairKey]
                    );
                    DB::table('staffing_company_relation_guards')
                        ->where('company_pair_key', $companyPairKey)
                        ->lockForUpdate()
                        ->first();
                }

                // Verify active relationship does not already exist
                $activeRelationExists = DB::table('staffing_company_relation')
                    ->where('from_company_id', $fromCompanyId)
                    ->where('to_company_id', $toCompanyId)
                    ->where('final_status', 1)
                    ->lockForUpdate()
                    ->exists();

                if ($activeRelationExists) {
                    throw new ActivePartnershipExistsException;
                }
            }

            // 7-Day Cooling-off check on declined invitations (F-07)
            $coolingOffActive = DB::table('staffing_company_invitation')
                ->where('from_company_id', $fromCompanyId)
                ->where('normalized_recipient', $normalizedRecipient)
                ->where('to_invitation_status', -1)
                ->where('updated_at', '>', Carbon::now()->subDays(7))
                ->exists();

            if ($coolingOffActive) {
                throw new CoolingOffPeriodException;
            }

            // 5. Lock Invitation Active Slot / Check active invitation
            $slotKey = "{$fromCompanyId}:{$normalizedRecipient}";
            $activeInvitationExists = DB::table('staffing_company_invitation')
                ->where('active_invite_slot', $slotKey)
                ->lockForUpdate()
                ->exists();

            if ($activeInvitationExists) {
                throw new ActiveInvitationExistsException;
            }

            // Fetch client primary ID
            $clientRow = DB::table('clients')
                ->select('id')
                ->where('company_id', $fromCompanyId)
                ->where('self_company', 1)
                ->first();

            // Insert invitation record
            $invitationId = DB::table('staffing_company_invitation')->insertGetId([
                'from_company_id' => $fromCompanyId,
                'from_client_id' => $clientRow ? $clientRow->id : null,
                'from_user_id' => $fromUserId,
                'from_status' => 1,
                'from_created_at' => Carbon::now(),
                'to_company_id' => $toCompanyId,
                'to_email' => $recipientEmail,
                'to_status' => $toCompanyId ? 1 : 0,
                'to_invitation_status' => 0, // 0 = PENDING
                'token_hash' => $tokenData['token_hash'],
                'expires_at' => $expiresAt,
                'delivery_status' => 'PENDING',
                'normalized_recipient' => $normalizedRecipient,
            ]);

            // Deduct 5 Coins
            $newBalance = (float) $coinRow->balance_coin - 5.00;
            DB::table('company_coins')
                ->where('id', $coinRow->id)
                ->update([
                    'balance_coin' => $newBalance,
                    'updated_at' => Carbon::now(),
                ]);

            // Increment Quota Counter
            $quotaUsed = (int) $quotaRow->invitations_sent + 1;
            DB::table('agency_daily_quotas')
                ->where('id', $quotaRow->id)
                ->update([
                    'invitations_sent' => $quotaUsed,
                    'updated_at' => Carbon::now(),
                ]);

            // 6. Write Immutable Audit Row in coin_ledger
            $txnId = 'TXN_INV_'.$invitationId.'_'.time();
            $ledgerIdemp = 'IDEMP_INV_'.$invitationId;
            DB::table('coin_ledger')->insert([
                'transaction_id' => $txnId,
                'company_id' => $fromCompanyId,
                'account_type' => 'operational',
                'coin_type' => 'standard',
                'entry_type' => 'DEBIT',
                'amount' => 5.00,
                'balance_after' => $newBalance,
                'reference_type' => 'AGENCY_INVITATION',
                'reference_id' => (string) $invitationId,
                'idempotency_key' => $ledgerIdemp,
                'metadata' => json_encode([
                    'invitation_id' => $invitationId,
                    'recipient' => $normalizedRecipient,
                    'user_id' => $fromUserId,
                ]),
                'created_at' => Carbon::now(),
            ]);

            // 7. Write Transactional Outbox Event
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $fromCompanyId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'AgencyRequest',
                'aggregate_id' => $invitationId,
                'aggregate_version' => 1,
                'event_type' => 'AGENCY_INVITATION_SENT',
                'payload' => json_encode([
                    'from_company_id' => $fromCompanyId,
                    'to_company_id' => $toCompanyId,
                    'to_email' => $recipientEmail,
                    'normalized_recipient' => $normalizedRecipient,
                    'invitation_id' => $invitationId,
                    'user_id' => $fromUserId,
                    'coins_deducted' => 5,
                    'new_balance' => $newBalance,
                    'expires_at' => $expiresAt->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            // Construct Final Response DTO
            $responseDto = [
                'status' => 'success',
                'message' => 'Agency partnership invitation sent successfully.',
                'invitation_id' => $invitationId,
                'recipient' => $recipientEmail,
                'coins_deducted' => 5,
                'remaining_coins' => $newBalance,
                'daily_quota_used' => $quotaUsed,
                'daily_quota_remaining' => max(0, 20 - $quotaUsed),
                'expires_at' => $expiresAt->toIso8601String(),
                'invite_url' => $tokenData['url'],
            ];

            // 8. Update sys_idempotency_keys with COMPLETED status
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $fromCompanyId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responseDto),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            // 9. Atomic Commit
            DB::commit();
        } catch (QueryException $qe) {
            DB::rollBack();
            // Catch Error 1062 specifically on uk_tenant_idemp or uk_tenant_active_slot
            if ($qe->getCode() === '23000' && str_contains($qe->getMessage(), '1062')) {
                if (str_contains($qe->getMessage(), 'uk_tenant_active_slot')) {
                    throw new ActiveInvitationExistsException;
                }
                if (str_contains($qe->getMessage(), 'uk_tenant_idemp') || str_contains($qe->getMessage(), 'idx_idempotency_tenant_key')) {
                    return $this->handleDuplicateIdempotencyKey($fromCompanyId, $idempotencyKey, $requestFingerprint);
                }
            }
            throw $qe;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Post-commit async mail dispatch & outbox job trigger
        $this->dispatchPostCommitSideEffects($fromCompanyId, $recipientEmail, $tokenData['url'], $outboxId);

        return $responseDto;
    }

    /**
     * Handle duplicate idempotency key recovery via modern MySQL 8.0 FOR SHARE read.
     */
    protected function handleDuplicateIdempotencyKey(int $tenantId, string $key, string $fingerprint): array
    {
        $record = DB::table('sys_idempotency_keys')
            ->select('request_hash', 'response_status', 'response_body', 'status')
            ->where('tenant_id', $tenantId)
            ->where('idempotency_key', $key)
            ->sharedLock()
            ->first();

        if (! $record) {
            throw new AgencyPartnershipException('Idempotency claim resolution failed.', 500);
        }

        if ($record->request_hash !== $fingerprint) {
            throw new AgencyPartnershipException('Idempotency key reuse with mismatched request payload.', 409);
        }

        if ($record->status === 'COMPLETED' && ! empty($record->response_body)) {
            $decoded = json_decode($record->response_body, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new AgencyPartnershipException('An identical invitation is currently being processed. Please wait a moment.', 409);
    }

    /**
     * Post-commit notification email & worker dispatch.
     */
    protected function dispatchPostCommitSideEffects(int $fromCompanyId, string $recipientEmail, string $inviteUrl, ?int $outboxId): void
    {
        if ($outboxId) {
            try {
                ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning('ProcessAgencyOutboxEventJob dispatch deferred: '.$e->getMessage());
            }
        }

        try {
            $company = Company::withoutGlobalScopes()->find($fromCompanyId);
            $reqDate = date('d M Y');
            $checkMailPermission = GeneralMethod::mailPermission(40);

            if ($checkMailPermission && $company) {
                $mailTypeSubject = GeneralMethod::mailTypeSubject(40, 1);
                $subject = str_replace(
                    ['[COMPANY_NAME]', '[REQUEST_DATE]'],
                    [$company->company_name, $reqDate],
                    $mailTypeSubject->subject ?? "Invitation to Become a Staffing Partner | {$company->company_name} | {$reqDate}"
                );

                $acceptInviteBtn = '<a href="'.$inviteUrl.'" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle">Accept Partnership Invitation</a>';
                $urlLink = '<a href="'.$inviteUrl.'" rel="noopener" style="text-decoration:none;color:#009EF7">'.$inviteUrl.'</a>';

                $template = str_replace(
                    ['[COMPANY_NAME]', '[ACCEPT_INVITE]', '[URL_LINK]'],
                    [$company->company_name, $acceptInviteBtn, $urlLink],
                    $mailTypeSubject->body_description ?? "You have been invited to partner with {$company->company_name}. Click below to accept:\n\n{$inviteUrl}"
                );

                $baseCompany = GeneralMethod::baseCompany();
                $senderName = $baseCompany ? ($baseCompany->companyName ?: '') : '';
                $fromAddress = $baseCompany ? ($baseCompany->companyEmail ?: '') : '';

                QueueNotificationMailService::dispatch(
                    $recipientEmail,
                    $subject,
                    'email.common.index',
                    (object) ['message' => $template],
                    [],
                    $fromAddress,
                    $senderName,
                    [],
                    $company->id
                )->onConnection('emails');
            }
        } catch (\Throwable $e) {
            Log::warning('CreateInvitationAction: Mail dispatch deferred: '.$e->getMessage());
        }
    }
}
