# Talent Marketplace — Architecture & Build Plan

> Vision: companies **trade talent with each other** — agencies, staffing firms, and
> employers exchange candidates/bench/requirements on a trusted, two-sided marketplace.
> You already have the seeds: client↔agency relations, candidate requests, recommendation
> relations, and a **coins economy** (`ats_coin_*`). This doc is the architect's plan to
> turn those seeds into a real marketplace. Written as strategy — build incrementally.

---

## 1. What you're really building
A **B2B two-sided marketplace** layered on top of the ATS:
- **Supply side:** companies with available talent — agency bench, sourced candidates,
  contractors, "open to share" profiles.
- **Demand side:** companies with open requirements — employers and agencies needing to
  fill roles fast.
- **The exchange:** discovery → request → share → submit → interview → place → settle
  (payment/coins) → rate. Trust, money, and quality are the core problems to solve.

This is the durable moat: an ATS is a tool; a **liquid talent network** is a platform.

---

## 2. Core marketplace primitives to build
| Primitive | Purpose | Seed in current code |
|---|---|---|
| **Talent listing** | A shareable, privacy-controlled candidate/bench profile | `BenchApplication`, `JobApplication`, `ats_candidates_bench_elastic` |
| **Requirement listing** | An open role broadcast to the network | `Job`, requisitions, `ats_jobs_request` |
| **Connection/relationship** | Who can transact with whom (trust graph) | `staffing_company_relation`, `recommendation_relation`, `ats_client_and_agency_relation_index` |
| **Request / offer** | "I want this candidate" / "I offer this candidate" | `ProcessCandidateRequest`, `ats_candidates_request` |
| **Matching engine** | Surface the right talent/requirement to the right party | `ElasticCandidatesIndex` (`skill_match`, `boostkeywords`) |
| **Transactions / coins** | Pay/charge for shares, unlocks, placements | `ats_coin_*`, `company_coins`, `coin_orders` |
| **Trust & ratings** | Reputation that makes the market safe | `ClientFeedback`, `Report` (extend) |
| **Contracts/terms** | Fees, splits, exclusivity, guarantees | **new** |

---

## 3. Marketplace architecture (on the existing stack)
```
        ┌──────────────────── Discovery & Matching ────────────────────┐
        │  Elasticsearch: cross-tenant talent & requirement indices     │
        │  (privacy-filtered, consent-aware, relevance-ranked)          │
        └───────────────▲───────────────────────────▲──────────────────┘
 supply │ list/share                          demand │ post/search
   ┌────┴─────┐   request/offer (events)      ┌──────┴─────┐
   │ Company A │◀───────── Reverb push ───────▶│ Company B  │
   └────┬─────┘                                └──────┬─────┘
        │  transaction (coins/payment, escrow, fees)  │
        └──────────────▶  Ledger / Coins service  ◀───┘
                          (MySQL source of truth)
```
Key design choices:
- **Cross-tenant search index** is the heart: a separate ES index of *shareable* talent &
  requirements, where each doc carries visibility rules (who may see it). The ATS's
  per-tenant scoping does **not** apply here — this is deliberately cross-tenant, so it
  needs its **own strict consent/visibility model** (see §5). Keep it isolated from the
  private per-tenant indices.
- **Event-driven**: listing/request/offer/placement are events → Reverb push + audit log.
- **Coins as the settlement rail** (already modeled): unlocks, featured listings,
  placement fees, subscriptions — extend into a proper **double-entry ledger**.

---

## 4. The transactional core (build this carefully — it's money)
- **Double-entry ledger** for coins/credits (debit/credit, immutable, reconciled). Don't
  bolt balances onto a column; model `ledger_entries`. Existing `coin_transection_logs`
  (sic) is the seed — formalize it.
- **Escrow / hold** for placement fees: hold on submission, release on placement +
  guarantee period, refund on fallout.
- **Fee/split engine**: marketplace take-rate, agency-agency splits, configurable per
  contract.
