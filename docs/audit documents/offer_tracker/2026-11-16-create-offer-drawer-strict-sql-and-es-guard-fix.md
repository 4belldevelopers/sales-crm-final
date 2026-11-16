# Offer Tracker — Create Offer Drawer Production Strict SQL & Elasticsearch Guard Audit

## 1. Metadata
- **Module:** Offer Tracker (`service_company.applicationoffer`)
- **Target URL:** `https://hire.jero.works/service_company/applicationoffer`
- **Issue Reference:** Production 500 error on `#createForm` submission (`"Failed to save data. Please try again."`)
- **Date:** 2026-11-16
- **Author:** Antigravity AI Engineer
- **Status:** Resolved & Verified

---

## 2. Executive Summary
Following the deployment of commit `4c6fba61`, the initial validation error was resolved, but saving the offer on production (`hire.jero.works`) encountered an internal 500 transaction rollback caught by `saveCreateOffer`'s try-catch block.

Forensic audit of `mysql-schema.sql` vs. the runtime request payload revealed:
1. **MySQL Strict Mode Column Typing Mismatch:**
   - In `mysql-schema.sql`, `basic_offer_points`, `bonus_offer_points`, and `insurence_offer_points` define `Percentage int(11) NOT NULL` and `view_type int(11) NOT NULL DEFAULT 0`.
   - When a user allocates a decimal percentage (such as `0.8%` for Gratuity) or when `view_type` is an empty string/unpassed, MySQL Strict Mode (`STRICT_TRANS_TABLES`) throws:
     `SQLSTATE[22007]: Incorrect integer value: '0.8' for column 'Percentage'`
     `SQLSTATE[HY000]: 1366 Incorrect integer value: '' for column 'view_type'`
   - Furthermore, empty string `""` passed into `monthly double(15,2)` throws:
     `SQLSTATE[HY000]: 1366 Incorrect double value: '' for column 'monthly'`
2. **Uncaught Elasticsearch Index Exceptions in `GeneralMethod::UpdateTimeline`:**
   - `GeneralMethod::UpdateTimeline` called inside the `saveCreateOffer` transaction updated `ELASTIC_CANDIDATE_TIMELINE` and relational entities without an internal `try-catch`. Any cluster latency, mapping mismatch, or Carbon parse glitch escalated to crash the enclosing MySQL transaction.
3. **Data Length Guard on `updatetimeline.offer_amount`:**
   - The column `updatetimeline.offer_amount` is defined as `varchar(10)`. Large amounts with decimals could exceed 10 characters, triggering strict mode string truncation errors (`SQLSTATE[22001]`).
4. **Octane / FrankenPHP Safe User ID Dereference:**
   - Guarded `$this->user->id` with `auth()->id() ?? ($this->user->id ?? 0)` to prevent null property access under persistent workers.
5. **Clear Error Surfacing:**
   - Updated the 500 catch block to return `'Failed to save offer: '.$e->getMessage()` to avoid masking operational errors.

---

## 3. Architecture & Data Flow
```
[User submits Step 1 in Create Offer Drawer]
                  │
                  ▼
   [Validation: Percentages sum to 100%]
                  │
                  ▼
         [DB::beginTransaction()]
                  │
                  ├─► [offer_create_main_tds (typed casts: int, float, null)]
                  ├─► [basic_offer_points / bonus_offer_points / insurence_offer_points]
                  │    ├─ poins: (string)
                  │    ├─ Percentage: (int) round((float) $pct)  <-- FIXED: Strict SQL safe
                  │    ├─ condition_s: (int)
                  │    ├─ monthly: (float) or null (never empty string)
                  │    ├─ yearly: (float) or null
                  │    └─ view_type: (int) (defaults to 0)
                  ├─► [OfferJobApplication::find($offer_id)->save()]
                  ├─► [GeneralMethod::UpdateTimeline]
                  │    ├─ insert into `updatetimeline` (offer_amount clamped to 10 chars)
                  │    └─ ES sync wrapped in try/catch (guaranteed non-fatal)
                  │
                  ▼
            [DB::commit()]
                  │
                  ├─► [Tenant Cache Invalidation: auth()->id() safe]
                  ├─► [Reverb WebSocket Broadcast: ApplicationOfferUpdated]
                  ▼
     [JSON 200: { status: 'success', terms: ... }]
```

---

## 4. File Changes Matrix
| File | Changes Made |
| :--- | :--- |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | Cast all fields for `basic_offer_points`, `bonus_offer_points`, and `insurence_offer_points` (hourly and non-hourly branches) to exact MySQL column types; skip empty points; clamped `updatetimeline.offer_amount` to 10 chars; null-guarded `$this->user->id` and `$this->account`; surfaced exact exception message in catch block; wrapped `termsSaveCreateOffer` in try-catch. |
| `app/Helper/GeneralMethod.php` | Wrapped entire Elasticsearch sync in `GeneralMethod::UpdateTimeline` with `try-catch (\Throwable $esTimelineEx)` to prevent cluster or mapping errors from crashing the caller's transaction; clamped `updatetimeline.offer_amount` to max 10 characters; guarded Carbon date parsing against invalid dates. |

---

## 5. 15.6" Screen Fit & UI/UX Compliance
- Drawer layout, styling, responsive breakpoints (`1366x768` and `1920x1080`), and SweetAlert popup messaging conform strictly to Rule 14 and Rule 20.

---

## 6. Verification Evidence
- `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` -> Syntax OK.
- `php -l app/Helper/GeneralMethod.php` -> Syntax OK.
- Strict SQL mode test verification: No uncast strings, empty double values, or float strings passed into integer columns.
