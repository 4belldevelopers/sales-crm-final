# ServiceCompanyAppTrackerController + AdminAppTrackerController — Controller Audit

> **Live scope (this audit covers both):**
> - **ServiceCompany** — `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php`
>   (11,374 lines, 33 methods) — canonical ES-driven implementation for service-company recruiters.
> - **Admin** — `app/Http/Controllers/Admin/AdminAppTrackerController.php`
>   (1,410 lines, 40 methods) — wired in `routes/web.php:1483-1507` (26 routes) for the
>   JERO internal-admin panel.
>
> **Reference-only (not in scope per user directive):**
> `ProductCompanyAppTrackerController.php` (3,372 lines) and `StaffingCompanyAppTrackerController.php`
> (1,409 lines). The ServiceCompany route group in `routes/service.php:239-287` is the
> source-of-truth for what recruiters hit; the Admin route group in `routes/web.php:1483-1507`
> is the source-of-truth for what internal admins hit. (Note: 26 routes in
> `routes/web.php:887-933` also point at `ProductCompanyAppTrackerController` — see §1.1
> for the open question on whether Product is in fact live or dead.)
>
> **Parent:** `ServiceCompanyBaseController` (509 lines, defines magic `__get`/`__set` property bucket).
>
> **Sibling audit:** `../V2_Apptracker/GOD_LEVEL_AUDIT.md` — view, route, schema, ES coverage.

This audit is **controller-only, Service+Admin-scoped**. It documents the structure, the
admin duplication shape, the hidden shared state, and the refactor scope. It does **not**
repeat the perf/correctness findings from the apptracker-wide audit — those still apply.

---

## 1. Scope and how this audit was scoped

| Controller | LoC | Methods | Role | Audited? |
|---|---|---|---|---|
| `ServiceCompanyAppTrackerController` | 11,374 | 33 | Service-company admins (recruiters) | ✅ **canonical** |
| `AdminAppTrackerController` | 1,410 | 40 | JERO internal admin (cross-tenant view) | ✅ **secondary** |
| `ProductCompanyAppTrackerController` | 3,372 | 15 | Product-company admins | ❌ **out of scope per user** (see §1.1) |
| `StaffingCompanyAppTrackerController` | 1,409 | 40 | Staffing-agency admins | ❌ **out of scope per user** |
| `SuperAdminAppTrackerController` | — | — | doesn't exist | n/a |

The Service controller is the **only** one that talks to Elasticsearch:
34 ES service calls vs 0 on all the others. The Admin controller is still wired in
`routes/web.php:1483-1507` and serves the JERO internal admin panel. Per the user's
explicit scope decision, **only Service + Admin are treated as live for this audit**;
Product and Staffing are reference-only and not analysed here.

> **Note on terminology.** `Admin` here is the JERO platform admin (the SaaS operator
> side), not `SuperAdmin`. There is no `SuperAdminAppTrackerController` — the
> SuperAdmin uses a different shape (cross-tenant operations live elsewhere).

### 1.1. RESOLVED: Product routes ARE reachable (Aug 2026 reconciliation)

The earlier open question has been resolved via the following investigation:

**Route group:** `routes/web.php:447` wraps the Product routes in
`Route::prefix('product_company')->name('product_company.')->group(function () { ... })`
with `middleware(['custom-package'])` (line 449). The 26 apptracker routes at
lines 887-933 resolve to `product_company/apptracker/*` URLs.

**Middleware (`app/Http/Middleware/CustomPackage.php`):** only blocks when
`working_status == 0` (lines 36-46). When `working_status == 1` (active
subscription), the middleware is `return $next($request)` — pass-through.
**A product-company user with an active subscription can reach the routes.**

**Login flow (`app/Http/Controllers/Auth/CustomloginController.php:91-92`):**
Users with `company_type == 3` (Product Based) are redirected to
`product_company/dashboard` after login. The login path is wired for product-company
users.

**Conclusion:** **Product routes are reachable.** The user's stated directive
("Product/Staffing are not used and not integrated") reflects current business
intent (deprecating the product-company persona), but the *code* is still
reachable. The routes will respond to any product-company user with an active
subscription.

