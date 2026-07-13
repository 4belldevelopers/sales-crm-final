# GOD-Mode Enterprise Playbook — Lightning Speed at Near-Zero Server Load

> Opinionated, **codebase-specific** strategy to take JERO from "large Laravel app" to
> an enterprise-grade, lightning-fast, low-infrastructure-cost product. Tied to the
> real findings in [ARCHITECTURE.md](ARCHITECTURE.md), [BAD_PATTERNS.md](BAD_PATTERNS.md),
> [DEPENDENCY_GRAPH.md](DEPENDENCY_GRAPH.md), [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).
>
> **Rules of engagement (do not violate):** no business-logic changes without sign-off;
> schema lives in the live DB (confirm before touching); every optimization ships
> behind a flag + benchmark; work in `testing.jero.works`. This file is *strategy* —
> implement incrementally, measure each step.

---

## 0. The one mental model: "Compute once, serve from the edge, push don't poll"

Your app already has the bones of a high-performance architecture (CQRS-ish: **MySQL =
write store, Elasticsearch = read store**, Redis, Octane, Reverb). The GOD-move is to
**finish that pattern** and add **3 cache tiers in front of it** so 90%+ of requests
never touch PHP or the DB.

```
Browser ──▶ CDN/Edge (HTML+JSON cache, SWR)  ── 80% served here, 0 origin load
        ──▶ Caddy/Octane (full-page + fragment cache, Redis)  ── 15% here
        ──▶ Elasticsearch (read store)  ── most dynamic reads
        ──▶ MySQL (writes + source of truth only)  ── <5% of traffic
        ◀── Reverb (push updates) instead of clients polling
```

Target: **p95 < 200 ms perceived, < 5% of requests reaching MySQL, origin CPU flat
under 10× current traffic.**

---

## 1. LIGHTNING SPEED — make every activity feel instant

### 1.1 Three cache tiers (biggest single win)
| Tier | What to cache | Tool | Invalidation |
|---|---|---|---|
| **Edge** | Public pages (job board, marketing, careers), static JSON | CDN (Cloudflare) + `Cache-Control`/`s-maxage` + **stale-while-revalidate** | tag/path purge on publish |
| **Full-page (server)** | Authenticated dashboards' shell, rarely-changing fragments | `responsecache` / Redis fragment cache | event-driven (Observers) |
| **Data** | ES query results, master data (countries, skills, packages), `smtp_settings`, permission maps | Redis with **tags** | Observer fires `Cache::tags(...)->flush()` |

> Concrete: `smtp_settings` is read ~7× per mail send (see BAD_PATTERNS). Cache all
> "settings" tables in Redis with a 1-event-driven tag. Master data
> (`countries`, `skills`, `td_industries`, `packages`) should **never** hit MySQL on a
> request — preload to Redis on deploy + Observer-invalidate.

### 1.2 Kill polling → push everything (Reverb is already installed)
Replace every `setInterval`/AJAX-refresh on dashboards, trackers, notifications, and
chat with **Reverb broadcasts**. Polling is the silent server-load killer in ATS UIs.
- New candidate / status change → Observer → broadcast to the tenant channel → client
  patches the DataTable row in place. No re-fetch.

### 1.3 Make the read path ES-only and pre-shaped
- Store ES docs **already shaped for the UI** (the `Elastic*Index` services should emit
  exactly the columns the tracker renders — no post-processing in PHP).
