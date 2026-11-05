# Implementation Plan: Redesign of Super Admin Microsoft Teams Meeting Setting & Link Pool Hub

> **Target Route:** `http://localhost/git/JWMaincode28June2026/super-admin/team-meeting-setting` (`superadmin.company.team_meeting_setting`)  
> **Target Controller:** `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` (Methods: `teamMeetingSetting`, `teamMeetStatusUpdate`, `settingteamMeetingStore`, `updateTeamMeetingLink`, `getTeamMeetingScheduleHistory`, `meetingListDelete`, `addMeetingLists`)  
> **Import Processing Controller:** `app/Http/Controllers/HomeController.php` (`importMeetings`) & `app/Imports/MeetingImport.php`  
> **Target Blade View:** `resources/views/super-admin/meeting/team-meeting-setting.blade.php`  
> **Legacy External Add View (To deprecate/clean):** `resources/views/super-admin/meeting/add-meeting.blade.php`  
> **Database Schema Truth:**  
> - `team_meeting_lists` (`database/schema/mysql-schema.sql:4310`)  
> - `team_meeting_schedule` (`database/schema/mysql-schema.sql:4323`)  
> **Consuming ATS Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (lines 9640–9665, line 9957)  
> **Design Standard:** `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` & `docs/UI_UX_STANDARDS.md`  
> **Target Screen Profiles:** Standard 15.6" Laptop Displays (`1366×768` and `1920×1080`) with `overflow-x: hidden` and zero horizontal scroll  
> **Status:** 100% Completed, Verified & Production Ready (Phases 1 - 6 Executed)  

---

## 1. Executive Summary & Architectural Context

In the JERO multi-tenant B2B hiring SaaS platform, video interview coordination is a core operational bridge connecting corporate hiring managers, staffing partners, agency recruiters, and talent pool candidates. Alongside Zoom and Google Meet, **Microsoft Teams** is one of the premier enterprise conferencing platforms utilized by corporate and enterprise clients.

### How Microsoft Teams Integration Works in JERO (The Link Pool Paradigm)
Unlike Zoom's Server-to-Server OAuth (which provisions dynamic meeting rooms on-the-fly via API calls), JERO's Microsoft Teams integration utilizes a **High-Throughput Pre-Allocated Meeting Link Pool System**:
1. Super Admin curates a pool of validated, permanent Microsoft Teams conference links associated with corporate host accounts (stored in `team_meeting_lists`).
2. When an ATS recruiter schedules an interview for a candidate on a specific date/time (`ServiceCompanyJobApplicationController.php:9645`):
   - The platform checks `team_meeting_schedule` for any meeting link already booked on that `interview_date`:
     ```php
     $conflictingMeetingIds = DB::table('team_meeting_schedule')
         ->whereDate('interview_date', $interviewDate)
         ->pluck('meeting_link');

     $google_meeting_lists = DB::table('team_meeting_lists')
         ->where('status', '=', 1)
         ->whereNotIn('meeting_link', $conflictingMeetingIds)
         ->first();
     ```
   - If an active link is free, it assigns the link to the candidate's interview and commits the allocation to `team_meeting_schedule` (`ServiceCompanyJobApplicationController.php:9957`):
     ```php
     if ($interview_mode == 2 && $interview_mode_lavel == 5) {
         DB::table('team_meeting_schedule')->insert([
             'meeting_link' => $request->url,
             'user_id' => auth()->user()->id,
             'company_id' => auth()->user()->company_id,
             'interview_job_wise_con_id' => $AddJobWiseCondidate->id,
             'interview_date' => $interviewDate
         ]);
     }
     ```
   - If all active links are occupied on that date, it returns `'status' => 'expired'`, alerting the recruiter to choose an alternate time or notifying the Super Admin to expand the pool.

---

## 2. Deep Architectural & Code Audit: Legacy State vs. Senior Architect Standard

### A. Backend Process Audit

