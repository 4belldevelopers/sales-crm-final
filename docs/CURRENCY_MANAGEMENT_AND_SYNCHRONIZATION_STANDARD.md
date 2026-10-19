# Enterprise Currency Management & Synchronization Standard — JERO ATS & SaaS Platform

> **Status:** Production Standard  
> **Applies To:** Service Company, Staffing Company, Product Company, Admin & SuperAdmin Modules  
> **Target Concurrency:** 10,000+ Concurrent Users with 0% Database Read Strain (CQRS + Redis)  
> **Primary Source Service:** `App\Services\CurrencyService`  

---

## 1. Executive Summary & Objective

In multi-tenant SaaS platforms (specifically ATS, Talent Marketplaces, Job Boards, and Hiring Services), currencies must be handled consistently across four critical boundaries:
1. **Tenant Organization Settings:** Configured once under **Company Profile &rarr; Settings** (`service_company/account`).
2. **Form Entry & Commercials:** Add/Edit Candidates, Job Postings, Requisitions, Invoices, Service Rate Cards.
3. **Transactional Persistence (MySQL CQRS Writes):** Safe storage in `INT(11)` columns without data truncation or key ambiguity.
4. **Authoritative Display & Calculation:** DataTables, drawered previews, public candidate profiles, client-side WASM previews, PDF/CSV exports, and Elasticsearch indexing.

Previously, **Company Profile &rarr; Settings** offered all 132 world currencies from `master_currency`, but individual forms (such as Add/Edit In-House Talent) only loaded 6 hardcoded currencies (`INR`, `EUR`, `JPY`, `GBP`, `USD`, `AED`) via legacy `GeneralMethod::currency()`. 

This document defines the **canonical architecture, data contracts, and implementation steps** to replicate the unified currency engine across **all other pages and forms** in JERO.

---

## 2. Core Architecture & Collision Prevention

### The Dual Numbering Problem
In legacy versions of JERO, two independent tables existed with overlapping numeric identifiers:
- `currencies`: Legacy table containing 6 rows with `getKey`: `0` (INR), `1` (EUR), `2` (JPY), `3` (GBP), `4` (USD), `5` (AED).
- `master_currency`: Reference table of 132 countries/currencies where `id = 1` (Albania - ALL), `id = 2` (America - USD), `id = 3` (Afghanistan - AFN), `id = 4` (Argentina - ARS), `id = 5` (Aruba - AWG), ..., `id = 22` (Canada - CAD), `id = 53` (India - INR).

If a form simply used `master_currency.id`, a candidate saved with `id = 4` (Argentina ARS) collided with key `4` (USD), and `id = 2` (America USD) collided with key `2` (Japan JPY). Furthermore, `AED` was present in `currencies` (key `5`), but missing from `master_currency`.

### The Unified Resolution Strategy
`App\Services\CurrencyService` establishes a **100% collision-free, backwards-compatible mapping**:

```
+--------------------------------------------------------------------------+
|                        Unified Integer Key Mapping                       |
+--------------------------------------------------------------------------+
|  0 .. 5       -> Legacy Core Keys (0:INR, 1:EUR, 2:JPY, 3:GBP, 4:USD, 5:AED) |
|  6 .. 132     -> Direct master_currency.id (e.g., 6:AUD, 22:CAD, 104:SGD)  |
|  1001 .. 1005 -> Offset master_currency.id for IDs 1..5 (ALL, AFN, ARS, AWG)|
+--------------------------------------------------------------------------+
```

1. **Legacy Compatibility (0..5):** All historical candidate and job records with keys 0..5 resolve identically to `INR`, `EUR`, `JPY`, `GBP`, `USD`, `AED`.
2. **Master Currencies (`id > 5`):** Currencies like `CAD` (22), `AUD` (6), `SGD` (104), `CHF` (71/113), `CNY` (25), `ZAR` (108) retain their native `master_currency.id`.
3. **Collision Offset (`id <= 5`):** For the few currencies whose `master_currency.id` fell in 1..5 (excluding USD which uses key 4), an offset of `1000 + id` is applied (`1001: ALL`, `1003: AFN`, `1004: ARS`, `1005: AWG`).
4. **AED Preserved:** United Arab Emirates Dirham (`AED`) is explicitly included with key `5`.
5. **Total Unique Currencies:** Exactly **113 unique ISO-3 currencies** covering all global business operations.

