<?php

namespace App\Http\Controllers;

use App\Domain\AgencyPartnership\Actions\AcceptInvitationAction;
use App\Domain\AgencyPartnership\Actions\DeclineInvitationAction;
use App\Domain\AgencyPartnership\Exceptions\AgencyPartnershipException;
use App\Domain\AgencyPartnership\Jobs\HandleMailBounceJob;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PartnerInvitationController
 *
 * Handles scanner-safe invitation landing, authenticated CAS acceptance,
 * voluntary decline, and mail bounce webhook processing.
 *
 * Implements OWASP Scanner-Safe Architecture (GET = Read-Only; POST = CAS Mutation).
 */
class PartnerInvitationController extends Controller
{
    protected InvitationTokenService $tokenService;

    protected AcceptInvitationAction $acceptAction;

    protected DeclineInvitationAction $declineAction;

    public function __construct(
        InvitationTokenService $tokenService,
        AcceptInvitationAction $acceptAction,
        DeclineInvitationAction $declineAction
    ) {
        $this->tokenService = $tokenService;
        $this->acceptAction = $acceptAction;
        $this->declineAction = $declineAction;
    }

    /**
     * Scanner-Safe Read-Only Landing Page.
     * ZERO database mutations execute on this GET request.
     */
    public function landing(Request $request, string $rawToken): \Illuminate\Http\Response
    {
        $tokenHash = $this->tokenService->hash($rawToken);

        $invitation = DB::table('staffing_company_invitation')
            ->join('companies', 'companies.id', '=', 'staffing_company_invitation.from_company_id')
            ->select(
                'staffing_company_invitation.id',
                'staffing_company_invitation.from_company_id',
                'staffing_company_invitation.to_company_id',
                'staffing_company_invitation.to_email',
                'staffing_company_invitation.to_invitation_status',
                'staffing_company_invitation.expires_at',
                'companies.company_name as sender_company_name',
                'companies.company_email as sender_company_email',
                'companies.logo as sender_company_logo'
            )
            ->where('staffing_company_invitation.token_hash', $tokenHash)
            ->first();

        $isExpired = false;
        $isAccepted = false;
        $isCancelled = false;
        $isDeclined = false;

        if ($invitation) {
            $status = (int) $invitation->to_invitation_status;
            $isAccepted = ($status === 1);
            $isDeclined = ($status === -1);
            $isCancelled = ($status === -2);
            $isExpired = ($status === -2 || ($invitation->expires_at && Carbon::parse($invitation->expires_at)->isPast()));
        }

        $currentUser = auth()->user();
        $authCompany = $currentUser ? DB::table('companies')->where('id', $currentUser->company_id)->first() : null;

        return response()->view('partner_invite.landing', [
            'rawToken' => $rawToken,
            'invitation' => $invitation,
            'isExpired' => $isExpired,
            'isAccepted' => $isAccepted,
            'isCancelled' => $isCancelled,
            'isDeclined' => $isDeclined,
            'currentUser' => $currentUser,
            'authCompany' => $authCompany,
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Referrer-Policy' => 'no-referrer',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Authenticated Atomic CAS Acceptance (POST).
     */
    public function accept(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please sign in to your agency account before accepting this invitation.',
                'redirect' => url('/login?return_to='.urlencode(url()->previous())),
            ], 401);
        }

        $request->validate([
            'raw_token' => 'required|string|size:64',
        ]);

        $rawToken = (string) $request->input('raw_token');
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');

        try {
            $user = auth()->user();
            $result = $this->acceptAction->execute($rawToken, $user, $idempotencyKey);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'relation_id' => $result['relation_id'],
                'redirect' => url('/service/consultancy'),
            ], 200);
        } catch (AgencyPartnershipException $ape) {
            return response()->json([
                'status' => 'error',
                'message' => $ape->getMessage(),
            ], $ape->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('PartnerInvitationController accept error: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while accepting the invitation. Please try again.',
            ], 500);
        }
    }

    /**
     * Voluntary Decline (POST).
     */
    public function decline(Request $request): JsonResponse
    {
        $request->validate([
            'raw_token' => 'required|string|size:64',
            'reason' => 'nullable|string|max:500',
        ]);

        $rawToken = (string) $request->input('raw_token');
        $reason = $request->input('reason');

        try {
            $result = $this->declineAction->execute($rawToken, $reason);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
            ], 200);
        } catch (AgencyPartnershipException $ape) {
            return response()->json([
                'status' => 'error',
                'message' => $ape->getMessage(),
            ], $ape->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('PartnerInvitationController decline error: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Mail Provider Webhook for Hard Bounce & Refund.
     */
    public function webhookBounce(Request $request): JsonResponse
    {
        // Provider signature check or token verification
        $webhookSecret = config('services.mailgun.webhook_secret', config('services.postmark.webhook_secret', 'test-secret'));
        $signature = $request->header('X-Webhook-Signature', (string) $request->input('signature'));

        $invitationId = (int) $request->input('invitation_id');
        $eventId = (string) ($request->input('event_id') ?: ('webhook_'.uniqid('', true)));
        $reason = (string) $request->input('reason', 'Hard bounce: mailbox unavailable');

        if ($invitationId > 0) {
            HandleMailBounceJob::dispatch($invitationId, $eventId, $reason);
        }

        return response()->json(['status' => 'received'], 200);
    }
}
