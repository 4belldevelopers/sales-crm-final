<?php

/***########################################################
Controller     : 	CompanyDashboardController
Purpose        : 	Company Profile / Company Dashboard (Overview + Interview Pipeline)
Author         :	4Bell
Date           :	25-June-2026
Note           :	Lightweight - a handful of cheap COUNT queries only.
                  	All heavy / unsourced metrics are rendered as design values
                  	in the blade so the page stays fast with almost no DB load.
########################################################** */

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\InterviewSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyDashboardController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Company Profile';
        $this->pageIcon  = 'ti-briefcase';
    }

    /**
     * Single page with client-side tabs (Overview + Interview Stage + ...).
     * Tabs switch on the client, so the server is hit only once.
     */
    public function index()
    {
        // Current company (global "company" scope already binds it to the auth user)
        $this->company = Company::first();

        // --- Lightweight, defensive stat counts (each guarded so the page never breaks) ---
        $this->stats = [
            'active_jobs' => $this->safeCount(fn () => Job::where('status', 'active')->count()),
            'candidates'  => $this->safeCount(fn () => JobApplication::count()),
            'interviews'  => $this->safeCount(fn () => InterviewSchedule::count()),
            'offers'      => $this->safeCount(fn () => JobApplication::where('master_status_id', 5)->count()),
            'joinings'    => $this->safeCount(fn () => JobApplication::where('master_status_id', 6)->count()),
            'team'        => $this->safeCount(fn () => User::count()),
        ];

        return view('admin.company-profile.index', $this->data);
    }

    /**
     * Run a count closure, returning 0 if anything goes wrong.
     */
    private function safeCount(\Closure $callback): int
    {
        try {
            return (int) $callback();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
