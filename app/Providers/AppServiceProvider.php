<?php

namespace App\Providers;

use App\Actions\Auth\AttemptLoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\ShowLoginFormAction;
use App\Actions\Registration\BootstrapTenantDefaultsAction;
use App\Actions\Registration\CreateCompanyAction;
use App\Actions\Registration\CreateCompanyUserAction;
use App\Actions\Registration\IssueVerificationTokenAction;
use App\Actions\Registration\SeedCoinLedgerAction;
use App\Actions\Registration\SeedCommercialRatesAction;
use App\Events\CompanyRegistered;
use App\Listeners\IndexNewCompany;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerActions();
        $this->app->bind(
            \App\Services\ForwardResume\Providers\ForwardResumeProvider::class,
            \App\Services\ForwardResume\Providers\SesForwardResumeProvider::class
        );
    }

    /**
     * Sprint 5.8 / Phase 2 — scoped() gives each Octane request a fresh Action instance (ADR-V2R-11).
     * Auth actions added in V2_login Phase 2 (docs/V2_login/IMPLEMENTATION_PLAN.md §2.1).
     */
    private function registerActions(): void
    {
        // Auth actions (V2_login Phase 2)
        $this->app->scoped(AttemptLoginAction::class);
        $this->app->scoped(ShowLoginFormAction::class);
        $this->app->scoped(LogoutAction::class);

        // Registration actions (Sprint 5.8)
        $this->app->scoped(CreateCompanyAction::class);
        $this->app->scoped(CreateCompanyUserAction::class);
        $this->app->scoped(BootstrapTenantDefaultsAction::class);
        $this->app->scoped(SeedCoinLedgerAction::class);
        $this->app->scoped(SeedCommercialRatesAction::class);
        $this->app->scoped(IssueVerificationTokenAction::class);
    }

    public function boot(): void
    {
        foreach (config('constants') as $key => $value) {
            if (! defined($key)) {
                define($key, $value);
            }
        }

        $this->configureRateLimiters();
        $this->registerObservers();
        // Sprint 5.8 — event → listener wiring (no EventServiceProvider in L11 default scaffold).
        Event::listen(CompanyRegistered::class, IndexNewCompany::class);
        Event::listen(
            \App\Events\TenantEntityDeactivatedDueToCoins::class,
            \App\Listeners\SyncElasticStatusOnCoinDeactivationListener::class
        );

        // V2_Perf-10K Phase 3 — Enforce N+1 query discipline in development/testing environments
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
    }

    private function registerObservers(): void
    {
        \App\Models\Company::observe(\App\Observers\CompanyObserver::class);
        \App\Models\Job::observe(\App\Observers\JobObserver::class);
        \App\Models\JobApplication::observe(\App\Observers\JobApplicationObserver::class);
        \App\Models\NoticePeriod::observe(\App\Observers\NoticePeriodCacheInvalidateObserver::class);
        \App\Models\BenchApplication::observe(\App\Observers\BenchApplicationObserver::class);
        \App\Models\RecommendationRelation::observe(\App\Observers\RecommendationRelationObserver::class);
        \App\Models\JobSkill::observe(\App\Observers\JobSkillObserver::class);
        \App\Models\JobCategory::observe(\App\Observers\JobCategoryObserver::class);
        \App\Models\Client::observe(\App\Observers\ClientObserver::class);
        if (class_exists(\App\Models\InterviewSchedule::class)) {
            \App\Models\InterviewSchedule::observe(\App\Observers\InterviewTrackerObserver::class);
        }
    }

    /**
     * Named rate limiters for the resume upload v2 subsystem and the registration flow.
     *
     * All limits are read from config/resumes.php so they can be tuned
     * without a deploy. Fail-open design: Redis outage → pass through + log.
     */
    private function configureRateLimiters(): void
    {
        // Registration submit: 10 attempts per hour per IP.
        // Prevents bot farms from hammering the 80-insert / 30-ES-write companyRegister() path.
        RateLimiter::for('register-submit', fn (Request $request) => Limit::perHour(10)->by('register-submit:'.$request->ip())
        );

        // Registration keystroke probes (email / phone / domain checks): 60 per minute per IP.
        // Prevents customer-enumeration at scale while allowing normal typing-speed usage.
        RateLimiter::for('register-form', fn (Request $request) => Limit::perMinute(60)->by('register-form:'.$request->ip())
        );

        // Verification resend: 3/hr per user-id + 10/hr per IP (audit H1 / Sprint 2.10).
        // Dual limits: per-user prevents targeting a specific account; per-IP caps bot farms.
        // Token is rotated on every resend so 3-attempt cap is acceptable.
        RateLimiter::for('register-resend', fn (Request $request) => [
            Limit::perHour(3)->by('register-resend:uid:'.$request->route('id')),
            Limit::perHour(10)->by('register-resend:ip:'.$request->ip()),
        ]);

        // Presign endpoint: 3/min per recruiter only — no company-level cap.
        // Fail-open on Redis outage so infra issues never block recruiters.
        RateLimiter::for('resume-presign', function (Request $request) {
            try {
                $userId = optional($request->user())->id ?? 'anon';

                return Limit::perMinute(config('resumes.rate_limits.presign_per_user', 3))
                    ->by('resume-presign:user:'.$userId)
                    ->response(fn () => response()->json([
                        'error' => 'rate_limit_exceeded',
                        'message' => 'You may upload up to '.config('resumes.rate_limits.presign_per_user', 3).' resumes per minute. Please wait.',
                        'retry_after' => 60,
                    ], 429));
            } catch (\Throwable $e) {
                // Redis is down — allow through, never block recruiters for infra issues
                Log::warning('jobs.resume.ratelimit_fails_open', [
                    'limiter' => 'resume-presign',
                    'exception' => $e::class,
                    'user_id' => optional($request->user())->id,
                ]);

                return Limit::none();
            }
        });

        // Finalize: one per upload, generous for retries
        RateLimiter::for('resume-finalize', function (Request $request) {
            $userId = optional($request->user())->id ?? 'anon';

            return Limit::perMinute(config('resumes.rate_limits.finalize_per_user', 10))
                ->by('resume-finalize:user:'.$userId);
        });

        // Status reads: polling is expected; very generous
        RateLimiter::for('resume-status', function (Request $request) {
            $userId = optional($request->user())->id ?? 'anon';

            return Limit::perMinute(config('resumes.rate_limits.status_per_user', 60))
                ->by('resume-status:user:'.$userId);
        });

        // Phase 3.8: super-admin bulk-ops (block/unblock company, toggle user status).
        // 10 per minute per super-admin prevents accidental or scripted mass-toggle.
        RateLimiter::for('superadmin-bulk-ops', fn (Request $request) => Limit::perMinute(10)->by('superadmin-bulk-ops:'.(optional($request->user())->id ?? 'anon'))
        );

        /* Intent: Rate limit public bench candidate listing route to 300 requests/minute per IP to prevent bot floods and DDoS attacks */
        RateLimiter::for('bench-public', fn (Request $request) => Limit::perMinute(300)->by('bench-public:'.$request->ip())
        );
    }
}
