<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Presenter helpers for AppTracker & ClientAppTracker Rule 20 candidate cards.
 *
 * Intent: Centralizes candidate card field formatting (strict current_company,
 * work mode + icon, NP prefix rules, LinkedIn Verified URL, Visa Status badge,
 * and relative submission timestamp) so all_apptracker and clientappdata_ajax share
 * one tested path without empty placeholder fields.
 */
class AppTrackerCardPresenter
{
    private const EMPTY_TOKENS = ['', '-', '--', '---', '----', '—', 'null', 'none', 'n/a', 'na'];

    /**
     * Build normalized, empty-safe metadata for a candidate row.
     *
     * @return array{
     *     designation: string,
     *     current_company: string,
     *     work_mode_label: string,
     *     work_mode_icon: string,
     *     qualification: string,
     *     notice_display: string,
     *     notice_class: string,
     *     submitted_ago: string,
     *     linkedin_url: string,
     *     visa_display: string,
     *     visa_raw: string
     * }
     */
    public static function present(object $row, ?string $resolvedCountryName = null, bool $dashedSnpDate = false): array
    {
        $designation = self::cleanText($row->current_designation ?? null);
        $currentCompany = self::cleanText($row->current_company ?? null);
        $workMode = self::resolveWorkMode($row);
        $qualification = self::cleanText($row->qualification_name ?? null);
        $notice = self::resolveNoticePeriod($row, $dashedSnpDate);
        $visa = self::resolveVisa($row, $resolvedCountryName);

        return [
            'designation' => $designation !== '' ? ucwords($designation) : '',
            'current_company' => $currentCompany !== '' ? ucwords($currentCompany) : '',
            'work_mode_label' => $workMode['label'],
            'work_mode_icon' => $workMode['icon'],
            'qualification' => $qualification,
            'notice_display' => $notice['display'],
            'notice_class' => $notice['classSuffix'],
            'submitted_ago' => self::resolveSubmittedAgo($row),
            'linkedin_url' => self::resolveLinkedinUrl($row),
            'visa_display' => $visa['display'],
            'visa_raw' => $visa['raw'],
        ];
    }

    /**
     * Strip placeholder tokens and return a clean string or empty string.
     */
    public static function cleanText($value): string
    {
        $trimmed = trim((string) ($value ?? ''));
        if ($trimmed === '' || in_array(mb_strtolower($trimmed), self::EMPTY_TOKENS, true)) {
            return '';
        }

        return $trimmed;
    }

    /**
     * Resolve Work From Home / Remote / Hybrid / Work From Office label and FontAwesome icon.
     *
     * @return array{label: string, icon: string}
     */
    public static function resolveWorkMode(object $row): array
    {
        $raw = mb_strtolower(self::cleanText($row->wm ?? ($row->job_mode ?? ($row->work_mode ?? null))));
        if ($raw === '') {
            return ['label' => '', 'icon' => ''];
        }

        if (str_contains($raw, 'wfh') || str_contains($raw, 'work from home')) {
            return ['label' => 'Work From Home', 'icon' => 'fas fa-laptop'];
        }
        if (str_contains($raw, 'remote') || $raw === '3' || $raw === 'yes') {
            return ['label' => 'Remote', 'icon' => 'fas fa-laptop'];
        }
        if (str_contains($raw, 'hybrid') || $raw === '2') {
            return ['label' => 'Hybrid', 'icon' => 'fas fa-building'];
        }
        if (str_contains($raw, 'office') || str_contains($raw, 'wfo') || $raw === '1' || $raw === 'no') {
            return ['label' => 'Work From Office', 'icon' => 'fas fa-building'];
        }

        return ['label' => ucwords($raw), 'icon' => 'fas fa-building'];
    }

    /**
     * Format notice period using `NP: 9 Days` or `NP: Immediate` (and preserve future SNP LWD).
     *
     * @return array{display: string, classSuffix: string}
     */
    public static function resolveNoticePeriod(object $row, bool $dashedSnpDate = false): array
    {
        $mode = (int) ($row->serving_notice_period_mode ?? -1);
        $rawNp = trim((string) ($row->np_id ?? ''));
        if ($rawNp === '' || in_array(mb_strtolower($rawNp), self::EMPTY_TOKENS, true)) {
            return ['display' => '', 'classSuffix' => 'default'];
        }

        if (mb_strtolower($rawNp) === 'immediate' || $rawNp === '0') {
            return ['display' => 'NP: Immediate', 'classSuffix' => 'immediate'];
        }

        if ($mode === 1) {
            $ts = strtotime($rawNp);
            if ($ts !== false) {
                if (date('Y-m-d', $ts) < date('Y-m-d')) {
                    return ['display' => 'NP: Immediate', 'classSuffix' => 'immediate'];
                }

                return [
                    'display' => 'SNP LWD '.date($dashedSnpDate ? 'd-M-Y' : 'd M y', $ts),
                    'classSuffix' => 'snp',
                ];
            }

            return ['display' => 'SNP LWD '.$rawNp, 'classSuffix' => 'snp'];
        }

        if ($mode === 0 || is_numeric($rawNp)) {
            $daysText = is_numeric($rawNp)
                ? ($rawNp.(((int) $rawNp === 1) ? ' Day' : ' Days'))
                : $rawNp;

            return ['display' => 'NP: '.$daysText, 'classSuffix' => 'days'];
        }

        return ['display' => 'NP: '.$rawNp, 'classSuffix' => 'days'];
    }