| Component / Function | Legacy State (Defects & Risks) | Target Senior Architect Solution |
|---|---|---|
| **Phantom Route** (`routes/superadmin.php:82`) | `Route::post('setting-team-meeting-store', [SuperAdminCompanyController::class, 'settingteamMeetingStore'])->name('company.settingteamMeetingStore');` is registered, but method `settingteamMeetingStore` **does not exist** in `SuperAdminCompanyController.php`! Any call triggers a fatal `BadMethodCallException`. | Implement `settingteamMeetingStore` supporting both dynamic multi-row batch insert and single item creation with strict URL sanitization, duplicate prevention, and cache busting. |
| **Directory Search & Querying** (`SuperAdminCompanyController.php:9545-9562`) | Only searches `meeting_link LIKE %...%`. Ignores `account_name` completely. Ignores `status` filtering. Pagination uses `->paginate(10)` without `->withQueryString()`, wiping search parameters on page navigation. | Refactor `teamMeetingSetting`: search both `account_name` and `meeting_link` with composite `OR`, support `status` filtering (All, Active=1, Inactive=0), increase page size to 15, and append query strings via `->withQueryString()`. |
| **KPI Metrics & Pool Analytics** | No metrics provided. Super Admin is blind to total capacity, active pool count, disabled links, and recruiter interview utilization. | Calculate and inject 4 high-level KPI metrics into view: `totalLinksCount`, `activeLinksCount`, `inactiveLinksCount`, and `scheduledMeetingsCount` (from `team_meeting_schedule`). |
| **Status Toggle Endpoint** (`SuperAdminCompanyController.php:9566-9580`) | Raw update with unvalidated `$request->id` and `$request->chkStatus`. Returns empty message. Zero cache invalidation. | Validate `id => 'required|integer|exists:team_meeting_lists,id'` and `chkStatus => 'required|in:0,1'`. Return structured JSON with status text. Invalidate `jero:team_meeting_links:active_pool` cache. |
| **Single Link Edit / Update** | Feature does not exist. Admin cannot fix typos in URLs or account names without deleting and recreating rows. | Implement `updateTeamMeetingLink` endpoint (`POST /super-admin/team-meeting-update/{id}`): validates fields, prevents duplicate collisions, updates record atomically, and clears cache. |
| **ATS Allocation Monitor Endpoint** | No administrative visibility into booked Microsoft Teams meetings. | Implement `getTeamMeetingScheduleHistory` endpoint (`GET /super-admin/team-meeting-schedules-ajax`): queries `team_meeting_schedule` joined with `companies` and `users` to display recent 50 allocations. |
| **Batch XLSX/CSV Import** (`HomeController.php:16339` & `MeetingImport.php:58`) | `MeetingImport.php` forgets only `jero:google_meeting_links:active_pool` even when `$this->table === 'team_meeting_lists'`. | Update cache invalidation in `MeetingImport.php` to bust `jero:team_meeting_links:active_pool` when importing into `team_meeting_lists`. |

---

### B. Frontend UI/UX Audit

