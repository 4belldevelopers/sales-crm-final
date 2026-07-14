# Task Memory — Job / Requisition Management

## Purpose
Create and manage **jobs/requisitions** (`Job`), including custom requisitions,
client requisitions, job questions, skills, categories, locations, and the public
job board.

## Workflows
- Create/edit job → MySQL → ES index (`ats_jobs_elastic`, list index).
- Custom vs client requisitions (different sources/owners).
- Attach screening `Question`s (pivot `job_questions`) and `JobSkill`s.
- Auto-close/expire jobs (scheduled).
- Public listing/detail via Front controllers.

## Business rules (verify before changing)
- Job belongs to `company_id` (and often a `client_id`).
- Status transitions drive visibility and auto-close (`AutoCloseJobsService`, `ExpiredJob`).
- Job changes must re-index ES (Observer/Job) for trackers/search to reflect them.
- Skills/questions linked via pivots — keep pivots consistent on edit/delete.

## Common files
- Controllers: `ServiceCompany/ServiceCompanyJobsController.php` 🔴,
  `ProductCompany/ProductCompanyJobsController.php` 🔴,
  `Admin/AdminJobsController.php` 🔴, `Front/FrontJobsController.php`
- Services: `ElasticJobIndex.php`, `ElasticJobListIndex.php`, `AutoCloseJobsService.php`
- Models: `Job`, `JobCategory`, `JobLocation`, `JobSkill`, `JobQuestion`, `Question`,
  `Skill`, `OpenworkspaceJob`
- Jobs/Commands: `ElasticJobsIndex`, `ExpiredJob`, `IndexJobsToElasticsearch`
- Observers: `JobSkillObserver`, `JobQuestionObserver`, `JobCategoryObserver`,
  `JobLocationObserver`, `QuestionObserver`, `SkillObserver`
- Routes: `routes/service.php` (`jobs.*`), `routes/product.php`, `routes/web.php` (front)

## Gotchas
- ⚠️ `old_ProductCompanyJobsController.php` is dead — ignore.
- `jobs` table here = job postings, distinct from the queue table (`custom_queue_jobs`).
- DataTables list endpoints come in page + `*_ajax` pairs (`all_data` / `all_data_ajax`).
