# Team Member Post-Activation Status & Elasticsearch Sync Audit Report

**Date:** November 3, 2026  
**Module:** Authentication / Team Member Registration, Activation & Listing Sync  
**Compliance Standards:** Rule 1 (CQRS Writes), Rule 2 (CQRS 100% Reads from Elasticsearch), Rule 5 (Zero Stale Data in ATS), Rule 6 (Multi-Tenant Atomic Cache Invalidation), Rule 10 (MySQL <-> Elasticsearch Sync), Rule 21 (Audit Document Storage Pattern)

---

## 1. Executive Summary

### Issue Description
Users Kajal (`kajalverma5057@gmail.com`) and Snehil (`snehilgupta10m@gmail.com`) successfully confirmed their invitation, set up their credentials, and logged in to JERO. However, when navigating to the **Team Members listing page** (`/service_company/team`), both accounts continued to display:
- **Account Status:** `Awaiting Signup`
- **Actions:** `Resend Invite` button (instead of the active toggle switch)

---

## 2. Forensic Root Cause Analysis

### A. The Core CQRS Asynchrony & Omission
1. **MySQL Write Path:**
   When Kajal and Snehil completed activation via `RegistrationActivationService::executeActivationTransaction()`:
   - In MySQL `users` table: `status = 'active'`, `working_status = 1`, `password` was updated to the bcrypt hash (`$2y$...`), `email_verification_code` was set to `NULL`.
   - In MySQL `team_invitations` table: `status = 'ACTIVATED'`, `used_at = now()`.
   - This part of the database transaction executed properly.

2. **The Missing Elasticsearch Synchronization (Violation of Rule 10):**
   - The team listing page (`ServiceCompanyTeamController::data_ajax()`) adheres to CQRS Rule 2: it reads **100% of its data directly from Elasticsearch index `ELASTIC_USERS_LIST`**.
   - `RegistrationActivationService.php` **never invoked** `GeneralMethod::userIndexingupload($user->id)` or dispatched `SyncUserToElasticJob`.
   - As a result, the Elasticsearch documents for both users still contained the initial pre-activation invitation state:
     - `status`: `'inactive'`
     - `password`: `''` (empty)
     - `email_verification_code`: `'47N18pBvyVlULYo4z2k85R8na5melyd0cupNsQ7Y'`
   - Because `ELASTIC_USERS_LIST` was never synchronized upon activation, Elasticsearch continued returning `password = ''` and `status = 'inactive'`.

3. **Multi-Tenant Redis Cache Invalidation Omission (Violation of Rule 6):**
   - `RegistrationActivationService.php` did not increment `ats:teamversion:company_{$companyId}` or clear `ats:teamcounts:{$companyId}`.
   - Any recruiter or admin browsing the team list was served the cached Elasticsearch dataset or cached HTML payload.

4. **Rigid View Conditional Logic:**
   - In `resources/views/service_company/ajax_pages/team-list-ajax.blade.php`:
     ```blade
     @if (!empty($row->password))
         {{-- Active / Inactive switch --}}
     @else
         {{-- Awaiting Signup + Resend Invite --}}
     @endif
     ```
   - In `resources/views/service_company/team/index.blade.php`:
     ```javascript
     if (row.password) { ... } else { /* Awaiting Signup */ }
     ```
   - Both templates relied solely on `!empty($row->password)` without checking `status === 'active'`. When ES had an empty password field due to missed indexing, both views defaulted directly to the "Awaiting Signup" branch.

---

## 3. Architecture & Data Flow Fix

