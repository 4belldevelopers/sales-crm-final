# Forensic Audit & Root Cause Analysis: Pending Invites Tab Counter Discrepancy & Member Status Parity

**Metadata:**
- **Date:** 2026-11-05
- **Module:** Service Company Team Management (`resources/views/service_company/team/index.blade.php`, `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`)
- **Author:** JERO Architecture & Engineering Team
- **Type:** Forensic Defect Audit & Permanent Resolution Report (Rule 21 Compliance)

---

## 1. Executive Summary

### The Issue
On the Team Members listing page for Service Company (`testing.jero.works/service_company/team`):
1. Clicking the **"Pending Invites"** tab (`data-tab="invited"`, subtitle *"Awaiting signup"*) displayed a tab badge counter showing **`0 Pending Invites`**.
2. However, the stream below rendered **`1 member`** (`Secondary Recruiter`, email `recruiter2_99992@example.com`).
3. Furthermore, within that single rendered card, the member's account status toggle was marked **`Active`** (toggle switch ON with green label), workload indicated **19 Active Jobs & 10 Captain Jobs**, and status badge read **`• Member`**.
4. This generated severe confusion:
   - A member marked **`Active`** was appearing inside the **`Pending Invites`** ("Awaiting signup") tab.
   - The KPI tab counter (`0`) contradicted the card list count (`1`).

---

## 2. Root Cause Analysis (Forensic Breakdown)

### Forensic Root Cause 1: Incomplete Invitation Predicate in Browser RAM Stream Filter
In `resources/views/service_company/team/index.blade.php`:
- At line 5536 (`updateLocalStatTabCounts`):
  ```javascript
  var isInvited = !r.password || status === 'invited';
  ```
- At line 5580 (`getDerivedRecords`):
  ```javascript
  var isInvited = !r.password || status === 'invited';
  ```
Both functions evaluated `isInvited` solely on whether `!r.password` was truthy, **without verifying whether `status !== 'active'`**.
Meanwhile, in `buildTeamMemberCardHtml()` (line 5719), the card renderer used:
```javascript
var isInvited = (!row.password && row.status !== 'active') || row.status === 'invited';
var isActive = (row.status === 'active');
```
Because `Secondary Recruiter` had `status = 'active'`, `buildTeamMemberCardHtml` correctly evaluated `isActive = true`, rendering the toggle as `Active`. But `getDerivedRecords()` (which lacked `status !== 'active'`) incorrectly returned `true` for `isInvited`, causing the user to be filtered into the "Pending Invites" tab stream!

### Forensic Root Cause 2: Asymmetric Predicates Between Aggregation and Query in Backend
In `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`:
1. In `getTeamCounts()`:
   The Elasticsearch `invited_count` aggregation explicitly excluded active members:
   ```php
   'must_not' => [
       ['term' => ['status' => 'active']],
   ]
   ```
   Hence, ES calculated `invited_count = 0`.
2. In `getTeamsServer()`:
   When `statusTab === 'invited'` was requested from the server, the query lacked `must_not: status: active`:
   ```php
   if ($statusTab === 'invited') {
       $must[] = [
           'bool' => [
               'should' => [
                   ['wildcard' => ['email_verification_code' => '?*']],
                   ['bool' => ['must_not' => [['wildcard' => ['password' => '?*']]]]],
               ],
               'minimum_should_match' => 1,
           ],
       ];
   }
   ```
   Thus, if server pagination occurred, `getTeamsServer` would return any active member lacking a password wildcard.
3. In `getTeamCounts()` MySQL fallback:
   Line 124 used:
   ```php
   SUM((email_verification_code IS NOT NULL AND email_verification_code != '') OR (password = '' OR password IS NULL)) as invited
   ```
   Which also lacked `status != 'active' AND`.

### Forensic Root Cause 3: Seed / Test User Password Anomaly
In `tests/Feature/HiringTeamDrawersGodLevelTest.php` (line 197), test user `99992` (`Secondary Recruiter`) was created with `status = 'active'`, assigned to 19 jobs and 10 captain positions, but **without setting a password hash** (`$recruiter2->password` was never populated, defaulting to empty string in MySQL). Because `password = ''`, the missing `status !== 'active'` checks triggered across the UI and server queries.

---

## 3. Architecture & Data Flow

