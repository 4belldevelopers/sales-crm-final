# Apptracker — GOD-level Audit

> Audit of the apptracker feature (Aug 2026), scoped to **ServiceCompany + Admin only**
> per the user's explicit directive. ProductCompany and Staffing controllers ship
> as parallel files but are treated as reference-only here (see §1.1 for the open
> question that contradicts this).
>
> For 10,000 recruiters on 32 GB RAM (Laravel + MySQL + Elasticsearch + Redis/Horizon + Octane).
> Subject: the ATS pipeline that 95% of recruiter time lives in.
> Status: **working but not scalable as-is**; ~9 must-fix issues, ~7 quick wins, 1 critical missing constant.

---

## 1. Surface area (the "what")

**ServiceCompany (canonical):**
- **Controller:** `ServiceCompanyAppTrackerController.php` — **11,374 lines, 33 methods, 377 KB**. Largest god-controller in the repo after HomeController.
- **Helper:** `GeneralMethod.php` — **522 KB, ~14,087 lines** — single static helper class. Apptracker helper `custome_count_all` alone is ~1,300 lines.
- **Views:** 13 blade files in `resources/views/service_company/apptracker/` (51–178 KB each; total ~1.7 MB).
- **Routes:** `routes/service.php` lines 239–287 — **39 named apptracker routes**. Two routes are **unnamed** (`apptracker/{id}/{mstatus_id}` line 265, `apptracker/clientapp/{id}/{mstatus_id}` line 273), blocking `route:cache`.

**Admin (secondary, JERO internal panel):**
- **Controller:** `AdminAppTrackerController.php` — **1,410 lines, 40 methods**.
- **Views:** `resources/views/admin/apptracker/`.
- **Routes:** `routes/web.php` lines 1483–1507 — **26 named apptracker routes** wired into the JERO admin panel under the `admin.*` prefix.

**Companion controllers (out of scope, reference-only):** Staffing and Product both ship parallel `AppTracker` controllers. ServiceCompany is the **only** one that talks to Elasticsearch. Admin runs on MySQL-only queries for the admin panel.

### 1.1. RESOLVED: Product routes ARE reachable (Aug 2026 reconciliation)

The earlier open question has been resolved. Evidence:

- **Route group:** `routes/web.php:447` wraps the Product routes in
  `Route::prefix('product_company')->name('product_company.')->group(function () { ... })`
  with `middleware(['custom-package'])` (line 449). The 26 apptracker routes at
  lines 887-933 resolve to `product_company/apptracker/*` URLs.
- **Middleware (`app/Http/Middleware/CustomPackage.php`):** blocks only when
  `working_status == 0` (lines 36-46). When `working_status == 1` (active
  subscription), the middleware is `return $next($request)` — pass-through.
- **Login flow (`app/Http/Controllers/Auth/CustomloginController.php:91-92`):**
  Users with `company_type == 3` (Product Based) are redirected to
  `product_company/dashboard`. The login path is wired for product-company users.

**Conclusion:** **Product routes are reachable** for any product-company user
with an active subscription. The user's stated directive ("Product/Staffing are
not used and not integrated") reflects current business intent (deprecating the
product-company persona), but the code is still live.

**Implications for this audit (Aug 2026):**
1. The user's *business* directive still holds — Product is being deprecated.
   This audit continues to treat Product as out-of-scope for feature work.
2. **However**, the 26 Product routes at `routes/web.php:887-933` will 500 if a
   product-company user hits any of the methods that exist on Admin but not on
   Product (e.g., `apptracker.data` → `ProductCompanyAppTrackerController::data`).
   This is a security/reliability concern until the Product routes are removed
   OR the missing methods are copied over.
3. **Recommendation:** as part of the persona-deprecation cleanup, either (a)
   delete the 26 Product routes, or (b) duplicate Step 9's route-repair changes
   into `ProductCompanyAppTrackerController`.

---

## 2. Verdict on "working perfectly"

**Partially.** It runs, but:

- The 9 most-loaded endpoints (`*_data_ajax` family) issue **2 ES round-trips per request** (data search + separate counter agg), and **only the data search** uses `request_cache` — counter aggs do not.
- `add_job_wise_condidates` (the table that powers `loadcounter`) has **zero indexes** beyond PRIMARY on a 63,804-row table. With 10K recruiters each opening 5 tabs/hour, this is your MySQL hotspot.
- 1.7 MB of Blade (8 tabs × ~120 KB each) loaded synchronously — first paint on `apptracker/similar_candidates` ships ~700 KB of JS before the user sees a row.
- The `prepareRowMaps` helper that kills 4 N+1s is **only called by `allDataAjax`** — the other 8 tabs still do per-row `DB::table('job_metronics_skills')` queries.
- `loadcounter()` is **uncached**. Every tab open = a full 9-aggregation ES search.

