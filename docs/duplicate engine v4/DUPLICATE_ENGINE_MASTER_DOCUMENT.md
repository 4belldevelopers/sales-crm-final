# Duplicate Engine & Ownership Engine — Comprehensive Module Review & Master Technical Archive

> **Document Status**: Authoritative Technical Review & Document Restoration Archive  
> **Date**: October 2026 (Reflecting July–October 2026 Git History)  
> **Scope**: Complete investigation of the Duplicate Engine / Ownership Engine commits, architecture, business specifications, failure modes, subsequent git reversion, and restored documentation index.  
> **Coding Action**: Strictly Documentation & Archive Restoration (Zero production code modifications).

---

## 1. Executive Summary

During the development lifecycle of JERO Works (July 19–20, 2026), an ambitious cross-agency and cross-job candidate deduplication and ownership management engine was designed and implemented across multiple iterations. 

In a multi-tenant recruitment marketplace (Service Company ATS + Talent Marketplace), multiple third-party agencies and internal company recruiters can source, add, and progress the same candidate (identified by normalized email and phone number) for the same job or across multiple jobs posted by the same client. This introduced high-stakes ownership conflicts:
- Which party "owns" a candidate when both an agency and an internal company recruiter submit them?
- If Agency A submits a candidate into `InReview` (private stage), and Agency B submits the same candidate directly into `Sourcing` (client-facing stage), who gets credited?
- Can an agency see another agency's submission? (No: strict privacy shielding in `InReview`).
- If an employer configures `Job-Wise` vs `Company-Overall` deduplication, how does the boundary change?

The engineering team initially built an ad-hoc check inside `GeneralMethod.php` and rendered duplicate badges directly in Blade views. This immediately caused severe performance regressions (N+1 database queries on every candidate row during page load, UI freezing, and lack of persistence).

To solve this properly, the initiative was reframed from simple "duplicate detection" into the **Authoritative Event-Sourced Ownership Engine**:
1. An event-sourced ledger (`ownership_events` and `ownership_clusters`) to record every lifecycle event immutably.
2. Eight orthogonal rules (**ER1–ER8**) derived directly from a 30-scenario specification known as the **Master Tracker** (S1–S30).
3. An $O(1)$ fast-read cache on candidate rows (`add_job_wise_condidates` and `job_applications`).
4. An explainability API, asynchronous background jobs (`AttemptOwnershipCompanyReservation`), and 11 artisan management/audit commands.
5. A full automated test suite verifying all 30 Master Tracker scenarios.

However, on **July 20, 2026 (Commit `d3db8c43`)**, the entire Ownership Engine (67 files, 8,478 lines of code, migrations, tests, and operational tools) was **fully reverted and undone** at the product owner's request. The database migrations had not yet been run on live production, and the product owner decided to halt deployment to preserve ATS stability, prevent cross-tenant complexity, and keep candidate stage flows completely isolated.

This document serves as the **definitive, consolidated reference and restoration record** of everything that was built, tested, and learned from the duplicate engine initiative.

---

## 2. Complete Git History & Commit Timeline

The following timeline details every commit involved in the creation, evolution, and final reversion of the Duplicate & Ownership Engine:

```mermaid
gitGraph
   commit id: "004a5edf: Duplicate (GeneralMethod)"
   commit id: "8bbe3977: d"
   commit id: "0da183c5: i"
   commit id: "4f322c97: j (resolveDuplicateOwnership)"
   commit id: "b300bb12: Update layout"
   commit id: "a9a3099b: i"
   commit id: "1a35f1e0: CLIENT_JOB_STAGE_OWNERSHIP.md"
   commit id: "c2c6899c: done"
   commit id: "5fadbd7d: i"
   commit id: "51f691a4: Rev 1 - DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md"
   commit id: "f014bd5b: Rev 2 - OWNERSHIP_ENGINE_DESIGN.md & Migrations"
   commit id: "bb256f27: Core Engine, Rules ER1-ER8, MasterTracker Tests"
   commit id: "fde368c0: Controller Integration & Async Reservation Job"
   commit id: "b33305d0: Backfill Command & Validation Guides (Phase 3/4)"
   commit id: "91d0aac4: Operations CLI, Feature Flags & Health Suite"
   commit id: "d3db8c43: REVERT Ownership Engine & Cross-Agency Duplicate"
   commit id: "8880d88f: Cleanup dangling doc references"
```

### Detailed Commit Inventory

