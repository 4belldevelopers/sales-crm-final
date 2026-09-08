# Find Talent Listing V2 — Implementation Plan

> **Document Type:** Technical Specification & V2 Implementation Roadmap  
> **Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/find-talents`  
> **Status:** PROPOSED PLAN — WAITING FOR USER APPROVAL (DO NOT IMPLEMENT)  
> **Date:** August 11, 2026  

---

## 1. V2 Objective & Vision

The objective of **Find Talent Listing V2** is to transform the B2B talent marketplace candidate discovery engine into a zero-database-hit, high-concurrency architecture capable of serving **10,000+ concurrent recruiters** with sub-50ms latency.

### Key V2 Targets:
* **0 MySQL Read Queries** during AJAX talent filtering and searching.
* **1 Elasticsearch Call** per search request (eliminating dual search/agg calls).
* **0 Painless Script Evaluations** during sorting and filtering.
* **< 8 KB JSON Payload Size** (85%+ reduction vs legacy HTML chunks).
* **< 50ms Total Server Response Time** under 10,000 concurrent recruiter sessions.

---

## 2. Proposed V2 Architecture

```
User Browser / Recruiters
  │
  ├── 1. Initial Page Load (GET /service_company/find-talents)
  │      └── Controller: ServiceCompanyAppTrackerController@findTalentV2
  │             ├── Reads company job context & returns lightweight Blade view shell
  │             └── Static dropdowns cached in Redis (Cities, Qualifications)
  │
  └── 2. Instant Filter / Search (GET /api/v2/service-company/find-talents)
         └── Controller: Api/V2/ServiceCompanyFindTalentController@index
                ├── Read from Redis Cache Key: ats:v2:findtalents:{user_id}:{hash}
                │      └── Cache HIT ──► Return Pure JSON Payload (< 50ms)
                │
                └── Cache MISS:
                       ├── Single ES Query on ELASTIC_CANDIDATES_BENCH
                       ├── Pure JSON Response (Candidates + Meta + Pagination)
                       ├── Async Cache Write to Redis
                       └── Client-side UI Rendering (Vanilla JS / Vue Component)
```

---

## 3. Query Optimization & N+1 Removal Strategy

### 3.1 Total Removal of N+1 MySQL Queries
* **Problem:** Blade template executes `DB::table('bench_candidate_skill_experience')` per candidate row inside `@foreach` loop.
* **Solution:** Extend `ELASTIC_CANDIDATES_BENCH` Elasticsearch mapping to include nested candidate skill ratings:
  ```json
  "candidate_skills": [
    { "skill_key": "php", "rating": 4, "total_months": 36 },
    { "skill_key": "laravel", "rating": 5, "total_months": 48 }
  ]
  ```
* **Result:** All star-rated skills are served directly from Elasticsearch document source. **MySQL query count drops from 20 to 0 per AJAX load.**
* **Risk:** Low. Requires backfill indexing script for existing candidates.
* **Testing:** Run Laravel Telescope or DB query logger during AJAX search and verify zero SQL queries executed.

---

## 4. Elasticsearch & Sorting Optimization

### 4.1 Elimination of Painless Sorting Scripts
* **Problem:** Sorting by notice period date or ads switch uses runtime Painless scripts (`LocalDate.parse()`), locking ES CPU threads.
* **Solution:** Pre-compute and index sorting fields directly into Elasticsearch during candidate index/update:
  1. `ads_switch_order` (Integer: `0` for Yes, `1` for No).
  2. `np_epoch_day` (Long: Epoch day integer computed at index time).
  3. `np_is_immediate` (Boolean: `true` / `false`).
* **Result:** Replaces CPU-bound Painless scripts with native, lightning-fast Lucene integer doc-values field sorting.
* **Risk:** Medium. Pipeline re-indexing required.
* **Testing:** Compare ES query latency using Elasticsearch Profile API before and after pre-computation.

---

## 5. API Payload & Client-Side Rendering Strategy

### 5.1 Transition from Rendered HTML to Pure JSON
* **Problem:** Controller returns raw HTML chunks (`html`, `pagination`) inflating payload size to ~60-80 KB.
* **Solution:** Return pure JSON array structure:
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": 1042,
        "full_name": "John Doe",
        "current_designation": "Senior Laravel Developer",
        "total_exp": "5.5",
        "work_mode": "Hybrid",
        "city_name": "Bangalore",
        "country_name": "India",
        "bill_rate_amount": 1500000,
        "bill_rate_currency": "INR",
        "bill_rate_mode": "Yearly",
        "outsourcing_type": "Full-time",
        "status_label": "Active",
        "candidate_skills": [
          { "skill_key": "PHP", "rating": 5 },
          { "skill_key": "Laravel", "rating": 4 }
        ],
        "count_for_add": 1,
        "can_add": true
      }
    ],
    "meta": {
      "total": 142,
      "page": 1,
      "per_page": 20,
      "last_page": 8
    }
  }
  ```
* **Risk:** Low. Requires updating frontend JavaScript renderer.
* **Testing:** Validate JSON structure and verify payload size is <8 KB.

---

## 6. Server & 10,000+ Concurrency Scaling Strategy

### 6.1 Redis / DragonflyDB Caching & Stampede Protection
* **Cache Key:** `ats:v2:findtalents:comp_{company_id}:usr_{user_id}:{hash}`
* **Locking:** Implement Redis probabilistic early expiration or mutex lock (`Cache::lock()`) to prevent cache stampedes when candidate version keys invalidate.

### 6.2 Asynchronous Candidate Indexing
* **Queue Strategy:** Move candidate ES indexing logic out of HTTP request lifecycles into dedicated background queue jobs (`SyncCandidateToElasticJob`) handled by Laravel Horizon worker pools.

