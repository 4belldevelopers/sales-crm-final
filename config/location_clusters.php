<?php

/**
 * Metro-area location clusters used by the Similar Candidates location
 * filter (ServiceCompanyAppTrackerController::similar_existing_data_ajax).
 *
 * Selecting one city from a cluster widens the location filter to include
 * every other city in the same cluster — e.g. selecting Gurgaon also
 * surfaces Noida/Delhi/Faridabad/Ghaziabad/Greater Noida candidates, since
 * Delhi NCR recruiters routinely consider all of them interchangeably for
 * commute purposes. The exact city selected is still ranked first in
 * results (see the location-priority sort in the controller) — this only
 * widens WHICH candidates are included, not their relative ranking.
 *
 * Deliberately a plain config array, not a database table — small, changes
 * rarely, no migration needed. Add a new cluster (e.g. Mumbai/Thane/Navi
 * Mumbai, or Bangalore's outer areas) by adding another entry below;
 * nothing else needs to change.
 *
 * Matching is by case-insensitive SUBSTRING against city_list.city, not
 * exact equality — city names in this database aren't always the plain
 * single-word form (e.g. seen stored as "Gurgaon/Gurugram", not just
 * "Gurgaon"), so a fragment match is more robust than assuming exact
 * spelling/formatting.
 */

return [
    'delhi_ncr' => [
        'label' => 'Delhi NCR',
        'city_fragments' => [
            'gurgaon',
            'gurugram',
            'noida',
            'greater noida',
            'delhi',
            'new delhi',
            'faridabad',
            'ghaziabad',
        ],
    ],
];
