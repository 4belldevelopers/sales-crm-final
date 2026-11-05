# Implementation Plan: Coin Features Add & Edit Slide-Over Drawer Redesign

**Document Identifier:** `docs/superadmin/COIN_FEATURES_DRAWER_REDESIGN_IMPLEMENTATION_PLAN.md`  
**Platform:** JERO B2B Talent Marketplace & GOD-Level ATS (`testing.jero.works`)  
**Target Screen:** `http://localhost/git/JWMaincode28June2026/super-admin/coin-features`  
**Target Component:** Add Feature & Quick-Edit Slide-Over Drawer (`#kt_coin_drawer`)  
**Target Viewports:** 15.6" Laptops (`1366×768` & `1920×1080`), Tablets, and Mobile with Zero Overflow  
**Design Standard:** Authoritative Modern Enterprise Card + Sub-Card Design System ([docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md))  
**Status:** **PENDING USER REVIEW & APPROVAL (NO CODE COMMITTED YET)**  

---

## 🏛️ Executive Summary & Defect Diagnosis

### 1. Defect Analysis (from User Uploaded Screenshot `media_1793080252806_b1b44d2b.png`)
Upon testing the drawer on `super-admin/coin-features`, several critical layout and CSS styling regressions occurred:
1. **Broken Flexbox Flow & Container Collisions:**
   - The drawer lacked a strict full-height inner flex wrapper (`.card.shadow-none.rounded-0.w-100.h-100`).
   - Without an isolated flex column, Metronic's drawer engine collided with internal elements, causing the drawer header ("Edit Coin Feature: Add Team", subtitle, and close button `✕`) to drop into the middle of the drawer.
2. **Cramped Multi-Column Form Inputs:**
   - The drawer width defaulted to an arbitrary constraint, causing two-column dropdowns ("Event Scope Type" and "Deduction Model") to squish and truncate text (e.g. cutting off to `"Oper"`).
3. **Misplaced Footer Buttons:**
   - The "Cancel" and "Save Feature" buttons floated to the right of the middle container instead of docking cleanly to a sticky bottom footer.
