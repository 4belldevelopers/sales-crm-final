<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Class InternalClientDuplicateService
 *
 * Dedicated domain service enforcing strict isolation between the Internal Client
 * and Direct Client domains during client validation and creation.
 *
 * NON-NEGOTIABLE CORE BUSINESS RULE:
 * Direct Client data must NEVER be considered a duplicate when creating an Internal Client.
 * Cross-domain equality does NOT equal duplication.
 *
 * Internal Client validation operates exclusively within:
 * - Internal Client Companies (self_company = 0, company_id = tenant)
 * - Internal Client Websites/Domains
 * - Internal Client Contacts
 * - Internal Client Emails
 * - Internal Client Phone Numbers
 *
 * Direct Client domain records (self_company = 1) must NEVER participate in or block
 * Internal Client validation.
 *
 * Tenancy: All queries are strictly scoped by the tenant's company_id.
 */
class InternalClientDuplicateService
{
    /**
     * Normalize company name for consistent, case-insensitive duplicate comparison.
     *
     * Rules:
     * - Trim leading and trailing whitespace
     * - Collapse multiple consecutive internal spaces into a single space
     * - Convert to lowercase
     */
    public static function normalizeCompanyName(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        $trimmed = trim($name);
        $collapsed = preg_replace('/\s+/', ' ', $trimmed);

        return mb_strtolower($collapsed, 'UTF-8');
    }

    /**
     * Canonicalize website/domain for uniform duplicate comparison across all layers.
     *
     * Rules:
     * - Trim whitespace
     * - Strip protocol (http://, https://, //)
     * - Strip leading www.
     * - Strip port numbers (:8080)
     * - Strip trailing slashes and paths
     * - Convert to lowercase
     *
     * Examples:
     * https://example.com          -> example.com
     * http://example.com           -> example.com
     * https://www.example.com      -> example.com
     * http://www.example.com/      -> example.com
     * www.example.com              -> example.com
     * example.com/                 -> example.com
     * EXAMPLE.COM                  -> example.com
     * https://www.Example.com/test -> example.com
     *
     * @return string Canonical domain
     */
    public static function canonicalizeDomain(?string $url): string
    {
        if ($url === null) {
            return '';
        }

        $raw = trim($url);
        if ($raw === '') {
            return '';
        }

        // Add temporary protocol if missing so parse_url reliably extracts host
        $target = $raw;
        if (! preg_match('#^https?://#i', $target)) {
            $target = 'https://'.ltrim($target, '/');
        }

        $parsedHost = parse_url($target, PHP_URL_HOST);
        if (empty($parsedHost)) {
            $parsedHost = $raw;
        }

        $domain = mb_strtolower(trim($parsedHost), 'UTF-8');

        // Remove leading www.
        $domain = preg_replace('/^www\./i', '', $domain);

        // Strip port if present
        $domain = preg_replace('/:\d+$/', '', $domain);

        // Strip any trailing slash
        $domain = rtrim($domain, '/');

        return $domain;
    }

    /**
     * Normalize email address for comparison.
     *
     * Rules:
     * - Trim whitespace
     * - Lowercase
     */
    public static function normalizeEmail(?string $email): string
    {
        if ($email === null) {
            return '';
        }

        return mb_strtolower(trim($email), 'UTF-8');
    }

    /**
     * Normalize phone number for comparison.
     *
     * Strips non-digit characters (whitespace, hyphens, parentheses, plus, dots).
     */
    public static function normalizePhone(?string $phone, ?string $callingCode = null): string
    {
        if ($phone === null) {
            return '';
        }

        // Extract only digits
        $digitsOnly = preg_replace('/\D/', '', $phone);
        if ($digitsOnly === '') {
            return '';
        }

        // Strip leading country dial code if duplicated in the digits string
        if (! empty($callingCode)) {
            $callingDigits = preg_replace('/\D/', '', $callingCode);
            if ($callingDigits !== '' && str_starts_with($digitsOnly, $callingDigits)) {
                $digitsOnly = substr($digitsOnly, strlen($callingDigits));
            }
        }

        // Strip leading zeros if remaining digits length > 7
        return ltrim($digitsOnly, '0');
    }

