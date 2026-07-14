# ADR 0002 — Redis + Horizon for queues, cache, and sessions

**Status:** Active (observed) · **[INFERRED] rationale**

## Decision
Use Redis as the backend for queues (`QUEUE_CONNECTION=redis`, custom table
`custom_queue_jobs`), cache (`CACHE_STORE=redis`), and sessions
(`SESSION_DRIVER=redis`). Manage workers with **Laravel Horizon** (`config/horizon.php`).

## Reason (inferred)
- Heavy async work (resume→S3 upload, ES indexing, mail/mass-mail, bulk import,
  candidate-request processing) must run off the request path.
- Horizon gives a dashboard, retries, and metrics; Redis gives fast cache/sessions
  suitable for Octane's multi-worker model.

## Alternatives (inferred)
- **Database queue** — simpler, but slower and contends with app DB.
- **SQS** — managed/scalable, but more latency and AWS coupling.
- **File/array cache, DB sessions** — unsuitable for multi-worker Octane.

## Tradeoffs
- ➕ Fast, observable, retryable background processing.
- ➖ Redis is a single point of failure for queue+cache+session simultaneously.
- ➖ **Single `default` queue** currently (no job-class isolation) — see
  [PERFORMANCE_BUDGET.md](../PERFORMANCE_BUDGET.md).

## Consequences
Dispatch all heavy work as Jobs. Consider per-purpose queues + supervisors. Monitor
Horizon for wait time and failed jobs (failed ES index jobs = data drift).
