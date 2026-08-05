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
                    'id'        => $row->id,
                    'name'      => $row->name,
                    'classname' => $row->classname,
                ];
            }
            return $result;
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
    }
}