---

## 3. Standard Service API (`CurrencyService`)

All modules must interact with currencies through `App\Services\CurrencyService`:

### 3.1. `CurrencyService::getSettingCurrenciesList(): array`
Returns the Redis-cached dictionary of all 113 currencies, priority-sorted with top global currencies at the top (`INR, USD, EUR, GBP, AED, CAD, AUD, SGD, JPY, CHF...`) followed alphabetically.

**Data Contract for Each Item:**
```php
[
    'key'       => 22,                                      // Integer for MySQL storage
    'code'      => 'CAD',                                   // ISO-3 uppercase code
    'name'      => 'Canadian Dollar',                       // Full name
    'symbol'    => 'C$',                                    // Display symbol
    'flag'      => '🇨🇦',                                   // Country flag emoji
    'label'     => 'CAD — Canadian Dollar (C$)',            // Enterprise Select2 label
    'keywords'  => 'cad canadian dollar c$ canada canada',  // Fuzzy search terms
    'countries' => ['Canada'],                             // Associated countries
]
```

### 3.2. `CurrencyService::getTenantCurrency(?int $companyId = null): string`
Returns the tenant's configured ISO-3 currency code (e.g. `'CAD'`, `'USD'`, `'INR'`). It reads from `companies.currency` (cached 24h in Redis), correctly decoding whether the tenant saved a `master_currency.id` (like `22`), a legacy key (like `4`), or an ISO code string.

### 3.3. `CurrencyService::getTenantCurrencyKey(?int $companyId = null): int`
Returns the tenant's default platform currency integer key for database insertion.

### 3.4. `CurrencyService::resolveCurrencyCode(string|int|null $currency): string`
Universal resolver that converts **any** representation (`0`, `4`, `22`, `1001`, `'CAD'`, `'USD'`, `'inr'`) into a canonical uppercase ISO-3 string (`'INR'`, `'USD'`, `'CAD'`).

### 3.5. `CurrencyService::resolveCurrencyKey(string|int|null $currency): int`
Universal resolver that converts any code or key into a safe integer key for MySQL `INT(11)` columns.

### 3.6. `CurrencyService::getCurrencySymbol(?string $code): string`
Returns the authoritative symbol (e.g. `C$` for CAD, `A$` for AUD, `S$` for SGD, `₹` for INR, `$` for USD, `€` for EUR, `£` for GBP, `د.إ` for AED).

### 3.7. `CurrencyService::convert($amount, $fromCurrency, $toCurrency = 'USD'): float`
Universal cross-currency converter using high-precision USD pivot rates from the active currency snapshot.

---

## 4. Step-by-Step Implementation Guide for Any Page

To upgrade any existing form or new feature to support all Setting currencies, follow these 4 steps:

### Step 1: Controller Setup (`create()` & `edit()`)

In your controller (e.g. `JobController`, `RequisitionController`, `InvoiceController`):

```php
use App\Services\CurrencyService;
use App\Helper\GeneralMethod;

public function create()
{
    $companyId = auth()->user()->company_id;

    // 1. Unified Setting Currencies List (113 currencies, cached 24h in Redis)
    $this->currencyList = CurrencyService::getSettingCurrenciesList();

    // 2. Backward-compatible [key => code] array for legacy view bindings
    $this->currency = array_column($this->currencyList, 'code', 'key');

    // 3. Organization's configured default currency from Settings
    $this->tenantDefaultCurrency = CurrencyService::getTenantCurrency($companyId);

    // 4. Frequency/salary modes and other reference lookups
    $this->salaryMode = GeneralMethod::salaryMode();

    return response()->view('your_module.create', $this->data);
}

public function edit($id)
{
    $companyId = auth()->user()->company_id;

    $this->currencyList = CurrencyService::getSettingCurrenciesList();
    $this->currency = array_column($this->currencyList, 'code', 'key');
    $this->tenantDefaultCurrency = CurrencyService::getTenantCurrency($companyId);
    $this->salaryMode = GeneralMethod::salaryMode();

    // ... Load entity ...
    return response()->view('your_module.create', $this->data);
}
```

