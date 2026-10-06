# Universal Tenant Defaults Architecture: Currency, Calling Code, Country & Timezone
## God-Level System Audit & Universal Implementation Plan (Zero-Code Stage)

> **System Profile:** JERO Multi-Tenant B2B SaaS (GOD-Level ATS + Global Talent Marketplace)  
> **Architecture Pattern:** CQRS (MySQL Strictly Writes, Elasticsearch Strictly Reads, Redis Cache / Horizon Queues)  
> **Scale Target:** 10,000 Concurrent Recruiters Per Page / 0% Database Read Strain  
> **Tenancy Scope:** Organization-Scoped (`companies.id` / `company_id`)  
> **UI Standard:** Metronic Design System, Poppins Font, Max 14px Font, 15.6-Inch Screen Responsive Fit  
> **Document Status:** Complete Audit & Production Implementation Blueprint (Pending User Approval — DO NOT CODE)  
> **Document Date:** October 6, 2026  

---

## 1. Executive Summary & Universal System Matrix

In a global enterprise ATS and talent marketplace platform, cross-border recruiting operations require seamless localization. When a tenant organization configures their company headquarters, operating currency, calling code, or timezone in **Account Settings**, that configuration must act as the **Authoritative Tenant Baseline** across the entire platform.

### The Problem Statement
Currently across the platform:
1. **Currency Disconnect:** While `GeneralMethod::defaultCurrency()` was recently linked on candidate addition pages, numerous other modules (Job Requisitions, Offers, Commercial Terms, Bench Candidates, Filter Drawers, and Public Portals) either hardcode currencies or fall back inconsistently between `master_currency` (160+ countries) and `currencies` (0..5 index keys).
2. **Phone Number Calling Code Inaccuracy:** In `<x-jero-phone-input>`, the code attempts to read `$company->headquarter_country_code`, a column that **does not exist** in the `companies` database schema (`mysql-schema.sql`). This causes an unintended fallback to `'IN'` (+91) for international tenants headquartered in the US, UK, UAE, or Europe.
3. **Country Dropdown Omissions:** Forms with country selects (e.g., location creation, candidate status locations) lack pre-selection, defaulting to the first alphabetical entry (Afghanistan) rather than the tenant's registered country.
4. **Timezone Misalignment:** In interview scheduling drawers, option values use abbreviations (e.g., `IST`, `EST`) while company settings store full IANA timezone strings (`Asia/Kolkata`, `America/New_York`), causing selection mismatches.

### The Architectural Vision: The 4 Tenant Anchors
Every tenant organization in JERO is anchored by four authoritative settings stored in the database and cached in Redis:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                       TENANT SETTINGS PAGE (Single Source of Truth)                         │
│                    `companies` table + `client_location` (self_company = 1)                 │
└──────────────────────────────────────────────┬──────────────────────────────────────────────┘
                                               │
             ┌─────────────────────────────────┼─────────────────────────────────┐
             ▼                                 ▼                                 ▼
┌─────────────────────────┐       ┌─────────────────────────┐       ┌─────────────────────────┐
│ 1. DEFAULT CURRENCY     │       │ 2. PHONE CALLING CODE   │       │ 3. DEFAULT COUNTRY      │
│ `companies.currency`    │       │ `companies.calling_code`│       │ `client_location.       │
│ Cached in Redis (1h)    │       │ + `countries.phonecode` │       │   country_id`           │
│ Auto-selected across    │       │ Auto-selected across    │       │ Auto-selected across    │
│ all salary/rate forms   │       │ all mobile/phone inputs │       │ all country selects     │
└─────────────────────────┘       └─────────────────────────┘       └─────────────────────────┘
                                               │
                                               ▼
                                  ┌─────────────────────────┐
                                  │ 4. DEFAULT TIMEZONE     │
                                  │ `companies.timezone`    │
                                  │ Auto-selected across    │
                                  │ interview schedulers    │
                                  └─────────────────────────┘