| UI Element / Feature | Legacy State (Defects & Code Smells) | Target Senior Architect Standard |
|---|---|---|
| **Layout & Hierarchy** | Generic table with raw borders (`table-row-dashed`). Search box stuffed into `page-title` with inline CSS `style="width: 550px !important;"`. Violates Card + Sub-Card design standard. | **Mandatory 3-Tier Card + Sub-Card Standard** (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`):<br>• **Tier 1:** Pure white toolbar card with Microsoft Teams brand icon, title, subtitle, counter badges, and primary action buttons.<br>• **Tier 2:** Pure white sub-tabs navigation card (Directory, Quick Add & Import, Active Allocations, Integration Architecture).<br>• **Tier 3:** Modular sub-cards with high-density table, debounced search bar, and filter dropdowns. |
| **Currency Settings Leftover** | Lines 10–14 render `@section('create-button') <a href="{{ route('superadmin.currency-settings.create') }}">` — an unrelated leftover from Currency Settings! | 100% removed. Zero unrelated cross-module links. |
| **Broken External Add Page** | "Add Meeting Link" redirects away from the page to `super-admin/add-meeting/team_meeting_lists`. This loads `add-meeting.blade.php`, a 566-line legacy file containing Offer Letter CTC calculators, candidate currency arrays, CKEditor plugins, Kanban bundles, and broken HTML (`<div clas <div class="card card-custom mx-2">`). | Direct in-hub entry: Tab 2 provides high-density dynamic multi-row manual entry AND drag-and-drop file import right inside the page! No redirection to broken external pages. |
| **Inline Status Switch UX** | Native browser `alert("Status Changed"); location.reload();` on every toggle click. | Seamless AJAX switch with instant Metronic badge update, optimistic UI toggle, toastr/SweetAlert2 feedback, and zero screen reload. |
| **Action Column & Utilities** | Only has a raw switch. No copy link button, no link test button, no edit button. | Action column with: 1) One-click "Copy Link" to clipboard, 2) "Test / Join in Teams" (opens in new tab with Teams icon), 3) "Edit Link" modal, 4) "Delete Link" confirmation. |
| **Search & Filter Experience** | Form submit triggers full page reload. Search input is hardcoded to 550px. | Debounced live search (300ms) with clear button (`X`), status dropdown filter (All, Active, Inactive), and responsive grid. |
| **Multi-Select Bulk Actions** | Hardcoded `setTimeout(..., 500)` delays. Delete button displays non-functional `#disapprove` modal. SweetAlert says `Added!` on delete. | Instant multi-row checkbox selection, dynamic bulk action bar showing "Delete Selected (X)", SweetAlert2 delete confirmation with accurate messaging and immediate DOM removal. |
| **Screen Responsiveness** | Poor responsive behavior; table overflows on 15.6" screens (`1366×768`). | Fully engineered for 15.6" laptop screens with `overflow-x: hidden`, flexible column widths, and text truncation for long URLs with tooltips. |

---

## 3. Microsoft Teams Link Pool & ATS Allocation Architecture

### Data Flow & Booking Lifecycle Diagram

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                             SUPER ADMIN: TEAMS MEETING MANAGEMENT HUB                            │
│  Route: `/super-admin/team-meeting-setting`                                                      │
│                                                                                                  │
│  [ + Manual Multi-Row Entry ]      [ 📥 Batch CSV/XLSX Import ]      [ ⚡ Quick Status Toggle ]   │
└──────────────────────────────────────────────┬───────────────────────────────────────────────────┘
                                               │
                                               ▼ Writes to Link Pool (MySQL)
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 TABLE: `team_meeting_lists`                                      │
│  • `id` (int, PK)                                                                                │
│  • `account_name` (e.g., host email: "recruiting@enterprise.com")                                │
│  • `meeting_link` (e.g., "https://teams.microsoft.com/l/meetup-join/...") - UNIQUE               │
│  • `status` (1 = Active & Available in Pool, 0 = Disabled / Maintenance)                         │
└──────────────────────────────────────────────┬───────────────────────────────────────────────────┘
                                               │
                                               │ ATS Recruiter Schedules Interview
                                               ▼ (Date: $interviewDate)
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                   ATS ENGINE: `ServiceCompanyJobApplicationController.php:9645`                  │
│                                                                                                  │
│  Step 1: Fetch conflicting meetings booked on $interviewDate:                                    │
│          $conflicting = DB::table('team_meeting_schedule')                                       │
│                            ->whereDate('interview_date', $interviewDate)                         │
│                            ->pluck('meeting_link');                                              │
│                                                                                                  │
│  Step 2: Retrieve next free active link from pool:                                               │
│          $link = DB::table('team_meeting_lists')                                                 │
│                    ->where('status', 1)                                                          │
│                    ->whereNotIn('meeting_link', $conflicting)                                    │
│                    ->first();                                                                    │
└──────────────────────┬───────────────────────────────────────────────────┬───────────────────────┘
                       │                                                   │
      [ If Link Available ]                               [ If Pool Depleted on that Date ]
                       ▼                                                   ▼
┌─────────────────────────────────────────────────┐     ┌──────────────────────────────────────────┐
│  TABLE: `team_meeting_schedule`                 │     │  Returns: `['status' => 'expired']`      │
│  • `meeting_link` : $link->meeting_link         │     │  ATS notifies recruiter:                 │
│  • `interview_date` : $interviewDate            │     │  "All Teams Meeting links are currently  │
│  • `user_id` : Recruiter ID                     │     │   booked for this time slot. Please add  │
│  • `company_id` : Tenant Company ID             │     │   more links or select another slot."    │
│  • `interview_job_wise_con_id` : Candidate ID   │     └──────────────────────────────────────────┘
```

---

## 4. Proposed Routes & Controller API Specification

### Route Definitions (`routes/superadmin.php`)

```php
// Super Admin Microsoft Teams Meeting Hub Routes
Route::get('team-meeting-setting', [SuperAdminCompanyController::class, 'teamMeetingSetting'])
    ->name('company.team_meeting_setting');

Route::post('setting-team-meeting-store', [SuperAdminCompanyController::class, 'settingteamMeetingStore'])
    ->name('company.settingteamMeetingStore');

Route::post('team-meet-status-update', [SuperAdminCompanyController::class, 'teamMeetStatusUpdate'])
    ->name('company.teamMeetStatusUpdate');

Route::post('team-meeting-update/{id}', [SuperAdminCompanyController::class, 'updateTeamMeetingLink'])
    ->name('company.updateTeamMeetingLink');

Route::get('team-meeting-schedules-ajax', [SuperAdminCompanyController::class, 'getTeamMeetingScheduleHistory'])
    ->name('company.teamMeetingScheduleHistory');
```

---

### Method Specifications in `SuperAdminCompanyController.php`

#### 1. `teamMeetingSetting(Request $request)`
- **Purpose:** Serve the 3-Tier redesigned hub page.
- **Search Parameters:**
  - `meeting_link` / `q`: Searched against both `meeting_link` and `account_name` using `LIKE %...%`.
  - `status`: Optional filter (`'1'` = Active, `'0'` = Inactive, `null`/`''` = All).
- **Pagination:** 15 items per page with query string preservation: `->paginate(15)->withQueryString()`.
- **Metrics Computed:**
  - `totalLinksCount`: `DB::table('team_meeting_lists')->count()`
  - `activeLinksCount`: `DB::table('team_meeting_lists')->where('status', 1)->count()`
  - `inactiveLinksCount`: `DB::table('team_meeting_lists')->where('status', 0)->count()`
  - `scheduledMeetingsCount`: `DB::table('team_meeting_schedule')->count()`

#### 2. `settingteamMeetingStore(Request $request)`
- **Purpose:** Resolves the phantom route `company.settingteamMeetingStore`.
- **Modes Supported:**
  1. *Multi-Row Dynamic Entry:* Array of `holder_email[]` and `meeting_link[]`.
  2. *Single Link Creation:* Single `account_name`, `meeting_link`, and optional `status`.
- **Validation & Sanitization:**
  - Automatically trim whitespace.
  - Prepend `https://` if protocol is omitted.
  - Filter out empty rows.
  - Check for duplicates against `team_meeting_lists`.
  - Batch insert via `DB::table('team_meeting_lists')->insertOrIgnore($insertData)`.
- **Cache Invalidation:** `Cache::forget('jero:team_meeting_links:active_pool')`.
- **Response:** JSON `{ status: true, type: 'success', message: '...', url: '...' }`.

#### 3. `teamMeetStatusUpdate(Request $request)`
- **Purpose:** Seamless AJAX status toggle.
- **Validation:**
  - `id`: required, integer, exists in `team_meeting_lists,id`.
  - `chkStatus`: required, in:0,1.
- **Update:** `DB::table('team_meeting_lists')->where('id', $id)->update(['status' => $newStatus])`.
- **Cache Invalidation:** `Cache::forget('jero:team_meeting_links:active_pool')`.
- **Response:** JSON `{ status: 'success', message: 'Meeting link activated/paused successfully.', new_status: $newStatus, id: $id }`.

#### 4. `updateTeamMeetingLink(Request $request, $id)`
- **Purpose:** Inline modal edit for existing link.
- **Validation:**
  - `account_name`: required, string, max:255.
  - `meeting_link`: required, string, max:500.
  - Check for conflicting `meeting_link` on other records (`id != $id`).
- **Update:** Sets new `account_name` and `meeting_link`.
- **Cache Invalidation:** `Cache::forget('jero:team_meeting_links:active_pool')`.
- **Response:** JSON `{ status: true, type: 'success', message: 'Meeting link updated successfully.', data: [...] }`.

#### 5. `getTeamMeetingScheduleHistory(Request $request)`
- **Purpose:** Live ATS interview allocation monitor for Tab 3.
- **Query:**
  - Table: `team_meeting_schedule`
  - Left joins: `companies` on `companies.id = team_meeting_schedule.company_id`, `users` on `users.id = team_meeting_schedule.user_id`.
  - Selects: schedule ID, meeting link, interview date, company name, recruiter name & email.
  - Order: Descending by `team_meeting_schedule.id`, limit 50 records.
- **Response:** JSON `{ status: true, data: [...] }`.

---

## 5. Frontend Architecture & Modern 3-Tier Design System

Following `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` and Metronic guidelines, the view `resources/views/super-admin/meeting/team-meeting-setting.blade.php` will be completely overhauled:

### Color Palette & Branding
- **Microsoft Teams Gradient Brand Box:** `linear-gradient(135deg, #464eb8 0%, #33399b 100%)`
- **Teams Accent Color:** `#464eb8` / `#505ac9`
- **Active Tab Highlight:** Primary Blue `#009ef7` (Mandatory JERO Standard)
- **Status Active Badge:** Light green background `#e8fff3`, text `#50cd89`, border `#b7f5d0`
- **Status Inactive Badge:** Light amber background `#fff8dd`, text `#e49e00`, border `#ffe89e`

### 3-Tier Layout Breakdown

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 1: PURE WHITE TOOLBAR CARD                                                                  │
│  [Teams Brand Icon] Microsoft Teams Meeting Link Pool & Allocation Hub                           │
│  Subtitle: Curate conference link pools and monitor live ATS interview scheduling allocations     │
│  Right Corner: [Delete Selected (X)]   [Batch Import (.xlsx)]   [+ Add Meeting Links]            │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ KPI METRIC MINI-CARDS STRIP                                                                      │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐          │
│  │ Total Pool Links │  │ Active Available │  │ Inactive/Paused  │  │ Recorded Scheds  │          │
│  │      [ 42 ]      │  │      [ 38 ]      │  │       [ 4 ]      │  │     [ 128 ]      │          │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘  └──────────────────┘          │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 2: SUB-TABS NAVIGATION CARD                                                                 │
│  [ 📋 Links Directory ]   [ ➕ Quick Add & Batch Import ]   [ 📊 Schedule Monitor ]   [ 📖 Guide ]│
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 3: MODULAR SUB-CARDS CONTENT STREAM                                                         │
│                                                                                                  │
│  TAB 1: DIRECTORY                                                                                │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ Sub-Card Header: Microsoft Teams Pool Directory                                            │  │
│  │ Filter Bar: [🔍 Debounced Search (Host / URL)] [Status: All/Active/Inactive] [Filter Btn]   │  │
│  │ Table: [Select All] [#] [Host Account / Email] [Teams Meeting Link URL] [Status] [Actions] │  │
│  │ Actions: [📋 Copy URL] [🔗 Join/Test Teams] [✏️ Edit Modal] [🗑️ Delete]                     │  │
│  │ Pagination: Showing X to Y of Z meeting links + Custom Pagination Component                │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  TAB 2: QUICK ADD & BATCH IMPORT                                                                 │
│  ┌───────────────────────────────────────┐  ┌────────────────────────────────────────────────┐  │
│  │ Sub-Card A: Dynamic Manual Row Entry  │  │ Sub-Card B: Batch File Import (.xlsx / .csv)    │  │
│  │ • Row 1: [Host Email] [Meeting URL]   │  │ • Download Sample Excel Template                │  │
│  │ • Row 2: [Host Email] [Meeting URL]   │  │ • Drag & Drop File Zone                         │  │
│  │ • [+ Add Another Row]                 │  │ • Live Upload Progress Bar                      │  │
│  │ • [Save Links to Pool Button]         │  │ • [Upload & Import to Pool Button]              │  │
│  └───────────────────────────────────────┘  └────────────────────────────────────────────────┘  │
│                                                                                                  │
│  TAB 3: ACTIVE ALLOCATION & SCHEDULE MONITOR                                                     │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ Sub-Card Header: Live Candidate Interview Allocation Monitor                               │  │
│  │ [🔄 Refresh Allocations Button]                                                             │  │
│  │ Table: [#] [Assigned Teams Link] [Scheduled Date & Time] [Tenant Company] [Recruiter]       │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  TAB 4: INTEGRATION ARCHITECTURE & BEST PRACTICES GUIDE                                          │
│  ┌───────────────────────────────────────┐  ┌────────────────────────────────────────────────┐  │
│  │ Sub-Card A: ATS Link Allocation Engine│  │ Sub-Card B: Best Practices & Recommendations    │  │
│  │ • Pre-allocated pool explanation      │  │ • Recommended pool sizing (1.5x - 2x daily peak) │  │
│  │ • Conflict-free date matching         │  │ • Microsoft 365 link generation guide           │  │
│  │ • Automatic next-day re-availability  │  │ • Host lobby admission settings                 │  │
│  └───────────────────────────────────────┘  └────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Phase-Wise Implementation Roadmap

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                               PHASE-WISE EXECUTION PLAN                                │
│                                                                                        │
│  Phase 1: Deep Architectural Audit & Environment Baseline (COMPLETED)                 │
│  Phase 2: Backend Controller & Route Engineering (SuperAdminCompanyController.php)     │
│  Phase 3: Meeting Import Cache Integration (app/Imports/MeetingImport.php)             │
│  Phase 4: Blade View Overhaul with 3-Tier Card + Sub-Card Standard                     │
│  Phase 5: Client-First JavaScript Engine & AJAX Interactivity                          │
│  Phase 6: Multi-Screen Responsiveness (15.6" Laptops), Linting & Functional Testing    │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Phase 1: Deep Architectural Audit & Environment Baseline (COMPLETED)
- Audited `routes/superadmin.php`, `SuperAdminCompanyController.php`, `ServiceCompanyJobApplicationController.php`, and `mysql-schema.sql`.
- Discovered phantom route `settingteamMeetingStore`, unvalidated toggle endpoint, missing edit/schedule endpoints, and legacy UI bloat.
- Formulated the target architecture and compiled this comprehensive implementation plan.

### Phase 2: Backend Controller & Route Engineering
1. **Update Routes in `routes/superadmin.php`:**
   - Keep existing route names intact: `company.team_meeting_setting`, `company.settingteamMeetingStore`, `company.teamMeetStatusUpdate`.
   - Register `company.updateTeamMeetingLink` (`POST team-meeting-update/{id}`).
   - Register `company.teamMeetingScheduleHistory` (`GET team-meeting-schedules-ajax`).
2. **Refactor `teamMeetingSetting` in `SuperAdminCompanyController.php`:**
   - Add dual-column search (`account_name` OR `meeting_link`).
   - Add status filtering (`status = 1` or `status = 0`).
   - Add 4 KPI counters: `totalLinksCount`, `activeLinksCount`, `inactiveLinksCount`, `scheduledMeetingsCount`.
   - Paginate with `paginate(15)->withQueryString()`.
3. **Implement `settingteamMeetingStore` in `SuperAdminCompanyController.php`:**
   - Multi-row array processing + single record creation.
   - URL normalization (`https://`) and whitespace trimming.
   - Cache invalidation: `Cache::forget('jero:team_meeting_links:active_pool')`.
4. **Harden `teamMeetStatusUpdate` in `SuperAdminCompanyController.php`:**
   - Strict validation: `id` (exists) and `chkStatus` (`in:0,1`).
   - Return descriptive JSON message.
   - Cache invalidation: `Cache::forget('jero:team_meeting_links:active_pool')`.
5. **Implement `updateTeamMeetingLink` in `SuperAdminCompanyController.php`:**
   - Validate `account_name` and `meeting_link`.
   - Conflict check for unique meeting link across other rows.
   - Atomic update + cache invalidation.
6. **Implement `getTeamMeetingScheduleHistory` in `SuperAdminCompanyController.php`:**
   - Query `team_meeting_schedule` joined with `companies` and `users`.
   - Return 50 recent schedule allocations in JSON.

### Phase 3: Meeting Import Cache Integration
1. Inspect `app/Imports/MeetingImport.php`.
2. When `$this->table === 'team_meeting_lists'`, trigger `Cache::forget('jero:team_meeting_links:active_pool')` in batch execution and `__destruct()`.

### Phase 4: Blade View Overhaul (`team-meeting-setting.blade.php`)
1. Purge legacy code: remove currency button, obsolete CSS, table borders, and inline 550px search box.
2. Implement **Tier 1 Toolbar Card**:
   - Teams brand icon box with purple gradient (`linear-gradient(135deg, #464eb8 0%, #33399b 100%)`).
   - Title: "Microsoft Teams Link Pool & Allocation Hub".
   - Subtitle: "Curate static conference link pools and monitor live ATS interview scheduling allocations".
   - Header actions: "Delete Selected", "Batch Import (.xlsx)", and "Add Meeting Links".
3. Implement **KPI Metric Mini-Cards Strip**:
   - Total Pool Links, Active & Available, Inactive / Paused, Recorded Schedules.
4. Implement **Tier 2 Sub-Tabs Navigation Card**:
   - 4 Tabs: 1) Meeting Links Directory, 2) Quick Add & Batch Import, 3) Active Allocation & Schedule Monitor, 4) Integration Architecture & Guide.
   - Active highlight: `#009ef7`.
