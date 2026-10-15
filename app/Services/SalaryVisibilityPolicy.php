<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;

/**
 * Class SalaryVisibilityPolicy
 *
 * Intent: Centralized authoritative backend visibility policy governing job compensation
 * visibility across all system boundaries (ATS, APIs, Elasticsearch documents, Agency Marketplace,
 * Career Page, shared links, and data exports).
 *
 * Tenancy Note: Tenant ID scoping ensures confidential salaries are strictly protected
 * while preserving internal visibility for authorized ATS users belonging to the job's tenant.
 *
 * Author: Antigravity AI
 * Date: October 2026
 */
class SalaryVisibilityPolicy
{
    public const VISIBLE_EVERYWHERE = 'everywhere';

    public const ATS_AGENCIES = 'ats_agencies';

    public const ATS_ONLY = 'ats_only';

    public const FULLY_CONFIDENTIAL = 'confidential';

    public const VALID_VISIBILITIES = [
        self::VISIBLE_EVERYWHERE,
        self::ATS_AGENCIES,
        self::ATS_ONLY,
        self::FULLY_CONFIDENTIAL,
    ];

    /**
     * Map of human-readable labels and descriptions.
     */
    public const OPTIONS_META = [
        self::VISIBLE_EVERYWHERE => [
            'label' => 'Visible Everywhere',
            'description' => 'Visible in ATS, authorized agencies, agency marketplace and career page.',
            'icon' => 'eye',
            'theme' => 'everywhere',
        ],
        self::ATS_AGENCIES => [
            'label' => 'ATS + Agencies',
            'description' => 'Visible in ATS and authorized agencies only. Hidden from agency marketplace and career page.',
            'icon' => 'users',
            'theme' => 'agencies',
        ],
        self::ATS_ONLY => [
            'label' => 'ATS Only',
            'description' => 'Visible only in ATS. Hidden from agencies, agency marketplace and career page.',
            'icon' => 'lock',
            'theme' => 'ats',
        ],
        self::FULLY_CONFIDENTIAL => [
            'label' => 'Fully Confidential',
            'description' => 'Hidden everywhere. Visible only to authorized ATS users.',
            'icon' => 'lock-red',
            'theme' => 'confidential',
        ],
    ];

    /**
     * Normalize raw visibility string into one of the 4 canonical values.
     */
    public static function normalize(?string $visibility): string
    {
        $v = strtolower(trim((string) $visibility));
        $v = preg_replace('/[^a-z0-9]+/', '_', $v);
        $v = trim((string) $v, '_');

        return match ($v) {
            'visible_everywhere', 'everywhere', 'all', '1' => self::VISIBLE_EVERYWHERE,
            'ats_agencies', 'ats_and_agencies', 'agencies', '2' => self::ATS_AGENCIES,
            'ats_only', 'ats', '3' => self::ATS_ONLY,
            'fully_confidential', 'confidential', 'private', '4' => self::FULLY_CONFIDENTIAL,
            default => self::VISIBLE_EVERYWHERE,
        };
    }

    /**
     * Get metadata for a visibility key.
     */
    public static function getMeta(?string $visibility): array
    {
        $v = self::normalize($visibility);

        return self::OPTIONS_META[$v] ?? self::OPTIONS_META[self::VISIBLE_EVERYWHERE];
    }

    /**
     * Get human readable label for a visibility key.
     */
    public static function getLabel(?string $visibility): string
    {
        return self::getMeta($visibility)['label'] ?? 'Visible Everywhere';
    }

    /**
     * Check whether salary is viewable in a specific context.
     *
     * Contexts:
     * - 'ats': Internal ATS user view
     * - 'agency' / 'authorized_agency': Authorized agency/vendor assigned to the job
     * - 'marketplace': Agency Marketplace / Public talent trading
     * - 'career_page': Career site / Applicant / Public job board
     * - 'api': External REST/GraphQL APIs
     * - 'export': Data exports (CSV/Excel/PDF)
     * - 'shared_link': Shared candidate or job link
     *
     * @param  Job|array|object  $job
     */
    public static function canViewSalary($job, ?User $user = null, string $context = 'ats'): bool
    {
        $rawVisibility = is_array($job)
            ? ($job['salary_visibility'] ?? null)
            : ($job->salary_visibility ?? null);

        $visibility = self::normalize($rawVisibility);

        // 1. Visible Everywhere — open to all verified surfaces
        if ($visibility === self::VISIBLE_EVERYWHERE) {
            return true;
        }

        // 2. ATS Internal context
        if ($context === 'ats') {
            if ($visibility === self::FULLY_CONFIDENTIAL) {
                return self::isAuthorizedAtsUser($job, $user);
            }

            return true;
        }

        // 3. Authorized Agency context
        if (in_array($context, ['agency', 'authorized_agency'], true)) {
            return $visibility === self::ATS_AGENCIES;
        }

        // 4. In all other external contexts (marketplace, career_page, api, export, shared_link):
        // If user is explicitly an authorized ATS user doing an internal export/action, allow.
        if (in_array($context, ['api', 'export'], true) && self::isAuthorizedAtsUser($job, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a user is an authorized ATS user for this job.
     *
     * @param  Job|array|object  $job
     */
    public static function isAuthorizedAtsUser($job, ?User $user = null): bool
    {
        if (! $user && auth()->check()) {
            $user = auth()->user();
        }

        if (! $user) {
            return false;
        }

        // Superadmin bypass
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $jobCompanyId = is_array($job)
            ? ($job['company_id'] ?? $job['comId'] ?? null)
            : ($job->company_id ?? $job->comId ?? null);

        if ($jobCompanyId !== null && (int) $user->company_id === (int) $jobCompanyId) {
            return true;
        }

        return false;
    }

    /**
     * Filter and mask salary data in-place if not viewable in given context.
     *
     * @param  array|object  $jobData
     * @return array|object
     */
    public static function filterSalaryData($jobData, string $context = 'external', ?User $user = null)
    {
        if (self::canViewSalary($jobData, $user, $context)) {
            if (is_array($jobData)) {
                $jobData['is_salary_confidential'] = false;
            } elseif (is_object($jobData)) {
                $jobData->is_salary_confidential = false;
            }

            return $jobData;
        }

        if (is_array($jobData)) {
            $jobData['min_salary'] = null;
            $jobData['max_salary'] = null;
            $jobData['min_salary_currency'] = null;
            $jobData['max_salary_currency'] = null;
            $jobData['min_sal_mode'] = null;
            $jobData['max_sal_mode'] = null;
            $jobData['min_yearly'] = 0.00;
            $jobData['max_yearly'] = 0.00;
            $jobData['min_monthly'] = 0.00;
            $jobData['max_monthly'] = 0.00;
            $jobData['min_hourly'] = 0.00;
            $jobData['max_hourly'] = 0.00;
            $jobData['salary_display'] = 'Confidential';
            $jobData['is_salary_confidential'] = true;
        } elseif (is_object($jobData)) {
            $jobData->min_salary = null;
            $jobData->max_salary = null;
            $jobData->min_salary_currency = null;
            $jobData->max_salary_currency = null;
            $jobData->min_sal_mode = null;
            $jobData->max_sal_mode = null;
            $jobData->min_yearly = 0.00;
            $jobData->max_yearly = 0.00;
            $jobData->min_monthly = 0.00;
            $jobData->max_monthly = 0.00;
            $jobData->min_hourly = 0.00;
            $jobData->max_hourly = 0.00;
            $jobData->salary_display = 'Confidential';
            $jobData->is_salary_confidential = true;
        }

        return $jobData;
    }
}
