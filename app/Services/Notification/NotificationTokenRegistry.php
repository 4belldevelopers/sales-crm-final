<?php

namespace App\Services\Notification;

/**
 * NotificationTokenRegistry — Canonical Registry & Context-Aware Replacement Engine.
 *
 * Intent: Houses the 96 verified production merge tokens across 5 functional domains.
 * Implements context-aware sanitization and substitution for template previews,
 * and exposes organized token metadata for the Authoring Studio 1-click palette.
 * Tenancy: Global Super Admin scope (tenant_id = 0).
 */
class NotificationTokenRegistry
{
    /**
     * Domain constant definitions.
     */
    public const DOMAIN_JOB = 'job';
    public const DOMAIN_CANDIDATE = 'candidate';
    public const DOMAIN_PEOPLE = 'people';
    public const DOMAIN_PARTNER = 'partner';
    public const DOMAIN_ACTION = 'action';

    /**
     * The 96 verified canonical tokens grouped by semantic domain with deterministic mock values.
     *
     * @var array<string, array{domain: string, label: string, mock: string, is_url: bool}>
     */
    protected static array $registry = [
        // --- DOMAIN 1: JOB & POSITION ---
        '[{JOB_TITLE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Title (Uppercase)', 'mock' => 'Senior Full-Stack Engineer', 'is_url' => false],
        '[JOB_TITLE]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Title (Bracketed)', 'mock' => 'Senior Full-Stack Engineer', 'is_url' => false],
        '[{job_title}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Title (Lowercase)', 'mock' => 'Senior Full-Stack Engineer', 'is_url' => false],
        '[title]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Position Title', 'mock' => 'Senior Full-Stack Engineer', 'is_url' => false],
        '[{JOB_ID}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Requisition ID', 'mock' => 'REQ-88210', 'is_url' => false],
        '[JOB_ID]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job ID (Bracketed)', 'mock' => 'REQ-88210', 'is_url' => false],
        '[{job_id}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job ID (Lowercase)', 'mock' => 'REQ-88210', 'is_url' => false],
        '[{JobID}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job ID (CamelCase)', 'mock' => 'REQ-88210', 'is_url' => false],
        '[jobId]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job ID (Short)', 'mock' => 'REQ-88210', 'is_url' => false],
        '[{JOB_TYPE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Employment Type (Upper)', 'mock' => 'Full-Time (C2C / W2)', 'is_url' => false],
        '[{JobType}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Employment Type', 'mock' => 'Full-Time (Permanent)', 'is_url' => false],
        '[{LOCATION}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Location (Upper)', 'mock' => 'Austin, TX', 'is_url' => false],
        '[{location}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Location (Lower)', 'mock' => 'Austin, TX', 'is_url' => false],
        '[{Location}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Location (Title)', 'mock' => 'Austin, TX', 'is_url' => false],
        '[{CITY_NAME}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'City Name', 'mock' => 'Austin', 'is_url' => false],
        '[{remote}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Remote Work Policy', 'mock' => 'Hybrid (2 Days Onsite)', 'is_url' => false],
        '[{SALARY_RANGE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Salary Compensation Range', 'mock' => '$140,000 - $165,000 / Year', 'is_url' => false],
        '[{salaryrange}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Salary Range (Lower)', 'mock' => '$140,000 - $165,000 / Year', 'is_url' => false],
        '[{BILL_RATE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Hourly Bill Rate', 'mock' => '$85.00 / hr', 'is_url' => false],
        '[{RATE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Placement Pay Rate', 'mock' => '$75.00 / hr', 'is_url' => false],
        '[{COMMISSION}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Recruiter Commission', 'mock' => '$5,000.00', 'is_url' => false],
        '[{EXPERIENCE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Required Experience (Years)', 'mock' => '8+ Years', 'is_url' => false],
        '[{EXPERENCE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Required Experience (Typo variant)', 'mock' => '8+ Years', 'is_url' => false],
        '[{EXPERIENCE_RANGE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Experience Range', 'mock' => '7 to 10 Years', 'is_url' => false],
        '[{experience}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Experience (Lower)', 'mock' => '8+ Years', 'is_url' => false],
        '[{Department}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Department Name', 'mock' => 'Enterprise Engineering', 'is_url' => false],
        '[{Qualification}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Educational Qualification', 'mock' => 'B.S. in Computer Science or equivalent', 'is_url' => false],
        '[{EDUCATION}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Required Education', 'mock' => "Bachelor's Degree", 'is_url' => false],
        '[{SKILL}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Required Skills', 'mock' => 'PHP, Laravel, Elasticsearch, MySQL, Vue.js', 'is_url' => false],
        '[{DESIGNATION}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Designation Title', 'mock' => 'Lead Software Engineer', 'is_url' => false],
        '[{NoofOpenings}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Number of Openings', 'mock' => '3 Positions', 'is_url' => false],
        '[{JobDescriptions}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Overview Excerpt', 'mock' => 'We are seeking an experienced architect to oversee high-concurrency microservices...', 'is_url' => false],
        '[{STATUS}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Status (Upper)', 'mock' => 'Active', 'is_url' => false],
        '[Status]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Status (Title)', 'mock' => 'Active', 'is_url' => false],
        '[{status}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Status (Lower)', 'mock' => 'Active', 'is_url' => false],
        '[{TERMS}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Employment Contract Terms', 'mock' => 'Standard Non-Disclosure & Non-Compete agreements apply.', 'is_url' => false],
        '[{COMMENTS}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Internal Recruiter Notes', 'mock' => 'Priority requisition for Q4 platform expansion.', 'is_url' => false],
        '[{OUTSOURCE}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Outsourcing Eligibility', 'mock' => 'Eligible for Authorized Staffing Agencies', 'is_url' => false],
        '[{DATEADD}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Job Creation Date', 'mock' => 'October 28, 2026', 'is_url' => false],
        '[{DATESET}]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Interview Scheduled Date', 'mock' => 'November 02, 2026 at 10:00 AM CST', 'is_url' => false],
        '[REQUEST_DATE]' => ['domain' => self::DOMAIN_JOB, 'label' => 'Requisition Request Date', 'mock' => 'October 25, 2026', 'is_url' => false],
        '{{submitted_at}}' => ['domain' => self::DOMAIN_JOB, 'label' => 'Candidate Submission Timestamp', 'mock' => '2026-10-28 10:15:00', 'is_url' => false],

        // --- DOMAIN 2: CANDIDATE & BENCH ---
        '[{CANDIDATE_NAME}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Full Name (Upper)', 'mock' => 'Alexandria Morgan', 'is_url' => false],
        '[CandidateName]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Name (CamelCase)', 'mock' => 'Alexandria Morgan', 'is_url' => false],
        '[{CANDIDATET_NAME}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Name (Typo variant)', 'mock' => 'Alexandria Morgan', 'is_url' => false],
        '[CandidateEmail]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Email Address', 'mock' => 'alex.morgan@example.com', 'is_url' => false],
        '[{CANDIDATET_EMAIL}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Email (Typo variant)', 'mock' => 'alex.morgan@example.com', 'is_url' => false],
        '[{CANDIDATET_PHONE}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Contact Phone', 'mock' => '+1 (512) 555-0194', 'is_url' => false],
        '[{CANDIDATET_DESINATION}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Current Role', 'mock' => 'Principal Systems Engineer', 'is_url' => false],
        '[{BENCH_CANDIDATE_NAME}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Bench Candidate Full Name', 'mock' => 'Devon Vance', 'is_url' => false],
        '[BENCH_CANDIDATE_NAME]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Bench Candidate (Bracketed)', 'mock' => 'Devon Vance', 'is_url' => false],
        '[{BENCH_ID}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Bench Profile ID', 'mock' => 'BENCH-4091', 'is_url' => false],
        '[BENCH_ID]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Bench ID (Bracketed)', 'mock' => 'BENCH-4091', 'is_url' => false],
        '[BENCH_TITLE]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Bench Candidate Title', 'mock' => 'Senior DevOps & Cloud Specialist', 'is_url' => false],
        '[{NP}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Notice Period Availability', 'mock' => 'Immediate (15 Days max)', 'is_url' => false],
        '[{AVAILABILITY}]' => ['domain' => self::DOMAIN_CANDIDATE, 'label' => 'Candidate Availability Status', 'mock' => 'Available Immediately', 'is_url' => false],

        // --- DOMAIN 3: PEOPLE & ROLES ---
        '[{RECIPIENT_NAME}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Recipient Full Name', 'mock' => 'Marcus Vance', 'is_url' => false],
        '[RecipientName]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Recipient Name (CamelCase)', 'mock' => 'Marcus Vance', 'is_url' => false],
        '{{customer_name}}' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Customer / Client Name', 'mock' => 'Sarah Jenkins', 'is_url' => false],
        '{{customer_email}}' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Customer Email Address', 'mock' => 's.jenkins@clientcorp.com', 'is_url' => false],
        '[{hiringManager}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Hiring Manager Name', 'mock' => 'David Sterling', 'is_url' => false],
        '[{recruiter}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Assigned Recruiter Name', 'mock' => 'Rachel Hayes', 'is_url' => false],
        '[{addedby}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Created By User Name', 'mock' => 'Super Administrator', 'is_url' => false],
        '[{HR_NAME}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'HR Representative Name', 'mock' => 'Emily Watson', 'is_url' => false],
        '[{HR_EMAILl}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'HR Email Address (Typo variant)', 'mock' => 'hr@jero.co.in', 'is_url' => false],
        '[{HR_PHONE}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'HR Phone Number', 'mock' => '+1 (512) 555-0144', 'is_url' => false],
        '[{CAPTAIN_NAME}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Account Captain / Lead', 'mock' => 'Jonathan Miller', 'is_url' => false],
        '[{CAPTAIN_EMAIL}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Captain Email Address', 'mock' => 'j.miller@jero.co.in', 'is_url' => false],
        '[{CAPTAIN_PHONE}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Captain Contact Phone', 'mock' => '+1 (512) 555-0122', 'is_url' => false],
        '[{RM}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Relationship Manager', 'mock' => 'Victoria Bennett', 'is_url' => false],
        '[{EMAIL}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Contact Email Address', 'mock' => 'notifications@jero.co.in', 'is_url' => false],
        '[{PHONE}]' => ['domain' => self::DOMAIN_PEOPLE, 'label' => 'Contact Phone Number', 'mock' => '+1 (800) 555-0100', 'is_url' => false],

        // --- DOMAIN 4: PARTNERS & CLIENTS ---
        '[{COMPANY_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Employer / Organization Name', 'mock' => 'JERO Works Inc.', 'is_url' => false],
        '[COMPANY_NAME]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Organization Name (Bracketed)', 'mock' => 'JERO Works Inc.', 'is_url' => false],
        '{{company_name}}' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Organization Name (Double Brace)', 'mock' => 'JERO Works Inc.', 'is_url' => false],
        '[{CLIENT_COMPANY_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company Name (Upper)', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[CLIENT_COMPANY_NAME]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company (Bracketed)', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[{ClientCompany}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company (PascalCase)', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[{clientCompany}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company (CamelCase)', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[clientCompanyName]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company (Lower Bracket)', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[{COMPANY_CLIENT}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Company Account', 'mock' => 'Apex Cloud Systems LLC', 'is_url' => false],
        '[{CLIENT_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Point of Contact', 'mock' => 'Brandon Clark', 'is_url' => false],
        '[{CLIENT_EMAIL}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Contact Email', 'mock' => 'b.clark@apexcloud.com', 'is_url' => false],
        '[{CLIENT_PHONE}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Contact Phone', 'mock' => '+1 (415) 555-0188', 'is_url' => false],
        '[{CLIENT_DESIGNATION}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Client Title / Role', 'mock' => 'Vice President of Talent', 'is_url' => false],
        '[{CONTACT_PERSON_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Primary Contact Name', 'mock' => 'Brandon Clark', 'is_url' => false],
        '[{VENDOR_COMPANY_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Vendor / Staffing Agency Name', 'mock' => 'Summit Talent Partners', 'is_url' => false],
        '[{VENDOR_NAME}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Vendor Representative Name', 'mock' => 'Tyler Robinson', 'is_url' => false],
        '[{VENDOR_EMAIL}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Vendor Contact Email', 'mock' => 'agency@summittalent.com', 'is_url' => false],
        '[{VENDOR_PHONE}]' => ['domain' => self::DOMAIN_PARTNER, 'label' => 'Vendor Contact Phone', 'mock' => '+1 (212) 555-0177', 'is_url' => false],

        // --- DOMAIN 5: ACTION URLS & TRACKING ---
        '[{JOB_LINK}]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'Public Job Opening Link', 'mock' => 'https://testing.jero.works/jobs/senior-fullstack-88210', 'is_url' => true],
        '[{maillink}]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'Portal Deep Link / Action Link', 'mock' => 'https://testing.jero.works/super-admin/notification', 'is_url' => true],
        '[{CLICK_HERE}]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'Click Action Direct URL', 'mock' => 'https://testing.jero.works/application/review/REQ-88210', 'is_url' => true],
        '[URL_LINK]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'General URL Deep Link', 'mock' => 'https://testing.jero.works/candidate/profile/4091', 'is_url' => true],
        '[ACCEPT_INVITE]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'Accept Offer / Interview URL', 'mock' => 'https://testing.jero.works/offer/accept/REQ-88210', 'is_url' => true],
        '[TRACKER]' => ['domain' => self::DOMAIN_ACTION, 'label' => 'Application Tracking Link', 'mock' => 'https://testing.jero.works/tracker/status/REQ-88210', 'is_url' => true],
    ];

    /**
     * Replace tokens in content using contextual escaping and deterministic mock values.
     *
     * @param string $templateContent Raw HTML template or subject line
     * @param array<string, string> $customValues Optional overrides for runtime substitution
     * @param bool $isHtmlContext Whether the target is rich HTML (escapes text tokens safely)
     * @return string
     */
    public function render(string $templateContent, array $customValues = [], bool $isHtmlContext = true): string
    {
        if (trim($templateContent) === '') {
            return '';
        }

        // Build substitution map
        $search = [];
        $replace = [];

        foreach (self::$registry as $token => $meta) {
            $value = $customValues[$token] ?? $meta['mock'];

            if ($meta['is_url']) {
                // Ensure URL validity
                $safeUrl = filter_var($value, FILTER_VALIDATE_URL) ? $value : '#';
                $safeVal = htmlspecialchars($safeUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } else {
                $safeVal = $isHtmlContext
                    ? htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                    : $value;
            }

            $search[] = $token;
            $replace[] = $safeVal;
        }

        return str_replace($search, $replace, $templateContent);
    }

    /**
     * Extract all unique tokens present within a given template text or HTML string.
     *
     * @return array<string>
     */
    public function extractTokens(string $content): array
    {
        preg_match_all('/(\[\{[^}]+\}\]|\[[A-Za-z0-9_-]+\]|\{\{[^}]+\}\})/', $content, $matches);
        return array_values(array_unique($matches[0] ?? []));
    }

    /**
     * Get grouped token dictionary structured for the Authoring Studio 1-Click Palette.
     *
     * @return array<string, array<int, array{token: string, label: string, mock: string, is_url: bool}>>
     */
    public function getPaletteTokens(): array
    {
        $grouped = [
            self::DOMAIN_JOB => [],
            self::DOMAIN_CANDIDATE => [],
            self::DOMAIN_PEOPLE => [],
            self::DOMAIN_PARTNER => [],
            self::DOMAIN_ACTION => [],
        ];

        foreach (self::$registry as $token => $meta) {
            $domain = $meta['domain'];
            $grouped[$domain][] = [
                'token' => $token,
                'label' => $meta['label'],
                'mock' => $meta['mock'],
                'is_url' => $meta['is_url'],
            ];
        }

        return $grouped;
    }

    /**
     * Get human-friendly domain metadata (labels, icons, colors).
     *
     * @return array<string, array{title: string, icon: string, badge_class: string}>
     */
    public function getDomainMeta(): array
    {
        return [
            self::DOMAIN_JOB => [
                'title' => 'Job & Position Attributes',
                'icon' => 'fas fa-briefcase',
                'badge_class' => 'badge-light-primary',
            ],
            self::DOMAIN_CANDIDATE => [
                'title' => 'Candidate & Bench Profiles',
                'icon' => 'fas fa-user-tie',
                'badge_class' => 'badge-light-success',
            ],
            self::DOMAIN_PEOPLE => [
                'title' => 'Team & Recruiter Roles',
                'icon' => 'fas fa-users-cog',
                'badge_class' => 'badge-light-info',
            ],
            self::DOMAIN_PARTNER => [
                'title' => 'Clients & Staffing Partners',
                'icon' => 'fas fa-building',
                'badge_class' => 'badge-light-warning',
            ],
            self::DOMAIN_ACTION => [
                'title' => 'Action Links & Portals',
                'icon' => 'fas fa-link',
                'badge_class' => 'badge-light-danger',
            ],
        ];
    }

    /**
     * Check if a given token is recognized in the canonical registry.
     */
    public function hasToken(string $token): bool
    {
        return isset(self::$registry[$token]);
    }

    /**
     * Total number of canonical tokens in the registry.
     */
    public function count(): int
    {
        return count(self::$registry);
    }
}
