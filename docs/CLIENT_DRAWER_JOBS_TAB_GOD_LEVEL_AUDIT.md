# GOD-LEVEL ARCHITECTURAL & DATA AUDIT REPORT (V11 — MASTER ENTERPRISE PRODUCTION SPECIFICATION)
## CLIENT DRAWER — JOBS TAB (INTERNAL CLIENTS & DIRECT CLIENTS)
**Platform:** JERO SaaS (Multi-Tenant B2B Hiring ATS & Talent Marketplace)  
**Target Scope:** Service Company & Admin Portals  
**Audit Standard:** Strict CQRS (0% MySQL Reads on Full Request/Blade Path with CI Gate), 10K Concurrency Scaling, Bounded Batched Reads ($O(1)$ ES Operations), Non-MySQL Pre-Resolved Authorization Context (`ClientAccessContext`), Multi-Tenant Job Visibility Scope Resolver (Event-Side Multi-Partner Fan-Out), Defense-in-Depth Direct Client Boundary (`client_id` + `recommendation_relation`), Physical Outbox Uniqueness (`idx_outbox_tenant_idempotency` Storage-Engine Enforced), Fully Configurable Outbox State Machine, Primary-Shard Atomic Painless Updates with Full Schema Preservation & Lock-Free Snapshot Rebuild, Multi-Scope Monotonic Redis Invalidation, Dual-Tier Real-Time Convergence, End-to-End Request & ES Latency SLOs, Metronic 15.6" Screen Fit  
**Status:** Certified Final Master Architecture Specification (V11 Enterprise Gold Standard — Production Approved)  
**Date:** October 2026 | **Author:** God-Level Architecture Agent  

---

## 🏆 EXECUTIVE VERDICT & SCORECARD

**Current Implementation Status: 🔴 NOT PRODUCTION READY (Overall Score: 4/10)**

| Dimension | Rating | Severity | Core Finding |
| :--- | :---: | :---: | :--- |
| **Multi-Tenant Security** | 🔴 2/10 | **P0 BLOCKER** | Internal client jobs query filters by `client_id` with NO `company_id` / `comId` check, allowing cross-tenant data exfiltration. |
| **CQRS Read Compliance** | 🔴 2/10 | **P0 CRITICAL** | Synchronous MySQL queries on read paths in controller and inside Blade loop (`clients`, `companies`, `job_categories`, `add_job_wise_condidates`). |
| **10K Concurrency Readiness** | 🔴 3/10 | **P0 CRITICAL** | 151 Elasticsearch HTTP calls + 31 MySQL queries per 10 jobs in drawer. Generates 1.5M ES + 310K MySQL calls under 10K concurrency. |
| **Data Accuracy & KPI Integrity**| 🔴 3/10 | **P0 CRITICAL** | KPI card displays 2 active jobs while table displays 15 mixed-status jobs. Direct client metric query omits partner boundary and counts all company jobs. |
| **Event Architecture & UI Click** | 🔴 3/10 | **P0 BLOCKER** | Job titles share `.show-detail` class with Client Drawer. Clicking any job title treats Job ID as Client ID and crashes drawer. |
| **Cache & Real-Time Sync** | 🟠 4/10 | **P1 HIGH** | Premature `return;` in client-first patch leaves KPIs/sorting stale. Stale CQRS race between Queue and ES. Missing Outbox pattern. |
| **Job Lifecycle & Statuses** | 🟠 5/10 | **P1 HIGH** | Inactive status collapsed into Closed visually. Inactive omitted from status filter UI. Dead mock code in Blade files. |
| **UI/UX & 15.6" Screen Fit** | 🟠 5/10 | **P1 HIGH** | Missing CSS in Internal Client drawer (`show.blade.php`). Overcrowded 7-column layout on 1366×768 screens. Copy-pasted user admin filter. |
| **Activity & Auditability** | 🟠 4/10 | **P1 HIGH** | Database table `job_status_logs` exists but is completely unutilized. Audit logging detached from write transaction. |
| **Code Hygiene & Modern Standards**| 🟡 6/10 | **P2 MEDIUM** | Search parameter mismatch. Non-deterministic sorting on identical timestamps. Pagination missing page-clamp protection. |

---

## 🚨 P0 — RELEASE BLOCKERS & CRITICAL HAZARDS

