# GOD-Level ATS — Feature Vision & Build Plan

> What a best-in-class Applicant Tracking System looks like, mapped to what JERO already
> has vs. what to build. Goal: rich, fast, AI-powered, enterprise ATS that scales to 10K
> companies. Pair with [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md), [SCALABILITY.md](SCALABILITY.md),
> [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md).

Legend: ✅ exists · 🟡 partial · 🆕 build.

---

## 1. Sourcing & Candidate Acquisition
- ✅ Manual add, bulk import (`JobApplicationsNewUpload`), resume upload to S3.
- 🟡 Resume parsing (`smalot/pdfparser`, phpword) → **🆕 AI parsing** (LLM extraction:
  skills, exp, education, normalized titles, seniority) with confidence scores.
- 🆕 **Career site / branded job board** per tenant (you have Front controllers — elevate
  to a fast, edge-cached, SEO-optimized site).
- 🆕 **Source integrations**: LinkedIn (`LinkedInSetting` seed), job boards, referrals,
  chrome-extension sourcing, email-to-apply.
- 🆕 **Talent pools / CRM**: nurture passive candidates, tags, segments, re-engagement.

## 2. Candidate Experience
- 🆕 **Frictionless apply** (parse resume → prefill, mobile-first, < 60s).
- 🆕 **Candidate portal**: status visibility, self-schedule interviews, document upload.
- 🆕 **Communication**: email + WhatsApp (`WhatsAppTextSend` ✅) + SMS, templated,
  2-way, with consent.
- 🆕 **Chatbot** (AI screening Q&A, FAQ, scheduling).

## 3. Pipeline & Workflow (core ATS)
- ✅ Pipeline statuses (`master_status_id`, `CandidateStatus`, `column_priority`),
  trackers (CV/App), bench.
- 🟡 **🆕 Kanban pipeline** drag-drop (push updates via Reverb, optimistic UI).
- 🆕 **Configurable stages per job/tenant** + **automation/workflow rules** ("when moved
  to Interview → email + create task + notify").
- 🆕 **Collaboration**: @mentions, scorecards, structured feedback, comparison view
  (extend `Comment`, `ApplicantNote`, `ClientFeedback`).
- 🆕 **Duplicate detection & merge** (you track `duplicate` — make it intelligent).

## 4. Interviews & Assessments
- ✅ Interview scheduling + Zoom (`InterviewScheduleController`, `ZoomService`), reminders.
- 🆕 **Self-scheduling** (candidate picks from interviewer availability — calendar sync,
  `CalendarService` seed).
- 🆕 **Interview kits / scorecards / structured interviews**; panel coordination.
- 🆕 **Assessments**: coding tests, skills tests, video interviews, integrations.
- 🆕 **AI interview notes/summaries** (transcribe → summarize → suggested score).

## 5. Offers, Placement & Onboarding
- ✅ Offer stage (`OfferJobApplication`), onboarding (`Onboard`, `OnboardFiles`).
- 🆕 **Offer management**: approval chains, e-sign, offer letters (you have dompdf/phpword),
  versioning.
- 🆕 **Onboarding workflows**: checklists, document collection, e-sign, day-1 readiness.
- 🆕 **Placement tracking & guarantees** (fall-off window) — ties to marketplace settlement.

## 6. AI & Intelligence (the GOD layer)
- 🟡 `ChatGptService` seed → 🆕 full AI suite:
  - **Semantic matching** (vector/kNN search in ES) candidate↔job.
  - **AI resume parsing & enrichment**, JD generation, boolean-search builder.
  - **Ranking & shortlisting** with explainability (why this candidate).
  - **Summaries** (candidate, interview, pipeline health), **next-best-action** nudges.
  - **Bias controls** (audit AI suggestions for fairness/EEO).

## 7. Analytics & Reporting
- 🟡 Dashboards exist → 🆕 **pre-computed analytics** (time-to-fill, source effectiveness,
  funnel conversion, recruiter productivity, diversity/EEO, SLA, revenue/placements).
- 🆕 **Custom report builder + exports** (you have maatwebsite/excel), scheduled reports.
- 🆕 **Warehouse-backed analytics** at scale (see [SCALABILITY.md](SCALABILITY.md)).

## 8. Collaboration & Org
- ✅ Teams, roles/permissions (Spatie), departments/designations.
- 🆕 **Granular per-object permissions** (fix IDOR — [SECURITY.md](SECURITY.md) SEC-07),
  approval workflows, hiring teams per job.

## 9. Compliance & Trust
- 🆕 **GDPR/DPDP/EEO/OFCCP**: consent, retention/erasure, audit logs of PII access,
  anonymized screening, data residency. Foundational at 100M records.

## 10. Platform & Extensibility
- 🆕 **Public REST/GraphQL API + webhooks** (versioned — see [API_CONTRACTS.md](API_CONTRACTS.md))
  for integrations & the marketplace partner API.
- 🆕 **Marketplace of integrations** (HRIS, calendars, assessments, background checks,
  payroll), **Zapier/automation**, **mobile app** (recruiter + candidate).
- 🆕 **White-label / per-tenant branding & theming** (you have ThemeSetting/CMS seeds).

---

## Architect's summary — build priorities
| Priority | Theme | Why |
|---|---|---|
| **P0** | Harden foundation (tenant isolation, env→config, XSS, secrets) | nothing rich matters if the base leaks/breaks ([BUG_REPORT.md](BUG_REPORT.md), [SECURITY.md](SECURITY.md)) |
| **P0** | Fast, dense, responsive UI (Metronic, ≤14px, fits all laptops) | recruiters live in this UI all day ([UI_UX_STANDARDS.md](UI_UX_STANDARDS.md)) |
| **P1** | Kanban pipeline + automation + collaboration (scorecards) | core ATS daily value; Reverb push |
| **P1** | AI parsing + semantic matching + summaries | the differentiator; reuse `ChatGptService`/ES |
| **P2** | Self-scheduling, assessments, offer/onboarding workflows | depth that wins enterprise deals |
| **P2** | Analytics (pre-computed) + report builder | proves ROI, drives retention |
| **P3** | Public API/webhooks, integrations, mobile, white-label | platform & ecosystem |
| **ongoing** | Compliance, audit, performance/scale | required to reach & hold 10K tenants |

**How to build it (process):** every feature = vertical slice (Route + FormRequest +
Action/Service + ES sync + Observer + Reverb event + `x-` Blade component + test),
extracted out of the god controllers (strangler pattern), cache-first, push-not-poll,
behind a feature flag. See [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md) §5 and the
AI doctrine §7.

> One line: **Turn the existing solid pipeline ATS into an AI-matched, automation-driven,
> collaborative, compliant, API-extensible platform — built as tested vertical slices on
> a hardened, fast, dense Metronic UI.**
