# AUTHORIZATION_GAPS.md

> Prioritized security & authorization gaps for JERO (2026-08-17). Analysis-only —
> nothing was fixed. Severity: CRITICAL / HIGH / MEDIUM / LOW.
> Each finding: Issue, Location, Current vs Expected behavior, Attack scenario,
> Affected actors/resources, Recommended solution (NOT implemented).

---

## CRITICAL

### C-1. Resume/document downloads have no ownership verification
- **Location:** `app/Http/Controllers/HomeController.php:797-819` (download flow),
  `app/Helper/start.php:34-37` (`asset_url` → public `file.jero.works` base),
  `JobApplication::getResumeUrlAttribute` (`documents/{id}/{hashname}` paths).
- **Current:** Authenticated user supplies a resume URL; controller strips the base,
  checks R2 existence, streams the file. No comparison of the document's owning
  `company_id` (or marketplace relation) to the requester.
- **Expected:** Resolve document → application → company; allow only same-company
  (or an established cross-tenant relation); serve via short-lived signed URL.
- **Attack:** Any tenant user enumerates `documents/{id}/{hash}` values (or scrapes
  IDs from responses) and downloads other companies' candidate resumes — PII breach
  (GDPR-class) across the entire marketplace.
- **Affected:** All tenant users → all candidates' resumes/documents.
- **Fix direction:** Ownership check + `Storage::temporaryUrl()` (or controller
  streaming after verify); add tests for cross-company 403.

### C-2. `Company` global scope silently no-ops for role-less users
- **Location:** `app/Models/Company.php:57-70` — scope applies only
  `if ($user && $user->roles->count() > 0)`.
- **Current:** Any authenticated user with zero roles queries `Company::…` and gets
  **all companies**. Login flows (SaasFront invite paths, legacy seeding) can produce
  role-less users.
- **Expected:** Scope for every authenticated non-superadmin; superadmin opt-out via
  explicit `withoutGlobalScope`, not via missing roles.
- **Attack:** Low-privilege/invited account lists companies (names, emails, package,
  sub_domain) → recon + tenant enumeration.
- **Affected:** All tenant users → `companies` table.
- **Fix direction:** Change guard to `if ($user && !$user->is_superadmin)`; add
  regression test with role-less user.

### C-3. Cross-tenant IDOR on unscoped models + raw queries (systemic)
- **Location:** Models without scopes (`Job`, `Client`, `BenchApplication`,
  `InterviewSchedule`, offers, etc.) + 5,375 raw `DB::table()` sites in controllers
  (6,170 app-wide) of which only ~870 mention `company_id`.
- **Current:** Routes take `{id}` and fetch rows without tenant predicate; several
  mutating endpoints (status updates, deletes, exports) then operate on them.
- **Expected:** Every tenant-owned read/write must be constrained by
  `company_id = auth()->user()->company_id` (or explicit marketplace relation).
- **Attack:** Company A user replays company B's entity IDs (harvestable from
  sequential IDs, ES responses, notifications) → read/edit/delete foreign pipelines.
- **Affected:** All tenant users → nearly all domain resources.
- **Fix direction:** Tenancy trait/global-scope rollout + IDOR sweep; enforce via
  ai-guards/PHPStan custom rule ("raw query on tenant table must filter company_id").

---

## HIGH

### H-1. Plaintext credential stores for impersonation
- **Location:** `users.super_password` (plaintext password copy — SEC-DEBT comments
  at `SaasFrontController.php:1073,1890,2953,3487`; written at
  `AttemptLoginAction::handleTeamInvite:139`), `users.super_admin_login_code`
  (numeric `companyId.rand(1111,9999)` — set at `AttemptLoginAction:140`,
  `SaasFrontController:1075,1892,2955,3486`, `SuperAdminCompanyController:321`).
- **Current:** Admins impersonate via login-with-code; the numeric code is weak and
  both columns leak credentials to anyone with DB/ES read (users index maps them —
  `HomeController:3563,3681`).
- **Expected:** No plaintext passwords; impersonation via audited, revocable,
  time-boxed mechanism with MFA-style confirmation.
- **Attack:** DB/ES exposure or superadmin code reuse → full account takeover of any
  tenant admin; numeric code brute-force is cheap (4 digits + company id).
- **Affected:** Platform admins, all tenant admin accounts.
- **Fix direction:** Drop `super_password`; replace login-code with signed
  impersonation tokens; purge codes from ES `_source`.

