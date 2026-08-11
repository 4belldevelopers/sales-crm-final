# Find Agency / Market Jobs Listing V2 — Technical Implementation Plan

> **Document Status:** PROPOSED IMPLEMENTATION PLAN ONLY  
> **Target Feature:** Find Agency / Market Jobs Listing Page (`/service_company/jobs/market-jobs/0`)  
> **Target Endpoint:** `jobs.marketJobListV2_ajax` (`/service_company/market-jobs-list-v2-ajax`)  
> **Constraint:** DO NOT IMPLEMENT UNTIL FORMALLY APPROVED BY USER.

---

## 1. V2 Objective

The primary objective of **Find Agency Listing V2** is to transform the Market Jobs Requisition discovery feature into a high-concurrency, sub-50ms API-driven module capable of scaling seamlessly to **10,000+ concurrent recruiters**. 

### Key Performance Goals:
- **Zero MySQL Queries on Read Route:** Eliminate all 40+ residual per-card DB queries inside `MarketJobV2Resource` by bulk pre-fetching bench candidate skill match counts in Elasticsearch.
- **Sub-50ms Response Latency:** Reduce end-to-end server response time from ~350ms to < 45ms.
- **Payload Compression:** Deliver a structured DTO JSON payload of ~5 KB per page of 20 items (95% bandwidth reduction vs 120 KB HTML payloads).
- **10,000+ Concurrency Guarantee:** Ensure Redis read-through caching and zero-query DTO mapping handle 10,000 concurrent recruiters without DB pool exhaustion.

---

## 2. Proposed Architecture & Data Flow

```
[User Browser / Metronic UI]
      │
      ├──> AJAX GET /service_company/market-jobs-list-v2-ajax?page=1
      │      │
      │      ├──> 1. Redis Cache Check: `ats:v2:allmarketjobs:{companyId}:{compVer}:{globVer}:{hash}:v2json`
      │      │      └──> Cache HIT (85%+ Target Rate) -> Return JSON response immediately (< 10ms)
      │      │
      │      └──> 2. Cache MISS -> Execute MarketJobV2Service
      │             ├──> Step A: 1 ES Query on `ats_jobs` (Paginated search, must_not comId = tenant)
      │             ├──> Step B: 1 Bulk ES Query on `ats_job_requests` (`getrRcommendationRelationDataBulk`)
      │             ├──> Step C: 1 Bulk ES Query on `ats_candidates` (`getBenchCandidateCountsBulk`)
      │             └──> Step D: Merge relation data & bench counts into raw hits array
      │
      ├──> 3. MarketJobV2Resource Collection Map (0 DB queries, 0 ES queries)
      │      └──> Standardizes field format, formats salary currencies, outputs DTO payload
      │
      ├──> 4. Redis Cache Write: cache()->put($key, $dtoPayload, 15 mins)
      │
      └──> 5. JS Client-Side Renderer (`renderMarketJobCard`) injects cards into DOM
```

---

## 3. Query & N+1 Removal Strategy

### 3.1 Bulk Loading Bench Candidate Counts
- **Problem:** `MarketJobV2Resource` calls `GeneralMethod::benchCandidateCountSkillBase($row->id)` item-by-item, triggering 20 DB queries (`gdFilterData`) + 20 ES queries (`ats_job_applications`) per AJAX request.
- **Solution:** Add `getBenchCandidateCountsBulk(int $companyId, array $jobHits)` in `MarketJobV2Service`. This method collects skill sets for all 20 jobs on the current page, builds a single Elasticsearch `msearch` query against `ats_candidates` / `ats_job_applications`, and returns an associative map `[job_id => match_count]`.
- **Risk:** Minor CPU overhead in building the `msearch` request array in PHP.
- **Testing:** Unit test `getBenchCandidateCountsBulk` to confirm that returned match counts match item-by-item query outputs.

### 3.2 Elimination of Dead MySQL Queries
- **Problem:** Unused SQL query construction in `dataCnt()` and unused `$subQuery` array when `cus_search` is passed.
- **Solution:** Clean up `dataCnt()` to only execute active Elasticsearch aggregation queries and remove the unused `$subQuery` code block.
- **Risk:** Zero risk; verified no downstream references.
- **Testing:** Verify initial page render metrics and console outputs.

---

## 4. Index & Search Strategy

### 4.1 Elasticsearch Index Maintenance
- Index `ats_jobs` schema confirmed optimal with nested mapping on `job_metronics_skills`:
  ```json
  "job_metronics_skills": {
    "type": "nested",
    "properties": {
      "skill_name": { "type": "keyword" }
    }
  }
  ```
- **Sorting Indexes:** Ensure `id`, `max_salary`, `client_name`, and `created_at` fields in `ats_jobs` have doc_values enabled for sub-10ms sorting.

---

## 5. Cache & Invalidation Architecture

### 5.1 Multi-Layered Redis Caching
- **Tenant Cache Key:** `ats:v2:allmarketjobs:{companyId}:{companyVersion}:{globalVersion}:{hash}:v2json`
- **Company Version Key:** `allmarketjobs_version_company_{companyId}` (incremented on company-specific actions such as applying to a job or sending a bench request).
- **Global Version Key:** `allmarketjobs_global_version` (incremented when new market jobs are published globally).
- **TTL:** 15 minutes.

