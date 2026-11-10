<?php

namespace App\Services;

use App\Models\Company;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;

/**
 * RegistrationContextResolver
 *
 * Implements Boundary 2 (Registration Context) and INV-06 (Declarative Schema Contract).
 *
 * Resolves whether the registration request is:
 * - SELF: Creating a new company and administrator.
 * - AGENCY: External agency onboarding.
 * - TEAM: Joining an existing company via a cryptographically bound invitation envelope.
 */
class RegistrationContextResolver
{
    public const CONTEXT_SELF = 'SELF';

    public const CONTEXT_AGENCY = 'AGENCY';

    public const CONTEXT_TEAM = 'TEAM';

    protected TeamInvitationService $invitationService;

    public function __construct(TeamInvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Resolves the registration context and declarative field schema from the incoming request.
     */
    public function resolve(Request $request): array
    {
        // 1. Direct Token Resolution from Query String (Immediate zero-latency path)
        if ($request->has('token')) {
            try {
                $rawSecret = (string) $request->query('token');
                $tokenHash = hash('sha256', $rawSecret);
                $invitation = TeamInvitation::where('token_hash', $tokenHash)->first();

                if ($invitation && $invitation->isAcceptable()) {
                    return $this->buildTeamContext($invitation);
                }
            } catch (\Throwable $e) {
                // Token invalid or expired, continue to fallback
            }
        }

        // 2. Check for Team Invitation Session Envelope Cookie (INV-02)
        $cookieVal = $request->cookie(TeamInvitationService::COOKIE_NAME) ?? $request->cookies->get(TeamInvitationService::COOKIE_NAME);
        if ($cookieVal) {
            try {
                $invitation = $this->invitationService->getValidatedInvitationFromEnvelope($request);

                return $this->buildTeamContext($invitation);
            } catch (\Throwable $e) {
                // If envelope is invalid or expired, clear cookie and fall back to SELF
                cookie()->queue(cookie()->forget(TeamInvitationService::COOKIE_NAME));
            }
        }

        // 3. Agency onboarding check (Option 1 & Option 2)
        if ($request->has('agency_code') || $request->has('agency_token') || $request->has('direct_user') || $request->route('code')) {
            $agencyCode = (string) ($request->input('agency_code') ?? $request->input('agency_token') ?? $request->input('direct_user') ?? $request->route('code'));
            return $this->buildAgencyContext($agencyCode);
        }

        // Default: Self-Registration
        return $this->buildSelfContext();
    }

    /**
     * Builds declarative schema contract for TEAM invitation activation.
     */
    protected function buildTeamContext(TeamInvitation $invitation): array
    {
        $company = Company::withoutGlobalScopes()->find($invitation->company_id);
        $role = $invitation->role;
        $user = \App\Models\User::withoutGlobalScopes()
            ->where('email', $invitation->email)
            ->where('company_id', $invitation->company_id)
            ->first();
        if (! $user) {
            $user = \App\Models\User::withoutGlobalScopes()->where('email', $invitation->email)->first();
        }

        $memberName = ucwords(trim($user->name ?? ''));
        $memberMobile = $user->mobile ?? '';
        $memberCallingCode = $user->calling_code ?? '+91';

        return [
            'context' => self::CONTEXT_TEAM,
            'context_version' => $invitation->context_version,
            'invitation_id' => $invitation->id,
            'invitation_uuid' => $invitation->uuid,
            'tenant' => [
                'id' => $invitation->company_id,
                'name' => $company->company_name ?? 'Organization',
                'logo' => $company->logo_url ?? null,
            ],
            'member' => [
                'name' => $memberName,
                'email' => $invitation->email,
                'mobile' => $memberMobile,
                'calling_code' => $memberCallingCode,
                'role_name' => $role->display_name ?? $role->name ?? 'Team Member',
                'role_id' => $invitation->role_id,
            ],
            'schema' => [
                'fields' => [
                    'name' => [
                        'type' => 'text',
                        'label' => 'Full Name',
                        'required' => true,
                        'editable' => false, // Immutable server-authoritative
                        'value' => $memberName,
                    ],
                    'email' => [
                        'type' => 'email',
                        'label' => 'Work Email Address',
                        'required' => true,
                        'editable' => false, // Immutable server-authoritative
                        'value' => $invitation->email,
                    ],
                    'mobile' => [
                        'type' => 'tel',
                        'label' => 'Phone Number',
                        'required' => false,
                        'editable' => true,
                        'value' => $memberMobile,
                    ],
                    'password' => [
                        'type' => 'password',
                        'label' => 'Create Password',
                        'required' => true,
                        'editable' => true,
                        'minlength' => 8,
                    ],
                    'password_confirmation' => [
                        'type' => 'password',
                        'label' => 'Confirm Password',
                        'required' => true,
                        'editable' => true,
                    ],
                ],
                'action_url' => route('register.activate'),
            ],
        ];
    }

    /**
     * Builds declarative schema contract for SELF registration.
     */
    protected function buildSelfContext(): array
    {
        return [
            'context' => self::CONTEXT_SELF,
            'context_version' => 'SELF_V2',
            'schema' => [
                'fields' => [
                    'company_name' => ['type' => 'text', 'label' => 'Company Name', 'required' => true, 'editable' => true],
                    'name' => ['type' => 'text', 'label' => 'Full Name', 'required' => true, 'editable' => true],
                    'email' => ['type' => 'email', 'label' => 'Corporate or Work Email', 'required' => true, 'editable' => true],
                    'password' => ['type' => 'password', 'label' => 'Password', 'required' => true, 'editable' => true, 'minlength' => 8],
                ],
                'action_url' => route('company-register'),
            ],
        ];
    }

    /**
     * Builds declarative schema contract for AGENCY registration.
     */
    protected function buildAgencyContext(string $code): array
    {
        $invitingCompany = null;
        $invitingUser = null;
        $invitationRecord = null;
        $directUserEncoded = '';

        $cleanCode = trim($code);
        $decoded = base64_decode($cleanCode, true);

        // 1. Try resolving as base64-encoded company_id or user_id
        if ($decoded !== false && is_numeric($decoded)) {
            $candidateId = (int) $decoded;
            $invitingCompany = Company::withoutGlobalScopes()->find($candidateId);
            if ($invitingCompany) {
                $invitingUser = \Illuminate\Support\Facades\DB::table('users')
                    ->where('company_id', $invitingCompany->id)
                    ->where('root_user', 1)
                    ->first();
            } else {
                $invitingUser = \Illuminate\Support\Facades\DB::table('users')->where('id', $candidateId)->first();
                if ($invitingUser) {
                    $invitingCompany = Company::withoutGlobalScopes()->find($invitingUser->company_id);
                }
            }
        }

        // 2. Try resolving as plain numeric company_id
        if (! $invitingCompany && is_numeric($cleanCode)) {
            $invitingCompany = Company::withoutGlobalScopes()->find((int) $cleanCode);
            if ($invitingCompany) {
                $invitingUser = \Illuminate\Support\Facades\DB::table('users')
                    ->where('company_id', $invitingCompany->id)
                    ->where('root_user', 1)
                    ->first();
            }
        }

        // 3. Try resolving as staffing_company_invitation token (SHA256 hash or raw token)
        if (! $invitingCompany && ! empty($cleanCode)) {
            $tokenHash = hash('sha256', $cleanCode);
            $invitationRecord = \Illuminate\Support\Facades\DB::table('staffing_company_invitation')
                ->where('token_hash', $tokenHash)
                ->orWhere('token_hash', $cleanCode)
                ->first();

            if ($invitationRecord) {
                $invitingCompany = Company::withoutGlobalScopes()->find($invitationRecord->from_company_id);
                $invitingUser = \Illuminate\Support\Facades\DB::table('users')->where('id', $invitationRecord->from_user_id)->first();
            }
        }

        if ($invitingUser) {
            $directUserEncoded = base64_encode($invitingUser->id);
        } elseif ($invitingCompany) {
            $directUserEncoded = base64_encode($invitingCompany->id);
        }

        $invitingCompanyName = $invitingCompany->company_name ?? 'Partner Organization';
        $invitingCompanyLogo = $invitingCompany->logo_url ?? null;

        return [
            'context' => self::CONTEXT_AGENCY,
            'context_version' => 'AGENCY_V2',
            'agency_code' => $cleanCode,
            'direct_user' => $directUserEncoded,
            'inviting_company' => [
                'id' => $invitingCompany->id ?? null,
                'name' => $invitingCompanyName,
                'email' => $invitingCompany->company_email ?? null,
                'logo' => $invitingCompanyLogo,
            ],
            'inviting_user' => [
                'id' => $invitingUser->id ?? null,
                'name' => $invitingUser->name ?? '',
            ],
            'invitation_record' => $invitationRecord ? [
                'id' => $invitationRecord->id,
                'to_email' => $invitationRecord->to_email ?? null,
                'expires_at' => $invitationRecord->expires_at ?? null,
                'status' => $invitationRecord->to_invitation_status ?? null,
            ] : null,
            'schema' => [
                'company_type' => 1,
                'fields' => [
                    'name' => ['type' => 'text', 'label' => 'Contact Person / Full Name', 'required' => true, 'editable' => true],
                    'email' => ['type' => 'email', 'label' => 'Agency Work Email', 'required' => true, 'editable' => true, 'value' => $invitationRecord->to_email ?? ''],
                    'contact_number' => ['type' => 'tel', 'label' => 'Phone Number', 'required' => true, 'editable' => true],
                    'company_type' => ['type' => 'hidden', 'value' => 1],
                    'password' => ['type' => 'password', 'label' => 'Password', 'required' => true, 'editable' => true, 'minlength' => 8],
                ],
                'action_url' => route('company-register'),
            ],
        ];
    }
}
