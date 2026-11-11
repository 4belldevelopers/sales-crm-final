# Currency Synchronization Audit & Resolution: Add Job ↔ Job Tracker Parity

**Metadata:**
- **Date:** 2026-11-11
- **Module:** Currency & Job Requisition Management (`currency_sync_bug`)
- **Impact Scope:** Add Job, Edit Job, Job Tracker, Job Details Drawer, All Jobs, Elasticsearch Projections
- **Status:** Resolved & Verified

---

## 1. Executive Summary

A critical currency corruption bug was identified in the JERO ATS platform:
- **Observed Behavior:** When a user created a job requisition and selected **Euro (EUR)** on the Add Job page, the Job Tracker, Job Listings, and Drawer views displayed **Albanian Lek (LEK)** (`Lek 250K - 450K /Monthly`) instead of Euro (`€ 250K - 450K /Monthly`).
- **Impact:** Systemic cross-currency mapping corruption affecting multiple major world currencies (e.g., EUR displayed as Albanian Lek, GBP displayed as Afghan Afghani, JPY displayed as USD).
- **Resolution:** Root cause forensic isolation in `GeneralMethod::currencySymbole()` and `CurrencyService`, removal of destructive key collisions from legacy master currency sets, strict integer/code normalization across MySQL and Elasticsearch, deduplication of currency picker items in Add/Edit Job views, and synchronized resolution across all tracker cards and details drawers.

---

## 2. Root Cause Analysis

### A. The Colliding Loop in `GeneralMethod::currencySymbole()`
In `app/Helper/GeneralMethod.php`, the static `currencySymbole()` method pre-computes an array of rendered currency symbols/icons and caches it under `currency_symbols_all` for 24 hours:

```php
// Step 1: currencies table loop (EUR getKey = 1)
$array[$curr['getKey']] = $html; // $array[1] = '<i class="fa fa-eur"></i>'

// Step 2: key_to_code loop
$array[(int) $key] = $html;

// Step 3: Legacy master_currencies loop (FATAL COLLISION)
if (! empty($data['master_currencies'])) {
    foreach ($data['master_currencies'] as $curr) {
        if (is_array($curr) && ! empty($curr['code']) && isset($curr['id'])) {
            $codeUpper = strtoupper(trim((string) $curr['code']));
            $html = $array[$codeUpper] ?? ...;
            $array[(int) $curr['id']] = $html; // <-- OVERWROTE $array[1]
        }
    }
}
```

1. In the `currencies` dataset, `getKey: 1` corresponds to **EUR** (`€`).
2. In the `master_currencies` legacy dataset, primary key `id: 1` corresponds to **Albania** (`ALL`), whose currency is **Leke** and symbol is **"Lek"**.
3. Because Step 3 unconditionally assigned `$array[(int) $curr['id']] = $html`, it directly overwrote `$array[1]` with Albanian Lek (`<span class="fw-bold">Lek </span>`)!
4. It also overwrote:
   - `id: 2` (America / USD) overwrote JPY (`getKey: 2`) with `$`
   - `id: 3` (Afghanistan / AFN) overwrote GBP (`getKey: 3`) with `AFN`
   - `id: 5` (Andorra / EUR) overwrote AED (`getKey: 5`) with `EUR`

### B. Bogus `+ 1000` Offset
Lines 4707–4709 added:
```php
if ((int) $key < 1000) {
    $array[(int) $key + 1000] = $html;
}
```
For `key = 1` (EUR), this set `$array[1001] = EUR`. However, `1001` is the actual system key for Albanian Lek (`ALL`). This corrupted keys in the 1000+ range.

### C. `currencySymboleforexcell()` Flattening
Line 4735 previously called `array_values(\App\Services\CurrencyService::getCurrencies())`, stripping the associative `getKey` keys (e.g. 22 for CAD) and re-indexing them sequentially from `0, 1, 2...`. Lookups by currency key in bench candidate listings and excel imports failed or mapped to the wrong currency.

### D. Duplicate Currency Popover Items
`CurrencyService::getCurrencies()` contains 15 mappings for EUR (due to historic Eurozone country entries). In `create.blade.php`, rendering all keys caused Euro to appear 15 times in the popover dropdown.

---

## 3. Architecture & Data Flow

