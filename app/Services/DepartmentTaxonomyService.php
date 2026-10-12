<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class DepartmentTaxonomyService
 *
 * Intent: Provides the authoritative enterprise global department taxonomy for JERO ATS.
 * Organizes departments into 10 hierarchical functional divisions with curated search synonyms.
 * Supports dual-tenancy merging: Platform Master Departments (company_id = NULL) + Tenant Custom Departments (company_id = X).
 *
 * Tenancy: Serves global master reference data and tenant-scoped department extensions.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class DepartmentTaxonomyService
{
    /**
     * The 10 canonical enterprise divisions.
     */
    public const DIVISIONS = [
        1 => [
            'id' => 1,
            'name' => 'Technology & Engineering',
            'slug' => 'technology-engineering',
            'icon' => 'code',
            'sort_order' => 1,
        ],
        2 => [
            'id' => 2,
            'name' => 'Sales & Commercial',
            'slug' => 'sales-commercial',
            'icon' => 'trending-up',
            'sort_order' => 2,
        ],
        3 => [
            'id' => 3,
            'name' => 'Marketing & Growth',
            'slug' => 'marketing-growth',
            'icon' => 'megaphone',
            'sort_order' => 3,
        ],
        4 => [
            'id' => 4,
            'name' => 'Finance & Accounting',
            'slug' => 'finance-accounting',
            'icon' => 'dollar-sign',
            'sort_order' => 4,
        ],
        5 => [
            'id' => 5,
            'name' => 'Human Resources & People',
            'slug' => 'human-resources-people',
            'icon' => 'users',
            'sort_order' => 5,
        ],
        6 => [
            'id' => 6,
            'name' => 'Operations & Supply Chain',
            'slug' => 'operations-supply-chain',
            'icon' => 'truck',
            'sort_order' => 6,
        ],
        7 => [
            'id' => 7,
            'name' => 'Legal, Risk & Compliance',
            'slug' => 'legal-risk-compliance',
            'icon' => 'shield',
            'sort_order' => 7,
        ],
        8 => [
            'id' => 8,
            'name' => 'Manufacturing & Quality',
            'slug' => 'manufacturing-quality',
            'icon' => 'tool',
            'sort_order' => 8,
        ],
        9 => [
            'id' => 9,
            'name' => 'Healthcare & Clinical',
            'slug' => 'healthcare-clinical',
            'icon' => 'activity',
            'sort_order' => 9,
        ],
        10 => [
            'id' => 10,
            'name' => 'Executive & Strategy',
            'slug' => 'executive-strategy',
            'icon' => 'briefcase',
            'sort_order' => 10,
        ],
    ];

    /**
     * Canonical list of standard global departments mapped to divisions with search keywords.
     */
    public const MASTER_DEPARTMENTS = [
        // 1. Technology & Engineering
        ['name' => 'Software Engineering', 'division_id' => 1, 'keywords' => 'sde, dev, engineer, programming, coding, backend, frontend, fullstack'],
        ['name' => 'Cloud & DevOps Infrastructure', 'division_id' => 1, 'keywords' => 'devops, sre, sysadmin, cloud, aws, azure, gcp, kubernetes'],
        ['name' => 'Data Science & AI / ML', 'division_id' => 1, 'keywords' => 'data, ml, ai, machine learning, data engineering, analytics, big data'],
        ['name' => 'Cybersecurity & InfoSec', 'division_id' => 1, 'keywords' => 'security, infosec, cyber, soc, penetration testing, compliance'],
        ['name' => 'Product Management', 'division_id' => 1, 'keywords' => 'pm, product, product manager, roadmap, feature, backlog'],
        ['name' => 'UI / UX & Product Design', 'division_id' => 1, 'keywords' => 'ui, ux, designer, product design, figma, user experience'],
        ['name' => 'IT Operations & Systems', 'division_id' => 1, 'keywords' => 'it, helpdesk, desktop support, network, hardware, systems'],
        ['name' => 'Quality Assurance & Testing (QA)', 'division_id' => 1, 'keywords' => 'qa, testing, test engineer, sdet, automation, manual testing'],
        ['name' => 'Research and Development (R&D)', 'division_id' => 1, 'keywords' => 'r&d, research, development, innovation, patents'],

        // 2. Sales & Commercial
        ['name' => 'Enterprise Sales', 'division_id' => 2, 'keywords' => 'sales, ae, account executive, enterprise, quota, closing'],
        ['name' => 'Inside Sales & Business Development', 'division_id' => 2, 'keywords' => 'bdr, sdr, inside sales, business development, outbound, cold call'],
        ['name' => 'Account Management & Client Servicing', 'division_id' => 2, 'keywords' => 'am, account manager, client relations, renewals, upsell'],
        ['name' => 'Customer Success (CS)', 'division_id' => 2, 'keywords' => 'cs, csm, customer success, onboarding, retention, client satisfaction'],
        ['name' => 'Partnerships & Strategic Alliances', 'division_id' => 2, 'keywords' => 'partnerships, alliances, channel partners, business relations'],
        ['name' => 'Solutions Architecture & Pre-Sales', 'division_id' => 2, 'keywords' => 'solutions architect, sa, pre-sales, technical sales, demo'],
        ['name' => 'Revenue Operations (RevOps)', 'division_id' => 2, 'keywords' => 'revops, sales operations, crm, salesforce, hubspot'],

        // 3. Marketing & Growth
        ['name' => 'Digital Marketing & Growth', 'division_id' => 3, 'keywords' => 'digital marketing, seo, sem, ppc, growth, performance marketing'],
        ['name' => 'Brand & Corporate Communications', 'division_id' => 3, 'keywords' => 'brand, communications, comms, corporate, positioning'],
        ['name' => 'Product Marketing (PMM)', 'division_id' => 3, 'keywords' => 'pmm, product marketing, go-to-market, gtm, product launch'],
        ['name' => 'Content Strategy & Social Media', 'division_id' => 3, 'keywords' => 'content, copywriter, copywriting, social media, blogging'],
        ['name' => 'Public Relations (PR)', 'division_id' => 3, 'keywords' => 'pr, public relations, press, media relations, journalism'],
        ['name' => 'Market Research & Intelligence', 'division_id' => 3, 'keywords' => 'market research, competitive intelligence, consumer insights'],

        // 4. Finance & Accounting
        ['name' => 'Finance and Accounting', 'division_id' => 4, 'keywords' => 'finance, accounting, books, financial reporting, ledger'],
        ['name' => 'Financial Planning & Analysis (FP&A)', 'division_id' => 4, 'keywords' => 'fp&a, forecasting, budget, financial modeling'],
        ['name' => 'Corporate Accounting & Controllership', 'division_id' => 4, 'keywords' => 'controller, gaap, ifrs, balance sheet, reconciliation'],
        ['name' => 'Treasury & Cash Management', 'division_id' => 4, 'keywords' => 'treasury, cash, liquidity, foreign exchange, banking'],
        ['name' => 'Tax & Regulatory Reporting', 'division_id' => 4, 'keywords' => 'tax, direct tax, indirect tax, vat, gst, compliance'],
        ['name' => 'Internal Audit & Risk Controls', 'division_id' => 4, 'keywords' => 'audit, internal audit, risk assessment, sox'],
        ['name' => 'Billing & Payroll Operations', 'division_id' => 4, 'keywords' => 'payroll, billing, accounts payable, accounts receivable, ap, ar'],

        // 5. Human Resources & People
        ['name' => 'Human Resources (HR)', 'division_id' => 5, 'keywords' => 'hr, human resources, people team, personnel'],
        ['name' => 'Talent Acquisition & Recruiting', 'division_id' => 5, 'keywords' => 'recruiting, recruiter, talent acquisition, sourcing, headhunting'],
        ['name' => 'People Operations & HR Generalist', 'division_id' => 5, 'keywords' => 'people ops, hr generalist, hr business partner, hrbp'],
        ['name' => 'Compensation & Benefits (Total Rewards)', 'division_id' => 5, 'keywords' => 'compensation, benefits, total rewards, payroll, equity'],
        ['name' => 'Learning & Organizational Development', 'division_id' => 5, 'keywords' => 'l&d, learning, development, training, leadership'],
        ['name' => 'Employee Relations & Culture', 'division_id' => 5, 'keywords' => 'employee relations, workplace culture, diversity, dei'],

        // 6. Operations & Supply Chain
        ['name' => 'Business Operations (BizOps)', 'division_id' => 6, 'keywords' => 'bizops, operations, process optimization, workflow'],
        ['name' => 'Customer Service & Support', 'division_id' => 6, 'keywords' => 'customer service, support, helpdesk, call center, customer care'],
        ['name' => 'Logistics and Supply Chain Management', 'division_id' => 6, 'keywords' => 'logistics, supply chain, freight, shipping, transportation'],
        ['name' => 'Purchasing or Procurement', 'division_id' => 6, 'keywords' => 'procurement, purchasing, vendor management, sourcing'],
        ['name' => 'Warehousing & Inventory Planning', 'division_id' => 6, 'keywords' => 'warehouse, inventory, fulfillment, 3pl, distribution'],
        ['name' => 'Facilities & Workplace Experience', 'division_id' => 6, 'keywords' => 'facilities, office management, real estate, workplace'],

        // 7. Legal, Risk & Compliance
        ['name' => 'Legal & Corporate Affairs', 'division_id' => 7, 'keywords' => 'legal, law, attorney, legal counsel, in-house counsel, contracts'],
        ['name' => 'Regulatory Compliance & Governance', 'division_id' => 7, 'keywords' => 'compliance, regulatory, governance, risk, policy'],
        ['name' => 'Intellectual Property & Patents', 'division_id' => 7, 'keywords' => 'ip, intellectual property, patents, trademarks, licensing'],
        ['name' => 'Privacy & Data Governance', 'division_id' => 7, 'keywords' => 'privacy, gdpr, ccpa, data protection, compliance'],

        // 8. Manufacturing & Quality
        ['name' => 'Production or Operations', 'division_id' => 8, 'keywords' => 'production, operations, plant, factory, manufacturing'],
        ['name' => 'Quality Assurance (QA) & QC', 'division_id' => 8, 'keywords' => 'qa, qc, quality control, iso, inspection, six sigma'],
        ['name' => 'Health, Safety & Environment (HSE)', 'division_id' => 8, 'keywords' => 'hse, ehs, workplace safety, environmental health, osha'],
        ['name' => 'Industrial & Maintenance Engineering', 'division_id' => 8, 'keywords' => 'industrial engineering, maintenance, equipment, mechanics'],

        // 9. Healthcare & Clinical
        ['name' => 'Clinical Operations & Patient Care', 'division_id' => 9, 'keywords' => 'clinical, healthcare, nursing, medical, patient care, hospital'],
        ['name' => 'Medical Affairs & Clinical Research', 'division_id' => 9, 'keywords' => 'medical affairs, clinical research, pharma, clinical trials'],
        ['name' => 'Health Informatics & Records', 'division_id' => 9, 'keywords' => 'health informatics, medical records, ehr, emr, health tech'],

        // 10. Executive & Strategy
        ['name' => 'Administration', 'division_id' => 10, 'keywords' => 'administration, administrative assistant, office admin, clerical'],
        ['name' => 'Executive Leadership & C-Suite', 'division_id' => 10, 'keywords' => 'executive, c-suite, ceo, coo, cfo, cto, vp, vice president'],
        ['name' => 'Corporate Strategy & Development', 'division_id' => 10, 'keywords' => 'strategy, corporate development, m&a, investments, growth'],
        ['name' => 'Public Affairs & Government Relations', 'division_id' => 10, 'keywords' => 'government affairs, public policy, lobbying, community relations'],
        ['name' => 'Others', 'division_id' => 6, 'keywords' => 'others, miscellaneous, general'],
    ];

    /**
     * Map a department name to a division based on exact or keyword matching.
     */
    public static function detectDivision(string $name): array
    {
        $normalized = Str::lower(trim($name));

        // 1. Direct match in master list
        foreach (self::MASTER_DEPARTMENTS as $master) {
            if (Str::lower($master['name']) === $normalized) {
                return self::DIVISIONS[$master['division_id']] ?? self::DIVISIONS[6];
            }
        }

        // 2. Keyword heuristics
        if (preg_match('/(soft|engineer|dev|code|tech|cloud|cyber|data|ai|ml|ui|ux|architect|product|qa|test|r&d)/i', $normalized)) {
            return self::DIVISIONS[1];
        }
        if (preg_match('/(sale|account|bdr|sdr|client|revenue|commercial|deal)/i', $normalized)) {
            return self::DIVISIONS[2];
        }
        if (preg_match('/(market|brand|growth|seo|content|pr|public relation|media)/i', $normalized)) {
            return self::DIVISIONS[3];
        }
        if (preg_match('/(financ|account|tax|audit|treasur|bill|payroll|cost|budget)/i', $normalized)) {
            return self::DIVISIONS[4];
        }
        if (preg_match('/(hr|human|talent|people|recruit|onboard|employ|culture)/i', $normalized)) {
            return self::DIVISIONS[5];
        }
        if (preg_match('/(logist|supply|procure|purchas|warehous|inventor|transport|fleet)/i', $normalized)) {
            return self::DIVISIONS[6];
        }
        if (preg_match('/(legal|law|complian|risk|govern|patent|privac)/i', $normalized)) {
            return self::DIVISIONS[7];
        }
        if (preg_match('/(manufact|product|plant|factor|qualit|hse|safet|maint)/i', $normalized)) {
            return self::DIVISIONS[8];
        }
        if (preg_match('/(health|clinic|medic|nurs|doctor|pharma|patient)/i', $normalized)) {
            return self::DIVISIONS[9];
        }
        if (preg_match('/(admin|execut|ceo|coo|cfo|strateg|direct|board)/i', $normalized)) {
            return self::DIVISIONS[10];
        }

        // Default to Operations & Supply Chain
        return self::DIVISIONS[6];
    }

    /**
     * Idempotently ensure that platform master departments exist in job_categories.
     * Master departments are seeded with company_id = NULL.
     */
    public static function ensureMasterCatalogSeeded(): void
    {
        $existingMasterNames = DB::table('job_categories')
            ->whereNull('company_id')
            ->pluck('name')
            ->map(fn ($n) => Str::lower(trim($n)))
            ->flip()
            ->toArray();

        $toInsert = [];
        $now = now();

        foreach (self::MASTER_DEPARTMENTS as $master) {
            $key = Str::lower(trim($master['name']));
            if (! isset($existingMasterNames[$key])) {
                $toInsert[] = [
                    'company_id' => null,
                    'name' => $master['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($toInsert)) {
            DB::table('job_categories')->insert($toInsert);
        }
    }

    /**
     * Build a complete tenant manifest bundle combining standard global library and tenant extensions.
     */
    public static function buildTenantManifest(int $companyId, int $version): array
    {
        self::ensureMasterCatalogSeeded();

        // 1. Fetch all master departments (company_id IS NULL)
        $masterRows = DB::table('job_categories')
            ->whereNull('company_id')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        // 2. Fetch tenant-specific departments (company_id = $companyId)
        $tenantRows = DB::table('job_categories')
            ->where('company_id', $companyId)
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        $departments = [];
        $seenNames = [];

        // Tenant custom departments take priority for badge & tracking
        foreach ($tenantRows as $row) {
            $div = self::detectDivision($row->name);
            $normalized = Str::lower(trim($row->name));
            $seenNames[$normalized] = true;

            $keywords = self::getKeywordsForDepartment($row->name);

            $departments[] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'division_id' => $div['id'],
                'division_name' => $div['name'],
                'is_standard' => 0, // Tenant Custom
                'keywords' => $keywords,
            ];
        }

        // Add platform master departments if not already defined as a tenant override
        foreach ($masterRows as $row) {
            $normalized = Str::lower(trim($row->name));
            if (isset($seenNames[$normalized])) {
                continue;
            }

            $div = self::detectDivision($row->name);
            $keywords = self::getKeywordsForDepartment($row->name);

            $departments[] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'division_id' => $div['id'],
                'division_name' => $div['name'],
                'is_standard' => 1, // Standard Global Catalog
                'keywords' => $keywords,
            ];
        }

        // Sort alphabetically by name within division
        usort($departments, function ($a, $b) {
            if ($a['division_id'] !== $b['division_id']) {
                return $a['division_id'] <=> $b['division_id'];
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return [
            'manifest_id' => 'dept-tenant-'.$companyId.'-v'.$version,
            'version' => $version,
            'timestamp' => now()->timestamp,
            'divisions' => array_values(self::DIVISIONS),
            'departments' => $departments,
            'count' => count($departments),
        ];
    }

    /**
     * Retrieve keywords or search synonyms for a department.
     */
    public static function getKeywordsForDepartment(string $name): string
    {
        $normalized = Str::lower(trim($name));
        foreach (self::MASTER_DEPARTMENTS as $master) {
            if (Str::lower($master['name']) === $normalized) {
                return $master['keywords'];
            }
        }

        return '';
    }

    /**
     * Get all divisions.
     */
    public static function getDivisions(): array
    {
        return self::DIVISIONS;
    }

    /**
     * Get all canonical master departments.
     */
    public static function getCanonicalDepartments(): array
    {
        return self::MASTER_DEPARTMENTS;
    }

    /**
     * Classify a department name into its division.
     */
    public static function classifyDepartment(string $name): array
    {
        return self::detectDivision($name);
    }
}
