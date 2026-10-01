# Work Authorization & Placement Eligibility — Production Specification & Hardened Architecture (V3 Final)

> **Document Version:** 3.0 (Production Hardened — Implementation Ready)  
> **Status:** Architecture Approved / Hardened / Implementation Ready  
> **Scope:** Service Company Bench Candidate Creation & Lifecycle (`resources/views/service_company/bench-candidates/create.blade.php`), Data Persistence, Elasticsearch Projection (`ats_candidates_elastic`), and Recruiter Discovery.  
> **Core Architectural Principle:** Decouple legal right-to-work, declared engagement models, sponsorship profile, temporal validity, and verification into a normalized multi-dimensional model. Preserve `visastatu` strictly as a backward-compatible display projection.

---

## 1. Executive Summary & Production Baseline

The V3 specification resolves all architectural, compliance, and data-integrity risks identified across technical reviews. 

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    V3 PRODUCTION MULTI-DIMENSIONAL ARCHITECTURE                 │
│                                                                                 │
│   1. Work Authorization Jurisdiction (Country where legal right applies)        │
│   2. Candidate Immigration Status (Canonical code from versioned taxonomy)      │
│   3. Declared / Available Engagement Models (C2C, W2, 1099, Direct Hire)        │
│   4. Sponsorship Profile (Candidate declaration vs Placement opportunity rule)   │
│   5. Temporal Validity & Expiry (Conditional date picker + warning buckets)     │
│   6. Verification Lifecycle (Self-Declared -> Document -> Review -> Verified)  │
│   7. Security & Compliance Firewall (Zero sensitive PII/Doc numbers in ES)      │
│   8. Dual-Storage & ES Projection (`work_authorizations[]` array schema)        │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Multi-Dimensional Information Architecture

### Dimension 1: Country, Citizenship & Work Jurisdiction
The system strictly distinguishes three independent geographical dimensions:
1. **Candidate Current Location:** Managed by JERO's existing location picker (`country`, `state`, `city_id`).
2. **Candidate Citizenship:** Nationality/Citizenship of origin (`nationality` field).
3. **Work Authorization Jurisdiction:** The sovereign country/region where the candidate possesses the legal right to work (e.g. US, CA, IN, UK, AE, SG, AU, Other).

### Dimension 2: Canonical Status Code & Taxonomy
- **Stable Canonical Codes:** Immutable identifiers (e.g., `US_CITIZEN`, `US_H1B`, `CA_PR`, `IN_CITIZEN`, `AE_GOLDEN_VISA`, `SG_EP`).
- **Human-Readable Labels:** Dynamically loaded from configuration (`config/work_authorization.php`).
- **Taxonomy Versioning:** Every record stores `taxonomy_version` (e.g. `WA-2026.10`) for auditability.

### Dimension 3: Sponsorship Semantics (Candidate Profile vs Placement Opportunity)
Sponsorship is separated into two distinct levels:
1. **Candidate Profile (Declared Profile):**
   - `NOT_REQUIRED`: Candidate holds unrestricted authorization (Citizen, PR, independent EAD).
   - `FUTURE_REQUIRED`: Valid today, but will eventually require employer sponsorship petition (e.g. STEM-OPT, temporary permit).
   - `IMMEDIATE_REQUIRED`: Requires visa transfer or new petition sponsor prior to day 1 (e.g. H-1B transfer, E-3, O-1).
   - `sponsorship_default`: UI suggestion only derived from taxonomy; **must never overwrite explicit recruiter entry**.
   - `sponsorship_notes`: Internal recruiter notes (e.g. *"I-140 approved, priority date 2018"*), marked as internal-only.
2. **Placement / Opportunity Context:**
   - Evaluated at submission time against the client requisition's sponsorship policy.

### Dimension 4: Declared / Available Engagement Models
> **Compliance Precision:** The system records *declared or available engagement preferences*, not legal certifications of C2C suitability.
- `C2C` (Corp-to-Corp): Agency/employer billing to client.
- `W2`: Direct vendor payroll or client payroll.
- `1099`: Independent contractor / self-employed.
- `DIRECT_HIRE`: Permanent full-time placement.

