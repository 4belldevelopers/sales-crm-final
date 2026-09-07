# Add / Edit / Submit Audit — Evidence File (scratch, do not commit)

> **Companion to:** [`../Add-Edit-Submit-Audit.md`](../Add-Edit-Submit-Audit.md) (master tracker).
> **Date:** 2026-08-15.
> **Scope:** same as the master — Service Co + Super Admin + Admin + Front/SaasFront/Auth. Product Co and Staffing Co are dormant and excluded.
>
> This is the **single underlying scratch file**. It contains five sections:
>
> 1. **Route inventory** (196 rows × 7 cols: line, method, URI, name, controller, blade view, DB table).
> 2. **CRUD completeness matrix** (55 entity rows × 4-verb matrix + coverage summary).
> 3. **Transitions / state-change route list** (196 rows reclassified with a `Transition class` column).
> 4. **Transitions matrix** (46 entity rows × 8-class transition map + T1–T7 evidence).
> 5. **Hotspots evidence** (themes A–G with controller/route evidence and prioritized order).
>
> Total underlying data: **196 routes · 55 entity groups · 46 transition-mapped entities · 13 hotspots**.

---

## 1. Route Inventory (narrow sweep, 196 rows)

Scope: routes/service.php, routes/superadmin.php, routes/web.php.
Out of scope (skipped): routes/product.php, routes/service_old.php, all controllers under App\Http\Controllers\ProductCompany\* and StaffingCompany\*.

Filter rules applied: Include all HTTP POST/PUT/PATCH/DELETE. Include GET whose URI or name contains: create, edit, store, update, destroy, delete, submit, save, remove, import, export, invite, add, assign, bulk, approve, reject, upload, download.
EXCLUDE: pure show/list/index/data/show-ajax-_-file-show/profile-show/dashboard-stat/kpi GETs, health/whatsapp/test/debug routes, webhook GETs that are inbound callbacks (Stripe/IPN/Razorpay).

Columns added in this revision: **Blade view** = the `.blade.php` rendered (or `-` for JSON/redirect endpoints); **DB table** = the underlying MySQL table the controller writes/reads (mapped from `database/schema/mysql-schema.sql`).

### 1.1 routes/service.php — Service Co (59 rows)

| Line | Method | URI | Name | Controller@Method | Blade view | DB table |
|------|--------|-----|------|-------------------|------------|----------|
| 49 | POST | resume-upload/presign | presign | ResumeUploadController@presign | - | documents |
| 50 | POST | resume-upload/store | store | ResumeUploadController@store | - | documents |
| 52 | POST | resume-upload/{id}/retry | retry | ResumeUploadController@retry | - | documents |
| 56 | POST | resume-upload-session/ | (none) | ResumeUploadSessionController@store | - | documents |
| 62 | POST | resume-upload-session/{uuid}/finalize | (none) | ResumeUploadSessionController@finalize | - | documents |
| 65 | DELETE | resume-upload-session/{uuid} | (none) | ResumeUploadSessionController@cancel | - | documents |
| 76 | POST | api/cache/invalidate | cache.invalidate | ServiceCompanyDashboardApiController@invalidateCache | - | (redis only) |
| 84 | POST | meeting-comment-new/{id?} | dashboard.meeting-comment-new | ServiceCompanyDashboardController@meetingcommentnew | - | comments |
| 112 | POST | clients/update/{id} | clients.update | ServiceCompanyClientController@update | - | clients |
| 150 | POST | jobs_hiring_contact_person/{id?} | jobs.hiring_contact_person | ServiceCompanyJobsController@hiring_contact_person | (rendered inline HTML) | client_contact_info |
| 151 | POST | jobs_mail_contact_person/{id?} | jobs.mail_contact_person | ServiceCompanyJobsController@mail_contact_person | (rendered inline HTML) | client_contact_info |
| 165 | GET | jobs/assignRejected | jobs.assignRejected | ServiceCompanyJobsController@assignRejected | - | jobs |
| 166 | GET | jobs/removeJob | jobs.removeJob | ServiceCompanyJobsController@removeJob | - | jobs |
| 170 | POST | jobs/updatestatus | jobs.updatestatus | ServiceCompanyJobsController@updatestatus | - | jobs |
| 171 | POST | jobs/hiring_manager/{id?} | jobs.hiring_manager | ServiceCompanyJobsController@hiring_manager | service_company.jobs.hiring_manager (rendered) | client_contact_info |
| 172 | POST | jobs/newUpdateJobstatus | jobs.newUpdateJobstatus | ServiceCompanyJobsController@newUpdateJobstatus | - | jobs |
| 173 | POST | jobs/update/{id} | jobs.update | ServiceCompanyJobsController@update | - | jobs / service_on_jobs |
| 192 | GET | job-applications/create/{jobId?}/{appId?} | job-applications.create | ServiceCompanyJobApplicationController@create | service_company.job-applications.create | job_applications |
| 195 | GET | job-applications/clientcreate/{jobId?}/{appId?} | job-applications.clientcreate | ServiceCompanyJobApplicationController@clientcreate | service_company.job-applications.create (client variant) | job_applications |
| 197 | POST | job-applications/update/{id} | job-applications.update | ServiceCompanyJobApplicationController@update | - | job_applications |
| 198 | POST | uploadresume | job-applications.uploadresume | ServiceCompanyJobApplicationController@uploadresume | - | documents |
| 199 | POST | unset-file | file.unset | ServiceCompanyJobApplicationController@unsetFile | - | documents |
| 203 | POST | job-applications/accessEmailTemplate | job-applications.accessEmailTemplate | ServiceCompanyJobApplicationController@accessEmailTemplate | - | email_templates |
| 205 | POST | recruiter-note-update | job-applications.recruiterNoteUpdate | ServiceCompanyJobApplicationController@recruiterNoteUpdate | - | applicant_notes |
| 206 | POST | job-applications/stage_submit | job-applications.stage_submit | ServiceCompanyJobApplicationController@stage_submit | - | job_applications |
| 207 | POST | job-applications/client_stage_submit | job-applications.client_stage_submit | ServiceCompanyJobApplicationController@client_stage_submit | - | job_applications |
| 208 | POST | job-applications/resume_delete | job-applications.resume_delete | ServiceCompanyJobApplicationController@resume_delete | - | documents |
| 209 | POST | job-applications/statusupdate | job-applications.statusupdate | ServiceCompanyJobApplicationController@statusupdate | - | job_applications |
| 210 | POST | job-applications/store-schedule | job-applications.store-schedule | ServiceCompanyJobApplicationController@storeSchedule | - | interview_schedules |
| 213 | POST | job-applications/getGoogleVideoUrl | job-applications.getGoogleVideoUrl | ServiceCompanyJobApplicationController@getGoogleVideoUrl | - | interview_schedules |
| 214 | POST | change-event-organizer/{eventId} | (none) | ServiceCompanyJobApplicationController@changeEventOrganizer | - | interview_schedules |
| 217 | POST | job-applications/recruiter/{id?} | job-applications.recruiter | ServiceCompanyJobApplicationController@recruiter | service_company.jobs.recruiter (rendered) | job_applications |
| 218 | POST | contact-persion-team/recruiter/{id?} | job-applications.contactPersionWithTeam | ServiceCompanyJobApplicationController@contactPersionWithTeam | - | job_applications / client_contact_info |
| 219 | POST | job-applications/clientappstore | job-applications.clientappstore | ServiceCompanyJobApplicationController@clientappstore | - | job_applications |
| 220 | GET | job-applications/download/{id} | job-applications.download | ServiceCompanyJobApplicationController@download | (file stream) | documents |
| 221 | GET | export-job-applications | export.sample-job-applications | ServiceCompanyJobApplicationController@sampleDownload | (file stream) | job_applications |
| 222 | GET | candidate-export-job-applications | candidateexport.candidate-job-applications | ServiceCompanyJobApplicationController@candidatejobDownload | (file stream) | job_applications |
| 223 | POST | import-job-applications | import.sample-job-applications | ServiceCompanyJobApplicationController@importJobCondidate | - | job_applications |
| 225 | POST | extract-resume-applications | extract.resume-job-applications | ServiceCompanyJobApplicationController@resumExtract | - | job_applications / documents |
| 226 | GET | create-offer/{id} | job-applications.create_offer | ServiceCompanyJobApplicationController@createOffer | service_company.applicationoffer.create_offer | application_offer |
| 227 | POST | save-create-offer | job-applications.save_create_offer | ServiceCompanyJobApplicationController@saveCreateOffer | - | offer_job_applications / application_offer |
| 228 | POST | preview-offer-letter/{id?} | cvtracker.previewOfferLettetr | ServiceCompanyCvTrackerController@previewOfferLettetr | offer-letter (rendered) | offer_job_applications |
| 229 | POST | terms-save-create-offer | job-applications.terms_save_create_offer | ServiceCompanyJobApplicationController@termsSaveCreateOffer | - | offer_job_applications |
| 240 | GET | resume-as-pdf/{id} | job-applications.resume-as-pdf | ServiceCompanyJobApplicationController@resumeAsPdf | (file stream) | documents |
| 244 | POST | offer-mail-format/{id?} | cvtracker.offer_send_mail | ServiceCompanyCvTrackerController@offerSendMail | - | offer_job_applications |
| 245 | POST | offer-send-mail/fwd_offer_send | cvtracker.fwd_offer_send | ServiceCompanyCvTrackerController@fwd_offer_send | - | offer_job_applications |
| 246 | POST | offer_download_mail/{id?} | cvtracker.offer_download_mail | ServiceCompanyCvTrackerController@fwd_offer_download | (file stream) | offer_job_applications |
| 283 | GET | apptracker/export/{jobs}/{mstatus_id}/{start}/{length} | apptracker.export | ServiceCompanyAppTrackerController@export | (file stream) | job_applications |
| 291 | POST | apptracker/store | apptracker.store | ServiceCompanyAppTrackerController@store | - | job_applications |
| 297 | POST | tickets/{ticket}/status | tickets.ticketUpdateStatus | ServiceCompanyAppTrackerController@ticketUpdateStatus | - | tickets |
| 298 | GET | ticketcreate | tickets.create | ServiceCompanyAppTrackerController@ticketCreate | service_company.supports.create | tickets |
| 300 | POST | storeticketcreate | tickets.store | ServiceCompanyAppTrackerController@storeticketCreate | - | tickets |
| 301 | POST | comments/store | comments.store | ServiceCompanyAppTrackerController@storetComment | - | comments |
| 307 | POST | cvtracker/fwd_resume/{id?} | cvtracker.fwd_resume | ServiceCompanyCvTrackerController@fwd_resume | - | job_applications / documents |
| 308 | POST | mass_mail/mass_mail/{id?} | cvtracker.mass_mail | ServiceCompanyCvTrackerController@mass_mail | - | job_applications |
| 309 | POST | send-mail/send-mail/{id?} | cvtracker.send_mail | ServiceCompanyCvTrackerController@send_mail | - | job_applications |
| 310 | POST | send-whatsapp/send-whatsapp/{id?} | cvtracker.send_whatsapp | ServiceCompanyCvTrackerController@send_whatsapp | - | job_applications |
| 311 | POST | cvtracker/application_copy | cvtracker.application_copy | ServiceCompanyCvTrackerController@application_copy | - | job_applications |
| 312 | POST | talent-add | cvtracker.talent-add | ServiceCompanyCvTrackerController@talentAdd | - | add_job_wise_condidates |
| 320 | GET | cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length} | cvtracker.export | ServiceCompanyCvTrackerController@export | (file stream) | add_job_wise_condidates |
| 328 | POST | cvtracker/get_client_job | cvtracker.get_client_job | ServiceCompanyCvTrackerController@get_client_job | (rendered snippet) | jobs |
| 329 | POST | cvtracker/candidate_process_update | cvtracker.candidate_process_update | ServiceCompanyCvTrackerController@candidate_process_update | - | job_applications / add_job_wise_condidates |
| 330 | POST | cvtracker/copy_current_selected_applicant | cvtracker.copy_current_selected_applicant | ServiceCompanyCvTrackerController@copy_current_selected_applicant | - | job_applications |
| 331 | POST | cvtracker/email_template | cvtracker.email_template | ServiceCompanyCvTrackerController@email_template | - | email_templates |
| 332 | POST | cvtracker/fwd_resume_send | cvtracker.fwd_resume_send | ServiceCompanyCvTrackerController@fwd_resume_send | - | job_applications |
| 333 | POST | mass_cvtracker/mass_mail_send | cvtracker.mass_mail_send | ServiceCompanyCvTrackerController@mass_mail_send | - | job_applications |
| 334 | POST | send_cvtracker/send_mail_send | cvtracker.send_mail_send | ServiceCompanyCvTrackerController@send_mail_send | - | job_applications |
| 336 | POST | send_cvtracker/send_mail_for_candidate | cvtracker.send_mail_for_candidate | ServiceCompanyCvTrackerController@send_mail_for_candidate | - | job_applications |
| 346 | POST | interview-schedule/followup-update | interview-schedule.followup_update | InterviewScheduleController@followupUpdate | - | interview_schedules |
| 347 | POST | interview-schedule/send-link | interview-schedule.send_link | InterviewScheduleController@sendInterviewLink | - | interview_schedules |
| 351 | POST | interview-schedule/change-status-multiple | interview-schedule.change-status-multiple | InterviewScheduleController@changeStatusMultiple | - | interview_schedules |
| 353 | GET | interview-schedule/export/{start}/{length} | interview-schedule.export | InterviewScheduleController@export | (file stream) | interview_schedules |
| 356 | POST | interview-schedule/updateFeedbackStatus | interview-schedule.updateFeedbackStatus | InterviewScheduleController@updateFeedbackStatus | - | interview_schedules |
| 357 | POST | interview-schedule/updateAvailableStatus | interview-schedule.updateAvailableStatus | InterviewScheduleController@updateAvailableStatus | - | interview_schedules |
| 358 | POST | interview-schedule/updateInterview | interview-schedule.updateInterview | InterviewScheduleController@updateInterview | - | interview_schedules |
| 366 | GET | applicationoffer/export/{start}/{length} | applicationoffer.export | ServiceCompanyApplicationOfferController@export | (file stream) | application_offer |
| 368 | POST | applicationoffer/update_salary | applicationoffer.update_salary | ServiceCompanyApplicationOfferController@updateSalary | - | application_offer |
| 369 | POST | applicationoffer/update_application | applicationoffer.update_application | ServiceCompanyApplicationOfferController@update_application | - | application_offer |
| 375 | GET | job-onboard/send-offer/{id?} | job-onboard.send-offer | ServiceCompanyJobOnboardController@sendOffer | - | application_offer |
| 376 | GET | job-onboard/update-status/{id?} | job-onboard.update-status | ServiceCompanyJobOnboardController@updateStatus | - | application_offer |
| 377 | GET | job-onboard/export/{start}/{length} | job-onboard.export | ServiceCompanyJobOnboardController@export | (file stream) | application_offer |
| 385 | GET | consultancy/export/{start}/{length} | consultancy.export | ServiceCompanyConsultancyController@export | (file stream) | clients |
| 386 | GET | consultancy/staffing_invitation/{id?} | consultancy.staffing_invitation | ServiceCompanyConsultancyController@staffing_invitation | - | clients |
| 400 | POST | boostkeywordadd | bench-candidates.boostkeywordadd | ServiceCompanyBenchCandidatesController@boostkeywordadd | - | boostkeywords |
| 401 | POST | bench-switch | bench-candidates.benchSwitch | ServiceCompanyBenchCandidatesController@benchSwitch | - | add_job_wise_condidates |
| 413 | POST | bench-candidates/save-assignment-details | bench-candidates.savePlacedAssignmentDetails | ServiceCompanyBenchCandidatesController@savePlacedAssignmentDetails | - | bench_applications |
| 414 | POST | bench-candidates/save-commercial-config | bench-candidates.savePlacedCommercialConfig | ServiceCompanyBenchCandidatesController@savePlacedCommercialConfig | - | bench_applications |
| 433 | GET | bench-candidates/download/{id} | bench-candidates.download | ServiceCompanyBenchCandidatesController@download | (file stream) | documents |
| 439 | POST | bench-candidates/update_placed_end_date | bench-candidates.update_placed_end_date | ServiceCompanyBenchCandidatesController@update_placed_end_date | - | bench_applications |
| 440 | POST | bench-candidates/status_submit | bench-candidates.status_submit | ServiceCompanyBenchCandidatesController@status_submit | - | bench_applications |
| 445 | POST | add-update-services-job | jobs.addUpdateJobServices | ServiceCompanyJobsController@jobServicesAddUpdate | - | jobs / service_on_jobs |
| 446 | POST | jobs/updateserviceviewmore | jobs.updateserviceviewmore | ServiceCompanyJobsController@updateserviceviewmore | - | jobs |
| 447 | POST | download-resume-bench-candidates/ | bench-candidates.downloadResumeBenchCandidate | ServiceCompanyBenchCandidatesController@downloadResumeBenchCandidate | (file stream) | documents |
| 456 | POST | career-page/settings/save | career.page.settings.save | ServiceCompanyAccountSettingsController@careerPageSettingsSave | - | company_settings / companies |
| 461 | POST | account/update/{id} | account.update | ServiceCompanyAccountSettingsController@update | - | companies |
| 462 | POST | account/videoMeetingSeetingUpdate | account.videoMeetingSeetingUpdate | ServiceCompanyAccountSettingsController@videoMeetingSeetingUpdate | - | companies |
| 463 | POST | boostkeywordadd | account.boostkeywordadd | ServiceCompanyAccountSettingsController@boostkeywordadd | - | boostkeywords |
| 465 | POST | boostStatusUpdate | account.boostStatusUpdate | ServiceCompanyAccountSettingsController@boostStatusUpdate | - | boostkeywords |
| 487 | POST | team/resetpassword | team.resetpassword | ServiceCompanyTeamController@resetpassword | - | users |
| 488 | POST | team/statusUpdate | team.statusUpdate | ServiceCompanyTeamController@statusUpdate | - | users |
| 489 | POST | team/teamDelete | team.teamDelete | ServiceCompanyTeamController@teamDelete | - | users / role_user |
| 490 | POST | team/reSendmail | team.reSendmail | ServiceCompanyTeamController@reSendmail | - | users |
| 491 | ANY | team/roleupdate | team.roleupdate | ServiceCompanyTeamController@roleupdate | - | role_user / model_has_roles |
| 492 | POST | team/WorkingStatus | team.WorkingStatus | ServiceCompanyTeamController@WorkingStatus | - | users |
| 502-507 | POST | role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | ServiceCompanyRolesPermissionController@* | - | role_has_permissions / role_user / model_has_permissions |
| 508 | POST | role-permission/saveRole | settings.saveRole | ServiceCompanyRolesPermissionController@saveRole | - | roles / role_has_permissions |
| 511 | DELETE | role-permission/removerole/{user_id?} | settings.removerole | ServiceCompanyRolesPermissionController@removerole | - | role_user |
| 518 | POST | account/addIntStage | account.addIntStage | ServiceCompanyAccountSettingsController@addIntStage | - | master_status / interview_processes |
| 519 | POST | account/delIntStage | account.delIntStage | ServiceCompanyAccountSettingsController@delIntStage | - | master_status / interview_processes |
| 520 | POST | account/updatePriority | account.updatePriority | ServiceCompanyAccountSettingsController@updatePriority | - | master_status |
| 521 | POST | account/storeDept | account.storeDept | ServiceCompanyAccountSettingsController@storeDept | - | departments |
| 522 | POST | account/sendinvitation | account.sendinvitation | AdminAccountSettingsController@sendinvitation | - | users |
| 523 | POST | addressupdate | account.addressupdate | ServiceCompanyAccountSettingsController@addressupdate | - | companies |
| 525 | POST | companysavecreateoffer | account.companysavecreateoffer | ServiceCompanyAccountSettingsController@companysavecreateoffer | - | offer_terms_conditions |
| 529 | GET | job-categories/export/{start}/{length} | job-categories.export | ServiceCompanyJobCategoryController@export | (file stream) | job_categories |
| 534 | POST | jobtracker/store | jobtracker.store | ServiceCompanyJobTrackerController@store | - | jobs / job_applications |
| 535 | POST | jobtracker/updateinformation | jobtracker.updateinformation | ServiceCompanyJobTrackerController@updateinformation | - | job_applications |
| 540 | POST | delete-account-store | settings.delete-account-store | CompanySettingsController@deleteAccountStore | - | companies |
| 547-552 | POST | role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | ManageRolePermissionController@* | - | role_has_permissions / role_user / model_has_permissions |
| 563 | POST | saveRole | settings.saveRole | ManageRolePermissionController@saveRole | - | roles / role_has_permissions |
| 564 | POST | updateRole | settings.updateRole | ManageRolePermissionController@updateRole | - | roles |
| 567 | DELETE | removerole/{user_id?} | settings.removerole | ManageRolePermissionController@removerole | - | role_user |
| 592 | POST | checkout-subscribe | subscribe.checkoutSubscribe | ManageSubscriptionController@checkoutSubscribe | - | subscriptions / package_invoice |
| 594 | POST | custom-checkout-subscribe | subscribe.customCheckoutSubscribe | ManageSubscriptionController@customCheckoutSubscribe | - | custompackages / package_invoice |
| 596 | GET | deletecheckout-subscribe | subscribe.deletecheckout | ManageSubscriptionController@deletecheckout | - | package_invoice |
| 597 | POST | checkout-subscribe/apply-coupon | subscribe.applyCoupon | ManageSubscriptionController@applyCoupon | - | coupons |
| 598 | POST | checkout-subscribe/remove-coupon | subscribe.removeCoupon | ManageSubscriptionController@removeCoupon | - | coupons |
| 603 | POST | auth-custom-package/validatePackage | auth-custom-package.validatePackage | AuthCustomPackageController@validatePackage | service_company.auth-custom-package.index | custompackages |
| 604 | POST | auth-custom-package/validateClientPackage | auth-custom-package.validateClientPackage | AuthCustomPackageController@validateClientPackage | service_company.auth-custom-package.index | custompackages |
| 606 | POST | todo-items/update-todo-item | todo-items.updateTodoItem | ServiceCompanyTodoItemController@updateTodoItem | service_company.todo-list.todo-item-edit | todo_items |

