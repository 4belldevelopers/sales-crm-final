<?php

use App\Http\Controllers\Admin\AdminJobApplicationController;
use App\Http\Controllers\Admin\CompanyDashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Front\FrontJobsController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\GoogleSheetsService;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalIPNController;
use App\Http\Controllers\PhonePePaymentController;
use App\Http\Controllers\RazorpayWebhookController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\SaasFront\SaasFrontController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController;
use App\Http\Controllers\StaffingCompany\StaffingCompanyJobApplicationController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\VerifyMobileController;
use App\Http\Controllers\ZoomController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    Route::get('/whatsapp/connect', function () {
        return view('whatsapp.connect');
    });
    Route::post('/whatsapp/create', [HomeController::class, 'create']);
    Route::get('/whatsapp/qr', [HomeController::class, 'qr']);
    Route::get('/whatsapp/status', [HomeController::class, 'status']);
    Route::post('/whatsapp/restart', [HomeController::class, 'restart']);
    Route::post('/whatsapp/send', [HomeController::class, 'send']);
    Route::post('/whatsapp/delete', [HomeController::class, 'delete']);

    Route::get('/mail-test', function () {
        $filePath = 'https://file.jero.works/Companies/4bell_technology_2/Resumes/2026/02/JERO_699c43f99f495.pdf';
        $attachments[] = [
            'path' => $filePath,
            'filename' => 'djfkjdfsd',
            'options' => [],
        ];
        Mail::raw('Laravel mail working ✅', function ($msg) use ($filePath) {
            $msg->to('4belldevelopers@gmail.com')
                ->subject('Test Mail From Laravel')
                ->attach($filePath, [
                    'as' => 'Custom_File_Name.pdf',
                    'mime' => 'application/pdf',
                ]);
        });

        return 'Mail Sent with Attachment';
    });

    Route::get('/redis-test', function () {
        cache()->tags(['ats'])->put('x', 'dragonfly', 60);

        return cache()->tags(['ats'])->get('x');
    });

    Route::get('/r2test', function () {
        Storage::disk('r2')->put(
            'test.txt',
            'HELLO R2'
        );

        return 'uploaded';
    });

    Route::get('/test-es', function () {
        $client = app(\App\Services\ElasticService::class)->getClient();

        return $client->ping();
    });
}

// web.php
Route::post('/notifications/read/{id}', function ($id) {
    $notification = auth()->user()->notifications()->find($id);
    if ($notification) {
        $notification->markAsRead();
    }

    return response()->json(['success' => true]);
});

Route::post('/salary/calculate', [HomeController::class, 'salaryCalculate'])->name('salary.calculate');

Route::get('/download-resume/{id}/{path}', [HomeController::class, 'downloadResume'])
    ->where('path', '[A-Za-z0-9\-_]+')
    ->name('download.resume');

Route::get('/captcha', [\App\Http\Controllers\Auth\CaptchaController::class, 'generate'])->name('captcha.generate');

// Route::get('/', [LoginController::class, 'showLoginForm']);

Route::post('/questions/store', [HomeController::class, 'questionsStore'])->name('questions.questionsStore');

// ====================== AUTH ROUTES ======================

// Login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::get('company-login-by-super-admin', [LoginController::class, 'superLoginForms'])->name('super.login.form');
// Logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// POST /register removed — RegisterController::register() does not exist; POST would 500 (Finding 1.6)

// Forgot Password
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset Password
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('ResetPasswordStore', [ResetPasswordController::class, 'ResetPasswordStore'])->name('ResetPasswordStore');
Route::get('password-confirmation', [ResetPasswordController::class, 'passwordConfirmation'])->name('password-confirmation');
Route::post('password/emailsend', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.emailsend');

// ====================== PROTECTED ROUTE ======================

Route::post('/delete-template', [HomeController::class, 'deleteTemplate'])->name('delete.template');
Route::get('/bench-condidates/{companyName}/{companyId}', [HomeController::class, 'benchCandidates'])->name('get.benchCandidates');
Route::get('/career-jobs/{companyName}/{companyId}', [HomeController::class, 'careerJobPage'])->name('get.careerJobPage');
Route::get('/api/v2/career-jobs/{companyId}', [HomeController::class, 'careerJobApi'])->name('get.careerJobApi');
Route::get('/job-apply/{jobId}/{companyId}', [HomeController::class, 'jobApply'])->name('get.jobApply');
Route::get('/whatsapp-terms-and-conditions', [HomeController::class, 'whatsappTermsAndConditions'])->name('get.whatsappTermsAndConditions');
Route::get('/job-details/{jobId}/{companyId}', [HomeController::class, 'jobDetails'])->name('get.jobDetails');
Route::post('/uploadresume', [HomeController::class, 'uploadresume'])->name('get.uploadresume');
Route::post('/extract-resume-applications', action: [HomeController::class, 'resumExtract'])->name('get.extract-resume-job-applications');
/* Intent: Rate limit public candidate submission route to 30 requests/minute to prevent bot spam and DDoS attacks */
Route::post('/careerStore', [HomeController::class, 'careerStore'])->middleware('throttle:30,1')->name('get.careerStore');
Route::get('benchCandidateDetails/public_show/{id?}', [HomeController::class, 'benchCandidateDetails'])->name('get.benchCandidateDetails');
// ** Cron start here */
Route::get('/company-wise-jobs', [HomeController::class, 'companyWiseJobs'])->name('get.companyWiseJobs');
Route::get('/market-company-wise-jobs', [HomeController::class, 'marketcompanyWiseJobs'])->name('get.marketcompanyWiseJobs');
Route::get('/market-jobs', [HomeController::class, 'marketJobs'])->name('get.marketJobs');
Route::get('/recommened-jobs', [HomeController::class, 'recommenedJobs'])->name(name: 'get.recommenedJobs');
Route::get('/partner-recommened', [HomeController::class, 'partnerRecommened'])->name('get.partnerRecommened');
Route::get('/notification-mail-reset', [HomeController::class, 'notificationMailReset'])->name('get.notificationMailReset');
Route::get('/pending-profiles-notification-mail', [HomeController::class, 'pendingProfilnotificationMail'])->name('get.pendingProfilnotificationMail');
Route::get('/job-request-auto-expired-after-valid-date', [HomeController::class, 'jobRequestAutoExpiredAfterValidDate'])->name('get.jobRequestAutoExpiredAfterValidDate');
Route::get('/client-request-auto-expired-after-valid-date', [HomeController::class, 'clientRequestAutoExpiredAfterValidDate'])->name('get.clientRequestAutoExpiredAfterValidDate');
Route::get('/candidate-request-auto-expired-after-valid-date', [HomeController::class, 'candidateRequestAutoExpiredAfterValidDate'])->name('get.candidateRequestAutoExpiredAfterValidDate');
Route::get('/job-auto-expired-after-valid-date', [HomeController::class, 'jobAutoExpiredAfterValidDate'])->name('get.jobAutoExpiredAfterValidDate');
Route::get('/update-currency-rates', [HomeController::class, 'updateCurrencyRates'])->name('get.updateCurrencyRates');
// ** Cron end here */

Route::get('/companies-index-elastic', [HomeController::class, 'companyIndexElastic'])->name('get.companyIndexElastic');
Route::get('/candidates-index-elastic', [HomeController::class, 'candidateIndexElastic'])->name('get.candidateIndexElastic');
Route::get('/candidate-career-apply-Index-elastic', [HomeController::class, 'candidateCareerApplyIndexElastic'])->name('get.candidateCareerApplyIndexElastic');
Route::get('/candidates-bench-index-elastic', [HomeController::class, 'candidateBenchIndexElastic'])->name('get.candidateBenchIndexElastic');
Route::get('/job-index-elastic', [HomeController::class, 'jobIndexElastic'])->name('get.jobIndexElastic');
Route::get('/location-index-elastic', [HomeController::class, 'locationIndexElastic'])->name('get.locationIndexElastic');
Route::get('/interview-tracker-index-elastic', [HomeController::class, 'interviewTrackerIndexElastic'])->name('get.interviewTrackerIndexElastic');
Route::get('/client-list-index-elastic', [HomeController::class, 'clientListIndexElastic'])->name('get.clientListIndexElastic');
Route::get('/user-index-elastic', [HomeController::class, 'usersIndexElastic'])->name('get.usersIndexElastic');
Route::get('/bench-placed-index-elastic', [HomeController::class, 'benchPlacedIndexElastic'])->name('get.benchPlacedIndexElastic');
Route::get('/candidate-request-index-elastic', [HomeController::class, 'candidateRequestIndexElastic'])->name('get.candidateRequestIndexElastic');
Route::get('/job-request-index-elastic', [HomeController::class, 'jobRequestIndexElastic'])->name('get.jobRequestIndexElastic');
Route::get('/client-request-index-elastic', [HomeController::class, 'clientRequestIndexElastic'])->name('get.clientRequestIndexElastic');
Route::get('/team-rank-elastic', [HomeController::class, 'teamRankElastic'])->name('get.teamRankElastic');
Route::get('/skills-elastic', [HomeController::class, 'skillskElastic'])->name('get.skillskElastic');

Route::get('/candidate-skill-exp-elastic', [HomeController::class, 'candidateSkillskExpElastic'])->name('get.candidateSkillskExpElastic');

Route::get('/candidate-job-question-elastic', [HomeController::class, 'candidateJobQuestionElastic'])->name('get.candidateJobQuestionElastic');

Route::get('/candidate-time-line-elastic', [HomeController::class, 'candidateTimeLineElastic'])->name('get.candidateTimeLineElastic');
Route::get('/master-status-elastic', [HomeController::class, 'masterStatusElastic'])->name('get.masterStatusElastic');
Route::get('/qualification-master-elastic', [HomeController::class, 'qualificationMasterElastic'])->name('get.qualificationMasterElastic');
Route::get('/bench-assign-elastic', [HomeController::class, 'benchAssignElastic'])->name('get.benchAssignElastic');
Route::get('/bench-additional-detail-elastic', [HomeController::class, 'benchAdditionalDetailElastic'])->name('get.benchAdditionalDetailElastic');
Route::get('/add-applicant-notes-elastic', [HomeController::class, 'applicantNotesElastic'])->name('get.applicantNotesElastic');
Route::get('/job-categories-elastic-elastic', [HomeController::class, 'jobCategoriesElastic'])->name('get.jobCategoriesElastic');
Route::get('/email-notifications-elastic', [HomeController::class, 'emailNotificationsElastic'])->name('get.emailNotificationsElastic');
Route::get('/mail-permissions-elastic', [HomeController::class, 'mailPermissionsElastic'])->name('get.mailPermissionsElastic');
Route::get('/master-notifications-elastic', [HomeController::class, 'masterNotificationsElastic'])->name('get.masterNotificationsElastic');
Route::get('/th-email-template-elastic', [HomeController::class, 'thEmailTemplateElastic'])->name('get.thEmailTemplateElastic');
Route::get('/whatsapp-email-elastic', [HomeController::class, 'whatsappAndMailelastic'])->name('get.whatsappAndMailelastic');
Route::get('/company-skill-elastic', [HomeController::class, 'companySkillselastic'])->name('get.companySkillselastic');

Route::get('/coin-event-elastic', [HomeController::class, 'coinEventsElastic'])->name('get.coinEventsElastic');
Route::get('/company-coins-elastic', [HomeController::class, 'compnayCoinsElastic'])->name('get.compnayCoinsElastic');
Route::get('/coin-packages-elastic', [HomeController::class, 'coinPackagesElastic'])->name('get.coinPackagesElastic');
Route::get('/coin-order-events', [HomeController::class, 'coinOrderEventsElastic'])->name('get.coinOrderEventsElastic');
Route::get('/coin-orders-elastic', [HomeController::class, 'coinOrdersElastic'])->name('get.coinOrdersElastic');
Route::get('/coin-transection-logs-elastic', [HomeController::class, 'coinTransectionLogsElastic'])->name('get.coinTransectionLogsElastic');
Route::get('/coin-package-types-elastic', [HomeController::class, 'coinPackageTypesElastic'])->name('get.coinPackageTypesElastic');
Route::get('/check-out-subscribe-elastic', [HomeController::class, 'checkoutSubscribeElastic'])->name('get.checkoutSubscribeElastic');
Route::get('/billing-address-companies-elastic', [HomeController::class, 'billingAddressCompaniesElastic'])->name('get.billingAddressCompaniesElastic');
Route::get('/payment-settings-elastic', [HomeController::class, 'paymentSettingsElastic'])->name('get.paymentSettingsElastic');
Route::get('/cart-transaction-history-elastic', [HomeController::class, 'cartTransactionHistoryElastic'])->name('get.cartTransactionHistoryElastic');
Route::get('/sold-by-address-elastic', [HomeController::class, 'soldByAddressElastic'])->name('get.soldByAddressElastic');
Route::get('/billing-address-by-orders-elastic', [HomeController::class, 'billingAddressByOrdersElastic'])->name('get.billingAddressByOrdersElastic');
Route::get('/job-application-skills-elastic', [HomeController::class, 'jobApplicationSkillsElastic'])->name('get.jobApplicationSkillsElastic');

Route::get('/user-removed', [HomeController::class, 'userRemoved'])->name('get.userRemoved');
Route::get('/offer-expired', [HomeController::class, 'offerExpired'])->name('get.offerExpired');

// /run-queue removed — unauthenticated queue-worker start (security: Finding #4)

Route::get('/get-states', [HomeController::class, 'getStates'])->name('get.states');
Route::get('/get-cities', [HomeController::class, 'getCities'])->name('get.cities');
Route::post('/submit-form', [HomeController::class, 'submit'])->name('form.submit');
Route::get('/city-job-suggestions', [HomeController::class, 'citySuggestions'])->name('city.citySuggestions');
Route::get('/skill-suggestions', [HomeController::class, 'skillSuggestions'])->name('skill.skillSuggestions');
Route::get('/resume', [ResumeController::class, 'showForm'])->name('resume.form');
Route::post('/resume/upload', [ResumeController::class, 'upload'])->name('resume.upload');
Route::get('/resume/search', [ResumeController::class, 'search'])->name('resume.search');

Route::get('/indexregister', [HomeController::class, 'indexregister'])->name('indexregister.tables');
Route::get('/truncate-tables', [HomeController::class, 'truncateTables'])->name('truncate.tables');
Route::get('country-city-list/{id?}', [HomeController::class, 'CountrycityList'])->name('countrycitylist');

Route::get('/signature-pad/{offerId?}/{mtype}/{email}/{from_email}', [HomeController::class, 'offerPreviewSignature'])->name('signature.pad');
Route::post('/save-signature', [HomeController::class, 'saveSignature'])->name('save.signature');
Route::get('/candidate-mail-response/{wise_condidateId?}/{mtype}/{email}/{to_email}/{subject}/{userId}/{companyId}/{insertId}', [HomeController::class, 'candidateMailResponse'])->name('mail.candidateMailResponse');

Route::get('/payment', function () {
    return view('payment');
})->name('phonepe.form');
Route::post('/create-payment', [PhonePePaymentController::class, 'createPayment'])->name('phonepe.payment');

Route::post('/payment-callback', [PhonePePaymentController::class, 'callback'])->name('phonepe.callback');

Route::get('phonepe', [PhonePePaymentController::class, 'phonePe']);
Route::any('phonepe-response', [PhonePePaymentController::class, 'response'])->name('response');
Route::get('/phonepe/callback', [PhonePePaymentController::class, 'paymentCallback'])->name('phonepe.callback');

Route::get('/sheets', [GoogleSheetsService::class, 'index']);

Route::get('/run-cron', function () {
    Artisan::call('notify:featured-end');

    return 'Cron job executed!';
});

Route::get('/zoom/authorize', [ZoomController::class, 'redirectToZoom'])->name('redirectToZoom'); // Updated route method
Route::get('/zoom/callback', [ZoomController::class, 'callback']);
Route::get('/zoom/create', [ZoomController::class, 'createMeeting']);

Route::get('/zoom/create-meeting', 'ZoomController@createZoomMeeting');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('redirectToGoogle');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('create-google-meet', [GoogleController::class, 'createGoogleMeet']);

Route::get('/create-payment', [PaymentController::class, 'createPayment'])->name('payment.create');

Route::post('/payment-callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

Route::get('/link-payment-callback', [PaymentController::class, 'linkPaymentCallback'])->name('payment.link_callback');

Route::get('/payment-success', function () {
    $paymentLink = request('payment_link');

    return view('success', ['paymentLink' => $paymentLink]);
})->name('payment.success');

Route::get('/payment-failed', function () {
    $paymentLink = request('payment_link');

    return view('failure', ['paymentLink' => $paymentLink]);
})->name('payment.failed');

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('optimize:clear');
    // return what you want
});

Route::get('admin/404', function () {
    return view('admin/error/error-404');
});

Route::get('500', function () {
    return view('admin/error/error-500');
});

Route::get('300', function () {
    return view('admin/error/error-300');
});

Route::post('/offer_download_mail/{id?}', [HomeController::class, 'fwd_offer_download'])->name('get.offer_download_mail');
// Direct PDF stream for candidate download — no file storage, no URL, no symlink dependency.
Route::get('/offer-letter-stream/{offerId}', [HomeController::class, 'streamOfferLetterPdf'])->name('offer.letter.stream');

