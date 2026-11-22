# Comprehensive Audit & Strategic Architecture: Agency Discovery & Matching Engine in JERO.works

**Document Type:** Architectural & Algorithmic Audit / Enterprise Strategy Specification  
**Target Module:** Service Company Agency Discovery Marketplace (`/service_company/find-agency`)  
**Date:** 2026-11-22  
**Standard Compliance:** Rule 1 (0% MySQL reads on search/query paths), Rule 2 (Elasticsearch Universal Scope), Rule 3 (Client-First Execution), Rule 4 (10K Concurrency), Rule 20 (Card + Sub-Card Design System), Rule 21 (Mandatory Audit Documentation), Rule 22 (Zero-Cron & JIT Evaluation).

---

## 1. Executive Summary

This audit provides a complete forensic evaluation of the **Agency Discovery & Matching Engine** powering `https://testing.jero.works/service_company/find-agency`, compares it with enterprise standards used by leading platforms (Upwork Enterprise, Toptal, Eightfold.ai, BountyJobs, SeekOut), and provides a mathematically sound strategy to ensure **maximum relevance for employers** while **guaranteeing fair lead distribution so that every verified agency gets opportunities to work and monetize**.

### Key Findings:
1. **Existing Architecture:** Strict CQRS using Elasticsearch `ELASTIC_COMPANIES_INDEX` with a 5-tier additive `function_score` boost model + Client-First in-memory (RAM) job matching.
2. **Current Limitations:**
   - Static string matching without semantic skill taxonomy or related competency graphs.
   - Zero feedback loop on historical performance (SLA, interview pass rate, replacement rate).
   - Coin-wallet gating (`company_coins > 0`) creates arbitrary exclusion of capable partners.
   - "Winner-Take-All" bias: Top agencies monopolize inquiries, starving newer or smaller specialized agencies.
3. **Enterprise Solutions:** Multi-Vector Hybrid Scoring (Semantic + SLA + Capacity + Commercial + Geo), Multi-Armed Bandit ($\epsilon$-greedy) exploration pools, capacity-based lead throttling, and multi-partner mandate syndication.

---

## 2. Forensic Audit: How Current Agency Listings & Matching Works Today

The current implementation in `app/Services/FindAgencyService.php` and `find-agency.blade.php` operates across **two coordinated layers**:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                               LAYER 1: BACKEND SERVER CQRS ENGINE                           │
│  Request Parameters ──> Redis L3 Cache Check ──> ELASTIC_COMPANY_SKILLS Pre-Query           │
│                                              ──> ELASTIC_COMPANIES_INDEX Function Score     │
│                                              ──> N+1 Batch Enrichment ──> View Render       │
└───────────────────────────────────────────────┬─────────────────────────────────────────────┘
                                                │ (JSON + Card Payload with data-agency-skills)
┌───────────────────────────────────────────────▼─────────────────────────────────────────────┐
│                               LAYER 2: CLIENT BROWSER RAM MATCHER                           │
│  Active Tenant Jobs (User RAM) <── Jaccard Overlap Matrix ──> Dynamic Card Match Badge      │
│  (100% Client-Side Execution, 0% Server Strain, Instant Re-computation on Requisition Select)│
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 2.1 Backend Step 1: Pre-flight Skill Retrieval
When a user searches or an active job is in context:
1. The service queries `ELASTIC_COMPANY_SKILLS` matching the required skill list.
2. It aggregates matched company IDs and counts how many required skills each company possesses (`$companySkillMatchCount`).

### 2.2 Backend Step 2: 5-Tier Function Score Additive Boosting
Elasticsearch executes a `function_score` query on `ELASTIC_COMPANIES_INDEX` using `score_mode: 'sum'` and `boost_mode: 'sum'`:

