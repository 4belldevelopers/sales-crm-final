<?php

namespace App\Providers;

use App\Logging\ResumeLogContext;
use Illuminate\Support\ServiceProvider;
use Monolog\LogRecord;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Phase 6 — register a Monolog processor that runs on EVERY log
     * record (across all channels) to inject pod_id and hostname
     * into the extra payload. This is how cross-pod log aggregation
     * finally gets a stable "which pod said this" answer without
     * forcing every log call site to manually append metadata.
     *
     * Resume-specific context (queue, worker) is bound per-job via
     * ResumeLogContext::initJob(); it lands in context(), not extra,
     * which is correct for Monolog 3.
     */
    public function boot(): void
    {
        foreach (config('constants') as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }
        // Phase 6 — global stderr/channel-agnostic pod stamping.
        // pushProcessor() applies the function to every record on
        // every channel. The function is idempotent: if pod_id is
        // already set (initJob ran first), we keep that value.
        $this->app['log']->pushProcessor(function (LogRecord $record): LogRecord {
            $extra = $record->extra;
            if (! isset($extra['pod_id'])) {
                $extra['pod_id'] = ResumeLogContext::podId();
            }
            if (! isset($extra['hostname'])) {
                $extra['hostname'] = ResumeLogContext::hostName();
            }
            return $record->with(extra: $extra);
        });
    }
}
