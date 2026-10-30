<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * SuperAdminMeetingUpdatedEvent — Real-time event emitted on meeting comment, creation, or deletion.
 *
 * Intent:
 * Broadcasts meeting state updates over Laravel Reverb / WebSockets so open Super Admin Meeting
 * dashboards and modal views immediately reflect updated comments and records in real time.
 *
 * Security & Tenancy:
 * - Scoped to private super-admin channel: private-superadmin.meetings (authorized in routes/channels.php)
 * - Fallback public channel superadmin-meetings for legacy environments
 * - Monotonic streamSequence counter guarantees causal ordering and gap detection.
 * - RFC-4122 eventId guarantees idempotent client deduplication.
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public readonly string $eventId;

    public readonly int $streamSequence;

    public function __construct(
        public readonly int $meetingId,
        public readonly string $action, // 'comment_added', 'schedule_created', 'schedule_updated', 'schedule_deleted'
        public readonly array $data = [],
        ?string $eventId = null,
        ?int $streamSequence = null
    ) {
        $this->eventId = $eventId ?? (string) Str::uuid();
        $this->streamSequence = $streamSequence ?? (int) Cache::increment('stream:sa:meetings:seq');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('superadmin.meetings'),
            new Channel('superadmin-meetings'), // Backward-compatibility fallback
        ];
    }

    public function broadcastAs(): string
    {
        return 'SuperAdminMeetingUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId,
            'meeting_id' => $this->meetingId,
            'action' => $this->action,
            'data' => $this->data,
            'stream_sequence' => $this->streamSequence,
            'timestamp' => now()->toISOString(),
        ];
    }
}
