# PERMISSION_MATRIX.md

> Complete Actor → Resource → Action → Permission map for JERO (2026-08-17).
> Permissions are Spatie abilities (global `permissions` table). "—" = no permission
> check exists (access gated only by session auth + tenancy).
> Evidence key: SC*=ServiceCompany controllers, AD*=Admin controllers, SA*=SuperAdmin.

---

## 1. The Permission Catalog (all names found in code)

### Checked via `abort_if(Gate::denies(...))` (primary pattern, 40+ sites)

| Permission | Guarded actions (examples) |
|---|---|
| `view_job_applications` | CV tracker, bench candidates list, archive list, offers view (SC ApplicationArchiveController:37,44; ApplicationOfferController:28,119; BenchCandidatesController:148,394,604,2105,2535,4240,4269,4476,4798,4974,5238; CvTrackerV2Controller:45) |
| `add_job_applications` | Create bench candidate (BenchCandidatesController:1033); designations store (DesignationController:34) |
| `edit_job_applications` | Update bench candidate (BenchCandidatesController:1921) |
| `delete_job_applications` | Delete archived applications (ApplicationArchiveController:79) |
| `view_jobs` | Jobs list views (BenchCandidatesController:6043,6519; JobCategoryController etc.) |
| `add_jobs` | Store job (AdminJobsController:406) |
| `edit_jobs` | Update job |
| `view_company` | Company index/show (SC CompanyController:40,128) |
| `add_company` | Company create (SC CompanyController:50,56) |
| `edit_company` | Company update (SC CompanyController:84,92) |
| `delete_company` | Company destroy (SC CompanyController:120) |
| `view_clients` / `add_clients` / `edit_clients` | Client CRUD |
| `view_category` / `add_category` / `edit_category` / `delete_category` | Job categories (SC JobCategoryController:45,52…) |
| `view_skills` / `add_skills` / `edit_skills` / `delete_skills` | Skills + interview process (AdminInterviewProcessController:40–131) + legacy re-used for customreports (AdminCustomreportController:111,927,951,1363…) |
| `view_schedule` / `add_schedule` / `edit_schedule` | Interview schedules (InterviewScheduleController_old:61,780,1328) |
| `view_team` / `add_team` / `edit_team` | Team management |
| `view_locations` / `add_locations` / `edit_locations` / `delete_locations` | Locations |
| `view_question` / `add_question` / `edit_question` | Job questions |
| `view_report` | Reports (SC ReportController, customreports) |
| `view_joining` | Onboarding/joining views |
| `view_roles_permission` | Role & permission manager view (ManageRolePermissionController:43) |
| `add_roles_permission` | Create/update roles & permissions (ManageRolePermissionController:240,455) |
| `manage_settings` | Application settings (ApplicationSettingsController:32,66) |
| `edit_offer` | Offer edit (ApplicationOfferController:253) |

### Checked via `$this->user->can(...)` (legacy Admin pattern, in-action flags)

| Permission | Used for |
|---|---|
| `edit_client` / `delete_client` | Row-level action buttons in DataTables (AdminClientController:701,705; AdminCandidateStatusController:370,374; AdminCustomreportController:2200,2210; AdminConsultancyController:539,543) |
| `edit_skills` / `delete_skills` / `view_skills` / `add_skills` | Misc legacy admin CRUD (many commented-out sites remain) |
| `edit_settings` | Settings updates |
| `edit_schedule` / `view_schedule` / `add_schedule` | Schedules |
| `add_team` / `edit_team` | Team |
| `add_jobs` / `view_jobs` / `view_job_applications` / `view_report` / `add_roles_permission` | Overlap with Gate pattern above |
| `delete_client` | Client delete buttons |

### Roles hard-coded in code

| Check | Sites |
|---|---|
| `hasRole('admin')` | AdminCustomreportController:66,2497; AdminJobApplicationController:1272,1382,1560; AdminAdvancedSearchController:93,695; legacy CvTracker |
| `is_superadmin` | SuperAdmin middleware:21; Controller base:33; AdminBaseController:102; ResetPasswordController:203,222; SaasFrontController:84; global-scope bypass in JobApplication:27 |
| `root_user == 1` | 60+ sites (owner-only ops, package exemptions) |
| `$user->id === 1` | AttemptLoginAction:86 — user 1 always → superadmin dashboard |

---

## 2. Actor → Resource → Action Matrix

Actors: **SA**=Platform Super Admin (`is_superadmin`), **Owner**=tenant root user,
**Admin**=tenant user with role granting permission, **Member**=tenant user w/o
permission, **Public**=unauthenticated, **JobSeeker**=public applicant.