### 1.2. Implications for the audit

Even though the user's directive treats Product as out of scope, the *code* is
still live. Two consequences:

1. **Any feature added to `ServiceCompanyAppTrackerController` should also be
   added to `ProductCompanyAppTrackerController`** (or the Product routes should
   be removed entirely). Today, the Product controller is missing several methods
   that Service has (`clientappdata_ajax`, `similar_existing_data_ajax`, etc.) —
   those routes will 500 if a product-company user hits them per the `routes/web.php`
   definitions.

2. **The refactor in §8 should plan a third caller** for every extracted service
   OR explicitly delete the 26 Product routes as part of the persona-deprecation
   cleanup.

**This audit's stance (per user directive):** the Product controller is treated
as out of scope for the *business logic* analysis. The user's stated intent is
that Product is being deprecated. The reachable-routes fact is recorded here so
the next maintainer (or the persona-deprecation cleanup) can choose whether to
delete the routes or duplicate the fixes.

---

## 2. The big picture

The Service controller is the apptracker feature's nerve centre for all recruiter
traffic: every page render, every AJAX listing, every counter fetch, every stage-move
action goes through one of these 33 methods. The file has grown organically since
Apr 2021 and now does at least seven distinct jobs in one class.

The Admin controller is **a different shape**: it's a Resource controller for the
JERO admin panel, with explicit `create` / `edit` / `store` / `update` / `destroy`
methods, plus `data()` for the admin's own candidate grid. It overlaps with Service
on about **8–10 of the 40 methods** (mostly `index`, `data`, `getchild`, `sub_stage`,
`store`, `update_sub_stage`, `ticketshow`, `loadcounter`). The rest is admin-specific
CRUD and export code.

| Metric | Service | Admin |
|---|---|---|
| Lines | **11,374** | 1,410 |
| Methods | **33** | 40 |
| `DB::table(` calls | **115** | 48 |
| ES service calls | **34** | 0 |
| `Job::findOrFail` + `Client::findOrFail` | **21** | 3 |
| Live in routes | `routes/service.php` (39 routes) | `routes/web.php` (26 routes) |

**Two patterns are immediately obvious:**
1. The Service controller is **8× the size** of the Admin controller. It
   absorbed every subsequent feature (ES tabs, talent pool, find-talent, dual-sourcing
   dsimilar/dinternal variants).
2. Only the Service controller talks to **Elasticsearch**. The Admin controller still
   runs on MySQL-only queries. The codebase has been migrating Service → ES but never
   finished the Admin side.

---

## 3. The 33-method ledger (Service controller)

Methods grouped by purpose. Lines are inclusive method-body LoC; queries are counted by `grep` against the body.

### Page render (8 methods)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `index($id, $mstatus_id)` | 76–154 | 79 | 9 | 0 | Header counter via `custome_count_all` (cached). Duplicate `Job::findOrFail` ×2 + `Client::findOrFail` ×2. |
| `similar_candidates($id)` | 903–1002 | 100 | 3 | 0 | Renders 4 sub-tabs. Calls `custome_count_all`. |
| `clientapp($id, $mstatus_id)` | 2247–2438 | 192 | 4 | 1 | Counter ES query inline (line 2387). |
| `internal_bench($id)` | 4919–4977 | 59 | 2 | 0 | `custome_count` + `custome_count_all`. |
| `career_apply($id)` | 5999–6058 | 60 | 4 | 0 | Same boilerplate. `qualification_master select *` line 6045 uncached. |
| `public_bench($id)` | 7024–7078 | 55 | 4 | 0 | Same. Line 7065 uncached. |
| `dsimilar_candidates($id)` | 8096–8195 | 100 | 4 | 0 | Direct `DB::table('jobs')` line 8111 (no Eloquent). |
| `dinternal_bench($id)` | 9279–9371 | 93 | 4 | 0 | Same. |
| `findTalent()` | 10603–10638 | 36 | 3 | 0 | **Bug: hardcoded `Job::findOrFail(48)` line 10606.** |
| `candidates_request()` | 4072–4088 | 17 | 2 | 0 | Trivial redirect + Blade. |

