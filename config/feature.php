<?php
// Intent: master feature-flag registry for JERO incremental rollouts.
// All flags default to false — safe to deploy before the feature is live.
// Usage: config('feature.<flag_name>')
// Toggle per environment: set the matching env var in .env or server config.

return [

    /*
    |--------------------------------------------------------------------------
    | Jobs Module — Three Drawers V3
    | (Job Status Drawer, Team Members Drawer, Add Agency Drawer)
    |
    | When true: drawer endpoints serve cached JSON instead of raw Blade HTML,
    | and background Horizon jobs handle write-path side effects.
    |
    | Redis cache keys used across all three drawers (1h TTL):
    |   status_drawer:{company_id}:{job_id}
    |   team_drawer:{company_id}:{job_id}
    |   agency_drawer:{company_id}:{job_id}:{page}
    |--------------------------------------------------------------------------
    */
    'use_three_drawers_v3' => (bool) env('FEATURE_THREE_DRAWERS_V3', false),

];