### 1.2 routes/superadmin.php — Super Admin (90 rows)

| Line | Method | URI | Name | Controller@Method | Blade view | DB table |
|------|--------|-----|------|-------------------|------------|----------|
| 47 | PUT | language-settings/change-language-status/{id} | language-settings.changeStatus | LanguageSettingsController@changeStatus | - | language_settings |
| 50 | PUT | footer-settings/update-footer-menu/{id} | footer-settings.updateFooterMenu | SuperAdminFooterSettingsController@updateFooterMenu | - | footer_menu |
| 51 | POST | footer-settings/store-footer-menu | footer-settings.storeFooterMenu | SuperAdminFooterSettingsController@storeFooterMenu | - | footer_menu |
| 54 | POST | update-application/deleteFile | update-application.deleteFile | UpdateApplicationController@deleteFile | - | (app-update log) |
| 62 | POST | company/updateCompanyPackage/{id} | company.updateCompanyPackage | SuperAdminCompanyController@updateCompanyPackage | - | company_packages / package_invoice |
| 69 | POST | setting-google-meeting-store | company.settingGoogleMeetingStore | SuperAdminCompanyController@settingGoogleMeetingStore | - | companies (settings blob) |
| 71 | POST | google-meet-status-update | company.googleMeetStatusUpdate | SuperAdminCompanyController@googleMeetStatusUpdate | - | companies (settings blob) |
| 76 | POST | setting-team-meeting-store | company.settingteamMeetingStore | SuperAdminCompanyController@settingteamMeetingStore | - | companies (settings blob) |
| 77 | POST | team-meet-status-update | company.teamMeetStatusUpdate | SuperAdminCompanyController@teamMeetStatusUpdate | - | companies (settings blob) |
| 79 | POST | add-meeting-list | company.addMeetingLists | SuperAdminCompanyController@addMeetingLists | - | (settings blob) |
| 81 | POST | meeting-list-delete | company.meetingListDelete | SuperAdminCompanyController@meetingListDelete | - | (settings blob) |
| 87 | POST | company/{id}/login | company.loginAsCompany | SuperAdminCompanyController@loginAsCompany | - | companies (impersonate) |
| 90 | POST | candidate-google-sheet-get-data | company.googleSheetCandidateData | SuperAdminCompanyController@googleSheetCandidateData | - | google_sheets |
| 91 | POST | google-sheet-get-data | company.googleSheetInsertdataInDatabase | SuperAdminCompanyController@googleSheetInsertdataInDatabase | - | google_sheets |
| 92 | POST | job-candidate-google-sheet-get-data | company.googleSheetCandidateDataJob | SuperAdminCompanyController@googleSheetCandidateDataJob | - | google_sheets |
| 93 | POST | single-notification-update | company.SingleNotificationUpdate | SuperAdminCompanyController@SingleNotificationUpdateS | - | notifications |
| 98 | POST | billing/viewmore | company.billinDetails | SuperAdminCompanyController@billinDetails | super-admin.company.change_package_modal | package_invoice |
| 104 | GET | company/edit/{id?} | company.edit_company | SuperAdminCompanyController@edit_company | super-admin.company.edit | companies |
| 105 | POST | company/update/{id?} | company.update_company | SuperAdminCompanyController@update_company | - | companies |
| 113 | POST | getmeetingchild | company.getmeetingchild | SuperAdminCompanyController@getmeetingchild | - | (settings blob) |
| 115 | POST | company/recruiter/{id?} | company.recruiter | SuperAdminCompanyController@recruiter | - | role_user |
| 117 | POST | meetingstoreSchedule | company.store-schedule | SuperAdminCompanyController@storeSchedule | - | interview_schedules |
| 128 | POST | updateAccessCode | company.updateAccessCode | SuperAdminCompanyController@updateAccessCode | - | companies |
| 140 | POST | team/statusUpdate | team.statusUpdate | SuperAdminTeamController@statusUpdate | - | users |
| 142 | POST | team/WorkingStatus | team.WorkingStatus | SuperAdminTeamController@WorkingStatus | - | users |
| 156 | ANY | email-themes/update/{id} | company.emailThemesupdate | SuperAdminCompanyController@emailThemesupdate | - | th_email_template |
| 157 | POST | email-themes/add-edit | company.addEditemailThemesupdate | SuperAdminCompanyController@addEditemailThemesupdate | - | th_email_template / emailthemes |
| 161 | ANY | whatsapp-themes/update/{id} | company.whatsappThemesupdate | SuperAdminCompanyController@whatsappThemesupdate | - | whatsapp_themes |
| 162 | POST | whatsapp-themes/add-edit | company.addEditwhatsappThemesupdate | SuperAdminCompanyController@addEditwhatsappThemesupdate | - | whatsapp_themes |
| 166 | ANY | features-list/update/{id} | company.featuresListupdate | SuperAdminCompanyController@featuresListupdate | - | features / front_icon_features |
| 168 | POST | features-list/add-edit | company.addEditfeaturesListupdate | SuperAdminCompanyController@addEditfeaturesListupdate | - | features / front_icon_features |
| 176 | POST | market-auto-approval | company.market-auto-approval | SuperAdminCompanyController@marketautoApproval | - | jobs |
| 177 | POST | job-approval | company.job-approval | SuperAdminCompanyController@jobApproval | - | jobs |
| 178 | POST | job_reject_mail/reject_mail/{id?} | company.job_reject_mail | SuperAdminCompanyController@job_reject_mail | - | jobs |
| 179 | POST | reject_job/reject_mail_send | company.reject_mail_send_job | SuperAdminCompanyController@reject_mail_send_job | - | jobs |
| 185 | POST | candidate-approval | company.candidate-approval | SuperAdminCompanyController@candidateApproval | - | job_applications / add_job_wise_condidates |
| 186 | POST | auto-approval | company.auto-approval | SuperAdminCompanyController@autoApproval | - | jobs |
| 187 | POST | reject_mail/reject_mail/{id?} | company.reject_mail | SuperAdminCompanyController@reject_mail | - | job_applications / add_job_wise_condidates |
| 188 | POST | email_template | company.email_template | SuperAdminCompanyController@email_template | - | th_email_template / emailthemes |
| 189 | POST | reject_cvtracker/reject_mail_send | company.reject_mail_send | SuperAdminCompanyController@reject_mail_send | - | job_applications / add_job_wise_condidates |
| 191 | POST | setting-zoom-store | company.settingZoomStore | SuperAdminCompanyController@settingZoomStore | - | (settings blob) |
| 219 | ANY | skill/update/{id}/{name} | company.skillUpdate | SuperAdminCompanyController@skillUpdate | - | skills |
| 220 | POST | skill/add-edit | company.addEditSkillUpdate | SuperAdminCompanyController@addEditSkillUpdate | - | skills |
| 221 | POST | skillDelete | company.skillDelete | SuperAdminCompanyController@skillDelete | - | skills |
| 227 | POST | interview-schedule/updateInterview | company.updateInterview | SuperAdminCompanyController@updateInterview | - | interview_schedules |
| 229 | POST | note-delete/comment | company.notedestroy | SuperAdminCompanyController@notedestroy | - | comments |
| 233 | POST | check-active | company.checkActive | SuperAdminCompanyController@checkActive | - | companies |
| 234 | POST | check-status-operational | company.checkStatusOperational | SuperAdminCompanyController@checkStatusOperational | - | companies |
| 235 | POST | check-status-sales | company.checkStatusSales | SuperAdminCompanyController@checkStatusSales | - | companies |
| 241 | ANY | notification/update/{id} | company.notificationupdate | SuperAdminCompanyController@notificationupdate | - | notifications |
| 243 | POST | notification/add-edit | company.addEditnotificationupdate | SuperAdminCompanyController@addEditnotificationupdate | - | notifications |
| 248 | POST | default-terms/add-edit | company.addEditTermsupdate | SuperAdminCompanyController@addEditTermsupdate | - | default-terms / offer_terms_conditions |
| 250 | POST | companysavecreateoffer | company.companysavecreateoffer | SuperAdminCompanyController@companysavecreateoffer | - | offer_terms_conditions |
| 254 | POST | coin-events-store | company.coinEventsStore | SuperAdminCompanyController@coinEventsStore | - | coin_events |
| 257 | POST | coin-package-store | company.coinPackageStore | SuperAdminCompanyController@coinPackageStore | - | coin_packages |
| 259 | POST | coin-event-status | company.coinEventStatus | SuperAdminCompanyController@coinEventStatus | - | coin_events |
| 262 | GET | coupons/add-edit/{id?} | coupons.addEdit | SuperAdmin\CouponController@addEdit | super-admin.coupons.add-edit | coupons |
| 263 | POST | coupons/store | coupons.store | SuperAdmin\CouponController@store | - | coupons |
| 264 | POST | coupons/toggle-status | coupons.toggleStatus | SuperAdmin\CouponController@toggleStatus | - | coupons |
| 265 | POST | coupons/destroy | coupons.destroy | SuperAdmin\CouponController@destroy | - | coupons |
| 269 | POST | top-upcoin-package-store | company.topUpCoinPackageStore | SuperAdminCompanyController@topUpCoinPackageStore | - | top_up_coin_packages |
| 271 | POST | topup-coins-added | company.topUpCoinsAdded | SuperAdminCompanyController@topUpCoinsAdded | - | coin_orders |
| 272 | POST | topuptypegetprice | company.topuptypegetprice | SuperAdminCompanyController@topuptypegetprice | - | coin_package_types / coin_package_points |
| 276 | POST | boostkeywordadd | company.boostkeywordadd | SuperAdminCompanyController@boostkeywordadd | - | boostkeywords |
| 283 | GET | createjd/{entryType}/{id?} | company.createjd | SuperAdminCompanyController@createjd | super-admin.jd.create | jobs |
| 284 | POST | jdgoogle-sheet-get-data | company.jdgoogleSheetInsertdataInDatabase | SuperAdminCompanyController@jdgoogleSheetInsertdataInDatabase | - | jobs / google_sheets |
| 292 | POST | tickets/{ticket}/status | tickets.ticketUpdateStatus | SuperAdminCompanyController@ticketUpdateStatus | - | tickets |
| 293 | GET | ticketcreate | tickets.create | SuperAdminCompanyController@ticketCreate | super-admin.supports.create | tickets |
| 296 | POST | storeticketcreate | tickets.store | SuperAdminCompanyController@storeticketCreate | - | tickets |
| 297 | POST | comments/store | comments.store | SuperAdminCompanyController@storetComment | - | comments |
| 310 | POST | dddcustompackages/createInvoice | global-settings.updatePr | GlobalSettingController@updatePr | - | package_invoice |
| 311 | GET | linkedin-settings/update-status/{id} | updateStatus | LinkedInSettingController@updateStatus | - | linked_in_settings |
| 315 | POST | front-cms/update-common-header | front-cms.updateCommonHeader | SuperAdminFrontCmsController@updateCommonHeader | - | front_cms_headers |
| 316 | POST | front-cms/update-header | front-cms.updateHeader | SuperAdminFrontCmsController@updateHeader | - | front_cms_headers |
| 318 | POST | front-cms/features | front-cms.savefeatures | SuperAdminFrontCmsController@saveImageFeatures | - | front_image_features |
| 319 | POST | front-cms/features/{id} | front-cms.updatefeatures | SuperAdminFrontCmsController@updatefeatures | - | front_image_features |
| 321 | POST | front-cms/deleteFeature/{id} | front-cms.deleteFeature | SuperAdminFrontCmsController@deleteFeature | - | front_image_features |
| 328 | ANY | add-edit-package-features/{id?} | package.add_edit_package_feature | PackageController@add_edit_package_feature | super-admin.packages.add_edit_package_feature | package_features / packages |
| 335 | POST | custompackages/createInvoice | custompackages.createInvoice | CustomPackageController@createInvoice | razorpay-invoice.custom_package_invoice (rendered) | package_invoice |
| 336 | GET | custompackages/create/{company_id?} | custompackages.create | CustomPackageController@create | super-admin.custompackages.create | custompackages |
| 338 | POST | custompackages/saveinvoice | custompackages.saveinvoice | CustomPackageController@saveinvoice | - | custompackages / package_invoice |
| 339 | POST | custompackages/submitcomment/{id?} | custompackages.submitcomment | CustomPackageController@submitcomment | - | package_invoice_comment |
| 342 | GET | custompackages/editWallet/{id?} | custompackages.editWallet | CustomPackageController@editWallet | super-admin.custompackages.edit | custompackages |
| 343 | POST | custompackages/update/{id} | custompackages.update | CustomPackageController@update | - | custompackages |
| 354 | POST | defaultpackages/update | defaultpackages.update | DefaultPackageController@update | - | packages / package_master |
| 365 | POST | custom-modules/verify-purchase | custom-modules.verify-purchase | CustomModuleController@verifyingModulePurchase | - | custom_modules |
| 371 | GET | recruiter-note | recruiter-note.index | RecruiterNoteTemplateController@index | super-admin.recruiter-note.index | recruiter_note_master |
| 372 | POST | recruiter-note | recruiter-note.store | RecruiterNoteTemplateController@store | - | recruiter_note_master |
| 373 | PUT | recruiter-note/{id} | recruiter-note.update | RecruiterNoteTemplateController@update | - | recruiter_note_master |
| 374 | DELETE | recruiter-note/{id} | recruiter-note.destroy | RecruiterNoteTemplateController@destroy | - | recruiter_note_master |