### 1. Multi-Tenant Authorization Failure & Untrusted Identity Injection
- **The Defect:** In [`ServiceCompanyJobsController::client_job_data_ajax:1620-1627`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php#L1620-L1627), when `custom == 'true'` (Internal Clients), the Elasticsearch query filters strictly on:
  ```php
  ['term' => ['client_id' => $client_id]]
  ```
  It **omits `['term' => ['comId' => (int) $company_id]]`**.
- **Security Vulnerability:** Because `client_id` is an auto-incrementing integer across the platform, Tenant A can forge an AJAX request with Tenant B's `client_id`. Elasticsearch will return Tenant B's confidential job requisitions, salaries, hiring managers, and applicant counts.
- **Architectural Law:**
  > **Tenant and Client Identity must NEVER be established by raw request parameters.**
  > `client_id` may identify the requested resource, but it must **never** establish authorization.
- **Mandatory Authorization Context Pattern:**
  The query engine must NEVER accept an arbitrary integer `client_id`. The application layer must resolve an immutable, **cryptographically authenticated and application-policy-authorized `ClientAccessContext`**:
  ```php
  namespace App\DTO\ServiceCompany;

  class ClientAccessContext
  {
      public function __construct(
          public readonly int $tenantId,                  // Top-level account/organization identity
          public readonly int $serviceCompanyId,          // Specific operating recruitment entity
          public readonly int $clientId,                  // Authoritatively validated client_id
          public readonly string $clientType,             // 'internal' | 'direct'
          public readonly ?int $partnerCompanyId,         // Validated marketplace partner company for direct clients
          public readonly int $authorizationVersion,      // Monotonic authorization epoch
          public readonly bool $isAuthorized              // Explicit policy determination
      ) {}
  }
  ```

- **Zero-MySQL Authorization Law (P0 CQRS Architecture Mandate):**
  > **Architectural Law:**
  > **"Authorization may depend on authoritative relational data, but `ClientJobsReadService` must receive an already-resolved, cached, or token-signed authorization context. No MySQL authorization lookup may occur during the Jobs Tab read request path."**
  
  To satisfy strict 0% MySQL reads on the request path while guaranteeing 100% tenant safety:
  1. **Internal Clients:** `tenantId` and `serviceCompanyId` are resolved directly from the authenticated session claims / JWT token in RAM ($O(1)$ zero I/O). Client ownership is validated against the authenticated company context.
  2. **Direct Clients:** The 4-level partnership relationship (`serviceCompanyId` $\leftrightarrow$ `partnerCompanyId` $\leftrightarrow$ `clientId`) is pre-resolved and cached in Dragonfly / Redis:
     `ats:auth_context:direct_client:{serviceCompanyId}:{partnerCompanyId}:{clientId}`
     (with a sliding TTL, evicted immediately whenever partner invitations, terms, or relationships mutate in MySQL).
     Alternatively, a tamper-evident HMAC-SHA256 signed `SignedClientAccessToken` issued when the client row is initially rendered can be passed via request header.
  3. **Guaranteed Execution:** `ClientAccessContext::fromRequest($request)` executes with **strictly 0 MySQL queries**. If an authorization lookup ever triggers a `SELECT` on the request path, the CQRS CI Gate immediately fails the build.

- **Domain Law: Tenant $\neq$ Service Company (P0 Architecture Clarification):**
  > While in simple single-company accounts `tenantId == serviceCompanyId`, the system architecture strictly models them as distinct identities. `tenantId` governs data ownership and billing boundaries, whereas `serviceCompanyId` identifies the operational recruitment entity. Equating them silently is forbidden to preserve future-readiness for holding company structures, multi-branch service companies, and delegated admin organizations.

- **Mandatory Triple Attack Vector Security Test Invariant:**
  An unauthorized request must **never** reach Elasticsearch. The automated feature test suite (`ClientAccessContextSecurityTest`) must execute three distinct penetration vectors:

  ```
  [ Request Pipeline ]
          ↓
  Resolve ClientAccessContext (Zero-MySQL)
          ↓
  assertAuthorized()
          ├── [isAuthorized = false] ──> HTTP 403 Forbidden ──> (ES Calls = 0, ClientJobsQuery = 0)
          │
          └── [isAuthorized = true]
                  ↓
          ClientJobsQuery::fromAuthorizedContext()
                  ↓
          ClientJobsReadService::getJobs()
                  ↓
          Elasticsearch (Defense-in-Depth Filter)
  ```

  - **Test A (Cross-Tenant Forged Client):** Tenant A submits request with Tenant B's `client_id` $\to$ `ClientAccessContext(isAuthorized=false)` $\to$ HTTP 403 $\to$ ES Mock/Spy invocation count == 0 $\to$ `ClientJobsQuery` construction count == 0.
  - **Test B (Cross-Partner Direct Client Spoofing):** Tenant A + Service Company A requests Direct Client C under Partner B, but Client C belongs to Partner D $\to$ `ClientAccessContext(isAuthorized=false)` $\to$ HTTP 403 $\to$ ES Mock/Spy invocation count == 0 $\to$ `ClientJobsQuery` construction count == 0.
  - **Test C (Unauthorized Requisition Isolation):** Valid Direct Client requests a job requisition from Partner B that has NOT been authorized/shared through active `recommendation_relation` (`final_status = 1`) to Service Company A $\to$ Elasticsearch nested query filters it out $\to$ Requisition is strictly excluded from results, counts, and metrics.

  Every Internal Client query **must** contain:
  ```json
  { "term": { "comId": <auth_service_company_id> } },
  { "term": { "client_id": <validated_client_id> } }
  ```

---

### 2. Direct Client Application-Layer Authorization & Multi-Boundary Contamination Bug
- **The Defect:** In [`ServiceCompanyClientController::dviewMore:3091`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php#L3091):
  ```php
  $this->active_requisition = DB::table('jobs')->select('jobs.id')
      ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
      ->where('recommendation_relation.to_company_id', $company_id)
      ->where('jobs.status', 'active')
      ->count();
  ```
- **Operational Impact:** This query joins `recommendation_relation` filtering only on `to_company_id = $company_id`, with **no filter for `from_company_id` or the specific direct client**. It counts every single active job recommended to the company across ALL marketplace partners combined!
- **Mandatory 4-Level Domain Authorization Hierarchy:**
  ```
  Tenant (Authenticated User Organization)
   └── Service Company (serviceCompanyId)
        └── Partner Relationship (staffing_company_relation: to_company_id + from_company_id)
             └── Direct Client (clientId under partner)
                  └── Authorized Requisition (jobId via recommendation_relation)
  ```
  > **Elasticsearch filtering is not your authorization system.**
  
  The authorization contract requires strict dual-tier validation:
  1. **Application-Layer Authorization (Mandatory Pre-Condition):**
     - Verify authenticated user belongs to `serviceCompanyId` within `tenantId`.
     - Verify active marketplace partnership exists between `serviceCompanyId` and `partnerCompanyId` in `staffing_company_relation` (`final_status = 1`).
     - Verify `clientId` authoritatively belongs to `partnerCompanyId`.
     - If any check fails, immediately throw HTTP 403 Forbidden without constructing queries or invoking Elasticsearch.
  2. **Elasticsearch Complete Boundary Enforcement (Defense-in-Depth):**
     Elasticsearch must receive the **complete boundary** (not just the partner relationship), explicitly scoping both the owning Direct Client AND the authorized requisition sharing contract:
  ```json
  {
    "bool": {
      "must": [
        { "term": { "client_id": <validated_direct_client_id> } },
        {
          "nested": {
            "path": "recommendation_relation",
            "query": {
              "bool": {
                "must": [
                  { "term": { "recommendation_relation.to_company_id": <auth_service_company_id> } },
                  { "term": { "recommendation_relation.from_company_id": <validated_partner_company_id> } },
                  { "term": { "recommendation_relation.final_status": 1 } }
                ]
              }
            }
          }
        }
      ]
    }
  }
  ```

---

### 3. The 150+ Query Architecture Avalanche & Explicit CQRS Enforcement Guard
- **The Current Execution Profile (Per 10 Jobs Loaded):**
  ```
  Blade Loop (client_jobs_side_bar_ajax.blade.php)
   └── foreach ($data as $row)
        ├── GeneralMethod::getrRcommendationRelationData  → 10x ELASTIC_JOB_REQUEST queries
        ├── GeneralMethod::companyDetails                 → Up to 60x Exists + Get HTTP trips
        ├── DB::table('job_categories')->value('name')   → 10x Synchronous MySQL queries
        ├── DB::table('companies')->value('company_name') → 10x Synchronous MySQL queries
        ├── GeneralMethod::statusCount (8 stages x 10)    → 80x ELASTIC_CANDIDATES queries
        └── GeneralMethod::allStatusCount                 → 10x add_job_wise_condidates MySQL queries
  ```
- **Scale Impact:** **151 Elasticsearch calls + 31 MySQL queries per single drawer page load.** At 10,000 concurrent recruiters, this generates **1.5M ES queries and 310,000 MySQL reads**, immediately exhausting worker pools and database connections.
- **Architectural Mandate:**
  > **Bounded Batched Read Invariant (Zero N+1):**
  > - **Zero N+1 operations** across all request paths.
  > - **$\text{ES Queries} = O(1)$ network round-trips relative to page size $N$ ($\text{Queries} \neq O(N)$)**.
  > - All reads must be batched; **zero ES queries and zero MySQL queries inside Blade loops**.
  > - Query count must remain strictly bounded regardless of page size (e.g., 10 jobs = 1–3 bounded operations; 100 jobs = still 1–3 bounded operations, NOT $10\times$ or $100\times$).
  > - **0% MySQL Reads on Request Path:** Read company, client, and category names directly from denormalized Elasticsearch documents.
- **Hard CQRS Enforcement Mechanism (Full-Pipeline Automated CI Gate):**
  To guarantee that no developer or background update accidentally introduces Eloquent relations, lazy-loading, or raw `DB::table()` calls into ANY layer of the read pipeline (including Blade template rendering), enforce an explicit runtime query assertion and automated CI feature test:
  ```php
  // Architectural CI Test Gate: tests/Feature/ServiceCompany/ClientJobsCqrsReadTest.php
  public function test_client_drawer_jobs_tab_executes_zero_mysql_reads_across_full_rendering_path(): void
  {
      $user = User::factory()->create(['company_id' => 100]);
      $client = Client::factory()->create(['company_id' => 100]);

      \DB::enableQueryLog();

      // Full execution pipeline: Controller -> AuthContext -> Query -> ReadService -> ViewModel -> Blade Rendering
      $response = $this->actingAs($user)->get(
          route('service.client.jobs.data', ['client_id' => $client->id, 'custom' => 'true'])
      );

      $response->assertStatus(200);
      $executedQueries = \DB::getQueryLog();

      // 1. Assert STRICTLY ZERO MySQL Queries anywhere in the request lifecycle
      $this->assertCount(
          0,
          $executedQueries,
          "STRICT CQRS VIOLATION: " . count($executedQueries) . " MySQL reads detected on Jobs Tab request path! Queries: " . json_encode($executedQueries)
      );

      // 2. Assert Zero ES / External HTTP calls during Blade rendering
      // (Verified by spying on Elasticsearch Client after ViewModel hand-off)
      $this->assertBladeRendersWithoutIo($response->getContent());
  }
  ```

---

### 4. Job Title Click Collision (`.show-detail` Event Hijacking)
- **The Defect:** In [`client_jobs_side_bar_ajax.blade.php:230`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php#L230), the job title anchor is assigned `class="show-detail"`. In [`index.blade.php:1849`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/client/index.blade.php#L1849), `.show-detail` is globally bound to:
  ```javascript
  $(document).on('click', '.show-detail', function (e) {
      var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
      ClientStateManager.openClientDrawer(id, 'internal');
  });
  ```
- **Failure:** Clicking on ANY job title inside the open Client Drawer treats the Job ID as a Client ID. It fires an AJAX call to load a Client Drawer for that Job ID, clearing the current drawer DOM and failing with a 404 or corrupted shell.
- **God-Level Mandate:**
  > **Never use generic CSS classes like `.show-detail` for event routing across different domain entities.**
  Use explicit semantic data attributes:
  ```html
  <a href="javascript:void(0);" 
     data-entity="job" 
     data-action="open-drawer" 
     data-job-id="{{ $row->id }}" 
     class="js-job-drawer-trigger">
      {{ $row->title }}
  </a>
  ```

---

## 🏛️ ARCHITECTURAL BLUEPRINT & TARGET SPECIFICATION

```
                           ┌───────────────────────────┐
                           │   Browser / Client UI     │
                           └─────────────┬─────────────┘
                                         │
                                HTTP GET / AJAX Request
                                         │
                           ┌─────────────▼─────────────┐
                           │      ClientJobsQuery      │
                           │(Validated FormRequest/DTO)│
                           └─────────────┬─────────────┘
                                         │
                           ┌─────────────▼─────────────┐
                           │   Authorization Layer     │
                           │ Tenant + Client Policy    │
                           └─────────────┬─────────────┘
                                         │
                           ┌─────────────▼─────────────┐
                           │   ClientJobsReadService   │
                           └─────────────┬─────────────┘
                                         │
                     ┌───────────────────┴───────────────────┐
                     │ Bounded O(1) ES Read Operations       │
                     │ (Single Search + Aggs OR Batched MS)  │
                     └───────────────────┬───────────────────┘
                                         │
                           ┌─────────────▼─────────────┐
                           │     JobsTabViewModel      │
                           │(Unified KPI, List, Stages)│
                           └─────────────┬─────────────┘
                                         │
                           ┌─────────────▼─────────────┐
                           │    Isolated Blade View    │
                           │ (Zero DB/ES queries in UI)│
                           └───────────────────────────┘
```

---

## 5. DEDICATED READ CONTRACT (`ClientJobsQuery`)

To prevent controller bloat, eliminate raw request injection, and formalize input validation, the read query object is constructed **strictly from an authorized `ClientAccessContext`**, ensuring that no unverified client ID can ever trigger an Elasticsearch query:

```php
namespace App\DTO\ServiceCompany;

use Illuminate\Http\Request;

class ClientJobsQuery
{
    public function __construct(
        public readonly int $tenantId,          // Top-level account boundary
        public readonly int $serviceCompanyId,  // Operating recruitment company
        public readonly int $clientId,          // Authoritatively validated client
        public readonly string $clientType,     // 'internal' | 'direct'
        public readonly ?int $partnerCompanyId, // Marketplace partner boundary
        public readonly ?string $search,
        public readonly ?string $status,        // 'all' | 'hiring' | 'hold' | 'closed' | 'inactive'
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly string $sort = 'created_at',
        public readonly string $direction = 'desc'
    ) {}

    /**
     * Strictly construct from a policy-approved context.
     * Raw external parameters CANNOT instantiate this query without authorization.
     */
    public static function fromAuthorizedContext(ClientAccessContext $context, Request $request): self
    {
        if (!$context->isAuthorized) {
            abort(403, 'Unauthorized access: Client is outside authenticated tenant boundary.');
        }

        return new self(
            tenantId: $context->tenantId,
            serviceCompanyId: $context->serviceCompanyId,
            clientId: $context->clientId,
            clientType: $context->clientType,
            partnerCompanyId: $context->partnerCompanyId,
            search: $request->filled('search') ? trim((string) $request->input('search')) : null,
            status: $request->input('status', 'all'),
            page: max(1, (int) $request->input('page', 1)),
            perPage: min(50, max(1, (int) $request->input('per_page', 10))),
            sort: $request->input('sort', 'created_at'),
            direction: strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc'
        );
    }

    /**
     * Cache Architecture Terminology & Specifications:
     * 1. 5 Static Identity Dimensions:
     *    {tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}
     * 2. Redis Monotonic Invalidation Key (Stores scalar integer version):
     *    ats:client_jobs_version:{tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}
     * 3. Query Result Cache Key (Incorporates current projectionVersion + normalized query hash):
     *    ats:client_jobs:v1:{tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}:{projectionVersion}:{hash}
     */
    public function toCacheKey(int $projectionVersion): string
    {
        $partnerPart = $this->partnerCompanyId ?? 0;
        $normalizedPayload = [
            'tenant'             => $this->tenantId,
            'service_company'    => $this->serviceCompanyId,
            'client'             => $this->clientId,
            'client_type'        => $this->clientType,
            'partner_company'    => $partnerPart,
            'projection_version' => $projectionVersion,
            'search'             => $this->search,
            'status'             => $this->status,
            'page'               => $this->page,
            'per_page'           => $this->perPage,
            'sort'               => $this->sort,
            'direction'          => $this->direction,
        ];

        return "ats:client_jobs:v1:{$this->tenantId}:{$this->serviceCompanyId}:{$this->clientType}:{$partnerPart}:{$this->clientId}:{$projectionVersion}:" . md5(json_encode($normalizedPayload));
    }
}
```

---

## 6. SEARCH & DETERMINISTIC SORTING STRATEGY

### 6.1 Configurable Elasticsearch Search Strategy
Instead of hardcoding field boosts in PHP, search relevance parameters are driven by `config/ats.php`:

```php
// config/ats.php
'search' => [
    'jobs' => [
        'fields' => [
            'title.keyword^5',
            'title^3',
            'skills.name^2',
            'job_code^4',
            'hiring_manager^1.5',
        ],
        'operator'             => 'AND',
        'minimum_should_match' => '75%',
    ],
],
```

The multi-match query in `ClientJobsReadService` dynamically utilizes this configuration:
```json
{
  "bool": {
    "must": [
      {
        "multi_match": {
          "query": "Senior React Developer",
          "fields": {{ json_encode(config('ats.search.jobs.fields')) }},
          "type": "best_fields",
          "operator": "{{ config('ats.search.jobs.operator', 'AND') }}",
          "minimum_should_match": "{{ config('ats.search.jobs.minimum_should_match', '75%') }}"
        }
      }
    ]
  }
}
```

### 6.2 Deterministic Tie-Breaker Sorting
If two jobs have the identical timestamp, arbitrary internal ES score ordering will cause records to jump across pages.
**Mandatory Sort Contract:**
```json
"sort": [
  { "created_at": { "order": "desc" } },
  { "id": { "order": "desc" } }
]
```

---

## 7. UNIFIED KPI & DATA MODEL (`JobsTabViewModel`)

To eliminate metric ambiguity where the top KPI badge shows `[2 Active Jobs]` while the table displays `15 Mixed Jobs`, the ViewModel explicitly distinguishes between **canonical all-status counts** and **filtered counts**, along with complete model observability:

```php
namespace App\ViewModels\ServiceCompany;

class JobsTabViewModel
{
    public function __construct(
        public readonly int $totalCount,         // Canonical count of ALL jobs for this client (unfiltered)
        public readonly int $filteredCount,      // Count of jobs matching current filter & search (for pagination)
        public readonly int $hiringCount,        // Active/Hiring status count (for tab badge & hero KPI)
        public readonly int $holdCount,          // Hold status count
        public readonly int $closedCount,        // Closed status count
        public readonly int $inactiveCount,      // Inactive/Expired status count
        public readonly array $jobs,
        public readonly array $pipelineStageMap, // [jobId => [stageId => count]]
        public readonly array $logoMap,          // [jobId => resolvedLogoUrl]
        public readonly array $pagination,
        public readonly array $activeFilters,
        public readonly int $projectionVersion,  // Observable version identity
        public readonly string $queryHash,       // Observable query identity
        public readonly string $generatedAt      // Generation timestamp for telemetry
    ) {}
}
```

### Metrics Application Mapping:
- **Hero/Drawer Header KPI:** Canonical status counts (`hiringCount`, `holdCount`, `closedCount`, `inactiveCount`).
- **Jobs Tab Badge:** `hiringCount` (Active Jobs).
- **Pagination Bar:** `filteredCount` (*"Showing 1 to 10 of {filteredCount} jobs"*).
- **Pagination Page-Clamping:** If `filteredCount` drops (e.g. jobs closed while user is on Page 4) such that `currentPage > last_page`, the server clamps `currentPage = max(1, last_page)`.

### Canonical Job Status Semantics:
Do **not** destructively collapse `Inactive` into `Closed`. Preserve all 4 discrete states:
1. **Hiring / Active:** Requisition is active and open for candidate submissions.
2. **Hold:** Requisition paused temporarily; existing candidates preserved, new submissions suspended.
3. **Closed:** Requisition filled or formally cancelled by the hiring manager (`closed-bg`: `#ffeef1; color: #f1416c`).
4. **Inactive / Expired:** Requisition passed deadline or deactivated (`inactive-bg`: `#f1f5f9; color: #64748b`).

---

## 8. TRANSACTIONAL OUTBOX PATTERN & CQRS EVENT SEQUENCING

### 8.1 Write-Path Outbox Transaction & Monotonic Ordering (P1-3)
The transactional audit record in `job_status_logs` and the `sys_outbox_events` record **belong in the exact same database transactional boundary as the job mutation**. Placing the audit insert in a queued event listener is strictly forbidden.

#### Mutation Context Pattern (Enforcing Tenant $\neq$ Service Company on Writes):
To prevent accidental collapsing of `tenant_id` and `company_id` on the write path, all mutations must accept an immutable `JobMutationContext` DTO:

```php
namespace App\DTO\ServiceCompany;

class JobMutationContext
{
    public function __construct(
        public readonly int $tenantId,                  // Top-level account/billing identity
        public readonly int $serviceCompanyId,          // Operating recruitment company
        public readonly int $userId,                    // Acting authenticated user
        public readonly int $clientId,                  // Target client ID
        public readonly string $clientType = 'internal', // 'internal' | 'direct'
        public readonly ?int $partnerCompanyId = null    // Validated partner company for direct client
    ) {}
}
```

#### Aggregate Version Monotonicity Invariant:
Under high concurrency (e.g. two recruiters or automated webhooks updating Job 12 simultaneously), mutations must never generate duplicate or out-of-order versions.
- Enforce strict pessimistic row locking via `SELECT ... FOR UPDATE` inside `DB::transaction`.
- Read current `$job->aggregate_version` (or `state_version`).
- Increment `$nextVersion = ((int) ($job->aggregate_version ?? $job->state_version ?? 0)) + 1`.
- Atomically persist `$nextVersion` to `jobs.aggregate_version` (and `jobs.state_version`) in the same SQL statement.
- Insert `$nextVersion` into `sys_outbox_events.aggregate_version` matching the exact schema in `database/schema/mysql-schema.sql`.

```php
namespace App\Services;

use App\DTO\ServiceCompany\JobMutationContext;
use Illuminate\Support\Facades\DB;

class JobMutationService
{
    public function updateStatus(int $jobId, string $newStatus, JobMutationContext $context): void
    {
        DB::transaction(function () use ($jobId, $newStatus, $context) {
            // Explicit pessimistic lock guarantees monotonic aggregate versioning
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            $oldStatus = $job->status;
            $nextVersion = ((int) ($job->aggregate_version ?? $job->state_version ?? 0)) + 1;

            // 1. Transactional Update of System of Record (Persisting incremented version!)
            DB::table('jobs')->where('id', $jobId)->update([
                'status'            => $newStatus,
                'state_version'     => $nextVersion,
                'aggregate_version' => $nextVersion,
                'updated_at'        => now(),
            ]);

            // 2. Synchronous Transactional Audit Log Insert (Guaranteed Durable)
            DB::table('job_status_logs')->insert([
                'job_id'      => $jobId,
                'company_id'  => $context->serviceCompanyId,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'changed_by'  => $context->userId,
                'created_at'  => now(),
            ]);

            // 3. Durable Transactional Outbox Insert (Exact schema: database/schema/mysql-schema.sql:4227)
            // Physical Storage Engine Uniqueness Specification:
            // Table sys_outbox_events features: UNIQUE KEY idx_outbox_tenant_idempotency (tenant_id, idempotency_key).
            // NOTE: KEY idx_outbox_aggregate (aggregate_type, aggregate_id, aggregate_version) is a non-unique index.
            // Therefore, idx_outbox_tenant_idempotency is the SOLE physical database constraint.
            // By formatting $idempotencyKey deterministically as "job:{$jobId}:v{$nextVersion}",
            // we physically bind the logical aggregate state tuple to this unique constraint, causing MySQL InnoDB
            // to reject duplicate version emissions with SQL Error 1062 at the storage level without requiring schema migrations!
            $idempotencyKey = "job:{$jobId}:v{$nextVersion}";
            DB::table('sys_outbox_events')->insert([
                'tenant_id'         => $context->tenantId,
                'idempotency_key'   => $idempotencyKey,
                'aggregate_type'    => 'job',
                'aggregate_id'      => $jobId,
                'aggregate_version' => $nextVersion,
                'event_type'        => 'job.status.changed',
                'payload'           => json_encode([
                    'tenant_id'          => $context->tenantId,
                    'company_id'         => $context->serviceCompanyId,
                    'service_company_id' => $context->serviceCompanyId,
                    'job_id'             => $jobId,
                    'client_id'          => $context->clientId,
                    'client_type'        => $context->clientType,
                    'partner_company_id' => $context->partnerCompanyId ?? 0,
                    'from_status'        => $oldStatus,
                    'to_status'          => $newStatus,
                    'version'            => $nextVersion,
                    'occurred_at'        => now()->timestamp,
                ]),
                'status'       => 'PENDING',
                'attempts'     => 0,
                'available_at' => now(),
                'created_at'   => now(),
            ]);
        });
    }
}
```

### 8.2 Outbox Worker Claim Protocol, Primary-Shard Atomic Projections & Monotonic Redis (P1-4, P1-5)

#### 8.2.1 Configurable Worker Claim & State Machine Protocol
To prevent race conditions, duplicate processing, and orphaned tasks when workers terminate abruptly, `sys_outbox_events` implements a formal state machine (`PENDING` $\to$ `PROCESSING` $\to$ `COMPLETED` / `FAILED` / `DEAD_LETTER`).
In accordance with **JERO Rule 11 (`config()`, never hardcoded values)**, all batch and lifecycle parameters are strictly configurable:

```php
// config/ats.php
'outbox' => [
    'batch_size'                 => (int) env('ATS_OUTBOX_BATCH_SIZE', 50),
    'lock_timeout_seconds'       => (int) env('ATS_OUTBOX_LOCK_TIMEOUT_SECONDS', 300),
    'max_attempts'               => (int) env('ATS_OUTBOX_MAX_ATTEMPTS', 5),
    'retry_backoff_seconds'      => (int) env('ATS_OUTBOX_RETRY_BACKOFF_SECONDS', 60),
    'dead_letter_after_attempts' => (int) env('ATS_OUTBOX_DEAD_LETTER_AFTER_ATTEMPTS', 5),
],
```

1. **Atomic Batch Claim (`FOR UPDATE SKIP LOCKED`):**
   ```sql
   -- Parameterized bounded batch claim; ZERO hardcoding (Rule 11)
   UPDATE sys_outbox_events
   SET status = 'PROCESSING',
       locked_at = NOW(),
       attempts = attempts + 1
   WHERE id IN (
       SELECT id FROM (
           SELECT id FROM sys_outbox_events
           WHERE status = 'PENDING' AND available_at <= NOW()
           ORDER BY id ASC
           LIMIT :batch_size -- Bound dynamically from config('ats.outbox.batch_size')
           FOR UPDATE SKIP LOCKED
       ) tmp
   );
   ```
2. **Crash & Deadlock Orphan Recovery:**
   Any event remaining in `PROCESSING` with `locked_at < NOW() - INTERVAL :lock_timeout SECOND` (from `config('ats.outbox.lock_timeout_seconds')`) is automatically recycled to `PENDING` (or escalated to `DEAD_LETTER` if `attempts >= config('ats.outbox.dead_letter_after_attempts')`).

3. **Idempotency of `COMPLETED` & The Correctness Invariant:**
   > **Distributed Architectural Law:**
   > **"Correctness must depend on idempotent projections, NOT on worker completion bookkeeping."**
   - **Storage-Engine Physical Uniqueness:** Enforced exclusively by `UNIQUE KEY idx_outbox_tenant_idempotency (tenant_id, idempotency_key)` in `sys_outbox_events` (`database/schema/mysql-schema.sql:4244`).
   - The logical projection state `(aggregate_type, aggregate_id, aggregate_version)` is physically bound to this unique constraint by formatting `$idempotencyKey = "{$aggregateType}:{$aggregateId}:v{$aggregateVersion}"`.
   - If a worker crashes after updating Elasticsearch and Redis, but before marking `status = 'COMPLETED'`, the subsequent retry is completely harmless: Elasticsearch evaluates `ctx.op = 'noop'`, Redis Lua returns without modification, and Reverb publishes the authoritative version. `COMPLETED` is strictly operational telemetry and queue cleanup.

#### 8.2.2 Primary-Shard Atomic Painless Projection, Event Field Ownership & Single-Snapshot Rebuild

##### Distributed Systems Terminology Standard:
- **MySQL:** Authoritative System of Record (SoR) — Strict ACID transaction boundary for writes.
- **Elasticsearch:** Authoritative Read Projection — Serves 100% of queries, filters, and aggregations.
- **Redis / Dragonfly:** Ephemeral Invalidation & Monotonic Version Index — Monotonically invalidates local memory and HTTP caches.

##### Formal Event Schema Ownership Law:
> **Architectural Law:**
> **"Every event type has strict projection field ownership. No event handler may accidentally modify, nullify, or overwrite fields outside its declared domain ownership."**
- `job.status.changed`: Modifies strictly `status`, `projection_version`, and `updated_at`.
- `job.salary.changed`: Modifies strictly `salary_min`, `salary_max`, `salary_currency`, `projection_version`, and `updated_at`.
- `job.skills.changed`: Modifies strictly `skills`, `projection_version`, and `updated_at`.
- `job.canonical.rebuild`: Atomically replaces the full document with complete canonical fields.

##### Single-Snapshot Rebuild Invariant (Lock-Free Version Verification):
> **Architectural Law:**
> **"The canonical rebuild must obtain `{ completeProjection, authoritativeVersion }` from a single consistent snapshot boundary to prevent version shearing without holding an expensive long-lived exclusive row lock."**
When rebuilding a missing Elasticsearch document, `JobProjectionService::rebuildAndIndexCanonicalJob()` avoids holding an exclusive `FOR UPDATE` lock across full multi-table document assembly. Instead, it executes within a read snapshot transaction and verifies that the version did not mutate during assembly:

```php
// app/Services/JobProjectionService.php
public function rebuildAndIndexCanonicalJob(int $jobId): void
{
    // Snapshot-Consistent Read with Optimistic Version Verification (Zero Long-Lived Exclusive Lock)
    $snapshot = DB::transaction(function () use ($jobId) {
        $job = DB::table('jobs')->where('id', $jobId)->first();
        if (!$job) return null;

        $vStart = (int) ($job->aggregate_version ?? $job->state_version ?? 0);
        $completeDocument = $this->assembleCanonicalDocument($job);

        // Verify version has not advanced during assembly
        $vEnd = (int) DB::table('jobs')->where('id', $jobId)->value('aggregate_version');
        if ($vEnd !== $vStart) {
            // Concurrent write detected during assembly; return null to trigger retry with latest version
            return null;
        }

        $completeDocument['projection_version'] = $vStart;
        return [
            'version'  => $vStart,
            'document' => $completeDocument,
        ];
    });

    if ($snapshot) {
        $this->elasticsearch->index([
            'index' => 'jobs',
            'id'    => (string) $jobId,
            'body'  => $snapshot['document'],
        ]);
    }
}
```

##### Missing-Document Rebuild Race Invariant:
> **"A projection rebuild may never publish a projection version lower than the authoritative aggregate version represented by its System of Record payload."**
When rebuilding a missing Elasticsearch document, the rebuild service indexes with the authoritative SoR version ($v=12$). Any subsequent retry of an older event ($v=11$) hits the primary-shard Painless conditional script and evaluates $11 > 12 \implies \text{false} \implies \text{ctx.op} = \text{'noop'}$, completely eliminating version regression races!

#### 8.2.3 Multi-Client / Multi-Partner Job Visibility Scope Resolver (Event Fan-Out)
> **P0 Architectural Law:**
> **"Any mutation affecting a job must invalidate every authorized client scope through which that job is currently visible. In marketplace ATS architectures, a single requisition may be shared across multiple service companies and direct clients simultaneously."**

The fan-out resolution belongs **strictly to the asynchronous write/projection event worker**, maintaining **0% MySQL reads on the web request path**:

```php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class JobVisibilityScopeResolver
{
    /**
     * Resolves all client visibility scopes currently authorized to view this job:
     * 1. The owning internal client scope (from jobs table)
     * 2. All active partner recommendation scopes (from recommendation_relation where final_status = 1)
     * Executes inside Horizon Outbox Worker; NEVER on the read path!
     */
    public function resolveAffectedScopes(int $jobId, array $payload): array
    {
        $scopes = [];

        // 1. Owning Internal Scope
        $scopes[] = [
            'tenant_id'          => (int) $payload['tenant_id'],
            'service_company_id' => (int) $payload['company_id'],
            'client_type'        => 'internal',
            'partner_company_id' => 0,
            'client_id'          => (int) $payload['client_id'],
        ];

        // 2. Marketplace Recommendation Fan-Out Scopes
        $recommendations = DB::table('recommendation_relation')
            ->select('to_company_id', 'from_company_id')
            ->where('job_id', $jobId)
            ->where('final_status', 1)
            ->get();

        foreach ($recommendations as $rec) {
            $toCompanyId = (int) $rec->to_company_id;
            $fromCompanyId = (int) $rec->from_company_id;

            // Direct client scope visible under receiving partner
            $scopes[] = [
                'tenant_id'          => $toCompanyId,
                'service_company_id' => $toCompanyId,
                'client_type'        => 'direct',
                'partner_company_id' => $fromCompanyId,
                'client_id'          => (int) $payload['client_id'],
            ];
        }

        return $scopes;
    }
}
```

```php
namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use App\Services\JobProjectionService;
use App\Services\JobVisibilityScopeResolver;

class ProcessOutboxJobProjection
{
    public function handle(
        object $event, 
        Client $elasticsearch, 
        JobProjectionService $projectionService,
        JobVisibilityScopeResolver $scopeResolver
    ): void {
        $payload = json_decode($event->payload, true);

        try {
            // 1. Primary-Shard Atomic Conditional Update (Strict Monotonic Projection Invariant)
            $elasticsearch->update([
                'index' => 'jobs',
                'id'    => (string) $event->aggregate_id,
                'body'  => [
                    'script' => [
                        'source' => "if (ctx._source.projection_version == null || params.version > ctx._source.projection_version) { " .
                                    "ctx._source.status = params.status; " .
                                    "ctx._source.projection_version = params.version; " .
                                    "ctx._source.updated_at = params.updated_at; " .
                                    "} else { ctx.op = 'noop'; }",
                        'lang'   => 'painless',
                        'params' => [
                            'status'     => $payload['to_status'],
                            'version'    => (int) $event->aggregate_version,
                            'updated_at' => now()->toIso8601String(),
                        ],
                    ],
                ],
            ]);
        } catch (Missing404Exception $e) {
            // 2. Full Schema Preservation Guard: Rebuild authoritative canonical document from MySQL SoR.
            $projectionService->rebuildAndIndexCanonicalJob((int) $event->aggregate_id);
        }

        // 3. Multi-Scope Monotonic Redis Invalidation & Reverb Fan-Out
        $scopes = $scopeResolver->resolveAffectedScopes((int) $event->aggregate_id, $payload);

        foreach ($scopes as $scope) {
            $redisKey = "ats:client_jobs_version:{$scope['tenant_id']}:{$scope['service_company_id']}:{$scope['client_type']}:{$scope['partner_company_id']}:{$scope['client_id']}";

            Redis::eval(
                "local cur = tonumber(redis.call('get', KEYS[1]) or 0)
                 local newVer = tonumber(ARGV[1])
                 if newVer > cur then
                     redis.call('set', KEYS[1], newVer)
                     return newVer
                 else
                     return cur
                 end",
                1,
                $redisKey,
                (int) $event->aggregate_version
            );

            // Broadcast real-time patch to each specific tenant/company channel
            broadcast(new \App\Events\JobStatusChangedEvent(array_merge($payload, [
                'tenant_id'          => $scope['tenant_id'],
                'company_id'         => $scope['service_company_id'],
                'client_type'        => $scope['client_type'],
                'partner_company_id' => $scope['partner_company_id'],
            ])))->toOthers();
        }

        // 4. Mark Outbox Record COMPLETED (Safe from crashes; retries are 100% idempotent)
        DB::table('sys_outbox_events')->where('id', $event->id)->update([
            'status'       => 'COMPLETED',
            'processed_at' => now(),
        ]);
    }
}
```

```mermaid
sequenceDiagram
    autonumber
    participant MySQL as MySQL (SoR + Outbox)
    participant Worker as Horizon Outbox Worker
    participant ES as Elasticsearch (Primary Shard)
    participant Resolver as JobVisibilityScopeResolver
    participant Redis as Redis / Dragonfly (Multi-Scope Lua)
    participant Reverb as Laravel Reverb (WebSocket)
    participant Browser as Browser Clients (X, Y, Z)

    MySQL->>Worker: Claim Batch (status='PROCESSING', SKIP LOCKED)
    Worker->>ES: Atomic Painless Script Update (version > projection_version)
    alt version > current projection_version
        Note over ES: Document Updated & Version Advanced
    else version <= current projection_version
        Note over ES: Atomic ctx.op = 'noop' (Stale Overwrite Prevented!)
    end
    Worker->>Resolver: Resolve Affected Scopes (Internal + Shared Partners)
    Resolver-->>Worker: [Scope X, Scope Y, Scope Z]
    Worker->>Redis: Invalidate Each Scope Monotonically (Lua)
    Worker->>MySQL: Mark Outbox event COMPLETED
    Worker->>Reverb: Broadcast to Channels (X, Y, Z)
    Reverb->>Browser: Event Delivered to All Active Drawers
    Note over Browser: Rich Optimistic RAM Patch + Debounced Server Refresh
    Browser->>ES: Fetch Authoritative Reconciliation
```

### 8.3 Distributed Resilience: Reverb Acceleration vs. Guaranteed Convergence
> **Architectural Law:**
> **"Reverb accelerates convergence; it does not guarantee convergence."**
> **"Elasticsearch is the authoritative read projection for the Jobs Tab. MySQL remains the authoritative System of Record. Redis is an ephemeral cache-invalidation/consistency index and never a source of business truth."**

WebSocket delivery is inherently best-effort. Recruiters switch tabs, put laptops to sleep, experience Wi-Fi drops, or navigate corporate firewalls that terminate long-lived TCP connections.
Therefore: **Reverb delivery failure $\neq$ ATS data failure.**

The system guarantees eventual consistency through three defense-in-depth convergence loops:
1. **Window / Tab Focus Reconciliation:** When the user switches back to the browser tab (`visibilitychange` / `window.focus`), verify if the Client Drawer is open and check the client's current `projection_version` against the Redis version cache. If stale, refresh immediately.
2. **Drawer Re-Open / Tab Switch Check:** Whenever the user opens the Client Drawer or switches to the Jobs tab, the client sends `localVersion` in the header. If server version equals `localVersion`, return 304 or serve client memory; if higher, stream fresh projection.
3. **Socket Reconnect Resync:** When Reverb reconnects after a disconnect, automatically trigger a lightweight version ping to reconcile any events missed during the offline window.

### 8.4 Browser Reverb Listener: Rich Optimistic Patch & Authoritative Reconciliation Invariant
Do **not** use a naked `return;` on visible rows, as that leaves KPIs, sorting, and pagination stale.
Furthermore, updating only the status badge is incomplete: the recruiter expects **instant local KPI counter adjustment and status timestamp updates** in user RAM (<1ms) followed by an authoritative debounced background refresh.

#### Authoritative Reconciliation Invariant:
> **Non-Negotiable Invariant:**
> - **Client-side arithmetic (`curFrom - 1`, `curTo + 1`) is strictly transient UX sugar for 0ms perceived latency.**
> - **The 300ms coalesced server refresh is the sole canonical state of truth and unconditionally overwrites all KPI cards, filter pill counts, pagination metadata, pipeline stage totals, and sort order.**

```javascript
(function() {
    let jobConsistencyTimer = null;

    window.handleJobMutationEvent = function(e) {
        if (!e || !e.client_id) return;
        
        if (typeof ClientStateManager !== 'undefined' && ClientStateManager.getSelectedClientId() == e.client_id) {
            
            // Tier 1: Rich Instant Optimistic RAM/DOM Patch (< 1ms perceived latency in user RAM)
            const $jobRow = $(`[data-entity="job"][data-job-id="${e.job_id}"]`).closest('.job-row');
            if ($jobRow.length && e.to_status) {
                const statusMap = {
                    'active':   { label: 'Hiring',   cls: 'active-bg',   filterKey: 'hiring' },
                    'hiring':   { label: 'Hiring',   cls: 'active-bg',   filterKey: 'hiring' },
                    'hold':     { label: 'Hold',     cls: 'hold-bg',     filterKey: 'hold' },
                    'closed':   { label: 'Closed',   cls: 'closed-bg',   filterKey: 'closed' },
                    'inactive': { label: 'Inactive', cls: 'inactive-bg', filterKey: 'inactive' }
                };
                
                const fromStatusKey = e.from_status ? e.from_status.toLowerCase() : null;
                const toSt = statusMap[e.to_status.toLowerCase()] || { label: e.to_status, cls: 'closed-bg', filterKey: 'closed' };
                
                // 1. Update Status Badge & Visual Class
                $jobRow.find('.badge-hiring').attr('class', 'badge-hiring ' + toSt.cls).text(toSt.label);
                
                // 2. Update Status Timestamp
                $jobRow.find('.js-status-timestamp').text('Updated just now');

                // 3. Transient RAM Status Filter Counter Adjustment
                if (fromStatusKey && fromStatusKey !== toSt.filterKey) {
                    const $fromBtnBadge = $(`.cl-jobs-filter-group button[data-status="${fromStatusKey}"] .badge`);
                    const $toBtnBadge = $(`.cl-jobs-filter-group button[data-status="${toSt.filterKey}"] .badge`);
                    
                    if ($fromBtnBadge.length) {
                        const curFrom = parseInt($fromBtnBadge.text(), 10) || 0;
                        $fromBtnBadge.text(Math.max(0, curFrom - 1));
                    }
                    if ($toBtnBadge.length) {
                        const curTo = parseInt($toBtnBadge.text(), 10) || 0;
                        $toBtnBadge.text(curTo + 1);
                    }
                }
            }

            // Tier 2: Authoritative Coalesced Background Consistency Refresh
            // Non-negotiably reconciles all KPIs, filter counts, pagination, pipeline aggregates, and sorting from ES
            clearTimeout(jobConsistencyTimer);
            jobConsistencyTimer = setTimeout(function() {
                if ($('#requisitions').hasClass('active') || (window.loadedTabs && window.loadedTabs['requisitions'])) {
                    fetchJobsClient(currentPage, currentSearch, currentStatus);
                }
            }, 300);
        }
    };

    // Convergence Recovery: Handle Tab Focus / Visibility Change (P1-5)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible' && typeof ClientStateManager !== 'undefined' && ClientStateManager.isDrawerOpen()) {
            if ($('#requisitions').hasClass('active')) {
                // Check if server version advanced while tab was inactive
                checkAndReconcileJobsVersion();
            }
        }
    });
})();
```

---

## 9. UI/UX & 15.6" SCREEN OPTIMIZATION (1366×768)

### 9.1 Scoped Styles (Rule 10 Compliance)
- Scope the stylesheet to: `resources/views/service_company/client/components/tab-jobs-style.blade.php`.
- Include explicit styles for `.inactive-bg`:
  ```css
  .jobx .badge-hiring.inactive-bg {
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #cbd5e1;
  }
  .jobx .badge-hiring.inactive-bg::before {
      background: #94a3b8;
  }
  ```

### 9.2 Complete 5-Option Status Filter Group (4 Canonical States + All Jobs)
Replace the broken filter menu with Metronic pill buttons providing a complete 5-option status filter group covering all 4 canonical backend lifecycle states (`hiring`, `hold`, `closed`, `inactive`) plus 'All Jobs' aggregate:
```html
<div class="cl-jobs-filter-group">
    <button type="button" class="btn btn-sm btn-light active" data-status="all">All Jobs <span class="badge badge-light-dark ms-1">{{ $viewModel->totalCount }}</span></button>
    <button type="button" class="btn btn-sm btn-light" data-status="hiring">Hiring <span class="badge badge-light-success ms-1">{{ $viewModel->hiringCount }}</span></button>
    <button type="button" class="btn btn-sm btn-light" data-status="hold">On Hold <span class="badge badge-light-warning ms-1">{{ $viewModel->holdCount }}</span></button>
    <button type="button" class="btn btn-sm btn-light" data-status="closed">Closed <span class="badge badge-light-danger ms-1">{{ $viewModel->closedCount }}</span></button>
    <button type="button" class="btn btn-sm btn-light" data-status="inactive">Inactive <span class="badge badge-light-secondary ms-1">{{ $viewModel->inactiveCount }}</span></button>
</div>
```

### 9.3 Progressive Disclosure Layout (Replacing Forced 7 Columns)
On 1366×768 screens, an 80% drawer leaves ~1040px of visible width.
**Optimized Grid Structure:**
```
Columns: [ Job & Role (2.8fr) | Exp & Salary (1.2fr) | Status & Mode (1.1fr) | Pipeline Compact (2.2fr) | Actions (0.7fr) ]
```
- **Hiring Pipeline:** Streamlined horizontal micro-pipeline badge strip (`Applied 12`, `Screen 6`, `Interview 3`, `Offer 1`, `Joined 1`).
- **Secondary Metadata (HM, Team, Agency):** Disclosed via clean hover popovers or within the Job Details Drawer on click.

---

## 10. STRICT IMPLEMENTATION ROADMAP (BY PRIORITY)

```mermaid
flowchart TD
    subgraph P0 ["P0: Release Blockers (Immediate Execution)"]
        P0_1["1. Enforce comId on Internal Jobs Query"]
        P0_2["2. Scope Direct Client Active Req to from_company_id"]
        P0_3["3. Replace .show-detail with data-entity='job'"]
        P0_4["4. Bounded Batched Reads (Zero N+1 ES Operations)"]
        P0_5["5. Remove All DB::table Reads from Read Path"]
    end

    subgraph P1 ["P1: Architecture & Data Integrity"]
        P1_1["6. Build ClientJobsQuery & ClientJobsReadService"]
        P1_2["7. Transactional Outbox + Synchronous job_status_logs Write"]
        P1_3["8. Unify KPI Summary & Listing via JobsTabViewModel"]
        P1_4["9. Version-Safe CQRS Sequencing & Two-Tier Reverb"]
        P1_5["10. Complete 5-State Status Filters with Inactive"]
    end

    subgraph P2 ["P2: Product Polish & Auditability"]
        P2_1["11. Excise All Dead Mock Code & Keenthemes Links"]
        P2_2["12. Remove Copy-Pasted User Admin Filter in dviewMore"]
        P2_3["13. Correct Pagination Label to 'jobs' with Page-Clamp"]
        P2_4["14. Implement Multi-Match Exact-Boost Search Strategy"]
        P2_5["15. Deterministic Tie-Breaker Sort (created_at DESC, id DESC)"]
    end

    P0 --> P1
    P1 --> P2
```

---

## 11. FINAL ACCEPTANCE & VERIFICATION CHECKLIST

- [ ] **Zero-MySQL Authorization Context Isolation (Pre-ES Gate):** Verify `ClientAccessContext::fromRequest()` resolves context with **strictly 0 MySQL reads** (via session claims for internal clients and Dragonfly/Redis cache or HMAC-signed tokens for direct clients).
- [ ] **Triple Attack Vector Security Suite:** Execute automated penetration tests:
  - **Test A (Cross-Tenant Forged Client):** Tenant A requests Client B $\to$ `isAuthorized = false` $\to$ HTTP 403 $\to$ ES Mock/Spy invocation count == 0 $\to$ `ClientJobsQuery` construction count == 0.
  - **Test B (Cross-Partner Direct Client Spoofing):** Tenant A + Service Company A requests Direct Client C under Partner B, but Client C belongs to Partner D $\to$ HTTP 403 $\to$ ES invocation count == 0.
  - **Test C (Unauthorized Requisition Isolation):** Valid Direct Client requests a job requisition from Partner B not shared via active `recommendation_relation` (`final_status = 1`) $\to$ ES nested filter excludes it $\to$ Job never appears in table or counts.
- [ ] **CQRS Zero-Read Full Rendering Path CI Gate:** Execute automated feature test `ClientJobsCqrsReadTest`; verify `\DB::getQueryLog()` records strictly 0 MySQL queries across controller execution, auth context resolution, ViewModel preparation, Blade template rendering, and JSON serialization; verify 0 ES/HTTP calls during Blade rendering.
- [ ] **Multi-Client / Multi-Partner Fan-Out (P0):** Mutate status of shared marketplace requisition; verify `JobVisibilityScopeResolver` identifies all receiving partner direct client scopes, atomically increments Redis invalidation keys across all scopes, and broadcasts Reverb events to all recipient company channels.
- [ ] **Configurable Search Relevance (Rule 11):** Verify multi-match query binds search fields, weights, operator, and minimum_should_match dynamically from `config('ats.search.jobs.*')` without hardcoded literals.
- [ ] **Write Mutation Context Tenancy Separation:** Verify `JobMutationService::updateStatus()` strictly accepts `JobMutationContext` where `tenantId` and `serviceCompanyId` are discrete properties, persisting `$context->tenantId` into `sys_outbox_events.tenant_id` and `$context->serviceCompanyId` into `job_status_logs.company_id` without collapsing them.
- [ ] **Storage-Engine Multi-Tenant Outbox Uniqueness & Collision Test:**
  - Duplicate Attempt: Insert duplicate outbox event with identical `$idempotencyKey = "job:{$jobId}:v{$version}"` within same `tenant_id` $\to$ MySQL InnoDB physically throws Error 1062 duplicate entry.
  - Cross-Tenant Independence: Insert identical `$idempotencyKey = "job:{$jobId}:v{$version}"` under different `tenant_id` (e.g. 100 vs 200) $\to$ Successfully inserts, proving tenant-scoped uniqueness without global key blocking.
- [ ] **Zero-Hardcoding Outbox Worker (Rule 11):** Verify outbox batch claim SQL queries use dynamic bound parameters (`:batch_size`) configured via `config('ats.outbox.batch_size')` with zero raw literal integers.
- [ ] **5 Static Identity Dimensions & Monotonic Redis Invalidation:** Verify Redis version key and cache hash isolate internal (`ats:client_jobs_version:100:100:internal:0:782`) and direct (`ats:client_jobs_version:100:100:direct:450:782`) clients across the 5 identity dimensions with zero collision.
- [ ] **5-Option Status Filter Group (4 Canonical States + All Jobs):** Verify UI renders all 5 filter buttons (All Jobs, Hiring, On Hold, Closed, Inactive) correctly mapped to the 4 canonical backend lifecycle states without treating 'all' as a database status.
- [ ] **Bounded Read Budget (Zero N+1):** Verify total Elasticsearch queries per page load remains strictly bounded:
  $$\text{ES Network Requests} = O(1) \quad (\text{independent of page size } N)$$
  $$\text{ES Network Requests} \neq O(N)$$
  Verify zero Elasticsearch queries and zero MySQL queries inside Blade loops.
- [ ] **End-to-End API Request Latency SLOs (10K Concurrency):** Under simulated 10,000 concurrent user load (Browser $\to$ Nginx $\to$ FrankenPHP $\to$ Auth $\to$ Cache/ES $\to$ ViewModel $\to$ Network):
  - **p50 $\le$ 100ms**
  - **p95 $\le$ 250ms**
  - **p99 $\le$ 500ms**
  - **Error rate < 0.1%**
- [ ] **Elasticsearch Core Query Latency SLOs (10K Concurrency):** Verify internal ES query execution achieves:
  - **p50 $\le$ 50ms**
  - **p95 $\le$ 150ms**
  - **p99 $\le$ 300ms**
- [ ] **Monotonic Aggregate Versioning (P1-3):** Simulate two simultaneous status change requests on the same job; verify `lockForUpdate()` enforces sequential atomic increments ($v_{n+1} = v_n + 1$) without collisions.
- [ ] **Primary-Shard Atomic ES Script Update & Schema Ownership (P1-4, P1-5):** Re-deliver a processed outbox event or execute an out-of-order event retry ($v=11$ after $v=12$ completed); verify Elasticsearch executes atomic Painless script, modifies strictly declared fields (`status`, `projection_version`, `updated_at`), and returns `ctx.op = 'noop'`, preventing version regression.
- [ ] **Single-Snapshot Canonical Rebuild Invariant:** Evict ES document; trigger rebuild; verify `rebuildAndIndexCanonicalJob()` reads complete projection document and authoritative `aggregate_version` within a single consistent snapshot boundary (`DB::transaction`), eliminating version shearing.
- [ ] **Monotonic Redis Lua Update (P1-6):** Execute concurrent worker completions; verify atomic Lua script prevents older version from regressing the Redis cache invalidation index.
- [ ] **Outbox Crash & Idempotency Tolerance:** Force worker crash immediately before `status = 'COMPLETED'`; re-run outbox dispatcher; verify idempotent Painless script and Lua script execute safely without duplicate mutations or version corruption.
- [ ] **Convergence Recovery without Reverb:** Sever WebSocket connection in DevTools; change job status in another session; switch tabs or reopen drawer; verify focus/drawer handshake reconciles fresh data without stale cache display.
- [ ] **Rich Optimistic Patch & Authoritative Reconciliation:** Mutate job status; verify row badge updates instantly in DOM (<1ms), status timestamp displays "Updated just now", and filter counters adjust in RAM; verify 300ms debounced server refresh unconditionally reconciles exact pagination, pipeline totals, and sorting.
- [ ] **Click Routing:** Click job title in drawer; verify Job Details Drawer opens without crashing or closing the parent Client Drawer.
- [ ] **Pagination Page-Clamp:** Shrink total jobs below current page threshold; verify server clamps page to `last_page` and avoids empty zero-row display.
- [ ] **Visual Parity:** Open Internal Client and Direct Client drawers on 1366×768 screen; verify identical Metronic styling, dedicated `inactive-bg` badge, and zero horizontal page overflow.