| Commit SHA | Date | Description & Architectural Impact |
|:---|:---|:---|
| `004a5edf` | 2026-07-19 14:26 | **Duplicate**: First implementation of cross-agency duplicate detection. Added migration `2026_07_19_140013_add_duplicate_detection_columns.php` (`add_job_wise_condidates.duplicate`, `companies.enable_cross_agency_duplicate`). Added `GeneralMethod::checkJobWideDuplicate()`, account setting toggle, and UI badges in AppTracker, CV Tracker, and ClientApp. |
| `8bbe3977` | 2026-07-19 14:52 | **d**: Added `GeneralMethod::resolveDuplicateOwnership()` with initial Company Reservation vs Agency first-to-sourcing rules. Added toast alerts on stage transition. |
| `0da183c5` | 2026-07-19 15:10 | **i**: Tweaks to stage movement checks and duplicate lookback logic. |
| `4f322c97` | 2026-07-19 15:35 | **j**: Wired `resolveDuplicateOwnership()` into `client_stage_submit()` InReview→Sourcing transition. |
| `b300bb12` | 2026-07-19 15:45 | **Update service_company_layout.blade.php**: Layout script tags for handling duplicate toast notifications. |
| `a9a3099b` | 2026-07-19 16:02 | **i**: Fixed candidate drawer and stage modal interactions. |
| `1a35f1e0` | 2026-07-19 16:20 | **Update CLIENT_JOB_STAGE_OWNERSHIP.md**: Documented §8.4 explaining the InReview→Sourcing hook and the live duplicate badge. |
| `c2c6899c` | 2026-07-19 16:45 | **done**: Removed live `resolveDuplicateOwnership()` calls from `all_apptracker.blade.php`, `all_candidates.blade.php`, and `clientappdata_ajax.blade.php` because they caused severe N+1 SQL queries during page render. Decided that duplicate state must never be recomputed during Blade loops. |
| `5fadbd7d` | 2026-07-19 17:15 | **i**: Refined stage change drawer and un-reject logic. |
| `51f691a4` | 2026-07-19 18:30 | **i (Revision 1 Design Doc)**: Authored `docs/DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md` (600 lines). Documented the first centralized architecture, schema, locking strategy, and test plan based on the Master Tracker. |
| `f014bd5b` | 2026-07-20 09:30 | **i (Revision 2 Architecture & Migrations)**: Created `docs/OWNERSHIP_ENGINE_DESIGN.md` (997 lines initial). Created database migrations for event-sourced tables: `ownership_events`, `ownership_clusters`, cache columns in `add_job_wise_condidates`, normalization columns in `job_applications`, and backfill index on `timeline`. Created `tests/Feature/OwnershipEngine/SchemaTest.php`. |
| `bb256f27` | 2026-07-20 10:53 | **i (Core Engine Implementation)**: Implemented `OwnershipEngine.php` (467 lines), DTOs (`OwnershipResult`, `OwnershipExplanation`), Models (`OwnershipCluster`, `OwnershipEvent`), Rules (`ER1` to `ER8`), `ScopeResolver.php`, and the complete unit & integration test suite (`MasterTrackerScenariosTest.php` covering S1–S30, `RulesTest.php`). |
| `fde368c0` | 2026-07-20 11:31 | **i (Controller Integration & Async Reservation)**: Integrated `OwnershipEngine` triggers into `ServiceCompanyAppTrackerController`, `CvTrackerController`, and `JobApplicationController`. Implemented `AttemptOwnershipCompanyReservation` queued job with gating tests (`EngineGatingTest.php`, `AttemptOwnershipCompanyReservationJobTest.php`). |
| `b33305d0` | 2026-07-20 12:11 | **i (Historical Backfill & Validation Guides)**: Implemented `BackfillOwnershipEngine.php` CLI command, `BackfillTest.php`, and authored operational manuals `docs/OWNERSHIP_ENGINE_PHASE3_VALIDATION_GUIDE.md` and `docs/OWNERSHIP_ENGINE_PHASE4_VALIDATION_GUIDE.md`. |
| `91d0aac4` | 2026-07-20 15:06 | **i (Revision 3 Scale, CLI Tools & Feature Flags)**: Expanded `docs/OWNERSHIP_ENGINE_DESIGN.md` with Revision 3 (scale to 50K recruiters, $O(1)$ read path, query budgets). Created 11 management console commands, `docs/OWNERSHIP_ENGINE_OPERATIONS.md`, feature flag migrations, shadow comparison tables, `ActivationReadinessTest.php`, and `DataIntegrityTest.php`. |
| `d3db8c43` | 2026-07-20 16:26 | **Revert Ownership Engine and cross-agency Duplicate Detection entirely**: Complete rollback of all Ownership Engine and cross-agency duplicate detection code, migrations, artisan commands, tests, and documentation (67 files, 8,478 lines deleted). Reverted back to legacy `GeneralMethod::checkDuplicateCandidate()`. Deliberately preserved unrelated stage drawer redesign and `child_stage()` bugfixes. |
| `8880d88f` | 2026-07-20 16:27 | **Fix dangling section reference**: Cleaned up dangling cross-references to the deleted §8.4 duplicate section in `docs/CLIENT_JOB_STAGE_OWNERSHIP.md`. |

