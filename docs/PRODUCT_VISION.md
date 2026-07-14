# Product Vision — JERO

> The "why" and "what" behind the code. Read this to understand the **concept** before
> the implementation. Pairs with [ATS_FEATURES.md](ATS_FEATURES.md),
> [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md), [SCALABILITY.md](SCALABILITY.md).

---

## 1. Elevator pitch
**JERO** is a multi-tenant B2B hiring platform that combines a **GOD-level Applicant
Tracking System (ATS)** with a **talent marketplace** where companies **trade talent with
each other** — agencies, staffing firms, and employers share bench candidates, sourced
profiles, and open requirements on one trusted, AI-matched network.

Two products, one platform:
1. **The ATS** — how a company runs its own hiring (jobs, candidates, pipeline,
   interviews, offers, onboarding, analytics).
2. **The Marketplace** — how companies *exchange* talent and requirements with each other
   (list, request, submit, place, settle via coins, rate).

The ATS gives daily utility and data; the marketplace turns that data into a **liquid
talent network** — the durable moat.

---

## 2. Who it's for (personas)
| Persona | Who | Primary job-to-be-done |
|---|---|---|
| **Service Company** | Staffing/recruitment **agency** | Source candidates, manage clients & requisitions, submit to clients, place, track bench |
| **Product Company** | **Employer** hiring in-house | Post jobs, manage applicants, interview, hire |
| **Staffing Company** | Vendor/agency in submission flows | Submit candidates against requirements |
| **Super Admin** | **Platform owner (you)** | Manage tenants, packages, billing, global settings |
| **Admin** | Tenant administrator | Configure & administer one company |
| **Candidate** *(growing)* | Job seeker | Apply, track status, schedule, self-serve |

The marketplace connects these companies to **each other** (supply ↔ demand of talent).

---

## 3. The core problem we solve
- Hiring is slow, fragmented, and relationship-bound. Agencies sit on **idle bench
  talent**; employers and other agencies have **unfilled requirements**. There's no
  trusted, fast, intelligent way to match supply with demand **across companies**.
- JERO makes each company productive internally (ATS) **and** lets them monetize/fill
  faster by trading talent externally (marketplace), with trust, consent, and settlement
  built in.

## 4. What makes it "GOD-level" (the bar)
- **Lightning fast & low server load** — edge/cache-first, ES read store, push-not-poll.
  See [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md).
- **AI-native** — semantic matching, resume parsing, summaries, recommendations.
- **Dense, beautiful, responsive UI** — Metronic, fits any laptop, ≤14px, no data loss.
  See [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md).
- **Trustworthy at scale** — strict tenant isolation, consent, audit, compliance.
- **Scales to 10K companies / 100M+ records/year** without failing.
  See [SCALABILITY.md](SCALABILITY.md).

---

## 5. How it works (concept, not code)
```
                ┌─────────────────────────────────────────────┐
                │                 JERO PLATFORM                 │
                ├──────────────────────┬──────────────────────┤
   private →    │   ATS (per-tenant)   │   MARKETPLACE (cross) │   ← shared, consent-gated
                │  jobs · candidates · │  list talent / reqs · │
                │  pipeline · interview│  request · submit ·   │
                │  offer · onboard ·   │  place · settle(coins)│
                │  analytics           │  · rate / reputation  │
                ├──────────────────────┴──────────────────────┤
                │  MySQL (write) · Elasticsearch (read) · Redis │
                │  Horizon queues · Octane · Reverb · S3 · AI   │
                └─────────────────────────────────────────────┘
```
- **ATS data is private** to each tenant (`company_id` scoped).
- **Marketplace is deliberately cross-tenant** but **consent- and visibility-gated** — a
  separate plane with its own indices and rules. (Tenant isolation must be rock-solid
  first — see [SECURITY.md](SECURITY.md).)
- **Coins** are the settlement currency (unlocks, featured listings, placement fees).
- **Elasticsearch** powers fast search/matching on both planes.

---

## 6. Business model (observed + intended)
- **SaaS subscriptions / packages** per tenant (Stripe/Paddle/Razorpay/PhonePe; packages,
  custom packages, licensing).
- **Coins economy** — pay-per-use for marketplace actions (unlock candidate, feature a
  listing, placement fees / take-rate).
- **Tiering** — free → paid plans; enterprise tenants get isolation/SLAs.

---

## 7. Guiding principles (for product & engineering decisions)
1. **Trust is the product.** Consent, isolation, audit, reputation come before features.
2. **Speed is a feature.** Every activity should feel instant; the server should rarely
   do work it could cache or push.
3. **AI assists, humans decide.** Matching/ranking is explainable; bias is audited.
4. **Build for 10K tenants from day one** — leave the seams (sharding, ILM, ledger,
   consent) in place even in the MVP.
5. **Extend, don't rewrite.** The seeds exist (relations, requests, coins, ranking) —
   formalize them into tested services.
6. **Density without clutter.** Recruiters live in this UI all day; respect their screen.

---

## 8. North-star outcomes
- Time-to-fill ↓, fill-rate ↑, bench utilization ↑ for every tenant.
- A liquid cross-company talent network with high trust and repeat transactions.
- A platform that stays fast and cheap to run as it grows to 10K companies.

> See [ROADMAP.md](ROADMAP.md) for the sequenced plan to get there.