### 1.3 routes/web.php — mixed (Auth, Front, SaasFront, Admin, inline) (47 rows)

| Line | Method | URI | Name | Controller@Method | Persona | Blade view | DB table |
|------|--------|-----|------|-------------------|---------|------------|----------|
| 37 | POST | whatsapp/create | (none) | HomeController@create | Debug | - | (none — debug) |
| 40 | POST | whatsapp/restart | (none) | HomeController@restart | Debug | - | (none — debug) |
| 41 | POST | whatsapp/send | (none) | HomeController@send | Debug | - | (none — debug) |
| 42 | POST | whatsapp/delete | (none) | HomeController@delete | Debug | - | (none — debug) |
| 112 | POST | notifications/read/{id} | (none) | (closure) | Debug | - | notifications |
| 122 | POST | salary/calculate | salary.calculate | HomeController@salaryCalculate | Debug | - | (calc only) |
| 132 | POST | questions/store | questions.questionsStore | HomeController@questionsStore | Public/Front | - | questions |
| 138 | POST | login | (none) | LoginController@login | Auth | - | users |
| 141 | POST | logout | logout | LoginController@logout | Auth | - | users |
| 149 | POST | password/email | password.email | ForgotPasswordController@sendResetLinkEmail | Auth | - | users |
| 153 | POST | password/reset | password.update | ResetPasswordController@reset | Auth | - | users |
| 156 | POST | ResetPasswordStore | ResetPasswordStore | ResetPasswordController@ResetPasswordStore | Auth | - | users |
| 158 | POST | password/emailsend | password.emailsend | ResetPasswordController@sendResetLinkEmail | Auth | - | users |
| 162 | POST | delete-template | delete.template | HomeController@deleteTemplate | Public | - | email_templates |
| 168 | POST | uploadresume | get.uploadresume | HomeController@uploadresume | Public/Front | - | documents |
| 169 | POST | extract-resume-applications | get.extract-resume-job-applications | HomeController@resumExtract | Public/Front | - | documents / job_applications |
| 171 | POST | careerStore | get.careerStore | HomeController@careerStore | Public/Front | - | job_applications |
| 250 | POST | submit-form | form.submit | HomeController@submit | Public | - | (form sink) |
| 254 | POST | resume/upload | resume.upload | ResumeController@upload | Public | - | documents |
| 264 | POST | save-signature | save.signature | HomeController@saveSignature | Public | - | signatures |
| 270 | POST | create-payment | phonepe.payment | PhonePePaymentController@createPayment | Payment | - | package_invoice / phonepe_invoices |
| 272 | POST | payment-callback | phonepe.callback | PhonePePaymentController@callback | Payment | - | phonepe_invoices |
| 275 | ANY | phonepe-response | response | PhonePePaymentController@response | Payment | - | phonepe_invoices |
| 298 | POST | payment-callback | payment.callback | PaymentController@paymentCallback | Payment | - | invoices / package_invoice |
| 329 | POST | offer_download_mail/{id?} | get.offer_download_mail | HomeController@fwd_offer_download | Public/Front | (file stream) | offer_job_applications |
| 342 | POST | verifyTeamProfile | verifyTeamProfile | SaasFrontController@verifyTeamProfile | SaasFront | - | users |
| 345 | POST | invitation-company-store/ | invitation-company-store | SaasFrontController@invitationCompanyStore | SaasFront | - | companies |
| 346 | POST | (root) | contact | SaasFrontController@submitContact | SaasFront | - | contacts |
| 347 | POST | company-register | company-register | SaasFrontController@companyRegister | SaasFront | - | companies + ~70 child rows |
| 348 | POST | company-register-chk | company-register-chk | SaasFrontController@companyRegisterChk | SaasFront | - | companies |
| 350 | POST | valid-email-chk | valid-email-chk | HomeController@validEmailChk | SaasFront | - | users |
| 351 | POST | import-meetings | importMeetings | HomeController@importMeetings | SaasFront | - | (meetings sink) |
| 353 | POST | contact-company-register-chk | contact-company-register-chk | SaasFrontController@contactCompanyRegisterChk | SaasFront | - | companies |
| 354 | POST | domain-company-register-chk | domain-company-register-chk | SaasFrontController@domainCompanyRegisterChk | SaasFront | - | companies |
| 355 | POST | client-domain-register-chk | client-domain-register-chk | SaasFrontController@domainClientRegisterChk | SaasFront | - | companies |
| 360 | POST | change-language/{code} | changeLanguage | SaasFrontController@changeLanguage | SaasFront | - | language_settings (session) |
| 380 | POST | save-offer | save-offer | FrontJobOfferController@saveOffer | Front | - | offers |
| 381 | POST | job/saveApplication | saveApplication | FrontJobsController@saveApplication | Front | - | job_applications |
| 382 | POST | job/fetch-country-state | fetchCountryState | FrontJobsController@fetchCountryState | Front | - | (lookup) |
| 404 | POST | save-offer | save-offer | FrontJobOfferController@saveOffer | Front | - | offers |
| 405 | POST | job/saveApplication | saveApplication | FrontJobsController@saveApplication | Front | - | job_applications |
| 406 | POST | job/fetch-country-state | fetchCountryState | FrontJobsController@fetchCountryState | Front | - | (lookup) |
| 418 | POST | verify-billing-ipn | verify-billing-ipn | PaypalIPNController@verifyBillingIPN | Payment Webhook | - | paypal_invoices / invoices |
| 419 | POST | save-invoices | save_webhook | StripeWebhookController@saveInvoices | Payment Webhook | - | stripe_invoices |
| 420 | POST | save-razorpay-invoices | save_razorpay-webhook | RazorpayWebhookController@saveInvoices | Payment Webhook | - | razorpay_invoices |
| 426 | POST | send-otp-code | sendOtpCode | VerifyMobileController@sendVerificationCode | Auth | - | users |
| 427 | POST | send-otp-code/account | sendOtpCode.account | VerifyMobileController@sendVerificationCode | Auth | - | users |
| 428 | POST | verify-otp-phone | verifyOtpCode | VerifyMobileController@verifyOtpCode | Auth | - | users |
| 429 | POST | verify-otp-phone/account | verifyOtpCode.account | VerifyMobileController@verifyOtpCode | Auth | - | users |
| 433 | POST | mark-notification-read | mark-notification-read | NotificationController@markAllRead | Auth | - | notifications |
| 461-473 | POST | settings/delete-account-store, role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | (dormant — see skipped below) | ProductCompany | - | (dormant) |
| 522 | POST | account/sendinvitation | account.sendinvitation | AdminAccountSettingsController@sendinvitation | Admin (inline) | - | users |

### 1.4 Summary

Service Co: 59 rows. Super Admin: 90 rows. web.php: 47 rows (Auth 9, Front 6, SaasFront 12, Public/Front 5, Payment 7, Debug 5, Admin inline 2, Payment Webhook 3, ProductCompany 6).
**TOTAL_ROWS: 196.**

Skipped (dormant): routes/product.php (large), routes/service_old.php, ProductCompany\* controllers in web.php (~250 routes including all the inline ProductCompany block from line ~445-836), StaffingCompany\* controllers.

---

## 2. CRUD Completeness Matrix (narrow sweep, 55 entity rows)

For each entity, mark which of the four CRUD verbs is wired into an in-scope persona controller.

Scope: routes/service.php + routes/superadmin.php + the in-scope block of routes/web.php (Auth, Front, SaasFront, Admin inline, Payment/Webhooks). Out: ProductCompany\* + StaffingCompany\*.

Methodology: each row lists the **driver table** (the MySQL table the verb ultimately writes to, mapped from `database/schema/mysql-schema.sql`). Add = GET form that renders a create form + POST handler. Edit = GET form that renders an edit form + POST/PUT handler. Submit = the final store/save endpoint. Delete = explicit DELETE/POST destroy/remove endpoint.

Legend: ✅ wired · ⚠ partial / one method only · ❌ missing · — N/A

| # | Entity (driver table) | Add | Edit | Submit (store/save) | Delete | Persona | Hot note |
|---|----------------------|-----|------|---------------------|--------|---------|----------|
| 1 | jobs | ⚠ service.php jobs/add page (in `ServiceCompanyJobsController::create` line 4852/4991/13163) but **no GET route `jobs/create` in routes/service.php** — only POST `jobs/update` and `jobs/removeJob` | ✅ `jobs.update` (line 173, `ServiceCompanyJobsController@update` line 13175) | ✅ `apptracker.store` (line 291) — also `ServiceCompanyJobsController@store` exists but no route | ✅ `jobs.removeJob` (line 166, GET) | Service | Add page reachable only via app entry points (POST-only invitees) — V2 needs explicit `jobs/create` GET |
| 2 | service_on_jobs / service_jobs (job services) | ⚠ POST `add-update-services-job` (line 445) — `jobServicesAddUpdate` | ⚠ POST `jobs/updateserviceviewmore` (line 446) | ⚠ same as Add | ❌ no delete | Service | services job is appended-only; no remove endpoint |
| 3 | job_applications | ✅ `job-applications/clientappstore` (line 219) + `stage_submit` (line 206) | ✅ `job-applications.update/{id}` (line 197, method line 5193) | ✅ `stage_submit` + `client_stage_submit` (lines 206-207) | ⚠ `resume_delete` (line 208) deletes **document** not the application row; **no `job-applications.destroy`** | Service | hottest module; full app-row delete missing — audit/regulatory risk |
| 4 | bench_applications / add_job_wise_condidates (bench talent) | ✅ `cvtracker.talent-add` (line 312) — `talentAdd` line 1717; also `BenchCandidatesController::create` line 1029 | ⚠ `BenchCandidatesController::edit` line 1917 — **no GET route** | ✅ `bench-candidates.status_submit` (line 440) | ❌ no `bench-candidates.destroy` | Service | bench edit GET is missing from route map |
| 5 | interview_schedules | ⚠ `job-applications/store-schedule` (line 210) — `storeSchedule` line 6828; **no GET create form route** | ✅ `interview-schedule/updateInterview` (line 358) + `updateFeedbackStatus` (line 356) + `updateAvailableStatus` (line 357) + `followupUpdate` (line 346) | ✅ `store-schedule` (line 210) | ⚠ status updates only; no row destroy | Service | create form only via app-tracker modal |
| 6 | offer_job_applications / application_offer | ✅ `save-create-offer` (line 227) — `saveCreateOffer` line 4583; GET form at `create-offer/{id}` (line 226) | ⚠ `applicationoffer/update_application` (line 369) | ✅ `terms-save-create-offer` (line 229) + `preview-offer-letter` (line 228) | ❌ no `applicationoffer.destroy` | Service | offer letters persisted forever; archive is via status, not delete |
| 7 | application_status / master_status (statuses) | ✅ `account/addIntStage` (line 518) | ⚠ `account/updatePriority` (line 520) — reorders | ❌ no store/save dedicated route | ✅ `account/delIntStage` (line 519) | Service | wired via account settings, not own controller |
| 8 | tickets (support) | ✅ `storeticketcreate` (line 300/296) — POST; GET form `ticketcreate` (line 298/293) | ⚠ only status updates `tickets/{ticket}/status` (line 297/292) | ✅ same as Add | ❌ no `tickets.destroy` | Service + SuperAdmin | both Service and SuperAdmin reuse same controller methods |
| 9 | comments (ticket / meeting) | ✅ `comments/store` (line 301/297) | ❌ | ✅ same as Add | ✅ `note-delete/comment` (line 229, SuperAdmin) — only SuperAdmin; Service missing | Service + SuperAdmin | Service has no comment delete route; SuperAdmin path uses `SuperAdminCompanyController@notedestroy` |
| 10 | clients | ✅ `ServiceCompanyClientController::create` (line 2968) + `store` (line 3095); GET routes missing in inventory but exist elsewhere | ✅ `clients/update/{id}` (line 112, method line 2712) + `edit` (line 2630) | ✅ `store` (line 3095) | ❌ no `clients.destroy` route in inventory | Service | clients are tenant-scoped; delete likely soft-only |
| 11 | users (team members) | ✅ `ServiceCompanyTeamController::create` (line 2543) + `store` (line 2220) — POST `team` (out of filter) but reachable; **`account/sendinvitation`** (line 522) routes to `AdminAccountSettingsController` — **cross-persona!** | ✅ `ServiceCompanyTeamController::edit` (line 1983) + `update` (line 2352) | ✅ via `store` | ✅ `team/teamDelete` (line 489) | Service | cross-persona: `account/sendinvitation` uses **Admin** controller; service has `sendinvitation` semantic elsewhere |
| 12 | role_user / roles / role_has_permissions / model_has_permissions | ✅ `role-permission/storeRole` (line 508); also `assignRole/detachRole` (lines 502-507); **DUPLICATED** `saveRole` (line 563) on `ManageRolePermissionController` | ✅ `updateRole` (line 564); service-side has `saveRole` (line 508) | ✅ `saveRole` ×2 (lines 508, 563) | ✅ `removerole/{user_id?}` (line 511, 567) — twice | Service | **Parallel controllers** — `ServiceCompanyRolesPermissionController` (line 502-511) and `ManageRolePermissionController` (lines 547-567) own the same surface |
| 13 | subscriptions / company_packages / package_invoice (subscription) | ✅ `checkout-subscribe` (line 592) + `custom-checkout-subscribe` (line 594) | ⚠ via update only; no dedicated GET edit | ✅ `applyCoupon` (line 597) + `removeCoupon` (line 598) | ✅ `deletecheckout-subscribe` (line 596, GET) | Service | gated by `ask_purchase` view redirect |
| 14 | packages (admin plans) | ✅ `PackageController::create` line 79 + `store` line 86 | ✅ `edit` line 189 + `update` line 199 | ✅ via store | ✅ `destroy` line 313 | SuperAdmin | clean CRUD |
| 15 | custompackages | ✅ `CustomPackageController::create` line 99 + GET `custompackages/create/{company_id?}` (line 336) + `saveinvoice` (line 338) | ✅ `custompackages/editWallet/{id?}` (line 342) + `custompackages/update/{id}` (line 343) | ✅ `custompackages/submitcomment/{id?}` (line 339) | ⚠ `destroy` line 1589 exists but no route | SuperAdmin | destroy method exists but unreachable from routes |
| 16 | package_invoice / invoices (admin invoices) | ⚠ `custompackages/createInvoice` (line 335) + global-settings.updatePr (line 310) | ⚠ no dedicated edit | ✅ `submitcomment` (line 339) | ❌ no destroy route | SuperAdmin | invoices are immutable in routes; bulk-via-custompackages |
| 17 | company_coins / coin_transection_logs / coin_events / coin_packages / coin_orders | ✅ `coin-events-store` (line 254) + `coin-package-store` (line 257) + `top-upcoin-package-store` (line 269) + `topup-coins-added` (line 271) | ✅ `addEditCoinEventUpdate` is implicit in `coin-event-status` (line 259) | ✅ same as Add | ⚠ `coin-event-status` (line 259) toggles; no hard delete | SuperAdmin | all coin admin flows live on `SuperAdminCompanyController` |
| 18 | departments | ✅ `account/storeDept` (line 521) — POST; **no GET create route** | ❌ | ✅ same as Add | ❌ | Service | POST-only — likely inline admin form |
| 19 | designations | ✅ `ServiceCompanyDesignationController::create` (line 32) + `store` (line 40); **no GET routes in inventory** | ✅ `edit` (line 40) + `update` (line 45) | ✅ via store | ✅ `destroy` (line 65) | Service | full CRUD on controller, but routes are filtered out (likely plain GET CRUD) |
| 20 | skills | ✅ `skill/add-edit` (line 220) — `addEditSkillUpdate`; also `ServiceCompanySkillsController::create` (line 36) | ✅ `skill/update/{id}/{name}` (line 219) + skills controller edit (line 72) | ✅ same as Add | ✅ `skillDelete` (line 221) + skills destroy (line 91) | SuperAdmin + Service | both controllers have full CRUD |
| 21 | job_categories | ✅ `ServiceCompanyJobCategoryController::create` (line 50) + `store` (line 57); **no GET routes in inventory** | ✅ `edit` (line 107) + `update` (line 115) | ✅ via store | ✅ `destroy` (line 146) | Service | export-only in routes; full CRUD via controller |
| 22 | boostkeywords (account boost) | ✅ `boostkeywordadd` (line 463 service / line 276 superadmin) | ❌ | ✅ same as Add | ✅ `boostStatusUpdate` (line 465) toggles | Service + SuperAdmin | status-only delete; no hard destroy |
| 23 | candidate_status / candidateStatus (own table) | ✅ `ServiceCompanyCandidateStatusController::create` (line 43) + `store` (line 55) | ✅ `edit` (line 287) + `update` (line 296) | ✅ via store | ✅ `destroy` (line 316) — but routed to `delete()` (line 625) | Service | full CRUD exists |
| 24 | interview_processes | ✅ `ServiceCompanyInterviewProcessController::create` (line 39) + `store` (line 47) | ✅ `edit` (line 80) + `update` (line 89) | ✅ via store | ✅ `destroy` (line 102) | Service | full CRUD |
| 25 | interview_mode_lavel / master_status (statuses) | ✅ `account/addIntStage` (line 518) | ✅ `account/updatePriority` (line 520) | ⚠ no dedicated store | ✅ `account/delIntStage` (line 519) | Service | wired through account-settings, not own controller |
| 26 | questions (interview questions) | ✅ `ServiceCompanyQuestionController::create` (line 35) + `store` (line 42); also public `questions/store` line 132 (`HomeController@questionsStore`) | ✅ `edit` (line 57) + `update` (line 65) | ✅ via store | ✅ `destroy` (line 76) | Service + Public | public POST writes anonymous |
| 27 | job_qualifications | ❌ no service controller, no route | ❌ | ❌ | ❌ | — | entity exists in DB schema but no CRUD in inventory |
| 28 | sticky_notes | ✅ `ServiceCompanyStickyNotesController::create` (line 38) + `store` (line 44) | ✅ `edit` (line 60) + `update` (line 74) | ✅ via store | ✅ `destroy` (line 83) | Service | full CRUD |
| 29 | todo_items | ✅ `ServiceCompanyTodoItemController::create` (line 57); `store` (line 67) | ✅ `edit` (line 61) + `update` (line 89) — POST `todo-items/update-todo-item` (line 606) | ✅ via store | ✅ `destroy` (line 128) | Service | full CRUD |
| 30 | applicant_notes | ✅ `recruiter-note-update` (line 205, POST) — `ServiceCompanyJobApplicationController@recruiterNoteUpdate` line 634 | ❌ (no GET form; inline AJAX only) | ✅ via the POST itself | ❌ | Service | inline form, no full edit |
| 31 | recruiter_note_master (templates) | ✅ `recruiter-note` POST (line 372) — `RecruiterNoteTemplateController@store` line 28 | ✅ GET `recruiter-note` (line 371) + PUT `recruiter-note/{id}` (line 373) | ✅ via store | ✅ DELETE `recruiter-note/{id}` (line 374) | SuperAdmin | clean REST CRUD |
| 32 | coupons | ✅ `coupons/add-edit/{id?}` GET (line 262) + `coupons/store` (line 263) | ✅ same addEdit path (line 262) | ✅ `coupons/store` (line 263) | ✅ `coupons/destroy` (line 265) | SuperAdmin | toggle via `coupons/toggle-status` (line 264) |
| 33 | footer_settings / footer_menu | ⚠ `SuperAdminFooterSettingsController::create` line 42 + edit line 48; no routes for plain edit | ✅ `footer-settings/update-footer-menu/{id}` (line 50) | ✅ `footer-settings/store-footer-menu` (line 51) | ✅ `destroy` line 151 (no route) | SuperAdmin | methods exist, partial route coverage |
| 34 | language_settings | ✅ `LanguageSettingsController::create` line 77 + `store` line 87 | ✅ `edit` line 112 + `update` line 118 | ✅ via store | ✅ `destroy` line 159 | SuperAdmin | toggle via `change-language-status/{id}` (line 47) |
| 35 | smtp_settings / sms_settings / payment_settings / theme_settings | ❌ no Add/Edit/Submit/Delete routes in scope — config files only | ❌ | ❌ | ❌ | — | managed via `.env` / `config/`, not CRUD |
| 36 | linked_in_settings | ⚠ only `updateStatus/{id}` (line 311, GET) — toggle only | ❌ | ❌ | ❌ | SuperAdmin | toggle-only; no full CRUD |
| 37 | google_sheets | ✅ `candidate-google-sheet-get-data` (line 90) + `google-sheet-get-data` (line 91) + `job-candidate-google-sheet-get-data` (line 92) + `jdgoogle-sheet-get-data` (line 284) | ❌ | ✅ same as Add | ❌ | SuperAdmin | one-way fetch — write only |
| 38 | front_cms_headers / front_image_features / features / front_icon_features | ✅ `front-cms/features` (line 318) + `front-cms/features/{id}` (line 319) + `front-cms/update-common-header` (line 315) + `front-cms/update-header` (line 316) + `features-list/add-edit` (line 168) | ✅ via addEdit path | ✅ via addEdit path | ✅ `front-cms/deleteFeature/{id}` (line 321) | SuperAdmin | full CRUD |
| 39 | company_packages / package_invoice (admin side) | ✅ `company/updateCompanyPackage/{id}` (line 62) — POST | ❌ no GET edit | ✅ same | ❌ | SuperAdmin | update-in-place only |
| 40 | companies (admin edits a company) | ✅ implicit via `companyRegister` flow (line 347); `company/{id}/login` (line 87) impersonate | ✅ `company/edit/{id?}` (line 104) + `company/update/{id?}` (line 105) | ✅ via store | ❌ no destroy | SuperAdmin + SaasFront | admin edits via `SuperAdminCompanyController@update_company` |
| 41 | companies (self-service account) | ✅ `ServiceCompanyAccountSettingsController::edit` (line 475) view; update POST `account/update/{id}` (line 461) | ✅ same edit | ✅ same | ❌ — only `delete-account-store` (line 540, `CompanySettingsController@deleteAccountStore`) soft delete | Service | soft delete only |
| 42 | documents (resumes) | ✅ `resume-upload/store` (line 50) + `uploadresume` (lines 198, 168) | ❌ | ✅ same | ✅ `unset-file` (line 199, `unsetFile`); `job-applications/resume_delete` (line 208); `resume-upload-session/{uuid}` DELETE (line 65) | Service + Public | three independent delete paths |
| 43 | offers (public save-offer) | ✅ `save-offer` (lines 380, 404) — `FrontJobOfferController@saveOffer` | ❌ | ✅ same | ❌ | Front | write-only public endpoint |
| 44 | jobs (public apply = job_applications) | ✅ `job/saveApplication` (lines 381, 405) — `FrontJobsController@saveApplication` | ❌ | ✅ same | ❌ | Front | public POST; no edit/delete |
| 45 | invoices / paypal_invoices / razorpay_invoices / stripe_invoices / phonepe_invoices (webhooks) | — N/A (gateway-driven) | — N/A | ✅ `verify-billing-ipn` (line 418), `save-invoices` (line 419), `save-razorpay-invoices` (line 420), `create-payment` (line 270), `payment-callback` (line 272, 298) | — N/A | Payment Webhook | **unauthenticated POST** — see hotspots §G |
| 46 | link_payments | ❌ no route | ❌ | ❌ | ❌ | — | table exists, no CRUD wired |
| 47 | custom_modules | ⚠ `custom-modules/verify-purchase` (line 365) — POST verification only | ❌ | ⚠ same | ❌ | SuperAdmin | verify-only, no install/uninstall UI in routes |
| 48 | default-terms / offer_terms_conditions | ✅ `default-terms/add-edit` (line 248) | ✅ same | ✅ `companysavecreateoffer` (line 250) — POST save | ❌ no destroy | SuperAdmin | in-place edit |
| 49 | th_email_template / emailthemes | ✅ `email-themes/add-edit` (line 157) | ✅ `email-themes/update/{id}` (line 156) | ✅ same | ❌ no destroy | SuperAdmin | toggle-only delete via status |
| 50 | whatsapp_themes | ✅ `whatsapp-themes/add-edit` (line 162) | ✅ `whatsapp-themes/update/{id}` (line 161) | ✅ same | ❌ no destroy | SuperAdmin | toggle-only delete via status |
| 51 | package_features (sub of packages) | ✅ `add-edit-package-features/{id?}` (line 328) — `PackageController@add_edit_package_feature` | ✅ same | ✅ same | ❌ no destroy | SuperAdmin | single endpoint covers Add/Edit |
| 52 | package_master / package_invoice_tax | ❌ no Add/Edit/Submit/Delete routes | ❌ | ❌ | ❌ | — | managed via packages + invoice flows |
| 53 | recruiter_note_master (already row 31) | — | — | — | — | — | see row 31 |
| 54 | coin_package_points / coin_package_types | ⚠ `topuptypegetprice` (line 272) — read-only AJAX | ❌ | ❌ | ❌ | SuperAdmin | read-only via package edit |
| 55 | package_invoice_comment | ✅ `custompackages/submitcomment/{id?}` (line 339) | ❌ | ✅ same | ❌ no destroy | SuperAdmin | append-only |

