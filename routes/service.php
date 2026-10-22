<?php

use App\Http\Controllers\Admin\AdminAccountSettingsController;
use App\Http\Controllers\ServiceCompany\ApplicantNoteController;
use App\Http\Controllers\ServiceCompany\ApplicationSettingsController;
use App\Http\Controllers\ServiceCompany\AuthCustomPackageController;
use App\Http\Controllers\ServiceCompany\CompanySettingsController;
use App\Http\Controllers\ServiceCompany\InterviewScheduleController;
use App\Http\Controllers\ServiceCompany\ManageRolePermissionController;
use App\Http\Controllers\ServiceCompany\ManageSubscriptionController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyAccountSettingsController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyApplicationOfferController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyBenchCandidatesController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyCompanyController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyConsultancyController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyCustomreportController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyCvTrackerController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyCvTrackerV2Controller;
use App\Http\Controllers\ServiceCompany\ServiceCompanyDashboardApiController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyDashboardController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobCategoryController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobOnboardController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobTrackerController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyLinkedInSettingsController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyProfileController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyProfileV2ApiController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyReportController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyRolesPermissionController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyStickyNotesController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTeamController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyThemeSettingsController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyTodoItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    // ########################################################################### SERVICE COMPANY ######################################################################
    Route::prefix('service_company')->name('service_company.')->group(function () {
        Route::group(['middleware' => ['EnsureOneDeviceLogin']], function () {

            // ─── Resume pre-signed upload pipeline (returns JSON) ──────────────────
            Route::prefix('resume')->name('resume.')->group(function () {
                // v1 — legacy (keep for backward-compat)
                Route::post('presign', [\App\Http\Controllers\ServiceCompany\ResumeUploadController::class, 'presign'])->name('presign');
                Route::post('store', [\App\Http\Controllers\ServiceCompany\ResumeUploadController::class, 'store'])->name('store');
                Route::get('{id}/status', [\App\Http\Controllers\ServiceCompany\ResumeUploadController::class, 'status'])->name('status');
                Route::post('{id}/retry', [\App\Http\Controllers\ServiceCompany\ResumeUploadController::class, 'retry'])->name('retry');

                // v2 — session-based upload (feature-flagged; all OFF by default)
                Route::prefix('v2/sessions')->name('v2.sessions.')->group(function () {
                    Route::post('/', [\App\Http\Controllers\ServiceCompany\ResumeUploadSessionController::class, 'store'])
                        ->middleware('throttle:resume-presign')
                        ->name('store');
                    Route::get('{uuid}', [\App\Http\Controllers\ServiceCompany\ResumeUploadSessionController::class, 'show'])
                        ->middleware('throttle:resume-status')
                        ->name('show');
                    Route::post('{uuid}/finalize', [\App\Http\Controllers\ServiceCompany\ResumeUploadSessionController::class, 'finalize'])
                        ->middleware('throttle:resume-finalize')
                        ->name('finalize');
                    Route::delete('{uuid}', [\App\Http\Controllers\ServiceCompany\ResumeUploadSessionController::class, 'cancel'])
                        ->name('cancel');
                });
            });
            // ──────────────────────────────────────────────────────────────────────

            // ─── V2 Dashboard JSON API (Phase 4) — compact Redis-cached JSON responses ──
            Route::prefix('api/v2/service-company/dashboard')->name('api.v2.dashboard.')->group(function () {
                Route::get('kpis', [ServiceCompanyDashboardApiController::class, 'kpis'])->name('kpis');
                Route::get('leaderboards', [ServiceCompanyDashboardApiController::class, 'leaderboards'])->name('leaderboards');
                Route::get('interviews', [ServiceCompanyDashboardApiController::class, 'interviews'])->name('interviews');
                Route::post('cache/invalidate', [ServiceCompanyDashboardApiController::class, 'invalidateCache'])->name('cache.invalidate');
            });

            // ─── V2 Profile JSON API — high performance cached recruiter profile endpoints ─
            Route::prefix('api/v2/service-company/profile')->name('api.v2.profile.')->group(function () {
                Route::get('kpis', [ServiceCompanyProfileV2ApiController::class, 'kpis'])->name('kpis');
                Route::get('leaderboard', [ServiceCompanyProfileV2ApiController::class, 'leaderboard'])->name('leaderboard');
                Route::get('ranks', [ServiceCompanyProfileV2ApiController::class, 'ranks'])->name('ranks');
                Route::get('graphs', [ServiceCompanyProfileV2ApiController::class, 'graphs'])->name('graphs');
                Route::get('list-items', [ServiceCompanyProfileV2ApiController::class, 'listItems'])->name('list-items');
                Route::post('cache/invalidate', [ServiceCompanyProfileV2ApiController::class, 'invalidateCache'])->name('cache.invalidate');
            });
            // ─────────────────────────────────────────────────────────────────────────

            // ─── ATS Candidate Drawer God-Level 0-MySQL-Read Architecture Routes (docs/drawer_candidate.md) ─
            Route::prefix('job-applications')->name('job-applications.')->group(function () {
                Route::get('{id}/core-json', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'coreJson'])->name('core-json');
                Route::get('{id}/interviews', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'interviews'])->name('interviews');
                Route::get('{id}/questions', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'questions'])->name('questions');
                Route::get('{id}/timeline', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'timeline'])->name('timeline-api');
                Route::get('{id}/resume-meta', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'resumeMeta'])->name('resume-meta-api');
                Route::post('{id}/mutate', [\App\Http\Controllers\ServiceCompany\CandidateDrawerApiController::class, 'mutate'])->name('mutate');
            });
            // ─────────────────────────────────────────────────────────────────────────

            // ─── ATS Client Drawer Candidates Tab 0-MySQL-Read Architecture Routes ───
            Route::get('clients/{clientId}/candidate-version', [\App\Http\Controllers\ServiceCompany\CandidateVersionApiController::class, 'getVersion'])->name('clients.candidate-version');
            Route::get('client-candidate-list', [\App\Http\Controllers\ServiceCompany\ClientCandidateDrawerApiController::class, 'clientCandidateList'])->name('job-applications.client-candidates');
            Route::get('direct-client-candidate-list', [\App\Http\Controllers\ServiceCompany\ClientCandidateDrawerApiController::class, 'directclientCandidateList'])->name('job-applications.direct-client-candidates');
            // ─────────────────────────────────────────────────────────────────────────

            Route::group(['middleware' => ['custom-package']], function () {
                Route::get('/dashboard', [ServiceCompanyDashboardController::class, 'index'])->name('dashboard');
                Route::get('meetingstoreScheduleList', [ServiceCompanyDashboardController::class, 'meetingScheduleList'])->name('dashboard.meeting-schedule-list');
                Route::get('meeting-comment/{id}', [ServiceCompanyDashboardController::class, 'meetingComment'])->name('dashboard.meeting-comment');
                Route::post('meeting-comment-new/{id?}', [ServiceCompanyDashboardController::class, 'meetingcommentnew'])->name('dashboard.meeting-comment-new');
                Route::get('/search-cities', function (Request $request) {
                    $query = $request->input('q');
                    $cities = DB::table('city_list')->where('city', 'LIKE', "%$query%")->limit(10)->get(['id', 'city']);

                    return response()->json($cities);
                });

                // #############       	START CLIENT MANAGEMENT
                Route::get('clients/industries-bundle', [ServiceCompanyClientController::class, 'industriesBundle'])->name('clients.industriesBundle');
                Route::get('clients/location-facets', [ServiceCompanyClientController::class, 'locationFacets'])->name('clients.locationFacets');
                Route::get('clients/active-projection', [ServiceCompanyClientController::class, 'activeClientProjection'])->name('clients.activeClientProjection');
                Route::get('clients/dcontactList', [ServiceCompanyClientController::class, 'dcontactList'])->name('clients.dcontactList');
                Route::get('clients/data', [ServiceCompanyClientController::class, 'data'])->name('clients.data');
                Route::get('clients/all_clients_data', [ServiceCompanyClientController::class, 'all_clients_data'])->name('clients.all_clients_data');
                Route::get('clients_ajax/all_clients_data', [ServiceCompanyClientController::class, 'all_clients_data_ajax'])->name('clients.all_clients_data_ajax');
                Route::get('clients/contactdata', [ServiceCompanyClientController::class, 'contactdata'])->name('clients.contactdata');
                Route::get('clients/client_direct_data', [ServiceCompanyClientController::class, 'client_direct_data'])->name('clients.client_direct_data');
                Route::get('client_direct_ajax/client_direct_data', [ServiceCompanyClientController::class, 'client_direct_data_ajax'])->name('clients.client_direct_data_ajax');
                Route::get('clients/custom_client_data', [ServiceCompanyClientController::class, 'custom_client_data'])->name('clients.custom_client_data');
                Route::get('clients_custom_ajax/custom_client_data', [ServiceCompanyClientController::class, 'custom_client_data_ajax'])->name('clients.custom_client_data_ajax');
                Route::get('clients/client_request_data', [ServiceCompanyClientController::class, 'client_request_data'])->name('clients.client_request_data');
                Route::match(['GET', 'POST'], 'clients/request_update', [ServiceCompanyClientController::class, 'request_update'])->name('clients.request_update');
                Route::post('clients/validate-internal-field', [ServiceCompanyClientController::class, 'validateInternalClientField'])->name('clients.validateInternalField');
                Route::get('clients/check-ready/{id}', [ServiceCompanyClientController::class, 'checkReady'])->name('clients.checkReady');
                Route::resource('clients', ServiceCompanyClientController::class);
                Route::get('clients/viewmore/{id?}', [ServiceCompanyClientController::class, 'viewMore'])->name('clients.viewmore');
                Route::get('clients/dviewmore/{id?}', [ServiceCompanyClientController::class, 'dviewMore'])->name('clients.dviewmore');
                Route::get('clients/dashboard-analytics/{id?}', [ServiceCompanyClientController::class, 'dashboardAnalytics'])->name('clients.dashboard-analytics');
                Route::get('clients/api/v1/performance-analytics/{id}', [ServiceCompanyClientController::class, 'performanceAnalyticsV1'])->name('clients.performance-analytics.v1');
                Route::get('clients/addresslist/{cid?}', [ServiceCompanyClientController::class, 'addresslist'])->name('clients.addresslist');
                Route::get('clients/citylist/{state_id?}', [ServiceCompanyClientController::class, 'citylist'])->name('clients.citylist');
                Route::get('clients/custom_client/{id?}', [ServiceCompanyClientController::class, 'custom_client'])->name('clients.custom_client');
                Route::get('clients/direct_client/{id?}', [ServiceCompanyClientController::class, 'direct_client'])->name('clients.direct_client');
                Route::post('clients/update/{id}', [ServiceCompanyClientController::class, 'update'])->name('clients.update');
                Route::get('clients/client_request/{id?}', [ServiceCompanyClientController::class, 'client_request'])->name('clients.client_request');
                Route::get('clients/requestviewmore/{id?}', [ServiceCompanyClientController::class, 'requestviewmore'])->name('clients.requestviewmore');
                // END
                // #############       	START JOB MANAGEMENT
                Route::get('jobs/custom_requisition_data', [ServiceCompanyJobsController::class, 'custom_requisition_data'])->name('jobs.custom_requisition_data');
                Route::get('custom_jobs_ajax/custom_requisition_data', [ServiceCompanyJobsController::class, 'custom_requisition_data_ajax'])->name('jobs.custom_requisition_data_ajax');
                Route::get('jobs/client_requisition_data', [ServiceCompanyJobsController::class, 'client_requisition_data'])->name('jobs.client_requisition_data');
                Route::get('client_jobs_ajax/client_requisition_data', [ServiceCompanyJobsController::class, 'client_requisition_data_ajax'])->name('jobs.client_requisition_data_ajax');
                Route::get('jobs/all_data', [ServiceCompanyJobsController::class, 'all_data'])->name('jobs.all_data');
                Route::get('jobs_ajax/all_data', [ServiceCompanyJobsController::class, 'all_data_ajax'])->name('jobs.all_data_ajax')->middleware('recruiter.throttle');
                Route::get('mail_jobs_ajax/{UrlMailJobId?}', [ServiceCompanyJobsController::class, 'all_data_assing'])->name('jobs.all_data_assing');
                Route::get('jobs/data', [ServiceCompanyJobsController::class, 'data'])->name('jobs.data');
                Route::get('jobs_side_bar_job_ajax/data', [ServiceCompanyJobsController::class, 'client_job_data_ajax'])->name('jobs.data_client_ajax');
                Route::get('jobs/viewmore/{id?}', [ServiceCompanyJobsController::class, 'viewMore'])->name('jobs.viewmore');
                Route::get('jobs/clientviewmore/{id?}', [ServiceCompanyJobsController::class, 'clientviewmore'])->name('jobs.clientviewmore');
                Route::get('jobs/market-job-detail/{id}', [ServiceCompanyJobsController::class, 'marketJobDetail'])->name('jobs.market_job_detail');
                Route::get('jobs/spartnerlisting/{id?}', [ServiceCompanyJobsController::class, 'spartnerlisting'])->name('jobs.spartnerlisting');
                Route::get('jobs/spartnerlistingview/{id?}', [ServiceCompanyJobsController::class, 'boostkeywordsAndrecommendedData'])->name('jobs.spartnerlistingview');
                Route::get('jobs/requestapproval', [ServiceCompanyJobsController::class, 'requestapproval'])->name('jobs.requestapproval');
                Route::get('jobs/hmlisting/{id?}', [ServiceCompanyJobsController::class, 'hmlisting'])->name('jobs.hmlisting');
                Route::get('jobs/clienthmlisting/{id?}', [ServiceCompanyJobsController::class, 'clienthmlisting'])->name('jobs.clienthmlisting');
                Route::get('jobs/termshmlisting/{id?}', [ServiceCompanyJobsController::class, 'termshmlisting'])->name('jobs.termshmlisting');
                Route::get('jobs/termshmlistingaccept/{id?}', [ServiceCompanyJobsController::class, 'termshmlistingaccept'])->name('jobs.termshmlistingaccept');
                Route::get('jobs/currentteamlisting/{id?}', [ServiceCompanyJobsController::class, 'currentteamlisting'])->name('jobs.currentteamlisting');
                Route::get('jobs/client_requisition/{id?}', [ServiceCompanyJobsController::class, 'client_requisition'])->name('jobs.client_requisition');
                Route::get('jobs/custom_requisition/{id?}', [ServiceCompanyJobsController::class, 'custom_requisition'])->name('jobs.custom_requisition');
                Route::get('jobs/market-jobs/{id?}', [ServiceCompanyJobsController::class, 'marketJobs'])->name('jobs.marketJobs');
                Route::get('market-jobs-list/{id?}', [ServiceCompanyJobsController::class, 'marketJobs'])->name('jobs.marketJobList');
                Route::get('market-jobs-list-ajax', [ServiceCompanyJobsController::class, 'marketJobList_ajax'])->name('jobs.marketJobList_ajax');
                Route::get('market-jobs-list-v2-ajax', [ServiceCompanyJobsController::class, 'marketJobListV2_ajax'])->name('jobs.marketJobListV2_ajax');
                Route::match(['get', 'post'], 'market/requestaadded', [ServiceCompanyJobsController::class, 'marketrequestaadded'])->name('jobs.marketrequestaadded');
                Route::post('jobs/market/claim', [ServiceCompanyJobsController::class, 'marketrequestaadded'])->name('jobs.market_claim');
                Route::match(['get', 'post'], 'auction/send_job_request/{id?}', [ServiceCompanyJobsController::class, 'auction_send_job_request'])->name('jobs.auction_send_job_request');
                Route::get('jobs/job_request/{id?}', [ServiceCompanyJobsController::class, 'job_request'])->name('jobs.job_request');
                Route::get('jobs/recommandedrequest', [ServiceCompanyJobsController::class, 'recommandedrequest'])->name('jobs.recommandedrequest');
                Route::get('request_jobs_ajax/recommandedrequest', [ServiceCompanyJobsController::class, 'recommandedrequest_ajax'])->name('jobs.recommandedrequest_ajax');
                Route::get('jobs/curstomcreate/{id?}', [ServiceCompanyJobsController::class, 'curstomcreate'])->name('jobs.curstomcreate');
                Route::get('jobs/hiring_manager/{id?}', [ServiceCompanyJobsController::class, 'hiring_manager'])->name('jobs.hiring_manager');
                Route::post('jobs_hiring_contact_person/{id?}', [ServiceCompanyJobsController::class, 'hiring_contact_person'])->name('jobs.hiring_contact_person');
                Route::post('jobs_mail_contact_person/{id?}', [ServiceCompanyJobsController::class, 'mail_contact_person'])->name('jobs.mail_contact_person');
                Route::get('jobs/recruiter/{id?}', [ServiceCompanyJobsController::class, 'recruiter'])->name('jobs.recruiter');
                Route::get('jobs/clients-bundle', [ServiceCompanyJobsController::class, 'clientsBundle'])->name('jobs.clientsBundle');
                Route::get('jobs/titles-bundle', [ServiceCompanyJobsController::class, 'jobTitlesBundle'])->name('jobs.jobTitlesBundle');
                Route::get('jobs/departments-bundle', [ServiceCompanyJobsController::class, 'departmentsBundle'])->name('jobs.departmentsBundle');
                Route::get('jobs/client-details/{client_id}', [ServiceCompanyJobsController::class, 'clientDetails'])->name('jobs.clientDetails');
                Route::get('jobs/getContactPerson/{client_id}', [ServiceCompanyJobsController::class, 'getContactPerson'])->name('jobs.getContactPerson');
                Route::get('jobs/getlocationCity/{location_id}', [ServiceCompanyJobsController::class, 'getlocationCity'])->name('jobs.getlocationCity');
                Route::get('jobs/editJobStatus/{id?}', [ServiceCompanyJobsController::class, 'editJobStatus'])->name('jobs.editJobStatus')->middleware('recruiter.throttle');
                Route::get('jobs/hmlistingupdate/{id?}', [ServiceCompanyJobsController::class, 'hmlistingupdate'])->name('jobs.hmlistingupdate');
                Route::get('jobs/boostkeywords', [ServiceCompanyJobsController::class, 'boostkeywords'])->name('jobs.boostkeywords');
                Route::get('jobs/recommendedData', [ServiceCompanyJobsController::class, 'recommendedData'])->name('jobs.recommendedData');
                Route::get('jobs/recommendedAssignList', [ServiceCompanyJobsController::class, 'recommendedAssignList'])->name('jobs.recommendedAssignList')->middleware('recruiter.throttle');
                Route::get('jobs/staffingPartnersRegister', [ServiceCompanyJobsController::class, 'staffingPartnersRegister'])->name('jobs.staffingPartnersRegister')->middleware('recruiter.throttle');
                Route::get('jobs/send_only_job_invitation/{id?}', [ServiceCompanyJobsController::class, 'send_only_job_invitation'])->name('jobs.send_only_job_invitation');
                Route::get('jobs/rsp_job_request_send/{id?}', [ServiceCompanyJobsController::class, 'rsp_job_request_send'])->name('jobs.rsp_job_request_send');
                Route::get('jobs/send_job_recommanded_request/{id?}', [ServiceCompanyJobsController::class, 'send_job_recommanded_request'])->name('jobs.send_job_recommanded_request');
                Route::get('jobs/assignRejected', [ServiceCompanyJobsController::class, 'assignRejected'])->name('jobs.assignRejected');
                Route::get('jobs/removeJob', [ServiceCompanyJobsController::class, 'removeJob'])->name('jobs.removeJob');
                Route::get('jobs/resend_job_recommanded_request/{id?}', [ServiceCompanyJobsController::class, 'resend_job_recommanded_request'])->name('jobs.resend_job_recommanded_request');
                Route::match(['get', 'post'], 'jobs/send_multiple_job_invitations', [ServiceCompanyJobsController::class, 'send_multiple_job_invitations'])->name('jobs.send_multiple_job_invitations');
                Route::get('jobs/check-ready/{id}', [ServiceCompanyJobsController::class, 'checkReady'])->name('jobs.checkReady');
                Route::middleware(['recruiter.throttle:60,1', 'resolve.taxonomy'])->group(function () {
                    Route::resource('jobs', ServiceCompanyJobsController::class);
                });
                Route::post('jobs/updatestatus', [ServiceCompanyJobsController::class, 'updatestatus'])->name('jobs.updatestatus');
                Route::post('jobs/hiring_manager/{id?}', [ServiceCompanyJobsController::class, 'hiring_manager'])->name('jobs.hiring_manager');
                Route::post('jobs/newUpdateJobstatus', [ServiceCompanyJobsController::class, 'newUpdateJobstatus'])->name('jobs.newUpdateJobstatus');
                Route::post('jobs/update/{id}', [ServiceCompanyJobsController::class, 'update'])->name('jobs.update');
                Route::get('jobs/add_to_list/{jobId?}', [ServiceCompanyJobsController::class, 'add_to_list'])->name('jobs.add_to_list');
                Route::get('demo/view-demo', [ServiceCompanyJobsController::class, 'demoView'])->name('jobs.demo');
                Route::get('/get-job-suggestions', [ServiceCompanyJobsController::class, 'getJobSuggestions'])->name('jobs.getJobSuggestions');
                Route::post('/jdDetails', [ServiceCompanyJobsController::class, 'jdDetails'])->name('jobs.jdDetails');
                // END
                // #############       	START JOB APPLICATION MANAGEMENT
                Route::resource('job-applications', ServiceCompanyJobApplicationController::class);
                Route::get('job-applications/clientshow/{id?}', [ServiceCompanyJobApplicationController::class, 'clientshow'])->name('job-applications.clientshow');
                Route::get('job-applications/websiteshow/{id?}', [ServiceCompanyJobApplicationController::class, 'websiteShow'])->name('job-applications.websiteshow');
                Route::get('job-applications/create-schedule/{id?}', [ServiceCompanyJobApplicationController::class, 'createSchedule'])->name('job-applications.create-schedule');
                Route::get('job-applications/recruiter/{id?}', [ServiceCompanyJobApplicationController::class, 'recruiter'])->name('job-applications.recruiter');
                Route::get('job-applications/statelist/{cid?}', [ServiceCompanyJobApplicationController::class, 'statelist'])->name('job-applications.statelist');
                Route::get('job-applications/citylist', [ServiceCompanyJobApplicationController::class, 'citylist'])->name('job-applications.citylist');
                Route::get('job-applications/stage_body_content/{id?}', [ServiceCompanyJobApplicationController::class, 'stage_body_content'])->name('job-applications.stage_body_content');
                Route::get('job-applications/client_stage_body_content/{id?}', [ServiceCompanyJobApplicationController::class, 'client_stage_body_content'])->name('job-applications.client_stage_body_content');
                Route::get('job-applications/child_stage/{id?}', [ServiceCompanyJobApplicationController::class, 'child_stage'])->name('job-applications.child_stage');
                Route::get('job-applications/timeline/{id}', [ServiceCompanyJobApplicationController::class, 'getTimeline'])->name('job-applications.timeline');
                Route::get('job-applications/resume-meta/{id}', [ServiceCompanyJobApplicationController::class, 'getResumeMeta'])->name('job-applications.resume-meta');
                Route::get('job-applications/r2-download-url/{id}', [ServiceCompanyJobApplicationController::class, 'resumeR2DownloadUrl'])->name('job-applications.r2-download-url');
                Route::post('job-applications/rdc-attachment-initiate', [ServiceCompanyJobApplicationController::class, 'rdc_attachment_initiate'])->name('job-applications.rdc-attachment-initiate');
                Route::get('job-applications/interview-show/{id?}', [ServiceCompanyJobApplicationController::class, 'interviewShow'])->name('job-applications.interview-show');
                Route::get('apptracker/show-interviews/{id?}', [ServiceCompanyAppTrackerController::class, 'showInterviews'])->name('apptracker.show-interviews');
                Route::get('job-applications/create/{jobId?}/{appId?}', [ServiceCompanyJobApplicationController::class, 'create'])->name('job-applications.create');
                Route::get('job-applications/clientcreate/{jobId?}/{appId?}', [ServiceCompanyJobApplicationController::class, 'clientcreate'])->name('job-applications.clientcreate');
                Route::get('job-applications/question/{jobID?}/{applicationId?}', [ServiceCompanyJobApplicationController::class, 'jobQuestion'])->name('job-applications.question');
                Route::get('job-applications/getAdditionalDetail/{job_id?}', [ServiceCompanyJobApplicationController::class, 'getAdditionalDetail'])->name('job-applications.getAdditionalDetail');

                Route::get('job-applications/check-index-status/{id}', [ServiceCompanyJobApplicationController::class, 'checkIndexStatus'])->name('job-applications.check-index-status');
                Route::resource('job-applications', ServiceCompanyJobApplicationController::class);
                Route::post('job-applications/update/{id}', [ServiceCompanyJobApplicationController::class, 'update'])->name('job-applications.update');
                Route::post('uploadresume', [ServiceCompanyJobApplicationController::class, 'uploadresume'])->name('job-applications.uploadresume');
                Route::post('unset-file', [ServiceCompanyJobApplicationController::class, 'unsetFile'])->name('file.unset');
                Route::post('skill-experience', [ServiceCompanyJobApplicationController::class, 'skillExperienceUpdate'])->name('job-applications.skillExperienceUpdate');
                Route::post('job-question-update', [ServiceCompanyJobApplicationController::class, 'JobQuestionUpdate'])->name('job-applications.JobQuestionUpdate');
                Route::post('slots-update', [ServiceCompanyJobApplicationController::class, 'SlotsUpdate'])->name('job-applications.SlotsUpdate');
                Route::post('job-applications/accessEmailTemplate', [ServiceCompanyJobApplicationController::class, 'accessEmailTemplate'])->name('job-applications.accessEmailTemplate');
                Route::get('recruiter-note-templates', [ServiceCompanyJobApplicationController::class, 'recruiterNoteTemplates'])->name('job-applications.recruiterNoteTemplates');
                Route::post('recruiter-note-update', [ServiceCompanyJobApplicationController::class, 'recruiterNoteUpdate'])->name('job-applications.recruiterNoteUpdate');
                Route::post('reason-for-change-update', [ServiceCompanyJobApplicationController::class, 'reasonForChangeUpdate'])->name('job-applications.reasonForChangeUpdate');
                Route::post('visa-status-update', [ServiceCompanyJobApplicationController::class, 'visaStatusUpdate'])->name('job-applications.visaStatusUpdate');
                Route::post('job-applications/stage_submit', [ServiceCompanyJobApplicationController::class, 'stage_submit'])->name('job-applications.stage_submit');
                Route::post('job-applications/client_stage_submit', [ServiceCompanyJobApplicationController::class, 'client_stage_submit'])->name('job-applications.client_stage_submit');
                Route::post('job-applications/resume_delete', [ServiceCompanyJobApplicationController::class, 'resume_delete'])->name('job-applications.resume_delete');
                Route::post('job-applications/statusupdate', [ServiceCompanyJobApplicationController::class, 'statusupdate'])->name('job-applications.statusupdate');
                Route::post('job-applications/store-schedule', [ServiceCompanyJobApplicationController::class, 'storeSchedule'])->name('job-applications.store-schedule');
                Route::post('job-applications/emailschedule', [ServiceCompanyJobApplicationController::class, 'emailSchedule'])->name('job-applications.emailschedule');
                Route::post('job-applications/getinterviewchild', [ServiceCompanyJobApplicationController::class, 'getinterviewchild'])->name('job-applications.getinterviewchild');
                Route::post('job-applications/getGoogleVideoUrl', [ServiceCompanyJobApplicationController::class, 'getGoogleVideoUrl'])->name('job-applications.getGoogleVideoUrl');
                Route::post('/change-event-organizer/{eventId}', [ServiceCompanyJobApplicationController::class, 'changeEventOrganizer']);
                Route::post('job-applications/getVideoUrl', [ServiceCompanyJobApplicationController::class, 'getVideoUrl'])->name('job-applications.getVideoUrl');
                // Route::get('job-applications/recruiter/{id?}', [ServiceCompanyJobApplicationController::class,'recruiter'])->name('job-applications.recruiter');
                Route::post('job-applications/recruiter/{id?}', [ServiceCompanyJobApplicationController::class, 'recruiter'])->name('job-applications.recruiter');
                Route::post('contact-persion-team/recruiter/{id?}', [ServiceCompanyJobApplicationController::class, 'contactPersionWithTeam'])->name('job-applications.contactPersionWithTeam');
                Route::post('job-applications/clientappstore', [ServiceCompanyJobApplicationController::class, 'clientappstore'])->name('job-applications.clientappstore');
                Route::get('job-applications/download/{id}', [ServiceCompanyJobApplicationController::class, 'download'])->name('job-applications.download');
                Route::get('/export-job-applications', [ServiceCompanyJobApplicationController::class, 'sampleDownload'])->name('export.sample-job-applications');
                Route::get('/candidate-export-job-applications', [ServiceCompanyJobApplicationController::class, 'candidatejobDownload'])->name('candidateexport.candidate-job-applications');
                Route::post('/import-job-applications', [ServiceCompanyJobApplicationController::class, 'importJobCondidate'])->name('import.sample-job-applications');
                Route::get('/unlink-job-applications', [ServiceCompanyJobApplicationController::class, 'unlinkDownload'])->name('unlink.sample-job-applications');
                Route::post('/extract-resume-applications', [ServiceCompanyJobApplicationController::class, 'resumExtract'])->name('extract.resume-job-applications');
                Route::get('create-offer/{id}', [ServiceCompanyJobApplicationController::class, 'createOffer'])->name('job-applications.create_offer');
                Route::post('save-create-offer', [ServiceCompanyJobApplicationController::class, 'saveCreateOffer'])->name('job-applications.save_create_offer');
                Route::post('preview-offer-letter/{id?}', [ServiceCompanyCvTrackerController::class, 'previewOfferLettetr'])->name('cvtracker.previewOfferLettetr');
                Route::post('terms-save-create-offer', [ServiceCompanyJobApplicationController::class, 'termsSaveCreateOffer'])->name('job-applications.terms_save_create_offer');
                /** custom ajax route define */
                Route::get('client-candidate-join-list', [ServiceCompanyJobApplicationController::class, 'clientCandidateJoinList'])->name('job-applications.client-candidates-join-list');
                Route::get('client-candidate-interview-tracker', [ServiceCompanyJobApplicationController::class, 'clientCandidateInterviewTracker'])->name('job-applications.client-candidates-interview-tracker');
                Route::get('direct-client-candidate-join-list', [ServiceCompanyJobApplicationController::class, 'directclientCandidateJoinList'])->name('job-applications.direct-client-candidates-join-list');
                Route::get('direct-client-candidate-interview-tracker', [ServiceCompanyJobApplicationController::class, 'directclientCandidateInterviewTracker'])->name('job-applications.direct-client-candidates-interview-tracker');
                Route::get('staffing-client-candidate-list', [ServiceCompanyJobApplicationController::class, 'staffingclientCandidateList'])->name('job-applications.staffing-client-candidates');
                Route::get('staffing-client-candidate-join-list', [ServiceCompanyJobApplicationController::class, 'staffingclientCandidateJoinList'])->name('job-applications.staffing-client-candidates-join-list');
                Route::get('staffing-client-candidate-interview-tracker', [ServiceCompanyJobApplicationController::class, 'staffingclientCandidateInterviewTracker'])->name('job-applications.staffing-client-candidates-interview-tracker');
                Route::get('company-logo-proxy', [ServiceCompanyJobApplicationController::class, 'companyLogoProxy'])->name('job-applications.company-logo-proxy');
                Route::get('resume-as-pdf/{id}', [ServiceCompanyJobApplicationController::class, 'resumeAsPdf'])->name('job-applications.resume-as-pdf');
                Route::get('resume-studio/{id}', [ServiceCompanyJobApplicationController::class, 'resumeStudio'])->name('job-applications.resume-studio');
                Route::get('resume-convert/{token}', [ServiceCompanyJobApplicationController::class, 'resumeConvertPdf'])->where('token', '.*')->name('job-applications.resume-convert');
                /** custom ajax route define */
                Route::post('offer-mail-format/{id?}', [ServiceCompanyCvTrackerController::class, 'offerSendMail'])->name('cvtracker.offer_send_mail');
                Route::post('offer-send-mail/fwd_offer_send', [ServiceCompanyCvTrackerController::class, 'fwd_offer_send'])->name('cvtracker.fwd_offer_send');
                Route::post('offer_download_mail/{id?}', [ServiceCompanyCvTrackerController::class, 'fwd_offer_download'])->name('cvtracker.offer_download_mail');
                // Direct streamed offer-letter PDF download (create-offer Step 3 button). GET so the
                // browser can navigate straight to it; streams the PDF as an attachment with no
                // intermediate file/URL/storage dependency. See downloadOfferLetterStream() docblock.
                Route::get('offer-letter/download/{offerId}', [ServiceCompanyCvTrackerController::class, 'downloadOfferLetterStream'])->name('cvtracker.offer_letter_stream');

                // END
                // #############       	START APPTRACKER MANAGEMENT
                Route::get('apptracker/dsimilar_candidates/{id}', [ServiceCompanyAppTrackerController::class, 'dsimilar_candidates'])->name('apptracker.dsimilar_candidates');
                Route::get('apptracker/dsimilar_existing_data', [ServiceCompanyAppTrackerController::class, 'dsimilar_existing_data'])->name('apptracker.dsimilar_existing_data');
                Route::get('apptracker_dsimilar_ajax/dsimilar_existing_data', [ServiceCompanyAppTrackerController::class, 'dsimilar_existing_data_ajax'])->name('apptracker.dsimilar_existing_data_ajax');
                Route::get('apptracker/dinternal_bench/{id}', [ServiceCompanyAppTrackerController::class, 'dinternal_bench'])->name('apptracker.dinternal_bench');
                Route::get('apptracker/dinternal_bench_data', [ServiceCompanyAppTrackerController::class, 'dinternal_bench_data'])->name('apptracker.dinternal_bench_data');
                Route::get('apptracker_dinternal_ajax/dinternal_bench_data', [ServiceCompanyAppTrackerController::class, 'dinternal_bench_data_ajax'])->name('apptracker.dinternal_bench_data_ajax');
                Route::get('apptracker/similar_candidates/{id}', [ServiceCompanyAppTrackerController::class, 'similar_candidates'])->name('apptracker.similar_candidates');
                Route::get('apptracker/candidates_request', [ServiceCompanyAppTrackerController::class, 'candidates_request'])->name('apptracker.candidates_request');
                Route::get('apptracker/similar_existing_data', [ServiceCompanyAppTrackerController::class, 'similar_existing_data'])->name('apptracker.similar_existing_data');
                Route::get('similar_apptracker/similar_existing_data', [ServiceCompanyAppTrackerController::class, 'similar_existing_data_ajax'])->name('apptracker.similar_existing_data_ajax');
                Route::get('talent-pool-candidate/request/{id}', [ServiceCompanyAppTrackerController::class, 'talentPoolRequest'])->name('apptracker.talentPoolRequest');
                Route::get('apptracker/rec_candidate_assign', [ServiceCompanyAppTrackerController::class, 'rec_candidate_assign'])->name('apptracker.rec_candidate_assign');
                Route::get('apptracker/internal_bench/{id}', [ServiceCompanyAppTrackerController::class, 'internal_bench'])->name('apptracker.internal_bench');
                Route::get('apptracker/internal_bench_data', [ServiceCompanyAppTrackerController::class, 'internal_bench_data'])->name('apptracker.internal_bench_data');
                Route::get('career-apptracker/career-apply/{id}', [ServiceCompanyAppTrackerController::class, 'career_apply'])->name('apptracker.career_apply');
                Route::get('career-apply-apptracker/career-apply-data', [ServiceCompanyAppTrackerController::class, 'career_apply_data_ajax'])->name('apptracker.career_apply_data_ajax');
                Route::get('internal_apptracker/internal_bench_data', [ServiceCompanyAppTrackerController::class, 'internal_bench_data_ajax'])->name('apptracker.internal_bench_data_ajax');
                Route::get('apptracker/public_bench/{id}', [ServiceCompanyAppTrackerController::class, 'public_bench'])->name('apptracker.public_bench');
                Route::get('apptracker/public_bench_data', [ServiceCompanyAppTrackerController::class, 'public_bench_data'])->name('apptracker.public_bench_data');
                Route::get('find-talents', [ServiceCompanyAppTrackerController::class, 'findTalent'])->name('apptracker.findTalent');
                Route::get('find-data-talent', [ServiceCompanyAppTrackerController::class, 'findTalentData'])->name('apptracker.findTalentData');
                Route::get('find-agency', [ServiceCompanyCompanyController::class, 'findAgency'])->name('find.findAgency');
                Route::get('find-agency-data', [ServiceCompanyCompanyController::class, 'findAgencyData'])->name('find.findAgencyData');
                Route::match(['get', 'post'], 'agency-request/{id?}', [ServiceCompanyCompanyController::class, 'findAgencyRequest'])->name('agency.recommanded_request');
                Route::get('apptrackerajax/public_bench_data', [ServiceCompanyAppTrackerController::class, 'public_bench_data_ajax'])->name('apptracker.public_bench_data_ajax');
                Route::get('apptracker/sub_stage/{status?}/{application_id?}/{job_id?}', [ServiceCompanyAppTrackerController::class, 'sub_stage'])->name('apptracker.sub_stage');
                Route::get('apptracker/{id}/{mstatus_id}', [ServiceCompanyAppTrackerController::class, 'index'])->name('apptracker.index');
                Route::get('apptracker/benchReqViewMore/{id}/{job_applications_id}', [ServiceCompanyAppTrackerController::class, 'benchReqViewMore'])->name('apptracker.benchReqViewMore');
                Route::get('apptracker/loadcounter/{jobid?}/{mstatus_id?}/{inputSearch?}', [ServiceCompanyAppTrackerController::class, 'loadcounter'])->name('apptracker.loadcounter');
                Route::get('apptracker/delta-sync', [ServiceCompanyAppTrackerController::class, 'deltaSync'])->name('apptracker.deltaSync');

                Route::get('apptracker/export/{jobs}/{mstatus_id}/{start}/{length}', [ServiceCompanyAppTrackerController::class, 'export'])->name('apptracker.export');
                Route::get('apptracker/data', [ServiceCompanyAppTrackerController::class, 'data'])->name('apptracker.data');
                Route::get('apptracker/candidates_request_data', [ServiceCompanyAppTrackerController::class, 'candidates_request_data'])->name('apptracker.candidates_request_data');
                Route::get('apptracker_request/candidates_request_data', [ServiceCompanyAppTrackerController::class, 'candidates_request_data_ajax'])->name('apptracker.candidates_request_data_ajax');
                Route::get('apptracker/openRejFrm/{application_id?}/{job_id?}/{parent_status_id?}/{current_child_id?}', [ServiceCompanyAppTrackerController::class, 'openRejFrm'])->name('apptracker.openRejFrm');
                Route::get('apptracker/clientapp/{id}/{mstatus_id}', [ServiceCompanyAppTrackerController::class, 'clientapp'])->name('apptracker.clientapp');
                Route::get('apptracker_ajax/clientappdata', [ServiceCompanyAppTrackerController::class, 'clientappdata_ajax'])->name('apptracker.clientappdata_ajax');
                Route::get('apptracker/dsimilar_existing_data', [ServiceCompanyAppTrackerController::class, 'dsimilar_existing_data'])->name('apptracker.dsimilar_existing_data');
                Route::post('apptracker/store', [ServiceCompanyAppTrackerController::class, 'store'])->name('apptracker.store');
                Route::post('apptracker/getchild', [ServiceCompanyAppTrackerController::class, 'getchild'])->name('apptracker.getchild');

                // Route::resource('tickets', TicketController::class);

                Route::get('ticket-list', [ServiceCompanyAppTrackerController::class, 'ticketList'])->name('tickets.ticketList');
                Route::post('tickets/{ticket}/status', [ServiceCompanyAppTrackerController::class, 'ticketUpdateStatus'])->name('tickets.ticketUpdateStatus');
                Route::get('ticketcreate', [ServiceCompanyAppTrackerController::class, 'ticketCreate'])->name('tickets.create');
                Route::get('sdshow-tickets/{id}', [ServiceCompanyAppTrackerController::class, 'ticketshow'])->name('tickets.show');
                Route::post('storeticketcreate', [ServiceCompanyAppTrackerController::class, 'storeticketCreate'])->name('tickets.store');
                Route::post('comments/store', [ServiceCompanyAppTrackerController::class, 'storetComment'])->name('comments.store');
                Route::get('all-app-tracker-list', [ServiceCompanyAppTrackerController::class, 'allDataAjax'])->name('apptracker.allDataAjax');

                // END
                // #############       	START CVTRACKER MANAGEMENT

                Route::post('cvtracker/fwd_resume/{id?}', [ServiceCompanyCvTrackerController::class, 'fwd_resume'])->name('cvtracker.fwd_resume');
                Route::post('mass_mail/mass_mail/{id?}', [ServiceCompanyCvTrackerController::class, 'mass_mail'])->name('cvtracker.mass_mail');
                Route::post('send-mail/send-mail/{id?}', [ServiceCompanyCvTrackerController::class, 'send_mail'])->name('cvtracker.send_mail');
                Route::post('send-whatsapp/send-whatsapp/{id?}', [ServiceCompanyCvTrackerController::class, 'send_whatsapp'])->name('cvtracker.send_whatsapp');
                Route::post('cvtracker/application_copy', [ServiceCompanyCvTrackerController::class, 'application_copy'])->name('cvtracker.application_copy');
                Route::post('talent-add', [ServiceCompanyCvTrackerController::class, 'talentAdd'])->name('cvtracker.talent-add');
                Route::get('cvtracker/resume/{id}', [ServiceCompanyCvTrackerController::class, 'resume'])->name('cvtracker.resume');
                Route::get('cvtracker/create_status/{id?}', [ServiceCompanyCvTrackerController::class, 'create_status'])->name('cvtracker.create_status');
                Route::get('cvtracker/data', [ServiceCompanyCvTrackerController::class, 'data'])->name('cvtracker.data');
                Route::get('cvtracker/alldata', [ServiceCompanyCvTrackerController::class, 'alldata'])->name('cvtracker.alldata');
                Route::get('all-candidate-list', [ServiceCompanyCvTrackerController::class, 'allcandidates'])->name('cvtracker.allcandidates');
                Route::get('all-candidate-list-v2', [ServiceCompanyCvTrackerV2Controller::class, 'allcandidates'])->name('cvtracker.v2.allcandidates');
                Route::get('cvtracker/candidateRightSideBar/{id?}', [ServiceCompanyCvTrackerController::class, 'candidateRightSideBar'])->name('cvtracker.candidateRightSideBar');
                Route::get('cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', [ServiceCompanyCvTrackerController::class, 'export'])->name('cvtracker.export');
                Route::get('cvtracker/copyjobtable', [ServiceCompanyCvTrackerController::class, 'copyjobtable'])->name('cvtracker.copyjobtable');

                Route::get('benchTalents/list/{id?}', [ServiceCompanyJobsController::class, 'btlisting'])->name('jobs.btlisting');
                Route::get('benchTalents/submission-history/{id}', [ServiceCompanyJobsController::class, 'benchSubmissionHistory'])->name('jobs.bench_submission_history');

                Route::get('bench-talents/copybenchtable', [ServiceCompanyCvTrackerController::class, 'copybenchtable'])->name('bench.copybenchtable');

                Route::resource('cvtracker', ServiceCompanyCvTrackerController::class);
                Route::post('cvtracker/get_client_job', [ServiceCompanyCvTrackerController::class, 'get_client_job'])->name('cvtracker.get_client_job');
                Route::post('cvtracker/candidate_process_update', [ServiceCompanyCvTrackerController::class, 'candidate_process_update'])->name('cvtracker.candidate_process_update');
                Route::post('cvtracker/copy_current_selected_applicant', [ServiceCompanyCvTrackerController::class, 'copy_current_selected_applicant'])->name('cvtracker.copy_current_selected_applicant');
                Route::post('cvtracker/email_template', [ServiceCompanyCvTrackerController::class, 'email_template'])->name('cvtracker.email_template');
                // Primary Forward Resume Pipeline (0% Server Load / Direct-to-R2 Engine)
                Route::post('cvtracker/test_fwd_resume/{id?}', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume'])->name('cvtracker.test_fwd_resume');
                Route::post('cvtracker/test_fwd_resume_data', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume_data'])->name('cvtracker.test_fwd_resume_data');
                Route::match(['get', 'post'], 'cvtracker/fwd_recipients', [ServiceCompanyCvTrackerController::class, 'fwd_recipients'])->name('cvtracker.fwd_recipients');
                Route::post('cvtracker/test_fwd_resume_intent', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume_intent'])->name('cvtracker.test_fwd_resume_intent');
                Route::post('cvtracker/test_fwd_resume_presigned', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume_presigned'])->name('cvtracker.test_fwd_resume_presigned');
                Route::post('cvtracker/test_fwd_resume_finalize', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume_finalize'])->name('cvtracker.test_fwd_resume_finalize');
                Route::post('cvtracker/test_fwd_resume_send', [ServiceCompanyCvTrackerController::class, 'test_fwd_resume_send'])->name('cvtracker.test_fwd_resume_send');
                Route::post('cvtracker/quick_upload_resume', [ServiceCompanyCvTrackerController::class, 'quick_upload_resume'])->name('cvtracker.quick_upload_resume');
                Route::post('mass_cvtracker/mass_mail_send', [ServiceCompanyCvTrackerController::class, 'mass_mail_send'])->name('cvtracker.mass_mail_send');
                Route::post('send_cvtracker/send_mail_send', [ServiceCompanyCvTrackerController::class, 'send_mail_send'])->name('cvtracker.send_mail_send');

                Route::post('send_cvtracker/send_mail_for_candidate', [ServiceCompanyCvTrackerController::class, 'send_mail_for_candidate'])->name('cvtracker.send_mail_for_candidate');

                // END
                // #############       	START INTERVIEW SCHEDULE MANAGEMENT

                Route::get('interview-schedule/create_status/{application_id?}/{interview_id?}', [InterviewScheduleController::class, 'create_status'])->name('interview-schedule.create_status');
                Route::get('interview-schedule/create_feedbackfrm/{application_id?}/{interview_id?}/{feedback_id?}', [InterviewScheduleController::class, 'create_feedbackfrm'])->name('interview-schedule.create_feedbackfrm');
                Route::get('interview-schedule/table-view', [InterviewScheduleController::class, 'table'])->name('interview-schedule.table-view');
                Route::get('interview-schedule/data', [InterviewScheduleController::class, 'data'])->name('interview-schedule.data');
                Route::get('interview-schedule_ajax/data', [InterviewScheduleController::class, 'data_ajax'])->name('interview-schedule.data_ajax');
                Route::post('interview-schedule/followup-update', [InterviewScheduleController::class, 'followupUpdate'])->name('interview-schedule.followup_update');
                Route::post('interview-schedule/send-link', [InterviewScheduleController::class, 'sendInterviewLink'])->name('interview-schedule.send_link');
                Route::get('interview-schedule/showfeedback/{id?}', [InterviewScheduleController::class, 'showfeedback'])->name('interview-schedule.showfeedback');
                Route::get('skill-interview-schedule/skillshowfeedback/{id?}', [InterviewScheduleController::class, 'skillshowfeedback'])->name('interview-schedule.skillshowfeedback');
                Route::get('skill-interview-schedule-download/skillshowfeedback/{id?}/{feed?}', [InterviewScheduleController::class, 'skillshowfeedbackDownload'])->name('interview-schedule.skillshowfeedbackdownload');
                Route::post('interview-schedule/change-status-multiple', [InterviewScheduleController::class, 'changeStatusMultiple'])->name('interview-schedule.change-status-multiple');
                Route::get('interview-schedule/interviewData', [InterviewScheduleController::class, 'interviewData'])->name('interview-schedule.interviewData');
                Route::get('interview-schedule/export/{start}/{length}', [InterviewScheduleController::class, 'export'])->name('interview-schedule.export');
                Route::get('interview-schedule/interviewDirectClientData', [InterviewScheduleController::class, 'interviewDirectClientData'])->name('interview-schedule.interviewDirectClientData');
                Route::resource('interview-schedule', InterviewScheduleController::class);
                Route::post('interview-schedule/updateFeedbackStatus', [InterviewScheduleController::class, 'updateFeedbackStatus'])->name('interview-schedule.updateFeedbackStatus');
                Route::post('interview-schedule/updateAvailableStatus', [InterviewScheduleController::class, 'updateAvailableStatus'])->name('interview-schedule.updateAvailableStatus');
                Route::post('interview-schedule/updateInterview', [InterviewScheduleController::class, 'updateInterview'])->name('interview-schedule.updateInterview');
                Route::get('sub_status_tab', [InterviewScheduleController::class, 'sub_status_tab'])->name('interview-schedule.sub_status_tab');

                // END
                // ############       	START APPLICATION OFFER MANAGEMENT

                Route::get('applicationoffer/tracker', [ServiceCompanyApplicationOfferController::class, 'tracker'])->name('applicationoffer.tracker');
                Route::get('applicationoffer/data', [ServiceCompanyApplicationOfferController::class, 'data'])->name('offer.data');
                Route::get('applicationoffer/export/{start}/{length}', [ServiceCompanyApplicationOfferController::class, 'export'])->name('applicationoffer.export');
                Route::resource('applicationoffer', ServiceCompanyApplicationOfferController::class);
                Route::post('applicationoffer/update_salary', [ServiceCompanyApplicationOfferController::class, 'updateSalary'])->name('applicationoffer.update_salary');
                Route::post('applicationoffer/update_application', [ServiceCompanyApplicationOfferController::class, 'update_application'])->name('applicationoffer.update_application');

                // END
                // ############       	START JON_ON_BOARD MANAGEMENT

                Route::get('job-onboard/data', [ServiceCompanyJobOnboardController::class, 'data'])->name('job-onboard.data');
                Route::get('job-onboard/send-offer/{id?}', [ServiceCompanyJobOnboardController::class, 'sendOffer'])->name('job-onboard.send-offer');
                Route::get('job-onboard/update-status/{id?}', [ServiceCompanyJobOnboardController::class, 'updateStatus'])->name('job-onboard.update-status');
                Route::get('job-onboard/export/{start}/{length}', [ServiceCompanyJobOnboardController::class, 'export'])->name('job-onboard.export');
                Route::resource('job-onboard', ServiceCompanyJobOnboardController::class);
                // END
                // ############      	START CONSULTANCY MANAGEMENT
                Route::get('consultancy_ajax/data', [ServiceCompanyConsultancyController::class, 'data_ajax'])->middleware(['throttle:120,1', 'consultancy.etag'])->name('consultancy.data_ajax');
                Route::get('consultancy/suggestedPartners/{id?}', [ServiceCompanyConsultancyController::class, 'suggestedPartners'])->name('consultancy.suggestedPartners');
                Route::get('consultancy/requestPartners/{id?}', [ServiceCompanyConsultancyController::class, 'requestPartners'])->name('consultancy.requestPartners');
                Route::match(['get', 'post'], 'consultancy/staffing_invitation/{id?}', [ServiceCompanyConsultancyController::class, 'staffing_invitation'])->name('consultancy.staffing_invitation');
                Route::get('consultancy/dviewmore/{id?}', [ServiceCompanyConsultancyController::class, 'dviewMore'])->middleware(['throttle:120,1', 'consultancy.etag'])->name('consultancy.dviewmore');
                Route::get('consultancy/staffing_contact_list/{id?}', [ServiceCompanyConsultancyController::class, 'staffingContactList'])->name('consultancy.staffing_contact_list');
                Route::get('consultancy_ajax/allreq/{id?}', [ServiceCompanyConsultancyController::class, 'allreq_ajax'])->name('consultancy.allreq_ajax');
                Route::get('consultancy_ajax/dashboard/{id?}', [ServiceCompanyConsultancyController::class, 'dashboard_ajax'])->name('consultancy.dashboard_ajax');
                Route::get('consultancy/uncleared_request_data', [ServiceCompanyConsultancyController::class, 'uncleared_request_data'])->name('consultancy.uncleared_request_data');
                Route::get('consultancy/export/{start}/{length}', [ServiceCompanyConsultancyController::class, 'export'])->name('consultancy.export');
                Route::resource('consultancy', ServiceCompanyConsultancyController::class);

                // END
                // ############      	START BENCH CANDIDATE MANAGEMENT

                Route::get('recommend/all_data', [ServiceCompanyBenchCandidatesController::class, 'all_data'])->name('recommend.all_data');
                Route::get('recommend_ajax/all_data', [ServiceCompanyBenchCandidatesController::class, 'all_data_ajax'])->name('recommend.all_data_ajax');
                Route::get('recommend_internal_ajax/all_data', [ServiceCompanyBenchCandidatesController::class, 'all_internal_data_ajax'])->name('recommend.all_internal_data_ajax');
                Route::post('boostkeywordadd', [ServiceCompanyBenchCandidatesController::class, 'boostkeywordadd'])->name('bench-candidates.boostkeywordadd');
                Route::post('bench-switch', [ServiceCompanyBenchCandidatesController::class, 'benchSwitch'])->name('bench-candidates.benchSwitch');
                Route::get('interview-view/bench-candidates', [InterviewScheduleController::class, 'benchinterviewtable'])->name('bench-candidates.bench-table-view');
                // Route::get('bench-data/bench-candidates', [InterviewScheduleController::class, 'benchinterviewdata'])->name('bench-candidates.bench-data'); // Deactivated: legacy broken endpoint (§3.4 audit)
                Route::get('bench-dataInterview_ajax/bench-candidates', [InterviewScheduleController::class, 'benchinterviewdata_ajax'])->name('bench-candidates.benchinterviewdata_ajax');
                Route::get('bench-candidates_ajax/v2/benchinterviewdata', [InterviewScheduleController::class, 'benchinterviewdata_v2'])->name('bench-candidates.benchinterviewdata_v2');
                Route::match(['get', 'post'], 'bench-candidates/hmlistingupdate/{id?}', [ServiceCompanyBenchCandidatesController::class, 'hmlistingupdate'])->name('bench-candidates.hmlistingupdate');
                Route::get('bench-candidates/rm-roster', [ServiceCompanyBenchCandidatesController::class, 'rmRosterJson'])->name('bench-candidates.rm-roster');
                Route::get('bench-candidates/talent_pool', [ServiceCompanyBenchCandidatesController::class, 'talent_pool'])->name('bench-candidates.talent_pool');
                Route::get('bench-candidates/benchListingPoolData', [ServiceCompanyBenchCandidatesController::class, 'benchListingPoolData'])->name('bench-candidates.benchListingPoolData');
                Route::get('bench-candidates/placedListing', [ServiceCompanyBenchCandidatesController::class, 'placedListing'])->name('bench-candidates.placedListing');
                Route::get('bench-candidates/placedListingData', [ServiceCompanyBenchCandidatesController::class, 'placedListingData'])->name('bench-candidates.placedListingData');
                Route::get('bench-candidates_ajax/placedListingData', [ServiceCompanyBenchCandidatesController::class, 'placedListingData_ajax'])->name('bench-candidates.placedListingData_ajax');
                Route::get('bench-candidates_ajax/v2/placedListingData', [ServiceCompanyBenchCandidatesController::class, 'placedListingData_v2'])->name('bench-candidates.placedListingData_v2');
                Route::get('bench-candidates_ajax/v2/placedListingStats', [ServiceCompanyBenchCandidatesController::class, 'placedListingStats_v2'])->name('bench-candidates.placedListingStats_v2');
                Route::post('bench-candidates/save-assignment-details', [ServiceCompanyBenchCandidatesController::class, 'savePlacedAssignmentDetails'])->name('bench-candidates.savePlacedAssignmentDetails');
                Route::post('bench-candidates/save-commercial-config', [ServiceCompanyBenchCandidatesController::class, 'savePlacedCommercialConfig'])->name('bench-candidates.savePlacedCommercialConfig');
                Route::get('bench-candidates/offerListing', [ServiceCompanyBenchCandidatesController::class, 'offerListing'])->name('bench-candidates.offerListing');
                Route::get('bench-candidates/offerListingData', [ServiceCompanyBenchCandidatesController::class, 'offerListingData'])->name('bench-candidates.offerListingData');
                Route::get('bench-candidates_ajax/offerListingData', [ServiceCompanyBenchCandidatesController::class, 'offerListingData_ajax'])->name('bench-candidates.offerListingData_ajax');
                Route::get('bench-candidates/staffing-dashboard', [ServiceCompanyBenchCandidatesController::class, 'staffingDashboard'])->name('bench-candidates.staffingDashboard');
                Route::get('bench-candidates/data', [ServiceCompanyBenchCandidatesController::class, 'data'])->name('bench-candidates.data');
                Route::get('bench-candidates_ajax/data', [ServiceCompanyBenchCandidatesController::class, 'data_ajax'])->name('bench-candidates.data_ajax');
                Route::get('bench-candidates_ajax/v2/data', [ServiceCompanyBenchCandidatesController::class, 'data_v2'])->name('bench-candidates.data_v2');
                Route::get('countDataGetBenchList', [ServiceCompanyBenchCandidatesController::class, 'countDataGetBenchList'])->name('bench-candidates.countDataGetBenchList');
                Route::get('bench-candidates/bench_explore_listing', [ServiceCompanyBenchCandidatesController::class, 'bench_explore_listing'])->name('bench-candidates.bench_explore_listing');
                Route::get('bench-candidates_explore_job_ajax/bench_explore_listing', [ServiceCompanyBenchCandidatesController::class, 'bench_explore_listing_job_ajax'])->name('bench-candidates.bench_explore_listing_job_ajax');
                Route::get('bench-candidates/hmlisting/{id?}', [ServiceCompanyBenchCandidatesController::class, 'hmlisting'])->name('bench-candidates.hmlisting');
                Route::get('bench-candidates/bench_explore/{id?}/{stage?}', [ServiceCompanyBenchCandidatesController::class, 'bench_explore'])->name('bench-candidates.bench_explore');
                Route::get('bench-candidates/bench_explore_listing_counter/{bench_id?}/{stage?}/{inputSearch?}', [ServiceCompanyBenchCandidatesController::class, 'bench_explore_listing_counter'])->name('bench-candidates.bench_explore_listing_counter');
                Route::get('bench-candidates/bench_assign_to_job/{id?}', [ServiceCompanyBenchCandidatesController::class, 'bench_assign_to_job'])->name('bench-candidates.bench_assign_to_job');
                Route::get('bench-candidates/public_show/{id?}', [ServiceCompanyBenchCandidatesController::class, 'public_show'])->name('bench-candidates.public_show');
                Route::get('bench-candidates/bench_assign_listing/{id?}', [ServiceCompanyBenchCandidatesController::class, 'bench_assign_listing'])->name('bench-candidates.bench_assign_listing');
                Route::get('bench-candidates/bench_application_status/{id?}', [ServiceCompanyBenchCandidatesController::class, 'bench_application_status'])->name('bench-candidates.bench_application_status');
                Route::get('bench-candidates/add_custom_detail/{counter}', [ServiceCompanyBenchCandidatesController::class, 'add_custom_detail'])->name('bench-candidates.add_custom_detail');
                Route::get('bench-candidates/download/{id}', [ServiceCompanyBenchCandidatesController::class, 'download'])->name('bench-candidates.download');
                Route::get('bench-candidates/bench_assign_to_job_listing', [ServiceCompanyBenchCandidatesController::class, 'bench_assign_to_job_listing'])->name('bench-candidates.bench_assign_to_job_listing');
                Route::get('bench-candidates/recommend_req/{id?}', [ServiceCompanyBenchCandidatesController::class, 'recommend_req'])->name('bench-candidates.recommend_req');
                Route::get('bench-candidates/recommend_internal_req/{id?}', [ServiceCompanyBenchCandidatesController::class, 'recommend_internal_req'])->name('bench-candidates.recommend_internal_req');
                // Intent: Lazy-loading tab endpoints for Bench Candidate Detail V2 (Ads Setting & Resume/CV)
                Route::get('bench-candidates/{id}/tab/ads', [ServiceCompanyBenchCandidatesController::class, 'getAdsTab'])->name('bench-candidates.tab.ads');
                Route::get('bench-candidates/{id}/tab/resume', [ServiceCompanyBenchCandidatesController::class, 'getResumeTab'])->name('bench-candidates.tab.resume');

                Route::middleware('resolve.taxonomy')->group(function () {
                    Route::resource('bench-candidates', ServiceCompanyBenchCandidatesController::class);
                });
                Route::post('bench-candidates/update_placed_end_date', [ServiceCompanyBenchCandidatesController::class, 'update_placed_end_date'])->name('bench-candidates.update_placed_end_date');
                Route::post('bench-candidates/status_submit', [ServiceCompanyBenchCandidatesController::class, 'status_submit'])->name('bench-candidates.status_submit');
                Route::match(['get', 'post'], 'jobs/recommandedrequestcondidate/{jobId}', [ServiceCompanyBenchCandidatesController::class, 'recommandedrequestcondidate'])->name('jobs.recommandedrequestcondidate');
                Route::get('jobs/mailrecommandedrequestcondidate/{jobId}', [ServiceCompanyBenchCandidatesController::class, 'mailrecommandedrequestcondidate'])->name('jobs.mailrecommandedrequestcondidate');
                Route::get('services-job', [ServiceCompanyJobsController::class, 'jobServices'])->name('jobs.jobServices');
                Route::get('jobs/serviceviewmore/{id?}', [ServiceCompanyJobsController::class, 'serviceviewmore'])->name('jobs.serviceviewmore');
                Route::post('add-update-services-job', [ServiceCompanyJobsController::class, 'jobServicesAddUpdate'])->name('jobs.addUpdateJobServices');
                Route::post('jobs/updateserviceviewmore', [ServiceCompanyJobsController::class, 'updateserviceviewmore'])->name('jobs.updateserviceviewmore');
                Route::post('download-resume-bench-candidates/', [ServiceCompanyBenchCandidatesController::class, 'downloadResumeBenchCandidate'])->name('bench-candidates.downloadResumeBenchCandidate');
                // Intent: Isolated Forward Resume endpoints for Bench Talent Drawer
                Route::post('bench-candidates/fwd_resume', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume'])->name('bench-candidates.fwd_resume');
                Route::post('bench-candidates/fwd_resume_send', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_send'])->name('bench-candidates.fwd_resume_send');
                Route::post('bench-candidates/quick_upload_resume', [ServiceCompanyBenchCandidatesController::class, 'quick_upload_resume'])->name('bench-candidates.quick_upload_resume');
                Route::post('bench-candidates/fwd_resume_intent', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_intent'])->name('bench-candidates.fwd_resume_intent');
                Route::post('bench-candidates/fwd_resume_presigned', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_presigned'])->name('bench-candidates.fwd_resume_presigned');
                Route::post('bench-candidates/fwd_resume_finalize', [ServiceCompanyBenchCandidatesController::class, 'fwd_resume_finalize'])->name('bench-candidates.fwd_resume_finalize');
                // Intent: Isolated Download Resume Center endpoints for Bench Talent Drawer (Engine 1-B)
                Route::post('bench-candidates/download_center', [ServiceCompanyBenchCandidatesController::class, 'download_center'])->name('bench-candidates.download_center');
                Route::post('bench-candidates/record_download', [ServiceCompanyBenchCandidatesController::class, 'record_download'])->name('bench-candidates.record_download');

                // END
                // #############       	START CUSTOM REPORT MANAGEMENT
                Route::resource('customreport', ServiceCompanyCustomreportController::class);
                // END
                // #############			START COMPANY ACCOUNT

                Route::get('career-page/settings', [ServiceCompanyAccountSettingsController::class, 'careerPageSettings'])->name('career.page.settings');
                Route::post('career-page/settings/save', [ServiceCompanyAccountSettingsController::class, 'careerPageSettingsSave'])->name('career.page.settings.save');

                Route::get('account/onboarding', [ServiceCompanyAccountSettingsController::class, 'onboarding'])->name('account.onboarding');
                Route::post('account/onboarding/save', [ServiceCompanyAccountSettingsController::class, 'onboardingSave'])->name('account.onboarding.save');
                Route::get('account/google/{id?}', [ServiceCompanyAccountSettingsController::class, 'google'])->name('account.google');
                Route::resource('account', ServiceCompanyAccountSettingsController::class);
                Route::get('account/tab/{tabName}', [ServiceCompanyAccountSettingsController::class, 'getTabContent'])->name('account.tab');
                Route::post('account/update/{id}', [ServiceCompanyAccountSettingsController::class, 'update'])->name('account.update');
                Route::post('account/videoMeetingSeetingUpdate', [ServiceCompanyAccountSettingsController::class, 'videoMeetingSeetingUpdate'])->name('account.videoMeetingSeetingUpdate');
                Route::post('boostkeywordadd', [ServiceCompanyAccountSettingsController::class, 'boostkeywordadd'])->name('account.boostkeywordadd');
                Route::get('kayaccount/key-add-create', [ServiceCompanyAccountSettingsController::class, 'keyaddcreate'])->name('account.key_add_create');
                Route::post('boostStatusUpdate', [ServiceCompanyAccountSettingsController::class, 'boostStatusUpdate'])->name('account.boostStatusUpdate');

                // END
                // #############       	START TEAM MANAGEMENT

                Route::get('team/appdata', [ServiceCompanyTeamController::class, 'appdata'])->name('team.appdata');
                Route::get('candidate_team_ajax/appdata', [ServiceCompanyTeamController::class, 'appdata_ajax'])->name('team.appdata_ajax');
                Route::get('team/intdata', [ServiceCompanyTeamController::class, 'intdata'])->name('team.intdata');
                Route::get('team_ajax_intdata/intdata', [ServiceCompanyTeamController::class, 'intdata_ajax'])->name('team.intdata_ajax');
                Route::get('team/joiningdata', [ServiceCompanyTeamController::class, 'joiningdata'])->name('team.joiningdata');
                Route::get('team_ajax_joiningdata/joiningdata', [ServiceCompanyTeamController::class, 'joiningdata_ajax'])->name('team.joiningdata_ajax');
                Route::get('team/data', [ServiceCompanyTeamController::class, 'data'])->name('team.data');
                Route::get('team_ajax/data', [ServiceCompanyTeamController::class, 'data_ajax'])->name('team.data_ajax');
                Route::get('team/editrole/{id?}', [ServiceCompanyTeamController::class, 'editrole'])->name('team.editrole');
                Route::get('team/viewmore/{id?}', [ServiceCompanyTeamController::class, 'viewMore'])->name('team.viewmore');
                Route::get('team/info/{id?}', [ServiceCompanyTeamController::class, 'info'])->name('team.info');
                Route::get('team/add_team_member_form/{id?}', [ServiceCompanyAccountSettingsController::class, 'add_team_member_form'])->name('team.add_team_member_form');
                Route::get('team/jobdata', [ServiceCompanyTeamController::class, 'jobdata'])->name('team.jobdata');
                Route::get('team_ajax/jobdata', [ServiceCompanyTeamController::class, 'jobdata_ajax'])->name('team.jobdata_ajax');
                Route::get('team_ajax/rankdata', [ServiceCompanyTeamController::class, 'rankdata_ajax'])->name('team.rankdata_ajax');
                Route::get('team_ajax/dashboarddata', [ServiceCompanyTeamController::class, 'dashboarddata_ajax'])->name('team.dashboarddata_ajax');
                Route::get('team/assigndata', [ServiceCompanyTeamController::class, 'assigndata'])->name('team.assigndata');
                Route::get('team/scoreboard', [ServiceCompanyTeamController::class, 'teamScoreboard'])->name('team.scoreboard');
                Route::resource('team', ServiceCompanyTeamController::class)->except(['show']);
                Route::post('team/resetpassword', [ServiceCompanyTeamController::class, 'resetpassword'])->name('team.resetpassword');
                Route::post('team/statusUpdate', [ServiceCompanyTeamController::class, 'statusUpdate'])->name('team.statusUpdate');
                Route::post('team/teamDelete', [ServiceCompanyTeamController::class, 'teamDelete'])->name('team.teamDelete');
                Route::post('team/reSendmail', [ServiceCompanyTeamController::class, 'reSendmail'])->name('team.reSendmail');
                Route::any('team/roleupdate', [ServiceCompanyTeamController::class, 'roleupdate'])->name('team.roleupdate');
                Route::post('team/WorkingStatus', [ServiceCompanyTeamController::class, 'WorkingStatus'])->name('team.WorkingStatus');

                // END
                // #############       	START ROLES AND PERMISSION
                Route::get('role-permission/show-role/{id?}', [ServiceCompanyRolesPermissionController::class, 'showRole'])->name('settings.showRole');
                // Route::get('role-permission/teamdata', [ServiceCompanyRolesPermissionController::class,'teamdata')->name('settings.teamdata');
                Route::get('role-permission/loadEditRole/{id?}', [ServiceCompanyRolesPermissionController::class, 'loadEditRole'])->name('settings.loadEditRole');
                Route::get('role-permission/loadRole/{id?}', [ServiceCompanyRolesPermissionController::class, 'loadRole'])->name('settings.loadRole');
                Route::get('role-permission/showMembers/{id}', [ServiceCompanyRolesPermissionController::class, 'showMembers']);
                Route::resource('role-permission', ServiceCompanyRolesPermissionController::class);
                Route::post('role-permission/assignAllPermission', [ServiceCompanyRolesPermissionController::class, 'assignAllPermission']);
                Route::post('role-permission/removeAllPermission', [ServiceCompanyRolesPermissionController::class, 'removeAllPermission']);
                Route::post('role-permission/assignRole', [ServiceCompanyRolesPermissionController::class, 'assignRole']);
                Route::post('role-permission/detachRole', [ServiceCompanyRolesPermissionController::class, 'detachRole']);
                Route::post('role-permission/storeRole', [ServiceCompanyRolesPermissionController::class, 'storeRole']);
                Route::post('role-permission/deleteRole', [ServiceCompanyRolesPermissionController::class, 'deleteRole']);
                Route::post('role-permission/saveRole', [ServiceCompanyRolesPermissionController::class, 'saveRole'])->name('settings.saveRole');
                // Route::post('role-permission/updateRole', [ServiceCompanyRolesPermissionController::class,'updateRole')->name('settings.updateRole');
                Route::get('manual-delete-role/{id?}', [ServiceCompanyRolesPermissionController::class, 'manualDeleteRole'])->name('settings.manualDeleteRole');
                Route::delete('role-permission/removerole/{user_id?}', [ServiceCompanyRolesPermissionController::class, 'removerole'])->name('settings.removerole');

                // END
                // ############       	START ACCOUNT MANAGEMENT
                Route::get('account/google/{id?}', [ServiceCompanyAccountSettingsController::class, 'google'])->name('account.google');
                Route::get('account/zoom/{id?}', [ServiceCompanyAccountSettingsController::class, 'zoom'])->name('account.zoom');
                Route::get('account/add_team_member_form/{id?}', [ServiceCompanyAccountSettingsController::class, 'add_team_member_form'])->name('account.add_team_member_form');
                Route::post('account/addIntStage', [ServiceCompanyAccountSettingsController::class, 'addIntStage'])->name('account.addIntStage');
                Route::post('account/delIntStage', [ServiceCompanyAccountSettingsController::class, 'delIntStage'])->name('account.delIntStage');
                Route::post('account/updatePriority', [ServiceCompanyAccountSettingsController::class, 'updatePriority'])->name('account.updatePriority');
                Route::post('account/storeDept', [ServiceCompanyAccountSettingsController::class, 'storeDept'])->name('account.storeDept');
                Route::post('account/sendinvitation', [AdminAccountSettingsController::class, 'sendinvitation'])->name('account.sendinvitation');
                Route::post('addressupdate', [ServiceCompanyAccountSettingsController::class, 'addressupdate'])->name('account.addressupdate');
                Route::get('offer-amount-change/{id?}', [ServiceCompanyAccountSettingsController::class, 'offerAmountChange'])->name('account.offerAmountChange');
                Route::post('companysavecreateoffer', [ServiceCompanyAccountSettingsController::class, 'companysavecreateoffer'])->name('account.companysavecreateoffer');

                // END
                Route::get('job-categories/data', [ServiceCompanyJobCategoryController::class, 'data'])->name('job-categories.data');
                Route::get('job-categories/export/{start}/{length}', [ServiceCompanyJobCategoryController::class, 'export'])->name('job-categories.export');
                Route::resource('job-categories', ServiceCompanyJobCategoryController::class);
                Route::resource('applicant-note', ApplicantNoteController::class);
                Route::get('jobtracker/data', [ServiceCompanyJobTrackerController::class, 'data'])->name('jobtracker.data');
                Route::get('jobtracker/addclass/{id?}', [ServiceCompanyJobTrackerController::class, 'addclass'])->name('jobtracker.addclass');
                Route::post('jobtracker/store', [ServiceCompanyJobTrackerController::class, 'store'])->name('jobtracker.store');
                Route::post('jobtracker/updateinformation', [ServiceCompanyJobTrackerController::class, 'updateinformation'])->name('jobtracker.updateinformation');

                Route::group(['prefix' => 'settings'], function () {
                    // Delete account
                    Route::get('delete-account', [CompanySettingsController::class, 'deleteAccount'])->name('settings.delete-account');
                    Route::post('delete-account-store', [CompanySettingsController::class, 'deleteAccountStore'])->name('settings.delete-account-store');

                    // Company settings
                    Route::get('/', [CompanySettingsController::class, 'index'])->name('settings.index');
                    Route::get('{id}/edit', [CompanySettingsController::class, 'edit'])->name('settings.edit');
                    Route::put('{id}', [CompanySettingsController::class, 'update'])->name('settings.update');
                    Route::post('{id}', [CompanySettingsController::class, 'update'])->name('settings.update.post');
                    Route::resource('application-setting', ApplicationSettingsController::class);

                    // Role permission routes
                    Route::post('role-permission/assignAllPermission', [ManageRolePermissionController::class, 'assignAllPermission'])->name('role-permission.assignAllPermission');
                    Route::post('role-permission/removeAllPermission', [ManageRolePermissionController::class, 'removeAllPermission'])->name('role-permission.removeAllPermission');
                    Route::post('role-permission/assignRole', [ManageRolePermissionController::class, 'assignRole'])->name('role-permission.assignRole');
                    Route::post('role-permission/detachRole', [ManageRolePermissionController::class, 'detachRole'])->name('role-permission.detachRole');
                    Route::post('role-permission/storeRole', [ManageRolePermissionController::class, 'storeRole'])->name('role-permission.storeRole');
                    Route::post('role-permission/deleteRole', [ManageRolePermissionController::class, 'deleteRole'])->name('role-permission.deleteRole');
                    Route::get('role-permission/showMembers/{id}', [ManageRolePermissionController::class, 'showMembers'])->name('role-permission.showMembers');
                    // Route::resource('role-permission', ManageRolePermissionController::class);

                    // Other settings
                    Route::resource('theme-settings', ServiceCompanyThemeSettingsController::class);
                    Route::resource('linkedin-settings', ServiceCompanyLinkedInSettingsController::class);

                    // Additional custom role routes
                    Route::get('loadRole/{id?}', [ManageRolePermissionController::class, 'loadRole'])->name('settings.loadRole');
                    Route::get('loadEditRole/{id?}', [ManageRolePermissionController::class, 'loadEditRole'])->name('settings.loadEditRole');
                    Route::post('saveRole', [ManageRolePermissionController::class, 'saveRole'])->name('settings.saveRole');
                    Route::post('updateRole', [ManageRolePermissionController::class, 'updateRole'])->name('settings.updateRole');
                    Route::get('show-role/{id?}', [ManageRolePermissionController::class, 'showRole'])->name('settings.showRole');
                    Route::get('teamdata', [ManageRolePermissionController::class, 'teamdata'])->name('settings.teamdata');
                    Route::delete('removerole/{user_id?}', [ManageRolePermissionController::class, 'removerole'])->name('settings.removerole');

                });
                // ############       START PROFILE MANAGEMENT
                Route::resource('profile', ServiceCompanyProfileController::class);
                // END
                // ############       START STICKY NOTE MANAGEMENT
                Route::resource('sticky-note', ServiceCompanyStickyNotesController::class);
                // END
                Route::get('profileedit', [ServiceCompanyProfileController::class, 'profileedit'])->name('profileedit');
            });

            Route::get('subscribe/history', [ManageSubscriptionController::class, 'history'])->name('subscribe.history');
            Route::get('subscribe/historylisting', [ManageSubscriptionController::class, 'historylisting'])->name('subscribe.historylisting');
            Route::resource('subscribe', ManageSubscriptionController::class);
            Route::get('subscribe/cart/{id?}', [ManageSubscriptionController::class, 'cart'])->name('subscribe.cart');
            Route::get('subscribe/viewmore/{id?}', [ManageSubscriptionController::class, 'viewMore'])->name('subscribe.viewmore');

            Route::get('transection-log', [ManageSubscriptionController::class, 'transectionLog'])->name('subscribe.transectionLog');
            // V2 AJAX data endpoint — returns JSON for tab content, filters, search, pagination
            Route::get('transection-log/data', [ManageSubscriptionController::class, 'transectionLogData'])->name('subscribe.transectionLogData');
            Route::get('new-subscribe/newindex', [ManageSubscriptionController::class, 'newindex'])->name('subscribe.newindex');
            Route::get('transactions/subscription', [ManageSubscriptionController::class, 'transactions'])->name('transactions.subscription');
            Route::get('transactions/subscription-ajax', [ManageSubscriptionController::class, 'transactionsAjax'])->name('transactions.subscriptionAjax');
            Route::get('checkout-subscribe', [ManageSubscriptionController::class, 'checkoutSubscribeIist'])->name('subscribe.checkoutSubscribeIist');
            Route::post('checkout-subscribe', [ManageSubscriptionController::class, 'checkoutSubscribe'])->name('subscribe.checkoutSubscribe');

            Route::post('custom-checkout-subscribe', [ManageSubscriptionController::class, 'customCheckoutSubscribe'])->name('subscribe.customCheckoutSubscribe');

            Route::get('deletecheckout-subscribe', [ManageSubscriptionController::class, 'deletecheckout'])->name('subscribe.deletecheckout');
            Route::post('checkout-subscribe/apply-coupon', [ManageSubscriptionController::class, 'applyCoupon'])->name('subscribe.applyCoupon');
            Route::post('checkout-subscribe/remove-coupon', [ManageSubscriptionController::class, 'removeCoupon'])->name('subscribe.removeCoupon');
            Route::get('cartcheckout-subscribe', [ManageSubscriptionController::class, 'cartcheckout'])->name('subscribe.cartcheckout');
            Route::get('receiptdownload-subscribe/{id?}', [ManageSubscriptionController::class, 'receiptdownload'])->name('subscribe.receiptdownload');

            Route::resource('auth-custom-package', AuthCustomPackageController::class);
            Route::post('auth-custom-package/validatePackage', [AuthCustomPackageController::class, 'validatePackage'])->name('auth-custom-package.validatePackage');
            Route::post('auth-custom-package/validateClientPackage', [AuthCustomPackageController::class, 'validateClientPackage'])->name('auth-custom-package.validateClientPackage');

            Route::post('todo-items/update-todo-item', [ServiceCompanyTodoItemController::class, 'updateTodoItem'])->name('todo-items.updateTodoItem');
            Route::resource('todo-items', ServiceCompanyTodoItemController::class);
            Route::resource('report', ServiceCompanyReportController::class);
            Route::get('report/data', [ServiceCompanyReportController::class, 'data'])->name('report.data');
        });
    });

});

// Route::prefix('service-company')->group(function () {
// 	Route::get('/dashboard', [ServiceCompanyDashboardController::class, 'index'])->name('dashboard');

//     // Route::get('/', function () {
//     //     return 'Service Dashboard';
//     // });

//     Route::get('/list', function () {
//         return 'Service List';
//     });
// });
