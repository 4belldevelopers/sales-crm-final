<?php

namespace App\Services;

use App\Exceptions\DomainProtectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * CorporateDomainProtectionService
 *
 * Implements INV-15: Universal Personal Email Inclusivity & Cross-Tenant Registered Domain Shield.
 *
 * Invariants:
 * 1. Personal / Public Webmails (Gmail, Yahoo, Outlook, etc.) are 100% ALLOWED for ALL companies.
 * 2. Cross-Tenant Corporate Domain Protection: No company can invite an email belonging to another
 *    organization's proprietary registered corporate domain (`companies.domain_key`).
 * 3. Disposable Domain Shield: Blocks temporary disposable burner addresses.
 */
class CorporateDomainProtectionService
{
    /**
     * Public consumer email providers 100% allowed for all companies without any restriction.
     */
    public const SHARED_PUBLIC_EMAIL_DOMAINS = [
        'gmail.com', 'googlemail.com', 'yahoo.com', 'yahoo.co.in', 'yahoo.co.uk',
        'hotmail.com', 'outlook.com', 'live.com', 'msn.com',
        'icloud.com', 'me.com', 'mac.com',
        'aol.com', 'protonmail.com', 'proton.me', 'zoho.com',
        'yandex.com', 'mail.com', 'gmx.com', 'gmx.net', 'fastmail.com',
    ];

    /**
     * Known disposable temporary burner domains.
     */
    public const DISPOSABLE_DOMAINS = [
        'mailinator.com', 'tempmail.com', 'guerrillamail.com', '10minutemail.com',
        'throwawaymail.com', 'yopmail.com', 'sharklasers.com', 'dispostable.com',
        'fakeinbox.com', 'trashmail.com', 'getairmail.com', 'maildrop.cc',
        'burnermail.io', 'temp-mail.org', 'crazymailing.com', 'tempinbox.com',
    ];

    /**
     * Validates target email domain against JERO domain protection policies.
     *
     * @throws DomainProtectionException
     */
    public static function validateTeamInvitationDomain(int $invitingCompanyId, string $targetEmail): void
    {
        $targetEmail = strtolower(trim($targetEmail));
        $atPos = strrpos($targetEmail, '@');
        if ($atPos === false) {
            throw new DomainProtectionException('Invalid email address format.', 422);
        }

        $targetDomain = Str::lower(substr($targetEmail, $atPos + 1));

        if (empty($targetDomain)) {
            throw new DomainProtectionException('Invalid email address domain.', 422);
        }

        // 1. Disposable Domain Shield (Blocks disposable bots / tempmail)
        if (self::isDisposableDomain($targetDomain)) {
            throw new DomainProtectionException('Disposable or temporary email addresses are strictly prohibited for team invitations.', 422);
        }

        // 2. Rule: Personal / Public Webmails (Gmail, Yahoo, Outlook, etc.) are 100% ALLOWED for ALL companies
        if (in_array($targetDomain, self::SHARED_PUBLIC_EMAIL_DOMAINS, true)) {
            return; // Clean pass - No company is ever blocked from inviting personal Gmail/webmail IDs
        }

        // 3. Cross-Tenant Registered Domain Shield (INV-15: Anti-Poaching / Anti-Infiltration)
        // If the domain is a proprietary corporate domain registered by ANOTHER company in JERO, block cross-tenant invasion.
        $conflictingCompany = DB::table('companies')
            ->where('domain_key', $targetDomain)
            ->where('id', '!=', $invitingCompanyId)
            ->first(['id', 'company_name']);

        if ($conflictingCompany) {
            throw new DomainProtectionException(
                "Corporate Domain Protection: The domain '@{$targetDomain}' is an exclusive corporate domain registered to another organization in JERO ({$conflictingCompany->company_name}). Cross-tenant team invitations are strictly prohibited.",
                403
            );
        }

        // If target domain matches the inviting company's domain or an unreserved corporate domain: 100% Allowed.
    }

    /**
     * Check if domain is disposable.
     */
    public static function isDisposableDomain(string $domain): bool
    {
        $domain = Str::lower(trim($domain));

        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return true;
        }

        // Database lookup fallback if table exists
        if (Schema::hasTable('disposable_email_domains')) {
            return DB::table('disposable_email_domains')->where('domain', $domain)->exists();
        }

        return false;
    }
}
