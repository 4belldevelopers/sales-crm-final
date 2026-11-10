<?php

namespace App\Domain\AgencyPartnership\Actions;

use App\Domain\AgencyPartnership\Exceptions\ActivePartnershipExistsException;
use App\Domain\AgencyPartnership\Exceptions\AgencyPartnershipException;
use App\Domain\AgencyPartnership\Exceptions\InvalidInvitationTokenException;
use App\Domain\AgencyPartnership\Exceptions\UnauthorizedAcceptanceException;
use App\Domain\AgencyPartnership\Services\AgencyRecipientNormalizer;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AcceptInvitationAction
 *
 * Implements atomic in-transaction CAS acceptance of an agency partnership:
 * - Read-First canonical pair guard lock pattern on staffing_company_relation_guards
 * - In-transaction security revalidation (token hash, TTL, binding authority, corporate domain)
 * - Single-transaction idempotency
 * - Atomic activation in staffing_company_relation (active_relation_slot)
 * - Releasing active_invite_slot on staffing_company_invitation
 * - Transactional Outbox publication (AGENCY_PARTNERSHIP_ACTIVATED)
 */
class AcceptInvitationAction
{
    protected InvitationTokenService $tokenService;

    public function __construct(InvitationTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Execute invitation acceptance.
     *
     * @param  string  $rawToken  64-character raw hex token
     * @param  User  $authenticatedUser  Currently logged-in agency representative
     *
     * @throws AgencyPartnershipException
     */
    public function execute(
        string $rawToken,
        User $authenticatedUser,
        ?string $idempotencyKey = null
    ): array {
        $tokenHash = $this->tokenService->hash($rawToken);
        $acceptingCompanyId = (int) $authenticatedUser->company_id;
        $acceptingUserId = (int) $authenticatedUser->id;

        if ($acceptingCompanyId <= 0) {
            throw new UnauthorizedAcceptanceException('User does not belong to a valid registered company.');
        }

        $idempotencyKey = $idempotencyKey ?: ('idemp_accept_'.md5($tokenHash.':'.$acceptingCompanyId));
        $requestFingerprint = hash('sha256', json_encode([
            'token_hash' => $tokenHash,
            'accepting_company_id' => $acceptingCompanyId,
            'user_id' => $acceptingUserId,
        ]));

        $outboxId = null;
        $relationId = null;
        $responseDto = [];

        try {
            DB::beginTransaction();

            // 1. Claim Idempotency Key
            $claimed = DB::table('sys_idempotency_keys')->insertOrIgnore([
                'tenant_id' => $acceptingCompanyId,
                'idempotency_key' => $idempotencyKey,
                'request_hash' => $requestFingerprint,
                'status' => 'PROCESSING',
                'expires_at' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $claimed) {
                DB::rollBack();

                return $this->handleDuplicateIdempotencyKey($acceptingCompanyId, $idempotencyKey, $requestFingerprint);
            }

            // 2. Read Uncommitted Invitation to get from_company_id
            $invitation = DB::table('staffing_company_invitation')
                ->where('token_hash', $tokenHash)
                ->first();

            if (! $invitation) {
                throw new InvalidInvitationTokenException('Invitation link is invalid or does not exist.');
            }

            $fromCompanyId = (int) $invitation->from_company_id;
            if ($fromCompanyId === $acceptingCompanyId) {
                throw new UnauthorizedAcceptanceException('A company cannot accept an invitation issued by itself.');
            }

            // Directional Canonical Pair Key: Client -> Agency
            $companyPairKey = "{$fromCompanyId}:{$acceptingCompanyId}";

            // 3. Read-First Canonical Pair Guard Lock Pattern
            $guard = DB::table('staffing_company_relation_guards')
                ->where('company_pair_key', $companyPairKey)
                ->lockForUpdate()
                ->first();

            if (! $guard) {
                DB::statement(
                    'INSERT IGNORE INTO staffing_company_relation_guards (company_pair_key, created_at, updated_at) VALUES (?, NOW(), NOW())',
                    [$companyPairKey]
                );
                $guard = DB::table('staffing_company_relation_guards')
                    ->where('company_pair_key', $companyPairKey)
                    ->lockForUpdate()
                    ->first();
            }

            // 4. Lock Invitation Row Exclusive
            $lockedInvitation = DB::table('staffing_company_invitation')
                ->where('id', $invitation->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedInvitation) {
                throw new InvalidInvitationTokenException('Invitation record could not be locked.');
            }

            // 5. In-Transaction Security Revalidation (F-06)
            if ((int) $lockedInvitation->to_invitation_status === 1) {
                throw new InvalidInvitationTokenException('This invitation has already been accepted.');
            }

            if ((int) $lockedInvitation->to_invitation_status !== 0) {
                throw new InvalidInvitationTokenException('This invitation is no longer active (cancelled or expired).');
            }

            if ($lockedInvitation->expires_at && Carbon::parse($lockedInvitation->expires_at)->isPast()) {
                // JIT (Just-In-Time) evaluation: lazily mark expired and free active_invite_slot
                DB::table('staffing_company_invitation')
                    ->where('id', $lockedInvitation->id)
                    ->update([
                        'to_invitation_status' => -2,
                        'active_invite_slot' => null,
                        'updated_at' => Carbon::now(),
                    ]);

                throw new InvalidInvitationTokenException('This invitation expired on '.$lockedInvitation->expires_at);
            }

            // Binding authority: root user or active admin
            if ((int) $authenticatedUser->root_user !== 1) {
                throw new UnauthorizedAcceptanceException('Only company administrators/root users can accept partnership invitations.');
            }

            // Check if active partnership already exists
            $relationExists = DB::table('staffing_company_relation')
                ->where('from_company_id', $fromCompanyId)
                ->where('to_company_id', $acceptingCompanyId)
                ->where('final_status', 1)
                ->lockForUpdate()
                ->exists();

            if ($relationExists) {
                throw new ActivePartnershipExistsException('Your company is already an active partner with this organization.');
            }

            // Recipient-to-Company Capability Binding
            $invitationRecipient = (string) $lockedInvitation->to_email;
            $invitationRecipientDomain = AgencyRecipientNormalizer::extractDomain($invitationRecipient);
            $userEmail = (string) $authenticatedUser->email;
            $userDomain = AgencyRecipientNormalizer::extractDomain($userEmail);

            $publicMailDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'mail.com'];
            $isPublicMail = in_array($invitationRecipientDomain, $publicMailDomains, true);

            if ($lockedInvitation->to_company_id !== null) {
                // Pre-scoped invitation to registered company
                if ((int) $lockedInvitation->to_company_id !== $acceptingCompanyId) {
                    throw new UnauthorizedAcceptanceException('This invitation was addressed to a different company.');
                }
            } else {
                // Addressed by email
                if ($isPublicMail) {
                    // Public webmail: user email must match exactly
                    if (strtolower($userEmail) !== strtolower($invitationRecipient)) {
                        throw new UnauthorizedAcceptanceException("This invitation was issued to {$invitationRecipient}. Please log in with that exact account.");
                    }
                } else {
                    // Corporate domain: domains must match
                    if ($userDomain !== $invitationRecipientDomain) {
                        throw new UnauthorizedAcceptanceException("Corporate domain mismatch. Invitation was sent to @{$invitationRecipientDomain}.");
                    }
                }
            }

            // Find client ID for accepting company
            $acceptingClient = DB::table('clients')
                ->select('id')
                ->where('company_id', $acceptingCompanyId)
                ->where('self_company', 1)
                ->first();

            // 6. Create Active Relationship
            $relationId = DB::table('staffing_company_relation')->insertGetId([
                'from_company_id' => $fromCompanyId,
                'from_client_id' => $lockedInvitation->from_client_id,
                'from_user_id' => $lockedInvitation->from_user_id,
                'from_status' => 1,
                'from_created_at' => $lockedInvitation->from_created_at,
                'to_company_id' => $acceptingCompanyId,
                'to_client_id' => $acceptingClient ? $acceptingClient->id : null,
                'to_user_id' => $acceptingUserId,
                'to_status' => 1,
                'to_created_at' => Carbon::now(),
                'final_status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // 7. Update Invitation to ACCEPTED
            DB::table('staffing_company_invitation')
                ->where('id', $lockedInvitation->id)
                ->update([
                    'to_invitation_status' => 1, // ACCEPTED
                    'to_company_id' => $acceptingCompanyId,
                    'to_status' => 1,
                    'consumed_at' => Carbon::now(),
                    'delivery_status' => 'DELIVERED',
                ]);

            // 8. Transactional Outbox Event
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $fromCompanyId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'AgencyRequest',
                'aggregate_id' => $lockedInvitation->id,
                'aggregate_version' => 2,
                'event_type' => 'AGENCY_PARTNERSHIP_ACTIVATED',
                'payload' => json_encode([
                    'invitation_id' => $lockedInvitation->id,
                    'relation_id' => $relationId,
                    'from_company_id' => $fromCompanyId,
                    'to_company_id' => $acceptingCompanyId,
                    'accepted_by_user_id' => $acceptingUserId,
                    'accepted_at' => Carbon::now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $responseDto = [
                'status' => 'success',
                'message' => 'Partnership successfully established! You are now connected staffing partners.',
                'relation_id' => $relationId,
                'from_company_id' => $fromCompanyId,
                'to_company_id' => $acceptingCompanyId,
                'invitation_id' => $lockedInvitation->id,
            ];

            // 9. Update Idempotency Record to COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $acceptingCompanyId)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($responseDto),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();
        } catch (QueryException $qe) {
            DB::rollBack();
            if ($qe->getCode() === '23000' && str_contains($qe->getMessage(), '1062')) {
                if (str_contains($qe->getMessage(), 'uk_active_relation_slot')) {
                    throw new ActivePartnershipExistsException('This partnership relationship is already active.');
                }
                if (str_contains($qe->getMessage(), 'uk_tenant_idemp') || str_contains($qe->getMessage(), 'idx_idempotency_tenant_key')) {
                    return $this->handleDuplicateIdempotencyKey($acceptingCompanyId, $idempotencyKey, $requestFingerprint);
                }
            }
            throw $qe;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // Post-commit side effects
        if ($outboxId) {
            try {
                ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning('ProcessAgencyOutboxEventJob dispatch deferred on accept: '.$e->getMessage());
            }
        }

        return $responseDto;
    }

    /**
     * Handle duplicate idempotency key recovery for acceptance.
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

        throw new AgencyPartnershipException('This acceptance is already in progress.', 409);
    }
}
