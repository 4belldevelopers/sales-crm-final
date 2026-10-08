# Add Candidate UI/UX Design — God-Level Specification & Architecture Audit

> **System:** JERO ATS & Talent Marketplace  
> **Source Reference Page:** `resources/views/service_company/job-applications/create.blade.php` (Internal / Regular Candidate Flow)  
> **Target Replica Pages:**  
> - `resources/views/service_company/job-applications/clientcreate.blade.php` (Direct Client Candidate Flow)  
> - `resources/views/career-apply.blade.php` (Public Career Portal Candidate Application)  
> **Baseline Resolution:** 15.6" Laptop Screens (**1366×768** & **1920×1080**) with zero page-level horizontal scroll  
> **Design Foundation:** Metronic Component Architecture, Poppins Typography Hierarchy, Client-First Browser RAM Execution  
> **Theme Differentiation:**  
> - Regular ATS Flow: Enterprise Corporate Blue (`#245bff` / `#1b84ff`)  
> - Direct Client Flow: Enterprise Light Green (`#059669` / Emerald)  
> - Career Page Flow: Enterprise Light Orange (`#F59E0B` / Amber / Warm Gold)

---

## 1. Executive Overview & Design System Philosophy

The **Add Candidate** page (`create.blade.php`) represents the authoritative candidate onboarding interface in the JERO ATS platform. Engineered for high data-density and 0% server read strain, the interface coordinates 15 distinct sections into a unified single-page recruiter workflow.

Recruiters evaluate and input candidate information across dozens of attributes under fast-paced talent delivery deadlines. Consequently, the UI/UX is built around three core pillars:
1. **Visual Clarity & Information Hierarchy:** Distinct card wrappers (`.candidate-card`), structured headers with dedicated icons, and grouped form grids prevent cognitive overload.
2. **Instant Browser-Side Interactivity (0% Server Strain):** Dropdowns, qualification comboboxes, currency converters, recruiter comment templates, and pre-flight validation execute entirely in **User Browser RAM**, guaranteeing zero network latency during field navigation.
3. **15.6" Display Viewport Optimization:** Every section, input, dropdown panel, and modal is engineered to fit within standard 15.6" laptop screens (1366×768 at 125% OS scaling) with strict overflow containment and zero horizontal viewport jumping.

---

## 2. Color Palette & Theme Tokens

The design uses a clean enterprise SaaS color system with high-contrast text, subtle neutral borders, and clear semantic accents.

### 2.1 Corporate Blue Theme (`create.blade.php` — Internal Flow)

| Token Name | Hex / RGBA | Role / Usage in UI |
|---|---|---|
| `--jac-primary` | `#245bff` / `#1b84ff` / `#009ef7` | Primary buttons, active tabs, header action accents, focus rings |
| `--jac-primary-hover` | `#1748e8` / `#1570ef` | Button hover and active press states |
| `--jac-soft` | `#f4f8ff` / `#eff6ff` / `#f7f9fe` | Card header icon backgrounds, selection chip tints, table hovers |
| `--jac-text` | `#0b1545` / `#0f172a` | Primary headings, candidate card titles, field values |
| `--jac-muted` | `#5e6a95` / `#64748b` | Field labels, helper descriptions, breadcrumb text, secondary icons |
| `--jac-border` | `#dfe6f2` / `#e2e8f0` | Card borders, form input borders, divider lines |
| Focus Ring | `rgba(59, 130, 246, 0.25)` | 4px focus outline box-shadow on focused form controls |
| Toolbar Background | `#f4f8ff` | Top contextual toolbar background fill |
| Toolbar Border | `#e3e9f5` | Bottom border of the top contextual toolbar |

### 2.2 Enterprise Light Green Theme (`clientcreate.blade.php` — Direct Client Flow)

The Direct Client flow is intentionally differentiated by an **Enterprise Light Green Theme** (Emerald / Mint) to instantly signal to recruiters and account managers that they are submitting candidates into a client-exclusive portal.

