<?php

use App\Http\Controllers\SuperAdmin\BenchRmNoteTemplateController;
use App\Http\Controllers\SuperAdmin\ClientFeedbackController;
use App\Http\Controllers\SuperAdmin\CustomModuleController;
use App\Http\Controllers\SuperAdmin\CustomPackageController;
use App\Http\Controllers\SuperAdmin\DefaultPackageController;
use App\Http\Controllers\SuperAdmin\FrontIconFeatureController;
use App\Http\Controllers\SuperAdmin\GlobalSettingController;
use App\Http\Controllers\SuperAdmin\InvoiceController;
use App\Http\Controllers\SuperAdmin\LanguageSettingsController;
use App\Http\Controllers\SuperAdmin\LinkedInSettingController;
use App\Http\Controllers\SuperAdmin\PackageController;
use App\Http\Controllers\SuperAdmin\RecruiterNoteTemplateController;
use App\Http\Controllers\SuperAdmin\SuperAdminCompanyController;
use App\Http\Controllers\SuperAdmin\SuperadminController;
use App\Http\Controllers\SuperAdmin\SuperAdminCurrencyController;
use App\Http\Controllers\SuperAdmin\SuperAdminCustomPlanRequestController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminFooterSettingsController;
use App\Http\Controllers\SuperAdmin\SuperAdminFrontCmsController;
use App\Http\Controllers\SuperAdmin\SuperAdminMarketplaceController;
use App\Http\Controllers\SuperAdmin\SuperAdminPaymentSettingsController;
use App\Http\Controllers\SuperAdmin\SuperAdminProfileController;
use App\Http\Controllers\SuperAdmin\SuperAdminSmsSettingsController;
use App\Http\Controllers\SuperAdmin\SuperAdminSmtpSettingController;
use App\Http\Controllers\SuperAdmin\SuperAdminTeamController;
use App\Http\Controllers\SuperAdmin\SuperAdminThemeSettingsController;
use App\Http\Controllers\SuperAdmin\UpdateApplicationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    // ########################################################################### SUPER ADMIN ################################################################################

    Route::prefix('super-admin')->name('superadmin.')->middleware(['super-admin'])->group(function () {
        Route::resource('profile', SuperAdminProfileController::class);
        Route::resource('dashboard', SuperAdminDashboardController::class);
        Route::get('dashboard/api/chart/{chartKey}', [SuperAdminDashboardController::class, 'chartApi'])->name('dashboard.chartApi');
        // Custom Plan Requests Management
        Route::get('custom-plan-requests', [SuperAdminCustomPlanRequestController::class, 'index'])->name('custom-plan-requests.index');
        Route::get('custom-plan-requests/data', [SuperAdminCustomPlanRequestController::class, 'data'])->name('custom-plan-requests.data');
        Route::get('custom-plan-requests/sync', [SuperAdminCustomPlanRequestController::class, 'sync'])->name('custom-plan-requests.sync');
        Route::get('custom-plan-requests/{id}', [SuperAdminCustomPlanRequestController::class, 'show'])->name('custom-plan-requests.show');
        Route::match(['post', 'put'], 'custom-plan-requests/{id}/status', [SuperAdminCustomPlanRequestController::class, 'updateStatus'])->name('custom-plan-requests.updateStatus');
        Route::get('smtp-settings/sent-test-email', [SuperAdminSmtpSettingController::class, 'sendTestEmail'])
            ->name('smtp-settings.sendTestEmail');
        Route::resource('smtp-settings', SuperAdminSmtpSettingController::class);
        // language settings
        Route::get('language-settings/change-language', [LanguageSettingsController::class, 'changeLanguage'])->name('language-settings.change-language');
        Route::put('language-settings/change-language-status/{id}', [LanguageSettingsController::class, 'changeStatus'])->name('language-settings.changeStatus');
        Route::resource('language-settings', LanguageSettingsController::class);
        Route::resource('theme-settings', SuperAdminThemeSettingsController::class);
        Route::put('footer-settings/update-footer-menu/{id}', [SuperAdminFooterSettingsController::class, 'updateFooterMenu'])->name('footer-settings.updateFooterMenu');
        Route::post('footer-settings/store-footer-menu', [SuperAdminFooterSettingsController::class, 'storeFooterMenu'])->name('footer-settings.storeFooterMenu');
        Route::get('footer-settings/data', [SuperAdminFooterSettingsController::class, 'data'])->name('footer-settings.data');
        Route::resource('footer-settings', SuperAdminFooterSettingsController::class);
        Route::post('update-application/deleteFile', [UpdateApplicationController::class, 'deleteFile'])->name('update-application.deleteFile');
        Route::get('update-application/update', [UpdateApplicationController::class, 'update'])->name('update-application.updateApp');
        Route::get('update-application/download', [UpdateApplicationController::class, 'download'])->name('update-application.download');
        Route::get('update-application/downloadPercent', [UpdateApplicationController::class, 'downloadPercent'])->name('update-application.downloadPercent');
        Route::get('update-application/checkIfFileExtracted', [UpdateApplicationController::class, 'checkIfFileExtracted'])->name('update-application.checkIfFileExtracted');
        Route::get('update-application/install', [UpdateApplicationController::class, 'install'])->name('update-application.install');
        Route::resource('update-application', UpdateApplicationController::class);

        Route::post('company/updateCompanyPackage/{id}', [SuperAdminCompanyController::class, 'updateCompanyPackage'])->name('company.updateCompanyPackage');
        Route::get('company/changePackage/{id}', [SuperAdminCompanyController::class, 'changePackage'])->name('company.changePackage');
        Route::get('company/data', [SuperAdminCompanyController::class, 'data'])->name('company.data');

        Route::get('company-list', [SuperAdminCompanyController::class, 'data_ajax'])->name('company.data_ajax');

        Route::get('google-meeting-setting', [SuperAdminCompanyController::class, 'googleMeetingSetting'])->name('company.google_meeting_setting');
        Route::post('setting-google-meeting-store', [SuperAdminCompanyController::class, 'settingGoogleMeetingStore'])->name('company.settingGoogleMeetingStore');

        Route::post('google-meet-status-update', [SuperAdminCompanyController::class, 'googleMeetStatusUpdate'])->name('company.googleMeetStatusUpdate');

        Route::get('ajax-bench-candidates/{status?}', [SuperAdminCompanyController::class, 'ajaxbenchNewCandidates'])->name('company.ajax-new-bench-candidates');

        Route::get('team-meeting-setting', [SuperAdminCompanyController::class, 'teamMeetingSetting'])->name('company.team_meeting_setting');
        Route::post('setting-team-meeting-store', [SuperAdminCompanyController::class, 'settingteamMeetingStore'])->name('company.settingteamMeetingStore');
        Route::post('team-meet-status-update', [SuperAdminCompanyController::class, 'teamMeetStatusUpdate'])->name('company.teamMeetStatusUpdate');

        Route::post('add-meeting-list', [SuperAdminCompanyController::class, 'addMeetingLists'])->name('company.addMeetingLists');

        Route::post('meeting-list-delete', [SuperAdminCompanyController::class, 'meetingListDelete'])->name('company.meetingListDelete');

        Route::get('add-meeting/{tablename?}', [SuperAdminCompanyController::class, 'addMeetingList'])->name('company.addMeetingList');

        Route::get('company/myContactPerson', [SuperAdminCompanyController::class, 'myContactPerson'])->name('company.myContactPerson');

        Route::post('company/{id}/login', [SuperAdminCompanyController::class, 'loginAsCompany'])->name('company.loginAsCompany');
        Route::resource('company', SuperAdminCompanyController::class);

        Route::post('candidate-google-sheet-get-data', [SuperAdminCompanyController::class, 'googleSheetCandidateData'])->name('company.googleSheetCandidateData');
        Route::post('google-sheet-get-data', [SuperAdminCompanyController::class, 'googleSheetInsertdataInDatabase'])->name('company.googleSheetInsertdataInDatabase');
        Route::post('job-candidate-google-sheet-get-data', [SuperAdminCompanyController::class, 'googleSheetCandidateDataJob'])->name('company.googleSheetCandidateDataJob');
        Route::post('single-notification-update', [SuperAdminCompanyController::class, 'SingleNotificationUpdateS'])->name('company.SingleNotificationUpdate');

        // ///////////////////////////////////////////////////////////////////////////////
        Route::get('clients/viewmore/{id?}', [SuperAdminCompanyController::class, 'viewMore'])->name('company.viewmore');

        Route::post('billing/viewmore', [SuperAdminCompanyController::class, 'billinDetails'])->name('company.billinDetails');

        Route::get('clients/contactdata', [SuperAdminCompanyController::class, 'contactdata'])->name('company.contactdata');
        Route::get('jobs/all_data', [SuperAdminCompanyController::class, 'all_data'])->name('company.all_data');

        Route::get('company/edit/{id?}', [SuperAdminCompanyController::class, 'edit_company'])->name('company.edit_company');
        Route::post('company/update/{id?}', [SuperAdminCompanyController::class, 'update_company'])->name('company.update_company');
        Route::post('company/{id}/toggle-verify', [SuperAdminCompanyController::class, 'toggleAccountVerify'])->name('company.toggleAccountVerify');

        Route::get('addresslist/{cid?}', [SuperAdminCompanyController::class, 'addresslist'])->name('company.addresslist');
        Route::get('statelist/{cid?}', [SuperAdminCompanyController::class, 'statelist'])->name('company.statelist');
        Route::get('citylist/{state_id?}', [SuperAdminCompanyController::class, 'citylist'])->name('company.citylist');

        Route::get('meeting-schedule/{id?}', [SuperAdminCompanyController::class, 'meetingSchedule'])->name('company.meeting-schedule');
        Route::post('getmeetingchild', [SuperAdminCompanyController::class, 'getmeetingchild'])->name('company.getmeetingchild');

        Route::post('company/recruiter/{id?}', [SuperAdminCompanyController::class, 'recruiter'])->name('company.recruiter');

        Route::post('meetingstoreSchedule', [SuperAdminCompanyController::class, 'storeSchedule'])->name('company.store-schedule');

        Route::get('subscription/{condition?}', [SuperAdminCompanyController::class, 'subscription'])->name('company.subscription');

        Route::get('subscriptiondata', [SuperAdminCompanyController::class, 'subscriptiondata'])->name('company.subscriptiondata');

        Route::get('all-subscription-list', [SuperAdminCompanyController::class, 'allSubscriptionDataAjax'])->name('company.allSubscriptionDataAjax');

        Route::get('super-admin-access', [SuperAdminCompanyController::class, 'superAdminAccess'])->name('company.superAdminAccess');
        Route::get('access-company/data', [SuperAdminCompanyController::class, 'accessData'])->name('company.accessData');
        Route::post('updateAccessCode', [SuperAdminCompanyController::class, 'updateAccessCode'])->name('company.updateAccessCode');
        Route::get('countaccountviewmore/{id?}', [SuperAdminCompanyController::class, 'countaccountviewmore'])->name('company.countaccountviewmore');

        Route::resource('team', SuperAdminTeamController::class);
        Route::get('team/data', [SuperAdminTeamController::class, 'data'])->name('team.data');
        Route::get('team/viewmore/{id?}', [SuperAdminTeamController::class, 'viewMore'])->name('team.viewmore');
        Route::get('team/jobdata', [SuperAdminTeamController::class, 'jobdata'])->name('team.jobdata');
        Route::get('team/appdata', [SuperAdminTeamController::class, 'appdata'])->name('team.appdata');
        Route::get('team/joiningdata', [SuperAdminTeamController::class, 'joiningdata'])->name('team.joiningdata');
        Route::get('team/intdata', [SuperAdminTeamController::class, 'intdata'])->name('team.intdata');
        Route::post('team/statusUpdate', [SuperAdminTeamController::class, 'statusUpdate'])->name('team.statusUpdate');
        Route::get('team/editrole/{id?}', [SuperAdminTeamController::class, 'editrole'])->name('team.editrole');
        Route::post('team/WorkingStatus', [SuperAdminTeamController::class, 'WorkingStatus'])->name('team.WorkingStatus');

        Route::get('team/add_team_member_form/{id?}', [SuperAdminCompanyController::class, 'add_team_member_form'])->name('company.add_team_member_form');

        Route::get('jobs/editJobStatus/{id?}', [SuperAdminCompanyController::class, 'editJobStatus'])->name('jobs.editJobStatus');

        Route::get('meetingstoreScheduleList', [SuperAdminCompanyController::class, 'meetingScheduleList'])->name('company.meeting-schedule-list');

        Route::get('meeting-comment/{id}', [SuperAdminCompanyController::class, 'meetingComment'])->name('company.meeting-comment');
        Route::post('meeting-comment-new/{id?}', [SuperAdminCompanyController::class, 'meetingcommentnew'])->name('company.meeting-comment-new');

        Route::get('email-themes', [SuperAdminCompanyController::class, 'emailThemes'])->name('company.emailthemes');
        Route::any('email-themes/update/{id}', [SuperAdminCompanyController::class, 'emailThemesupdate'])->name('company.emailThemesupdate');
        Route::post('email-themes/add-edit', [SuperAdminCompanyController::class, 'addEditemailThemesupdate'])->name('company.addEditemailThemesupdate');
        Route::post('email-themes/toggle-status', [SuperAdminCompanyController::class, 'toggleEmailTemplateStatus'])->name('company.emailThemesToggleStatus');
        Route::post('email-themes/toggle-client-required', [SuperAdminCompanyController::class, 'toggleEmailTemplateClientRequired'])->name('company.emailThemesToggleClientRequired');
        Route::post('email-themes/bulk-status', [SuperAdminCompanyController::class, 'bulkUpdateEmailTemplateStatus'])->name('company.emailThemesBulkStatus');

        Route::get('whatsapp-themes', [SuperAdminCompanyController::class, 'whatsappThemes'])->name('company.whatsappthemes');
        Route::any('whatsapp-themes/update/{id}', [SuperAdminCompanyController::class, 'whatsappThemesupdate'])->name('company.whatsappThemesupdate');
        Route::post('whatsapp-themes/add-edit', [SuperAdminCompanyController::class, 'addEditwhatsappThemesupdate'])->name('company.addEditwhatsappThemesupdate');

        Route::get('features-list', [SuperAdminCompanyController::class, 'featuresList'])->name('company.featuresList');
        Route::any('features-list/update/{id}', [SuperAdminCompanyController::class, 'featuresListupdate'])->name('company.featuresListupdate');

        Route::post('features-list/add-edit', [SuperAdminCompanyController::class, 'addEditfeaturesListupdate'])->name('company.addEditfeaturesListupdate');

        Route::get('bench-candidates/{status?}', [SuperAdminCompanyController::class, 'bernchNewCandidates'])->name('company.new-bench-candidates');
        Route::get('showdetails-bench-candidates', [SuperAdminCompanyController::class, 'bernchDetailsCandidates'])->name('company.showdetails-bench-candidates');

        // =========================================================================
        // SUPER ADMIN MARKETPLACE OPERATIONS COMMAND CENTER (100% CQRS)
        // =========================================================================
        Route::prefix('marketplace')->name('marketplace.')->group(function () {
            Route::get('/', [SuperAdminMarketplaceController::class, 'index'])->name('index');
            Route::get('dashboard', [SuperAdminMarketplaceController::class, 'index'])->name('dashboard');
            Route::get('jobs', [SuperAdminMarketplaceController::class, 'index'])->name('jobs');
            Route::get('moderation', [SuperAdminMarketplaceController::class, 'index'])->name('moderation');
            Route::get('live', [SuperAdminMarketplaceController::class, 'index'])->name('live');
            Route::get('quarantine', [SuperAdminMarketplaceController::class, 'index'])->name('quarantine');
            Route::get('delisted', [SuperAdminMarketplaceController::class, 'index'])->name('delisted');
            Route::get('claims', [SuperAdminMarketplaceController::class, 'index'])->name('claims');
            Route::get('bench', [SuperAdminMarketplaceController::class, 'index'])->name('bench');
            Route::get('audit', [SuperAdminMarketplaceController::class, 'index'])->name('audit');

            // High-Performance AJAX Endpoints
            Route::get('telemetry-ajax', [SuperAdminMarketplaceController::class, 'getTelemetryAjax'])->name('telemetry.ajax');
            Route::get('jobs-grid-ajax', [SuperAdminMarketplaceController::class, 'getJobsGridAjax'])->name('grid.ajax');
            Route::get('job-detail-ajax/{id}', [SuperAdminMarketplaceController::class, 'getJobDetailAjax'])->name('detail.ajax');

            // State & Moderation Actions (ACID Writes + Outbox)
            Route::post('approve', [SuperAdminMarketplaceController::class, 'approveJob'])->name('approve');
            Route::post('reject', [SuperAdminMarketplaceController::class, 'rejectJob'])->name('reject');
            Route::post('settings/auto-approval', [SuperAdminMarketplaceController::class, 'updateSettings'])->name('settings.auto-approval');
            Route::post('outbox/replay', [SuperAdminMarketplaceController::class, 'replayOutboxEvent'])->name('outbox.replay');
        });

        // Backward-Compatibility Aliases (Legacy Endpoint Mapping)
        Route::get('job-public-market/{status?}', [SuperAdminMarketplaceController::class, 'index'])->name('company.job-public-market');
        Route::get('marketshow-job-details', [SuperAdminMarketplaceController::class, 'getJobDetailAjax'])->name('company.marketshow-job-details');
        Route::post('market-auto-approval', [SuperAdminMarketplaceController::class, 'updateSettings'])->name('company.market-auto-approval');
        Route::post('job-approval', [SuperAdminMarketplaceController::class, 'approveJob'])->name('company.job-approval');
        Route::post('job_reject_mail/reject_mail/{id?}', [SuperAdminMarketplaceController::class, 'rejectJob'])->name('company.job_reject_mail');
        Route::post('reject_job/reject_mail_send', [SuperAdminMarketplaceController::class, 'rejectJob'])->name('company.reject_mail_send_job');

        Route::post('candidate-approval', [SuperAdminCompanyController::class, 'candidateApproval'])->name('company.candidate-approval');
        Route::post('auto-approval', [SuperAdminCompanyController::class, 'autoApproval'])->name('company.auto-approval');
        Route::post('reject_mail/reject_mail/{id?}', [SuperAdminCompanyController::class, 'reject_mail'])->name('company.reject_mail');
        Route::post('email_template', [SuperAdminCompanyController::class, 'email_template'])->name('company.email_template');
        Route::post('reject_cvtracker/reject_mail_send', [SuperAdminCompanyController::class, 'reject_mail_send'])->name('company.reject_mail_send');
        Route::get('zoom-setting', [SuperAdminCompanyController::class, 'zoomSetting'])->name('company.zoom_setting');
        Route::post('setting-zoom-store', [SuperAdminCompanyController::class, 'settingZoomStore'])->name('company.settingZoomStore');
        Route::get('jobs/job_request/{id?}', [SuperAdminCompanyController::class, 'job_request'])->name('company.job_request');

        Route::get('jobs/recommandedrequest', [SuperAdminCompanyController::class, 'recommandedrequest'])->name('company.recommandedrequest');
        Route::get('jobs-recommandedrequest-ajax', [SuperAdminCompanyController::class, 'all_recommandedrequest_data_ajax'])->name('company.all_recommandedrequest_data_ajax');

        Route::get('jobs/job-lists/{id?}', [SuperAdminCompanyController::class, 'jobLists'])->name('company.all_jobs');
        Route::get('jobs/joblists', [SuperAdminCompanyController::class, 'all_jobs_data'])->name('company.all_jobs_data');

        Route::get('jobs-joblist-ajax', [SuperAdminCompanyController::class, 'all_jobs_data_ajax'])->name('company.all_jobs_data_ajax');
        Route::get('jobs/editJobStatus/{id?}', [SuperAdminCompanyController::class, 'editJobStatus'])->name('company.jobs.editJobStatus');
        Route::post('jobs/newUpdateJobstatus', [SuperAdminCompanyController::class, 'newUpdateJobstatus'])->name('company.jobs.newUpdateJobstatus');
        Route::get('jobs/job-viewmore/{id?}', [SuperAdminCompanyController::class, 'jobViewMore'])->name('company.jobs.job_viewmore');

        Route::get('candidate-lists', [SuperAdminCompanyController::class, 'canndidateList'])->name('company.canndidateList');

        Route::get('candidatedata', [SuperAdminCompanyController::class, 'candidatedata'])->name('company.candidatedata');
        Route::get('candidate-data-lists-ajax', [SuperAdminCompanyController::class, 'canndidateListAjax'])->name('company.canndidateListAjax');

        Route::get('candidatedatashow/{id}', [SuperAdminCompanyController::class, 'candidatedatashow'])->name('company.job-applications.candidatedatashow');

        Route::get('jobs/job_request_accept/{id?}', [SuperAdminCompanyController::class, 'job_request_accept'])->name('company.job_request_accept');
        Route::get('jobs/recommandedrequest_accept', [SuperAdminCompanyController::class, 'recommandedrequest_accept'])->name('company.recommandedrequest_accept');

        Route::get('jobs_recommandedrequest_accept_ajax', [SuperAdminCompanyController::class, 'recommandedrequest_accept_ajax'])->name('company.recommandedrequest_accept_ajax');

        Route::get('skills', [SuperAdminCompanyController::class, 'skillskElastic'])->name('company.skillskElastic');
        Route::any('skill/update/{id}/{name}', [SuperAdminCompanyController::class, 'skillUpdate'])->name('company.skillUpdate');
        Route::post('skill/add-edit', [SuperAdminCompanyController::class, 'addEditSkillUpdate'])->name('company.addEditSkillUpdate');
        Route::post('skillDelete', [SuperAdminCompanyController::class, 'skillDelete'])->name('company.skillDelete');

        Route::get('jobs/hmlisting/{id?}', [SuperAdminCompanyController::class, 'hmlisting'])->name('company.hmlisting');

        Route::get('interview-schedule/showfeedback/{id?}', [SuperAdminCompanyController::class, 'showfeedback'])->name('company.showfeedback');
        Route::post('interview-schedule/updateInterview', [SuperAdminCompanyController::class, 'updateInterview'])->name('company.updateInterview');

        Route::post('note-delete/comment', [SuperAdminCompanyController::class, 'notedestroy'])->name('company.notedestroy');

        // Phase 3.8: bulk-ops rate limit — 10/min per super-admin.
        Route::middleware('throttle:superadmin-bulk-ops')->group(function () {
            Route::post('check-active', [SuperAdminCompanyController::class, 'checkActive'])->name('company.checkActive');
            Route::post('check-status-operational', [SuperAdminCompanyController::class, 'checkStatusOperational'])->name('company.checkStatusOperational');
            Route::post('check-status-sales', [SuperAdminCompanyController::class, 'checkStatusSales'])->name('company.checkStatusSales');
        });

        Route::get('notification', [SuperAdminCompanyController::class, 'notification'])->name('company.notification');

        Route::any('notification/update/{id}', [SuperAdminCompanyController::class, 'notificationupdate'])->name('company.notificationupdate');

        Route::post('notification/add-edit', [SuperAdminCompanyController::class, 'addEditnotificationupdate'])->name('company.addEditnotificationupdate');

        Route::get('default-terms/{id?}', [SuperAdminCompanyController::class, 'defaultTerms'])->name('company.defaultTerms');
        Route::get('list-default-terms', [SuperAdminCompanyController::class, 'defaultTermsList'])->name('company.defaultTermsList');

        Route::post('default-terms/add-edit', [SuperAdminCompanyController::class, 'addEditTermsupdate'])->name('company.addEditTermsupdate');
        Route::get('offer-amount-change/{id?}', [SuperAdminCompanyController::class, 'offerAmountChange'])->name('company.offerAmountChange');
        Route::post('companysavecreateoffer', [SuperAdminCompanyController::class, 'companysavecreateoffer'])->name('company.companysavecreateoffer');

        Route::get('coin-features', [SuperAdminCompanyController::class, 'coinFeatures'])->name('company.coinFeatures');
        Route::get('add-edit-coin-featutes/{id?}', [SuperAdminCompanyController::class, 'addEditcoinFeatutes'])->name('company.addEditcoinFeatutes');
        Route::post('coin-events-store', [SuperAdminCompanyController::class, 'coinEventsStore'])->name('company.coinEventsStore');
        Route::get('coin-packages', [SuperAdminCompanyController::class, 'coinPackages'])->name('company.coinPackages');
        Route::get('add-edit-coin-packages/{id?}', [SuperAdminCompanyController::class, 'addEditcoinPackages'])->name('company.addEditcoinPackages');
        Route::post('coin-package-store', [SuperAdminCompanyController::class, 'coinPackageStore'])->name('company.coinPackageStore');

        Route::post('coin-event-status', [SuperAdminCompanyController::class, 'coinEventStatus'])->name('company.coinEventStatus');

        Route::get('coupons', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/add-edit/{id?}', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'addEdit'])->name('coupons.addEdit');
        Route::post('coupons/store', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'store'])->name('coupons.store');
        Route::post('coupons/toggle-status', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'toggleStatus'])->name('coupons.toggleStatus');
        Route::post('coupons/destroy', [\App\Http\Controllers\SuperAdmin\CouponController::class, 'destroy'])->name('coupons.destroy');

        Route::get('topup-coin-packages', [SuperAdminCompanyController::class, 'topUpCoinPackages'])->name('company.topUpCoinPackages');
        Route::get('add-edit-top-up-coin-packages/{id?}', [SuperAdminCompanyController::class, 'addEditTopUpcoinPackages'])->name('company.addEditTopUpcoinPackages');
        Route::post('top-upcoin-package-store', [SuperAdminCompanyController::class, 'topUpCoinPackageStore'])->name('company.topUpCoinPackageStore');

        Route::post('topup-coins-added', [SuperAdminCompanyController::class, 'topUpCoinsAdded'])->name('company.topUpCoinsAdded');
        Route::post('topuptypegetprice', [SuperAdminCompanyController::class, 'topuptypegetprice'])->name('company.topuptypegetprice');
        Route::get('price-currency-wise', [SuperAdminCompanyController::class, 'priceCurrencyWise'])->name('company.priceCurrencyWise');

        Route::post('boostkeywordadd', [SuperAdminCompanyController::class, 'boostkeywordadd'])->name('company.boostkeywordadd');

        Route::get('receiptdownload-subscribe/{id?}', [SuperAdminCompanyController::class, 'receiptdownload'])->name('company.receiptdownload');

        Route::get('jd-list/{status?}', [SuperAdminCompanyController::class, 'jdlist'])->name('company.jdlist');
        Route::get('jdlist/data', [SuperAdminCompanyController::class, 'jdlistdata'])->name('company.jdlistdata');
        Route::get('createjd/{entryType}/{id?}', [SuperAdminCompanyController::class, 'createjd'])->name('company.createjd');
        Route::post('jdgoogle-sheet-get-data', [SuperAdminCompanyController::class, 'jdgoogleSheetInsertdataInDatabase'])->name('company.jdgoogleSheetInsertdataInDatabase');
        // Phase 5: Transactional Outbox & Idempotency JD Mutation Routes
        Route::post('jd/status/{id}', [SuperAdminCompanyController::class, 'updateJdStatus'])->name('company.updateJdStatus');
        Route::post('jd/batch-status', [SuperAdminCompanyController::class, 'batchUpdateJdStatus'])->name('company.batchUpdateJdStatus');
        Route::delete('jd/{id}', [SuperAdminCompanyController::class, 'deleteJd'])->name('company.deleteJd');

        Route::get('jobs/hiring_manager/{id?}', [SuperAdminCompanyController::class, 'hiring_manager'])->name('company.hiring_manager');
        Route::get('jobs/recruiter/{id?}', [SuperAdminCompanyController::class, 'recruiter'])->name('company.recruiter');

        Route::get('ticket-list', [SuperAdminCompanyController::class, 'ticketList'])->name('tickets.ticketList');

        Route::post('tickets/{ticket}/status', [SuperAdminCompanyController::class, 'ticketUpdateStatus'])->name('tickets.ticketUpdateStatus');
        Route::get('ticketcreate', [SuperAdminCompanyController::class, 'ticketCreate'])->name('tickets.create');

        Route::get('sdshow-tickets/{id}', [SuperAdminCompanyController::class, 'ticketshow'])->name('tickets.show');
        Route::post('storeticketcreate', [SuperAdminCompanyController::class, 'storeticketCreate'])->name('tickets.store');
        Route::post('comments/store', [SuperAdminCompanyController::class, 'storetComment'])->name('comments.store');

        Route::get('transaction-history', [SuperAdminCompanyController::class, 'transactionhistory'])->name('comments.transactionhistory');

        Route::get('invoiceupdate', [SuperAdminCompanyController::class, 'invoiceupdate'])->name('company.invoiceupdate');

        // ///////////////////////////////////////////////////////////////

        Route::resource('global-settings', GlobalSettingController::class);
        Route::post('dddcustompackages/createInvoice', [GlobalSettingController::class, 'updatePr'])->name('global-settings.updatePr');
        Route::get('linkedin-settings/update-status/{id}', [LinkedInSettingController::class, 'updateStatus'])->name('updateStatus');
        Route::resource('linkedin-settings', LinkedInSettingController::class);
        Route::get('front-cms/index', [SuperAdminFrontCmsController::class, 'index'])->name('front-cms.index');
        Route::get('front-cms/change-form', [SuperAdminFrontCmsController::class, 'changeForm'])->name('front-cms.changeForm');
        Route::post('front-cms/update-common-header', [SuperAdminFrontCmsController::class, 'updateCommonHeader'])->name('front-cms.updateCommonHeader');
        Route::post('front-cms/update-header', [SuperAdminFrontCmsController::class, 'updateHeader'])->name('front-cms.updateHeader');
        Route::get('front-cms/features', [SuperAdminFrontCmsController::class, 'imageFeatures'])->name('front-cms.features');
        Route::post('front-cms/features', [SuperAdminFrontCmsController::class, 'saveImageFeatures'])->name('front-cms.savefeatures');
        Route::post('front-cms/features/{id}', [SuperAdminFrontCmsController::class, 'updatefeatures'])->name('front-cms.updatefeatures');
        Route::get('front-cms/features/{id}', [SuperAdminFrontCmsController::class, 'editImageFeatures'])->name('front-cms.editfeatures');
        Route::post('front-cms/deleteFeature/{id}', [SuperAdminFrontCmsController::class, 'deleteFeature'])->name('front-cms.deleteFeature');
        Route::resource('icon-features', FrontIconFeatureController::class);
        Route::resource('client-feedbacks', ClientFeedbackController::class);

        Route::resource('packages', PackageController::class);

        Route::get('package-features', [PackageController::class, 'package_feature'])->name('package.package_feature');
        Route::any('add-edit-package-features/{id?}', [PackageController::class, 'add_edit_package_feature'])->name('package.add_edit_package_feature');

        Route::get('custompackages/myPackageHistory', [CustomPackageController::class, 'myPackageHistory'])->name('custompackages.myPackageHistory');
        Route::get('custompackages/data', [CustomPackageController::class, 'data'])->name('custompackages.data');
        Route::get('custompackages/invoiceComment', [CustomPackageController::class, 'invoiceComment'])->name('custompackages.invoiceComment');
        Route::resource('custompackages', CustomPackageController::class);

        Route::post('custompackages/createInvoice', [CustomPackageController::class, 'createInvoice'])->name('custompackages.createInvoice');
        Route::get('custompackages/create/{company_id?}', [CustomPackageController::class, 'create'])->name('custompackages.create');
        Route::get('custompackages/invoiceDownload/{id}', [CustomPackageController::class, 'invoiceDownload'])->name('custompackages.invoiceDownload');
        Route::post('custompackages/saveinvoice', [CustomPackageController::class, 'saveinvoice'])->name('custompackages.saveinvoice');
        Route::post('custompackages/submitcomment/{id?}', [CustomPackageController::class, 'submitcomment'])->name('custompackages.submitcomment');

        Route::get('custompackages/viewmore/{id?}', [CustomPackageController::class, 'viewMore'])->name('custompackages.viewmore');
        Route::get('custompackages/editWallet/{id?}', [CustomPackageController::class, 'editWallet'])->name('custompackages.editWallet');
        Route::post('custompackages/update/{id}', [CustomPackageController::class, 'update'])->name('custompackages.update');

        Route::get('generatecustompackages', [CustomPackageController::class, 'generatecustompackages'])->name('custompackages.generatecustompackages');

        Route::get('package/feature', [CustomPackageController::class, 'packageFeature'])->name('custompackages.packagefeature');
        Route::get('package/lastdate', [CustomPackageController::class, 'lastdate'])->name('custompackages.lastdate');

        Route::get('defaultpackages/editDefaultPackage', [DefaultPackageController::class, 'editDefaultPackage'])->name('defaultpackages.editDefaultPackage');
        Route::resource('defaultpackages', DefaultPackageController::class);
        Route::post('defaultpackages/update', [DefaultPackageController::class, 'update'])->name('defaultpackages.update');

        Route::resource('currency-settings', SuperAdminCurrencyController::class);
        Route::resource('/payment-settings', SuperAdminPaymentSettingsController::class)->only(['index', 'update']);
        Route::resource('/sms-settings', SuperAdminSmsSettingsController::class)->only(['index', 'update']);
        Route::get('invoices/invoice-download/{invoice}', [InvoiceController::class, 'download'])->name('invoices.invoice-download');
        Route::get('invoices/paypal-invoice-download/{id}', [InvoiceController::class, 'paypalInvoiceDownload'])->name('invoices.paypal-invoice-download');
        Route::get('invoices/razorpay-invoice-download/{id}', [InvoiceController::class, 'razorpayInvoiceDownload'])->name('invoices.razorpay-invoice-download');
        Route::get('invoices/data', [InvoiceController::class, 'data'])->name('invoices.data');
        Route::resource('invoices', InvoiceController::class);
        // Custom Modules
        Route::post('custom-modules/verify-purchase', [CustomModuleController::class, 'verifyingModulePurchase'])->name('custom-modules.verify-purchase');
        Route::resource('custom-modules', CustomModuleController::class);
        Route::get('superadmins/data', [SuperadminController::class, 'data'])->name('superadmins.data');
        Route::resource('superadmins', SuperadminController::class);

        // Recruiter Note Templates (global defaults, company_id = NULL)
        Route::get('recruiter-note', [RecruiterNoteTemplateController::class, 'index'])->name('recruiter-note.index');
        Route::post('recruiter-note', [RecruiterNoteTemplateController::class, 'store'])->name('recruiter-note.store');
        Route::put('recruiter-note/{id}', [RecruiterNoteTemplateController::class, 'update'])->name('recruiter-note.update');
        Route::delete('recruiter-note/{id}', [RecruiterNoteTemplateController::class, 'destroy'])->name('recruiter-note.destroy');
        Route::get('recruiter-note/{id}/data', [RecruiterNoteTemplateController::class, 'getData'])->name('recruiter-note.getData');

        // Relationship Manager Note Templates (global defaults, company_id = NULL)
        Route::get('rm-note', [BenchRmNoteTemplateController::class, 'index'])->name('rm-note.index');
        Route::post('rm-note', [BenchRmNoteTemplateController::class, 'store'])->name('rm-note.store');
        Route::put('rm-note/{id}', [BenchRmNoteTemplateController::class, 'update'])->name('rm-note.update');
        Route::delete('rm-note/{id}', [BenchRmNoteTemplateController::class, 'destroy'])->name('rm-note.destroy');
        Route::get('rm-note/{id}/data', [BenchRmNoteTemplateController::class, 'getData'])->name('rm-note.getData');
    });

});
