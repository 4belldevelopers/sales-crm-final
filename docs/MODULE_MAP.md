# Module Map

> File-level index of each functional module. Paths are relative to repo root.
> Controllers are **very large** — always `grep` for the method you need; never read
> a controller whole. **[INFERRED]** groupings are by naming convention (no formal
> module package). Verify before relying on a path.

Legend: 🔴 = oversized file (>100 KB), ⚠️ = dead/legacy code (do not edit).

---

## Candidate / Application Management

The pipeline entity is **`JobApplication`** (a candidate attached to a job). Bench =
candidates not yet placed; CV Tracker / App Tracker = pipeline views.

- **Controllers:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` 🔴
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` 🔴
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` 🔴
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` 🔴
  - `app/Http/Controllers/ProductCompany/ProductCompanyJobApplicationController.php`
  - `app/Http/Controllers/ProductCompany/ProductCompanyAppTrackerController.php` 🔴
  - `app/Http/Controllers/ProductCompany/ProductCompanyBenchCandidatesController.php` 🔴
  - `app/Http/Controllers/StaffingCompany/StaffingCompanyJobApplicationController.php` 🔴
  - `app/Http/Controllers/Admin/AdminJobApplicationController.php` 🔴
  - `ServiceCompanyJobApplicationController.php` (repo **root**) 🔴 ⚠️ likely stray copy
- **Services:** `app/Services/ElasticCandidatesIndex.php`,
  `ElasticBenchCandidatesIndex.php`, `JobApplicationsNewUploadService.php`,
  `CandidatesMailAndResumeForward.php`
- **Models:** `JobApplication`, `BenchApplication`, `OfferJobApplication`,
  `ApplicantNote`, `CandidateStatus`, `ApplicationStatus`, `JobApplicationAnswer`,
  `AddJobWiseCondidate` (pivot `add_job_wise_condidates`), `Document`, `Onboard`,
  `OnboardFiles`
- **Jobs:** `JobApplicationsNewUpload`, `UploadResumeToS3`, `ElasticCandidateIndex`,
  `ElasticBenchCandidateIndex`, `ProcessCandidateRequest`,
  `QueueCandidatesMailAndResumeForwardMailService`
- **Observers:** `JobApplicationObserver`, `JobApplicationAnswerObserver`,
  `ApplicationStatusObserver`, `DocumentObserver`, `OnBoardObserver`,
  `OnBoardFilesObserver`
- **Routes:** `routes/service.php`, `routes/product.php`, `routes/web.php` (Staffing/Admin)
- **Views:** `resources/views/` (very large — see memory note
  [[agency-marketplace-views]]); candidate/CV tracker / bench subfolders
- **Dependencies:** Elasticsearch, S3 (resumes), Mail services, `Job`, `Company`

---

## Job / Requisition Management

