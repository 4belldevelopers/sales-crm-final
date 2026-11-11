#!/bin/bash
export PHPRC=/www/server/php/83/etc/php-cli.ini
export PATH="/www/server/php/83/bin:$PATH"

# Detect port from argument, .env OCTANE_PORT, or path
PORT=$1
if [ -z "$PORT" ]; then
    PORT=$(grep "^OCTANE_PORT=" .env 2>/dev/null | cut -d '=' -f2 | tr -d ' "\r\n')
fi
if [ -z "$PORT" ]; then
    if [[ "$PWD" =~ hire\.jero\.works ]]; then
        PORT=8010
    else
        PORT=8000
    fi
fi

# Pre-flight: if an orphaned process is deadlocking the port, release it safely
if command -v fuser >/dev/null 2>&1; then
    fuser -k -n tcp "$PORT" 2>/dev/null || true
fi

php artisan octane:start --server=frankenphp --host=127.0.0.1 --port=$PORT --workers=16 --max-requests=500