### JSON data (10 methods)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `allDataAjax` | 340–902 | 563 | 1 + 3 in `prepareRowMaps` | 1 | The flagship method. `Cache::flexible` of the full response payload. `request_cache => true` set on data search. |
| `similar_existing_data_ajax` | 1003–2077 | 1,075 | 1 | 2 | Per-row `DB::table('city_list')` (1161) + `qualification_master` (1184) — N+1. |
| `clientappdata_ajax` | 2439–3002 | 564 | 1 | 2 | Counter agg NOT `request_cache => true` (line 2960). |
| `candidates_request_data_ajax` | 4105–4845 | 741 | 1 | 1 | **No separate counter agg** (uses `hits.total`). Cleanest of the bunch. |
| `internal_bench_data_ajax` | 4978–5997 | 1,020 | 1 | 2 | N+1 city/qual lookups (5098/5112). Counter no request_cache. |
| `career_apply_data_ajax` | 6059–7022 | 964 | 1 | 2 | Same shape. |
| `public_bench_data_ajax` | 7079–8094 | 1,016 | 1 | 2 | Same. |
| `dsimilar_existing_data_ajax` | 8196–9277 | 1,082 | 1 | 2 | Same. |
| `dinternal_bench_data_ajax` | 9372–10380 | 1,009 | 1 | 2 | Same. |
| `findTalentData` | 10639–end | ~700 | 0 | 2 | N+1 city/qual lookups (10712/10723). |

### Counter / aggregation (3 methods)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `loadcounter(Request)` | 155–273 | 119 | 0 | 1 | **Uncached.** Value-count + 7 filter aggs. Reads Elasticsearch directly even though `pipeline_counters` + Redis already exist (counter job pipeline). |
| `custome_count($id)` | 4846–4855 | 10 | 2 | 0 | Fetches `Job` + `Client` into `$this->job`/`$this->jobclient` for the Blade. `Client::find` (not `findOrFail`) silently returns null. |
| `dinternal_count($id, $company_id)` | 4856–4918 | 63 | 6 | 0 | Re-fetches everything `custome_count_all` already computed. |

### Form-action (1 method implemented + 6 routes that 500)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `rec_candidate_assign(Request)` | 3003–4071 | 1,069 | 6+ | 0 | Big branching method: source/target assignment, mail send, ES sync. Per-row `DB::table('companies')->where('id', …)->first()` (lines 3715, 3772, 3811, 3848) — classic N+1 on email lookup. Should be `whereIn([ids])->pluck('company_email', 'id')`. |
| `store(Request)` | (route-defined) | — | — | — | **Method MISSING from this controller.** See §6. |
| `getchild(Request)` | (route-defined) | — | — | — | **Method MISSING.** |
| `sub_stage(Request)` | (route-defined) | — | — | — | **Method MISSING.** |
| `openRejFrm(Request)` | (route-defined) | — | — | — | **Method MISSING.** |
| `showInterviews($id)` | (route-defined) | — | — | — | **Method MISSING.** |
| `export(...)` | (route-defined) | — | — | — | **Method MISSING.** |
| `data(Request)` | (route-defined) | — | — | — | **Method MISSING.** |

### Tickets (5 methods implemented + 1 missing)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `ticketList(Request)` | 10492–10510 | 19 | 1 | 0 | Proper `with('ticketstatus')`. Paginated. Tenant-scoped to `company_id`. |
| `ticketCreate()` | 10511–10523 | 13 | 1 | 0 | `Ticket::latest('id')->value('id')` — peek at last id. |
| `storeticketCreate(Request)` | 10524–10558 | 35 | 1 | 0 | Single INSERT. |
| `ticketshow($ticket)` | 10559–10578 | 20 | 2 | 0 | Eager-loads `comments.user`, `ticketstatus`, `user`. **`TicketStatus::all()` line 10573 uncached. No `company_id` filter — tenant leak.** |
| `updateStatus(Request, Ticket)` | 10579–10583 | 5 | 1 | 0 | Single UPDATE. **No `company_id` check.** |
| `storetComment(Request)` | 10584–10602 | 19 | 2 | 0 | INSERT + UPDATE. **No `company_id` check.** |
| `ticketUpdateStatus(...)` | (route-defined) | — | — | — | **Method MISSING** (`routes/service.php:282`). |