### H-2. Mutating endpoints without permission checks (UI-only authorization)
- **Location:** Pattern-wide. Examples: job sub-routes in `routes/service.php`
  (`jobs/updatestatus`, `jobs/priority`, `jobs/hiring_manager`, `jobs/recruiter`,
  `jobs/export`, `jobs/removeJob`…), many apptracker/bench AJAX endpoints,
  DataTables `*_ajax` routes. Views hide buttons via `$this->userPermissions`
  (`AdminBaseController:151-156`), but backend rarely re-checks.
- **Current:** Any authenticated company member (no permission) can invoke
  state-changing endpoints directly.
- **Expected:** Every mutating route gated by `Gate::denies()`/policy mirroring the
  UI permission.
- **Attack:** Junior recruiter curls `jobs/updatestatus` with foreign-permission
  semantics; privilege escalation inside tenant.
- **Affected:** All tenant users → jobs, applications, statuses, exports.
- **Fix direction:** Permission matrix rollout (PERMISSION_MATRIX.md §3 lists
  unchecked surfaces); add `abort_if` at controller entry.

### H-3. FormRequests never authorize
- **Location:** All ~55 classes in `app/Http/Requests/` — `authorize()` returns
  `true` unconditionally (StoreJob:23, StoreRole:16, UpdateJob:18, StoreCompany:16,
  StoreJobApplication:19, FrontJobApplication:21, ContactForm:16, StoreTeam,
  RegisterForm:16…).
- **Current:** Validation layer grants everything by design.
- **Expected:** `authorize()` embodies the resource permission, or is unused because
  the controller checks first (document that convention).
- **Attack:** Same as H-2 (defense-in-depth missing).
- **Fix direction:** Either wire real authorize() logic for new slices, or codify
  "controller checks, FormRequest validates" in the guide and stop treating
  authorize() as a control.

### H-4. `loginAsCompany` impersonation unaudited & unbounded
- **Location:** `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php:4723`.
- **Current:** Superadmin logs in as company's first admin (`frontAllAdmins()->first()`),
  no audit record, no expiry, no banner/flag beyond session `token_check` set later
  by super-code path only (this path doesn't even set it).
- **Expected:** Time-boxed, logged, one-click-revocable impersonation with visible
  indicator in UI.
- **Attack:** Insider/compromised platform admin acts as any tenant with no trail;
  hard to detect in incident response.
- **Affected:** All tenants.
- **Fix direction:** Impersonation table (who, whom, start/end, reason); enforce
  session flag + expiry; surface active impersonation.

---

## MEDIUM

### M-1. `Package` middleware is a no-op (alias `active-package`)
- **Location:** `app/Http/Middleware/Package.php:16-20` (empty handle), registered
  `bootstrap/app.php:54`.
- **Current/Expected:** Either enforce package/licence checks or remove the alias to
  avoid false confidence.
- **Risk:** Developers add `active-package` believing it gates subscriptions; it
  doesn't. Real gating is `custom-package` + login-time `working_status`.
- **Fix direction:** Implement or delete; document the real chain.

### M-2. Inconsistent device-login enforcement across personas
- **Location:** `routes/service.php` (has `EnsureOneDeviceLogin`), `routes/product.php`
  (missing), Admin persona via base-controller dead `user_token` remnants
  (`AdminBaseController:79-111` — code present but neutered).
- **Risk:** Concurrent sessions where enforcement assumed; stale sessions for
  suspended users in personas without middleware (epoch gate absent).
- **Fix direction:** Apply `EnsureOneDeviceLogin` uniformly (in-scope personas:
  service/admin); delete dead user_token block.

### M-3. Dual role systems & only-roles[0] semantics
- **Location:** Spatie `model_has_roles` + legacy `role_user` (User::role() maps to
  RoleUser); `AdminBaseController:153` reads `roles[0]` only.
- **Risk:** Grants via 2nd role silently ignored in permission loading while Spatie
  `hasRole/hasPermissionTo` (used elsewhere) honors all → divergent decisions by
  code path; two pivots drift.
- **Fix direction:** Migrate to Spatie-only; make loader iterate all roles or enforce
  single-role invariant at write time.

### M-4. Permission-name semantic drift / reuse
- **Location:** `*_skills` guards interview-process & customreports
  (`AdminInterviewProcessController:40-131`, `AdminCustomreportController:111+`);
  `add_job_applications` guards designations (`ServiceCompanyDesignationController:34`).
- **Risk:** Granting a benign-sounding permission unlocks unrelated modules;
  least-privilege impossible.
- **Fix direction:** Dedicated permission names per module; migrate grants.

### M-5. ES reads without central tenant filter
- **Location:** 60+ index services/controllers hand-write `term company_id`
  (e.g., `ElasticJobIndex.php:48+`, consumers in controllers).
- **Risk:** One forgotten filter = silent cross-tenant search results (ES is
  scope-unaware).
- **Fix direction:** Shared query-builder/default filter; guard rule.

### M-6. Public/unhashed file namespaces
- **Location:** `candidate-photos/{photo}`, `company-logo/{logo}`,
  `profile/{image}` via `asset_url()` on public base.
- **Risk:** Guessable URLs expose photos/logos independent of authorization.
- **Fix direction:** Hash filenames (resumes already use `hashname`); or move behind
  authenticated controller.

### M-7. Debug routes & live dd() remnants
- **Location:** `/mail-test`, `/redis-test`, `/r2test`, `/test-es`, `/whatsapp/*`
  (routes/web.php); `dd($cities)` live in `routes/service.php` `/search-cities`;
  `service.php:88` per project memory.
- **Risk:** Information disclosure / DoS primitives on production.
- **Fix direction:** Gate behind `app()->environment('local')` or remove.

### M-8. Stale security docs vs code (JobApplication scope)
- **Location:** docs/ARCHITECTURE.md & CLAUDE.md say scope commented out (BUG-03);
  `app/Models/JobApplication.php:24-31` shows it ACTIVE with superadmin bypass.
- **Risk:** Agents rely on stale docs; the new superadmin bypass itself differs from
  Company scope logic (inconsistent privilege carve-outs).
- **Fix direction:** Update docs; unify bypass semantics.

---

## LOW

### L-1. Dead multi-login token table & code (`user_token`, AdminBaseController).
### L-2. `hasRole('admin')` hard-coded gates (AdminCustomreportController:66,2497;
  AdminJobApplicationController:1272,1382,1560; AdminAdvancedSearchController:93,695)
  — any tenant naming a role "admin" triggers platform-logic branches.