### 2.1 Coverage summary

- **Service Co fully wired Add/Edit/Submit/Delete:** skills (own controller), job_categories, candidate_status, interview_processes, questions, sticky_notes, todo_items.
- **Service Co POST-only / inline-only:** departments, applicant_notes, application_status/interview_mode_lavel — no GET create/edit pages.
- **Super Admin fully wired:** coupons, language_settings, recruiter_note_master, front_cms features, packages, custompackages (minus destroy), themes.
- **Wire-only (no GET UI):** google_sheets, coin_events/packages/top_up, company_settings fields.
- **No CRUD at all:** smtp/sms/payment_settings (config), link_payments, package_master, package_invoice_tax, coin_package_points, job_qualifications.
- **Webhooks:** all billing webhooks (PayPal IPN, Stripe, Razorpay, PhonePe) are unauthenticated POST endpoints.

**TOTAL_ROWS: 55 entity groups.**

---

## 3. Transitions / State-Change Route List (broad sweep, 196 rows)

Scope: same narrow audit scope as §1 — `routes/service.php`, `routes/superadmin.php`, and the in-scope block of `routes/web.php` (Auth, Front, SaasFront, Admin inline, Payment/Webhooks). Out: `routes/product.php`, `routes/service_old.php`, controllers under `App\Http\Controllers\ProductCompany\*` and `StaffingCompany\*`.

### 3.1 Methodology

This is a **re-filter** of the 196-row narrow inventory. A row qualifies as a "transition" if **any** of:

1. **HTTP verb is PUT / PATCH / DELETE** (inherently state-changing).
2. **HTTP verb is POST** AND URI or name matches one of: status, change-status, switch, toggle, approve, reject, assign, invite, verify, login, impersonate, accept, decline, cancel, complete, start, stop, pause, resume, open, close, archive, restore, publish, unpublish, active, inactive, dashboard, follower, like, share, connect, disconnect, send, push, dispatch, queue, schedule, send-link, follow, unfollow, followup, send-mail, send_mail, send_mail_for_candidate, mass_mail, mass_mail_send, send_whatsapp, application_copy, copy_current, candidate_process_update, fwd, contactPersion, updateAccessCode, recruiter, workingStatus, roleupdate, billingDetails, marketautoApproval, jobApproval, candidateApproval, autoApproval, reject_mail, job_reject_mail, reject_mail_send, addEdit, single-notification, allNotification, notification, singleNotification, loginAsCompany, sendOtpCode, verifyOtpCode, mark-read, mark-notification-read, updatePassword, resetPassword, forgot, register, registerChk, verifyTeam, invitation, company-store, addEditfeatures, addEditskills, addEditTermsupdate, addEditnotificationupdate, addEditCoinEventUpdate, addEditwhatsappThemesupdate, addEditemailThemesupdate, addEditSkillUpdate, add_meeting, store-schedule, addMeeting, deleteMeeting, storeSchedule, saveApplication, saveOffer, save_webhook, saveInvoices, saveResume, saveinvoice, saveRole, saveCommercial, saveSignature, saveSetting, savePolicy, saveInvoice, saveTemplate, savePool, saveCall, saveConnect, saveAddress, saveCandidate, saveJob, saveMap, saveChanges, saveDraft, saveNotification, save-coins, save-comm, save-offer, fetch-country-state, get_data, getData, getdata, get_key, ajax, postCon, newsletter, send-otp, connect, push.
3. **URI contains** any of: statusupdate, removeJob, sendinvitation, sendresumemails, sendinterviewmail, sendPayment, import, export, _submit, add-to-queue, notify, kick, trigger, ingest, reindex, reIndex, rebuild, cache/invalidate, cache-warm.

A small number of borderline rows (purely additive `update` / `save*` writes with no status flip) are kept but tagged **`UPDATE row`** so the entity-level audit can still cross-reference CRUD coverage. UI-only AJAX (e.g. `get_client_job`, `fetch-country-state`) is bucketed as **`READ-AJAX`** and not counted as a real transition.

Total filtered rows: 196 (the same set; this filter only **re-labels** them, it does not drop any). Transition class assignments:

- `STATE TRANSITION` — moves a row through a status pipeline (status flip, stage move, offer accept, impersonate).
- `UPDATE row` — single-row mutation, no status flip.
- `BULK UPDATE` — many rows at once (assignRole, detachRole, mass_mail, saveAll, addAssignment).
- `SIDE EFFECT` — does not write the primary entity; triggers mail / whatsapp / notification / ES reindex.
- `IMPORT` — pulls data in (sheet import, resume parse, meeting import).
- `EXPORT` — pulls data out (csv, sample download).
- `EXTERNAL CALL` — pass-through to a third-party gateway (webhook, payment callback).
- `READ-AJAX` — feeds the UI without writing.

Source: §1 (196 rows, 2026-08-15). Cross-reference matrix: §4.

### 3.2 routes/service.php — Service Co (59 rows)

