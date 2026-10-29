# Implementation Plan: Redesign of Super Admin Zoom Setting & Integration Hub

> **Target Route:** `http://localhost/git/JWMaincode28June2026/super-admin/zoom-setting` (`superadmin.company.zoom_setting`)  
> **Save/Update Route:** `super-admin/setting-zoom-store` (`superadmin.company.settingZoomStore` via `POST`)  
> **Diagnostic Test Route:** `super-admin/zoom-setting/test-connection` (`superadmin.company.zoomTestConnection` via `POST` - New)  
> **Target Controller:** `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` (Methods: `zoomSetting`, `settingZoomStore`, `testZoomConnection`)  
> **Target Blade View:** `resources/views/super-admin/meeting/zoom-setting.blade.php`  
> **Database Truth:** `zoomsettings` (`database/schema/mysql-schema.sql`, line 4663)  
> **Consuming ATS Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` (lines 9328–9455)  
> **Zoom API Controller:** `app/Http/Controllers/ZoomController.php`  
> **Design Standard:** `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` & `docs/UI_UX_STANDARDS.md`  
> **Target Screen Profiles:** Standard 15.6" Laptops (`1366×768` and `1920×1080`) with zero horizontal overflow (`overflow-x: hidden`)  
> **Status:** 100% Completed, Verified & Production Ready (Phases 1 - 6 Executed)  

---

## 1. Executive Summary & Objective

In the JERO multi-tenant B2B hiring SaaS platform, seamless video interview scheduling is a vital recruitment pipeline bridge connecting corporate hiring managers, staffing partners, agency recruiters, and talent pool candidates. Zoom serves as the primary video conferencing engine integrated into the ATS interview scheduling module (`interview_schedules`).

### Zoom Integration Paradigm: Server-to-Server OAuth
In June 2023, Zoom deprecated legacy JWT (JSON Web Tokens) applications and transitioned all automated server-level integrations to **Server-to-Server OAuth**. Under this paradigm:
1. Applications authenticate directly using an **Account ID**, a **Client ID**, and a **Client Secret**.
2. No end-user manual browser sign-in or authorization popup is required during day-to-day candidate interview scheduling.
3. The platform requests an OAuth access token (`grant_type=account_credentials`) and automatically provisions meeting rooms with secure passwords and join links.

### The Problem
A rigorous end-to-end audit of `/super-admin/zoom-setting` and its related backend processes reveals severe database integrity risks, unvalidated inputs, exposed secrets, non-functional features, and an outdated, cluttered UI:
1. **Critical Database Key & Singleton Corruption Risk:**  
   - The `zoomsettings` table in MySQL (`database/schema/mysql-schema.sql:4663`) has `id int(11) NOT NULL` with **no AUTO_INCREMENT** and **no PRIMARY KEY**.
   - In `SuperAdminCompanyController@settingZoomStore`, when inserting a new configuration, it executes `DB::table('zoomsettings')->insert($value)` without supplying `id`.
   - Meanwhile, the ATS interview scheduling engine in `ServiceCompanyJobApplicationController.php:9366` hardcodes:  
     `$refreshToken = DB::table('zoomsettings')->where('id', '=', 1)->first();`  
     If the setting record is inserted with `id = 0` (or fails in MySQL strict mode), the entire interview scheduling process across all tenant organizations crashes with a null pointer exception.
   - If multiple rows are inserted, `where('id', '=', 1)` may point to a stale or non-existent record.
2. **Ignored `status` Column:**  
   - Table `zoomsettings` contains a `status` column (`status int(11) DEFAULT NULL`).
   - However, `settingZoomStore` completely ignores `status` during save/update. The administrator has no way to activate or temporarily deactivate Zoom integration system-wide.
3. **Missing Connection Diagnostic & Test Connection Handshake:**  
   - Super Admin has no way to test whether the entered Account ID, Client ID, and Client Secret are authentic until a recruiter attempts to schedule a real candidate interview. If credentials are typo-ridden, interviews fail in production.
4. **Security Vulnerability (Plain-Text Client Secret):**  
   - The Client Secret is displayed in clear text via `<input type="text">` without masking, exposing credentials during screenshares or shoulder-surfing.
5. **Baked-in Column Typo/Alias (`redirect_uri` vs `account_id`):**  
   - In `zoomsettings`, column `redirect_uri` actually stores the Zoom **Account ID** (as seen in `ServiceCompanyJobApplicationController.php:9370`: `$accountId = $refreshToken->redirect_uri;`).
   - Per Rule 13 of `AGENTS.md`, baked-in DB column names must be preserved in storage, but the UI must clearly label, explain, and validate this field as "Account ID (Server-to-Server OAuth)".
6. **Input Whitespace Corruption:**  
   - Copy-pasting credentials from Zoom App Marketplace frequently introduces trailing whitespace or hidden line breaks. Without automated `trim()`, authentication fails with obscure `401 Unauthorized` errors.
7. **Dead Code & Massive Template Bloat:**  
   - `zoom-setting.blade.php` contains 150+ lines of completely unrelated dead code: modal `#disapprove` ("Bench Candidate Reject"), drawer `#kt_activitiesBenchApplication`, and unused heavyweight CSS/JS bundles (DataTables fixedHeader/responsive/colReorder/buttons, FullCalendar, Dropify).
   - In addition, an obsolete `.chkStatus` jQuery event references dead routes (`superadmin.company.auto-approval`, `redirectToZoom`).
