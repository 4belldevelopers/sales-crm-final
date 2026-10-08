<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function showForm()
    {
        return response()->json(['status' => 'success']);
    }

    public function upload(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    public function search(Request $request)
    {
        return response()->json(['status' => 'success']);
    }
}
