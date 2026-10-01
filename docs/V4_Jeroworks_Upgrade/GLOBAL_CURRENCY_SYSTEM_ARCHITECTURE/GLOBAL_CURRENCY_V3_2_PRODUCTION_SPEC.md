# JERO Global Currency Engine — v3.2 Final Production Corrections & Acceptance Specification

> **System Profile:** JERO Multi-Tenant B2B Hiring SaaS + Global Talent Marketplace  
> **Architecture Pattern:** CQRS (MySQL Strictly Writes, Elasticsearch Strictly Reads, Redis Horizon Queues)  
> **Document Status:** ARCHITECTURE FROZEN / IMPLEMENTATION-READY SPECIFICATION  
> **Document Version:** 3.2 — Final Production Hardening & Acceptance Specification  
> **Parent Architecture:** `global_currency_and_marketplace_architecture.md` (v3.1)  

---

## 1. Executive Summary & Purpose

This specification represents the **final production delta (v3.2)** to the JERO Global Currency Engine architecture. Rather than replacing the foundational model established in v3.1, this document formalizes the **six P0 corrections** and **operational hardening standards** required to achieve institutional financial safety, Elasticsearch pagination consistency, and auditable event reproduction.

---

## 2. P0 — Contractual FX vs. Reference FX Precedence Model

### 2.1. The Commercial Gap
Market reference exchange rates (e.g. daily mid-market rates from ExchangeRate-API) fluctuate daily. However, enterprise commercial agreements frequently specify a fixed or peg-adjusted contractual exchange rate (e.g. *"All invoices under SOW #4021 shall convert INR to USD at a fixed rate of 85.00 INR/USD"*). Conflating daily reference rates with contractual obligations produces technically reproducible but commercially erroneous invoices and candidate payouts.

### 2.2. Explicit FX Source Hierarchy
Every commercial transaction event (Offer, Placement, Invoicing, Billing Adjustment) resolves exchange rates according to a strict deterministic precedence chain:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. CONTRACTUAL FX (Highest Precedence)                                      │
│    - Explicitly agreed rate stipulated in the SOW, Master Services          │
│      Agreement (MSA), or Candidate Offer Letter.                            │
│    - Source reference: `contract:sow_id` or `offer:offer_id`.               │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (if absent)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. REFERENCE FX (System Default Standard)                                   │
│    - Active daily snapshot from `currency_system_state`                     │
│      (ExchangeRate-API mid-market verified feed).                           │
│    - Source reference: `snapshot:YYYYMMDD-NNN`.                             │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ (if explicitly permitted by SuperAdmin)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. MANUAL OVERRIDE (Audited Emergency Fallback)                             │
│    - Authorized override with mandatory dual-approval and audit logging.    │
│    - Source reference: `audit_log:override_id`.                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

* **`RULE-COMMERCIAL-004` (Contractual Precedence):** If a candidate placement or client invoice is tied to a contract specifying a contractual FX rate, that rate MUST be used for commercial valuation, overriding the active daily reference snapshot.
* **`RULE-COMMERCIAL-005` (Indicative Isolation):** Indicative conversions (Add Talent preview pills, Marketplace candidate card converted badges) MUST ALWAYS use the **Reference FX** snapshot. They never adopt client-specific contractual FX rates.

### 2.3. Hardened `commercial_fx_snapshots` Schema
The commercial snapshot schema is updated to record the explicit rate source, its forensic reference, and the working hours source:

