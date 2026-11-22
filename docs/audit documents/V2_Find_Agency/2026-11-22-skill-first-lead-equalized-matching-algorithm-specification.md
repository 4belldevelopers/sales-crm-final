# Architectural Specification: Skill-First, Lead-Equalized Round-Robin (SLERR) Matching Engine

**Document Metadata:**
- **Module:** Service Company Marketplace (`/service_company/find-agency`)
- **Author:** Antigravity (Google DeepMind)
- **Standard:** Strict CQRS (0% MySQL Reads, Universal ES Reads, Atomic Redis Caching, Zero-Cron JIT)
- **Design Standard:** Rule 20 (Modern Card + Sub-Card Enterprise Design System with Anchored `#f8fafc` Headers)
- **Date:** 2026-11-22

---

## 1. Executive Summary & Core Paradigm

Traditional marketplace matching engines suffer from the **"Rich-Get-Richer" Monopoly Effect**, where a handful of early or top-rated agencies receive 100% of client leads while thousands of equally capable agencies receive 0.

Arbitrary time-based cooldowns (e.g. 48-hour timers) fail because:
1. In high-volume tech stacks (e.g. Java), an agency can wait out 48 hours and repeatedly capture leads, starving the remaining 1,980 agencies.
2. In low-volume tech stacks (e.g. specialized ERP/Scala), an arbitrary timer expires before any other agency has received a lead, breaking fair distribution.

The **Skill-First, Lead-Equalized Round-Robin (SLERR)** engine solves this by establishing an immutable 4-level sorting hierarchy:
1. **Quality Guarantee (Match Tier):** Relevant skill matches ALWAYS take absolute precedence over unmatched agencies. A skill-matched agency with prior leads will always rank above an unskilled agency with 0 leads.
2. **Lead Equity (Min-Lead Stratification):** Within the same skill match tier, agencies with fewer leads (`lead_count = 0`) are prioritized over agencies with more leads (`lead_count = 1`), until ALL agencies in the pool reach lead parity.
3. **Capability Score (Points):** Within the same match tier and lead-count bucket, agencies are sorted by merit (bench availability, domestic country alignment, rate fit, hiring model).
4. **Intra-Bucket Deterministic Randomization:** Tied agencies within the same point score are shuffled randomly strictly across their rank slice (e.g. Ranks 1–20).

---

## 2. Mathematical Formalization & Sorting Tuple

Every shortlisted agency document $A_i$ evaluated against employer mandate $M$ is projected into a 4-dimensional sorting tuple:

$$\vec{R}(A_i, M) = \Big\langle \mathcal{T}_{\text{match}}(A_i, M),\; -\mathcal{C}_{\text{leads}}(A_i),\; \mathcal{S}_{\text{capability}}(A_i, M),\; \mathcal{H}_{\text{rand}}(A_i, M) \Big\rangle$$

```
ORDER BY:
  1. match_tier DESC           (1 = Verified Skills Match, 0 = Fallback / Non-Skills)
  2. lead_count ASC            (0 leads first, then 1 lead, then 2 leads, until parity)
  3. capability_score DESC     (Higher points: Bench + Country + Rate Fit + Hiring Model)
  4. intra_bucket_hash ASC     (Seeded pseudo-random shuffle strictly within tied scores)
```

### Hierarchy Breakdown

| Tuple Dimension | Metric / Type | Values | Mathematical Function & Role |
| :--- | :--- | :--- | :--- |
| **Dimension 1: $\mathcal{T}_{\text{match}}$** | `match_tier` | `1` (Match) or `0` (Fallback) | **Skills First Guarantee.** If mandate skills match agency competencies, tier = 1. Else 0. Tier 1 agencies ALWAYS beat Tier 0 agencies. |
| **Dimension 2: $\mathcal{C}_{\text{leads}}$** | `lead_count` | `0, 1, 2, ... N` (Integer) | **Lead Count Equity.** Ascending sort (`ASC`). Agencies with 0 leads rank above 1 lead. When all reach 1 lead, all tie on this dimension and compete on capability score. |
| **Dimension 3: $\mathcal{S}_{\text{capability}}$** | `points` | `0` to `6,800` pts | **Capability Ranking.** Primary skills (+3,000), Domestic country (+1,500), Salary/Rate fit (+1,000), Bench joiners (+800), Hiring model (+500). |
| **Dimension 4: $\mathcal{H}_{\text{rand}}$** | `intra_bucket_hash` | `0` to `9,999` (Integer) | **Fair Tie-Breaking.** `(agency_id * seed) % 10000`. Ensures identical-scoring agencies rotate across ranks 1–20 without bias. |

---

## 3. Walkthrough of User Scenarios

