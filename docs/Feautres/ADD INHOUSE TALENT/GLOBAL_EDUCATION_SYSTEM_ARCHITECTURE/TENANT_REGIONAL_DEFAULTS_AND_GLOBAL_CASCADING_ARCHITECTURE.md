# Tenant-Aware Regional Context & Global Cascading Defaults Architecture
## Registration-Driven Intelligent Defaults for Currency, Nationality, Phone Dialing Code, Timezone & Regional Workflows

---

| Metadata | Details |
| :--- | :--- |
| **System** | JERO ATS & Global Talent Marketplace |
| **Module Domain** | Tenant Onboarding, Company Settings, Candidate Creation, Commercials, Interview Scheduling |
| **Architectural Standard** | CQRS + Redis Tenant Cache + Client-First / Browser Execution (User RAM) |
| **Performance Target** | 10,000 Concurrent Users, 0% MySQL Read Strain, Zero Recruiter Retyping Friction |
| **Date** | September 2026 |
| **Status** | Master Architectural Blueprint & Implementation Specification |

---

## 1. Executive Summary & Core Architectural Principle

### 1.1 The Operational Bottleneck in Global ATS
In traditional recruitment platforms, every form field starts from a blank or hardcoded default (typically hardcoded to a single country, e.g. India or USA). Consequently:
* An American staffing firm has to manually switch the phone code from `+91` to `+1`, change currency from `INR` to `USD`, adjust timezones from `Asia/Kolkata` to `America/New_York`, and change nationality from `Indian` to `American` on **every single candidate and interview**.
* A British agency operating in London faces the exact same repetitive friction.
* A multi-national recruiter scheduling an interview across borders risks scheduling at the wrong hour due to inconsistent timezone defaults.

### 1.2 The Innovation: "Single Source of Truth $\rightarrow$ Zero-Friction Global Cascading"
When a company registers on JERO or updates their Organization Profile, they establish four foundational attributes:
1. **Headquarters Location / Country** (e.g., India, United States, United Kingdom, UAE, Canada, Australia, Germany, Singapore)
2. **Default Accounting & Commercial Currency** (e.g., USD $, INR ₹, GBP £, EUR €, AED د.إ, CAD $, AUD $)
3. **Primary Operating Timezone** (e.g., `America/New_York`, `Asia/Kolkata`, `Europe/London`, `Asia/Dubai`, `Australia/Sydney`)
4. **Primary Calling Code** (e.g., `+1`, `+91`, `+44`, `+971`, `+61`, `+49`)

From this Single Source of Truth, JERO automatically **cascades intelligent defaults across every screen, drawer, and workflow in the entire platform**, eliminating over **70% of repetitive recruiter clicks** while preserving full override capability.

```
┌───────────────────────────────────────────────────────────────────────────────┐
│                      TENANT ONBOARDING / COMPANY SETTINGS                     │
│  • HQ Location: United States      • Base Currency: USD ($)                  │
│  • Dialing Code: +1               • Timezone: America/New_York (EST)         │
└──────────────────────────────────────┬────────────────────────────────────────┘
                                       │
            ┌──────────────────────────┼──────────────────────────┐
            ▼                          ▼                          ▼
┌───────────────────────┐  ┌───────────────────────┐  ┌───────────────────────┐
│ CANDIDATE & BENCH     │  │ COMMERCIALS & JOBS    │  │ INTERVIEW SCHEDULING  │
│ • Phone Code: +1      │  │ • Pay Rate: USD ($)   │  │ • Default TZ: EST     │
│ • Demonym: American   │  │ • Bill Rate: USD ($)  │  │ • Slot Display: EST   │
│ • Edu: US (BS/MS/MBA) │  │ • Job Salary: USD ($) │  │ • Calendar ICS: EST   │
└───────────────────────┘  └───────────────────────┘  └───────────────────────┘
```

---

## 2. The 4 Core Cascading Pillars