| Line | Method | URI | Name | Controller@Method | Transition class | DB table(s) |
|------|--------|-----|------|-------------------|------------------|-------------|
| 49 | POST | resume-upload/presign | presign | ResumeUploadController@presign | SIDE EFFECT (presign S3/R2) | documents |
| 50 | POST | resume-upload/store | store | ResumeUploadController@store | SIDE EFFECT (commit upload) | documents |
| 52 | POST | resume-upload/{id}/retry | retry | ResumeUploadController@retry | SIDE EFFECT (resume retry) | documents |
| 56 | POST | resume-upload-session/ | (none) | ResumeUploadSessionController@store | SIDE EFFECT (session start) | documents |
| 62 | POST | resume-upload-session/{uuid}/finalize | (none) | ResumeUploadSessionController@finalize | STATE TRANSITION (upload finalize) | documents |
| 65 | DELETE | resume-upload-session/{uuid} | (none) | ResumeUploadSessionController@cancel | STATE TRANSITION (upload cancel) | documents |
| 76 | POST | api/cache/invalidate | cache.invalidate | ServiceCompanyDashboardApiController@invalidateCache | SIDE EFFECT (cache wipe) | (redis only) |
| 84 | POST | meeting-comment-new/{id?} | dashboard.meeting-comment-new | ServiceCompanyDashboardController@meetingcommentnew | UPDATE row | comments |
| 112 | POST | clients/update/{id} | clients.update | ServiceCompanyClientController@update | UPDATE row | clients |
| 150 | POST | jobs_hiring_contact_person/{id?} | jobs.hiring_contact_person | ServiceCompanyJobsController@hiring_contact_person | UPDATE row | client_contact_info |
| 151 | POST | jobs_mail_contact_person/{id?} | jobs.mail_contact_person | ServiceCompanyJobsController@mail_contact_person | SIDE EFFECT (send to contact) | client_contact_info |
| 165 | GET | jobs/assignRejected | jobs.assignRejected | ServiceCompanyJobsController@assignRejected | STATE TRANSITION (assign rejected candidate) | jobs |
| 166 | GET | jobs/removeJob | jobs.removeJob | ServiceCompanyJobsController@removeJob | STATE TRANSITION (soft delete job) | jobs |
| 170 | POST | jobs/updatestatus | jobs.updatestatus | ServiceCompanyJobsController@updatestatus | STATE TRANSITION (job status) | jobs |
| 171 | POST | jobs/hiring_manager/{id?} | jobs.hiring_manager | ServiceCompanyJobsController@hiring_manager | UPDATE row | client_contact_info |
| 172 | POST | jobs/newUpdateJobstatus | jobs.newUpdateJobstatus | ServiceCompanyJobsController@newUpdateJobstatus | STATE TRANSITION (job status v2) | jobs |
| 173 | POST | jobs/update/{id} | jobs.update | ServiceCompanyJobsController@update | UPDATE row | jobs / service_on_jobs |
| 192 | GET | job-applications/create/{jobId?}/{appId?} | job-applications.create | ServiceCompanyJobApplicationController@create | READ-AJAX (form render) | job_applications |
| 195 | GET | job-applications/clientcreate/{jobId?}/{appId?} | job-applications.clientcreate | ServiceCompanyJobApplicationController@clientcreate | READ-AJAX (form render) | job_applications |
| 197 | POST | job-applications/update/{id} | job-applications.update | ServiceCompanyJobApplicationController@update | UPDATE row | job_applications |
| 198 | POST | uploadresume | job-applications.uploadresume | ServiceCompanyJobApplicationController@uploadresume | SIDE EFFECT (file commit) | documents |
| 199 | POST | unset-file | file.unset | ServiceCompanyJobApplicationController@unsetFile | STATE TRANSITION (detach file) | documents |
| 203 | POST | job-applications/accessEmailTemplate | job-applications.accessEmailTemplate | ServiceCompanyJobApplicationController@accessEmailTemplate | UPDATE row | email_templates |
| 205 | POST | recruiter-note-update | job-applications.recruiterNoteUpdate | ServiceCompanyJobApplicationController@recruiterNoteUpdate | UPDATE row | applicant_notes |
| 206 | POST | job-applications/stage_submit | job-applications.stage_submit | ServiceCompanyJobApplicationController@stage_submit | STATE TRANSITION (stage pipeline) | job_applications / application_event |
| 207 | POST | job-applications/client_stage_submit | job-applications.client_stage_submit | ServiceCompanyJobApplicationController@client_stage_submit | STATE TRANSITION (stage pipeline, client view) | job_applications |
| 208 | POST | job-applications/resume_delete | job-applications.resume_delete | ServiceCompanyJobApplicationController@resume_delete | STATE TRANSITION (file delete) | documents |
| 209 | POST | job-applications/statusupdate | job-applications.statusupdate | ServiceCompanyJobApplicationController@statusupdate | STATE TRANSITION (application status) | job_applications |
| 210 | POST | job-applications/store-schedule | job-applications.store-schedule | ServiceCompanyJobApplicationController@storeSchedule | STATE TRANSITION (interview scheduled) | interview_schedules |
| 213 | POST | job-applications/getGoogleVideoUrl | job-applications.getGoogleVideoUrl | ServiceCompanyJobApplicationController@getGoogleVideoUrl | SIDE EFFECT (google meeting URL) | interview_schedules |
| 214 | POST | change-event-organizer/{eventId} | (none) | ServiceCompanyJobApplicationController@changeEventOrganizer | STATE TRANSITION (organizer swap) | interview_schedules |
| 217 | POST | job-applications/recruiter/{id?} | job-applications.recruiter | ServiceCompanyJobApplicationController@recruiter | STATE TRANSITION (recruiter reassign) | job_applications |
| 218 | POST | contact-persion-team/recruiter/{id?} | job-applications.contactPersionWithTeam | ServiceCompanyJobApplicationController@contactPersionWithTeam | STATE TRANSITION (contact assign) | job_applications / client_contact_info |
| 219 | POST | job-applications/clientappstore | job-applications.clientappstore | ServiceCompanyJobApplicationController@clientappstore | STATE TRANSITION (client-side create) | job_applications |
| 220 | GET | job-applications/download/{id} | job-applications.download | ServiceCompanyJobApplicationController@download | EXPORT (file stream) | documents |
| 221 | GET | export-job-applications | export.sample-job-applications | ServiceCompanyJobApplicationController@sampleDownload | EXPORT (sample CSV) | job_applications |
| 222 | GET | candidate-export-job-applications | candidateexport.candidate-job-applications | ServiceCompanyJobApplicationController@candidatejobDownload | EXPORT (candidate CSV) | job_applications |
| 223 | POST | import-job-applications | import.sample-job-applications | ServiceCompanyJobApplicationController@importJobCondidate | IMPORT (sheet → applications) | job_applications |
| 225 | POST | extract-resume-applications | extract.resume-job-applications | ServiceCompanyJobApplicationController@resumExtract | IMPORT (resume parse) | job_applications / documents |
| 226 | GET | create-offer/{id} | job-applications.create_offer | ServiceCompanyJobApplicationController@createOffer | READ-AJAX (form render) | application_offer |
| 227 | POST | save-create-offer | job-applications.save_create_offer | ServiceCompanyJobApplicationController@saveCreateOffer | STATE TRANSITION (offer draft → sent) | offer_job_applications / application_offer |
| 228 | POST | preview-offer-letter/{id?} | cvtracker.previewOfferLettetr | ServiceCompanyCvTrackerController@previewOfferLettetr | READ-AJAX (offer preview) | offer_job_applications |
| 229 | POST | terms-save-create-offer | job-applications.terms_save_create_offer | ServiceCompanyJobApplicationController@termsSaveCreateOffer | UPDATE row | offer_job_applications |
| 240 | GET | resume-as-pdf/{id} | job-applications.resume-as-pdf | ServiceCompanyJobApplicationController@resumeAsPdf | EXPORT (PDF render) | documents |
| 244 | POST | offer-mail-format/{id?} | cvtracker.offer_send_mail | ServiceCompanyCvTrackerController@offerSendMail | SIDE EFFECT (offer email) | offer_job_applications |
| 245 | POST | offer-send-mail/fwd_offer_send | cvtracker.fwd_offer_send | ServiceCompanyCvTrackerController@fwd_offer_send | SIDE EFFECT (forward offer) | offer_job_applications |
| 246 | POST | offer_download_mail/{id?} | cvtracker.offer_download_mail | ServiceCompanyCvTrackerController@fwd_offer_download | EXPORT (file stream) | offer_job_applications |
| 283 | GET | apptracker/export/{jobs}/{mstatus_id}/{start}/{length} | apptracker.export | ServiceCompanyAppTrackerController@export | EXPORT (CSV) | job_applications |
| 291 | POST | apptracker/store | apptracker.store | ServiceCompanyAppTrackerController@store | BULK UPDATE (store many apps) | job_applications |
| 297 | POST | tickets/{ticket}/status | tickets.ticketUpdateStatus | ServiceCompanyAppTrackerController@ticketUpdateStatus | STATE TRANSITION (ticket status) | tickets |
| 298 | GET | ticketcreate | tickets.create | ServiceCompanyAppTrackerController@ticketCreate | READ-AJAX (form render) | tickets |
| 300 | POST | storeticketcreate | tickets.store | ServiceCompanyAppTrackerController@storeticketCreate | STATE TRANSITION (ticket open) | tickets |
| 301 | POST | comments/store | comments.store | ServiceCompanyAppTrackerController@storetComment | UPDATE row | comments |
| 307 | POST | cvtracker/fwd_resume/{id?} | cvtracker.fwd_resume | ServiceCompanyCvTrackerController@fwd_resume | SIDE EFFECT (forward resume) | job_applications / documents |
| 308 | POST | mass_mail/mass_mail/{id?} | cvtracker.mass_mail | ServiceCompanyCvTrackerController@mass_mail | BULK UPDATE + SIDE EFFECT (mail blast) | job_applications |
| 309 | POST | send-mail/send-mail/{id?} | cvtracker.send_mail | ServiceCompanyCvTrackerController@send_mail | SIDE EFFECT (single mail) | job_applications |
| 310 | POST | send-whatsapp/send-whatsapp/{id?} | cvtracker.send_whatsapp | ServiceCompanyCvTrackerController@send_whatsapp | SIDE EFFECT (whatsapp send) | job_applications |
| 311 | POST | cvtracker/application_copy | cvtracker.application_copy | ServiceCompanyCvTrackerController@application_copy | STATE TRANSITION (copy app to job) | job_applications |
| 312 | POST | talent-add | cvtracker.talent-add | ServiceCompanyCvTrackerController@talentAdd | STATE TRANSITION (add to bench) | add_job_wise_condidates |
| 320 | GET | cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length} | cvtracker.export | ServiceCompanyCvTrackerController@export | EXPORT (CSV) | add_job_wise_condidates |
| 328 | POST | cvtracker/get_client_job | cvtracker.get_client_job | ServiceCompanyCvTrackerController@get_client_job | READ-AJAX (jobs dropdown) | jobs |
| 329 | POST | cvtracker/candidate_process_update | cvtracker.candidate_process_update | ServiceCompanyCvTrackerController@candidate_process_update | STATE TRANSITION (move candidate between stages) | job_applications / add_job_wise_condidates |
| 330 | POST | cvtracker/copy_current_selected_applicant | cvtracker.copy_current_selected_applicant | ServiceCompanyCvTrackerController@copy_current_selected_applicant | STATE TRANSITION (clone applicant) | job_applications |
| 331 | POST | cvtracker/email_template | cvtracker.email_template | ServiceCompanyCvTrackerController@email_template | UPDATE row | email_templates |
| 332 | POST | cvtracker/fwd_resume_send | cvtracker.fwd_resume_send | ServiceCompanyCvTrackerController@fwd_resume_send | SIDE EFFECT (fwd resume + mail) | job_applications |
| 333 | POST | mass_cvtracker/mass_mail_send | cvtracker.mass_mail_send | ServiceCompanyCvTrackerController@mass_mail_send | BULK UPDATE + SIDE EFFECT (mass mail) | job_applications |
| 334 | POST | send_cvtracker/send_mail_send | cvtracker.send_mail_send | ServiceCompanyCvTrackerController@send_mail_send | SIDE EFFECT (single mail) | job_applications |
| 336 | POST | send_cvtracker/send_mail_for_candidate | cvtracker.send_mail_for_candidate | ServiceCompanyCvTrackerController@send_mail_for_candidate | SIDE EFFECT (candidate-targeted mail) | job_applications |
| 346 | POST | interview-schedule/followup-update | interview-schedule.followup_update | InterviewScheduleController@followupUpdate | UPDATE row | interview_schedules |
| 347 | POST | interview-schedule/send-link | interview-schedule.send_link | InterviewScheduleController@sendInterviewLink | SIDE EFFECT (send interview link) | interview_schedules |
| 351 | POST | interview-schedule/change-status-multiple | interview-schedule.change-status-multiple | InterviewScheduleController@changeStatusMultiple | BULK UPDATE (status flip) | interview_schedules |
| 353 | GET | interview-schedule/export/{start}/{length} | interview-schedule.export | InterviewScheduleController@export | EXPORT (CSV) | interview_schedules |
| 356 | POST | interview-schedule/updateFeedbackStatus | interview-schedule.updateFeedbackStatus | InterviewScheduleController@updateFeedbackStatus | STATE TRANSITION (feedback collected) | interview_schedules |
| 357 | POST | interview-schedule/updateAvailableStatus | interview-schedule.updateAvailableStatus | InterviewScheduleController@updateAvailableStatus | STATE TRANSITION (candidate available) | interview_schedules |
| 358 | POST | interview-schedule/updateInterview | interview-schedule.updateInterview | InterviewScheduleController@updateInterview | UPDATE row | interview_schedules |
| 366 | GET | applicationoffer/export/{start}/{length} | applicationoffer.export | ServiceCompanyApplicationOfferController@export | EXPORT (CSV) | application_offer |
| 368 | POST | applicationoffer/update_salary | applicationoffer.update_salary | ServiceCompanyApplicationOfferController@updateSalary | UPDATE row | application_offer |
| 369 | POST | applicationoffer/update_application | applicationoffer.update_application | ServiceCompanyApplicationOfferController@update_application | STATE TRANSITION (offer → application link) | application_offer |
| 375 | GET | job-onboard/send-offer/{id?} | job-onboard.send-offer | ServiceCompanyJobOnboardController@sendOffer | SIDE EFFECT (send offer email) | application_offer |
| 376 | GET | job-onboard/update-status/{id?} | job-onboard.update-status | ServiceCompanyJobOnboardController@updateStatus | STATE TRANSITION (onboard status) | application_offer |
| 377 | GET | job-onboard/export/{start}/{length} | job-onboard.export | ServiceCompanyJobOnboardController@export | EXPORT (CSV) | application_offer |
| 385 | GET | consultancy/export/{start}/{length} | consultancy.export | ServiceCompanyConsultancyController@export | EXPORT (CSV) | clients |
| 386 | GET | consultancy/staffing_invitation/{id?} | consultancy.staffing_invitation | ServiceCompanyConsultancyController@staffing_invitation | STATE TRANSITION (staffing partner invite) | clients |
| 400 | POST | boostkeywordadd | bench-candidates.boostkeywordadd | ServiceCompanyBenchCandidatesController@boostkeywordadd | UPDATE row | boostkeywords |
| 401 | POST | bench-switch | bench-candidates.benchSwitch | ServiceCompanyBenchCandidatesController@benchSwitch | STATE TRANSITION (bench on/off) | add_job_wise_condidates |
| 413 | POST | bench-candidates/save-assignment-details | bench-candidates.savePlacedAssignmentDetails | ServiceCompanyBenchCandidatesController@savePlacedAssignmentDetails | UPDATE row | bench_applications |
| 414 | POST | bench-candidates/save-commercial-config | bench-candidates.savePlacedCommercialConfig | ServiceCompanyBenchCandidatesController@savePlacedCommercialConfig | UPDATE row | bench_applications |
| 433 | GET | bench-candidates/download/{id} | bench-candidates.download | ServiceCompanyBenchCandidatesController@download | EXPORT (file stream) | documents |
| 439 | POST | bench-candidates/update_placed_end_date | bench-candidates.update_placed_end_date | ServiceCompanyBenchCandidatesController@update_placed_end_date | UPDATE row | bench_applications |
| 440 | POST | bench-candidates/status_submit | bench-candidates.status_submit | ServiceCompanyBenchCandidatesController@status_submit | STATE TRANSITION (bench status pipeline) | bench_applications |
| 445 | POST | add-update-services-job | jobs.addUpdateJobServices | ServiceCompanyJobsController@jobServicesAddUpdate | UPDATE row | jobs / service_on_jobs |
| 446 | POST | jobs/updateserviceviewmore | jobs.updateserviceviewmore | ServiceCompanyJobsController@updateserviceviewmore | UPDATE row | jobs |
| 447 | POST | download-resume-bench-candidates/ | bench-candidates.downloadResumeBenchCandidate | ServiceCompanyBenchCandidatesController@downloadResumeBenchCandidate | EXPORT (file stream) | documents |
| 456 | POST | career-page/settings/save | career.page.settings.save | ServiceCompanyAccountSettingsController@careerPageSettingsSave | UPDATE row | company_settings / companies |
| 461 | POST | account/update/{id} | account.update | ServiceCompanyAccountSettingsController@update | UPDATE row | companies |
| 462 | POST | account/videoMeetingSeetingUpdate | account.videoMeetingSeetingUpdate | ServiceCompanyAccountSettingsController@videoMeetingSeetingUpdate | UPDATE row | companies |
| 463 | POST | boostkeywordadd | account.boostkeywordadd | ServiceCompanyAccountSettingsController@boostkeywordadd | UPDATE row | boostkeywords |
| 465 | POST | boostStatusUpdate | account.boostStatusUpdate | ServiceCompanyAccountSettingsController@boostStatusUpdate | STATE TRANSITION (boost toggle) | boostkeywords |
| 487 | POST | team/resetpassword | team.resetpassword | ServiceCompanyTeamController@resetpassword | STATE TRANSITION (password reset link) | users |
| 488 | POST | team/statusUpdate | team.statusUpdate | ServiceCompanyTeamController@statusUpdate | STATE TRANSITION (active/inactive) | users |
| 489 | POST | team/teamDelete | team.teamDelete | ServiceCompanyTeamController@teamDelete | STATE TRANSITION (soft delete team) | users / role_user |
| 490 | POST | team/reSendmail | team.reSendmail | ServiceCompanyTeamController@reSendmail | SIDE EFFECT (re-send invite) | users |
| 491 | ANY | team/roleupdate | team.roleupdate | ServiceCompanyTeamController@roleupdate | STATE TRANSITION (role attach/detach) | role_user / model_has_roles |
| 492 | POST | team/WorkingStatus | team.WorkingStatus | ServiceCompanyTeamController@WorkingStatus | STATE TRANSITION (working on/off) | users |
| 502-507 | POST | role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | ServiceCompanyRolesPermissionController@* | BULK UPDATE (role/permission sync) | role_has_permissions / role_user / model_has_permissions |
| 508 | POST | role-permission/saveRole | settings.saveRole | ServiceCompanyRolesPermissionController@saveRole | UPDATE row | roles / role_has_permissions |
| 511 | DELETE | role-permission/removerole/{user_id?} | settings.removerole | ServiceCompanyRolesPermissionController@removerole | STATE TRANSITION (role detach) | role_user |
| 518 | POST | account/addIntStage | account.addIntStage | ServiceCompanyAccountSettingsController@addIntStage | STATE TRANSITION (add interview stage) | master_status / interview_processes |
| 519 | POST | account/delIntStage | account.delIntStage | ServiceCompanyAccountSettingsController@delIntStage | STATE TRANSITION (remove interview stage) | master_status / interview_processes |
| 520 | POST | account/updatePriority | account.updatePriority | ServiceCompanyAccountSettingsController@updatePriority | UPDATE row (reorder) | master_status |
| 521 | POST | account/storeDept | account.storeDept | ServiceCompanyAccountSettingsController@storeDept | UPDATE row | departments |
| 522 | POST | account/sendinvitation | account.sendinvitation | AdminAccountSettingsController@sendinvitation | STATE TRANSITION (invite team member) | users |
| 523 | POST | addressupdate | account.addressupdate | ServiceCompanyAccountSettingsController@addressupdate | UPDATE row | companies |
| 525 | POST | companysavecreateoffer | account.companysavecreateoffer | ServiceCompanyAccountSettingsController@companysavecreateoffer | UPDATE row | offer_terms_conditions |
| 529 | GET | job-categories/export/{start}/{length} | job-categories.export | ServiceCompanyJobCategoryController@export | EXPORT (CSV) | job_categories |
| 534 | POST | jobtracker/store | jobtracker.store | ServiceCompanyJobTrackerController@store | BULK UPDATE (apps from tracker) | jobs / job_applications |
| 535 | POST | jobtracker/updateinformation | jobtracker.updateinformation | ServiceCompanyJobTrackerController@updateinformation | UPDATE row | job_applications |
| 540 | POST | delete-account-store | settings.delete-account-store | CompanySettingsController@deleteAccountStore | STATE TRANSITION (soft account delete) | companies |
| 547-552 | POST | role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | ManageRolePermissionController@* | BULK UPDATE (role/permission sync) | role_has_permissions / role_user / model_has_permissions |
| 563 | POST | saveRole | settings.saveRole | ManageRolePermissionController@saveRole | UPDATE row | roles / role_has_permissions |
| 564 | POST | updateRole | settings.updateRole | ManageRolePermissionController@updateRole | UPDATE row | roles |
| 567 | DELETE | removerole/{user_id?} | settings.removerole | ManageRolePermissionController@removerole | STATE TRANSITION (role detach) | role_user |
| 592 | POST | checkout-subscribe | subscribe.checkoutSubscribe | ManageSubscriptionController@checkoutSubscribe | STATE TRANSITION (subscription start) | subscriptions / package_invoice |
| 594 | POST | custom-checkout-subscribe | subscribe.customCheckoutSubscribe | ManageSubscriptionController@customCheckoutSubscribe | STATE TRANSITION (custom plan start) | custompackages / package_invoice |
| 596 | GET | deletecheckout-subscribe | subscribe.deletecheckout | ManageSubscriptionController@deletecheckout | STATE TRANSITION (subscription cancel) | package_invoice |
| 597 | POST | checkout-subscribe/apply-coupon | subscribe.applyCoupon | ManageSubscriptionController@applyCoupon | UPDATE row | coupons |
| 598 | POST | checkout-subscribe/remove-coupon | subscribe.removeCoupon | ManageSubscriptionController@removeCoupon | UPDATE row | coupons |
| 603 | POST | auth-custom-package/validatePackage | auth-custom-package.validatePackage | AuthCustomPackageController@validatePackage | READ-AJAX (form render) | custompackages |
| 604 | POST | auth-custom-package/validateClientPackage | auth-custom-package.validateClientPackage | AuthCustomPackageController@validateClientPackage | READ-AJAX (form render) | custompackages |
| 606 | POST | todo-items/update-todo-item | todo-items.updateTodoItem | ServiceCompanyTodoItemController@updateTodoItem | UPDATE row | todo_items |

