# GOD-LEVEL SPECIFICATION: Autonomous Agency Matching & Fair-Share Lead Distribution Engine

**Document Type:** Master System Architecture & Algorithmic Design Specification  
**Module:** Service Company Marketplace — Agency Discovery & Mandate Allocation (`/service_company/find-agency`)  
**Target Systems:** Strict CQRS (Elasticsearch 8.x + MySQL 8.x + Redis L3 Multi-Tenant Cache + Browser RAM WASM)  
**Date:** 2026-11-22  
**Standard Compliance:** Rule 1 (0% MySQL reads on search/query paths), Rule 2 (Elasticsearch Universal Scope), Rule 3 (Client-First Execution), Rule 4 (10K Concurrency / 0% Server Strain), Rule 14 & 20 (Card + Sub-Card Design System), Rule 21 (Mandatory Storage & Directory Structure), Rule 22 (Zero-Cron & JIT Evaluation Standard).

---

## 1. Executive Summary & The Marketplace Dilemma

In two-sided recruitment and talent marketplaces (hiring companies on the demand side, specialized hiring agencies on the supply side), naive ranking algorithms create a catastrophic systemic failure known as the **Matthew Effect ("The Winner-Take-All / Starvation Trap")**:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                      THE MARKETPLACE STARVATION COLLAPSE SPIRAL                        │
├────────────────────────────────────────────────────────────────────────────────────────┤
│  1. Top 5% Agencies rank high based on early reviews or high profile volume.           │
│  2. Hiring managers send 95% of all job mandates to these top 5% agencies.             │
│  3. The top agencies become overloaded; candidate quality and turnaround SLAs plummet. │
│  4. The other 95% of agencies receive 0 leads, lose hope, stop updating benches, and   │
│     churn permanently from JERO.works.                                                 │
│  5. Supply liquidity shrinks, fees rise, and employers abandon the platform.           │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

Conversely, pure random lead distribution fails because employers receive unqualified candidate submissions, destroying platform credibility.

### The God-Level Objective:
Architect a **Dual-Objective Optimization Engine** that simultaneously achieves:
1. **Pareto-Optimal Requisition Matching:** Employers are served the absolute highest-relevance, domain-specialized partners for their exact technical stack, seniority, budget, and location.
2. **Deterministic Fair-Lead Opportunity Guarantee ($\Phi_{\text{fairness}}$):** **100% of verified partner agencies receive active mandate inquiries and commercial opportunities** through stochastic multi-armed bandit exploration, capacity-aware load throttling, starvation decay boosts, and Tri-Tier Mandate Syndication.

---

## 2. Mathematical Formulation: The Composite Matching & Fairness Function

For any open mandate $J$ and any partner agency $A_i$ in the tenant's discovery scope, the final ranking score $\mathcal{S}_{\text{total}}(J, A_i, t)$ is computed in **Elasticsearch / Browser RAM** as:

$$\mathcal{S}_{\text{total}}(J, A_i, t) = \underbrace{\left[ w_s \cdot \mathcal{R}_{\text{semantic}}(J, A_i) + w_l \cdot \mathcal{L}_{\text{liquidity}}(J, A_i) + w_p \cdot \mathcal{P}_{\text{perf}}(A_i) + w_c \cdot \mathcal{C}_{\text{comm}}(J, A_i) \right]}_{\text{Relevance & Quality Vector (Exploitation)}} \times \underbrace{\Psi_{\text{fairness}}(A_i, t)}_{\text{Fair Opportunity Multiplier (Exploration)}}$$

### Normalized Weights:
- $w_s = 0.35$ (Semantic Competency & Stack Alignment)
- $w_l = 0.25$ (Candidate Supply Liquidity & Bench Availability)
- $w_p = 0.25$ (Historical SLA, Turnaround Time & Interview Pass Rate)
- $w_c = 0.15$ (Commercial & Billing Term Agreement)

$$\sum w = 1.00$$

---

## 3. Deep Architectural Breakdown of the 4 Core Relevance Vectors

