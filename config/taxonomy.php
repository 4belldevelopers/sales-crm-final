<?php

/**
 * JERO Talent Taxonomy Configuration
 * Single Source of Truth for Taxonomy V2 Settings & Feature Flags.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flag: New Canonical Talent Taxonomy V2
    |--------------------------------------------------------------------------
    | When enabled, client-first autocomplete hydrates canonical designations,
    | aliases, and modular 12-block JD templates with zero database read strain.
    */
    'feature_new_taxonomy' => env('FEATURE_NEW_TAXONOMY', true),

    /*
    |--------------------------------------------------------------------------
    | Current Taxonomy Engine & Manifest Version
    |--------------------------------------------------------------------------
    */
    'current_version' => '2.0.0',
    'minimum_supported_version' => '2.0.0',
    'schema_version' => 2,

    /*
    |--------------------------------------------------------------------------
    | Static Bundle Public Storage Paths
    |--------------------------------------------------------------------------
    */
    'bundle_dir' => public_path('designations'),
    'manifest_file' => 'taxonomy-manifest.json',
    'core_bundle_file' => 'core-taxonomy.json',

    /*
    |--------------------------------------------------------------------------
    | Cache Lifetimes & Invalidation
    |--------------------------------------------------------------------------
    */
    'cache_ttl_seconds' => 86400, // 24 hours
];
