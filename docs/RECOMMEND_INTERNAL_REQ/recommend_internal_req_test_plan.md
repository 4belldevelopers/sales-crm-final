# TEST PLAN & VERIFICATION SPECIFICATION
## Service Company → Recommend Internal Request (Bench Candidates)

> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/recommend_internal_req/6`  
> **Target Route:** `service_company.bench-candidates.recommend_internal_req`  
> **Document Status:** AUTHORITATIVE TEST SPECIFICATION  
> **Date:** October 12, 2026  

---

## 1. Test Strategy & Scope

This document specifies the verification plan to ensure 100% correctness, strict business-logic preservation, robust tenant isolation, concurrency protection, outbox delivery guarantees, and load performance for the `recommend_internal_req` module.

Testing covers:
1. Automated Feature Tests (PHPUnit / Pest in Laravel).
2. Concurrency & Idempotency Tests (simultaneous requests and replay attacks).
3. Failure & Recovery Tests (database rollback, worker retry, Reverb reconnection).
4. Load & Capacity Benchmarks (simulating 1,000, 5,000, and 10,000 concurrent user sessions).

---

## 2. Test Matrix (20 Mandatory Scenarios)

| # | Test Scenario | Expected Outcome | Verification Method |
| :--- | :--- | :--- | :--- |
| **1** | Authorized service company user views Candidate 6 | Eligible internal job requisitions (matching skills) are displayed with accurate counts | Automated Feature Test |
| **2** | Unauthenticated user navigates to URL | Redirected to login with HTTP 302 | Automated HTTP Test |
| **3** | Tenant isolation enforcement | User from Company A cannot view or submit candidates belonging to Company B | Automated Tenant Isolation Test |
| **4** | Candidate 6 / Job 6 integrity | Candidate 6 (*Bhupendra Char*) is evaluated against active jobs; Job 6 (*Junior Java Dev*) renders accurately | DB & View Verification |
| **5** | Status transitions | Submitting candidate transitions row state from "Submit Candidate" to "Request Sent" | DOM & DB Assertion |
| **6** | Concurrent duplicate submissions | 10 simultaneous POST/GET requests for Candidate 6 and Job 7 result in exactly 1 row in `rec_candidate_assign` | Concurrency Test Script |
| **7** | Idempotent replay | Sending the same request with the same idempotency key returns the prior HTTP 200 payload without re-inserting | Idempotency Test |
| **8** | Idempotency payload mismatch | Reusing an existing idempotency key with a different `job_id` returns HTTP 409 Conflict | Idempotency Conflict Test |
| **9** | Transaction failure rollback | Exception inside transaction rolls back `rec_candidate_assign`, coins, and creates ZERO outbox events | Rollback Assertion Test |
| **10**| Event publication crash recovery | Database commits but worker crashes; worker restart claims and processes outbox event via `SKIP LOCKED` | Worker Recovery Test |
| **11**| Duplicate event delivery | Browser receiving duplicate `bench.request.created` event does not duplicate DOM badges or throw errors | Frontend Unit Assertion |
| **12**| Out-of-order event protection | Event with older timestamp or aggregate version does not overwrite newer state in store | State Version Test |
| **13**| WebSocket reconnect recovery | Client disconnects, reconnects; recovers missed status transitions cleanly | Reconnection Test |
| **14**| Stale cache authorization | Tampered client cache cannot bypass server-side tenant or status validation | Security Assertion Test |
| **15**| MySQL degradation | Database timeout produces user-friendly error response without leaking credentials or raw SQL | Fault Injection Test |
| **16**| Elasticsearch degradation | Elasticsearch outage does not block MySQL transactional assignment; search degrades gracefully | ES Fallback Test |
| **17**| Queue worker retry | Failed email or ES sync retries with exponential backoff up to 3 times before dead-lettering | Queue Retry Test |
| **18**| Notification preservation | Existing email dispatch (Mail Type 21) is queued and sent with correct body parameters | Mail Queue Test |
| **19**| Multi-tab consistency | Candidate submitted in Tab 1 updates button in Tab 2 in <50ms via Reverb broadcast | Multi-tab Reverb Test |
| **20**| Performance targets | Initial page load <120ms (cold) / <5ms (warm); AJAX fetch <100ms (cold) / <3ms (warm) | Automated Latency Benchmark |

---

## 3. Test Implementation Artifacts

1. `tests/Feature/BenchCandidateRecommendInternalReqTest.php`:
   Contains full automated test suite verifying:
   - Route accessibility and permission checks.
   - Cache hit/miss query counts.
   - Idempotency key persistence and replay.
   - Transactional outbox event logging.
   - Multi-tenant boundary isolation.
2. `scratch/load_test_10k_simulation.php`:
   Simulates 1K, 5K, and 10K concurrent requests measuring p50, p95, p99 latencies, cache hit ratios, and throughput.
