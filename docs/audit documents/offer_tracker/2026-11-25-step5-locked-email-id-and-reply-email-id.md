# UI/UX Specification & Guard: Page 5 System-Locked Email ID & Reply Email ID Fields

> **Document Type:** UI/UX Specification & Security Lock  
> **Module:** Offer Tracker → Offer Drawer → Step 5 (`#step5`, Send Offer Letter)  
> **Date:** 2026-11-25  
> **Standard:** Rule 14 (15.6" Screen Fit), Rule 20 (Card + Sub-Card Design Standard), Rule 21 (Module Audit Standard), Rule 26 (/unslop Standard)

---

## 1. Executive Summary

On Page 5 (**Send Offer Letter**) of the Offer Drawer, the **Email ID** (From) and **Reply Email ID** fields previously rendered as standard text/email inputs, allowing user interaction or displaying static platform defaults (`notify@jeroworks.com`).

Per the user's specification, both fields have been transformed into **system-generated, locked information containers**:
1. **Dynamic Logged-In User Population:** Both `Email ID` and `Reply Email ID` automatically resolve and display the authenticated user's email address (`auth()->user()->email`).
2. **Locked / Non-Editable UI Design:** Both fields no longer appear as editable input boxes. They are styled with `.co-send-locked-field` (`#f8fafc` soft background, `#e2e8f0` crisp hairline border, `height: 40px`, `#1e293b` font-weight 600, lock SVG padlock icon), preventing typing, focus rings, pasting, or overwriting.
3. **Form Integrity Preserved:** A hidden `<input type="hidden" id="co_send_replyto" value="{{ $so_reply_to_email }}">` and `#co_send_fromemail` preserve seamless JavaScript form serialization when the offer email is dispatched via AJAX (`#kt_offercreate_save`).

---

## 2. Visual Architecture

```
┌────────────────────────────────────────────────────────┬────────────────────────────────────────────────────────┐
│ ✉ Email ID                                             │ ↩ Reply Email ID                                       │
│ ┌────────────────────────────────────────────────────┐ │ ┌────────────────────────────────────────────────────┐ │
│ │ ✉ vibhanshu@4bell.com                            🔒 │ │ │ ↩ vibhanshu@4bell.com                            🔒 │ │
│ └────────────────────────────────────────────────────┘ │ └────────────────────────────────────────────────────┘ │
│ Sender address associated with your logged-in account  │ Direct candidate replies will be routed to your account│
└────────────────────────────────────────────────────────┴────────────────────────────────────────────────────────┘
```

---

## 3. Implementation Details

### 3.1 PHP Controller / Blade Data Population
In [create-offer.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create-offer.blade.php#L5116-L5119):
```php
$so_from_name      = auth()->user()->name ?? '';
$so_from_email     = auth()->user()->email ?? 'notify@jeroworks.com';
$so_reply_to_email = auth()->user()->email ?? 'notify@jeroworks.com';
```

### 3.2 HTML Locked Field Component
In [create-offer.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create-offer.blade.php#L5230-L5260):
```html
<!-- Row 1: Email ID & Reply Email ID (2 columns) -->
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fs-7 fw-semibold text-gray-700 mb-1 d-flex align-items-center gap-1.5">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            Email ID
        </label>
        <div class="co-send-locked-field" title="System-locked sender email (logged-in user: {{ $so_from_email }})">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="flex-shrink-0"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            <span class="text-truncate fw-semibold text-gray-800 fs-7 flex-grow-1">{{ $so_from_email }}</span>
            <svg class="co-chip-lock" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" title="Locked: system-generated email"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
        </div>
        <div class="co-send-hint">Sender address associated with your logged-in account (read-only)</div>
    </div>
    <div class="col-md-6">
        <label class="form-label fs-7 fw-semibold text-gray-700 mb-1 d-flex align-items-center gap-1.5">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
            Reply Email ID
        </label>
        <div class="co-send-locked-field" title="System-locked reply-to email (logged-in user: {{ $so_reply_to_email }})">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" class="flex-shrink-0"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
            <span class="text-truncate fw-semibold text-gray-800 fs-7 flex-grow-1">{{ $so_reply_to_email }}</span>
            <svg class="co-chip-lock" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" title="Locked: system-generated email"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            <input type="hidden" id="co_send_replyto" value="{{ $so_reply_to_email }}">
        </div>
        <div class="co-send-hint">Direct candidate replies will be routed to your account email (read-only)</div>
    </div>
</div>
```

### 3.3 CSS Styles
In [create-offer.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create-offer.blade.php#L2250-L2275):
```css
.co-send-locked-field {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    height: 40px !important;
    padding: 0 12px !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    color: #1e293b !important;
    cursor: default !important;
    user-select: text !important;
    transition: border-color 0.15s ease !important;
}
.co-send-locked-field:hover {
    border-color: #cbd5e1 !important;
}
.co-send-locked-field .co-chip-lock {
    width: 13px !important;
    height: 13px !important;
    color: #64748b !important;
    opacity: 0.75 !important;
    flex-shrink: 0 !important;
}
```

---

## 4. Verification Checklist

- [x] **Email ID Populated:** `auth()->user()->email` automatically fetched and displayed.
- [x] **Reply Email ID Populated:** `auth()->user()->email` automatically fetched and displayed.
- [x] **Completely Non-Editable:** Users cannot type, click-focus, paste, or overwrite.
- [x] **System-Generated Visual Style:** Rendered in locked box `#f8fafc` with padlock SVG icon `🔒`.
- [x] **Form Transmission Compatibility:** Hidden input `#co_send_replyto` and `#co_send_fromemail` preserve AJAX payload values.
- [x] **PHP Syntax Verification:** `php -l` passed with zero errors.
- [x] **Zero Business Logic Mutations:** Adheres strictly to Rules 1, 2, 7, 14, 20, 21, and 26.