| Token Name | Hex / RGBA | Role / Usage in UI |
|---|---|---|
| `--jac-primary` | `#059669` | Primary action buttons, active tabs, client header accents |
| `--jac-primary-hover` | `#047857` | Button hover and pressed active states |
| `--jac-soft` | `#f0fdf4` / `#ecfdf5` | Card header icon backgrounds, pill tints, table hover fills |
| `--jac-text` | `#0b1545` / `#0f172a` | Identical high-contrast enterprise text hierarchy |
| `--jac-muted` | `#5e6a95` / `#64748b` | Secondary labels and helper copy |
| `--jac-border` | `#dfe6f2` / `#e2e8f0` | Standard neutral card and field containment borders |
| Focus Ring | `rgba(16, 185, 129, 0.25)` | 4px emerald focus outline on interactive controls |
| Focus Border | `#10b981` | 2px solid border on focused inputs and active combobox triggers |
| Attention Highlight | `rgba(236, 253, 245, 0.6)` | Soft green pulse background on jumped/highlighted fields |
| Toolbar Background | `#f0fdf4` | Light green contextual header fill |
| Toolbar Border | `#d1fae5` | Soft emerald separator line under toolbar |
### 2.3 Enterprise Light Orange Theme (`career-apply.blade.php` — Public Candidate Flow)

The candidate-facing Career Page application is styled with an **Enterprise Light Orange Theme** to deliver a warm, modern, and engaging applicant journey while preserving the pristine white/neutral foundation of the enterprise ATS.

| Token Name | Hex / RGBA | Role / Usage in UI |
|---|---|---|
| `--jac-primary` | `#F59E0B` | Primary Apply/Submit buttons, active tabs, header action accents, focus rings |
| `--jac-primary-hover` | `#D97706` | Button hover and pressed active states |
| `--jac-soft` | `#FFF7ED` | Card header icon backgrounds, selection chip tints, table hovers |
| `--jac-highlight` | `#FFEDD5` | Soft highlights, active chip tints, attention pulse background |
| `--jac-border` | `#FED7AA` | Accent borders, contextual toolbar bottom border, active input border |
| `--jac-text` | `#0b1545` / `#0f172a` | Identical high-contrast enterprise text hierarchy |
| `--jac-muted` | `#5e6a95` / `#64748b` | Secondary labels, helper descriptions, placeholder text |
| Focus Ring | `rgba(245, 158, 11, 0.25)` | 4px amber/orange focus outline on interactive controls |
| Focus Border | `#F59E0B` | 2px solid border on focused inputs and active combobox triggers |
| Attention Highlight | `rgba(255, 237, 213, 0.6)` | Soft orange pulse background on jumped/highlighted fields |
| Toolbar Background | `#FFF7ED` | Light orange contextual header fill |
| Toolbar Border | `#FED7AA` | Soft orange separator line under toolbar |
| Modal Primary CTA | `#F59E0B` (hover `#D97706`) | Complete Required Fields modal action button |

### 2.4 Universal Semantic Accents (Shared Across All Flows)

- **Success / Positive:** `#0ca866` / `#10b981` (Completed parsing status, serving notice positive badge).
- **Warning / Amber:** `#f59e0b` / `#d97706` / `#fef9c3` (Parsing in progress, required fields warning modal shield, notice period buyout).
- **Danger / Error:** `#ef4444` / `#dc2626` / `#fef2f2` (Required field red asterisk `*`, validation error outlines, missing fields modal counter).
- **Neutral Surface:** `#ffffff` (Card bodies, input backgrounds, modal surfaces).
- **Page Canvas:** `#f7f9fc` (Background for the overall viewport and container).

---

## 3. Header Design & Contextual Toolbar

The header architecture combines system navigation with requisition context and persistent primary action triggers.

