# V2 Login — Documentation Index

**What this folder is:** the design record for the V2_login slice — the redesigned login + session-lifecycle subsystem of JERO, sized for 10,000 concurrent recruiters on 32 GB servers with minimal server load and near real-time client UX.

**Why it exists:** JERO's login path is currently a 825-LOC god controller (`app/Http/Controllers/Auth/LoginController.php`) with 23+ blocking-scenario footguns across 15 different code paths. The V2_login slice is the bounded remediation: audit, plan, compare, and execute, with no business-logic changes (per AI_RULE #8).

## Reading order

1. **[`AUDIT_REPORT.md`](AUDIT_REPORT.md)** — the god-level audit of login + middleware + captcha + session config. 41 findings ranked P0–P3. *Start here.*
2. **[`BLOCKING_SCENARIOS_AUDIT.md`](BLOCKING_SCENARIOS_AUDIT.md)** — the *companion* audit for every blocking path (user-block-by-company, company-block-by-superadmin, coin-depletion, self-deactivate, payment-failure, super-admin impersonation, etc.). 4 architectural refinements applied during a 2026-08-06 review pass.
3. **[`IMPLEMENTATION_PLAN.md`](IMPLEMENTATION_PLAN.md)** — 7 phases + Appendix A (audit cross-references + pending product sign-off). The binding fix sequence.
4. **[`BEFORE_AFTER_COMPARISON.md`](BEFORE_AFTER_COMPARISON.md)** — side-by-side view of every layer before/after the V2_login slice. Includes the §15 sign-off criteria and §16 blocking-scenarios delta.
5. **[`../adr/0007-blocking-scenarios-architecture.md`](../adr/0007-blocking-scenarios-architecture.md) — the decision record for the 4 architectural refinements.

## Cross-reference

| If you are implementing… | Read first… |
|--------------------------|-------------|
| Any Phase-1 work (captcha cache, ES bulk, `Auth::validate` removal) | `AUDIT_REPORT.md` §1–§7 |
| Any Phase-3 sub-phase (3.1–3.8) | `BLOCKING_SCENARIOS_AUDIT.md` §5.1–§5.6 + `IMPLEMENTATION_PLAN.md` Appendix A |
| Phase 3.5 controller rewires | `BLOCKING_SCENARIOS_AUDIT.md` §1 footgun table |
| Phase 3.6 superadmin rewire | `BLOCKING_SCENARIOS_AUDIT.md` §2 defect table |
| Phase 3.7 `refresh:true` lint guard | `BLOCKING_SCENARIOS_AUDIT.md` §5.5 + §8 |
| Phase 6.4 `block_log` migration | `BLOCKING_SCENARIOS_AUDIT.md` §5.3 |
| Phase 6.5 scheduler wires | `BLOCKING_SCENARIOS_AUDIT.md` §3.1 + §3.3 |
| Sign-off / acceptance | `BEFORE_AFTER_COMPARISON.md` §15 |

## Document status (2026-08-06)

| Doc | State | Last touched |
|-----|-------|--------------|
| `AUDIT_REPORT.md` | Frozen 2026-08-06 | 2026-08-06 (refinement cross-references added) |
| `BLOCKING_SCENARIOS_AUDIT.md` | Frozen 2026-08-06 | 2026-08-06 (recount + footgun #1 footnote + §4.3 future-state column) |
| `IMPLEMENTATION_PLAN.md` | Frozen 2026-08-06 | 2026-08-06 (Phase 3.3–3.8 + 6.4/6.5 + Appendix A) |
| `BEFORE_AFTER_COMPARISON.md` | Frozen 2026-08-06 | 2026-08-06 (§15 sign-off + §16 blocking-scenarios delta) |
| `../adr/0007-blocking-scenarios-architecture.md` | Frozen 2026-08-06 | 2026-08-06 |

Until `app/` code lands, treat these as *the* source of truth for V2_login. Any subsequent finding should be added here before being implemented.

## Companion memory

- The auto-memory entry at `~/.claude/projects/C--Users-VIBHANSHU-JAISWAL-000-Documents-GitHub-JWMaincode28June2026/memory/` captures user-level insights (UI standards, tenant isolation, registration patterns).
- `.ai/PROJECT_MEMORY.md` carries project-level facts that have changed since the V2_login slice landed.
- The plan file at `~/.claude/plans/memoized-stargazing-sutherland.md` documents the workflow history (now redundant with Appendix A; retained for audit trail).
