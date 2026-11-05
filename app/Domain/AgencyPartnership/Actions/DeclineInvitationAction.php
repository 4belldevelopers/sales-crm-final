<?php

namespace App\Domain\AgencyPartnership\Actions;

use App\Domain\AgencyPartnership\Exceptions\AgencyPartnershipException;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use App\Jobs\ProcessAgencyOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DeclineInvitationAction
 *
 * Implements agency declining a partnership invitation:
 * - Transitions to_invitation_status: 0 -> -1
 * - Starts the 7-day cooling-off period (updated_at = NOW())
 * - Releases active_invite_slot (evaluates to NULL)
 * - Transactional outbox event publication
 */
class DeclineInvitationAction
{
    protected InvitationTokenService $tokenService;

    public function __construct(InvitationTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Decline an invitation using its raw token.
     *
     * @throws AgencyPartnershipException
     */
    public function execute(string $rawToken, ?string $reason = null): array
    {
        $tokenHash = $this->tokenService->hash($rawToken);
        $outboxId = null;
        $invitationId = null;

        DB::beginTransaction();
        try {
            $invitation = DB::table('staffing_company_invitation')
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if (! $invitation) {
                throw new AgencyPartnershipException('Invitation link is invalid or expired.', 404);
            }

            if ((int) $invitation->to_invitation_status === 1) {
                throw new AgencyPartnershipException('Cannot decline an invitation that has already been accepted.', 422);
            }

            if ((int) $invitation->to_invitation_status === -1) {
                DB::rollBack();

                return [
                    'status' => 'success',
                    'message' => 'Invitation has already been declined.',
                    'invitation_id' => $invitation->id,
                ];
            }

            $invitationId = $invitation->id;

            DB::table('staffing_company_invitation')
                ->where('id', $invitationId)
                ->update([
                    'to_invitation_status' => -1, // DECLINED
                    'updated_at' => Carbon::now(),
                ]);

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $invitation->from_company_id,
                'aggregate_type' => 'AgencyRequest',
                'aggregate_id' => $invitationId,
                'aggregate_version' => 2,
                'event_type' => 'AGENCY_INVITATION_DECLINED',
                'payload' => json_encode([
                    'invitation_id' => $invitationId,
                    'from_company_id' => $invitation->from_company_id,
                    'reason' => $reason,
                    'declined_at' => Carbon::now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($outboxId) {
            try {
                ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning('ProcessAgencyOutboxEventJob dispatch deferred on decline: '.$e->getMessage());
            }
        }

        return [
            'status' => 'success',
            'message' => 'Partnership invitation declined.',
            'invitation_id' => $invitationId,
        ];
    }
}
