<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Normalizes the three mutually exclusive candidate notice-period states.
 *
 * The existing database contract is preserved: selected notice days and the
 * serving-notice date use np_id, while an immediate joiner's last working date
 * uses last_working_date and np_id remains the legacy "Immediate" marker.
 */
final class CandidateNoticePeriodState
{
    public const SELECT_PERIOD = 0;

    public const SERVING_NOTICE = 1;

    public const IMMEDIATE_JOINER = 2;

    /** @return array{serving_notice_period_mode:int,np_id:?string,last_working_date:?string} */
    public static function fromRequest(Request $request): array
    {
        return self::normalize(
            $request->input('serving_notice_period'),
            $request->input('np_id'),
            $request->input('ddl_np_id')
        );
    }

    /** @return array{serving_notice_period_mode:int,np_id:?string,last_working_date:?string} */
    public static function normalize(mixed $mode, mixed $date, mixed $days): array
    {
        $normalizedMode = (int) $mode;
        if (! in_array($normalizedMode, [self::SELECT_PERIOD, self::SERVING_NOTICE, self::IMMEDIATE_JOINER], true)) {
            $normalizedMode = self::SELECT_PERIOD;
        }

        $normalizedDate = self::nullableString($date);

        if ($normalizedMode === self::SERVING_NOTICE) {
            return [
                'serving_notice_period_mode' => self::SERVING_NOTICE,
                'np_id' => $normalizedDate,
                'last_working_date' => null,
            ];
        }

        if ($normalizedMode === self::IMMEDIATE_JOINER) {
            return [
                'serving_notice_period_mode' => self::IMMEDIATE_JOINER,
                'np_id' => 'Immediate',
                'last_working_date' => $normalizedDate,
            ];
        }

        $normalizedDays = self::nullableString($days);

        return [
            'serving_notice_period_mode' => self::SELECT_PERIOD,
            'np_id' => $normalizedDays,
            'last_working_date' => null,
        ];
    }

    /** @return array<string, array<int, string>> */
    public static function validationRules(mixed $mode): array
    {
        $normalizedMode = (int) $mode;

        return [
            'serving_notice_period' => ['required', 'integer', 'in:0,1,2'],
            'np_id' => $normalizedMode === self::SERVING_NOTICE
                ? ['required', 'date', 'after_or_equal:today']
                : ($normalizedMode === self::IMMEDIATE_JOINER
                    ? ['required', 'date', 'before_or_equal:today']
                    : ['nullable']),
            'ddl_np_id' => $normalizedMode === self::SELECT_PERIOD
                ? ['required', 'integer', 'between:1,200']
                : ['nullable'],
        ];
    }

    /** @return array<string, string> */
    public static function validationMessages(): array
    {
        return [
            'serving_notice_period.required' => 'Please select a notice period status.',
            'serving_notice_period.in' => 'Please select a valid notice period status.',
            'np_id.required' => 'Please select the relevant last working date.',
            'np_id.after_or_equal' => 'Expected last working date cannot be in the past.',
            'np_id.before_or_equal' => 'Last working date cannot be in the future for an immediate joiner.',
            'ddl_np_id.required' => 'Please select the candidate notice period.',
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}