# Architectural Specification: Universal Tier-Stratified Agency Matching & Cross-Skill Lead Rotation Engine

**Document Type:** Universal System Architecture & Algorithmic Design Specification  
**Module:** Service Company Marketplace — Universal Requisition Matching & Lead Equalization (`/service_company/find-agency`)  
**Core Technologies:** Strict CQRS (Elasticsearch 8.x + Redis L3 Cache + MySQL ACID Ledger + Octane/FrankenPHP)  
**Date:** 2026-11-22  
**Standard Compliance:** Rule 1 (0% MySQL reads on search paths), Rule 2 (Elasticsearch Universal Scope), Rule 3 (Client-First Execution), Rule 4 (10K Concurrency), Rule 20 (Card + Sub-Card Design Standard), Rule 21 (Mandatory Storage & Directory Structure), Rule 22 (Zero-Cron & JIT Evaluation Standard).

---

## 1. Executive Summary & Core Paradigm

This specification defines the universal, platform-wide matching and lead distribution engine for JERO.works across **all technical skills, industries, geographies, and hiring models** (Java, Node.js, PHP, React, Python, DevOps, Cloud, AI/ML, Embedded).

### The Two Non-Negotiable Pillars:
1. **Demand-Side Guarantee (Zero Random Agencies for Employers):** Employers **NEVER** receive random, unqualified agencies. Every agency surfaced has verified expertise, active candidate pipelines, matching operating location, commercial rate compatibility, and verified candidate benches for that exact position.
2. **Supply-Side Guarantee (Universal Fair-Share Lead Rotation Across Skills):** Once an agency receives a lead in *any* technical category (e.g. Java), they enter a cooling cooldown and drop to the **bottom of the stack** for subsequent requirements (e.g. Node.js, PHP). This allows other equally capable and specialized partner agencies to receive leads, completely eliminating agency starvation.
3. **Tier-Stratified Intra-Bucket Shuffling (Zero Alphabetical or ID Bias):** When multiple verified agencies tie with the exact same high score, they are randomized **strictly within their score tier slice** (e.g., 20 agencies with 5,000 pts shuffle fairly across ranks 1–20; 10 agencies with 4,500 pts shuffle fairly across ranks 21–30).

---

## 2. Real-World Multi-Skill Scenario Walkthrough

