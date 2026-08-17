# ACTION_INVENTORY.md

> Catalog of meaningful actions and their authorization requirements (2026-08-17).
> "—" = no check found in code (session auth + tenancy only). ⚠ = risk flagged
> (see AUTHORIZATION_GAPS.md). Routes live in web.php (W), service.php (S),
> product.php (P, dormant persona), superadmin.php (SA), console (C).

---

## A. Authentication & Session Actions

| # | Action | Actor | Route/Entry | Controller/Class | Permission | Tenant scope | Notes / Risk |
|---|---|---|---|---|---|---|---|
| A1 | Login | anyone | POST /login (W) | `AttemptLoginAction` | — | — | captcha; status check; DeviceSessionManager; login-attempt log |
| A2 | Team-invite first login | invited user | POST /login (userId+companyid+toc) | `AttemptLoginAction::handleTeamInvite` | token params | update WHERE company_id+id | sets super_password plaintext ⚠ (H-1) |
| A3 | Super-code impersonation login | platform admin | POST /login (super_code) | `AttemptLoginAction::handleSuperCode` | `super_admin_login_code` match | — | numeric 4-digit code ⚠ (H-1) |
| A4 | Logout | auth user | POST /logout | `LogoutAction` | — | — | clears token_check for superadmin |
| A5 | Password reset | anyone | reset flow | `ResetPasswordController` | signed token | — | superadmin branch (line 203) |
| A6 | Register company (SaaS front) | public | SaasFront register | `SaasFrontController` | — | creates company + root_user=1 | writes super_admin_login_code ⚠ |
| A7 | Per-request session enforcement | auth user | every persona route | `EnsureOneDeviceLogin` | — | epoch keys user+company | status, epoch, single-device |

## B. Super Admin (platform) — all behind `auth` + `super-admin` (`is_superadmin`)

| # | Action | Route | Controller | Permission | Notes / Risk |
|---|---|---|---|---|---|
| B1 | Dashboard | SA super-admin/dashboard | SuperAdminDashboardController | middleware | — |
| B2 | Company CRUD/list | SA companies | SuperAdminCompanyController | middleware | `withoutGlobalScope('company')` (intended) |
| B3 | Impersonate company | SA loginAsCompany/{id} | SuperAdminCompanyController:4723 | middleware | no audit trail, no expiry ⚠ (H-4) |
| B4 | Set super_admin_login_code | SA user update | SuperAdminCompanyController:321 | middleware | numeric code write ⚠ (H-1) |
| B5 | Package CRUD / custom packages | SA packages* | Package/CustomPackageController | middleware | — |
| B6 | Global settings (SMTP, theme, footer, CMS, currency, payment, SMS) | SA resources | respective controllers | middleware | platform-wide impact |
| B7 | Invoice sync (Stripe/Razorpay) | SA invoices | InvoiceController | middleware | — |
| B8 | myContactPerson users list | SA | SuperAdminCompanyController | middleware | company filter optional from request (low risk behind super-admin) |

## C. Service Company — Jobs

| # | Action | Route (S, prefix `service_company/`) | Controller | Permission | Tenant | Ownership | Business rule | Risk |
|---|---|---|---|---|---|---|---|---|
| C1 | View jobs list/page | jobs index | ServiceCompanyJobsController | `view_jobs` (some views) | company_id filter | company | custom-package gate | many sub-routes unchecked ⚠ (H-2) |
| C2 | Jobs DataTable AJAX | jobs/data | same | — | query-built | company | — | ⚠ export leak if filter missed |
| C3 | Create job | jobs.store | same + `StoreJob` | `add_jobs` (Admin persona; SC partial) | yes | company | coin deduction | check inconsistency |
| C4 | Update job | jobs/update/{id} | same + `UpdateJob` | `edit_jobs` (partial) | yes | company | — | ⚠ some update routes bare |
| C5 | Change job status | jobs/updatestatus | same | — | yes | company | pipeline rules | ⚠ (H-2) |
| C6 | Assign recruiter / HM | jobs/recruiter, jobs/hiring_manager | same | — | yes | company | — | ⚠ |
| C7 | Job priority | jobs/priority/{id} | same | — | yes | company | — | ⚠ |
| C8 | Boost keywords | jobs/boostkeywords | same | — | yes | company | coins | ⚠ |
| C9 | Export jobs | jobs/export/{s}/{l} | same | — | yes | company | — | ⚠ bulk egress |
| C10 | Recommend / invite agencies | jobs/send_job_recommanded_request etc. | same | — | cross-tenant (marketplace) | relation rows | coins | — |