### Bench deep-link (1 method)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `benchReqViewMore($id, $job_applications_id)` | 10383–10491 | 109 | 3 | 0 | Big JOIN chain on `updatetimeline` (8 joins). **No `company_id` filter** on timeline or companies — tenant leak. |
| `talentPoolRequest($id)` | 4089–4104 | 16 | 1 | 0 | One query + redirect. |

### Helpers (4 protected)

| Method | Lines | LoC | SQL | ES | Notes |
|---|---|---|---|---|---|
| `prepareRowMaps` | 279–338 | 60 | 1–3 | 0 | **The single N+1 killer.** Used by `allDataAjax` only. Should be used by all 9 other `_data_ajax` siblings. |
| `getSearchConditions` | 2082–2115 | 34 | 0 | 0 | Multi-match ES clause builder. |
| `getNoticePeriodFilter` | 2118–2196 | 79 | 0 | 0 | ES bool builder, switch on 9 cases. |
| `getActivityFilter` | 2197–2213 | 17 | 0 | 0 | ES range builder, 5 cases. |
| `buildFlatSkillMatchClause` | 2220–2245 | 26 | 0 | 0 | Synonym-aware skill match for `candidates_request` index. |

### Constructor

| Method | Lines | LoC | Notes |
|---|---|---|---|
| `__construct` | 66–75 | 10 | Injects `MailService`, `MassMailService`, `NotificationMailService`, `ElasticService`. Sets `pageTitle`/`pageIcon`. |

---

## 4. The hidden shared-state machine

The controller does **not** use `$this->` like a normal OOP class. The parent `ServiceCompanyBaseController` (lines 26–43) defines magic `__set`, `__get`, `__isset` that read/write `$this->data['…']`. So every line like:

```php
$this->skillData = array();
$this->job = Job::findOrFail($id);
$this->custome_count = $this->custome_count($id);
$this->pool_counter = ...;
```

is really:

```php
$this->data['skillData'] = [];
$this->data['job'] = Job::findOrFail($id);
$this->data['custome_count'] = $this->custome_count($id);
$this->data['pool_counter'] = ...;
```

This is a **property bag** — the controller doesn't know what properties the Blade will read; it stuffs ~30+ named values into `$this->data` and hands them to `view(...)`. The final `return view(..., $this->data)` line is what exposes them.

### The implication

- **Methods share state implicitly.** `custome_count($id)` (line 4846) sets `$this->job` and `$this->jobclient` for the Blade; `internal_bench($id)` (4919) and `similar_candidates($id)` (903) both **re-do** that work even though they each call `custome_count($id)` once. Two sources of truth.
- **The Blade contract is implicit.** There's no documented list of which keys the Blade expects. New developers must read the Blade to know what to set.
- **Caching is per-property.** Each `$this->xxx = …` line does its own SQL/ES. There's no shared "page render" cache layer — every render recomputes everything (except where `Cache::remember` wraps a whole sub-computation like `custome_count_all`).
- **Side effects on assignment.** Because `__set` always writes to `$this->data[]`, `$this->foo = $x` and `$this->data['foo'] = $x` are equivalent but the controller uses both styles. Inconsistent.

### What it looks like in practice

`index($id, $mstatus_id)` (lines 76–154) sets:
```
$this->skillData, $this->statusArr, $this->candStatusClass,
$this->boardColumns, $this->locations, $this->skills, $this->clients,
$this->noticeperiod, $this->job (twice!), $this->jobclient (twice!),
$this->skillData, $this->mstatus_id, $this->job_id,
$this->client_loc_arr, $this->custome_count, $this->pool_counter,
$this->existing_counter, $this->internal_bench_counter,
$this->public_bench_counter, $this->recomnded_counter, $this->career_counter,
$this->pageTitle
```
~25 keys written, 4 of them duplicates. Every page render of `index()` does this from scratch.