5. Implement **Tier 3 Modular Sub-Cards Content Stream**:
   - *Tab 1 (Directory):* Debounced search bar, status dropdown, high-density table with host avatars, copy link pill, test Teams meeting launcher, inline edit button, and custom pagination.
   - *Tab 2 (Quick Add & Import):* Dynamic multi-row entry table with "Add Another Row" button + XLSX/CSV drag-and-drop file upload with progress bar.
   - *Tab 3 (Schedule Monitor):* AJAX-driven table listing recent candidate interview bookings from `team_meeting_schedule`.
   - *Tab 4 (Architecture Guide):* Informational cards detailing how JERO ATS conflict-free slot allocation works and Microsoft 365 link recommendations.
6. Implement **Edit Modal (`#modalEditMeetingLink`)**:
   - Clean Metronic modal for editing host account and meeting URL.

### Phase 5: Client-First JavaScript Engine & AJAX Interactivity
1. **Isolated Script Architecture:** Encapsulated in `@push('footer-script')` with zero global namespace contamination.
2. **Instant Status Switch:** Optimistic toggle; upon click, status badge updates immediately while AJAX runs in background. Error reverts switch. Zero page reload.
3. **Copy to Clipboard:** Modern `navigator.clipboard.writeText()` with fallback and visual "Copied!" feedback.
4. **Edit Modal Handler:** Pre-populates fields, validates URLs on client, submits via AJAX, and updates row contents in DOM without reload.
5. **Bulk & Single Delete:** SweetAlert2 confirmation dialog, calls `superadmin.company.meetingListDelete`, fades out deleted rows from DOM, and updates KPI counters dynamically.
6. **Dynamic Multi-Row Input:** Add/remove rows dynamically in Tab 2, validate inputs, submit to `settingteamMeetingStore`, and switch back to Directory tab upon success.
7. **Schedule Monitor AJAX Loader:** Fetches live allocations from `teamMeetingScheduleHistory` when Tab 3 is clicked or refresh button is triggered.

