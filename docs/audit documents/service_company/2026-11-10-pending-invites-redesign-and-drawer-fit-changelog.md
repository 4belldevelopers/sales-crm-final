# Pending Invites Tab Redesign & Invite Agency Drawer Polish Changelog

## Metadata
- **Date:** 2026-11-10
- **Module:** Service Company / Consultancy (`staffing-partner-ajax.blade.php`, `consultancy/index.blade.php`, `ServiceCompanyConsultancyController.php`)
- **Type:** Enterprise UI/UX Redesign, Layout Optimization, Anti-Redundancy & Anti-Harvesting Security
- **Compliance:** Rule 1 (0% MySQL Reads on web request paths), Rule 2 (CQRS Universal Read Scope), Rule 14 (15.6" Screen Fit 1366×768 & 1920×1080), Rule 18 (Code Freeze on `product_company/` & `staffing_company/`), Rule 20 (Card + Sub-Card Design System), Rule 21 (Audit Document Storage Pattern)

---

## Executive Summary
This upgrade resolves the full 3-item UI/UX and privacy punch list across the Service Company Consultancy module:

1. **Option 2: Registration Link Full Visibility & Drawer Width Polish (Image 1):**
   - The Share Registration Link input box previously truncated the generated URL with an ellipsis (`https://testing.jero.works/company-registration...`).
   - Expanded `.add-agency-drawer` width from `560px` to `620px` (`max-width: 95vw`), providing 60px of additional breathing room while strictly preserving 15.6" screen responsiveness.
   - Refined link input typography to `font-size: 10.5px`, `font-weight: 500`, `text-overflow: clip`, and `cursor: pointer` with `onclick="this.select();"`. The complete 67-character registration URL is now 100% visible at a glance without truncation.
   - Verified authoritative drawer header title as **"Invite Agencies"**.

2. **Option 1: Enterprise Company Search Dropdown & Privacy Protection (Image 2):**
   - Eliminated raw email exposure (e.g., `4bell.marketing@gmail.com`) under company names in dropdown suggestions.
   - Suggestions now display **Company Name + Official Website Domain** (with a dedicated globe icon and primary blue styling) or "No official website listed" in italic slate.
   - Completely stripped all email fields (`email`, `company_email`, `root_email`) from the autocomplete JSON API payload to prevent email harvesting and spam campaigns.
   - Search queries match exclusively against `company_name` and `website` across Elasticsearch (`ELASTIC_COMPANIES_INDEX`) and MySQL fallback.

3. **Tab 6: Pending Invites Enterprise Card Stream Redesign (Image 3):**
   - **Dedicated Sub-Card 4: Invitation Actions:** Sub-Card 4 is now exclusively dedicated to invitation management actions (`Resend Invite` and `Cancel` buttons) with clean enterprise padding, clear icons, and remaining TTL window badge.
   - **Elevated Dispatch & Expiration Timeline to Tier 5 Bottom Strip:** Moved Dispatched timestamp (`d M, Y • h:i A`), elapsed time (`48m ago`), exact Expiration timestamp (`d M, Y • h:i A`), and remaining TTL pill (`7 days left`, `2 days left`, or `Expired`) to the Bottom Tier Card.
   - **Escrow & Audit Reference Trace:** Bottom tier combines the timeline with coin auto-refund guarantee (`Auto-refund guarantee: 5 coins refunded if cancelled, declined, or expired`) and unique reference ID (`Ref: #INV-00XXX`).
   - **Eliminated All Redundancy:** Eradicated repetitive *"Pending Registration"* text (from 4 duplicate occurrences down to 1 authoritative status badge in Sub-Card 1) and removed duplicate email lines.

---

## Architecture & Data Flow

```
[ServiceCompanyConsultancyController::index]
       │
       ▼ (Tab = request / pending invites)
[staffing_company_invitation Query]
       ├── SELECT: id, from_company_id, to_company_id, to_email, 
       │           from_created_at, expires_at, to_invitation_status
       │
       ▼
[staffing-partner-ajax.blade.php]
       ├── Carbon Engine:
       │     - Dispatched: $dateSentFormatted, $timeAgo
       │     - Expiry: $expiryDateFormatted, $diffDays, $ttlLabel, $isExpired
       │
       ├── Sub-Card 1: Agency Identity (1 status badge, no duplicate emails)
       ├── Sub-Card 2: Location (Pin + City/State/Country or "Unspecified")
       ├── Sub-Card 3: Network & Escrow (Registered/New + 5 Coins Escrowed)
       ├── Sub-Card 4: Invitation Actions (Resend Invite & Cancel Buttons)
       │
       └── Bottom Activity Strip (Tier 5): Dispatched + Expires Timeline + Auto-Refund + #INV- Ref ID
```

---

## File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php` | Added `staffing_company_invitation.expires_at` to select query; ensured `searchAgencyAutocomplete` redacts email fields and queries strictly `company_name` and `website`. |
| `resources/views/service_company/ajax_pages/staffing-partner-ajax.blade.php` | Complete redesign of Pending Invites card stream per Rule 20. Integrated action buttons into Dispatch Timeline, added expiry dates, removed all redundant labels. |
| `resources/views/service_company/consultancy/index.blade.php` | Expanded `.add-agency-drawer` width to 620px, set link input font-size to 10.5px with clip overflow, added `.cancel-agency-invite` to row-click ignore list, updated resend AJAX handler. |
| `tests/Feature/AgencyPartnershipGodAuditTest.php` | Added test `T-21` (`test_pending_invites_tab_stream_rendering`) verifying all tokens, classes, timestamps, buttons, and escrow labels. |

---

## 15.6" Screen Fit Verification
- **1366×768 (Standard Laptop):** Drawer width 620px leaves >740px of visible backdrop; card stream sub-cards collapse smoothly without horizontal scrolling.
- **1920×1080 (Full HD):** Crisp, balanced 4-column sub-card grid (`2.8fr 1.0fr 0.95fr 2.85fr`) with zero whitespace wastage.
- **Drawer Link Input:** 468px available width comfortably holds the 67-character registration URL at 10.5px font-size (~380px rendered width) with >80px of right padding before the "Copy Link" button.

---

## Verification Evidence
- **PHP Syntax:** `php -l` passed with 0 errors across all 4 files.
- **Laravel Pint:** `./vendor/bin/pint --test` passed with 0 style violations.
- **PHPUnit Suite:** `php ./vendor/bin/phpunit tests/Feature/AgencyPartnershipGodAuditTest.php` executed **21/21 tests with 100% pass (115 assertions)**.
