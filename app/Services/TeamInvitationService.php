<?php

namespace App\Services;

use App\Enums\DeliveryStatus;
use App\Enums\InvitationStatus;
use App\Exceptions\DomainProtectionException;
use App\Models\Company;
use App\Models\Role;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * TeamInvitationService
 *
 * Implements Boundary 1 (Invitation Security) and Boundary 3 (Activation Authority).
 *
 * Key Invariants:
 * - INV-01: Zero-IDOR Token Resolution via SHA-256 hash matching.
 * - INV-02: URL Token Scrubbing & Session Envelope handoff.
 * - INV-03: Database-enforced active invitation uniqueness.
 * - INV-04: Strict decoupled state machine.
 * - INV-05: Global identity collision checks.
 * - INV-15: Universal personal email inclusivity & cross-tenant domain shield.
 * - INV-17: Triple-barrier session lifecycle (SESSION-01, 02, 03).
 */
class TeamInvitationService
{
    public const COOKIE_NAME = 'jero_inv_session';

    public const ENVELOPE_TTL_MINUTES = 15;

    /**
     * Creates a secure team invitation.
     *
     * @throws DomainProtectionException
     * @throws HttpException
     */
    public function createInvitation(int $companyId, int $inviterId, string $email, int $roleId, int $expirationHours = 72): array
    {
        $email = strtolower(trim($email));

        // 1. Domain Validation (INV-15)
        CorporateDomainProtectionService::validateTeamInvitationDomain($companyId, $email);

        // 2. Global Identity Collision Resolution (INV-05)
        $existingUser = DB::table('users')->where('email', $email)->first();
        if ($existingUser) {
            if ((int) $existingUser->company_id === $companyId) {
                abort(409, 'This user is already an active member of this organization.');
            } else {
                abort(422, 'This email is registered with another organization in JERO. Please invite the user with their corporate email.');
            }
        }

        // 3. Verify Role belongs to company or system
        $roleExists = DB::table('roles')
            ->where('id', $roleId)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->exists();

        if (! $roleExists) {
            abort(422, 'Selected role is invalid or unauthorized for this company.');
        }

        // 4. Generate High-Entropy Opaque Secret (256-bit)
        $rawSecret = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawSecret);
        $uuid = (string) Str::uuid();

