<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a user logs in from a new device/session.
 * Broadcasts to the user's private channel to auto-logout any previously open browser tabs.
 */
class SingleDeviceLogout implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $newSessionId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SingleDeviceLogout';
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'message' => 'You have been logged in from another device. Please login again.',
            'redirect' => url('/login'),
        ];
    }
}
