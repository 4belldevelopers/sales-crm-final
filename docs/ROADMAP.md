# Roadmap — Sequenced Build Plan

> Consolidated, ordered plan tying together every strategy doc. Sequence matters: you
> cannot safely build a cross-tenant marketplace on a base with tenant-isolation bugs.
> This is a recommendation; adjust priorities with product. Cross-refs in each row.

Legend: 🟥 must-do-first · 🟧 high · 🟨 next · ⬜ later.

---

## Phase 0 — Harden the foundation 🟥 (weeks, low risk, high leverage)
*Nothing rich matters if the base leaks or breaks.*
- [ ] **env() → config()** then `config:cache` (BUG-02) — prevents silent prod outage.
- [ ] **Remove 63 active `dd()`** + gate debug routes `/mail-test`,`/whatsapp/*` (BUG-01/06).
- [ ] **Fix tenant isolation** — enforce `company_id` everywhere; tenant-isolation tests (BUG-03/SEC-02).
- [ ] **Secrets** — gitignore `.env`, rotate exposed keys (incl. ES password), least-priv DB user (SEC-01).
- [ ] **XSS sweep start** — escape user data, add CSP (BUG-04/SEC-03).
- [ ] **Empty exception handler** + `APP_DEBUG=false` in prod (BUG-10).
Refs: [BUG_REPORT.md](BUG_REPORT.md), [SECURITY.md](SECURITY.md).

## Phase 1 — Speed & UI baseline 🟥/🟧
- [ ] **Redis cache** for settings/master-data/permissions (kills repeat queries).
- [ ] **Split Horizon queues** (`indexing`/`uploads`/`mail`/`exports`/`default`).
- [ ] **Index audit + sargable fixes** on `job_applications`/`jobs` (BUG-05).
- [ ] **MySQL read replica** + read/write split.
- [ ] **Design system**: Metronic + ≤14px tokens, density, fluid root font, sticky+virtualized
      tables, responsive column priority. Reusable `x-` components. ([UI_UX_STANDARDS.md](UI_UX_STANDARDS.md))
- [ ] **Edge/CDN + SWR** on public job board/careers; ETag/304 on JSON GETs.
Refs: [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md), [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md).

## Phase 2 — Core ATS depth 🟧
- [ ] **Kanban pipeline** (drag-drop, Reverb push, optimistic UI).
- [ ] **Workflow automation** (stage triggers: email/task/notify).
- [ ] **Collaboration**: scorecards, @mentions, structured feedback, compare view.
- [ ] **AI resume parsing** (LLM extraction w/ confidence) + enrichment.
- [ ] **Replace polling with Reverb** across trackers/dashboards/notifications.
Refs: [ATS_FEATURES.md](ATS_FEATURES.md).

## Phase 3 — AI matching & intelligence 🟧
- [ ] **Semantic/vector search** (ES kNN) candidate↔job, two-sided matcher.
- [ ] **Explainable ranking**, shortlisting, summaries, next-best-action.
- [ ] **Pre-computed dashboard aggregates** (1 ES GET vs many COUNTs).
- [ ] **Bias/fairness audit** on AI suggestions.

## Phase 4 — Marketplace MVP 🟨 (requires Phase 0 isolation done)
- [ ] **Cross-tenant search plane** (separate consent-gated ES indices).
- [ ] **Shareable talent + requirement listings**; connection/trust graph.
- [ ] **Request/offer flow** + Reverb notifications.
- [ ] **Consent management** + progressive PII disclosure (anonymized → unlock).
Refs: [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md).

## Phase 5 — Settlement & trust 🟨
- [ ] **Coins ledger** (double-entry, idempotent, reconciled) — formalize `coin_*`.
- [ ] **Unlock-to-reveal**, featured listings, placement fees/take-rate.
- [ ] **Escrow/holds + guarantees + disputes**.
- [ ] **Reputation/ratings**, audit log of PII access.

## Phase 6 — Scale, platform & compliance ⬜
- [ ] **Partition + archive** hot tables; **ES ILM + aliases**; connection proxy.
- [ ] **Tenant sharding** by `company_id`; dedicated DB for top tenants; multi-region/residency.
- [ ] **Warehouse** for analytics & marketplace intelligence.
- [ ] **Public API/webhooks (versioned)**, integrations, mobile, white-label.
- [ ] **PII compliance** (GDPR/DPDP/EEO): retention/erasure, consent, audit.
- [ ] **Observability**: APM/tracing, SLOs, load tests gating each growth stage.
Refs: [SCALABILITY.md](SCALABILITY.md), [API_CONTRACTS.md](API_CONTRACTS.md).

---

## How every feature ships (definition of done)
Vertical slice: **Route + FormRequest + Action/Service + ES sync + Observer + Reverb event
+ Blade component (Metronic, ≤14px) + test**, extracted out of god controllers (strangler),
cache-first, push-not-poll, behind a feature flag, verified per [VERIFY.md](VERIFY.md),
with before/after numbers. Update [.ai/PROJECT_MEMORY.md](../.ai/PROJECT_MEMORY.md) when a
phase/decision changes.
