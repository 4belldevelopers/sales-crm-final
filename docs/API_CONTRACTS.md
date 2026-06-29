# API & Route Contracts

> This app is a **server-rendered Blade + AJAX (DataTables) monolith**, not a
> REST-first API. "Endpoints" are mostly web routes returning HTML pages or JSON for
> DataTables / Alpine. There is **no formal API versioning** and **no OpenAPI spec**.
> Treat this as a routing contract guide. **[INFERRED]** where noted.

---

## Route files & scale

| File | ~Routes | Scope |
|---|---|---|
| `routes/web.php` | ~1,180 | Public front, webhooks, Admin, Staffing, WhatsApp, misc |
| `routes/service.php` | ~413 | Service-company (agency) app — prefix `service_company/`, name `service_company.` |
| `routes/product.php` | ~224 | Product-company app — prefix `product_company/` |
| `routes/superadmin.php` | ~208 | Platform admin |
| `routes/auth.php` | ~15 | Login/register/password (Breeze-style) |
| `routes/console.php` | — | Scheduled command bindings |
| `routes/channels.php` | — | Reverb broadcast channel auth |

To find a route: `php artisan route:list --path=<fragment>` or grep the route file
for the `->name('...')` you need. **Do not read route files whole** (`web.php` ≈158 KB).

---

## Auth & middleware contract

Middleware aliases (`bootstrap/app.php`):

| Alias | Class | Meaning |
|---|---|---|
| `auth` | `Authenticate` | Logged-in session required |
| `guest` | `RedirectIfAuthenticated` | Only for unauthenticated |
| `super-admin` | `App\Http\Middleware\SuperAdmin` | Platform-owner only |
| `EnsureOneDeviceLogin` | same | Single active session per user |
| `active-package` | `App\Http\Middleware\Package` | Tenant must have valid plan |
| `custom-package` | `App\Http\Middleware\CustomPackage` | Feature gating by plan |
| `role` / `permission` / `role_or_permission` | Spatie | RBAC |
| `throttle`, `signed`, `bindings`, `can` | Laravel core | rate limit / signed URLs / model binding / policy |

**Typical group nesting** (from `routes/service.php`):
```
Route::group(['middleware' => 'auth'], fn() =>
  Route::prefix('service_company')->name('service_company.')->group(fn() =>
    Route::group(['middleware' => ['EnsureOneDeviceLogin']], fn() =>
      Route::group(['middleware' => ['custom-package']], fn() =>
        /* feature routes */ ))))
```
**When adding a route, place it inside the matching persona + middleware group.**

Auth also supports **JWT** (`tymon/jwt-auth`) and **Socialite/Google** OAuth
(`GoogleController`). **[INFERRED]** JWT is used for specific API-ish/mobile or
external flows; confirm guard in `config/auth.php` before relying on it.

---

## Request schemas

Validation is centralized in **`app/Http/Requests/`** (55 form-request classes).
Reuse the existing `*Request` for an entity rather than inlining `$request->validate()`.
To find one: `ls app/Http/Requests | grep -i <entity>`.

---

## Response conventions

| Pattern | Returns |
|---|---|
| Page route (`jobs/data`, `clients/viewmore/{id}`) | Blade HTML view |
| Paired `*_ajax` route (`jobs_ajax/all_data`) | **DataTables JSON** (`{data, recordsTotal, recordsFiltered}`) |
| Action route (`clients/update/{id}` POST) | Redirect w/ flash, or JSON `{status, message}` **[INFERRED]** |
| Webhooks (`StripeWebhookController`, `RazorpayWebhookController`, `PaypalIPNController`) | 200/4xx to provider |
| Broadcast (`MessageSent` event) | pushed over Reverb websocket |

**DataTables list pattern:** almost every list screen is two routes — a page route
and an `*_ajax` data route that yajra DataTables calls. Match this pair when adding lists.

---

## Pagination

- **List screens:** server-side pagination via **yajra DataTables** (`start`,
  `length`, `search[value]`, `order[...]` query params → JSON envelope).
- **Elasticsearch-backed lists:** pagination via ES `from`/`size` inside the
  `Elastic*Index` services. **[INFERRED]**
- Standard Eloquent `->paginate()` appears in simpler screens.

---

## Webhooks / external callbacks (in `routes/web.php`)

| Route | Controller | Provider |
|---|---|---|
| Stripe webhook | `StripeWebhookController` | Stripe/Cashier |
| Razorpay webhook | `RazorpayWebhookController` | Razorpay |
| PayPal IPN | `PaypalIPNController` | PayPal |
| PhonePe callback | `PhonePePaymentController` | PhonePe |
| `/whatsapp/*` | `HomeController` | external WhatsApp Node service |
| Reverb channel auth | `routes/channels.php` | Reverb |

⚠️ **Webhook routes must be CSRF-exempt** — verify `VerifyCsrfToken` exclusions when
touching them.

---

## Versioning

**None.** No `/api/v1` namespace, no versioned contracts. If introducing a real API,
propose an ADR (see [adr/](adr/)) rather than bolting onto `web.php`.

---

## ⚠️ Debug/test routes left in code

`routes/web.php` contains live debug routes (e.g. `/mail-test`, `/whatsapp/connect`).
These should not exist in production. See [BAD_PATTERNS.md](BAD_PATTERNS.md).
