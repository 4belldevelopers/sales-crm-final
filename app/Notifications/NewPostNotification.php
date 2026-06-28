<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function via($notifiable)
    {
        // store in database and broadcast (optional)
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New post created',
            'post_id' => $this->post->id ?? null,
            'message' => $this->post->title ?? 'A new post',
        ];
    }

    // Optional: explicit broadcast payload
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New post created',
            'post_id' => $this->post->id ?? null,
            'message' => $this->post->title ?? 'A new post',
            'url' => url('/posts/' . ($this->post->id ?? '')),
        ]);
    }
}