### 3.3 routes/superadmin.php — Super Admin (90 rows)

| Line | Method | URI | Name | Controller@Method | Transition class | DB table(s) |
|------|--------|-----|------|-------------------|------------------|-------------|
| 47 | PUT | language-settings/change-language-status/{id} | language-settings.changeStatus | LanguageSettingsController@changeStatus | STATE TRANSITION (toggle) | language_settings |
| 50 | PUT | footer-settings/update-footer-menu/{id} | footer-settings.updateFooterMenu | SuperAdminFooterSettingsController@updateFooterMenu | UPDATE row | footer_menu |
| 51 | POST | footer-settings/store-footer-menu | footer-settings.storeFooterMenu | SuperAdminFooterSettingsController@storeFooterMenu | UPDATE row | footer_menu |
| 54 | POST | update-application/deleteFile | update-application.deleteFile | UpdateApplicationController@deleteFile | STATE TRANSITION (file delete) | (app-update log) |
| 62 | POST | company/updateCompanyPackage/{id} | company.updateCompanyPackage | SuperAdminCompanyController@updateCompanyPackage | STATE TRANSITION (package swap) | company_packages / package_invoice |
| 69 | POST | setting-google-meeting-store | company.settingGoogleMeetingStore | SuperAdminCompanyController@settingGoogleMeetingStore | UPDATE row | companies (settings blob) |
| 71 | POST | google-meet-status-update | company.googleMeetStatusUpdate | SuperAdminCompanyController@googleMeetStatusUpdate | STATE TRANSITION (toggle) | companies (settings blob) |
| 76 | POST | setting-team-meeting-store | company.settingteamMeetingStore | SuperAdminCompanyController@settingteamMeetingStore | UPDATE row | companies (settings blob) |
| 77 | POST | team-meet-status-update | company.teamMeetStatusUpdate | SuperAdminCompanyController@teamMeetStatusUpdate | STATE TRANSITION (toggle) | companies (settings blob) |
| 79 | POST | add-meeting-list | company.addMeetingLists | SuperAdminCompanyController@addMeetingLists | UPDATE row | (settings blob) |
| 81 | POST | meeting-list-delete | company.meetingListDelete | SuperAdminCompanyController@meetingListDelete | STATE TRANSITION (meeting row delete) | (settings blob) |
| 87 | POST | company/{id}/login | company.loginAsCompany | SuperAdminCompanyController@loginAsCompany | STATE TRANSITION (impersonate) | companies |
| 90 | POST | candidate-google-sheet-get-data | company.googleSheetCandidateData | SuperAdminCompanyController@googleSheetCandidateData | READ-AJAX (sheet preview) | google_sheets |
| 91 | POST | google-sheet-get-data | company.googleSheetInsertdataInDatabase | SuperAdminCompanyController@googleSheetInsertdataInDatabase | IMPORT (sheet → DB) | google_sheets |
| 92 | POST | job-candidate-google-sheet-get-data | company.googleSheetCandidateDataJob | SuperAdminCompanyController@googleSheetCandidateDataJob | READ-AJAX (sheet preview) | google_sheets |
| 93 | POST | single-notification-update | company.SingleNotificationUpdate | SuperAdminCompanyController@SingleNotificationUpdateS | STATE TRANSITION (mark read) | notifications |
| 98 | POST | billing/viewmore | company.billinDetails | SuperAdminCompanyController@billinDetails | READ-AJAX (modal render) | package_invoice |
| 104 | GET | company/edit/{id?} | company.edit_company | SuperAdminCompanyController@edit_company | READ-AJAX (form render) | companies |
| 105 | POST | company/update/{id?} | company.update_company | SuperAdminCompanyController@update_company | UPDATE row | companies |
| 113 | POST | getmeetingchild | company.getmeetingchild | SuperAdminCompanyController@getmeetingchild | READ-AJAX | (settings blob) |
| 115 | POST | company/recruiter/{id?} | company.recruiter | SuperAdminCompanyController@recruiter | STATE TRANSITION (assign recruiter) | role_user |
| 117 | POST | meetingstoreSchedule | company.store-schedule | SuperAdminCompanyController@storeSchedule | STATE TRANSITION (schedule created) | interview_schedules |
| 128 | POST | updateAccessCode | company.updateAccessCode | SuperAdminCompanyController@updateAccessCode | UPDATE row | companies |
| 140 | POST | team/statusUpdate | team.statusUpdate | SuperAdminTeamController@statusUpdate | STATE TRANSITION (toggle) | users |
| 142 | POST | team/WorkingStatus | team.WorkingStatus | SuperAdminTeamController@WorkingStatus | STATE TRANSITION (working on/off) | users |
| 156 | ANY | email-themes/update/{id} | company.emailThemesupdate | SuperAdminCompanyController@emailThemesupdate | UPDATE row | th_email_template |
| 157 | POST | email-themes/add-edit | company.addEditemailThemesupdate | SuperAdminCompanyController@addEditemailThemesupdate | UPDATE row | th_email_template / emailthemes |
| 161 | ANY | whatsapp-themes/update/{id} | company.whatsappThemesupdate | SuperAdminCompanyController@whatsappThemesupdate | UPDATE row | whatsapp_themes |
| 162 | POST | whatsapp-themes/add-edit | company.addEditwhatsappThemesupdate | SuperAdminCompanyController@addEditwhatsappThemesupdate | UPDATE row | whatsapp_themes |
| 166 | ANY | features-list/update/{id} | company.featuresListupdate | SuperAdminCompanyController@featuresListupdate | UPDATE row | features / front_icon_features |
| 168 | POST | features-list/add-edit | company.addEditfeaturesListupdate | SuperAdminCompanyController@addEditfeaturesListupdate | UPDATE row | features / front_icon_features |
| 176 | POST | market-auto-approval | company.market-auto-approval | SuperAdminCompanyController@marketautoApproval | STATE TRANSITION (auto-approve toggle) | jobs |
| 177 | POST | job-approval | company.job-approval | SuperAdminCompanyController@jobApproval | STATE TRANSITION (approve job) | jobs |
| 178 | POST | job_reject_mail/reject_mail/{id?} | company.job_reject_mail | SuperAdminCompanyController@job_reject_mail | SIDE EFFECT (reject + mail) | jobs |
| 179 | POST | reject_job/reject_mail_send | company.reject_mail_send_job | SuperAdminCompanyController@reject_mail_send_job | SIDE EFFECT (reject send) | jobs |
| 185 | POST | candidate-approval | company.candidate-approval | SuperAdminCompanyController@candidateApproval | STATE TRANSITION (approve candidate) | job_applications / add_job_wise_condidates |
| 186 | POST | auto-approval | company.auto-approval | SuperAdminCompanyController@autoApproval | STATE TRANSITION (auto-approve toggle) | jobs |
| 187 | POST | reject_mail/reject_mail/{id?} | company.reject_mail | SuperAdminCompanyController@reject_mail | SIDE EFFECT (reject + mail) | job_applications / add_job_wise_condidates |
| 188 | POST | email_template | company.email_template | SuperAdminCompanyController@email_template | UPDATE row | th_email_template / emailthemes |
| 189 | POST | reject_cvtracker/reject_mail_send | company.reject_mail_send | SuperAdminCompanyController@reject_mail_send | SIDE EFFECT (reject send) | job_applications / add_job_wise_condidates |
| 191 | POST | setting-zoom-store | company.settingZoomStore | SuperAdminCompanyController@settingZoomStore | UPDATE row | (settings blob) |
| 219 | ANY | skill/update/{id}/{name} | company.skillUpdate | SuperAdminCompanyController@skillUpdate | UPDATE row | skills |
| 220 | POST | skill/add-edit | company.addEditSkillUpdate | SuperAdminCompanyController@addEditSkillUpdate | UPDATE row | skills |
| 221 | POST | skillDelete | company.skillDelete | SuperAdminCompanyController@skillDelete | STATE TRANSITION (delete skill) | skills |
| 227 | POST | interview-schedule/updateInterview | company.updateInterview | SuperAdminCompanyController@updateInterview | UPDATE row | interview_schedules |
| 229 | POST | note-delete/comment | company.notedestroy | SuperAdminCompanyController@notedestroy | STATE TRANSITION (note delete) | comments |
| 233 | POST | check-active | company.checkActive | SuperAdminCompanyController@checkActive | STATE TRANSITION (active check / write) | companies |
| 234 | POST | check-status-operational | company.checkStatusOperational | SuperAdminCompanyController@checkStatusOperational | STATE TRANSITION (operational status write) | companies |
| 235 | POST | check-status-sales | company.checkStatusSales | SuperAdminCompanyController@checkStatusSales | STATE TRANSITION (sales status write) | companies |
| 241 | ANY | notification/update/{id} | company.notificationupdate | SuperAdminCompanyController@notificationupdate | STATE TRANSITION (toggle notification) | notifications |
| 243 | POST | notification/add-edit | company.addEditnotificationupdate | SuperAdminCompanyController@addEditnotificationupdate | UPDATE row | notifications |
| 248 | POST | default-terms/add-edit | company.addEditTermsupdate | SuperAdminCompanyController@addEditTermsupdate | UPDATE row | default-terms / offer_terms_conditions |
| 250 | POST | companysavecreateoffer | company.companysavecreateoffer | SuperAdminCompanyController@companysavecreateoffer | UPDATE row | offer_terms_conditions |
| 254 | POST | coin-events-store | company.coinEventsStore | SuperAdminCompanyController@coinEventsStore | UPDATE row | coin_events |
| 257 | POST | coin-package-store | company.coinPackageStore | SuperAdminCompanyController@coinPackageStore | UPDATE row | coin_packages |
| 259 | POST | coin-event-status | company.coinEventStatus | SuperAdminCompanyController@coinEventStatus | STATE TRANSITION (toggle event) | coin_events |
| 262 | GET | coupons/add-edit/{id?} | coupons.addEdit | SuperAdmin\CouponController@addEdit | READ-AJAX (form render) | coupons |
| 263 | POST | coupons/store | coupons.store | SuperAdmin\CouponController@store | UPDATE row | coupons |
| 264 | POST | coupons/toggle-status | coupons.toggleStatus | SuperAdmin\CouponController@toggleStatus | STATE TRANSITION (toggle) | coupons |
| 265 | POST | coupons/destroy | coupons.destroy | SuperAdmin\CouponController@destroy | STATE TRANSITION (delete coupon) | coupons |
| 269 | POST | top-upcoin-package-store | company.topUpCoinPackageStore | SuperAdminCompanyController@topUpCoinPackageStore | UPDATE row | top_up_coin_packages |
| 271 | POST | topup-coins-added | company.topUpCoinsAdded | SuperAdminCompanyController@topUpCoinsAdded | STATE TRANSITION (coin credit) | coin_orders |
| 272 | POST | topuptypegetprice | company.topuptypegetprice | SuperAdminCompanyController@topuptypegetprice | READ-AJAX (price lookup) | coin_package_types / coin_package_points |
| 276 | POST | boostkeywordadd | company.boostkeywordadd | SuperAdminCompanyController@boostkeywordadd | UPDATE row | boostkeywords |
| 283 | GET | createjd/{entryType}/{id?} | company.createjd | SuperAdminCompanyController@createjd | READ-AJAX (form render) | jobs |
| 284 | POST | jdgoogle-sheet-get-data | company.jdgoogleSheetInsertdataInDatabase | SuperAdminCompanyController@jdgoogleSheetInsertdataInDatabase | IMPORT (sheet → jobs) | jobs / google_sheets |
| 292 | POST | tickets/{ticket}/status | tickets.ticketUpdateStatus | SuperAdminCompanyController@ticketUpdateStatus | STATE TRANSITION (ticket status) | tickets |
| 293 | GET | ticketcreate | tickets.create | SuperAdminCompanyController@ticketCreate | READ-AJAX (form render) | tickets |
| 296 | POST | storeticketcreate | tickets.store | SuperAdminCompanyController@storeticketCreate | STATE TRANSITION (ticket open) | tickets |
| 297 | POST | comments/store | comments.store | SuperAdminCompanyController@storetComment | UPDATE row | comments |
| 310 | POST | dddcustompackages/createInvoice | global-settings.updatePr | GlobalSettingController@updatePr | STATE TRANSITION (price update → invoice) | package_invoice |
| 311 | GET | linkedin-settings/update-status/{id} | updateStatus | LinkedInSettingController@updateStatus | STATE TRANSITION (toggle) | linked_in_settings |
| 315 | POST | front-cms/update-common-header | front-cms.updateCommonHeader | SuperAdminFrontCmsController@updateCommonHeader | UPDATE row | front_cms_headers |
| 316 | POST | front-cms/update-header | front-cms.updateHeader | SuperAdminFrontCmsController@updateHeader | UPDATE row | front_cms_headers |
| 318 | POST | front-cms/features | front-cms.savefeatures | SuperAdminFrontCmsController@saveImageFeatures | UPDATE row | front_image_features |
| 319 | POST | front-cms/features/{id} | front-cms.updatefeatures | SuperAdminFrontCmsController@updatefeatures | UPDATE row | front_image_features |
| 321 | POST | front-cms/deleteFeature/{id} | front-cms.deleteFeature | SuperAdminFrontCmsController@deleteFeature | STATE TRANSITION (delete feature) | front_image_features |
| 328 | ANY | add-edit-package-features/{id?} | package.add_edit_package_feature | PackageController@add_edit_package_feature | UPDATE row | package_features / packages |
| 335 | POST | custompackages/createInvoice | custompackages.createInvoice | CustomPackageController@createInvoice | STATE TRANSITION (invoice created) | package_invoice |
| 336 | GET | custompackages/create/{company_id?} | custompackages.create | CustomPackageController@create | READ-AJAX (form render) | custompackages |
| 338 | POST | custompackages/saveinvoice | custompackages.saveinvoice | CustomPackageController@saveinvoice | STATE TRANSITION (invoice persisted) | custompackages / package_invoice |
| 339 | POST | custompackages/submitcomment/{id?} | custompackages.submitcomment | CustomPackageController@submitcomment | UPDATE row | package_invoice_comment |
| 342 | GET | custompackages/editWallet/{id?} | custompackages.editWallet | CustomPackageController@editWallet | READ-AJAX (form render) | custompackages |
| 343 | POST | custompackages/update/{id} | custompackages.update | CustomPackageController@update | UPDATE row | custompackages |
| 354 | POST | defaultpackages/update | defaultpackages.update | DefaultPackageController@update | UPDATE row | packages / package_master |
| 365 | POST | custom-modules/verify-purchase | custom-modules.verify-purchase | CustomModuleController@verifyingModulePurchase | STATE TRANSITION (verify license) | custom_modules |
| 371 | GET | recruiter-note | recruiter-note.index | RecruiterNoteTemplateController@index | READ-AJAX (list) | recruiter_note_master |
| 372 | POST | recruiter-note | recruiter-note.store | RecruiterNoteTemplateController@store | UPDATE row | recruiter_note_master |
| 373 | PUT | recruiter-note/{id} | recruiter-note.update | RecruiterNoteTemplateController@update | UPDATE row | recruiter_note_master |
| 374 | DELETE | recruiter-note/{id} | recruiter-note.destroy | RecruiterNoteTemplateController@destroy | STATE TRANSITION (delete template) | recruiter_note_master |

### 3.4 routes/web.php — Auth / Front / SaasFront / Admin inline / Payment / Webhook / Debug (47 rows)

