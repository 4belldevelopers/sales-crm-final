<?php

namespace App\Services\Interview;

use InvalidArgumentException;

/**
 * Class InterviewTenantScope
 *
 * Intent: Canonical, authoritative Elasticsearch tenancy scope builder for client interview queries.
 * Strictly enforces tenant isolation across Internal Clients and Direct Clients.
 * Legacy authorization branches are eliminated from production authorization queries,
 * with pure normalized security envelope support and tenant-guarded fallbacks during migration.
 *
 * Tenancy Rule: Both companyId and clientId are strictly mandatory and must be positive integers.
 */
class InterviewTenantScope
{
    /**
     * Constructs the canonical, authoritative Elasticsearch query filter for a client's interviews.
     * Strictly enforces tenant boundaries with mandatory typed parameters.
     *
     * @param int $companyId Authenticated tenant organization ID (Service Company)
     * @param int $clientId  Target client ID
     * @param string $clientType 'internal' or 'direct'
     * @return array Elasticsearch bool query filter array
     *
     * @throws InvalidArgumentException If tenancy parameters are non-positive or client type is invalid
     */
    public static function forClient(int $companyId, int $clientId, string $clientType = 'internal'): array
    {
        if ($companyId <= 0 || $clientId <= 0) {
            throw new InvalidArgumentException(
                "Tenancy Violation: Both companyId ({$companyId}) and clientId ({$clientId}) must be strictly positive integers."
            );
        }

        $canonicalType = strtolower(trim($clientType));
        $relationshipType = match ($canonicalType) {
            'direct' => 'DIRECT_CLIENT',
            'internal', 'service' => 'INTERNAL_CLIENT',
            default => throw new InvalidArgumentException("Invalid client type: '{$clientType}'. Must be 'internal' or 'direct'."),
        };

        if ($relationshipType === 'DIRECT_CLIENT') {
            // Direct Client Tenancy Filter:
            // 1. Normalized Envelope: tenant is visible_to_tenants, client_id matches from_company, rel matches DIRECT_CLIENT
            // 2. Strict Migration Fallback: ClientComID or to_company_id matches companyId AND from_company_id or client_id matches clientId
            return [
                'bool' => [
                    'should' => [
                        [
                            'bool' => [
                                'must' => [
                                    ['term' => ['security.visible_to_tenants' => $companyId]],
                                    ['term' => ['security.client_id' => $clientId]],
                                    ['term' => ['security.relationship_type' => 'DIRECT_CLIENT']],
                                ],
                            ],
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['ClientComID' => $companyId]],
                                                ['term' => ['thirdparty_company_id' => $companyId]],
                                                ['term' => ['to_company_id' => $companyId]],
                                                ['term' => ['job_company_id' => $companyId]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                    [
                                        'bool' => [
                                            'should' => [
                                                ['term' => ['from_company_id' => $clientId]],
                                                ['term' => ['client_id' => $clientId]],
                                                ['term' => ['company_id' => $clientId]],
                                            ],
                                            'minimum_should_match' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // Internal Client Tenancy Filter:
        // 1. Normalized Envelope: visible_to_tenants contains companyId, client_id matches clientId, rel is INTERNAL_CLIENT
        // 2. Strict Migration Fallback: job_company_id or ClientComID matches companyId AND client_id matches clientId
        return [
            'bool' => [
                'should' => [
                    [
                        'bool' => [
                            'must' => [
                                ['term' => ['security.visible_to_tenants' => $companyId]],
                                ['term' => ['security.client_id' => $clientId]],
                                ['term' => ['security.relationship_type' => 'INTERNAL_CLIENT']],
                            ],
                        ],
                    ],
                    [
                        'bool' => [
                            'must' => [
                                [
                                    'bool' => [
                                        'should' => [
                                            ['term' => ['job_company_id' => $companyId]],
                                            ['term' => ['ClientComID' => $companyId]],
                                        ],
                                        'minimum_should_match' => 1,
                                    ],
                                ],
                                [
                                    'term' => ['client_id' => $clientId],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];
    }
}
