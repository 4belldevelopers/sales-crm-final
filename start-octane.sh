#!/bin/bash
export PHPRC=/www/server/php/83/etc/php-cli.ini
php artisan octane:start --server=frankenphp --host=127.0.0.1 --port=8000 --workers=8 --max-requests=1000