| Tier | Condition | Score Boost | Purpose |
| :--- | :--- | :--- | :--- |
| **Tier 1** | Exact Mandate Match ($\ge 2$ skills match + target country + target hiring model) | **+5,000 pts** | Propels agencies capable of fulfilling the exact requirement to the top. |
| **Tier 2** | Strong Technology Specialization | **+1,000 pts / skill**<br>(+1,500 bonus for $\ge 2$) | Rewards deep vertical focus in required stacks (e.g. Java, React, Cloud). |
| **Tier 3** | Industry Alignment | **+500 pts** | Matches domain industry (Fintech, Healthcare, Staffing). |
| **Tier 4** | Location-Based Match | **+300 pts** | Matches the employer's target operating country. |
| **Tier 5** | Commercial Model Fit | **+200 pts** | Matches Contract (C2C/C2H) vs Full-Time permanent staffing. |
| **Bonus** | Bench Availability | **+150 pts** | Agencies with active bench candidates (`bench_candidates.id > 0`). |

### 2.3 Backend Step 3: Base Filters & Tenancy Invariants
- `status: 'active'`
- `company_type: [1, 2]` (Staffing and Recruitment agencies)
- `company_coins.event_type: 'sales'` AND `company_coins.coins > 0`
- `must_not`: Current company ID (`$company_id`) and SuperAdmin company IDs.
- Dynamic tab filters:
  - `domestic`: `commericial_countries.country_id == $domesticCountryId` (Account HQ country).
  - `global`: `must_not: commericial_countries.country_id == $domesticCountryId`.
  - `bench`: `commericials.commtype == 1` OR `bench_candidates.id > 0`.
  - `contract` / `fulltime`: `commericials.commtype` matching.

### 2.4 Backend Step 4: Batch Pre-Enrichment (N+1 Elimination)
To prevent N+1 queries across the 10–20 agencies returned per page, the service executes high-speed batch lookups:
- `GeneralMethod::batchGetClientIdCompany()`: Client relationships.
- `GeneralMethod::batchGetCommercialsData()`: Dual commercials (Full-time % + Contract $/hr).
- `GeneralMethod::batchAgencyJobsCount()` & `batchAgencyJobsCandidatesCount()`: Pool sizes.
- `batchServingCountries`: Commercially served countries.

### 2.5 Layer 2: Client-First Real-Time Job Matcher (`applyActiveJobMatching`)
- In `find-agency.blade.php`, client JavaScript maintains `window.activeTenantJobs` (all open requisitions of the employer).
- Each rendered agency card embeds `data-agency-skills='["react", "node", "aws"]'`.
- When an employer selects a specific job from the dropdown or views "Overview Mode", the client calculates the intersection set:
  $$\text{Match Percentage} = \min\left(100, \max\left(1, \text{round}\left(\frac{|\text{Job Skills} \cap \text{Agency Skills}|}{|\text{Job Skills}|} \times 100\right)\right)\right)$$
- If $\text{Match} > 0$, it updates the green badge: `95% Match • Matches 5 Jobs (Senior DevOps, React Lead...)`.
- **Zero Server Overhead:** 10,000 employers can toggle jobs simultaneously in browser RAM without generating a single SQL or Elasticsearch read query.

---

## 3. How Enterprise Product Companies (Upwork, Toptal, Eightfold) Architect Vendor Matching

Enterprise talent marketplaces do not rely on static keyword overlap. They evaluate **Relevance ($R$)**, **Reliability ($SLA$)**, and **Fulfillment Velocity ($V$)** using a balanced composite function:

$$\text{Total Match Score} = w_{skill} \cdot S_{skill} + w_{perf} \cdot S_{perf} + w_{cap} \cdot S_{cap} + w_{comm} \cdot S_{comm}$$

### 3.1 Vector 1: Semantic Competency ($S_{skill}$ — Weight: 35%)
- **Semantic Embeddings:** Compares requisition descriptions with agency profiles using dense vector embeddings (e.g. 384-dimensional MiniLM or ONNX CPU vectors per Rule 19).
- **Taxonomy Relationship:** Recognizes that `Next.js` implies `React`, `FastAPI` implies `Python`, and `Kubernetes` implies `DevOps`.