```text
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [Requisition Title & Code]   [Client Name]   [Openings: 5]   [Status: Active]   [Stage: Sourcing ▾]   │
│                                                                            ┌───────────────────────────┐│
│                                                                            │ ✈ Submit Candidate       ││
│                                                                            └───────────────────────────┘│
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 3.1 Structural Specifications
- **Element Container:** `#kt_toolbar.toolbar.candidate-top-toolbar`
- **Inner Wrapper:** `#kt_toolbar_container.container-fluid.d-flex.flex-stack.candidate-toolbar-container`
- **Dimensions & Spacing:**
  - Height: `min-height: 64px`
  - Outer Padding: `0 !important`
  - Inner Padding: `8px 16px !important`
  - Gap: `12px`
- **Requisition Context Integration:** Included via `@include('sections.service_company.toolbar')`:
  - `hideLocationDate => true` (strips redundant metadata to maintain short vertical height)
  - `showStage => true` (displays current recruitment pipeline stage)
  - `stageId => $currentAppMasterStatus` (authoritative pipeline stage binding)
  - Requisition title, client company badge, openings counter, active status badge.
- **Persistent Header Action (`.candidate-header-actions`):**
  - Trigger Button: `#save-form` (Add mode) / `#update-form` (Edit mode)
  - Height: `38px`
  - Padding: `9px 16px`
  - Font: Poppins, `13px`, `font-weight: 600`
  - Icon: `<i class="fa-solid fa-paper-plane"></i>` (Add) / `<i class="fa-solid fa-pen-to-square"></i>` (Edit)
  - Spacing: `gap: 8px`
  - Border Radius: `6px`
  - Visual Depth: `box-shadow: 0 6px 14px rgba(..., .18)`
  - State Indicators: Integrates Metronic indicator `data-kt-indicator="on"` with spinner `<span class="spinner-border spinner-border-sm"></span>` upon submission.

---

## 4. Card & Section Structure (The 15 Core Cards)

The entire candidate form is composed of **15 self-contained cards**. Every card adheres strictly to the `.candidate-card` structure:
- **Card Wrapper:** `.fs.candidate-card` (White background `#ffffff`, `border: 1px solid #e2e8f0`, `border-radius: 8px` to `12px`, `box-shadow: 0 1px 3px rgba(0,0,0,0.04)`, `margin-bottom: 20px`).
- **Card Header (`.candidate-card-header`):**
  - Minimum height: `56px` to `58px`
  - Padding: `12px 18px` to `14px 20px`
  - Background: Linear gradient with subtle theme tint
  - Border Bottom: `1px solid #edf2f7`
  - Icon Badge: `32×32px` rounded icon badge with colored background and matching icon glyph
  - Title: `15px` Poppins, `font-weight: 600`, color `#0f172a`
  - Subtitle / Helper: `12px` Poppins, `font-weight: 400`, color `#64748b`
- **Card Body (`.candidate-card-body`):** Padding `20px` (or `16px 20px` in high-density sections), structured via CSS Grid or Metronic form rows.

### Inventory of the 15 Cards

