<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * CandidateOriginResolver
 *
 * Core service governing candidate sourcing attribution, multi-tenant bench ownership,
 * contact confidentiality (anti-poaching guards), and contractual offer term resolution.
 *
 * Sourcing Classifications:
 * 1. Direct Candidate: Standard sourced applicant (direct contact permitted).
 * 2. Internal Bench Candidate: Talent on viewer's own company bench (direct contact permitted).
 * 3. Agency Bench Candidate: Talent on external vendor/staffing partner bench (strict confidentiality,
 *    offer dispatched to Agency RM, real candidate email hidden).
 * 4. Education / Training Bench Candidate: Exception for universities/colleges/academies seeking
 *    direct full-time placement (real candidate email unmasked, Full-Time offer letter default).
 *
 * Tenancy & Security: Explicit tenant validation; never leaks third-party vendor credentials or candidate PII.
 */
class CandidateOriginResolver
{
    /**
     * Known educational/training industry IDs in td_industries
     * 35: Education Management
     * 56: Higher Education
     * 109: Primary/Secondary Education
     * 111: Professional Training & Coaching
     */
    public const EDUCATION_INDUSTRY_IDS = [35, 56, 109, 111];

    /**
     * Resolve candidate origin, confidentiality policy, and recipient routing.
     *
     * @param  int|object  $applicationOrId
     */
    public static function resolve($applicationOrId, ?int $viewerCompanyId = null, ?int $jobId = null): array
    {
        $viewerCompanyId = (int) ($viewerCompanyId ?: auth()->user()->company_id ?? 0);

        // 1. Fetch application record if ID passed
        if (is_numeric($applicationOrId)) {
            $application = DB::table('job_applications')
                ->where('id', (int) $applicationOrId)
                ->first();
        } else {
            $application = (object) $applicationOrId;
        }

        if (! $application) {
            return self::buildDirectPayload(null, 'Candidate Not Found');
        }

        $appId = (int) ($application->id ?? 0);
        $candidateType = trim((string) ($application->condidate_type ?? ''));
        $appCompanyId = (int) ($application->company_id ?? 0);
        $thirdpartyCompanyId = (int) ($application->thirdparty_company_id ?? 0);

        // 2. Check if candidate is NOT a bench candidate
        if ($candidateType !== 'bench_condidate') {
            return self::buildDirectPayload($application, 'Direct Candidate');
        }

        // 3. Resolve bench linkage via rec_candidate_assign
        $rca = DB::table('rec_candidate_assign')
            ->where('source_application_id', $appId)
            ->orderByDesc('id')
            ->first();

        $benchAppId = $rca->source_from_id ?? null;
        $sourceCompanyId = (int) ($rca->source_from_company_id ?? $thirdpartyCompanyId ?: $appCompanyId);

        // 4. Check if Internal Bench (owned by viewer's own organization)
        if ($sourceCompanyId === $viewerCompanyId || ($sourceCompanyId === 0 && $appCompanyId === $viewerCompanyId)) {
            return self::buildInternalBenchPayload($application, $benchAppId, $viewerCompanyId);
        }

        // 5. External Sourcing: Check if vendor company is an Educational / Training Institution
        $sourceCompany = DB::table('companies')
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->where('companies.id', $sourceCompanyId)
            ->select('companies.id', 'companies.company_name', 'companies.industries', 'td_industries.ind_name')
            ->first();

        $isEducationOrTraining = self::isEducationalInstitution($sourceCompany);

        // Resolve vendor relationship manager / coordinator
        $rmContact = self::resolveVendorRmContact($benchAppId, $rca, $sourceCompanyId);
        $agencyContacts = self::fetchVendorAgencyContacts($sourceCompanyId);

        if ($isEducationOrTraining) {
            return self::buildEducationBenchPayload($application, $sourceCompany, $rmContact, $agencyContacts);
        }

        // Standard Staffing Agency Bench Candidate (Strict Confidentiality Guard)
        return self::buildAgencyBenchPayload($application, $sourceCompany, $rmContact, $agencyContacts);
    }