```
+-----------------------------------------------------------------------------------------+
|                                    CANONICAL INVITATION FLOW                            |
+-----------------------------------------------------------------------------------------+
|                                                                                         |
| 1. Admin Invites Member                                                                 |
|    -> User created: status = 'inactive', working_status = 0, email_verification_code != '' |
|    -> team_invitations created: status = 'PENDING'                                     |
|                                                                                         |
| 2. Member Classification Invariant (Universal Rule)                                     |
|    -> isInvited = (status !== 'active') && (!password || status == 'invited' || code)    |
|    -> If status === 'active', user is AUTHORITATIVELY ACTIVE, never invited!           |
|                                                                                         |
| 3. Member Activates Registration (/register?token=...)                                 |
|    -> RegistrationActivationService sets: status = 'active', working_status = 1,        |
|       password = Hash::make(...), email_verification_code = null                        |
|    -> ES Document synced, Cache purged                                                  |
|                                                                                         |
| 4. Client-First Browser RAM Stream & Server Queries                                     |
|    -> KPI Counter: invited_count strictly excludes status: 'active'                     |
|    -> Client getDerivedRecords: strictly excludes status: 'active'                     |
|    -> Listing and tab badge match 100% with 0 discrepancies                             |
+-----------------------------------------------------------------------------------------+
```

---

## 4. File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `resources/views/service_company/team/index.blade.php` | - Updated `updateLocalStatTabCounts()` (line 5536): `isInvited` requires `status !== 'active'`.<br>- Updated `getDerivedRecords()` (line 5580): `isInvited` requires `status !== 'active'`.<br>- Updated `buildTeamMemberCardHtml()` (line 5719): aligned canonical definition. |
| `resources/views/service_company/ajax_pages/team-list-ajax.blade.php` | - Updated line 12: `$isInvited` strictly requires `strtolower($row->status ?? '') !== 'active'`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | - Updated `getTeamCounts()` ES aggregation: removed password wildcard constraint on active users; preserved `must_not: status: active` on invited.<br>- Updated `getTeamCounts()` MySQL fallback: added `status != 'active' AND` to invited expression and aligned active expression.<br>- Updated `getTeamsServer()`: added `must_not: status: active` to `$statusTab === 'invited'` bool query.<br>- Updated line 683: added `strtolower($u['status'] ?? '') !== 'active'` to in-memory filter. |
| `tests/Feature/HiringTeamDrawersGodLevelTest.php` | - Lines 202 & 263: added `$recruiter2->password = bcrypt('Password123!');` to guarantee all test users have valid password hashes. |

---

## 5. 15.6" Screen Fit & UI/UX Compliance

- **Card + Sub-Card Enterprise Design (Rule 20):**
  - KPI Tab Badge: Shows `0 Pending Invites` (with clean primary highlight on select).
  - Empty State Card: When tab is empty, renders standard enterprise Metronic card:
    - Icon: `fa-solid fa-user-group fa-3x`
    - Title: "No Team Members Found"
    - Subtitle: "No team members match the selected filter criteria."
- **Viewport Constraints (Rule 14):**
  - Tested across `1366×768` and `1920×1080` screen sizes.
  - Zero horizontal overflow.

---

## 6. Verification Evidence

The fix was verified using automated script `scratch/verify_pending_invites_fix.php`:

```
=== 1. VERIFYING getTeamCounts(2) ===
Total: 4
Active: 4
Inactive: 0
Invited: 0
=== 2. VERIFYING data_ajax for company 2 ===
Invited tab returned records count: 0
All tab returned records count: 4
Member: vibhanshu jaiswal | Status: active | isInvited: NO
Member: kajal | Status: active | isInvited: NO
Member: snehil | Status: active | isInvited: NO
Member: secondary recruiter | Status: active | isInvited: NO

>>> ALL VERIFICATION CHECKS PASSED PERFECTLY! <<<
```
1. `getTeamCounts(2)` returns `invited: 0`, `active: 4`, `total: 4`.
2. Requesting `statusTab = 'invited'` returns `0` records.
3. Requesting `statusTab = 'all'` returns `4` active members.
4. Active member `Secondary Recruiter` (ID 99992) is correctly categorized under `All Members` and `Active Members`, and excluded from `Pending Invites`.