| # | Card Name | CSS Class Selector | Key Function & Contents |
|---|---|---|---|
| **1** | **Upload Resume** | `.resume-card` | Resume dropzone (PDF/DOCX max 10MB), direct-to-R2 presigned upload session, parsing status badge (`#resume-parser-status`), file details card (`.up-details-card`), replace/preview actions. |
| **2** | **Career Summary** | `.summary-card` | Professional summary textarea (`#profile_summary`), character counter, prefill toggle, AI-assisted candidate positioning. |
| **3** | **Candidate Profile** | `.candidate-profile-card` / `.bd-card` | Avatar/photo uploader, Full Name (with clear button), Email (with clear button), Mobile Phone (with country code), Alternate Phone, DOB datepicker, Gender select, Marital Status, Current Location (Tagify Google Places), Native Place. |
| **4** | **Primary Skills** | `.primary-skills-card` / `.se-card` | Tagify multi-skill tag input (`#skills`), skill pill tags with remove cross, popular skills quick-add bar. |
| **5** | **Experience Details** | `.experience-details-card` / `.pd-experience-section-card` | Total Experience (Years & Months split inputs), Relevant Experience (Years & Months split inputs), Employment Gap toggle and explanation textarea. |
| **6** | **Professional Profile** | `.professional-profile-card` / `.pd-card` | Current Employer / Company Name, Current Designation, Department / Functional Area, Highest Qualification (100% In-Memory Browser Combobox). |
| **7** | **Salary Details** | `.salary-card` / `.br-card` | Multi-currency selector (USD/INR/EUR/GBP/AED), indicative rate pill, Current Salary panel (Annual/Monthly/Hourly tabs + auto calculations), Expected Salary panel (synced tabs + auto calculations), Salary Summary panel (Current, Expected, Difference, Hike % badge). |
| **8** | **Notice Period & Joining Details** | `.notice-joining-card` / `.aw-card` | Notice period dropdown (Immediate, 15, 30, 60, 90 days), Serving Notice Period toggle, Last Working Day datepicker, Earliest Joining Date datepicker, Official Buyout Available toggle, Reason for Change textarea. |
| **9** | **Preferred Work Mode** | `.preferred-work-mode-card` | Interactive choice tiles: Work From Office (On-site), Hybrid, Work From Home (Remote) with icons and active border states. |
| **10** | **Job Skills Details** | `.job-skills-card` | Dynamic skill matrix matching candidate proficiency against requisition required skills, years of experience input, 5-star Select2 proficiency rating. |
| **11** | **Screening Questions** | `.questions-card` | Requisition-specific custom questions (text, number, radio, select) dynamically rendered from `job_additional_detail`. |
| **12** | **Availability for Next 3 Days** | `.interview-card` | 3 interview date/time slot windows (Flatpickr datepicker + JERO Enterprise Time Clock Popover), Preferred Interview Mode dropdown (Video Call, Telephonic, In-Person). |
| **13** | **Professional Links** | `.professional-links-card` / `.addl-card` | LinkedIn URL, GitHub URL, Portfolio / Website URL with brand leading icons. |
| **14** | **Visa Status & Work Authorization** | `.additional-details-card` / `.addl-card--details` | Citizenship Country dropdown, Visa Status dropdown (Citizen, Green Card, H-1B, EAD, TN, etc.), popular visa chips, Work Authorization expiry datepicker. |
| **15** | **Recruiter Comments** | `.recruiter-note-card` / `.rm-note-card` | Categorized template dropdown (Fit, Strong Fit, Screening, Technical), Sync details from form action button, Recruiter comments textarea, live word counter (0 / 100 words), internal privacy disclaimer badge. *(Internal & Direct Client flows only; strictly omitted on Career Page).* |

> [!IMPORTANT]
> **Candidate-Facing Flow Scope Isolation (`career-apply.blade.php`):**  
> On the public Career Page candidate application (`career-apply.blade.php`), **Card 15 (Recruiter Comments) is strictly omitted/hidden**. Candidates must never view or interact with internal recruiter evaluation templates or confidential hiring notes. The candidate application form consists of Cards 1 through 14. Cards 1 through 14 are 100% identical in UI, layout, typography, box sizes, and interaction models across all flows.

---

## 5. Box Sizes, Positions, Spacing & Grid System

The page layout adheres to a strict spacing and dimensional hierarchy to prevent layout shift and fit standard laptop screens:

### 5.1 Container & Wrapper Architecture
- **Max Container Width:** `max-width: 1560px !important; margin: 0 auto;`
- **Main Container:** `#kt_content_container.container-fluid.px-3`
- **Outer Page Background:** `#f7f9fc`

### 5.2 Sizing Standards
- **Form Inputs (`input[type="text"]`, `input[type="number"]`, `input[type="email"]`, `.form-control`):**
  - Height: `38px` to `42px`
  - Padding: `8px 12px`
  - Border: `1px solid #e2e8f0`
  - Border Radius: `6px`
  - Background: `#ffffff`
  - Font Size: `13px`
- **Dropdown Triggers (`select`, `.select2-selection--single`, `.combobox-trigger`):**
  - Height: `38px` to `42px`
  - Border: `1px solid #e2e8f0`
  - Border Radius: `6px`
  - Line Height: Matches height for vertical text centering
