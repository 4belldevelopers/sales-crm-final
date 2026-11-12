#!/usr/bin/env bash
# Purpose: Clean up orphaned Octane/Caddy sockets and stale PID state before
# starting Laravel Octane, preventing "Port already in use" and false
# "FrankenPHP server is already running" errors after reboots or deploys.

APP_DIR="${1:-/www/wwwroot/testing.jero.works}"
PORT="${2:-8010}"
ADMIN_PORT="${3:-2029}"
STATE_FILE="$APP_DIR/storage/logs/octane-server-state.json"

# 1. Release any orphaned TCP listeners holding the Octane or Caddy admin port
for P in "$PORT" "$ADMIN_PORT"; do
    if command -v fuser >/dev/null 2>&1; then
        fuser -k -n tcp "$P" >/dev/null 2>&1 || true
    elif command -v lsof >/dev/null 2>&1; then
        PIDS=$(lsof -ti TCP:"$P" -sTCP:LISTEN 2>/dev/null || true)
        if [ -n "$PIDS" ]; then
            kill -9 $PIDS >/dev/null 2>&1 || true
        fi
    fi
done

sleep 0.5

# 2. Remove stale octane-server-state.json if the recorded PID is dead or not frankenphp/octane
if [ -f "$STATE_FILE" ]; then
    OLD_PID=$(grep -oE '"masterProcessId"[[:space:]]*:[[:space:]]*[0-9]+' "$STATE_FILE" 2>/dev/null | grep -oE '[0-9]+' | head -n1 || true)
    STALE=0
    if [ -z "$OLD_PID" ] || [ ! -d "/proc/$OLD_PID" ]; then
        STALE=1
    else
        CMDLINE=$(tr '\0' ' ' < "/proc/$OLD_PID/cmdline" 2>/dev/null || true)
        if [[ "$CMDLINE" != *frankenphp* ]] && [[ "$CMDLINE" != *octane* ]]; then
            STALE=1
        fi
    fi
    if [ "$STALE" -eq 1 ]; then
        rm -f "$STATE_FILE"
    fi
fi

exit 0
