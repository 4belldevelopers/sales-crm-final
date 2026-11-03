<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Services\Contracts\TenantMembershipResolverInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * RegistrationActivationService
 *
 * Implements Boundary 3: Activation Authority & Database Integrity.
 *
 * Enforces:
 * - INV-08 & INV-16: Database-enforced idempotency pipeline.
 * - INV-09: Atomic transaction with pessimistic row lock (`SELECT ... FOR UPDATE`).
 * - INV-10: Temporal role resolution at activation time.
 * - INV-11 & INV-12: Monotonic tenant state version allocation.
 * - INV-17: Triple-barrier session envelope lifecycle (rotation, CSRF, nonce burn).
 */
class RegistrationActivationService
{
    protected TeamInvitationService $invitationService;

    protected RegistrationIdempotencyHandler $idempotencyHandler;

    protected TenantMembershipResolverInterface $membershipResolver;

    public function __construct(
        TeamInvitationService $invitationService,
        RegistrationIdempotencyHandler $idempotencyHandler,
        TenantMembershipResolverInterface $membershipResolver
    ) {
        $this->invitationService = $invitationService;
        $this->idempotencyHandler = $idempotencyHandler;
        $this->membershipResolver = $membershipResolver;
    }

    /**
     * Executes the activation request within the full idempotent and transactional envelope.
     */
    public function activate(Request $request): JsonResponse
    {
        // 1. Session Envelope Validation (INV-02 & INV-17)
        $rawCookie = $request->cookie(TeamInvitationService::COOKIE_NAME)
            ?? $request->cookies->get(TeamInvitationService::COOKIE_NAME)
            ?? $request->input('invitation_envelope')
            ?? $request->header('X-Invitation-Envelope');

        $envelope = null;

        if ($rawCookie) {
            try {
                $envelope = Crypt::decrypt($rawCookie);
            } catch (\Throwable $e) {
                // If encrypted envelope string failed, will try token fallback
            }
        }

        if (! $envelope && $request->has('token')) {
            $rawSecret = (string) $request->input('token');
            $tokenHash = hash('sha256', $rawSecret);
            $inv = TeamInvitation::where('token_hash', $tokenHash)->first();
            if ($inv && $inv->isAcceptable()) {
                $envelope = [
                    'invitation_id' => $inv->id,
                    'tenant_id' => $inv->company_id,
                    'token_hash' => $tokenHash,
                    'session_nonce' => bin2hex(random_bytes(16)),
                ];
            }
        }

        if (! $envelope) {
            return response()->json([
                'error' => 'MISSING_INVITATION_SESSION',
                'message' => 'No active invitation session found. Please re-open your invitation link.',
            ], 401);
        }

        $invitationId = (int) ($envelope['invitation_id'] ?? 0);
        $tenantId = (int) ($envelope['tenant_id'] ?? 0);
        $nonce = (string) ($envelope['session_nonce'] ?? '');

        // 2. Validate Input Payload
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:191',
            'mobile' => 'nullable|string|max:25',
            'calling_code' => 'nullable|string|max:10',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'toc' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'VALIDATION_FAILED',
                'message' => 'Form validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 3. Execute through Database-Enforced Idempotency Handler (INV-08 & INV-16)
        return $this->idempotencyHandler->handle(
            $request,
            $tenantId,
            $invitationId,
            function () use ($request, $invitationId, $tenantId, $nonce) {
                return $this->executeActivationTransaction($request, $invitationId, $tenantId, $nonce);
            }
        );
    }

