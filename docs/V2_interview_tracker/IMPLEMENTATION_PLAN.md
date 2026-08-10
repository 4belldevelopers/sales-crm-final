# V2 Performance Implementation Plan: Interview Schedule Listing Page (`V2_interview_tracker`)

## Objective & Performance Targets

Transform the Interview Schedule Listing Page into a ultra-performant, zero-N+1, highly scalable engine capable of handling **10,000+ concurrent recruiters/users** while preserving 100% of existing business rules, display data, and permissions.

### Performance SLA Checklist
- [x] **Remove unnecessary database queries**: Reduce DB query count per page render from 80+ to 0–3 queries.
- [x] **Eliminate N+1 queries**: Batch pre-fetch all relational lookup data (follow-up status, feedback result/recommendation, SPOC person, client company name, captain permissions) prior to rendering the Blade template.
- [x] **Deduplicate Search Service execution**: Execute Elasticsearch search ONCE per cache-miss request.
- [x] **Filter & Tab Integration**: Fully map UI tabs (`all`, `today`, `upcoming`, `pending_feedback`, `selected`, `rejected`, `rescheduled`) and dropdown filters (`stage_filter`, `status_filter`, `owner_filter`, `vendor_filter`, `mode_filter`) into ES Boolean queries.
- [x] **Cache Key Granularity**: Prevent cache collision across tabs and dropdown filters in DragonflyDB / Redis cache.
- [x] **Clean Code Standards**: Remove 380 lines of unreachable legacy SQL code with memory-heavy `count($query->get())` logic. Add comprehensive docblock documentation per project standard (`CLAUDE.md` / `AGENTS.md`).

---

## Technical Architecture & Design

```
                                  V2 DATA FLOW & HYDRATION
                                 ==========================

  HTTP Request: GET /service_company/interview-schedule/data_ajax
  (Params: page, size, tab, search, stage_filter, status_filter, owner_filter, mode_filter...)
                                      │
                                      ▼
                        ┌───────────────────────────┐
                        │ Check Redis / DragonflyDB │
                        └─────────────┬─────────────┘
                                      │
                         ┌────────────┴────────────┐
                         ▼                         ▼
                    [ Cache Hit ]           [ Cache Miss ]
                         │                         │
                         ▼                         ▼
                  Return JSON (<5ms)       1. Build ES Query (Must + Filter)
                                           2. Search ES (Single Call)
                                           3. Extract IDs from Hits
                                           4. Batch Pre-fetch Lookup Maps:
                                              • $followupMap (1 query)
                                              • $feedbackMap (1 query)
                                              • $spokpersonMap (1 query)
                                              • $clientCompanyMap (in-memory / batch)
                                              • $userCaptainMap (1 query)
                                           5. Render Blade with Hydrated Maps
                                           6. Store in Redis Cache
                                           7. Return JSON (<75ms)
```

---

## Detailed Step-by-Step Implementation Steps

### Phase 1: Controller Optimization (`InterviewScheduleController.php`)

#### Step 1.1: Deduplicate Elasticsearch Query & Fix Cache Keys
- Refactor `data_ajax` in `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php`:
  ```php
  // Check index existence FIRST
  if (!$this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
      return response()->json([
          'html' => view('service_company.ajax_pages.interview-tracker-ajax', ['data' => []])->render(),
          'total' => 0, 'from' => 0, 'to' => 0, 'pagination' => '', 'stats' => []
      ]);
  }
  
  // Single ES search execution
  $response = $this->elasticService->getClient()->search($params);
  $total = $response['hits']['total']['value'] ?? 0;
  $data = array_map(fn($hit) => $hit['_source'], $response['hits']['hits'] ?? []);
  ```
- Expand `$cachePayload` to include `tab`, `stage_filter`, `status_filter`, `owner_filter`, `vendor_filter`, `mode_filter`.

#### Step 1.2: Implement Full ES Query Builder for Tabs & Dropdown Filters
- Map incoming `tab` parameter:
  - `today`: Schedule date range covering start to end of current day.
  - `upcoming`: Schedule date >= start of tomorrow.
  - `pending_feedback`: Schedule date <= 1 hour ago AND (available_status is 0/null OR feedback is 5).
  - `selected`: `feedback` = 1 OR `interview_result` = 'selected'.
  - `rejected`: `feedback` = 3 OR `interview_result` = 'rejected' OR `status` = 'rejected'.
  - `rescheduled`: `available_status` = 3 OR `interview_result` = 'rescheduled'.