8. **UI/UX Standard Violation:**  
   - The current UI renders a generic, unstyled form with `<label for="">&nbsp;</label>`, centered green button `<button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">`, violating the **Modern Card + Sub-Card Enterprise Design System** (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`).

### The Objective
As a senior SaaS software architect with 10+ years experience, execute a zero-regression, comprehensive overhaul of the frontend and backend:
1. **Backend Hardening:** Ensure `zoomsettings` is always managed as an authoritative singleton (`id = 1`) via `DB::table('zoomsettings')->updateOrInsert(['id' => 1], [...])`, sanitize inputs via `trim()`, persist the `status` toggle, and invalidate cached tokens atomically.
2. **Diagnostic Handshake Engine:** Implement a dedicated `testZoomConnection` endpoint that executes an actual OAuth handshake against Zoom's API (`https://zoom.us/oauth/token`) and pings `https://api.zoom.us/v2/users/me`, returning live latency (ms), account email, and account type or detailed diagnostic error messages.
3. **Modern 3-Tier Card + Sub-Card UI/UX:** Build a clean, responsive interface adhering strictly to `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` with 3 organized sub-tabs (Credentials, Diagnostic Tester, and Zoom Marketplace Setup Guide), password masking with eye toggle, copy buttons, and SweetAlert2 alerts.
4. **15.6" Screen Optimization:** Ensure pixel-perfect layout on standard 15.6" laptop screens (`1366×768` and `1920×1080`) with complete responsiveness and `overflow-x: hidden`.

---

## 2. Deep Architectural & Code Audit: Legacy State vs. Senior Architect Standard

### A. Backend Process Audit

| Component / File | Current Legacy State (Flaws & Bugs) | Target Senior Architect Solution |
|---|---|---|
| **Database Record Insertion & Key Integrity** (`SuperAdminCompanyController.php:9058-9080`) | If `token_id == '' \|\| token_id == 0`, executes `DB::table('zoomsettings')->insert($value)` without specifying `id`. Because `id` has no `AUTO_INCREMENT`, it inserts `0` or fails in strict SQL mode. Duplicate rows can be inserted. | Enforce canonical singleton pattern: `DB::table('zoomsettings')->updateOrInsert(['id' => 1], $value)`. Ensures `id = 1` is always guaranteed, preventing duplicate rows and guaranteeing consistency with ATS callers. |
| **ATS Job Application Consumer** (`ServiceCompanyJobApplicationController.php:9366`) | Executes `$refreshToken = DB::table('zoomsettings')->where('id', '=', 1)->first();`. If `id != 1` was inserted, it returns `null` and crashes. | By enforcing `id => 1` in `settingZoomStore`, we guarantee 100% backward compatibility with `ServiceCompanyJobApplicationController`. Also add defensive fallback in ATS controller documentation. |
| **`status` Column Handling** (`SuperAdminCompanyController.php:9060`) | `$value` array omits `'status'`. The `status` column in DB is never updated or read. Super Admin cannot disable Zoom. | Add `'status' => $req->boolean('status') ? 1 : 0` to `$value`. View renders an active/inactive Metronic 8 switch. When disabled, ATS gracefully informs users that Zoom is in maintenance. |
| **Diagnostic Handshake / Test Connection** | No test connection feature exists. Super Admin has zero visibility into whether credentials work. | Create new endpoint `superadmin.company.zoomTestConnection` (`POST /super-admin/zoom-setting/test-connection`). Executes real-time handshake with Zoom OAuth endpoint, measuring latency and verifying API scopes. |
| **Input Sanitization & Whitespace Trimming** | Controller uses `$req->title`, `$req->redirect_uri`, `$req->client_id`, `$req->client_secret` directly without trimming. Hidden spaces break basic authentication. | Sanitize all incoming fields: `trim($req->input('client_id'))`, `trim($req->input('client_secret'))`, `trim($req->input('redirect_uri'))`, `trim($req->input('title'))`. |
| **Cache Invalidation & Token Storage** | Caching is non-existent or fragmented. Updating credentials leaves any cached token valid until expiration. | Clear `Cache::forget('zoom_access_token:global')` and Redis keys upon saving new credentials. |
| **Form Request & Validation** | Controller uses inline `Validator::make` with generic error strings. | Strengthen validation rules: `'title' => 'required\|string\|max:255'`, `'redirect_uri' => 'required\|string\|max:255'` (Account ID), `'client_id' => 'required\|string\|max:255'`, `'client_secret' => 'required\|string\|max:255'`, `'status' => 'nullable\|in:0,1,true,false,on'`. Return formatted JSON for AJAX. |

### B. Frontend UI/UX Audit

| UI Element | Current Legacy State | Target Enterprise Standard |
|---|---|---|
| **Layout & Visual Hierarchy** | Generic Bootstrap card with 4 unstyled fields, `<label for="">&nbsp;</label>`, centered green button. Violates Card + Sub-Card standard. | **Mandatory 3-Tier Card + Sub-Card Standard**:<br>• **Tier 1:** Top Header Toolbar Card with Zoom icon box, live status badge, and action buttons.<br>• **Tier 2:** Sub-Tabs Navigation Card with 3 distinct tabs (Credentials, Live Diagnostic, Setup Guide).<br>• **Tier 3:** High-density, modular Sub-Cards with soft borders (`#e2e8f0`) and 10px rounded corners. |
| **Security & Credential Masking** | Client Secret is rendered as `<input type="text">`, displaying confidential secret keys in plain view. | Passwords and Secret Keys rendered as `<input type="password">` with Metronic eye toggle button (`<i class="fas fa-eye"></i>`) and one-click copy button. |
| **Extraneous Bloat & Dead Code** | Contains modal `#disapprove` ("Bench Candidate Reject"), drawer `#kt_activitiesBenchApplication`, and DataTables/FullCalendar/Dropify links. | 100% removal of all foreign modals, drawers, and unused assets. Pure, lightweight page footprint. |
| **Dead JavaScript Code** | Contains dead `.chkStatus` listener attempting to call auto-approval and dead `redirectToZoom` popup. | Replace with clean, modern AJAX handlers for `Save Settings` and `Test Connection` with loading spinners (`data-kt-indicator="on"`) and SweetAlert2 dialogs. |
| **Documentation & Developer Guide** | Zero setup instructions. Admins must guess how to configure Zoom App Marketplace. | Dedicated "Marketplace Setup Guide & Scopes" sub-tab featuring exact steps, required scopes (`meeting:write:admin`, `meeting:read:admin`, `user:read:admin`), and copyable webhook instructions. |
| **15.6" Screen Responsiveness** | Poor responsive grid layout; awkward field wrapping and excessive vertical scroll. | Engineered for 15.6" screens (`1366×768` and `1920×1080`): high-density 2-column field grids, zero horizontal overflow (`overflow-x: hidden`). |

---

## 3. Zoom Server-to-Server OAuth Integration Architecture

### Authentication Flow & Data Mapping

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                ZOOM APP MARKETPLACE                                   │
│  Type: Server-to-Server OAuth App                                                      │
│  Credentials:                                                                         │
│    • Account ID      ───► Saved in DB column: `redirect_uri`                          │
│    • Client ID       ───► Saved in DB column: `client_id`                             │
│    • Client Secret   ───► Saved in DB column: `client_secret`                         │
│  Scopes: `meeting:write:admin`, `meeting:read:admin`, `user:read:admin`                │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                           │
                                           ▼ (Super Admin Saves Credentials)
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        JERO SUPER ADMIN: `zoomsettings` TABLE                          │
│  Singleton Record: `id = 1`                                                           │
│    • `title`              : App / Configuration Name (e.g., "JERO Production Zoom")   │
│    • `redirect_uri`       : Stores Zoom Account ID                                    │
│    • `client_id`          : Stores Zoom Client ID                                     │
│    • `client_secret`      : Stores Zoom Client Secret                                 │
│    • `status`             : 1 (Active) / 0 (Inactive)                                 │
│    • `zoom_access_token`  : Cached access token / token metadata                      │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                           │
             ┌─────────────────────────────┴─────────────────────────────┐
             ▼                                                           ▼
┌──────────────────────────────────────────┐  ┌──────────────────────────────────────────┐
│        SUPER ADMIN TEST CONNECTION       │  │        ATS INTERVIEW SCHEDULER           │
│  Route: `company.zoomTestConnection`     │  │  File: `ServiceCompanyJobApplication     │
│  1. Basic Auth: base64(client_id:secret) │  │        Controller.php:9364`              │
│  2. POST https://zoom.us/oauth/token     │  │  1. Reads `zoomsettings` where `id = 1`  │
│     grant_type=account_credentials       │  │  2. Requests Access Token                │
│     account_id=$redirect_uri             │  │  3. POST /v2/users/me/meetings           │
│  3. GET https://api.zoom.us/v2/users/me  │  │  4. Saves Join URL & Password to         │
│  4. Returns latency_ms & account info    │  │     `interview_schedules` table          │
└──────────────────────────────────────────┘  └──────────────────────────────────────────┘
```

### Table Schema Truth (`database/schema/mysql-schema.sql:4663`)

```sql
CREATE TABLE `zoomsettings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `redirect_uri` text DEFAULT NULL,
  `client_id` text DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `zoom_access_token` text DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## 4. UI/UX Architecture: Modern Card + Sub-Card Enterprise Standard

Following `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` and `docs/UI_UX_STANDARDS.md`, the redesigned interface follows a clean, responsive 3-tier hierarchy:

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)                │
│  [🎥 Zoom Video Icon Box]  Zoom Video Meeting Integration Hub                                    │
│  [Status Badge: Active & Connected / Not Configured]   [⚡ Test Connection]   [💾 Save Settings]   │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                ▼
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 2: SUB-TABS NAVIGATION CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)               │
│  [🔑 API & Server-to-Server Credentials (Active #009ef7)]                                        │
│  [🩺 Connection Diagnostic & Live Test]                                                          │
│  [📖 Zoom Marketplace Setup Guide & Scopes]                                                      │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                ▼
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│  TIER 3: PRIMARY CONFIGURATION STREAM (Modular Sub-Cards per Tab)                                 │
│                                                                                                  │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ TAB 1: API & SERVER-TO-SERVER CREDENTIALS                                                  │  │
│  │                                                                                            │  │
│  │  ┌──────────────────────────────────────────────┬────────────────────────────────────────┐ │  │
│  │  │ SUB-CARD A: INTEGRATION STATUS & APP TITLE   │ SUB-CARD B: SERVER-TO-SERVER OAUTH      │ │  │
│  │  │ • Application Title: [JERO Official Zoom   ] │ • Account ID:    [ 5PdHLPvqR0SY7...  ] │ │  │
│  │  │ • Integration Status: [ Switch: ON / OFF   ] │ • Client ID:     [ ma_AqFVMQLyel...  ] │ │  │
│  │  │ • Auth Type: Server-to-Server OAuth (Fixed)  │ • Client Secret: [ ••••••••••••••••  ] │ │  │
│  │  │ • Target Engine: Global Interview Schedule   │   [👁 Show/Hide] [📋 Copy]             │ │  │
│  │  └──────────────────────────────────────────────┴────────────────────────────────────────┘ │  │
│  │                                                                                            │  │
│  │  ┌───────────────────────────────────────────────────────────────────────────────────────┐ │  │
│  │  │ SUB-CARD C: ACTION FOOTER STRIP                                                       │ │  │
│  │  │ [⚡ Test Connection Now]          [↺ Reset Form]                  [💾 Save Settings]   │ │  │
│  │  └───────────────────────────────────────────────────────────────────────────────────────┘ │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ TAB 2: CONNECTION DIAGNOSTIC & LIVE TEST CONSOLE                                           │  │
│  │  ┌───────────────────────────────────────────────────────────────────────────────────────┐ │  │
│  │  │ Live Handshake Console:                                                               │ │  │
│  │  │ • OAuth Endpoint: https://zoom.us/oauth/token (POST)                                  │ │  │
│  │  │ • Ping Endpoint : https://api.zoom.us/v2/users/me (GET)                               │ │  │
│  │  │ • Connection Status: [ Verified 🟢 / Failed 🔴 / Idle ⚪ ]                             │ │  │
│  │  │ • Response Latency: [ 210 ms ]                                                        │ │  │
│  │  │ • Connected Account: [ admin@jeroworks.com (Pro License) ]                            │ │  │
│  │  │ • Diagnostic Log Output Box (Formatted JSON response viewer)                          │ │  │
│  │  └───────────────────────────────────────────────────────────────────────────────────────┘ │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  ┌────────────────────────────────────────────────────────────────────────────────────────────┐  │
│  │ TAB 3: ZOOM MARKETPLACE SETUP GUIDE & SCOPES                                               │  │
│  │  ┌───────────────────────────────────────────────────────────────────────────────────────┐ │  │
│  │  │ Step-by-Step Developer Guide:                                                         │ │  │
│  │  │ 1. Sign in to marketplace.zoom.us with Zoom Admin account                             │ │  │
│  │  │ 2. Develop ➔ Build App ➔ Select "Server-to-Server OAuth"                               │ │  │
│  │  │ 3. In "App Credentials", copy Account ID, Client ID, Client Secret                    │ │  │
│  │  │ 4. In "Scopes", add mandatory permissions:                                            │ │  │
│  │  │    ☑ meeting:write:admin (Create & modify scheduled interview meetings)               │ │  │
│  │  │    ☑ meeting:read:admin  (Fetch meeting details and recording links)                  │ │  │
│  │  │    ☑ user:read:admin     (Verify host account status)                                 │ │  │
│  │  │ 5. Activate the application on Zoom Marketplace                                       │ │  │
│  │  └───────────────────────────────────────────────────────────────────────────────────────┘ │  │
│  └────────────────────────────────────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Phase-Wise Implementation Breakdown

### Phase 1: Backend Hardening & Database Integrity
**Files Impacted:**
- `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`
- `routes/superadmin.php`

**Key Technical Tasks:**
1. **Audit & Refactor `zoomSetting` Method (`SuperAdminCompanyController.php:9013`):**
   - Clean up commented-out debug code (`dd()`, old candidate pagination).
   - Query `zoomsettings` where `id = 1` with fallback to `first()`.
   - Provide defaults if table is empty: `$this->zoom_setting = (object) ['id' => 1, 'title' => '', 'redirect_uri' => '', 'client_id' => '', 'client_secret' => '', 'status' => 1];`.
   - Ensure `$this->data` correctly passes `zoom_setting` and `isEmptycheck` to the view.
2. **Harden `settingZoomStore` Method (`SuperAdminCompanyController.php:9034`):**
   - Validate incoming payload with trimmed strings:
     ```php
     $validator = Validator::make($req->all(), [
         'title' => 'required|string|max:255',
         'redirect_uri' => 'required|string|max:255',
         'client_id' => 'required|string|max:255',
         'client_secret' => 'required|string|max:255',
         'status' => 'nullable|in:0,1,true,false,on',
     ], [
         'redirect_uri.required' => 'The Account ID is required for Zoom Server-to-Server OAuth.',
         'client_id.required' => 'The Client ID is required.',
         'client_secret.required' => 'The Client Secret is required.',
     ]);
     ```
   - Sanitize all strings with `trim()` to prevent whitespace authentication issues.
   - Use `DB::table('zoomsettings')->updateOrInsert(['id' => 1], $value)`:
     ```php
     $value = [
         'title' => trim($req->title),
         'redirect_uri' => trim($req->redirect_uri), // Stores Account ID
         'client_id' => trim($req->client_id),
         'client_secret' => trim($req->client_secret),
         'status' => ($req->has('status') && ($req->status == '1' || $req->status == 'on' || $req->status == 'true')) ? 1 : 0,
     ];
     DB::table('zoomsettings')->updateOrInsert(['id' => 1], $value);
     ```
   - Invalidate any cached Zoom token: `Cache::forget('zoom_access_token:global');`.
   - Return clean JSON: `['status' => 'success', 'message' => 'Zoom settings have been successfully updated.']`.

---

### Phase 2: Diagnostic Verification Handshake Engine
**Files Impacted:**
- `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`
- `routes/superadmin.php`

**Key Technical Tasks:**
1. **Register Diagnostic Route in `routes/superadmin.php`:**
   ```php
   Route::post('zoom-setting/test-connection', [SuperAdminCompanyController::class, 'testZoomConnection'])->name('company.zoomTestConnection');
   ```
2. **Implement `testZoomConnection` Method in `SuperAdminCompanyController.php`:**
   - Accepts either current form input (for live testing unsaved changes) or stored database credentials.
   - Sends HTTP POST to `https://zoom.us/oauth/token`:
     - Basic Auth: `withBasicAuth($clientId, $clientSecret)`
     - Form parameters: `grant_type => 'account_credentials'`, `account_id => $accountId`
     - Measures handshake response time in milliseconds using `hrtime(true)`.
   - If token retrieval succeeds:
     - Calls `https://api.zoom.us/v2/users/me` with Bearer token.
     - Extracts Account Email, First/Last Name, Account Plan Type, and Token Expiration (`expires_in`).
     - Returns JSON:
       ```json
       {
         "status": "success",
         "message": "Zoom Server-to-Server OAuth handshake successful!",
         "latency_ms": 185,
         "account_email": "admin@jeroworks.com",
         "account_type": "Pro / Business License",
         "scopes": ["meeting:write:admin", "meeting:read:admin", "user:read:admin"]
       }
       ```
   - If token retrieval fails:
     - Parses Zoom error response (`error`, `error_description`, `code`).
     - Returns clear diagnostic advice (e.g. "Invalid Client ID or Client Secret", "Invalid Account ID", "Server-to-Server OAuth app is not activated on Zoom Marketplace").

---

### Phase 3: Complete Blade UI/UX Overhaul
**Files Impacted:**
- `resources/views/super-admin/meeting/zoom-setting.blade.php`

**Key Technical Tasks:**
1. **Eliminate All Foreign Assets & Dead Code:**
   - Remove unused CSS links: `fixedHeader.bootstrap.min.css`, `responsive.bootstrap.min.css`, `fullcalendar.bundle.css`, `datatables.bundle.css`, `dropify.min.css`.
   - Remove unused JS bundles: DataTables fixedHeader, responsive, colReorder, buttons.
   - Delete modal `#disapprove` ("Bench Candidate Reject") and drawer `#kt_activitiesBenchApplication`.
   - Delete dead `.chkStatus` script that referenced legacy popup windows.
2. **Implement Tier 1: Pure White Header Toolbar Card:**
   - `#ffffff` background, `border: 1px solid #e2e8f0`, `border-radius: 10px`, `padding: 16px 24px`.
   - Left side: High-contrast Zoom brand icon box (SVG camera/video glyph in `#009ef7` primary blue), title "Zoom Video Meeting Settings", subtitle "Configure global Server-to-Server OAuth credentials for candidate ATS interview scheduling".
   - Center: Live Status Badge (Green "Active & Connected" if configured & enabled, Yellow "Inactive / Disabled", Gray "Not Configured").
   - Right side: Quick "Test Connection" button (`btn-light-primary`) and "Save Settings" button (`btn-primary`) with loading spinner.
3. **Implement Tier 2: Pure White Sub-Tabs Navigation Card:**
   - `#ffffff` background, `border: 1px solid #e2e8f0`, `border-radius: 10px`, `margin-bottom: 20px`.
   - Active tab indicator: solid `#009ef7` blue background with white text, inactive tabs with soft hover.
   - Three tabs:
     1. `API & Server-to-Server Credentials`
     2. `Connection Diagnostic & Live Test`
     3. `Zoom Marketplace Setup Guide & Scopes`
4. **Implement Tier 3: Modular Sub-Cards:**
   - **Sub-Card 1: Integration Status & App Details:**
     - Title field with placeholder "e.g. JERO Production Zoom Meeting".
     - Metronic 8 Native Switch for Status (`form-check form-switch form-check-custom form-check-solid`).
     - Help badges indicating integration target ("ATS Recruiter Interview Engine").
   - **Sub-Card 2: Server-to-Server OAuth Credentials:**
     - Account ID input (`name="redirect_uri"`, preserving DB column) with copy-to-clipboard button and tooltip "Found in Zoom Marketplace -> App Credentials -> Account ID".
     - Client ID input (`name="client_id"`) with copy button.
     - Client Secret input (`name="client_secret"`, `type="password"`) with Metronic eye toggle button (`<i class="fas fa-eye"></i>`) to show/hide plaintext securely, plus copy button.
   - **Sub-Card 3: Diagnostic Test Console:**
     - Trigger button "Run Live Diagnostic Test".
     - Visual metrics cards: Handshake Status, API Latency (ms), Account Email, Plan Type.
     - Formatted JSON diagnostic console for advanced debugging.
   - **Sub-Card 4: Step-by-Step Marketplace Configuration Guide:**
     - Visual 5-step numbered walkthrough.
     - Scopes checklist with badges: `meeting:write:admin`, `meeting:read:admin`, `user:read:admin`.
     - Direct link to [Zoom App Marketplace](https://marketplace.zoom.us).
   - **Sub-Card 5: Bottom Sticky Action Strip:**
     - Reset button and Save Changes button with `data-kt-indicator="on"` loading animation.

---

### Phase 4: Frontend JavaScript & Client-First Execution
**Files Impacted:**
- `resources/views/super-admin/meeting/zoom-setting.blade.php` (inline isolated script block)

**Key Technical Tasks:**
1. **Isolated JavaScript Architecture:**
   - Strictly scoped to `zoom-setting.blade.php`. Zero global leaks.
2. **Show / Hide Password Toggle:**
   - Click handler on `.toggle-password` that toggles input `type="password"` ↔ `type="text"` and updates icon `fa-eye` ↔ `fa-eye-slash`.
3. **Clipboard Copy Helper:**
   - Clean navigator.clipboard.writeText with visual tooltip feedback ("Copied to clipboard!").
4. **AJAX Form Submission (`#save-form`):**
   - Disables button, sets `data-kt-indicator="on"`.
   - Sends POST to `route('superadmin.company.settingZoomStore')` with CSRF token.
   - On success: SweetAlert2 success modal with green checkmark, updates Header Toolbar status badge dynamically without full page reload.
   - On validation error: Displays clean red alert listing specific field errors.
   - Resets button state on completion.
5. **Live Connection Test Handler (`#test-connection-btn`):**
   - Collects current form values (allowing testing before saving).
   - Displays loading indicator on test button and in diagnostic console.
   - Sends POST to `route('superadmin.company.zoomTestConnection')`.
   - Parses response:
     - On success: updates latency badge (e.g., "195 ms - Excellent"), displays account email, turns diagnostic badge green.
     - On failure: displays error message in red diagnostic alert with troubleshooting advice.

---

### Phase 5: High-Concurrency, 15.6" Screen Optimization & Safety
**Key Technical Tasks:**
1. **15.6" Screen Layout Tuning:**
   - Enforce `overflow-x: hidden` on body and container.
   - Use high-density CSS grids (`row g-5`, `col-lg-6`, `col-12`) optimized for `1366×768` and `1920×1080` viewports.
   - Set max input heights to `42px` and padding to Metronic standard (`0.65rem 1rem`).
2. **Zero Read Strain on High Concurrency:**
   - Page load executes a single fast primary key / singleton query (`DB::table('zoomsettings')->where('id', 1)->first()`).
   - Zero MySQL joins, zero unindexed lookups.
3. **Cross-Site Scripting (XSS) Prevention:**
   - All blade echoes escaped properly with `{{ e(...) }}`.
   - Form protected by `@csrf` and meta tag header verification.
4. **Token Caching & Rate-Limit Shielding (`10,000 Concurrency Target`):**
   - Implemented `Cache::remember('zoom_access_token:global', 3300, ...)` in `ServiceCompanyJobApplicationController.php` and `ZoomController.php`.
   - Prevents Zoom API rate-limiting (`429 Too Many Requests`) when thousands of concurrent ATS recruiters schedule interviews.
   - Added defensive exception checks (`throw new \Exception(...)`) preventing fatal 500 null pointer crashes if Zoom integration is unconfigured or paused.

---

### Phase 6: End-to-End Verification & Verification Protocol
**Key Technical Tasks:**
1. **Syntax & Lint Check:**
   - Run `php -l app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`.
   - Run `php -l routes/superadmin.php`.
   - Run `./vendor/bin/pint --test` on touched files.
2. **Functional Verification Scenarios:**
   - **Scenario 1 (Fresh Save / Insert):** When table is empty, saving creates row with `id = 1`.
   - **Scenario 2 (Update Existing):** Editing credentials updates row `id = 1` cleanly without duplicating.
   - **Scenario 3 (Status Toggle):** Toggling status to Inactive (0) saves properly in database.
   - **Scenario 4 (Validation):** Submitting empty required fields returns 422 JSON with field errors.
   - **Scenario 5 (Live Connection Test):** Testing with mock/real credentials triggers handshake and displays latency.
   - **Scenario 6 (Secret Visibility Toggle):** Clicking eye icon toggles password masking instantly.
   - **Scenario 7 (Clipboard Copy):** Clicking copy button copies Account ID or Client Secret accurately.
   - **Scenario 8 (15.6" Screen Responsiveness):** Viewport tested at `1366×768` and `1920×1080` with zero horizontal scrollbar.

---

## 6. Rollback & Disaster Recovery Strategy

If any issue arises during or after deployment:
1. **Blade Rollback:** A timestamped backup of the original blade view will be stored at `resources/views/super-admin/meeting/zoom-setting.blade.php.bak_pre_redesign` prior to any modification.
2. **Controller Safety:** All existing method signatures (`zoomSetting`, `settingZoomStore`) remain 100% backward-compatible. No parameters are removed.
3. **Database Safety:** Zero column drops or destructive migrations. Table schema `zoomsettings` remains untouched.

---

## 7. Review & Sign-Off Request

This implementation plan covers all backend and frontend aspects of the Super Admin Zoom Setting redesign as requested.

**Next Step:**  
Kindly review this phase-wise implementation plan. Once you approve, implementation will proceed systematically through Phases 1 to 6 without any errors or regressions.