| Actor | Resource | Action | Permission required | Tenant required | Ownership | Additional conditions |
|---|---|---|---|---|---|---|
| Public | Front pages / jobs | view | — | n/a | n/a | public routes |
| JobSeeker | Front job application | apply | — (FormRequest authorize=true) | n/a | n/a | captcha |
| Public | Register (SaasFront) | create company+user | — | creates tenant | n/a | sets root_user=1 |
| SA | Super-admin panel (all) | * | `super-admin` middleware (`is_superadmin`) | no (cross-tenant) | n/a | login redirect if id=1 |
| SA | Company | impersonate (`loginAsCompany`) | super-admin middleware | no | n/a | logs in as company's first admin (SuperAdminCompanyController:4723) |
| SA | Companies list | view | super-admin | no | n/a | `withoutGlobalScope('company')` |
| Owner | Team members | manage | — | yes | company | root_user exempt from working_status |
| Owner | Roles/Permissions | view / add | `view_roles_permission` / `add_roles_permission` | yes | company | ManageRolePermissionController |
| Admin | Jobs | view / create / edit / delete | `view_jobs`/`add_jobs`/`edit_jobs` (+coin check on create) | yes | company | many job routes UNCHECKED |
| Admin | JobApplications (CV tracker) | view | `view_job_applications` | yes | company | global scope |
| Admin | Bench candidates | view / add / edit | `view_job_applications`/`add_job_applications`/`edit_job_applications` | yes | company | coin deduction on add (BenchCandidatesController:1147) |
| Admin | Offers | view / edit | `view_job_applications` / `edit_offer` | yes | company | — |
| Admin | Clients | CRUD | `view_clients`/`add_clients`/`edit_clients` (+can() row buttons) | yes | company | — |
| Admin | Categories | CRUD | `view_category`/`add_category`/`edit_category`/`delete_category` | yes | company | — |
| Admin | Skills | CRUD | `*_skills` | yes | company | — |
| Admin | Interview schedules | view / add / edit | `view_schedule`/`add_schedule`/`edit_schedule` | yes | company | Zoom config |
| Admin | Team | view / add / edit | `view_team`/`add_team`/`edit_team` | yes | company | coin deduction for invites |
| Admin | Locations | CRUD | `*_locations` | yes | company | — |
| Admin | Questions | CRUD | `*_question` | yes | company | — |
| Admin | Reports / Customreports | view | `view_report` (+hasRole('admin') gates some) | yes | company | — |
| Admin | Joining / onboarding | view | `view_joining` | yes | company | — |
| Admin | Settings | manage | `manage_settings` / `edit_settings` | yes | company | — |
| Member | Dashboard | view | — | yes | company | `custom-package` middleware: working_status must be 1 |
| Member (blocked) | Any custom-package route | — | — | — | — | redirected to package purchase screen |
| Any tenant user | Files (resume download) | download | — ⚠ **none** | ⚠ not verified | ⚠ not verified | HomeController:797–819 |
| Any tenant user | Resume upload (presign/store) | upload | session auth only | yes | company | throttled (`throttle:resume-presign`) |
| Any tenant user | Dashboard API v2 (kpis, leaderboards) | read | session auth | yes (channel check for push) | company | Redis cache |
| Marketplace | Recommendation relations | create/accept/reject | session auth + business rules | cross-tenant by design | from/to company | coin checks both sides |
| Marketplace | Bench requests | send/accept | session auth | cross-tenant by design | relation rows | coin deductions |
| System | Queued jobs | process | n/a (no re-auth) | explicit company_id param | n/a | dispatch-time trust |
| System | Scheduled commands (expiry, auto-close) | run | n/a | loop companies | n/a | console skips scopes |

---

## 3. Permission → Enforcement Map (where each is actually enforced)

| Permission | Enforced in (file:line) | Not enforced where it should be |
|---|---|---|
| `view_job_applications` | 15+ abort_if sites in SC controllers | Older Admin CV tracker routes rely on it partially; export/download endpoints unchecked |
| `add_job_applications` | BenchCandidatesController:1033 | Direct upload endpoints route-level only |
| `edit_job_applications` | BenchCandidatesController:1921 | Status-change AJAX endpoints (updatestatus) not gated |
| `delete_job_applications` | ApplicationArchiveController:79 | Other delete paths (force delete, bulk) unchecked |
| `view_jobs`/`add_jobs`/`edit_jobs` | scattered | Many `jobs/*` AJAX sub-routes (assign, priority, boostkeywords, export) have NO permission check |
| `*_company` | SC CompanyController | Admin persona company routes unchecked |
| `*_clients` | SC ClientController (partial) | ProductCompany dormant equivalents |
| `manage_settings` | ApplicationSettingsController | Other settings controllers (SMTP, theme) unchecked |
| `edit_offer` | ApplicationOfferController:253 | Offer creation/release routes |
| `view_roles_permission`/`add_roles_permission` | ManageRolePermissionController (both personas) | SuperAdmin role editing is middleware-gated instead |

---

## 4. Unused / Duplicated / Obsolete Permission Signals

- `permissions` table has ~67 rows; only ~42 distinct names are ever checked in
  code → **~25 permissions are dead weight** (exact list requires DB dump; UNKNOWN).
- `*_skills` permissions are re-used for unrelated modules (interview process,
  customreports) — duplicated semantics.
- `delete_jobs`, `delete_team`, `delete_question`, `delete_schedule`,
  `delete_company`(SC only) — checked in few or zero places (mixed CONFIRMED/INFERRED).
- Legacy `permission_role` + `permission_role.permission` relation coexists with
  Spatie `role_has_permissions` — two sources of truth for role→permission.
- `modules` table (`module_id` on permissions) — grouping only, no enforcement.

---

## 5. Role → Permission Matrix (runtime semantics)

Roles are per-company rows; grants live in Spatie pivots. Therefore the effective
matrix is **data-driven per tenant**, not code. Code-level truths:

1. Whatever `roles[0]` of the user carries is THE permission set used everywhere
   (`AdminBaseController:151-156` and the views' `userPermissions` array).
2. Super admin persona ignores permissions entirely (middleware `is_superadmin`).
3. `hasRole('admin')` sites grant/deny regardless of pivot contents — any tenant that
   names a role "admin" gets those gates.
4. `root_user=1` bypasses `custom-package`/`working_status` restrictions but NOT
   permission checks.
