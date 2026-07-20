<?php

namespace Tests\Unit\OwnershipEngine;

use App\Jobs\AttemptOwnershipCompanyReservation;
use App\Services\OwnershipEngine\DataTransferObjects\OwnershipResult;
use App\Services\OwnershipEngine\OwnershipEngine;
use Tests\TestCase;

/**
 * Phase 3: the queued job's handle() must call the engine with the right row id, and must
 * swallow any exception the engine throws (never rethrow) — the same isolation guarantee
 * the old inline try/catch call sites had, now living inside the job instead. Pure mock —
 * no database — but uses Laravel's TestCase (not raw PHPUnit) since handle()'s catch block
 * calls the Log facade, which needs the app container bootstrapped.
 */
class AttemptOwnershipCompanyReservationJobTest extends TestCase
{
    public function test_handle_calls_engine_with_the_row_id(): void
    {
        $engine = $this->createMock(OwnershipEngine::class);
        $engine->expects($this->once())
            ->method('attemptCompanyReservation')
            ->with(42)
            ->willReturn(new OwnershipResult(42, OwnershipResult::STATE_OWNER, 1));

        $job = new AttemptOwnershipCompanyReservation(42);
        $job->handle($engine);

        $this->assertTrue(true); // reaching here means no exception escaped
    }

    public function test_handle_swallows_engine_exceptions(): void
    {
        $engine = $this->createMock(OwnershipEngine::class);
        $engine->method('attemptCompanyReservation')->willThrowException(new \RuntimeException('boom'));

        $job = new AttemptOwnershipCompanyReservation(42);
        $job->handle($engine); // must not throw

        $this->assertTrue(true);
    }
}
