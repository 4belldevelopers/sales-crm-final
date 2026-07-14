# ADR 0006 — Multiple payment gateways

**Status:** Active (observed) · **[INFERRED] rationale**

## Decision
Support several payment providers in parallel: **Stripe** (via `laravel/cashier`),
**Paddle** (`laravel/cashier-paddle`), **Razorpay**, **PhonePe**, and **PayPal** (IPN).
Each has its own controller/webhook and settings.

## Reason (inferred)
- Global + India-focused SaaS: Stripe/Paddle for international cards & subscriptions;
  Razorpay/PhonePe for India (UPI, local methods); PayPal for broad reach.
- Per-tenant configurable payment settings (`payment_settings`, `PaymentSettings`
  trait) let each company/region use a suitable gateway.

## Alternatives (inferred)
- **Single gateway (Stripe only)** — simplest, but loses India coverage / local
  payment methods.
- **A payment abstraction (Omnipay-style)** — uniform interface, but extra layer over
  already-integrated SDKs.

## Tradeoffs
- ➕ Wide market & payment-method coverage; per-tenant flexibility.
- ➖ N integrations to maintain, N webhook/IPN handlers, N reconciliation paths.
- ➖ Inconsistent abstractions (Cashier for Stripe/Paddle, custom for the rest).
- ➖ Larger attack/verification surface (each webhook must verify signatures &
  be CSRF-exempt).

## Consequences
When touching billing: identify the active gateway for the tenant, use that provider's
controller/trait, verify webhook signature handling and CSRF exemption, and keep
`Order`/`CompanyPackage`/`licence_expire_on` updates consistent across gateways.