- **Textareas (`#profile_summary`, `#recruiter_note`, `#reason_for_change`):**
  - Minimum Height: `88px` to `120px`
  - Padding: `10px 14px`
  - Border Radius: `6px`
- **Buttons:**
  - Large / Primary Header CTA: Height `38px`, padding `9px 16px`, font `13px`, weight `600`
  - Modal Primary CTA: Height `44px`, padding `10px 20px`, font `14px`, weight `600`
  - Secondary / Utility Buttons: Height `32px`, padding `4px 10px`, font `12px`

### 5.3 Spacing & Grid Tokens
- **Section Margins:** `margin-bottom: 20px` (consistent across all 15 cards)
- **Grid Gaps:**
  - Card 2-Column Grids: `gap: 16px` (e.g. Total Exp vs Relevant Exp, First Name vs Last Name)
  - Card 3-Column Grids: `gap: 16px` (e.g. Salary panels: Current, Expected, Summary)
  - Card 4-Column Grids: `gap: 12px` on 1920px screens, reflowing to 2-columns on 1366px screens
- **Internal Card Padding:**
  - Headers: `12px 18px`
  - Bodies: `20px`
  - Sub-cards / Panels: `14px 16px`

---

## 6. Field Alignment, Grouping & Ordering

Field grouping follows intuitive human cognition patterns for recruiter data entry:

### 6.1 Logical Flow Progression
1. **Source Asset First (Card 1):** Resume Upload parses and auto-populates subsequent fields in browser RAM.
2. **Executive Synthesis (Card 2):** Career Summary establishes high-level candidate qualifications.
3. **Identity & Contact (Card 3):** Full Name, Email, Phones, DOB, Gender, Current Location.
4. **Core Capabilities (Cards 4–6):** Primary Skills, Experience Breakdown (Total & Relevant), Current Employer & Designation, Highest Qualification.
5. **Commercials & Logistics (Cards 7–9):** Multi-currency Salary (Current vs Expected), Notice Period & LWD, Work Mode preferences.
6. **Requisition Fit (Cards 10–12):** Job Skills Matrix rating, Screening Questions, 3-Day Interview Availability windows.
7. **Compliance & Verification (Cards 13–14):** Professional Social Profiles, Work Authorization / Visa Status.
8. **Internal Evaluation (Card 15):** Recruiter Evaluation Note (with internal lock privacy tag).

### 6.2 Label & Input Alignment
- **Top-Aligned Labels:** Labels sit directly above their respective inputs (`margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #475569; text-transform: none;`).
- **Mandatory Indicators:** Every required field is explicitly flagged with `<span class="rq text-danger" style="color: #f1416c;">*</span>`.
- **Inline Action Buttons:** Full Name and Email inputs feature inline floating "Clear" buttons (`.field-clear-btn`) that reveal on input focus/content for rapid corrections.
- **Side-by-Side Dual Selectors:**
  - Country Code + Phone Number: Country code dropdown (fixed `100px` width) seamlessly attached to phone number text input.
  - Frequency + Salary: Annual / Monthly / Hourly segmented pill switches positioned atop current and expected CTC input boxes.

---

## 7. Dropdown & Combobox Design Specifications

All dropdowns on the page are engineered to prevent browser freeze and eliminate server roundtrips:

### 7.1 Highest Qualification Combobox (100% Browser RAM Engine)
- **Container Isolation:** Scoped with `.candidate-field-control--has-combobox`
- **Z-Index Layering:** Automatically elevates parent card (`.has-active-combobox { z-index: 1050 !important; }`) to prevent dropdown clipping behind adjacent cards.
- **Trigger Element (`.combobox-trigger`):**
  - Height: `42px`
  - Border: `1px solid #e2e8f0`
  - Display: Flex with text label and custom SVG chevron
  - Active State: Border `#009ef7` (Regular) / `#059669` (Client) with 4px soft glow
