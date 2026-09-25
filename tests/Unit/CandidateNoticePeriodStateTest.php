<?php

namespace Tests\Unit;

use App\Services\CandidateNoticePeriodState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Guards the single-source Notice Period state contract used by candidate flows.
 */
class CandidateNoticePeriodStateTest extends TestCase
{
    public function test_selected_notice_period_keeps_days_and_clears_dates(): void
    {
        $state = CandidateNoticePeriodState::normalize(0, '2026-10-15', '60');

        $this->assertSame(0, $state['serving_notice_period_mode']);
        $this->assertSame('60', $state['np_id']);
        $this->assertNull($state['last_working_date']);
    }

    public function test_serving_notice_keeps_expected_date_and_clears_other_state_values(): void
    {
        $state = CandidateNoticePeriodState::normalize(1, '2026-10-15', '90');

        $this->assertSame(1, $state['serving_notice_period_mode']);
        $this->assertSame('2026-10-15', $state['np_id']);
        $this->assertNull($state['last_working_date']);
    }

    public function test_immediate_joiner_keeps_only_legacy_marker_and_last_working_date(): void
    {
        $state = CandidateNoticePeriodState::normalize(2, '2026-09-24', '30');

        $this->assertSame(2, $state['serving_notice_period_mode']);
        $this->assertSame('Immediate', $state['np_id']);
        $this->assertSame('2026-09-24', $state['last_working_date']);
    }

    public function test_request_normalization_ignores_inactive_sub_field(): void
    {
        $request = Request::create('/', 'POST', [
            'serving_notice_period' => 1,
            'np_id' => '2026-10-15',
            'ddl_np_id' => '45',
        ]);

        $this->assertSame([
            'serving_notice_period_mode' => 1,
            'np_id' => '2026-10-15',
            'last_working_date' => null,
        ], CandidateNoticePeriodState::fromRequest($request));
    }

    public function test_validation_rules_follow_the_active_state(): void
    {
        $serving = CandidateNoticePeriodState::validationRules(1);
        $immediate = CandidateNoticePeriodState::validationRules(2);
        $selected = CandidateNoticePeriodState::validationRules(0);

        $this->assertContains('after_or_equal:today', $serving['np_id']);
        $this->assertContains('before_or_equal:today', $immediate['np_id']);
        $this->assertContains('required', $selected['ddl_np_id']);
        $this->assertSame(['nullable'], $selected['np_id']);
    }

    public function test_notice_period_can_be_left_unselected(): void
    {
        $rules = CandidateNoticePeriodState::validationRules('');
        $validator = Validator::make(['serving_notice_period' => ''], $rules);

        $this->assertTrue($validator->passes());
        $this->assertSame(['nullable'], $rules['np_id']);
        $this->assertSame(['nullable'], $rules['ddl_np_id']);
        $this->assertSame([
            'serving_notice_period_mode' => 0,
            'np_id' => null,
            'last_working_date' => null,
        ], CandidateNoticePeriodState::normalize('', null, null));
    }
}