```

### The Non-Negotiable Rules of Engagement
1. **0% MySQL Reads on Web Requests:** Tenant anchors are cached in Redis with a 24-hour TTL. Sub-millisecond reads from memory; zero database queries on user page loads.
2. **Atomic Cache Invalidation:** When a tenant updates their profile or settings on `ServiceCompanyAccountSettingsController@update` or `addressupdate`, all tenant cache keys are wiped immediately.
3. **Smart Defaults with 100% Manual Override:** On **ADD / CREATE** forms, the tenant's setting is pre-selected by default. On **EDIT** forms, existing record data is preserved. Recruiters retain 100% freedom to manually change currency, country, calling code, or timezone at any time for cross-border transactions.
4. **Complete Page/Blade Isolation (Rule 17):** Changes to each blade remain self-contained without state leaks.
5. **Frozen Module Protection (Rule 18):** `product_company/` and `staffing_company/` files remain 100% frozen. Enhancements are strictly scoped to `service_company/` and `admin/`.

---

## 2. Pillar 1: Global Currency Audit & Universal Linkage Blueprint

### 2.1. Audit: All Pages Currently Implementing Currency
A comprehensive audit of the entire codebase reveals **15 distinct operational areas** where currency inputs, selectors, or displays exist:

| # | Page / Blade File | UI Element / Field Name | Storage / Data Binding | Current Status / Gap |
|---|---|---|---|---|
| **1** | `service_company/job-applications/create.blade.php` | Candidate Expected CTC Currency (`current_ctc_cur`, `current_ectc_cur`) | `job_applications.current_ctc_cur` | **Linked** to `GeneralMethod::defaultCurrency()` |
| **2** | `service_company/job-applications/clientcreate.blade.php` | Client Candidate Submission Currency | `job_applications.current_ctc_cur` | **Linked** to `GeneralMethod::defaultCurrency()` |
| **3** | `resources/views/career-apply.blade.php` | Public Career Job Application CTC Currency | `job_applications.current_ctc_cur` | **Linked** to `GeneralMethod::defaultCurrency()` |
| **4** | `service_company/jobs/create.blade.php` | Minimum & Maximum Salary Currency (`min_salary_currency`, `max_salary_currency`, `salary_currency`) | `jobs.min_salary_currency`, `jobs.max_salary_currency` | Uses `GeneralMethod::defaultCurrency()`; legacy hidden input `salary_currency` needs verification |
| **5** | `service_company/jobs/create1.blade.php` & `OLD_2create.blade.php` | Legacy / Alternate Job Creation Currencies | `jobs.min_salary_currency` | Partially hardcoded to INR / USD |
| **6** | `service_company/bench-candidates/create.blade.php` | Bench Candidate Pay Rate & Bill Rate Currency (`pay_rate_currency`, `bill_rate_currency`, `current_th_cur`) | `job_applications.pay_rate_currency`, `bill_rate_currency` | Defines `$currency_code = GeneralMethod::defaultCurrency()`, selects dynamically |
| **7** | `service_company/account/index.blade.php` | Settings Page: Tenant Currency Selector (`currency`) | `companies.currency` (saves to DB & flushes cache) | Options populated from `master_currency`; saves `master_currency.id` |
| **8** | `service_company/account/agency_commercials_drawer.blade.php` | Agency Commercials: Fixed Fee Currency (`fulltime_fixed_fee_currency`), Range Currency (`fulltime_range_currency`), General Currency (`general_currency`) | `agency_commercial_terms.contract_rate_currency`, `settings_json` | **Hardcoded options** (0=INR, 1=EUR, 2=JPY, 3=GBP, 4=USD, 5=AED); defaults to 4 (USD) or 0 (INR) rather than tenant setting |
| **9** | `service_company/account/v2/tabs/overview.blade.php` | Account Overview Display | Display only | Displays `currencyArr->firstWhere('id', $account->currency)` |
| **10** | `service_company/job-applications/create-offer.blade.php` | Offer Generation CTC Currency (`current_ctc_cur`) & Offer Table Headers (`show_currency`) | `job_applications.current_ctc_cur`, `offer_letters` | **Hardcoded options (0 to 5)**; defaults to `0` (INR) when `$currency` is empty instead of tenant default |
| **11** | `service_company/account/offerAmountChange.blade.php` | Salary Component Currency & Offer Template preview | `offer_point_companies` | Defaults to INR; needs dynamic tenant default currency symbol |
| **12** | `service_company/cvtracker/index.blade.php` | CV Tracker Filter Drawer Salary Currency (`min_salary_currency`) | Filter parameter | **Hardcoded options** (`<option value="0">INR</option>`); not dynamically linked |
| **13** | `service_company/apptracker/find-talent.blade.php` & `similar_candidates.blade.php` | Find Talent CTC Filter Currency (`min_salary_currency`) | Search query parameter | **Hardcoded options** (0=INR, 1=EUR, 4=USD, 5=AED); defaults to hardcoded check |
| **14** | `service_company/job-applications/showdetail.blade.php` & `bench-candidates/showdetail.blade.php` | Candidate Details Modal / Header CTC Display (`current_ctc_cur`, `current_ectc_cur`) | Display only | Uses `GeneralMethod::currencySymbole()`; needs seamless index-to-code resolution |
| **15** | `service_company/career-page/tabs/jobs.blade.php` | Career Portal Requisition Salary Display | Public display | Reads job's `min_salary_currency`; fallback must link to tenant currency |

### 2.2. The Database Reality: Dual Currency Storage
The database contains two distinct currency tables:
1. `master_currency`: 160+ rows with `id`, `country`, `currency`, `code` (ISO 3-letter e.g. `INR`, `USD`, `AED`), `symbol`.
   - `companies.currency` stores an integer foreign key pointing to `master_currency.id` (e.g., 53 for INR, 2 for USD).
2. `currencies`: 6 rows with `id`, `currency_code`, `currency_symbol`, `getKey` (0=INR, 1=EUR, 2=JPY, 3=GBP, 4=USD, 5=AED).
   - ATS forms historically store `getKey` (0..5) in candidate salary columns.

### 2.3. The Universal Linkage Engine Architecture
To ensure seamless synchronization everywhere, the resolution chain operates as follows:

```
Tenant updates Currency on Settings Page (`companies.currency = 53`)
                           │
                           ▼
