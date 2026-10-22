<?php

namespace App\Services\Analytics;

/**
 * ClientPerformanceInsightEngine
 *
 * Generates deterministic, algorithmic recruitment intelligence insights
 * by analyzing funnel drop-offs, conversion ratios, and baseline deltas.
 *
 * Strict Compliance:
 * Adheres strictly to Rule 19 (Zero Cloud AI APIs / LangChain).
 * Operates with 100% mathematical determinism on the client's actual data.
 */
class ClientPerformanceInsightEngine
{
    /**
     * Generate actionable insights comparing current metrics against previous period.
     *
     * @param  array  $currentMetrics  Current period summary and funnel metrics
     * @param  array  $previousMetrics Previous period summary metrics
     * @return array  Array of insight cards
     */
    public function generateInsights(array $currentMetrics, array $previousMetrics): array
    {
        $insights = [];

        $candCount = $currentMetrics['candidates']['value'] ?? 0;
        $screenedCount = $currentMetrics['screened']['value'] ?? 0;
        $interviewCount = $currentMetrics['interviewed']['value'] ?? 0;
        $offerCount = $currentMetrics['offers']['value'] ?? 0;
        $joinedCount = $currentMetrics['joins']['value'] ?? 0;
        $avgTth = $currentMetrics['avg_time_to_hire_days']['value'] ?? 0;

        $prevTth = $previousMetrics['avg_time_to_hire_days']['value'] ?? 0;

        // Conversion ratios
        $candToInterviewRate = $candCount > 0 ? ($interviewCount / $candCount) * 100 : 0;
        $interviewToOfferRate = $interviewCount > 0 ? ($offerCount / $interviewCount) * 100 : 0;
        $offerToJoinRate = $offerCount > 0 ? ($joinedCount / $offerCount) * 100 : 0;
        $overallYield = $candCount > 0 ? ($joinedCount / $candCount) * 100 : 0;

        // Rule 1: High Offer-to-Join Conversion (Positive)
        if ($offerCount >= 2 && $offerToJoinRate >= 66.0) {
            $prevOfferToJoin = ($previousMetrics['offers']['value'] ?? 0) > 0
                ? (($previousMetrics['joins']['value'] ?? 0) / $previousMetrics['offers']['value']) * 100
                : 0;
            $deltaPts = $offerToJoinRate - $prevOfferToJoin;

            $insights[] = [
                'type'  => 'positive',
                'icon'  => 'fa-circle-check',
                'title' => 'High Offer Acceptance Velocity',
                'text'  => sprintf(
                    'Offer-to-join rate is %.1f%% (%d joined out of %d offers)%s.',
                    $offerToJoinRate,
                    $joinedCount,
                    $offerCount,
                    $prevOfferToJoin > 0 ? sprintf(', outperforming prior period baseline by %+.1f pts', $deltaPts) : ''
                ),
            ];
        }

        // Rule 2: Screening Drop-Off Detected (Warning)
        if ($candCount >= 15 && $candToInterviewRate < 25.0) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => 'fa-filter-circle-xmark',
                'title' => 'Screening Drop-Off Detected',
                'text'  => sprintf(
                    'Candidate-to-interview conversion is %.1f%% (%d of %d candidates), indicating strong submission volume but high drop-off during initial evaluation.',
                    $candToInterviewRate,
                    $interviewCount,
                    $candCount
                ),
            ];
        }

        // Rule 3: Interview-to-Offer Bottleneck (Warning)
        if ($interviewCount >= 8 && $interviewToOfferRate < 20.0) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => 'fa-triangle-exclamation',
                'title' => 'Interview Conversion Bottleneck',
                'text'  => sprintf(
                    '%d candidates were interviewed, but only %d reached offer stage (%.1f%% conversion). Consider reviewing evaluation criteria with hiring managers.',
                    $interviewCount,
                    $offerCount,
                    $interviewToOfferRate
                ),
            ];
        }

        // Rule 4: Hiring Cycle Lengthening (Warning)
        if ($avgTth > 0 && $prevTth > 0 && ($avgTth - $prevTth) >= 7) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => 'fa-clock',
                'title' => 'Hiring Cycle Lengthening',
                'text'  => sprintf(
                    'Average Time-to-Hire increased to %d days (%+d days vs %d days in prior period).',
                    $avgTth,
                    $avgTth - $prevTth,
                    $prevTth
                ),
            ];
        } elseif ($avgTth > 0 && $prevTth > 0 && ($prevTth - $avgTth) >= 5) {
            $insights[] = [
                'type'  => 'positive',
                'icon'  => 'fa-bolt',
                'title' => 'Hiring Velocity Accelerating',
                'text'  => sprintf(
                    'Average Time-to-Hire dropped to %d days (%d days faster than prior period).',
                    $avgTth,
                    $prevTth - $avgTth
                ),
            ];
        }

        // Rule 5: Healthy Overall Yield
        if ($candCount >= 10 && $overallYield >= 10.0 && count($insights) < 2) {
            $insights[] = [
                'type'  => 'positive',
                'icon'  => 'fa-chart-line',
                'title' => 'Strong Cumulative Pipeline Yield',
                'text'  => sprintf(
                    'Overall pipeline yield is %.1f%% (%d placements from %d submitted candidates), reflecting high talent alignment.',
                    $overallYield,
                    $joinedCount,
                    $candCount
                ),
            ];
        }

        // Default Steady State
        if (empty($insights)) {
            $insights[] = [
                'type'  => 'info',
                'icon'  => 'fa-circle-info',
                'title' => 'Pipeline Activity Steady',
                'text'  => 'Hiring velocity and stage conversion ratios are operating within standard historical baseline ranges.',
            ];
        }

        return $insights;
    }
}
