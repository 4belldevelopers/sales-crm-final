<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Notifications\SuspiciousTenantRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * TenantRiskAssessmentService
 *
 * Evaluates inbound tenant registrations for risk and suspicious indicators
 * (e.g. disposable emails, bot velocities, throwaway domains, synthetic phone numbers).
 *
 * ARCHITECTURAL RULE:
 * Tenants are NEVER blocked or restricted from using the product or marketplace.
 * If risk score >= 35, a passive alarm is raised to SuperAdmin for manual verification,
 * and the company's accountVerify state is flagged as pending review (0).
 */
class TenantRiskAssessmentService
{
    /**
     * Common disposable / throwaway email domain blacklist.
     */
    protected const DISPOSABLE_EMAIL_DOMAINS = [
        'mailinator.com', 'tempmail.com', '10minutemail.com', 'guerrillamail.com',
        'sharklasers.com', 'throwawaymail.com', 'burnermail.io', 'getairmail.com',
        'dispostable.com', 'trashmail.com', 'mytemp.email', 'yopmail.com',
        'fakeinbox.com', 'emailondeck.com', 'temp-mail.org', 'mohmal.com',
        'crazymailing.com', 'generator.email', 'dropmail.me', 'getnada.com',
    ];

    /**
     * High-risk TLDs frequently associated with automated bot registrations.
     */
    protected const HIGH_RISK_TLDS = [
        'top', 'xyz', 'click', 'loan', 'men', 'stream', 'win', 'bid', 'work', 'country',
    ];

    /**
     * Evaluate registration payload in-memory (0 DB reads).
     *
     * @param  array|Request  $payload
     * @return array Contains 'is_suspicious', 'risk_score', 'reasons'
     */
    public function evaluate($payload, ?string $clientIp = null): array
    {
        $email = strtolower(trim($payload['email'] ?? ''));
        $name = trim($payload['name'] ?? '');
        $phone = preg_replace('/\D/', '', (string) ($payload['contact_number'] ?? ''));
        $clientIp = $clientIp ?: request()->ip();

        $riskScore = 0;
        $reasons = [];

        // 1. Disposable Email Check
        $domain = substr(strrchr($email, '@'), 1) ?: '';
        if (in_array($domain, self::DISPOSABLE_EMAIL_DOMAINS, true)) {
            $riskScore += 45;
            $reasons[] = "Disposable / temporary email provider ({$domain})";
        }

        // 2. High-Risk TLD Check
        $tld = substr(strrchr($domain, '.'), 1) ?: '';
        if (in_array($tld, self::HIGH_RISK_TLDS, true)) {
            $riskScore += 25;
            $reasons[] = "High-risk domain extension (.{$tld})";
        }

        // 3. Synthetic or Repeated Phone Digits Check (e.g. 1111111111, 1234567890)
        if (strlen($phone) >= 8) {
            if (preg_match('/^(\d)\1{6,}$/', $phone)) {
                $riskScore += 25;
                $reasons[] = 'Repetitive phone number digits';
            } elseif (in_array($phone, ['1234567890', '0123456789', '9876543210'], true)) {
                $riskScore += 25;
                $reasons[] = 'Sequential placeholder phone number';
            }
        }

        // 4. Name Anomaly (Gibberish or Consonant Clusters)
        if (strlen($name) < 2) {
            $riskScore += 20;
            $reasons[] = 'Name too short';
        } elseif (preg_match('/^[bcdfghjklmnpqrstvwxyz]{6,}$/i', str_replace(' ', '', $name))) {
            $riskScore += 20;
            $reasons[] = 'Unusual consonant pattern in name';
        }

        // 5. Rapid Registration Frequency Check via Redis cache
        if ($clientIp) {
            $ipKey = 'reg_freq:'.md5($clientIp);
            $regCount = (int) Cache::get($ipKey, 0);
            if ($regCount >= 3) {
                $riskScore += 30;
                $reasons[] = "High registration velocity from IP ({$regCount} signups in past hour)";
            }
            Cache::put($ipKey, $regCount + 1, 3600);
        }

        $isSuspicious = ($riskScore >= 35);

        return [
            'is_suspicious' => $isSuspicious,
            'risk_score' => min($riskScore, 100),
            'reasons' => $reasons,
        ];
    }

    /**
     * Notify SuperAdmin if suspicious, without restricting the tenant.
     */
    public function handlePostRegistration(Company $company, User $user, array $assessment): void
    {
        if (! $assessment['is_suspicious']) {
            return;
        }

        try {
            // Retrieve all active SuperAdmin users
            $superAdmins = User::where('is_superadmin', 1)->get();

            if ($superAdmins->isNotEmpty()) {
                Notification::sendNow(
                    $superAdmins,
                    new SuspiciousTenantRegistrationNotification(
                        $company->id,
                        $company->company_name,
                        $user->email,
                        (string) ($company->company_phone ?? $user->mobile ?? ''),
                        (int) $company->company_type,
                        (int) $assessment['risk_score'],
                        $assessment['reasons']
                    )
                );
            }

            Log::warning("[TENANT_RISK_ALERT] Suspicious registration detected for Company #{$company->id} ({$company->company_name}). Score: {$assessment['risk_score']}. Reasons: ".implode('; ', $assessment['reasons']));

        } catch (\Throwable $e) {
            Log::error('[TENANT_RISK_ALERT] Failed to dispatch SuperAdmin notification: '.$e->getMessage());
        }
    }
}