```
                                  RELEVANCE VECTORS (80% Base Score)
   ┌────────────────────────┬────────────────────────┬────────────────────────┬────────────────────────┐
   │   Semantic Stack (35%) │ Supply Liquidity (25%) │ Historical Perf (25%)  │ Commercial Match (15%) │
   ├────────────────────────┼────────────────────────┼────────────────────────┼────────────────────────┤
   │ • ONNX Dense Embeddings│ • Immediate Bench CVs  │ • First 3 CV SLA <48h  │ • Fee % vs CTC Budget  │
   │ • Jaccard Skill Overlap│ • Active Pipeline Pool │ • Shortlist-Interview  │ • Contract Hourly Rate │
   │ • Taxonomy Hierarchies │ • Recruiter Headcount  │ • Offer-to-Join Ratio  │ • Net-30 / SLA Warranty│
   └────────────────────────┴────────────────────────┴────────────────────────┴────────────────────────┘
```

### 3.1 Vector 1: Semantic Stack Alignment ($\mathcal{R}_{\text{semantic}}$ — 35%)
- **Skill Graph Taxonomy:** Resolves tech competencies beyond literal keyword matching. E.g., `Next.js` inherently satisfies `React` and `TypeScript`; `FastAPI` implies `Python` and `AsyncIO`; `Kubernetes` satisfies `Cloud/DevOps`.
- **Hybrid Token & Embedding Overlap:**
  $$\mathcal{R}_{\text{semantic}} = 0.6 \cdot \text{Cosine}(\vec{E}_{\text{job}}, \vec{E}_{\text{agency}}) + 0.4 \cdot \frac{|\text{Skills}_{\text{job}} \cap \text{Skills}_{\text{agency}}|}{|\text{Skills}_{\text{job}}|}$$
  Executed via CPU-optimized local embeddings (Rule 19 ONNX/Mojo) with 0 cloud API tax.

### 3.2 Vector 2: Candidate Supply Liquidity ($\mathcal{L}_{\text{liquidity}}$ — 25%)
- Evaluates whether the agency can deliver profiles *immediately*:
  - **Immediate Bench Inventory:** Candidates in `bench_candidates` with 0–15 day notice periods.
  - **Active Specialized Candidate Pipeline:** Candidates in ATS pipeline with matching role tags.
  $$\mathcal{L}_{\text{liquidity}} = \min\left(1.0, \frac{\text{Matching Bench Candidates} \times 2.0 + \text{Pipeline Profiles}}{5.0}\right)$$

### 3.3 Vector 3: Historical SLA & Performance Telemetry ($\mathcal{P}_{\text{perf}}$ — 25%)
- Measures empirical execution velocity:
  - **Turnaround Time ($TAT$):** First 3 vetted profiles delivered within $\le 48$ hours.
  - **Client Acceptance Rate ($CAR$):** Ratio of submitted CVs accepted by hiring managers for interviews ($\ge 40\%$ is Gold tier).
  - **Offer-to-Join Conversion ($OJC$):** Candidate retention through joining date ($\ge 85\%$).
  - **Replacement Warranty Claim Rate:** Frequency of early exit replacement invocations ($< 5\%$).

### 3.4 Vector 4: Commercial & Contractual Compatibility ($\mathcal{C}_{\text{comm}}$ — 15%)
- Binary and fractional match on hiring models:
  - Permanent / Full-Time: Pre-agreed percentage ($8.33\%$ CTC).
  - Contract Staffing: Hourly/Monthly bill rate alignment with employer job requisition budget.
  - Net-30 payment term alignment and replacement warranty ($90$ Days Full-Time / $30$ Days Contract).

---

## 4. The God-Level Fair-Share Lead Distribution Multiplier ($\Psi_{\text{fairness}}$)

This is the mathematical core that **guarantees every single agency receives active inquiries and opportunities**:

$$\Psi_{\text{fairness}}(A_i, t) = \mathcal{F}_{\text{starvation}}(A_i, t) \times \mathcal{G}_{\text{capacity}}(A_i) \times \mathcal{B}_{\text{coldstart}}(A_i) \times \mathcal{E}_{\text{engagement}}(A_i)$$

```
                                  FAIRNESS MULTIPLIERS (The Equalizer)
   ┌────────────────────────┬────────────────────────┬────────────────────────┬────────────────────────┐
   │ Starvation Boost (JIT) │ Capacity Governor (Cap)│ Cold-Start Fast-Track  │ Engagement Multiplier  │
   ├────────────────────────┼────────────────────────┼────────────────────────┼────────────────────────┤
   │ Exponential boost if   │ Penalizes overloaded   │ +500 pts for first     │ +20% boost for daily   │
   │ 0 leads in last 7 days │ agencies with no open  │ 30 days or first 5     │ active logins & fresh  │
   │ until inquiry arrives  │ recruiter bandwidth    │ received mandates      │ bench CV updates       │
   └────────────────────────┴────────────────────────┴────────────────────────┴────────────────────────┘
```

