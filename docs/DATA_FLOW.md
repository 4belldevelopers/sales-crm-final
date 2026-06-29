# Data Flows — Major Workflows

> Step-by-step traces of the main workflows. Use these to find the right files fast.
> **[INFERRED]** steps are deduced from naming + observed code, not line-traced end to
> end — verify the exact method before editing.

Common pattern across all write flows:
**Controller → (validate via FormRequest) → write MySQL → fire Observer / dispatch
Job → async index to Elasticsearch → (optional) queue mail/notification → respond.**

---

## 1. Candidate / Application Creation

```
HTTP POST (service_company/... or product_company/...)
  └─> *JobApplicationController@store
        ├─ validate (app/Http/Requests/*)
        ├─ create JobApplication (MySQL)              [Models/JobApplication.php]
        ├─ resume file → dispatch UploadResumeToS3    [Jobs/UploadResumeToS3.php] → S3
        ├─ create Document (morph, name="Resume")     [Observers/DocumentObserver.php]
        ├─ JobApplicationObserver fires               [Observers/JobApplicationObserver.php]
        │     └─ dispatch ElasticCandidateIndex       [Jobs/ElasticCandidateIndex.php]
        │           └─ ElasticCandidatesIndex service builds doc → ES ats_candidates_elastic
        └─ (optional) queue mail to candidate/recruiter
Response: redirect / DataTables refresh
```
Key files: `ElasticCandidatesIndex.php` (column→ES mapping), `add_job_wise_condidates`
pivot for job matching.

---

## 2. Resume Parsing & Upload

```
Upload (single or bulk) 
  ├─ smalot/pdfparser / phpword extracts text          [composer deps]
  ├─ parsed fields → JobApplication columns (skills, exp, ctc...)  [INFERRED]
  ├─ dispatch UploadResumeToS3 / UploadResumeToTestS3 / UploadResumeToTestRTwo (queued)
  │     └─ store file on S3 (file.jero.works), save hashname on Document
  └─ trigger ES candidate (re)index
```
Bulk path: `JobApplicationsNewUpload` job + `JobApplicationsNewUploadService`
(spreadsheet import via maatwebsite/excel). **[INFERRED]** ChatGptService may assist
parsing/normalization.

---

## 3. Vendor / Agency Submission (marketplace)

```
Service company sources/sends candidates to a client/agency requirement
  ├─ Client + requisition created                      [ServiceCompanyClientController, ServiceCompanyJobsController]
  ├─ candidate linked via add_job_wise_condidates / rec_candidate_assign
  ├─ relation rows: staffing_company_relation, recommendation_relation
  ├─ dispatch ProcessCandidateRequest (queued)         [Jobs/ProcessCandidateRequest.php]
  └─ index to ES: ats_candidates_request / ats_jobs_request / ats_clients_request /
                  ats_client_and_agency_relation_index
```
Reindex commands: `IndexStaffingCompanyRelationToElasticsearch`,
`IndexRecommendationRelationToElasticsearch`.
See memory [[agency-marketplace-views]] for the UI files.

---

## 4. Interview Scheduling

```
POST schedule interview                                 [InterviewScheduleController]
  ├─ create InterviewSchedule (+ InterviewScheduleEmployee per attendee)
  ├─ ZoomService creates meeting (ZoomJWT trait)        [Services/ZoomService.php]
  ├─ InterviewScheduleObserver → index ats_interview_tracker_elastic
  ├─ queue confirmation mail                            [QueueInterviewRemainderMailService]
  └─ broadcast notification (Reverb)                    [INFERRED]
Reminders (scheduled):
  console.php → InterviewReminderCommand / SendInterviewReminder(s)
    └─ InterviewRemainderMail service → queued mail
```
⚠️ Legacy: `OLD_InterviewScheduleController`, `OLD_2_...` exist — ignore, use the
non-prefixed controller.

---

## 5. Search Indexing (write → ES) & Querying (read ← ES)

**Indexing (keep ES in sync):**
```
Model change (create/update/delete)
  └─ Observer (app/Observers/*) 
        └─ dispatch Elastic*Index job
              └─ Elastic*Index service builds denormalized doc
                    └─ ElasticService (HTTP) → PUT/POST to ES index
Full rebuild: artisan Index*ToElasticsearch  (backfill from MySQL)
```
**Querying (list/dashboard reads):**
```
DataTables AJAX / page load
  └─ Controller builds ES query (bool/must/filter, from/size)
        └─ ElasticService → GET ES → hits
              └─ shape rows → DataTables JSON / Blade
```
Matching/ranking (e.g. `skill_match`, boosting via `boostkeywords`) is computed when
building docs/queries inside `ElasticCandidatesIndex` / controllers. **[BUSINESS LOGIC —
do not alter without confirmation.]**

---

## 6. Placement Pipeline

```
Candidate progresses through master_status / CandidateStatus stages
  ├─ status change on JobApplication (master_status_id, status, column_priority)
  ├─ ApplicationStatusObserver / JobApplicationObserver → re-index ES
  ├─ on placement: OfferJobApplication created; bench_assign / placed_end_date set
  └─ ES: ats_bench_placed_elastic, ats_bench_assign updated
```

---

## 7. Billing / Subscription

```
Checkout (Stripe/Razorpay/PhonePe/PayPal/Paddle)
  ├─ create Order / PackageOrderItem
  ├─ provider redirect/charge                           [PaymentController, PhonePeService]
  ├─ webhook/IPN callback                               [*WebhookController, PaypalIPNController]
  │     └─ verify signature → mark paid → assign CompanyPackage → set licence_expire_on
  └─ coins economy updated in ES (ats_coin_*) where applicable
Scheduled: LicenceExpire, NotifyFeaturedEnd, AutoRenewFreePackage
```

---

## 8. Notifications & Mail

```
Trigger (status change, interview, mass campaign)
  └─ dispatch Queue*MailService / SendEmailJob (Redis queue, Horizon)
        └─ MailService / NotificationMailService / MassMailService
              ├─ load smtp_settings (SmtpSettings trait)  ⚠️ queried repeatedly
              └─ send via SMTP; WhatsAppTextSend → external WhatsApp service
In-app: NotificationController + Reverb broadcast (MessageSent event)
```