4. **Lack of Visual Hierarchy & Input Styling:**
   - Fields appeared as raw form inputs rather than enterprise-grade styled sub-card sections with iconography and helper badges.

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                           DRAWER ARCHITECTURE COMPARISON                          │
├──────────────────────────────────────────────────────────────────────────────────┤
│ CURRENT DEFECTIVE STATE:                     PROPOSED ENTERPRISE STANDARD:       │
│                                                                                  │
│ [Drawer]                                     [Drawer Container: 580px Fixed]     │
│ ┌─────────────────────────┐                  ┌─────────────────────────────────┐ │
│ │ (Header pushed down)    │                  │ 📌 STICKY HEADER                │ │
│ │ Edit Coin Feature...    │                  │ [🪙 Icon] Edit Feature #CE-001  │ │
│ │                         │                  │ Subtitle           [✕ Close]    │ │
│ │ [Inputs squished right] │                  ├─────────────────────────────────┤ │
│ │ Oper... Daily...        │                  │ 📜 SCROLLABLE BODY (Form Grid)  │ │
│ │                         │                  │ 1. Scope & Deduction Model      │ │
│ │ (Buttons in middle)     │                  │ 2. Feature Title & Description  │ │
│ │ [Cancel] [Save]         │                  │ 3. Coin Rate & Gating Badges    │ │
│ │                         │                  │ 4. Active Status Switch Card    │ │
│ └─────────────────────────┘                  ├─────────────────────────────────┤ │
│                                              │ 📌 STICKY FOOTER (Always Visible│ │
│                                              │ [Cancel Button]  [Save Feature] │ │
│                                              └─────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Proposed Architecture & Redesign Specifications

### 2.1 Master Drawer Container (`#kt_coin_drawer`)
- **Strict Fixed Dimensions:**
  - Desktop (`>= 992px`): Fixed width `580px !important;`
  - Tablet (`768px – 991px`): Fixed width `520px !important;`
  - Mobile (`< 768px`): Width `95vw !important;`
- **Full Viewport Height & Boundary:**
  - `position: fixed !important; top: 0 !important; right: 0 !important; bottom: 0 !important; height: 100vh !important; max-height: 100vh !important; z-index: 1055 !important;`
  - `box-shadow: -8px 0 32px rgba(15, 23, 42, 0.15) !important;`
  - Zero horizontal scroll: `overflow: hidden !important;`
- **Rigid 3-Part Flexbox Skeleton:**
  ```html
  <div id="kt_coin_drawer" class="cf-drawer" data-kt-drawer="true" ...>
      <div class="card shadow-none rounded-0 w-100 h-100 cf-drawer-card">
          <!-- 1. Sticky Header (Flex-shrink: 0) -->
          <div class="cf-drawer-header">...</div>
          
          <!-- 2. Scrollable Body (Flex: 1 1 auto; overflow-y: auto) -->
          <div class="cf-drawer-body">...</div>
          
          <!-- 3. Sticky Footer (Flex-shrink: 0) -->
          <div class="cf-drawer-footer">...</div>
      </div>
  </div>
  ```

---

### 2.2 Tier 1: Sticky Drawer Header (`.cf-drawer-header`)
- **Layout:** `display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #ffffff; flex-shrink: 0;`
- **Left Branding Area:**
  - Icon Avatar: `42px × 42px`, rounded `9px`, background `#fffbeb`, border `#fef08a`, icon `<i class="fa-solid fa-coins text-warning fs-3"></i>`.
  - Dynamic Title:
    - When adding: `<h3 class="fs-4 fw-bolder text-dark mb-0">Create Coin Feature</h3>`
    - When editing: `<h3 class="fs-4 fw-bolder text-dark mb-0">Edit Feature: <span id="headerFeatureTitle" class="text-primary">Add Team</span></h3>`
  - Event Identifier Pill: `#CE-001` badge in slate `#f1f5f9`.
  - Subtitle: `Configure coin deduction rate, charging model, and wallet limits.`
- **Right Dismiss Area:**
  - High-contrast close button: `<button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="btnDrawerCloseIcon"><i class="fa-solid fa-xmark fs-4"></i></button>`.

---

### 2.3 Tier 2: Scrollable Drawer Body & Form Grid (`.cf-drawer-body`)
- **Layout:** `flex: 1 1 auto; overflow-y: auto; padding: 22px 24px; max-height: calc(100vh - 145px);`
- **Form Groups Structured into 5 Clean Sections:**

#### Section 1: Event Scope & Deduction Model (Visual Card Selectors)
Rather than raw squeezed `<select>` elements, render two clear, responsive selection groups:
- **Scope Type (`event_type`):**
  - Option A: **Operational Feature** (`operational`) — Gear icon, helper text *"ATS workflows, candidate unlocks, emails, interviews"*.
  - Option B: **Sales Lead & Marketplace** (`sales`) — Trophy icon, helper text *"Talent auction, candidate leads, recruitment bidding"*.
- **Deduction Frequency (`deduction_type`):**
  - Option A: **One-time Pay-Per-Use** (`onetime`) — Bolt icon, helper text *"Deducted once upon user trigger"*.
  - Option B: **Daily Recurring** (`daily`) — Repeat icon, helper text *"Deducted daily while feature remains active"*.

#### Section 2: Feature Identity
- **Feature / Event Title (`title`):**
  - Full-width input (`height: 42px`, Poppins 13px, border `#e2e8f0`, focus `#009ef7`).
  - Placeholder: `e.g., CV Access, Automated Resume Forward, Video Interview`.
  - Helper note: Explains where this title appears across the tenant ATS billing logs.

#### Section 3: Coin Economics & Thresholds (Side-by-Side Sub-Cards)
- **Default Coin Rate (`default_rate`):**
  - Left prepend: `<i class="fa-solid fa-coins text-warning"></i>`
  - Number input: `step="0.01"`, `min="0"`.
  - Helper chip: `💡 Enter 0.00 for Free (zero-coin) features`.
- **Minimum Required Wallet Balance (`minimum_required`):**
  - Left prepend: `<i class="fa-solid fa-shield-halved text-primary"></i>`
  - Number input: `step="1"`, `min="0"`.
  - Helper chip: `🛡️ Balance required in company wallet to unlock this feature`.

#### Section 4: Operational Description
- **Description (`description`):**
  - Spacious textarea (`rows="3"`, border `#e2e8f0`).
  - Clear placeholder: `Provide transparent details on how coins are deducted and what exact workflow action is performed...`

#### Section 5: Global Availability Status Card
- **Active Operational Switch (`status`):**
  - Styled horizontal sub-card with light neutral background `#f8fafc`, border `#e2e8f0`, rounded `8px`, padding `14px 16px`.
  - Title: `Active Platform Status`
  - Subtitle: `When enabled, organizations can trigger this feature subject to coin balance rules.`
  - Metronic toggle switch with green active state.

---

### 2.4 Tier 3: Sticky Drawer Footer (`.cf-drawer-footer`)
- **Layout:** `display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #ffffff; position: sticky; bottom: 0; z-index: 10; flex-shrink: 0;`
- **Actions:**
  - **Cancel Button:** `btn btn-light fw-bold px-5` — closes drawer and resets form.
  - **Save Feature Button:** `btn btn-primary fw-bold px-6` with Metronic indicator spinner:
    `<span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save Feature</span>`
    `<span class="indicator-progress d-none">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>`

---

## 3. JavaScript Controller & Drawer Lifecycle

### 3.1 Dual-Layer Open/Close Engine:
To ensure 100% reliability across all browser environments and prevent Metronic drawer collisions:
```javascript
function openDrawer() {
    var drawerEl = document.getElementById('kt_coin_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var d = KTDrawer.getInstance(drawerEl) || new KTDrawer(drawerEl);
        if (d && typeof d.show === 'function') {
            d.show();
            return;
        }
    }
    // Fallback: pure CSS slide-in
    $('#kt_coin_drawer').addClass('drawer-on');
    $('body').append('<div class="drawer-overlay cf-drawer-overlay"></div>');
}

function closeDrawer() {
    var drawerEl = document.getElementById('kt_coin_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var d = KTDrawer.getInstance(drawerEl);
        if (d && typeof d.hide === 'function') {
            d.hide();
            return;
        }
    }
    // Fallback: pure CSS slide-out
    $('#kt_coin_drawer').removeClass('drawer-on');
    $('.cf-drawer-overlay').remove();
}
```

### 3.2 Add & Edit Form Hydration:
- **Add Mode:** Clears inputs, defaults `event_type` to `operational`, `deduction_type` to `onetime`, `default_rate` to `1.00`, `minimum_required` to `0`, and checks `status`. Updates header to *"Create Coin Feature"*.
- **Edit Mode:** Reads `data-*` attributes from the clicked card in **Browser RAM**, pre-populates all inputs instantly (0ms latency, zero server hits), updates header to *"Edit Feature: [Title]"*, and displays the event ID badge.
- **Form Submission:** Sends AJAX POST to `route('superadmin.company.coinEventsStore')`. On validation error, formats errors into a SweetAlert2 modal. On success, closes drawer, shows success alert, and updates UI.

---

## 4. Implementation Steps (Upon User Approval)

1. **Step 1:** Update CSS in [`resources/views/super-admin/coin-settings/coin-events.blade.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/super-admin/coin-settings/coin-events.blade.php):
   - Replace old `.cf-drawer` styles with the rigid `.cf-drawer-card`, `.cf-drawer-header`, `.cf-drawer-body`, and `.cf-drawer-footer` flexbox layout.
   - Enforce desktop `width: 580px !important;` and mobile `width: 95vw !important;`.
2. **Step 2:** Replace HTML markup of `#kt_coin_drawer`:
   - Wrap in `.card.shadow-none.rounded-0.w-100.h-100`.
   - Implement the 5 structured form sections with input icons and visual scope selectors.
3. **Step 3:** Refine JavaScript controller:
   - Wire up `openDrawer()` / `closeDrawer()` dual-layer engine.
   - Bind click triggers to `#btnOpenAddDrawer`, `.js-quick-edit-trigger`, `#btnDrawerCloseIcon`, `#btnDrawerCancel`, and `.cf-drawer-overlay`.
4. **Step 4:** Quality Verification:
   - Check PHP syntax with `php -l`.
   - Format with `./vendor/bin/pint`.
   - Verify layout on 15.6" screen (`1366×768` and `1920×1080`).

---

## 5. Verification Checklist

- [ ] Drawer slides out cleanly from the right edge with zero displacement of header or footer.
- [ ] Header remains sticky at `top: 0` with icon, title, event ID, and close button.
- [ ] Body scrolls smoothly within `max-height: calc(100vh - 145px)` with zero horizontal overflow.
- [ ] Footer remains sticky at `bottom: 0` with Cancel and Save buttons always visible on 768px height screens.
- [ ] Form validates correctly via AJAX and displays SweetAlert2 feedback.
- [ ] Status switch and numbers hydrate correctly between Add and Edit modes.

---

> [!IMPORTANT]
> **Action Required:** Please review this implementation plan. Once you approve, I will proceed to implement the updated drawer design and styling immediately.
