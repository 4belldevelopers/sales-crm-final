# Jobs Summary (`app/Jobs/`)

Queued background jobs (Redis/Horizon, `default` queue). ~2 lines each.
**[INFERRED]** from names + sizes; verify `handle()` before relying.

### Elasticsearch indexing
- **ElasticCandidateIndex** — Async index/update one candidate doc → `ats_candidates_elastic`.
  Deps: ElasticCandidatesIndex service.
- **ElasticBenchCandidateIndex** — Async bench candidate index → `ats_candidates_bench_elastic`.
- **ElasticJobsIndex** — Async job index → `ats_jobs_elastic`. Deps: ElasticJobIndex service.
- **ElasticCompanyIndex** — Async company index → `ats_companies_elastic`.

### Files / Resumes
- **UploadResumeToS3** — Uploads a candidate resume file to S3, saves Document hashname.
  In: file + application id → Out: S3 object + Document row.
- **UploadResumeToTestS3 / UploadResumeToTestRTwo** — Test/alternate S3/R2 upload variants.
  ⚠️ likely experimental; confirm which is live before editing.

### Imports
- **JobApplicationsNewUpload** — Processes a bulk candidate spreadsheet import row batch.
  Deps: JobApplicationsNewUploadService.

### Vendor / requests
- **ProcessCandidateRequest** — Handles agency/vendor candidate-request workflow + ES
  request indices (`ats_candidates_request` etc.).

### Mail / messaging (queued wrappers)
- **SendEmailJob** — Generic queued email send. Deps: MailService.
- **QueueMailService** — Queues a single transactional mail. Deps: MailService.
- **QueueMassMailService** — Queues a bulk/campaign send. Deps: MassMailService.
- **QueueNotificationMailService** — Queues notification emails. Deps: NotificationMailService.
- **QueueCandidatesMailAndResumeForwardMailService** — Queues candidate+resume forward.
  Deps: CandidatesMailAndResumeForward.
- **QueueInterviewRemainderMailService** (sic) — Queues interview reminder emails.
  Deps: InterviewRemainderMail.
- **WhatsAppTextSend** — Sends a WhatsApp message via external Node service. Deps: Http.
