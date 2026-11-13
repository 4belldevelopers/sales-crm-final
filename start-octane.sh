#!/bin/bash
export PHPRC=/www/server/php/83/etc/php-cli.ini
export PATH="/www/server/php/83/bin:$PATH"
export HOME="${HOME:-/root}"

# Detect port from argument, .env OCTANE_PORT, or path
PORT=$1
if [ -z "$PORT" ]; then
    PORT=$(grep "^OCTANE_PORT=" .env 2>/dev/null | cut -d '=' -f2 | tr -d ' "\r\n')
fi
if [ -z "$PORT" ]; then
    if [[ "$PWD" =~ (hire|testing)\.jero\.works ]]; then
        PORT=8010
    else
        PORT=8000
    fi
fi

WORKERS=16
if [[ "$PWD" =~ testing\.jero\.works ]]; then
    WORKERS=8
fi

# Pre-flight: reclaim orphaned sockets and stale PID state before starting
if [ -f scripts/octane-preflight.sh ]; then
    bash scripts/octane-preflight.sh "$PWD" "$PORT" 2029 || true
elif command -v fuser >/dev/null 2>&1; then
    fuser -k -n tcp "$PORT" 2>/dev/null || true
    fuser -k -n tcp 2029 2>/dev/null || true
fi

php artisan octane:start --server=frankenphp --host=127.0.0.1 --port=$PORT --workers=$WORKERS --max-requests=500
