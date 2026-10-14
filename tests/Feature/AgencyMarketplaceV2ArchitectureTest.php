<?php

namespace Tests\Feature;

use App\Enums\ClaimCapacity;
use App\Enums\MarketModState;
use App\Enums\MarketplaceState;
use App\Enums\MarketPubState;
use App\Models\Job;
use App\Services\CoinLedgerService;
use App\Services\MarketplaceModerationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * AgencyMarketplaceV2ArchitectureTest
 *
 * Verifies Architecture V2.2 Master Specification:
 * - 6-dimensional state machine & backward compatibility projections
 * - Composite risk & anti-circumvention engine
 * - Double-entry coin ledger & 50% SLA penalty rules
 * - Self-healing reconciliation engine
 */
class AgencyMarketplaceV2ArchitectureTest extends TestCase
{
    /**
     * Test dynamic computation of legacy public_market_status integer projection.
     */
    public function test_computed_legacy_status_projection(): void
    {
        $statusActive = MarketplaceState::computeLegacyStatus(
            pubState: MarketPubState::PUBLISHED->value,
            modState: MarketModState::APPROVED->value,
            claimCap: ClaimCapacity::OPEN->value
        );
        $this->assertEquals(2, $statusActive, 'Published and approved should compute to legacy status 2');

        $statusVendorLimit = MarketplaceState::computeLegacyStatus(
            pubState: MarketPubState::PUBLISHED->value,
            modState: MarketModState::APPROVED->value,
            claimCap: ClaimCapacity::LIMIT_REACHED->value
        );
        $this->assertEquals(4, $statusVendorLimit, 'Limit reached should compute to legacy status 4');

        $statusSuspended = MarketplaceState::computeLegacyStatus(
            pubState: MarketPubState::PUBLISHED->value,
            modState: MarketModState::SUSPENDED->value,
            claimCap: ClaimCapacity::OPEN->value
        );
        $this->assertEquals(5, $statusSuspended, 'Suspended should take precedence and compute to 5');

        $statusUnpublished = MarketplaceState::computeLegacyStatus(
            pubState: MarketPubState::UNPUBLISHED->value,
            modState: MarketModState::APPROVED->value,
            claimCap: ClaimCapacity::OPEN->value
        );
        $this->assertEquals(6, $statusUnpublished, 'Unpublished should compute to 6');
    }

    /**
     * Test Job model mutator guard synchronizes dimensional states.
     */
    public function test_job_model_mutator_guard_synchronizes_dimensional_states(): void
    {
        $job = new Job;
        $job->setPublicMarketStatusAttribute(2);

        $this->assertEquals(MarketPubState::PUBLISHED->value, $job->market_pub_state);
        $this->assertEquals(MarketModState::APPROVED->value, $job->market_mod_state);
        $this->assertEquals(ClaimCapacity::OPEN->value, $job->claim_capacity);

        $job->setPublicMarketStatusAttribute(6);
        $this->assertEquals(MarketPubState::UNPUBLISHED->value, $job->market_pub_state);
    }

