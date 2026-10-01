<?php

namespace App\Services;

use App\Models\ApplicationStatus;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Thin cache layer for global reference lookups that are hit on every
 * AppTracker and JobCreation page load.
 *
 * All data here is truly global (no company_id scope) and changes rarely,
 * so a 24-hour TTL is safe. Each method is safe to call many times per
 * request — the second call returns the in-process PHP value (Cache returns
 * from APC / memory on warm hits, not a new DB round-trip).
 *
 * Cache keys follow the pattern: global_{resource}
 * Bust manually via Cache::forget('global_{resource}') when seeding reference
 * data (e.g. adding a new ApplicationStatus row via the admin panel).
 */
class ReferenceDataCache
{
    private const TTL_24H = 86400;

    /**
     * All ApplicationStatus rows, cached 24 h.
     * Replaces repeated `ApplicationStatus::all()` calls.
     */
    public static function applicationStatuses(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_application_statuses', self::TTL_24H, function () {
            return ApplicationStatus::all();
        });
    }

    /**
     * All TicketStatus rows, cached 24 h.
     * Replaces `TicketStatus::all()` in ticketshow().
     */
    public static function ticketStatuses(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_ticket_statuses', self::TTL_24H, function () {
            return TicketStatus::all();
        });
    }

    /**
     * qualification_master rows (excluding id=60), sorted by display_order.
     * Replaces the repeated `DB::table('qualification_master')` queries in
     * all 8 data-ajax tab methods and the job-creation form.
     */
    public static function qualifications(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_qualification_master', self::TTL_24H, function () {
            return DB::table('qualification_master')
                ->select('*')
                ->whereNotIn('id', [60])
                ->orderBy('display_order', 'ASC')
                ->get();
        });
    }

    /**
     * Qualification names as a flat Collection — used for Tagify inputs.
     * Derived in-memory from qualifications() so it shares the same cache entry.
     */
    public static function qualificationNames(): \Illuminate\Support\Collection
    {
        return static::qualifications()->pluck('name');
    }

    /**
     * feedback_status keyed array from interview_sub_status, cached 24 h.
     * Replaces GeneralMethod::feedback_status() on every data-ajax call.
     */
    public static function feedbackStatus(): array
    {
        return Cache::remember('global_feedback_status', self::TTL_24H, function () {
            $rows = DB::table('interview_sub_status')
                ->where('status', 1)
                ->get();

            $result = [];
            foreach ($rows as $row) {
                $result[$row->id] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'classname' => $row->classname,
                ];
            }

            return $result;
        });
    }

    /**
     * Master languages, cached 24 h.
     */
    public static function masterLanguages(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_languages', self::TTL_24H, function () {
            return DB::table('master_languages')->select('*')->orderBy('name', 'ASC')->get();
        });
    }

    /**
     * Master timezones, cached 24 h.
     */
    public static function masterTimezones(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_timezones', self::TTL_24H, function () {
            return DB::table('master_timezone')->select('*')->orderByDesc('priority')->get();
        });
    }

    /**
     * Master currencies, cached 24 h.
     */
    public static function masterCurrencies(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_currencies', self::TTL_24H, function () {
            return DB::table('master_currency')->select('*')->orderBy('country', 'ASC')->get();
        });
    }

    /**
     * Master time formats, cached 24 h.
     */
    public static function masterTimeFormats(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_time_formats', self::TTL_24H, function () {
            return DB::table('master_time_format')->select('*')->orderBy('priority', 'ASC')->get();
        });
    }

    /**
     * Master date formats, cached 24 h.
     */
    public static function masterDateFormats(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_date_formats', self::TTL_24H, function () {
            return DB::table('master_date_format')->select('*')->orderBy('priority', 'ASC')->get();
        });
    }

    /**
     * Master email preferences, cached 24 h.
     */
    public static function masterEmailPreferences(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_email_preferences', self::TTL_24H, function () {
            return DB::table('master_email_preferences')->select('*')->orderBy('priority', 'ASC')->get();
        });
    }

    /**
     * All Country models, cached 24 h.
     */
    public static function masterCountries(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_countries', self::TTL_24H, function () {
            return \App\Models\Country::all();
        });
    }

    /**
     * Master statuses, cached 24 h.
     */
    public static function masterStatuses(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_statuses', self::TTL_24H, function () {
            return DB::table('master_status')->select('*')->orderBy('priority', 'ASC')->get();
        });
    }

    /**
     * Elastic Master Notifications, cached 24 h.
     */
    public static function masterNotificationsElastic(ElasticService $elasticService): \Illuminate\Support\Collection
    {
        return Cache::remember('global_master_notifications_elastic', self::TTL_24H, function () use ($elasticService) {
            try {
                $params = [
                    'index' => ELASTIC_MASTER_NOTIFICATIONS,
                    'body' => ['size' => 10000],
                ];
                $response = $elasticService->getClient()->search($params);

                return collect($response['hits']['hits'] ?? [])->map(function ($hit) {
                    return (object) $hit['_source'];
                });
            } catch (\Exception $e) {
                return collect([]);
            }
        });
    }

    /**
     * Interview modes, cached 24 h.
     * Global reference lookup for interview modes.
     */
    public static function interviewModes(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_interview_modes', self::TTL_24H, function () {
            return DB::table('interview_mode')->select('*')->orderBy('id', 'ASC')->get();
        });
    }

    /**
     * Intent: Global job_categories map [id => name], cached 24 h to eliminate per-row
     * DB::table('job_categories') N+1 queries in job listing / Add-to-Job drawer blades.
     * Tenancy: Global reference table (not tenant-scoped).
     */
    public static function jobCategories(): array
    {
        return Cache::remember('global_job_categories_map', self::TTL_24H, function () {
            return DB::table('job_categories')->pluck('name', 'id')->toArray();
        });
    }

    /**
     * Intent: Global skills collection, cached 24 h to eliminate full-table Skill::all()
     * reads on every AppTracker page load.
     * Tenancy: Global reference table (not tenant-scoped).
     */
    public static function skills(): \Illuminate\Support\Collection
    {
        return Cache::remember('global_skills_all', self::TTL_24H, function () {
            return \App\Models\Skill::all();
        });
    }

    /**
     * Bust all global reference caches at once — call from a seeder or
     * an admin action after editing reference data.
     */
    public static function flush(): void
    {
        Cache::forget('global_application_statuses');
        Cache::forget('global_ticket_statuses');
        Cache::forget('global_qualification_master');
        Cache::forget('global_feedback_status');
        Cache::forget('global_master_languages');
        Cache::forget('global_master_timezones');
        Cache::forget('global_master_currencies');
        Cache::forget('global_master_time_formats');
        Cache::forget('global_master_date_formats');
        Cache::forget('global_master_email_preferences');
        Cache::forget('global_master_countries');
        Cache::forget('global_master_statuses');
        Cache::forget('global_interview_modes');
        Cache::forget('global_master_notifications_elastic');
        Cache::forget('global_job_categories_map');
        Cache::forget('global_skills_all');
    }
}
