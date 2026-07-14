# Services Summary (`app/Services/`)

One entry per class. ~2-3 lines: responsibility · inputs → outputs · deps.
**[INFERRED]** from names + observed code; verify method signatures before relying.

### Search / Elasticsearch
- **ElasticService** — Thin HTTP wrapper around Elasticsearch (auth, host from
  `config/elasticsearch.php`). In: index name + query/doc → Out: ES response. Deps: `Http`.
- **ElasticCandidatesIndex** — Builds denormalized candidate docs from
  `job_applications` + `add_job_wise_condidates` and indexes to `ats_candidates_elastic`.
  In: candidate/job id → Out: ES upsert. Deps: `JobApplication`, DB, Cache, ElasticService.
- **ElasticBenchCandidatesIndex** — Same for bench candidates → `ats_candidates_bench_elastic`.
- **ElasticJobIndex** — Indexes a single job → `ats_jobs_elastic`. Deps: `Job`, ElasticService.
- **ElasticJobListIndex** — Builds job-list/search docs (list view) for ES.
- **ElasticCaompanyIndex** (sic) — Indexes companies → `ats_companies_elastic`.

### Mail / Notifications
- **MailService** — Core SMTP send; loads tenant SMTP settings. In: message/recipients
  → Out: sent mail. Deps: `SmtpSettings` trait, `smtp_settings`.
- **NotificationMailService** — Builds + sends notification emails (status/events).
- **MassMailService** — Bulk/campaign email send. In: recipient list/template → Out:
  queued/sent batch.
- **CandidatesMailAndResumeForward** — Forwards candidate + resume by email. Deps: S3, Mail.
- **InterviewRemainderMail** (sic) — Builds interview reminder emails. Deps: InterviewSchedule.

### Jobs / Packages / Lifecycle
- **AutoCloseJobsService** — Closes jobs past expiry/criteria. In: jobs → Out: status
  updates (+ES reindex). Used by `ExpiredJob` command.
- **AutoRenewFreePackage** — Renews free-tier packages on expiry. Deps: Package/CompanyPackage.
- **JobApplicationsNewUploadService** — Parses/bulk-imports candidate spreadsheets into
  `job_applications`. Deps: maatwebsite/excel, `JobApplicationsNewUpload` job.

### Integrations
- **ZoomService** — Creates/manages Zoom meetings for interviews. Deps: `ZoomJWT` trait, Http.
- **CalendarService** — Calendar/ICS or scheduling helper for interviews. **[UNCERTAIN]**
- **GoogleSheetServiceCandidate / GoogleSheetsServiceGet** — Export/import candidates
  to/from Google Sheets. Deps: Google API.
- **PhonePeService** — PhonePe payment init/verify. In: order → Out: payment status. Deps: Http.
- **ChatGptService** — OpenAI call wrapper (parsing/assist). Deps: OPENAI_API_KEY.
- **FaviconService** — Generates/serves tenant favicon assets.
