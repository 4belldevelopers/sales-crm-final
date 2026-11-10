# Changelog: Clients Tracker Pending Requests Modern Card + Sub-Card Redesign

## Metadata
- **Date:** 2026-11-10
- **Module:** Service Company / Clients Tracker (`/service_company/clients?tab=request`)
- **Sub-Module:** Tab 6 "Pending Requests" (Inbound Client Partnership Invitations Stream)
- **Standard Compliance:** Rule 1 (0% MySQL Reads on web request paths), Rule 2 (CQRS Universal Read Scope via Elasticsearch), Rule 5 (Zero Stale Data in ATS), Rule 6 (Multi-Tenant Atomic Cache Invalidation), Rule 7 (Zero Business Logic Mutations & Zero Module Regressions), Rule 14 (15.6" Screen Fit 1366×768 & 1920×1080), Rule 18 (Persona Code Freeze on `product_company/` & `staffing_company/`), Rule 20 (Modern Card + Sub-Card Enterprise Standard), Rule 21 (Mandatory Storage Pattern)
- **Author:** Advanced Agentic AI Assistant

---

## Executive Summary
Prior to this enhancement, navigating to the **Pending Requests** tab on the Clients Tracker page (`/service_company/clients?tab=request`) presented an outdated, flat HTML table row with a raw checkbox, basic text columns, and vertically stacked unstyled action buttons. 

In strict compliance with **Rule 20 (Modern Card + Sub-Card Enterprise Standard)** and **Rule 14 (15.6" screen responsiveness)**, the Pending Requests tab has been completely refactored to use the authoritative 3-tier enterprise card architecture:
1. **Tier 1:** Pure white toolbar card (`#ffffff`, border `#e2e8f0`, radius `10px`).
2. **Tier 2:** Dedicated KPI sub-tabs card with active purple highlight (`#a855f7`) for Tab 6 ("Pending Requests").
3. **Tier 3:** Primary Card stream with 4 discrete upper sub-cards, an authoritative amber status accent (`border-left: 3.5px solid #f59e0b`), and a Tier 5 bottom audit strip with tenant timezone awareness.

---

## Architectural & Data Flow

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1 & 2: Toolbar Header & KPI Sub-Tabs (Clients Tracker)                          │
│  [Total Clients: 0] [Internal: 0] [Direct: 0] [Active: 0] [Inactive: 0] [Pending: 1]  │
└────────────────────────────────────────┬───────────────────────────────────────────────┘
                                         ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 3: PRIMARY CARD (.cl-primary-card.is-pending-request)                             │
│  Border-left: 3.5px solid #f59e0b (Amber Action Needed Accent)                         │
│  ┌──────────────────┬──────────────────┬──────────────────┬──────────────────────────┐ │
│  │ SUB-CARD 1:      │ SUB-CARD 2:      │ SUB-CARD 3:      │ SUB-CARD 4:              │ │
│  │ Client Identity  │ Location         │ Engagement       │ Actions & SLA            │ │
│  │ • Logo / Avatar  │ • Pin Icon       │ • Direct Invite  │ • Countdown: 6d left     │ │
│  │ • Client Name    │ • City, Country  │ • Inbound Req    │ • [Accept] [Decline]     │ │
│  │ • Industry       │                  │ • Talent Partner │   [Terms & Profile]      │ │
│  │ • Website Link   │                  │                  │ (Equal 33.3% buttons)    │ │
│  └──────────────────┴──────────────────┴──────────────────┴──────────────────────────┘ │
│  ┌───────────────────────────────────────────────────────────────────────────────────┐ │
│  │ TIER 5 (BOTTOM STRIP): Request Received (Date & Time + TZ) • Expires • Ref ID     │ │
│  └───────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Sub-Card Anatomical Breakdown

### 1. Sub-Card 1: Client Identity (`.cl-subcard-identity`)
- **Header:** Title **"Client"** with Dual Badges:
  - Scope Pill: `[Direct Client]` (`#ecfdf5` background, `#059669` text, `#a7f3d0` border).
  - Authoritative Status Pill: `[● Action Needed]` (`#fffbeb` background, `#d97706` text, animated pulsating amber dot) when pending; transitions to `[Connected]` (green) or `[Declined]` (rose) based on server-confirmed status.
- **Body:**
  - Dynamic company logo or initials avatar with deterministic background color hash.
  - Bold company title linked to the Client Terms drawer (`.view-request-terms`).
  - Industry specialization badge.
  - Official website link with SVG globe icon.

### 2. Sub-Card 2: Location (`.cl-subcard-loc`)
- **Header:** Title **"Location"**.
- **Body:**
  - Geographic pin SVG icon in primary blue (`#2563eb`).
  - Clean typographic hierarchy displaying City, State, and Country (e.g. `Pune, India`), or `Unspecified • Set on connection` fallback.

### 3. Sub-Card 3: Engagement Scope (`.cl-subcard-jobs`)
- **Header:** Title **"Engagement"**.
- **Body:**
  - Scope Badge: `[Direct Invitation]` (`#eff6ff` background, `#2563eb` text).
  - Relationship Context: `"Talent & Hiring Partner"`.

### 4. Sub-Card 4: Actions & SLA Center (`.cl-subcard-pipeline`)
- **Header:**
  - Title **"Actions"**.
  - Dynamic countdown badge: `[6 days left]` / `[Expires in Xd]` with semantic color coding (green for >2d, amber for ≤2d, red for <24h or expired).
- **Body:**
  - Three equal-width, high-contrast action buttons:
    1. **Accept:** Emerald green background (`#10b981`), white text, checkmark SVG icon (`.request_event[data-row-event="1"]`).
    2. **Decline:** Light rose background (`#fef2f2`), red border (`#fecaca`), red text (`#ef4444`), cancel X SVG icon (`.request_event[data-row-event="-1"]`).
    3. **Terms:** Light blue background (`#eff6ff`), blue border (`#bfdbfe`), blue text (`#2563eb`), document SVG icon (`.view-request-terms`).
  - Optimistic 0ms UI Transition: Clicking Accept/Decline instantly shows an inline micro-spinner while executing the mutation in the background.

### 5. Tier 5: Bottom Activity & Audit Strip (`.cl-card-bottom-strip`)
- **Left Meta:**
  - Clock icon + `"Request Received: [DD Mon, YYYY • HH:MM AM/PM TZ]"` + relative time ago (`15 hours ago`).
  - Expiry indicator + `"Expires: [DD Mon, YYYY • HH:MM AM/PM TZ] [Xd remaining]"`.
- **Right Meta:**
  - Shield check icon + `"Verified Client Outreach"`.
  - Canonical Reference ID: `"Ref: #REQ-[00115]"`.

---

## File Changes Matrix

| File Path | Action | Description |
| :--- | :--- | :--- |
| `resources/views/service_company/ajax_pages/client_request_ajax.blade.php` | Refactored | Completely replaced flat HTML table rows with the Rule 20 Modern Card + Sub-Card layout, 4 discrete sub-cards, and bottom audit strip. |
| `resources/views/service_company/ajax_pages/all-client-ajax.blade.php` | Updated | Aligned the `$isPending` block to match the updated 4-subcard layout and audit bottom strip. |
| `resources/views/service_company/client/index.blade.php` | Updated | Updated client-side JavaScript `renderClientRows()` `r.is_pending_request` template for unified client-side search/WASM rendering. |
| `app/Helper/GeneralMethod.php` | Enhanced | Updated `clientRequestIndexElastic()` to index `expires_at` and `website` into Elasticsearch index `ats_clients_request`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | Enhanced | Added `website`, `expires_at`, `from_created_at` to `_source` list and `rowsDto` array in `client_request_data()`. |

---

## 15.6" Screen Fit & Responsiveness (Rule 14)
- **1366×768 & 1920×1080 Laptops:** The grid template `grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.1fr) minmax(0, 1.1fr) minmax(0, 2.3fr)` ensures all 4 discrete sub-cards expand and contract proportionally without wrapping or causing horizontal overflow (`overflow-x: hidden`).
- **Tablet / Medium Screens (≤992px):** Gracefully collapses into a 2×2 grid with auto-adjusting heights.
- **Mobile Screens (≤640px):** Stacks cleanly into a single vertical stream.

---

## Verification Evidence
1. **Blade & Controller Execution Test (`test_controller_client_request.php`):**
   - Simulated authenticated session for Agency 5 (`4bell.marketing@gmail.com`).
   - Output confirmed: `Total: 1`, `HTML Length: 13,816 bytes`, `cl-primary-card: YES`, `Accept: YES`, `Decline: YES`, `Terms: YES`.
2. **PHPUnit Test Suite:**
   - Command: `php ./vendor/bin/phpunit --filter AgencyPartnershipGodAuditTest`
   - Result: **22/22 (100%) tests passing**, 126 assertions, 0 errors.
