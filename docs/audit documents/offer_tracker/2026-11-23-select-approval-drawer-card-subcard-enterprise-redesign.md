# Select Approval Drawer — Modern Card + Sub-Card Enterprise Redesign (Rule 20 & Rule 26)

## Metadata
- **Date:** 2026-11-23
- **Author:** Antigravity Agent (God-Level UI/UX Architect)
- **Module:** Offer Tracker & Candidate Offer Workflow (`service_company/applicationoffer`, `service_company/cvtracker`)
- **Category:** UI/UX Redesign & Enterprise Design System Enforcement
- **Standards Applied:** Rule 20 (Modern Card + Sub-Card Enterprise Design Standard & Header Background Standard), Rule 26 (/unslop Craft Standard), Rule 27 (Transitions & Zero-Jank Motion Standard)

---

## Executive Summary
The "Select Approval" drawer (which opens when a recruiter or hiring manager clicks **+ Add Approver** on Step 4 of the Offer Generation workflow) previously exhibited inconsistent formatting, unstyled floating sub-tabs, raw radio buttons, unanchored headers with standard red cross buttons, and basic tabular styling lacking visual hierarchy.

In accordance with user feedback and **Rule 20**, the entire drawer interface has been refactored strictly into the **3-Tier Modern Card + Sub-Card Enterprise Design System**:
1. **Tier 1 Outer Drawer & Topbar:** Anchored header with dedicated `#f8fafc` background, crisp `#e2e8f0` border, primary blue user-check icon (`#eff6ff` bg / `#2563eb` text), bold 15px Poppins title, descriptive subtitle, and enterprise close icon button with soft red hover transition.
2. **Tier 2 Source Sub-Cards (Sub-Tabs):** Replaced raw radios with modern interactive Sub-Cards ("Internal Company Approvers" and "Client Company Approvers"). Features dedicated SVG icon containers (Building / Handshake), bold 12.5px title, count badge pills (`X available`), custom circular indicator with primary blue dot, and Metronic blue `#009ef7` active borders.
3. **Tier 1 Search Toolbar Card:** Dedicated `#f8fafc` toolbar card with crisp hairline border, inner search box with search SVG icon, clear button (`×`), and live result counter badge (`Showing X approvers`).
4. **Tier 3 Primary Table Card:** Encapsulated in a modern card with **Mandatory Header Background (`#f8fafc`, border `#e2e8f0`)** and uppercase 10.5px bold tracking headers (`APPROVER`, `CONTACT`, `COMPANY`, `ACTION`).
   - Approvers display 34px circular pastel avatars with deterministic palette per letter.
   - Clean 12.5px semibold name with dedicated role tag pill.
   - Contact and Company columns with SVG icons.
   - Enterprise Action button in dark slate `#1e293b` with `+` icon, transitions to spinner `Sending…` on click, and emerald `Sent ✓` upon confirmation.

---

## Architecture & Data Flow

```
[ Step 4: Approval Workflow ]
           │
           │  Click "+ Add Approver" (.fwd_offer)
           ▼
[ ApplicationOffer Index ]
  - Activates #kt_fwd_offer_drawer & #kt_fwd_offer_overlay
  - Topbar rendered: #f8fafc background, #e2e8f0 border, user-check icon
  - Loads AJAX view from ServiceCompanyCvTrackerController::offer_send_mail
           │
           ▼
[ OfferSendMail Blade (mtype=2) ]
  - Automatically invokes coApprOpenDrawer('to')
  - Pre-populates approval email template via AJAX
           │
           ├── Tier 2: Source Sub-Cards (Internal vs Client Approvers)
           │     └─ Instant client-side DOM toggle without server requests
           ├── Tier 1: Search Toolbar Card
           │     └─ Instant client-side search across Name, Email & Company
           └── Tier 3: Approver Table Card
                 ├─ thead tr: Mandatory #f8fafc header background
                 ├─ Deterministic pastel circular avatars
                 ├─ Role pills, email & company with SVG icons
                 └─ .to_select action button
                       ├─ In-flight: Spinner + "Sending…"
                       └─ Success: "Sent ✓" (closes drawers, updates Step 4 timeline)
```

---

## File Changes Matrix

| File Path | Changes Description |
|-----------|---------------------|
| `resources/views/service_company/applicationoffer/index.blade.php` | Redesigned `#kt_fwd_offer_drawer` to 720px width with smooth cubic-bezier transitions and `-8px 0 32px rgba(15,23,42,0.16)` shadow. Replaced plain card-header with enterprise `.co-drawer-topbar` featuring `#f8fafc` anchor background, `#e2e8f0` border, 36px icon box, 15px title, descriptive subtitle, and modern close button. |
| `resources/views/service_company/cvtracker/offerSendMail.blade.php` | Replaced legacy CSS and markup with Rule 20 Card + Sub-Card Design System. Redesigned both `#kt_activitiesIntTeam_to` and `#kt_activitiesIntTeam_cc` drawers with Tier 2 source sub-cards, Tier 1 search toolbar card with clear button, and Tier 3 table card with `#f8fafc` header background, 34px circular pastel avatars, SVG icons, and `#1e293b` action buttons. Enhanced client-side filter JS with live count. |

---

## 15.6" Screen Fit Verification

- **Screen Resolutions:** Tested for `1366×768` (standard HD laptop) and `1920×1080` (Full HD).
- **Drawer Width:** `720px` (max-width `95vw`), providing ample breathing room for 4 data columns while keeping the underlying offer page visible on the left.
- **Scroll Containment:** The drawer body is constrained with `overflow-y: auto; overflow-x: hidden;`, completely eliminating horizontal scrollbars and ensuring smooth internal vertical scrolling.
- **Typography & Touch Targets:** All font sizes strictly follow enterprise guidelines (10.5px for uppercase column headers, 11px for subtitles/counters, 12.5px for names, 28px height for action buttons).

---

## Verification Evidence
- Syntax verification executed with zero errors:
  ```powershell
  php -l resources\views\service_company\applicationoffer\index.blade.php
  # No syntax errors detected
  php -l resources\views\service_company\cvtracker\offerSendMail.blade.php
  # No syntax errors detected
  ```
- Git diff inspected: Clean replacement of legacy table styling with modern Card + Sub-Card system; all backend action bindings (`to_select`, `cp_select`, `AddEvent`, `DeleteEvent`, `fwd_offer_send`) 100% preserved.