    /**
     * Batch resolve origin metadata for a list of application IDs.
     * Optimized for listings and offer tracker views to prevent N+1 queries.
     *
     * @return array<int, array> Map of applicationId => origin badge data
     */
    public static function resolveBatch(array $applicationIds, int $viewerCompanyId): array
    {
        if (empty($applicationIds)) {
            return [];
        }

        $appRows = DB::table('job_applications')
            ->whereIn('id', $applicationIds)
            ->select('id', 'condidate_type', 'company_id', 'thirdparty_company_id')
            ->get()
            ->keyBy('id');

        $rcaRows = DB::table('rec_candidate_assign')
            ->whereIn('source_application_id', $applicationIds)
            ->select('source_application_id', 'source_from_id', 'source_from_company_id')
            ->get()
            ->groupBy('source_application_id');

        $companyIds = [];
        foreach ($appRows as $app) {
            $rca = $rcaRows->get($app->id)?->first();
            $srcCompanyId = (int) ($rca->source_from_company_id ?? $app->thirdparty_company_id ?: $app->company_id);
            if ($srcCompanyId > 0 && $srcCompanyId !== $viewerCompanyId) {
                $companyIds[$srcCompanyId] = $srcCompanyId;
            }
        }

        $companies = collect();
        if (! empty($companyIds)) {
            $companies = DB::table('companies')
                ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
                ->whereIn('companies.id', array_values($companyIds))
                ->select('companies.id', 'companies.company_name', 'companies.industries', 'td_industries.ind_name')
                ->get()
                ->keyBy('id');
        }

        $result = [];
        foreach ($applicationIds as $appId) {
            $app = $appRows->get($appId);
            if (! $app || $app->condidate_type !== 'bench_condidate') {
                $result[$appId] = [
                    'origin_type' => 'direct',
                    'badge_label' => 'Direct',
                    'badge_class' => 'ot-pill-neutral',
                    'badge_style' => 'background:#ffffff; color:#475569; border:1px solid #e2e8f0;',
                    'badge_icon' => 'fas fa-user',
                ];

                continue;
            }

            $rca = $rcaRows->get($appId)?->first();
            $srcCompanyId = (int) ($rca->source_from_company_id ?? $app->thirdparty_company_id ?: $app->company_id);

            if ($srcCompanyId === $viewerCompanyId || ($srcCompanyId === 0 && (int) $app->company_id === $viewerCompanyId)) {
                $result[$appId] = [
                    'origin_type' => 'internal_bench',
                    'badge_label' => 'Internal Bench',
                    'badge_class' => 'ot-pill-internal-bench',
                    'badge_style' => 'background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;',
                    'badge_icon' => 'fas fa-users',
                ];
            } else {
                $cmp = $companies->get($srcCompanyId);
                if (self::isEducationalInstitution($cmp)) {
                    $result[$appId] = [
                        'origin_type' => 'education_bench',
                        'badge_label' => 'Campus Talent',
                        'badge_class' => 'ot-pill-campus',
                        'badge_style' => 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;',
                        'badge_icon' => 'fas fa-graduation-cap',
                    ];
                } else {
                    $result[$appId] = [
                        'origin_type' => 'agency_bench',
                        'badge_label' => 'Agency Bench',
                        'badge_class' => 'ot-pill-agency-bench',
                        'badge_style' => 'background:#fef3c7; color:#92400e; border:1px solid #fcd34d;',
                        'badge_icon' => 'fas fa-handshake',
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Determines whether a company is an Educational or Training Institution.
     * Checks both official td_industries linkage and semantic keyword heuristics.
     */
    public static function isEducationalInstitution(?object $company): bool
    {
        if (! $company) {
            return false;
        }

        $industryId = (int) ($company->industries ?? 0);
        if (in_array($industryId, self::EDUCATION_INDUSTRY_IDS, true)) {
            return true;
        }

        $industryName = strtolower(trim((string) ($company->ind_name ?? '')));
        if ($industryName !== '') {
            $keywords = ['education', 'training', 'university', 'college', 'academy', 'institute', 'school', 'coaching'];
            foreach ($keywords as $kw) {
                if (str_contains($industryName, $kw)) {
                    return true;
                }
            }
        }

        $companyName = strtolower(trim((string) ($company->company_name ?? '')));
        if ($companyName !== '') {
            $nameKeywords = ['university', 'college', 'academy', 'institute of', 'training academy', 'polytechnic', 'edtech', 'school of'];
            foreach ($nameKeywords as $nkw) {
                if (str_contains($companyName, $nkw)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Resolve vendor relationship manager (RM) / contact.
     */
    protected static function resolveVendorRmContact(?int $benchAppId, ?object $rca, int $sourceCompanyId): ?object
    {
        // Tier 1: Check bench_assign table via bench_applications_id
        if (! empty($benchAppId)) {
            $rmUser = DB::table('bench_assign')
                ->join('users', 'users.id', '=', 'bench_assign.user_id')
                ->where('bench_assign.bench_applications_id', $benchAppId)
                ->where('users.status', 'active')
                ->select('users.id', 'users.name', 'users.email', 'users.mobile')
                ->first();

            if ($rmUser && ! empty($rmUser->email)) {
                return $rmUser;
            }
        }

        // Tier 2: Check rec_candidate_assign.source_from_user_id
        if (! empty($rca->source_from_user_id)) {
            $rmUser = DB::table('users')
                ->where('id', $rca->source_from_user_id)
                ->where('status', 'active')
                ->select('id', 'name', 'email', 'mobile')
                ->first();

            if ($rmUser && ! empty($rmUser->email)) {
                return $rmUser;
            }
        }

        // Tier 3: Root user / Primary admin of the vendor company
        if ($sourceCompanyId > 0) {
            $rmUser = DB::table('users')
                ->where('company_id', $sourceCompanyId)
                ->where('status', 'active')
                ->orderByDesc('root_user')
                ->orderBy('id', 'asc')
                ->select('id', 'name', 'email', 'mobile')
                ->first();

            if ($rmUser && ! empty($rmUser->email)) {
                return $rmUser;
            }
        }

        return null;
    }

    /**
     * Fetch all active team members of the vendor agency to populate "+ Add Email" drawer.
     */
    protected static function fetchVendorAgencyContacts(int $sourceCompanyId): array
    {
        if ($sourceCompanyId <= 0) {
            return [];
        }

        return DB::table('users')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
            ->where('users.company_id', $sourceCompanyId)
            ->where('users.status', 'active')
            ->whereNotNull('users.email')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users_profile.designation as display_name',
                'companies.company_name'
            )
            ->groupBy('users.id')
            ->get()
            ->toArray();
    }

    /**
     * Mask an email address to protect privacy (e.g. j***e@domain.com).
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email) || ! str_contains($email, '@')) {
            return '••••••@••••.com';
        }

        [$user, $domain] = explode('@', $email, 2);
        $len = strlen($user);

        if ($len <= 2) {
            $maskedUser = substr($user, 0, 1).'***';
        } else {
            $maskedUser = substr($user, 0, 1).str_repeat('*', min(4, $len - 2)).substr($user, -1);
        }

        return $maskedUser.'@'.$domain;
    }

    /**
     * Build Direct Candidate payload
     */
    protected static function buildDirectPayload(?object $application, string $label): array
    {
        $realEmail = $application->email ?? '';
        $realName = $application->full_name ?? 'Candidate';

        return [
            'origin_type' => 'direct',
            'is_bench' => false,
            'is_confidential' => false,
            'badge_label' => null,
            'badge_class' => '',
            'badge_style' => '',
            'badge_icon' => '',
            'source_company_id' => (int) ($application->company_id ?? 0),
            'source_company_name' => '',
            'candidate_name' => $realName,
            'candidate_email' => $realEmail,
            'display_email' => $realEmail,
            'primary_recipient_email' => $realEmail,
            'primary_recipient_name' => $realName,
            'primary_recipient_label' => 'Direct Candidate',
            'cc_recipient_emails' => [],
            'agency_contacts' => [],
            'agency_rm_name' => null,
            'agency_rm_email' => null,
            'default_template_key' => 'candidate-offer-letter',
            'allowed_template_keys' => ['candidate-offer-letter', 'internship-offer-letter', 'contract-offer-letter'],
            'can_offer_fulltime' => true,
            'can_offer_contract' => true,
            'confidential_reason' => '',
        ];
    }

    /**
     * Build Internal Bench Candidate payload
     */
    protected static function buildInternalBenchPayload(object $application, ?int $benchAppId, int $companyId): array
    {
        $realEmail = $application->email ?? '';
        $realName = $application->full_name ?? 'Candidate';

        return [
            'origin_type' => 'internal_bench',
            'is_bench' => true,
            'is_confidential' => false,
            'badge_label' => 'Internal Bench',
            'badge_class' => 'ot-pill-internal-bench',
            'badge_style' => 'background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;',
            'badge_icon' => 'fas fa-users',
            'source_company_id' => $companyId,
            'source_company_name' => 'Internal Organization',
            'candidate_name' => $realName,
            'candidate_email' => $realEmail,
            'display_email' => $realEmail,
            'primary_recipient_email' => $realEmail,
            'primary_recipient_name' => $realName,
            'primary_recipient_label' => 'Internal Bench Talent',
            'cc_recipient_emails' => [],
            'agency_contacts' => [],
            'agency_rm_name' => null,
            'agency_rm_email' => null,
            'default_template_key' => 'candidate-offer-letter',
            'allowed_template_keys' => ['candidate-offer-letter', 'contract-offer-letter', 'internship-offer-letter'],
            'can_offer_fulltime' => true,
            'can_offer_contract' => true,
            'confidential_reason' => '',
        ];
    }

    /**
     * Build Educational / Training Institution Bench Candidate payload (Exception Rule)
     */
    protected static function buildEducationBenchPayload(object $application, ?object $sourceCompany, ?object $rmContact, array $agencyContacts): array
    {
        $realEmail = $application->email ?? '';
        $realName = $application->full_name ?? 'Candidate';
        $companyName = $sourceCompany->company_name ?? 'Academic Partner';
        $rmEmail = $rmContact->email ?? null;
        $rmName = $rmContact->name ?? 'Placement Coordinator';

        $ccEmails = [];
        if (! empty($rmEmail)) {
            $ccEmails[] = $rmEmail;
        }

        return [
            'origin_type' => 'education_bench',
            'is_bench' => true,
            'is_confidential' => false,
            'badge_label' => 'Campus Talent',
            'badge_class' => 'ot-pill-campus',
            'badge_style' => 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;',
            'badge_icon' => 'fas fa-graduation-cap',
            'source_company_id' => (int) ($sourceCompany->id ?? 0),
            'source_company_name' => $companyName,
            'candidate_name' => $realName,
            'candidate_email' => $realEmail,
            'display_email' => $realEmail,
            'primary_recipient_email' => $realEmail,
            'primary_recipient_name' => $realName,
            'primary_recipient_label' => 'Campus / Training Candidate',
            'cc_recipient_emails' => $ccEmails,
            'agency_contacts' => $agencyContacts,
            'agency_rm_name' => $rmName,
            'agency_rm_email' => $rmEmail,
            'default_template_key' => 'candidate-offer-letter',
            'allowed_template_keys' => ['candidate-offer-letter', 'internship-offer-letter', 'contract-offer-letter'],
            'can_offer_fulltime' => true,
            'can_offer_contract' => true,
            'confidential_reason' => 'Educational / Training partner candidate seeking direct full-time career placement.',
        ];
    }

    /**
     * Build Standard Staffing Agency Bench Candidate payload (Confidentiality Rule)
     */
    protected static function buildAgencyBenchPayload(object $application, ?object $sourceCompany, ?object $rmContact, array $agencyContacts): array
    {
        $realEmail = $application->email ?? '';
        $realName = $application->full_name ?? 'Candidate';
        $companyName = $sourceCompany->company_name ?? 'Partner Staffing Agency';
        $rmEmail = $rmContact->email ?? null;
        $rmName = $rmContact->name ?? ($companyName.' RM');

        $primaryEmail = ! empty($rmEmail) ? $rmEmail : 'agency_rm_pending@jeroworks.com';
        $maskedEmail = self::maskEmail($realEmail);

        return [
            'origin_type' => 'agency_bench',
            'is_bench' => true,
            'is_confidential' => true,
            'badge_label' => 'Agency Bench',
            'badge_class' => 'ot-pill-agency-bench',
            'badge_style' => 'background:#fef3c7; color:#92400e; border:1px solid #fcd34d;',
            'badge_icon' => 'fas fa-handshake',
            'source_company_id' => (int) ($sourceCompany->id ?? 0),
            'source_company_name' => $companyName,
            'candidate_name' => $realName,
            'candidate_email' => $realEmail, // Protected on backend, never exposed in client UI
            'display_email' => 'Confidential (Agency Bench Talent)',
            'masked_candidate_email' => $maskedEmail,
            'primary_recipient_email' => $primaryEmail,
            'primary_recipient_name' => $rmName,
            'primary_recipient_label' => 'Agency Relationship Manager ('.$companyName.')',
            'cc_recipient_emails' => [],
            'agency_contacts' => $agencyContacts,
            'agency_rm_name' => $rmName,
            'agency_rm_email' => $rmEmail,
            'default_template_key' => 'agency-offer-letter',
            'allowed_template_keys' => ['agency-offer-letter', 'candidate-offer-letter', 'contract-offer-letter', 'internship-offer-letter'],
            'can_offer_fulltime' => true,
            'can_offer_contract' => true,
            'confidential_reason' => 'Marketplace confidentiality guard: candidate personal contact information remains private. Offer delivered to Agency RM.',
        ];
    }
}