// Route::prefix('/')->group(function () {
Route::get('/', [SaasFrontController::class, 'index'])->name('index');
Route::get('teamReg/{companyId?}/{invitationId?}', [SaasFrontController::class, 'teamReg'])->name('teamReg');
Route::get('terms-and-conditions/', [SaasFrontController::class, 'termsAndConditions'])->name('termsAndConditions');
Route::get('payment-terms-and-conditions/', [SaasFrontController::class, 'paymenttermsAndConditions'])->name('paymenttermsAndConditions');
Route::get('privacy-policy/', [SaasFrontController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('refund-policy/', [SaasFrontController::class, 'refundPolicy'])->name('refundPolicy');
Route::get('help_page/', [SaasFrontController::class, 'help_page'])->name('help_page');
Route::post('verifyTeamProfile', [SaasFrontController::class, 'verifyTeamProfile'])->name('verifyTeamProfile');
Route::get('invitation-company-register/{code?}', [SaasFrontController::class, 'invitationCompanyRegister'])->name('invitation-company-register');
Route::get('company-registration-invitation/{code?}', [SaasFrontController::class, 'invitationCompanyRegisterDirectAccess'])->name('company-registration-invitation');
Route::post('invitation-company-store/', [SaasFrontController::class, 'invitationCompanyStore'])->name('invitation-company-store');
Route::post('/', [SaasFrontController::class, 'submitContact'])->name('contact');
Route::post('/company-register', [SaasFrontController::class, 'companyRegister'])->name('company-register')->middleware('throttle:register-submit');
Route::post('/company-register-chk', [SaasFrontController::class, 'companyRegisterChk'])->name('company-register-chk')->middleware('throttle:register-form');

Route::post('/valid-email-chk', [HomeController::class, 'validEmailChk'])->name('valid-email-chk');
Route::post('/import-meetings', [HomeController::class, 'importMeetings'])->name('importMeetings');

Route::post('/contact-company-register-chk', [SaasFrontController::class, 'contactCompanyRegisterChk'])->name('contact-company-register-chk')->middleware('throttle:register-form');
Route::post('/domain-company-register-chk', [SaasFrontController::class, 'domainCompanyRegisterChk'])->name('domain-company-register-chk')->middleware('throttle:register-form');
Route::post('/client-domain-register-chk', [SaasFrontController::class, 'domainClientRegisterChk'])->name('client-domain-register-chk')->middleware('throttle:register-form');
// signed middleware added Sprint 5 task 1.5/5.10 — validates temporarySignedRoute URL.
// WARNING: in-flight unsigned links (sent before this deploy) will 403. Resend clears them.
Route::get('/email-verification/{code}', [SaasFrontController::class, 'getEmailVerification'])->name('get-email-verification')->middleware('signed');
Route::get('/resend/{id}', [SaasFrontController::class, 'resend'])->name('resend')->middleware('throttle:register-resend');
Route::post('change-language/{code}', [SaasFrontController::class, 'changeLanguage'])->name('changeLanguage');
Route::get('page/{slug?}', [SaasFrontController::class, 'page'])->name('page');
Route::get('help/{slug?}', [SaasFrontController::class, 'help'])->name('help');

// });

// Front routes end
// Front routes start
// Route::prefix('Front')->group(function () {

Route::group(['namespace' => 'Front'], function () {

    Route::get('/careers/{slug}', 'FrontJobsController@jobOpenings')->name('jobOpenings');
    Route::get('/careers/{slug}/job-listing', 'FrontJobsController@jobListing')->name('job-listing');
    Route::get('/job/{companySlug}/{slug}', 'FrontJobsController@jobDetailWithCompany')->name('jobDetail');
    Route::get('/job/{companySlug}/{slug}/apply', 'FrontJobsController@jobApplyWithCompany')->name('jobApply');

    Route::get('/job-offer/{slug}', 'FrontJobOfferController@index')->name('job-offer');
    Route::post('/save-offer', 'FrontJobOfferController@saveOffer')->name('save-offer');
    Route::post('/job/saveApplication', 'FrontJobsController@saveApplication')->name('saveApplication');
    Route::post('/job/fetch-country-state', 'FrontJobsController@fetchCountryState')->name('fetchCountryState');
    Route::get('/redirect/linkedin', ['uses' => 'FrontJobsController@redirectToProvider', 'as' => 'social.login']);
    Route::get('/linkedin/callback', ['uses' => 'FrontJobsController@callback']);
    Route::get('auth/callback/{provider}', 'FrontJobsController@callback')->name('linkedinCallback');
    Route::get('auth/redirect/{provider}', 'FrontJobsController@redirect')->name('linkedinRedirect');

});
Route::group(['namespace' => 'FrontOld', 'as' => 'jobs.'], function () {
    if (! module_enabled('Subdomain')) {
        Route::get('/careers', 'FrontJobsController@jobOpenings')->name('jobOpenings');
        // Route::get('/job/{companySlug}/{slug}', 'FrontJobsController@jobDetailWithCompany')->name('jobDetail');
        // Route::get('/job/{slug}/{jobid}', 'FrontJobsController@jobDetail')->name('jobDetail');
        // Route::get('/job/{slug}', 'FrontJobsController@jobDetail')->name('jobDetail');
        Route::get('/job/{slug}', 'FrontJobsController@jobDetail')->name('jobDetail');
        Route::get('/job/{slug}/apply', 'FrontJobsController@jobApply')->name('jobApply');
    } else {
        Route::get('/careers/{slug}', 'FrontJobsController@jobOpenings')->name('jobOpenings');
        Route::get('/job/{companySlug}/{slug}', 'FrontJobsController@jobDetailWithCompany')->name('jobDetail');
        Route::get('/job/{companySlug}/{slug}/apply', 'FrontJobsController@jobApplyWithCompany')->name('jobApply');
    }

    Route::get('/job-offer/{slug}', 'FrontJobOfferController@index')->name('job-offer');
    Route::post('/save-offer', 'FrontJobOfferController@saveOffer')->name('save-offer');
    Route::post('/job/saveApplication', 'FrontJobsController@saveApplication')->name('saveApplication');
    Route::post('/job/fetch-country-state', 'FrontJobsController@fetchCountryState')->name('fetchCountryState');
    Route::get('/redirect/linkedin', ['uses' => 'FrontJobsController@redirectToProvider', 'as' => 'social.login']);
    Route::get('/linkedin/callback', ['uses' => 'FrontJobsController@callback']);
    Route::get('auth/callback/{provider}', 'FrontJobsController@callback')->name('linkedinCallback');
    Route::get('auth/redirect/{provider}', 'FrontJobsController@redirect')->name('linkedinRedirect');
});

// });

// Front routes end
// Paypal IPN Confirm
Route::post('verify-billing-ipn', [PaypalIPNController::class, 'verifyBillingIPN'])->name('verify-billing-ipn');
Route::post('/save-invoices', [StripeWebhookController::class, 'saveInvoices'])->name('save_webhook');
Route::post('/save-razorpay-invoices', [RazorpayWebhookController::class, 'saveInvoices'])->name('save_razorpay-webhook');
Route::get('/check-razorpay-invoices', [RazorpayWebhookController::class, 'checkInvoices'])->name('check_razorpay-webhook');

Route::get('change-mobile', [VerifyMobileController::class, 'changeMobile'])->name('changeMobile');
Route::post('/send-otp-code', [VerifyMobileController::class, 'sendVerificationCode'])->name('sendOtpCode');
Route::post('/send-otp-code/account', [VerifyMobileController::class, 'sendVerificationCode'])->name('sendOtpCode.account');
Route::post('/verify-otp-phone', [VerifyMobileController::class, 'verifyOtpCode'])->name('verifyOtpCode');
Route::post('/verify-otp-phone/account', [VerifyMobileController::class, 'verifyOtpCode'])->name('verifyOtpCode.account');
Route::get('/remove-session', [VerifyMobileController::class, 'removeSession'])->name('removeSession');

Route::post('mark-notification-read', [NotificationController::class, 'markAllRead'])->name('mark-notification-read');

Route::get('job-applications/create-schedule/{id?}', [AdminJobApplicationController::class, 'createSchedule'])->name('job-applications.create-schedule');

Route::get('job-applications/create-schedule/{id?}', [StaffingCompanyJobApplicationController::class, 'createSchedule'])->name('job-applications.create-schedule');

Route::get('job-applications/create-schedule/{id?}', [ServiceCompanyJobApplicationController::class, 'createSchedule'])->name('job-applications.create-schedule');

// ####################################################### PRODUCT BASED COMPANY-3
Route::prefix('product_company')->name('product_company.')->group(function () {

    Route::group(['middleware' => ['custom-package']], function () {

        Route::get('/dashboard', 'ProductCompanyDashboardController@index')->name('dashboard');
        Route::get('/home', 'ProductCompanyDashboardController@home')->name('home');
        Route::get('job-categories/data', 'ProductCompanyJobCategoryController@data')->name('job-categories.data');
        Route::get('job-categories/getSkills/{categoryId}', 'ProductCompanyJobCategoryController@getSkills')->name('job-categories.getSkills');
        Route::resource('job-categories', 'ProductCompanyJobCategoryController');
        Route::get('job-categories/export/{start}/{length}', 'ProductCompanyJobCategoryController@export')->name('job-categories.export'); // NEW
        // Questions
        Route::get('questions/data', 'ProductCompanyQuestionController@data')->name('questions.data');
        Route::resource('questions', 'ProductCompanyQuestionController');
        // company settings
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

        // ->except(['update']): the explicit POST route below already owns the
        // "product_company.account.update" name (a custom update action, not the resource's
        // conventional PUT/PATCH). Without this exclusion the two collide and
        // `php artisan route:cache` fails at serialization ("Another route has already been
        // assigned name..."), which was silently breaking every staging deploy — the deploy
        // script aborts at that step, so `octane:reload` never runs and the server keeps
        // serving stale code from memory indefinitely.
        Route::resource('account', 'ProductCompanyAccountSettingsController')->except(['update']);
        Route::get('account/editProfile/{id?}', 'ProductCompanyAccountSettingsController@editProfile')->name('account.editProfile');
        Route::post('account/update/{id}', 'ProductCompanyAccountSettingsController@update')->name('account.update');
        Route::post('account/addIntStage', 'ProductCompanyAccountSettingsController@addIntStage')->name('account.addIntStage');
        Route::post('account/delIntStage', 'ProductCompanyAccountSettingsController@delIntStage')->name('account.delIntStage');
        Route::post('account/updatePriority', 'ProductCompanyAccountSettingsController@updatePriority')->name('account.updatePriority');
        Route::get('account/data', 'ProductCompanyAccountSettingsController@data')->name('account.data');
        Route::post('account/storeDept', 'ProductCompanyAccountSettingsController@storeDept')->name('account.storeDept');
        Route::get('account/zoom/{id?}', 'ProductCompanyAccountSettingsController@zoom')->name('account.zoom');
        Route::get('account/add_team_member_form/{id?}', 'ProductCompanyAccountSettingsController@add_team_member_form')->name('account.add_team_member_form');
        Route::post('account/sendinvitation', 'ProductCompanyAccountSettingsController@sendinvitation')->name('account.sendinvitation');

        Route::get('team/add_team_member_form/{id?}', 'ProductCompanyAccountSettingsController@add_team_member_form')->name('team.add_team_member_form');

        Route::post('team/sendinvitation', 'ProductCompanyAccountSettingsController@sendinvitation')->name('team.sendinvitation');
        Route::get('skills/data', 'ProductCompanySkillsController@data')->name('skills.data');
        Route::resource('skills', 'ProductCompanySkillsController');
        Route::get('skills/addMoreSkill/{id}', 'ProductCompanySkillsController@addMoreSkill')->name('skills.addMoreSkill'); // NEW
        Route::post('skills/saveMoreSkills/', 'ProductCompanySkillsController@saveMoreSkills')->name('skills.saveMoreSkills'); // NEW
        Route::get('locations/data', 'ProductCompanyLocationsController@data')->name('locations.data');
        Route::resource('locations', 'ProductCompanyLocationsController');

        // #############       START JOB
        Route::post('jobs/refresh-date/{id}', 'ProductCompanyJobsController@refreshDate')->name('jobs.refreshDate');

        Route::get('jobs/data', 'ProductCompanyJobsController@data')->name('jobs.data');
        Route::get('jobs/recommendedData', 'ProductCompanyJobsController@recommendedData')->name('jobs.recommendedData');

        Route::get('jobs/boostkeywords', 'ProductCompanyJobsController@boostkeywords')->name('jobs.boostkeywords');

        Route::get('jobs/hmlistingData', 'ProductCompanyJobsController@hmlistingData')->name('jobs.hmlistingData');
        Route::get('jobs/teamJobAssignRequest', 'ProductCompanyJobsController@teamJobAssignRequest')->name('jobs.teamJobAssignRequest'); // NEW

        Route::get('jobs/recommendedAssignList', 'ProductCompanyJobsController@recommendedAssignList')->name('jobs.recommendedAssignList');
        Route::get('jobs/staffingPartnersRegister', 'ProductCompanyJobsController@staffingPartnersRegister')->name('jobs.staffingPartnersRegister');
        Route::get('jobs/assignRejected', 'ProductCompanyJobsController@assignRejected')->name('jobs.assignRejected'); // NEW
        Route::get('jobs/removeJob', 'ProductCompanyJobsController@removeJob')->name('jobs.removeJob'); // NEW

        Route::get('jobs/application-data', 'ProductCompanyJobsController@applicationData')->name('jobs.applicationData');
        Route::post('jobs/send-emails', 'ProductCompanyJobsController@sendEmails')->name('jobs.sendEmails');
        Route::get('jobs/send-email', 'ProductCompanyJobsController@sendEmail')->name('jobs.sendEmail');
        Route::resource('jobs', 'ProductCompanyJobsController')->except(['update']);
        Route::get('jobs/getContactPerson/{client_id}', 'ProductCompanyJobsController@getContactPerson')->name('jobs.getContactPerson'); // NEW
        Route::get('jobs/lightbox/{client_id}', 'ProductCompanyJobsController@lightbox')->name('jobs.lightbox'); // NEW
        Route::get('jobs/loc_lightbox/{client_id}', 'ProductCompanyJobsController@loc_lightbox')->name('jobs.loc_lightbox'); // NEW
        Route::get('jobs/viewmore/{id?}', 'ProductCompanyJobsController@viewMore')->name('jobs.viewmore'); // NEW
        Route::get('jobs/spartnerlisting/{id?}', 'ProductCompanyJobsController@spartnerlisting')->name('jobs.spartnerlisting'); // NEW
        Route::get('jobs/load_visibility_control/{id?}', 'ProductCompanyJobsController@loadVisibilityControl')->name('jobs.load_visibility_control'); // NEW
        Route::get('jobs/editJobStatus/{id?}', 'ProductCompanyJobsController@editJobStatus')->name('jobs.editJobStatus'); // NEW
        Route::get('jobs/priority/{id?}', 'ProductCompanyJobsController@priority')->name('jobs.priority'); // NEW
        Route::post('jobs/priorityUpdate', 'ProductCompanyJobsController@priorityUpdate')->name('jobs.priorityUpdate'); // NEW
        Route::get('jobs/export/{start}/{length}', 'ProductCompanyJobsController@export')->name('jobs.export'); // NEW
        Route::post('jobs/updatestatus', 'ProductCompanyJobsController@updatestatus')->name('jobs.updatestatus'); // NEW
        Route::post('jobs/spartnerAssign', 'ProductCompanyJobsController@spartnerAssign')->name('jobs.spartnerAssign'); // NEW
        Route::post('jobs/newUpdateJobstatus', 'ProductCompanyJobsController@newUpdateJobstatus')->name('jobs.newUpdateJobstatus'); // NEW
        Route::post('jobs/update/{id}', 'ProductCompanyJobsController@update')->name('jobs.update');
        // Route::get('jobs/hiring_manager/{id?}', 'AdminJobsController@hiring_manager')->name('jobs.hiring_manager'); // NEW
        // GET and POST merged into one match() — two separate Route:: calls sharing the same
        // name broke `php artisan route:cache` serialization (see note near the account/jobs
        // resource() fixes above for how this class of bug silently killed staging deploys).
        Route::match(['get', 'post'], 'jobs/hiring_manager/{id?}', 'ProductCompanyJobsController@hiring_manager')->name('jobs.hiring_manager'); 				// NEW
        Route::get('jobs/recruiter/{id?}', 'ProductCompanyJobsController@recruiter')->name('jobs.recruiter'); 								// NEW
        Route::post('jobs/recruiter/{id?}', 'ProductCompanyJobsController@recruiter')->name('jobs.recruiter'); 								// NEW
        Route::get('jobs/all_jobs/{id?}', 'ProductCompanyJobsController@all_jobs')->name('jobs.all_jobs'); 									// NEW
        Route::get('jobs/teamdata', 'ProductCompanyJobsController@teamdata')->name('jobs.teamdata');

        Route::get('jobs/recommended_request/{id?}', 'ProductCompanyJobsController@recommended_request')->name('jobs.recommended_request');
        Route::get('jobs/rsp_job_request_send/{id?}', 'ProductCompanyJobsController@rsp_job_request_send')->name('jobs.rsp_job_request_send');
        Route::get('jobs/send_job_recommanded_request/{id?}', 'ProductCompanyJobsController@send_job_recommanded_request')->name('jobs.send_job_recommanded_request');
        Route::get('jobs/resend_job_recommanded_request/{id?}', 'ProductCompanyJobsController@resend_job_recommanded_request')->name('jobs.resend_job_recommanded_request');

        Route::get('jobs/send_only_job_invitation/{id?}', 'ProductCompanyJobsController@send_only_job_invitation')->name('jobs.send_only_job_invitation');

        Route::get('jobs/hmlisting/{id?}', 'ProductCompanyJobsController@hmlisting')->name('jobs.hmlisting'); 								// NEW
        Route::get('jobs/hmlistingupdate/{id?}', 'ProductCompanyJobsController@hmlistingupdate')->name('jobs.hmlistingupdate'); 			// NEW

        // #############       END JOB

        // #############		START BENCH CANDIDATE
        Route::get('bench-candidates/offerListing', 'ProductCompanyBenchCandidatesController@offerListing')->name('bench-candidates.offerListing');                           						// NEW
        Route::get('bench-candidates/offerListingData', 'ProductCompanyBenchCandidatesController@offerListingData')->name('bench-candidates.offerListingData');               						// NEW
        Route::get('bench-candidates/placedListing', 'ProductCompanyBenchCandidatesController@placedListing')->name('bench-candidates.placedListing');                         						// NEW
        Route::get('bench-candidates/placedListingData', 'ProductCompanyBenchCandidatesController@placedListingData')->name('bench-candidates.placedListingData');             						// NEW
        Route::get('bench-candidates/bench_explore_listing', 'ProductCompanyBenchCandidatesController@bench_explore_listing')->name('bench-candidates.bench_explore_listing');  					// NEW
        Route::get('bench-candidates/bench_assign_to_job_listing', 'ProductCompanyBenchCandidatesController@bench_assign_to_job_listing')->name('bench-candidates.bench_assign_to_job_listing');  	// NEW
        Route::get('bench-candidates/talent_pool', 'ProductCompanyBenchCandidatesController@talent_pool')->name('bench-candidates.talent_pool');                           	   						// NEW
        Route::get('bench-candidates/data', 'ProductCompanyBenchCandidatesController@data')->name('bench-candidates.data');                           						   						// NEW
        Route::get('bench-candidates/benchListingPoolData', 'ProductCompanyBenchCandidatesController@benchListingPoolData')->name('bench-candidates.benchListingPoolData');							// NEW
        Route::resource('bench-candidates', 'ProductCompanyBenchCandidatesController');
        Route::get('bench-candidates/benchListingData', 'ProductCompanyBenchCandidatesController@benchListingData')->name('bench-candidates.benchListingData');				   						// NEW
        Route::get('bench-candidates/hmlisting/{id?}', 'ProductCompanyBenchCandidatesController@hmlisting')->name('bench-candidates.hmlisting'); 							    					// NEW
        Route::get('bench-candidates/bench_assign_to_job/{id?}', 'ProductCompanyBenchCandidatesController@bench_assign_to_job')->name('bench-candidates.bench_assign_to_job'); 		   				// NEW
        Route::get('bench-candidates/bench_assign_listing/{id?}', 'ProductCompanyBenchCandidatesController@bench_assign_listing')->name('bench-candidates.bench_assign_listing'); 					// NEW
        Route::get('bench-candidates/public_show/{id?}', 'ProductCompanyBenchCandidatesController@public_show')->name('bench-candidates.public_show'); 							    				// NEW
        Route::get('bench-candidates/hmlistingupdate/{id?}', 'ProductCompanyBenchCandidatesController@hmlistingupdate')->name('bench-candidates.hmlistingupdate'); 									// NEW
        Route::get('bench-candidates/inner_bench_list/{id?}/{stage?}', 'ProductCompanyBenchCandidatesController@inner_bench_list')->name('bench-candidates.inner_bench_list');   					// NEW
        Route::get('bench-candidates/bench_explore/{id?}/{stage?}', 'ProductCompanyBenchCandidatesController@bench_explore')->name('bench-candidates.bench_explore');            					// NEW
        Route::get('bench-candidates/bench_explore_listing_counter/{bench_id?}/{stage?}/{inputSearch?}', 'ProductCompanyBenchCandidatesController@bench_explore_listing_counter')->name('bench-candidates.bench_explore_listing_counter');  // WORKING
        Route::get('bench-candidates/talent_pool/{id?}', 'ProductCompanyBenchCandidatesController@talent_pool')->name('bench-candidates.talent_pool');                           					// NEW
        Route::get('bench-candidates/recommend_req/{id?}', 'ProductCompanyBenchCandidatesController@recommend_req')->name('bench-candidates.recommend_req');                     					// NEW
        Route::get('bench-candidates/talent_pool/add_talent_pool/{id?}', 'ProductCompanyBenchCandidatesController@add_talent_pool')->name('bench-candidates.add_talent_pool');   					// NEW
        Route::get('bench-candidates/add_custom_detail/{counter}', 'ProductCompanyBenchCandidatesController@add_custom_detail')->name('bench-candidates.add_custom_detail'); 	 					// NEW
        Route::get('bench-candidates/bench_application_status/{id?}', 'ProductCompanyBenchCandidatesController@bench_application_status')->name('bench-candidates.bench_application_status');
        Route::get('bench-candidates/bench_application_offerstatus/{id?}', 'ProductCompanyBenchCandidatesController@bench_application_offerstatus')->name('bench-candidates.bench_application_offerstatus');
        Route::post('bench-candidates/status_submit', 'ProductCompanyBenchCandidatesController@status_submit')->name('bench-candidates.status_submit');
        Route::post('bench-candidates/update_placed_end_date', 'ProductCompanyBenchCandidatesController@update_placed_end_date')->name('bench-candidates.update_placed_end_date');
        Route::get('bench-candidates/download/{id}', 'ProductCompanyBenchCandidatesController@download')->name('bench-candidates.download');
        // #############		END BENCH CANDIDATE

        // #############       START OFFER
        Route::post('offer/refresh-date/{id}', 'ProductCompanyOfferController@refreshDate')->name('offer.refreshDate');
        Route::get('offer/data', 'ProductCompanyOfferController@data')->name('offer.data');
        Route::get('offer/application-data', 'ProductCompanyOfferController@applicationData')->name('offer.applicationData');
        Route::post('offer/send-emails', 'ProductCompanyOfferController@sendEmails')->name('offer.sendEmails');
        Route::get('offer/send-email', 'ProductCompanyOfferController@sendEmail')->name('offer.sendEmail');
        Route::resource('offer', 'ProductCompanyOfferController');
        Route::get('offer/getContactPerson/{client_id}', 'ProductCompanyOfferController@getContactPerson')->name('offer.getContactPerson'); // NEW
        Route::get('offer/lightbox/{client_id}', 'ProductCompanyOfferController@lightbox')->name('offer.lightbox'); // NEW
        Route::get('offer/loc_lightbox/{client_id}', 'ProductCompanyOfferController@loc_lightbox')->name('offer.loc_lightbox'); // NEW
        Route::get('offer/viewmore/{id?}', 'ProductCompanyOfferController@viewMore')->name('offer.viewmore'); // NEW
        Route::get('offer/load_visibility_control/{id?}', 'ProductCompanyOfferController@loadVisibilityControl')->name('offer.load_visibility_control'); // NEW
        Route::get('offer/editJobStatus/{id?}', 'ProductCompanyOfferController@editJobStatus')->name('offer.editJobStatus'); // NEW
        Route::get('offer/priority/{id?}', 'ProductCompanyOfferController@priority')->name('offer.priority'); // NEW
        Route::post('offer/priorityUpdate', 'ProductCompanyOfferController@priorityUpdate')->name('offer.priorityUpdate'); // NEW
        Route::get('offer/export/{start}/{length}', 'ProductCompanyOfferController@export')->name('offer.export'); // NEW
        Route::post('offer/updatestatus', 'ProductCompanyOfferController@updatestatus')->name('offer.updatestatus'); // NEW
        Route::post('offer/newUpdateJobstatus', 'ProductCompanyOfferController@newUpdateJobstatus')->name('offer.newUpdateJobstatus'); // NEW
        Route::post('offer/update_application', 'ProductCompanyOfferController@update_application')->name('offer.update_application'); // NEW
        // #############       END OFFER

        // #############       START OFFER
        Route::post('applicationoffer/refresh-date/{id}', 'ProductCompanyApplicationOfferController@refreshDate')->name('applicationoffer.refreshDate');
        Route::get('applicationoffer/data', 'ProductCompanyApplicationOfferController@data')->name('offer.data');
        Route::get('applicationoffer/application-data', 'ProductCompanyApplicationOfferController@applicationData')->name('applicationoffer.applicationData');
        Route::post('applicationoffer/send-emails', 'ProductCompanyApplicationOfferController@sendEmails')->name('applicationoffer.sendEmails');
        Route::get('applicationoffer/send-email', 'ProductCompanyApplicationOfferController@sendEmail')->name('applicationoffer.sendEmail');
        Route::resource('applicationoffer', 'ProductCompanyApplicationOfferController');
        Route::get('applicationoffer/getContactPerson/{client_id}', 'ProductCompanyApplicationOfferController@getContactPerson')->name('applicationoffer.getContactPerson'); // NEW
        Route::get('applicationoffer/lightbox/{client_id}', 'ProductCompanyApplicationOfferController@lightbox')->name('applicationoffer.lightbox'); // NEW
        Route::get('applicationoffer/loc_lightbox/{client_id}', 'ProductCompanyApplicationOfferController@loc_lightbox')->name('applicationoffer.loc_lightbox'); // NEW
        Route::get('applicationoffer/viewmore/{id?}', 'ProductCompanyApplicationOfferController@viewMore')->name('applicationoffer.viewmore'); // NEW
        Route::get('applicationoffer/load_visibility_control/{id?}', 'ProductCompanyApplicationOfferController@loadVisibilityControl')->name('applicationoffer.load_visibility_control'); // NEW
        Route::get('applicationoffer/editJobStatus/{id?}', 'ProductCompanyApplicationOfferController@editJobStatus')->name('applicationoffer.editJobStatus'); // NEW
        Route::get('applicationoffer/priority/{id?}', 'ProductCompanyApplicationOfferController@priority')->name('applicationoffer.priority'); // NEW
        Route::post('applicationoffer/priorityUpdate', 'ProductCompanyApplicationOfferController@priorityUpdate')->name('applicationoffer.priorityUpdate'); // NEW
        Route::get('applicationoffer/export/{start}/{length}', 'ProductCompanyApplicationOfferController@export')->name('applicationoffer.export'); // NEW
        Route::post('applicationoffer/updatestatus', 'ProductCompanyApplicationOfferController@updatestatus')->name('applicationoffer.updatestatus'); // NEW
        Route::post('applicationoffer/newUpdateJobstatus', 'ProductCompanyApplicationOfferController@newUpdateJobstatus')->name('applicationoffer.newUpdateJobstatus'); // NEW
        Route::post('applicationoffer/update_application', 'ProductCompanyApplicationOfferController@update_application')->name('applicationoffer.update_application'); // NEW
        Route::post('applicationoffer/update_salary', 'ProductCompanyApplicationOfferController@updateSalary')->name('applicationoffer.update_salary'); // NEW
        // #############       END OFFER

        // Employer Bulk Upload
        Route::get('bulkupload/download/{id}', 'ProductCompanyBulkUploadController@download')->name('bulkupload.download');
        Route::post('bulkupload/rating-save/{id?}', 'ProductCompanyBulkUploadController@ratingSave')->name('bulkupload.rating-save');
        Route::post('bulkupload/viewDetails', 'ProductCompanyBulkUploadController@viewDetails')->name('bulkupload.viewDetails');
        Route::get('bulkupload/create-schedule/{id?}', 'ProductCompanyBulkUploadController@createSchedule')->name('bulkupload.create-schedule');
        Route::post('bulkupload/store-schedule', 'ProductCompanyBulkUploadController@storeSchedule')->name('bulkupload.store-schedule');
        Route::get('bulkupload/question/{jobID}', 'ProductCompanyBulkUploadController@jobQuestion')->name('bulkupload.question');
        Route::get('bulkupload/export/{status}/{location}/{startDate}/{endDate}/{jobs}', 'ProductCompanyBulkUploadController@export')->name('bulkupload.export');
        Route::get('bulkupload/data', 'ProductCompanyBulkUploadController@data')->name('bulkupload.data');
        Route::get('bulkupload/table-view', 'ProductCompanyBulkUploadController@table')->name('bulkupload.table');
        Route::post('bulkupload/updateIndex', 'ProductCompanyBulkUploadController@updateIndex')->name('bulkupload.updateIndex');
        Route::post('bulkupload/statusupdate', 'ProductCompanyBulkUploadController@statusupdate')->name('bulkupload.statusupdate');
        Route::get('bulkupload/create/{id?}', 'ProductCompanyBulkUploadController@create')->name('bulkupload.create');
        Route::get('bulkupload/jobskill/{jobID}', 'ProductCompanyBulkUploadController@jobskill')->name('bulkupload.jobskill');
        Route::post('bulkupload/store', 'ProductCompanyBulkUploadController@store')->name('bulkupload.store');
        Route::resource('bulkupload', 'ProductCompanyBulkUploadController');

        Route::get('job-applications/download/{id}', 'ProductCompanyJobApplicationController@download')->name('job-applications.download');
        Route::post('job-applications/rating-save/{id?}', 'ProductCompanyJobApplicationController@ratingSave')->name('job-applications.rating-save');
        Route::post('job-applications/viewDetails', 'ProductCompanyJobApplicationController@viewDetails')->name('job-applications.viewDetails');
        Route::get('job-applications/create-schedule/{id?}', 'ProductCompanyJobApplicationController@createSchedule')->name('job-applications.create-schedule');

        Route::post('job-applications/store-schedule-email-template', 'ProductCompanyJobApplicationController@storeScheduleWithEmailTempate')->name('job-applications.store-schedule-email-template');
        Route::get('job-applications/question/{jobID?}/{applicationId?}', 'ProductCompanyJobApplicationController@jobQuestion')->name('job-applications.question');
        Route::get('job-applications/getAdditionalDetail/{job_id?}', 'ProductCompanyJobApplicationController@getAdditionalDetail')->name('job-applications.getAdditionalDetail');
        Route::get('job-applications/export/{status}/{location}/{startDate}/{endDate}/{jobs}', 'ProductCompanyJobApplicationController@export')->name('job-applications.export');
        Route::get('job-applications/data', 'ProductCompanyJobApplicationController@data')->name('job-applications.data');
        Route::get('job-applications/table-view', 'ProductCompanyJobApplicationController@table')->name('job-applications.table');
        Route::post('job-applications/updateIndex', 'ProductCompanyJobApplicationController@updateIndex')->name('job-applications.updateIndex');
        Route::post('job-applications/archive-job-application/{application}', 'ProductCompanyJobApplicationController@archiveJobApplication')->name('job-applications.archiveJobApplication');
        Route::post('job-applications/unarchive-job-application/{application}', 'ProductCompanyJobApplicationController@unarchiveJobApplication')->name('job-applications.unarchiveJobApplication');
        Route::post('job-applications/add-skills/{applicationId}', 'ProductCompanyJobApplicationController@addSkills')->name('job-applications.addSkills');
        Route::resource('job-applications', 'ProductCompanyJobApplicationController');
        Route::post('job-applications/statusupdate', 'ProductCompanyJobApplicationController@statusupdate')->name('job-applications.statusupdate');
        Route::get('job-applications/export/{status}/{location}/{startDate}/{endDate}/{jobs}/{start}/{length}', 'ProductCompanyJobApplicationController@export')->name('job-applications.export');
        Route::get('job-applications/load_visibility_control/{id?}', 'ProductCompanyJobApplicationController@loadVisibilityControl')->name('job-applications.load_visibility_control');
        Route::get('job-applications/create-move/{id?}', 'ProductCompanyJobApplicationController@createMove')->name('job-applications.create-move');
        Route::get('job-applications/interview-show/{id?}', 'ProductCompanyJobApplicationController@interviewShow')->name('job-applications.interview-show');
        Route::post('job-applications/store-move', 'ProductCompanyJobApplicationController@storeMove')->name('job-applications.store-move');
        Route::get('job-applications/create-copy/{id?}', 'ProductCompanyJobApplicationController@createCopy')->name('job-applications.create-copy');
        Route::post('job-applications/store-copy', 'ProductCompanyJobApplicationController@storeCopy')->name('job-applications.store-copy');
        Route::get('job-applications/updateSocurce/{id}', 'ProductCompanyJobApplicationController@updateSocurce')->name('job-applications.updateSocurce');
        Route::get('job-applications/resume_view/{id}', 'ProductCompanyJobApplicationController@resume_view')->name('job-applications.resume_view');
        Route::post('job-applications/resume_update', 'ProductCompanyJobApplicationController@resume_update')->name('job-applications.resume_update');
        Route::get('job-applications/resume_change/{id}', 'ProductCompanyJobApplicationController@resume_change')->name('job-applications.resume_change');
        Route::post('job-applications/resume_delete', 'ProductCompanyJobApplicationController@resume_delete')->name('job-applications.resume_delete');
        Route::post('job-applications/source_update', 'ProductCompanyJobApplicationController@source_update')->name('job-applications.source_update');
        Route::post('job-applications/getinterviewchild', 'ProductCompanyJobApplicationController@getinterviewchild')->name('job-applications.getinterviewchild');
        Route::post('job-applications/accessEmailTemplate', 'ProductCompanyJobApplicationController@accessEmailTemplate')->name('job-applications.accessEmailTemplate');
        Route::post('job-applications/processFormData', 'ProductCompanyJobApplicationController@processFormData')->name('job-applications.processFormData');
        Route::get('job-applications/create/{jobId?}/{appId?}', 'ProductCompanyJobApplicationController@create')->name('job-applications.create');
        Route::get('job-applications/statelist/{cid?}', 'ProductCompanyJobApplicationController@statelist')->name('job-applications.statelist');
        Route::get('job-applications/citylist', 'ProductCompanyJobApplicationController@citylist')->name('job-applications.citylist');
        Route::post('job-applications/update/{id}', 'ProductCompanyJobApplicationController@update')->name('job-applications.update');
        Route::get('job-applications/recruiter/{id?}', 'ProductCompanyJobApplicationController@recruiter')->name('job-applications.recruiter'); 					// NEW
        Route::post('job-applications/recruiter/{id?}', 'ProductCompanyJobApplicationController@recruiter')->name('job-applications.recruiter'); 				// NEW
        Route::get('job-applications/editor/{id?}', 'ProductCompanyJobApplicationController@editor')->name('job-applications.editor');
        Route::post('job-applications/store-schedule', 'ProductCompanyJobApplicationController@storeSchedule')->name('job-applications.store-schedule');
        Route::post('job-applications/emailschedule', 'ProductCompanyJobApplicationController@emailSchedule')->name('job-applications.emailschedule');

        Route::post('job-applications/getVideoUrl', 'ProductCompanyJobApplicationController@getVideoUrl')->name('job-applications.getVideoUrl');
        Route::post('job-applications/getGoogleVideoUrl', 'ProductCompanyJobApplicationController@getGoogleVideoUrl')->name('job-applications.getGoogleVideoUrl');
        Route::get('job-applications/stage_body_content/{id?}', 'ProductCompanyJobApplicationController@stage_body_content')->name('job-applications.stage_body_content');
        Route::get('job-applications/child_stage/{id?}', 'ProductCompanyJobApplicationController@child_stage')->name('job-applications.child_stage');
        Route::post('job-applications/stage_submit', 'ProductCompanyJobApplicationController@stage_submit')->name('job-applications.stage_submit');
        // Route::post('job-applications/store', 'AdminJobApplicationController@store')->name('job-applications.store');
        Route::get('applications-archive/data', 'ProductCompanyApplicationArchiveController@data')->name('applications-archive.data');
        Route::get('applications-archive/export/{skill}', 'ProductCompanyApplicationArchiveController@export')->name('applications-archive.export');
        Route::resource('applications-archive', 'ProductCompanyApplicationArchiveController');
        Route::resource('profile', 'ProductCompanyProfileController');
        Route::get('appdup', 'ProductCompanyProfileController@appdup')->name('profile.appdup');
        Route::post('appdupupdate', 'ProductCompanyProfileController@appdupupdate')->name('profile.appdupupdate');
        Route::resource('application-status', 'ProductCompanyApplicationStatusController');
        Route::get('video-meetings-settings', 'ProductCompanyProfileController@videoMeetingSettings')->name('profile.video-meetings-settings');
        Route::post('videoupdate', 'ProductCompanyProfileController@videoupdate')->name('profile.videoupdate');
        Route::get('edit-profile', 'ProductCompanyProfileController@editProfile')->name('profile.edit-profile');
        Route::get('profileedit', 'ProductCompanyProfileController@profileedit')->name('profileedit');

        // Route::post('verify-google-credentials', 'AdminProfileController@verifyGoogleCredentials')->name('profile.verify-google-credentials');
        Route::post('verify-google-credentials', 'ProductCompanyProfileController@verifyGoogleCredentials')->name('profile.verify-google-credentials');
        Route::get('verify-google-redirect/{code?}/{scope?}', 'ProductCompanyProfileController@verifyGoogleRedirect')->name('profile.verify-google-redirect');
        // Route::get('verify-google-redirect', 'AdminProfileController@verifyGoogleRedirect')->name('profile.verify-google-redirect');

        Route::get('account/google/{id?}', 'ProductCompanyAccountSettingsController@google')->name('account.google');
        Route::post('account/videoMeetingSeetingUpdate', 'ProductCompanyAccountSettingsController@videoMeetingSeetingUpdate')->name('account.videoMeetingSeetingUpdate');

        // Route::get('appdupaaa', 'ProductCompanyProfileController@appdupaaa');
        Route::get('team/data', 'ProductCompanyTeamController@data')->name('team.data');
        Route::get('team/assigndata', 'ProductCompanyTeamController@assigndata')->name('team.assigndata');
        Route::get('team/jobdata', 'ProductCompanyTeamController@jobdata')->name('team.jobdata');
        Route::get('team/intdata', 'ProductCompanyTeamController@intdata')->name('team.intdata');
        Route::get('team/joiningdata', 'ProductCompanyTeamController@joiningdata')->name('team.joiningdata');
        Route::get('team/appdata', 'ProductCompanyTeamController@appdata')->name('team.appdata');
        Route::get('team/info/{id?}', 'ProductCompanyTeamController@info')->name('team.info');
        Route::post('team/resetpassword', 'ProductCompanyTeamController@resetpassword')->name('team.resetpassword');
        Route::post('team/update/{id?}', 'ProductCompanyTeamController@update')->name('team.update');
        Route::post('team/roleupdate', 'ProductCompanyTeamController@roleupdate')->name('team.roleupdate');
        Route::post('team/change-role', 'ProductCompanyTeamController@changeRole')->name('team.changeRole');
        Route::resource('team', 'ProductCompanyTeamController');
        Route::get('team/export/{start}/{length}', 'ProductCompanyTeamController@export')->name('team.export'); // NEW
        Route::resource('applicant-note', 'ApplicantNoteController');
        Route::resource('sticky-note', 'ProductCompanyStickyNotesController');
        Route::resource('departments', 'ProductCompanyDepartmentController');
        Route::resource('designations', 'ProductCompanyDesignationController');
        Route::get('documents/data', 'ProductCompanyDocumentController@data')->name('documents.data');
        Route::get('documents/download-document/{document}', 'ProductCompanyDocumentController@downloadDoc')->name('documents.downloadDoc');
        Route::resource('documents', 'ProductCompanyDocumentController');
        Route::post('team/statusUpdate', 'ProductCompanyTeamController@statusUpdate')->name('team.statusUpdate');

        Route::post('team/WorkingStatus', 'ProductCompanyTeamController@WorkingStatus')->name('team.WorkingStatus');

        Route::get('team/viewmore/{id?}', 'ProductCompanyTeamController@viewMore')->name('team.viewmore'); // NEW
        Route::get('team/editrole/{id?}', 'ProductCompanyTeamController@editrole')->name('team.editrole'); // NEW

        // #############       START CLIENT MANAGEMENT
        Route::get('clients/data', 'ProductCompanyClientController@data')->name('clients.data');
        Route::get('clients/contactdata', 'ProductCompanyClientController@contactdata')->name('clients.contactdata');
        Route::resource('clients', 'ProductCompanyClientController');
        Route::post('clients/deleteAddress', 'ProductCompanyClientController@deleteAddress')->name('clients.deleteAddress');
        Route::get('clients/deleteAddress', 'ProductCompanyClientController@deleteAddress');
        Route::post('deleteCompanyDocument', 'ProductCompanyClientController@deletedoc')->name('deleteCompanyDocument');
        Route::get('clients/deleterecord/{id}', 'ProductCompanyClientController@deleterecord')->name('clients.deleterecord');
        Route::get('clients/download/{id}', 'ProductCompanyClientController@download')->name('clients.download');
        Route::get('clients/download/', 'ProductCompanyClientController@download')->name('clients.download');
        Route::post('clients/deleteUser', 'ProductCompanyClientController@deleteUser')->name('clients.deleteUser');
        Route::post('clients/deleteAddress', 'ProductCompanyClientController@deleteAddress')->name('clients.deleteAddress');
        Route::get('clients/edit_information/{id}', 'ProductCompanyClientController@edit_information')->name('clients.edit_information');
        Route::get('clients/edit_location/{id}', 'ProductCompanyClientController@edit_location')->name('clients.edit_location');
        Route::post('clients/update_info/{id}', 'ProductCompanyClientController@update_info')->name('clients.update_info');
        Route::post('clients/update_loc/{id}', 'ProductCompanyClientController@update_loc')->name('clients.update_loc');
        Route::post('clients/usermore/', 'ProductCompanyClientController@usermore')->name('clients.usermore');
        Route::post('clients/addressmore/', 'ProductCompanyClientController@addressmore')->name('clients.addressmore');
        Route::get('clients/export/{start}/{length}', 'ProductCompanyClientController@export')->name('clients.export');
        Route::get('clients/myapplicationlist', 'ProductCompanyClientController@myapplicationlist')->name('clients.myapplicationlist');
        Route::get('clients/viewmore/{id?}', 'ProductCompanyClientController@viewMore')->name('clients.viewmore'); // NEW
        Route::get('clients/addresslist/{cid?}', 'ProductCompanyClientController@addresslist')->name('clients.addresslist');
        Route::post('clients/update/{id}', 'ProductCompanyClientController@update')->name('clients.update');
        Route::get('clients/citylist/{state_id?}', 'ProductCompanyClientController@citylist')->name('clients.citylist');
        Route::get('clients/client_request/{id?}', 'ProductCompanyClientController@client_request')->name('clients.client_request');
        // END
        // #############       START CUSTOM REPORT MANAGEMENT
        Route::get('customreport/custom_report_filter', 'ProductCompanyCustomreportController@custom_report_filter')->name('customreport.custom_report_filter');
        Route::get('customreport/data', 'ProductCompanyCustomreportController@data')->name('customreport.data');
        Route::get('customreport/myInterviewSchedule', 'ProductCompanyCustomreportController@myInterviewSchedule')->name('customreport.myInterviewSchedule');
        Route::resource('customreport', 'ProductCompanyCustomreportController');
        Route::post('customreport/deleteAddress', 'ProductCompanyCustomreportController@deleteAddress')->name('customreport.deleteAddress');
        Route::get('customreport/deleteAddress', 'ProductCompanyCustomreportController@deleteAddress');
        // Route::post('deleteCompanyDocument', 'AdminCustomreportController@deletedoc')->name('deleteCompanyDocument');
        Route::get('customreport/deleterecord/{id}', 'ProductCompanyCustomreportController@deleterecord')->name('customreport.deleterecord');
        Route::get('customreport/download/{id}', 'ProductCompanyCustomreportController@download')->name('customreport.download');
        Route::get('customreport/download/', 'ProductCompanyCustomreportController@download')->name('customreport.download');
        Route::post('customreport/deleteUser', 'ProductCompanyCustomreportController@deleteUser')->name('customreport.deleteUser');
        Route::post('customreport/deleteAddress', 'ProductCompanyCustomreportController@deleteAddress')->name('customreport.deleteAddress');
        Route::get('customreport/edit_information/{id}', 'ProductCompanyCustomreportController@edit_information')->name('customreport.edit_information');
        Route::get('customreport/edit_location/{id}', 'ProductCompanyCustomreportController@edit_location')->name('customreport.edit_location');
        Route::post('customreport/update_info/{id}', 'ProductCompanyCustomreportController@update_info')->name('customreport.update_info');
        Route::post('customreport/update_loc/{id}', 'ProductCompanyCustomreportController@update_loc')->name('customreport.update_loc');
        Route::post('customreport/usermore/', 'ProductCompanyCustomreportController@usermore')->name('customreport.usermore');
        Route::post('customreport/addressmore/', 'ProductCompanyCustomreportController@addressmore')->name('customreport.addressmore');
        Route::get('customreport/show', 'ProductCompanyCustomreportController@show')->name('customreport.show');
        Route::get('customreport/myapplicationlist', 'ProductCompanyCustomreportController@myapplicationlist')->name('customreport.myapplicationlist');
        Route::get('customreport/export/{clients}/{jobs}/{master_status_id}/{source}/{custom_text}/{start_date}/{end_date}/{start}/{length}', 'ProductCompanyCustomreportController@export')->name('customreport.export');
        Route::get('customreport/interviewexport/{clients}/{jobs}/{master_status_id}/{source}/{custom_text}/{start_date}/{end_date}/{start}/{length}', 'ProductCompanyCustomreportController@interviewexport')->name('customreport.interviewexport');
        Route::post('customreport/chart_teamwork', 'ProductCompanyCustomreportController@chart_teamwork')->name('customreport.chart_teamwork');
        Route::get('customreport/report_filter', 'ProductCompanyCustomreportController@report_filter')->name('customreport.report_filter');
        // END
        Route::get('job-onboard/data', 'ProductCompanyJobOnboardController@data')->name('job-onboard.data');
        // Route::get('job-onboard/create', 'AdminJobOnboardController@data')->name('job-onboard.create');
        Route::get('job-onboard/send-offer/{id?}', 'ProductCompanyJobOnboardController@sendOffer')->name('job-onboard.send-offer');
        Route::get('job-onboard/update-status/{id?}', 'ProductCompanyJobOnboardController@updateStatus')->name('job-onboard.update-status');
        Route::resource('job-onboard', 'ProductCompanyJobOnboardController');
        Route::get('job-onboard/load_visibility_control/{id?}', 'ProductCompanyJobOnboardController@loadVisibilityControl')->name('job-onboard.load_visibility_control');
        Route::get('job-onboard/export/{start}/{length}', 'ProductCompanyJobOnboardController@export')->name('job-onboard.export');
        // END
        Route::get('candidateStatus/data', 'ProductCompanyCandidateStatusController@data')->name('candidateStatus.data');
        Route::resource('candidateStatus', 'ProductCompanyCandidateStatusController');
        Route::post('candidateStatus/deleteAddress', 'ProductCompanyCandidateStatusController@deleteAddress')->name('candidateStatus.deleteAddress');
        Route::get('candidateStatus/deleteAddress', 'ProductCompanyCandidateStatusController@deleteAddress');
        Route::get('candidateStatus/deleterecord/{id}', 'ProductCompanyCandidateStatusController@deleterecord')->name('candidateStatus.deleterecord');
        Route::get('candidateStatus/download/{id}', 'ProductCompanyCandidateStatusController@download')->name('candidateStatus.download');
        Route::get('candidateStatus/download/', 'ProductCompanyCandidateStatusController@download')->name('candidateStatus.download');
        Route::post('candidateStatus/deleteUser', 'ProductCompanyCandidateStatusController@deleteUser')->name('candidateStatus.deleteUser');
        Route::post('candidateStatus/deleteAddress', 'ProductCompanyCandidateStatusController@deleteAddress')->name('candidateStatus.deleteAddress');
        Route::get('candidateStatus/edit_information/{id}', 'ProductCompanyCandidateStatusController@edit_information')->name('candidateStatus.edit_information');
        Route::get('candidateStatus/edit_location/{id}', 'ProductCompanyCandidateStatusController@edit_location')->name('candidateStatus.edit_location');
        Route::post('candidateStatus/update_info/{id}', 'ProductCompanyCandidateStatusController@update_info')->name('candidateStatus.update_info');
        Route::post('candidateStatus/update_loc/{id}', 'ProductCompanyCandidateStatusController@update_loc')->name('candidateStatus.update_loc');
        Route::post('candidateStatus/usermore/', 'ProductCompanyCandidateStatusController@usermore')->name('candidateStatus.usermore');
        Route::post('candidateStatus/addressmore/', 'ProductCompanyCandidateStatusController@addressmore')->name('candidateStatus.addressmore');
        Route::post('candidateStatus/delete/', 'ProductCompanyCandidateStatusController@delete')->name('candidateStatus.delete');
        Route::get('interview-schedule/create_status/{application_id?}/{interview_id?}', 'InterviewScheduleController@create_status')->name('interview-schedule.create_status');
        Route::get('interview-schedule/create_feedbackfrm/{application_id?}/{interview_id?}/{feedback_id?}', 'InterviewScheduleController@create_feedbackfrm')->name('interview-schedule.create_feedbackfrm');
        Route::get('interview-schedule/data', 'InterviewScheduleController@data')->name('interview-schedule.data');
        Route::get('interview-schedule/interviewData', 'InterviewScheduleController@interviewData')->name('interview-schedule.interviewData');
        Route::get('interview-schedule/table-view', 'InterviewScheduleController@table')->name('interview-schedule.table-view');
        Route::post('interview-schedule/change-status', 'InterviewScheduleController@changeStatus')->name('interview-schedule.change-status');
        Route::post('interview-schedule/change-status-multiple', 'InterviewScheduleController@changeStatusMultiple')->name('interview-schedule.change-status-multiple');
        Route::get('interview-schedule/notify/{id}/{type}', 'InterviewScheduleController@notify')->name('interview-schedule.notify');
        Route::get('interview-schedule/response/{id}/{type}', 'InterviewScheduleController@employeeResponse')->name('interview-schedule.response');
        Route::resource('interview-schedule', 'InterviewScheduleController');
        Route::post('interview-schedule/updateAvailableStatus', 'InterviewScheduleController@updateAvailableStatus')->name('interview-schedule.updateAvailableStatus');
        Route::post('interview-schedule/updateFeedbackStatus', 'InterviewScheduleController@updateFeedbackStatus')->name('interview-schedule.updateFeedbackStatus');
        Route::post('interview-schedule/store_feedbackfrm', 'InterviewScheduleController@store_feedbackfrm')->name('interview-schedule.store_feedbackfrm');
        Route::post('interview-schedule/updateInterview', 'InterviewScheduleController@updateInterview')->name('interview-schedule.updateInterview');
        // Route::get('interview-schedule/feedback_create/{application_id?}/{interview_id?}/{feedback_id?}', 'InterviewScheduleController@feedback_create')->name('interview-schedule.feedback_create');
        Route::get('interview-schedule/export/{start}/{length}', 'InterviewScheduleController@export')->name('interview-schedule.export');
        Route::get('interview-schedule/showfeedback/{id?}', 'InterviewScheduleController@showfeedback')->name('interview-schedule.showfeedback'); // NEW

        // #############       START CONSULTANCY
        Route::get('consultancy/recrive', 'ProductCompanyConsultancyController@recrive')->name('consultancy.recrive');
        Route::get('consultancy/data', 'ProductCompanyConsultancyController@data')->name('consultancy.data');
        Route::get('consultancy/uncleared_request_data', 'ProductCompanyConsultancyController@uncleared_request_data')->name('consultancy.uncleared_request_data');
        Route::resource('consultancy', 'ProductCompanyConsultancyController');

        // Route::get('consultancy/uncleared_request_data', 'ProductCompanyConsultancyController@uncleared_request_data')->name('consultancy.uncleared_request_data');

        Route::get('consultancy/staffing_contact_list/{id?}', 'ProductCompanyConsultancyController@staffingContactList')->name('consultancy.staffing_contact_list');
        Route::get('consultancy/allreq/{id?}', 'ProductCompanyConsultancyController@allreq')->name('consultancy.allreq');

        Route::get('consultancy/suggestedPartners/{id?}', 'ProductCompanyConsultancyController@suggestedPartners')->name('consultancy.suggestedPartners');
        Route::get('consultancy/requestPartners/{id?}', 'ProductCompanyConsultancyController@requestPartners')->name('consultancy.requestPartners');
        Route::post('consultancy/deleteAddress', 'ProductCompanyConsultancyController@deleteAddress')->name('consultancy.deleteAddress');
        Route::get('consultancy/deleteAddress', 'ProductCompanyConsultancyController@deleteAddress');
        Route::post('deleteCompanyDocument', 'ProductCompanyConsultancyController@deletedoc')->name('deleteCompanyDocument');
        Route::get('consultancy/deleterecord/{id}', 'ProductCompanyConsultancyController@deleterecord')->name('consultancy.deleterecord');
        Route::get('consultancy/download/{id}', 'ProductCompanyConsultancyController@download')->name('consultancy.download');
        Route::get('consultancy/download/', 'ProductCompanyConsultancyController@download')->name('consultancy.download');
        Route::post('consultancy/update/{id}', 'ProductCompanyConsultancyController@update')->name('consultancy.update');
        Route::get('consultancy/load_visibility_control/{id?}', 'ProductCompanyConsultancyController@loadVisibilityControl')->name('consultancy.load_visibility_control');
        Route::get('consultancy/export/{start}/{length}', 'ProductCompanyConsultancyController@export')->name('consultancy.export'); // NEW
        Route::get('consultancy/show/{id?}', 'ProductCompanyConsultancyController@show')->name('consultancy.show'); // NEW

        Route::get('consultancy/staffing_invitation/{id?}', 'ProductCompanyConsultancyController@staffing_invitation')->name('consultancy.staffing_invitation'); // NEW
        Route::get('consultancy/dviewmore/{id?}', 'ProductCompanyConsultancyController@dviewMore')->name('consultancy.dviewmore'); // NEW

        // #############       END CONSULTANCY

        // #############       START APP TRACKER

        Route::get('apptracker/dsimilar_candidates/{id}', 'ProductCompanyAppTrackerController@dsimilar_candidates')->name('apptracker.dsimilar_candidates');
        Route::get('apptracker/dinternal_bench/{id}', 'ProductCompanyAppTrackerController@dinternal_bench')->name('apptracker.dinternal_bench');
        Route::get('apptracker/similar_candidates/{id}', 'ProductCompanyAppTrackerController@similar_candidates')->name('apptracker.similar_candidates');
        Route::get('apptracker/internal_bench/{id}', 'ProductCompanyAppTrackerController@internal_bench')->name('apptracker.internal_bench');
        Route::get('apptracker/public_bench/{id}', 'ProductCompanyAppTrackerController@public_bench')->name('apptracker.public_bench');

        Route::get('apptracker/benchReqViewMore/{id}/{job_applications_id}', 'ProductCompanyAppTrackerController@benchReqViewMore')->name('apptracker.benchReqViewMore');

        Route::get('apptracker/update_sub_stage/{status?}/{application_id?}/{job_id?}/{parent_status?}', 'ProductCompanyAppTrackerController@update_sub_stage')->name('apptracker.update_sub_stage');
        Route::get('apptracker/loadcounter/{jobid?}/{mstatus_id?}/{inputSearch?}', 'ProductCompanyAppTrackerController@loadcounter')->name('apptracker.loadcounter');

        Route::get('apptracker/sub_stage/{status?}/{application_id?}/{job_id?}', 'ProductCompanyAppTrackerController@sub_stage')->name('apptracker.sub_stage');
        Route::post('apptracker/save_rejected_resion', 'ProductCompanyAppTrackerController@save_rejected_resion')->name('apptracker.save_rejected_resion');
        Route::get('apptracker/{id}/{mstatus_id}', 'ProductCompanyAppTrackerController@index');

        Route::get('apptracker/download/{id4}', 'ProductCompanyAppTrackerController@download')->name('apptracker.download');
        Route::post('apptracker/rating-save/{id3?}', 'ProductCompanyAppTrackerController@ratingSave')->name('apptracker.rating-save');
        Route::post('apptracker/viewDetails', 'ProductCompanyAppTrackerController@viewDetails')->name('apptracker.viewDetails');
        Route::get('apptracker/create-schedule/{id2?}', 'ProductCompanyAppTrackerController@createSchedule')->name('apptracker.create-schedule');
        Route::post('apptracker/store-schedule', 'ProductCompanyAppTrackerController@storeSchedule')->name('apptracker.store-schedule');
        Route::post('apptracker/store-schedule-email-template', 'ProductCompanyAppTrackerController@storeScheduleWithEmailTempate')->name('apptracker.store-schedule-email-template');
        Route::get('apptracker/question/{jobID}', 'ProductCompanyAppTrackerController@jobQuestion')->name('apptracker.question');
        Route::get('apptracker/data', 'ProductCompanyAppTrackerController@data')->name('apptracker.data');

        Route::get('apptracker/clientappdata', 'ProductCompanyAppTrackerController@clientappdata')->name('apptracker.clientappdata');
        Route::get('apptracker/similar_existing_data', 'ProductCompanyAppTrackerController@similar_existing_data')->name('apptracker.similar_existing_data');
        Route::get('apptracker/internal_bench_data', 'ProductCompanyAppTrackerController@internal_bench_data')->name('apptracker.internal_bench_data');
        Route::get('apptracker/public_bench_data', 'ProductCompanyAppTrackerController@public_bench_data')->name('apptracker.public_bench_data');
        Route::get('apptracker/dsimilar_existing_data', 'ProductCompanyAppTrackerController@dsimilar_existing_data')->name('apptracker.dsimilar_existing_data');
        Route::get('apptracker/dinternal_bench_data', 'ProductCompanyAppTrackerController@dinternal_bench_data')->name('apptracker.dinternal_bench_data');
        Route::get('apptracker/rec_candidate_assign', 'ProductCompanyAppTrackerController@rec_candidate_assign')->name('apptracker.rec_candidate_assign');        	// NEW

        Route::get('apptracker/table-view', 'ProductCompanyAppTrackerController@table')->name('apptracker.table');
        Route::post('apptracker/updateIndex', 'ProductCompanyAppTrackerController@updateIndex')->name('apptracker.updateIndex');
        Route::post('apptracker/statusupdate', 'ProductCompanyAppTrackerController@statusupdate')->name('apptracker.statusupdate');
        Route::post('apptracker/get_client_job', 'ProductCompanyAppTrackerController@get_client_job')->name('apptracker.get_client_job');
        // Route::get('apptracker/export/{jobs}/{source}/{search_passport}/{search_np_id}/{status}', 'AdminAppTrackerController@export')->name('apptracker.export');
        Route::post('apptracker/store', 'ProductCompanyAppTrackerController@store')->name('apptracker.store');
        Route::post('apptracker/updateinformation', 'ProductCompanyAppTrackerController@updateinformation')->name('apptracker.updateinformation');
        Route::get('apptracker/export/{jobs}/{mstatus_id}/{start}/{length}', 'ProductCompanyAppTrackerController@export')->name('apptracker.export');
        Route::get('apptracker/load_visibility_control/{id?}/{mstatus_id?}', 'ProductCompanyAppTrackerController@loadVisibilityControl')->name('apptracker.load_visibility_control');
        Route::get('apptracker/openRejFrm/{application_id?}/{job_id?}/{parent_status_id?}/{current_child_id?}', 'ProductCompanyAppTrackerController@openRejFrm')->name('apptracker.openRejFrm');
        Route::get('apptracker/{id1?}/{id12}', 'ProductCompanyAppTrackerController@index')->name('apptracker');
        Route::get('apptracker/show-interviews/{id?}', 'ProductCompanyAppTrackerController@showInterviews')->name('apptracker.show-interviews');
        Route::post('apptracker/getchild', 'ProductCompanyAppTrackerController@getchild')->name('apptracker.getchild');

        Route::get('apptracker/candidates_request', 'ProductCompanyAppTrackerController@candidates_request')->name('apptracker.candidates_request');

        // admin.apptracker.showInterview
        // #############       END APP TRACKER

        // ############       START CV Tracker
        Route::get('cvtracker/copyjobtable', 'ProductCompanyCvTrackerController@copyjobtable')->name('cvtracker.copyjobtable');
        Route::get('cvtracker/create_status/{id?}', 'ProductCompanyCvTrackerController@create_status')->name('cvtracker.create_status');
        Route::post('cvtracker/getchild', 'ProductCompanyCvTrackerController@getchild')->name('cvtracker.getchild');

        Route::post('cvtracker/fwd_resume/{id?}', 'ProductCompanyCvTrackerController@fwd_resume')->name('cvtracker.fwd_resume');
        Route::post('cvtracker/email_template', 'ProductCompanyCvTrackerController@email_template')->name('cvtracker.email_template');
        Route::post('cvtracker/fwd_resume_send', 'ProductCompanyCvTrackerController@fwd_resume_send')->name('cvtracker.fwd_resume_send');

        Route::post('cvtracker/copy_current_selected_applicant', 'ProductCompanyCvTrackerController@copy_current_selected_applicant')->name('cvtracker.copy_current_selected_applicant');

        Route::post('cvtracker/app_copy', 'ProductCompanyCvTrackerController@app_copy')->name('cvtracker.app_copy');
        Route::post('cvtracker/application_copy', 'ProductCompanyCvTrackerController@application_copy')->name('cvtracker.application_copy');

        Route::get('cvtracker/jobDetailById/{id}', 'ProductCompanyCvTrackerController@jobDetailById')->name('cvtracker.jobDetailById');
        Route::post('cvtracker/statusUpdate', 'ProductCompanyCvTrackerController@statusUpdate')->name('cvtracker.statusUpdate');
        Route::get('cvtracker/resume/{id}', 'ProductCompanyCvTrackerController@resume')->name('cvtracker.resume');
        Route::get('cvtracker/download/{id}', 'ProductCompanyCvTrackerController@download')->name('cvtracker.download');
        Route::post('cvtracker/rating-save/{id?}', 'ProductCompanyCvTrackerController@ratingSave')->name('cvtracker.rating-save');
        Route::post('cvtracker/viewDetails', 'ProductCompanyCvTrackerController@viewDetails')->name('cvtracker.viewDetails');
        Route::get('cvtracker/create-schedule/{id?}', 'ProductCompanyCvTrackerController@createSchedule')->name('cvtracker.create-schedule');
        Route::post('cvtracker/store-schedule', 'ProductCompanyCvTrackerController@storeSchedule')->name('cvtracker.store-schedule');
        Route::get('cvtracker/question/{jobID}', 'ProductCompanyCvTrackerController@jobQuestion')->name('cvtracker.question');
        Route::get('cvtracker/data', 'ProductCompanyCvTrackerController@data')->name('cvtracker.data');
        Route::get('cvtracker/table-view', 'ProductCompanyCvTrackerController@table')->name('cvtracker.table');
        Route::post('cvtracker/updateIndex', 'ProductCompanyCvTrackerController@updateIndex')->name('cvtracker.updateIndex');
        Route::resource('cvtracker', 'ProductCompanyCvTrackerController');
        Route::post('cvtracker/statusupdate', 'ProductCompanyCvTrackerController@statusupdate')->name('cvtracker.statusupdate');
        Route::post('cvtracker/get_client_job', 'ProductCompanyCvTrackerController@get_client_job')->name('cvtracker.get_client_job');
        Route::post('cvtracker/hrprocess_store', 'ProductCompanyCvTrackerController@hrprocess_store')->name('cvtracker.hrprocess_store');
        Route::get('cvtracker/sehedulecreate/{id?}/{process_id?}', 'ProductCompanyCvTrackerController@sehedulecreate')->name('cvtracker.sehedulecreate');
        Route::post('cvtracker/schedulestore', 'ProductCompanyCvTrackerController@schedulestore')->name('cvtracker.schedulestore');
        Route::get('cvtracker/candidateRightSideBar/{id?}', 'ProductCompanyCvTrackerController@candidateRightSideBar')->name('cvtracker.candidateRightSideBar');
        Route::post('cvtracker/candidateRightSideBarUpdate/{id?}', 'ProductCompanyCvTrackerController@candidateRightSideBarUpdate')->name('cvtracker.candidateRightSideBarUpdate');
        Route::post('cvtracker/candidateRightSideBarUpdate_display/{id?}', 'ProductCompanyCvTrackerController@candidateRightSideBarUpdate_display')->name('cvtracker.candidateRightSideBarUpdate_display');
        Route::post('cvtracker/generateDynamicColumn/{id?}', 'ProductCompanyCvTrackerController@generateDynamicColumn')->name('cvtracker.generateDynamicColumn');
        Route::post('cvtracker/candidate_process_update', 'ProductCompanyCvTrackerController@candidate_process_update')->name('cvtracker.candidate_process_update');
        Route::get('cvtracker/load_visibility_control/{id?}', 'ProductCompanyCvTrackerController@loadVisibilityControl')->name('cvtracker.load_visibility_control');
        Route::get('cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', 'ProductCompanyCvTrackerController@export')->name('cvtracker.export');
        // ############       END CV Tracker
        // ############       START Advanced Search
        Route::get('advsearch/create_status/{id?}', 'ProductCompanyAdvancedSearchController@create_status')->name('advsearch.create_status');
        Route::post('advsearch/getchild', 'ProductCompanyAdvancedSearchController@getchild')->name('advsearch.getchild');
        Route::post('advsearch/statusUpdate', 'ProductCompanyAdvancedSearchController@statusUpdate')->name('advsearch.statusUpdate');
        Route::get('advsearch/resume/{id}', 'ProductCompanyAdvancedSearchController@resume')->name('advsearch.resume');
        Route::get('advsearch/download/{id}', 'ProductCompanyAdvancedSearchController@download')->name('advsearch.download');
        Route::post('advsearch/rating-save/{id?}', 'ProductCompanyAdvancedSearchController@ratingSave')->name('advsearch.rating-save');
        Route::post('advsearch/viewDetails', 'ProductCompanyAdvancedSearchController@viewDetails')->name('advsearch.viewDetails');
        Route::get('advsearch/create-schedule/{id?}', 'ProductCompanyAdvancedSearchController@createSchedule')->name('advsearch.create-schedule');
        Route::post('advsearch/store-schedule', 'ProductCompanyAdvancedSearchController@storeSchedule')->name('advsearch.store-schedule');
        Route::get('advsearch/question/{jobID}', 'ProductCompanyAdvancedSearchController@jobQuestion')->name('advsearch.question');
        Route::get('advsearch/data', 'ProductCompanyAdvancedSearchController@data')->name('advsearch.data');
        Route::get('advsearch/table-view', 'ProductCompanyAdvancedSearchController@table')->name('advsearch.table');
        Route::post('advsearch/updateIndex', 'ProductCompanyAdvancedSearchController@updateIndex')->name('advsearch.updateIndex');
        Route::resource('advsearch', 'ProductCompanyAdvancedSearchController');
        Route::post('advsearch/statusupdate', 'ProductCompanyAdvancedSearchController@statusupdate')->name('advsearch.statusupdate');
        Route::post('advsearch/get_client_job', 'ProductCompanyAdvancedSearchController@get_client_job')->name('advsearch.get_client_job');
        Route::post('advsearch/hrprocess_store', 'ProductCompanyAdvancedSearchController@hrprocess_store')->name('advsearch.hrprocess_store');
        Route::get('advsearch/sehedulecreate/{id?}/{process_id?}', 'ProductCompanyAdvancedSearchController@sehedulecreate')->name('advsearch.sehedulecreate');
        Route::post('advsearch/schedulestore', 'ProductCompanyAdvancedSearchController@schedulestore')->name('advsearch.schedulestore');
        Route::get('advsearch/candidateRightSideBar/{id?}', 'ProductCompanyAdvancedSearchController@candidateRightSideBar')->name('advsearch.candidateRightSideBar');
        Route::post('advsearch/candidateRightSideBarUpdate/{id?}', 'ProductCompanyAdvancedSearchController@candidateRightSideBarUpdate')->name('advsearch.candidateRightSideBarUpdate');
        Route::post('advsearch/candidateRightSideBarUpdate_display/{id?}', 'ProductCompanyAdvancedSearchController@candidateRightSideBarUpdate_display')->name('advsearch.candidateRightSideBarUpdate_display');
        Route::post('advsearch/generateDynamicColumn/{id?}', 'ProductCompanyAdvancedSearchController@generateDynamicColumn')->name('advsearch.generateDynamicColumn');
        Route::post('advsearch/candidate_process_update', 'ProductCompanyAdvancedSearchController@candidate_process_update')->name('advsearch.candidate_process_update');
        Route::get('advsearch/load_visibility_control/{id?}', 'ProductCompanyAdvancedSearchController@loadVisibilityControl')->name('advsearch.load_visibility_control');
        Route::get('advsearch/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', 'ProductCompanyAdvancedSearchController@export')->name('advsearch.export');
        // ############       END Advanced Search
        // START Job Tracker
        Route::post('job/saveClass', 'ProductCompanyJobsController@saveClass')->name('job.saveClass');
        Route::get('jobtracker/addclass/{id?}', 'ProductCompanyJobTrackerController@addclass')->name('jobtracker.addclass');
        Route::get('jobtracker/download/{id4}', 'ProductCompanyJobTrackerController@download')->name('jobtracker.download');
        Route::post('jobtracker/rating-save/{id3?}', 'ProductCompanyJobTrackerController@ratingSave')->name('jobtracker.rating-save');
        Route::post('jobtracker/viewDetails', 'ProductCompanyJobTrackerController@viewDetails')->name('jobtracker.viewDetails');
        Route::get('jobtracker/create-schedule/{id2?}', 'ProductCompanyJobTrackerController@createSchedule')->name('jobtracker.create-schedule');
        Route::post('jobtracker/store-schedule', 'ProductCompanyJobTrackerController@storeSchedule')->name('jobtracker.store-schedule');
        Route::get('jobtracker/question/{jobID}', 'ProductCompanyJobTrackerController@jobQuestion')->name('jobtracker.question');
        Route::get('jobtracker/data', 'ProductCompanyJobTrackerController@data')->name('jobtracker.data');
        Route::get('jobtracker/table-view', 'ProductCompanyJobTrackerController@table')->name('jobtracker.table');
        Route::post('jobtracker/updateIndex', 'ProductCompanyJobTrackerController@updateIndex')->name('jobtracker.updateIndex');
        Route::post('jobtracker/statusupdate', 'ProductCompanyJobTrackerController@statusupdate')->name('jobtracker.statusupdate');
        Route::post('jobtracker/get_client_job', 'ProductCompanyJobTrackerController@get_client_job')->name('jobtracker.get_client_job');
        Route::get('jobtracker/export/{jobs}/{source}/{search_passport}/{search_np_id}/{status}', 'ProductCompanyJobTrackerController@export')->name('jobtracker.export');
        Route::post('jobtracker/store', 'ProductCompanyJobTrackerController@store')->name('jobtracker.store');
        Route::post('jobtracker/updateinformation', 'ProductCompanyJobTrackerController@updateinformation')->name('jobtracker.updateinformation');
        Route::get('jobtracker/{id}', 'ProductCompanyJobTrackerController@index');
        Route::get('jobtracker/export/{jobs}/{start}/{length}', 'ProductCompanyJobTrackerController@export')->name('jobtracker.export');
        Route::get('jobtracker/load_visibility_control/{id?}', 'ProductCompanyJobTrackerController@loadVisibilityControl')->name('jobtracker.load_visibility_control');
        Route::get('jobtracker/applicaationByJobStatus/{jobid}/{status}', 'ProductCompanyJobTrackerController@applicaationByJobStatus');
        Route::get('jobtracker/applicationdata/{jobid}/{status}', 'ProductCompanyJobTrackerController@applicationdata')->name('jobtracker.applicationdata');
    });
    Route::get('paypal-recurring', ['as' => 'paypal-recurring', 'uses' => 'ProductCompanyPaypalController@payWithPaypalRecurrring']);
    Route::get('paypal-invoice-download/{id}', ['as' => 'paypal.invoice-download', 'uses' => 'ProductCompanyPaypalController@paypalInvoiceDownload']);
    // route for view/blade file
    Route::get('paywithpaypal', ['as' => 'paywithpaypal', 'uses' => 'ProductCompanyPaypalController@payWithPaypal']);
    // route for post request
    Route::get('paypal/{packageId}/{type}', ['as' => 'paypal', 'uses' => 'ProductCompanyPaypalController@paymentWithpaypal']);
    Route::get('paypal/cancel-subscription', ['as' => 'paypal.cancel-subscription', 'uses' => 'ProductCompanyPaypalController@cancelSubscription']);
    Route::get('paypal/cancel-agreement', ['as' => 'paypal.cancel-agreement', 'uses' => 'ProductCompanyPaypalController@cancelAgreement']);
    // route for check status responce
    Route::get('paypal', ['as' => 'status', 'uses' => 'ProductCompanyPaypalController@getPaymentStatus']);

    Route::get('subscribe/invoice', 'ManageSubscriptionController@invoice')->name('subscribe.invoice');

    Route::get('subscribe/historylisting', 'ManageSubscriptionController@historylisting')->name('subscribe.historylisting');
    Route::get('subscribe/viewmore/{id?}', 'ManageSubscriptionController@viewMore')->name('subscribe.viewmore');

    Route::get('subscribe/data', 'ManageSubscriptionController@data')->name('subscribe.data');
    Route::get('subscribe/history-data', 'ManageSubscriptionController@historyData')->name('subscribe.history-data');
    Route::get('subscribe/cancel-subscription/{type?}', 'ManageSubscriptionController@cancelSubscription')->name('subscribe.cancel-subscription');
    Route::post('subscribe/payment-stripe', 'ManageSubscriptionController@payment')->name('payments.stripe');
    Route::get('subscribe/select-package/{packageID}', 'ManageSubscriptionController@selectPackage')->name('subscribe.select-package');
    Route::get('subscribe/invoice-download/{invoice}', 'ManageSubscriptionController@download')->name('subscribe.invoice-download');
    Route::get('subscribe/default-invoice-download/{invoice}', 'ManageSubscriptionController@invoiceDownload')->name('subscribe.default-invoice-download');
    Route::get('subscribe/paypal-invoice-download/{id}', ['as' => 'subscribe.paypal-invoice-download', 'uses' => 'ManageSubscriptionController@paypalInvoiceDownload']);
    Route::get('subscribe/history', ['as' => 'subscribe.history', 'uses' => 'ManageSubscriptionController@history']);
    Route::get('subscribe/cart/{id?}', 'ManageSubscriptionController@cart')->name('subscribe.cart');                                    // NEW
    Route::get('subscribe/razorpay-invoice-download/{id}', 'ManageSubscriptionController@razorpayInvoiceDownload')->name('subscribe.razorpay-invoice-download');
    Route::post('subscribe/razorpay-payment', 'ManageSubscriptionController@razorpayPayment')->name('subscribe.razorpay-payment');
    Route::post('subscribe/razorpay-subscription', 'ManageSubscriptionController@razorpaySubscription')->name('subscribe.razorpay-subscription');
    Route::resource('subscribe', 'ManageSubscriptionController');

    Route::resource('auth-custom-package', 'AuthCustomPackageController');
    Route::post('auth-custom-package/validatePackage', 'AuthCustomPackageController@validatePackage')->name('auth-custom-package.validatePackage');

    Route::post('todo-items/update-todo-item', 'ProductCompanyTodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');
    Route::resource('todo-items', 'ProductCompanyTodoItemController');
    Route::resource('report', 'ProductCompanyReportController');
    Route::get('report/data', 'ProductCompanyReportController@data')->name('report.data');

});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::group(['middleware' => ['active-package']], function () {
        Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');
        Route::get('/home', 'AdminDashboardController@home')->name('home');
        Route::get('job-categories/data', 'AdminJobCategoryController@data')->name('job-categories.data');
        Route::get('job-categories/getSkills/{categoryId}', 'AdminJobCategoryController@getSkills')->name('job-categories.getSkills');
        Route::resource('job-categories', 'AdminJobCategoryController');
        Route::get('job-categories/export/{start}/{length}', 'AdminJobCategoryController@export')->name('job-categories.export'); // NEW
        // Questions
        Route::get('questions/data', 'AdminQuestionController@data')->name('questions.data');
        Route::resource('questions', 'AdminQuestionController');
        // company settings
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
            Route::resource('theme-settings', 'AdminThemeSettingsController');
            Route::resource('linkedin-settings', 'AdminLinkedInSettingsController');

            Route::get('settings/loadRole/{id?}', 'ManageRolePermissionController@loadRole')->name('settings.loadRole');
            Route::get('settings/loadEditRole/{id?}', 'ManageRolePermissionController@loadEditRole')->name('settings.loadEditRole');

            Route::post('saveRole', 'ManageRolePermissionController@saveRole')->name('settings.saveRole');
            Route::post('updateRole', 'ManageRolePermissionController@updateRole')->name('settings.updateRole');

            Route::get('show-role/{id?}', 'ManageRolePermissionController@showRole')->name('settings.showRole');

            Route::get('teamdata', 'ManageRolePermissionController@teamdata')->name('settings.teamdata');
            Route::delete('removerole/{user_id?}', 'ManageRolePermissionController@removerole')->name('settings.removerole');

        });

        Route::resource('account', 'AdminAccountSettingsController');
        Route::get('account/editProfile/{id?}', 'AdminAccountSettingsController@editProfile')->name('account.editProfile');
        Route::post('account/update/{id}', 'AdminAccountSettingsController@update')->name('account.update');
        Route::post('account/addIntStage', 'AdminAccountSettingsController@addIntStage')->name('account.addIntStage');
        Route::post('account/delIntStage', 'AdminAccountSettingsController@delIntStage')->name('account.delIntStage');
        Route::post('account/updatePriority', 'AdminAccountSettingsController@updatePriority')->name('account.updatePriority');
        Route::get('account/data', 'AdminAccountSettingsController@data')->name('account.data');
        Route::post('account/storeDept', 'AdminAccountSettingsController@storeDept')->name('account.storeDept');

        Route::get('account/zoom/{id?}', 'AdminAccountSettingsController@zoom')->name('account.zoom');

        Route::get('account/add_team_member_form/{id?}', 'AdminAccountSettingsController@add_team_member_form')->name('account.add_team_member_form');
        Route::post('account/sendinvitation', 'AdminAccountSettingsController@sendinvitation')->name('account.sendinvitation');

        Route::get('team/add_team_member_form/{id?}', 'AdminAccountSettingsController@add_team_member_form')->name('team.add_team_member_form');
        Route::post('team/sendinvitation', 'AdminAccountSettingsController@sendinvitation')->name('team.sendinvitation');

        Route::get('skills/data', 'AdminSkillsController@data')->name('skills.data');
        Route::resource('skills', 'AdminSkillsController');
        Route::get('skills/addMoreSkill/{id}', 'AdminSkillsController@addMoreSkill')->name('skills.addMoreSkill'); // NEW
        Route::post('skills/saveMoreSkills/', 'AdminSkillsController@saveMoreSkills')->name('skills.saveMoreSkills'); // NEW
        Route::get('locations/data', 'AdminLocationsController@data')->name('locations.data');
        Route::resource('locations', 'AdminLocationsController');

        // #############       START JOB
        Route::post('jobs/refresh-date/{id}', 'AdminJobsController@refreshDate')->name('jobs.refreshDate');
        Route::get('jobs/data', 'AdminJobsController@data')->name('jobs.data');
        Route::get('jobs/application-data', 'AdminJobsController@applicationData')->name('jobs.applicationData');
        Route::post('jobs/send-emails', 'AdminJobsController@sendEmails')->name('jobs.sendEmails');
        Route::get('jobs/send-email', 'AdminJobsController@sendEmail')->name('jobs.sendEmail');
        Route::resource('jobs', 'AdminJobsController');
        Route::get('jobs/getContactPerson/{client_id}', 'AdminJobsController@getContactPerson')->name('jobs.getContactPerson'); // NEW
        Route::get('jobs/lightbox/{client_id}', 'AdminJobsController@lightbox')->name('jobs.lightbox'); // NEW
        Route::get('jobs/loc_lightbox/{client_id}', 'AdminJobsController@loc_lightbox')->name('jobs.loc_lightbox'); // NEW
        Route::get('jobs/viewmore/{id?}', 'AdminJobsController@viewMore')->name('jobs.viewmore'); // NEW
        Route::get('jobs/spartnerlisting/{id?}', 'AdminJobsController@spartnerlisting')->name('jobs.spartnerlisting'); // NEW

        Route::get('jobs/load_visibility_control/{id?}', 'AdminJobsController@loadVisibilityControl')->name('jobs.load_visibility_control'); // NEW
        Route::get('jobs/editJobStatus/{id?}', 'AdminJobsController@editJobStatus')->name('jobs.editJobStatus'); // NEW
        Route::get('jobs/priority/{id?}', 'AdminJobsController@priority')->name('jobs.priority'); // NEW
        Route::post('jobs/priorityUpdate', 'AdminJobsController@priorityUpdate')->name('jobs.priorityUpdate'); // NEW
        Route::get('jobs/export/{start}/{length}', 'AdminJobsController@export')->name('jobs.export'); // NEW
        Route::post('jobs/updatestatus', 'AdminJobsController@updatestatus')->name('jobs.updatestatus'); // NEW
        Route::post('jobs/spartnerAssign', 'AdminJobsController@spartnerAssign')->name('jobs.spartnerAssign'); // NEW
        Route::post('jobs/newUpdateJobstatus', 'AdminJobsController@newUpdateJobstatus')->name('jobs.newUpdateJobstatus'); // NEW

        Route::post('jobs/update/{id}', 'AdminJobsController@update')->name('jobs.update');
        // Route::get('jobs/hiring_manager/{id?}', 'AdminJobsController@hiring_manager')->name('jobs.hiring_manager'); // NEW

        Route::get('jobs/hiring_manager/{id?}', 'AdminJobsController@hiring_manager')->name('jobs.hiring_manager'); 				// NEW
        Route::post('jobs/hiring_manager/{id?}', 'AdminJobsController@hiring_manager')->name('jobs.hiring_manager'); 				// NEW
        Route::get('jobs/recruiter/{id?}', 'AdminJobsController@recruiter')->name('jobs.recruiter'); 								// NEW
        Route::post('jobs/recruiter/{id?}', 'AdminJobsController@recruiter')->name('jobs.recruiter'); 								// NEW

        Route::get('jobs/all_jobs/{id?}', 'AdminJobsController@all_jobs')->name('jobs.all_jobs'); 								// NEW

        Route::get('jobs/teamdata', 'AdminJobsController@teamdata')->name('jobs.teamdata');

        // #############       END JOB
        // #############       START OFFER
        Route::post('offer/refresh-date/{id}', 'AdminOfferController@refreshDate')->name('offer.refreshDate');
        Route::get('offer/data', 'AdminOfferController@data')->name('offer.data');
        Route::get('offer/application-data', 'AdminOfferController@applicationData')->name('offer.applicationData');
        Route::post('offer/send-emails', 'AdminOfferController@sendEmails')->name('offer.sendEmails');
        Route::get('offer/send-email', 'AdminOfferController@sendEmail')->name('offer.sendEmail');
        Route::resource('offer', 'AdminOfferController');
        Route::get('offer/getContactPerson/{client_id}', 'AdminOfferController@getContactPerson')->name('offer.getContactPerson'); // NEW
        Route::get('offer/lightbox/{client_id}', 'AdminOfferController@lightbox')->name('offer.lightbox'); // NEW
        Route::get('offer/loc_lightbox/{client_id}', 'AdminOfferController@loc_lightbox')->name('offer.loc_lightbox'); // NEW
        Route::get('offer/viewmore/{id?}', 'AdminOfferController@viewMore')->name('offer.viewmore'); // NEW
        Route::get('offer/load_visibility_control/{id?}', 'AdminOfferController@loadVisibilityControl')->name('offer.load_visibility_control'); // NEW
        Route::get('offer/editJobStatus/{id?}', 'AdminOfferController@editJobStatus')->name('offer.editJobStatus'); // NEW
        Route::get('offer/priority/{id?}', 'AdminOfferController@priority')->name('offer.priority'); // NEW
        Route::post('offer/priorityUpdate', 'AdminOfferController@priorityUpdate')->name('offer.priorityUpdate'); // NEW
        Route::get('offer/export/{start}/{length}', 'AdminOfferController@export')->name('offer.export'); // NEW
        Route::post('offer/updatestatus', 'AdminOfferController@updatestatus')->name('offer.updatestatus'); // NEW
        Route::post('offer/newUpdateJobstatus', 'AdminOfferController@newUpdateJobstatus')->name('offer.newUpdateJobstatus'); // NEW
        Route::post('offer/update_application', 'AdminOfferController@update_application')->name('offer.update_application'); // NEW
        // #############       END OFFER

        // #############       START OFFER
        Route::post('applicationoffer/refresh-date/{id}', 'AdminApplicationOfferController@refreshDate')->name('applicationoffer.refreshDate');
        Route::get('applicationoffer/data', 'AdminApplicationOfferController@data')->name('offer.data');
        Route::get('applicationoffer/application-data', 'AdminApplicationOfferController@applicationData')->name('applicationoffer.applicationData');
        Route::post('applicationoffer/send-emails', 'AdminApplicationOfferController@sendEmails')->name('applicationoffer.sendEmails');
        Route::get('applicationoffer/send-email', 'AdminApplicationOfferController@sendEmail')->name('applicationoffer.sendEmail');
        Route::resource('applicationoffer', 'AdminApplicationOfferController');
        Route::get('applicationoffer/getContactPerson/{client_id}', 'AdminApplicationOfferController@getContactPerson')->name('applicationoffer.getContactPerson'); // NEW
        Route::get('applicationoffer/lightbox/{client_id}', 'AdminApplicationOfferController@lightbox')->name('applicationoffer.lightbox'); // NEW
        Route::get('applicationoffer/loc_lightbox/{client_id}', 'AdminApplicationOfferController@loc_lightbox')->name('applicationoffer.loc_lightbox'); // NEW
        Route::get('applicationoffer/viewmore/{id?}', 'AdminApplicationOfferController@viewMore')->name('applicationoffer.viewmore'); // NEW
        Route::get('applicationoffer/load_visibility_control/{id?}', 'AdminApplicationOfferController@loadVisibilityControl')->name('applicationoffer.load_visibility_control'); // NEW
        Route::get('applicationoffer/editJobStatus/{id?}', 'AdminApplicationOfferController@editJobStatus')->name('applicationoffer.editJobStatus'); // NEW
        Route::get('applicationoffer/priority/{id?}', 'AdminApplicationOfferController@priority')->name('applicationoffer.priority'); // NEW
        Route::post('applicationoffer/priorityUpdate', 'AdminApplicationOfferController@priorityUpdate')->name('applicationoffer.priorityUpdate'); // NEW
        Route::get('applicationoffer/export/{start}/{length}', 'AdminApplicationOfferController@export')->name('applicationoffer.export'); // NEW
        Route::post('applicationoffer/updatestatus', 'AdminApplicationOfferController@updatestatus')->name('applicationoffer.updatestatus'); // NEW
        Route::post('applicationoffer/newUpdateJobstatus', 'AdminApplicationOfferController@newUpdateJobstatus')->name('applicationoffer.newUpdateJobstatus'); // NEW
        Route::post('applicationoffer/update_application', 'AdminApplicationOfferController@update_application')->name('applicationoffer.update_application'); // NEW
        Route::post('applicationoffer/update_salary', 'AdminApplicationOfferController@updateSalary')->name('applicationoffer.update_salary'); // NEW
        // #############       END OFFER

        // Employer Bulk Upload
        Route::get('bulkupload/download/{id}', 'AdminBulkUploadController@download')->name('bulkupload.download');
        Route::post('bulkupload/rating-save/{id?}', 'AdminBulkUploadController@ratingSave')->name('bulkupload.rating-save');
        Route::post('bulkupload/viewDetails', 'AdminBulkUploadController@viewDetails')->name('bulkupload.viewDetails');
        Route::get('bulkupload/create-schedule/{id?}', 'AdminBulkUploadController@createSchedule')->name('bulkupload.create-schedule');
        Route::post('bulkupload/store-schedule', 'AdminBulkUploadController@storeSchedule')->name('bulkupload.store-schedule');
        Route::get('bulkupload/question/{jobID}', 'AdminBulkUploadController@jobQuestion')->name('bulkupload.question');
        Route::get('bulkupload/export/{status}/{location}/{startDate}/{endDate}/{jobs}', 'AdminBulkUploadController@export')->name('bulkupload.export');
        Route::get('bulkupload/data', 'AdminBulkUploadController@data')->name('bulkupload.data');
        Route::get('bulkupload/table-view', 'AdminBulkUploadController@table')->name('bulkupload.table');
        Route::post('bulkupload/updateIndex', 'AdminBulkUploadController@updateIndex')->name('bulkupload.updateIndex');
        Route::post('bulkupload/statusupdate', 'AdminBulkUploadController@statusupdate')->name('bulkupload.statusupdate');
        Route::get('bulkupload/create/{id?}', 'AdminBulkUploadController@create')->name('bulkupload.create');
        Route::get('bulkupload/jobskill/{jobID}', 'AdminBulkUploadController@jobskill')->name('bulkupload.jobskill');
        Route::post('bulkupload/store', 'AdminBulkUploadController@store')->name('bulkupload.store');
        Route::resource('bulkupload', 'AdminBulkUploadController');

        Route::get('job-applications/download/{id}', 'AdminJobApplicationController@download')->name('job-applications.download');
        Route::post('job-applications/rating-save/{id?}', 'AdminJobApplicationController@ratingSave')->name('job-applications.rating-save');
        Route::post('job-applications/viewDetails', 'AdminJobApplicationController@viewDetails')->name('job-applications.viewDetails');
        Route::get('job-applications/create-schedule/{id?}', 'AdminJobApplicationController@createSchedule')->name('job-applications.create-schedule');

        Route::post('job-applications/store-schedule-email-template', 'AdminJobApplicationController@storeScheduleWithEmailTempate')->name('job-applications.store-schedule-email-template');
        Route::get('job-applications/question/{jobID?}/{applicationId?}', 'AdminJobApplicationController@jobQuestion')->name('job-applications.question');
        Route::get('job-applications/getAdditionalDetail/{job_id?}', 'AdminJobApplicationController@getAdditionalDetail')->name('job-applications.getAdditionalDetail');
        Route::get('job-applications/export/{status}/{location}/{startDate}/{endDate}/{jobs}', 'AdminJobApplicationController@export')->name('job-applications.export');
        Route::get('job-applications/data', 'AdminJobApplicationController@data')->name('job-applications.data');
        Route::get('job-applications/table-view', 'AdminJobApplicationController@table')->name('job-applications.table');
        Route::post('job-applications/updateIndex', 'AdminJobApplicationController@updateIndex')->name('job-applications.updateIndex');
        Route::post('job-applications/archive-job-application/{application}', 'AdminJobApplicationController@archiveJobApplication')->name('job-applications.archiveJobApplication');
        Route::post('job-applications/unarchive-job-application/{application}', 'AdminJobApplicationController@unarchiveJobApplication')->name('job-applications.unarchiveJobApplication');
        Route::post('job-applications/add-skills/{applicationId}', 'AdminJobApplicationController@addSkills')->name('job-applications.addSkills');
        Route::resource('job-applications', 'AdminJobApplicationController');
        Route::post('job-applications/statusupdate', 'AdminJobApplicationController@statusupdate')->name('job-applications.statusupdate');
        Route::get('job-applications/export/{status}/{location}/{startDate}/{endDate}/{jobs}/{start}/{length}', 'AdminJobApplicationController@export')->name('job-applications.export');
        Route::get('job-applications/load_visibility_control/{id?}', 'AdminJobApplicationController@loadVisibilityControl')->name('job-applications.load_visibility_control');
        Route::get('job-applications/create-move/{id?}', 'AdminJobApplicationController@createMove')->name('job-applications.create-move');
        Route::get('job-applications/interview-show/{id?}', 'AdminJobApplicationController@interviewShow')->name('job-applications.interview-show');
        Route::post('job-applications/store-move', 'AdminJobApplicationController@storeMove')->name('job-applications.store-move');
        Route::get('job-applications/create-copy/{id?}', 'AdminJobApplicationController@createCopy')->name('job-applications.create-copy');
        Route::post('job-applications/store-copy', 'AdminJobApplicationController@storeCopy')->name('job-applications.store-copy');
        Route::get('job-applications/updateSocurce/{id}', 'AdminJobApplicationController@updateSocurce')->name('job-applications.updateSocurce');
        Route::get('job-applications/resume_view/{id}', 'AdminJobApplicationController@resume_view')->name('job-applications.resume_view');
        Route::post('job-applications/resume_update', 'AdminJobApplicationController@resume_update')->name('job-applications.resume_update');
        Route::get('job-applications/resume_change/{id}', 'AdminJobApplicationController@resume_change')->name('job-applications.resume_change');
        Route::post('job-applications/resume_delete', 'AdminJobApplicationController@resume_delete')->name('job-applications.resume_delete');
        Route::post('job-applications/source_update', 'AdminJobApplicationController@source_update')->name('job-applications.source_update');
        Route::post('job-applications/getinterviewchild', 'AdminJobApplicationController@getinterviewchild')->name('job-applications.getinterviewchild');
        Route::post('job-applications/accessEmailTemplate', 'AdminJobApplicationController@accessEmailTemplate')->name('job-applications.accessEmailTemplate');
        Route::post('job-applications/processFormData', 'AdminJobApplicationController@processFormData')->name('job-applications.processFormData');
        Route::get('job-applications/create/{jobId?}/{appId?}', 'AdminJobApplicationController@create')->name('job-applications.create');
        Route::get('job-applications/statelist/{cid?}', 'AdminJobApplicationController@statelist')->name('job-applications.statelist');

        Route::get('job-applications/citylist', 'AdminJobApplicationController@citylist')->name('job-applications.citylist');

        Route::post('job-applications/update/{id}', 'AdminJobApplicationController@update')->name('job-applications.update');
        Route::get('job-applications/recruiter/{id?}', 'AdminJobApplicationController@recruiter')->name('job-applications.recruiter'); 					// NEW
        Route::post('job-applications/recruiter/{id?}', 'AdminJobApplicationController@recruiter')->name('job-applications.recruiter'); 				// NEW
        Route::get('job-applications/editor/{id?}', 'AdminJobApplicationController@editor')->name('job-applications.editor');
        Route::post('job-applications/store-schedule', 'AdminJobApplicationController@storeSchedule')->name('job-applications.store-schedule');
        Route::post('job-applications/emailschedule', 'AdminJobApplicationController@emailSchedule')->name('job-applications.emailschedule');

        Route::post('job-applications/getVideoUrl', 'AdminJobApplicationController@getVideoUrl')->name('job-applications.getVideoUrl');
        Route::post('job-applications/getGoogleVideoUrl', 'AdminJobApplicationController@getGoogleVideoUrl')->name('job-applications.getGoogleVideoUrl');
        Route::get('job-applications/stage_body_content/{id?}', 'AdminJobApplicationController@stage_body_content')->name('job-applications.stage_body_content');
        Route::get('job-applications/child_stage/{id?}', 'AdminJobApplicationController@child_stage')->name('job-applications.child_stage');
        Route::post('job-applications/stage_submit', 'AdminJobApplicationController@stage_submit')->name('job-applications.stage_submit');

        // Route::post('job-applications/store', 'AdminJobApplicationController@store')->name('job-applications.store');
        Route::get('applications-archive/data', 'AdminApplicationArchiveController@data')->name('applications-archive.data');
        Route::get('applications-archive/export/{skill}', 'AdminApplicationArchiveController@export')->name('applications-archive.export');
        Route::resource('applications-archive', 'AdminApplicationArchiveController');
        // Company Profile / Company Dashboard (Overview + Interview Pipeline) - client-side tabs
        Route::get('company-profile', [CompanyDashboardController::class, 'index'])->name('company-profile');
        Route::resource('profile', 'AdminProfileController');
        Route::get('appdup', 'AdminProfileController@appdup')->name('profile.appdup');
        Route::post('appdupupdate', 'AdminProfileController@appdupupdate')->name('profile.appdupupdate');
        Route::resource('application-status', 'AdminApplicationStatusController');
        Route::get('video-meetings-settings', 'AdminProfileController@videoMeetingSettings')->name('profile.video-meetings-settings');
        Route::post('videoupdate', 'AdminProfileController@videoupdate')->name('profile.videoupdate');
        Route::get('edit-profile', 'AdminProfileController@editProfile')->name('profile.edit-profile');
        Route::get('profileedit', 'AdminProfileController@profileedit')->name('profileedit');

        Route::post('verify-google-credentials', 'AdminProfileController@verifyGoogleCredentials')->name('profile.verify-google-credentials');
        Route::get('verify-google-redirect/{code?}/{scope?}', 'AdminProfileController@verifyGoogleRedirect')->name('profile.verify-google-redirect');
        // Route::get('verify-google-redirect', 'AdminProfileController@verifyGoogleRedirect')->name('profile.verify-google-redirect');

        Route::get('account/google/{id?}', 'AdminAccountSettingsController@google')->name('account.google');
        Route::post('account/videoMeetingSeetingUpdate', 'AdminAccountSettingsController@videoMeetingSeetingUpdate')->name('account.videoMeetingSeetingUpdate');

        // Route::get('appdupaaa', 'AdminProfileController@appdupaaa');
        Route::get('team/data', 'AdminTeamController@data')->name('team.data');
        Route::get('team/assigndata', 'AdminTeamController@assigndata')->name('team.assigndata');
        Route::get('team/jobdata', 'AdminTeamController@jobdata')->name('team.jobdata');
        Route::get('team/intdata', 'AdminTeamController@intdata')->name('team.intdata');
        Route::get('team/joiningdata', 'AdminTeamController@joiningdata')->name('team.joiningdata');
        Route::get('team/appdata', 'AdminTeamController@appdata')->name('team.appdata');
        Route::get('team/info/{id?}', 'AdminTeamController@info')->name('team.info');
        Route::post('team/resetpassword', 'AdminTeamController@resetpassword')->name('team.resetpassword');
        Route::post('team/update/{id?}', 'AdminTeamController@update')->name('team.update');

        Route::post('team/roleupdate', 'AdminTeamController@roleupdate')->name('team.roleupdate');

        Route::post('team/change-role', 'AdminTeamController@changeRole')->name('team.changeRole');
        Route::resource('team', 'AdminTeamController');
        Route::get('team/export/{start}/{length}', 'AdminTeamController@export')->name('team.export'); // NEW
        Route::resource('applicant-note', 'ApplicantNoteController');
        Route::resource('sticky-note', 'AdminStickyNotesController');
        Route::resource('departments', 'AdminDepartmentController');
        Route::resource('designations', 'AdminDesignationController');
        Route::get('documents/data', 'AdminDocumentController@data')->name('documents.data');
        Route::get('documents/download-document/{document}', 'AdminDocumentController@downloadDoc')->name('documents.downloadDoc');
        Route::resource('documents', 'AdminDocumentController');
        Route::post('team/statusUpdate', 'AdminTeamController@statusUpdate')->name('team.statusUpdate');
        Route::get('team/viewmore/{id?}', 'AdminTeamController@viewMore')->name('team.viewmore'); // NEW
        Route::get('team/editrole/{id?}', 'AdminTeamController@editrole')->name('team.editrole'); // NEW

        // #############       START CLIENT MANAGEMENT
        Route::get('clients/data', 'AdminClientController@data')->name('clients.data');
        Route::get('clients/contactdata', 'AdminClientController@contactdata')->name('clients.contactdata');
        Route::resource('clients', 'AdminClientController');
        Route::post('clients/deleteAddress', 'AdminClientController@deleteAddress')->name('clients.deleteAddress');
        Route::get('clients/deleteAddress', 'AdminClientController@deleteAddress');
        Route::post('deleteCompanyDocument', 'AdminClientController@deletedoc')->name('deleteCompanyDocument');
        Route::get('clients/deleterecord/{id}', 'AdminClientController@deleterecord')->name('clients.deleterecord');
        Route::get('clients/download/{id}', 'AdminClientController@download')->name('clients.download');
        Route::get('clients/download/', 'AdminClientController@download')->name('clients.download');
        Route::post('clients/deleteUser', 'AdminClientController@deleteUser')->name('clients.deleteUser');
        Route::post('clients/deleteAddress', 'AdminClientController@deleteAddress')->name('clients.deleteAddress');
        Route::get('clients/edit_information/{id}', 'AdminClientController@edit_information')->name('clients.edit_information');
        Route::get('clients/edit_location/{id}', 'AdminClientController@edit_location')->name('clients.edit_location');
        Route::post('clients/update_info/{id}', 'AdminClientController@update_info')->name('clients.update_info');
        Route::post('clients/update_loc/{id}', 'AdminClientController@update_loc')->name('clients.update_loc');
        Route::post('clients/usermore/', 'AdminClientController@usermore')->name('clients.usermore');
        Route::post('clients/addressmore/', 'AdminClientController@addressmore')->name('clients.addressmore');
        Route::get('clients/export/{start}/{length}', 'AdminClientController@export')->name('clients.export');
        Route::get('clients/myapplicationlist', 'AdminClientController@myapplicationlist')->name('clients.myapplicationlist');
        Route::get('clients/viewmore/{id?}', 'AdminClientController@viewMore')->name('clients.viewmore'); // NEW
        Route::get('clients/addresslist/{cid?}', 'AdminClientController@addresslist')->name('clients.addresslist');

        Route::post('clients/update/{id}', 'AdminClientController@update')->name('clients.update');
        Route::get('clients/citylist/{state_id?}', 'AdminClientController@citylist')->name('clients.citylist');

        Route::get('clients/client_request/{id?}', 'AdminClientController@client_request')->name('clients.client_request');

        // END
        // #############       START CUSTOM REPORT MANAGEMENT
        Route::get('customreport/custom_report_filter', 'AdminCustomreportController@custom_report_filter')->name('customreport.custom_report_filter');
        Route::get('customreport/data', 'AdminCustomreportController@data')->name('customreport.data');
        Route::get('customreport/myInterviewSchedule', 'AdminCustomreportController@myInterviewSchedule')->name('customreport.myInterviewSchedule');
        Route::resource('customreport', 'AdminCustomreportController');
        Route::post('customreport/deleteAddress', 'AdminCustomreportController@deleteAddress')->name('customreport.deleteAddress');
        Route::get('customreport/deleteAddress', 'AdminCustomreportController@deleteAddress');
        // Route::post('deleteCompanyDocument', 'AdminCustomreportController@deletedoc')->name('deleteCompanyDocument');
        Route::get('customreport/deleterecord/{id}', 'AdminCustomreportController@deleterecord')->name('customreport.deleterecord');
        Route::get('customreport/download/{id}', 'AdminCustomreportController@download')->name('customreport.download');
        Route::get('customreport/download/', 'AdminCustomreportController@download')->name('customreport.download');
        Route::post('customreport/deleteUser', 'AdminCustomreportController@deleteUser')->name('customreport.deleteUser');
        Route::post('customreport/deleteAddress', 'AdminCustomreportController@deleteAddress')->name('customreport.deleteAddress');
        Route::get('customreport/edit_information/{id}', 'AdminCustomreportController@edit_information')->name('customreport.edit_information');
        Route::get('customreport/edit_location/{id}', 'AdminCustomreportController@edit_location')->name('customreport.edit_location');
        Route::post('customreport/update_info/{id}', 'AdminCustomreportController@update_info')->name('customreport.update_info');
        Route::post('customreport/update_loc/{id}', 'AdminCustomreportController@update_loc')->name('customreport.update_loc');
        Route::post('customreport/usermore/', 'AdminCustomreportController@usermore')->name('customreport.usermore');
        Route::post('customreport/addressmore/', 'AdminCustomreportController@addressmore')->name('customreport.addressmore');
        Route::get('customreport/show', 'AdminCustomreportController@show')->name('customreport.show');
        Route::get('customreport/myapplicationlist', 'AdminCustomreportController@myapplicationlist')->name('customreport.myapplicationlist');
        Route::get('customreport/export/{clients}/{jobs}/{master_status_id}/{source}/{custom_text}/{start_date}/{end_date}/{start}/{length}', 'AdminCustomreportController@export')->name('customreport.export');
        Route::get('customreport/interviewexport/{clients}/{jobs}/{master_status_id}/{source}/{custom_text}/{start_date}/{end_date}/{start}/{length}', 'AdminCustomreportController@interviewexport')->name('customreport.interviewexport');
        Route::post('customreport/chart_teamwork', 'AdminCustomreportController@chart_teamwork')->name('customreport.chart_teamwork');
        Route::get('customreport/report_filter', 'AdminCustomreportController@report_filter')->name('customreport.report_filter');
        // END

        Route::get('job-onboard/data', 'AdminJobOnboardController@data')->name('job-onboard.data');
        // Route::get('job-onboard/create', 'AdminJobOnboardController@data')->name('job-onboard.create');
        Route::get('job-onboard/send-offer/{id?}', 'AdminJobOnboardController@sendOffer')->name('job-onboard.send-offer');
        Route::get('job-onboard/update-status/{id?}', 'AdminJobOnboardController@updateStatus')->name('job-onboard.update-status');
        Route::resource('job-onboard', 'AdminJobOnboardController');
        Route::get('job-onboard/load_visibility_control/{id?}', 'AdminJobOnboardController@loadVisibilityControl')->name('job-onboard.load_visibility_control');
        Route::get('job-onboard/export/{start}/{length}', 'AdminJobOnboardController@export')->name('job-onboard.export');
        // END
        Route::get('candidateStatus/data', 'AdminCandidateStatusController@data')->name('candidateStatus.data');
        Route::resource('candidateStatus', 'AdminCandidateStatusController');
        Route::post('candidateStatus/deleteAddress', 'AdminCandidateStatusController@deleteAddress')->name('candidateStatus.deleteAddress');
        Route::get('candidateStatus/deleteAddress', 'AdminCandidateStatusController@deleteAddress');
        Route::get('candidateStatus/deleterecord/{id}', 'AdminCandidateStatusController@deleterecord')->name('candidateStatus.deleterecord');
        Route::get('candidateStatus/download/{id}', 'AdminCandidateStatusController@download')->name('candidateStatus.download');
        Route::get('candidateStatus/download/', 'AdminCandidateStatusController@download')->name('candidateStatus.download');
        Route::post('candidateStatus/deleteUser', 'AdminCandidateStatusController@deleteUser')->name('candidateStatus.deleteUser');
        Route::post('candidateStatus/deleteAddress', 'AdminCandidateStatusController@deleteAddress')->name('candidateStatus.deleteAddress');
        Route::get('candidateStatus/edit_information/{id}', 'AdminCandidateStatusController@edit_information')->name('candidateStatus.edit_information');
        Route::get('candidateStatus/edit_location/{id}', 'AdminCandidateStatusController@edit_location')->name('candidateStatus.edit_location');
        Route::post('candidateStatus/update_info/{id}', 'AdminCandidateStatusController@update_info')->name('candidateStatus.update_info');
        Route::post('candidateStatus/update_loc/{id}', 'AdminCandidateStatusController@update_loc')->name('candidateStatus.update_loc');
        Route::post('candidateStatus/usermore/', 'AdminCandidateStatusController@usermore')->name('candidateStatus.usermore');
        Route::post('candidateStatus/addressmore/', 'AdminCandidateStatusController@addressmore')->name('candidateStatus.addressmore');
        Route::post('candidateStatus/delete/', 'AdminCandidateStatusController@delete')->name('candidateStatus.delete');

        Route::get('interview-schedule/create_status/{application_id?}/{interview_id?}', 'InterviewScheduleController@create_status')->name('interview-schedule.create_status');
        Route::get('interview-schedule/create_feedbackfrm/{application_id?}/{interview_id?}/{feedback_id?}', 'InterviewScheduleController@create_feedbackfrm')->name('interview-schedule.create_feedbackfrm');
        Route::get('interview-schedule/data', 'InterviewScheduleController@data')->name('interview-schedule.data');
        Route::get('interview-schedule/interviewData', 'InterviewScheduleController@interviewData')->name('interview-schedule.interviewData');

        Route::get('interview-schedule/table-view', 'InterviewScheduleController@table')->name('interview-schedule.table-view');

        Route::post('interview-schedule/change-status', 'InterviewScheduleController@changeStatus')->name('interview-schedule.change-status');
        Route::post('interview-schedule/change-status-multiple', 'InterviewScheduleController@changeStatusMultiple')->name('interview-schedule.change-status-multiple');
        Route::get('interview-schedule/notify/{id}/{type}', 'InterviewScheduleController@notify')->name('interview-schedule.notify');
        Route::get('interview-schedule/response/{id}/{type}', 'InterviewScheduleController@employeeResponse')->name('interview-schedule.response');
        Route::resource('interview-schedule', 'InterviewScheduleController');
        Route::post('interview-schedule/updateAvailableStatus', 'InterviewScheduleController@updateAvailableStatus')->name('interview-schedule.updateAvailableStatus');
        Route::post('interview-schedule/updateFeedbackStatus', 'InterviewScheduleController@updateFeedbackStatus')->name('interview-schedule.updateFeedbackStatus');
        Route::post('interview-schedule/store_feedbackfrm', 'InterviewScheduleController@store_feedbackfrm')->name('interview-schedule.store_feedbackfrm');
        Route::post('interview-schedule/updateInterview', 'InterviewScheduleController@updateInterview')->name('interview-schedule.updateInterview');
        // Route::get('interview-schedule/feedback_create/{application_id?}/{interview_id?}/{feedback_id?}', 'InterviewScheduleController@feedback_create')->name('interview-schedule.feedback_create');
        Route::get('interview-schedule/export/{start}/{length}', 'InterviewScheduleController@export')->name('interview-schedule.export');
        Route::get('interview-schedule/showfeedback/{id?}', 'InterviewScheduleController@showfeedback')->name('interview-schedule.showfeedback'); // NEW

        // #############       START CONSULTANCY
        Route::get('consultancy/recrive', 'AdminConsultancyController@recrive')->name('consultancy.recrive');
        Route::get('consultancy/data', 'AdminConsultancyController@data')->name('consultancy.data');
        Route::resource('consultancy', 'AdminConsultancyController');
        Route::get('consultancy/suggestedPartners/{id?}', 'AdminConsultancyController@suggestedPartners')->name('consultancy.suggestedPartners');
        Route::get('consultancy/requestPartners/{id?}', 'AdminConsultancyController@requestPartners')->name('consultancy.requestPartners');

        Route::post('consultancy/deleteAddress', 'AdminConsultancyController@deleteAddress')->name('consultancy.deleteAddress');
        Route::get('consultancy/deleteAddress', 'AdminConsultancyController@deleteAddress');
        Route::post('deleteCompanyDocument', 'AdminConsultancyController@deletedoc')->name('deleteCompanyDocument');
        Route::get('consultancy/deleterecord/{id}', 'AdminConsultancyController@deleterecord')->name('consultancy.deleterecord');
        Route::get('consultancy/download/{id}', 'AdminConsultancyController@download')->name('consultancy.download');
        Route::get('consultancy/download/', 'AdminConsultancyController@download')->name('consultancy.download');
        Route::post('consultancy/update/{id}', 'AdminConsultancyController@update')->name('consultancy.update');
        Route::get('consultancy/load_visibility_control/{id?}', 'AdminConsultancyController@loadVisibilityControl')->name('consultancy.load_visibility_control');
        Route::get('consultancy/export/{start}/{length}', 'AdminConsultancyController@export')->name('consultancy.export'); // NEW
        Route::get('consultancy/show/{id?}', 'AdminConsultancyController@show')->name('consultancy.show'); // NEW

        // #############       END CONSULTANCY

        // #############       START APP TRACKER
        Route::get('apptracker/update_sub_stage/{status?}/{application_id?}/{job_id?}/{parent_status?}', 'AdminAppTrackerController@update_sub_stage')->name('apptracker.update_sub_stage');
        Route::get('apptracker/sub_stage/{status?}/{application_id?}/{job_id?}', 'AdminAppTrackerController@sub_stage')->name('apptracker.sub_stage');
        Route::post('apptracker/save_rejected_resion', 'AdminAppTrackerController@save_rejected_resion')->name('apptracker.save_rejected_resion');
        Route::get('apptracker/{id}/{mstatus_id}', 'AdminAppTrackerController@index');
        Route::get('apptracker/download/{id4}', 'AdminAppTrackerController@download')->name('apptracker.download');
        Route::post('apptracker/rating-save/{id3?}', 'AdminAppTrackerController@ratingSave')->name('apptracker.rating-save');
        Route::post('apptracker/viewDetails', 'AdminAppTrackerController@viewDetails')->name('apptracker.viewDetails');
        Route::get('apptracker/create-schedule/{id2?}', 'AdminAppTrackerController@createSchedule')->name('apptracker.create-schedule');
        Route::post('apptracker/store-schedule', 'AdminAppTrackerController@storeSchedule')->name('apptracker.store-schedule');
        Route::post('apptracker/store-schedule-email-template', 'AdminAppTrackerController@storeScheduleWithEmailTempate')->name('apptracker.store-schedule-email-template');
        Route::get('apptracker/question/{jobID}', 'AdminAppTrackerController@jobQuestion')->name('apptracker.question');
        Route::get('apptracker/data', 'AdminAppTrackerController@data')->name('apptracker.data');
        Route::get('apptracker/table-view', 'AdminAppTrackerController@table')->name('apptracker.table');
        Route::post('apptracker/updateIndex', 'AdminAppTrackerController@updateIndex')->name('apptracker.updateIndex');
        Route::post('apptracker/statusupdate', 'AdminAppTrackerController@statusupdate')->name('apptracker.statusupdate');
        Route::post('apptracker/get_client_job', 'AdminAppTrackerController@get_client_job')->name('apptracker.get_client_job');
        // Route::get('apptracker/export/{jobs}/{source}/{search_passport}/{search_np_id}/{status}', 'AdminAppTrackerController@export')->name('apptracker.export');
        Route::post('apptracker/store', 'AdminAppTrackerController@store')->name('apptracker.store');
        Route::post('apptracker/updateinformation', 'AdminAppTrackerController@updateinformation')->name('apptracker.updateinformation');
        Route::get('apptracker/export/{jobs}/{mstatus_id}/{start}/{length}', 'AdminAppTrackerController@export')->name('apptracker.export');
        Route::get('apptracker/load_visibility_control/{id?}/{mstatus_id?}', 'AdminAppTrackerController@loadVisibilityControl')->name('apptracker.load_visibility_control');
        Route::get('apptracker/openRejFrm/{application_id?}/{job_id?}/{parent_status_id?}/{current_child_id?}', 'AdminAppTrackerController@openRejFrm')->name('apptracker.openRejFrm');
        Route::get('apptracker/{id1?}/{id12}', 'AdminAppTrackerController@index')->name('apptracker');
        Route::get('apptracker/show-interviews/{id?}', 'AdminAppTrackerController@showInterviews')->name('apptracker.show-interviews');
        Route::post('apptracker/getchild', 'AdminAppTrackerController@getchild')->name('apptracker.getchild');
        // admin.apptracker.showInterview
        // #############       END APP TRACKER

        // ############       START CV Tracker
        Route::get('cvtracker/create_status/{id?}', 'AdminCvTrackerController@create_status')->name('cvtracker.create_status');
        Route::post('cvtracker/getchild', 'AdminCvTrackerController@getchild')->name('cvtracker.getchild');
        Route::post('cvtracker/statusUpdate', 'AdminCvTrackerController@statusUpdate')->name('cvtracker.statusUpdate');
        Route::get('cvtracker/resume/{id}', 'AdminCvTrackerController@resume')->name('cvtracker.resume');
        Route::get('cvtracker/download/{id}', 'AdminCvTrackerController@download')->name('cvtracker.download');
        Route::post('cvtracker/rating-save/{id?}', 'AdminCvTrackerController@ratingSave')->name('cvtracker.rating-save');
        Route::post('cvtracker/viewDetails', 'AdminCvTrackerController@viewDetails')->name('cvtracker.viewDetails');
        Route::get('cvtracker/create-schedule/{id?}', 'AdminCvTrackerController@createSchedule')->name('cvtracker.create-schedule');
        Route::post('cvtracker/store-schedule', 'AdminCvTrackerController@storeSchedule')->name('cvtracker.store-schedule');
        Route::get('cvtracker/question/{jobID}', 'AdminCvTrackerController@jobQuestion')->name('cvtracker.question');
        Route::get('cvtracker/data', 'AdminCvTrackerController@data')->name('cvtracker.data');
        Route::get('cvtracker/table-view', 'AdminCvTrackerController@table')->name('cvtracker.table');
        Route::post('cvtracker/updateIndex', 'AdminCvTrackerController@updateIndex')->name('cvtracker.updateIndex');
        Route::resource('cvtracker', 'AdminCvTrackerController');
        Route::post('cvtracker/statusupdate', 'AdminCvTrackerController@statusupdate')->name('cvtracker.statusupdate');
        Route::post('cvtracker/get_client_job', 'AdminCvTrackerController@get_client_job')->name('cvtracker.get_client_job');
        Route::post('cvtracker/hrprocess_store', 'AdminCvTrackerController@hrprocess_store')->name('cvtracker.hrprocess_store');
        Route::get('cvtracker/sehedulecreate/{id?}/{process_id?}', 'AdminCvTrackerController@sehedulecreate')->name('cvtracker.sehedulecreate');
        Route::post('cvtracker/schedulestore', 'AdminCvTrackerController@schedulestore')->name('cvtracker.schedulestore');
        Route::get('cvtracker/candidateRightSideBar/{id?}', 'AdminCvTrackerController@candidateRightSideBar')->name('cvtracker.candidateRightSideBar');
        Route::post('cvtracker/candidateRightSideBarUpdate/{id?}', 'AdminCvTrackerController@candidateRightSideBarUpdate')->name('cvtracker.candidateRightSideBarUpdate');
        Route::post('cvtracker/candidateRightSideBarUpdate_display/{id?}', 'AdminCvTrackerController@candidateRightSideBarUpdate_display')->name('cvtracker.candidateRightSideBarUpdate_display');
        Route::post('cvtracker/generateDynamicColumn/{id?}', 'AdminCvTrackerController@generateDynamicColumn')->name('cvtracker.generateDynamicColumn');
        Route::post('cvtracker/candidate_process_update', 'AdminCvTrackerController@candidate_process_update')->name('cvtracker.candidate_process_update');
        Route::get('cvtracker/load_visibility_control/{id?}', 'AdminCvTrackerController@loadVisibilityControl')->name('cvtracker.load_visibility_control');
        Route::get('cvtracker/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', 'AdminCvTrackerController@export')->name('cvtracker.export');
        // ############       END CV Tracker

        // ############       START Advanced Search
        Route::get('advsearch/create_status/{id?}', 'AdminAdvancedSearchController@create_status')->name('advsearch.create_status');
        Route::post('advsearch/getchild', 'AdminAdvancedSearchController@getchild')->name('advsearch.getchild');
        Route::post('advsearch/statusUpdate', 'AdminAdvancedSearchController@statusUpdate')->name('advsearch.statusUpdate');
        Route::get('advsearch/resume/{id}', 'AdminAdvancedSearchController@resume')->name('advsearch.resume');
        Route::get('advsearch/download/{id}', 'AdminAdvancedSearchController@download')->name('advsearch.download');
        Route::post('advsearch/rating-save/{id?}', 'AdminAdvancedSearchController@ratingSave')->name('advsearch.rating-save');
        Route::post('advsearch/viewDetails', 'AdminAdvancedSearchController@viewDetails')->name('advsearch.viewDetails');
        Route::get('advsearch/create-schedule/{id?}', 'AdminAdvancedSearchController@createSchedule')->name('advsearch.create-schedule');
        Route::post('advsearch/store-schedule', 'AdminAdvancedSearchController@storeSchedule')->name('advsearch.store-schedule');
        Route::get('advsearch/question/{jobID}', 'AdminAdvancedSearchController@jobQuestion')->name('advsearch.question');
        Route::get('advsearch/data', 'AdminAdvancedSearchController@data')->name('advsearch.data');
        Route::get('advsearch/table-view', 'AdminAdvancedSearchController@table')->name('advsearch.table');
        Route::post('advsearch/updateIndex', 'AdminAdvancedSearchController@updateIndex')->name('advsearch.updateIndex');
        Route::resource('advsearch', 'AdminAdvancedSearchController');
        Route::post('advsearch/statusupdate', 'AdminAdvancedSearchController@statusupdate')->name('advsearch.statusupdate');
        Route::post('advsearch/get_client_job', 'AdminAdvancedSearchController@get_client_job')->name('advsearch.get_client_job');
        Route::post('advsearch/hrprocess_store', 'AdminAdvancedSearchController@hrprocess_store')->name('advsearch.hrprocess_store');
        Route::get('advsearch/sehedulecreate/{id?}/{process_id?}', 'AdminAdvancedSearchController@sehedulecreate')->name('advsearch.sehedulecreate');
        Route::post('advsearch/schedulestore', 'AdminAdvancedSearchController@schedulestore')->name('advsearch.schedulestore');
        Route::get('advsearch/candidateRightSideBar/{id?}', 'AdminAdvancedSearchController@candidateRightSideBar')->name('advsearch.candidateRightSideBar');
        Route::post('advsearch/candidateRightSideBarUpdate/{id?}', 'AdminAdvancedSearchController@candidateRightSideBarUpdate')->name('advsearch.candidateRightSideBarUpdate');
        Route::post('advsearch/candidateRightSideBarUpdate_display/{id?}', 'AdminAdvancedSearchController@candidateRightSideBarUpdate_display')->name('advsearch.candidateRightSideBarUpdate_display');
        Route::post('advsearch/generateDynamicColumn/{id?}', 'AdminAdvancedSearchController@generateDynamicColumn')->name('advsearch.generateDynamicColumn');
        Route::post('advsearch/candidate_process_update', 'AdminAdvancedSearchController@candidate_process_update')->name('advsearch.candidate_process_update');
        Route::get('advsearch/load_visibility_control/{id?}', 'AdminAdvancedSearchController@loadVisibilityControl')->name('advsearch.load_visibility_control');
        Route::get('advsearch/export/{clients}/{jobs}/{source}/{search_passport}/{search_np_id}/{status}/{start}/{length}', 'AdminAdvancedSearchController@export')->name('advsearch.export');
        // ############       END Advanced Search

        // START Job Tracker
        Route::post('job/saveClass', 'AdminJobsController@saveClass')->name('job.saveClass');
        Route::get('jobtracker/addclass/{id?}', 'AdminJobTrackerController@addclass')->name('jobtracker.addclass');
        Route::get('jobtracker/download/{id4}', 'AdminJobTrackerController@download')->name('jobtracker.download');
        Route::post('jobtracker/rating-save/{id3?}', 'AdminJobTrackerController@ratingSave')->name('jobtracker.rating-save');
        Route::post('jobtracker/viewDetails', 'AdminJobTrackerController@viewDetails')->name('jobtracker.viewDetails');
        Route::get('jobtracker/create-schedule/{id2?}', 'AdminJobTrackerController@createSchedule')->name('jobtracker.create-schedule');
        Route::post('jobtracker/store-schedule', 'AdminJobTrackerController@storeSchedule')->name('jobtracker.store-schedule');
        Route::get('jobtracker/question/{jobID}', 'AdminJobTrackerController@jobQuestion')->name('jobtracker.question');
        Route::get('jobtracker/data', 'AdminJobTrackerController@data')->name('jobtracker.data');
        Route::get('jobtracker/table-view', 'AdminJobTrackerController@table')->name('jobtracker.table');
        Route::post('jobtracker/updateIndex', 'AdminJobTrackerController@updateIndex')->name('jobtracker.updateIndex');
        Route::post('jobtracker/statusupdate', 'AdminJobTrackerController@statusupdate')->name('jobtracker.statusupdate');
        Route::post('jobtracker/get_client_job', 'AdminJobTrackerController@get_client_job')->name('jobtracker.get_client_job');
        Route::get('jobtracker/export/{jobs}/{source}/{search_passport}/{search_np_id}/{status}', 'AdminJobTrackerController@export')->name('jobtracker.export');
        Route::post('jobtracker/store', 'AdminJobTrackerController@store')->name('jobtracker.store');
        Route::post('jobtracker/updateinformation', 'AdminJobTrackerController@updateinformation')->name('jobtracker.updateinformation');
        Route::get('jobtracker/{id}', 'AdminJobTrackerController@index');
        Route::get('jobtracker/export/{jobs}/{start}/{length}', 'AdminJobTrackerController@export')->name('jobtracker.export');
        Route::get('jobtracker/load_visibility_control/{id?}', 'AdminJobTrackerController@loadVisibilityControl')->name('jobtracker.load_visibility_control');
        Route::get('jobtracker/applicaationByJobStatus/{jobid}/{status}', 'AdminJobTrackerController@applicaationByJobStatus');
        Route::get('jobtracker/applicationdata/{jobid}/{status}', 'AdminJobTrackerController@applicationdata')->name('jobtracker.applicationdata');
    });
    Route::get('paypal-recurring', ['as' => 'paypal-recurring', 'uses' => 'AdminPaypalController@payWithPaypalRecurrring']);
    Route::get('paypal-invoice-download/{id}', ['as' => 'paypal.invoice-download', 'uses' => 'AdminPaypalController@paypalInvoiceDownload']);
    // route for view/blade file
    Route::get('paywithpaypal', ['as' => 'paywithpaypal', 'uses' => 'AdminPaypalController@payWithPaypal']);
    // route for post request
    Route::get('paypal/{packageId}/{type}', ['as' => 'paypal', 'uses' => 'AdminPaypalController@paymentWithpaypal']);
    Route::get('paypal/cancel-subscription', ['as' => 'paypal.cancel-subscription', 'uses' => 'AdminPaypalController@cancelSubscription']);
    Route::get('paypal/cancel-agreement', ['as' => 'paypal.cancel-agreement', 'uses' => 'AdminPaypalController@cancelAgreement']);
    // route for check status responce
    Route::get('paypal', ['as' => 'status', 'uses' => 'AdminPaypalController@getPaymentStatus']);
    Route::get('subscribe/invoice', 'ManageSubscriptionController@invoice')->name('subscribe.invoice');
    Route::get('subscribe/data', 'ManageSubscriptionController@data')->name('subscribe.data');
    Route::get('subscribe/history-data', 'ManageSubscriptionController@historyData')->name('subscribe.history-data');
    Route::get('subscribe/cancel-subscription/{type?}', 'ManageSubscriptionController@cancelSubscription')->name('subscribe.cancel-subscription');
    Route::post('subscribe/payment-stripe', 'ManageSubscriptionController@payment')->name('payments.stripe');
    Route::get('subscribe/select-package/{packageID}', 'ManageSubscriptionController@selectPackage')->name('subscribe.select-package');
    Route::get('subscribe/invoice-download/{invoice}', 'ManageSubscriptionController@download')->name('subscribe.invoice-download');
    Route::get('subscribe/default-invoice-download/{invoice}', 'ManageSubscriptionController@invoiceDownload')->name('subscribe.default-invoice-download');
    Route::get('subscribe/paypal-invoice-download/{id}', ['as' => 'subscribe.paypal-invoice-download', 'uses' => 'ManageSubscriptionController@paypalInvoiceDownload']);
    Route::get('subscribe/history', ['as' => 'subscribe.history', 'uses' => 'ManageSubscriptionController@history']);
    Route::get('subscribe/razorpay-invoice-download/{id}', 'ManageSubscriptionController@razorpayInvoiceDownload')->name('subscribe.razorpay-invoice-download');
    Route::post('subscribe/razorpay-payment', 'ManageSubscriptionController@razorpayPayment')->name('subscribe.razorpay-payment');
    Route::post('subscribe/razorpay-subscription', 'ManageSubscriptionController@razorpaySubscription')->name('subscribe.razorpay-subscription');
    Route::resource('subscribe', 'ManageSubscriptionController');
    Route::post('todo-items/update-todo-item', 'AdminTodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');
    Route::resource('todo-items', 'AdminTodoItemController');
    Route::resource('report', 'AdminReportController');
    Route::get('report/data', 'AdminReportController@data')->name('report.data');
});

// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/send-notification', [NotificationController::class, 'send'])->name('send.notification');

// dd(collect(Route::getRoutes())->pluck('action.as')->filter()->duplicates());

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/service.php';
require __DIR__.'/superadmin.php';
// require __DIR__.'/auth.php';
