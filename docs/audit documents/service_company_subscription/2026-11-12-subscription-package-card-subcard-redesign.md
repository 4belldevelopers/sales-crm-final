# Service Company Subscription & Wallet Center — Rule 20 Enterprise Card + Sub-Card Redesign

## 1. Metadata
- **Date:** 2026-11-12
- **Module:** Service Company — Subscription & Wallet Center (`/service_company/new-subscribe/newindex`)
- **Persona Scope:** Service Company (`resources/views/service_company/`)
- **Design Standard:** Rule 20 — Modern Card + Sub-Card Enterprise Design System ([UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md](../../UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md))
- **Architecture:** Strict CQRS (0% MySQL changes; existing `SubscriptionService` Redis-cached projection + lazy AJAX transaction endpoint preserved)

---

## 2. Executive Summary
Redesigned the **Subscription & Wallet Center** (`newindex.blade.php`), its dual wallet selector partial (`_wallet_cards.blade.php`), and the AJAX transaction history partial (`subscription_transactions.blade.php`) into the **3-Tier Modern Card + Sub-Card Enterprise Design System**:
- **Tier 1 (Pure White Toolbar Card):** Consolidated branding header, live wallet status pill (`Operational Wallet Active` / `Sales Wallet Active`), 300ms debounced live search (`#subscription_search` with clear `×` button across packages and loaded transactions), integrated Cart badge pill (`$carts_count`), and primary `Buy Coins` CTA. Removed the legacy absolute-positioned toolbar cart overlap.
- **Tier 2 (Pure White Sub-Tabs Card):** Dedicated navigation card with single active primary blue accent (`#009ef7` / `#f1faff`) for `Overview`, `Packages & Pricing` (with live wallet plan count), and `Transactions` (with loaded total count), plus isolated wallet security chip.
- **Tier 3 (Primary Card + Discrete Sub-Card Stream):**
  - **Primary Card 1 — Active Coin Wallets:** 2-column discrete wallet selector sub-cards (Operational & Sales) with left-border active accent (`3.5px`), usage ring (`tabular-nums`), and 3-box metric footer (`Used Coins`, `Purchased`, `Days Validity`), plus keyboard accessibility (`Enter` / `Space`).
  - **Primary Card 2 — Current Plan & Coin Telemetry:** 4-column plan breakdown sub-cards + 4-column telemetry KPI sub-cards (`Total Coins Purchased`, `Coins Available`, `Coins Used`, `Total Spend`), with clean empty-state sub-card when no plan is active.
  - **Primary Card 3 — Coin Packages & Upgrade Plans:** Unified responsive 4-column CSS Grid (`repeat(4, minmax(0, 1fr))`) displaying pricing, original discount strikethrough, coin allocation pills, action buttons (`.upgradeselect`, `.btn-contact-sales`, `.customsubscription`, `.customsubscription_sal`), and full feature matrices without duplicate grids or negative-margin badge clipping.
  - **Primary Card 4 — Enterprise Guarantees & Custom Plans:** 4-column SLA guarantees (`Instant Activation`, `Secure Payments`, `GST Invoices`, `24×7 Support`), 2-column value proposition & custom enterprise inquiry card, and high-contrast upgrade banner.
  - **Transactions Tab (`subscription_transactions.blade.php`):** Replaced raw HTML `<table>` (`#walletTransactionsTable`) with a 5-subcard Card Stream (Order & Date, Package & Wallet, Coins Credited, Amount & Gateway, Status & PDF Receipt download) with semantic left-border status accents and preserved AJAX pagination (`.pagination a.page-link`).

---

## 3. Architecture & Data Flow Preservation
- **Controller & Service:** Zero edits to `ManageSubscriptionController.php` or `SubscriptionService.php`. All Redis caching (`tenant_{$companyId}_wallet_center_data`, `master_coin_packages_with_features`) and lazy AJAX loading (`service_company.transactions.subscriptionAjax`) remain untouched.
- **Business Logic & Interactive Contracts Preserved:**
  - `.package-switch` (`data-kt-plan="operational|sales"`) switches `.operational-packages` / `.salse-packages`, updates Tier 1 badge/CTA label, and updates Tier 2 package count.
  - `.upgradeselect` posts to `service_company.subscribe.checkoutSubscribe` and redirects to `service_company.subscribe.checkoutSubscribeIist`.
  - `.customsubscription`, `.customsubscription_sal`, `.btn-contact-sales` trigger SweetAlert confirmation and post to `service_company.subscribe.customCheckoutSubscribe`.
  - `#tab=overview`, `#tab=packages`, and `#tab=transactions` hash routing preserved (`checkout-subscribe.blade.php` and sidebar links continue working seamlessly).
- **Null-Safety Fix:** Replaced unguarded `$current_coins_operational[0]['custome_pck']` / `$current_coins_sales[0]['custome_pck']` lookups with null-coalescing (`?? 0`) so accounts without active plans render cleanly without array key warnings.

---

## 4. File Changes Matrix

| File | Change Type | Purpose |
| :--- | :--- | :--- |
| `resources/views/service_company/subscription/newindex.blade.php` | Modified | Redesigned into isolated `.sub-wrapper` Rule 20 3-Tier Card + Sub-Card layout with live search & responsive grids |
| `resources/views/service_company/subscription/partials/_wallet_cards.blade.php` | Modified | Upgraded dual wallet cards to Rule 20 sub-cards with 3-metric grid, `tabular-nums`, and FA6 glyphs |
| `resources/views/service_company/ajax_pages/subscription_transactions.blade.php` | Modified | Converted raw `<table>` ledger into Rule 20 5-subcard transaction cards with semantic left accents and search scope |
| `tests/Feature/ServiceCompanySubscriptionTest.php` | Created | Automated Blade/View & contract verification tests for Subscription & Wallet Center |

---

## 5. 15.6-Inch Screen Fit & Isolation
- **CSS Isolation:** All styles strictly scoped under `.sub-wrapper` using Poppins typography, `font-variant-numeric: tabular-nums`, and explicit FontAwesome 5/6 glyph rules. Removed legacy `.hg-plan-btn-group`, `.WebRupee`, and global `.active { color: white !important; }` rules from `newindex.blade.php`.
- **Responsive Grids (`1366×768` & `1920×1080`):** Uses `minmax(0, ...)` CSS Grids (`4 cols` >= 1200px, `2 cols` 768–1199px, `1 col` < 768px) with `overflow-x: hidden` on `body` to guarantee zero horizontal page scroll.

---

## 6. Verification Evidence
- `php -l` syntax checks on all edited Blade views and PHP test file.
- `php artisan test tests/Feature/ServiceCompanySubscriptionTest.php` verifying view existence, Rule 20 3-tier markup, empty-state safety, non-table transaction cards, and preserved JS/route contracts.