```
[ Add / Edit Job Form ]
       │
       │ User selects 'EUR' (€) -> min_salary_currency = 1
       ▼
[ JobService::createJob / updateJob ]
       │
       │ Normalizes numeric key or ISO code -> integer getKey (1)
       │ Computes converted INR values for search filters
       ▼
[ MySQL (jobs table) ] ── (INSERT / UPDATE) ──> jobs.min_salary_currency = 1
       │
       │ Transaction commit triggers async indexing
       ▼
[ Elasticsearch (jobs index) ] ──> min_salary_currency = 1
       │
       │ Universal CQRS Read Path
       ▼
[ ServiceCompanyJobsController::all_data_ajax ]
       │
       │ Reads ES document ($row->min_salary_currency = 1)
       │ Resolves icon via GeneralMethod::currencySymbole()[1]
       ▼
[ Fixed GeneralMethod::currencySymbole() ]
       │
       │ 1 => '<i class="fa fa-eur" aria-hidden="true"></i>' (EUR)
       │ 1001 => '<span class="fw-bold">Lek </span>' (ALL)
       ▼
[ Client-First Job Card Rendering ]
       │
       │ Displays: € 250K - 450K /Monthly
       │ Job Details Drawer: € 250,000 /Monthly | Currency: EUR
```

---

## 4. File Changes Matrix

| File Path | Component | Changes Made |
|---|---|---|
| `app/Helper/GeneralMethod.php` | `currencySymbole()` | Added non-collision guard `!isset($array[$mId])` preventing `master_currencies` from overriding ATS keys; removed bogus `+ 1000` key pollution; preserved string & integer keys for all currencies. |
| `app/Helper/GeneralMethod.php` | `currencySymboleforexcell()` | Removed destructive `array_values()` call; preserves associative `getKey` mappings. |
| `app/Services/CurrencyService.php` | `resolveCurrencyCode()` | Removed `key - 1000` deduction that erroneously mapped keys >= 1000 to lower keys; handles integer, numeric string, and master ID fallback. |
| `app/Services/JobService.php` | `createJob()` | Normalizes `min_salary_currency` and `max_salary_currency` to integer keys on creation. |
| `app/Services/JobService.php` | `updateJob()` | Preserves existing job currency on partial updates; normalizes provided currency values safely. |
| `resources/views/service_company/jobs/create.blade.php` | Add/Edit View | Added dual-type preselection check (`$key == $val || code == $val`); deduplicated popover items by currency code so EUR appears once. |
| `resources/views/service_company/jobs/show.blade.php` | Job Details Drawer | Updated currency display to resolve authoritative code and symbols via `CurrencyService::resolveCurrencyCode()` with fallback safety. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `all_data_ajax()` | Added fallback to `CurrencyService::resolveCurrencyCode()` for rock-solid symbol resolution. |
| `resources/views/service_company/ajax_pages/all_jobs_ajax.blade.php` | Job Tracker Blade | Added fallback resolution for `min_salary_currency` and `max_salary_currency`. |

---

## 5. Verification Evidence

### CLI Verification Script Output
A dedicated verification script was executed against the application container:
```bash
=== CURRENCY SYMBOLS RESOLUTION TEST ===
Key 0 (INR): <i class="fa fa-inr" aria-hidden="true"></i>
Key 1 (EUR): <i class="fa fa-eur" aria-hidden="true"></i>
Key 2 (JPY): <i class="fa fa-jpy" aria-hidden="true"></i>
Key 3 (GBP): <i class="fa fa-gbp" aria-hidden="true"></i>
Key 4 (USD): <i class="fa fa-usd" aria-hidden="true"></i>
Key 5 (AED): <span class="fw-bold">AED </span>
Key 6 (AUD): <span class="fw-bold">A$ </span>
Key 22 (CAD): <span class="fw-bold">C$ </span>
Key 1001 (ALL): <span class="fw-bold">Lek </span>
Code EUR: <i class="fa fa-eur" aria-hidden="true"></i>
Code USD: <i class="fa fa-usd" aria-hidden="true"></i>
Code INR: <i class="fa fa-inr" aria-hidden="true"></i>
Code ALL: <span class="fw-bold">Lek </span>

=== CURRENCY SYMBOLE FOR EXCELL TEST ===
Key 0: INR
Key 1: EUR
Key 4: USD
Key 22: CAD
Key 1001: ALL
```

- Key 1 (EUR) **strictly resolves to Euro** (`<i class="fa fa-eur"></i>`), never Albanian Lek.
- Key 1001 (ALL) **strictly resolves to Albanian Lek** (`Lek`), never Euro.
- Key 2 (JPY), Key 3 (GBP), Key 4 (USD), Key 5 (AED), Key 22 (CAD) all resolve accurately without corruption.
- Syntax verification: `php -l` passed on all modified PHP files with 0 errors.