### Dimension 5: Temporal Validity & Warning State
- **Conditional Visibility:** Expiry fields are revealed only when the selected authorization has `expires = true`.
- **Warning States (No Automatic Hard Blocks):**
  - `HEALTHY` (> 90 days remaining)
  - `EXPIRING_SOON` (30–90 days remaining — warning indicator)
  - `CRITICAL` (< 30 days remaining — high-priority renewal prompt)
  - `EXPIRED` (Date in past — displays a prominent warning flag; submission eligibility is determined by placement context/client rules, not an automatic profile-level hard block).

### Dimension 6: Verification Lifecycle
Trust rating is explicitly auditable without making false legal certifications:
```
[SELF_DECLARED] ──> [DOCUMENT_PROVIDED] ──> [PENDING_REVIEW] ──> [VERIFIED]
                                                 │
                                                 └──> [REJECTED]
[VERIFIED] ──(Validity Period Expired)──> [VERIFICATION_EXPIRED]
```
- Metadata tracked: `verification_status`, `verification_source` (`SELF_DECLARED`, `AGENCY_REVIEW`, `JERO_OPERATIONS`), `verified_at`, `verified_by`.

---

## 3. Mandatory Security & Compliance Firewall

> [!CAUTION]
> **Strict ES Security Rule:** Elasticsearch must **NEVER** index or store sensitive immigration document identifiers or attachments.

### Permitted in Elasticsearch (`ats_candidates_elastic`)
- Canonical authorization code (`US_H1B`)
- Work jurisdiction (`US`)
- Expiry date and bucket (`2027-08-31`, `HEALTHY`)
- Declared engagement models (`["C2C", "W2"]`)
- Sponsorship profile (`IMMEDIATE_REQUIRED`)
- Verification status enum (`VERIFIED`, `SELF_DECLARED`)

### Strictly Forbidden in Elasticsearch (Restricted to Encrypted MySQL / Private Storage)
- ❌ Passport numbers
- ❌ USCIS Alien / Registration numbers (A-Number)
- ❌ Form I-797 receipt or approval numbers
- ❌ EAD document numbers
- ❌ Document binary contents or unauthenticated presigned URLs
- ❌ Sensitive internal legal/compliance notes

---

## 4. Configuration-Driven Architecture (`config/work_authorization.php`)

All rules, labels, suggested defaults, and expiration flags reside in a versioned configuration file:

