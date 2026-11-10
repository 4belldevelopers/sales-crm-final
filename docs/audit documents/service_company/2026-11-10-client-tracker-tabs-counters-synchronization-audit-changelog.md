# Client Tracker Tabs, Counter Synchronization & Invitation Visibility Audit & Changelog

## Metadata
- **Date:** 2026-11-10
- **Module:** Service Company / Clients Tracker (`/service_company/clients`)
- **Type:** Critical Bug Fix & Synchronization Enhancement
- **Target Audience:** Engineering, Product, QA
- **Compliance:** Rule 1 & 2 (CQRS), Rule 5 (Authoritative Data), Rule 6 (Multi-Tenant Atomic Invalidation), Rule 20 (Card + Sub-Card), Rule 21 (Audit Storage)

---

## Executive Summary
A comprehensive audit and remediation of the client listing tabs, invitation counts, and counter synchronization across the Clients Tracker (`/service_company/clients`) has been completed.

### Key Issues Identified & Resolved:
1. **Misleading Hardcoded "+1 this month" on Total Clients Tab:**
   - *Previous state:* Line 1412 of `resources/views/service_company/client/index.blade.php` contained `+{{ max($totalClientsCount, 1) }} this month`. Even when Total Clients was `0`, it displayed `+1 this month`, causing acute confusion with pending invitations.
   - *Resolution:* Completely eradicated `max(..., 1)`. In its place, dynamically integrated the invitation count into the Total Clients card via an upper badge (`#badge_clientAllInvites`) and subtitle (`#span_clientAllSub`), clearly indicating e.g. `1 pending invitation`.
2. **Strict Separation of Client Requests from Total Clients:**
   - *Requirement:* Total Clients must count strictly registered/approved clients (`clientCustom + clientDirect`), completely separate from pending client requests (`clientRequest`).
   - *Resolution:* Verified and reinforced both backend and frontend query boundaries. `clientAll` strictly represents approved clients (`0` when no approved clients exist), while `clientRequest` represents incoming invitations. Pending requests are NEVER added to the primary Total Clients counter.
3. **Invitation Count Visibility on Both Tabs:**
   - **Total Clients Tab (Card 1):** Primary number displays `0` (registered clients). Beside the number, an enterprise purple pill badge (`[● 1 invite]`) is displayed when pending invitations exist. The subtitle displays `1 pending invitation` in purple (`#7c3aed`).
   - **Pending Requests Tab (Card 6):** Primary number displays `1` (pending requests). Subtitle displays `1 pending invitation` (or `You're all caught up` when `0`).
4. **Full Dynamic Counter & Subtitle Synchronization:**
   - Previous `updateClientTabCounters()` only updated primary numbers, leaving subtitles static.
   - Refactored `updateClientTabCounters()` to synchronize all 6 primary numbers, all 6 subtitles, direct client percentage, and the invitation badge across initial load, tab changes, filter applications, and request mutation lifecycle events (Accept / Decline).
5. **Multi-Tenant Atomic Cache Invalidation:**
   - Identified that `request_update` previously invoked `commonclient()` before clearing tenant version and without evicting the versioned counter cache key `"ats:v2:client_counters:{$company_id}:{$tenantVersion}"`.
   - Introduced `ServiceCompanyClientController::invalidateTenantClientCache($companyId)`, which atomically increments the tenant version and evicts counter and hiring caches across version boundaries, guaranteeing zero stale data.
6. **Card + Sub-Card Header Consistency (Rule 20):**
   - Redundant table header `#clientTableHead` is now dynamically hidden when viewing the `request` tab, preventing table header clutter above modern Card + Sub-Cards.
7. **Accurate Pagination Grammar:**
   - Pagination text now dynamically reflects the domain entity: `Showing 1 to 1 of 1 request` on requests tab, vs `client(s)` on directory tabs.

---

## File Changes Matrix

| File Path | Type | Key Changes |
|---|---|---|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | Controller | • Added `invalidateTenantClientCache(int $companyId): int` helper method.<br>• Updated `request_update()` for both ignore (`-1`) and accept (`1`) events to atomically invalidate tenant cache before recomputing `$clientDataCnt`.<br>• Set `'listType' => 'request'` in `client_request_data()` pagination. |
| `resources/views/service_company/ajax_pages/client-pagination.blade.php` | Blade View | • Dynamically outputs `request`/`requests` or `client`/`clients` based on `$listType`. |
| `resources/views/service_company/client/index.blade.php` | Blade View & JS | • Added `.cl-invitation-badge` and `.cl-invitation-dot` CSS.<br>• Computed dynamic `$currentTab` in Blade.<br>• Enhanced all 6 stat cards with dynamic `active` classes and element IDs.<br>• Replaced hardcoded `+1 this month` on Card 1 with invitation badge and dynamic `#span_clientAllSub`.<br>• Refactored `updateClientTabCounters()` to update numbers, subtitles, and badges dynamically.<br>• Hidden `#clientTableHead` on `request` tab.<br>• Updated optimistic update and error rollback in request action. |

---

## 15.6" Screen Fit & Enterprise UI/UX Compliance
- **Resolution:** 1366×768 & 1920×1080.
- **Design Tokens:** Metronic palette, Poppins font, `#7c3aed` purple for invitation queues, `#009ef7` primary blue.
- **Badge Spec:** `.cl-invitation-badge` with subtle pulsing dot (`#8b5cf6`), soft lilac background (`#f5f3ff`), and border (`#ddd6fe`).
- **Layout:** Strict Rule 20 modern Card + Sub-Card design system with 0 horizontal overflow.

---

## Verification Evidence
1. **PHP Lint:**
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` -> No syntax errors detected.
2. **Blade Render Test:**
   - Executed full view render for Company ID `5` (`marketing 4bell`, user `4bell.marketing@gmail.com`).
   - Verified Card 1 renders Total Clients = `0`, badge = `1 invite`, subtitle = `1 pending invitation`.
   - Verified Card 6 renders Pending Requests = `1`, subtitle = `1 pending invitation`.
   - Verified `+1 this month` is completely absent.
   - Verified `#clientTableHead` is hidden when `tab=request`.
3. **AJAX Tabs Test:**
   - Tested `/service_company/clients_ajax` across all 6 tabs (`all`, `internal`, `direct`, `hiring`, `inactive`, `request`).
   - Confirmed `all`, `internal`, `direct`, `hiring`, `inactive` return `total: 0`.
   - Confirmed `request` returns `total: 1` with 4Bell Technology request.
   - Confirmed `clientDataCnt` returns authoritative counts across all tabs.
4. **PHPUnit Test Suite:**
   - Ran `php ./vendor/bin/phpunit --filter AgencyPartnershipGodAuditTest`.
   - Result: **22 / 22 tests passed (100%), 126 assertions, 0 errors**.
