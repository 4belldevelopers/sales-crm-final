# Controllers Summary (`app/Http/Controllers/`, 241 controllers)

Indexed by persona namespace. **Controllers are huge — grep for the method, never read
whole.** ~1 line each (key ones). 🔴 = oversized · ⚠️ = dead/legacy (ignore).
**[INFERRED]** responsibilities from names/routes.

## ServiceCompany/ (agency app — `routes/service.php`)
- **ServiceCompanyDashboardController** 🔴 — Dashboard, meeting schedule/comments.
- **ServiceCompanyJobsController** 🔴 — Jobs/requisitions CRUD, custom/client requisitions, partner listing.
- **ServiceCompanyJobApplicationController** 🔴 — Candidate/application CRUD, status, resume forward.
- **ServiceCompanyCvTrackerController** 🔴 — CV tracker pipeline views (ES-backed).
- **ServiceCompanyAppTrackerController** 🔴 — Application tracker pipeline.
- **ServiceCompanyBenchCandidatesController** 🔴 — Bench candidate management.
- **ServiceCompanyClientController** 🔴 — Clients, contacts, addresses, requests.
- **InterviewScheduleController** 🔴 — Interview scheduling (Zoom, attendees).
- **ServiceCompanyTeamController** 🔴 — Team/user management within agency.
- **OLD_InterviewScheduleController / OLD_2_InterviewScheduleController** ⚠️ — legacy, ignore.

## ProductCompany/ (employer app — `routes/product.php`)
- **ProductCompanyJobsController** 🔴 — Job posting/management.
- **ProductCompanyJobApplicationController** — Applicant management.
- **ProductCompanyAppTrackerController** 🔴 — Applicant tracker.
- **ProductCompanyBenchCandidatesController** 🔴 — Bench candidates.
- **old_ProductCompanyJobsController** ⚠️ — legacy, ignore.

## StaffingCompany/
- **StaffingCompanyJobApplicationController** 🔴 — Vendor/staffing submission workflows.

## SuperAdmin/ (`routes/superadmin.php`)
- **SuperAdminCompanyController** 🔴 — Tenant (company) lifecycle, packages, licensing.
- **CustomPackageController** 🔴 — Custom plan creation/management.
- **SuperAdminTeamController** 🔴 — Platform team/users.

## Admin/ (tenant admin)
- **AdminJobsController** 🔴 / **AdminJobApplicationController** 🔴 — Tenant-level jobs & applications.

## Front/ & SaasFront/ (public)
- **SaasFrontController** 🔴 — Public marketing site / CMS.
- **Front/FrontJobsController** — Public job board listing/detail.
- **Front/FrontJobOfferController** — Public offer/apply flow.

## Auth/ & cross-cutting (root namespace, `routes/web.php` & `auth.php`)
- **Auth/LoginController, RegisterController, ForgotPasswordController, ResetPasswordController** — Auth.
- **HomeController** 🔴 — Catch-all incl. `/whatsapp/*` integration ⚠️ (also holds debug routes).
- **GoogleController** — Socialite OAuth. **ZoomController** — Zoom OAuth/callbacks.
- **NotificationController** — In-app notifications (Reverb).
- **ResumeController** — Resume view/download.
- **PaymentController / PhonePePaymentController** — Checkout flows.
- **StripeWebhookController / RazorpayWebhookController / PaypalIPNController** — Payment webhooks (CSRF-exempt).
- **VerifyMobileController** — OTP/mobile verification.
- **GoogleSheetsService** (controller) — Sheets export endpoints.

⚠️ Root-level `ServiceCompanyJobApplicationController.php` (outside `app/`) is a stray copy — ignore.