### 3.2 Vector 2: Historical Performance & Reliability ($S_{perf}$ — Weight: 30%)
- **Turnaround SLA ($TAT$):** Does the agency deliver the first 3 vetted profiles within the contractual 36–48h window?
- **Shortlist-to-Interview Ratio:** Ratio of submitted candidates accepted by hiring managers for technical interviews (Enterprise threshold: $> 35\%$).
- **Offer-to-Join Ratio:** Ratio of extended offers that convert to successful joining ($> 85\%$).
- **Warranty Claim Rate:** Low rate of 90-day replacement invocations.

### 3.3 Vector 3: Supply Liquidity & Active Capacity ($S_{cap}$ — Weight: 20%)
- **Immediate Bench Inventory:** Verified candidates available on bench with 0–15 day notice periods.
- **Recruiter Concurrency Load:** Total recruiters deployed vs. active assigned mandates. An agency with 2 recruiters handling 10 mandates has negative capacity; an agency with 5 recruiters and 1 mandate has high liquidity.

### 3.4 Vector 4: Commercial & Bilateral Alignment ($S_{comm}$ — Weight: 15%)
- Standard contingent success fee fit ($8.33\%$ CTC / market rate).
- Payment terms compliance (Net-30 / Net-15).
- Jurisdiction and compliance accreditation.

---

## 4. Solving the Marketplace Dilemma: Ensuring Every Agency Gets Leads

A fundamental law of two-sided hiring marketplaces is:
> **"If an algorithm only rewards past winners, new agencies starve, leave the platform, and your talent marketplace collapses into an oligopoly."**

To ensure every verified partner gets viable commercial opportunities while guaranteeing high quality for employers, enterprise platforms implement **4 Core Mechanisms**:

### Mechanism 1: Multi-Armed Bandit ($\epsilon$-Greedy) Exploration
Instead of ranking 100% by pure historical score (exploitation), split the listings stream:
- **80% Exploitation (Top Relevance):** Guaranteed best-matching agencies for the mandate.
- **20% Exploration (Discovery & Lead Equalization):** High-potential agencies who have not received a mandate request in the last 7 days, or newly verified partners.
- This introduces new agencies to employers organically without degrading average match quality.

### Mechanism 2: Agency Concurrency Throttling & Fatigue Cool-Down
- If Agency A has received **5 mandate requests in the last 48 hours** and has not yet accepted or submitted profiles, their visibility boost is temporarily cooled down by $30\%$.
- This naturally pushes Agencies B, C, and D (who have open recruiter capacity) into the top 3 slots.

### Mechanism 3: Cold-Start Onboarding Fast-Track (+500 pts for 30 Days)
- Newly verified agencies receive a **"New Partner Discovery Boost"** for their first 30 days or until they receive their first 5 mandate requests.
- This guarantees that every onboarding agency gets immediate lead exposure to test their recruitment speed.

### Mechanism 4: Multi-Partner Mandate Syndication ("Direct RFP Broadcast")
- In the ATS workflow, instead of forcing the hiring company to select only 1 agency:
  - Provide a one-click option: **"Dispatch Mandate to Top 3 Specialized Partners"**.
  - Slot 1: **Top Ranked Historical Partner** (Proven SLA).
  - Slot 2: **Fast-Response Bench Partner** (Immediate candidate supply).
  - Slot 3: **Emerging Specialized Domestic Partner** (Hungry for placement, high focus).
- All 3 receive notification; the fastest agency with pre-screened talent wins the interview and placement fee.

---

## 5. Technical Implementation Roadmap for JERO.works

| Phase | Enhancement | Architecture Impact |
| :--- | :--- | :--- |
| **Phase 1** | **Fair-Lead Rotation Pool** | Add decay factor in Elasticsearch `function_score` based on `last_mandate_received_at` to prioritize hungry partners. |
| **Phase 2** | **Multi-Agency Syndication** | Allow employers to invite up to 3 agencies simultaneously per mandate in 1 click. |
| **Phase 3** | **SLA & Performance Telemetry** | Track actual TAT from `staffing_company_invitation` $\rightarrow$ first candidate submission to calculate real SLA points. |
| **Phase 4** | **Local Vector Matching (Rule 19)** | Integrate local ONNX/Mojo embeddings for JD $\leftrightarrow$ Agency skill taxonomy semantic similarity with 0% external cloud API cost. |