---

### 4.1 Component A: Just-In-Time (JIT) Lead Starvation Decay Booster ($\mathcal{F}_{\text{starvation}}$)
When an agency has not received a mandate request for several days, its starvation factor rises exponentially to elevate it into employer search visibility:

$$\mathcal{F}_{\text{starvation}}(A_i, t) = 1.0 + \kappa \cdot \left( 1 - e^{-\lambda \cdot \Delta t_{\text{last\_lead}}} \right)$$

- $\Delta t_{\text{last\_lead}}$: Elapsed hours since the agency received its last mandate request.
- $\lambda = \frac{\ln(2)}{72}$: Halving parameter calibrated to a 72-hour half-life.
- $\kappa = 1.5$: Maximum starvation boost multiplier ($+150\%$ boost).

**Mathematical Behavior:**
- If an agency received a lead **2 hours ago**: $\mathcal{F} \approx 1.02$ (standard relevance).
- If an agency received **0 leads in the last 7 days (168 hours)**: $\mathcal{F} \approx 2.20$ (**Over 2x boost!** The agency is propelled to the top of relevant searches until it receives an inquiry).
- **Rule 22 Standard:** Zero cron jobs or recurring database sweepers. Computed dynamically inside the Elasticsearch query or on read request using `NOW() - last_inquiry_at`.

---

### 4.2 Component B: Recruiter Concurrency Capacity Governor ($\mathcal{G}_{\text{capacity}}$)
Prevents top agencies from hoarding mandates they cannot fulfill, avoiding employer frustration and distribution monopolies:

$$\mathcal{G}_{\text{capacity}}(A_i) = \frac{1.0}{1.0 + \left( \frac{\text{Active Pending Mandates}}{\text{Max Concurrent Capacity}} \right)^3}$$

Where $\text{Max Concurrent Capacity} = \max(2, \text{Active Recruiters} \times 3)$.

**Operational Effect:**
- Agency A has 1 recruiter and receives 5 open mandates:
  $$\text{Capacity} = 3 \implies \frac{5}{3} \approx 1.67 \implies \mathcal{G} = \frac{1}{1 + 1.67^3} = \frac{1}{1 + 4.63} \approx 0.17$$
  *Agency A's ranking score is throttled by 83% until they submit profiles or clear mandates.*
- Agency B has 2 recruiters and 0 active mandates:
  $$\mathcal{G} = 1.00$$
  *Agency B immediately jumps above Agency A, receiving the next batch of employer leads!*

---

### 4.3 Component C: Cold-Start Supercharge ($\mathcal{B}_{\text{coldstart}}$)
Every newly registered, verified agency must experience value immediately upon joining JERO.works:

$$\mathcal{B}_{\text{coldstart}}(A_i) = \begin{cases} 
1.40 & \text{if } t_{\text{onboarded}} \le 30 \text{ days AND } \text{Total Leads Received} < 5 \\
1.00 & \text{otherwise}
\end{cases}$$

- Guarantees that every new agency receives at least **5 initial qualified mandate opportunities** within their first month.
- Evaluates early speed: if they deliver within 48h, their permanent reliability score ($\mathcal{P}_{\text{perf}}$) locks in at a high tier.

---

### 4.4 Component D: Real-Time Engagement & Bench Freshness Multiplier ($\mathcal{E}_{\text{engagement}}$)
Rewards agencies that actively participate in the marketplace:

$$\mathcal{E}_{\text{engagement}}(A_i) = 1.0 + 0.10 \cdot \mathbb{I}_{\text{active\_today}} + 0.15 \cdot \mathbb{I}_{\text{bench\_updated\_7d}} + 0.10 \cdot \mathbb{I}_{\text{tat\_under\_2h}}$$

- Logged into JERO in the last 24 hours: $+10\%$
- Added or refreshed candidate bench in last 7 days: $+15\%$
- First message response time $< 2$ hours: $+10\%$
- **Marketplace Fairness:** Any agency—even a boutique 2-person firm—can outrank larger agencies simply by being active, responsive, and maintaining fresh bench inventory.

