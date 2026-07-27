# 30 — v1 brief (user-supplied)

> *Intent: preserve the user's v1 product brief exactly as supplied,*
> *including ASCII tree, ratings, and gaps. Treat this as the source*
> *material; everything upstream is v2's response to it.*

Source: `C:\Users\VIBHANSHU JAISWAL.000\Favorites\Downloads\NEW_ Resume Upload_JEROATS.docx`
(tar-archived as zip and parsed via `tar -xf`).

## Rating of v1 brief (as supplied)

| Lens | Score |
|---|---|
| Flow / UX diagram clarity | **8/10** |
| Visual completeness (recovery, retry, cancel) | **8/10** |
| Design spec for engineering sign-off | **3/10** |
| Stakeholder-kickoff document | **6/10** |
| **Overall** | **5/10** — usable as §30 in a real design doc |

## What's there

A 382-paragraph ASCII tree covering:

- Page-load init (ResumeUpload + Parser modules, online/offline/store
  listeners, restore localStorage, restore UUID with validity check)
- Resume selection → upload UUID + idempotency key + epoch token →
  upload pipeline via presigned PUT
- Parallel parser pipeline: validate → detect type → extract → 8 fields
  (name, email, phone, skills, experience, company, education,
  keywords) → populate empty-only inputs
- Upload completion: HEAD, finalize, badge, enable submit
- Recruiter form-edit: manual values always win
- Submit flow: preflight ping → wait → POST candidate
- Two offline recovery flows (network lost/restored), parser retry
  (`Retry-After` + exponential backoff, retry once), refresh recovery,
  cancel, session cleanup, health endpoint

## What's missing (the reason v2 exists)

| Gap | Why it matters at 10K |
|---|---|
| No data model | can't scale; carriers of truth unclear |
| No API contract | can't load-test; error matrix unknown |
| No storage layer | PHP receives every byte = 60 GB/s |
| No queue/job model | parser single point of failure |
| No tenant isolation | tenant-leak at scale = catastrophic |
| No Reverb contract | cross-tab + cross-tenant unstructured |
| No SLAs / caps / timeouts | load spike = failure |
| Persona split absent | recruiter / career / bench diverge |
| Bulk path absent | bulk is a separate surface |
| No security review | file injection / tenant leak unaddressed |
| No error matrix | every checkpoint needs a "what if" list |
| No test plan / rollback | release gate absent |

## The v1 ASCII tree (verbatim from supplied docx)