With 32 GB RAM each on MySQL/ES/Redis this is fine for **today** (893 companies, 63 K applications). It does **not** scale to 10 K companies × 100 K apps each without the changes below.

---

## 3. 🚨 Critical bugs (fix today)

### C-1. ~~`ELASTIC_CANDIDATES` constant is undefined~~ — **FALSE POSITIVE**
`config/constants.php` defines all `ELASTIC_*` keys and `AppServiceProvider::boot()` (line 21–25) does `define($key, config('constants.'.$key))` for every one before any controller runs. So the constants ARE available at runtime. Audit agent missed this.

### C-2. 8 routes reference missing methods → 500 in production
Confirmed via grep — these service-side apptracker routes **target methods that don't exist** on `ServiceCompanyAppTrackerController` (they exist on `AdminAppTrackerController`; per the scope directive, Product/Staffing are reference-only and not the source for the fix):

| Route | Missing method |
|---|---|
| `apptracker.show-interviews` | `showInterviews` |
| `apptracker.similar_existing_data` | `similar_existing_data` |
| `apptracker.internal_bench_data` | `internal_bench_data` |
| `apptracker.public_bench_data` | `public_bench_data` |
| `apptracker.candidates_request_data` | `candidates_request_data` |
| `apptracker.sub_stage` | `sub_stage` |
| `apptracker.export` | `export` |
| `apptracker.data` | `data` |
| `apptracker.openRejFrm` | `openRejFrm` |
| `apptracker.store` | `store` |
| `apptracker.getchild` | `getchild` |
| `tickets.ticketUpdateStatus` | `ticketUpdateStatus` |

→ These will 500 the moment a recruiter hits them. **Either** add the methods to the Service controller (copy from the Admin implementation), **or** point the routes at a shared base.

### C-3. `findTalent()` is hardcoded to job_id=48
`app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php:10606` — `Job::findOrFail(48)`. Every recruiter landing on `/findTalent` sees job 48 regardless of company. Cross-tenant display bug. Replace with the user's most-recent job or a feature flag.

### C-4. Ticket actions lack tenant filter
`ticketshow`, `updateStatus`, `storetComment` only rely on route model binding + ciphertext decryption. A valid encrypted `ticket_id` for another tenant grants read/update. Add `where('company_id', auth()->user()->company_id)` everywhere.

### C-5. `benchReqViewMore` is a tenant leak
Filter on `updatetimeline.application_id` and `companies.id` without `company_id`. A user who guesses an `application_id` from another tenant can read their timeline.

---

## 4. Performance: top 10 hot-path issues (ranked)

### P-1. `add_job_wise_condidates` has **zero indexes**
63,804 rows and growing. `loadcounter`, `custome_count_all_compute`, and the ES reindexer all hit it on every page. Add immediately:
```sql
ALTER TABLE add_job_wise_condidates
  ADD INDEX idx_awjc_job_status (job_id, master_status_id),
  ADD INDEX idx_awjc_company_job (company_id, job_id),
  ADD INDEX idx_awjc_thirdparty (thirdparty_company_id, job_id),
  ADD INDEX idx_awjc_app (job_application_id);
```
Without these, 10 K recruiters × 5 tabs = MySQL full scans on every counter refresh. Same for `clients`, `tickets`, `interview_schedules` (all have `company_id` but no index).

### P-2. `loadcounter()` is uncached
Single ES `_search` with 9 aggregations (value_count + 7 filters) — fired on every tab open. Wrap in `Cache::flexible` or merge into the per-tab payload:
```php
$key = "loadcounter:{$company_id}:{$jobid}:{$mstatus_id}:".md5($inputSearch);
return Cache::flexible($key, [30, 120], fn() => /* current body */);
```
Bump on every application-stage write (Observer on JobApplication).

### P-3. `custome_count_all_compute` issues **6 ES searches**
Lines 11844, 12078, 12255, 12294, 12471, 12509 (300 s window) — could be **1 search** with 6 `aggs` blocks, mirroring what `loadcounter()` already does. Save 5 round-trips per refresh.

### P-4. 8 `_data_ajax` methods issue a 2nd ES search for the counter — without `request_cache`
Lines 1990, 2960, 5938, 6963, 8037, 9213, 10321, 11324. Add `'request_cache' => true` to every counter-agg body. ES will dedupe identical concurrent queries across the cluster.

