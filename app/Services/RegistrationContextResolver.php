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
        // Check for Team Invitation Session Envelope Cookie (INV-02)
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

        // Agency onboarding check
        if ($request->has('agency_code') || $request->route('code')) {
            return $this->buildAgencyContext((string) ($request->input('agency_code') ?? $request->route('code')));
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
                'email' => $invitation->email,
                'role_name' => $role->display_name ?? $role->name ?? 'Team Member',
                'role_id' => $invitation->role_id,
            ],
            'schema' => [
                'fields' => [
                    'name' => [
                        'type' => 'text',
                        'label' => 'Full Name',
                        'required' => true,
                        'editable' => true,
                        'placeholder' => 'Enter your full name',
                    ],
                    'email' => [
                        'type' => 'email',
                        'label' => 'Email Address',
                        'required' => true,
                        'editable' => false, // Immutable server-authoritative
                        'value' => $invitation->email,
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
                    'toc' => [
                        'type' => 'checkbox',
                        'label' => 'I accept the Terms & Conditions and Privacy Policy',
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
        return [
            'context' => self::CONTEXT_AGENCY,
            'context_version' => 'AGENCY_V2',
            'agency_code' => $code,
            'schema' => [
                'fields' => [
                    'company_name' => ['type' => 'text', 'label' => 'Agency Name', 'required' => true, 'editable' => true],
                    'name' => ['type' => 'text', 'label' => 'Contact Person', 'required' => true, 'editable' => true],
                    'email' => ['type' => 'email', 'label' => 'Email', 'required' => true, 'editable' => true],
                    'password' => ['type' => 'password', 'label' => 'Password', 'required' => true, 'editable' => true, 'minlength' => 8],
                ],
                'action_url' => route('company-register'),
            ],
        ];
    }
}