    /**
     * Build a safe external LinkedIn profile URL when provided, otherwise empty string.
     */
    public static function resolveLinkedinUrl(object $row): string
    {
        $raw = self::cleanText($row->linkedin ?? ($row->ci_linkedin ?? null));
        if ($raw === '' || $raw === '0') {
            return '';
        }

        $url = $raw;
        if (! preg_match('#^https?://#i', $url)) {
            if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
                return '';
            }
            $url = str_contains(mb_strtolower($url), 'linkedin.com')
                ? 'https://'.$url
                : 'https://www.linkedin.com/in/'.ltrim($url, '@/');
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }

    /**
     * Format Visa Status as `(Country, Visa Type)` only when visa details are provided.
     *
     * @return array{display: string, raw: string}
     */
    public static function resolveVisa(object $row, ?string $fallbackCountryName = null): array
    {
        $rawVisa = self::cleanText($row->visastatu ?? ($row->visa_status ?? ($row->visa ?? null)));
        if ($rawVisa === '' || $rawVisa === '0') {
            return ['display' => '', 'raw' => ''];
        }

        $countryShort = '';
        $countryName = self::cleanText($fallbackCountryName ?: ($row->country_name ?? null));
        if ($countryName !== '') {
            $upper = strtoupper($countryName);
            $countryAliases = [
                'UNITED STATES' => 'US',
                'UNITED STATES OF AMERICA' => 'US',
                'USA' => 'US',
                'UNITED KINGDOM' => 'UK',
                'GREAT BRITAIN' => 'UK',
                'UNITED ARAB EMIRATES' => 'UAE',
                'NEW ZEALAND' => 'NZ',
                'SOUTH AFRICA' => 'ZA',
                'SAUDI ARABIA' => 'SA',
                'CANADA' => 'CA',
                'INDIA' => 'IN',
                'AUSTRALIA' => 'AU',
                'SINGAPORE' => 'SG',
            ];
            $countryShort = $countryAliases[$upper]
                ?? (mb_strlen($upper) > 2 ? mb_substr($upper, 0, 2) : $upper);
        }

        $vLower = mb_strtolower($rawVisa);
        if ($countryShort === '') {
            $keywordCountry = [
                'US' => ['us ', 'u.s', 'united states', 'h-1b', 'h1b', 'green card', 'gc-ead', 'opt', 'cpt', 'tn permit'],
                'CA' => ['canada', 'canadian'],
                'IN' => ['india', 'indian'],
                'UK' => ['uk', 'british'],
                'UAE' => ['uae', 'dubai'],
                'SG' => ['singapore'],
                'AU' => ['australia'],
            ];
            foreach ($keywordCountry as $code => $terms) {
                foreach ($terms as $term) {
                    if (str_contains($vLower, $term)) {
                        $countryShort = $code;
                        break 2;
                    }
                }
            }
        }

        $cleanVisaName = trim((string) preg_replace(
            '/^(US|United States|Canada|Canadian|India|Indian|UK|UAE|Dubai|Singapore|Australia)\s+/i',
            '',
            $rawVisa
        ));
        if ($cleanVisaName === '') {
            $cleanVisaName = $rawVisa;
        }

        $display = $countryShort !== ''
            ? "({$countryShort}, {$cleanVisaName})"
            : "({$cleanVisaName})";

        return ['display' => $display, 'raw' => $rawVisa];
    }

    /**
     * Format relative submission time (`20 days ago`) from `created_at` when available.
     */
    public static function resolveSubmittedAgo(object $row): string
    {
        $raw = self::cleanText($row->created_at ?? null);
        if ($raw === '' || $raw === '0000-00-00 00:00:00') {
            return '';
        }

        try {
            $parsed = Carbon::parse($raw);
            $days = (int) $parsed->diffInDays(now());
            if ($days >= 1 && $days < 30) {
                return $days.' '.($days === 1 ? 'day' : 'days').' ago';
            }

            return $parsed->diffForHumans();
        } catch (\Throwable) {
            return '';
        }
    }
}
