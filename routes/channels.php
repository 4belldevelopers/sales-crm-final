<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// V2 Dashboard Phase 5 — private channel for dashboard WebSocket push events.
// Only members of the matching company may subscribe.
Broadcast::channel('company.{companyId}.dashboard', function ($user, int $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

// Phase 4 & 5 — private tenant-isolated channel for job recruitment pipeline events
Broadcast::channel('tenant.{companyId}.job.{jobId}', function ($user, int $companyId, int $jobId) {
    return (int) $user->company_id === (int) $companyId;
});

// Bench Talent Requests — private tenant-isolated channel for job candidate request events
Broadcast::channel('company.{companyId}.job.{jobId}', function ($user, int $companyId, int $jobId) {
    return (int) $user->company_id === (int) $companyId;
});

// Company-wide private tenant channel for job status, candidate assignment, and drawer push events
Broadcast::channel('company.{companyId}', function ($user, int $companyId) {
    return (int) $user->company_id === (int) $companyId;
});

// Super Admin private channel for Custom Plan Requests real-time synchronization
Broadcast::channel('superadmin.custom-plans', function ($user) {
    return (bool) ($user && ! empty($user->is_superadmin));
});

// Super Admin private channel for Company real-time synchronization
Broadcast::channel('superadmin.companies', function ($user) {
    return (bool) ($user && ! empty($user->is_superadmin));
});
