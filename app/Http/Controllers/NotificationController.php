<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\NewPostNotification; // 👈 use your notification

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        // Example: Send to user with ID 1
        $user = User::withoutGlobalScopes()->find(3);

        if ($user) {
            // Mock data for broadcast
            $post = (object) ['id' => 123, 'title' => 'Test post from controller'];

            $user->notify(new NewPostNotification($post));

            return response()->json(['status' => 'Notification sent successfully']);
        }

        return response()->json(['error' => 'User not found'], 404);
    }
}