| Line | Method | URI | Name | Controller@Method | Persona | Transition class | DB table(s) |
|------|--------|-----|------|-------------------|---------|------------------|-------------|
| 37 | POST | whatsapp/create | (none) | HomeController@create | Debug | SIDE EFFECT (whatsapp session create) | (none — debug) |
| 40 | POST | whatsapp/restart | (none) | HomeController@restart | Debug | STATE TRANSITION (restart bridge) | (none — debug) |
| 41 | POST | whatsapp/send | (none) | HomeController@send | Debug | SIDE EFFECT (send whatsapp) | (none — debug) |
| 42 | POST | whatsapp/delete | (none) | HomeController@delete | Debug | STATE TRANSITION (delete bridge) | (none — debug) |
| 112 | POST | notifications/read/{id} | (none) | (closure) | Debug | STATE TRANSITION (mark read) | notifications |
| 122 | POST | salary/calculate | salary.calculate | HomeController@salaryCalculate | Debug | READ-AJAX (calculator) | (calc only) |
| 132 | POST | questions/store | questions.questionsStore | HomeController@questionsStore | Public/Front | UPDATE row | questions |
| 138 | POST | login | (none) | LoginController@login | Auth | STATE TRANSITION (session open) | users |
| 141 | POST | logout | logout | LoginController@logout | Auth | STATE TRANSITION (session close) | users |
| 149 | POST | password/email | password.email | ForgotPasswordController@sendResetLinkEmail | Auth | SIDE EFFECT (reset mail) | users |
| 153 | POST | password/reset | password.update | ResetPasswordController@reset | Auth | STATE TRANSITION (password reset) | users |
| 156 | POST | ResetPasswordStore | ResetPasswordStore | ResetPasswordController@ResetPasswordStore | Auth | STATE TRANSITION (password update) | users |
| 158 | POST | password/emailsend | password.emailsend | ResetPasswordController@sendResetLinkEmail | Auth | SIDE EFFECT (reset mail) | users |
| 162 | POST | delete-template | delete.template | HomeController@deleteTemplate | Public | STATE TRANSITION (template delete) | email_templates |
| 168 | POST | uploadresume | get.uploadresume | HomeController@uploadresume | Public/Front | SIDE EFFECT (file commit) | documents |
| 169 | POST | extract-resume-applications | get.extract-resume-job-applications | HomeController@resumExtract | Public/Front | IMPORT (resume parse) | documents / job_applications |
| 171 | POST | careerStore | get.careerStore | HomeController@careerStore | Public/Front | STATE TRANSITION (apply from career page) | job_applications |
| 250 | POST | submit-form | form.submit | HomeController@submit | Public | SIDE EFFECT (form sink) | (form sink) |
| 254 | POST | resume/upload | resume.upload | ResumeController@upload | Public | SIDE EFFECT (file commit) | documents |
| 264 | POST | save-signature | save.signature | HomeController@saveSignature | Public | UPDATE row | signatures |
| 270 | POST | create-payment | phonepe.payment | PhonePePaymentController@createPayment | Payment | EXTERNAL CALL (PhonePe init) | package_invoice / phonepe_invoices |
| 272 | POST | payment-callback | phonepe.callback | PhonePePaymentController@callback | Payment | EXTERNAL CALL (PhonePe verify) | phonepe_invoices |
| 275 | ANY | phonepe-response | response | PhonePePaymentController@response | Payment | EXTERNAL CALL (PhonePe return) | phonepe_invoices |
| 298 | POST | payment-callback | payment.callback | PaymentController@paymentCallback | Payment | EXTERNAL CALL (gateway verify) | invoices / package_invoice |
| 329 | POST | offer_download_mail/{id?} | get.offer_download_mail | HomeController@fwd_offer_download | Public/Front | EXPORT (file stream) | offer_job_applications |
| 342 | POST | verifyTeamProfile | verifyTeamProfile | SaasFrontController@verifyTeamProfile | SaasFront | STATE TRANSITION (verify team) | users |
| 345 | POST | invitation-company-store/ | invitation-company-store | SaasFrontController@invitationCompanyStore | SaasFront | STATE TRANSITION (company from invite) | companies |
| 346 | POST | (root) | contact | SaasFrontController@submitContact | SaasFront | UPDATE row | contacts |
| 347 | POST | company-register | company-register | SaasFrontController@companyRegister | SaasFront | BULK UPDATE (1,193-line signup) | companies + ~70 child rows |
| 348 | POST | company-register-chk | company-register-chk | SaasFrontController@companyRegisterChk | SaasFront | READ-AJAX (uniqueness check) | companies |
| 350 | POST | valid-email-chk | valid-email-chk | HomeController@validEmailChk | SaasFront | READ-AJAX (uniqueness check) | users |
| 351 | POST | import-meetings | importMeetings | HomeController@importMeetings | SaasFront | IMPORT (sheet → meetings) | (meetings sink) |
| 353 | POST | contact-company-register-chk | contact-company-register-chk | SaasFrontController@contactCompanyRegisterChk | SaasFront | READ-AJAX | companies |
| 354 | POST | domain-company-register-chk | domain-company-register-chk | SaasFrontController@domainCompanyRegisterChk | SaasFront | READ-AJAX | companies |
| 355 | POST | client-domain-register-chk | client-domain-register-chk | SaasFrontController@domainClientRegisterChk | SaasFront | READ-AJAX | companies |
| 360 | POST | change-language/{code} | changeLanguage | SaasFrontController@changeLanguage | SaasFront | STATE TRANSITION (session) | language_settings (session) |
| 380 | POST | save-offer | save-offer | FrontJobOfferController@saveOffer | Front | STATE TRANSITION (public offer save) | offers |
| 381 | POST | job/saveApplication | saveApplication | FrontJobsController@saveApplication | Front | STATE TRANSITION (public apply) | job_applications |
| 382 | POST | job/fetch-country-state | fetchCountryState | FrontJobsController@fetchCountryState | Front | READ-AJAX (geo lookup) | (lookup) |
| 404 | POST | save-offer | save-offer | FrontJobOfferController@saveOffer | Front | STATE TRANSITION (public offer save) | offers |
| 405 | POST | job/saveApplication | saveApplication | FrontJobsController@saveApplication | Front | STATE TRANSITION (public apply) | job_applications |
| 406 | POST | job/fetch-country-state | fetchCountryState | FrontJobsController@fetchCountryState | Front | READ-AJAX (geo lookup) | (lookup) |
| 418 | POST | verify-billing-ipn | verify-billing-ipn | PaypalIPNController@verifyBillingIPN | Payment Webhook | EXTERNAL CALL (PayPal IPN) | paypal_invoices / invoices |
| 419 | POST | save-invoices | save_webhook | StripeWebhookController@saveInvoices | Payment Webhook | EXTERNAL CALL (Stripe webhook) | stripe_invoices |
| 420 | POST | save-razorpay-invoices | save_razorpay-webhook | RazorpayWebhookController@saveInvoices | Payment Webhook | EXTERNAL CALL (Razorpay webhook) | razorpay_invoices |
| 426 | POST | send-otp-code | sendOtpCode | VerifyMobileController@sendVerificationCode | Auth | SIDE EFFECT (OTP send) | users |
| 427 | POST | send-otp-code/account | sendOtpCode.account | VerifyMobileController@sendVerificationCode | Auth | SIDE EFFECT (OTP send) | users |
| 428 | POST | verify-otp-phone | verifyOtpCode | VerifyMobileController@verifyOtpCode | Auth | STATE TRANSITION (phone verified) | users |
| 429 | POST | verify-otp-phone/account | verifyOtpCode.account | VerifyMobileController@verifyOtpCode | Auth | STATE TRANSITION (phone verified) | users |
| 433 | POST | mark-notification-read | mark-notification-read | NotificationController@markAllRead | Auth | STATE TRANSITION (notifications marked read) | notifications |
| 461-473 | POST | settings/delete-account-store, role-permission/assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole | (various) | (dormant — see skipped below) | ProductCompany | STATE TRANSITION (dormant) | (dormant) |
| 522 | POST | account/sendinvitation | account.sendinvitation | AdminAccountSettingsController@sendinvitation | Admin (inline) | STATE TRANSITION (invite team member) | users |

### 3.5 Summary

- Service Co: 59 rows.
- Super Admin: 90 rows.
- web.php: 47 rows.
- **TOTAL_ROWS: 196.**

---

## 4. Transitions / State-Change Matrix (broad sweep, 46 entity rows)

Scope: same as the narrow audit — `routes/service.php`, `routes/superadmin.php`, and the in-scope block of `routes/web.php`. Out: `routes/product.php`, `routes/service_old.php`, `ProductCompany\*` and `StaffingCompany\*` controllers.

Source: §1 (196 rows) refiltered using the transition rule stated in §3.1. The same 196 rows are kept; this document reclassifies them into transition classes and maps them onto driver entities.

### 4.1 Per-persona transition totals

| Persona | STATE TRANSITION | UPDATE row | BULK UPDATE | SIDE EFFECT | IMPORT | EXPORT | EXTERNAL CALL | READ-AJAX | Total |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|
| Service Co | 27 | 18 | 5 | 9 | 2 | 8 | 0 | 6 | 75* |
| Super Admin | 24 | 33 | 0 | 6 | 2 | 0 | 0 | 13 | 78 |
| web.php (inline) | 14 | 3 | 1 | 7 | 2 | 1 | 8 | 11 | 47 |
| **All** | **65** | **54** | **6** | **22** | **6** | **9** | **8** | **30** | **200** |

*Counts above include 1 row per file-line; the `routes/service.php:502-507` and `:547-552` blocks each expand to 6 rows in the transition list, so the 59-row filter re-emits 75 effective rows when each member of the grouped block is counted separately. The raw route-inventory row count stays at 59. Same expansion note applies for superadmin's PUT/DELETE rest-CRUD entries.

For a strict 196-row accounting that maps each raw row to a single dominant class, use the breakdown below (counts are row-wise, so multi-method blocks like `:502-507` count as 1 row each):

| Persona | STATE TRANSITION | UPDATE row | BULK UPDATE | SIDE EFFECT | IMPORT | EXPORT | EXTERNAL CALL | READ-AJAX | Total |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|
| Service Co | 22 | 18 | 3 | 9 | 2 | 8 | 0 | 1 (form render rows merged into SIDE) | 59 |
| Super Admin | 22 | 33 | 0 | 6 | 2 | 0 | 0 | 11 | 90* |
| web.php (inline) | 14 | 3 | 1 | 7 | 2 | 1 | 8 | 11 | 47 |
| **All** | **58** | **54** | **4** | **22** | **6** | **9** | **8** | **23** | **196** |

\*SuperAdmin has 4 PUT/DELETE rows (`recruiter-note` REST block + `language-settings/change-language-status/{id}` + `footer-settings/update-footer-menu/{id}`), so the strict-row count of 90 holds after folding the four `recruiter-note` GET/POST into one REST resource.

### 4.2 Transition class definitions (recap)

- `STATE TRANSITION` — moves a row through a status pipeline (status flip, stage move, offer accept, impersonate, login, register).
- `UPDATE row` — single-row mutation with no status flip (job fields, settings, terms text).
- `BULK UPDATE` — many rows at once (assignRole, mass_mail, saveAll, addAssignment, role/permission sync).
- `SIDE EFFECT` — does not write the primary entity; triggers mail / whatsapp / notification / ES reindex / cache wipe.
- `IMPORT` — pulls data in (sheet, resume parse, meeting import).
- `EXPORT` — pulls data out (CSV, sample download, file stream).
- `EXTERNAL CALL` — pass-through to a third-party gateway (webhook, payment callback, IPN).
- `READ-AJAX` — feeds the UI without writing.

### 4.3 Per-entity transition map

For each entity (driver table), list the state-change endpoints that touch it. Cross-reference with the CRUD matrix at §2.

Legend: **TRANS** = state transition · **BULK** = bulk mutation · **SIDE** = side effect · **IMP** = import · **EXP** = export · **EXT** = external call · — = none