```php
<?php
// config/work_authorization.php — Canonical Taxonomies for Global Work Authorization & Compliance.
// Taxonomy Version: WA-2026.10 | Schema Version: 2026-01

return [
    'schema_version' => '2026-01',
    'taxonomy_version' => 'WA-2026.10',

    'popular_jurisdictions' => ['US', 'CA', 'IN', 'GB', 'AE', 'SG', 'AU', 'EU'],

    'jurisdictions' => [
        'US' => [
            'label' => 'United States',
            'categories' => [
                'CITIZEN' => [
                    'label' => 'Citizenship & Permanent Residency',
                    'items' => [
                        'US_CITIZEN' => ['label' => 'US Citizen (USC)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'US_PERMANENT_RESIDENT' => ['label' => 'Permanent Resident (Green Card)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                    ],
                ],
                'EAD' => [
                    'label' => 'Employment Authorization Document (EAD)',
                    'items' => [
                        'US_GC_EAD' => ['label' => 'GC-EAD (Adjustment of Status)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'US_H4_EAD' => ['label' => 'H-4 EAD (Spouse Dependent)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'US_L2_EAD' => ['label' => 'L-2 / L-2S EAD', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'US_OPT_EAD' => ['label' => 'OPT / STEM-OPT EAD', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                        'US_OTHER_EAD' => ['label' => 'Other US EAD (TPS / Asylee)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                    ],
                ],
                'TEMPORARY_WORK' => [
                    'label' => 'Non-Immigrant Work Visas',
                    'items' => [
                        'US_H1B' => ['label' => 'H-1B (Specialty Occupation)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'US_TN' => ['label' => 'TN Visa (USMCA - Canada/Mexico)', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                        'US_E3' => ['label' => 'E-3 (Australian Specialty)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'US_O1' => ['label' => 'O-1 (Extraordinary Ability)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'US_L1' => ['label' => 'L-1 (Intracompany Transferee)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'US_CPT' => ['label' => 'CPT (Student Curricular Training)', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                    ],
                ],
            ],
        ],

        'CA' => [
            'label' => 'Canada',
            'categories' => [
                'GENERAL' => [
                    'label' => 'Status & Work Permits',
                    'items' => [
                        'CA_CITIZEN' => ['label' => 'Canadian Citizen', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'CA_PR' => ['label' => 'Permanent Resident (PR)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'CA_OPEN_WP' => ['label' => 'Open Work Permit / PGWP', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                        'CA_CLOSED_WP' => ['label' => 'Employer-Specific / LMIA Work Permit', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                    ],
                ],
            ],
        ],

        'IN' => [
            'label' => 'India',
            'categories' => [
                'GENERAL' => [
                    'label' => 'Indian Right to Work',
                    'items' => [
                        'IN_CITIZEN' => ['label' => 'Indian Citizen / Domestic Right to Work', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'IN_OCI_PIO' => ['label' => 'OCI / PIO Cardholder', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'IN_WORK_VISA' => ['label' => 'Employment Visa (India)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                    ],
                ],
            ],
        ],

        'GB' => [
            'label' => 'United Kingdom',
            'categories' => [
                'GENERAL' => [
                    'label' => 'UK Right to Work',
                    'items' => [
                        'UK_CITIZEN' => ['label' => 'British Citizen', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'UK_ILR' => ['label' => 'Indefinite Leave to Remain (ILR)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'UK_SKILLED_WORKER' => ['label' => 'Skilled Worker / Tier 2 Visa', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'UK_GRADUATE' => ['label' => 'Graduate / Youth Mobility Visa', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                    ],
                ],
            ],
        ],

        'AE' => [
            'label' => 'United Arab Emirates & GCC',
            'categories' => [
                'GENERAL' => [
                    'label' => 'UAE & Gulf Work Authorizations',
                    'items' => [
                        'AE_GOLDEN_VISA' => ['label' => 'UAE Golden Visa (10-Year Self-Sponsored)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'AE_GREEN_VISA' => ['label' => 'UAE Green Visa (5-Year Skilled / Freelance)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'AE_EMPLOYMENT_VISA' => ['label' => 'UAE Employment Residence Visa (Company / Freezone)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'AE_FREELANCE_VISA' => ['label' => 'UAE Freelance / Remote Work Visa', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'AE_NATIONAL' => ['label' => 'UAE / GCC National', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'GCC_IQAMA' => ['label' => 'GCC Resident Work Permit / Iqama', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                    ],
                ],
            ],
        ],

        'SG' => [
            'label' => 'Singapore',
            'categories' => [
                'GENERAL' => [
                    'label' => 'Singapore Work Passes',
                    'items' => [
                        'SG_CITIZEN' => ['label' => 'Singapore Citizen (SC)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'SG_PR' => ['label' => 'Singapore Permanent Resident (PR)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'SG_EP' => ['label' => 'Employment Pass (EP)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'SG_SPASS' => ['label' => 'S Pass (Skilled Staff)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'SG_ONE_PASS' => ['label' => 'ONE Pass / Tech.Pass / PEP', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'SG_LOC_DP' => ['label' => 'Letter of Consent (LOC / Dependant Pass)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                    ],
                ],
            ],
        ],

        'AU' => [
            'label' => 'Australia & New Zealand',
            'categories' => [
                'GENERAL' => [
                    'label' => 'Australia & NZ Right to Work',
                    'items' => [
                        'AU_CITIZEN_PR' => ['label' => 'Australian Citizen / Permanent Resident', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'AU_TSS_482' => ['label' => 'TSS Visa (Subclass 482 - Temporary Skill)', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'AU_SKILLED_189_190' => ['label' => 'Skilled Work Visa (Subclass 189 / 190)', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'AU_GRADUATE_485' => ['label' => 'Temporary Graduate Visa (Subclass 485)', 'expires' => true, 'sponsorship_default' => 'FUTURE_REQUIRED'],
                        'NZ_CITIZEN_PR' => ['label' => 'New Zealand Citizen / Resident', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                    ],
                ],
            ],
        ],

        'GLOBAL' => [
            'label' => 'European Union & Global',
            'categories' => [
                'GENERAL' => [
                    'label' => 'International & Other',
                    'items' => [
                        'EU_CITIZEN' => ['label' => 'EU / EEA Citizen', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'EU_BLUE_CARD' => ['label' => 'EU Blue Card', 'expires' => true, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'GLOBAL_AUTH_PERMIT' => ['label' => 'Authorized Work Permit (Country Specific)', 'expires' => true, 'sponsorship_default' => 'NOT_REQUIRED'],
                        'GLOBAL_REQUIRES_SPONSOR' => ['label' => 'Requires International Visa Sponsorship', 'expires' => false, 'sponsorship_default' => 'IMMEDIATE_REQUIRED'],
                        'NOT_APPLICABLE' => ['label' => 'Not Applicable / Global Remote Contractor', 'expires' => false, 'sponsorship_default' => 'NOT_REQUIRED'],
                    ],
                ],
            ],
        ],
    ],
];
```