- **Controllers:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` 🔴
  - `app/Http/Controllers/ProductCompany/ProductCompanyJobsController.php` 🔴
  - `app/Http/Controllers/ProductCompany/old_ProductCompanyJobsController.php` ⚠️
  - `app/Http/Controllers/Admin/AdminJobsController.php` 🔴
  - `app/Http/Controllers/Front/FrontJobsController.php` (public job board)
- **Services:** `ElasticJobIndex.php`, `ElasticJobListIndex.php`, `AutoCloseJobsService.php`
- **Models:** `Job`, `OpenworkspaceJob`, `JobCategory`, `JobLocation`, `JobSkill`,
  `JobQuestion`, `Question`, `Skill`, `client_city_jobs`, `jobCountry`
- **Jobs/Commands:** `ElasticJobsIndex` (job), `ExpiredJob`, `IndexJobsToElasticsearch`
- **Observers:** `JobSkillObserver`, `JobQuestionObserver`, `JobCategoryObserver`,
  `JobLocationObserver`, `QuestionObserver`, `SkillObserver`
- **Routes:** `routes/service.php` (`jobs.*`), `routes/product.php`, `routes/web.php` (front)
- **Dependencies:** Candidate module, Elasticsearch, `Client`

---

## Vendor / Agency Marketplace

(Find-agency / send-requirement / skill-search — see memory [[agency-marketplace-views]].)

- **Controllers:** within `ServiceCompany/` (client + requisition + partner listing
  methods inside `ServiceCompanyClientController.php` 🔴 and `ServiceCompanyJobsController.php`).
  Grep route names `clients.*`, `jobs.spartnerlisting`, `*_request_data`.
- **Models:** `Client`, `ClientFeedback`, `ElasticStaffingCompanyRelation`,
  `ElasticRecommendationRelation`, `Report`
- **Jobs:** `ProcessCandidateRequest`
- **ES indices:** `ats_client_and_agency_relation_index`, `ats_candidates_request`,
  `ats_jobs_request`, `ats_clients_request`, `ats_bench_assign`
- **Commands:** `IndexStaffingCompanyRelationToElasticsearch`,
  `IndexRecommendationRelationToElasticsearch`
- **Routes:** `routes/service.php` (`clients.*`)
- **Dependencies:** Job + Candidate modules, Elasticsearch

---

## Interview Scheduling

- **Controllers:**
  - `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` 🔴
  - `OLD_InterviewScheduleController.php`, `OLD_2_InterviewScheduleController.php` ⚠️
- **Services:** `InterviewRemainderMail.php`, `ZoomService.php`, `CalendarService.php`
- **Models:** `InterviewSchedule`, `InterviewScheduleEmployee`,
  `OpenworkspaceInterviewSchedule`, `MeetingSchedule`, `MeetingComment`,
  `ScheduleComments`
- **Jobs/Commands:** `QueueInterviewRemainderMailService`, `InterviewReminderCommand`,
  `SendInterviewReminder`, `SendInterviewReminders` (⚠️ duplicate-looking pair)
- **Observers:** `InterviewScheduleObserver`, `InterviewScheduleEmployeeObserver`,
  `InterviewScheduleCommentObserver`
- **Traits:** `ZoomJWT`
- **ES index:** `ats_interview_tracker_elastic`
- **Routes:** `routes/service.php`, `routes/product.php`
- **Dependencies:** Zoom, Mail, Candidate module, Reverb (notifications)

---

## Search & Indexing (cross-cutting)

- **Services:** `ElasticService.php` (HTTP client wrapper), `ElasticCandidatesIndex`,
  `ElasticBenchCandidatesIndex`, `ElasticJobIndex`, `ElasticJobListIndex`,
  `ElasticCaompanyIndex` (sic)
- **Jobs:** `ElasticCandidateIndex`, `ElasticBenchCandidateIndex`, `ElasticJobsIndex`,
  `ElasticCompanyIndex`
- **Commands:** all `app/Console/Commands/Index*ToElasticsearch.php`
- **Models (ES-mirrors):** `ElasticCandidate`, `ElasticJobs`, `ElasticCampany`,
  `ElasticCampanyCoins`, `ElasticCommericials`, `ElasticCommericialCountries`,
  `ElasticTdIndustries`, `ElasticJobApplicationSkills`, `ElasticJobMetronicsSkills`,
  `ElasticRecommendationRelation`, `ElasticStaffingCompanyRelation`
- **Config:** `config/elasticsearch.php`, `.env` `ELASTIC_*` (60+ index names)
- **Dependencies:** Every list/search/dashboard screen

---

## Billing / Packages / Coins

- **Controllers:**
  - `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` 🔴
  - `app/Http/Controllers/SuperAdmin/CustomPackageController.php` 🔴
  - `PaymentController.php`, `PhonePePaymentController.php`,
    `StripeWebhookController.php`, `RazorpayWebhookController.php`, `PaypalIPNController.php`
- **Services:** `AutoRenewFreePackage.php`, `PhonePeService.php`
- **Models:** `Package`, `PackageDetail`, `PackageFeature`, `CompanyPackage`,
  `CustomPackage` (via controller), `Order`, `PackageOrderItem`, `Subscription`,
  `StripeInvoice`, `RazorpayInvoice`, `RazorpaySubscription`, `PaypalInvoice`,
  `PaymentSetting`
- **Traits:** `StripeSettings`, `PaymentSettings`
- **Middleware:** `Package`, `CustomPackage` (feature gating per plan)
- **Commands:** `LicenceExpire`, `NotifyFeaturedEnd`
- **ES indices:** `ats_coin_*`, `ats_carts`, `ats_billing_address_*`, `ats_payment_settings`
- **Routes:** `routes/superadmin.php`, `routes/web.php` (webhooks)
- **Dependencies:** Stripe/Paddle/Razorpay/PhonePe/PayPal, `Company`

---

## Notifications / Mail / Messaging

- **Services:** `MailService`, `NotificationMailService`, `MassMailService`
- **Jobs:** `SendEmailJob`, `QueueMailService`, `QueueMassMailService`,
  `QueueNotificationMailService`, `WhatsAppTextSend`
- **Models:** `EmailSetting`, `SmsSetting`, `LinkedInSetting`
- **Traits:** `SmtpSettings`, `SmsSettings`
- **Controllers:** `NotificationController`, `HomeController` (`/whatsapp/*` 🔴)
- **Realtime:** `routes/channels.php`, `app/Events/MessageSent.php`, Reverb
- **ES indices:** `ats_email_*`, `ats_master_notifications`, `ats_whatsapp_count`
- **Dependencies:** Mail (SMTP), WhatsApp (external Node service), Reverb

---

## Super Admin / Platform / CMS

- **Controllers:** `SuperAdmin/*` (company/tenant mgmt, teams, packages),
  `SaasFront/SaasFrontController.php` 🔴, `Front/*`
- **Models:** `Company`, `CompanySetting`, `GlobalSetting`, `ThemeSetting`,
  `LanguageSetting`, `ApplicationSetting`, `SeoDetail`, `FrontCmsHeader`,
  `FrontIconFeature`, `FrontImageFeature`, `FooterMenu`, `FooterSetting`,
  `Module`, `Ticket`, `TicketStatus`
- **Observers:** `CompanyObserver`, `FrontCmsHeaderObserver`,
  `FrontImageFeatureObserver`, `ThemeSettingObserver`, `RoleObserver`, `UserObserver`
- **Middleware:** `SuperAdmin`
- **Routes:** `routes/superadmin.php`, public parts in `routes/web.php`

---

## Auth / RBAC / Users / Org

- **Controllers:** `app/Http/Controllers/Auth/*`, `GoogleController` (Socialite),
  `VerifyMobileController`, team controllers (`*TeamController`)
- **Models:** `User`, `Role`, `Permission`, `RoleUser`, `PermissionRole`,
  `Department`, `Designation`
- **Middleware:** `EnsureOneDeviceLogin`, `RedirectIfAuthenticated`, Spatie
  `role`/`permission`/`role_or_permission`
- **Observers:** `UserObserver`, `RoleObserver`, `DepartmentObserver`, `DesignationObserver`
- **Routes:** `routes/auth.php`, `routes/web.php`
- **Dependencies:** spatie/permission, JWT, Socialite, `Company`
