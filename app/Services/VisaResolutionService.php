<?php

namespace App\Services;

/**
 * VisaResolutionService — Resolves visa/work authorization strings into structured metadata.
 *
 * Intent:
 * Normalizes candidate visa status values (e.g. "GC-EAD", "H1B", "US Citizen", "UAE Golden Visa")
 * into display-ready components: country, country flag emoji, visa category type, and legal work eligibility.
 */
class VisaResolutionService
{
    /**
     * Resolves raw visa status string into structured details.
     *
     * @param string|null $rawVisa
     * @return array
     */
    public static function resolve(?string $rawVisa): array
    {
        $raw = trim((string) $rawVisa);

        if ($raw === '') {
            return [
                'raw' => '',
                'has_visa' => false,
                'name' => 'Not Specified',
                'country' => '—',
                'country_flag' => '🌐',
                'country_display' => 'Not Specified',
                'type' => 'Not Specified',
                'eligibility' => 'Not Specified',
                'badge_style' => 'background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;',
                'sponsorship_needed' => false,
            ];
        }

        // Comprehensive lookup table for standard recruitment hub visas
        $map = [
            // United States (US)
            'US Citizen' => [
                'name' => 'US Citizen (USC)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Citizen' => [
                'name' => 'US Citizen (USC)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Green Card' => [
                'name' => 'Permanent Resident (Green Card)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Lawful Permanent Resident (LPR)',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'GC-EAD' => [
                'name' => 'GC-EAD (Adjustment of Status)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Employment Authorization Document (Form I-765 / AOS)',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'GC EAD' => [
                'name' => 'GC-EAD (Adjustment of Status)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Employment Authorization Document (Form I-765 / AOS)',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'H1B' => [
                'name' => 'H-1B (Specialty Occupation)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Non-Immigrant Work Visa (Specialty Occupation)',
                'eligibility' => 'Employer-Sponsored (Transferable)',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'H-1B' => [
                'name' => 'H-1B (Specialty Occupation)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Non-Immigrant Work Visa (Specialty Occupation)',
                'eligibility' => 'Employer-Sponsored (Transferable)',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'H-4 EAD' => [
                'name' => 'H-4 EAD (Spouse Dependent)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Dependent Employment Authorization (Form I-765)',
                'eligibility' => 'Authorized to Work (Open)',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'H4-EAD' => [
                'name' => 'H-4 EAD (Spouse Dependent)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Dependent Employment Authorization (Form I-765)',
                'eligibility' => 'Authorized to Work (Open)',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'L-2 EAD' => [
                'name' => 'L-2 EAD (Intracompany Dependent)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Intracompany Transferee Dependent EAD',
                'eligibility' => 'Authorized to Work (Open)',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'L2-EAD' => [
                'name' => 'L-2 EAD (Intracompany Dependent)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Intracompany Transferee Dependent EAD',
                'eligibility' => 'Authorized to Work (Open)',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'TN Permit' => [
                'name' => 'TN Visa (USMCA - Canada / Mexico)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'USMCA Professional Non-Immigrant',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'OPT-EAD' => [
                'name' => 'OPT / STEM-OPT EAD',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'F-1 Post-Graduation / STEM Extension EAD',
                'eligibility' => 'Authorized to Work (Time-Bound)',
                'badge' => 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;',
            ],
            'OPT' => [
                'name' => 'OPT / STEM-OPT EAD',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'F-1 Post-Graduation / STEM Extension EAD',
                'eligibility' => 'Authorized to Work (Time-Bound)',
                'badge' => 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;',
            ],
            'CPT' => [
                'name' => 'CPT (Curricular Practical Training)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Curricular Practical Training (Student)',
                'eligibility' => 'Student Authorized',
                'badge' => 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;',
            ],
            'E-3' => [
                'name' => 'E-3 (Australian Specialty Occupation)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Australian Specialty Occupation Visa',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'O-1' => [
                'name' => 'O-1 (Extraordinary Ability)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Extraordinary Ability Non-Immigrant',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'L1' => [
                'name' => 'L-1 (Intracompany Transferee)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Intracompany Transferee Visa',
                'eligibility' => 'Employer-Specific',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'Other US EAD' => [
                'name' => 'Other US EAD (TPS / Asylee / DACA)',
                'country' => 'United States',
                'flag' => '🇺🇸',
                'type' => 'Employment Authorization Document',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],

            // Canada (CA)
            'Canadian Citizen' => [
                'name' => 'Canadian Citizen',
                'country' => 'Canada',
                'flag' => '🇨🇦',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Canada PR' => [
                'name' => 'Canada Permanent Resident (PR)',
                'country' => 'Canada',
                'flag' => '🇨🇦',
                'type' => 'Permanent Resident (PR)',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Canada Open Work Permit' => [
                'name' => 'Canada Open Work Permit / PGWP',
                'country' => 'Canada',
                'flag' => '🇨🇦',
                'type' => 'Post-Graduation / Open Work Permit',
                'eligibility' => 'Open Work Authorization',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'Canada Closed Work Permit' => [
                'name' => 'Canada Closed Work Permit (LMIA)',
                'country' => 'Canada',
                'flag' => '🇨🇦',
                'type' => 'Closed Work Permit (LMIA-backed)',
                'eligibility' => 'Employer-Specific',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],

            // India (IN)
            'Indian Citizen' => [
                'name' => 'Indian Citizen',
                'country' => 'India',
                'flag' => '🇮🇳',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'OCI / PIO' => [
                'name' => 'OCI / PIO Cardholder',
                'country' => 'India',
                'flag' => '🇮🇳',
                'type' => 'Overseas Citizen of India (OCI)',
                'eligibility' => 'Lifelong Right to Work',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'India Work Visa' => [
                'name' => 'India Employment Visa',
                'country' => 'India',
                'flag' => '🇮🇳',
                'type' => 'Employment Visa (E-Visa)',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],

            // United Kingdom (UK)
            'UK Citizen' => [
                'name' => 'British Citizen',
                'country' => 'United Kingdom',
                'flag' => '🇬🇧',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'UK ILR' => [
                'name' => 'UK Indefinite Leave to Remain (ILR)',
                'country' => 'United Kingdom',
                'flag' => '🇬🇧',
                'type' => 'Settlement / Indefinite Leave to Remain',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'UK Skilled Worker' => [
                'name' => 'UK Skilled Worker / Tier 2',
                'country' => 'United Kingdom',
                'flag' => '🇬🇧',
                'type' => 'Points-Based Skilled Worker Visa',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'UK Graduate Visa' => [
                'name' => 'UK Graduate Visa',
                'country' => 'United Kingdom',
                'flag' => '🇬🇧',
                'type' => 'Post-Study Graduate Visa',
                'eligibility' => 'Open Work Rights',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],

            // United Arab Emirates & GCC (Dubai / UAE)
            'UAE Golden Visa' => [
                'name' => 'UAE Golden Visa (10-Year Self-Sponsored)',
                'country' => 'United Arab Emirates',
                'flag' => '🇦🇪',
                'type' => '10-Year Long-Term Residence Visa',
                'eligibility' => 'Self-Sponsored / Unrestricted',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'UAE Green Visa' => [
                'name' => 'UAE Green Visa (5-Year Skilled)',
                'country' => 'United Arab Emirates',
                'flag' => '🇦🇪',
                'type' => '5-Year Skilled Professional Visa',
                'eligibility' => 'Self-Sponsored',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'UAE Employment Visa' => [
                'name' => 'UAE Employment Residence Visa',
                'country' => 'United Arab Emirates',
                'flag' => '🇦🇪',
                'type' => 'Standard Employment Visa',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'UAE Freelance Visa' => [
                'name' => 'UAE Freelance / Remote Work Visa',
                'country' => 'United Arab Emirates',
                'flag' => '🇦🇪',
                'type' => 'Freelance / Virtual Work Visa',
                'eligibility' => 'Self-Sponsored',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'UAE National' => [
                'name' => 'UAE / GCC National',
                'country' => 'United Arab Emirates',
                'flag' => '🇦🇪',
                'type' => 'Citizen / National',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'GCC Iqama' => [
                'name' => 'GCC Resident Work Permit / Iqama',
                'country' => 'GCC Region',
                'flag' => '🇸🇦',
                'type' => 'Resident Work Permit (Iqama)',
                'eligibility' => 'Transferable Iqama',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],

            // Singapore (SG)
            'Singapore Citizen' => [
                'name' => 'Singapore Citizen (SC)',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'Citizen / Domestic Right to Work',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Singapore PR' => [
                'name' => 'Singapore Permanent Resident (PR)',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'Permanent Resident (PR)',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Singapore EP' => [
                'name' => 'Singapore Employment Pass (EP)',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'Employment Pass (EP)',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'Singapore S Pass' => [
                'name' => 'Singapore S Pass (Skilled Staff)',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'S Pass (Mid-Level Technical)',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'Singapore ONE Pass' => [
                'name' => 'Singapore ONE Pass / Tech.Pass / PEP',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'Overseas Networks & Expertise Pass',
                'eligibility' => 'Open High-Earner Pass',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Singapore LOC / DP' => [
                'name' => 'Singapore LOC / Dependant Pass',
                'country' => 'Singapore',
                'flag' => '🇸🇬',
                'type' => 'Dependant Pass / Letter of Consent',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],

            // Australia & New Zealand (AU / NZ)
            'Australia Citizen / PR' => [
                'name' => 'Australian Citizen / Permanent Resident',
                'country' => 'Australia',
                'flag' => '🇦🇺',
                'type' => 'Citizen / Permanent Resident',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Australia TSS 482' => [
                'name' => 'Australia TSS Visa (Subclass 482)',
                'country' => 'Australia',
                'flag' => '🇦🇺',
                'type' => 'Temporary Skill Shortage Visa',
                'eligibility' => 'Employer-Sponsored',
                'badge' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
            ],
            'Australia Skilled 189/190' => [
                'name' => 'Australia Skilled Visa (Subclass 189 / 190)',
                'country' => 'Australia',
                'flag' => '🇦🇺',
                'type' => 'Permanent Skilled Migration Visa',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'Australia Graduate 485' => [
                'name' => 'Australia Graduate Visa (Subclass 485)',
                'country' => 'Australia',
                'flag' => '🇦🇺',
                'type' => 'Temporary Graduate Work Visa',
                'eligibility' => 'Open Work Rights',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'New Zealand Citizen / PR' => [
                'name' => 'New Zealand Citizen / Resident',
                'country' => 'New Zealand',
                'flag' => '🇳🇿',
                'type' => 'Citizen / Permanent Resident',
                'eligibility' => 'Unrestricted Work Authorization',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],

            // European Union (EU)
            'EU Citizen' => [
                'name' => 'EU / EEA Citizen',
                'country' => 'European Union',
                'flag' => '🇪🇺',
                'type' => 'EU Freedom of Movement / Citizen',
                'eligibility' => 'Unrestricted EU-wide',
                'badge' => 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;',
            ],
            'EU Blue Card' => [
                'name' => 'EU Blue Card',
                'country' => 'European Union',
                'flag' => '🇪🇺',
                'type' => 'EU Highly-Qualified Worker Permit',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'Authorized Work Permit' => [
                'name' => 'Authorized Work Permit',
                'country' => 'Global',
                'flag' => '🌐',
                'type' => 'National Work Permit',
                'eligibility' => 'Authorized to Work',
                'badge' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            ],
            'Requires Sponsorship' => [
                'name' => 'Requires International Visa Sponsorship',
                'country' => 'International',
                'flag' => '🌐',
                'type' => 'Visa Sponsorship Required',
                'eligibility' => 'Requires Employer Sponsorship',
                'badge' => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                'sponsorship_needed' => true,
            ],
            'Requires International Visa Sponsorship' => [
                'name' => 'Requires International Visa Sponsorship',
                'country' => 'International',
                'flag' => '🌐',
                'type' => 'Visa Sponsorship Required',
                'eligibility' => 'Requires Employer Sponsorship',
                'badge' => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                'sponsorship_needed' => true,
            ],
        ];

        // 1. Direct match in dictionary
        if (isset($map[$raw])) {
            $item = $map[$raw];
            return [
                'raw' => $raw,
                'has_visa' => true,
                'name' => $item['name'],
                'country' => $item['country'],
                'country_flag' => $item['flag'],
                'country_display' => $item['country'] . ' ' . $item['flag'],
                'type' => $item['type'],
                'eligibility' => $item['eligibility'],
                'badge_style' => $item['badge'],
                'sponsorship_needed' => $item['sponsorship_needed'] ?? false,
            ];
        }

        // 2. Pattern matching for country taxonomy format "Country - Subtype"
        if (str_contains($raw, ' - ')) {
            $parts = explode(' - ', $raw, 2);
            $countryPart = trim($parts[0]);
            $typePart = trim($parts[1]);

            $isCitizen = str_contains(strtolower($typePart), 'citizen');
            $isPr = str_contains(strtolower($typePart), 'permanent') || str_contains(strtolower($typePart), 'pr');
            $isSponsor = str_contains(strtolower($typePart), 'sponsorship');

            $eligibility = 'Authorized to Work';
            $badgeStyle = 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;';
            if ($isCitizen || $isPr) {
                $eligibility = 'Unrestricted Work Authorization';
                $badgeStyle = 'background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;';
            } elseif ($isSponsor) {
                $eligibility = 'Requires Employer Sponsorship';
                $badgeStyle = 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;';
            }

            return [
                'raw' => $raw,
                'has_visa' => true,
                'name' => $raw,
                'country' => $countryPart,
                'country_flag' => '🌐',
                'country_display' => $countryPart . ' 🌐',
                'type' => $typePart,
                'eligibility' => $eligibility,
                'badge_style' => $badgeStyle,
                'sponsorship_needed' => $isSponsor,
            ];
        }

        // 3. Fallback for custom text
        return [
            'raw' => $raw,
            'has_visa' => true,
            'name' => $raw,
            'country' => 'Global',
            'country_flag' => '🌐',
            'country_display' => 'Global 🌐',
            'type' => $raw,
            'eligibility' => 'Recorded / Active',
            'badge_style' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
            'sponsorship_needed' => false,
        ];
    }
}
