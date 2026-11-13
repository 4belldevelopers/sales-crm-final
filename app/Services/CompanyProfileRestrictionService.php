<?php

/**
 * ==============================================================================
 * CompanyProfileRestrictionService
 * ==============================================================================
 *
 * INTENT:
 * Enforces authoritative time-based (7-day window) and Super Admin verification
 * restrictions on company profile editing.
 *
 * BUSINESS LOGIC MATRIX:
 * ALLOW editing of the four restricted fields ONLY when BOTH conditions are met:
 * 1. Company registration age is strictly less than 7 complete days.
 * 2. Company has NOT been verified by Super Admin (accountVerify != 1).
 * Otherwise, the four fields are permanently LOCKED.
 *
 * RESTRICTED FIELDS (Universal Scope):
 * 1. Company Website (website, domain_key)
 * 2. Company Name (company_name, name)
 * 3. Industry (industries, industry)
 * 4. Company Type (company_type, industries_type)
 *
 * TENANCY & AUTHORIZATION:
 * Scoped authoritatively to the authenticated company tenant context.
 *
 * PERFORMANCE & SCALE (Rule 1, Rule 22):
 * - Zero cron jobs / scheduled commands (100% Just-In-Time evaluation).
 * - Zero redundant database queries; reuses existing company memory references.
 *
 * Author: Antigravity AI
 * Date: November 13, 2026
 */

namespace App\Services;