---

## 6. Business Logic Preservation Checklist

The following rules MUST remain 100% intact:
- [x] **Tenant Separation:** `comId != auth()->user()->company_id` filter strictly enforced in `ats_jobs` search query.
- [x] **Marketplace Status:** Public marketplace jobs (`public_market_status = 2`) allow agency partnership applications.
- [x] **Vendor Limit Cap:** `vendor_added_count >= 20` or `public_market_status = 6` transitions action button to `vendor_fulfilled` or `vendor_limit`.
- [x] **Closed / Hold Jobs Check:** Jobs with `status = 'inactive'` or `'hold'` reject invitations and applications.
- [x] **Salary & Currency Conversions:** Multi-mode salary displays (Yearly, Monthly, Hourly) and currency conversions to INR (`GeneralMethod::convertToINR`) preserved.

---

## 7. Server & 10,000+ Concurrency Strategy

### 7.1 Architecture Limits & Capacity
- **Target Concurrency:** 10,000+ active recruiters.
- **Strategy:**
  1. **Zero DB Access on Read Route:** 100% of read traffic served from DragonflyDB / Redis or Elasticsearch.
  2. **Fast Response Times:** Redis cache hits < 5ms; Elasticsearch bulk queries < 35ms.
  3. **FrankenPHP Worker Scaling:** Octane worker pool set to keep static memory warmed, handling 5,000+ req/sec per node.

---

## 8. Detailed Recommendation Summary

Every recommendation follows the mandatory **Problem → Solution → Risk → Testing** format:

### Recommendation 1: Implement Bulk Bench Candidate Count Method
- **Problem:** Per-card N+1 queries in `MarketJobV2Resource` execute 40 DB/ES queries per page load.
- **Solution:** Implement `MarketJobV2Service::getBenchCandidateCountsBulk($companyId, $jobHits)` using an Elasticsearch `msearch` query.
- **Risk:** Complex ES `msearch` array structure.
- **Testing:** Verify bench candidate count match accuracy against legacy single-item helper.

### Recommendation 2: Pure DTO Transformation in MarketJobV2Resource
- **Problem:** Resource class executes database and search engine calls during array serialization.
- **Solution:** Convert `MarketJobV2Resource` into a pure, stateless DTO transformer that only formats data already pre-loaded into `$row`.
- **Risk:** Low risk.
- **Testing:** Verify resource output JSON schema against API contracts.

### Recommendation 3: Global Country Master Caching
- **Problem:** `countries` table queried on every initial page load (`marketJobs`).
- **Solution:** Cache country names globally in Redis for 24 hours (`cache()->remember('global_countries_list', ...)`).
- **Risk:** Very low risk; country names are static reference data.
- **Testing:** Verify country filter dropdown population in UI.

---

## 9. Phase-Wise Implementation Roadmap

| Phase | Phase Name | Key Tasks & Scope | Estimated Work | Risk Level | Validation / Testing Criteria |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | Service Layer Bulk Optimization | Add `getBenchCandidateCountsBulk` to `MarketJobV2Service` using ES `msearch`; attach counts to raw job hits before resource mapping | 1 Day | Low | Verify 0 DB queries executed during AJAX fetch |
| **Phase 2** | DTO Resource Cleanup | Refactor `MarketJobV2Resource` into pure DTO transformer; eliminate internal calls to `GeneralMethod::benchCandidateCountSkillBase` | 0.5 Days | Low | Compare JSON output schema parity with current V2 API |
| **Phase 3** | Dead Code & Initial Load Cleanup | Cache `countries` list in `marketJobs`; remove dead SQL builder in `dataCnt()` and unused `$subQuery` | 0.5 Days | Very Low | Confirm `marketJobs` initial load executes 0 unneeded MySQL queries |
| **Phase 4** | Redis Cache & Invalidation Hardening | Verify tenant/global version invalidation hooks on job creation, application submission, and bench request sending | 0.5 Days | Low | Test cache hit rate under rapid filter toggle scenarios |
| **Phase 5** | Concurrency Testing & Verification | Run concurrency tests, verify sub-50ms latency, validate UI parity on 1366x768 to 4K displays | 1 Day | Low | Execute `VERIFY.md` checks; confirm 0 breaking changes |

---

## 10. Rollout & Rollback Plan

### Rollout Plan:
1. Deploy `MarketJobV2Service` bulk bench count optimization to staging environment.
2. Verify zero DB queries during AJAX listing requests.
3. Validate client-side JavaScript rendering on all browser screen sizes.
4. Promote to production under Octane / FrankenPHP.

### Rollback Plan:
1. If any edge-case discrepancy occurs, revert `marketJobListV2_ajax` route to point back to single-item fallback helper.
2. Re-enable fallback cached response keys.
3. Flushing Redis cache key pattern `ats:v2:allmarketjobs:*` restores immediate state.

---
