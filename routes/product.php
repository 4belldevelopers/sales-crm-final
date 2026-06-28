<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::prefix('product-company')->group(function () {
    Route::get('/', function () {
        return 'Product Dashboard';
    });

    Route::get('/list', function () {
        return 'Product List';
    });
});

Route::group(['middleware' => 'auth'], function () {

//########################################################################### PRODUCT COMPANY ######################################################################
Route::group(['namespace' => 'ProductCompany', 'prefix' => 'product_company', 'as' => 'product_company.'], function () {

Route::group(['middleware' => ['custom-package']], function () {

//#############       	 DASHBOARD MANAGEMENT
Route::get('/dashboard', 'ProductCompanyDashboardController@index')->name('dashboard');
Route::resource('todo-items', 'ProductCompanyTodoItemController');
Route::post('todo-items/update-todo-item', 'ProductCompanyTodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');
//END

//#############       	 CLIENTS MANAGEMENT
Route::get('clients/citylist/{state_id?}', 'ProductCompanyClientController@citylist')->name('clients.citylist');
Route::get('clients/addresslist/{cid?}', 'ProductCompanyClientController@addresslist')->name('clients.addresslist');
Route::resource('clients', 'ProductCompanyClientController');
//END 
   
//#############       	 JOBS MANAGEMENT 
Route::get('jobs/resend_job_recommanded_request/{id?}', 'ProductCompanyJobsController@resend_job_recommanded_request')->name('jobs.resend_job_recommanded_request');
Route::get('jobs/removeJob', 'ProductCompanyJobsController@removeJob')->name('jobs.removeJob');
Route::get('jobs/assignRejected', 'ProductCompanyJobsController@assignRejected')->name('jobs.assignRejected');
Route::get('jobs/send_job_recommanded_request/{id?}', 'ProductCompanyJobsController@send_job_recommanded_request')->name('jobs.send_job_recommanded_request');
Route::get('jobs/rsp_job_request_send/{id?}', 'ProductCompanyJobsController@rsp_job_request_send')->name('jobs.rsp_job_request_send');
Route::get('jobs/send_only_job_invitation/{id?}', 'ProductCompanyJobsController@send_only_job_invitation')->name('jobs.send_only_job_invitation');

Route::get('jobs/add_to_list/{jobId?}', 'ProductCompanyJobsController@add_to_list')->name('jobs.add_to_list');

Route::get('jobs/clientviewmore/{id?}', 'ProductCompanyJobsController@clientviewmore')->name('jobs.clientviewmore');
Route::get('jobs/clienthmlisting/{id?}', 'ProductCompanyJobsController@clienthmlisting')->name('jobs.clienthmlisting'); 
Route::get('jobs/currentteamlisting/{id?}', 'ProductCompanyJobsController@currentteamlisting')->name('jobs.currentteamlisting'); 

Route::get('jobs/staffingPartnersRegister', 'ProductCompanyJobsController@staffingPartnersRegister')->name('jobs.staffingPartnersRegister');
Route::get('jobs/recommendedAssignList', 'ProductCompanyJobsController@recommendedAssignList')->name('jobs.recommendedAssignList');
Route::get('jobs/hmlistingupdate/{id?}', 'ProductCompanyJobsController@hmlistingupdate')->name('jobs.hmlistingupdate'); 		
Route::get('jobs/getContactPerson/{client_id}', 'ProductCompanyJobsController@getContactPerson')->name('jobs.getContactPerson');
Route::get('jobs/hmlisting/{id?}', 'ProductCompanyJobsController@hmlisting')->name('jobs.hmlisting'); 
Route::get('jobs/editJobStatus/{id?}', 'ProductCompanyJobsController@editJobStatus')->name('jobs.editJobStatus');
Route::get('jobs/spartnerlisting/{id?}', 'ProductCompanyJobsController@spartnerlisting')->name('jobs.spartnerlisting');
Route::get('jobs/viewmore/{id?}', 'ProductCompanyJobsController@viewMore')->name('jobs.viewmore');
Route::get('jobs/data', 'ProductCompanyJobsController@data')->name('jobs.data');	
Route::get('jobs/priority/{id?}', 'ProductCompanyJobsController@priority')->name('jobs.priority');
Route::get('jobs/export/{start}/{length}', 'ProductCompanyJobsController@export')->name('jobs.export');
Route::get('jobs/hiring_manager/{id?}', 'ProductCompanyJobsController@hiring_manager')->name('jobs.hiring_manager'); 
Route::get('jobs/recruiter/{id?}', 'ProductCompanyJobsController@recruiter')->name('jobs.recruiter'); 
Route::get('jobs/boostkeywords', 'ProductCompanyJobsController@boostkeywords')->name('jobs.boostkeywords');		
Route::get('jobs/recommendedData', 'ProductCompanyJobsController@recommendedData')->name('jobs.recommendedData');
Route::resource('jobs', 'ProductCompanyJobsController');
Route::post('jobs/update/{id}', 'ProductCompanyJobsController@update')->name('jobs.update');
Route::post('jobs/updatestatus', 'ProductCompanyJobsController@updatestatus')->name('jobs.updatestatus');
Route::post('jobs/hiring_manager/{id?}', 'ProductCompanyJobsController@hiring_manager')->name('jobs.hiring_manager'); 
Route::post('jobs/recruiter/{id?}', 'ProductCompanyJobsController@recruiter')->name('jobs.recruiter'); 	
Route::post('jobs/update/{id}', 'ProductCompanyJobsController@update')->name('jobs.update');					 
						
//END

//#############       	 JOB TRACKER MANAGEMENT
Route::get('jobtracker/addclass/{id?}', 'ProductCompanyJobTrackerController@addclass')->name('jobtracker.addclass');
Route::post('jobtracker/store', 'ProductCompanyJobTrackerController@store')->name('jobtracker.store');
Route::post('jobtracker/updateinformation', 'ProductCompanyJobTrackerController@updateinformation')->name('jobtracker.updateinformation');
//END

//#############       	 APP TRACKER MANAGEMENT
Route::get('apptracker/similar_candidates/{id}', 'ProductCompanyAppTrackerController@similar_candidates')->name('apptracker.similar_candidates');
Route::get('apptracker/internal_bench_data', 'ProductCompanyAppTrackerController@internal_bench_data')->name('apptracker.internal_bench_data');	
Route::get('apptracker/internal_bench/{id}', 'ProductCompanyAppTrackerController@internal_bench')->name('apptracker.internal_bench');
Route::get('apptracker/public_bench_data', 'ProductCompanyAppTrackerController@public_bench_data')->name('apptracker.public_bench_data');

Route::get('apptracker/candidates_request_data', 'ProductCompanyAppTrackerController@candidates_request_data')->name('apptracker.candidates_request_data');

Route::get('apptracker/public_bench/{id}', 'ProductCompanyAppTrackerController@public_bench')->name('apptracker.public_bench');
Route::get('apptracker/rec_candidate_assign', 'ProductCompanyAppTrackerController@rec_candidate_assign')->name('apptracker.rec_candidate_assign'); 
Route::get('apptracker/similar_existing_data', 'ProductCompanyAppTrackerController@similar_existing_data')->name('apptracker.similar_existing_data');	
Route::get('apptracker/candidates_request', 'ProductCompanyAppTrackerController@candidates_request')->name('apptracker.candidates_request');
//Route::get('apptracker/similar_candidates/{id}', 'ProductCompanyAppTrackerController@similar_candidates')->name('apptracker.similar_candidates');
Route::get('apptracker/benchReqViewMore/{id}/{job_applications_id}', 'ProductCompanyAppTrackerController@benchReqViewMore')->name('apptracker.benchReqViewMore');	
Route::get('apptracker/show-interviews/{id?}', 'ProductCompanyAppTrackerController@showInterviews')->name('apptracker.show-interviews');
Route::get('apptracker/sub_stage/{status?}/{application_id?}/{job_id?}', 'ProductCompanyAppTrackerController@sub_stage')->name('apptracker.sub_stage');
Route::get('apptracker/loadcounter/{jobid?}/{mstatus_id?}/{inputSearch?}', 'ProductCompanyAppTrackerController@loadcounter')->name('apptracker.loadcounter');
Route::get('apptracker/data', 'ProductCompanyAppTrackerController@data')->name('apptracker.data'); 
Route::get('apptracker/export/{jobs}/{mstatus_id}/{start}/{length}', 'ProductCompanyAppTrackerController@export')->name('apptracker.export');
Route::get('apptracker/{id1?}/{id12}', 'ProductCompanyAppTrackerController@index')->name('apptracker');
Route::get('apptracker/openRejFrm/{application_id?}/{job_id?}/{parent_status_id?}/{current_child_id?}', 'ProductCompanyAppTrackerController@openRejFrm')->name('apptracker.openRejFrm');
Route::post('apptracker/store', 'ProductCompanyAppTrackerController@store')->name('apptracker.store');
Route::post('apptracker/getchild', 'ProductCompanyAppTrackerController@getchild')->name('apptracker.getchild');
//END


//#############       	 JOB APPLICATION MANAGEMENT 
Route::get('job-applications/recruiter/{id?}', 'ProductCompanyJobApplicationController@recruiter')->name('job-applications.recruiter'); 
Route::get('job-applications/download/{id}', 'ProductCompanyJobApplicationController@download')->name('job-applications.download');
Route::get('job-applications/question/{jobID?}/{applicationId?}', 'ProductCompanyJobApplicationController@jobQuestion')->name('job-applications.question');
Route::get('job-applications/getAdditionalDetail/{job_id?}', 'ProductCompanyJobApplicationController@getAdditionalDetail')->name('job-applications.getAdditionalDetail');
Route::get('job-applications/create/{jobId?}/{appId?}', 'ProductCompanyJobApplicationController@create')->name('job-applications.create');
Route::get('job-applications/interview-show/{id?}', 'ProductCompanyJobApplicationController@interviewShow')->name('job-applications.interview-show');
Route::get('job-applications/statelist/{cid?}', 'ProductCompanyJobApplicationController@statelist')->name('job-applications.statelist');
	
Route::get('job-applications/create-schedule/{id?}', 'ProductCompanyJobApplicationController@createSchedule')->name('job-applications.create-schedule');
Route::get('job-applications/stage_body_content/{id?}', 'ProductCompanyJobApplicationController@stage_body_content')->name('job-applications.stage_body_content');
Route::get('job-applications/child_stage/{id?}', 'ProductCompanyJobApplicationController@child_stage')->name('job-applications.child_stage');

Route::resource('job-applications', 'ProductCompanyJobApplicationController');
Route::post('job-applications/stage_submit', 'ProductCompanyJobApplicationController@stage_submit')->name('job-applications.stage_submit');	
Route::post('job-applications/statusupdate', 'ProductCompanyJobApplicationController@statusupdate')->name('job-applications.statusupdate');
Route::post('job-applications/getinterviewchild', 'ProductCompanyJobApplicationController@getinterviewchild')->name('job-applications.getinterviewchild');
Route::post('job-applications/getGoogleVideoUrl', 'ProductCompanyJobApplicationController@getGoogleVideoUrl')->name('job-applications.getGoogleVideoUrl');
Route::post('job-applications/emailschedule', 'ProductCompanyJobApplicationController@emailSchedule')->name('job-applications.emailschedule');
Route::post('job-applications/store-schedule', 'ProductCompanyJobApplicationController@storeSchedule')->name('job-applications.store-schedule');
Route::post('job-applications/accessEmailTemplate', 'ProductCompanyJobApplicationController@accessEmailTemplate')->name('job-applications.accessEmailTemplate');
Route::post('job-applications/resume_delete', 'ProductCompanyJobApplicationController@resume_delete')->name('job-applications.resume_delete');
Route::post('job-applications/getVideoUrl', 'ProductCompanyJobApplicationController@getVideoUrl')->name('job-applications.getVideoUrl');
Route::post('job-applications/update/{id}', 'ProductCompanyJobApplicationController@update')->name('job-applications.update');	
Route::post('job-applications/recruiter/{id?}', 'ProductCompanyJobApplicationController@recruiter')->name('job-applications.recruiter');

//END

//#############       	 CVTRACKER MANAGEMENT
Route::get('cvtracker/copyjobtable', 'ProductCompanyCvTrackerController@copyjobtable')->name('cvtracker.copyjobtable');
Route::get('cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', 'ProductCompanyCvTrackerController@export')->name('cvtracker.export');
Route::get('cvtracker/candidateRightSideBar/{id?}', 'ProductCompanyCvTrackerController@candidateRightSideBar')->name('cvtracker.candidateRightSideBar');
Route::get('cvtracker/resume/{id}', 'ProductCompanyCvTrackerController@resume')->name('cvtracker.resume');
Route::get('cvtracker/data', 'ProductCompanyCvTrackerController@data')->name('cvtracker.data');
Route::get('cvtracker/create_status/{id?}', 'ProductCompanyCvTrackerController@create_status')->name('cvtracker.create_status');
Route::resource('cvtracker', 'ProductCompanyCvTrackerController');
Route::post('cvtracker/application_copy', 'ProductCompanyCvTrackerController@application_copy')->name('cvtracker.application_copy');
Route::post('cvtracker/fwd_resume/{id?}', 'ProductCompanyCvTrackerController@fwd_resume')->name('cvtracker.fwd_resume');
Route::post('cvtracker/app_copy', 'ProductCompanyCvTrackerController@app_copy')->name('cvtracker.app_copy');
Route::post('cvtracker/copy_current_selected_applicant', 'ProductCompanyCvTrackerController@copy_current_selected_applicant')->name('cvtracker.copy_current_selected_applicant');
Route::post('cvtracker/get_client_job', 'ProductCompanyCvTrackerController@get_client_job')->name('cvtracker.get_client_job');
Route::post('cvtracker/candidate_process_update', 'ProductCompanyCvTrackerController@candidate_process_update')->name('cvtracker.candidate_process_update');
Route::post('cvtracker/email_template', 'ProductCompanyCvTrackerController@email_template')->name('cvtracker.email_template');	
Route::post('cvtracker/fwd_resume_send', 'ProductCompanyCvTrackerController@fwd_resume_send')->name('cvtracker.fwd_resume_send');
//END

//#############       	 INTERVIEW_SCHEDULE MANAGEMENT
Route::get('interview-schedule/create_feedbackfrm/{application_id?}/{interview_id?}/{feedback_id?}', 'InterviewScheduleController@create_feedbackfrm')->name('interview-schedule.create_feedbackfrm');
Route::get('interview-schedule/create_status/{application_id?}/{interview_id?}', 'InterviewScheduleController@create_status')->name('interview-schedule.create_status');
Route::get('interview-schedule/showfeedback/{id?}', 'InterviewScheduleController@showfeedback')->name('interview-schedule.showfeedback');
Route::get('interview-schedule/data', 'InterviewScheduleController@data')->name('interview-schedule.data');
Route::get('interview-schedule/interviewData', 'InterviewScheduleController@interviewData')->name('interview-schedule.interviewData');
Route::get('interview-schedule/interviewData', 'InterviewScheduleController@interviewData')->name('interview-schedule.interviewData');
Route::get('interview-schedule/table-view', 'InterviewScheduleController@table')->name('interview-schedule.table-view');
Route::get('interview-schedule/export/{start}/{length}', 'InterviewScheduleController@export')->name('interview-schedule.export');
Route::resource('interview-schedule', 'InterviewScheduleController');
Route::post('interview-schedule/change-status-multiple', 'InterviewScheduleController@changeStatusMultiple')->name('interview-schedule.change-status-multiple');
Route::post('interview-schedule/updateFeedbackStatus', 'InterviewScheduleController@updateFeedbackStatus')->name('interview-schedule.updateFeedbackStatus');
Route::post('interview-schedule/updateAvailableStatus', 'InterviewScheduleController@updateAvailableStatus')->name('interview-schedule.updateAvailableStatus');
Route::post('interview-schedule/updateInterview', 'InterviewScheduleController@updateInterview')->name('interview-schedule.updateInterview');
//END

//#############       	 OFFER MANAGEMENT
Route::get('applicationoffer/export/{start}/{length}', 'ProductCompanyApplicationOfferController@export')->name('applicationoffer.export');
Route::get('applicationoffer/data', 'ProductCompanyApplicationOfferController@data')->name('offer.data');
Route::resource('applicationoffer', 'ProductCompanyApplicationOfferController');
Route::post('applicationoffer/update_salary', 'ProductCompanyApplicationOfferController@updateSalary')->name('applicationoffer.update_salary');
Route::post('applicationoffer/update_application', 'ProductCompanyApplicationOfferController@update_application')->name('applicationoffer.update_application');
//END

//#############       	 JOB-ON-BOARD MANAGEMENT
Route::get('job-onboard/export/{start}/{length}', 'ProductCompanyJobOnboardController@export')->name('job-onboard.export');
Route::get('job-onboard/update-status/{id?}', 'ProductCompanyJobOnboardController@updateStatus')->name('job-onboard.update-status');
Route::get('job-onboard/send-offer/{id?}', 'ProductCompanyJobOnboardController@sendOffer')->name('job-onboard.send-offer');
Route::get('job-onboard/data', 'ProductCompanyJobOnboardController@data')->name('job-onboard.data');
Route::resource('job-onboard', 'ProductCompanyJobOnboardController');
//END 

//#############       	 STAFFING PARTNER MANAGEMENT
Route::get('consultancy/uncleared_request_data', 'ProductCompanyConsultancyController@uncleared_request_data')->name('consultancy.uncleared_request_data');
Route::get('consultancy/allreq/{id?}', 'ProductCompanyConsultancyController@allreq')->name('consultancy.allreq');
Route::get('consultancy/staffing_contact_list/{id?}', 'ProductCompanyConsultancyController@staffingContactList')->name('consultancy.staffing_contact_list');
Route::get('consultancy/dviewmore/{id?}', 'ProductCompanyConsultancyController@dviewMore')->name('consultancy.dviewmore');
Route::get('consultancy/export/{start}/{length}', 'ProductCompanyConsultancyController@export')->name('consultancy.export');
Route::get('consultancy/data', 'ProductCompanyConsultancyController@data')->name('consultancy.data');
Route::get('consultancy/staffing_invitation/{id?}', 'ProductCompanyConsultancyController@staffing_invitation')->name('consultancy.staffing_invitation');
Route::get('consultancy/requestPartners/{id?}', 'ProductCompanyConsultancyController@requestPartners')->name('consultancy.requestPartners');
Route::get('consultancy/suggestedPartners/{id?}', 'ProductCompanyConsultancyController@suggestedPartners')->name('consultancy.suggestedPartners');
Route::resource('consultancy', 'ProductCompanyConsultancyController');
//END 
 
//#############       	 ACCOUNT SETTING MANAGEMENT
Route::resource('account', 'ProductCompanyAccountSettingsController'); 
//END 

//#############       	 BENCH CANDIDATE MANAGEMENT  
Route::get('recommend/all_data', 'ProductCompanyBenchCandidatesController@all_data')->name('recommend.all_data'); 
Route::get('bench-candidates/recommend_req/{id?}', 'ProductCompanyBenchCandidatesController@recommend_req')->name('bench-candidates.recommend_req'); 
Route::get('bench-candidates/hmlistingupdate/{id?}', 'ProductCompanyBenchCandidatesController@hmlistingupdate')->name('bench-candidates.hmlistingupdate');
Route::get('bench-candidates/bench_explore_listing_counter/{bench_id?}/{stage?}/{inputSearch?}', 'ProductCompanyBenchCandidatesController@bench_explore_listing_counter')->name('bench-candidates.bench_explore_listing_counter');
Route::get('bench-candidates/bench_explore_listing', 'ProductCompanyBenchCandidatesController@bench_explore_listing')->name('bench-candidates.bench_explore_listing'); 
Route::get('bench-candidates/bench_explore/{id?}/{stage?}', 'ProductCompanyBenchCandidatesController@bench_explore')->name('bench-candidates.bench_explore');  
Route::get('bench-candidates/bench_assign_to_job_listing', 'ProductCompanyBenchCandidatesController@bench_assign_to_job_listing')->name('bench-candidates.bench_assign_to_job_listing');
Route::get('bench-candidates/download/{id}', 'ProductCompanyBenchCandidatesController@download')->name('bench-candidates.download');
Route::get('bench-candidates/add_custom_detail/{counter}', 'ProductCompanyBenchCandidatesController@add_custom_detail')->name('bench-candidates.add_custom_detail'); 
Route::get('bench-candidates/benchListingPoolData', 'ProductCompanyBenchCandidatesController@benchListingPoolData')->name('bench-candidates.benchListingPoolData');	
Route::get('bench-candidates/placedListingData', 'ProductCompanyBenchCandidatesController@placedListingData')->name('bench-candidates.placedListingData'); 
Route::get('bench-candidates/bench_assign_to_job/{id?}', 'ProductCompanyBenchCandidatesController@bench_assign_to_job')->name('bench-candidates.bench_assign_to_job'); 
Route::get('bench-candidates/offerListingData', 'ProductCompanyBenchCandidatesController@offerListingData')->name('bench-candidates.offerListingData');
Route::get('bench-candidates/bench_application_status/{id?}', 'ProductCompanyBenchCandidatesController@bench_application_status')->name('bench-candidates.bench_application_status');
Route::get('bench-candidates/hmlisting/{id?}', 'ProductCompanyBenchCandidatesController@hmlisting')->name('bench-candidates.hmlisting');
Route::get('bench-candidates/data', 'ProductCompanyBenchCandidatesController@data')->name('bench-candidates.data'); 
Route::get('bench-candidates/bench_assign_listing/{id?}', 'ProductCompanyBenchCandidatesController@bench_assign_listing')->name('bench-candidates.bench_assign_listing'); 
Route::get('bench-candidates/talent_pool', 'ProductCompanyBenchCandidatesController@talent_pool')->name('bench-candidates.talent_pool');
Route::get('bench-candidates/offerListing', 'ProductCompanyBenchCandidatesController@offerListing')->name('bench-candidates.offerListing');  
Route::get('bench-candidates/placedListing', 'ProductCompanyBenchCandidatesController@placedListing')->name('bench-candidates.placedListing'); 
Route::get('bench-candidates/public_show/{id?}', 'ProductCompanyBenchCandidatesController@public_show')->name('bench-candidates.public_show');     
Route::resource('bench-candidates', 'ProductCompanyBenchCandidatesController'); 
Route::post('bench-candidates/update_placed_end_date', 'ProductCompanyBenchCandidatesController@update_placed_end_date')->name('bench-candidates.update_placed_end_date');
Route::post('bench-candidates/status_submit', 'ProductCompanyBenchCandidatesController@status_submit')->name('bench-candidates.status_submit');

Route::get('jobs/recommandedrequestcondidate/{jobId}', 'ProductCompanyBenchCandidatesController@recommandedrequestcondidate')->name('jobs.recommandedrequestcondidate');
//END 

//#############       	 TEAM MANAGEMENT
Route::get('team/assigndata', 'ProductCompanyTeamController@assigndata')->name('team.assigndata');
Route::get('team/export/{start}/{length}', 'ProductCompanyTeamController@export')->name('team.export');
Route::post('team/change-role', 'ProductCompanyTeamController@changeRole')->name('team.changeRole');
Route::post('team/WorkingStatus', 'ProductCompanyTeamController@WorkingStatus')->name('team.WorkingStatus');
Route::post('team/statusUpdate', 'ProductCompanyTeamController@statusUpdate')->name('team.statusUpdate');
Route::get('team/editrole/{id?}', 'ProductCompanyTeamController@editrole')->name('team.editrole'); 
Route::get('team/viewmore/{id?}', 'ProductCompanyTeamController@viewMore')->name('team.viewmore');
Route::get('team/add_team_member_form/{id?}', 'ProductCompanyAccountSettingsController@add_team_member_form')->name('team.add_team_member_form');
Route::get('team/data', 'ProductCompanyTeamController@data')->name('team.data');
Route::get('team/info/{id?}', 'ProductCompanyTeamController@info')->name('team.info'); 
Route::get('team/joiningdata', 'ProductCompanyTeamController@joiningdata')->name('team.joiningdata');
Route::get('team/jobdata', 'ProductCompanyTeamController@jobdata')->name('team.jobdata');
Route::get('team/intdata', 'ProductCompanyTeamController@intdata')->name('team.intdata');
Route::get('team/appdata', 'ProductCompanyTeamController@appdata')->name('team.appdata');
Route::any('team/roleupdate', 'ProductCompanyTeamController@roleupdate')->name('team.roleupdate');
Route::resource('team', 'ProductCompanyTeamController');
Route::post('team/resetpassword', 'ProductCompanyTeamController@resetpassword')->name('team.resetpassword'); 
//END 
 
Route::group(['prefix' => 'settings'], function () {
            Route::get('settings/delete-account', ['as' => 'settings.delete-account', 'uses' => 'CompanySettingsController@deleteAccount']);
            Route::post('settings/delete-account-store', ['as' => 'settings.delete-account-store', 'uses' => 'CompanySettingsController@deleteAccountStore']);
            //                        Route::post('settings/delete-account-cancel', ['as' => 'settings.delete-account-cancel', 'uses' => 'ApplicationSettingsController@deleteAccountStore']);
            Route::resource('settings', 'CompanySettingsController', ['only' => ['edit', 'update', 'index']]);
            // Application Form routes
            Route::resource('application-setting', 'ApplicationSettingsController');			
            // Role permission routes
            Route::resource('settings', 'CompanySettingsController', ['only' => ['edit', 'update', 'index']]);
            Route::post('role-permission/assignAllPermission', ['as' => 'role-permission.assignAllPermission', 'uses' => 'ManageRolePermissionController@assignAllPermission']);
            Route::post('role-permission/removeAllPermission', ['as' => 'role-permission.removeAllPermission', 'uses' => 'ManageRolePermissionController@removeAllPermission']);
            Route::post('role-permission/assignRole', ['as' => 'role-permission.assignRole', 'uses' => 'ManageRolePermissionController@assignRole']);
            Route::post('role-permission/detachRole', ['as' => 'role-permission.detachRole', 'uses' => 'ManageRolePermissionController@detachRole']);
            Route::post('role-permission/storeRole', ['as' => 'role-permission.storeRole', 'uses' => 'ManageRolePermissionController@storeRole']);
            Route::post('role-permission/deleteRole', ['as' => 'role-permission.deleteRole', 'uses' => 'ManageRolePermissionController@deleteRole']);
            Route::get('role-permission/showMembers/{id}', ['as' => 'role-permission.showMembers', 'uses' => 'ManageRolePermissionController@showMembers']);
            Route::resource('role-permission', 'ManageRolePermissionController');
            Route::resource('theme-settings', 'ProductCompanyThemeSettingsController');
            Route::resource('linkedin-settings', 'ProductCompanyLinkedInSettingsController');			
            Route::get('settings/loadRole/{id?}', 'ManageRolePermissionController@loadRole')->name('settings.loadRole');
            Route::get('settings/loadEditRole/{id?}', 'ManageRolePermissionController@loadEditRole')->name('settings.loadEditRole');
            Route::post('saveRole', 'ManageRolePermissionController@saveRole')->name('settings.saveRole');
            Route::post('updateRole', 'ManageRolePermissionController@updateRole')->name('settings.updateRole');
            Route::get('show-role/{id?}', 'ManageRolePermissionController@showRole')->name('settings.showRole');
            Route::get('teamdata', 'ManageRolePermissionController@teamdata')->name('settings.teamdata');
            Route::delete('removerole/{user_id?}', 'ManageRolePermissionController@removerole')->name('settings.removerole');
        });
		
//#############       	 SUBSCRIPTION MANAGEMENT
Route::get('subscribe/cart/{id?}', 'ManageSubscriptionController@cart')->name('subscribe.cart'); 
Route::get('subscribe/history', array('as' => 'subscribe.history', 'uses' => 'ManageSubscriptionController@history',));
Route::get('subscribe/cancel-subscription/{type?}', 'ManageSubscriptionController@cancelSubscription')->name('subscribe.cancel-subscription');
Route::get('subscribe/select-package/{packageID}', 'ManageSubscriptionController@selectPackage')->name('subscribe.select-package');
Route::get('subscribe/historylisting', 'ManageSubscriptionController@historylisting')->name('subscribe.historylisting');
Route::get('subscribe/viewmore/{id?}', 'ManageSubscriptionController@viewMore')->name('subscribe.viewmore');
Route::resource('subscribe', 'ManageSubscriptionController');
//END 

//#############       	 PROFILE MANAGEMENT
Route::post('account/sendinvitation', 'ProductCompanyAccountSettingsController@sendinvitation')->name('account.sendinvitation');
Route::get('account/add_team_member_form/{id?}', 'ProductCompanyAccountSettingsController@add_team_member_form')->name('account.add_team_member_form');
Route::get('account/zoom/{id?}', 'ProductCompanyAccountSettingsController@zoom')->name('account.zoom');
Route::get('account/google/{id?}', 'ProductCompanyAccountSettingsController@google')->name('account.google');
Route::resource('profile', 'ProductCompanyProfileController');
Route::post('account/update/{id}', 'ProductCompanyAccountSettingsController@update')->name('account.update');
Route::post('account/storeDept', 'ProductCompanyAccountSettingsController@storeDept')->name('account.storeDept'); 
Route::post('account/delIntStage', 'ProductCompanyAccountSettingsController@delIntStage')->name('account.delIntStage');
Route::post('account/addIntStage', 'ProductCompanyAccountSettingsController@addIntStage')->name('account.addIntStage');
Route::post('account/updatePriority', 'ProductCompanyAccountSettingsController@updatePriority')->name('account.updatePriority');
//END 

//#############       	 JOB CATEGORY MANAGEMENT
Route::get('job-categories/export/{start}/{length}', 'ProductCompanyJobCategoryController@export')->name('job-categories.export'); 	
Route::get('job-categories/data', 'ProductCompanyJobCategoryController@data')->name('job-categories.data');
Route::resource('job-categories', 'ProductCompanyJobCategoryController');
//END 

});

Route::get('profileedit', 'ProductCompanyProfileController@profileedit')->name('profileedit');	
Route::resource('auth-custom-package', 'AuthCustomPackageController');	
Route::post('auth-custom-package/validatePackage', 'AuthCustomPackageController@validatePackage')->name('auth-custom-package.validatePackage');
Route::resource('sticky-note', 'ProductCompanyStickyNotesController'); 		 
Route::get('paypal', array('as' => 'status', 'uses' => 'ProductCompanyPaypalController@getPaymentStatus',));
Route::resource('applicant-note', 'ApplicantNoteController');
 });


});
