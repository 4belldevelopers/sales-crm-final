# Offer Drawer Step 5 (Send Offer Letter) – Modern Card + Sub-Card Enterprise Redesign

## 1. Metadata
- **Module:** Offer Tracker / Offer Drawer (`create-offer.blade.php`)
- **Step / Target:** Step 5 (`Send Offer Letter` / Candidate Email Composer)
- **Date:** 2026-11-23
- **Author:** JERO Architecture Team & AI Coding Agent
- **Standard Reference:** 
  - [Rule 14: 15.6" Screen Fit & Enterprise Typography Standard](../../docs/UI_UX_STANDARDS.md)
  - [Rule 20: Modern Card + Sub-Card Enterprise Design System](../../docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md)
  - [Rule 21: Audit Documents Standard Folder Pattern](../../docs/AI_RULES.md)
  - [Rule 26: Zero AI Slop UI/UX Review Standard](../../docs/AI_RULES.md)

---

## 2. Executive Summary
Step 5 (`Send Offer Letter`) of the Create Offer Drawer has been completely refactored to align strictly with the **Modern Card + Sub-Card Enterprise Design System (Rule 20)**:
1. **Candidate Hero Card Removal:** Completely eliminated the redundant `.co-send-hero` card (avatar, candidate name, offer ID, CTC, joining date, etc.) that cluttered vertical space and repeated facts already established in earlier steps.
2. **Sub-Header Metadata Consolidation:** Moved the dynamic **Offer Status** (`Offer Status: Not Sent` / `Offer Released`) and **Offer Valid Upto** (`Valid Upto: DD MMM YYYY`) pills directly into the main card's sub-header (`.card-header.co-card-header`), featuring dedicated anchored `#f8fafc` background styling with high-contrast semantic badges and SVG iconography.
3. **Reply-To Direct Routing:** Resolved the hardcoded system address (`notify@jeroworks.com`) on the `Reply To` field to dynamically default to the authenticated logged-in user's email (`auth()->user()->email`), allowing candidate replies to route straight to the offer issuer.
4. **Card + Sub-Card Structure:** Re-architected the composer form into two distinct, structured sub-cards:
   - **Sub-Card 1: `EMAIL RECIPIENTS & CONFIGURATION`** (From Address, Reply-To, To Email ID with Marketplace Confidentiality guard, CC Email ID with team selector, E-Signature switch, and Template dropdown).
   - **Sub-Card 2: `OFFER LETTER COMPOSER & ATTACHMENTS`** (Subject line, CKEditor email body, attachments row with counter and upload trigger).
   - **Bottom Information Strip:** Anchored `#f8fafc` strip confirming that the offer letter email dispatches automatically on clicking **Save & Next** in the topbar.

---

## 3. Architecture & Data Flow

```
+-----------------------------------------------------------------------------------------+
| STEP 5: SEND OFFER LETTER (Main Card - .co-send-main-card)                              |
| Sub-Header (#f8fafc): Title + Final Step | [Offer Status: Not Sent] [Valid Upto: Date] |
+-----------------------------------------------------------------------------------------+
| SUB-CARD 1: EMAIL RECIPIENTS & CONFIGURATION (#f8fafc header, #e2e8f0 border)           |
| - Row 1: From Email ID (Readonly) | Reply To (auth()->user()->email - User mailbox)     |
| - Row 2: To Email ID (Locked chip: candidate email / Agency RM + Confidentiality guard) |
| - Row 3: CC Email ID (Chips input + "+ Add Email" team/client approver modal trigger)   |
| - Row 4: E-Signature Requirement (Toggle) | Email Template (Dropdown select)            |
+-----------------------------------------------------------------------------------------+
| SUB-CARD 2: OFFER LETTER COMPOSER & ATTACHMENTS (#f8fafc header, #e2e8f0 border)        |
| - Row 1: Subject Line (Input)                                                           |
| - Row 2: Email Body (CKEditor container)                                                |
| - Row 3: Attachments (Chips list + "+ Add Attachment" file input, max 2MB guard)        |
+-----------------------------------------------------------------------------------------+
| BOTTOM NOTIFICATION STRIP (#f8fafc background, #e2e8f0 border)                          |
| - Dispatches automatically upon clicking Save & Next in header topbar                   |
+-----------------------------------------------------------------------------------------+
```

### Data Flow & Tenancy
- **Reply-To Resolution:** `auth()->user()->email` populates `#co_send_replyto`. When the header topbar's `Save & Next` button triggers `#co_send_submit`, `reply_to_email` is appended to `FormData` and received by `ServiceCompanyCvTrackerController::fwd_offer_send()`, where it is sanitized and set as the email's `reply_to` header.
- **Offer Status Synchronization:** On AJAX success, `window.coSendInFlight` is reset, the topbar Save button transitions to `Offer Released ✓`, and `#co_send_status_badge` in the Sub-Header automatically transitions from warning (`badge-light-warning`) to success (`badge-light-success`) displaying `Offer Released` with a checkmark SVG.

---

## 4. File Changes Matrix

| File Path | Section | Changes Implemented |
| :--- | :--- | :--- |
| `resources/views/service_company/job-applications/create-offer.blade.php` | CSS (lines 2098–2240) | Modernized Step 5 styles to Rule 20 Card + Sub-Card standard; added `#co_send_status_badge`, `#co_send_valid_badge`, `.co-send-main-card`; removed obsolete `.co-send-hero` CSS; standardized `#co_send_member_table` header backgrounds (`#f1f5f9`). |
| `resources/views/service_company/job-applications/create-offer.blade.php` | HTML (lines 4991–5210) | Removed candidate hero card completely; introduced Sub-Header with anchored background `#f8fafc` and status/expiry badges; resolved `$so_reply_to_email = auth()->user()->email`; divided composer into 2 clean Tier 3 sub-cards (`EMAIL RECIPIENTS & CONFIGURATION`, `OFFER LETTER COMPOSER & ATTACHMENTS`). |
| `resources/views/service_company/job-applications/create-offer.blade.php` | JS (line 8201) | Extended post-send success handler to update `#co_send_status_badge` dynamically to `Offer Released` with SVG checkmark while preserving backward-compatible classes (`co-send-fact-sent`). |

---

## 5. 15.6" Screen Fit & UI/UX Standards Compliance

- **Rule 14 & Rule 20:** Screen dimensions (`1366×768` and `1920×1080`) are strictly respected. By removing the bulky candidate hero card, vertical height is reclaimed, allowing the composer fields and attachments to be viewed without awkward scroll-jumping.
- **Header Background Standard:** Main drawer sub-header and sub-card headers both feature dedicated light backgrounds (`#f8fafc` with border `#e2e8f0`), creating clear visual hierarchy.
- **Rule 26 (Zero AI Slop):** No glowing effects, no neon gradients, zero emojis in badges or labels. All icons rendered via clean, deterministic inline SVGs to prevent square character glyph collisions (`□`).

---

## 6. Verification Evidence

1. **PHP Syntax Check:**
   ```powershell
   php -l resources/views/service_company/job-applications/create-offer.blade.php
   # Output: No syntax errors detected in resources/views/service_company/job-applications/create-offer.blade.php
   ```
2. **Backward Compatibility:**
   - Hidden inputs (`#co_send_offerid`, `#co_send_appid`, `#co_send_fromemail`, `#co_send_candidateemail`) and hidden trigger (`#co_send_submit`) retained.
   - Compatibility anchor `#co-send-step5-designation` preserved for dynamic designation updater.
   - Status badge carries both `#co_send_status_badge` and `.co-send-fact-notsent` / `.co-send-fact-sent` classes for instant reactivity across all existing scripts.
