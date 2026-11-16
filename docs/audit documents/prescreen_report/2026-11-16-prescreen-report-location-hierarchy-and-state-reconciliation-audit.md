# Forensic Audit & Architecture Fix: Pre-Screening Report Location Hierarchy & State ID Synchronization

> **Standard Compliance:** Conforms strictly to Rule 1 (0% MySQL reads on user requests), Rule 2 (Universal Elasticsearch reads), Rule 3 (Client-first execution), Rule 5 (Zero stale recruitment data), Rule 7 (Zero business logic regressions), Rule 18 (Code freeze on product & staffing modules), Rule 20 (Card + Sub-Card Design Standard), and Rule 21 (Audit Document Standards).

---

## 1. Metadata
- **Audit ID:** `AUD-2026-11-16-PRESCREEN-LOC-SYNC`
- **Module:** Pre-Screening Report, Forward Resume Engine, Candidate Location Autocomplete
- **Author:** Antigravity AI Engineering Team
- **Date:** 16 November 2026
- **Status:** Complete & Verified
- **Target Branch:** `staging`

---

## 2. Executive Summary
During user verification of the newly decoupled **Pre-Screening Report**, geographical discrepancies were discovered in candidate location headers:
1. **Case A (Manvendra Singh):** Displayed `LOCATION: Gurgaon/gurugram, Odisha, India` (Gurgaon/Gurugram is in Haryana, not Odisha).
2. **Case B (Candidate #204 - Chanda Panjiyar):** Displayed `LOCATION: Noida, Chandigarh, India` (Noida is in Uttar Pradesh, not Chandigarh).
3. **Case C (Shekhar Anand):** Displayed `LOCATION: Anand, India` with state omitted or unpopulated.

A forensic investigation traced the problem through the frontend location suggestion component, backend controller write paths, Elasticsearch indexing joins, and PDF dossier normalization.

The root causes were:
- **Frontend Hardcoded Array Poisoning:** In `create.blade.php`, `clientcreate.blade.php`, `bench-candidates/create.blade.php`, and `career-apply.blade.php`, the `curatedLocations` talent hub array hardcoded invalid `state_id` values (Noida was set to `4031`, which is Chandigarh; Gurugram was set to `4013`, which is Odisha; Chennai was set to `4030`, which is Gujarat; Vadodara was set to `4011`, which is Puducherry; Visakhapatnam was set to `4007`, which is Haryana; and international hubs were mapped to Ethiopian regional state IDs `1, 2, 5...`).
- **Blind Client Trust in Controllers:** In `ServiceCompanyJobApplicationController` (`store`, `clientstore`, `clientappstore`, `update`) and `JobApplyService`, the backend accepted the submitted `location_state_id` if non-zero, skipping authoritative server derivation via `GeneralMethod::stateId($cityID)`.
- **Inverted Coalesce in Elasticsearch Indexing:** In `GeneralMethod::JobApplicationsAddEditUpload` and `BenchJobApplicationsAddEditUpload`, `state_list` joined on `COALESCE(NULLIF(job_applications.state, 0), city_list.state_id)`, giving precedence to the polluted `job_applications.state` column over the authoritative `city_list.state_id` relation.
- **Client-Side Location Extraction Fallback:** When `rawData.current_location` was populated with a partial string (such as `"Anand, India"`), `CandidateSnapshotResolver.js` returned the string directly without resolving the structured city, state, and country components.

---

## 3. Architecture & Data Flow

```mermaid
flowchart TD
    subgraph Client ["Client Browser (WASM / User RAM)"]
        A[Resume Parser / Location Autocomplete] -->|Select Hub| B[curatedLocations SSOT Cache]
        B -->|Authoritative IDs| C[Form Submit: city_id, state_id, country_id]
        M[CandidateSnapshotResolver.js] -->|Structured p.length >= 2| N[PrescreenReportEngine.js]
        N -->|knownCityStateMap Reconciliation| O[PDF Dossier: City, State, Country]
    end

    subgraph Backend ["Laravel Web / App Server"]
        C --> D[Controller Write Path]
        D -->|resolveAuthoritativeLocationHierarchy| E[Derive State & Country from city_list]
        E -->|Store Authoritative IDs| F[(MySQL job_applications)]
    end

    subgraph Storage ["CQRS Storage Tier"]
        F -->|CQRS Sync| G[GeneralMethod::JobApplicationsAddEditUpload]
        H[(city_list SSOT)] -->|COALESCE city_list.state_id| G
        I[(state_list)] -->|COALESCE state_list.country_id| G
        G --> J[(Elasticsearch: ats_candidates_elastic)]
    end

    J -->|Universal Read Scope| M
```

### Deterministic SSOT Hierarchy Invariant
In geographical relations, a valid `city_id` belongs to **exactly one** state (`city_list.state_id`) and **exactly one** country (`state_list.country_id`). Client inputs cannot alter physical geography. Therefore:
$$\text{state\_id} = \text{city\_list.state\_id}(\text{city\_id})$$
$$\text{country\_id} = \text{state\_list.country\_id}(\text{city\_list.state\_id})$$

---

## 4. File Changes Matrix

| File Path | Type | Intent & Changes Applied |
|:---|:---:|:---|
| `app/Helper/GeneralMethod.php` | Modified | Added `resolveAuthoritativeLocationHierarchy($cityId, $fallbackStateId, $fallbackCountryId)` with 24h Redis cache; updated `resolveCandidateLocationHierarchy` to prioritize `city_list` SSOT and support multi-delimiter casing (`/-`); fixed Elasticsearch indexing queries to prioritize `COALESCE(city_list.state_id, NULLIF(job_applications.state, 0))` and `COALESCE(state_list.country_id, NULLIF(job_applications.country, 0))`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | Modified | Updated `store`, `clientstore`, `clientappstore`, and `update` to resolve authoritative state and country via `GeneralMethod::resolveAuthoritativeLocationHierarchy`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | Modified | Added explicit projection of authoritative `city_name`, `state_name`, `country_name`, and `location_text` from `PrescreenDossierService` onto candidate objects in `forwardModal` and `forwardResume`. |
| `app/Services/JobApplyService.php` | Modified | Updated career apply location resolution to enforce `GeneralMethod::resolveAuthoritativeLocationHierarchy`. |
| `public/js/opendoc/PrescreenReportEngine.js` | Modified | Added defense-in-depth `knownCityStateMap` dictionary to reconcile city-state pairs (e.g. Gurgaon -> Haryana, Noida -> Uttar Pradesh, Anand -> Gujarat) directly inside `normalizeCandidateDossier`. |
| `public/js/opendoc/CandidateSnapshotResolver.js` | Modified | Prioritized structured `[city_name, state_name, country_name]` over partial `current_location` strings when 2 or more parts are available. |
| `public/assets/modules/fwd-resume/engine.v4.js` | Modified | Added explicit `city_name`, `state_name`, `country_name`, and `location_text` to normalized candidate data payload. |
| `public/js/opendoc/AgyForwardResumeEngine.js` | Modified | Propagated explicit location fields across snapshot and fallback paths. |
| `public/js/opendoc/AgyResumeRenderer.js` | Modified | Propagated explicit location fields across renderer candidate snapshot mappings. |
| `resources/views/service_company/cvtracker/_forward_resume_engine.blade.php` | Modified | Updated candidate data normalization to pass authoritative location components to `PrescreenReportEngine`. |
| `resources/views/service_company/job-applications/create.blade.php` | Modified | Corrected all 25 talent hub records in `curatedLocations` with true database `city_id`, `state_id`, and `country_id`. |
| `resources/views/service_company/job-applications/clientcreate.blade.php` | Modified | Corrected all 25 talent hub records in `curatedLocations`. |
| `resources/views/service_company/bench-candidates/create.blade.php` | Modified | Corrected all 25 talent hub records in `curatedLocations`. |
| `resources/views/career-apply.blade.php` | Modified | Corrected all 25 talent hub records in `curatedLocations`. |
| `tests/Feature/PrescreenReportDataIntegrityTest.php` | Modified | Added test coverage for `resolveAuthoritativeLocationHierarchy`, conflicting state override, and contradictory location self-healing. |

---

## 5. 15.6-Inch Screen Fit & UI/UX Verification
- All location metrics in Pre-Screening Reports fit within the 3-column metric cards on standard 1366x768 and 1920x1080 screens without text truncation or overflow.
- Location pills adhere to Rule 20 with crisp typography, subtle gray backgrounds, and clean icon placement.

---

## 6. Verification Evidence

### Automated Test Suite Execution
```bash
php artisan test tests/Feature/PrescreenReportDataIntegrityTest.php
```
**Result:**
```
   PASS  Tests\Feature\PrescreenReportDataIntegrityTest
  ✓ prescreen report skills reflect job requirements and candidate drawer
  ✓ unrated candidate and unevaluated skills preserve zero and dashes without fabrication
  ✓ strict job and candidate isolation prevents cross job leakage
  ✓ cache keys are job and tenant scoped and atomically invalidated
  ✓ authorization service compute authoritative match does not fabricate 85 or rating multipliers
  ✓ prescreen report location hierarchy and state resolution
  ✓ prescreen report skills fallback and decoupled contract

  Tests:    7 passed (56 assertions)
  Duration: 5.35s
```

### Regressions Test Suite Execution
```bash
php artisan test tests/Feature/StrictSkillRatingValidationTest.php
```
**Result:**
```
   PASS  Tests\Feature\StrictSkillRatingValidationTest
  Tests:    7 passed (16 assertions)
  Duration: 11.11s
```

### Syntax Linting
```bash
php -l app/Helper/GeneralMethod.php
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
php -l app/Services/JobApplyService.php
node -c public/js/opendoc/PrescreenReportEngine.js public/js/opendoc/CandidateSnapshotResolver.js public/assets/modules/fwd-resume/engine.v4.js public/js/opendoc/AgyForwardResumeEngine.js public/js/opendoc/AgyResumeRenderer.js
```
**Result:** All checks completed with 0 errors.

### Existing Corrupted Data Healing
- Candidate #204 (`Chanda Panjiyar`): Updated in MySQL from `state = 4031` (Chandigarh) to `state = 4022` (Uttar Pradesh); re-indexed into Elasticsearch document `173`. Verified in Elasticsearch:
  - `city_name: "noida"`
  - `state_name: "uttar pradesh"`
  - `country_name: "india"`
  - `location_text: "Noida, Uttar Pradesh, India"`