```sql
CREATE TABLE `commercial_fx_snapshots` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Idempotency key: e.g. offer:18291:accepted:v1',
  `entity_type` VARCHAR(50) NOT NULL COMMENT 'job_applications, offers, placements, invoices',
  `entity_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(50) NOT NULL COMMENT 'OFFER_ACCEPTED, PLACED, INVOICED, ADJUSTMENT',
  
  -- Candidate Pay Details
  `pay_currency` VARCHAR(3) NOT NULL,
  `pay_amount` DECIMAL(20,6) NOT NULL,
  `pay_basis` ENUM('HOURLY', 'MONTHLY', 'ANNUAL') NOT NULL,
  `pay_fx_rate` DECIMAL(20,10) NOT NULL COMMENT 'Units of pay currency per 1.00 USD',
  `pay_fx_source` ENUM('CONTRACTUAL', 'REFERENCE', 'MANUAL_OVERRIDE') NOT NULL DEFAULT 'REFERENCE',
  `pay_fx_source_ref` VARCHAR(100) NULL COMMENT 'e.g. contract:4812, snapshot:20260930-001',

  -- Client Bill Details
  `bill_currency` VARCHAR(3) NOT NULL,
  `bill_amount` DECIMAL(20,6) NOT NULL,
  `bill_basis` ENUM('HOURLY', 'MONTHLY', 'ANNUAL') NOT NULL,
  `bill_fx_rate` DECIMAL(20,10) NOT NULL COMMENT 'Units of bill currency per 1.00 USD',
  `bill_fx_source` ENUM('CONTRACTUAL', 'REFERENCE', 'MANUAL_OVERRIDE') NOT NULL DEFAULT 'REFERENCE',
  `bill_fx_source_ref` VARCHAR(100) NULL COMMENT 'e.g. sow:9914, snapshot:20260930-001',

  -- Forensic Context
  `annual_hours_basis` INT UNSIGNED NOT NULL DEFAULT 2080,
  `annual_hours_source` ENUM('CONTRACT', 'JOB', 'TENANT', 'GLOBAL', 'MANUAL') NOT NULL DEFAULT 'GLOBAL',
  `annual_hours_source_ref` VARCHAR(100) NULL,
  
  `snapshot_version` VARCHAR(30) NOT NULL,
  `effective_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_commercial_entity` (`entity_type`, `entity_id`),
  INDEX `idx_commercial_event` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. P0 — Search Completeness & Adaptive Overfetch Pagination Strategy

### 3.1. Accuracy vs. Completeness Invariant
* **`RULE-SEARCH-003` (Search Completeness & Accuracy Distinction):**
  1. **Returned-Result Accuracy:** The browser reconciliation layer guarantees that 100% of candidates rendered on screen strictly satisfy the recruiter's price filter under the current active FX snapshot.
  2. **Membership Completeness:** Elasticsearch candidate retrieval is an approximate clustering query. Because exchange rates fluctuate between reindexing cycles, Elasticsearch range filters cannot mathematically guarantee complete membership if currency drift exceeds the indexing buffer.
  3. **Drift Ceiling Alert:** If observed FX drift for any active currency exceeds $10\%$ against the last indexed snapshot, an emergency reindex worker is dispatched automatically.

### 3.2. Adaptive Overfetch & Cursor Pagination Pipeline
When the recruiter applies an exact numeric range filter (e.g. `$40.00 – $60.00 / hr`):
1. **The Short-Page Problem:** If Elasticsearch returns 20 documents and client-side reconciliation discards 6 documents that drifted out of range, the user sees an incomplete page (14 candidates) despite valid candidates existing in subsequent Elasticsearch partitions.
2. **Adaptive Overfetch Factor:** The query pipeline applies an adaptive multiplier based on recent reconciliation discard metrics:
   $$\text{OverfetchFactor} = \min\left(2.0, \; 1.0 + (\text{ReconciliationDiscardRate} \times 1.5)\right)$$
   *(Default factor is $1.25$ if no discard history exists; minimum is $1.0$, maximum is $2.0$).*
3. **Execution Flow:**
   - User requests page size $N = 20$.
   - Elastic query fetches $\text{FetchLimit} = \lceil N \times \text{OverfetchFactor} \rceil = \lceil 20 \times 1.25 \rceil = 25$ documents.
   - Browser reconciliation evaluates candidates in order, collecting valid matches until $N = 20$ valid candidates are selected.
   - **Cursor Pagination Continuity:** Elasticsearch pagination uses `search_after` tied to the sort key and document ID of the **last evaluated document** from Elasticsearch (not the last displayed document), guaranteeing zero skipped candidates across subsequent page fetches.

---

## 4. P0 — Financial Precision & Acceptance Standard AC-01