---

### Step 2: Blade Template Setup (`create.blade.php`)

#### A. Initialize Top-Level Currency Variable
At the top of your Blade file:
```blade
@php
    $currency_code = $tenantDefaultCurrency ?? GeneralMethod::defaultCurrency();
@endphp
```

#### B. Main Currency Select2 Dropdown
```blade
<div class="form-group">
    <label for="currency_select">Currency</label>
    <select id="currency_select" name="currency_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Search currency (e.g. USD, CAD, INR, Euro, $)...">
        @php
            // 1. Get saved entity currency (if editing)
            $savedCurrency = @$entity->currency_id;
            $savedCurCode = ($savedCurrency !== null && $savedCurrency !== '')
                ? \App\Services\CurrencyService::resolveCurrencyCode($savedCurrency)
                : null;

            // 2. Active currency: Saved entity currency takes precedence; fallback to Tenant Setting default
            $activeCurCode = $savedCurCode ?: ($currency_code ?: 'USD');
            $currenciesToRender = !empty($currencyList) ? $currencyList : (isset($currency) ? $currency : []);
        @endphp

        @foreach($currenciesToRender as $cItem)
            @php
                if (is_array($cItem)) {
                    $optKey = $cItem['key'];
                    $optCode = $cItem['code'];
                    $optLabel = $cItem['label'];
                    $optKeywords = $cItem['keywords'];
                } else {
                    $optKey = $loop->index;
                    $optCode = $cItem;
                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($cItem);
                    $optLabel = $cMeta['label'] ?? $cItem;
                    $optKeywords = $cMeta['keywords'] ?? strtolower($cItem);
                }

                $sel = '';
                if ($savedCurrency === null || $savedCurrency === '') {
                    // Create Mode: Pre-select tenant default currency from Settings
                    if (strtoupper($activeCurCode) === strtoupper($optCode)) {
                        $sel = 'selected="selected"';
                    }
                } else {
                    // Edit Mode: Retain candidate/entity's saved currency
                    if ((string)$savedCurrency === (string)$optKey || strtoupper($savedCurCode) === strtoupper($optCode)) {
                        $sel = 'selected="selected"';
                    }
                }
            @endphp
            <option value="{{ $optKey }}" data-code="{{ $optCode }}" data-search="{{ $optKeywords }}" {!! $sel !!}>{{ $optLabel }}</option>
        @endforeach
    </select>
</div>
```

#### C. Synchronized Hidden Fields (if form uses separate rate selects)
If your form submits separate `pay_rate_currency` and `bill_rate_currency`, render synchronized secondary selects using the same `$currenciesToRender`:
```blade
<div class="d-none">
    <select name="bill_rate_currency" id="bill_rate_currency">
        @foreach($currenciesToRender as $cItem)
            @php
                $optKey = is_array($cItem) ? $cItem['key'] : $loop->index;
                $optCode = is_array($cItem) ? $cItem['code'] : $cItem;
                $sel = (strtoupper($activeCurCode) === strtoupper($optCode)) ? 'selected="selected"' : '';
            @endphp
            <option value="{{ $optKey }}" data-code="{{ $optCode }}" {!! $sel !!}>{{ $optCode }}</option>
        @endforeach
    </select>
</div>
```

#### D. Select2 Fuzzy Search Initialization (JavaScript)
Enable custom Select2 matching so users can search by **Country**, **Currency Name**, or **Symbol**:
```javascript
function initCurrencySelect2() {
    if (!$.fn.select2) return;
    let $select = $('#currency_select');
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    $select.select2({
        width: '100%',
        dropdownParent: $(document.body),
        placeholder: 'Search currency (e.g. USD, CAD, INR, Euro, $)...',
        minimumResultsForSearch: 0,
        matcher: function (params, data) {
            if ($.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;

            let term = params.term.toLowerCase();
            let text = data.text.toLowerCase();
            let $el = $(data.element);
            let code = ($el.data('code') || '').toString().toLowerCase();
            let searchKw = ($el.data('search') || '').toString().toLowerCase();

            // Match against Label, ISO Code, or Keywords (country name, symbols, synonyms)
            if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    });
}
```