        // 5. Database Transaction with Version Increment
        return DB::transaction(function () use (
            $companyId, $inviterId, $email, $roleId, $expirationHours, $rawSecret, $tokenHash, $uuid
        ) {
            $invitationId = DB::table('team_invitations')->insertGetId([
                'uuid' => $uuid,
                'company_id' => $companyId,
                'email' => $email,
                'token_hash' => $tokenHash,
                'role_id' => $roleId,
                'context_version' => 'TEAM_V2',
                'status' => InvitationStatus::PENDING->value,
                'delivery_status' => DeliveryStatus::QUEUED->value,
                'invited_by' => $inviterId,
                'expires_at' => now()->addHours($expirationHours),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Audit Log
            DB::table('team_invitation_audit_logs')->insert([
                'invitation_id' => $invitationId,
                'actor_id' => $inviterId,
                'action' => 'INVITATION_CREATED',
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
                'payload_snapshot' => json_encode(['email' => $email, 'role_id' => $roleId]),
                'created_at' => now(),
            ]);

            // Increment Universal Tenant State Version for 'team' domain (INV-11 & INV-12)
            $nextVersion = TenantVersioningService::allocateNextVersion($companyId, 'team');

            $invitation = TeamInvitation::find($invitationId);

            return [
                'invitation' => $invitation,
                'raw_token' => $rawSecret,
                'invite_url' => url('/register?token='.$rawSecret),
                'team_version' => $nextVersion,
            ];
        });
    }

    /**
     * Resolves the raw token and creates a short-lived session envelope cookie (INV-01 & INV-02).
     */
    public function resolveTokenAndCreateEnvelope(string $rawSecret, Request $request): array
    {
        $tokenHash = hash('sha256', $rawSecret);

        $invitation = TeamInvitation::where('token_hash', $tokenHash)->first();

        if (! $invitation || ! $invitation->isAcceptable()) {
            abort(404, 'Invitation is invalid, expired, or has already been used.');
        }

        // Mark link accessed
        if ($invitation->status === InvitationStatus::PENDING) {
            $invitation->status = InvitationStatus::LINK_ACCESSED;
            $invitation->link_accessed_at = now();
            $invitation->save();

            DB::table('team_invitation_audit_logs')->insert([
                'invitation_id' => $invitation->id,
                'actor_id' => null,
                'action' => 'LINK_ACCESSED',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'payload_snapshot' => null,
                'created_at' => now(),
            ]);
        }

        $sessionNonce = bin2hex(random_bytes(16));

        $envelope = [
            'invitation_id' => $invitation->id,
            'tenant_id' => $invitation->company_id,
            'token_hash' => $tokenHash,
            'context_version' => $invitation->context_version,
            'session_nonce' => $sessionNonce,
            'user_agent_hash' => hash('sha256', (string) $request->userAgent()),
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(self::ENVELOPE_TTL_MINUTES)->timestamp,
        ];

        return [
            'invitation' => $invitation,
            'envelope' => $envelope,
            'cookie' => cookie(
                self::COOKIE_NAME,
                Crypt::encrypt($envelope),
                self::ENVELOPE_TTL_MINUTES,
                '/',
                null,
                true, // Secure
                true, // HttpOnly
                false,
                'Strict' // SameSite=Strict
            ),
        ];
    }

    /**
     * Validates the session envelope from the request cookie (INV-02).
     */
    public function getValidatedInvitationFromEnvelope(Request $request): TeamInvitation
    {
        $rawCookie = $request->cookie(self::COOKIE_NAME) ?? $request->cookies->get(self::COOKIE_NAME);
        if (! $rawCookie) {
            abort(401, 'No active invitation session found.');
        }

        try {
            $envelope = Crypt::decrypt($rawCookie);
        } catch (\Throwable $e) {
            abort(401, 'Corrupted or invalid invitation session envelope.');
        }

        if (empty($envelope['expires_at']) || $envelope['expires_at'] < now()->timestamp) {
            abort(401, 'Invitation session has expired.');
        }

        $currentUa = (string) $request->userAgent();
        if (! empty($envelope['user_agent_hash']) && ! empty($currentUa)) {
            $currentUaHash = hash('sha256', $currentUa);
            if (! hash_equals($envelope['user_agent_hash'], $currentUaHash)) {
                abort(401, 'Invitation session is bound to a different browser environment.');
            }
        }

        $invitation = TeamInvitation::find($envelope['invitation_id'] ?? 0);
        if (! $invitation || ! hash_equals($invitation->token_hash, $envelope['token_hash'] ?? '')) {
            abort(401, 'Invitation record does not match session envelope.');
        }

        if (! $invitation->isAcceptable()) {
            abort(410, 'Invitation is no longer acceptable.');
        }

        return $invitation;
    }

    /**
     * Burns the session nonce and performs post-activation session rotation (SESSION-01 & SESSION-03).
     */
    public function burnNonceAndRotateSession(string $nonce, Request $request): void
    {
        // 1. Replay defense: Burn nonce atomically (SESSION-03)
        $nonceKey = "ats:consumed_nonce:{$nonce}";
        $fresh = Cache::add($nonceKey, 1, now()->addMinutes(self::ENVELOPE_TTL_MINUTES));

        if (! $fresh) {
            abort(401, 'Invitation session nonce has already been consumed.');
        }

        // 2. Destroy invitation cookie (SESSION-01)
        cookie()->queue(cookie()->forget(self::COOKIE_NAME));

        // 3. Rotate Laravel session ID to defeat session fixation (SESSION-01)
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
    }
}