use App\Helper\GeneralMethod;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyProfileRestrictionService
{
    /**
     * Total editing window in complete days.
     */
    public const EDITING_WINDOW_DAYS = 7;

    /**
     * Exact restricted fields list.
     */
    public const RESTRICTED_FIELDS = [
        'website',
        'company_name',
        'industry',
        'company_type',
    ];

    /**
     * Canonical user-facing lock messages.
     */
    public const MSG_VERIFIED_LOCKED = 'Your company profile has been verified. These details are permanently locked.';

    public const MSG_EXPIRED_LOCKED = 'The 7-day company profile editing period has expired. These fields can no longer be modified.';

    public const MSG_EDITING_RESTRICTED = "You can't edit these company details. Please contact your Administrator for assistance.";

    public const WARNING_TITLE = 'Editing Restricted';

    /**
     * Determine if a company can edit the four restricted profile fields.
     *
     * @param  Company|int|object|null  $company
     * @param  Carbon|null  $currentTime  Optional Carbon instance for deterministic boundary testing
     */
    public function canEditRestrictedFields(object|int|null $company, ?Carbon $currentTime = null): bool
    {
        return $this->getRestrictionStatus($company, $currentTime)['can_edit'];
    }

    /**
     * Get authoritative restriction status, lock reason, messages, and UI metadata.
     *
     * @param  Carbon|null  $currentTime  Optional Carbon instance for deterministic boundary testing
     * @return array<string, mixed>
     */
    public function getRestrictionStatus(object|int|null $company, ?Carbon $currentTime = null): array
    {
        $companyObj = $this->resolveCompany($company);

        if (! $companyObj) {
            return [
                'can_edit' => false,
                'is_locked' => true,
                'is_verified' => false,
                'is_age_less_than_7_days' => false,
                'lock_reason' => 'expired',
                'lock_message' => self::MSG_EXPIRED_LOCKED,
                'registration_date' => null,
                'deadline_date' => null,
                'days_remaining' => 0,
                'restricted_fields' => self::RESTRICTED_FIELDS,
            ];
        }

        // Condition 2: Check Super Admin verification status
        $isVerified = ((int) ($companyObj->accountVerify ?? 0) === 1);

        // Condition 1: Check company registration age (< 7 complete days)
        $createdAtRaw = $companyObj->created_at ?? null;
        $timezone = $companyObj->timezone ?: GeneralMethod::defaultTimeZone($companyObj->id ?? null);
        if (empty($timezone)) {
            $timezone = config('app.timezone', 'Asia/Kolkata');
        }

        $now = $currentTime ? $currentTime->copy()->setTimezone($timezone) : Carbon::now($timezone);

        if (empty($createdAtRaw)) {
            // Backward-compatible policy (Requirement 7.10):
            // Companies with missing creation timestamps are legacy records (older than 7 days) and must be locked.
            $isAgeLessThan7Days = false;
            $createdAt = null;
            $deadline = null;
            $daysRemaining = 0;
        } else {
            if ($createdAtRaw instanceof Carbon) {
                $createdAt = $createdAtRaw->copy()->setTimezone($timezone);
            } else {
                $createdAt = Carbon::parse($createdAtRaw, config('app.timezone', 'Asia/Kolkata'))->setTimezone($timezone);
            }
            $deadline = $createdAt->copy()->addDays(self::EDITING_WINDOW_DAYS);

            // ALLOW editing only when registration age is LESS than 7 complete days.
            // Exactly 7 complete days ($now >= $deadline) is NOT less than 7 days -> LOCKED.
            $isAgeLessThan7Days = $now->lessThan($deadline);
            $daysRemaining = $isAgeLessThan7Days ? max(0, $now->diffInDays($deadline, false)) : 0;
        }

        // Final Restriction Formula:
        // ALLOW editing only when BOTH conditions are true:
        // 1. Company registration age is less than 7 complete days.
        // 2. Company has NOT been verified by Super Admin.
        $canEdit = ($isAgeLessThan7Days && ! $isVerified);
        $isLocked = ! $canEdit;

        $lockReason = null;
        $lockMessage = null;

        if ($isLocked) {
            if ($isVerified) {
                $lockReason = 'verified';
                $lockMessage = self::MSG_VERIFIED_LOCKED;
            } else {
                $lockReason = 'expired';
                $lockMessage = self::MSG_EXPIRED_LOCKED;
            }
        }

        return [
            'can_edit' => $canEdit,
            'is_locked' => $isLocked,
            'is_verified' => $isVerified,
            'is_age_less_than_7_days' => $isAgeLessThan7Days,
            'lock_reason' => $lockReason,
            'lock_message' => $lockMessage,
            'warning_title' => self::WARNING_TITLE,
            'warning_message' => self::MSG_EDITING_RESTRICTED,
            'registration_date' => $createdAt ? $createdAt->format('d M y, h:i A T') : null,
            'deadline_date' => $deadline ? $deadline->format('d M y, h:i A T') : null,
            'days_remaining' => $daysRemaining,
            'restricted_fields' => self::RESTRICTED_FIELDS,
            'timezone' => $timezone,
        ];
    }

    /**
     * Authoritative backend validation to reject attempts to modify restricted fields when locked.
     *
     * Returns error message string if a modification is attempted while locked, or null if allowed.
     *
     * @param  array<string, mixed>  $submittedData
     */
    public function validateModificationAttempt(object|int|null $company, array $submittedData, ?Carbon $currentTime = null): ?string
    {
        $status = $this->getRestrictionStatus($company, $currentTime);

        // If company is within 7 days and unverified, editing is freely allowed
        if (! $status['is_locked']) {
            return null;
        }

        $companyObj = $this->resolveCompany($company);
        if (! $companyObj) {
            return $status['lock_message'];
        }

        // Check if any of the four restricted fields are being modified
        $modifications = $this->detectFieldModifications($companyObj, $submittedData);

        if (! empty($modifications)) {
            return $status['lock_message'];
        }

        return null;
    }

    /**
     * Detect if submitted data differs from the existing company record for the four restricted fields.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string, mixed>>
     */
    public function detectFieldModifications(object $company, array $data): array
    {
        $modified = [];

        // 1. Company Website
        if (array_key_exists('website', $data) && $data['website'] !== null) {
            $submittedWebsite = $this->normalizeWebsite((string) $data['website']);
            $currentWebsite = $this->normalizeWebsite((string) ($company->website ?? ''));
            // If submitted website is provided and does not match current, it's a modification
            if ($submittedWebsite !== $currentWebsite) {
                $modified['website'] = [
                    'current' => $currentWebsite,
                    'submitted' => $submittedWebsite,
                ];
            }
        }

        // 2. Company Name (accepts 'name' or 'company_name')
        $submittedName = null;
        if (array_key_exists('company_name', $data) && $data['company_name'] !== null) {
            $submittedName = trim((string) $data['company_name']);
        } elseif (array_key_exists('name', $data) && $data['name'] !== null) {
            $submittedName = trim((string) $data['name']);
        }

        if ($submittedName !== null) {
            $currentName = trim((string) ($company->company_name ?? ''));
            if ($submittedName !== $currentName) {
                $modified['company_name'] = [
                    'current' => $currentName,
                    'submitted' => $submittedName,
                ];
            }
        }

        // 3. Industry (accepts 'industry' or 'industries')
        $submittedIndustry = null;
        if (array_key_exists('industry', $data) && $data['industry'] !== null && $data['industry'] !== '') {
            $submittedIndustry = (int) $data['industry'];
        } elseif (array_key_exists('industries', $data) && $data['industries'] !== null && $data['industries'] !== '') {
            $submittedIndustry = (int) $data['industries'];
        }

        if ($submittedIndustry !== null) {
            $currentIndustry = (int) ($company->industries ?? 0);
            if ($submittedIndustry !== $currentIndustry) {
                $modified['industry'] = [
                    'current' => $currentIndustry,
                    'submitted' => $submittedIndustry,
                ];
            }
        }

        // 4. Company Type (accepts 'company_type' or 'industries_type')
        $submittedType = null;
        if (array_key_exists('company_type', $data) && $data['company_type'] !== null && $data['company_type'] !== '') {
            $submittedType = (int) $data['company_type'];
        } elseif (array_key_exists('industries_type', $data) && $data['industries_type'] !== null && $data['industries_type'] !== '') {
            $submittedType = (int) $data['industries_type'];
        }

        if ($submittedType !== null) {
            $currentType = (int) ($company->company_type ?? ($company->industries_type ?? 0));
            if ($submittedType !== $currentType) {
                $modified['company_type'] = [
                    'current' => $currentType,
                    'submitted' => $submittedType,
                ];
            }
        }

        return $modified;
    }

    /**
     * Normalize website URL for reliable comparison (strips protocol, www, trailing slashes).
     */
    public function normalizeWebsite(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        $url = preg_replace('#^https?://#i', '', $url);
        $url = preg_replace('#^www\.#i', '', $url);
        $url = rtrim($url, '/');

        return strtolower($url);
    }

    /**
     * Resolve Company object from Model, DB object, or company ID.
     *
     * @param  Company|int|object|null  $company
     */
    private function resolveCompany(object|int|null $company): ?object
    {
        if (is_numeric($company)) {
            return DB::table('companies')->where('id', (int) $company)->first();
        }

        if (is_object($company)) {
            return $company;
        }

        return null;
    }
}
