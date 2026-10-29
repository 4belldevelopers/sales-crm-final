---
name: smart-commit
description: Automated pre-commit verification, AI guard enforcement, and safe staging deployment.
---

# 🚀 Smart Commit & Verification Protocol

This skill replaces ad-hoc `commit and push` commands with an automated, zero-regression verification and staging gate.

## Pre-Commit Verification Pipeline

Before staging any file, execute the following verification steps:

1. **PHP & Blade Syntax Check**:
   - Run `php -l <file>` on every modified `.php` and `.blade.php` file.
   - Any syntax error halts the process immediately.

2. **AI Guard Scan**:
   - Check modified files for forbidden debug statements (`dd()`, `dump()`, `ray()`, `var_dump()`).
   - Verify that no `env()` calls are used outside `config/` (use `config(...)` instead).
   - Check for unescaped `{!! !!}` output on user-supplied variables.

3. **Multi-Tenant Isolation Verification**:
   - Check all modified SQL queries or Eloquent queries to ensure `company_id` filter is present.

4. **Git Staging Audit**:
   - Run `git status --porcelain`.
   - Ensure temporary logs, test files, and unintended edits are excluded.

5. **Staging Commit & Push**:
   - Formulate a conventional commit message referencing the module and ADR/Rule:
     `feat(service-company): <action description> per Rule 20 Card Standard`
   - Push strictly to `origin/staging`. **NEVER push to main**.
