# Testing Guide — How to Test This App

> **Reality:** `tests/` currently holds only default Breeze/Laravel scaffolding — the ATS
> business logic (matching, billing, ES sync, tenancy) is **untested**. This is the #1
> regression risk ([BAD_PATTERNS.md](BAD_PATTERNS.md)). Rule #11 requires a test per
> feature slice; this doc shows how to write meaningful tests for *this* codebase.

Stack: PHPUnit 11 (`phpunit.xml`), Laravel test helpers, `fakerphp/faker`, Mockery.
Run: `php artisan test` · `php artisan test --filter=<Name>`.

---

## 1. What to test (priority for an ATS at scale)
1. **Tenant isolation** — the most important tests here. Tenant A must never read/write
   Tenant B's data. Guards the leak risk ([SEC-02](SECURITY.md)).
2. **Business logic** — candidate matching/`skill_match`, status pipeline transitions,
   billing/coins, package gating.
3. **MySQL→ES sync** — writing an entity produces the right ES document.
4. **Validation** — FormRequests accept/reject correctly.
5. **AuthZ** — RBAC + object-level (no IDOR on `{id}` routes).
6. **Webhooks** — signature verification + idempotency.

## 2. Test data: factories (build these as you go)
Most models lack factories — add them under `database/factories/` for the entities you
touch. ⚠️ Schema is in the live DB, so a test DB must mirror it (see §5).
```php
// CompanyFactory — minimal valid tenant for tests.
public function definition(): array {
    return ['name' => fake()->company(), 'status' => 'active' /* + required cols */];
}
```
> Confirm required columns via `php artisan db:table <t>` before writing the factory.

## 3. Pattern: tenant-isolation test (copy this)
```php
/** A user from company A must not see company B's candidates. */
public function test_candidates_are_tenant_isolated(): void
{
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $mine   = JobApplication::factory()->for($companyA)->create();
    $theirs = JobApplication::factory()->for($companyB)->create();

    $this->actingAs(User::factory()->for($companyA)->create());

    $resp = $this->get(route('service_company.cv-tracker.data')); // the *_ajax route
    $resp->assertOk();
    $resp->assertSee($mine->full_name);
    $resp->assertDontSee($theirs->full_name);   // ← the critical assertion
}
```
Add the equivalent for raw-query endpoints — those bypass the global scope, so they're the
highest-risk paths.

## 4. Pattern: MySQL→Elasticsearch sync
Don't hit a real ES cluster in unit tests — **mock the ES boundary** (`ElasticService` /
`Http::fake()`) and assert the **document shape** the service would send.
```php
public function test_saving_candidate_builds_expected_es_document(): void
{
    Http::fake(); // or mock ElasticService
    $app = JobApplication::factory()->create(['skills' => 'php,laravel']);
    app(\App\Services\ElasticCandidatesIndex::class)->reindex($app->id);
    Http::assertSent(fn ($r) => str_contains($r->url(), config('...candidates index'))
        && $r['skills'] === 'php,laravel');
}
```
For jobs/observers, assert the job is dispatched: `Queue::fake()` + `Queue::assertPushed(...)`.

## 5. Test environment (important — schema lives in the live DB)
Because there are no migrations for most tables, the usual `RefreshDatabase` won't build
the schema. Options (pick one, document it):
- **Schema snapshot:** run `php artisan schema:dump` against a dev DB once → commit
  `database/schema/mysql-schema.sql`; tests load it (recommended first step — also helps
  [ADR 0005](adr/0005-schema-in-live-db.md)).
- **Dedicated test DB** mirroring prod schema, reset between runs (transactions).
- **Never** point tests at the production `dbmaster` DB. Use a separate `.env.testing`.
Mock external services always (S3, ES, Stripe/Razorpay/PhonePe, Zoom, WhatsApp, OpenAI).

## 6. Conventions
- Feature tests under `tests/Feature/<Module>/`, unit under `tests/Unit/`.
- Name: `test_<thing>_<expected_behavior>`.
- One behavior per test; arrange-act-assert; no shared mutable state (Octane mindset).
- Fast + deterministic; no network; no sleeps.

## 7. Minimum bar for a change (ties to [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md))
- New data feature → at least one **tenant-isolation** test + one happy-path test.
- Bug fix → a **failing test first**, then make it pass (Recipe 4).
- Business logic → cover the rule + an edge case.

## 8. Roadmap to coverage (incremental)
1. Add `schema:dump` + `.env.testing` so real tests can run.
2. Backfill factories for core entities (Company, User, Job, JobApplication, Client).
3. Write tenant-isolation tests for the busiest endpoints first.
4. Add a CI job running `php artisan test` on every PR (see [AI_GUARDRAILS.md](AI_GUARDRAILS.md) enforcement).

> Verify: `php artisan test` — and report real results, including failures.