---

## 5. Dual-Storage & Migration Contract (Zero Data Corruption)

### Explicit Normalization States
Historical data is never silently reinterpreted:
- `VERIFIED_CANONICAL`: Submissions created with a valid canonical code.
- `LEGACY_MAPPED`: Unambiguous historical mapping (`H1B` → `US_H1B`, `Green Card` → `US_PERMANENT_RESIDENT`).
- `LEGACY_UNVERIFIED`: Ambiguous historical records (`F1`, `L1`, custom text). Stored with `legacy_source_value = "F1"` and tagged as `LEGACY_UNVERIFIED`. The UI displays *"Legacy Value (Unverified: F1)"* prompting recruiter review.

### Persistence Strategy (Interim vs Long-Term Target)
1. **Interim (Phase 1–3):**
   - `bench_applications.visastatu`: Preserved display string sync.
   - `bench_additional_detail`: Key-value structured metadata storage (`work_auth_code`, `work_auth_jurisdiction`, `work_auth_sponsorship`, `work_auth_engagement`, `work_auth_expires_at`, `work_auth_verification`, `schema_version`, `taxonomy_version`, `legacy_source_value`).
2. **Long-Term Target (Phase 5):**
   - Dedicated normalized relation `candidate_work_authorizations` (supports multiple jurisdictions per candidate).

---

## 6. Elasticsearch Read Projection (`work_authorizations[]` Array)

To future-proof for candidates holding multiple legal authorizations (e.g. US + Canada dual authorization), Elasticsearch projects an array of objects:

```json
{
  "work_authorizations": [
    {
      "jurisdiction": "US",
      "code": "US_H1B",
      "category": "TEMPORARY_WORK",
      "display_label": "H-1B (Specialty Occupation)",
      "sponsorship_requirement": "IMMEDIATE_REQUIRED",
      "declared_engagement_models": ["C2C", "W2"],
      "has_expiry": true,
      "expires_at": "2027-08-31",
      "expiry_bucket": "HEALTHY",
      "verification_status": "SELF_DECLARED",
      "normalization_confidence": "VERIFIED_CANONICAL",
      "taxonomy_version": "WA-2026.10",
      "schema_version": "2026-01"
    }
  ]
}
```