- **Floating Panel (`.combobox-panel`):**
  - Position: Absolute below trigger (`top: calc(100% + 4px); z-index: 1055;`)
  - Width: `100%`
  - Surface: `#ffffff`, border `1px solid #e2e8f0`, shadow `0 10px 25px -5px rgba(0,0,0,0.1)`
  - Sticky Search Header: Search input (`#hqComboboxSearch`) sticks to top with magnifying glass icon.
  - Scrollable Options List: `max-height: 260px; overflow-y: auto;`
  - Grouped Category Sections: Categorized under PhD, Master's, Bachelor's, Diploma, Certification with small category badges (`.combobox-badge`).
  - Keyboard Navigation: Full support for `ArrowDown`, `ArrowUp`, `Enter`, and `Escape`.

### 7.2 Multi-Currency Selector & Indicative Hover Lens
- **Select2 Component:** Scoped currency selector supporting USD, INR, EUR, GBP, AED, JPY, CAD, AUD.
- **Indicative Pill (`.jero-pill-wrapper`):** Displays equivalent USD amount next to field labels.
- **Hover Lens Popover:**
  - Fixed popover displaying live conversions calculated client-side from currency snapshot rates.
  - Renders tabular breakdown of annual, monthly, and hourly equivalents without contacting backend currency endpoints.

### 7.3 JERO Enterprise Time Clock Popover (`.jgtp-popover`)
- **Trigger:** Interactive clock icon on each of the 3 availability interview windows.
- **Popover Dimensions:** Width `310px`, background `#ffffff`, border-radius `12px`, shadow `0 16px 40px -8px rgba(15, 23, 42, 0.28)`.
- **Clock Interface:**
  - 12-Hour format with interactive Hour and Minute dials/pickers.
  - AM / PM pill toggle with instant state switch.
  - Quick presets: 09:00 AM, 11:00 AM, 02:00 PM, 04:00 PM, 06:00 PM.
  - Keyboard-accessible Done button.

### 7.4 Recruiter Comments Template Dropdown
- **Trigger:** Categorized template selector with preset badges.
- **Template Categories:** "Strong Fit", "Relevant Experience", "Screening Notes", "Technical Match".
- **Dynamic Token Interpolation:** Selecting a template automatically injects and replaces candidate variables (`[relevant experience]`, `[all skills in job]`, `[current designation]`) in real time.

---

## 8. Modals, Drawers & Interactive UI Elements

### 8.1 Complete Required Fields Modal (`#requiredFieldsModal`)
Pre-flight client-side submission defense that intercepts invalid submissions before any HTTP request leaves the browser:

```text
┌────────────────────────────────────────────────────────┐
│                          [ ! ]                         │
│               Complete Required Fields                 │
│   3 required fields need your attention before you     │
│   can submit this candidate.                           │
│                                                        │
│  REQUIRED FIELDS                                       │
│  ┌──────────────────────────────────────────────────┐  │
│  │ (1) Candidate Full Name                        > │  │
│  └──────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────┐  │
│  │ (2) Email Address                              > │  │
│  └──────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────┐  │
│  │ (3) Primary Skills                             > │  │
│  └──────────────────────────────────────────────────┘  │
│                                                        │
│  ℹ Click a field above to jump directly to it.         │
│  ┌──────────────────────────────────────────────────┐  │
│  │ Review Required Fields                         → │  │
│  └──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────┘
```

- **Overlay (`.rf-modal-overlay`):** Fixed full-screen (`100vw × 100vh`), `background: rgba(15, 23, 42, 0.65)`, `backdrop-filter: blur(4px)`, `z-index: 999999`.
- **Modal Box (`.rf-modal-box`):**
  - Dimensions: `width: 100%; max-width: 480px;`
  - Surface: `#ffffff`, `border-radius: 16px`, `padding: 32px 30px 26px`, border `1px solid #e2e8f0`.
  - Elevation: `box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25)`.
- **Warning Shield (`.rf-warning-icon-wrap`):**
  - Size: `64×64px` circular badge, background `#fef9c3`, border `2px solid #facc15`.
  - Icon: Bold `!` in color `#ca8a04`.
