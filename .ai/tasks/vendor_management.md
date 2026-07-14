# Task Memory — Vendor / Agency Marketplace

## Purpose
Manage **clients** (an agency's customers) and the **agency↔client / agency↔agency
relationships**: sending requirements, requesting candidates, recommendations, and
staffing-company relations. See memory note [[agency-marketplace-views]] for UI files.

## Workflows
- Manage clients, contacts, addresses, client requisitions.
- Send a requirement / request candidates between parties.
- Process candidate requests asynchronously (`ProcessCandidateRequest`).
- Maintain relation/recommendation indices in ES.
See [DATA_FLOW.md](../../docs/DATA_FLOW.md) §3.

## Business rules (verify before changing)
- Relations are tenant-scoped; cross-tenant visibility is intentional only via the
  relation tables (`staffing_company_relation`, `recommendation_relation`,
  `rec_candidate_assign`, `add_job_wise_condidates`).
- Request/relation changes must update ES request indices
  (`ats_candidates_request`, `ats_jobs_request`, `ats_clients_request`,
  `ats_client_and_agency_relation_index`).
- Skill-search / recommendation ranking is business logic — confirm before edits.

## Common files
- Controllers (methods inside): `ServiceCompany/ServiceCompanyClientController.php` 🔴,
  `ServiceCompany/ServiceCompanyJobsController.php` 🔴 (partner listing, requisition data)
- Jobs: `ProcessCandidateRequest`
- Models: `Client`, `ClientFeedback`, `Report`, `ElasticStaffingCompanyRelation`,
  `ElasticRecommendationRelation`
- Commands: `IndexStaffingCompanyRelationToElasticsearch`,
  `IndexRecommendationRelationToElasticsearch`
- Tables: `clients`, `client_location*`, `client_contact_info*`, `client_city_jobs`,
  `recommendation_relation`, `staffing_company_relation`, `commericials*`
- Routes: `routes/service.php` (`clients.*`, `jobs.spartnerlisting`, `*_request_data`)

## Gotchas
- Find UI via memory [[agency-marketplace-views]] — `resources/views` too big to grep.
- Spellings `commericial`, `condidate` are intentional.
- Many `*_request_data` page routes have `*_ajax` data twins (DataTables).