    /**
     * Test composite risk engine catches circumvention and scores clean postings safely.
     */
    public function test_risk_engine_circumvention_and_scoring(): void
    {
        $service = new MarketplaceModerationService;

        // 1. Clean job
        $clean = [
            'title' => 'Senior Backend Engineer',
            'job_description' => 'We are seeking an experienced Senior Backend Engineer with deep Laravel, MySQL, and Elasticsearch knowledge to join our core architecture team. Responsibilities include designing scalable CQRS microservices, optimizing database transactions, and writing maintainable unit tests.',
            'job_requirement' => 'Minimum 5 years of production experience in Laravel. Strong architectural foundation and team leadership.',
            'min_salary' => 1200000,
            'max_salary' => 2000000,
            'company_id' => 1,
        ];
        $evalClean = $service->evaluateRisk($clean);
        $this->assertFalse($evalClean['hard_blocked']);
        $this->assertLessThan(0.20, $evalClean['risk_score']);

        // 2. Off-platform phone leak
        $phoneLeak = $clean;
        $phoneLeak['job_description'] .= ' Contact the recruiter directly on WhatsApp at +91 9876543210.';
        $evalPhone = $service->evaluateRisk($phoneLeak);
        $this->assertTrue($evalPhone['hard_blocked']);
        $this->assertGreaterThanOrEqual(0.85, $evalPhone['risk_score']);

        // 3. Off-platform email leak
        $emailLeak = $clean;
        $emailLeak['job_description'] .= ' Send your CV to direct.hire@externalagency.com';
        $evalEmail = $service->evaluateRisk($emailLeak);
        $this->assertTrue($evalEmail['hard_blocked']);
        $this->assertGreaterThanOrEqual(0.85, $evalEmail['risk_score']);

        // 4. Inverted compensation anomaly
        $inverted = $clean;
        $inverted['min_salary'] = 3000000;
        $inverted['max_salary'] = 1000000;
        $evalInverted = $service->evaluateRisk($inverted);
        $this->assertGreaterThan(0.20, $evalInverted['risk_score']);
    }

    /**
     * Test double-entry coin ledger, atomic row-locking, and 50% SLA penalty.
     */
    public function test_coin_ledger_and_sla_penalty(): void
    {
        if (config('database.default') === 'sqlite') {
            \Illuminate\Support\Facades\Schema::create('company_coins', function ($table) {
                $table->id();
                $table->integer('company_id');
                $table->string('event_type');
                $table->integer('coin_order_id')->default(1);
                $table->double('amount')->default(0);
                $table->double('coins')->default(0);
                $table->double('balance_coin')->default(0);
                $table->string('status')->default('valid');
                $table->string('free_pack_status')->default('active');
                $table->timestamps();
            });

            \Illuminate\Support\Facades\Schema::create('coin_ledger', function ($table) {
                $table->id();
                $table->string('transaction_id');
                $table->integer('company_id');
                $table->string('account_type');
                $table->string('coin_type');
                $table->string('entry_type');
                $table->double('amount');
                $table->double('balance_after');
                $table->string('reference_type');
                $table->string('reference_id');
                $table->string('idempotency_key');
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        $service = new CoinLedgerService;
        $testCompanyId = 99998;

        DB::table('company_coins')->updateOrInsert(
            ['company_id' => $testCompanyId, 'event_type' => 'operational'],
            [
                'coin_order_id' => 1,
                'amount' => 1000.00,
                'coins' => 1000.00,
                'balance_coin' => 400.00,
                'status' => 'valid',
                'free_pack_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $claimRef = 'claim_'.Str::random(8);
        $idemKey = 'rsv_'.Str::random(8);

        // Reserve 100 coins
        $txn = $service->reserveCoinsForClaim($testCompanyId, 100.00, $claimRef, $idemKey);
        $this->assertNotEmpty($txn);
        $this->assertEquals(300.00, $service->getBalance($testCompanyId));

        // Idempotent retry returns matching txn
        $retryTxn = $service->reserveCoinsForClaim($testCompanyId, 100.00, $claimRef, $idemKey);
        $this->assertEquals($txn, $retryTxn);
        $this->assertEquals(300.00, $service->getBalance($testCompanyId));

        // 50% SLA penalty (50 refund, 50 forfeited)
        $slaRes = $service->penalizeSlaViolation($testCompanyId, 100.00, $claimRef, 'sla_'.Str::random(8));
        $this->assertEquals(50.00, $slaRes['refunded']);
        $this->assertEquals(50.00, $slaRes['forfeited']);
        $this->assertEquals(350.00, $service->getBalance($testCompanyId));

        // Clean up test rows
        DB::table('coin_ledger')->where('company_id', $testCompanyId)->delete();
        DB::table('company_coins')->where('company_id', $testCompanyId)->delete();
    }
}
