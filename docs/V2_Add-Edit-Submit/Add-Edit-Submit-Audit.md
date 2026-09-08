# Add / Edit / Submit Audit — V2 Workflow Readiness Tracker

> **Date:** 2026-08-15
> **Scope (strictly enforced):** the four **in-scope personas** — Service Company, Super Admin, Admin, public Front/SaasFront/Auth. **Product Co and Staffing Co are dormant — not in this audit.** Full scope rule: [PERSONA_SCOPE.md](../PERSONA_SCOPE.md).
> **Sweep modes combined:** *narrow* (literal CRUD verbs) + *broad / transition-state-change* (every status flip, bulk update, side effect, import, export, external call, read-ajax).
> **Goal:** produce a tracker the team can use to (1) see every Add/Edit/Submit touchpoint in scope, (2) see what state transitions are wired, (3) spot what's missing, (4) prioritize the slice refactors that make this ready for 10K concurrent users with low maintenance + low server cost.
>
> **Personas missing from this audit:** Product Company, Staffing Company. Dormant code paths in `routes/product.php`, `routes/service_old.php`, `app/Http/Controllers/ProductCompany/*`, `app/Http/Controllers/StaffingCompany/*`, `resources/views/product_company/*`, `resources/views/staffing_company/*` are intentionally not listed.

---

## How to read this document

1. **§ 1 — Summary at a glance.** The 16 numbers that matter (CRUD + transition).
2. **§ 1b — Blade view index.** Curated entity → blade file index (where the Add/Edit/Submit buttons live in `resources/views/`). Read this when triaging "where do I edit jobs?" / "which file shows the offer-letter preview button?".
3. **§ 2 — Tracker table.** Every matching route, grouped by persona → module → entity. Read this when triaging "who owns this Add/Edit screen?"
4. **§ 3 — CRUD completeness matrix.** 55 entity rows × 4 CRUD verbs (Add / Edit / Submit / Delete). Read this when planning what to build or what to remove.
5. **§ 4 — Per-entity transition map.** 46 entity rows × transition classes (STATE TRANSITION / UPDATE row / BULK UPDATE / SIDE EFFECT / IMPORT / EXPORT / EXTERNAL CALL). Use this when planning which entity needs which kind of refactor.
6. **§ 5 — Hotspots (H1–H10 + T1–T7).** Fat controllers, parallel stacks, debug routes, ES sync gaps, webhook exposure, fan-out concurrency, bulk-by-Observers gaps. Read this before deciding what to refactor first.
7. **§ 6 — Recommended next steps.** Sequenced Phases 1–7, with effort estimates. Combined ~31 dev-days.
8. **§ 7 — Related materials.** Where the underlying scratch data and cross-references live.

Underlying working file (scratch, do not commit to source control — underscored prefix signals this):

- [`evidence/_Add-Edit-Submit-Audit-evidence.md`](evidence/_Add-Edit-Submit-Audit-evidence.md) — full per-row data: 196-row route inventory, 55-row CRUD matrix, hotspots themes A–G, 196-row transition reclassification, 46-row transition matrix.

---

## 1. Summary at a glance

| Metric | Value |
|---|---|
| In-scope personas audited | 4 (Service Co · Super Admin · Admin · Front/SaasFront/Auth) |
| Out-of-scope (dormant, skipped) | 2 (Product Co · Staffing Co) |
| **Filtered routes discovered** | **196** |
| &nbsp;&nbsp;· Service Co (`routes/service.php`) | 59 |
| &nbsp;&nbsp;· Super Admin (`routes/superadmin.php`) | 90 |
| &nbsp;&nbsp;· `routes/web.php` (Auth · Front · SaasFront · Admin inline · Payment · Public · Debug) | 47 |
| Distinct driver tables touched | ~55 |
| Controllers > 100 KB (slice-refactor candidates) | 13 |
| **Duplicate-controller pairs** (parallel stacks) | 1 high (Role/Permission), 2 medium (Tickets/Comments, Account Settings) |
| Public debug routes still reachable | 12+ (incl. `/whatsapp/*`, `/mail-test`, `/redis-test`) |
| Authenticated webhook endpoints (unauth POST, sync external HTTP + DB write) | 7 (PayPal IPN, Stripe, Razorpay, PhonePe ×3, generic) |
| Entities missing Add page (POST-only) | 5 (jobs, interview_schedules, departments, interview_mode_lavel, applicant_notes) |
| Entities missing Delete route | 14 |
| Entities with zero CRUD UI (config or read-only) | 5 (smtp_settings, sms_settings, payment_settings, link_payments, job_qualifications) |
| **Reclassified into 8 transition classes** | STATE 58 · UPDATE 54 · SIDE 22 · READ-AJAX 23 · EXPORT 9 · EXTERNAL CALL 8 · IMPORT 6 · BULK 4 |
| **State transitions that do MySQL + ES + Reverb + (sometimes) mail inline** | **3 methods on `job_applications`** (hottest single row in the codebase) |
| **Webhook endpoints without idempotency-key middleware** | **7** |
| **Controller methods that bypass Observers** | role/permission pair ×2 controllers + `team/roleupdate` + coin-events/package/top-up |

**One-sentence verdict:** of 196 routes, ~100 are stateful (UPDATE row + STATE TRANSITION) and the workload is dominated by **three pipelines** — application stage transitions, role/permission flips, and billing webhooks. The stage pipeline is already the hottest single row in the codebase and currently does four channels (MySQL + ES + Reverb + mail) inline in the controller; the role pipeline has duplicate controllers writing without an Observer; the billing webhook pipeline has zero idempotency. The surface area is dominated by **two Service god controllers** (`ServiceCompanyJobsController`, `ServiceCompanyJobApplicationController`) that own ~80% of writes. Combined refactor: ~31 dev-days to 10K-concurrent readiness.

---

## 1b. Blade view index — where the Add / Edit / Submit buttons actually live