---

## 7. Business Logic Preservation Assurance Matrix

| Business Rule | Legacy Implementation | V2 Implementation Guarantee | Verification Method |
| :--- | :--- | :--- | :--- |
| **Marketplace Tenant Isolation** | `must_not: company_id = $company_id` | Preserved in ES `bool.must_not` query filter | Automated test confirming zero own-company candidates returned |
| **Active Candidate Requirement** | `term: status = 2` | Preserved in ES `bool.filter` query | Automated test confirming inactive candidates hidden |
| **Submission Limit Check** | `countforadd >= 3` disables button | Computed in API resource (`can_add = count_for_add < 3`) | Unit test for `can_add` flag derivation |
| **Multi-Currency Conversion** | `GeneralMethod::convertToINR` | Handled seamlessly at query parameter normalization stage | Verification across USD, EUR, INR salary searches |

---

## 8. Recommendations Summary (Problem → Solution → Risk → Testing)

1. **N+1 Candidate Skills Query:**
   * **Problem:** Blade template runs 20 MySQL queries per page load for candidate skill ratings.
   * **Solution:** Index candidate skill experiences as nested objects in Elasticsearch index.
   * **Risk:** Low (Requires index pipeline update).
   * **Testing:** Telescope query count check = 0 MySQL queries.

2. **Painless Sorting Script Overhead:**
   * **Problem:** Java date parsing inside ES Painless scripts during sort consumes high CPU.
   * **Solution:** Pre-compute epoch days and sort orders at index time.
   * **Risk:** Medium (Requires data migration backfill).
   * **Testing:** ES Profile API showing 0ms script execution time.

3. **Dual ES Search Calls:**
   * **Problem:** Separate search and cardinality agg queries run per AJAX request.
   * **Solution:** Single search call utilizing `track_total_hits` and single-pass aggregation.
   * **Risk:** Low.
   * **Testing:** ES client request counter = 1 per search.

4. **HTML Payload Bloat:**
   * **Problem:** 60-80 KB HTML payload over AJAX degrades network performance.
   * **Solution:** Pure JSON REST API with client-side DOM rendering.
   * **Risk:** Low.
   * **Testing:** Network tab payload inspection (<8 KB).

5. **Synchronous Writes:**
   * **Problem:** Candidate profile edits synchronously wait for ES index response.
   * **Solution:** Dispatch `SyncCandidateToElasticJob` to Redis background queue.
   * **Risk:** Low.
   * **Testing:** Horizon queue processing verification.

---

## 9. Phase-Wise Implementation Plan (V2 Roadmap)

> **IMPORTANT:** Implementation must proceed sequentially by phases upon user approval.

| Phase | Module / Area | Specific Tasks & Deliverables | Dependencies | Risk Level | Verification Criteria |
| :---: | :--- | :--- | :--- | :---: | :--- |
| **Phase 1** | **Elasticsearch Mapping & Pipeline** | • Update `ELASTIC_CANDIDATES_BENCH` schema mapping to include nested `candidate_skills` array<br>• Add pre-computed integer fields: `np_epoch_day`, `ads_switch_order`, `np_is_immediate`<br>• Create CLI artisan command `php artisan bench:reindex-es-v2` to backfill existing candidates | Access to ES cluster schema | Medium | ES mapping inspection & 100% document backfill validation |
| **Phase 2** | **Async Queue Indexing** | • Create `SyncCandidateToElasticJob` queue job<br>• Attach queue triggers to candidate update/create Eloquent events<br>• Configure Horizon supervisor worker pool for candidate index queues | Phase 1 | Low | Verify queue job execution in Horizon dashboard |
| **Phase 3** | **V2 API Controller & Service** | • Create `ServiceCompanyFindTalentV2Controller@index`<br>• Implement single-query ES search wrapper<br>• Implement `ats:v2:findtalents` Redis cache layer<br>• Return structured clean JSON payload | Phase 1, Phase 2 | Low | 100% PHPUnit API test pass & 0 MySQL queries verified |
| **Phase 4** | **Client-Side Frontend V2** | • Refactor `find-talent.blade.php` to include lightweight JS template renderer<br>• Replace HTML innerHTML AJAX response handlers with JSON array parser<br>• Preserve all Metronic styling, badges, tooltips, and action modals | Phase 3 | Medium | Visual UX parity check & payload size <8KB confirmation |
| **Phase 5** | **Testing & Concurrency Verification** | • Run regression test suite covering all 9 filter combinations<br>• Perform load testing with Apache JMeter / k6 simulating 10,000 recruiters<br>• Monitor CPU, RAM, Redis, and ES thread pools during stress run | Phase 4 | High | <50ms response time @ 1,000 req/sec under zero errors |
| **Phase 6** | **Production Rollout & Monitoring** | • Feature-flag rollout (`find_talent_v2_enabled`) for tenant company groups<br>• Setup Sentry & Prometheus alerting for latency spikes<br>• Full cutover and deprecation of legacy V1 endpoint | Phase 5 | Low | 0 error rate & stable server load across 24h cycle |

---

## 10. Rollback & Safety Plan

* **Feature Flag Protection:** V2 endpoint will be gated behind configuration flag `config('features.find_talent_v2')`.
* **Instant Rollback:** If latency spikes or unexpected behavior occurs in production, setting `FIND_TALENT_V2_ENABLED=false` in `.env` instantly routes recruiters back to the legacy V1 implementation without downtime or code deployment.

---

> **End of V2 Implementation Plan**  
> **STATUS: WAITING FOR USER APPROVAL BEFORE IMPLEMENTATION**
