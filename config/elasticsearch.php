<?php

return [
    /*
     | Elasticsearch connection settings.
     | env() is read HERE (inside config/) on purpose: these values are resolved
     | when config is loaded/cached, so they survive `config:cache` + Octane — where
     | env() returns null everywhere else. App code must read these via config(),
     | never env(). See CLAUDE.md Rule #5 / BUG-02 (and SEC-01 re: the ES password).
     */
    'host'   => env('ELASTICSEARCH_HOST', 'localhost'),
    'port'   => env('ELASTICSEARCH_PORT', 9200),
    'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
    'user'   => env('ELASTICSEARCH_USER'),
    'pass'   => env('ELASTICSEARCH_PASS'),

    'hosts' => [
        env('ELASTICSEARCH_HOST') . ':' . env('ELASTICSEARCH_PORT'),
    ],

    'retries' => 1,  // Optional: retry mechanism in case of failures
];