    /**
     * Check if a company name already exists within the tenant's Internal Client dataset.
     *
     * Direct Client records (self_company = 1) are strictly excluded.
     *
     * @param  int  $tenantId  The current company_id (tenant)
     * @param  int|null  $ignoreClientId  Client ID to ignore during Edit operations
     * @return array|null Null if valid (no duplicate), or array with error details if duplicate
     */
    public function checkCompanyNameDuplicate(int $tenantId, string $companyName, ?int $ignoreClientId = null): ?array
    {
        $normalizedName = self::normalizeCompanyName($companyName);
        if ($normalizedName === '') {
            return null;
        }

        // Query strictly within Internal Client domain: self_company = 0 AND company_id = tenantId
        $query = DB::table('clients')
            ->select('id', 'name')
            ->where('company_id', $tenantId)
            ->where('self_company', 0)
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedName]);

        if (! empty($ignoreClientId)) {
            $query->where('id', '!=', (int) $ignoreClientId);
        }

        $existing = $query->first();

        if ($existing) {
            return [
                'field' => 'name',
                'duplicate' => true,
                'existing_id' => $existing->id,
                'existing_name' => $existing->name,
                'message' => 'This client is already registered internally. A client with this company name already exists in the Internal Client list.',
            ];
        }

        return null;
    }

    /**
     * Check if a website / canonical domain already exists within the tenant's Internal Client dataset.
     *
     * Direct Client records (self_company = 1) are strictly excluded.
     *
     * @return array|null Null if valid, or array with error details if duplicate
     */
    public function checkDomainDuplicate(int $tenantId, string $website, ?int $ignoreClientId = null): ?array
    {
        $canonicalDomain = self::canonicalizeDomain($website);
        if ($canonicalDomain === '') {
            return null;
        }

        // 1. Check against Main Account (Tenant Company) records
        $mainCompany = DB::table('companies')
            ->where('id', $tenantId)
            ->select('id', 'company_name', 'website')
            ->first();

        if ($mainCompany && ! empty($mainCompany->website)) {
            $mainCanonical = self::canonicalizeDomain($mainCompany->website);
            if ($mainCanonical !== '' && $mainCanonical === $canonicalDomain) {
                return [
                    'field' => 'website',
                    'scenario' => 'SCENARIO_MAIN_ACCOUNT_WEBSITE',
                    'duplicate' => true,
                    'existing_name' => $mainCompany->company_name ?? 'Main Account',
                    'canonical_domain' => $canonicalDomain,
                    'message' => 'This website/domain is already registered with your Main Account. It cannot be added as an Internal Client.',
                ];
            }
        }

        // 2. Query strictly within Internal Client domain: self_company = 0 AND company_id = tenantId
        $candidates = DB::table('clients')
            ->select('id', 'name', 'website')
            ->where('company_id', $tenantId)
            ->where('self_company', 0)
            ->where(function ($q) use ($canonicalDomain) {
                $q->where('website', 'LIKE', '%'.$canonicalDomain.'%')
                    ->orWhere('website', '=', $canonicalDomain);
            });

        if (! empty($ignoreClientId)) {
            $candidates->where('id', '!=', (int) $ignoreClientId);
        }

        $matches = $candidates->get();

        foreach ($matches as $candidate) {
            $candidateCanonical = self::canonicalizeDomain($candidate->website);
            if ($candidateCanonical === $canonicalDomain) {
                return [
                    'field' => 'website',
                    'duplicate' => true,
                    'existing_id' => $candidate->id,
                    'existing_name' => $candidate->name,
                    'canonical_domain' => $canonicalDomain,
                    'message' => 'This website/domain is already registered internally. A client with this website already exists in your Internal Client list.',
                ];
            }
        }

        return null;
    }

    /**
     * Check if a contact (email/phone) is duplicated:
     * - Scenario B: Duplicate within the current company/submission draft
     * - Scenario C: Duplicate across another Internal Client belonging to the tenant
     *
     * Direct Client contacts are strictly excluded.
     *
     * @param  array  $draftContacts  List of contacts already added in the current wizard session
     * @return array|null Null if valid, or array with error details if duplicate
     */
    public function checkContactDuplicate(
        int $tenantId,
        string $email,
        ?string $phone = null,
        ?string $callingCode = null,
        ?int $ignoreClientId = null,
        array $draftContacts = []
    ): ?array {
        $normEmail = self::normalizeEmail($email);
        $normPhone = self::normalizePhone($phone, $callingCode);

        // 1. Scenario B: Check duplication in current submission draft
        if ($normEmail !== '') {
            $matchCount = 0;
            foreach ($draftContacts as $draft) {
                $draftEmail = is_array($draft) ? ($draft['email'] ?? ($draft['contact_person_email'] ?? '')) : ($draft->email ?? '');
                if (self::normalizeEmail($draftEmail) === $normEmail) {
                    $matchCount++;
                    if ($matchCount > 1) {
                        return [
                            'field' => 'contact_email',
                            'scenario' => 'SCENARIO_B_CURRENT_COMPANY',
                            'duplicate' => true,
                            'message' => 'This contact is already registered with the current company. Please use the existing contact or enter a different contact.',
                        ];
                    }
                }
            }
        }

        // 2. Check duplication against Main Account records and Tenant Administrator
        if ($normEmail !== '') {
            // A) Check Main Account company_email in companies table
            $mainCompany = DB::table('companies')
                ->where('id', $tenantId)
                ->select('company_email', 'company_name', 'company_phone', 'calling_code')
                ->first();

            if ($mainCompany && ! empty($mainCompany->company_email)) {
                if (self::normalizeEmail($mainCompany->company_email) === $normEmail) {
                    return [
                        'field' => 'contact_email',
                        'scenario' => 'SCENARIO_MAIN_ACCOUNT_CONTACT',
                        'duplicate' => true,
                        'message' => 'This contact email is already registered with your Main Account. It cannot be added to Internal Clients.',
                    ];
                }
            }

            // B) Check Tenant Administrator email(s) in users table (root_user = 1 or admin role)
            $adminUser = DB::table('users')
                ->where('company_id', $tenantId)
                ->where(function ($q) {
                    $q->where('root_user', 1)
                        ->orWhereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('role_user')
                                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                                ->whereColumn('role_user.user_id', 'users.id')
                                ->where('roles.name', 'admin');
                        });
                })
                ->whereRaw('LOWER(TRIM(email)) = ?', [$normEmail])
                ->first();

            if ($adminUser) {
                return [
                    'field' => 'contact_email',
                    'scenario' => 'SCENARIO_MAIN_ACCOUNT_CONTACT',
                    'duplicate' => true,
                    'admin_user_id' => $adminUser->id,
                    'admin_name' => $adminUser->name ?? '',
                    'message' => 'This contact email is already registered with your Main Account (Tenant Administrator). It cannot be added to Internal Clients.',
                ];
            }
        }

        // Check phone against Main Account and Tenant Administrator (if normalized phone >= 7 digits)
        if ($normPhone !== '' && strlen($normPhone) >= 7) {
            $mainPhoneRow = DB::table('companies')
                ->where('id', $tenantId)
                ->select('company_phone', 'calling_code')
                ->first();

            if ($mainPhoneRow && ! empty($mainPhoneRow->company_phone)) {
                $mainPhoneNorm = self::normalizePhone($mainPhoneRow->company_phone, $mainPhoneRow->calling_code ?? null);
                if ($mainPhoneNorm !== '' && (str_contains($mainPhoneNorm, $normPhone) || str_contains($normPhone, $mainPhoneNorm))) {
                    return [
                        'field' => 'contact_phone',
                        'scenario' => 'SCENARIO_MAIN_ACCOUNT_PHONE',
                        'duplicate' => true,
                        'message' => 'This contact phone number is already registered with your Main Account. It cannot be added to Internal Clients.',
                    ];
                }
            }

            $adminPhoneUser = DB::table('users')
                ->where('company_id', $tenantId)
                ->where(function ($q) {
                    $q->where('root_user', 1)
                        ->orWhereExists(function ($sub) {
                            $sub->select(DB::raw(1))
                                ->from('role_user')
                                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                                ->whereColumn('role_user.user_id', 'users.id')
                                ->where('roles.name', 'admin');
                        });
                })
                ->whereNotNull('mobile')
                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mobile, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') LIKE ?", ['%'.$normPhone.'%'])
                ->first();

            if ($adminPhoneUser) {
                return [
                    'field' => 'contact_phone',
                    'scenario' => 'SCENARIO_MAIN_ACCOUNT_PHONE',
                    'duplicate' => true,
                    'admin_user_id' => $adminPhoneUser->id,
                    'admin_name' => $adminPhoneUser->name ?? '',
                    'message' => 'This contact phone number is already registered with your Main Account (Tenant Administrator). It cannot be added to Internal Clients.',
                ];
            }
        }

        // 3. Scenario C: Check duplication across other Internal Clients belonging to this tenant
        if ($normEmail !== '') {
            $query = DB::table('client_contact_info')
                ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                ->select(
                    'client_contact_info.id',
                    'client_contact_info.client_id',
                    'client_contact_info.contact_person_name',
                    'clients.name as client_name'
                )
                ->where('clients.company_id', $tenantId)
                ->where('clients.self_company', 0) // Strictly Internal Clients
                ->whereRaw('LOWER(TRIM(client_contact_info.contact_person_email)) = ?', [$normEmail]);

            if (! empty($ignoreClientId)) {
                $query->where('clients.id', '!=', (int) $ignoreClientId);
            }

            $existingContact = $query->first();

            if ($existingContact) {
                return [
                    'field' => 'contact_email',
                    'scenario' => 'SCENARIO_C_ANOTHER_INTERNAL_CLIENT',
                    'duplicate' => true,
                    'existing_client_id' => $existingContact->client_id,
                    'existing_client_name' => $existingContact->client_name,
                    'existing_contact_name' => $existingContact->contact_person_name,
                    'message' => 'This contact is already registered with another internal company. Please verify the contact details before adding this person.',
                ];
            }
        }

        // 3. Check Phone Duplication across Internal Clients (if normalized phone is valid >= 7 digits)
        if ($normPhone !== '' && strlen($normPhone) >= 7) {
            $phoneQuery = DB::table('client_contact_info')
                ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                ->select(
                    'client_contact_info.id',
                    'client_contact_info.client_id',
                    'client_contact_info.contact_person_name',
                    'clients.name as client_name'
                )
                ->where('clients.company_id', $tenantId)
                ->where('clients.self_company', 0) // Strictly Internal Clients
                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(client_contact_info.contact_person_number, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') LIKE ?", ['%'.$normPhone.'%']);

            if (! empty($ignoreClientId)) {
                $phoneQuery->where('clients.id', '!=', (int) $ignoreClientId);
            }

            $existingPhone = $phoneQuery->first();

            if ($existingPhone) {
                return [
                    'field' => 'contact_phone',
                    'scenario' => 'SCENARIO_C_PHONE_ANOTHER_INTERNAL_CLIENT',
                    'duplicate' => true,
                    'existing_client_id' => $existingPhone->client_id,
                    'existing_client_name' => $existingPhone->client_name,
                    'existing_contact_name' => $existingPhone->contact_person_name,
                    'message' => 'This phone number is already registered with an internal company. Please verify the contact details before adding this person.',
                ];
            }
        }

        return null;
    }

    /**
     * Complete authoritative pre-creation duplicate validation.
     *
     * Validates:
     * 1. Company Name uniqueness in Internal Client domain
     * 2. Canonical Website/Domain uniqueness in Internal Client domain
     * 3. Contact Emails & Phones uniqueness in Internal Client domain
     *
     * @return array|null Null if all checks pass, or array with the first detected duplicate error
     */
    public function validateSubmissionDuplicates(int $tenantId, array $payload, ?int $ignoreClientId = null): ?array
    {
        // 1. Company Name
        $companyName = $payload['name'] ?? '';
        if (! empty($companyName)) {
            $dup = $this->checkCompanyNameDuplicate($tenantId, $companyName, $ignoreClientId);
            if ($dup !== null) {
                return $dup;
            }
        }

        // 2. Website / Domain
        $website = $payload['website'] ?? '';
        if (! empty($website)) {
            $dup = $this->checkDomainDuplicate($tenantId, $website, $ignoreClientId);
            if ($dup !== null) {
                return $dup;
            }
        }

        // 3. Contacts
        $cpNames = $payload['cp_name'] ?? [];
        $cpEmails = $payload['cp_email'] ?? [];
        $cpNumbers = $payload['cp_number'] ?? [];
        $callingCodes = $payload['calling_code'] ?? [];

        $totalContacts = is_array($cpEmails) ? count($cpEmails) : 0;
        $seenEmails = [];

        for ($i = 0; $i < $totalContacts; $i++) {
            $email = trim($cpEmails[$i] ?? '');
            $phone = trim($cpNumbers[$i] ?? '');
            $calling = trim($callingCodes[$i] ?? '+91');

            if ($email === '') {
                continue;
            }

            $norm = self::normalizeEmail($email);
            if (isset($seenEmails[$norm])) {
                // Scenario B: duplicate in current submission
                return [
                    'field' => 'contact_email',
                    'scenario' => 'SCENARIO_B_CURRENT_COMPANY',
                    'duplicate' => true,
                    'index' => $i,
                    'message' => 'This contact is already registered with the current company. Please use the existing contact or enter a different contact.',
                ];
            }
            $seenEmails[$norm] = true;

            // Scenario C: duplicate across other internal clients
            $dup = $this->checkContactDuplicate($tenantId, $email, $phone, $calling, $ignoreClientId, []);
            if ($dup !== null) {
                $dup['index'] = $i;

                return $dup;
            }
        }

        return null;
    }
}
