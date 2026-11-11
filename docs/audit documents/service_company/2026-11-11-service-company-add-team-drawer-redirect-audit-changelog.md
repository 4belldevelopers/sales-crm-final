# Service Company — Add Team Flow Migration & Drawer Auto-Open Changelog

## Metadata
- **Date:** 2026-11-11
- **Module:** Service Company Team & Account (`/service_company/account`, `/service_company/team`)
- **Author:** Antigravity AI Pair Programmer
- **Standard Applied:** Modern Card + Sub-Card Enterprise Design System (Rule 20), Multi-Tenant Isolation (Rule 6), Zero Business Logic Mutations (Rule 7), Drawer Master Standards.

---

## Executive Summary
Per user directive, the legacy standalone "Add Profile" page (`/service_company/team/create`) has been permanently decommissioned and deleted. The "Add Team" action button on the Company Profile page now redirects directly to the primary Team management page (`/service_company/team?action=invite`), where the modern **Add/Invite Members** sliding drawer (`#inviteDrawer`) automatically opens seamlessly upon load.

---

## Architecture & Data Flow

```
   [ Company Profile Header ]
        "Add Team" Button
               │
               ▼
   [ GET /service_company/team?action=invite ]
               │
               ▼
   [ Team Management Page (`service_company.team.index`) ]
               │
       (Auto-detection in JS)
               │
               ▼
   [ openInviteDrawer() ] ───► Opens `#inviteDrawer` + `#inviteOverlay`
                               Loads cached roles from RAM (0 HTTP requests)
                               Resets form & autofocuses input
```

If a user accesses `/service_company/team/create` directly, `ServiceCompanyTeamController::create()` issues an immediate HTTP 302 redirect to `route('service_company.team.index', ['action' => 'invite'])`.

When the user closes the drawer, URL search parameters (`action=invite`) are automatically stripped using `window.history.replaceState` to maintain clean browser history without page reloads.

---

## File Changes Matrix

| File Path | Change Type | Description |
| :--- | :--- | :--- |
| `resources/views/service_company/team/create.blade.php` | Deleted | Completely removed the legacy, deprecated standalone team creation page. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php` | Modified | Updated `create()` method to redirect immediately to `route('service_company.team.index', ['action' => 'invite'])`. |
| `resources/views/service_company/account/index.blade.php` | Modified | Updated the "Add Team" action button link from `team.create` to `route('service_company.team.index', ['action' => 'invite'])`. |
| `resources/views/service_company/team/index.blade.php` | Modified | Added URL query parameter auto-detection (`action=invite`, `invite=1`, `#invite`) to trigger `openInviteDrawer()` on page load, and cleaned URL state on drawer dismiss. |

---

## Verification Evidence
1. **PHP Syntax & Code Quality:**
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`: Pass.
   - `php -l resources/views/service_company/account/index.blade.php`: Pass.
   - `php -l resources/views/service_company/team/index.blade.php`: Pass.
   - `./vendor/bin/pint app/Http/Controllers/ServiceCompany/ServiceCompanyTeamController.php`: Pass.
2. **Behavioral Test:**
   - Calling `ServiceCompanyTeamController::create()` returns HTTP 302 redirecting to `https://testing.jero.works/service_company/team?action=invite`.
   - Clicking "Add Team" on `/service_company/account` opens `/service_company/team?action=invite`.
   - On `/service_company/team?action=invite`, `#inviteDrawer` opens automatically and closes cleanly with state cleanup.
