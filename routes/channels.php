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