- **Interactive Missing Field Rows (`.rf-field-row`):**
  - Background: Soft red `#fef2f2`, border `1px solid #fee2e2`, `border-radius: 8px`, `padding: 10px 14px`.
  - Number Badge: Red circle `20×20px`, background `#ef4444`, text `#ffffff`.
  - Interaction: Clicking any row closes the modal, smoothly scrolls the browser viewport directly to the invalid input, sets focus, and triggers a 2-second visual pulse outline (`.field-attention-highlight`).
- **CTA Button (`.rf-cta-btn`):** Full-width `44px` height button that shifts focus to the first missing field.

---

## 9. Buttons, Badges, Borders, Icons & States

### 9.1 Buttons
- **Primary CTA (`.talent-topbar-primary`):**
  - Corporate Blue (`create.blade.php`): `#245bff` (hover `#1748e8`), shadow `rgba(36, 91, 255, .18)`.
  - Light Green (`clientcreate.blade.php`): `#059669` (hover `#047857`), shadow `rgba(5, 150, 105, .20)`.
  - Disabled State: `opacity: 0.65; cursor: not-allowed;`
  - Loading State: `data-kt-indicator="on"`, hidden text label, visible rotating SVG spinner.

### 9.2 Badges & Pill Chips
- **Status Badges:** Small uppercase indicators with `border-radius: 4px` or `20px` (pill), padding `4px 8px`, `font-size: 11px`, `font-weight: 600`.
- **Popular Skill / Visa Chips (`.wa-chip`):**
  - Background: `#f1f5f9` (default), border `1px solid #e2e8f0`, text `#475569`.
  - Selected State: Theme tint (`#eff6ff` blue / `#ecfdf5` green), matching border and text.

### 9.3 Icons & Glyphs
- Standardized icon system combining **FontAwesome 6 Pro/Free** (`fa-solid`, `fa-regular`) and clean **Metronic SVG Icons**.
- Card headers strictly utilize `16px` to `18px` glyphs centered within `32×32px` rounded accent containers.

---

## 10. Responsive Behavior & Visual Hierarchy (15.6" Baseline)