```
[User Clicks "Activate Account & Join Workspace"]
                       │
                       ▼
    [POST /register/activate (Atomic Transaction)]
                       │
    ┌──────────────────┴──────────────────┐
    ▼                                     ▼
[Update MySQL users table]     [Update team_invitations]
- status = 'active'            - status = 'ACTIVATED'
- password = bcrypt(...)       - used_at = now()
- working_status = 1
- email_verification_code = null
                       │
                       ▼
        [Tenant Cache Invalidation (Rule 6)]
        - Cache::forget('ats:teamcounts:{company_id}')
        - Cache::increment('ats:teamversion:company_{company_id}')
        - GeneralMethod::cacheClean('allteam_version_user_{user_id}')
                       │
                       ▼
        [DB::afterCommit Hook (Rule 10)]
        1. Synchronous ES Sync: GeneralMethod::userIndexingupload($user->id)
        2. Async Backup Job: SyncUserToElasticJob::dispatch($user->id)
        3. Real-Time Broadcast: broadcast(new TeamMemberUpdated(...))
                       │
                       ▼
      [Team Listing Page (CQRS Read from ES)]
      - ES document has: status='active', password='...', email_verification_code=null
      - View check: !empty($row->password) || $row->status === 'active'
      - Render: "Active" with active toggle switch (ZERO "Awaiting Signup")
```

---

## 4. File Changes Matrix

| File | Changes Made |
|---|---|
| `app/Services/RegistrationActivationService.php` | - Added `DB::afterCommit` hook to synchronously re-index user via `GeneralMethod::userIndexingupload($user->id)` and dispatch `SyncUserToElasticJob`.<br>- Invalidate tenant team version key `ats:teamversion:company_{$tenantId}` and counts cache `ats:teamcounts:{$tenantId}` upon activation.<br>- Broadcast `TeamMemberUpdated` event on company private channel. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | - Updated `data_ajax()`: `$isInvited` requires `status !== 'active'`.<br>- Updated `getTeamCounts()`: `invited_count` aggregation excludes `status = 'active'`. |
| `resources/views/service_company/ajax_pages/team-list-ajax.blade.php` | - Replaced rigid `empty($row->password)` with `(!empty($row->password) \|\| (isset($row->status) && strtolower($row->status) === 'active'))` in both rank computation and Account Status subcard. |
| `resources/views/service_company/team/index.blade.php` | - Replaced `!row.password` with `(!row.password && row.status !== 'active')` in client-side card builder.<br>- In subcard 4, checks `row.password \|\| (row.status && row.status.toLowerCase() === 'active')`. |
| `resources/views/auth/login.blade.php` | - Restored custom blue gradient background (`linear-gradient(180deg, #f0f6ff 0%, #f8faff 42%, #ffffff 100%)`) and blue top accent line (`4px`) on `.jw-card-team-invite` per User Request #7. |

---

## 5. Verification & Live Diagnostic Results

### 1. Live Data Sync for Kajal & Snehil
- Re-indexed Kajal (`id: 999139`) and Snehil (`id: 999131`) into `ELASTIC_USERS_LIST`.
- Verified Elasticsearch document state:
  - `User 999139 (kajal)`: `Status='active'`, `Password=SET`, `Code=NULL`
  - `User 999131 (snehil)`: `Status='active'`, `Password=SET`, `Code=NULL`
- Incremented Company 2 version key and verified team listing output:
  - `Kajal status in rendered HTML: Active`
  - `Snehil status in rendered HTML: Active`

### 2. Automated End-to-End Test Suite
Executed `scratch/test_activation_and_team_listing_sync.php`:
- **Step 1 (Pre-Activation):** Team listing rendered `Awaiting Signup` with Resend Invite button.
- **Step 2 (Activation POST /register/activate):** Status code `201 Created`.
- **Step 3 (Elasticsearch Verification):** `ELASTIC_USERS_LIST` document automatically updated to `status='active'`, password hash populated, verification code cleared.
- **Step 4 (Team Listing Post-Activation):** Output verified: `ZERO 'Awaiting Signup'`, immediately rendered as `Active` with iOS toggle switch.
- **Step 5 (Team Counters):** `Active` count incremented, `Invited` count decremented to 0.