### Scenario 1: High-Volume Stack (2,000 Java Agencies)
- **Context:** 2,000 agencies match Java. 20 have previously received 1 lead (`lead_count = 1`). 1,980 have received 0 leads (`lead_count = 0`).
- **Execution:**
  1. $\mathcal{T}_{\text{match}} = 1$ for all 2,000 agencies.
  2. $\mathcal{C}_{\text{leads}} = 0$ for 1,980 agencies $\rightarrow$ **Ranked 1 to 1,980**.
  3. $\mathcal{C}_{\text{leads}} = 1$ for 20 agencies $\rightarrow$ **Ranked 1,981 to 2,000**.
  4. Top 20 of the 1,980 receive invitations. Their `lead_count` becomes 1.
  5. In subsequent mandates, the remaining 1,960 agencies with 0 leads are prioritized.
  6. **Outcome:** This continues progressively until all 2,000 agencies have received at least 1 lead.

### Scenario 2: Perfect Lead Parity (25 Specialized Agencies with 1 Lead Each)
- **Context:** 25 agencies shortlisted for a niche skill. All 25 already have 1 lead (`lead_count = 1`).
- **Execution:**
  1. $\mathcal{T}_{\text{match}} = 1$ for all 25.
  2. $\mathcal{C}_{\text{leads}} = 1$ for all 25 (Perfect Parity $\rightarrow$ Zero penalty differential).
  3. $\mathcal{S}_{\text{capability}}$ takes over: agencies are ranked 1 to 25 based on points (bench joiners, domestic proximity, pricing).
  4. If tied on points, $\mathcal{H}_{\text{rand}}$ shuffles them fairly.
  5. **Outcome:** When everyone has equal leads, merit (capability points) governs ranking.

### Scenario 3: Universal Fairness Across Diverse Skills (Node.js & PHP)
- **Context:** An agency received a lead in Java yesterday. Today, an employer posts a Node.js mandate.
- **Execution:**
  - In the Node.js pool, if there are qualified agencies with `lead_count = 0`, they rank above the agency with `lead_count = 1`.
  - The agency that did Java is not banned or blocked; it simply yields priority to other qualified partners who have not yet received work.

### Scenario 4: Quality Overrides Lead Count (36 Agencies, 5 Skilled with Leads vs 31 Unskilled without Leads)
- **Context:** 36 agencies in total.
  - 5 agencies match the required skill, but have `lead_count = 1`.
  - 31 agencies have `lead_count = 0`, but DO NOT match the required skill.
- **Execution:**
  - The 5 skilled agencies have $\mathcal{T}_{\text{match}} = 1$.
  - The 31 unskilled agencies have $\mathcal{T}_{\text{match}} = 0$.
  - Because $\mathcal{T}_{\text{match}}$ is Sort Key #1, the 5 skilled agencies occupy **Ranks 1 to 5**.
  - The 31 unskilled agencies occupy **Ranks 6 to 36**.
- **Outcome:** **Employers NEVER get unskilled/irrelevant agencies.** Quality is the absolute foundation; fair lead distribution operates strictly among qualified peers.

---

## 4. Elasticsearch CQRS Implementation (0% MySQL Reads)

The search query uses native Elasticsearch multi-level sorting and function scoring:

```json
{
  "query": {
    "bool": {
      "filter": [
        { "term": { "is_agency": 1 } },
        { "term": { "status": "active" } }
      ],
      "should": [
        { "terms": { "skills": ["java", "spring boot"], "boost": 3000 } },
        { "term": { "country_id": 101, "boost": 1500 } },
        { "term": { "hiring_model": "contract", "boost": 500 } },
        { "range": { "bench_candidates_count": { "gt": 0 }, "boost": 800 } }
      ]
    }
  },
  "sort": [
    {
      "_script": {
        "type": "number",
        "script": {
          "source": "return doc['skills'].contains(params.target_skill) ? 1 : 0;",
          "params": { "target_skill": "java" }
        },
        "order": "desc"
      }
    },
    {
      "lead_count": {
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
          "params": { "seed": 83921 }
        },
        "order": "asc"
      }
    }
  ],
  "size": 20
}
```

---

## 5. UI/UX Dual-State Presentation (Rule 20 Design Standard)

### State A: UI When MATCHED (Skill-Aligned Mandate)
- **Top Bar:** Emerald green pill `[⚡ Exact Mandate Match • 98% Fit]` + `[Rank #01 in Rotation]`.
- **Sub-Cards:**
  - Sub-Card 1 (Identity): Verified agency badge, staff size.
  - Sub-Card 2 (Location): Domestic country, timezone.
  - Sub-Card 3 (Commercials): Rate fit within budget.
  - Sub-Card 4 (Talent): Active bench count with glowing indicator.
  - Sub-Card 5 (Action): `[⚡ Send Request]` + Top syndication bar: `[👥 Dispatch Mandate to Top 20 Vendors]`.

### State B: UI When UNMATCHED (Graceful Constraint Relaxation)
- **Alert Banner:** Soft Slate/Amber `#f8fafc` banner: *"No exact skill matches found for '{skill}'. Showing verified domestic partners aligned by Location & Domain Industry."*
- **Top Bar:** Soft Blue/Slate pill `[📍 Geographic & Domain Match • 65% Fit]`.
- **Sub-Cards:** Missing skill tags replaced by domain credentials (`Banking & Fintech Specialist`, `Domestic Sourcing SLA`).
- **Action:** Changes to `[⚡ Send Custom Mandate]`.
