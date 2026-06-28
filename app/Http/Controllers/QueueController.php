<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function runQueue()
    {
        try {
            // Run the queue worker via Artisan
            Artisan::call('queue:work --queue=emails --timeout=120 --tries=3');
            // Return a response to confirm that the queue is being processed
            return response()->json(['message' => 'Queue worker is running!']);
        } catch (\Exception $e) {
            // Handle any exceptions if something goes wrong
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
