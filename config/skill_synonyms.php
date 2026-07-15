<?php

/**
 * Skill alias/synonym map used by the Similar Candidates skill filter
 * (ServiceCompanyAppTrackerController::similar_existing_data_ajax) to widen
 * recall on match_phrase queries against job_application_skills.skills.
 *
 * match_phrase requires a literal text match, so a candidate tagged "JS"
 * never matches a filter for "JavaScript" even though they're the same
 * skill — this map lets the query expand a typed skill into itself plus
 * its known variants before matching. Keys and values should be
 * lowercase (skills are lowercased before lookup); a skill with no
 * entry here simply matches only itself, unchanged from before.
 *
 * Deliberately a plain config array, not a database table — it's small,
 * changes rarely, and needs no migration or admin UI to maintain. Add
 * pairs in both directions (each side lists the other) so the expansion
 * works regardless of which variant the recruiter types.
 */

return [
    'js' => ['javascript'],
    'javascript' => ['js'],

    'ts' => ['typescript'],
    'typescript' => ['ts'],

    'react' => ['reactjs', 'react.js'],
    'reactjs' => ['react', 'react.js'],
    'react.js' => ['react', 'reactjs'],

    'node' => ['nodejs', 'node.js'],
    'nodejs' => ['node', 'node.js'],
    'node.js' => ['node', 'nodejs'],

    'vue' => ['vuejs', 'vue.js'],
    'vuejs' => ['vue', 'vue.js'],
    'vue.js' => ['vue', 'vuejs'],

    'angular' => ['angularjs', 'angular.js'],
    'angularjs' => ['angular', 'angular.js'],

    'dotnet' => ['.net', 'dot net', 'asp.net'],
    '.net' => ['dotnet', 'dot net', 'asp.net'],
    'asp.net' => ['dotnet', '.net'],

    'c#' => ['csharp', 'c sharp'],
    'csharp' => ['c#', 'c sharp'],

    'c++' => ['cpp'],
    'cpp' => ['c++'],

    'golang' => ['go'],
    'go' => ['golang'],

    'py' => ['python'],
    'python' => ['py'],

    'k8s' => ['kubernetes'],
    'kubernetes' => ['k8s'],

    'postgres' => ['postgresql'],
    'postgresql' => ['postgres'],

    'aws' => ['amazon web services'],
    'amazon web services' => ['aws'],

    'gcp' => ['google cloud platform', 'google cloud'],
    'google cloud platform' => ['gcp', 'google cloud'],

    'ml' => ['machine learning'],
    'machine learning' => ['ml'],

    'ai' => ['artificial intelligence'],
    'artificial intelligence' => ['ai'],

    'ui' => ['user interface'],
    'ux' => ['user experience'],

    'ci/cd' => ['cicd', 'ci cd'],
    'cicd' => ['ci/cd', 'ci cd'],
];