- **Idempotent, audited** transactions; tie to payment gateways (Stripe/Razorpay/PhonePe)
  for coin purchase and payouts.
- **Disputes & guarantees**: replacement windows, dispute workflow, hold-back.

---

## 5. Trust, privacy & consent (make-or-break for talent trading)
- **Candidate consent**: a candidate's profile can only be shared/listed with consent;
  track consent + purpose (GDPR/DPDP — see [SECURITY.md](SECURITY.md)). This is legal,
  not optional.
- **Progressive disclosure**: show anonymized profile first (skills, exp, rate), reveal
  PII only after a paid unlock/accepted request. Prevents poaching/scraping.
- **Reputation system**: ratings on responsiveness, candidate quality, fill rate, fall-off
  rate; verified placements; tiers/badges. Bad actors lose access.
- **Anti-leakage / anti-disintermediation**: watermarking, audit of who viewed what,
  duplicate-candidate detection across tenants (you already track `duplicate`).
- **Connection model**: open marketplace vs invite-only networks vs private circles —
  support all three via the relationship graph.

---

## 6. Matching & intelligence (your differentiator)
- Reuse + elevate `ElasticCandidatesIndex` ranking into a **two-sided matcher**:
  talent→requirements and requirements→talent, with skill, location, rate, availability,
  and **historical success signals** (who places well with whom).
- **AI layer** (you have `OPENAI_API_KEY`/`ChatGptService`): semantic skill matching,
  embeddings/vector search (ES kNN), JD↔resume similarity, smart recommendations,
  auto-summaries. Cache aggressively; run async.
- **Liquidity features**: alerts ("3 new matches for your req"), featured boosts (coins),
  "talent you might share", demand heatmaps.

---

## 7. What to build — phased
| Phase | Deliver | Builds on |
|---|---|---|
| **0. Harden base** | Fix tenant isolation, secrets, XSS, env→config (BUG/SEC docs) | required before cross-tenant anything |
| **1. MVP exchange** | Shareable talent + requirement listings, connection graph, request/offer flow, Reverb notifications | existing relations + ES request indices |
| **2. Settlement** | Coins ledger (double-entry), unlock-to-reveal, featured listings, payment top-up | `ats_coin_*`, gateways |
| **3. Trust** | Consent management, anonymized profiles, ratings/reputation, audit, dispute flow | `ClientFeedback`, audit log |
| **4. Intelligence** | Two-sided matcher, vector/semantic search, alerts, recommendations | ES + AI |
| **5. Scale & network** | Public/private circles, partner API, payouts/escrow, analytics warehouse | [SCALABILITY.md](SCALABILITY.md) |

---

## 8. Architect's summary — what you need to build & think about
1. **Separate the marketplace plane from the ATS plane.** ATS = private, per-tenant.
   Marketplace = deliberately cross-tenant with its own consent/visibility engine and its
   own ES indices. Don't let one leak into the other (today's tenant bugs make this
   priority zero).
2. **The exchange is event-driven + push-first.** Listings, requests, offers, placements
   are events → Reverb + ledger + audit. No polling.
3. **Money is sacred:** double-entry ledger, escrow, idempotency, reconciliation, disputes.
   Build it as a dedicated, well-tested service, not columns on `companies`.
4. **Trust is the product:** consent, progressive PII disclosure, reputation, anti-
   leakage. Without these, companies won't share their best talent.
5. **Matching is the moat:** invest in two-sided, AI-assisted, success-signal-driven
   matching on ES (+ vector search). This is what creates liquidity.
6. **Reuse the seeds you already have** (relations, requests, coins, ranking) — you're
   extending, not greenfielding. Formalize them into services with tests.
7. **Design for 10K tenants / 100M records from day one** — cross-tenant search,
   ledger, and consent must scale (see [SCALABILITY.md](SCALABILITY.md)).

> One line: **Build a consent-safe, event-driven, ledger-backed, AI-matched cross-tenant
> exchange on top of the existing ATS — and treat tenant isolation, money, and trust as
> non-negotiable foundations.**
