<?php

return [
    'hosts' => [
        env('ELASTICSEARCH_HOST') . ':' . env('ELASTICSEARCH_PORT'),
    ],

    'retries' => 1,  // Optional: retry mechanism in case of failures
];
