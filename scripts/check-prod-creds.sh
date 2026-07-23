#!/usr/bin/env bash
# check-prod-creds.sh — pre-push guard that refuses any push whose diff
# contains a production credential, host, or known legacy prod password.
#
# Usage (called from .git/hooks/pre-push; can also be run by hand):
#   bash scripts/check-prod-creds.sh
#
# Exit code 0 = clean (push allowed), 1 = violation (push blocked).
#
# Per docs/SECURITY.md, the production DB host (72.62.229.143) and the legacy
# prod password (Hanuman@123) must never appear in this repository. They live
# only on the production VPS's own .env.

set -uo pipefail
cd "$(dirname "$0")/.." || exit 2

# Patterns that, if present in any tracked file, indicate a prod-credential leak.
patterns=(
  '72\.62\.229\.143'                  # production DB host
  '72\.62\.229\.143:3307'             # production DB host:port
  'Hanuman@123'                       # legacy production DB password
  'hire\.jero\.works/.env'            # path-style leak of prod .env
  'PROD_DB_PASSWORD'                  # prod-only env var name
)

# Files we never check (own policy doc + this guard script itself).
EXCLUDES=(
  ':(exclude)docs/SECURITY.md'
  ':(exclude)scripts/check-prod-creds.sh'
)

fail=0

# Search every tracked file under the repo, excluding the doc + script.
for pat in "${patterns[@]}"; do
  hits="$(git grep -nE "$pat" -- . "${EXCLUDES[@]}" 2>/dev/null || true)"
  if [ -n "$hits" ]; then
    echo "❌ check-prod-creds: refusing push — pattern matched: '$pat'"
    echo ""
    echo "$hits" | sed 's/^/    /'
    echo ""
    fail=1
  fi
done

# Also catch anything the user is about to push via diff against origin/main.
# This catches the case where the secret was committed then amended.
diff_hits="$(git diff --diff-filter=ACMR origin/main...HEAD -- . 2>/dev/null \
  | grep -nE '72\.62\.229\.143|Hanuman@123|hire\.jero\.works/.env|PROD_DB_PASSWORD' \
  || true)"

if [ -n "$diff_hits" ]; then
  echo "❌ check-prod-creds: refusing push — production credential pattern in commits ahead of origin/main:"
  echo ""
  echo "$diff_hits" | sed 's/^/    /'
  echo ""
  echo "Rewrite history before pushing, or rotate the leaked credential and document the incident."
  fail=1
fi

if [ "$fail" -ne 0 ]; then
  cat <<EOF

Production credentials must NEVER appear in this repo. Per docs/SECURITY.md:
  - The production DB host is 72.62.229.143 and lives only on the prod VPS .env.
  - Production passwords live only on the prod VPS, not on developer machines.
  - Rotate any leaked credential IMMEDIATELY before continuing.

If this is a false positive (e.g. you intentionally wrote 72.62.229.143 in
docs/SECURITY.md for documentation purposes), edit this script's EXCLUDES list.
EOF
  exit 1
fi

echo "✓ check-prod-creds: clean — no production credentials in the working tree or diff."
exit 0