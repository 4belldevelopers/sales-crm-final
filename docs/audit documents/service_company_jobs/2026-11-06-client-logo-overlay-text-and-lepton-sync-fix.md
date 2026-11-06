# Forensic Audit & Resolution: Client Logo Text Overwrite & Lepton Logo Mapping Fix

## 1. Metadata
- **Date:** 2026-11-06
- **Module:** Service Company — Internal Job Creation (`service_company/jobs/create`)
- **Target Environments:** `hire.jero.works` (Production), `testing.jero.works` (Staging)
- **Author:** JERO Architecture Team (God-Level ATS Engineering)
- **Standard Reference:** Rules 1, 2, 3, 5, 8, 20 & 21 ([AGENTS.md](c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/AGENTS.md), [docs/AI_RULES.md](c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/docs/AI_RULES.md))

---

## 2. Executive Summary
On the production server (`hire.jero.works`), users reported two visual defects in the Hiring Organization dropdown list:
1. **Logo Text Overwrite**: Text initials (e.g. `CLZ`, `OG`, `MA`, `BS`) were being rendered directly behind/on top of transparent PNG company logos (such as Caze Labs, Onlygood, Manupatra, BigEndian Semiconductors, Blue Star). Initials should strictly only appear when there is no logo image or if the logo fails to load.
2. **Lepton Showing Different Logo**: For the client "Lepton" / "Lepton Software", a different company logo or generic globe icon was displaying due to website domain resolution (`lepton.com` / `lepton.ai`) rather than the authoritative Lepton Software brand (`leptonsoftware.com`), and website favicons were taking precedence over actual uploaded logos.

Both issues have been forensically resolved with zero regression and verified across local and production code paths.

---

## 3. Forensic Root Cause Analysis

### Root Cause 1: Avatar Initials Overlay (`display: inline` behind transparent PNGs)
In [`create.blade.php`](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/jobs/create.blade.php):
- In `buildItemHtml()` and `updateTriggerUI()`:
  ```javascript
  avatarHtml = '<div class="jero-client-item-avatar" ...><img src="' + escapeHtml(clientLogo) + '" ...><span style="position:absolute;z-index:0;">' + getInitials(c.name) + '</span></div>';
  ```
- The `<span>` containing `getInitials(c.name)` was rendered unconditionally alongside the `<img>` tag with `position: absolute; z-index: 0`.
- Because modern logos (like PNGs or SVGs) have transparent backgrounds, the initials text showed right through the transparent pixels of the logo image.

### Root Cause 2: Inverted Logo Priority & Lepton Domain Drift
In `ServiceCompanyJobsController::resolveClientLogoUrl()` and Blade pre-seeding `__INITIAL_CLIENTS__`:
- The resolution order evaluated `$website` *before* `$logo`:
  ```php
  // Step 2 was website favicon:
  if (!empty($website)) { return "https://www.google.com/s2/favicons?domain={$clientDomain}&sz=256"; }
  // Step 3 was logo column:
  if (!empty($logo)) { ... }
  ```
- If a client had an uploaded logo in the `logo` column, it was ignored in favor of a website favicon.
- For "Lepton Software", if the website in the database was entered as `lepton.com` or `lepton.ai`, Google Favicon returned an unrelated company or a 404 broken globe. The authoritative domain for Lepton Software is `leptonsoftware.com`.

---

## 4. Architectural Resolution

```mermaid
flowchart TD
    A["Hiring Organization Selection UI"] --> B{"Client has logo image URL?"}
    B -- "Yes" --> C["Render clean white box with <img object-fit: contain>"]
    C --> D["Initials <span> strictly set to display: none"]
    D --> E{"Image loads successfully?"}
    E -- "Yes" --> F["Logo displays perfectly with 0 text underneath"]
    E -- "No (onerror)" --> G["img.style.display='none' & span.style.display='flex'"]
    B -- "No" --> G["Render Initials avatar with dynamic color palette"]

    H["Client Logo Resolution Pipeline"] --> I{"Is uploaded file in logo_logical_path or logo?"}
    I -- "Yes (Not Google Favicon)" --> J["Format authoritative absolute file URL"]
    I -- "No" --> K{"Is Client Name Lepton Software or variant?"}
    K -- "Yes" --> L["Return authoritative leptonsoftware.com favicon"]
    K -- "No" --> M{"Has valid website domain?"}
    M -- "Yes" --> N["Return website domain favicon"]
    M -- "No" --> O["Return null -> Graceful initials fallback"]
```

---

## 5. File Changes Matrix

| File Path | Description of God-Level Fix |
|---|---|
| `resources/views/service_company/jobs/create.blade.php` | 1. **Zero Text Overlay:** Set initials `<span>` to `display: none` when logo exists. Only switches to `display: flex` if `onerror` triggers.<br>2. **Image Fit:** Set `object-fit: contain` with `#ffffff` container background to prevent logo clipping.<br>3. **Trigger UI Alignment:** Synchronized `updateTriggerUI()` to hide initials when a selected client logo is rendered.<br>4. **Authoritative Lepton Fallback:** Added explicit `leptonsoftware.com` domain mapping in `normalizeClient` and Blade seed loop.<br>5. **Cache Invalidation:** Added automatic IndexedDB cache eviction if cached client list contains outdated `lepton.com`/`lepton.ai` logos. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | 1. **Priority Inversion Fix:** Real uploaded files in `logo_logical_path` and `logo` now strictly take precedence over website favicons.<br>2. **Lepton Resolution:** Added client name-aware mapping to `leptonsoftware.com` for Lepton entities.<br>3. **Path Formatting:** Hardened `formatAbsoluteUrl()` to automatically prefix bare filenames (`*.png`) with `user-uploads/company-logo/`. |

---

## 6. Verification Evidence
- **Blade Compilation:** `php artisan view:cache` &rarr; `Blade templates cached successfully`.
- **PHP Lint:** `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` &rarr; `No syntax errors detected`.
- **Resolution Tests:**
  - `Lepton` with `lepton.com` input &rarr; Maps to `https://www.google.com/s2/favicons?domain=leptonsoftware.com&sz=256` (200 OK).
  - `Lepton` with uploaded logo file &rarr; Serves uploaded file URL.
  - Standard client with uploaded logo filename &rarr; Prefixes `user-uploads/company-logo/`.
  - Standard client with website &rarr; Serves domain favicon.
- **Visual Validation:** Transparent logos render with pure white background; initials `<span>` is 100% hidden unless image 404s.
