#!/usr/bin/env bash
# ES reindex script for local_companies_elastic after _source.excludes mapping update.
# INTENT: Reindex local_companies_elastic to strip API secrets from _source.
set -e

ES_HOST="${ES_HOST:-http://localhost:9200}"

echo "Starting reindex for local_companies_elastic..."

# Run artisan command to apply fresh mapping and bulk index
php artisan elasticsearch:index-companies --fresh

echo "Reindex completed for local_companies_elastic."