- Map dropdown filters (`stage_filter`, `status_filter`, `owner_filter`, `vendor_filter`, `mode_filter`) into ES `must` / `filter` term matches.

#### Step 1.3: Create Batch Hydration Engine (`prepareViewMaps`)
- Implement private method `prepareViewMaps(array $hits, int $companyId, int $userId)`:
  1. **Collect Hit Identifiers**:
     ```php
     $interviewIds = array_column($hits, 'id');
     $jobIds       = array_unique(array_filter(array_column($hits, 'jobId')));
     $jobWiseIds   = array_unique(array_filter(array_column($hits, 'wiseID')));
     ```
  2. **Batch Query 1 — Follow-up Statuses**:
     ```php
     $followupMap = DB::table('interview_schedules')
         ->whereIn('id', $interviewIds)
         ->get(['id', 'followup_status', 'followup_status_at'])
         ->keyBy('id');
     ```
  3. **Batch Query 2 — Latest Feedback & Overall Recommendation**:
     ```php
     // Fetch latest feedback row per interview in 1 query using max id or orderBy
     $feedbackMap = DB::table('interview_feedback')
         ->whereIn('interview_id', $interviewIds)
         ->orderBy('id', 'desc')
         ->get()
         ->unique('interview_id')
         ->keyBy('interview_id');
     ```
  4. **Batch Query 3 — SPOC / Source Persons**:
     ```php
     $spokpersonMap = DB::table('job_assign')
         ->join('users', 'users.id', '=', 'job_assign.spok_user_id')
         ->whereIn('job_assign.job_id', $jobIds)
         ->select('job_assign.job_id', 'job_assign.company_id', 'users.name')
         ->get()
         ->keyBy(fn($item) => $item->job_id . '_' . $item->company_id);
     ```
  5. **Batch Query 4 — Current User Captain Permissions**:
     ```php
     $userCaptainJobIds = DB::table('job_assign')
         ->whereIn('job_id', $jobIds)
         ->where('spok_user_id', $userId)
         ->pluck('job_id')
         ->flip()
         ->all(); // constant time O(1) isset check
     ```
  6. **Batch Query 5 — Client Company Names**:
     ```php
     $clientCompanyMap = DB::table('jobs')
         ->join('clients', 'clients.id', '=', 'jobs.client_id')
         ->whereIn('jobs.id', $jobIds)
         ->pluck('clients.name', 'jobs.id')
         ->all();
     ```

#### Step 1.4: Dead Code Cleanup
- Remove unreachable lines 402–788 from `InterviewScheduleController.php`.

---

### Phase 2: Blade View N+1 Elimination (`interview-tracker-ajax.blade.php`)

#### Step 2.1: Update Blade Variables & Hydrated Map Consumption
- Update `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` to accept pre-hydrated lookup maps:
  - `$followupMap`
  - `$feedbackMap`
  - `$spokpersonMap`
  - `$clientCompanyMap`
  - `$userCaptainJobIds`

#### Step 2.2: Refactor Direct DB Calls inside Row Loop
1. **Follow-up Status**:
   - Replace top `DB::table('interview_schedules')->whereIn(...)` query with `$followupMap->get($row->id)`.
2. **Client Company Name**:
   - Replace `GeneralMethod::companyDetailsByClient($row->job_id)` with `$clientCompanyMap[$row->job_id] ?? $row->client_name`.
3. **SPOC / Source Person**:
   - Replace `DB::table('job_assign')->select('users.name')...` with `$spokpersonMap[$row->jobId . '_' . $row->ClientComID]->name ?? $row->posted_by`.
4. **Interview Feedback**:
   - Replace `DB::table('interview_feedback')->where(...)` with `$feedbackMap->get($row->id)`.
5. **Feedback Permission Calculation**:
   - Replace `DB::table('job_assign')->where(...)->count()` with `isset($userCaptainJobIds[$row->jobId])`.

---

## Verification & Scalability Validation

1. **Query Count Verification**:
   - Enable `DB::enableQueryLog()` during test request to `data_ajax`.
   - Assert total database query count **<= 4 SQL queries**.
2. **Feature Parity Audit**:
   - Verify every UI badge, follow-up dropdown, feedback result button, candidate avatar color, meeting link popup, and permissions match original functionality exactly.
3. **Pint / Code Documentation Compliance**:
   - Verify all modified files adhere to PSR-12/Pint standards and include intent comments per `docs/CODE_DOCUMENTATION_STANDARD.md`.
