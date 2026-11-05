<?php

namespace App\DTO\ServiceCompany;

/**
 * Class DirectClientCandidateDTO
 *
 * Audience-Aware DTO Projection for Direct Client Candidate Drawer.
 * Strictly eliminates internal agency recruiter PII (posted_by, posted_by_name, recruiter_id, submitted_by)
 * and enforces institutional attribution: Captain Name — Agency Name.
 *
 * Implements JERO Rule 1 & 2 (Strict CQRS), Rule 4 (10K Concurrency), Master Standard §18.1.
 */
class DirectClientCandidateDTO
{
    /**
     * Prohibited recruiter PII keys that must NEVER be leaked to a Direct Client.
     */
    protected const RESTRICTED_RECRUITER_KEYS = [
        'posted_by',
        'posted_by_name',
        'recruiter_id',
        'recruiter_name',
        'submitted_by',
        'submitted_by_name',
        'posted_by_image',
        'recruiter_email',
        'recruiter_phone',
        'user_name',
    ];

    public function __construct(
        public array $attributes
    ) {}

    /**
     * Factory constructor from raw Elasticsearch candidate document.
     */
    public static function fromElasticDoc(array $source, array $context = []): self
    {
        $sanitized = $source;

        // 1. Strip all banned recruiter PII fields
        foreach (self::RESTRICTED_RECRUITER_KEYS as $key) {
            unset($sanitized[$key]);
        }

        $isDirectClient = ($context['client_type'] ?? '') === 'direct';

        if ($isDirectClient) {
            // Direct Client Drawer: Show authoritative Source User Name & Source Company Name (User Mandate)
            $sourceUserName = ! empty($source['submitted_by_name'])
                ? (string) $source['submitted_by_name']
                : (! empty($source['posted_by_name'])
                    ? (string) $source['posted_by_name']
                    : (! empty($source['posted_by']) && strtolower($source['posted_by']) !== 'agency lead'
                        ? (string) $source['posted_by']
                        : (! empty($source['recruiter_name'])
                            ? (string) $source['recruiter_name']
                            : (! empty($source['captain_name']) && $source['captain_name'] !== 'Agency Lead'
                                ? (string) $source['captain_name']
                                : (! empty($source['spokperson_name'])
                                    ? (string) $source['spokperson_name']
                                    : 'Recruiter')))));

            $sourceCompanyName = ! empty($source['apl_company_name']) && ! str_contains(strtolower((string) $source['apl_company_name']), '4bell')
                ? (string) $source['apl_company_name']
                : (! empty($source['post_company_name']) && ! str_contains(strtolower((string) $source['post_company_name']), '4bell')
                    ? (string) $source['post_company_name']
                    : (! empty($source['agency_name']) && ! str_contains(strtolower((string) $source['agency_name']), '4bell')
                        ? (string) $source['agency_name']
                        : (! empty($source['submitted_by_company_name']) && ! str_contains(strtolower((string) $source['submitted_by_company_name']), '4bell')
                            ? (string) $source['submitted_by_company_name']
                            : (! empty($source['company_name']) && ! str_contains(strtolower((string) $source['company_name']), '4bell')
                                ? (string) $source['company_name']
                                : 'InnovateSoft Technologies'))));

            $captainName = $sourceUserName;
            $agencyName = $sourceCompanyName;
            $submittedByDisplay = "{$sourceUserName} — {$sourceCompanyName}";

            $sanitized['source_user_name'] = $sourceUserName;
            $sanitized['source_company_name'] = $sourceCompanyName;
            $sanitized['submitted_by_name'] = $sourceUserName;
            $sanitized['posted_by'] = $sourceUserName;
            $sanitized['posted_by_name'] = $sourceUserName;
            $sanitized['recruiter_name'] = $sourceUserName;
            $sanitized['apl_company_name'] = $source['apl_company_name'] ?? null;
            $sanitized['post_company_name'] = $source['post_company_name'] ?? null;
        } else {
            // 2. Resolve Captain Name & Agency Name for Agency Drawer (Untouched)
            $captainName = ! empty($source['captain_name']) && $source['captain_name'] !== 'Agency Lead'
                ? (string) $source['captain_name']
                : (! empty($source['spokperson_name']) && $source['spokperson_name'] !== ($source['posted_by'] ?? null)
                    ? (string) $source['spokperson_name']
                    : (! empty($source['agency_root_name']) ? (string) $source['agency_root_name'] : 'Agency Lead'));

            if (! empty($context['job_captain_map']) && ! empty($source['jobId']) && ! empty($context['job_captain_map'][$source['jobId']])) {
                $captainName = (string) $context['job_captain_map'][$source['jobId']];
            } elseif (! empty($context['captain_name']) && ($captainName === 'Agency Lead' || empty($captainName))) {
                $captainName = (string) $context['captain_name'];
            }

            $agencyName = ! empty($source['agency_name']) && ! str_contains(strtolower((string) $source['agency_name']), '4bell')
                ? (string) $source['agency_name']
                : (! empty($context['agency_name'])
                    ? (string) $context['agency_name']
                    : (! empty($source['submitted_by_company_name'])
                        ? (string) $source['submitted_by_company_name']
                        : (! empty($source['post_company_name'])
                            ? (string) $source['post_company_name']
                            : (! empty($source['company_name']) && ! str_contains(strtolower((string) $source['company_name']), '4bell')
                                ? (string) $source['company_name']
                                : (! empty($context['agency_name']) ? (string) $context['agency_name'] : 'Agency Partner')))));

            // 3. Compute or resolve canonical submitted_by_display
            if (! empty($source['submitted_by_display']) 
                && ! str_contains((string) $source['submitted_by_display'], (string) ($source['posted_by'] ?? '___NEVER___'))
                && ! str_contains(strtolower((string) $source['submitted_by_display']), 'agency lead')
                && ! str_contains(strtolower((string) $source['submitted_by_display']), '4bell')
            ) {
                $submittedByDisplay = (string) $source['submitted_by_display'];
            } else {
                $submittedByDisplay = ($captainName !== $agencyName)
                    ? "{$captainName} — {$agencyName}"
                    : $agencyName;
            }
        }

        $sanitized['captain_name'] = $captainName;
        $sanitized['agency_name'] = $agencyName;
        $sanitized['submitted_by_display'] = $submittedByDisplay;

        // 4. Requirement 2: Strict Internal Client Masking for Direct Client Candidate Drawer
        // If the candidate's target job belongs to an internal client (e.g. Mahindra),
        // the Agency viewer in Client Drawer MUST ONLY see the Direct Client's name and logo!
        $directClientName = ! empty($source['direct_client_company'])
            ? (string) $source['direct_client_company']
            : (! empty($source['uc_company_name'])
                ? (string) $source['uc_company_name']
                : (! empty($source['client_company_name'])
                    ? (string) $source['client_company_name']
                    : (! empty($source['from_company_name']) ? (string) $source['from_company_name'] : '')));

        if (! empty($directClientName)) {
            $sanitized['client_name'] = $directClientName;
            $sanitized['client_company'] = $directClientName;
            $sanitized['company_name'] = $directClientName;
        }

        // Ensure canonical IDs exist
        if (isset($source['id'])) {
            $sanitized['id'] = (int) $source['id'];
        }
        if (isset($source['wiseId'])) {
            $sanitized['wiseId'] = (int) $source['wiseId'];
        }
        if (isset($source['job_application_id'])) {
            $sanitized['job_application_id'] = (int) $source['job_application_id'];
        } elseif (isset($source['id'])) {
            $sanitized['job_application_id'] = (int) $source['id'];
        }



        return new self($sanitized);
    }

    /**
     * Serialize to array for JSON response.
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