### 4.1. The Round-Trip Reversibility Fallacy
A test asserting that converting $1,000,000\text{ JPY} \to \text{USD} \to \text{JPY}$ must reverse to within $0.01\text{ JPY}$ is mathematically flawed because currency minor-unit truncation (`USD` has 2 decimal places, `JPY` has 0) discards fractional cents during intermediate display conversions.

### 4.2. Decomposed Precision Acceptance Standards
AC-01 is formally split into three rigorous, mathematically valid tests:

* **`AC-01A` (Mathematical Calculation Precision):**
  Intermediate currency conversions in `BCMath` or financial calculation routines MUST maintain `DECIMAL(20,10)` with zero rounding. For any two non-zero positive rates $R_1, R_2$:
  $$\left| \frac{\text{Amount}}{R_1} \times R_1 - \text{Amount} \right| < 10^{-9}$$
* **`AC-01B` (Minor-Unit Display Rounding):**
  Monetary amounts formatted for presentation or persisted to billing tables are rounded according to the target currency's ISO `minor_unit` using `ROUND_HALF_UP`.
  - USD: Rounded to 2 decimals ($12.456 \to 12.46$).
  - JPY: Rounded to 0 decimals ($1245.6 \to 1246$).
  - KWD: Rounded to 3 decimals ($12.4567 \to 12.457$).
* **`AC-01C` (Bounded Round-Trip Tolerance):**
  A round-trip conversion without intermediate truncation must satisfy:
  $$\text{RoundTripError} = \left| \text{Convert}\left(\text{Convert}(A, C_1 \to C_2), C_2 \to C_1\right) - A \right| \le \epsilon$$
  Where $\epsilon$ is bounded by the precision of the currency rate ($10^{-6}$).

---

## 5. P0 — Core Engine Signed Decimal Support & Business Context Isolation

### 5.1. Separation of Responsibilities
A financial engine that universally rejects negative numbers cannot support:
- Credit notes and client refunds.
- Upstream billing adjustments and debit corrections.
- Cross-currency margin variances and negative spreads.

### 5.2. Architecture Rule
* **`RULE-CURRENCY-009` (Signed Decimal Neutrality):** The low-level `JeroCurrencyEngine` supports all finite, signed decimal values ($-\infty < x < +\infty$).
* **Business-Layer Validation:** Context-specific validators enforce domain rules:
  - **Candidate Cost / Bill Rate:** Rejects values $\le 0$.
  - **Credit Note / Refund:** Permits signed negative values.
  - **Accounting Variance:** Permits signed negative or positive values.

### 5.3. Corrected JavaScript Engine Conversion Sequence
The engine now validates currency support **before** inspecting amount magnitude, ensuring invalid currency codes are never silently accepted on zero amounts:

```javascript
convert: function(amount, fromCode, toCode) {
    // 1. Validate currency code formats
    const cleanFrom = this.cleanCode(fromCode);
    const cleanTo = this.cleanCode(toCode);
    if (!cleanFrom || !cleanTo) {
        return { status: 'INVALID_INPUT', amount: null, error: 'Source and target currency codes are required' };
    }

    // 2. Validate currency support against active bundle
    const from = this.getRate(cleanFrom);
    const to = this.getRate(cleanTo);
    if (from.status !== 'VALID') {
        return { status: from.status, amount: null, error: from.error };
    }
    if (to.status !== 'VALID') {
        return { status: to.status, amount: null, error: to.error };
    }

    // 3. Validate numeric value (supports signed numbers)
    if (amount === null || typeof amount === 'undefined' || amount === '') {
        return { status: 'INVALID_INPUT', amount: null, error: 'Amount is required' };
    }
    const numericAmount = Number(amount);
    if (isNaN(numericAmount) || !isFinite(numericAmount)) {
        return { status: 'INVALID_INPUT', amount: null, error: 'Amount must be a finite numeric value' };
    }

    // 4. Identity bypass
    if (cleanFrom === cleanTo) {
        return { status: 'VALID', amount: numericAmount, error: null };
    }

    // 5. Zero handling (validated only after currencies are verified valid)
    if (numericAmount === 0) {
        return { status: 'VALID', amount: 0, error: null };
    }

    // 6. High-precision conversion
    const amountUsd = numericAmount / from.rate;
    const converted = amountUsd * to.rate;

    return { status: 'VALID', amount: converted, error: null };
}
```