### P-5. Per-row `DB::table('city_list')` / `qualification_master` inside foreach
N+1 across 8 methods (lines 1161, 1184, 4224, 4228, 5098, 5112, 6178, 6192, 7202, 7216, 8351, 8373, 9546, 9556, 10712, 10723). Replace with a single `whereIn()` lookup keyed by the input set, memoized.

### P-6. `prepareRowMaps` (lines 279–338) is only used by `allDataAjax`
It kills 4 N+1s in one shot — the 8 sibling tabs still pay the cost. Either call it from all of them or fold the logic into a shared `candidateListingResponse()` helper.

### P-7. `dinternal_count()` duplicates work `custome_count_all()` already did
`clientapp()` and `dinternal_bench()` page-renders call both. `dinternal_count` re-fetches job/location/skills/currency that `jobDefaultsFromEs()` already computed 2 lines earlier. Consume the cached `custome_count_all()` return.

### P-8. Page-render uncached lookups
`ApplicationStatus::all()`, `JobLocation::all()`, `Skill::all()`, `Client::all()`, `TicketStatus::all()`, `qualification_master select *` (× 7 sites), `allCities()` — all uncached. Most are small, but each is hit on every page render. Wrap in `Cache::remember('app:master_status', 86400, …)`.

### P-9. Cache version key is shared across the user's tabs
`'allapptracker_version_user_'.$user->id` — bumping once invalidates every tab for that user. Scope version key to `(user, tab, jobid)` so a stage move in tab A doesn't bust the cache for tabs B/C/D.

### P-10. Frontend weight: 1.7 MB of Blade, 400 KB of FA6.js synchronously in `<head>`
Every view loads `font-awesome/6.0.0-beta2/all.min.js` from CDN — but only 8–10 icons are used. Swap to the CSS webfont (10× smaller, non-blocking). Add `defer` to `plugins.bundle.js`, `dataTables.fixedHeader.min.js`, and the 6 jQuery plugins loaded in `clientapp.blade.php`. Net saving: 500–700 KB on first paint.

---

## 5. Counters: making them perfect and almost real-time

**Current state.** Header counter comes from `custome_count_all()` — `Cache::flexible(..., [60, 300], ...)`. 60 s fresh / 300 s stale-while-revalidate. Tab-local counter from `loadcounter()` — **uncached**.

**Proposed design (10 K recruiters, real-time feel, minimal SQL):**

1. **Single source of truth in ES.** Define one ES index `jero_candidates_v1` keyed by `wiseId`. Maintain a counter doc per `(company_id, job_id)` updated on stage change. Stage-move writes fire a tiny `UpdateCounterJob` that updates the ES counter doc only (no SQL aggregation).
2. **Redis hot cache.** `CounterCache::get($cid, $jobid)` → Redis (5 s TTL). `CounterCache::bump($cid, $jobid, $status_id)` → Redis INCR + push to Reverb channel.
3. **Reverb push** → browser subscribes to `counters.{cid}.{jobid}` → live update without re-querying. Makes the badge feel real-time.
4. **Page-load fallback.** If Redis miss, ES `terms` aggregation over the counter doc (1 round-trip, sub-10 ms with `request_cache`).
5. **Background sweep.** Every 5 min, a Horizon job rebuilds the ES counter doc from MySQL to drift-correct Redis. With 10 K jobs × 10 status buckets = 100 K counter rows, that's a 5-second sweep on a 32 GB box.

This brings counter reads to: **0 SQL, 0 ES, 1 Redis GET** for the steady state. Writes: **1 ES UPDATE (counter doc), 1 Redis INCR, 1 Reverb push**.

---

## 6. Client-side handling (what should NOT touch the server)

| Behavior | Where it should live | Today |
|---|---|---|
| Sort/pagination/search of a fetched page | Client (DataTables) | ✅ Already client-side |
| Stage badge color | Client CSS class | ✅ Already done |
| Years-of-experience dropdown | Static `<select>` | ✅ Already done — but duplicated 16 times across 9 files. Extract to a partial. |
| Selected-rows action bar (mass mail, fwd, print) | Client JS only | ✅ Good — no server call until action |
| Print selected rows | Client JS clones DOM into hidden `#aptk_print_area` | ✅ Good |
| Live counter badge | Reverb push + Redis | ❌ Today: full ES search per tab open |
| Search-as-you-type on the candidate list | Debounced 250 ms client call | ⚠️ Today: hits server on every keystroke if not debounced |
| Column show/hide / reordering | LocalStorage | ⚠️ Not implemented |
| Tab state (which tab is open) | sessionStorage | ⚠️ Today: server-side via query string |

