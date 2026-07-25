<?php

use Illuminate\Support\Facades\Broadcast;

// Existing: 1:1 user→user private channel for the navbar toast
// notifications already shipped in service_company_layout.blade.php
// (~line 1199). Kept verbatim for backward compatibility.
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Phase 8 — company-scoped private channel. Same-tenant
// recruiters see ResumeParsed events while sitting on the
// create/edit form (so empty fields can be auto-filled) and
// while on the candidate detail page (so the board edits
// propagate without a manual reload).
//
// Auth model: a user is in this channel iff their
// `users.company_id` matches the company's id in the route key.
// This is the same tenant-isolation shape the rest of the app
// uses (CLAUDE.md BUG-03, memory note), so an attacker
// attempting to subscribe to a foreign tenant sees a 403 at
// the websocket layer before the message is dispatched.
//
// Echo client subscription example (from resume-populate-if-empty.js):
//   window.Echo.private('App.Models.Company.' + COMPANY_ID)
//            .listen('.resume.parsed', handler);
Broadcast::channel('App.Models.Company.{companyId}', function ($user, $companyId) {
    if (! $user || ! isset($user->company_id)) {
        return false;
    }
    return (int) $user->company_id === (int) $companyId;
});