Cache Invalidation: Flush `tenant_default_currency:{$company_id}`
                           │
                           ▼
Universal Helper: `GeneralMethod::defaultCurrency($company_id)`
  1. Resolves `master_currency.code` -> "INR"
  2. Resolves `currencies.getKey` -> 0
                           │
             ┌─────────────┴─────────────┐
             ▼                           ▼
   Forms expecting ISO Code     Forms expecting Integer Key
   (e.g., "INR", "USD")         (e.g., 0, 4)
   -> Pre-selects "INR"         -> Pre-selects 0
```

#### Linkage Implementation Blueprint:
1. **Helper Expansion in `GeneralMethod`:**
   - `GeneralMethod::defaultCurrency($company_id)`: returns ISO string (e.g. `'INR'`, `'USD'`).
   - `GeneralMethod::defaultCurrencyKey($company_id)`: returns integer index (0=INR, 1=EUR, 2=JPY, 3=GBP, 4=USD, 5=AED).
   - `GeneralMethod::defaultCurrencySymbol($company_id)`: returns symbol (e.g. `₹`, `$`, `€`, `£`, `AED`).
2. **Standard Option Pre-Selection Rule:**
   - In **ADD** mode: Check `if (empty($savedCurrency)) $savedCurrency = GeneralMethod::defaultCurrencyKey($company_id)`.
   - In **EDIT** mode: If `$savedCurrency` is present, respect it; if null, fallback to tenant default.
   - User override: Recruiter can change currency at any time via the select dropdown.

---

## 3. Pillar 2: Phone Number Country Calling Code Auto-Selection Specification

### 3.1. Audit: All Pages Requiring Phone / Calling Code Inputs

| # | Page / Blade File | Phone Field Name | Calling Code Field | Current Behavior & Defect |
|---|---|---|---|---|
| **1** | `service_company/job-applications/create.blade.php` | `phone`, `secondary_phone` | `primary_country_code`, `second_phone_code` | Uses `<x-jero-phone-input>`; falls back to 'IN' if `:defaultIso` is missing |
| **2** | `service_company/job-applications/clientcreate.blade.php` | `phone` | `primary_country_code` | Uses `<x-jero-phone-input>` |
| **3** | `resources/views/career-apply.blade.php` | `phone` | `primary_country_code` | Public career application; needs tenant HQ default |
| **4** | `service_company/bench-candidates/create.blade.php` | `phone`, `secondary_phone` | `phone_code`, `second_phone_code` | Candidate bench creation; uses custom dial code picker |
| **5** | `service_company/team/create.blade.php` | `mobile` | `calling_code` | Uses `<x-jero-phone-input id="mobile">`; **omits `:defaultIso`**, forcing hardcoded +91 |
| **6** | `service_company/team/edit.blade.php` | `mobile` | `calling_code` | Uses `<x-jero-phone-input>`; preserves existing user mobile |
| **7** | `service_company/client/create.blade.php` | `contact_number[]` | `calling_code[]` | Client contact creation; **hardcodes `initialCountry = 'in'`** in JS function |
| **8** | `service_company/client/components/tab-contacts.blade.php` | `contact_number` | `calling_code` | Client contact drawer |
| **9** | `service_company/jobs/hiring_manager.blade.php` | `hm_phone` | `hm_calling_code` | Job requisition HM phone |
| **10** | `service_company/jobs/recruiter.blade.php` | `recruiter_phone` | `recruiter_calling_code` | Recruiter contact phone |
| **11** | `service_company/profile/edit.blade.php` | `mobile` | `calling_code` | Recruiter's own profile phone |
| **12** | `service_company/settings/index.blade.php` | `company_phone` | N/A | Company phone input |

### 3.2. Critical Bug Identified: Non-Existent Column Reference
In `resources/views/components/jero-phone-input.blade.php` (line 22):
```php
$tenantHqIso = strtoupper($defaultIso ?: (auth()->check() && auth()->user()->company ? (auth()->user()->company->headquarter_country_code ?? 'IN') : 'IN'));
```
**Defect Analysis:**
- The column `headquarter_country_code` **does not exist** on the `companies` table schema (`database/schema/mysql-schema.sql` line 1042).
- As a result, whenever a blade view invokes `<x-jero-phone-input>` without explicitly supplying `:defaultIso`, it evaluates to `null` and **permanently defaults to `'IN'` (+91)**.
- A US company (headquartered in New York, calling code `+1`), a UK company (`+44`), or a UAE company (`+971`) will always see `+91` pre-selected by mistake!

### 3.3. The Solution: Multi-Tier Authoritative Resolution Chain
The tenant's country and calling code are stored in two authoritative locations:
1. `client_location` table (where `client_id` belongs to `clients.self_company = 1`):
   - Stores `country_id` -> linked to `countries.country_code` (e.g. `'US'`, `'AE'`, `'GB'`, `'IN'`) and `countries.phonecode` (e.g. `'1'`, `'971'`, `'44'`, `'971'`).
2. `companies.calling_code` (e.g. `'1'`, `'44'`, `'971'`, `'91'`).

```
                              RESOLVE TENANT CALLING CODE
                                           │
                ┌──────────────────────────┴──────────────────────────┐
                ▼                                                     ▼
     Tier 1: Check self_company                            Tier 2: Check `companies.
         `client_location.country_id`                                 calling_code`
         -> `countries.country_code`                           e.g. '1' -> US (+1)
         e.g. 233 -> 'US' (+1)                                 e.g. '44' -> GB (+44)
         e.g. 231 -> 'AE' (+971)                               e.g. '971' -> AE (+971)
                │                                                     │
                └──────────────────────────┬──────────────────────────┘
                                           ▼
                                Tier 3: Safe Fallback: 'IN' (+91)