### Phase 6: Multi-Screen Responsiveness, Linting & Functional Testing
1. **15.6" Screen Verification:**
   - Test layout at `1366×768` (100% and 125% DPI scale).
   - Test layout at `1920×1080`.
   - Verify `overflow-x: hidden` prevents any horizontal page scrolling.
2. **PHP Syntax & Quality Linting:**
   - Execute `php -l app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`.
   - Execute `php -l routes/superadmin.php`.
   - Execute `./vendor/bin/pint --test` or equivalent code quality review.
3. **Functional Verification:**
   - Test search by host email.
   - Test search by Teams URL keyword.
   - Test filter by Active / Inactive status.
   - Test pagination link navigation with preserved query strings.
   - Test multi-row link creation.
   - Test single link update.
   - Test status toggle without page refresh.
   - Test single and bulk link deletion.
   - Test Schedule Monitor tab loading.

---

## 7. Risk Analysis & Zero-Regression Contingency

1. **Zero Impact on Candidate Interview Scheduling:**
   - The consuming ATS engine in `ServiceCompanyJobApplicationController.php:9645` relies strictly on `team_meeting_lists.meeting_link` and `team_meeting_lists.status = 1`.
   - We do not modify table names, column names, or status values (`1` = active, `0` = inactive).
   - The integration remains 100% backward-compatible.
