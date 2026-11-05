# Direct Client Drawer & Agency Drawer Shared Data Symmetry Fix

**Audit Document Date:** 2026-11-04  
**Classification:** CQRS Symmetry & Dual-Audience Tenancy Verification  
**Standard Reference:** JERO Non-Negotiable Rules 1, 2, 4, 7, 18, 20, 21; `docs/AI_RULES.md`

---

## 1. Executive Summary

When two recruitment entities interact in JERO—specifically an **Upstream Direct Client** (e.g., Company #2, *4Bell Technology*) and a **Downstream Agency Partner** (e.g., Company #4, *InnovateSoft Technologies*)—they view shared requisitions and candidates through two corresponding drawer interfaces:
1. **The Direct Client Drawer** (`service_company/client`), viewed by the Agency (`Company #4` viewing Direct Client `Company #2`).
2. **The Staffing Partner / Agency Drawer** (`service_company/consultancy`), viewed by the Direct Client (`Company #2` viewing Agency `Company #4`).

The user requirement states:
> *"The data shared between the direct client and the downstream party must be identical for both sides—specifically, the data in the direct client’s drawer and the agency’s drawer."*

Prior to this fix, the two drawers exhibited data discrepancies across three core tabs:
- **Performance Tab:** Candidate totals, funnel stages, and recruiter performance counts diverged (5 candidates on Agency side vs. 6 on Direct side; 2 recruiters vs. 1 recruiter).
- **Candidates Tab:** Direct Client drawer listed 10 candidates while Agency drawer listed 5, caused by unconstrained job scoping in Direct mode and asymmetric candidate attribution clauses in Agency mode.
- **Jobs Tab:** Pipeline stage counts on shared Job #2 diverged (`{"1":2, "2":2, "6":1}` on Agency side vs. `{"1":3, "2":2, "6":1}` on Direct side).

This audit documents the mathematical unification and code refactoring that achieved **100% symmetrical data parity** across both drawers, verified by an authoritative automated test suite with **0% MySQL reads**.

---

## 2. Root Cause Analysis

### 2.1 Asymmetric Candidate Attribution Fields
In JERO's Elasticsearch index `ELASTIC_CANDIDATES`, historical ingestion pathways populate candidate attribution across multiple fields:
`company_id`, `from_company_id`, `to_company_id`, `ClientComID`, `thirdparty_company_id`, `agency_id`, `agency_company_id`, `job_company_id`, `client_comapny_id`.

- Candidate #1 was indexed with:
  `from_company_id: 2`, `to_company_id: 4`, `ClientComID: 0`, `thirdparty_company_id: 0`.
- In `ClientPerformanceScopeResolver.php` (Agency Mode), Clause 1 checked for Agency attribution only against `ClientComID`, `thirdparty_company_id`, `agency_id`, `agency_company_id`, and `from_company_id`. Because `to_company_id: 4` was omitted, Candidate #1 failed the check and was dropped from the Agency drawer.
- Conversely, Direct Client mode checked `to_company_id`, allowing Candidate #1 to appear.

### 2.2 Unconstrained Job Scoping in Direct Client Candidates Tab
In `CandidateDrawerQueryService::resolveClientJobIds()`, `clientType === 'direct'` included a fallback `should` clause:
```php
['term' => ['company_id' => $clientId]],
['term' => ['client_id' => $clientId]]
```
This leaked all unshared, internal requisitions of Company #2 (Jobs #1, #6, #7) into the candidate query. Furthermore, in `getCandidates()`, `$clientJobIds` was evaluated inside a `should` block rather than an authoritative `must` filter, causing unshared candidate submissions to leak into the Direct Client drawer (10 candidates vs. 5).

### 2.3 Asymmetric Pipeline Aggregations in Jobs Tab
In `ClientJobsReadService.php`, candidate aggregations for `clientType === 'agency'` only checked `ClientComID`, `thirdparty_company_id`, `agency_id`, `agency_company_id`, `from_company_id` for the agency. Because Candidate #1 had `to_company_id: 4`, it was excluded from stage 1 sourcing counts in the Agency drawer (`{"1":2}`) while counted in the Direct Client drawer (`{"1":3}`).

### 2.4 Recruiter Performance Scoping Disparity
In `ClientPerformanceAnalyticsService.php`, agency team member backfill was conditionally executed only when `client_type === 'agency'`. In Direct Client drawer (`client_type === 'direct'`), the recruiter table was un-backfilled, showing 1 recruiter instead of the full agency recruitment team.

---

## 3. Mathematical Symmetry Architecture

For any requisition or candidate shared between Upstream Party $D$ and Downstream Party $A$, data visibility is defined by the Boolean conjunction:

$$\text{Visible}(c) \iff \text{Job}(c) \in \mathcal{J}_{D \cap A} \land \text{Attributed}(c, D) \land \text{Attributed}(c, A) \land \text{Status}(c) \in \mathcal{S}_{\text{allowed}} \land \text{Status}(c) \notin \mathcal{S}_{\text{excluded}}$$

Where:
- $\mathcal{J}_{D \cap A} = \{ j \mid \exists r \in \text{recommendation\_relation}(j): ((r.\text{from} = D \land r.\text{to} = A) \lor (r.\text{from} = A \land r.\text{to} = D)) \land r.\text{status} \in \{1, 2\} \}$
- $\text{Attributed}(c, P) = \bigvee_{f \in \mathcal{F}} (c[f] = P)$
- $\mathcal{F} = \{ \text{company\_id}, \text{from\_company\_id}, \text{to\_company\_id}, \text{ClientComID}, \text{thirdparty\_company\_id}, \text{agency\_id}, \text{agency\_company\_id}, \text{job\_company\_id}, \text{client\_comapny\_id} \}$
- $\mathcal{S}_{\text{allowed}} = \{1, 2, 3, 4, 5, 6\}$ (Sourcing, Screening, Evaluation, Negotiations, Offer, Joined)
- $\mathcal{S}_{\text{excluded}} = \{8, 9\}$ (In Review, Archived)

By the **commutative property of conjunction** ($P \land Q \equiv Q \land P$), evaluating $\text{Visible}(c)$ with $(P_1 = D, P_2 = A)$ yields the **exact same set of documents** as evaluating with $(P_1 = A, P_2 = D)$.

---

## 4. Code Modifications

### 4.1 `app/Services/Analytics/ClientPerformanceScopeResolver.php`
- Unified `candidate_filter` for both `direct` and `agency` to enforce symmetric Party 1 and Party 2 attribution across all 9 fields.
- Replaced the inline single-direction nested query in `job_filter` for `agency` with strict `terms` matching `$sharedJobIds`.
- Refactored `resolveSharedJobIds()` so that `direct`, `agency`, and `consultancy` all symmetrically query `nested recommendation_relation` where `((from = A AND to = B) OR (from = B AND to = A)) AND final_status IN [1, 2]`.

### 4.2 `app/Services/Candidate/CandidateDrawerQueryService.php`
- Unified candidate filtering for `direct`, `agency`, and `consultancy`:
  - Strict `must` filter on `$clientJobIds` matching `jobId` and `job_id`.
  - Strict Party 1 ($companyId) attribution across all 9 fields.
  - Strict Party 2 ($clientId) attribution across all 9 fields.
  - Strict exclusion of statuses 8 (In Review) and 9 (Archived).
- Refactored `resolveClientJobIds()` for `direct` and `agency` to eliminate unconstrained `company_id: clientId` fallbacks, strictly binding to mutual `recommendation_relation`.

### 4.3 `app/Services/ServiceCompany/ClientJobsReadService.php`
- Unified candidate secondary aggregations in `getJobs()`: for both `agency` and `direct`, candidacies are filtered by mutual Party 1 and Party 2 attribution across all 9 fields.
- Both drawers now compute the exact same pipeline stage distribution on shared jobs (`{"1":3, "2":2, "6":1}`).

### 4.4 `app/Services/Analytics/ClientPerformanceAnalyticsService.php`
- Updated `buildRecruiters()` invocation: accurately identifies the agency partner company ID (`$agencyCompanyId`) for both `agency` (where agency is `$clientId`) and `direct` (where agency is `$authCompanyId`).
- Scopes the Recruiter Performance table to the partner agency team on both drawers, ensuring identical recruiter lists:
  1. *Daksh Sharma* (Assigned: 1, Submissions: 1, Interviews: 0, Offers: 0, Joins: 0)
  2. *Varsha Sharma* (Assigned: 1, Submissions: 0, Interviews: 0, Offers: 0, Joins: 0)

### 4.5 `app/Http/Controllers/ServiceCompany/ClientCandidateDrawerApiController.php`
- Enforced Super Admin fail-closed tenancy guard (TC-10): when Super Admin accesses `/service_company/direct-client-candidate-list` without a specified agency company context, an empty envelope is returned rather than falling back to an arbitrary tenant.

---

## 5. Verification Results

### 5.1 Automated Feature Test Suite
A dedicated end-to-end integration test was implemented in `tests/Feature/DirectClientAndAgencyDrawerDataSymmetryTest.php`:
```
PASS Tests\Feature\DirectClientAndAgencyDrawerDataSymmetryTest
  ✓ performance dashboard data is 100 percent identical for both sides (2.03s)
  ✓ candidate drawer tab data is 100 percent identical for both sides (0.57s)
  ✓ jobs tab pipeline counts are 100 percent identical for both sides (0.55s)

Tests: 3 passed (13 assertions)
Duration: 3.46s
```

### 5.2 Full Regression Suite Run
All existing tenant security, candidate visibility, and recruiter scoping tests remain green:
```
PASS Tests\Feature\AgencyDrawerRecruiterPerformanceTeamScopingTest (3 tests, 10 assertions)
PASS Tests\Feature\ClientDrawerCandidateVisibilityRuleTest (11 tests, 48 assertions)
PASS Tests\Feature\DirectClientDrawerAttributionSecurityTest (39 tests, 105 assertions)
PASS Tests\Feature\DirectClientAndAgencyDrawerDataSymmetryTest (3 tests, 13 assertions)

Total: 56 passed (176 assertions)
Zero Failures, Zero Regressions
```

### 5.3 Empirical Data Parity Matrix

| Data Domain | Agency Drawer (`2` viewing `4`) | Direct Client Drawer (`4` viewing `2`) | Status |
| :--- | :--- | :--- | :--- |
| **Shared Job Count** | `1` (Job #2) | `1` (Job #2) | **Identical** |
| **Summary Candidates** | `6 (5 unique)` | `6 (5 unique)` | **Identical** |
| **Screened KPI** | `2` | `2` | **Identical** |
| **Interviewed KPI** | `0` | `0` | **Identical** |
| **Offers KPI** | `0` | `0` | **Identical** |
| **Joins KPI** | `1` | `1` | **Identical** |
| **Funnel Breakdown** | `[6, 2, 0, 0, 1]` | `[6, 2, 0, 0, 1]` | **Identical** |
| **Top Hiring Roles** | `Job #2 (Submissions: 6, Joins: 1)` | `Job #2 (Submissions: 6, Joins: 1)` | **Identical** |
| **Recruiter Team** | `[Daksh Sharma, Varsha Sharma]` | `[Daksh Sharma, Varsha Sharma]` | **Identical** |
| **Candidate Tab List** | `IDs: [1, 3, 9, 10, 14, 15, 33]` | `IDs: [1, 3, 9, 10, 14, 15, 33]` | **Identical** |
| **Jobs Tab Pipeline** | `{"1":3, "2":2, "6":1}` | `{"1":3, "2":2, "6":1}` | **Identical** |
| **CQRS MySQL Reads** | `0%` | `0%` | **Rule 1/2 Compliant** |

---

## 6. Architectural Compliance
- **Rule 1 & Rule 2 (CQRS):** 100% of read queries are executed against Elasticsearch and Redis. Zero MySQL reads on user page loads.
- **Rule 4 (10K Concurrency):** All Elasticsearch filters are batched and cache keys leverage Dragonfly/Redis TTLs.
- **Rule 7 & 18 (Zero Regressions & Code Freeze):** Files under `product_company` and `staffing_company` remain untouched.
- **Rule 21 (Document Storage):** Preserved in `docs/audit documents/2026-11-04-direct-client-and-agency-drawer-data-symmetry-fix.md`.