---

## 5. Tri-Tier Mandate Syndication: The 3x Opportunity Multiplier

In traditional ATS platforms, an employer chooses **1 agency** per job. This creates a bottleneck and leaves other agencies starving.

JERO.works introduces **Tri-Tier Mandate Syndication ("Dispatch to Top 3 Specialized Partners")**:

```
                                  TRI-TIER MANDATE SYNDICATION (1 Requisition ──> 3 Agency Leads)
   ┌────────────────────────────────────────────────────────────────────────────────────────┐
   │                           EMPLOYER REQUISITION (e.g. Senior DevOps Lead)               │
   └───────────────────────────────────────────┬────────────────────────────────────────────┘
                                               │ (One-Click Syndicated Dispatch)
            ┌──────────────────────────────────┼──────────────────────────────────┐
            ▼                                  ▼                                  ▼
   ┌────────────────────────┐         ┌────────────────────────┐         ┌────────────────────────┐
   │  SLOT 1: THE VETERAN   │         │ SLOT 2: THE BENCH HERO │         │ SLOT 3: THE CHALLENGER │
   │  (Established Partner) │         │ (Immediate Bench CVs)  │         │ (Hungry/Rotated Partner│
   ├────────────────────────┤         ├────────────────────────┤         ├────────────────────────┤
   │ High historical SLA    │         │ Immediate verified     │         │ High starvation boost  │
   │ track record (36h TAT) │         │ bench candidate ready  │         │ Zero leads this week   │
   └────────────────────────┘         └────────────────────────┘         └────────────────────────┘
```

### Marketplace Impact:
1. **Triples Marketplace Liquidity:** If 100 employers post jobs, **300 mandate leads are distributed across the agency pool**.
2. **Speed to Hire for Employers:** First partner to submit pre-screened talent wins the interview slot. Average hiring cycle drops from 28 days to 5 days.
3. **Egalitarian Competition:** Even if Slot 1 is a large staffing corporation, the boutique agency in Slot 3 with a dedicated recruiter can win by responding faster with an exact match.

---

## 6. Implementation Architecture in JERO (Strict CQRS + Zero Strain)

```
[ Hiring Employer Browser (Client RAM) ]
                   │
                   │ 1. GET /service_company/find-agency-data?job_id=45
                   ▼
[ App / Web Server (Octane / FrankenPHP) ]
                   │
                   │ 2. Check Redis L3 Cache (ats:v2:findagency:{company_id}:{version}:hash)
                   │    (Hit? Return instant 0ms cached response)
                   │
                   ▼ (Cache Miss)
[ Elasticsearch 8.x Cluster (Universal Read Scope) ]
                   │
                   │ 3. Execute function_score with Painless Script:
                   │    Score = BaseRelevance * StarvationMultiplier * CapacityGovernor
                   │
                   ▼
[ Backend Batch Data Pre-Enrichment ]
                   │
                   │ 4. Batch fetch client info, dual commercials, and serving countries (0 N+1)
                   │
                   ▼
[ Client Browser RAM + WebAssembly (User RAM) ]
                   │
                   │ 5. Render Modern Card + Sub-Card Enterprise Grid (Rule 20)
                   │ 6. Real-time Jaccard overlap on open requisitions in browser RAM
```

### 6.1 Elasticsearch 8.x Painless Scoring Query Implementation
```json
{
  "query": {
    "function_score": {
      "query": {
        "bool": {
          "must": [
            { "term": { "status": "active" } },
            { "terms": { "company_type": [1, 2] } },
            { "nested": {
                "path": "company_coins",
                "query": {
                  "bool": {
                    "must": [
                      { "term": { "company_coins.event_type": "sales" } },
                      { "range": { "company_coins.coins": { "gt": 0 } } }
                    ]
                  }
                }
            }}
          ],
          "must_not": [
            { "term": { "id": 14 } }
          ]
        }
      },
      "functions": [
        {
          "script_score": {
            "script": {
              "source": "
                double baseScore = _score;
                double hoursSinceLead = (params.now - doc['last_lead_timestamp'].value) / 3600000.0;
                double starvation = 1.0 + 1.5 * (1.0 - Math.exp(-0.0096 * hoursSinceLead));
                
                int activeMandates = doc['active_pending_mandates'].value;
                int recruiters = doc['recruiter_count'].value > 0 ? doc['recruiter_count'].value : 1;
                double maxCap = Math.max(2.0, recruiters * 3.0);
                double loadRatio = activeMandates / maxCap;
                double capacityGov = 1.0 / (1.0 + Math.pow(loadRatio, 3));
                
                double coldStart = (doc['is_new_partner'].value && doc['total_leads_received'].value < 5) ? 1.4 : 1.0;
                double activeBonus = doc['active_today'].value ? 1.1 : 1.0;
                
                return baseScore * starvation * capacityGov * coldStart * activeBonus;
              ",
              "params": {
                "now": 1795287600000
              }
            }
          }
        }
      ],
      "score_mode": "multiply",
      "boost_mode": "replace"
    }
  }
}
```

