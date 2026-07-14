# Enforcement — Making the Rules Mechanical

> Docs describe good behavior; **enforcement makes it unskippable.** This wires the key
> rules from [AI_RULES.md](AI_RULES.md) / [BUG_REPORT.md](BUG_REPORT.md) into automated
> checks so neither an AI nor a human can quietly break them. Everything here is **dev/CI
> only** — nothing ships to production, nothing touches the live DB.

Design principle: **only block NEW violations.** Checks run on *changed* files, so the
huge backlog of legacy `dd()`/`env()` (see BUG_REPORT) won't block you today — but no
*new* ones get in.

---

## 1. AI guards (works now, zero install)
`scripts/ai-guards.sh` — fast bash grep checks. Currently enforces:
- **No active `dd()`/`dump()`/`var_dump()`/`die()`/`exit()`** in changed `app/` files (BUG-01).
- **No `env()` outside `config/`** (returns null under `config:cache`/Octane — BUG-02).

```bash
bash scripts/ai-guards.sh                 # staged files (used by the hook)
bash scripts/ai-guards.sh --changed main  # files changed vs a base ref (used by CI)
bash scripts/ai-guards.sh --all           # scan all app/ (will show legacy debt)
```
Tested: flags the known-bad files, passes clean files. Extend it as you add rules.

## 2. Pre-commit hook (opt-in, 1 command)
`.githooks/pre-commit` runs the guards + **Pint** (style) on staged files before each commit.
Enable once per clone:
```bash
git config core.hooksPath .githooks
```
Bypass in a real emergency: `git commit --no-verify` (CI still catches it).

## 3. CI (GitHub Actions)
`.github/workflows/ci.yml` runs on PRs + pushes to `main`/`master`/`qwen-dev`:
- **AI guards** on changed files · **Pint** style · **PHPStan** (non-blocking until baseline).
- Needs **no DB/ES/Redis** → reliable and cheap. Repo:
  `github.com/4belltechnology-tech/jero-ats-qwen`.
- The **test job is commented out** — enable after a schema dump exists (§5).

## 4. PHPStan (static analysis — needs one install)
What it is: a tool that reads code without running it and finds bugs (unknown methods,
wrong args, null issues) — directly enforces the "no invented APIs/columns" guardrail.
**Larastan** adds Laravel awareness. Config: `phpstan.neon.dist` (level 2 to start).

Install (dev only) and run:
```bash
composer require --dev phpstan/phpstan larastan/larastan
cp phpstan.neon.dist phpstan.neon          # or rename
vendor/bin/phpstan analyse                  # see the issues
vendor/bin/phpstan analyse --generate-baseline   # freeze existing issues → only NEW ones fail
```
Then uncomment the `phpstan-baseline.neon` include in the config. Raise `level` over time
(0→9) as the code gets cleaner.

## 5. Test schema (unblocks real tests — do on a LOCAL DB only)
Tests can't build the schema because it lives in the live DB ([ADR 0005](adr/0005-schema-in-live-db.md)).
Create a committed snapshot **from a local/dev copy, NEVER production**:
```bash
# point .env at a LOCAL dev DB first, then:
php artisan schema:dump                      # writes database/schema/mysql-schema.sql
```
⚠️ **Do not run schema:dump against the prod `dbmaster` host (76.13.243.68).** Once the
snapshot is committed, enable the CI test job and follow [TESTING.md](TESTING.md).

---

## Setup checklist (what to run yourself)
- [ ] `git config core.hooksPath .githooks`  ← enables the pre-commit hook
- [ ] `composer require --dev phpstan/phpstan larastan/larastan` then `cp phpstan.neon.dist phpstan.neon`
- [ ] `vendor/bin/phpstan analyse --generate-baseline` (accept current debt)
- [ ] (CI) push `.github/workflows/ci.yml`; confirm Actions is enabled on the repo
- [ ] (later, local DB) `php artisan schema:dump` → commit → enable CI test job

## What's enforced vs. still advisory
| Rule | Enforced by | Status |
|---|---|---|
| No new `dd()`/debug | ai-guards + hook + CI | ✅ automated |
| No `env()` outside config | ai-guards + hook + CI | ✅ automated |
| Code style | Pint (hook + CI) | ✅ automated |
| No invented methods/columns | PHPStan | ⚙️ after install + baseline |
| Tests per change | CI test job | ⚙️ after schema dump |
| Tenancy (`company_id`), ES sync, ≤14px UI, docs-on-new-code | review + DEFINITION_OF_DONE | 📋 human/AI review (hard to lint reliably) |

> Add new automated guards to `scripts/ai-guards.sh` as patterns become clear (e.g. a
> warn-only check for `{!! !!}` on user data). Keep guards on *changed* files so adoption
> stays painless.