- Use ES `search_after` (cursor pagination) instead of deep `from/size` for big lists.
- Add ES **request cache** + **filter context** (filters are cacheable; queries aren't).
- Pre-compute dashboard aggregates into a dedicated index (`ats_*_dashboard`) updated by
  jobs — dashboards then = 1 cheap ES GET, not 20 COUNT queries.

### 1.4 Octane: exploit the warm process
- Cache compiled config/routes/views (`config:cache route:cache view:cache`).
- Use Octane **concurrent tasks** (`Octane::concurrently([...])`) to parallelize the
  many independent ES/DB calls a god-controller method currently does serially.
- Move per-request bootstrapping (settings, permissions) into Octane "tick"/warm cache,
  not re-resolved per request. **Watch state leaks** (see ADR 0003).

### 1.5 Frontend speed budget (perceived speed > server speed)
- Ship **skeleton loaders** + render the page shell from edge cache instantly; hydrate
  data via cached JSON. User sees content < 100 ms even if data is 300 ms.
- Vite: code-split per persona, lazy-load DataTables/Metronic chunks, `defer` non-critical
  JS, preload fonts, `content-visibility: auto` for long lists.
- Serve images via CDN with AVIF/WebP + responsive `srcset`; resumes/photos already on
  S3 → put CloudFront/Cloudflare in front.

---

## 2. MINIMUM / NEAR-ZERO SERVER LOAD

The cheapest request is the one your server never sees.

1. **Edge-render public surface area** — the job board, careers pages, and marketing
   front (`Front/`, `SaasFront/`) should be CDN-cached HTML with SWR. These are
   currently dynamic PHP renders; they can be ~100% offloaded.
2. **HTTP caching everywhere** — `ETag` + `Cache-Control` + `304 Not Modified` on every
   GET JSON endpoint (DataTables data routes). Conditional requests = no DB, no render.
3. **Read replicas** — point all read traffic (and ES indexers) at a MySQL **read
   replica**; keep the primary for writes only. Laravel supports read/write split in
   `config/database.php` natively.
4. **Connection pooling** — remote MySQL/ES (`76.13.243.68`) means RTT per query.
   Use persistent connections / a proxy (ProxySQL) and co-locate app+DB+ES in one region.
5. **Batch + debounce writes** — status drags, "is_viewed", ratings → buffer client-side,
   flush in one request; bulk-index to ES (`_bulk`) instead of per-row.
6. **Offload heavy compute to async + serverless** — resume parsing, PDF/Excel export,
   bulk mail, AI calls → queue (already) and optionally Lambda/Cloud Run for spiky
   workloads so the always-on box stays small.
7. **Autoscale workers, not web** — most cost is background. Scale Horizon workers by
   queue depth; keep web tier lean behind cache.

**Result:** the always-on origin can be a small box; CDN + ES + replicas absorb load.

---

## 3. DATABASE & QUERY LOAD — minimize, then minimize again

> ⚠️ Schema is in the live DB (ADR 0005). **Confirm every index/column before acting.**

### 3.1 Stop the bleeding (highest impact, lowest risk)
- **Eliminate N+1**: enable query logging in staging, walk the trackers/dashboards,
  add eager loading. The `job_applications`-centric screens are the prime suspects.
- **Index audit** on hot columns: `job_applications(company_id, job_id, master_status_id,
  status, email, duplicate)`, `jobs(company_id, client_id, status)`,
  `add_job_wise_condidates(job_id)`, every FK + every `company_id`.
- **Make queries sargable**: replace `WHERE DATE(col) = ?` (e.g. `Company::isFeatured`)
  with range predicates `col >= ? AND col < ?` so indexes are used.
- **Select only needed columns** — but the real fix is reads should hit **ES, not MySQL**.

### 3.2 Architectural (medium term)
- **Finish CQRS**: MySQL is *write-only* in the request path; **all list/search/dashboard
  reads come from ES**. Where a screen still queries MySQL directly, move it to ES.
- **Covering indexes / summary tables** for the few aggregates that must stay in SQL.
- **Partition / archive**: old `job_applications`, logs, `coin_transection_logs` →
  archive tables/cold storage so hot tables stay small and fast.
- **Reconciliation job**: nightly MySQL↔ES diff to fix drift (the dual-write risk in
  ADR 0001). This *enables* trusting ES for 100% of reads.

### 3.3 Query governance
- Add a **slow-query budget** (log > 50 ms) and fail CI if a new query exceeds it.
- Wrap raw `DB::table()` in repository/query helpers that **enforce `company_id`** (fixes
  the tenant-leak risk and centralizes optimization).

---

## 4. RESPONSIVE + CLEAN ICON-BASED DESIGN (beautiful, lightweight, fast)

**Design direction: clean, beautiful, professional, icon-led — NO 3D.** No Three.js /
WebGL / 3D charts / 3D scenes. Beauty comes from layout, whitespace, **Poppins**
typography, color, and **consistent icons** — which is also the *fastest, lightest* option
(tiny payload, no GPU cost). Full spec: [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md).

### 4.1 Responsive foundation
- Adopt a **design-token system** (Tailwind config as source of truth: spacing, color,
  typography, radius, shadow, motion). Kills the hardcoded-value sprawl and makes the UI
  consistent across all six personas.
- **Poppins** font app-wide; **max 14px** type scale; follow **Metronic**.
- Container queries + fluid root font (`clamp()`); test at 1366×768 → 4K (see UI_UX_STANDARDS §4).
- Componentize Blade/Alpine into reusable `x-` components; one card/table/modal pattern.
- Accessibility as a gate (WCAG AA): contrast, focus, keyboard, reduced-motion.

### 4.2 Where the "beautiful" comes from (2D, icon-based)
| Goal | Technique (lightweight) |
|---|---|
| Premium feel | Generous whitespace, soft shadows/rounded cards, consistent **Poppins** type, restrained color palette (Metronic tokens) |
| Visual clarity & polish | One consistent **icon style** across nav, buttons, stat cards, row actions, status chips, empty states. Keenicons is **not mandatory** — pick whichever icon set/format (Keenicons, Font Awesome, inline SVG, etc.) best fits the requirement and is verifiably available; just stay consistent within a given screen |
| Dashboards / data-viz | Clean **2D charts** (e.g. ApexCharts/Chart.js — already common with Metronic): bars, lines, donuts, funnels. **No 3D charts.** |
| Delight without weight | Subtle **CSS transitions** (hover, fade, slide), skeleton loaders, micro-interactions — fast and GPU-free |

**Rules (keep it light & beautiful):**
- ❌ No 3D libraries, no WebGL, no GLTF, no "talent globe". If a chart needs depth, use a
  clean 2D representation instead.
- ✅ Flat, modern, icon-led; beautiful **empty states** (icon + line + action), not blank tables.
- ✅ Subtle 2D motion only; respect `prefers-reduced-motion`.
- ✅ Lazy-load chart JS only on pages that need it; keep the main bundle small.

> Net: a clean, premium, icon-based enterprise look that is **lighter and faster** than any
> 3D approach — minimal payload, no GPU cost, instant feel.

---

## 5. FEATURE IMPLEMENTATION VELOCITY (ship fast without new debt)

The god controllers are the velocity tax. Pay it down *as you build*, never in a big bang.

1. **Strangler pattern**: every new/edited feature gets extracted into a **Service /
   Action class** (single public `handle()`), leaving the god controller as a thin caller.
   Over time the controllers shrink to routing.
2. **Vertical-slice scaffolding**: a feature = Route + FormRequest + Action + ES index
   update + Observer hook + Blade component + test. Make a `make:feature` generator or a
   checklist so every feature is consistent and AI-buildable.
3. **Contract-first for any new API**: define request/response (and version it under
   `/api/v1`) — don't bolt onto `web.php` (see API_CONTRACTS).
4. **Feature flags** (DB/Redis-backed) so features ship dark and roll out per tenant.
5. **Reuse first**: 55 FormRequests, the Services, Traits, and `x-` components already
   exist — the AI agent must search before writing (see §7).
6. **Tests as you go**: every extracted Action gets a unit test. This is how you escape
   the "zero coverage → fear of change → slow" trap (BAD_PATTERNS).

---

## 6. OBSERVABILITY & RESILIENCE (enterprise table-stakes)

You can't make fast what you can't see.
- **APM**: Laravel Telescope (non-prod) + a prod APM (Sentry/New Relic/OpenTelemetry).
  Trace p95 per route, slow queries, N+1, queue wait.
- **ES `took`** is already returned — log it per query; alert on regressions.
- **Horizon metrics**: per-queue wait/throughput/failures; alert on failed index jobs
  (= data drift).
- **SLOs + error budgets** per the [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).
- **Resilience**: circuit-breakers/timeouts/retries on Zoom, payment gateways, ES, S3,
  WhatsApp, OpenAI — one slow third party must not stall a worker or request.
- **Idempotency keys** on payment webhooks; **dead-letter** queue visibility.
- **Security**: move secrets out of the tree (.env leak in BAD_PATTERNS), rotate the
  exposed ES password, gate debug routes, harden the commented-out tenant scope.

---

## 7. THE AI MODEL OPERATING DOCTRINE (how the agent achieves all the above)

> These are *additional* rules layered on [AI_RULES.md](AI_RULES.md). They make the AI a
> force-multiplier instead of a debt-generator.

1. **Measure before and after, always.** No optimization is "done" without a
   before/after number (query count, ms, payload size, bundle KB). State them.
2. **Cache-first thinking.** For any new read path, the agent's first question is "can
   this be served from edge/Redis/ES instead of MySQL?" — and it implements the cache +
   the Observer-driven invalidation together.
3. **Push, don't poll.** Any UI that auto-refreshes must use Reverb, never `setInterval`.
4. **One feature = one vertical slice + one test.** The agent extracts logic into an
   Action/Service rather than growing a controller. It never adds to a 500 KB file
   without extracting.
5. **Tenant-safety reflex.** Every raw query gets a `company_id` filter; the agent flags
   any code path that could leak across tenants.
6. **ES sync reflex.** Any write to an indexed entity must update the matching
   `Elastic*Index` service and ensure the Observer/Job re-indexes — verified, not assumed.
7. **Schema humility.** Never invent migrations/columns; confirm against the live DB and
   ask before structural change.
8. **Budget enforcement.** Reject its own change if it adds a query > 50 ms, an N+1, a
   main-bundle bloat, or a synchronous heavy op. Heavy work → queue.
9. **Token discipline = speed.** Grep for methods, read `.ai/summaries` and
   `.ai/tasks/*` first; never load god files whole. (Faster for the agent *and* cheaper.)
10. **Ship behind flags, benchmark, then widen.** Especially for caching and read-path
    moves to ES — flag, canary one tenant, measure, expand.

---

## 8. Prioritized roadmap (impact × effort)

| # | Move | Impact | Effort | Risk |
|---|---|---|---|---|
| 1 | Redis data-cache for settings + master data (kill repeat queries) | 🔥🔥🔥 | S | Low |
| 2 | Split Horizon queues (`indexing`/`uploads`/`mail`/`default`) | 🔥🔥🔥 | S | Low |
| 3 | Index audit + sargable fixes on `job_applications`/`jobs` | 🔥🔥🔥 | M | Med (verify schema) |
| 4 | ETag/`Cache-Control` + SWR on public pages → CDN | 🔥🔥🔥 | M | Low |
| 5 | Replace dashboard/tracker polling with Reverb push | 🔥🔥 | M | Low |
| 6 | MySQL read replica + read/write split | 🔥🔥 | M | Med |
| 7 | Move remaining MySQL reads → ES; pre-shaped docs + `search_after` | 🔥🔥🔥 | L | Med |
| 8 | Pre-computed dashboard aggregate index | 🔥🔥 | M | Low |
| 9 | MySQL↔ES reconciliation job (trust ES for 100% reads) | 🔥🔥 | M | Med |
| 10 | Strangle god controllers into Actions (ongoing, per feature) | 🔥🔥 | L | Med |
| 11 | Design-token system + reusable components | 🔥 | M | Low |
| 12 | Clean icon-based UI polish + 2D dashboard charts (Poppins, ≤14px) | 🔥 | M | Low |
| 13 | APM/observability + SLO alerts | 🔥🔥 | M | Low |
| 14 | Secrets hygiene + tenant-scope hardening + remove debug routes | 🔥🔥 | S | Low (security) |

**Sequencing:** do 1→2→3→4 first (days, huge wins, low risk), then 5/6/8, then the
deeper CQRS/ES moves (7/9) and the ongoing strangler (10), with design/UI (11/12) and
observability/security (13/14) running in parallel as a separate track.

---

### TL;DR
You already have a CQRS-shaped, queue-backed, push-capable stack. **GOD mode = finish
the pattern and put three cache tiers in front of it**: serve the public surface from
the edge, push live updates over Reverb, read from a pre-shaped ES, write to MySQL only,
and offload everything heavy to async/serverless. The server load problem disappears
because most requests never reach the server. The UI stays a clean, icon-based, Poppins,
≤14px Metronic design — beautiful *and* lightweight (no 3D). And the AI agent gets fast and safe by measuring everything, caching
first, slicing features vertically, and never reading a god file whole.