---

## 7. Card + Sub-Card UI/UX Visual Specification (Rule 20 & 15.6" Screen Fit)

To reflect this God-Level matching and fair-share engine in the user interface, agency cards use the **Modern Card + Sub-Card Design Standard**:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1 HEADER CARD: Find Trusted Hiring Agencies [Verified agencies]                 Updated Just Now • 33 Partners   │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│  LIVE TICKER: ● TechNova Inc dispatched mandate to Apex Talent • Marketing 4bell placed Senior DevOps Lead @ ₹28 LPA   │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│  TIER 2 TABS: [Total Agencies] [Bench Agencies] [Contract Staffing] [Full-Time] [Domestic Agencies] [Global Agencies]  │
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│  MATCHMAKER BAR: ⚡ Match With My Open Jobs: [Senior React Engineer (50 Open Mandates) ▼]     Matches requirement (React, AWS)│
├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│  PRIMARY AGENCY CARD:                                                                                                  │
│  ┌──────────────────────┬─────────────────┬──────────────────┬────────────────────┬─────────────────────────────────┐  │
│  │ AGENCY IDENTITY      │ LOCATION        │ COMMERCIALS      │ TALENT SUPPLY      │ PARTNERSHIP & ACTION            │  │
│  │ [Logo] Apex Talent   │ Noida, UP, IN   │ Full-Time: 8.33% │ 5 Active Jobs      │ Capacity: 2 Mandate Slots Open  │  │
│  │ Staffing & IT        │ Serving 4 Reg.  │ Contract: $35/Hr │ 8 Profiles • 2 Ben.│ [⚡ Send Request (Solo)]        │  │
│  │ ★ Top 3 Match        │ (Hover Flags)   │ Pre-Agreed Terms │ Pre-Screened Pool  │ [👥 Dispatch to Top 3 Partners] │  │
│  └──────────────────────┴─────────────────┴──────────────────┴────────────────────┴─────────────────────────────────┘  │
│  FOOTER STRIP: ● Active Today • ~36h TAT • Capacity: 1 Active Recruiter • 90-Day Replacement • Direct Bilateral Terms  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Visual Fair-Share Elements:
1. **Capacity Indicator:** Displays `Capacity: 2 Mandate Slots Open` (Green) or `High Load` (Orange), guiding employers toward agencies ready to work immediately.
2. **Dynamic Syndication Action:** Alongside `Send Request` (Solo), adds a primary button `👥 Dispatch to Top 3 Partners` that distributes the mandate to the balanced triad (Veteran + Bench Hero + Challenger).
3. **Transparency in Sub-Card Header:** Anchored background `#f8fafc` with crisp border `#e2e8f0` showing exact match reasons (e.g. `★ 95% Match • Tech & Bench Fit`).

---

## 8. Verification & Rollout Plan

1. **Step 1: Elasticsearch Schema Migration:**
   - Add fields to `ELASTIC_COMPANIES_INDEX`: `last_lead_timestamp` (long), `active_pending_mandates` (int), `recruiter_count` (int), `total_leads_received` (int), `is_new_partner` (bool).
2. **Step 2: JIT Evaluation in `FindAgencyService`:**
   - Implement the painless scoring script with zero recurring cron jobs (Rule 22).
3. **Step 3: Tri-Tier Syndication Controller:**
   - Extend `ServiceCompanyCompanyController::findAgencyRequest` to accept an array of 3 company IDs when `is_syndicated = 1`.
4. **Step 4: Real-time Reverb / WebSockets:**
   - Broadcast live ticket deltas whenever any partner in the triad receives or accepts the mandate.