---

### Step 3: Controller Store & Update Logic (Persistence & CQRS)

Never rely on hardcoded `$currencyList[$request->currency]` arrays in store methods. Always resolve canonically:

```php
public function store(Request $request)
{
    // 1. Resolve canonical currency code from user submission
    $currencyCode = CurrencyService::resolveCurrencyCode($request->currency_id);
    
    // 2. Persist safe integer key in MySQL INT(11) column
    $entity->currency_id = (int) $request->currency_id;
    $entity->amount = (float) $request->amount;

    // 3. Normalized CQRS Storage (e.g., INR or USD baseline for search & indexing)
    $normalizedAmount = (float) $request->amount;
    if ($currencyCode !== 'INR' && $normalizedAmount > 0) {
        $normalizedAmount = GeneralMethod::convertToINR($normalizedAmount, $currencyCode);
    }
    
    // 4. Divide by frequency mode (0: Annual, 1: Monthly, 2: Hourly)
    $divided = GeneralMethod::salaryDividedByMode(0, $normalizedAmount);
    $entity->amount_yearly  = $divided->yearly;
    $entity->amount_monthly = $divided->monthly;
    $entity->amount_hourly  = $divided->hourly;

    $entity->save();
}
```

---

### Step 4: Display, DataTables, Drawers, and Exports

When rendering candidates, jobs, or rates in DataTables, cards, or PDF/CSV exports:

```php
// In DataTable editColumn or Blade row rendering:
$currKey = $row->currency_id;
$currCode = CurrencyService::resolveCurrencyCode($currKey);
$currencySymbol = CurrencyService::getCurrencySymbol($currCode);

// Formatted display: e.g. "C$ 120,000 / Y" or "$ 50 / H"
$formattedSalary = $currencySymbol . ' ' . GeneralMethod::thousandsCurrencyFormat($row->amount) . ' / ' . $modeText;
```

---

## 5. Verification Checklist

Before deploying or approving any currency upgrade across JERO, execute this 10-point checklist:

- [ ] **1. Dropdown Population:** Select2 dropdown contains **113 currencies** (including CAD, AUD, SGD, AED, EUR, GBP, USD, INR).
- [ ] **2. Default Pre-Selection:** In `ADD` mode, the currency dropdown automatically pre-selects the organization's currency configured in **Company Profile &rarr; Settings**.
- [ ] **3. Edit Mode Preservation:** When editing an existing record, the entity's saved currency is accurately pre-selected, not overwritten by company default.
- [ ] **4. Country Name Search:** Typing `"Canada"` finds `CAD`, `"Germany"` finds `EUR`, `"Singapore"` finds `SGD`, `"Dubai"` finds `AED`, `"Australia"` finds `AUD`.
- [ ] **5. Integer Column Safety:** Submitted `currency_id` is an integer that safely persists into MySQL `INT(11)` columns without truncation.
- [ ] **6. Backwards Compatibility:** Existing candidate and job records with keys `0..5` continue to resolve correctly as `INR`, `EUR`, `JPY`, `GBP`, `USD`, `AED`.
- [ ] **7. Accurate FX Conversion:** Foreign currencies convert properly to baseline INR/USD for `ctc_yearly`, `ctc_monthly`, `ctc_hourly`.
- [ ] **8. UI Addon Badges:** Input addon labels (e.g., `.changeTextCurrency`) update to the selected currency code on change.
- [ ] **9. Code Freeze Compliance:** No files in `resources/views/product_company/` or `resources/views/staffing_company/` are modified (strictly scoped per Rule 18).
- [ ] **10. Zero MySQL Read Strain:** Master currency lists are cached in Redis for 24h via `CurrencyService::getSettingCurrenciesList()`.

---

## 6. Reference Links
- [`App\Services\CurrencyService`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/CurrencyService.php)
- [`App\Services\ReferenceDataCache`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/ReferenceDataCache.php)
- [`App\Helper\GeneralMethod`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php)
- Reference Implementation: [`ServiceCompanyBenchCandidatesController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php) & [`create.blade.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/bench-candidates/create.blade.php)
