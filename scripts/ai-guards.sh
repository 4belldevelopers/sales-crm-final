#!/usr/bin/env bash
# ai-guards.sh — fast, dependency-free guards that block NEW rule violations.
# Enforces a subset of docs/AI_RULES.md & docs/V2_BugsReport and Implementation/BUG_REPORT.md so they can't be skipped.
# Designed to check only CHANGED files (not legacy), so it won't drown in existing debt.
#
# Usage:
#   bash scripts/ai-guards.sh                 # check staged files (pre-commit)
#   bash scripts/ai-guards.sh --changed main  # check files changed vs <ref> (CI)
#   bash scripts/ai-guards.sh --all           # scan whole app/ (reporting; may show legacy)
#   bash scripts/ai-guards.sh f1.php f2.php   # check specific files
#
# Exit code 0 = clean, 1 = violations found.

set -uo pipefail
cd "$(dirname "$0")/.." || exit 2

mode="${1:-staged}"
files=()

collect_php_app_files() { # filter a newline list to existing app/**/*.php
  while IFS= read -r f; do
    [ -n "$f" ] && [[ "$f" == app/*.php || "$f" == app/*/*.php || "$f" == app/*/*/*.php || "$f" == app/*/*/*/*.php ]] && [ -f "$f" ] && files+=("$f")
  done
}

case "$mode" in
  --all)
    while IFS= read -r f; do files+=("$f"); done < <(find app -name '*.php' -type f) ;;
  --changed)
    base="${2:-origin/main}"
    git diff --name-only --diff-filter=ACMR "$base"...HEAD | collect_php_app_files ;;
  -*)
    echo "Unknown option: $mode"; exit 2 ;;
  staged)
    git diff --cached --name-only --diff-filter=ACMR | collect_php_app_files ;;
  *)
    for f in "$@"; do [[ "$f" == app/*.php ]] && [ -f "$f" ] && files+=("$f"); done ;;
esac

if [ "${#files[@]}" -eq 0 ]; then
  echo "ai-guards: no changed app/*.php files to check."
  exit 0
fi

fail=0
note() { echo "  ✗ $1"; fail=1; }

for f in "${files[@]}"; do
  # GUARD 1: active debug calls (allow commented // dd(...) and inline /* */ best-effort).
  # Matches a debug call at statement start, not preceded by // or *.
  if grep -nE '^[[:space:]]*(dd|dump|var_dump|print_r|die|exit|ray)[[:space:]]*\(' "$f" \
       | grep -vE '^\s*[0-9]+:\s*(//|\*|#)' >/dev/null; then
    echo "[$f] active debug call(s):"
    grep -nE '^[[:space:]]*(dd|dump|var_dump|print_r|die|exit|ray)[[:space:]]*\(' "$f" \
      | grep -vE ':\s*(//|\*|#)' | sed 's/^/    /'
    note "remove dd()/dump()/die()/exit() — use Log::* instead (AI_RULES, BUG-01)"
  fi

  # GUARD 2: env() used outside config/ (returns null under config:cache + Octane).
  if [[ "$f" != config/* ]] && grep -nE '(^|[^_[:alnum:]])env[[:space:]]*\(' "$f" \
       | grep -vE ':\s*(//|\*|#)' >/dev/null; then
    echo "[$f] env() used outside config/:"
    grep -nE '(^|[^_[:alnum:]])env[[:space:]]*\(' "$f" | sed 's/^/    /'
    note "move to config/*.php and read via config() (AI_RULES #5, BUG-02)"
  fi
  # GUARD 3: new 'refresh' => true in ES calls forces a shard sync in the HTTP response path,
  # adding ~50-200 ms per call and blocking Octane worker threads. Remove it.
  if grep -nE "'refresh'\s*=>\s*true" "$f" | grep -vE ':\s*(//|\*|#)' >/dev/null; then
    echo "[$f] 'refresh' => true in ES call:"
    grep -nE "'refresh'\s*=>\s*true" "$f" | grep -vE ':\s*(//|\*|#)' | sed 's/^/    /'
    note "remove 'refresh' => true — ES eventual consistency is fine; sync flush blocks workers (V2_login Phase 3.7)"
  fi

  # GUARD 4 (V2_Perf-10K §12.1): also block 'refresh' => 'wait_for' in the HTTP path
  # unless the file is in the explicit allowlist (BlockCompanyAction, BulkReindexCommand).
  # 'wait_for' blocks the worker until the next refresh — same damage as refresh:true.
  case "$f" in
    app/Actions/Admin/BlockCompanyAction.php|app/Console/Commands/BulkReindexCommand.php)
      : ;; # allowlisted exceptions
    *)
      if grep -nE "'refresh'\s*=>\s*['\"]wait_for['\"]" "$f" | grep -vE ':\s*(//|\*|#)' >/dev/null; then
        echo "[$f] 'refresh' => 'wait_for' in HTTP-path ES call (not in allowlist):"
        grep -nE "'refresh'\s*=>\s*['\"]wait_for['\"]" "$f" | grep -vE ':\s*(//|\*|#)' | sed 's/^/    /'
        note "remove 'refresh' => 'wait_for' — blocks worker until next refresh. Allowlist: BlockCompanyAction, BulkReindexCommand only (V2_Perf-10K §12.1)"
      fi
      ;;
  esac
done

echo ""
if [ "$fail" -ne 0 ]; then
  echo "ai-guards: FAILED — fix the issues above (or justify + override the hook)."
  exit 1
fi
echo "ai-guards: OK (${#files[@]} file(s) checked)."
exit 0