```

#### Linkage Implementation Blueprint:
1. **Enhance `GeneralMethod::countryCodeFun($company_id = null)`:**
   - Returns lowercase ISO code (e.g. `'us'`, `'ae'`, `'gb'`, `'in'`).
   - Caches result in Redis (`tenant_default_country_iso:{$company_id}`, TTL 24h).
2. **Add `GeneralMethod::defaultCallingCode($company_id = null)`:**
   - Returns dial code with plus (e.g. `'+1'`, `'+971'`, `'+44'`, `'+91'`).
   - Caches result in Redis (`tenant_default_calling_code:{$company_id}`, TTL 24h).
3. **Refactor `<x-jero-phone-input>` Component:**
   - Replace the non-existent property lookup with:
     ```php
     $tenantHqIso = strtoupper($defaultIso ?: GeneralMethod::countryCodeFun());
     ```
   - Automatically pre-select the tenant's country calling code on initial render.
4. **Behavioral Contract (ADD vs EDIT):**
   - **ADD Form:** Phone number is blank -> flag & code automatically pre-select tenant's calling code.
   - **EDIT Form:** Existing phone & country code are loaded -> pre-selects the candidate's/contact's saved country code.
   - **Recruiter Override:** Clicking the flag/dropdown opens the search menu, allowing the user to select any country in the world without restriction.

---

## 4. Pillar 3: Country Auto-Selection Specification

### 4.1. Audit: All Pages Requiring Country Selection

| # | Page / Blade File | UI Control | Binding Field | Current State & Gap |
|---|---|---|---|---|
| **1** | `service_company/jobs/create.blade.php` | Job Location City Tagify (`kt_tagify_work_location`) | `client_location_jobs` | Autocomplete allows any city; needs tenant country prioritizing |
| **2** | `service_company/job-applications/create.blade.php` | Candidate Work Location & Current Location Tagify | `job_applications.address` | Tagify search; should bias towards tenant default country |
| **3** | `service_company/bench-candidates/create.blade.php` | Bench Candidate Location | `job_applications.job_locations` | Uses `$country_code = GeneralMethod::countryCodeFun()` |
| **4** | `service_company/client/create.blade.php` & `tab-locations.blade.php` | Client Office Locations (`country[]`) | `client_location.country_id` | Select dropdown; defaults to `cid = $(".countryfirst").val()` |
| **5** | `service_company/locations/create.blade.php` | Company Branch Office Country (`country_id`) | `job_locations.country_id` | **No selected attribute**; defaults to first country in loop (Afghanistan, id 1) |
| **6** | `service_company/candidateStatus/edit_location.blade.php` | Edit Location Country (`country_id`) | `client_location.country_id` | Respects existing record's `country_id` |
| **7** | `service_company/account/add_edit_key.blade.php` | Boost Keywords Country Filter | SEO settings | Reads `$country_code = GeneralMethod::countryCodeFun()` |

### 4.2. Audit Findings: The Missing Pre-Selection in Location Selects
In `resources/views/service_company/locations/create.blade.php`:
```html
<select name="country_id" id="country_id" class="form-control select2 custom-select">
    @foreach($countries as $country)
        <option value="{{ $country->id }}">{{ ucfirst($country->country_name) }}</option>
    @endforeach