```
 Enterprise Resume Upload & Parsing Workflow 2.0
 Recruiter Opens "Add Candidate"
 │
 ├── Page Loads
 │   │
 │   ├── Initialize ResumeUpload JS Module
 │   ├── Initialize Parser Module
 │   ├── Register Offline Listener
 │   ├── Register Online Listener
 │   ├── Register Storage Event Listener
 │   ├── Register Retry Manager
 │   ├── Register Upload Progress Events
 │   ├── Register Parser Progress Events
 │   ├── Register Auto-fill Listener
 │   ├── Restore localStorage Upload Session (if exists)
 │   ├── Restore Draft State (future)
 │   ├── Restore Upload UUID (if valid)
 │   └── UI Ready
 │
 └──────────────────────────────────────────────────────────────
 Recruiter Selects Resume
 Resume Selected
 │
 ├── Generate Upload UUID
 ├── Generate Idempotency Key
 ├── Cache File Object
 ├── Cache Metadata
 │     ├── Filename
 │     ├── Size
 │     ├── MIME
 │     ├── Last Modified
 │
 ├── Reset Previous State
 ├── Abort Previous Upload
 ├── Abort Previous Parser
 ├── Increment Epoch Token
 ├── Initialize State Machine
 │
 └── Fire Upload Pipeline
 Upload Pipeline
 Upload Pipeline
 │
 ├── Request Upload Session
 │
 ├── Server Creates / Reuses Session
 │
 ├── Returns
 │     ├── upload_session_uuid
 │     ├── Presigned PUT URL
 │     ├── Final Object Key
 │
 ├── Save upload_session_uuid
 │
 ├── Save UUID to localStorage
 │
 ├── Save UUID to Hidden Input
 │
 └── Begin Upload
 Upload Progress
 Upload Started
 │
 ├── Progress = 0%
 │
 ├── Progress Events
 │
 ├── Update Progress Bar
 │
 ├── Update ETA
 │
 ├── Update Speed
 │
 ├── Update Upload Badge
 │
 ├── Enable Cancel Button
 │
 ├── Recruiter Continues Filling Form
 │
 └── Upload Continues
 Parallel Parser Pipeline
 Runs simultaneously.
 Upload Starts
 │
 └── Start Resume Parser
 │
 ├── Validate File
 ├── Detect Type
 │      ├── PDF
 │      ├── DOC
 │      └── DOCX
 │
 ├── Extract Text
 ├── Extract
 │      ├── Name
 │      ├── Email
 │      ├── Phone
 │      ├── Skills
 │      ├── Experience
 │      ├── Company
 │      ├── Education
 │      └── Keywords
 │
 ├── Build JSON
 │
 ├── Populate Empty Fields
 │
 ├── Never Overwrite Manual Fields
 │
 ├── Emit parseInline Event
 │
 └── Parser Finished
 Upload Completion
 PUT Complete
 │
 ├── Verify Object Exists
 │
 ├── Finalize Upload Session
 │
 ├── Mark Uploaded
 │
 ├── Update Badge
 │
 ├── Hide Cancel Button
 │
 ├── Enable Submit
 │
 └── Wait for Recruiter
 Recruiter Edits Form
 Recruiter
 │
 ├── Changes Name
 ├── Changes Email
 ├── Changes Phone
 ├── Adds Notes
 ├── Changes Salary
 ├── Selects Job
 ├── Changes Skills
 │
 └── Manual Values Always Win
 Submit Flow
 Submit Clicked
 │
 ├── Disable Button
 ├── Show Loader
 ├── Run Preflight Ping
 │
 ├── Server Reachable?
 │      │
 │      ├── NO
 │      │      │
 │      │      ├── Show
 │      │      │   "Server temporarily unavailable"
 │      │      ├── Wait
 │      │      └── Retry
 │      │
 │      └── YES
 │
 ├── Upload Completed?
 │      │
 │      ├── NO
 │      │
 │      ├── Wait Until Ready
 │      │
 │      └── Continue
 │
 ├── Parser Completed?
 │      │
 │      ├── Wait
 │      ├── Timeout
 │      └── Continue
 │
 └── POST Candidate
 Server Processing
 Receive Candidate
 │
 ├── Validate
 ├── Verify Upload Session
 ├── Verify Object Exists
 ├── Link Resume
 ├── Link Candidate
 ├── Queue Background Jobs
 ├── Audit Log
 ├── Commit Transaction
 │
 └── Return Success
 Network Failure Recovery
 Network Lost
 │
 ├── Browser Offline Event
 │
 ├── Badge
 │     "Network Lost"
 │
 ├── Abort Upload
 ├── Abort Parser
 ├── Pause Progress
 ├── Save State
 ├── Wait
 │
 └── Online Event
 Connection Restored
 Browser Online
 │
 ├── Verify Server Reachable
 │
 ├── Restore Upload UUID
 │
 ├── Resume Upload
 │
 ├── Resume Parser
 │
 ├── Continue Progress
 │
 └── Finish Normally
 Upload Retry
 Retry Upload
 │
 ├── Same Upload UUID
 ├── Same Idempotency Key
 ├── Same Session
 ├── Same Object
 ├── Continue Upload
 │
 └── Never Duplicate
 Parser Retry
 Retry Parser
 │
 ├── Only if Parser Failed
 ├── Honor Retry-After
 ├── Exponential Backoff
 ├── Retry Once
 │
 └── Continue
 Submit Network Failure
 POST Failed
 │
 ├── Detect Network Failure
 │
 ├── Verify Upload Session
 │
 ├── Candidate Already Created?
 │      │
 │      ├── YES
 │      │
 │      └── Success
 │
 │
 └── NO
 │
 ├── Enable Retry
 └── Recruiter Clicks Submit Again
 Browser Refresh
 Refresh
 │
 ├── Read localStorage
 ├── Read Upload UUID
 ├── Verify Session
 ├── Restore State
 ├── Restore Progress
 ├── Continue Upload
 │
 └── Continue Editing
 Cancel Upload
 Cancel
 │
 ├── Abort Upload
 ├── Abort Parser
 ├── Clear Progress
 ├── Remove localStorage
 ├── Clear Hidden UUID
 ├── Reset State
 ├── Hide Progress
 ├── Hide Cancel Button
 │
 └── Ready For New Resume
 Session Cleanup
 Scheduler
 │
 ├── Find Expired Sessions
 ├── Find Failed Sessions
 ├── Find Orphans
 ├── Mark Tombstone
 ├── Cleanup R2
 ├── Cleanup Cache
 ├── Cleanup Temp State
 │
 └── Metrics
 Monitoring
 Health Endpoint
 │
 ├── MySQL
 ├── Redis
 ├── Queue
 ├── R2
 ├── Upload Sessions
 ├── Parser
 ├── Scheduler
 │
 └── Return Status
```