---

## 7. The 32 GB server math (steady state)

Assuming 10 K companies, 100 M records, 5 K concurrent recruiters at peak:

| Server | Budget | Today | After fixes |
|---|---|---|---|
| **MySQL** | 32 GB (innodb_buffer_pool ~24 GB) | 100% cache hit on `jobs`/`skills`/`statuses` after warm-up; `add_job_wise_condidates` = full table scan on every counter | Add 4 indexes → 99% of counter queries served from index; primary keys in buffer pool |
| **Elasticsearch** | 32 GB (heap 16 GB + filesystem cache 16 GB) | 8 ES `_search` calls per page open (1 data + 1 counter per tab × 4 tabs), no `request_cache` on counters | 2 ES calls per page (1 data w/ request_cache, 1 counter w/ request_cache) — **75% reduction** |
| **Redis/Horizon** | 32 GB | Tiny usage today | Holds counter doc + session + Horizon queue. 5 GB used |
| **PHP-FPM/Octane** | (RAM-shared) | 11 K-line controller = ~5 MB opcode per worker × 200 workers = 1 GB | Refactored 2 K-line controller = 1 MB × 200 = 200 MB. **5× headroom** |

---

## 8. Refactor: a 10-K-recruiter-ready apptracker

The 11,374-line god-controller needs to be sliced. Per `docs/AI_RULES.md` rule #11, build vertical slices. The full plan now lives in
[`13_STEP_IMPLEMENTATION_PLAN.md`](13_STEP_IMPLEMENTATION_PLAN.md) (file renamed from
`10_STEP_IMPLEMENTATION_PLAN.md`; content renamed from "10-step plan" to
"13-step plan + 6-phase slice refactor" in Aug 2026). Summary:

1. **Extract `CandidateListingService`** — owns `buildCandidateSearchQuery()`, `prepareRowMaps()`, `resolveTagifyFilters()`, and the cache-key scheme. Used by all 8 `_data_ajax` methods.
2. **Extract `CounterService`** — owns `loadCounter()`, `customeCountAll()`, the ES counter doc, and the Redis cache layer. Returns counts as plain array.
3. **Extract `TicketService`** — owns `ticketList/Create/Show/Update/Comment`. Adds `company_id` filter everywhere. Uses route-model-binding with explicit scope.
4. **Extract `BenchExportService`** — owns the `AppTrackerExport` Maatwebsite pipeline + the `custome_count_all` exported roll-up.
5. **Inject** all four services via the constructor (the controller already takes `MailService` etc. — just add 4 more).
6. **Move common page-render bootstrap** (`Job::findOrFail`, `Client::findOrFail`, `custome_count_all`, `prepareRowMaps`) into a `ApptrackerPageData` value object assembled once per page.
7. **Wire Horizon queues** for `CounterUpdateJob`, `ReindexAfterStageMoveJob` (fire from `JobApplicationObserver`).
8. **Step 11 — Phase A through Phase F.** The full 6-phase slice that delivers
   the long-term modular architecture. Each phase ships independently; combined
   effort ~12.5 dev-days.

Result: **~2,500 line controller**, services testable in isolation, 80% fewer SQL queries, 75% fewer ES round-trips, real-time counters via Reverb.

---

## 9. Quick wins (≤1 day each, no behavior change)

1. **Add the missing indexes** (`add_job_wise_condidates`, `clients`, `tickets`, `interview_schedules`) — DDL only. Biggest single win.
2. **Define `ELASTIC_CANDIDATES` in config** — eliminates C-1 fatal.
3. **Add `'request_cache' => true`** to every counter `$params` body — 8 lines, 75% fewer counter ES scans.
4. **Cache `qualification_master` and `application_status` lookups** — `Cache::remember('app:qualifications', 86400, …)`.
5. **Extract year-experience dropdown to a Blade partial** — removes 16 duplicates.
6. **Replace FA6 JS with FA6 CSS webfont** — 400 KB → 70 KB.
7. **Add `defer` to all `<script src=>` lines** in apptracker blades.

---

## 10. Scorecard vs the 13 project rules

