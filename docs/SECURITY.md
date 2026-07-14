# Security Risk Register & Remediation

> Findings from static review + architecture. Severities 🟥/🟧/🟨. This app handles
> **PII at scale** (candidate names, emails, phones, resumes, salaries) across many
> tenants + payment flows — security is existential at 10K-company scale. No code changed.

---

## 🟥 SEC-01 — Live secrets in the working tree (`.env`)
`.env` contains real credentials: MySQL **root** password, **Elasticsearch password in
plaintext** (`ELASTICSEARCH_PASS=...`), AWS keys, Stripe/Razorpay/PhonePe keys, Google
& Zoom OAuth secrets, OpenAI key, Reverb secrets.
**Risk:** full compromise if the tree/backups leak. **Fix:** ensure `.env` is gitignored
(check git history with `git log -- .env`); **rotate every exposed secret now**; use a
secrets manager (AWS Secrets Manager / Vault) in prod; never commit installers
(`composer-setup.php`). Use least-privilege DB users (not `root`).

## 🟥 SEC-02 — Cross-tenant data leakage (multi-tenancy)
See [BUG-03](BUG_REPORT.md). `JobApplication` company scope commented out; raw
`DB::table()` bypasses scopes. **Risk:** Tenant A reads Tenant B's candidates/clients.
**Fix:** enforce `company_id` centrally (base query helper / mandatory scope), add
automated tenant-isolation tests in CI, audit every raw query. This is the #1 trust risk
for a talent marketplace.

## 🟥 SEC-03 — Stored XSS via 1,227 `{!! !!}` outputs
See [BUG-04](BUG_REPORT.md). **Risk:** attacker-supplied candidate/client/notes data
executes JS in a recruiter's session → account takeover, data theft. **Fix:** escape
user data (`{{ }}`), sanitize trusted HTML server-side, add a strict **Content-Security-
Policy** header as defense-in-depth.

## 🟥 SEC-04 — Debug exposure (`dd()` + debug routes + APP_DEBUG)
63 active `dd()` ([BUG-01](BUG_REPORT.md)), `/mail-test` & `/whatsapp/*`
([BUG-06](BUG_REPORT.md)), empty exception handler ([BUG-10](BUG_REPORT.md)).
**Risk:** info disclosure (stack traces, internal data), unauthenticated mail/abuse.
**Fix:** remove debug code, gate debug routes to local, `APP_DEBUG=false` in prod,
centralized exception reporting.

---

## 🟧 SEC-05 — Webhook & payment endpoint integrity
Multiple gateways (Stripe/Razorpay/PayPal/PhonePe). **Risks:** unverified webhook
signatures → forged payment events; missing **idempotency** → double-processing;
CSRF-exemption must be correct. **Fix:** verify provider signatures on every webhook,
add idempotency keys (store processed event IDs), confirm `VerifyCsrfToken::$except`
covers only the webhook paths, rate-limit them.

## 🟧 SEC-06 — File upload safety (resumes)
Resumes (PDF/DOC) parsed (`smalot/pdfparser`, phpword) and stored on S3. **Risks:**
malicious files, XXE/zip-bombs via office parsers, content-type spoofing, SSRF if URLs
are fetched (note `Mail::raw` `/mail-test` fetches a remote URL). **Fix:** validate
MIME + extension + size, scan (ClamAV), store outside web root (S3 private + signed
URLs), disable XML external entities in parsers, never fetch arbitrary user URLs.

## 🟧 SEC-07 — AuthZ depth (RBAC + IDOR)
Spatie permissions exist, but with god controllers and raw queries, **object-level
authorization** (can *this* user touch *this* candidate/job/client id?) is easy to miss
→ IDOR. **Fix:** policies/gates per resource, check ownership (`company_id` + role) on
every `{id}` route, default-deny.

## 🟧 SEC-08 — Session / auth hardening
`EnsureOneDeviceLogin` exists (good). **Verify/add:** secure+httpOnly+SameSite cookies,
session fixation regeneration on login, password hashing via bcrypt/argon (confirm no
plaintext — none found in Auth, good), rate-limit + lockout on login, 2FA for admin/
super-admin, JWT expiry/rotation/revocation for `tymon/jwt-auth`.

## 🟧 SEC-09 — Octane state bleed (security flavor)
Long-lived workers can leak one tenant's data into another request via static/singleton
state. **Fix:** no request/tenant data in statics; reset container bindings; test under
Octane. (See ADR 0003.)

---

## 🟨 SEC-10 — Transport & headers
**Add:** HSTS, CSP, `X-Content-Type-Options`, `X-Frame-Options`/frame-ancestors,
Referrer-Policy, Permissions-Policy. Force HTTPS (Octane `OCTANE_HTTPS=true` set — good).

## 🟨 SEC-11 — Dependency & supply chain
Large dependency surface (payments, AWS, ES, office parsers). **Fix:** `composer audit`
in CI, Dependabot, pin versions, review `froiden/*` and other less-common packages.

## 🟨 SEC-12 — PII compliance at scale (GDPR/CCPA/India DPDP)
100M candidate records/year. **Need:** data-retention/erasure (right to be forgotten),
consent tracking, audit logs of who viewed/exported candidate data, field-level
encryption for sensitive PII (salary, passport — note `passport` column on
`job_applications`), regional data residency. Build these as first-class features
(see [SCALABILITY.md](SCALABILITY.md) data lifecycle).

---

## Program: how to operationalize security
1. **Now:** rotate secrets (SEC-01), remove debug (SEC-04), tenant tests (SEC-02).
2. **30 days:** XSS sweep + CSP (SEC-03), webhook signatures + idempotency (SEC-05),
   upload scanning (SEC-06), security headers (SEC-10).
3. **90 days:** per-resource policies (SEC-07), 2FA + session hardening (SEC-08),
   audit logging + PII retention/erasure (SEC-12), `composer audit` in CI (SEC-11).
4. **Continuous:** pen-test before major launches, bug-bounty at scale, secret scanning
   in CI (gitleaks), threat-model new marketplace flows (money + talent = high value target).