    /**
     * Atomic, pessimistic-locked activation transaction (INV-09).
     */
    protected function executeActivationTransaction(Request $request, int $invitationId, int $tenantId, string $nonce): JsonResponse
    {
        return DB::transaction(function () use ($request, $invitationId, $tenantId, $nonce) {
            // Pessimistic Row-Lock on Invitation Record (INV-09)
            $invitation = TeamInvitation::where('id', $invitationId)
                ->where('company_id', $tenantId)
                ->lockForUpdate()
                ->first();

            if (! $invitation || ! $invitation->isAcceptable()) {
                abort(410, 'Invitation is invalid, expired, or has already been used.');
            }

            // Single-Use Nonce Burn (SESSION-03)
            $this->invitationService->burnNonceAndRotateSession($nonce, $request);

            $email = strtolower(trim($invitation->email));
            $password = (string) $request->input('password');

            // Identity Collision Resolution (INV-05)
            $existingUser = User::withoutGlobalScopes()->where('email', $email)->first();
            $name = trim((string) ($request->input('name') ?: ($existingUser->name ?? 'Team Member')));

            if ($existingUser) {
                if ((int) $existingUser->company_id !== $tenantId) {
                    abort(422, 'This email is already registered with another organization in JERO.');
                }

                // Intra-tenant activation
                $user = $existingUser;
                $user->name = $name;
                $user->password = Hash::make($password);
                $user->status = 'active';
                $user->working_status = 1;
                $user->email_verification_code = null;
                if ($request->filled('mobile')) {
                    $user->mobile = trim((string) $request->input('mobile'));
                }
                if ($request->filled('calling_code')) {
                    $user->calling_code = trim((string) $request->input('calling_code'));
                }
                $user->save();
            } else {
                // Provision new User
                $user = new User;
                $user->name = $name;
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->status = 'active';
                $user->working_status = 1;
                $user->email_verification_code = null;
                if ($request->filled('mobile')) {
                    $user->mobile = trim((string) $request->input('mobile'));
                }
                if ($request->filled('calling_code')) {
                    $user->calling_code = trim((string) $request->input('calling_code'));
                }
                $user->save();

                // Attach User to Tenant via Resolver Interface (Model A / Model B Bridge)
                $this->membershipResolver->attachUserToTenant($user, $tenantId, $invitation->role_id);
            }

            // Marketplace Contact Assignments Integration (Phase 3 & Boundary 4)
            if ($request->has('marketplace_channels')) {
                $channels = (array) $request->input('marketplace_channels');
                $validChannels = ['client', 'agency', 'bench'];
                foreach ($channels as $channel => $enabled) {
                    if ($enabled && in_array($channel, $validChannels, true)) {
                        DB::table('company_marketplace_contact_assignments')->updateOrInsert(
                            [
                                'company_id' => $tenantId,
                                'channel' => $channel,
                                'user_id' => $user->id,
                            ],
                            [
                                'visibility' => 'public',
                                'priority' => 'secondary',
                                'is_active' => 1,
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }
                }
            }

            // Mark Invitation Activated
            $invitation->status = InvitationStatus::ACTIVATED;
            $invitation->used_at = now();
            $invitation->save();

            // Increment Monotonic Universal Tenant State Version (INV-11 & INV-12)
            $nextVersion = TenantVersioningService::allocateNextVersion($tenantId, 'team');

            // Audit Log
            DB::table('team_invitation_audit_logs')->insert([
                'invitation_id' => $invitation->id,
                'actor_id' => $user->id,
                'action' => 'INVITATION_ACTIVATED',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'payload_snapshot' => json_encode(['user_id' => $user->id, 'team_version' => $nextVersion]),
                'created_at' => now(),
            ]);

            // Authenticate user & record single-device session
            Auth::login($user);
            if ($request->hasSession()) {
                $sessionId = session()->getId();
                $user->active_session = $sessionId;
                $user->session_id = $sessionId;
                $user->last_login = now()->toDateTimeString();
                $user->save();
                \App\Support\DeviceSessionManager::recordLogin($user->id, $sessionId);
                session(['login_at' => now()->timestamp]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Account activated successfully. Welcome to JERO!',
                'user_id' => $user->id,
                'team_version' => $nextVersion,
                'redirect_url' => url('/service_company/dashboard'),
            ], 201);
        });
    }
}
