# ADR 0003 — Octane + FrankenPHP application runtime

**Status:** Active (observed) · **[INFERRED] rationale**

## Decision
Serve the app with **Laravel Octane** on **FrankenPHP** (`OCTANE_SERVER=frankenphp`,
`OCTANE_HTTPS=true`; `frankenphp` binary committed at repo root; Caddy config under
`caddy/`).

## Reason (inferred)
- The app is large with heavy per-request work; Octane keeps the framework booted
  between requests for big latency wins over PHP-FPM.
- FrankenPHP bundles a modern HTTP/2/3 server (Caddy) + PHP, simplifying deploy and
  enabling worker mode + Reverb websockets on one stack.

## Alternatives (inferred)
- **PHP-FPM + Nginx** — standard, no shared-state risk, but slower (full bootstrap
  per request).
- **Octane + Swoole/RoadRunner** — similar perf; FrankenPHP chosen for Caddy/HTTPS
  ergonomics. **[UNCERTAIN]**

## Tradeoffs
- ➕ Much faster request handling; long-lived workers.
- ➖ **Shared-state hazards:** static properties/singletons persist across requests →
  data bleed/memory leaks if misused.
- ➖ Committing a 58 MB binary bloats the repo.

## Consequences
Write request-stateless code: no mutable static caches of request data, reset/avoid
stateful singletons, close resources. Test under Octane, not just `artisan serve`.
