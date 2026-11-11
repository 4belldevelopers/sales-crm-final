# SERVICE COMPANY — USER PROFILE VIEW PAGE UI/UX AUDIT & REDESIGN PROPOSAL

> **Module:** Service Company — User Profile (View Mode)  
> **Target Route:** `service_company.profile.index` (`/service_company/profile`)  
> **Current Blade File:** `resources/views/service_company/profile/index.blade.php`  
> **User Reference Screenshot:** `media_1794383522837_e63ec57c.jpg` (`https://testing.jero.works/service_company/profile`)  
> **Design Benchmark Reference:** JERO Enterprise Modern Card + Sub-Card Design Standard (Rule 20) & Add Internal Benchmark  
> **Screen Specification:** 15.6" Laptop Displays (`1366×768` & `1920×1080`), Zero Horizontal Overflow (`overflow-x: hidden`)  
> **Date:** November 11, 2026  
> **Status:** AUDIT & PROPOSED REDESIGN (Awaiting Explicit User Approval Before Code Implementation)

---

## 1. Executive Summary & Context

The Service Company **User Profile View Page** (`https://testing.jero.works/service_company/profile`) serves as the central command dashboard and recruiter identity hub within JERO ATS. It aggregates personal coordinates, organization affiliation, account security, subscription tiers, high-level recruitment metrics (Active Jobs, Candidates, Interviews, Joinings, Rank), and tabbed deep-dive operational modules (My Jobs, My Candidates, Interview Tracker, Joinings, Rank, Dashboard, Settings).

Following a forensic audit of [`resources/views/service_company/profile/index.blade.php`](../../resources/views/service_company/profile/index.blade.php) and the user-provided live screenshot, significant UI/UX opportunities were identified to transition this view into the modern **Card + Sub-Card Enterprise Design System (Rule 20)**.

> [!IMPORTANT]
> **Zero Implementation Rule:** Per strict user instructions, no application code files will be modified until explicit review and approval of this proposal is provided by the user.

---

## 2. Forensic UI/UX Audit of Existing Profile View Page

### 2.1 Visual & Structural Audit Findings

| Component / Area | Existing Implementation (`profile/index.blade.php`) | Proposed Modern Card + Sub-Card Standard (Rule 20) | Severity / Gap Analysis |
|---|---|---|---|
| **Top Hero Section** | Gradient background (`linear-gradient(135deg, #eef2ff, #f5f8ff)`) with an unstyled circular initial ("V") and an inline completion bar stretched across the center. | **Tier 1 Header Profile Card:** Pure white container (`#ffffff`, border `#e2e8f0`, radius 10px) with elevated avatar, verified recruiter badge, user ID pill, location pill, and compact profile health widget. | **High:** Existing gradient feels dated; inconsistent with modern white card enterprise layouts. |
| **Hero Actions Group** | Stacked buttons on right ("Edit Profile", "Share Company Page", "Upload Profile"). "Upload Profile" actually routes to the same edit page. | Streamlined 2-button hierarchy: Prominent Primary Action (`Edit Profile` with pen icon) + Secondary Outline (`Share Company Page`). | **Medium:** Redundant action eliminated; clear visual hierarchy established. |
| **KPI Metrics Strip** | Flat 5-column grid (`.scp-stats`) with arbitrary colored icon boxes. | **Tier 1.5 Sub-Card Metrics Grid:** Interactive 5-card row with soft-tinted icons (`#eff6ff`, `#ecfdf5`, etc.), bold `16px` metrics, and 1-click tab jump triggers. | **Medium:** Cards now serve as interactive shortcuts to their respective tabs. |
| **Sub-Tabs Header** | Legacy Metronic tab line (`.nav-tabs-line-3x`) with a plain blue bottom underline and unstyled link text. | **Tier 2 Dedicated Sub-Tabs Card:** White container (`#ffffff`, border `#e2e8f0`) with rounded tab buttons; **ONLY** the single active tab receives primary blue highlight (`#009ef7`), with pill count badges. | **Critical:** Non-compliant with Rule 20. Tabs must sit in a dedicated card with discrete buttons. |
| **My Details Tab Layout** | 3-column grid (`.scp-grid3`) with uneven card heights (Col 1: 6 rows, Col 2: 5 rows, Col 3: stacked 2 cards). | Balanced 3-column grid of equal-height **Tier 3 Cards** with matching headers, subtitles, and action links. | **High:** Existing layout produces jagged bottoms and horizontal cramping on 1366×768 screens. |
| **Data Rows & Dividers** | Plain text rows with dashed borders (`border-bottom: 1px dashed #eef2f7`), grayed-out keys (`#94a3b8`), and right-aligned values. | **Discrete Sub-Card Data Tiles:** Individual bordered sub-cards (`#f8fafc`, border `#e2e8f0`, radius 8px) with left glyph icons (user, hash, briefcase, email, phone) and bold values. | **High:** Sub-card tiles improve readability and match the Add Internal design benchmark. |
| **WhatsApp Field** | Crammed "Click to Connect" button wrapped in right-aligned column. | Dedicated Sub-Card Tile with WhatsApp green pill badge (`#25d366`) and branded connect button. | **Medium:** Clean visual alignment with other communication channels. |
| **Company Info Actions** | Shows an "Edit" button that links to *user profile edit*, where company fields cannot actually be changed. | Contextual "Manage" link that properly routes to Company Profile settings for authorized users. | **Medium:** Prevents recruiter confusion regarding organization-level data. |