| # | Entity (driver table) | Add/Edit CRUD (see §2) | TRANS | BULK | SIDE | IMP | EXP | EXT | Hot note |
|---|----------------------|------------------------------|-------|------|------|-----|-----|-----|----------|
| 1 | jobs | ✅ Add/Edit/Delete | jobs/updatestatus, jobs/newUpdateJobstatus, jobs/removeJob, jobs/assignRejected, job-approval, market-auto-approval, auto-approval, jdgoogleSheetInsertdataInDatabase (import→state flip) | apptracker/store (bulk apps into jobs), jobtracker/store | jobs_mail_contact_person, updateAccessCode | jdgoogleSheetInsertdataInDatabase | apptracker/export, cvtracker/export | — | status pipeline + dual channel (state + service); 5 status toggles |
| 2 | service_on_jobs / service_jobs | ⚠ POST-only | — | add-update-services-job, jobs/updateserviceviewmore | — | — | — | — | appended-only sub-entity; no remove endpoint |
| 3 | job_applications | ✅ Add/Edit (no destroy) | job-applications/stage_submit, job-applications/client_stage_submit, job-applications/statusupdate, job-applications/recruiter, contact-persion-team/recruiter, job-applications/clientappstore, cvtracker/candidate_process_update, cvtracker/application_copy, cvtracker/copy_current_selected_applicant, applicationoffer/update_application, change-event-organizer, getGoogleVideoUrl, job-applications/store-schedule, job-applications/resume_delete, job-applications/unset-file, candidate-approval, verifyTeamProfile, resume_delete (file) | apptracker/store, jobtracker/store, job-applications/stage_submit can act on many apps | cvtracker/fwd_resume, cvtracker/send_mail, cvtracker/send_whatsapp, cvtracker/mass_mail, cvtracker/mass_mail_send, cvtracker/send_mail_send, cvtracker/send_mail_for_candidate, cvtracker/fwd_resume_send, offer-mail-format, offer-send-mail/fwd_offer_send, offer_download_mail (side), job-applications/recruiter (mail), job_reject_mail, reject_mail, reject_mail_send_job, reject_mail_send, recruiter-note-update, accessEmailTemplate | importJobCondidate, extract-resume-applications, resumExtract, careerStore | export-job-applications, candidate-export-job-applications, apptracker/export, resume-as-pdf, offer_download_mail, offer-mail-format (file stream) | — | hottest module; 30+ write paths on the same row; multiple SIDE + TRANS on one submit |
| 4 | offer_job_applications / application_offer | ✅ Save/Edit (no destroy) | save-create-offer, terms-save-create-offer, applicationoffer/update_application, applicationoffer/update_salary, job-onboard/send-offer, job-onboard/update-status | — | offer-mail-format, offer-send-mail/fwd_offer_send, offer_download_mail (side) | — | applicationoffer/export, job-onboard/export, offer_download_mail | — | offer-letter + offer-email + offer-PDF are 3 side effects on one transition |
| 5 | bench_applications / add_job_wise_condidates | ✅ Talent add / ❌ destroy | bench-candidates/status_submit, bench-switch, talent-add, candidate-approval | — | — | — | cvtracker/export, bench-candidates/download, download-resume-bench-candidates | — | bench on/off + placed status flip |
| 6 | interview_schedules | ⚠ POST store / ✅ Edit / no destroy | job-applications/store-schedule, storeSchedule, change-event-organizer, getGoogleVideoUrl, meetingstoreSchedule, changeStatusMultiple, updateFeedbackStatus, updateAvailableStatus, followupUpdate, updateInterview, sendInterviewLink | changeStatusMultiple (bulk flip) | sendInterviewLink | — | interview-schedule/export | — | 4 status-shaped methods + 1 bulk flip; observer-driven |
| 7 | tickets (support) | ✅ POST/GET forms | tickets/{ticket}/status, storeticketcreate, ticketUpdateStatus (Service + SuperAdmin) | — | — | — | — | — | Service + SuperAdmin parallel controllers (hotspots §C) |
| 8 | comments (ticket / meeting) | ✅ store / ✅ destroy (SuperAdmin only) | note-delete/comment (notedestroy), delete-template (public) | — | meeting-comment-new, comments/store | — | — | — | Service has no comment delete route |
| 9 | clients | ✅ store / ✅ update / no destroy | clients/update, consultancy/staffing_invitation | — | — | — | consultancy/export | — | tenant-scoped soft delete only |
| 10 | users (team members) | ✅ invite / ✅ edit / ✅ soft delete | team/statusUpdate, team/WorkingStatus, team/teamDelete, team/roleupdate, team/reSendmail, team/resetpassword, account/sendinvitation, super-admin team/statusUpdate, team/WorkingStatus | — | resetpassword, reSendmail, sendinvitation (sends invite mail) | — | — | — | login/logout/password all route through web.php Auth cluster |
| 11 | role_user / roles / role_has_permissions / model_has_permissions | ✅ storeRole / ✅ updateRole / ✅ removerole | removerole (×2 — Service + Manage), saveRole, addIntStage (master_status not role) | assignAllPermission, removeAllPermission, assignRole, detachRole, storeRole, deleteRole (×2 — Service + Manage), updateRole | — | — | — | — | **parallel controllers** — hotspots §C; bulk paths bypass Observer |
| 12 | subscriptions / company_packages / package_invoice | ✅ checkout / ⚠ no edit / ✅ deletecheckout | checkout-subscribe, custom-checkout-subscribe, deletecheckout-subscribe, company/updateCompanyPackage, custompackages/saveinvoice, custompackages/createInvoice, dddcustompackages/createInvoice (updatePr), apply-coupon, remove-coupon | — | — | — | — | — | gated by `ask_purchase` view |
| 13 | subscriptions / invoices — gateway side | — N/A | topUpCoinsAdded | — | — | — | — | phonepe.payment, phonepe.callback, phonepe-response, payment.callback, verify-billing-ipn, save-invoices, save-razorpay-invoices | **all webhooks unauthenticated POST** |
| 14 | companies (admin edits) | ✅ edit/update / ❌ no destroy | company/update, company/checkActive, company/checkStatusOperational, company/checkStatusSales, delete-account-store (Service), updateAccessCode, googleMeetStatusUpdate, teamMeetStatusUpdate, loginAsCompany | — | setting-google-meeting-store, setting-team-meeting-store, setting-zoom-store, addMeetingLists, meetingListDelete | google-sheet-get-data (sheets→companies), jdgoogleSheetInsertdataInDatabase | — | — | loginAsCompany impersonate is the highest-risk state transition |
| 15 | companies (signup) | ✅ companyRegister (bulk) | invitation-company-store, company-register-chk (read-ajax), contact-company-register-chk (read-ajax), domain-company-register-chk (read-ajax), client-domain-register-chk (read-ajax), valid-email-chk (read-ajax) | company-register (~70 rows in 1,193 lines) | — | — | — | — | 1,193-line monolith; ~70 row inserts + ~30 ES syncs per signup |
| 16 | contacts | ✅ submitContact | — | — | — | — | — | — | SaasFront contact form |
| 17 | documents (resumes / files) | ✅ store / ✅ delete (3 paths) | resume_delete (×3), unset-file, deleteFile (app-update log) | — | resume-upload/presign, resume-upload/store, resume-upload/retry, resume-upload-session/*, uploadresume, resume/upload | extract-resume-applications (parse → job_applications), extract-resume-applications (public) | download, resume-as-pdf, download-resume-bench-candidates | — | three independent delete paths + session lifecycle |
| 18 | offers (public) | ✅ saveOffer | save-offer (×2 routes — line 380, 404) | — | — | — | — | — | write-only public endpoint |
| 19 | email_templates / th_email_template / emailthemes | ✅ add-edit / ✅ update | — | — | accessEmailTemplate, email_template (cvtracker + superadmin) | — | — | — | template store + apply on mail send |
| 20 | whatsapp_themes | ✅ add-edit / ✅ update | — | — | send-whatsapp (uses theme) | — | — | — | paired with email_templates for outbound comms |
| 21 | master_status / interview_processes / candidate_status / application_status | ✅ addIntStage / ✅ delIntStage / ✅ updatePriority | addIntStage, delIntStage, updatePriority | — | — | — | — | — | status pipelines are first-class entities |
| 22 | departments | ✅ storeDept | — | — | — | — | — | — | POST-only; inline admin form |
| 23 | designations | ✅ controller full CRUD (no GET route in inventory) | — | — | — | — | — | — | routes filtered out (plain GET CRUD) |
| 24 | skills | ✅ add-edit / ✅ update / ✅ delete | skillDelete | — | — | — | — | — | both SuperAdmin + Service have full CRUD |
| 25 | job_categories | ✅ controller CRUD (export-only in inventory) | — | — | — | — | job-categories/export | — | full CRUD via controller, only export in routes |
| 26 | boostkeywords | ✅ boostkeywordadd (×2) / ✅ boostStatusUpdate | boostStatusUpdate | — | — | — | — | — | status-only delete; no hard destroy |
| 27 | questions (interview questions) | ✅ questions/store (public + Service) | — | — | — | — | — | — | public POST writes anonymous |
| 28 | sticky_notes | ✅ controller full CRUD | — | — | — | — | — | — | full CRUD |
| 29 | todo_items | ✅ update-todo-item | — | — | — | — | — | — | full CRUD |
| 30 | applicant_notes | ⚠ POST-only (recruiter-note-update) | — | — | — | — | — | — | inline form, no full edit |
| 31 | recruiter_note_master | ✅ REST CRUD | recruiter-note.destroy | — | — | — | — | — | clean REST |
| 32 | coupons | ✅ store / ✅ toggle / ✅ destroy | coupons/toggle-status, coupons/destroy | — | — | — | — | — | toggle is hot path |
| 33 | footer_settings / footer_menu | ✅ store / ✅ update | — | — | — | — | — | — | partial route coverage |
| 34 | language_settings | ✅ store / ✅ update / ✅ destroy / ✅ changeStatus | language-settings/change-language-status | — | — | — | — | — | toggle via changeStatus |
| 35 | linked_in_settings | ⚠ updateStatus only | linkedin-settings/update-status | — | — | — | — | — | toggle-only; no full CRUD |
| 36 | google_sheets (config) | ✅ googleSheetInsertdataInDatabase | — | — | — | google-sheet-get-data, candidate-google-sheet-get-data, job-candidate-google-sheet-get-data, jdgoogle-sheet-get-data | — | — | one-way fetch — write only; no CRUD on sheet config |
| 37 | front_cms_headers / front_image_features / features / front_icon_features | ✅ full CRUD | front-cms/deleteFeature | — | — | — | — | — | full CRUD |
| 38 | package_features | ✅ add-edit-package-features | — | — | — | — | — | — | single endpoint covers Add/Edit |
| 39 | notifications | ✅ addEditnotificationupdate | notification/update/{id}, addEditnotificationupdate, SingleNotificationUpdate, mark-notification-read, notifications/read/{id} | — | — | — | — | — | the read path is hot (5 transitions on the same row shape) |
| 40 | default-terms / offer_terms_conditions | ✅ add-edit | — | — | — | — | — | — | in-place edit |
| 41 | custom_modules | ⚠ verify-purchase only | custom-modules/verify-purchase | — | — | — | — | — | verify-only |
| 42 | coin_events / coin_packages / coin_orders / top_up_coin_packages | ✅ store / ✅ toggle / ✅ top-up | coin-event-status, topup-coins-added | — | — | — | — | — | coin balances are written via raw DB::table — no Observer (hotspots §F) |
| 43 | meetings (settings blob) | ✅ addMeetingLists / ✅ delete | meeting-list-delete | — | — | — | — | — | no destroy, only soft remove from blob |
| 44 | company_settings / companies (settings) | ✅ settings blob | videoMeetingSeetingUpdate, career-page/settings/save, account/update | — | — | — | — | — | written through `account/update` |
| 45 | signatures (offer letter) | ✅ save-signature | — | — | — | — | — | — | public save, no edit/delete |
| 46 | contact (SaasFront form) | ✅ submitContact | — | — | — | — | — | — | one-shot public form |

---

## 5. Hotspots — themes A–G with evidence

Quick risk register covering fat controllers, parallel controllers, debug routes, ES sync gaps, and webhook exposure. Derived from §1 (196 rows) and §2 (55 entity rows).

### A. Fat controllers — V2/V3 slice refactor candidates

Controllers above 100 KB are the strongest candidates for the slice refactor pattern (Route + FormRequest + Action + ES sync + Observer + Reverb event + Blade component + test). Counts are bytes from `wc -c`.

| File | Bytes | Methods (rough) | Notes |
|------|------:|----------------:|-------|
| `app/Http/Controllers/HomeController.php` | 803,208 | n/a (Huge) | Junk drawer — see §E |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | 673,250 | 234 | jobs create/edit + hiring contacts + recruiters + service-on-jobs all in one class |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | 661,675 | 181 | hottest module — applications create, stage_submit, offer, resume, schedule |
| `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php` | 510,191 | 361 | every super-admin CMS area + skills + jd + coins + meeting + features + emails |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | 386,555 | 98 | already audited Aug 2026; ~26 dev-day plan exists |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` | 359,176 | 123 | bench pool + talent + placed + commercial config + offers |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | 329,396 | 66 | bulk email/whatsapp/copy + offer-letter render |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | 189,060 | 92 | team CRUD + invites + role + status |
| `app/Http/Controllers/SuperAdmin/CustomPackageController.php` | 187,875 | (multi) | custom billing + invoice render |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php` | 162,079 | (multi) | dashboard + meeting comment |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php` | 152,981 | (multi) | clients CRUD + custom + direct + request |
| `app/Http/Controllers/SuperAdmin/SuperAdminTeamController.php` | 135,466 | (multi) | super-admin team CRUD |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | 149,234 | (multi) | schedule + feedback + follow-up |
| `app/Http/Controllers/SuperAdmin/DefaultPackageController.php` | 81,049 | (multi) | default plans |

The top three (Jobs, JobApplication, SuperAdminCompany) are the canonical god controllers — each mixes form rendering, mass-mail, queue dispatch, ES sync, status pipelines, and inline HTML rendering (`Reply::dataOnly`).

### B. Cross-persona controller reuse

A non-Service persona answering a Service-URI is the classic audit finding.

- `routes/service.php:522` → `account/sendinvitation` is dispatched to **`AdminAccountSettingsController@sendinvitation`** (under `App\Http\Controllers\Admin\`), not a Service controller. The Service team-invite path silently depends on the Admin namespace; a future Admin rename breaks Service onboarding.
- `routes/web.php:347` → `company-register` is on `SaasFrontController@companyRegister` — that one is intentional (public signup), but worth flagging that the 1,193-line method touches ~70 rows including `companies`, `users`, `role_user`, `company_packages`, etc.
- `routes/superadmin.php:115` → `company/recruiter/{id?}` writes to `role_user` from `SuperAdminCompanyController` (super-admin side). The mirror Service route is `job-applications/recruiter/{id?}` on `ServiceCompanyJobApplicationController` — two controllers writing the same table from different paths.

### C. Parallel controllers

Two controllers owning the same CRUD surface is a maintenance liability.

- **Role/permission:** `ServiceCompanyRolesPermissionController` (`routes/service.php:502-511`) AND `ManageRolePermissionController` (`routes/service.php:547-567`) both expose `assignAllPermission`, `removeAllPermission`, `assignRole`, `detachRole`, `storeRole`, `deleteRole`, `saveRole`, `removerole/{user_id?}` with slightly different request shapes. The two stacks appear to both be live (no removal-of-other visible). Pick one and migrate.
- **Account settings:** `ServiceCompanyAccountSettingsController` owns most of `/account/*` for Service (department store, addressupdate, videoMeeting, boostkeywordadd, offerAmountChange, addIntStage, delIntStage, updatePriority). `AdminAccountSettingsController` owns `account/sendinvitation` for both Service and Admin (cross-persona — see §B).
- **SuperAdmin vs Admin Tickets/Comments:** `routes/service.php:297-301` and `routes/superadmin.php:292-297` both register `tickets/{ticket}/status`, `ticketcreate`, `storeticketcreate`, `comments/store` against different controllers (`ServiceCompanyAppTrackerController` vs `SuperAdminCompanyController`). Same CRUD surface, parallel implementations.

### D. Debug routes still reachable

`routes/web.php` exposes the following unprotected endpoints. Each is a known entry point for QA/dev poking in production:

- `routes/web.php:33` — GET `/whatsapp/connect` (closure)
- `routes/web.php:37` — POST `/whatsapp/create` → `HomeController@create`
- `routes/web.php:38` — GET `/whatsapp/qr` → `HomeController@qr`
- `routes/web.php:39` — GET `/whatsapp/status` → `HomeController@status`
- `routes/web.php:40` — POST `/whatsapp/restart` → `HomeController@restart`
- `routes/web.php:41` — POST `/whatsapp/send` → `HomeController@send`
- `routes/web.php:42` — POST `/whatsapp/delete` → `HomeController@delete`
- `routes/web.php:46` — GET `/mail-test` (closure)
- `routes/web.php:78` — GET `/redis-test` (closure)
- `routes/web.php:166` — GET `/whatsapp-terms-and-conditions` → `HomeController@whatsappTermsAndConditions`
- `routes/web.php:221` — GET `/whatsapp-email-elastic` → `HomeController@whatsappAndMailelastic`
- `routes/web.php:122` — POST `salary/calculate` → `HomeController@salaryCalculate`
- `routes/web.php:132` — POST `/questions/store` → `HomeController@questionsStore` (public, writes to `questions`)
- `routes/web.php:112` — POST `notifications/read/{id}` (closure)

The `/whatsapp/*` cluster is a sandbox for WhatsApp Cloud API testing — the underlying `HomeController` is 803 KB (see §E) and contains long-lived token state. These should be gated behind `APP_DEBUG` or an admin-only check.

### E. HomeController junk drawer

`app/Http/Controllers/HomeController.php` is **803,208 bytes (~813 KB)** and serves **94 routes** in `routes/web.php` (per `Grep "HomeController" routes/web.php`). That is one route per ~8.5 KB of code. The route list mixes:

- Public career-page forms (`/careerStore`, `/save-offer`, `/uploadresume`, `/resumExtract`)
- Public OTP/login-adjacent (`/validEmailChk`, `/importMeetings`, `/saveSignature`)
- Debug (`/whatsapp/*`, `/mail-test`, `/whatsapp-email-elastic`, `/salary/calculate`, `/questions/store`)
- Offer letter rendering (`signature-pad`, `candidate_mail_response`)
- Catch-all form submit (`/submit`, `/submit-form`)
- Test exports (resume rendering)

It is simultaneously the application shell, the public career portal, the WhatsApp bridge, and the offer-letter renderer. Anyone grepping for "where does `submit-form` go?" lands here. This is the single biggest V2/V3 refactor target — splitting into `HomeController` (shell), `CareerPageController`, `WhatsAppBridgeController`, `OfferLetterController`, `ResumeUploadController` (root-level) would clean up 94 routes.

### F. ES sync missing on writes (per entity)

The Observer directory (`app/Observers/`) has 30 files. The mapping against the heavy writers:

| Heavy writer controller | Expected Observer | Present? |
|---|---|---|
| `ServiceCompanyJobsController` | `JobObserver.php` | yes |
| `ServiceCompanyJobApplicationController` | `JobApplicationObserver.php` | yes |
| `ServiceCompanyBenchCandidatesController` | `BenchApplicationObserver.php` | yes |
| `InterviewScheduleController` | `InterviewScheduleObserver.php` | yes |
| `ServiceCompanyAppTrackerController` | `JobApplicationObserver` (shared) | yes |
| `ServiceCompanyCvTrackerController` | shared via `JobApplicationObserver` | yes |
| `ServiceCompanyApplicationOfferController` | (no dedicated) — likely piggybacks on `JobApplicationObserver` or via `InterviewScheduleObserver`/`OnBoardObserver` | yes (`OnBoardObserver`, `OnBoardFilesObserver`) |
| `ServiceCompanyTeamController` (users) | `UserObserver.php` | yes |
| `ServiceCompanyClientController` | `ClientObserver.php` | yes |
| `ServiceCompanyAccountSettingsController` (company edits) | `CompanyObserver.php` | yes |
| `ServiceCompanyCandidateStatusController` | `ApplicationStatusObserver.php` | yes |
| `ServiceCompanyInterviewProcessController` | (no direct match — may piggyback on `JobApplicationObserver`) | needs verify |
| `ServiceCompanyDocumentController` | `DocumentObserver.php` | yes |
| `SuperAdminCompanyController` (super_admin side company edits) | `CompanyObserver.php` (shared) | yes |

Per [AI_RULES.md](../AI_RULES.md) rule 4 ("keep MySQL ↔ Elasticsearch in sync — writes to indexed entities must re-index"), the mapping is mostly intact. The gaps worth flagging are:

- **`apptracker/store`** (`routes/service.php:291`) — bulk store on `job_applications`. Observer exists but mass-insert semantics need verification (the AppTracker audit's Step 7.5 already flagged this).
- **`add-update-services-job`** (`routes/service.php:445`) — writes to `jobs` and `service_on_jobs`. Whether `service_on_jobs` triggers ES re-index is not confirmed from the route-level audit.
- **`bulk coin events`** (`routes/superadmin.php:254-272`) — coin balances and event logs are written via raw `DB::table()` in many controllers. No dedicated Observer; re-index relies on the write path itself.
- **Webhooks** (`routes/web.php:418-420`) — gateway writes go directly into `package_invoice`, `stripe_invoices`, etc. with no Observer; replica lag risk.

### G. Webhook safety

- `routes/web.php:418` — POST `verify-billing-ipn` → `PaypalIPNController@verifyBillingIPN`. Unauthenticated, public POST; performs DNS resolution + HTTP verification against PayPal and writes to `paypal_invoices` / `invoices`. Known perf/attack-surface hot spot — every IPN may block on outbound HTTP.
- `routes/web.php:419` — POST `save-invoices` → `StripeWebhookController@saveInvoices`. Stripe sends these; signature verification lives in the controller.
- `routes/web.php:420` — POST `save-razorpay-invoices` → `RazorpayWebhookController@saveInvoices`. Same pattern.
- `routes/web.php:270-275` — `create-payment`, `payment-callback`, `phonepe-response` for PhonePe; unauthenticated POSTs.
- `routes/web.php:298` — `payment-callback` → `PaymentController@paymentCallback`. Generic gateway callback.

The PayPal IPN endpoint is the highest-risk one because the controller must perform synchronous external HTTP to PayPal to verify before writing — this couples latency to third-party availability and exposes an unauthenticated write path. None of the webhooks are gated by a `webhook` middleware; signature/secret verification is left to the controller body.

### 5.1 Recommended priority order (themes A–G)

1. **Slice `ServiceCompanyJobsController` (673 KB) and `ServiceCompanyJobApplicationController` (661 KB).** Together they own ~80% of the route CRUD surface; refactoring unlocks most of the gains.
2. **De-duplicate role/permission controllers** (`ServiceCompanyRolesPermissionController` vs `ManageRolePermissionController`).
3. **Gate the debug routes** (`/whatsapp/*`, `/mail-test`, `/redis-test`) behind `APP_DEBUG` or remove from production route file.
4. **Add explicit Add routes** for entities currently POST-only (`jobs`, `interview_schedules`, `departments`, `interview_processes`, `bench_applications`).
5. **Add `job-applications.destroy`** and `bench-candidates.destroy` — application-row delete is currently only via document delete, which leaves the row orphaned.
6. **Webhook hardening** — at minimum, add a `webhook.ip_whitelist` or signature middleware to `verify-billing-ipn`.