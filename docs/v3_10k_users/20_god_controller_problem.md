# 20 — The God Controller Problem

> **Intent:** Document why `HomeController` and `ServiceCompanyJobApplicationController`
> are the single biggest scalability threat, what they cost in RAM, and exactly what
> to do about it. This is the #1 action item in the entire V3 scale plan.

---

## What Are God Controllers?

```
HomeController                           — 16,633 lines of code
ServiceCompanyJobApplicationController  — ~813 lines of code (but init chain is heavy)
```

These are "god" controllers because they do everything: routing, business logic,
data access, file handling, Elasticsearch sync, payment triggers, notifications,
and more. They were acceptable when the system had 100 users. At 10K, they are
the primary failure point.

---

## How Octane Makes This Worse

In traditional PHP-FPM, each request spawns a fresh process. The god controller
loads, the request serves, the process dies. Memory is always fresh.

In Octane (which JERO uses), workers are **persistent processes**:

```
Worker boots:
  → loads HomeController (16,633 LOC)
  → loads all its use statements (50+ classes)
  → loads all service providers in the init chain
  → keeps everything in RAM for the lifetime of the worker

Worker serves request 1:     [done]  — memory stays
Worker serves request 2:     [done]  — memory stays
Worker serves request 500:   [recycles per max_requests=500]
Worker boots again:           → reloads everything
```

The god controller is loaded **once per worker boot** and stays in RAM.
At 512 MB per worker, a 32 GB app server can only hold **50 workers**.

---

## The RAM Math

```
Without god controller extraction (today):
  HomeController object graph in memory: ~300–400 MB
  + service providers + dependency injection container: ~100 MB
  + PHP runtime overhead: ~50 MB
  = ~450–512 MB per Octane worker

  32 GB server → 50 workers max → 50 concurrent PHP requests max

After extraction (V2 Steps 7 + 16):
  CareerApplyController: ~200 LOC, only its own deps
  ResumeSessionController: ~300 LOC, only upload deps
  Each thin controller object graph: ~80–120 MB
  + shared service providers: ~50 MB
  = ~128–200 MB per Octane worker

  32 GB server → 130–162 workers max → 130–162 concurrent PHP requests max
```

**Extraction gives you 2.5–3× more workers from the same hardware.**

---

## Why It Causes 502 Errors at Scale

```
10K concurrent users → burst of 200 simultaneous PHP requests
50 Octane workers available
→ 150 requests queue behind the 50 active workers
→ Queue depth grows
→ FrankenPHP queue timeout (default: 30s)
→ Client receives 502 Bad Gateway
→ Client retries → queue grows further
→ Cascade failure
```

This is exactly what happened in the V1 `/login` 500 chain — the boot-time
dependency in the god controller init path caused a cascade under moderate load.

---

## The Fix — Vertical Slice Extraction

The V2 implementation plan already defines this in Steps 7 and 16.
The pattern is:

### Before (god controller handles everything):
```php
// routes/web.php — everything goes to HomeController
Route::post('/career-apply', [HomeController::class, 'careerStore']);
Route::post('/job-applications', [ServiceCompanyJobApplicationController::class, 'store']);
```

### After (thin delegating wrappers):
```php
// routes/web.php — routes to thin vertical slice controllers
Route::post('/career-apply', [CareerApplyController::class, 'store']);
Route::post('/job-applications', [ResumeSubmitController::class, 'store']);
```

```php
// app/Http/Controllers/ResumeSubmitController.php (~300 LOC)
// Intent: Thin controller for the V2 resume submit flow.
// Delegates all business logic to ResumeUploadSession service.
// Does NOT load HomeController or its dependency graph.

class ResumeSubmitController extends Controller
{
    public function __construct(
        private ResumeUploadSession $session,
        private ElasticIndexService $elastic,
    ) {}

    public function store(SubmitResumeRequest $request): JsonResponse
    {
        return $this->session->submitWithSession($request->validated());
    }
}
```

---

## What Stays in the God Controller (Do Not Move)

The god controller contains business logic unrelated to resume upload.
Do NOT do a big-bang rewrite. The V2 plan extracts only:

| What to extract | Step | Controller created |
|---|---|---|
| `careerStore` (public career apply) | Step 16 / v2.0.0m | `CareerApplyController` |
| `store` (add candidate + resume submit) | Step 7 / v2.0.0e | `ResumeSubmitController` |

Everything else in `HomeController` and `ServiceCompanyJobApplicationController`
stays untouched until dedicated extraction sprints.

---

## Rollback Safety

```
Feature flag: config('resumes.features.submit_with_session')

ON  → routes to ResumeSubmitController (V2 thin path)
OFF → routes to ServiceCompanyJobApplicationController::store() (V1 legacy path)

Rollback: flip flag OFF → config:cache → 30 seconds
No data migration. No DB change. Zero risk.
```

---

## Verification

After extraction, measure worker RAM in production:
```bash
# Get PID of an Octane worker
ps aux | grep octane | grep -v grep | head -1

# Check its RSS (resident set size in KB)
cat /proc/<PID>/status | grep VmRSS
# Before extraction: ~450,000 kB (450 MB)
# After extraction:  ~150,000 kB (150 MB) ← target
```

If RSS per worker drops below 200 MB, extraction succeeded.