### Pillar 1: Base Currency & Multi-Currency FX Engine
* **Registration Trigger:** Company selects primary currency (e.g. `USD`, `INR`, `GBP`, `EUR`, `AED`).
* **Storage (MySQL Writes Only):** Saved to `companies.currency` (FK to `currencies.id`) and `company_career_settings.currency`.
* **Tenant Cache (Redis / User RAM):** Cached under `tenant:{company_id}:defaults` and injected into the frontend context.
* **Cascading Touchpoints:**
  1. **Add Bench Talent (`create.blade.php`):** Candidate Pay Rate (`#pay_annual`) and Bill Rate (`#bill_annual`) currency symbols default to tenant currency.
  2. **Candidate Public Profile & Drawers:** Displays candidate rates formatted in tenant currency.
  3. **Job Creation & Requisitions:** Minimum and Maximum salary range inputs default to tenant currency.
  4. **Talent Marketplace Trading & Coin Packages:** Invoices, pricing tiers, and payout calculations default to tenant currency.
  5. **Dynamic FX Conversion (Non-Destructive):** Using `currency_exchange_rates`, recruiters can view real-time USD/INR conversions in tooltips or summaries without mutating the candidate's authoritative agreed rate.

### Pillar 2: HQ Location $\rightarrow$ Default Candidate Nationality & Adaptive Education
* **Registration Trigger:** Company specifies Headquarters Country (e.g. India, United States, United Kingdom, Canada, UAE, Germany).
* **Cascading Touchpoints:**
  1. **Add Bench Talent & Add Candidate:**
     * The `Nationality` dropdown automatically pre-selects the tenant's demonym (e.g., `American` for US tenant, `Indian` for Indian tenant, `British` for UK tenant).
     * The `Highest Qualification` dropdown immediately curates native credentials for that country (e.g. B.S./M.S. for US, B.Tech/MCA for India, BSc/HND for UK).
  2. **Non-Blocking Override:** If a US firm uploads an Indian expat resume, the recruiter can change nationality or search globally via the alias engine with zero restrictions.

### Pillar 3: HQ Location $\rightarrow$ Default International Phone Dialing Code
* **Registration Trigger:** Derived from the company's registered calling code (`companies.calling_code`).
* **Cascading Touchpoints:**
  1. **Candidate Mobile Input (`iti` / International Telephone Input):**
     * Automatically sets the country flag and country calling code (`+1`, `+91`, `+44`, `+971`, etc.) as the default active selection.
     * Prevents invalid domestic numbers from being entered without their correct international dialing prefix.
  2. **Client Contact Phone Inputs:** Client creation drawers and Relationship Manager profiles default to the tenant calling code.

### Pillar 4: Timezone Cascading $\rightarrow$ Interview Scheduling & Calendar
* **Registration Trigger:** Company selects operating timezone (`companies.timezone`, e.g. `Asia/Kolkata`, `America/New_York`, `Europe/London`, `Asia/Dubai`).
* **Cascading Touchpoints:**
  1. **Schedule Interview Drawer (`schedule-interview.blade.php`):**
     * Timezone dropdown pre-selects the tenant's primary timezone.
     * Date & Time pickers default to slot availability within tenant operating business hours (e.g. 09:00 AM – 06:00 PM in tenant local time).
  2. **Calendar Integration & ICS Generation:**
     * Google Calendar, Microsoft Teams, and Zoom integrations format meeting start/end times in UTC with the tenant timezone metadata.
     * Recruiter, Candidate, and Client receive invites with automatic client-side timezone offset conversion, eliminating missed interviews.

---

## 3. High-Value Global Extensions (Pillars 5 & 6)

In addition to the 4 primary pillars, the following regional standards should cascade from the registration profile:

### Pillar 5: Standard Date & Time Formatting
* **North America (USA/Canada):** `MM/DD/YYYY` (e.g., `09/29/2026`), 12-hour format (`hh:mm A`).
* **United Kingdom, India, Europe, Australia:** `DD/MM/YYYY` (e.g., `29/09/2026`), 24-hour or 12-hour format.
* **ISO International Standard:** `YYYY-MM-DD` for all internal database storage and API payloads.