### Performance & Idempotency SLA
- **Latency Target:** Low-latency Elasticsearch filtering validated under the JERO 10,000 concurrent-user load profile (0 MySQL reads on query hot-path).
- **Client Cache Strategy:** No per-keystroke server request for locally cached taxonomy search.
- **Idempotent Queue Updates:** Queue jobs track candidate version/timestamp (`aggregate_version`) to guarantee out-of-order queue executions do not overwrite newer states.

---

## 7. Progressive Disclosure UI & State Machine

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 🌟 Popular Hiring Hubs (Quick Pills)                                        │
│ [ 🇺🇸 US ] [ 🇨🇦 Canada ] [ 🇮🇳 India ] [ 🇬🇧 UK ] [ 🇦🇪 Dubai ] [ 🇸🇬 SG ] [ 🇦🇺 AU ] │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Jurisdiction (Suggested from location, can be freely changed)            │
│ 2. Authorization Status (Dropdown populated for selected jurisdiction)      │
│ 3. Declared Engagement Models: [ C2C ] [ W2 ] [ 1099 ] [ Direct Hire ]      │
│ 4. Sponsorship Profile: [ Not Required ] [ May Require Later ] [ Required ] │
├─────────────────────────────────────────────────────────────────────────────┤
│ (Conditional) If status expires:                                            │
│   • Authorization Expiry Date [ DD/MM/YYYY ]                                │
├─────────────────────────────────────────────────────────────────────────────┤
│ (+ Additional Compliance Details - Collapsed by Default):                   │
│   • Verification Status [ Self-Declared / Document Provided ]               │
│   • Internal Recruiter Compliance Notes (Internal Only)                     │
└─────────────────────────────────────────────────────────────────────────────┘
```

### UI Dependency State Machine
1. **Jurisdiction Changed:** Immediately resets authorization selection to prevent illegal state combinations (e.g. India + H-1B).
2. **Authorization Selected:** If `expires = true`, smoothly transitions the expiry date field into view; if `expires = false`, hides expiry and clears previous date.
3. **Sponsorship Suggested:** Selecting an authorization code populates `sponsorship_default` as a suggestion; user can freely override.

---

## 8. Explicit Validation Matrix

| Condition | Validation Rule | Error / Warning Action |
|---|---|---|
| Jurisdiction empty | Authorization dropdown disabled | Prompt: "Select jurisdiction first" |
| Authorization requires expiry | Expiry date recommended | Soft warning if omitted (optional field) |
| Expiry date in past | Warning flag applied | Warning: "Authorization has expired" |
| Jurisdiction changed | Clear authorization | Resets dependent authorization selection |
| Legacy unverified record | Tagged as `LEGACY_UNVERIFIED` | Recruiter badge: "Needs confirmation" |
| Document uploaded | Set to `DOCUMENT_PROVIDED` | Moves status from `SELF_DECLARED` |
| Verification set to `VERIFIED` | Requires compliance role & timestamp | Blocks non-privileged users |

---

## 9. Phased Execution Roadmap

- **Phase 0 — Data Contract & Taxonomy:** Approve `config/work_authorization.php`, canonical codes, and validation matrix.
- **Phase 1 — Progressive UI Component:** Upgrade `create.blade.php` to consume config, render top search, popular pills, and progressive disclosure.
- **Phase 2 — Persistence & Dual-Sync:** Update `ServiceCompanyBenchCandidatesController` to store canonical codes and metadata while keeping `visastatu` display in sync.
- **Phase 3 — Elasticsearch Projection:** Update `GeneralMethod` to project `work_authorizations[]` into ES with idempotency guards.
- **Phase 4 — Recruiter Discovery:** Update candidate drawer and listings with prioritized badges (`H-1B`, `C2C/W2`, `Exp: Aug 2027`).
- **Phase 5 — Load & Concurrency Testing:** Validate under 10K concurrency load and test legacy candidate integrity.