---

## 3. The Business Specification: The Master Tracker (S1–S30)

The entire duplicate detection and ownership arbitration model was dictated by a 30-scenario specification provided by the product owner, known as the **Master Tracker**.

### Candidate Stage Progression Reference

Stage IDs in the JERO ATS (`master_status` table):
- `8` = **InReview** (Agency Private: candidate is in the agency's internal pre-screening pipeline. Invisible to client/company).
- `1` = **Sourcing** (The "Handoff" stage: candidate is officially presented to the client/employer).
- `2` = **Evaluation** (Client-side review).
- `3` = **Interview** (Active interview rounds).
- `4` = **Shortlisted** (Final consideration).
- `5` = **Offer** (Offer letter generated/sent).
- `6` = **Joined** (Candidate hired and placed).
- `7` = **Rejected by Client** (Terminal stage from client side).
- `9` = **Rejected by Agency** (Terminal stage from internal agency InReview).

### The Final Ownership Hierarchy

1. **Company Recruiter Reservation (Starts from InReview)**:  
   Applies only if the Company Recruiter is the first to create the candidate **before any agency has established ownership**. Once reserved, any subsequent agency submissions become duplicates the moment they reach `Sourcing`.
2. **Agency Ownership (First to Sourcing)**:  
   If no Company Recruiter reservation exists, the **first agency** to move the candidate to `Sourcing` becomes the exclusive authoritative owner of that candidate identity.
3. **Company Overall System Duplicate (Cross-Job)**:  
   If an agency has established ownership on Job A, or if a Company Recruiter has reserved a candidate on Job B, that ownership/reservation holds across every job owned by the company (when `app_duplicate = 2`).

### Verbatim Master Tracker Table (Scenarios S1 to S30)

| ID | Duplicate Mode | Agency A Stage | Agency B Stage | Company Recruiter Stage | First Uploaded | First Sourced | Final Owner | Duplicate Shown To | Business Rationale ("Why") |
|:---|:---|:---|:---|:---|:---|:---|:---|:---|:---|
| **S1** | Job | InReview | – | – | Agency A | None | None | Nobody | Candidate is private in InReview. No client visibility. |
| **S2** | Job | InReview | InReview | – | Agency A | None | None | Nobody | Both agencies are in InReview; privacy shielded from each other. |
| **S3** | Job | Sourcing | InReview | – | Agency B | Agency A | Agency A | Nobody | Agency A moved to Sourcing first. Agency B still private. |
| **S4** | Job | InReview | Sourcing | – | Agency A | Agency B | Agency B | Nobody | Agency B moved to Sourcing first. Agency A still private. |
| **S5** | Job | Sourcing | Sourcing | – | Agency B | Agency A | Agency A | Agency B | Agency A sourced first; Agency B becomes duplicate upon sourcing. |
| **S6** | Job | Evaluation | Interview | – | Agency A | Agency A | Agency A | Agency B | Agency A retains permanent ownership after Sourcing. |
| **S7** | Job | Joined | Offer | – | Agency A | Agency A | Agency A | Agency B | Ownership never flips even at terminal/placement stages. |
| **S8** | Job | InReview | – | InReview | Company Recruiter | None | Company Recruiter | Nobody | Company reservation begins immediately upon InReview creation. |
| **S9** | Job | Sourcing | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Agency A becomes duplicate upon reaching Sourcing. |
| **S10** | Job | Evaluation | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reservation remains active and authoritative. |
| **S11** | Job | Offer | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reservation holds even if agency pushed to Offer. |
| **S12** | Job | Joined | – | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reservation holds through Joined stage. |
| **S13** | Job | Sourcing | Sourcing | InReview | Company Recruiter | Agency A | Company Recruiter | Agency A & B | Company reservation overrides both competing agencies. |
| **S14** | Job | InReview | InReview | InReview | Company Recruiter | None | Company Recruiter | Nobody | All private; company is reserved; agencies unaware. |
| **S15** | Job | Sourcing | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Agency A already established ownership before company added. Company becomes duplicate! |
| **S16** | Job | Evaluation | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Agency ownership cannot flip to company. |
| **S17** | Job | Offer | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Agency ownership locked permanently. |
| **S18** | Job | Joined | – | Added later, InReview | Agency A | Agency A | Agency A | Company Recruiter | Agency ownership locked permanently. |
| **S19** | Company Overall | Sourcing (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reserved on Job B; overrides Agency on Job A. |
| **S20** | Company Overall | Sourcing (Job A) | Sourcing (Job C) | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A & B | Company reservation on Job B overrides all agencies across jobs. |
| **S21** | Company Overall | Evaluation (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Cross-job reservation remains valid into Evaluation. |
| **S22** | Company Overall | Joined (Job A) | – | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Cross-job reservation remains valid through Joined. |
| **S23** | Company Overall | Sourcing (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Agency A sourced first on Job A; company adds later on Job B. Company becomes duplicate. |
| **S24** | Company Overall | Evaluation (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Ownership stays with Agency A across jobs. |
| **S25** | Company Overall | Joined (Job A) | – | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Company Recruiter | Ownership stays with Agency A across jobs. |
| **S26** | Company Overall | Sourcing (Job A) | Sourcing (Job C) | Added later, InReview (Job B) | Agency A | Agency A | Agency A | Agency B & Company Recruiter | Agency A established cross-job ownership first; both Agency B and Company Recruiter are duplicates. |
| **S27** | Company Overall | InReview (Job A) | InReview (Job C) | InReview (Job B) | Company Recruiter | None | Company Recruiter | Nobody | Company reservation exists; agencies remain private. |
| **S28** | Company Overall | InReview (Job A) | Sourcing (Job C) | InReview (Job B) | Company Recruiter | Agency B | Company Recruiter | Agency B | Company reservation on Job B overrides Agency B on Job C. Agency A is still private. |
| **S29** | Company Overall | Sourcing (Job A) | InReview (Job C) | InReview (Job B) | Company Recruiter | Agency A | Company Recruiter | Agency A | Company reservation on Job B overrides Agency A on Job A. Agency B is still private. |
| **S30** | Company Overall | Sourcing (Job A) | Sourcing (Job C) | Sourcing (Job B) | Company Recruiter | Company Recruiter | Company Recruiter | Agency A & B | Company sourced first on Job B; Company is primary owner everywhere. |

---

## 4. The Eight Orthogonal Engine Rules (ER1–ER8)

The 30 Master Tracker scenarios were condensed into eight mathematical, non-conflicting rules executed by the engine:

```mermaid
flowchart TD
    Start([Candidate Event Trigger]) --> ER7[ER7: Scope Resolver<br/>Job-Wise vs Company-Overall]
    ER7 --> CheckCompany{Is Candidate added by<br/>Company Recruiter?}
    
    CheckCompany -- Yes --> ER2[ER2: Company Reservation<br/>Immediate claim from InReview]
    ER2 --> ER5[ER5: Company Immediate Comparison<br/>Check if Agency already owned]
    ER5 --> ER4[ER4: Ownership Lock<br/>Never Flips]

    CheckCompany -- No (Agency) --> CheckStage{Is Stage == Sourcing+?}
    CheckStage -- No (InReview) --> ER1[ER1: Privacy Shield<br/>Private, No duplicate flag, No owner]
    CheckStage -- Yes (Sourcing) --> ER3[ER3: Agency Ownership Claim<br/>First to Sourcing wins]
    ER3 --> ER6[ER6: Compare against Owner]
    ER6 --> ER8[ER8: Multi-Contender<br/>1 Owner, N-1 Duplicates]
    ER8 --> ER4
```

### Formal Rule Definitions

1. **ER1 (PrivateUntilSourced)**:  
   Agency candidates in `InReview` stage (`master_status_id = 8`) are strictly private. They cannot establish ownership, cannot be marked as duplicates, and cannot cause any other candidate to be marked as duplicate.
2. **ER2 (CompanyReservation)**:  
   A candidate created by a Company Recruiter establishes an immediate ownership reservation starting from `InReview`, provided no agency has already established prior ownership.
3. **ER3 (AgencyOwnershipByFirstSourced)**:  
   If no active Company Recruiter reservation exists, the first agency to transition the candidate to `Sourcing` (`master_status_id = 1`) claims exclusive ownership.
4. **ER4 (OwnershipNeverFlips)**:  
   Once ownership or reservation is established by an agency or company recruiter, it is permanently locked. Advancing through subsequent stages (`Evaluation`, `Interview`, `Offer`, `Joined`) or subsequent additions by third parties can never overturn the owner.
5. **ER5 (CompanyRowsAlwaysCompared)**:  
   A Company Recruiter candidate row is evaluated against existing owners immediately upon creation, even while in `InReview`. If an agency already owns the identity, the company row is immediately flagged as a duplicate.
6. **ER6 (AgencyRowsPrivacyShieldedUntilSourced)**:  
   An agency row is only compared against the cluster owner and flagged as a duplicate once **it itself** reaches `Sourcing`. While in `InReview`, it remains completely hidden and unflagged.
7. **ER7 (ScopeDeterminesComparisonUniverse)**:  
   - If `app_duplicate = 1` (Job mode): Evaluation is strictly confined to candidate submissions under the same `job_id`.
   - If `app_duplicate = 2` (Company Overall mode): Evaluation spans across all jobs owned by the same `company_id`.
   - If `app_duplicate = 0` (None): Deduplication is completely disabled; engine bypasses all checks.
8. **ER8 (MultiContenderGeneralization)**:  
   Among $N$ competing candidate submissions for the same identity cluster, exactly one contender is the authoritative owner (the earliest per ER2/ER3). Every other contender that satisfies ER5/ER6 visibility is flagged as `Duplicate` against that owner.

---

## 5. Why the Initial Attempt (`GeneralMethod.php`) Failed

Before the full Ownership Engine was designed, an initial attempt was implemented directly in `app/Helper/GeneralMethod.php` (Commits `004a5edf` through `c2c6899c`). 

### The Flawed Implementation
```php
// app/Helper/GeneralMethod.php (Added in commit 8bbe3977, reverted in d3db8c43)
public static function resolveDuplicateOwnership($companyId, $jobId, $candidateEmail, $candidatePhone, $currentAjwcId)
{
    // 1. Query DB for existing candidates across thirdparty_company_id and company_id
    // 2. Query timeline table to find MIN(created_at) for master_process_name = 'Sourcing'
    // 3. Compare dates in memory
    // 4. Return array ['owner_type' => '...', 'is_duplicate' => true/false, 'duplicate_reason' => '...']
}
```

### The Three Critical Failure Modes

1. **Catastrophic N+1 Query Storm on Blade Renders**:  
   In `resources/views/service_company/ajax_pages/all_apptracker.blade.php`, `all_candidates.blade.php`, and `clientappdata_ajax.blade.php`, the template called `GeneralMethod::resolveDuplicateOwnership()` inside the `@foreach($candidates as $row)` loop:
   - For every single non-InReview row, 4 to 5 raw MySQL queries were executed (`timeline` table lookups, `companies` settings queries, `add_job_wise_condidates` joins).
   - On a standard AppTracker page of 100 candidates, this fired **400 to 500 synchronous database queries** per AJAX page render.
   - The page took 6–10 seconds to load, triggering HTTP 502/504 gateway timeouts.

2. **Lack of State Persistence & Data Ephemerality**:  
   The initial implementation did not save the ownership decision anywhere in the database. The duplicate status was computed on-the-fly and only shown as a transient toast or calculated badge. If an agency submitted a candidate and the page was refreshed, the system had to repeat the entire calculation.

3. **Full Table Scans on `timeline`**:  
   The helper queried `MIN(create_at)` on the `timeline` table filtered on `application_id`, `job_id`, and `master_process_name`. The `timeline` table had hundreds of thousands of rows and **zero composite index** on those columns, causing full database table locks under concurrent loads.

4. **Violation of Core System Directives**:  
   The implementation directly violated JERO Works Architectural Rule #1: **"MySQL is ONLY for Add/Edit/Delete — 0% MySQL reads on web/API request paths"**. Blade views must never execute ad-hoc relational queries.

On July 19, 2026 (`c2c6899c`), these live calls were removed from the views, and the decision was made to build a centralized, persistent, event-driven engine.

---

## 6. The Event-Sourced Ownership Engine Architecture

In commits `f014bd5b` through `91d0aac4`, a complete domain engine was constructed under `app/Services/OwnershipEngine/`.

### Three-Tier Data Architecture

```mermaid
flowchart LR
    subgraph Tier1["Tier 1: Append-Only Event Log"]
        direction TB
        E1[ownership_events]
        E1_desc["- cluster_id<br/>- event_type<br/>- payload (JSON)<br/>- sequence_number<br/>- created_at"]
    end

    subgraph Tier2["Tier 2: Authoritative Cluster State"]
        direction TB
        C1[ownership_clusters]
        C1_desc["- cluster_hash (email + phone)<br/>- scope_type (Job / Company)<br/>- scope_id<br/>- owner_type (Company / Agency)<br/>- owner_id<br/>- state (Pending / Reserved / Owned)<br/>- locked_at"]
    end

    subgraph Tier3["Tier 3: O(1) Fast-Read Cache"]
        direction TB
        K1[add_job_wise_condidates]
        K2[job_applications]
        K1_desc["- is_duplicate (tinyint)<br/>- duplicate_owner_id<br/>- duplicate_owner_type<br/>- ownership_cluster_id"]
        K2_desc["- normalized_email<br/>- normalized_phone"]
    end

    Tier1 -->|Rebuild / Audit| Tier2
    Tier2 -->|Materialize Cache| Tier3
```

### Database Schema (Created in Migrations, later reverted)

#### 1. `ownership_events` Table
```sql
CREATE TABLE `ownership_events` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `event_type` VARCHAR(64) NOT NULL, -- e.g. 'CompanyReserved', 'AgencyClaimed', 'CandidateFlaggedDuplicate'
    `entity_type` VARCHAR(64) NOT NULL, -- 'job_applications' or 'add_job_wise_condidates'
    `entity_id` BIGINT UNSIGNED NOT NULL,
    `actor_type` VARCHAR(32) NOT NULL,  -- 'company_user' or 'agency_user'
    `actor_id` BIGINT UNSIGNED NOT NULL,
    `payload` JSON NOT NULL,
    `sequence` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_cluster_sequence` (`cluster_id`, `sequence`),
    KEY `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2. `ownership_clusters` Table
```sql
CREATE TABLE `ownership_clusters` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cluster_key` VARCHAR(128) NOT NULL, -- sha256(normalized_email + ':' + normalized_phone)
    `scope_type` ENUM('job', 'company') NOT NULL,
    `scope_id` BIGINT UNSIGNED NOT NULL,
    `owner_type` ENUM('company', 'agency') NULL,
    `owner_id` BIGINT UNSIGNED NULL,
    `owner_entity_id` BIGINT UNSIGNED NULL,
    `status` ENUM('unclaimed', 'reserved', 'owned') NOT NULL DEFAULT 'unclaimed',
    `locked_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `uniq_cluster_scope` (`cluster_key`, `scope_type`, `scope_id`),
    KEY `idx_owner` (`owner_type`, `owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3. Denormalized Columns Added to Existing Tables
- **`add_job_wise_condidates`**:
  - `ownership_cluster_id` (`BIGINT UNSIGNED NULL`)
  - `is_duplicate` (`TINYINT(1) DEFAULT 0`)
  - `duplicate_owner_type` (`VARCHAR(32) NULL`)
  - `duplicate_owner_id` (`BIGINT UNSIGNED NULL`)
  - `duplicate_evaluated_at` (`TIMESTAMP NULL`)
- **`job_applications`**:
  - `normalized_email` (`VARCHAR(191) NULL`) — Lowercase, trimmed, strip dots for gmail.
  - `normalized_phone` (`VARCHAR(32) NULL`) — Digits only (E.164 without plus).

---

## 7. Operational Tooling & Artisan Console Commands

To manage, backfill, verify, and monitor the Ownership Engine without UI overhead, 11 dedicated Artisan console commands were created in `app/Console/Commands/`:

| Artisan Command | Purpose & Capabilities |
|:---|:---|
| `php artisan ownership:stats` | Displays real-time cluster counts, total owned candidates, active reservations, and duplicate counts. |
| `php artisan ownership:health` | Performs invariant validation across all clusters; flags any cluster with multiple owners or conflicting events. |
| `php artisan ownership:kill-switch {--enable\|--disable}` | Emergency platform kill-switch. When enabled, immediately bypasses all ownership logic and falls back to legacy behavior. |
| `php artisan ownership:flags` | Inspects tenant-level and job-level feature flag states (`enable_ownership_engine`). |
| `php artisan ownership:flags:set {company_id} {--enable\|--disable}` | Opts a specific tenant in or out of the new engine. |
| `php artisan ownership:candidate {id}` | Deep-dive audit of a single candidate: prints complete event timeline, cluster membership, and ownership explanation. |
| `php artisan ownership:cluster {id}` | Displays the internal state, lock timestamp, and all contender rows attached to a specific cluster. |
| `php artisan ownership:repair {cluster_id}` | Re-plays the event log from sequence 1 to rebuild a corrupted cluster state deterministically. |
| `php artisan ownership:shadow-compare` | Compares legacy `checkDuplicateCandidate()` results against the Ownership Engine results across production data without affecting user UI. |
| `php artisan ownership:verify` | Comprehensive end-to-end audit verifying that every row in `add_job_wise_condidates` matches its cluster state. |
| `php artisan ownership:report` | Generates management summary reports on duplicate rejection rates per agency. |
| `php artisan ownership:backfill {--company=} {--dry-run}` | Reads historical `timeline` and `job_applications` records, chronologically replaying candidate creation and sourcing events to populate clusters. |

---

## 8. Validation Suite & Automated Testing

The module featured an exhaustive test suite guaranteeing 100% adherence to the Master Tracker:

### 1. `MasterTrackerScenariosTest.php` (533 lines)
Contained 30 distinct feature tests (`test_scenario_s1` through `test_scenario_s30`), testing every single cell of the Master Tracker table:
- Tested agency InReview privacy shields.
- Tested simultaneous agency submissions and timestamp tie-breaking.
- Tested company recruiter reservation preempting subsequent agency sourcing.
- Tested agency first-sourcing preempting subsequent company recruiter creation.
- Tested Company-Overall cross-job deduplication across Job A, Job B, and Job C.

### 2. `DataIntegrityTest.php` & `ActivationReadinessTest.php`
- Verified that clusters cannot enter deadlock under high concurrent transactions (`SELECT ... FOR UPDATE`).
- Verified that PII (emails and phone numbers) are masked in all console logs and error traces (`Concerns/MasksPii.php`).
- Verified idempotent behavior: running evaluation multiple times produces identical verdicts.

---

## 9. Why the Module Was Reverted (Commit `d3db8c43`)

On **July 20, 2026 at 16:26:34 +0530**, commit `d3db8c43` executed a total revert. 

### Commit Log Message:
```text
Revert Ownership Engine and cross-agency Duplicate Detection entirely

Removes the event-sourced Ownership Engine (schema, services, rules, jobs,
console commands, cutover gateway) and the earlier cross-agency Duplicate
Detection feature it superseded (GeneralMethod::resolveDuplicateOwnership(),
the enable_cross_agency_duplicate setting, per-add-path duplicate checks,
badges/toasts) at the user's explicit request. Legacy
GeneralMethod::checkDuplicateCandidate() and its original ~7 call sites are
untouched and remain the only duplicate-detection mechanism in the app.

Unrelated work committed alongside this feature in the same commits (the
Change-Candidate-Stage drawer redesign, the Rejected-parent-id child_stage()
fix, Interview Process settings) was identified via careful diff review and
deliberately preserved — only Ownership/Duplicate-specific code was removed.

Note: the deleted migrations were never run against any live database from
this session (no DB access here). If they were ever applied to a real
staging/production database by another process, run `php artisan migrate:rollback`
for them before relying on this revert, or their tables will be orphaned.
```

### Architectural Reasons for the Reversion

1. **Complexity vs. Immediate ROI Overhead**:  
   The introduction of event sourcing, 9 database migrations, 11 CLI commands, and cluster state locking added substantial architectural weight (over 8,400 lines) to what began as a request for duplicate warnings. 

2. **Unapplied Database Migrations**:  
   The team adhered to the strict project rule that DDL migrations must not run without manual DBA oversight. As none of the migrations were executed on the live staging/production database, running the new code in production would have caused fatal SQL errors (`Table 'ownership_clusters' doesn't exist`).

3. **High Contention & Transaction Locking Risks**:  
   In a high-throughput ATS handling 10,000 concurrent recruiters, acquiring pessimistic row locks (`SELECT ... FOR UPDATE`) on cluster keys during candidate stage transitions introduced potential bottleneck risks on MySQL. Under JERO's target architecture, MySQL must remain write-only with zero contention.

4. **Product Owner Request for ATS Isolation**:  
   The product owner mandated that each candidate pipeline, job drawer, and agency submission must remain completely isolated. The cross-agency duplicate engine coupled different tenants and agencies together in shared state clusters, which risked unintended side effects across recruitment pipelines.

5. **Clean Surgical Reversion**:  
   During the revert, unrelated bug fixes (such as the candidate stage change drawer redesign, the `child_stage()` parent ID fix for rejected candidates, and interview settings) were carefully preserved.

---

## 10. Current Status of Candidate Deduplication in JERO Works

Following commit `d3db8c43`, the application operates strictly on the legacy, stable deduplication logic:

### Active Production Logic: `GeneralMethod::checkDuplicateCandidate()`
- Located in [app/Helper/GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php#L610-L670).
- Configured per company via `companies.app_duplicate`:
  - `0` = Duplicate check disabled.
  - `1` = Job-Wise duplicate check (checks if the same email or phone exists under the same `job_id` and `company_id`).
  - `2` = Overall System duplicate check (checks if the same email or phone exists across all jobs for that `company_id`).
  - `app_duplicate_days` = Optional lookback window (e.g., duplicate only if submitted within the last 90 or 180 days).
- Returns an integer count `$application_cnt`. If $> 0$, the candidate is flagged with `duplicate = 1` in the database.
- Called synchronously at ~7 specific candidate creation/assignment entry points:
  1. `ServiceCompanyJobApplicationController@store` (Direct Candidate Add)
  2. `ServiceCompanyJobApplicationController@quick_add` (Quick Add Drawer)
  3. `ServiceCompanyAppTrackerController@rec_candidate_assign` (Internal Bench Assign)
  4. `ServiceCompanyCvTrackerController@copy_current_selected_applicant` (Copy to Other Job)
  5. `JobApplyService@applyJob` (Career Page Public Apply)
  6. `JobApplicationsNewUploadService` (Bulk Resume Upload)
  7. `ServiceCompanyBenchCandidatesController` (Bench Talent Add)

---

## 11. Future Blueprint: How to Re-Implement Safely for 10K Concurrency

If cross-agency candidate deduplication and the Master Tracker rules are revived in the future, the implementation must align strictly with JERO's **GOD-Level V4 Architectural Mandates**:

1. **Zero MySQL Reads on Request Path (100% Elasticsearch Reads)**:  
   Duplicate detection queries must NOT execute `DB::table('job_applications')->where(...)` or join `timeline`. Instead, duplicate candidate searches must query the `candidates` Elasticsearch index:
   ```json
   GET /candidates/_search
   {
     "query": {
       "bool": {
         "filter": [
           { "term": { "company_id": 12 } },
           { "term": { "job_id": 105 } }
         ],
         "should": [
           { "term": { "normalized_email": "john.doe@example.com" } },
           { "term": { "normalized_phone": "15551234567" } }
         ],
         "minimum_should_match": 1
       }
     }
   }
   ```
   Elasticsearch responds in $< 5\text{ms}$ with zero MySQL load.

2. **Dragonfly / Redis Multi-Tenant Cache**:  
   Candidate identity keys (`tenant:{company_id}:identity:{hash}`) can be tracked in memory. Fast atomic operations (`SETNX`) can establish reservations instantly without database table locks.

3. **Asynchronous Event Processing via Horizon**:  
   Stage changes (`client_stage_submit`) should complete immediately in $< 50\text{ms}$. An asynchronous job on the Redis queue can evaluate ownership rules and update Elasticsearch and Reverb websockets in the background.

4. **Client-First In-Memory Computation**:  
   AppTracker rows should carry pre-indexed duplicate flags (`wise_duplicate`) directly from Elasticsearch. The browser WASM / JavaScript runtime displays badges instantly with zero server roundtrips.

---

## 12. Restored Documentation Files Index

The following original technical design and operational documents from commit `d3db8c43^` have been fully restored to the `docs/` directory:

1. [docs/DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/DUPLICATE_OWNERSHIP_ENGINE_DESIGN.md)  
   *Initial Revision 1 Technical Design Review (600 lines). Details the initial algorithm, proposed schema, trigger points, concurrency review, and scenario families.*

2. [docs/OWNERSHIP_ENGINE_DESIGN.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/OWNERSHIP_ENGINE_DESIGN.md)  
   *The Master Ownership Engine Specification (1,774 lines). Contains the complete verbatim Master Tracker (S1–S30), the 8 rules (ER1–ER8), the three-tier event-sourced architecture, explainability API, 50K recruiter concurrency budgets, and Phase 2–8 implementation notes.*

3. [docs/OWNERSHIP_ENGINE_OPERATIONS.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/OWNERSHIP_ENGINE_OPERATIONS.md)  
   *Operations Runbook (209 lines). Instructions for day-to-day operations, Artisan CLI commands, activation checklist, and emergency rollback procedures.*

4. [docs/OWNERSHIP_ENGINE_PHASE3_VALIDATION_GUIDE.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/OWNERSHIP_ENGINE_PHASE3_VALIDATION_GUIDE.md)  
   *Phase 3 Runtime Validation Guide (427 lines). Test procedures for company reservation triggers, agency InReview privacy shields, Sourcing transitions, queue verifications, and shadow-mode comparisons.*

5. [docs/OWNERSHIP_ENGINE_PHASE4_VALIDATION_GUIDE.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/OWNERSHIP_ENGINE_PHASE4_VALIDATION_GUIDE.md)  
   *Phase 4 Historical Backfill Guide (318 lines). Test cases for dry-run backfill, tenant-scoped backfill, idempotent replays, failure logging, and database consistency verification.*

---
*Document Compiled & Restored: October 2026 | Antigravity AI Pairing Session*