---

## 6. P1 — Currency Capability Model & Master Table Refactoring

### 6.1. Decoupled Capability Flags
The mutually exclusive `lifecycle_state` ENUM is replaced with an authoritative operational `status` plus granular functional capability flags:

```sql
CREATE TABLE `currencies` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(3) NOT NULL UNIQUE COMMENT 'ISO-4217 (e.g. USD, EUR, INR)',
  `name` VARCHAR(100) NOT NULL,
  `symbol` VARCHAR(10) NOT NULL,
  `numeric_code` CHAR(3) DEFAULT NULL,
  `minor_unit` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  
  -- Lifecycle Status
  `status` ENUM('ACTIVE', 'DEPRECATED', 'UNSUPPORTED') NOT NULL DEFAULT 'ACTIVE',
  
  -- Granular Functional Capabilities
  `supports_display` TINYINT(1) NOT NULL DEFAULT 1,
  `supports_conversion` TINYINT(1) NOT NULL DEFAULT 1,
  `supports_search` TINYINT(1) NOT NULL DEFAULT 1,
  `supports_commercial` TINYINT(1) NOT NULL DEFAULT 1,
  
  `getKey` INT NOT NULL DEFAULT 0 COMMENT 'Legacy integer index compatibility',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_currencies_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 7. P1 — Canonical Hourly Rate Elasticsearch Normalization

### 7.1. Rate Basis Ambiguity Elimination
Elasticsearch search indices MUST normalize monetary values to a single, unambiguous canonical time basis: **Hourly Rate in USD**.

### 7.2. Standardized Document Schema in `ELASTIC_CANDIDATES_BENCH`
```json
{
  "id": 10421,
  "commercials": {
    "pay_rate_amount": 1200000.00,
    "pay_rate_currency": "INR",
    "pay_rate_basis": "ANNUAL",
    "bill_rate_amount": 30000.00,
    "bill_rate_currency": "USD",
    "bill_rate_basis": "ANNUAL",
    "effective_annual_hours": 2080
  },
  "search_normalization": {
    "canonical_basis": "HOURLY",
    "bill_rate_usd_hourly_index": 14.42,
    "pay_rate_usd_hourly_index": 6.78,
    "fx_version": "20260930-001",
    "fx_timestamp": "2026-09-30T00:00:00Z"
  }
}
```

* When a recruiter searches by Annual or Monthly ranges, the query builder translates the filter bounds to the canonical Hourly USD baseline:
  $$\text{QueryMinHourly} = \frac{\text{FilterMinAnnual}}{2080}$$
* Elasticsearch queries filter against `search_normalization.bill_rate_usd_hourly_index` exclusively, eliminating cross-basis comparison defects.

---

## 8. P1 — Snapshot Rollback, Revocation, and Outbox Concurrency

### 8.1. Operational Rollback Workflow
If an activated snapshot is discovered to contain corrupted or distorted rates after publication:
1. SuperAdmin triggers the `currency:rollback` command.
2. In a single MySQL transaction:
   - Mark the current snapshot as `status = 'REVOKED'`.
   - Update `currency_system_state` pointing `active_snapshot_id` back to the previous known valid snapshot ID.
   - Insert outbox event `SNAPSHOT_REVOKED` containing the target rollback version.
3. The outbox dispatcher atomically points Redis `currency:active_version` to the restored version, updates the CDN active pointer, and enqueues an urgent Elasticsearch reindex.

### 8.2. Outbox Table Concurrency & Retry Locking
To prevent race conditions between concurrent Horizon workers processing outbox events:

```sql
CREATE TABLE `currency_outbox_events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(50) NOT NULL,
  `payload` JSON NOT NULL,
  `status` ENUM('PENDING', 'PROCESSING', 'PROCESSED', 'FAILED') NOT NULL DEFAULT 'PENDING',
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_at` DATETIME NULL,
  `locked_by` VARCHAR(100) NULL COMMENT 'Worker process UUID',
  `next_attempt_at` DATETIME NULL,
  `last_error` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` DATETIME NULL,
  INDEX `idx_outbox_worker_poll` (`status`, `next_attempt_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Workers claim batches using atomic row-level reservation:
```sql
UPDATE `currency_outbox_events`
SET `status` = 'PROCESSING', `locked_at` = NOW(), `locked_by` = :worker_id
WHERE `status` IN ('PENDING', 'FAILED')
  AND (`next_attempt_at` IS NULL OR `next_attempt_at` <= NOW())
  AND `attempts` < 5
ORDER BY `id` ASC
LIMIT 10;
```

---

## 9. P2 — Multi-Tenant Scoping, AUTO, and ORIGINAL Semantics

### 9.1. Historical Immutability on Tenant Currency Reconfiguration
* **`RULE-TENANT-001` (Tenant Currency Mutation Immutability):** Changing a tenant's operating currency (e.g. from `INR` to `USD` in company settings) affects ONLY future candidates, default form selections, and forward financial reporting. It MUST NEVER mutate existing candidate records, past job offers, historical placements, or issued invoices.

### 9.2. Resolution of `AUTO` Mode
The header currency selector `AUTO` option resolves deterministically:
$$\text{ResolvedCurrency} = \text{user.display\_currency} \;\lor\; \text{company.operating\_currency} \;\lor\; \text{'USD'}$$

### 9.3. Marketplace `ORIGINAL` Mode Behavior
* **Display Behavior:** Candidate cards render each candidate's exact contractual rate in their original currency (e.g. ₹1,200,000 / yr, €45,000 / yr, $60 / hr).
* **Search Filter Behavior:** In `ORIGINAL` mode, cross-currency numeric slider filtering is disabled, or explicitly converted to the tenant's default currency with an explanatory UI badge ("Filtering applied in USD equivalent").

---

## 10. Comprehensive Verification Matrix (AC-01 through AC-13)

| Gate | Category | Test Condition | Expected Result | Priority |
| :--- | :--- | :--- | :--- | :--- |
| **AC-01A** | Precision | Mathematical calculation precision in `BCMath` | Reversible within $10^{-9}$ with zero intermediate rounding | `P0` |
| **AC-01B** | Precision | Final presentation rounding | Rounded strictly to ISO `minor_unit` using `ROUND_HALF_UP` | `P0` |
| **AC-02** | Immutability | Daily FX update applied | Existing candidates/invoices maintain exact historical figures | `P0` |
| **AC-03** | Idempotency | Worker retries offer acceptance event (`offer:101:v1`) | Duplicate record safely rejected by unique `event_key` | `P0` |
| **AC-04** | Concurrency | 50 concurrent snapshot promotions | `currency_system_state` maintains exactly 1 active ID | `P0` |
| **AC-05** | Safety | Convert with unsupported currency code `XYZ` | Returns `{ status: 'UNSUPPORTED' }`; never defaults to `1.0` | `P0` |
| **AC-06** | Completeness | Rate drifts $6\%$ between index cycles | Dynamic ES buffer + adaptive overfetch returns all valid results | `P0` |
| **AC-07** | Backward Comp | Legacy candidate created with integer key `0` | Dual-write persists `pay_rate_currency = 0` and `code = 'INR'` | `P1` |
| **AC-08** | Performance | 10,000 concurrent marketplace requests | Zero MySQL `SELECT` queries for currency conversions | `P0` |
| **AC-09** | Rollback | Corrupt snapshot activated, then revoked | System points to previous valid snapshot; outbox event enqueued | `P1` |
| **AC-10** | Resilience | Crash after MySQL commit before Redis/CDN update | Outbox worker resumes automatically on recovery | `P1` |
| **AC-11** | Pagination | 50 returned from ES, 20 discarded by browser reconciliation | Cursor pagination continues seamlessly via `search_after` | `P0` |
| **AC-12** | Contractual FX | Contract specifies 85 INR/USD; daily reference is 83.5 | Commercial invoice uses 85; Indicative UI uses 83.5 | `P0` |
| **AC-13** | Tenancy | Tenant updates operating currency from INR to USD | All pre-existing candidates and invoices remain in INR | `P1` |

---

*End of Final Production Specification v3.2 — Architecture Frozen for Implementation.*