---

## 3. The 3-Tier Layered Architecture (Rule 20)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1: TOP HERO PROFILE HEADER CARD (White #ffffff, Border #e2e8f0, Radius 10px)                │
│  [Avatar 72px]  Vibhanshu Jaiswal [✔ Verified]           [Profile Health: 75% ████░] [Edit Profile]│
│                 Directors • 4Bell Technology             ✓ Basic  ✓ Contact  ✓ Company             │
│                 [🛡️ Verified Employer] [ID: #2] [📍 Noida]                                       │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1.5: HIGH-DENSITY KPI STATS METRIC STRIP (Rule 20 Sub-Cards)                              │
│  ┌───────────────┐ ┌───────────────┐ ┌───────────────┐ ┌───────────────┐ ┌───────────────┐        │
│  │ 💼 Active Jobs│ │ 👥 Candidates │ │ 📅 Interviews │ │ 🤝 Joinings   │ │ 🏆 My Rank    │        │
│  │      43       │ │      18       │ │       2       │ │       0       │ │      #1       │        │
│  └───────────────┘ └───────────────┘ └───────────────┘ └───────────────┘ └───────────────┘        │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 2: DEDICATED SUB-TABS CARD (White #ffffff, Border #e2e8f0, Radius 10px - Rule 20)           │
│  [My Details (Active Blue)] [My Jobs (43)] [My Candidate (18)] [Interviews (2)] [Joinings (0)]...│
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 3: "MY DETAILS" TAB — BALANCED 3-CARD ENTERPRISE GRID                                      │
│  ┌─────────────────────────────┐ ┌─────────────────────────────┐ ┌─────────────────────────────┐  │
│  │ CARD A: PERSONAL INFO [Edit]│ │ CARD B: COMPANY INFO [Manage│ │ CARD C: SECURITY & PLAN     │  │
│  │ ┌─────────────────────────┐ │ │ ┌─────────────────────────┐ │ │ ┌─────────────────────────┐ │  │
│  │ │ 👤 Name: Vibhanshu J.   │ │ │ │ 🏢 Company: 4Bell Tech   │ │ │ │ 🔑 Password: [Reset]   │ │  │
│  │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │  │
│  │ │ #️⃣ User ID: #2          │ │ │ │ 🌐 Website: 4belltech.com│ │ │ │ 🛡️ Status: [Active]    │ │  │
│  │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │  │
│  │ │ 💼 Role: Directors      │ │ │ │ 🏳️ Country: India        │ │ │ │ 📦 Operational: Silver  │ │  │
│  │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │  │
│  │ │ ✉️ Email: [Verified]     │ │ │ │ 🗺️ State: Uttar Pradesh  │ │ │ │ 🏷️ Sales: Free Sales   │ │  │
│  │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │ │ ├─────────────────────────┤ │  │
│  │ │ 📞 Phone: +91 7217860087 │ │ │ │ 📍 City: Noida          │ │ │ │ [Upgrade Plan Button]   │ │  │
│  │ ├─────────────────────────┤ │ │ └─────────────────────────┘ │ │ └─────────────────────────┘ │  │
│  │ │ 💬 WhatsApp: [Connect]  │ │ │                             │ │                             │  │
│  │ └─────────────────────────┘ │ │                             │ │                             │  │
│  └─────────────────────────────┘ └─────────────────────────────┘ └─────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Preservation of Existing Functionality & Contracts

To maintain strict compliance with **Rule 7 (Zero Business Logic Mutations)**:

1. **All Tab Navigation Triggers Preserved:**
   - `#requisitions` (`load_jobs(...)`)
   - `#candidate` (`load_application(...)`)
   - `#interview_tracker` (`load_interviewSchedule(...)`)
   - `#joinings` (`jointableLoad(...)`)
   - `#myRank`, `#candidateDashboard`, `#settings`
2. **WhatsApp Connection Logic Preserved:**
   - Existing dynamic hooks (`.sendwhatsapp_connected_{id}`, `.sendwhatsapp_connect_{id}`, `.sendwhatsapp_panel`, `.deleteBtn`) remain 100% untouched.
3. **Password Reset Modal:**
   - Trigger attributes (`data-toggle="modal" data-target="#reset_password"`) preserved.
4. **Subscription Redirection:**
   - Route `service_company.subscribe.index` bound to view and upgrade triggers.
5. **0% MySQL Reads / Strict CQRS:**
   - The view remains 100% read-cached via `ServiceCompanyProfileService` and Redis/Elasticsearch.

---

## 5. Screen Responsiveness & 15.6" Screen Fit Verification

- **1920×1080 (Desktop):** Full 3-column card grid; KPI strip spans all 5 columns; zero vertical crowding; all primary recruiter information visible above standard fold.
- **1366×768 (Standard 15.6" Laptop):** Container max-width constrained to `1400px`; sub-cards prevent horizontal text wrapping; font size calibrated between `11.5px` and `16px`; zero horizontal scrollbar (`overflow-x: hidden`).
- **1024px & Tablets:** Responsive 2-column or 1-column card collapse; KPI metrics adapt gracefully to 3+2 grid; tab strip scrolls horizontally with hidden scrollbar.

---

## 6. Standalone Interactive Visual Preview

A complete, pixel-perfect visual preview of the redesigned User Profile View page has been generated:

- **Local Preview File:** [`public/previews/service_company_user_profile_view_redesign_preview.html`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/public/previews/service_company_user_profile_view_redesign_preview.html)
- **Web Server Preview URL:** [https://testing.jero.works/previews/service_company_user_profile_view_redesign_preview.html](https://testing.jero.works/previews/service_company_user_profile_view_redesign_preview.html)
- **Artifact Copy:** [`service_company_user_profile_view_redesign_preview.html`](file:///C:/Users/VIBHANSHU%20JAISWAL.000/.gemini/antigravity/brain/9280ad73-f8ae-4b04-8795-970e4d5dd30e/service_company_user_profile_view_redesign_preview.html)

**Next Step:** Review the audit feedback and visual preview above. Once you provide explicit approval, the implementation will proceed safely into [`resources/views/service_company/profile/index.blade.php`](../../resources/views/service_company/profile/index.blade.php).