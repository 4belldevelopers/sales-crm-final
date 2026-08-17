# /docs/V2_Zcode — ZCode (GLM) Audit Index

> **Purpose of this folder:** Central home for audits and implementation plans produced by ZCode
> (GLM). **Convention: every audit lives in its own separate subfolder** — named
> `<Topic> Audit by GLM` — so each audit's documents stay self-contained and discoverable.
> New audit? Create a new subfolder here, never mix files into an existing audit's folder.
> This README is the AI-discoverable index; also indexed from [../README.md](../README.md).

## Read order per audit: main audit doc → companion plan → supporting ledgers

---

## [Code Review and UI Audit by GLM/](Code%20Review%20and%20UI%20Audit%20by%20GLM/) — full codebase review (2026-08-17)

Codebase-wide review: multi-tenancy security, backend architecture, UI/UX layer + master remediation plan.

| File | What it is |
|---|---|
| [GLM_FIX_Audit.md](Code%20Review%20and%20UI%20Audit%20by%20GLM/GLM_FIX_Audit.md) | **Start here.** Verified findings: 6,025 unscoped raw `DB::` queries (of 6,938), IDOR endpoints with file:line evidence, 6 disabled model scopes, god controllers, debug routes, 18 `env()` violations, UI findings (inline CSS, Poppins/Figtree conflict, card-system adoption 8/1,642). Risk matrix + "what NOT to do". |
| [GOD_LEVEL_IMPLEMENTATION_PLAN.md](Code%20Review%20and%20UI%20Audit%20by%20GLM/GOD_LEVEL_IMPLEMENTATION_PLAN.md) | Master execution plan, 3 tracks: **A** = 20-step tenancy audit (hotfixes → TenantScope/`tenant_table()` + CI guard → migrate 6,025 queries → pen-test) · **B** = backend upgrade (dead code, `env()`, god controllers, ES sync, queues) · **C** = UI standardization. Week-by-week milestones, acceptance criteria per step. |

## [SAAS Architecture Audit by GLM/](SAAS%20Architecture%20Audit%20by%20GLM/) — SaaS architecture & authorization audit (2026-08-17)

Deep audit of tenancy, authorization, and access control. Analysis-only — no code modified. Confidence-labeled (CONFIRMED / INFERRED / UNKNOWN).

| File | What it is |
|---|---|
| [SAAS_ARCHITECTURE_AUDIT.md](SAAS%20Architecture%20Audit%20by%20GLM/SAAS_ARCHITECTURE_AUDIT.md) | **Start here.** Executive summary + tenancy/auth/account/role/permission model as implemented (authorization maturity: LOW; SaaS architecture maturity: MEDIUM). |
| [AUTHORIZATION_GAPS.md](SAAS%20Architecture%20Audit%20by%20GLM/AUTHORIZATION_GAPS.md) | Prioritized security gaps (CRITICAL/HIGH/MEDIUM/LOW) with locations, attack scenarios, and expected behavior. 5 critical findings. |
| [PERMISSION_MATRIX.md](SAAS%20Architecture%20Audit%20by%20GLM/PERMISSION_MATRIX.md) | Complete Actor → Resource → Action → Permission map (Spatie abilities; "—" = no check exists). |
| [ACTION_INVENTORY.md](SAAS%20Architecture%20Audit%20by%20GLM/ACTION_INVENTORY.md) | Catalog of meaningful actions and their authorization requirements per route file (⚠ = flagged risk). |
| [TENANT_ISOLATION_AUDIT.md](SAAS%20Architecture%20Audit%20by%20GLM/TENANT_ISOLATION_AUDIT.md) | Tenant isolation deep-dive: `company_id` + marketplace columns (`from_company_id`/`to_company_id`/`thirdparty_company_id`). |
| [SAAS_AUTHORIZATION_IMPLEMENTATION_GUIDE.md](SAAS%20Architecture%20Audit%20by%20GLM/SAAS_AUTHORIZATION_IMPLEMENTATION_GUIDE.md) | **For AI agents:** how authorization/tenancy ACTUALLY works today — read before touching any access-control code. |

---

## Cross-audit relationship

Both audits examine tenant security from different angles and agree on root causes
(no centralized TenantScope, raw `DB::` bypass, inconsistent scope bypass logic).
The **Code Review** audit's GOD_LEVEL_IMPLEMENTATION_PLAN Track A is the execution plan;
the **SAAS Architecture** audit's AUTHORIZATION_GAPS remediation priorities feed into it
(authorization gaps beyond tenancy: permission gates, impersonation, unsigned file URLs).

## Related (outside this folder)

- [../README.md](../README.md) — master docs index (this folder is listed under "Per-feature audits & implementation plans")
- [../PERSONA_SCOPE.md](../PERSONA_SCOPE.md) — scope rule: ignore Product Co and Staffing Co for audits/plans (the Code Review audit kept them in scope for tenancy security — flagged, unresolved)
- [../../.ai/PROJECT_MEMORY.md](../../.ai/PROJECT_MEMORY.md) — living project state & decision log
