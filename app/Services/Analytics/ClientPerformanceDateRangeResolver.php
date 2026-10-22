<?php

namespace App\Services\Analytics;

use Carbon\Carbon;

/**
 * ClientPerformanceDateRangeResolver
 *
 * Resolves exact UTC datetime boundaries for rolling date ranges and comparison periods,
 * accounting for client/tenant timezone settings.
 */
class ClientPerformanceDateRangeResolver
{
    /**
     * Resolve date boundaries for the given range and comparison option.
     *
     * @param  string  $range       '30D', '3M', '6M', '12M', 'all'
     * @param  string  $compareWith 'previous_period', 'previous_year', 'none'
     * @param  string  $timezone    Tenant timezone (e.g. 'Asia/Kolkata')
     * @return array
     */
    public function resolveBoundaries(string $range = '12M', string $compareWith = 'previous_period', string $timezone = 'Asia/Kolkata'): array
    {
        $range = strtoupper(trim($range));
        if (!in_array($range, ['30D', '3M', '6M', '12M', 'ALL'])) {
            $range = '12M';
        }

        $now = Carbon::now($timezone);
        $currentEnd = $now->copy();

        switch ($range) {
            case '30D':
                $days = 30;
                $currentStart = $now->copy()->subDays($days);
                break;
            case '3M':
                $days = 90;
                $currentStart = $now->copy()->subDays($days);
                break;
            case '6M':
                $days = 180;
                $currentStart = $now->copy()->subDays($days);
                break;
            case 'ALL':
                $days = null;
                $currentStart = Carbon::createFromTimestamp(0, $timezone);
                break;
            case '12M':
            default:
                $days = 365;
                $currentStart = $now->copy()->subDays($days);
                break;
        }

        // Calculate comparison period boundaries
        $previousStart = null;
        $previousEnd = null;

        if ($compareWith === 'previous_year') {
            $previousEnd = $currentEnd->copy()->subYear();
            $previousStart = $days ? $currentStart->copy()->subYear() : null;
        } elseif ($compareWith === 'previous_period' && $days) {
            $previousEnd = $currentStart->copy();
            $previousStart = $currentStart->copy()->subDays($days);
        }

        return [
            'range'          => $range,
            'compare'        => $compareWith,
            'timezone'       => $timezone,
            'days'           => $days,
            // UTC ISO-8601 strings for Elasticsearch queries
            'current_start'  => $currentStart->copy()->utc()->toIso8601String(),
            'current_end'    => $currentEnd->copy()->utc()->toIso8601String(),
            'previous_start' => $previousStart ? $previousStart->copy()->utc()->toIso8601String() : null,
            'previous_end'   => $previousEnd ? $previousEnd->copy()->utc()->toIso8601String() : null,
            // Formatted display labels
            'label_current'  => $currentStart->format('d M Y').' - '.$currentEnd->format('d M Y'),
            'label_previous' => $previousStart && $previousEnd ? $previousStart->format('d M Y').' - '.$previousEnd->format('d M Y') : 'N/A',
        ];
    }
}