</select>
```
Because no option has `selected`, browsers select the first option (Afghanistan). A recruiter based in Dubai, London, or Mumbai must scroll through 240+ countries every time they add a location!

### 4.3. The Country Auto-Selection Architecture
1. **Helper Method: `GeneralMethod::defaultCountryId($company_id = null)`:**
   - Queries the company's self-client location record (`clients.self_company = 1` -> `client_location.country_id`).
   - Caches the result in Redis (`tenant_default_country_id:{$company_id}`, TTL 24h).
   - Fallback: If no location record exists yet, derives country from `companies.calling_code` via `countries.phonecode` (e.g. `'91'` -> 101 India, `'1'` -> 233 USA, `'971'` -> 231 UAE, `'44'` -> 232 UK).
2. **Pre-Selection Contract across all Forms:**
   - In country `<select>` dropdowns:
     ```php
     $tenantCountryId = GeneralMethod::defaultCountryId();
     $selectedCountryId = old('country_id', $existingCountryId ?? $tenantCountryId);
     ```
     Render `<option value="{{ $country->id }}" {{ $selectedCountryId == $country->id ? 'selected' : '' }}>`.
   - In City Tagify inputs:
     - When user starts typing a city name, the query biases autocomplete towards cities belonging to `$tenantCountryId` first, while still allowing search across all global cities.
3. **Manual Override:**
   - The dropdown contains all 240+ countries. The recruiter can change the country with a single click at any time.

---

## 5. Pillar 4: Timezone Auto-Selection Specification

### 5.1. Audit: All Pages Requiring Timezone Selection

| # | Page / Blade File | UI Control | Stored Field | Current State & Defect |
|---|---|---|---|---|
| **1** | `service_company/job-applications/interview-create.blade.php` | Interview Scheduling Timezone (`timezone`) | `interview_schedule.timezone` | Matches `$defaultTimeZone === $tval->time_zone`, but **submits abbreviation** (`$tval->timezone_abbreviation`), causing potential cross-lookup mismatch |
| **2** | `service_company/bench-candidates/interview-schedule.blade.php` | Bench Interview Schedule Table & Calendar | Display | Displays `row.timezone_abbreviation` |
| **3** | `service_company/ajax_pages/interview-tracker-ajax.blade.php` | Interview Tracker Modal Timezone | `interview_schedule.timezone` | Uses `$defaultTimeZone` |
| **4** | `service_company/account/index.blade.php` (Settings Tab) | Default Timezone Selector (`timezone`) | `companies.timezone` | Shows Popular + All Time Zones from `master_timezone`; saves full IANA string (e.g. `Asia/Kolkata`, `America/New_York`) |
| **5** | `service_company/settings/index.blade.php` | Legacy Company Settings Timezone (`timezone`) | `companies.timezone` | Select2 dropdown of `\DateTimeZone::listIdentifiers()` |
| **6** | `service_company/dashboard/meeting-list.blade.php` | Dashboard Meetings List | Display | Displays scheduled interview times with timezone |
| **7** | Video Integrations (Zoom, Google Meet, Teams) | Integration sync | API payload | Requires IANA timezone for meeting link creation |

### 5.2. Audit Findings: Timezone Value Mismatch
In `service_company/job-applications/interview-create.blade.php` (line 330):
```php
@php $sel = ($defaultTimeZone === $tval->time_zone) ? 'selected="selected"' : ''; @endphp
{!! '<option value="'.$tval->timezone_abbreviation.'" '.$sel.'>('.$tval->gmt_offse.') '.$tval->country_name.' - '.$tval->timezone_abbreviation.'</option>' !!}
```
**Defect Analysis:**
- Option value is `$tval->timezone_abbreviation` (e.g. `IST`, `EST`, `GMT`).
- However, the settings page saves the full IANA identifier (`Asia/Kolkata`, `America/New_York`) into `companies.timezone`.
- Several timezones share the same abbreviation (e.g., `CST` could be China Standard Time or Central Standard Time). Submitting only the abbreviation can lead to meeting sync errors in Google Calendar, Outlook, and Zoom APIs.

### 5.3. The Timezone Auto-Selection Architecture
1. **Unified Helper: `GeneralMethod::defaultTimeZone($company_id = null)`:**
   - Reads `companies.timezone`.
   - Caches in Redis (`tenant_default_timezone:{$company_id}`, TTL 24h).
   - Fallback: `'Asia/Kolkata'`.
2. **Option Value & Display Standardization:**
   - Standardize option values to submit the authoritative IANA timezone (`$tval->time_zone`), while sending abbreviation (`$tval->timezone_abbreviation`) as a companion field if legacy controllers expect it.
   - Pre-selection condition:
     ```php
     $isSel = ($defaultTimeZone === $tval->time_zone || $defaultTimeZone === $tval->timezone_abbreviation);
     ```
3. **Manual Override:**
   - Recruiters can change the timezone freely on the interview scheduling drawer to match a remote candidate's or external client's timezone.

---

## 6. Atomic Cache Invalidation & Tenancy Synchronization Matrix

To achieve **10,000 concurrent users with 0% server read strain**, every tenant default is stored in Redis. When the tenant changes their settings, atomic cache invalidation guarantees zero stale data without server restarts.

### 6.1. Redis Tenant Cache Keys
| Cache Key | Content | TTL | Invalidation Trigger |
|---|---|---|---|
| `tenant_default_currency:{$company_id}` | ISO Code (e.g. `'USD'`) | 24 Hours | Settings Save (`currency` changed) |
| `tenant_default_currency_key:{$company_id}` | Integer Index (e.g. `4`) | 24 Hours | Settings Save (`currency` changed) |
| `tenant_default_currency_symbol:{$company_id}` | Symbol (e.g. `'$'`) | 24 Hours | Settings Save (`currency` changed) |
| `tenant_default_country_id:{$company_id}` | Integer ID (e.g. `233`) | 24 Hours | Address / Office Save (`addressupdate`) |
| `tenant_default_country_iso:{$company_id}` | Lowercase ISO (e.g. `'us'`) | 24 Hours | Address / Office Save (`addressupdate`) |
| `tenant_default_calling_code:{$company_id}` | Dial Code (e.g. `'+1'`) | 24 Hours | Settings Save (`calling_code` / address) |
| `tenant_default_timezone:{$company_id}` | IANA String (e.g. `'America/New_York'`) | 24 Hours | Settings Save (`timezone` changed) |

### 6.2. Invalidation Controller Hooks
In `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php`:
1. **Inside `update(Request $request, $id)` (lines 1268–1271):**
   ```php
   Cache::forget("tenant_default_currency:{$company_id}");
   Cache::forget("tenant_default_currency_key:{$company_id}");
   Cache::forget("tenant_default_currency_symbol:{$company_id}");
   Cache::forget("tenant_default_calling_code:{$company_id}");
   Cache::forget("tenant_default_timezone:{$company_id}");
   $this->accountSettingsService->flushAccountCache($company_id);
   ```
2. **Inside `addressupdate(Request $request)` (line 1325):**
   ```php
   Cache::forget("tenant_default_country_id:{$company_id}");
   Cache::forget("tenant_default_country_iso:{$company_id}");
   Cache::forget("tenant_default_calling_code:{$company_id}");
   $this->accountSettingsService->flushAccountCache($company_id);
   ```

---

## 7. Phased Implementation Plan (Ready for User Review)

The implementation is structured into **5 completely isolated, zero-regression phases**. Each phase will be executed step-by-step only after user review and approval:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: CORE HELPER & REDIS CACHING FOUNDATION                             │
│ - Upgrade `GeneralMethod` with 4 tenant default methods                     │
│ - Implement atomic cache invalidation in `AccountSettingsController`        │
│ - Zero Blade changes; zero regression risk                                  │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: PHONE CALLING CODE ENGINE & BUGFIX                                 │
│ - Fix `$company->headquarter_country_code` bug in `<x-jero-phone-input>`    │
│ - Bind tenant default ISO and dial code into all phone inputs               │
│ - Verify `client/create.blade.php` and `team/create.blade.php`              │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 3: GLOBAL CURRENCY UNIVERSAL LINKAGE                                  │
│ - Link all 15 currency touchpoints (Job create, Offer drawer, Agency terms) │
│ - Replace hardcoded currency loops with `GeneralMethod::currency()`         │
│ - Pre-select tenant default currency key/code on all new entity forms        │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 4: COUNTRY & TIMEZONE AUTO-SELECTION LINKAGE                          │
│ - Pre-select tenant `defaultCountryId` on location creation & address forms │
│ - Standardize interview scheduling timezone dropdown with tenant timezone   │
│ - Align IANA timezone and abbreviation representations                      │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 5: END-TO-END VERIFICATION & AUDIT SIGN-OFF                           │
│ - Verify 15.6" screen UI responsiveness (1366x768 & 1920x1080)              │
│ - Verify 0% MySQL reads via Redis cache monitoring                          │
│ - Verify manual override capability across all inputs                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 7.1. Detailed Phase Breakdown

#### Phase 1: Core Helper & Redis Caching Foundation
- Files touched: `app/Helper/GeneralMethod.php`, `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php`.
- Objective: Provide bulletproof, 10K-concurrency cached resolution for `defaultCurrency`, `defaultCurrencyKey`, `defaultCountryId`, `countryCodeFun`, `defaultCallingCode`, and `defaultTimeZone`.

#### Phase 2: Phone Calling Code Component Refactoring
- Files touched: `resources/views/components/jero-phone-input.blade.php`, `resources/views/service_company/team/create.blade.php`, `resources/views/service_company/client/create.blade.php`.
- Objective: Fix the missing schema column defect so `<x-jero-phone-input>` automatically detects the tenant's true HQ country (US, UK, UAE, India) instead of hardcoding `IN` (+91).

#### Phase 3: Global Currency Universal Linkage
- Files touched:
  - `resources/views/service_company/jobs/create.blade.php` (Min/Max salary currency)
  - `resources/views/service_company/bench-candidates/create.blade.php` (Pay rate & Bill rate currency)
  - `resources/views/service_company/job-applications/create-offer.blade.php` (Offer compensation currency)
  - `resources/views/service_company/account/agency_commercials_drawer.blade.php` (Agency commercial rates)
  - `resources/views/service_company/cvtracker/index.blade.php` (Filter drawer currency)
  - `resources/views/service_company/apptracker/find-talent.blade.php` (Talent search currency)
- Objective: Link all currency dropdowns dynamically to `GeneralMethod::defaultCurrency()` and `defaultCurrencyKey()`.

#### Phase 4: Country & Timezone Auto-Selection Linkage
- Files touched:
  - `resources/views/service_company/locations/create.blade.php` (Office location country)
  - `resources/views/service_company/candidateStatus/edit_location.blade.php` (Candidate status location)
  - `resources/views/service_company/job-applications/interview-create.blade.php` (Interview scheduling timezone)
  - `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` (Interview tracker modal)
- Objective: Automatically pre-select the tenant's registered country and timezone on all creation forms.

#### Phase 5: Verification & Production Certification
- Syntactic verification via `php -l`.
- Tenancy verification: ensure changes are strictly scoped to the logged-in tenant's company.
- Concurrency verification: verify Redis keys populate with zero MySQL queries on warm loads.
- UI verification: ensure perfect Metronic compliance on 15.6" displays (1366×768 and 1920×1080).

---

## 8. Summary of Architectural Guarantees

| Metric / Requirement | Target | Architectural Mechanism |
|---|---|---|
| **MySQL Reads on Page Load** | **0%** | Redis Cache (`Cache::remember`, TTL 24h) + in-memory request caching |
| **Concurrent Recruiters Supported** | **10,000+** | Sub-millisecond Redis lookups; zero runtime DDL; zero blocking queries |
| **Manual User Override** | **100% Supported** | Auto-selection only provides initial value; user retains full manual edit control |
| **Tenant Isolation** | **Strict** | All cache keys and database queries strictly scoped by `company_id` |
| **Module Regressions** | **Zero** | Preserves existing input names, IDs, and payload structures across all forms |
| **Code Freeze Compliance** | **100%** | Zero modifications to `product_company/` or `staffing_company/` (Rule 18) |

---

*This document is submitted for formal review. No code has been modified in accordance with the user's explicit instructions.*