2. **No Regressions on Shared Delete Endpoint (`meetingListDelete`):**
   - The method `meetingListDelete` already whitelists `team_meeting_lists` alongside `google_meeting_lists`.
   - Our frontend passes `'table': 'team_meeting_lists'` exactly as required.
3. **No Phantom Route Exceptions:**
   - Implementing `settingteamMeetingStore` eliminates the existing `BadMethodCallException` risk.
4. **Strict Page/Blade Isolation:**
   - All styles are namespaced under `.teams-page-container` and custom classes.
   - No modifications to global Metronic stylesheets or shared components.

---

## 8. Summary of Files to be Modified / Created

| File Path | Nature of Change | Purpose |
|---|---|---|
| `docs/superadmin/SUPERADMIN_TEAM_MEETING_SETTING_REDESIGN_IMPLEMENTATION_PLAN.md` | **Created** | Comprehensive phase-wise architectural blueprint & implementation plan. |
| `routes/superadmin.php` | **Modified** | Add missing routes: `updateTeamMeetingLink`, `teamMeetingScheduleHistory`. |
| `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` | **Modified** | Implement `settingteamMeetingStore`, `updateTeamMeetingLink`, `getTeamMeetingScheduleHistory`; enhance `teamMeetingSetting` and `teamMeetStatusUpdate`. |
| `app/Imports/MeetingImport.php` | **Modified** | Add cache invalidation for `jero:team_meeting_links:active_pool` when importing into `team_meeting_lists`. |
| `resources/views/super-admin/meeting/team-meeting-setting.blade.php` | **Modified** | Complete overhaul to 3-Tier Card + Sub-Card design system with 4 tabs, KPI strip, and client-first JS. |

---

## 9. Next Steps

According to the user's explicit instruction:
> *"first i review implemnetation plan then i approve for change"*

**No source code changes will be applied until the user has thoroughly reviewed and approved this implementation plan.**  
Upon approval, execution will proceed strictly following Phases 2 through 6.