### L-3. ~25 of 67 permissions never checked in code (dead weight; exact list needs
  DB dump — UNKNOWN).
### L-4. Cache-invalidate endpoint (`api/v2/.../cache/invalidate`) lets any company
  member flush company cache (annoyance vector only).
### L-5. Impersonation session flag (`token_check`) only set by super-code path —
  `loginAsCompany` sessions are indistinguishable from real user logins (ties into H-4).
### L-6. `users.active_session/session_id/session_time` columns legacy/unused.
### L-7. CSRF exemptions list includes wildcard patterns
  (`*/auth-custom-package/validatePackage`) — verify each still needs exemption.

---

## Consolidated Attack/Bypass Scenarios (cross-reference)

| Scenario | Chain | Findings |
|---|---|---|
| Mass resume harvesting | login (any tenant) → download endpoints, no ownership check | C-1 |
| Tenant enumeration | role-less invite account → Company::all() | C-2 |
| Pipeline sabotage | IDOR on unscoped job/application mutations | C-3, H-2 |
| Privilege escalation in-tenant | hidden-button endpoints callable directly | H-2, H-3 |
| Full admin takeover | numeric super_admin_login_code or ES-readable codes | H-1 |
| Untraceable impersonation | loginAsCompany no audit | H-4, L-5 |

---

## Remediation Order (recommendation only — NOT implemented)

1. **P0:** C-1 ownership checks + signed URLs (highest PII impact, smallest blast radius).
2. **P0:** C-2 scope guard fix (one-line class of bug, blocks tenant enumeration).
3. **P1:** C-3 tenancy trait rollout + raw-query guard (largest effort; stage by module,
   service_company first per PERSONA_SCOPE).
4. **P1:** H-1/H-4 impersonation rework (needs product sign-off — business logic rule).
5. **P1:** H-2/H-3 permission gate rollout using PERMISSION_MATRIX.md as checklist.
6. **P2:** M-1..M-8 in order listed.
7. **P3:** L-items during routine refactors.

> Execution is NOT approved — these are documented recommendations awaiting sign-off,
> consistent with the project's Track A plan
> (docs/V2_Zcode/Code Review and UI Audit by GLM/GOD_LEVEL_IMPLEMENTATION_PLAN.md).