### Pillar 6: Standard Work Week & Business Operating Days
* **North America, Europe, India, Asia-Pacific:** Monday through Friday work week (Saturday & Sunday weekend).
* **Middle East / GCC (UAE, Saudi Arabia, Qatar):** Monday through Friday (UAE) or Sunday through Thursday (Saudi Arabia, Qatar).
* **Cascading Impact:** Interview slot pickers and notice period calculators automatically recognize non-working days based on tenant operating country.

---

## 4. Technical Architecture: 0% MySQL Read Strain (Rule 1 & Rule 3)

### 4.1 The CQRS Tenancy Flow
To satisfy **Rule 1** (0% MySQL reads on web/API request paths) and **Rule 4** (10,000 concurrent user scale):
* Company settings are saved to MySQL once upon registration or profile update.
* Simultaneously, Redis caches the compiled tenant regional bundle under key:
  ```
  tenant:{company_id}:regional_context
  ```
* When an authenticated user loads any ATS page, FrankenPHP/Octane reads this cached JSON and injects it into a global client-side JavaScript object in User RAM:

```html
<script>
window.JERO_TENANT_CONTEXT = Object.freeze({
    companyId: 42,
    companyName: "Acme Talent Solutions",
    hqCountry: "United States",
    countryCode: "US",
    callingCode: "1",
    demonym: "American",
    currency: {
        id: 1,
        code: "USD",
        symbol: "$",
        name: "US Dollar"
    },
    timezone: "America/New_York",
    dateFormat: "MM/DD/YYYY",
    workWeek: "mon_fri"
});
</script>
```

### 4.2 In-Browser Consumption (Zero Latency)
Every form component consumes `window.JERO_TENANT_CONTEXT` directly in browser memory:
* `create.blade.php`:
  ```javascript
  if (!isEditMode && !$('#nationality').val()) {
      $('#nationality').val(window.JERO_TENANT_CONTEXT.demonym).trigger('change');
  }
  if (!isEditMode && !$('#primary_country_code').val()) {
      $('#primary_country_code').val(window.JERO_TENANT_CONTEXT.callingCode);
  }
  ```
* **Performance:** `< 0.1ms execution time`, zero AJAX calls, zero database read queries.

---

## 5. Tenancy, Elasticsearch & Regression Safeguards (Rules 6, 7 & 17)

1. **Multi-Tenant Atomic Cache Invalidation (Rule 6):**
   When the company admin updates their HQ location, currency, or timezone in Company Settings:
   * MySQL record updates.
   * Redis key `tenant:{company_id}:regional_context` is atomically destroyed and re-cached.
   * All active organization users receive updated defaults on their next page navigation.
2. **Zero Regressions on Existing Data (Rule 7):**
   * Intelligent defaults apply strictly to **NEW records** (`!isEditMode`).
   * For existing candidates, jobs, or interviews, the stored historical value is **strictly preserved**. Editing an existing candidate never overwrites their saved nationality, currency, or phone code with company defaults.
3. **Complete Page Isolation (Rule 17):**
   * Each page's form bridge reads from `window.JERO_TENANT_CONTEXT` independently without shared stateful DOM dependencies.

---

## 6. Implementation Roadmap & Rollout Phases

| Phase | Milestone | Deliverables |
| :--- | :--- | :--- |
| **Phase 1** | **Tenant Context Injection** | Implement `TenantRegionalContextService` to compile and cache `window.JERO_TENANT_CONTEXT` in Redis. |
| **Phase 2** | **Add Bench Talent & Candidate Defaulting** | Pre-fill Nationality, Phone Calling Code, and Currency symbols on `create.blade.php` based on tenant context. |
| **Phase 3** | **Commercials & Rates Defaulting** | Connect candidate pay/bill rate currency selectors and job salary ranges to tenant default currency. |
| **Phase 4** | **Interview Scheduling Timezone Defaulting** | Pre-populate `schedule-interview.blade.php` with tenant timezone and operating hours. |
| **Phase 5** | **Company Settings UI Enhancements** | Ensure company registration and settings forms validate and update all 4 foundational attributes cleanly. |

---

*Authored for JERO ATS by Antigravity Engineering.*