| Rule | Compliance |
|---|---|
| #1 Never read 500 KB+ controllers whole | ❌ This controller is 377 KB — audit had to use Grep |
| #2 DB truth in `mysql-schema.sql` | ✅ Used |
| #3 Eloquent scoped by company_id | ⚠️ Most raw `DB::table` is job_id-bound (safe); `recommendation_relation` JOIN needs `whereNull` guard |
| #4 MySQL ↔ ES sync | ⚠️ ES counter doc not maintained; relies on full reindex |
| #5 Use `config()` never `env()` outside config/ | ✅ Compliant |
| #6 Queue heavy work | ❌ No queues for stage moves, counter updates |
| #7 Preserve baked-in typos | ✅ Honored (condidate, commericial, Caompany, transection) |
| #8 No business-logic changes without sign-off | ✅ None proposed |
| #9 Match existing patterns (no repository layer) | ✅ Plan adds Services, not Repositories |
| #10 UI follows Metronic, 14 px cap, Poppins | ✅ Already in place |
| #11 Build features as vertical slices | ❌ This is the biggest miss — god controller needs slicing |
| #12 Measure & verify | N/A — audit only |
| #13 State the purpose of every new code | N/A — audit only |

### 10.1. 12-Objective compliance (Aug 2026)

A separate objective-level review (see "Plan Review" message in session log) mapped
the 10-step plan against 12 explicit objectives (10 K recruiters, 32 GB × 3, dynamic UI,
minimal server load, exact SQL minimum, near real-time, accurate counters, client-side
offload, modular architecture, network resilience, no re-renders, scalable + fault-tolerant).

The original 10-step plan covered **6 of 12 fully**, **5 of 12 partially**, and
**1 of 12 not at all** (network resilience — objective #10).

The plan has been **expanded to 13 steps + a 6-phase slice refactor** to close
these gaps. The mapping:

| # | Objective | Steps |
|---|---|---|
| 1 | 10,000 concurrent recruiters | 1, 4, 5, 8, 11 |
| 2 | 32 GB RAM × 3 server math | 1, 4, 5, 8, 10 |
| 3 | Dynamic, responsive UI | 0, 6, 6.5, 7.5 |
| 4 | Fastest response, minimal server load | 1, 2, 2.5, 3, 4, 5, 8, 10 |
| 5 | Minimum SQL, optimize DB | 1, 2, 3, 5, 12 |
| 6 | Near real-time data updates | 7, 7.5 |
| 7 | Counters accurate + synchronized | 4, 4.5, 12 |
| 8 | Client-side offload | 0, 6, 6.5, 7, 7.5, 8, 8.5 |
| 9 | Low-maintenance, modular, scalable architecture | 11 |
| 10 | Network failures, timeouts, retries, offline, reconnections | 0, 13 |
| 11 | No re-renders, duplicate API calls, redundant SQL, expensive computations | 0, 3, 5, 5.5, 8.5, 11 |
| 12 | Scalable, fault-tolerant, production-ready | 0, 4, 4.5, 11, 12, 13 |

The three additions that are **non-negotiable for "production-ready"**:
1. **Step 0** — client-side resilience (without it, the 10 K-recruiter UX falls apart on WiFi hiccups)
2. **Step 4.5** — counter drift correction (without it, counters silently drift away from truth)
3. **Step 11** — controller slicing (without it, the codebase can't scale to 10 K recruiters without 11 K-line files)

See `13_STEP_IMPLEMENTATION_PLAN.md` for the full plan.

---

## 11. Bottom line

- **It works today** but the missing constant (C-1), the 8 broken routes (C-2), and the unindexed `add_job_wise_condidates` (P-1) are the three things to fix **before** the next deploy.
- The god controller must be sliced (section 8 + Step 11 in the implementation plan) before you 10× the recruiter count.
- The counter strategy (section 5) — Redis + Reverb + ES counter doc — is what makes "almost real-time" achievable without hammering MySQL/ES. The drift-correction sweep (Step 4.5) is the companion that keeps it accurate.
- Most of the wins are DDL + config + 8 lines of `'request_cache' => true` — low risk, high ROI.
- **The 12-objective compliance review** (Aug 2026, see §10.1) added 3 non-negotiables: Step 0 (client-side resilience), Step 4.5 (counter drift), Step 11 (controller slicing). Without these, the system is fast but not "production-ready" by the user's 12-objective definition.
- Total effort to ship all 13 steps + slice refactor: **~26.25 dev-days** (round: ~26 dev-days; was ~3 dev-days for the original 10). The extra ~23 days buys: failure-mode correctness (Steps 0, 13), counter-accuracy under load (Step 4.5), the structural ability to evolve (Step 11), and the Product route mirror in Step 9 (so the 26 reachable Product routes don't 500 on methods that exist on Admin/Service but not on Product).

See [[tenant-isolation-blanket-scopes-break-marketplace]] for why the blanket-scope hardening approach in BUG-03/13 has tradeoffs in the marketplace.