## D. Service Company — Candidates / Applications / Bench

| # | Action | Route | Controller | Permission | Tenant | Business | Risk |
|---|---|---|---|---|---|---|---|
| D1 | CV tracker view | cv tracker routes | ServiceCompanyCvTracker(V2)Controller | `view_job_applications` (V2:45) | model scope | — | V1 partial |
| D2 | App tracker view/list | apptracker/* | ServiceCompanyAppTrackerController | — mostly | yes | — | ⚠ (H-2) |
| D3 | Add bench candidate | bench store | ServiceCompanyBenchCandidatesController:1033 | `add_job_applications` | yes | coins (1147) | — |
| D4 | Edit bench candidate | bench update :1921 | same | `edit_job_applications` | yes | — | — |
| D5 | Bench list views | multiple :148–5238 | same | `view_job_applications` | yes | — | — |
| D6 | Bench → client submit (place) | marketplace flow | same :6911 | — | cross-tenant | coinsValidations | — |
| D7 | Archive view/delete | archive routes | ApplicationArchiveController:37,44,79 | `view_job_applications` / `delete_job_applications` | yes | — | — |
| D8 | Offer view/edit | offer routes | ApplicationOfferController:28,119,253 | `view_job_applications` / `edit_offer` | yes | offer terms | — |
| D9 | Status pipeline change | updatestatus variants | SC JobApplicationController | — | model scope | pipeline rules | ⚠ |
| D10 | Resume presign/store/status/retry | resume/* | ResumeUploadController | session only | yes | throttled | signed upload — OK |
| D11 | Resume upload session v2 | resume/v2/sessions/* | ResumeUploadSessionController | session only | yes | throttled, feature-flagged | — |
| D12 | Bulk applications import | upload → queued | JobApplicationsNewUpload (job) | dispatch-time only | explicit company_id | duplicate rules | no re-auth (accepted pattern) |
| D13 | Similar candidates | apptracker/similar_candidates | AppTracker | — | ES filter | — | ⚠ if filter missed (M-5) |

## E. Service Company — Clients & Master Data

| # | Action | Controller | Permission | Notes |
|---|---|---|---|---|
| E1 | Clients CRUD + contacts + files | ServiceCompanyClientController | `*_clients` partial | some AJAX list routes bare ⚠ |
| E2 | Job categories CRUD | ServiceCompanyJobCategoryController:45,52… | `*_category` | — |
| E3 | Skills CRUD | SkillController | `*_skills` | — |
| E4 | Designations | ServiceCompanyDesignationController:34 | `add_job_applications` (borrowed) | semantic misuse (M-4) |
| E5 | Questions | QuestionController | `*_question` | — |
| E6 | Locations | LocationController | `*_locations` | — |
| E7 | Consultancy mgmt | ServiceCompanyConsultancyController | — mostly ⚠ | staffing legacy |

## F. Service Company — Team, Roles, Settings

| # | Action | Controller | Permission | Business | Risk |
|---|---|---|---|---|---|
| F1 | Team list/invite/edit | ServiceCompanyTeamController | `*_team` partial | coin deduction per invite; package user limits | root_user exempt |
| F2 | Roles & permission manager | ManageRolePermissionController:43,240,455 | `view_roles_permission` / `add_roles_permission` | roles per company | only roles[0] effective (M-3) |
| F3 | Application settings | ApplicationSettingsController:32,66 | `manage_settings` | — | — |
| F4 | Account settings | ServiceCompanyAccountSettingsController | UpdateAccountSettingsRequest | — | verify authorize body |
| F5 | Embedded salary editor | account.offerAmountChange | routeIs check in SecurityHeaders | same-origin drawer only | — |
| F6 | Subscription mgmt | ManageSubscriptionController | — | coins/packages | billing logic |
| F7 | Sticky notes / todo / theme / LinkedIn | respective controllers | — | user-scoped | low risk |

## G. Dashboard & Realtime

| # | Action | Route | Controller | Checks |
|---|---|---|---|---|
| G1 | Dashboard view | S dashboard | ServiceCompanyDashboardController | custom-package gate |
| G2 | Dashboard API v2 (kpis/leaderboards/interviews) | api/v2/…/dashboard | ServiceCompanyDashboardApiController | session; Redis cache company-scoped |
| G3 | Cache invalidate | POST …/cache/invalidate | same | session (any member can flush — low, L-4) |
| G4 | WebSocket subscribe | channels | routes/channels.php | user/company match ✅ |
| G5 | Search cities inline route | S /search-cities | closure | master data; live `dd()` ⚠ (M-7) |

## H. Payments / Coins / Billing (tenant-facing)

| # | Action | Entry | Checks | Notes |
|---|---|---|---|---|
| H1 | Coin validation & deduction | `GeneralMethod::coinsValidations*`, `coinsDeductions` (Helper:5035) | company balance | business-rule engine |
| H2 | Cart / checkout | carts flow | session | — |
| H3 | Gateway webhooks (Stripe/Razorpay/PhonePe/PayPal) | webhook routes | provider signature; CSRF-exempt whitelist (bootstrap/app.php) | ✅ pattern |
| H4 | Invoice download | invoice routes | — ⚠ | verify ownership |

## I. Files

| # | Action | Entry | Checks | Risk |
|---|---|---|---|---|
| I1 | Resume download | HomeController:797-819 | session only | **no ownership verify** — CRITICAL (C-1) |
| I2 | Logo / login bg / avatar / candidate photo | asset_url public | none | guessable photo paths (M-6) |
| I3 | Candidate photo delete | Admin/ApplicationArchive controllers | `delete_job_applications` (partial) | — |
| I4 | R2 orphan reconcile | console ReconcileOrphanUploads | console | — |

## J. Marketplace (cross-tenant by design)

| # | Action | Tables | Checks |
|---|---|---|---|
| J1 | Requirement send/accept/reject | recommendation_relation | session + coins + relation state |
| J2 | Staffing partner link | staffing_company_relation | session + relation |
| J3 | Bench request processing (queued) | ProcessCandidateRequest | explicit company ids |
| J4 | Conversations / ratings | conversations, conversation_log | from/to company |

## K. Background / Scheduled (no interactive auth; console skips scopes)

| # | Action | Class | Tenant context |
|---|---|---|---|
| K1 | ES indexing jobs | Elastic*Index / *IndexJob | explicit ids |
| K2 | Counter refresh jobs | Bulk/Update/Decrement*Jobs | explicit company_id |
| K3 | Auto-close expired jobs | AutoCloseExpiredJobsJob | loops companies |
| K4 | Licence expiry sweep | Console LicenceExpire | CompanyPackage per company |
| K5 | Operational coin deductions | ProcessOperationalCoinDeductionsJob | company |
| K6 | Offer timeout | ProcessOfferTimeoutJob | application-bound |
| K7 | Last-login ES write | RecordLastLoginEsJob | user+company params |
| K8 | Mail / WhatsApp / notifications | Queue* jobs | payload recipients |

---

## How to extend this inventory

For every NEW action append a row: Actor, Route, Controller, Permission (name or —),
Tenant scope (auto/manual/none), Ownership rule, Business rule (coins/package/pipeline),
Background job (if any), Audit log (none exists today), Potential risk. Mirror any new
permission into PERMISSION_MATRIX.md §3 and any new rule into the implementation guide.