```
                                  CROSS-SKILL UNIVERSAL FLOW
   ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
   │ CASE 1: JAVA REQUISITION (Company A)                                                             │
   │ 2,000 Verified Java Agencies Shortlisted ──> Scored by Points                                    │
   │ ┌────────────────────────────────────────────────────────┐                                       │
   │ │ TOP 20 JAVA AGENCIES RECEIVE ACTIVE INVITATIONS        │ ──> Dispatched & Accepted             │
   │ └────────────────────────────────────────────────────────┘                                       │
   │ Status: These 20 agencies are now flagged as "Active Lead Cooldown" in the Universal Ledger.     │
   └───────────────────────────────────────────────┬──────────────────────────────────────────────────┘
                                                   │
                                                   ▼
   ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
   │ CASE 2: NODE.JS REQUISITION (Company B)                                                          │
   │ 2,000 Verified Node.js Agencies Shortlisted ──> Evaluated against Cooldown Ledger                │
   │ • 5 Agencies in this list already received a lead in Case 1 (Cross-skill overlap)                │
   │ ┌────────────────────────────────────────────────────────┐                                       │
   │ │ 1,995 FRESH AGENCIES RANK ON TOP BY HIGHEST POINTS     │ ──> Top 20 receive Node.js Leads!     │
   │ └────────────────────────────────────────────────────────┘                                       │
   │ ┌────────────────────────────────────────────────────────┐                                       │
   │ │ 5 PREVIOUSLY INVITED AGENCIES ARE MOVED TO BOTTOM!     │ ──> Sits at the bottom of the stack   │
   │ └────────────────────────────────────────────────────────┘                                       │
   └───────────────────────────────────────────────┬──────────────────────────────────────────────────┘
                                                   │
                                                   ▼
   ┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
   │ CASE 3: PHP REQUISITION (Company C)                                                              │
   │ 50 Verified PHP Agencies Shortlisted                                                             │
   │ • 6 Agencies already received leads from Case 1 or Case 2                                        │
   │ ┌────────────────────────────────────────────────────────┐                                       │
   │ │ 44 FRESH AGENCIES RANK ON TOP BY HIGHEST POINTS        │ ──> Top agencies receive PHP Leads!   │
   │ └────────────────────────────────────────────────────────┘                                       │
   │ ┌────────────────────────────────────────────────────────┐                                       │
   │ │ 6 PREVIOUSLY INVITED AGENCIES MOVED TO BOTTOM STACK    │ ──> Sits below all fresh agencies     │
   │ └────────────────────────────────────────────────────────┘                                       │
   └──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Mathematical & Algorithmic Formulation

### 3.1 Step 1: Multi-Pillar Capability Scoring (Quality Gate)
For any requisition $J$ in skill stack $K$, an agency $A_i$ receives capability points:

$$\mathcal{S}_{\text{cap}}(J, A_i) = \mathcal{S}_{\text{skill}} + \mathcal{S}_{\text{geo}} + \mathcal{S}_{\text{comp}} + \mathcal{S}_{\text{model}} + \mathcal{S}_{\text{bench}}$$

- $\mathcal{S}_{\text{skill}}$: Actual verified positions, placements, and candidate pipeline in stack $K$ ($+2,000$ to $+3,000$ pts).
- $\mathcal{S}_{\text{geo}}$: Operating in the same country as the requisition ($+1,500$ pts).
- $\mathcal{S}_{\text{comp}}$: Fee % / Hourly contract rate aligns with client budget ($+1,000$ pts).
- $\mathcal{S}_{\text{model}}$: Contract (C2C/C2H) vs Full-Time fit ($+500$ pts).
- $\mathcal{S}_{\text{bench}}$: Immediate verified bench candidates in stack $K$ ($+800$ pts).

$$\text{Score Range: } 1,000 \text{ to } 6,800 \text{ points}$$

---

### 3.2 Step 2: Universal Cooldown Flagging (The Cross-Skill Bottom-Stack Demotion)
A global Redis set and Elasticsearch field tracks active lead cooldown:

$$\text{HasActiveLead}(A_i, t) = \begin{cases} 
1 & \text{if Agency received an invitation within Cooldown Window } \tau \text{ (e.g. 7 days)} \\
0 & \text{if Agency has received 0 invitations within } \tau
\end{cases}$$

When sorting the candidate agencies, Elasticsearch applies **Primary Multi-Key Sorting**:
1. **Key 1 (Cooldown Status ASC):** `HasActiveLead == 0` comes before `HasActiveLead == 1`.
2. **Key 2 (Capability Score DESC):** Highest capability points first.
3. **Key 3 (Intra-Bucket Deterministic Jitter):** Random shuffle strictly among tied points.

$$\text{Sort Order: } [\text{HasActiveLead ASC}, \quad \mathcal{S}_{\text{cap}} \text{ DESC}, \quad \text{RandomJitter}]$$

**Outcome:**
- All agencies with `HasActiveLead == 0` occupy the upper part of the results (Ranks 1 to $N$).
- All agencies with `HasActiveLead == 1` drop **below all fresh agencies**, regardless of whether their lead was for Java, Node.js, or PHP!

---

### 3.3 Step 3: Tier-Stratified Intra-Bucket Random Shuffling (Equal Points Fairness)
When multiple agencies have identical capability points, alphabetical or database ID bias is completely eliminated:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                          TIER-STRATIFIED INTRA-BUCKET JITTER                           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│  BUCKET 1: 5,000 Points (20 Agencies Tie)                                              │
│  └── Shuffled randomly WITHIN Ranks 1 to 20!                                           │
│      (No agency outside Bucket 1 can enter Ranks 1–20)                                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│  BUCKET 2: 4,500 Points (10 Agencies Tie)                                              │
│  └── Shuffled randomly WITHIN Ranks 21 to 30!                                          │
│      (Guaranteed to sit below Bucket 1, but fairly shuffled within 21–30)              │
├────────────────────────────────────────────────────────────────────────────────────────┤
│  BUCKET 3: 4,000 Points (35 Agencies Tie)                                              │
│  └── Shuffled randomly WITHIN Ranks 31 to 65!                                          │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

#### Mathematical Formulation:
To prevent pagination jumps while ensuring intra-bucket fairness, the tie-breaker uses a **Session-Seeded Cryptographic Pseudo-Random Hash**:

$$\text{Jitter}(A_i, \text{Seed}) = \frac{\text{CRC32}(A_i\text{.id} \oplus \text{Seed})}{2^{32} - 1}$$

- In Elasticsearch, this is implemented natively using `random_score` with a session seed (e.g. `params.seed = request_session_id`).
- **Guaranteed Invariant:**
  $$\mathcal{S}_{\text{cap}}(A_a) > \mathcal{S}_{\text{cap}}(A_b) \implies \text{Rank}(A_a) < \text{Rank}(A_b)$$
  *An agency with lower points can NEVER leapfrog an agency with higher points.*
- Within the same score tier ($\mathcal{S}_{\text{cap}}(A_a) == \mathcal{S}_{\text{cap}}(A_b)$), the relative ordering is randomized fairly so no company has an unfair alphabetical or indexing advantage.

---

### 3.4 Step 4: Graceful Multi-Constraint Fallback (When Direct Skill Matches Are Zero)
When a requisition has rare, niche, or unindexed skills (e.g., Haskell, Embedded Rust, COBOL) where zero agencies have verified direct positions:

1. **Phase 1: Skill Constraint Relaxation:**
   Drop the literal skill requirement and evaluate secondary relevance:
   - Operating Country Alignment ($+1,500$ pts)
   - Domain Industry (Fintech, Telecom, Embedded) ($+1,000$ pts)
   - Commercial Alignment (Fee % & Contract Rate) ($+800$ pts)
   - Recruiter User Headcount & Capacity ($+500$ pts)
2. **Phase 2: Universal Cooldown & Tier Shuffling Still Applies:**
   Even in fallback mode:
   - Fresh agencies without recent leads rank at the top.
   - Agencies with recent leads drop to the bottom.
   - Tied points shuffle randomly within their tier.
- **Employer Outcome:** The employer is never met with an empty screen or error message; they receive the top 20 best-positioned local partner agencies in that industry.

---

## 4. Elasticsearch 8.x Production Query Implementation

This query executes the entire 3-step logic natively in a single pass with sub-10ms latency:

```json
{
  "query": {
    "function_score": {
      "query": {
        "bool": {
          "must": [
            { "term": { "status": "active" } },
            { "terms": { "company_type": [1, 2] } }
          ],
          "must_not": [
            { "term": { "id": 14 } }
          ]
        }
      },
      "functions": [
        {
          "filter": { "terms": { "job_metronics_skills.skill_name": ["node.js", "nodejs"] } },
          "weight": 2500
        },
        {
          "filter": { "term": { "commericial_countries.country_id": 101 } },
          "weight": 1500
        },
        {
          "filter": { "term": { "commericials.commtype": 2 } },
          "weight": 500
        },
        {
          "filter": { "range": { "bench_candidates.id": { "gt": 0 } } },
          "weight": 800
        }
      ],
      "score_mode": "sum",
      "boost_mode": "replace"
    }
  },
  "sort": [
    {
      "_script": {
        "type": "number",
        "script": {
          "source": "return params.cooldown_ids.contains(doc['id'].value) ? 1 : 0;",
          "params": {
            "cooldown_ids": [102, 105, 189, 230, 312]
          }
        },
        "order": "asc"
      }
    },
    {
      "_score": {
        "order": "desc"
      }
    },
    {
      "_script": {
        "type": "number",
        "script": {
          "source": "return (doc['id'].value * params.seed) % 10000;",
          "params": {
            "seed": 48291
          }
        },
        "order": "asc"
      }
    }
  ]
}
```

### Explanation of Sort Hierarchy:
1. `cooldown_ids.contains(doc['id'].value) ? 1 : 0`: Agencies with recent leads get `1`, fresh agencies get `0`. All fresh agencies (`0`) strictly sort above cooled-down agencies (`1`).
2. `_score DESC`: Within the fresh group, agencies with the highest capability points rank on top.
3. `(doc['id'].value * params.seed) % 10000`: For agencies with the exact same score, shuffles them pseudo-randomly within their score tier.

---

## 5. Architectural Safeguards (The 3 Pro Rules)

1. **Lead Acceptance & SLA Re-Queueing:**
   - If an agency in the Top 20 receives an invitation but **does not respond within 48 hours**, their invitation is automatically revoked.
   - The agency stays in the bottom stack for an extra 7 days (punishing ghosting).
   - The **21st ranked agency** is instantly notified via WebSocket to take their place.
2. **Cooldown Release Event:**
   - An agency's cooldown expires automatically after **7 days**, OR when they successfully submit 3 qualified candidate profiles for their active mandate.
3. **Cross-Skill Independence Guard:**
   - If an agency has separate dedicated recruiter teams (e.g. 10 recruiters across Java, Python, and SAP), their cooldown can be calibrated per recruiter capacity so large enterprise staffing firms are not unfairly paralyzed if they have available bandwidth.