> The route-level Blade view column already exists in [`evidence §1`](evidence/_Add-Edit-Submit-Audit-evidence.md#1-route-inventory-narrow-sweep-196-rows). Below is a **curated entity → blade view** index for fast lookup: "where do I edit jobs?" / "which file shows the offer-letter preview button?". JSON/redirect/file-stream endpoints (`-` or `(file stream)` or `(rendered inline HTML)`) are omitted — only views that render Add/Edit/Submit UI are listed.

### Service Company — UI files

| Entity (driver table) | Add/Edit/Submit view file(s) under `resources/views/` | Notes |
|---|---|---|
| `job_applications` (create) | `service_company/job-applications/create.blade.php` + `clientcreate` variant of same file | Two GET routes (`create` + `clientcreate`) both render this view |
| `offer_job_applications` / `application_offer` (create-offer form) | `service_company/applicationoffer/create_offer.blade.php` | GET `create-offer/{id}` |
| `offer_job_applications` (preview) | `offer-letter` (rendered by `cvtracker.previewOfferLettetr`) | inline-rendered HTML, not a file route |
| `tickets` (create) | `service_company/supports/create.blade.php` | GET `ticketcreate` |
| `todo_items` (edit inline) | `service_company/todo-list/todo-item-edit.blade.php` | rendered by POST `todo-items/update-todo-item` |
| `subscriptions` (custom-package picker) | `service_company/auth-custom-package/index.blade.php` | `validatePackage` + `validateClientPackage` |
| `jobs` (hiring manager form) | `service_company/jobs/hiring_manager` (rendered inline snippet, not a file route) | POST `jobs/hiring_manager/{id?}` |
| `job_applications` (recruiter reassign) | `service_company/jobs/recruiter` (rendered inline) | POST `job-applications/recruiter/{id?}` |
| `jobs` (client-job picker on CV tracker) | `cvtracker/get_client_job` (rendered inline snippet) | POST `cvtracker/get_client_job` |

### Super Admin — UI files

| Entity (driver table) | Add/Edit/Submit view file(s) under `resources/views/` | Notes |
|---|---|---|
| `companies` (edit form) | `super-admin/company/edit.blade.php` | GET `company/edit/{id?}` |
| `companies` (billing modal) | `super-admin/company/change_package_modal.blade.php` | POST `billing/viewmore` |
| `coupons` (add/edit form) | `super-admin/coupons/add-edit.blade.php` | GET `coupons/add-edit/{id?}` |
| `jobs` (JD create) | `super-admin/jd/create.blade.php` | GET `createjd/{entryType}/{id?}` |
| `tickets` (create) | `super-admin/supports/create.blade.php` | GET `ticketcreate` |
| `packages` (feature editor) | `super-admin/packages/add_edit_package_feature.blade.php` | ANY `add-edit-package-features/{id?}` |
| `custompackages` (create) | `super-admin/custompackages/create.blade.php` | GET `custompackages/create/{company_id?}` |
| `custompackages` (edit) | `super-admin/custompackages/edit.blade.php` | GET `custompackages/editWallet/{id?}` |
| `custompackages` (invoice render) | `razorpay-invoice.custom_package_invoice` (rendered inline) | POST `custompackages/createInvoice` |
| `recruiter_note_master` (list) | `super-admin/recruiter-note/index.blade.php` | GET `recruiter-note` |

### Front / SaasFront / Public — UI files

| Entity (driver table) | Add/Edit/Submit view file(s) under `resources/views/` | Notes |
|---|---|---|
| `job_applications` (public apply) | served from `resources/views/saas-front/` + `resources/views/front/` (no dedicated route-side view file) | POST `job/saveApplication` (×2 route entries) |
| `offers` (public save-offer) | served from same front view cluster | POST `save-offer` (×2 route entries) |
| `users` (OTP/login/forgot/reset) | standard Laravel Auth views under `resources/views/auth/` | web.php Auth cluster |
| `contacts` (public contact form) | rendered from SaasFront view cluster | POST `(root)` `contact` |

### Where the Blade view column is NOT in the matrix (POST-only / inline)

Several controllers render UI **inline** from the controller (returning HTML strings) instead of from a view file. These are the routes whose `Blade view` in `evidence §1` reads `(rendered inline HTML)`:

- `ServiceCompanyJobsController@jobs/hiring_contact_person` (line 150)
- `ServiceCompanyJobsController@mail_contact_person` (line 151)
- `ServiceCompanyJobsController@jobs/hiring_manager` (line 171)
- `ServiceCompanyJobApplicationController@recruiter` (line 217)
- `ServiceCompanyCvTrackerController@cvtracker/get_client_job` (line 328)
- `SuperAdminCustomPackageController@custompackages/createInvoice` (line 335)
- `SuperAdminCompanyController@googleSheetCandidateData` (line 90)
- `SuperAdminCompanyController@company/billinDetails` (line 98)

These are the **slice-refactor candidates** with the highest leverage — moving them to `.partials/*.blade.php` makes the rendered HTML discoverable to grep, designers, and form-automation tools.

---

## 2. Tracker table (Add/Edit/Submit touchpoints)

> Format: persona → module → counts. Full per-row data (route name, line, controller, blade view, DB table) lives in the scratch file linked at §7.
>
> **Where the actual Add/Edit/Submit buttons live:** the **Blade view column** in [`evidence/_Add-Edit-Submit-Audit-evidence.md §1`](evidence/_Add-Edit-Submit-Audit-evidence.md#1-route-inventory-narrow-sweep-196-rows) is the source of truth — every GET route has a `.blade.php` path (or `-` for JSON/redirect endpoints). Below is a curated view file → entity index for the most-touched screens.

### 2.1 Service Company (59 routes)

The dominant module by route count and weight.

| Module | Routes | Primary controller | Driver tables | CRUD shape |
|---|---:|---|---|---|
| **Resume upload** (session-based, presigned) | 6 | `ResumeUploadController` + `ResumeUploadSessionController` | `documents`, `bench_applications` | add/store/retry/finalize/cancel |
| **Clients** (clients + custom_client + direct_client + client_request) | 1 + 5 inline GETs | `ServiceCompanyClientController` | `clients`, `client_*` | edit/update, mass-update |
| **Jobs** (create/edit/hiring/recruiter/servicing/remove) | 14 | `ServiceCompanyJobsController` (673 KB god) | `jobs`, `service_on_jobs` | add/update/removeJob/status/hiring |
| **Job applications** (the hottest module) | 27 | `ServiceCompanyJobApplicationController` (661 KB god) | `job_applications`, `documents`, `interview_*`, `offer_*` | clientappstore/stage_submit/client_stage_submit/update/schedule/recruiter/offer/mass-mail/import/export |
| **App tracker + tickets + comments** | 6 | `ServiceCompanyAppTrackerController` (386 KB) | `job_applications`, `tickets`, `comments` | store, ticketUpdate, ticketCreate, storetComment, export |
| **CV tracker + bulk comms** | 14 | `ServiceCompanyCvTrackerController` (329 KB) | `job_applications`, `add_job_wise_condidates` | talent-add/copy/mass-mail/send-mail/send-whatsapp/process_update |
| **Interview schedule** | 8 | `InterviewScheduleController` | `interview_schedules`, `interview_*` | followupUpdate/send-link/changeStatusMult/export/updateFeedback/updateInterview |
| **Offer tracker** | 3 | `ServiceCompanyApplicationOfferController` | `application_offer`, `offer_*` | export/updateSalary/update_application |
| **Job onboarding** | 3 | `ServiceCompanyJobOnboardController` | `on_board_*` | sendOffer/updateStatus/export |
| **Consultancy** | 2 | `ServiceCompanyConsultancyController` | `consultancy`, `invitation_*` | export/staffing_invitation |
| **Bench candidates** (the marketplace core) | 7 | `ServiceCompanyBenchCandidatesController` (359 KB) | `bench_applications`, `bench_*` | boostkeywordadd/benchSwitch/savePlacedAssignment/savePlacedCommercial/download/status_submit/update_placed_end_date |
| **Account settings** (account + career-page + dept + stage) | 11 | `ServiceCompanyAccountSettingsController` (note: `sendinvitation` → Admin*) | `companies`, `career_*`, `departments`, `master_status` | careerPageSettingsSave/update/videoMeeting/boostkeywordadd/boostStatus/addIntStage/delIntStage/updatePriority/storeDept/companysavecreateoffer |
| **Team** | 7 | `ServiceCompanyTeamController` | `users` | resetpassword/statusUpdate/teamDelete/reSendmail/roleupdate/WorkingStatus |
| **Role / permission** (parallel stack — see §5.C) | 13 | `ServiceCompanyRolesPermissionController` AND `ManageRolePermissionController` | `roles`, `permissions`, `role_user`, `permission_role`, `role_has_permissions`, `model_has_permissions` | assignAll/removeAll/assignRole/detachRole/storeRole/deleteRole/saveRole (×2)/removerole |
| **Subscriptions & billing** | 5 | `ManageSubscriptionController` | `subscriptions`, `company_packages`, `package_invoice`, `coupons` | checkoutSubscribe/customCheckoutSubscribe/deletecheckout/applyCoupon/removeCoupon |
| **Auth/feature gating** | 2 | `AuthCustomPackageController` | `subscriptions` | validatePackage/validateClientPackage |
| **Job category** | 1 | `ServiceCompanyJobCategoryController` | `job_categories` | export |
| **Job tracker** | 2 | `ServiceCompanyJobTrackerController` | `job_tracker_*` | store/updateinformation |
| **Account lifecycle** | 1 | `CompanySettingsController` | `companies` | delete-account-store |
| **Todo items** | 1 | `ServiceCompanyTodoItemController` | `todo_items` | updateTodoItem |

### 2.2 Super Admin (90 routes)

| Module | Routes | Primary controller | Driver tables | CRUD shape |
|---|---:|---|---|---|
| **System i18n** (footer + language) | 4 | `SuperAdminFooterSettingsController` + `LanguageSettingsController` | `footer_*`, `language_settings` | changeStatus/storeFooterMenu/updateFooterMenu/changeLanguage |
| **Application update** (admin dev tool) | 6 | `UpdateApplicationController` | `applications`, `update_*` | deleteFile/update/download/install (dev-only) |
| **Company admin** (the super-admin god — meeting, skills, jd, coins, notification, themes, billing) | 38 | `SuperAdminCompanyController` (510 KB god) | `companies`, `users`, `skills`, `boostkeywords`, `coin_*`, `feature_*`, `theme_settings`, `notifications`, `default-terms`, `google_meeting_*`, `team_meeting_*`, `meeting_schedules` | updateCompanyPackage/loginAsCompany/edit_company/update_company/recruiter/store-schedule/skillUpdate/skillDelete/candidateApproval/jobApproval/autoApproval/reject_mail/emailThemes/email_template/whatsappThemes/featuresList/coinEventsStore/coinPackageStore/coinEventStatus/topUpCoinPackageStore/notificationupdate/addEditnotificationupdate/addEditTermsupdate/companysavecreateoffer/feature/notification/jd-elastic/google-sheet/recruiter |
| **Coupon** (clean REST) | 4 | `SuperAdmin\CouponController` | `coupons` | addEdit/store/toggleStatus/destroy |
| **Tickets + comments (super-admin mirror)** | 6 | `SuperAdminCompanyController` | `tickets`, `comments` | ticketUpdateStatus/ticketCreate/storeticketCreate/storetComment |
| **Front CMS** (homepage hero + features) | 6 | `SuperAdminFrontCmsController` | `front_cms_headers`, `front_image_features`, `features` | updateCommonHeader/updateHeader/savefeatures/updatefeatures/deleteFeature |
| **LinkedIn / global settings** | 2 | `LinkedInSettingController`, `GlobalSettingController` | `linked_in_settings`, `global_settings` | updateStatus/updatePr |
| **Custom billing / packages** | 9 | `CustomPackageController` + `DefaultPackageController` + `PackageController` | `custompackages`, `defaultpackages`, `package_*` | createInvoice/create/saveinvoice/submitcomment/editWallet/update/update/add_edit_package_feature |
| **Recruiter note templates** | 4 | `RecruiterNoteTemplateController` | `recruiter_note_master` | index/store/update/destroy (clean REST) |
| **Custom modules (purchases)** | 1 | `CustomModuleController` | `custom_modules` | verifyingModulePurchase |

### 2.3 `routes/web.php` (47 routes, mixed)

| Persona bucket | Routes | Notes |
|---|---:|---|
| **Auth** | 9 | Login (throttle 5,1), logout, password reset/email/ResetPasswordStore/emailsend, OTP send/verify, mark-all-read |
| **Front** | 6 | `save-offer` (×2), `saveApplication` (×2), `fetch-country-state` (×2) — public job apply + offer response |
| **SaasFront** (marketing site + signup) | 12 | `verifyTeamProfile`, `invitation-company-store`, `submit` (contact), `companyRegister` (1,193-line method — see Reg audit), `companyRegisterChk`, `validEmailChk`, `importMeetings`, `contactCompanyRegisterChk`, `domainCompanyRegisterChk`, `domainClientRegisterChk`, `changeLanguage`, `submitContact` |
| **Public/Front mixed** | 5 | `delete-template`, `uploadresume`, `resumExtract`, `careerStore`, `fwd_offer_download` |
| **Payment gateways** | 7 | PhonePe `create-payment` / `callback` / `phonepe-response`, generic `payment-callback`, `fwd_offer_download` |
| **Debug (must be gated — see §5.D)** | 5 | `whatsapp/create`, `whatsapp/restart`, `whatsapp/send`, `whatsapp/delete`, `salary/calculate`, `notifications/read/{id}`, `questions/store` |
| **Admin (inline in web.php)** | 2 | `account/sendinvitation` → `AdminAccountSettingsController` (see §5.B), and one more inline |
| **Payment webhooks** | 3 | `verify-billing-ipn` (PayPal), `save-invoices` (Stripe), `save-razorpay-invoices` (Razorpay) — unauthenticated POSTs doing sync external HTTP + DB writes |

---

## 3. CRUD completeness matrix (55 entity groups, 4 verbs × 4 personas)

> Legend: **✅** wired · **⚠** partial / one method only / no GET form route · **❌** missing · **—** N/A.
> Full per-row notes with controller line numbers in the scratch file [evidence §2](evidence/_Add-Edit-Submit-Audit-evidence.md#2-crud-completeness).

### 3.1 Service Company — heavy modules (the god controllers)

| Entity (driver table) | Add | Edit | Submit | Delete | Hot note |
|---|---|---|---|---|---|
| `jobs` | ⚠ POST-only (no GET `jobs/create`) | ✅ `jobs.update` | ✅ `apptracker.store` | ✅ `jobs.removeJob` (GET) | Add form is reached only via POST entry points — V2 needs explicit `jobs/create` GET |
| `service_on_jobs` (job services) | ✅ POST `add-update-services-job` | ✅ POST `updateserviceviewmore` | ⚠ same | ❌ no destroy | appended-only |
| `job_applications` | ✅ `clientappstore` + `stage_submit` | ✅ `job-applications.update/{id}` | ✅ `stage_submit` + `client_stage_submit` | ⚠ `resume_delete` deletes docs not the row | **hottest module**; row-level delete is missing — audit/regulatory risk |
| `bench_applications` (bench talent) | ✅ `cvtracker.talent-add` | ⚠ edit method exists, no GET route | ✅ `status_submit` | ❌ no destroy | bench edit GET route is missing |
| `interview_schedules` | ⚠ POST `store-schedule`, no GET create | ✅ updateInterview/Feedback/Available/followup | ✅ `store-schedule` | ⚠ status only | create form reached only via apptracker modal |
| `offer_job_applications` | ✅ `save-create-offer` (GET form at `create-offer/{id}`) | ⚠ `update_application` | ✅ `terms-save-create-offer` | ❌ no destroy | offer letters persisted forever; archive via status |
| `application_status` / `master_status` | ✅ `account/addIntStage` | ⚠ `updatePriority` (reorder) | ❌ no store | ✅ `delIntStage` | wired through account settings, no own store endpoint |
| `tickets` / `comments` | ✅ `storeticketcreate` + GET `ticketcreate` + `comments/store` | ⚠ only `tickets/{ticket}/status` | ✅ same as Add | ❌ no destroy on Service side (SuperAdmin has `notedestroy`) | Service has no comment delete route |
| `clients` | ✅ Client controller `create` + `store` (routes filtered out) | ✅ `clients/update/{id}` + `edit` | ✅ `store` | ❌ no destroy | likely soft-only by design |
| `users` (team) | ✅ `ServiceCompanyTeamController::create`/`store` | ✅ `edit` + `update` | ✅ `store` | ✅ `team/teamDelete` | **`account/sendinvitation` dispatches to `AdminAccountSettingsController` (cross-persona — see §5.B)** |
| `role_*` / `permissions_*` | ✅ `storeRole` ×2 stacks | ✅ `updateRole` | ✅ `saveRole` ×2 | ✅ `removerole/{user_id?}` ×2 | **parallel controllers — see §5.C** |
| `subscriptions` / `company_packages` | ✅ `checkout-subscribe` / `custom-checkout-subscribe` | ⚠ no dedicated edit | ✅ `applyCoupon` / `removeCoupon` | ✅ `deletecheckout-subscribe` (GET — shouldn't be GET) | gated by `ask_purchase` view |
| `documents` (resumes) | ✅ `resume-upload/store` + `uploadresume` (×2) | ❌ | ✅ same | ✅ THREE independent delete paths (`unset-file`, `resume_delete`, session DELETE) | delete fragmentation |
| `todo_items` | ✅ full CRUD | ✅ full CRUD | ✅ | ✅ | clean |
| `sticky_notes` | ✅ full CRUD | ✅ full CRUD | ✅ | ✅ | clean |
| `interview_processes` | ✅ full CRUD | ✅ | ✅ | ✅ | clean (but POST-only routes) |
| `questions` | ✅ full CRUD (+ public `questions/store`) | ✅ | ✅ | ✅ | public POST writes anonymous |
| `candidate_status` | ✅ full CRUD | ✅ | ✅ | ✅ | clean |
| `job_categories` | ✅ full CRUD (export-only in routes) | ✅ | ✅ | ✅ | clean |
| `skills` | ✅ | ✅ | ✅ | ✅ | clean (also SuperAdmin skills stack) |
| `boostkeywords` | ✅ `boostkeywordadd` | ❌ | ✅ | ✅ `boostStatusUpdate` (toggle) | no hard destroy |
| `departments` | ✅ POST-only | ❌ | ✅ | ❌ | inline admin form |
| `applicant_notes` | ✅ POST-only (`recruiter-note-update`) | ❌ | ✅ | ❌ | AJAX inline only |
| `job_qualifications` | ❌ no service controller, no route | ❌ | ❌ | ❌ | **entity exists in DB schema but no CRUD in inventory** |

### 3.2 Super Admin — heavy modules

| Entity (driver table) | Add | Edit | Submit | Delete | Hot note |
|---|---|---|---|---|---|
| `packages` (admin plans) | ✅ full CRUD | ✅ | ✅ | ✅ | **clean CRUD — exemplar** |
| `packages_features` | ✅ `add-edit-package-features` | same path | same path | ❌ | single endpoint covers Add/Edit |
| `custompackages` | ✅ `createInvoice` + `create` + `saveinvoice` | ✅ `editWallet` + `update` | ✅ `submitcomment` | ⚠ `destroy` exists, no route | dead destroy method |
| `package_invoice` / admin invoices | ⚠ `createInvoice` | ⚠ none | ✅ | ❌ | immutable in routes — bulk via custompackages |
| `coupons` | ✅ `addEdit` + `store` | same | ✅ `store` | ✅ `destroy` (+ `toggleStatus`) | clean |
| `recruiter_note_master` | ✅ `recruiter-note` POST | ✅ GET + PUT | ✅ | ✅ DELETE | **clean REST — exemplar** |
| `companies` | ✅ via `companyRegister` flow + `loginAsCompany` impersonate | ✅ `company/edit/{id?}` + `company/update/{id?}` | ✅ | ❌ no destroy | no row destroy |
| `companies` (self-service) | ✅ `ServiceCompanyAccountSettingsController::edit` | ✅ | ✅ | ⚠ soft only (`deleteAccountStore`) | service soft delete |
| `coin_*` family | ✅ `coin-events-store` + `coin-package-store` + `top-upcoin-package-store` + `topup-coins-added` | ⚠ implicit via `coin-event-status` | ✅ | ⚠ `coin-event-status` toggle, no hard delete | all admin flows live on `SuperAdminCompanyController` |
| `front_cms_*` | ✅ `features`/`update-common-header`/`update-header`/`add-edit` | same addEdit | same | ✅ `deleteFeature` | full CRUD |
| `language_settings` | ✅ `create` + `store` | ✅ `edit` + `update` | ✅ | ✅ `destroy` | toggle via `change-language-status` |
| `footer_settings` / `footer_menu` | ⚠ method exists, no GET add route | ✅ `update-footer-menu/{id}` | ✅ `store-footer-menu` | ✅ `destroy` exists, no route | partial route coverage |
| `th_email_template` / `emailthemes` | ✅ `email-themes/add-edit` | ✅ `email-themes/update/{id}` | same | ❌ | status toggle is the only delete |
| `whatsapp_themes` | ✅ same pattern | ✅ same | same | ❌ | same as above |
| `default-terms` / `offer_terms_conditions` | ✅ `add-edit` | same | ✅ `companysavecreateoffer` | ❌ | in-place edit only |
| `linked_in_settings` | ⚠ toggle only | ❌ | ❌ | ❌ | toggle-only via `updateStatus/{id}` |
| `google_sheets` | ✅ `*-google-sheet-get-data` (4 endpoints) | ❌ | same | ❌ | one-way fetch / write |
| `smtp_*` / `sms_*` / `payment_*` settings | ❌ | ❌ | ❌ | ❌ | config files only |
| `custom_modules` | ⚠ `verify-purchase` only | ❌ | same | ❌ | verify-only, no install/uninstall UI |
| `link_payments` | ❌ | ❌ | ❌ | ❌ | no CRUD wired |

### 3.3 Front / SaasFront / Public / Auth

| Entity (driver table) | Add | Edit | Submit | Delete | Hot note |
|---|---|---|---|---|---|
| `offers` (public save-offer) | ✅ `save-offer` | ❌ | ✅ | ❌ | write-only public endpoint |
| `job_applications` (public apply) | ✅ `job/saveApplication` | ❌ | ✅ | ❌ | public POST; no edit/delete |
| `users` (password reset, OTP, register) | ✅ register | ✅ (profile) | ✅ reset/verify | ❌ | standard auth |
| `companies` (signup) | ✅ `companyRegister` / `companyRegisterChk` | ⚠ none | ✅ | ❌ | **1,193-line monolith — see [V2 Registration audit](../V2_Registration/)** |
| `companies` (domain/sub domain signup) | ✅ `*RegisterChk` flows | ❌ | ✅ | ❌ | partial |
| `invoices` (payment webhooks) | — N/A | — N/A | ✅ `verify-billing-ipn` + `save-invoices` + `save-razorpay-invoices` | — N/A | unauthenticated POST doing sync external HTTP + DB write |

---

## 4. Per-entity transition map (46 entity rows × 8 transition classes)

> Strict row-count: the 196 narrow rows are reclassified here into 8 classes. Legend: **TRANS** = state transition · **BULK** = bulk mutation · **SIDE** = side effect · **IMP** = import · **EXP** = export · **EXT** = external call · — = none. Full per-row data in [evidence §3](evidence/_Add-Edit-Submit-Audit-evidence.md#3-transitions--state-change-route-list-broad-sweep).

### 4.1 Reclassified totals

| Persona | STATE TRANSITION | UPDATE row | BULK UPDATE | SIDE EFFECT | IMPORT | EXPORT | EXTERNAL CALL | READ-AJAX | Total |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|
| **Service Co** | 22 | 18 | 3 | 9 | 2 | 8 | 0 | 1 | **59** |
| **Super Admin** | 22 | 33 | 0 | 6 | 2 | 0 | 0 | 11 | **90** |
| **web.php (inline)** | 14 | 3 | 1 | 7 | 2 | 1 | 8 | 11 | **47** |
| **All** | **58** | **54** | **4** | **22** | **6** | **9** | **8** | **23** | **196** |

### 4.2 Class definitions

| Class | Definition | Example |
|---|---|---|
| `STATE TRANSITION` | Moves a row through a status pipeline (status flip, stage move, offer accept, impersonate, login, register). | `job-applications/stage_submit`, `tickets/{ticket}/status`, `coupons/toggle-status`, `company/{id}/login` |
| `UPDATE row` | Single-row mutation with no status flip (job fields, settings, terms text). | `jobs/update/{id}`, `account/update/{id}`, `email-themes/update/{id}` |
| `BULK UPDATE` | Many rows at once (mass role/schedule/insert/save). | `role-permission/assignAllPermission`, `apptracker/store`, `cvtracker/mass_mail`, `interview-schedule/change-status-multiple` |
| `SIDE EFFECT` | No primary-entity write; triggers mail / whatsapp / notification / ES reindex / cache wipe. | `cvtracker/send_mail`, `account/sendinvitation`, `interview-schedule/send-link`, `notifications/read/{id}` |
| `IMPORT` | Pulls data in (sheet, resume parse, meeting import). | `import-job-applications`, `candidate-google-sheet-get-data`, `extract-resume-applications` |
| `EXPORT` | Pulls data out (CSV, sample download, file stream). | `export-job-applications`, `apptracker/export/{...}`, `resume-as-pdf/{id}` |
| `EXTERNAL CALL` | Pass-through to a third-party gateway (webhook, payment callback, IPN). | `verify-billing-ipn`, `save-invoices`, `phonepe.callback` |
| `READ-AJAX` | Feeds the UI without writing. | `cvtracker/get_client_job`, `job/fetch-country-state`, `valid-email-chk` |

### 4.3 Per-persona shape (where the work actually is)

- **Service Co (59 routes):** `STATE TRANSITION` (~37%) + `UPDATE row` (~31%) + `SIDE EFFECT` (~15%) dominate. The split reflects the application lifecycle: actions are mostly state moves, many via the apptracker + cvtracker + bench controllers.
- **Super Admin (90 routes):** `UPDATE row` (~37%) is the majority — plan/package/settings edits. `STATE TRANSITION` (~24%) for the approval toggles (`market-auto-approval`, `job-approval`, `candidate-approval`, `auto-approval`). Heavy in `READ-AJAX` (~12%) for the admin UI feed endpoints.
- **web.php mixed (47 routes):** `EXTERNAL CALL` is the standout (~17%) — all the billing webhooks live here.

### 4.4 Entity-level highlights

| # | Entity (driver table) | Add/Edit CRUD (see §3) | TRANS | BULK | SIDE | IMP | EXP | EXT | Hot note |
|---|----------------------|------------------------------|-------|------|------|-----|-----|-----|----------|
| 1 | **jobs** | ✅ Add/Edit/Delete | ✅ 5 toggles (updatestatus, newUpdateJobstatus, removeJob, assignRejected, hiring_manager, mail_contact_person, jd-google-sheet → state) | ✅ apptracker/store, jobtracker/store | 2 mail paths | jd-google-sheet | ✅ apptracker/export, cvtracker/export | — | status pipeline + dual channel |
| 2 | **job_applications** | ✅ Add/Edit (no destroy) | ✅ 13+ state moves (stage_submit, client_stage_submit, statusupdate, recruiter, candidate_process_update, application_copy, copy_current_selected_applicant, change-event-organizer, getGoogleVideoUrl, store-schedule, contactPersionWithTeam, update_application, unset-file, resume_delete, candidate-approval) | ✅ apptracker/store, jobtracker/store | **6 mail/whatsapp send methods** + 3 fwd methods | ✅ importJobCondidate, extract-resume-applications, resumeExtract, careerStore | ✅ export-job-applications, candidateExport, apptracker/export, resume-as-pdf, offer_download_mail | — | **hottest row in the codebase**; 30+ write paths, multi-channel |
| 3 | **offer_job_applications** | ✅ Save/Edit (no destroy) | ✅ save-create-offer, terms-save-create-offer, applicationoffer/update_application, applicationoffer/update_salary, job-onboard/send-offer, job-onboard/update-status | — | ✅ 3 offer-letter + offer-email + offer-PDF side effects | — | ✅ applicationoffer/export, job-onboard/export | — | offer-letter + offer-email + offer-PDF are **3 side effects on one transition** |
| 4 | **bench_applications** | ✅ Talent add / ❌ no destroy | ✅ status_submit, bench-switch, talent-add, candidate-approval | — | — | — | ✅ cvtracker/export, bench-candidates/download | — | bench on/off + placed status flip |
| 5 | **interview_schedules** | ⚠ POST / ✅ Edit / no destroy | ✅ 10 status-shaped methods (storeSchedule, change-event-organizer, getGoogleVideoUrl, changeStatusMultiple, updateFeedbackStatus, updateAvailableStatus, followupUpdate, updateInterview, sendInterviewLink, meetingstoreSchedule) | ✅ changeStatusMultiple (bulk flip) | sendInterviewLink | — | ✅ interview-schedule/export | — | 4 status-shaped methods + 1 bulk flip; observer-driven |
| 6 | **tickets** (support) | ✅ POST/GET forms | ✅ tickets/{ticket}/status (Service + SuperAdmin parallel) | — | — | — | — | — | Service + SuperAdmin parallel controllers (§5.C) |
| 7 | **comments** | ✅ store / ✅ destroy (SuperAdmin only) | ✅ note-delete/comment (SuperAdmin), delete-template (public) | — | meeting-comment-new, comments/store | — | — | — | Service has no comment delete route |
| 8 | **clients** | ✅ store/ update / no destroy | ✅ clients/update, consultancy/staffing_invitation | — | — | — | ✅ consultancy/export | — | tenant-scoped soft delete only |
| 9 | **users** (team) | ✅ invite / edit / soft delete | ✅ team/statusUpdate, team/WorkingStatus, team/teamDelete, team/roleupdate, team/reSendmail, team/resetpassword, account/sendinvitation, super-admin team/statusUpdate, team/WorkingStatus | — | resetpassword, reSendmail, sendinvitation | — | — | — | login/logout/password all route through web.php Auth cluster |
| 10 | **role_user / roles / role_has_permissions** | ✅ storeRole / updateRole / removerole | ✅ removerole ×2, saveRole | ✅ assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole (×2) | — | — | — | — | **parallel controllers — §5.C; bulk paths bypass Observer** |
| 11 | **subscriptions / company_packages / package_invoice** | ✅ checkout / ⚠ no edit / ✅ deletecheckout | ✅ checkoutSubscribe, customCheckoutSubscribe, deletecheckout-subscribe, company/updateCompanyPackage, saveinvoice, createInvoice (×2), applyCoupon, removeCoupon | — | — | — | — | — | gated by `ask_purchase` view |
| 12 | **invoices (gateway side)** | — N/A | ✅ topUpCoinsAdded | — | — | — | — | ✅ **7 webhooks unauth POST** | must read §5.T5 |
| 13 | **companies (admin edits)** | ✅ edit/ update / no destroy | ✅ company/update, checkActive, checkStatusOperational, checkStatusSales, delete-account-store, updateAccessCode, googleMeetStatusUpdate, teamMeetStatusUpdate, **loginAsCompany** | — | setting-google-meeting-store, setting-team-meeting-store, setting-zoom-store, addMeetingLists, meetingListDelete | ✅ google-sheet-get-data, jdgoogleSheetInsertdataInDatabase | — | — | **loginAsCompany impersonate is the highest-risk state transition** |
| 14 | **companies (signup)** | ✅ companyRegister (bulk) | ✅ invitation-company-store, register-chk (read-ajax ×5) | ✅ ~70 rows in 1,193 lines | — | — | — | — | 1,193-line monolith — see [V2_Registration/](../V2_Registration/) |
| 15 | **documents** (resumes / files) | ✅ store / ✅ delete (3 paths) | ✅ resume_delete (×3), unset-file, deleteFile (app-update log) | — | resume-upload/presign, store, retry, session/*, uploadresume, resume/upload | ✅ extract-resume-applications | ✅ download, resume-as-pdf, download-resume-bench-candidates | — | three independent delete paths + session lifecycle |
| 16 | **coin_events / coin_packages / coin_orders / top_up_coin_packages** | ✅ store / ✅ toggle / ✅ top-up | ✅ coin-event-status, topup-coins-added | — | — | — | — | — | **coin_balances written via raw DB::table — no CoinObserver** |
| 17 | **companies / settings (company_settings blob)** | ✅ settings blob | ✅ videoMeetingSeetingUpdate, career-page/settings/save, account/update | — | — | — | — | — | written through `account/update` |
| 18 | **notifications** | ✅ addEdit | ✅ notification/update, addEdit, SingleNotificationUpdate, mark-notification-read, notifications/read/{id} | — | — | — | — | — | 5 read/update transitions on the same row shape |
| 19 | **default-terms / offer_terms_conditions** | ✅ add-edit | — | — | — | — | — | — | in-place edit |
| 20 | **custom_modules** | ⚠ verify-purchase only | ✅ custom-modules/verify-purchase | — | — | — | — | — | verify-only |
| 21 | **linked_in_settings** | ⚠ updateStatus only | ✅ linkedin-settings/update-status | — | — | — | — | — | toggle-only |
| 22 | **google_sheets** (config) | ✅ googleSheetInsertdataInDatabase | — | — | — | ✅ 4 sheet-fetch endpoints | — | — | one-way fetch — write only |
| 23 | **front_cms_*** / **features** | ✅ full CRUD | ✅ front-cms/deleteFeature | — | — | — | — | — | full CRUD |
| 24 | **recruiter_note_master** | ✅ REST CRUD | ✅ recruiter-note.destroy | — | — | — | — | — | clean REST |
| 25 | **coupons** | ✅ store / toggle / destroy | ✅ coupons/toggle-status, coupons/destroy | — | — | — | — | — | toggle is hot path |
| 26 | **footer_settings / footer_menu / language_settings** | ✅ store / update / destroy | ✅ change-language-status, update-footer-menu | — | — | — | — | — | toggle-only delete |
| 27 | **questions** (interview) | ✅ questions/store (public + Service) | — | — | — | — | — | — | public POST writes anonymous |
| 28 | **sticky_notes / todo_items / applicant_notes / departments / designations** | ✅ various | — | — | — | — | — | — | pure CRUD; no transitions |
| 29 | **signatures** (offer letter) | ✅ save-signature | — | — | — | — | — | — | public save, no edit/delete |
| 30 | **contact** (SaasFront form) | ✅ submitContact | — | — | — | — | — | — | one-shot public form |

---

## 5. Hotspots (priority order — drives the refactor plan)

> Full evidence (controller line numbers, route line numbers, byte sizes) in [evidence §4 (hotspots) and §3.5 (transition hotspots)](evidence/_Add-Edit-Submit-Audit-evidence.md).

### H1. **God controllers own ~80% of writes** (P0 — slice refactor)

| Controller | Bytes | Methods | Owner |
|---|---:|---:|---|
| `app/Http/Controllers/HomeController.php` | 803,208 | ~many | root — junk drawer (see H5) |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | 673,250 | ~234 | Service — jobs + hiring contacts + recruiters + service-on-jobs all in one class |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | 661,675 | ~181 | Service — hottest module; applications create, stage_submit, offer, resume, schedule |
| `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` | 510,191 | ~361 | SuperAdmin — every CMS area + skills + jd + coins + meeting + features + emails |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | 386,555 | ~98 | Service — **already has a 13-step plan from the Aug 2026 audit** |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | 359,176 | ~123 | Service — bench pool + talent + placed + commercial config + offers |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | 329,396 | ~66 | Service — bulk email/whatsapp/copy + offer-letter render |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | 189,060 | ~92 | Service — team CRUD + invites + role + status |
| `app/Http/Controllers/SuperAdmin/CustomPackageController.php` | 187,875 | (multi) | SuperAdmin — custom billing + invoice render |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php` | 162,079 | (multi) | Service — dashboard + meeting comment |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | 152,981 | (multi) | Service — clients CRUD + custom + direct + request |
| `app/Http/Controllers/SuperAdmin/SuperAdminTeamController.php` | 135,466 | (multi) | SuperAdmin — admin-side team CRUD |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | 149,234 | (multi) | Service — schedule + feedback + follow-up |

**Action:** apply the V2 slice pattern (Route + FormRequest + Action + ES sync + Observer + Reverb event + Blade component + test) extracted out of these. Each Action should do one thing, queue heavy work, and re-index ES via the existing Observer.

### H2. **Parallel controllers — duplicate ownership of the same CRUD surface** (P0 — drift risk)

- **Role/Permission (worst):** `ServiceCompanyRolesPermissionController` (routes/service.php:502–511) AND `ManageRolePermissionController` (routes/service.php:547–567) **both expose** `assignAllPermission`, `removeAllPermission`, `assignRole`, `detachRole`, `storeRole`, `deleteRole`, `saveRole`, `removerole/{user_id?}` — slightly different request shapes, two stacks of code, drift certain. **Pick one, migrate the other.**
- **Account settings:** `ServiceCompanyAccountSettingsController` owns most `/account/*` for Service. `AdminAccountSettingsController` owns `account/sendinvitation` for both Service and Admin (cross-persona — see H3). One of these should go away post-deprecation.
- **Tickets/Comments:** `routes/service.php:297–301` and `routes/superadmin.php:292–297` register the same CRUD against different controllers (`ServiceCompanyAppTrackerController` vs `SuperAdminCompanyController`). Parallel implementations on the same data.

### H3. **Cross-persona controller reuse** (P1 — silent breakage risk)

- `routes/service.php:522` → `account/sendinvitation` is dispatched to **`AdminAccountSettingsController@sendinvitation`** (under `App\Http\Controllers\Admin\`), not a Service controller. The Service team-invite path silently depends on the Admin namespace. A future Admin rename breaks Service onboarding.

### H4. **Debug routes still reachable in production** (P1 — kill before 10K)

`routes/web.php:33–78, 122, 132, 166, 221, 112` exposes unprotected endpoints that an attacker (or a misplaced link) can hit at any time:
- `/whatsapp/connect`, `/whatsapp/create`, `/whatsapp/qr`, `/whatsapp/status`, `/whatsapp/restart`, `/whatsapp/send`, `/whatsapp/delete` — WhatsApp bridge.
- `/mail-test`, `/redis-test`, `/r2test`, `/send-interview-mail`, `/send-meeting`, `/wewwewewewewew`, `/send`, `/test-es`, `/notifications/read/{id}` — pure debug.
- `/whatsapp-terms-and-conditions`, `/whatsapp-email-elastic`, `salary/calculate`, `questions/store`, `import-meetings` — semi-public.

**Action:** gate behind `app()->environment('local')` or remove from production route file. See [BUG_REPORT.md](../BUG_REPORT.md) for prior history.

### H5. **HomeController junk drawer — 803 KB, 94 routes** (P1 — split before 10K)

`app/Http/Controllers/HomeController.php` is 803 KB. It serves 94 routes in `routes/web.php` mixing:
- Public career-page forms
- WhatsApp bridge
- Offer-letter renderer
- Catch-all form submit (`/submit`, `/submit-form`)
- Test exports
- Misc landing pages

Split into `HomeController` (shell only), `CareerPageController`, `WhatsAppBridgeController`, `OfferLetterController`, `ResumeUploadController` (root-level). Removes 94 routes from a single class.

### H6. **Document delete fragmentation** (P2 — pick a path)

Three independent paths delete a `documents` row:
- `POST unset-file` (routes/service.php:199) → `ServiceCompanyJobApplicationController@unsetFile`
- `POST job-applications/resume_delete` (routes/service.php:208) → `ServiceCompanyJobApplicationController@resume_delete`
- `DELETE resume-upload-session/{uuid}` (routes/service.php:65) → `ResumeUploadSessionController@cancel`

**Action:** centralize through a single `DocumentService::delete()` that handles S3/disk cleanup + DB row + Observer.

### H7. **Webhook safety** (P2 — synchronous external HTTP)

- `routes/web.php:418` — `verify-billing-ipn` → `PaypalIPNController@verifyBillingIPN`. Unauthenticated POST; synchronous external HTTP to verify before write. Couples latency to PayPal availability.
- `routes/web.php:419, 420` — Stripe + Razorpay webhooks; signature verification left to the controller body.
- `routes/web.php:270-275, 298` — PhonePe + generic PaymentController callbacks.

**Action:** add a `webhook.signature` middleware; queue the actual DB write so the request returns 200 fast.

### H8. **Missing Add / Edit / Submit routes that block workflow** (P2 — fill before 10K)

| Missing | Where | Why it matters |
|---|---|---|
| `jobs/create` GET | routes/service.php | Add job page only reached via POST entry points — UI must exist |
| `bench-candidates/{id}/edit` GET | routes/service.php | bench edit GET is missing |
| `interview-schedule/create` GET | routes/service.php | interview form reached only via apptracker modal |
| `job-applications.destroy` | routes/service.php | application-row delete is only via `resume_delete`; row is orphaned if the document is absent |
| `bench-candidates.destroy` | routes/service.php | bench row delete missing entirely |
| `clients.destroy` | routes/service.php | clients controller has no destroy method surfaced |
| `comments/{id}` DELETE on Service side | routes/service.php | only SuperAdmin has `note-delete/comment` |
| `comments/{id}` DELETE on SuperAdmin side | routes/superadmin.php | uses `SuperAdminCompanyController@notedestroy` (mixed responsibility) |
| `applicationoffer.destroy` | routes/service.php | offer letters never deleted |
| `coupons/toggle-status`-as-flip-only delete | routes/superadmin.php | no hard delete on coupons path (toggle OK) |

### H9. **ES sync — mostly intact, watch the bulk paths** (P3 — verify, don't refactor)

`app/Observers/` has 30 files. Mapping against the heavy writers:
- ✅ `JobObserver`, `JobApplicationObserver`, `BenchApplicationObserver`, `InterviewScheduleObserver`, `OnBoardObserver`, `OnBoardFilesObserver`, `UserObserver`, `ClientObserver`, `CompanyObserver`, `ApplicationStatusObserver`, `DocumentObserver` — present for the standard writes.
- ⚠ `apptracker/store` (bulk insert) — verify mass-insert semantics; see AppTracker audit Step 7.5.
- ⚠ `add-update-services-job` (writes `service_on_jobs`) — verify `service_on_jobs` ES re-index path.
- ⚠ Bulk coin events (super-admin routes:254–272) — raw `DB::table()` writes; no dedicated Observer; relies on write path itself.
- ⚠ Webhooks (routes/web.php:418–420) — direct writes to `package_invoice`, `stripe_invoices`; replica-lag risk.

### H10. **Entities with zero CRUD UI (config-only or read-only)** (P3 — intentional, leave alone)

`smtp_settings`, `sms_settings`, `payment_settings`, `link_payments`, `package_master`, `package_invoice_tax`, `job_qualifications`. Either config-only or scheduled to be wired elsewhere. **Do not auto-add UI for these.**

### T1. **Hot controllers for state transitions** (P0 — slice refactor)

The narrow audit's fat controllers dominate the transition list too. The **specific transition methods** matter most:

| Controller | Bytes | Transition methods (count, examples) |
|---|---:|---|
| `ServiceCompanyJobsController.php` | 673,250 | ~10 state moves: `updatestatus`, `newUpdateJobstatus`, `removeJob`, `assignRejected`, `hiring_manager`, `mail_contact_person`, `hiring_contact_person`, `jobServicesAddUpdate`, `updateserviceviewmore` |
| `ServiceCompanyJobApplicationController.php` | 661,675 | **~13 state moves** (the canonical app pipeline): `stage_submit` (line 7829), `client_stage_submit` (line 10348), `statusupdate`, `recruiter`, `contactPersionWithTeam`, `clientappstore`, `candidate_process_update`, `recruiterNoteUpdate`, `saveCreateOffer` (line 4583), `termsSaveCreateOffer` (line 5161), `storeSchedule` (line 6828), `changeEventOrganizer`, `getGoogleVideoUrl`, `resume_delete` |
| `ServiceCompanyCvTrackerController.php` | 329,396 | **~13 state moves** (the bulk-side fan-out): `mass_mail`, `mass_mail_send`, `send_mail`, `send_mail_send`, `send_mail_for_candidate`, `send_whatsapp`, `fwd_resume`, `fwd_resume_send`, `fwd_offer_send`, `application_copy`, `copy_current_selected_applicant`, `talentAdd`, `email_template` |
| `ServiceCompanyAppTrackerController.php` | 386,555 | `apptracker/store` (bulk insert), `ticketUpdateStatus`, `storetComment` — tracked by AppTracker audit (Aug 2026) |
| `SuperAdminCompanyController.php` | 510,191 | **~30+ state moves** in a single class: `loginAsCompany` (impersonate, line 4722), `updateCompanyPackage`, `updateAccessCode`, `googleMeetStatusUpdate`, `teamMeetStatusUpdate`, `settingZoomStore`, `recruiter`, `SingleNotificationUpdateS`, `coinEventsStore`, `coinPackageStore`, `topUpCoinPackageStore`, `topUpCoinsAdded`, `boostkeywordadd`, `marketautoApproval`, `jobApproval`, `candidateApproval`, `autoApproval`, `reject_mail`, `reject_mail_send`, `job_reject_mail`, `reject_mail_send_job`, `email_template`, `notedestroy`, `checkActive`, `notificationupdate`, `addEditnotificationupdate`, `addEditTermsupdate`, `skillDelete`, `skillUpdate`, `createjd`, `jdgoogleSheetInsertdataInDatabase` |

### T2. **ES / notification fan-out per state change** (P1 — queue the all-four paths)

Each `STATE TRANSITION` was scored for four side-effect channels: **MySQL write (M)**, **ES reindex (E)**, **Reverb push (R)**, **mail/whatsapp (W)**.

**All-four paths (M + E + R + W) inline** — the hottest single row in the codebase:
- `job-applications/stage_submit` — M (job_applications + application_event) + E (JobApplicationObserver) + R (`application.stage.changed`) + W (conditional on threshold). **Should be queued.**
- `job-applications/client_stage_submit` — same four-channel pattern.
- `cvtracker/candidate_process_update` — same four-channel pattern.

**Three-channel paths (M + E + R, no mail):**
- `apptracker/store` — bulk insert + per-row E (no bulk-insert opt) + per-row R event.
- `coin-events-store` / `coin-package-store` / `top-upcoin-package-store` / `topup-coins-added` — M (via raw `DB::table()`) + R (no E because no CoinObserver).

**Two-channel paths (M + E, no R/W):**
- `jobs/updatestatus`, `jobs/newUpdateJobstatus`, `jobs/removeJob` — JobObserver handles E.
- `team/statusUpdate`, `team/WorkingStatus`, `team/teamDelete` — UserObserver.
- `interview-schedule/updateInterview` — InterviewScheduleObserver.

**Lightest transitions (M only, no side):**
- `boostStatusUpdate`, `coupons/toggle-status`, `language-settings/change-language-status`.

**Pure side effects (no M):**
- `cvtracker/send_whatsapp`, `cvtracker/send_mail`, `cvtracker/fwd_resume`, `cvtracker/mass_mail` — outbound comms only.
- `interview-schedule/send-link` — outbound mail only.
- `account/sendinvitation`, `team/reSendmail`, `team/resetpassword`, `password/email`, `password/emailsend`, `sendOtpCode`, `send-otp-code/account` — invite/OTP mail only.
- `mark-notification-read`, `notifications/read/{id}` — cache-only.

**Routing decision:** any M+E+R+W path on a hot row (job_applications, bench_applications, offer_job_applications, interview_schedules) must end up behind an Observer+Job chain. Today they are inline. Phase 7 step 1.

### T3. **Concurrency hazards** (P1 — add locks)

State transitions on the same row from multiple sources collide today without row-level locks:

- **`job_applications`** — two recruiters (one in apptracker, one in cvtracker) hit `stage_submit` and `candidate_process_update` concurrently. **Already flagged by the AppTracker audit's `Step 4.5 (drift)`.** Observer-based reindex can lose to last-writer-wins.
- **`interview_schedules`** — `changeStatusMultiple` (bulk flip) collides with `updateFeedbackStatus` / `updateAvailableStatus`. Read-modify-write with no transaction wrapper.
- **`users / role_user`** — `team/roleupdate` (Service) collides with `assignRole` / `detachRole` (Manage) on the same `(user_id, role_id)` pivot.
- **`companies`** — `delete-account-store` (Service) collides with `loginAsCompany` (SuperAdmin).
- **`coin_orders`** — `topUpCoinsAdded` (admin) collides with `subscribe.checkoutSubscribe` (coin grant) on the same `company_id`. **No idempotency key.**
- **`tickets`** — Service and SuperAdmin both write `tickets/{ticket}/status`. The controller-level drift means two flips can produce inconsistent audit trails.

### T4. **Side-effect chains — quantified for the heaviest methods** (P1 — batch fan-out)

| Method | M writes | E writes | R pushes | Mail/WA |
|---|---:|---:|---:|---:|
| `cvtracker/mass_mail` (200-candidate blast) | 200 `job_applications` | 200 ES | 1 Reverb | 200 emails |
| `cvtracker/mass_mail_send` | 200 `job_applications` | 200 ES | 1 Reverb | 200 emails |
| `job-applications/stage_submit` | 2 (`job_applications` + `application_event`) | 1 ES | 1 Reverb | 0–1 mail |
| `SuperAdminCompanyController@reject_mail` / `job_reject_mail` | 1 `job_applications` (status) | 1 ES | 1 Reverb | 1 mail |
| `company/{id}/login` (impersonate) | 1 `sessions` (+ audit gap, see T2 follow-up) | 0 | 0 | 0 |
| **`SaasFrontController@companyRegister`** | **~70 rows** + **~30 ES syncs** + **3 external HTTP** | ~30 | 0 | 1 verification mail |

`companyRegister` is the **largest single transition in the codebase** by far. Already audited in [V2_Registration/](../V2_Registration/) — the prescription there (slice into Forms + Actions + queue) applies to all 5 `*-register-chk` flows.

### T5. **Webhook idempotency** (P1 — middleware before 10K)

All 7 billing-webhook routes are unauthenticated POSTs from third parties with **no idempotency-key middleware**:

| Line | URI | Controller | Note |
|---|---|---|---|
| `routes/web.php:270` | `phonepe.payment` (createPayment) | PhonePePaymentController | outbound to gateway |
| `routes/web.php:272` | `phonepe.callback` | PhonePePaymentController | inbound webhook |
| `routes/web.php:275` | `phonepe-response` (ANY) | PhonePePaymentController | inbound |
| `routes/web.php:298` | `payment.callback` | PaymentController | generic gateway callback |
| `routes/web.php:418` | `verify-billing-ipn` | PaypalIPNController | **highest-risk: synchronous outbound DNS + HTTP** |
| `routes/web.php:419` | `save-invoices` | StripeWebhookController | signature verification in controller body |
| `routes/web.php:420` | `save-razorpay-invoices` | RazorpayWebhookController | signature verification in controller body |

The PayPal IPN endpoint is the worst because it must perform synchronous outbound DNS + HTTP to PayPal to verify before writing to `paypal_invoices`. **Couples latency to PayPal availability + exposes an unauthenticated write path.** Phase 7 step 2.

### T6. **Bulk paths that bypass Observers** (P2 — add observers)

| Path | Tables written | Observer? |
|---|---|---|
| `role-permission/assignAllPermission` / `removeAllPermission` / `assignRole` / `detachRole` / `storeRole` / `deleteRole` (×2 controllers) | `role_has_permissions`, `role_user`, `model_has_permissions` | ❌ **No `RoleObserver` exists** (manual Spatie cache invalidation) |
| `team/roleupdate` | `role_user` | ❌ |
| `cvtracker/mass_mail*` (per-row updates via Eloquent) | `job_applications` | ✅ fires per-row (no batch), N ES writes for N rows |
| `apptracker/store` | `job_applications` (insert) | ✅ fires per-row — see AppTracker audit Step 7.5 |
| `coin-events-store` / `coin-package-store` / `topUpCoinsAdded` | `coin_events`, `coin_packages`, `coin_orders` | ❌ **raw `DB::table()` writes; no CoinObserver; no audit-log row** |

### T7. **Persona-cross-cutting transitions** (P2 — fold into Phase 1 of narrow audit)

Reuses the narrow audit's H3 (cross-persona controller reuse) but for transition-shaped routes:

- **`routes/service.php:522` → `AdminAccountSettingsController@sendinvitation`** — Service team-invite path depends on the Admin namespace; if Admin renames, Service breaks silently.
- **`routes/service.php:297-301` ↔ `routes/superadmin.php:292-297`** — both register `tickets/{ticket}/status`, `ticketcreate`, `storeticketcreate`, `comments/store` against different controllers (`ServiceCompanyAppTrackerController` vs `SuperAdminCompanyController`).
- **`routes/service.php:502-511` ↔ `routes/service.php:547-567`** — within Service itself, two controllers expose the same role/permission transitions (paired CRUD/transition problem from H2).
- **`routes/service.php:197` ↔ `routes/superadmin.php:227` ↔ `routes/web.php:381`** — **three different controllers writing `job_applications` from three personas** (Service app-management, SuperAdmin on-behalf-of edit, public apply). Each handles ES sync differently.
- **`routes/web.php:380` and `:404`** — both `save-offer` (duplicate route definitions).
- **`routes/web.php:381` and `:405`** — both `job/saveApplication` (duplicate).
- **`routes/web.php:382` and `:406`** — both `job/fetch-country-state` (duplicate).

The web.php duplicates are bugs; folding them is a 5-minute PR.

---

## 6. Recommended next steps (sequenced)

### Phase 1 — Triage (this week)
1. **Adopt this doc + PERSONA_SCOPE.md** as the working audit artifact for V2/V3 work.
2. **Gate the debug routes** (H4) — single PR, ~½ day: wrap each in `if (app()->environment('local')) { ... }` or move to a `debug.php` route file loaded only in dev.
3. **Pick the role/permission canonical controller** (H2) — decide ServiceCompanyRolesPermissionController vs ManageRolePermissionController. Migrate the loser. One PR.
4. **Move `account/sendinvitation` to a Service controller** (H3) — copy the method to ServiceCompanyTeamController (or a new ServiceCompanyInvitationController) and update the route. Half a day.

### Phase 2 — Slice the top two god controllers (this sprint)
5. **Slice `ServiceCompanyJobsController` (673 KB) into per-entity Actions**:
   - `Job\StoreAction`, `Job\UpdateAction`, `Job\RemoveJobAction`, `Job\AssignRejectedAction`, `Job\HiringContactPersonAction`, `Job\UpdateStatusAction`, `Job\HiringManagerAction`
   - Keep one thin controller that maps form requests → actions + renders views.
   - Plan: ~3 dev-days.
6. **Slice `ServiceCompanyJobApplicationController` (661 KB)**:
   - `JobApplication\StageSubmitAction`, `\ClientStageSubmitAction`, `\RecruiterAssignAction`, `\ScheduleAction`, `\Offer\SaveAction`, `\Document\UnsetFileAction`, `\MassMailAction`
   - Plan: ~4 dev-days.

### Phase 3 — Fill the CRUD gaps (next sprint)
7. **Add the 10 missing routes / methods** flagged in H8. Estimated 2–3 dev-days.
8. **Centralize document delete** (H6) into `DocumentService::delete()`. ~1 dev-day.
9. **Add explicit Add GET routes** for jobs / bench / interview / departments. ~1 dev-day.

### Phase 4 — Webhook hardening (before 10K launch)
10. **Add `webhook.signature` middleware** and queue webhook DB writes (H7). ~2 dev-days + load test.

### Phase 5 — Maintain the tracker
11. **Re-run this audit** whenever:
   - A new V2/V3 vertical slice ships.
   - A doc in `docs/V2_*/` plans a new endpoint.
   - The role/permission duplication is resolved (remove H2 row).
   - Product/Staffing personas are re-activated (lift scope rule; the matrix grows but the audit pattern does not).

### Phase 6 — Cvtracker slice (companion to Phase 2)
12. **Slice `ServiceCompanyCvTrackerController` (329 KB)** — extract `BulkMailAction`, `BulkWhatsappAction`, `CandidateCopyAction`, `TalentAddAction` per the V2/V3 slice pattern. Plan: ~4 dev-days.

### Phase 7 — Transition sweep (new, layered on top of Phases 1–6)

> Each step is sized in dev-days and targets a specific §5 hotspot.

**P7.1. Queue the all-four-channel pipeline on `job_applications` (3 dev-days)** — Targets: T1, T2.
1. Add a `StageTransitioned` event (already-named slot in `application_event`); wire `JobApplicationObserver::created/updated` to fire it.
2. Move the Reverb push into a `BroadcastStageTransitioned` Job (queue: `transitions`).
3. Move the conditional mail into a `SendStageMail` Job (queue: `mail`, retry with backoff).
4. Update `stage_submit` + `client_stage_submit` + `candidate_process_update` to `return` after the create/update; the rest happens out-of-band.
**Acceptance:** the three methods are <40 lines each, the Job chain can replay, and the apptracker concurrency audit Step 4.5 closes.

**P7.2. Idempotency-key middleware on all billing webhooks (2 dev-days + load test)** — Targets: T5.
1. Add `Webhooks\IdempotencyKeyMiddleware` that hashes `(gateway, event_id, payload-hash)` and stores in Redis (`webhook:idem:<hash>` TTL 24h). On hit, short-circuit with 200.
2. Wrap all 7 routes.
3. Move PayPal IPN outbound verify into a `VerifyPaypalIpnJob`; the controller only enqueues.

**P7.3. Bulk-insert optimization on `apptracker/store` and `cvtracker/mass_mail*` (3 dev-days)** — Targets: T1, T6.
1. Replace per-row `update()` with a single `DB::table()->update(...)` then dispatch a single `BulkStageTransitioned` event.
2. Fan out one Reverb event with a payload `{ids: [...], stage: '...'}`, not N events.
3. Move the email side-effect behind a `BulkMailJob` that batches per template.

**P7.4. De-duplicate role/permission controllers, add `RoleObserver` (1 dev-day)** — Targets: H2 + T6.
1. Pick canonical = `ManageRolePermissionController` (smaller).
2. Migrate `ServiceCompanyRolesPermissionController` methods that diverge; remove the duplicate.
3. Add `RoleObserver` for `roles`, `permissions`, `role_user`, `role_has_permissions`, `model_has_permissions` — fire a `SpatiePermissionCacheInvalidated` event so downstream code can react.
4. Wire the pivot writes (`assignRole`, `detachRole`, `saveRole`, `removerole`, `deleteRole`) to invalidate the Spatie permission cache (currently manual).

**P7.5. Slice `ServiceCompanyCvTrackerController` (4 dev-days)** — Targets: T1.
1. Extract `BulkMailAction`, `BulkWhatsappAction`, `CandidateCopyAction`, `TalentAddAction` per the V2/V3 slice pattern.
2. Move ES fan-out into the relevant Observer.
3. Convert mass_mail/mass_mail_send/mass_mail_for_candidate into a single `BulkOutboundAction` with queue + batching.

**P7.6. Lock `loginAsCompany` behind an audit-log row (½ dev-day)** — Targets: T3, T7 (cross-persona safety).
1. Add `impersonation_logs` table (audit table, indexed by `admin_id`, `company_id`, `started_at`).
2. In `SuperAdminCompanyController@loginAsCompany`, write a row BEFORE the session swap. Add `ended_at` on logout or next login.
3. Surface in admin dashboard with a search by admin/company.

**P7.7. Add `CoinObserver` + idempotency on coin grants (2 dev-days)** — Targets: T6, T3 (coin_orders collision).
1. Add `CoinObserver` for `coin_events`, `coin_orders`, `top_up_logs`, `coin_transection_logs`.
2. Add a unique `(company_id, gateway_event_id)` index on `coin_orders`.
3. Convert raw `DB::table()` writes on `top-upcoin-package-store`, `coin-events-store`, `topup-coins-added` to Eloquent.

**P7.8. Consolidate duplicate routes in `routes/web.php` (½ dev-day)** — Targets: T7 (3 duplicate route pairs).
1. Verify the duplicate POST routes truly resolve to the same handler (no shadow middleware).
2. Collapse `save-offer` ×2, `saveApplication` ×2, `fetch-country-state` ×2 into a single declaration each.

**P7.9. Move `account/sendinvitation` to a Service controller (½ dev-day)** — Targets: T7 (cross-persona reuse) + H3.
1. Copy `AdminAccountSettingsController@sendinvitation` body to `ServiceCompanyTeamController` (or new `ServiceCompanyInvitationController`).
2. Wire `routes/service.php:522` to the new path.
3. Leave Admin's `sendinvitation` wired to its own Admin-URI (if any) for symmetry.

**P7.10. Restrict debug routes behind `APP_DEBUG` or admin middleware (½ dev-day)** — Targets: T7 + H4.
1. Wrap each `routes/web.php:33-78, 112, 122, 132, 166, 221` in `if (config('app.debug')) { ... }` or under a `Route::middleware('admin')->group(...)`.
2. Confirm `/whatsapp/*` is dev-only and remove production routes file entry.

### Combined effort

| Phase | Effort | Cumulative |
|---|---:|---:|
| Phase 1 — Triage | ~2 dev-days | 2 |
| Phase 2 — Slice top god controllers | ~7 dev-days | 9 |
| Phase 3 — Fill CRUD gaps | ~4 dev-days | 13 |
| Phase 4 — Webhook hardening | ~2 dev-days | 15 |
| Phase 6 — Cvtracker slice | ~4 dev-days | 19 |
| Phase 7.1 — Queue stage pipeline | 3 dev-days | 22 |
| Phase 7.2 — Webhook idempotency | 2 dev-days | 24 |
| Phase 7.3 — Bulk-insert optimization | 3 dev-days | 27 |
| Phase 7.4 — Role de-dup + observer | 1 dev-day | 28 |
| Phase 7.5 — Cvtracker slice (overlaps Phase 6) | (4) | (28) |
| Phase 7.6 — Impersonate audit log | 0.5 dev-day | 28.5 |
| Phase 7.7 — CoinObserver | 2 dev-days | 30.5 |
| Phase 7.8 — Duplicate route collapse | 0.5 dev-day | 31 |
| Phase 7.9 — sendinvitation move (overlaps Phase 1) | (0.5) | (31) |
| Phase 7.10 — Debug route gating (overlaps Phase 1) | (0.5) | (31) |

**Total: ~31 dev-days** before the system is ready to make the 10K-concurrent claim (Phase 7.5, 7.9, 7.10 overlap earlier phases, so net ~31 dev-days not 32).

---

## 7. Related materials

- [PERSONA_SCOPE.md](../PERSONA_SCOPE.md) — the scope rule this audit follows. Read first.
- [AI_RULES.md](../AI_RULES.md) — token discipline + tenancy + ES sync rules that govern any refactor.
- [DEFINITION_OF_DONE.md](../DEFINITION_OF_DONE.md) — the gate any Action/PR must pass.
- [PERFORMANCE_BUDGET.md](../PERFORMANCE_BUDGET.md) — the targets a 10K-concurrent system must hit (P95/P99, queue depths, etc.).
- [SECURITY.md](../SECURITY.md) — webhook signature, XSS (BUG-04/SEC-03), tenant leak (BUG-03/SEC-02).
- [BUG_REPORT.md](../BUG_REPORT.md) — 63 active `dd()` calls, `env()`-outside-config, `JobApplication` tenant scope commented out.
- [MODULE_MAP.md](../MODULE_MAP.md) — which files own which module.
- [`evidence/_Add-Edit-Submit-Audit-evidence.md`](evidence/_Add-Edit-Submit-Audit-evidence.md) — single underlying scratch file with 5 sections:
  1. Route inventory (196 rows × 7 cols: line, method, URI, name, controller, blade view, DB table)
  2. CRUD completeness matrix (55 entity rows × 4-verb matrix + coverage summary)
  3. Transitions / state-change route list (196 rows reclassified with Transition class column)
  4. Transitions matrix (46 entity rows × 8-class transition map + T1–T7 evidence)
  5. Hotspots evidence (themes A–G with controller/route evidence and prioritized order)
- AppTracker-specific audit: [[apptracker-god-level-audit]] (memory) — already a 13-step plan for the AppTracker controller.
- V2 Registration audit: [V2_Registration/](../V2_Registration/) — covers the 1,193-line `companyRegister` flagged at H3 / web.php:347.