### 10.1 Viewport Baseline Strategy
1. **1366×768 (Standard 15.6" Display with 125% OS Scaling):**
   - Usable browser viewport is approximately `1366×600px`.
   - Modals and popovers constrain height with `max-height: calc(100vh - 40px)` and utilize internal overflow scrolling (`overflow-y: auto`).
   - Sticky topbar ensures action buttons remain clickable regardless of form scroll depth.
2. **1920×1080 (Full HD):**
   - Content container caps at `1560px` centered width to maintain optimal eye-tracking line length.
   - 3-column and 4-column cards (e.g. Salary, Interview Slots, Basic Details) display fully expanded side-by-side.
3. **Mobile & Compact Screens (< 768px):**
   - Multi-column form groups collapse automatically to single-column (`col-12`).
   - Sibling dual selectors (e.g. Total Exp Years + Months) preserve inline pairing for compact ergonomics.

---

## 11. Replication Roadmap for `clientcreate.blade.php`

To achieve **100% pixel-perfect UI/UX identity** between `create.blade.php` and `clientcreate.blade.php`:
1. **Replicate the complete HTML structure** of all 15 cards, field orders, labels, combobox wrappers, modals, and header toolbar containers.
2. **Replicate all modern JavaScript modules** (presign upload session, JeroCandidateCurrencyEngine, Candidate Required Fields Config and Modal Navigation, 100% Browser RAM Highest Qualification Combobox, Recruiter Comments Categorized Template Engine, Availability Pickers with Time Clock popover).
3. **Preserve strict backend isolation**:
   - Submission endpoint routes strictly to `route("service_company.job-applications.clientappstore")`.
   - Update endpoint routes strictly to `route("service_company.job-applications.update", @$application->id ?: (@$appId ?: 0))`.
   - Retain hidden input `percentage_hike` and `AddJobWiseCondidateRankRow`.
4. **Apply the Enterprise Light Green Theme**:
   - Override `--jac-primary: #059669;`, `--jac-primary-hover: #047857;`, `--jac-soft: #f0fdf4;`, `--jac-success: #059669;`.
   - Update toolbar background to `#f0fdf4` with border `#d1fae5`.
   - Update focus rings and interactive states to emerald `#10b981` / `#059669`.
   - Update Required Fields Modal primary CTA to `#059669` (hover `#047857`).

---

## 12. Replication Roadmap for Career Page (`career-apply.blade.php`)

To achieve **100% pixel-perfect UI/UX identity** between `create.blade.php` and `career-apply.blade.php`:
1. **100% Structural & Component Replication**:
   - Replicate the exact HTML structure, widths, heights, margins, padding, spacing, alignment, card layouts, field orderings, and responsive behavior for Cards 1 through 14.
   - Header design and height strictly match `create.blade.php`, integrating `@include('sections.service_company.open-toolbar', ...)` with public context and the primary submit button `#save-form`.
   - Standalone public HTML page layout loading Metronic CSS/JS bundles, Poppins typography, FontAwesome 6, Tagify, Flatpickr, Select2, and SweetAlert2.
2. **Candidate-Facing Scope Isolation (Omit Recruiter Comments)**:
   - **Card 15 (Recruiter Comments) is completely removed** from `career-apply.blade.php`.
   - Recruiter evaluation templates and internal note JS engines are omitted.
   - Existing candidate data models, recruiter workflows, and backend storage structures remain 100% untouched.
3. **Enterprise Light Orange Theme Applied**:
   - Primary: `#F59E0B`, Hover/Active: `#D97706`, Light Background: `#FFF7ED`, Soft Highlight: `#FFEDD5`, Border: `#FED7AA`.
   - Clean white/neutral surfaces preserved for all card bodies, inputs, and general canvas.
   - Selective light orange styling applied to top contextual header toolbar, primary buttons, input/select focus rings, combobox active triggers, chip selections, time clock presets, and Required Fields Modal CTA.
4. **Complete Page-Level Validation Before Submission**:
   - Candidates are **strictly blocked from submitting incomplete forms**.
   - Pre-flight `validateAllMandatoryCandidateFields()` validates 100% of required fields across all cards:
     - Card 1: Resume Upload (uploaded file path or session)
     - Card 2: Career Summary (non-empty summary text)
     - Card 3: Candidate Profile (Full Name ≥ 2 chars, Primary Email valid format, Primary Contact ≥ 6 digits, Current Location, Gender)
     - Card 4: Primary Skills (minimum 1 Tagify skill)
     - Card 5: Experience Details (Total Exp & Relevant Exp years)
     - Card 6: Professional Profile (Highest Qualification; plus Current Designation, Company, and Employment Type if non-fresher)
     - Card 7: Salary Details (Current CTC & Expected CTC if non-fresher)
     - Card 8: Notice Period & Joining Details (Notice period selection if non-fresher)
     - Card 9: Preferred Work Mode (Work from Office, Hybrid, or Remote selected)
     - Card 10: Job Skills Matrix (proficiency rating selected for all required requisition skills)
     - Card 11: Screening Questions (answers completed for all requisition screening questions)
   - Pre-flight `#requiredFieldsModal` intercepts invalid submissions, displays missing field count, and guides the candidate directly to the first incomplete field with smooth scroll, auto-focus, and a 2-second orange visual pulse highlight (`.field-attention-highlight`).
5. **Robust Public Submission Flow**:
   - Submission endpoint routes strictly to `route("get.careerStore")` with FormData and atomic `submission_token`.
   - Direct R2 / presign upload pipeline with fallback to `route("get.uploadresume")`.
   - Integrated client-side draft autosave powered by `CareerApplyDraftManager` (scoped to `company_id` and `job_id`).

---
*Document authoritatively verified against production AST, database schemas, and Metronic component registry.*
