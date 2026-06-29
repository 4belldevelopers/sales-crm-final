# Models Summary (`app/Models/`, 86 models)

Grouped by domain. ~1 line each: role · key relations. **[INFERRED]** columns — schema
is in the live DB (see [DB_SCHEMA.md](../../docs/DB_SCHEMA.md)). Tenant key: `company_id`.

### Tenancy / org / auth
- **Company** — Tenant root; applies `company` global scope; Cashier `Billable`. hasMany clients/applications/packages.
- **User** — App user; belongsTo Company; Spatie roles; `reports_to_id`, `created_by`.
- **Role / Permission / RoleUser / PermissionRole** — Custom + Spatie RBAC bridge.
- **Department / Designation** — Org structure under a company.
- **Module** — Feature/module flag record. **[UNCERTAIN]**

### Jobs / requisitions
- **Job** — Job posting/requisition; belongsTo JobCategory/JobLocation; hasMany JobSkill; m:m Question.
- **OpenworkspaceJob** — Variant of Job for "openworkspace" flow. **[UNCERTAIN]**
- **JobCategory / JobLocation / JobSkill / Skill / JobQuestion / Question** — Job taxonomy & screening Q's.
- **client_city_jobs / jobCountry / client_*_jobs** (lowercase) — geo/client pivot tables for jobs.

### Candidates / applications (core)
- **JobApplication** — Central candidate-in-pipeline; belongsTo Job; morph Documents; hasMany answers. ⚠️ company scope commented out.
- **BenchApplication** — Bench (available) candidate.
- **OfferJobApplication** — Offer/placement record.
- **AddJobWiseCondidate** (sic) — Candidate↔job matching pivot (`skill_match`, `existing`, `placed_end_date`).
- **JobApplicationAnswer** — Candidate answers to JobQuestion.
- **ApplicantNote / Comment / StickyNote / TodoItem** — Notes/annotations.
- **CandidateStatus / ApplicationStatus** — Pipeline status definitions.
- **Document** — Polymorphic file (resume etc.), S3 hashname.
- **Onboard / OnboardFiles** — Onboarding records + files.

### Clients / vendor marketplace
- **Client** — Client/customer of an agency; belongsTo Company.
- **ClientFeedback / Report** — Feedback & reporting.
- **ElasticStaffingCompanyRelation / ElasticRecommendationRelation** — ES-mirror relation models.

### Interviews
- **InterviewSchedule** — Interview event; hasMany InterviewScheduleEmployee; m:m User.
- **InterviewScheduleEmployee** — Interview attendee pivot.
- **OpenworkspaceInterviewSchedule / MeetingSchedule / MeetingComment / ScheduleComments** — meeting variants & comments.

### Billing / packages / coins
- **Package / PackageDetail / PackageFeature / CompanyPackage** — Plans & assignments.
- **Order / PackageOrderItem / Subscription** — Orders & subscriptions.
- **StripeInvoice / RazorpayInvoice / RazorpaySubscription / PaypalInvoice** — Gateway records.
- **PaymentSetting** — Per-tenant payment config.
- **ElasticCampanyCoins** (sic) — Coins economy ES mirror.

### Settings / CMS / front
- **GlobalSetting / CompanySetting / ApplicationSetting / ThemeSetting / LanguageSetting** — Config.
- **EmailSetting / SmsSetting / LinkedInSetting** — Channel config.
- **FrontCmsHeader / FrontIconFeature / FrontImageFeature / FooterMenu / FooterSetting / SeoDetail** — Marketing CMS.
- **Ticket / TicketStatus** — Support tickets.

### Master / geo / misc
- **Country / Currency / NoticePeriod / TdIndustry** — Master data.
- **ElasticCandidate / ElasticJobs / ElasticCampany / ElasticCommericials / ElasticCommericialCountries / ElasticTdIndustries / ElasticJobApplicationSkills / ElasticJobMetronicsSkills** — ES-mirror models (read side).
- ⚠️ Non-class files in `app/Models/`: `constants.php`, `error_log` — not models; ignore.
