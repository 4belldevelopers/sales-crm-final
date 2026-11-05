<?php

namespace App\Domain\AgencyPartnership\Actions;

use App\Domain\AgencyPartnership\Exceptions\AgencyPartnershipException;
use App\Jobs\ProcessAgencyOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CancelInvitationAction
 *
 * Implements client cancellation of pending invitations:
 * - Cancels pending invitation (to_invitation_status: 0 -> -2)
 * - Releases active_invite_slot (evaluates to NULL)
 * - Transactional outbox event publication
 */
class CancelInvitationAction
{
    /**
     * Cancel an active invitation.
     *
     * @throws AgencyPartnershipException
     */
    public function execute(int $invitationId, int $fromCompanyId): array
    {
        $outboxId = null;

        DB::beginTransaction();
        try {
            $invitation = DB::table('staffing_company_invitation')
                ->where('id', $invitationId)
                ->where('from_company_id', $fromCompanyId)
                ->lockForUpdate()
                ->first();

            if (! $invitation) {
                throw new AgencyPartnershipException('Invitation record not found or access denied.', 404);
            }

            if ((int) $invitation->to_invitation_status === 1) {
                throw new AgencyPartnershipException('Cannot cancel an invitation that has already been accepted.', 422);
            }

            if ((int) $invitation->to_invitation_status !== 0) {
                throw new AgencyPartnershipException('This invitation is not in a cancellable pending state.', 422);
            }

            // Update to CANCELLED (-2)
            DB::table('staffing_company_invitation')
                ->where('id', $invitationId)
                ->update([
                    'to_invitation_status' => -2,
                    'updated_at' => Carbon::now(),
                ]);

            // Transactional Outbox Event
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $fromCompanyId,
                'aggregate_type' => 'AgencyRequest',
                'aggregate_id' => $invitationId,
                'aggregate_version' => 2,
                'event_type' => 'AGENCY_INVITATION_CANCELLED',
                'payload' => json_encode([
                    'invitation_id' => $invitationId,
                    'from_company_id' => $fromCompanyId,
                    'to_company_id' => $invitation->to_company_id,
                    'to_email' => $invitation->to_email,
                    'cancelled_at' => Carbon::now()->toIso8601String(),
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
                Log::warning('ProcessAgencyOutboxEventJob dispatch deferred on cancel: '.$e->getMessage());
            }
        }

        return [
            'status' => 'success',
            'message' => 'Invitation cancelled successfully.',
            'invitation_id' => $invitationId,
        ];
    }
}
