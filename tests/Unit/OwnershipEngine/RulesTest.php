<?php

namespace Tests\Unit\OwnershipEngine;

use App\Services\OwnershipEngine\Rules\ER1_PrivateUntilSourced;
use App\Services\OwnershipEngine\Rules\ER2_CompanyReservation;
use App\Services\OwnershipEngine\Rules\ER3_AgencyOwnershipByFirstSourced;
use App\Services\OwnershipEngine\Rules\ER4_OwnershipNeverFlips;
use App\Services\OwnershipEngine\Rules\ER5_CompanyRowsAlwaysCompared;
use App\Services\OwnershipEngine\Rules\ER6_AgencyRowsPrivacyShieldedUntilSourced;
use App\Services\OwnershipEngine\Rules\ER8_MultiContenderGeneralization;
use PHPUnit\Framework\TestCase;

/**
 * Phase 2: pure-logic unit tests for each Engine Rule in isolation, no database required.
 * docs/OWNERSHIP_ENGINE_DESIGN.md §2/§14.
 */
class RulesTest extends TestCase
{
    public function test_er1_throws_for_agency_row_that_has_not_reached_sourcing(): void
    {
        $rule = new ER1_PrivateUntilSourced();

        $this->expectException(\LogicException::class);
        $rule->assertMayEvaluate(isCompanyRow: false, hasReachedSourcing: false, rowId: 1);
    }

    public function test_er1_allows_agency_row_that_has_reached_sourcing(): void
    {
        $rule = new ER1_PrivateUntilSourced();
        $rule->assertMayEvaluate(isCompanyRow: false, hasReachedSourcing: true, rowId: 1);
        $this->assertTrue(true); // no exception thrown
    }

    public function test_er1_allows_company_row_regardless_of_sourcing(): void
    {
        $rule = new ER1_PrivateUntilSourced();
        $rule->assertMayEvaluate(isCompanyRow: true, hasReachedSourcing: false, rowId: 1);
        $this->assertTrue(true);
    }

    public function test_er2_establishes_reservation_only_when_no_owner_exists(): void
    {
        $rule = new ER2_CompanyReservation();
        $this->assertTrue($rule->wouldEstablishOwnership(null));
        $this->assertFalse($rule->wouldEstablishOwnership(42));
    }

    public function test_er3_claims_ownership_only_when_no_owner_exists(): void
    {
        $rule = new ER3_AgencyOwnershipByFirstSourced();
        $this->assertTrue($rule->wouldClaimOwnership(null));
        $this->assertFalse($rule->wouldClaimOwnership(42));
    }

    public function test_er4_throws_when_owner_already_set(): void
    {
        $rule = new ER4_OwnershipNeverFlips();
        $this->expectException(\LogicException::class);
        $rule->assertNotOverwritingOwner(existingOwnerRowId: 7, clusterId: 1);
    }

    public function test_er4_allows_when_no_owner_set_yet(): void
    {
        $rule = new ER4_OwnershipNeverFlips();
        $rule->assertNotOverwritingOwner(existingOwnerRowId: null, clusterId: 1);
        $this->assertTrue(true);
    }

    public function test_er5_company_row_is_duplicate_whenever_it_is_not_the_owner(): void
    {
        $rule = new ER5_CompanyRowsAlwaysCompared();
        $this->assertTrue($rule->isDuplicateAgainstOwner(ownerRowId: 10, thisRowId: 11));
        $this->assertFalse($rule->isDuplicateAgainstOwner(ownerRowId: 10, thisRowId: 10));
    }

    public function test_er6_agency_row_is_duplicate_only_once_it_has_reached_sourcing(): void
    {
        $rule = new ER6_AgencyRowsPrivacyShieldedUntilSourced();

        $this->assertTrue($rule->isDuplicateAgainstOwner(ownerRowId: 10, thisRowId: 11, hasReachedSourcing: true));
        $this->assertFalse($rule->isDuplicateAgainstOwner(ownerRowId: 10, thisRowId: 11, hasReachedSourcing: false));
        $this->assertFalse($rule->isDuplicateAgainstOwner(ownerRowId: 10, thisRowId: 10, hasReachedSourcing: true));
    }

    public function test_er8_describes_only_when_more_than_two_contenders(): void
    {
        $rule = new ER8_MultiContenderGeneralization();

        $this->assertSame('', $rule->describe(1));
        $this->assertSame('', $rule->describe(2));
        $this->assertNotSame('', $rule->describe(3));
    }
}
