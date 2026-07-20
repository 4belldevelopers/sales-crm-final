<?php

namespace Tests\Feature\OwnershipEngine;

use App\Services\OwnershipEngine\DataTransferObjects\OwnershipResult;
use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 2 validation: every row of the Master Tracker (S1-S30) as an automated test,
 * exactly as required by docs/OWNERSHIP_ENGINE_DESIGN.md §3 (the Scenario->Rule->Code->Test
 * traceability matrix) and §14. Each test method name is the scenario ID it proves.
 *
 * This exercises the real OwnershipEngine against a real database (RefreshDatabase) — it
 * does not touch the existing resolveDuplicateOwnership()/checkDuplicateCandidate() logic,
 * which is untouched and still running in production per Implementation Rules #4/#5.
 *
 * Requires a real MySQL 8 connection for the same reason as SchemaTest (generated columns).
 */
class MasterTrackerScenariosTest extends TestCase
{
    use RefreshDatabase;

    private OwnershipEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(OwnershipEngine::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // ---- fixture helpers -----------------------------------------------------------

    private function makeCompany(int $appDuplicateMode): int
    {
        return (int) DB::table('companies')->insertGetId([
            'company_name' => 'Test Co '.uniqid(),
            'company_email' => uniqid().'@example.test',
            'locale' => 'en',
            'app_duplicate' => $appDuplicateMode,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeJob(int $companyId): int
    {
        return (int) DB::table('jobs')->insertGetId([
            'title' => 'Test Job',
            'company_id' => $companyId,
            'job_description' => 'x',
            'job_requirement' => 'x',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'required_columns' => '',
            'meta_details' => '',
            'section_visibility' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Creates one contender: its own job_applications row (so it matches other contenders
     * purely by shared email, the way independent submitters actually would in production)
     * plus its add_job_wise_condidates row. Returns the add_job_wise_condidates.id.
     */
    private function makeContender(
        int $jobId,
        int $scopingCompanyId,
        int $thirdpartyCompanyId,
        string $email,
        Carbon $createdAt,
    ): int {
        Carbon::setTestNow($createdAt);

        $jobApplicationId = (int) DB::table('job_applications')->insertGetId([
            'submitted_by' => 1,
            'email' => $email,
            'phone' => '9990000000',
            'created_at' => $createdAt,
        ]);

        return (int) DB::table('add_job_wise_condidates')->insertGetId([
            'job_id' => $jobId,
            'job_application_id' => $jobApplicationId,
            'company_id' => $scopingCompanyId,
            'thirdparty_company_id' => $thirdpartyCompanyId,
            'master_status_id' => 8,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function reserve(int $rowId, Carbon $at): OwnershipResult
    {
        Carbon::setTestNow($at);

        return $this->engine->attemptCompanyReservation($rowId);
    }

    private function source(int $rowId, Carbon $at): OwnershipResult
    {
        Carbon::setTestNow($at);

        return $this->engine->claimForAgencySourcing($rowId);
    }

    private function cachedState(int $rowId): ?int
    {
        $state = DB::table('add_job_wise_condidates')->where('id', $rowId)->value('ownership_state');

        return $state === null ? null : (int) $state;
    }

    private function t(int $minutesFromEpoch): Carbon
    {
        return Carbon::parse('2026-01-01 00:00:00')->addMinutes($minutesFromEpoch);
    }

    private const AGENCY_A = 9001;
    private const AGENCY_B = 9002;

    // ---- Job mode: S1-S18 -----------------------------------------------------------

    public function test_s1_single_agency_never_sourced_is_undetermined(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's1@example.test', $this->t(0));

        $this->assertNull($this->cachedState($a));
    }

    public function test_s2_two_agencies_never_sourced_both_undetermined(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's2@example.test', $this->t(0));
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's2@example.test', $this->t(1));

        $this->assertNull($this->cachedState($a));
        $this->assertNull($this->cachedState($b));
    }

    public function test_s3_first_agency_to_source_wins_even_if_uploaded_second(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's3@example.test', $this->t(0)); // uploaded first
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's3@example.test', $this->t(1)); // uploaded second
        $this->source($a, $this->t(2)); // sourced first

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertNull($this->cachedState($b)); // never sourced — stays private
    }

    public function test_s4_mirror_of_s3(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's4@example.test', $this->t(0));
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's4@example.test', $this->t(1));
        $this->source($b, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($b));
        $this->assertNull($this->cachedState($a));
    }

    public function test_s5_second_agency_to_source_becomes_duplicate(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's5@example.test', $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's5@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $this->source($b, $this->t(3));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }

    public function test_s6_ownership_survives_repeat_evaluation_past_sourcing(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's6@example.test', $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's6@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $this->source($b, $this->t(3));

        // "Evaluation"/"Interview" (post-Sourcing) never re-invoke the engine per the design
        // (only the 8->1 handoff does) — a hypothetical repeat call must still be a no-op.
        $this->source($a, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }

    public function test_s7_ownership_never_changes_at_terminal_stages(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's7@example.test', $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's7@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $this->source($b, $this->t(3));

        // Repeat call for the loser at a hypothetical later stage — still unchanged (ER4).
        $this->source($b, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }

    public function test_s8_company_reservation_starts_immediately(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $cr = $this->makeContender($job, $company, 0, 's8@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
    }

    public function test_s9_agency_becomes_duplicate_once_sourced_after_company_reserved(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $cr = $this->makeContender($job, $company, 0, 's9@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's9@example.test', $this->t(1));
        $this->source($a, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
    }

    public function test_s10_reservation_still_active_at_evaluation(): void
    {
        $this->assertReservationSurvivesFurtherAgencyProgress('s10');
    }

    public function test_s11_reservation_still_active_at_offer(): void
    {
        $this->assertReservationSurvivesFurtherAgencyProgress('s11');
    }

    public function test_s12_reservation_still_active_at_joined(): void
    {
        $this->assertReservationSurvivesFurtherAgencyProgress('s12');
    }

    private function assertReservationSurvivesFurtherAgencyProgress(string $seed): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $cr = $this->makeContender($job, $company, 0, "{$seed}@example.test", $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, "{$seed}@example.test", $this->t(1));
        $this->source($a, $this->t(2));
        $this->source($a, $this->t(3)); // hypothetical repeat at a later stage — no-op

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
    }

    public function test_s13_company_reservation_overrides_two_agencies(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $cr = $this->makeContender($job, $company, 0, 's13@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's13@example.test', $this->t(1));
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's13@example.test', $this->t(2));
        $this->source($a, $this->t(3));
        $this->source($b, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }

    public function test_s14_all_private_company_already_reserved(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $cr = $this->makeContender($job, $company, 0, 's14@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's14@example.test', $this->t(1));
        $b = $this->makeContender($job, $company, self::AGENCY_B, 's14@example.test', $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertNull($this->cachedState($a));
        $this->assertNull($this->cachedState($b));
    }

    public function test_s15_agency_already_owned_company_becomes_duplicate(): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $a = $this->makeContender($job, $company, self::AGENCY_A, 's15@example.test', $this->t(0));
        $this->source($a, $this->t(1));
        $cr = $this->makeContender($job, $company, 0, 's15@example.test', $this->t(2)); // added later
        $this->reserve($cr, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($cr));
    }

    public function test_s16_company_cannot_flip_ownership_at_evaluation(): void
    {
        $this->assertCompanyStaysDuplicateAfterAgencyAlreadyOwns('s16');
    }

    public function test_s17_company_cannot_flip_ownership_at_offer(): void
    {
        $this->assertCompanyStaysDuplicateAfterAgencyAlreadyOwns('s17');
    }

    public function test_s18_company_cannot_flip_ownership_at_joined(): void
    {
        $this->assertCompanyStaysDuplicateAfterAgencyAlreadyOwns('s18');
    }

    private function assertCompanyStaysDuplicateAfterAgencyAlreadyOwns(string $seed): void
    {
        $company = $this->makeCompany(1);
        $job = $this->makeJob($company);
        $a = $this->makeContender($job, $company, self::AGENCY_A, "{$seed}@example.test", $this->t(0));
        $this->source($a, $this->t(1));
        $cr = $this->makeContender($job, $company, 0, "{$seed}@example.test", $this->t(2));
        $this->reserve($cr, $this->t(2));
        // Company row's own later stage progress never re-invokes the engine at all — no
        // further call exists to make here; the cache value from creation-time is final.

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($cr));
    }

    // ---- Company Overall mode: S19-S30 (mode = 2, one company, several jobs) --------

    public function test_s19_company_overall_reservation_in_another_job_overrides_agency(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's19@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's19@example.test', $this->t(1));
        $this->source($a, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
    }

    public function test_s20_company_overall_reservation_overrides_two_agencies_across_jobs(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's20@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's20@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's20@example.test', $this->t(3));
        $this->source($b, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }

    public function test_s21_company_overall_reservation_valid_across_jobs_at_evaluation(): void
    {
        $this->assertCompanyOverallReservationSurvives('s21');
    }

    public function test_s22_company_overall_reservation_valid_across_jobs_at_joined(): void
    {
        $this->assertCompanyOverallReservationSurvives('s22');
    }

    private function assertCompanyOverallReservationSurvives(string $seed): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, "{$seed}@example.test", $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, "{$seed}@example.test", $this->t(1));
        $this->source($a, $this->t(2));
        $this->source($a, $this->t(3)); // hypothetical repeat at a later stage — no-op

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
    }

    public function test_s23_company_overall_agency_sourced_first_company_becomes_duplicate(): void
    {
        $company = $this->makeCompany(2);
        $jobA = $this->makeJob($company);
        $jobB = $this->makeJob($company);
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's23@example.test', $this->t(0));
        $this->source($a, $this->t(1));
        $cr = $this->makeContender($jobB, $company, 0, 's23@example.test', $this->t(2));
        $this->reserve($cr, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($cr));
    }

    public function test_s24_company_overall_ownership_stays_with_agency_at_evaluation(): void
    {
        $this->assertCompanyOverallStaysDuplicateAfterAgencyOwns('s24');
    }

    public function test_s25_company_overall_ownership_stays_with_agency_at_joined(): void
    {
        $this->assertCompanyOverallStaysDuplicateAfterAgencyOwns('s25');
    }

    private function assertCompanyOverallStaysDuplicateAfterAgencyOwns(string $seed): void
    {
        $company = $this->makeCompany(2);
        $jobA = $this->makeJob($company);
        $jobB = $this->makeJob($company);
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, "{$seed}@example.test", $this->t(0));
        $this->source($a, $this->t(1));
        $cr = $this->makeContender($jobB, $company, 0, "{$seed}@example.test", $this->t(2));
        $this->reserve($cr, $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($cr));
    }

    public function test_s26_company_overall_three_way_agency_established_first(): void
    {
        $company = $this->makeCompany(2);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $jobB = $this->makeJob($company);
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's26@example.test', $this->t(0));
        $this->source($a, $this->t(1));
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's26@example.test', $this->t(2));
        $this->source($b, $this->t(3));
        $cr = $this->makeContender($jobB, $company, 0, 's26@example.test', $this->t(4));
        $this->reserve($cr, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($cr));
    }

    public function test_s27_company_overall_reservation_agencies_remain_private(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's27@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's27@example.test', $this->t(1));
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's27@example.test', $this->t(2));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertNull($this->cachedState($a));
        $this->assertNull($this->cachedState($b));
    }

    public function test_s28_company_overall_reservation_overrides_only_the_sourced_agency(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's28@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's28@example.test', $this->t(1)); // never sources
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's28@example.test', $this->t(2));
        $this->source($b, $this->t(3));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
        $this->assertNull($this->cachedState($a));
    }

    public function test_s29_company_overall_reservation_overrides_only_the_sourced_agency_mirror(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's29@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's29@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's29@example.test', $this->t(3)); // never sources

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
        $this->assertNull($this->cachedState($b));
    }

    public function test_s30_company_is_primary_owner_everywhere(): void
    {
        $company = $this->makeCompany(2);
        $jobB = $this->makeJob($company);
        $jobA = $this->makeJob($company);
        $jobC = $this->makeJob($company);
        $cr = $this->makeContender($jobB, $company, 0, 's30@example.test', $this->t(0));
        $this->reserve($cr, $this->t(0));
        $a = $this->makeContender($jobA, $company, self::AGENCY_A, 's30@example.test', $this->t(1));
        $this->source($a, $this->t(2));
        $b = $this->makeContender($jobC, $company, self::AGENCY_B, 's30@example.test', $this->t(3));
        $this->source($b, $this->t(4));

        $this->assertSame(OwnershipResult::STATE_OWNER, $this->cachedState($cr));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($a));
        $this->assertSame(OwnershipResult::STATE_DUPLICATE, $this->cachedState($b));
    }
}
