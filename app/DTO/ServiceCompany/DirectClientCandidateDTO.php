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
    public static function fromElasticDoc(array $source): self
    {
        $sanitized = $source;

        // 1. Strip all banned recruiter PII fields
        foreach (self::RESTRICTED_RECRUITER_KEYS as $key) {
            unset($sanitized[$key]);
        }

        // 2. Resolve Captain Name & Agency Name
        $captainName = ! empty($source['captain_name'])
            ? (string) $source['captain_name']
            : (! empty($source['spokperson_name']) && $source['spokperson_name'] !== ($source['posted_by'] ?? null)
                ? (string) $source['spokperson_name']
                : (! empty($source['agency_root_name']) ? (string) $source['agency_root_name'] : 'Agency Lead'));

        $agencyName = ! empty($source['agency_name'])
            ? (string) $source['agency_name']
            : (! empty($source['submitted_by_company_name'])
                ? (string) $source['submitted_by_company_name']
                : (! empty($source['post_company_name'])
                    ? (string) $source['post_company_name']
                    : (! empty($source['company_name']) ? (string) $source['company_name'] : 'Agency Partner')));

        // 3. Compute or resolve canonical submitted_by_display
        if (! empty($source['submitted_by_display']) && ! str_contains((string) $source['submitted_by_display'], (string) ($source['posted_by'] ?? '___NEVER___'))) {
            $submittedByDisplay = (string) $source['submitted_by_display'];
        } else {
            $submittedByDisplay = ($captainName !== $agencyName)
                ? "{$captainName} — {$agencyName}"
                : $agencyName;
        }

        $sanitized['captain_name'] = $captainName;
        $sanitized['agency_name'] = $agencyName;
        $sanitized['submitted_by_display'] = $submittedByDisplay;

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