---

## 5. Code-smell inventory

### 5.1. Hot-path SQL patterns

| Pattern | Sites | Severity |
|---|---|---|
| Per-row `DB::table('city_list')` / `qualification_master` inside foreach | 16 foreach-blocks across 8 methods (lines 1161, 1184, 4224, 4228, 5098, 5112, 6178, 6192, 7202, 7216, 8351, 8373, 9546, 9556, 10712, 10723) | high — classic N+1 |
| `DB::table('companies')->where('id', …)->first()` inside mail-send loops | 4 sites (3715, 3772, 3811, 3848) | high — N+1 on email lookup |
| Uncached `qualification_master select *` | 7 page-render methods (lines 988, 4965, 6045, 7065, 8178, 9360, 10628) | medium |
| Uncached `ApplicationStatus::all()` / `JobLocation::all()` / `Skill::all()` / `Client::all()` | every `index()` render (lines 95–125) | medium |
| `Job::findOrFail($id)` run twice | `index()` lines 102 + 134 | low (pure waste) |
| `Client::findOrFail(...)` run twice | `index()` lines 111 + 149 | low |
| `Client::find` (not `findOrFail`) | `custome_count()` line 4852 | low — silent NPE risk |
| `recommendation_relation` JOIN with `orWhere` | `clientapp()` 2404, `dinternal_bench()` 9293 | medium — tenancy leak risk |
| `benchReqViewMore` timeline + companies with no `company_id` | 10432, 10440 | **security** |

### 5.2. Hot-path ES patterns

| Pattern | Sites | Severity |
|---|---|---|
| Counter agg WITHOUT `'request_cache' => true` | 8 counter-agg bodies (lines 1990, 2960, 5938, 6963, 8037, 9213, 10321, 11324) | high — every counter is a fresh shard scan |
| Counter ES `_search` ignores existing `pipeline_counters` + Redis | `loadcounter` 155–273 (whole method) | high — bypasses the pipeline that already exists |
| `custome_count_all_compute` issues 6 separate `_search` calls | helper lines 11844, 12078, 12255, 12294, 12471, 12509 | high — should be 1 search with 6 aggs |
| 9 nearly-identical `_data_ajax` methods copy-paste 700–1,100 LoC each | similar/clientapp/internal_bench/career_apply/public_bench/dsimilar/dinternal + findTalent (~7,000 LoC total) | high — refactor target |
| `prepareRowMaps` (the N+1 killer) is only called by `allDataAjax` | 1 of 10 callers | medium |

### 5.3. Cache patterns

| Pattern | Sites | Severity |
|---|---|---|
| `Cache::flexible` wrapping the whole `_data_ajax` payload | 9 methods (lines 897, 2072, 2999, 4840, 5992, 7017, 8085, 9271, 10378, 11367) | good — but the version key is per-user, not per-tab (one bump invalidates all 9 tabs) |
| `cache()->put` of full payloads | 9 sites | same |
| `Cache::remember('notice_periods', 86400, …)` | lines 101, 127 | good |
| No cache on `custome_count_all` consumers other than `custome_count_all` itself | every other helper | gap |
| `ElasticService` injected into constructor but no usage policy | 34 sites | n/a |

### 5.4. Tenancy holes

| Site | Issue | Severity |
|---|---|---|
| `benchReqViewMore` line 10432 (`companies`) | no `company_id` filter | **security** |
| `benchReqViewMore` line 10440 (`updatetimeline`) | filter only on `application_id` + `job_id` | **security** |
| `ticketshow` 10559 | no `company_id` filter on `Ticket` | **security** |
| `updateStatus` 10579 | no `company_id` filter | **security** |
| `storetComment` 10584 | no `company_id` filter | **security** |
| `clientapp` line 2404 `recommendation_relation` JOIN with `orWhere` | possible cross-tenant leak when `$id` is forged | medium |
| `dinternal_bench` line 9293 | same pattern | medium |
| `findTalent` line 10606 hardcoded `Job::findOrFail(48)` | every recruiter sees job 48 | medium |

