<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach (config('constants') as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }

        $this->configureRateLimiters();
        $this->registerObservers();
    }

    private function registerObservers(): void
    {
        \App\Models\Job::observe(\App\Observers\JobObserver::class);
        \App\Models\JobApplication::observe(\App\Observers\JobApplicationObserver::class);
        \App\Models\NoticePeriod::observe(\App\Observers\NoticePeriodCacheInvalidateObserver::class);
        \App\Models\BenchApplication::observe(\App\Observers\BenchApplicationObserver::class);
        \App\Models\RecommendationRelation::observe(\App\Observers\RecommendationRelationObserver::class);
        \App\Models\JobSkill::observe(\App\Observers\JobSkillObserver::class);
        \App\Models\JobCategory::observe(\App\Observers\JobCategoryObserver::class);
        \App\Models\Client::observe(\App\Observers\ClientObserver::class);
    }

    /**
     * Named rate limiters for the resume upload v2 subsystem.
     *
     * All limits are read from config/resumes.php so they can be tuned
     * without a deploy. Fail-open design: Redis outage → pass through + log.
     */
    private function configureRateLimiters(): void
    {
        // Presign endpoint: 3/min per recruiter only — no company-level cap.
        // Fail-open on Redis outage so infra issues never block recruiters.
        RateLimiter::for('resume-presign', function (Request $request) {
            try {
                $userId = optional($request->user())->id ?? 'anon';

                return Limit::perMinute(config('resumes.rate_limits.presign_per_user', 3))
                            ->by('resume-presign:user:' . $userId)
                            ->response(fn () => response()->json([
                                'error'       => 'rate_limit_exceeded',
                                'message'     => 'You may upload up to ' . config('resumes.rate_limits.presign_per_user', 3) . ' resumes per minute. Please wait.',
                                'retry_after' => 60,
                            ], 429));
            } catch (\Throwable $e) {
                // Redis is down — allow through, never block recruiters for infra issues
                Log::warning('jobs.resume.ratelimit_fails_open', [
                    'limiter'   => 'resume-presign',
                    'exception' => $e::class,
                    'user_id'   => optional($request->user())->id,
                ]);

                return Limit::none();
            }
        });

        // Finalize: one per upload, generous for retries
        RateLimiter::for('resume-finalize', function (Request $request) {
            $userId = optional($request->user())->id ?? 'anon';
            return Limit::perMinute(config('resumes.rate_limits.finalize_per_user', 10))
                        ->by('resume-finalize:user:' . $userId);
        });

        // Status reads: polling is expected; very generous
        RateLimiter::for('resume-status', function (Request $request) {
            $userId = optional($request->user())->id ?? 'anon';
            return Limit::perMinute(config('resumes.rate_limits.status_per_user', 60))
                        ->by('resume-status:user:' . $userId);
        });
    }
}
