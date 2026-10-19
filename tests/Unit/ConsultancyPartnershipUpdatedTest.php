<?php

namespace Tests\Unit;

use App\Events\ConsultancyPartnershipUpdated;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * ConsultancyPartnershipUpdatedTest — Verifies contract and resilience properties of ConsultancyPartnershipUpdated.
 */
class ConsultancyPartnershipUpdatedTest extends TestCase
{
    /** @test */
    public function it_has_correct_broadcast_channel_and_event_name(): void
    {
        $event = new ConsultancyPartnershipUpdated(
            companyId: 42,
            mutationType: 'invitation_sent',
            spartnerAll: 5,
            sentRequest: 12,
            extra: ['source' => 'test']
        );

        $channel = $event->broadcastOn();
        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertSame('private-company.42', $channel->name);
        $this->assertSame('consultancy.updated', $event->broadcastAs());
    }

    /** @test */
    public function it_builds_expected_broadcast_payload(): void
    {
        $event = new ConsultancyPartnershipUpdated(
            companyId: 10,
            mutationType: 'invitation_sent',
            spartnerAll: 3,
            sentRequest: 7,
            extra: ['key' => 'val']
        );

        $payload = $event->broadcastWith();

        $this->assertSame(10, $payload['company_id']);
        $this->assertSame('invitation_sent', $payload['mutation_type']);
        $this->assertSame(3, $payload['spartnerAll']);
        $this->assertSame(7, $payload['sentRequest']);
        $this->assertSame(['key' => 'val'], $payload['extra']);
        $this->assertArrayHasKey('timestamp', $payload);
    }

    /** @test */
    public function it_has_bounded_retry_and_resilience_configuration(): void
    {
        $event = new ConsultancyPartnershipUpdated(companyId: 1, mutationType: 'test');

        $this->assertTrue($event->afterCommit, 'Must dispatch after commit');
        $this->assertSame(3, $event->tries, 'Must bound attempts to 3');
        $this->assertSame([10, 30, 60], $event->backoff, 'Must have exponential backoff');
        $this->assertSame(15, $event->timeout, 'Must have 15s timeout cap');
    }

    /** @test */
    public function it_logs_structured_warning_on_permanent_failure_without_throwing(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'WebSocket broadcast permanently failed')
                    && $context['company_id'] === 99
                    && $context['mutation_type'] === 'invitation_sent'
                    && str_contains($context['error'], 'cURL error 7');
            });

        $event = new ConsultancyPartnershipUpdated(
            companyId: 99,
            mutationType: 'invitation_sent',
            spartnerAll: 1,
            sentRequest: 2
        );

        $event->failed(new \RuntimeException('cURL error 7: Failed to connect'));
        $this->assertTrue(true);
    }
}