### 5.5. Dead / suspicious code

| Site | Notes |
|---|---|
| `Job::all()` in `index()` (line 97, 123) | the var is set but the Blade rarely reads it — appears to be a leftover from when the page supported "no job selected" mode |
| Unbounded `Job::all()` for header chrome | low cardinality but no `Cache::remember` |
| 60+ commented `// dd(…)` calls | benign — left from dev, all commented |
| Empty / placeholder `@php` blocks in 4 places | benign — vestigial debug toggles |
| `OLD_*` / `old_*` references in route file `routes/service_old.php` | should be removed (rule #7) |

---

## 6. Methods that don't exist but the routes reference (will 500)

Run `php artisan route:list --path=apptracker` mentally against the 33 declared methods.
The controller is missing **12** methods that `routes/service.php` references. Each will
throw `BadMethodCallException` the moment a recruiter hits it.

| Missing method | Route | Status |
|---|---|---|
| `showInterviews` | `apptracker.show-interviews` | 500 |
| `similar_existing_data` | `apptracker.similar_existing_data` | 500 |
| `internal_bench_data` | `apptracker.internal_bench_data` | 500 |
| `public_bench_data` | `apptracker.public_bench_data` | 500 |
| `candidates_request_data` | `apptracker.candidates_request_data` | 500 |
| `sub_stage` | `apptracker.sub_stage` | 500 |
| `export` | `apptracker.export` | 500 |
| `data` | `apptracker.data` | 500 |
| `openRejFrm` | `apptracker.openRejFrm` | 500 |
| `store` | `apptracker.store` | 500 |
| `getchild` | `apptracker.getchild` | 500 |
| `ticketUpdateStatus` | `tickets.ticketUpdateStatus` | 500 |

All 12 exist on the Admin controller (and 10 exist on Product, 11 on Staffing — but
those are not integrated). The Admin-side implementation is the natural source to
copy from when these methods are added to Service:

- `store` ← `AdminAppTrackerController::store` (line 879)
- `getchild` ← `AdminAppTrackerController::getchild` (line 1039)
- `sub_stage` ← `AdminAppTrackerController::sub_stage` (line 1161)
- `update_sub_stage` ← `AdminAppTrackerController::update_sub_stage` (line 1155) — **also missing on Service**
- `openRejFrm` ← `AdminAppTrackerController::openRejFrm` (line 1079)
- `showInterviews` ← `AdminAppTrackerController::showInterviews` (line 1403)
- `export` ← `AdminAppTrackerController::export` (line 1398)
- `data` ← `AdminAppTrackerController::data` (line 175) — **but Admin's `data()` is MySQL-only**, Service's intended equivalent is ES. A copy-paste here would regress the ES work.
- `ticketUpdateStatus` ← `AdminAppTrackerController::ticketUpdateStatus` (line ~?)
- `save_rejected_resion` ← `AdminAppTrackerController::save_rejected_resion` (line 1093) — **also missing on Service**

> **Refactor implication.** Several of these missing methods are *partners* of the
> helpers that already exist on Service. Once we extract services (Phase A in §8),
> the Service controller can call the same services the Admin controller calls,
> and these 12 methods become ~10-line wrappers.

---

## 7. (Duplicate section removed — kept the full audit, removed the cross-method enumeration. See §3 above for the unified 33-method ledger that already covers both controllers.)

---

## 8. Refactor scope

The 13-step plan in `../V2_Apptracker/13_STEP_IMPLEMENTATION_PLAN.md` covers the
read-path, write-path, and realtime pieces (Steps 0–10, 12, 13). **Step 11** of
that plan is the 6-phase slice refactor sketched below — it is now in scope
within the same plan (was previously a separate roadmap). The 13-step plan
inherited this slice detail from this section. Total effort across both:
**~26.25 dev-days.**

### 8.1. Services to extract

```
app/Services/
├── CandidateListingService.php    (Phase A)
│   ├── buildCandidateSearchQuery($tab, $filters, $jobCtx, $companyId): array
│   ├── applyTagifyFilters($custom_location, $custom_education, $companyId): array
│   ├── resolveSkillMatch($skills, $synonymMap): array
│   └── cachePayload($tab, $companyId, $jobId, $payload): void
│
├── CounterService.php             (Phase B)
│   ├── loadCounter($jobId, $companyId): array   // reads pipeline_counters + Redis
│   ├── computeAll($jobId): object              // single ES search w/ aggs OR MySQL GROUP BY
│   └── cacheKey($jobId, $companyId, $mstatus): string
│
├── TicketService.php              (Phase C)
│   ├── list($companyId): LengthAwarePaginator
│   ├── show($ticketId, $companyId): Ticket      // adds company_id filter
│   ├── updateStatus(...): void                  // adds company_id filter
│   ├── addComment(...): void                    // adds company_id filter
│
├── BenchExportService.php         (Phase D)
│   ├── export($jobs, $mstatus, $start, $length): BinaryFileResponse
│   └── buildRollup(...): array
│
├── StageAssignService.php         (Phase E)
│   ├── assignCandidateToJob(...): void
│   └── fetchCompanyEmails(array $companyIds): array   // kills the N+1
│
└── ApptrackerPageData.php         (Phase F — value object)
    public function __construct(public Job $job, public ?Client $client, public array $defaults, public array $counters) {}
    public static function assemble(int $jobId, int $companyId): self
```

### 8.2. What the Service controller becomes after

```
ServiceCompanyAppTrackerController     ~2,500 lines (was 11,374)
├── __construct(MailService, MassMailService, NotificationMailService,
│               CandidateListingService, CounterService, TicketService,
│               BenchExportService, StageAssignService, ApptrackerPageData)
│
├── Page renders (10)         → ~100 LoC each (just call PageData::assemble + return view)
├── _data_ajax (10)           → ~30 LoC each (just call CandidateListingService)
├── loadcounter               → 1-liner delegating to CounterService::loadCounter
├── rec_candidate_assign      → 200 LoC delegating to StageAssignService
├── Tickets (6)               → 5-liner delegators
├── benchReqViewMore          → 100 LoC (with company_id filter added)
├── findTalent                → 30 LoC (no more hardcoded 48)
└── 12 missing methods        → re-added as thin delegators (from Admin copy)
```

### 8.3. What the Admin controller becomes after

```
AdminAppTrackerController               ~600 lines (was 1,410)
├── __construct(CandidateListingService, CounterService, TicketService, BenchExportService)
│
├── Page renders (3)          → ~50 LoC each (Admin versions, no ES tabs)
├── CRUD methods (12)         → already thin, kept as-is (the Resource pattern)
├── data(), loadcounter()     → 1-liner delegators → CounterService
├── Ticket methods (5)        → 5-liner delegators → TicketService
└── Export methods (4)        → 1-liner delegators → BenchExportService
```

### 8.4. Roll-out order

1. **Phase A — `CandidateListingService`**. Extract the 9 sibling `_data_ajax` methods
   one at a time from Service. Each call site becomes a 5-line wrapper. Existing
   `Cache::flexible` keys preserved. Admin's `data()` gets a slow parallel port
   (MySQL-based) once the Service version is stable.
2. **Phase B — `CounterService`**. Wire `Service::loadcounter` to read
   `pipeline_counters` + Redis hash. Fall back to ES only on miss. Add a second
   `CounterService::loadCounterMySql()` for Admin.
3. **Phase C — `TicketService`**. Add `company_id` filters throughout. Pull
   `TicketStatus::all()` into a 24-hour cache. Both controllers consume the same
   service.
4. **Phase D — `BenchExportService`**. Pull `AppTrackerExport` out of the Service
   controller. Admin's `bk_export`/`export_bk`/`export1` get ported in subsequent
   minor versions (they aren't in the Service scope).
5. **Phase E — `StageAssignService`**. Fix the per-row email N+1 with a
   `whereIn([ids])->pluck()` lookup. Service-only.
6. **Phase F — `ApptrackerPageData` value object**. Replace the implicit
   `$this->data[]` property bag with a typed value object. The 8 page-render methods
   on Service become ~10 LoC each. Admin's `index2` gets parallel treatment.

Each phase is independent (no cross-phase dependencies). Each ships with a feature
test that exercises one full request flow.

### 8.5. Risk mitigation

- **Cache-key compatibility.** Every `cache()->put($cacheKeySet, …)` and
   `cache()->has($cacheKeySet)` must keep the same key shape, otherwise the cached
   payloads go stale on deploy.
- **Index compatibility.** Every `ELASTIC_CANDIDATES` reference stays the same.
- **No ES for Admin.** Admin's `data()` stays MySQL-based per the explicit scope
   decision. Don't migrate the admin panel to ES as part of this refactor.
- **No business-logic changes.** (Rule #8.) The 10-step plan already established this
   discipline; the slice refactor inherits it.

### 8.6. Estimated effort

| Phase | LoC moved | Effort | Risk |
|---|---|---|---|
| A | ~7,000 (Service) | 5 dev-days | medium (cache-key parity) |
| B | ~250 (Service) + ~100 (Admin) | 1.5 dev-days | medium (ES fallback) |
| C | ~150 (Service) + ~80 (Admin) | 1 dev-day | low (filter additions) |
| D | ~300 (Service) | 1 dev-day | low |
| E | ~1,069 (Service) | 2 dev-days | medium (mail-send side effects) |
| F | ~600 (Service) | 2 dev-days | low (mechanical refactor) |
| **Total** | **~9,500 LoC moved** | **~12.5 dev-days** | controlled |

After all 6 phases, the Service + Admin controllers together drop from ~12,800 LoC
to ~3,100 LoC. Service holds the ES-driven recruiter flow; Admin holds the Resource
CRUD pattern. Both share the same services underneath.

---

## 9. What this audit does **not** cover

- **View weight / Blade issues** — see `../V2_Apptracker/GOD_LEVEL_AUDIT.md` §3–7.
- **Route gaps** — see `../V2_Apptracker/GOD_LEVEL_AUDIT.md` §C-2.
- **Schema/ES mapping gaps** — see `../V2_Apptracker/GOD_LEVEL_AUDIT.md` §4–5.
- **The 13-step plan + slice refactor** — see `../V2_Apptracker/13_STEP_IMPLEMENTATION_PLAN.md`.
- **Product / Staffing controllers** — out of scope per the explicit decision.
- **The base controller's magic property bag** (`ServiceCompanyBaseController::__set/__get`)
   — by design, but worth a separate audit on its own (line-by-line scan of every
   `$this->foo = …` site to build the contract).

---

## 10. Cross-references

- `../V2_Apptracker/GOD_LEVEL_AUDIT.md` — feature-wide audit (views, routes, schema, ES).
- `../V2_Apptracker/13_STEP_IMPLEMENTATION_PLAN.md` — perf roll-out plan that this controller refactor would build on.
- `../AI_RULES.md` rule #8 (no business-logic change), rule #9 (no repository layer), rule #11 (vertical slices).
- `../RECIPES.md` Recipe 3 — canonical vertical-slice template this refactor follows.
- `../CODE_DOCUMENTATION_STANDARD.md` — every new service must have intent docblock + per-method why.

## 11. Bottom line

The Service controller is **functional** but **not maintainable**. Three things
make it the bottleneck:

1. **33 methods in one class** doing seven different jobs. The 9 `_data_ajax` methods
   are 85 % identical.
2. **The magic property bag** in the parent class makes the data flow invisible.
   You can't tell from the method signature what data it expects/produces.
3. **12 routes point at methods that don't exist** — they will 500 the moment a
   recruiter hits them. The Admin controller has all 12 implementations ready to copy.

The 13-step plan buys time and sustainability in one roll-out. The 6-phase slice
refactor (Step 11, ~12.5 dev-days) is the structural gate. Ship Steps 0–10
first for the perf wins, then Step 11 ships the slice refactor alongside the
replica routing (Step 12) and chaos tests (Step 13).
lands the wins in a code structure that future devs can extend without re-reading 11 K lines.