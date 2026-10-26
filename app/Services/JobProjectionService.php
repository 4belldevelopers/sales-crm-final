<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * JobProjectionService
 *
 * Rebuilds canonical Elasticsearch job projections from the MySQL System of Record.
 *
 * Guarantees:
 * 1. Single-Snapshot Invariant: Reads payload and aggregate version under a consistent snapshot.
 * 2. Lock-Free Version Verification: Optimistically verifies version did not mutate during assembly,
 *    avoiding long-lived exclusive locks (FOR UPDATE).
 * 3. Full Schema Preservation: Preserves all 25+ fields, preventing ghost-document amputation.
 */
class JobProjectionService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Rebuilds and indexes the full canonical document for a job.
     */
    public function rebuildAndIndexCanonicalJob(int $jobId): bool
    {
        $snapshot = DB::transaction(function () use ($jobId) {
            $job = DB::table('jobs')->where('id', $jobId)->first();
            if (! $job) {
                return null;
            }

            $vStart = (int) ($job->aggregate_version ?? $job->state_version ?? 0);
            $completeDocument = $this->assembleCanonicalDocument($job);

            // Optimistic version verification: ensure version did not advance mid-assembly
            $vEnd = (int) DB::table('jobs')->where('id', $jobId)->value('aggregate_version');
            if ($vEnd !== $vStart && $vEnd > 0) {
                return null; // Concurrent mutation occurred; abort and allow retry
            }

            $completeDocument['projection_version'] = $vStart;

            return [
                'version' => $vStart,
                'document' => $completeDocument,
            ];
        });

        if ($snapshot) {
            $index = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';
            $this->elasticService->getClient()->index([
                'index' => $index,
                'id' => (string) $jobId,
                'body' => $snapshot['document'],
            ]);

            return true;
        }

        return false;
    }

    /**
     * Assembles all canonical fields from MySQL SoR for the job document.
     */
    public function assembleCanonicalDocument(object $job): array
    {
        $jobId = (int) $job->id;
        $companyId = (int) ($job->company_id ?? 0);

        // Fetch company / client metadata
        $company = DB::table('companies')->where('id', $companyId)->first();
        $client = $job->client_id ? DB::table('clients')->where('id', $job->client_id)->first() : null;
        $category = $job->job_category_id ? DB::table('job_categories')->where('id', $job->job_category_id)->first() : null;

        // Fetch recommendations
        $recommendations = DB::table('recommendation_relation')
            ->select('id', 'from_company_id', 'to_company_id', 'final_status', 'created_at')
            ->where('job_id', $jobId)
            ->get()
            ->map(fn ($r) => (array) $r)
            ->toArray();

        return [
            'id' => $jobId,
            'title' => (string) ($job->title ?? ''),
            'status' => (string) ($job->status ?? 'active'),
            'min_exp' => (int) ($job->min_exp ?? 0),
            'max_exp' => (int) ($job->max_exp ?? 0),
            'min_salary' => $job->min_salary ?? null,
            'max_salary' => $job->max_salary ?? null,
            'min_salary_currency' => $job->min_salary_currency ?? '$',
            'max_salary_currency' => $job->max_salary_currency ?? '$',
            'min_sal_mode' => $job->min_sal_mode ?? null,
            'max_sal_mode' => $job->max_sal_mode ?? null,
            'created_at' => $job->created_at ? date('Y-m-d H:i:s', strtotime((string) $job->created_at)) : now()->toDateTimeString(),
            'updated_at' => $job->updated_at ? date('Y-m-d H:i:s', strtotime((string) $job->updated_at)) : now()->toDateTimeString(),
            'hiring_manager' => (string) ($job->hiring_manager ?? ''),
            'skills' => (string) ($job->skills ?? ''),
            'total_positions' => (int) ($job->total_positions ?? 1),
            'client_id' => (int) ($job->client_id ?? 0),
            'comId' => $companyId,
            'location_id' => (int) ($job->location_id ?? 0),
            'category_id' => (int) ($job->job_category_id ?? 0),
            'client_name' => (string) ($client->name ?? ($client->client_name ?? '')),
            'client_company' => (string) ($client->name ?? ($client->client_company ?? '')),
            'company_name' => (string) ($company->company_name ?? ''),
            'self_company' => (int) ($job->self_company ?? 0),
            'work_type' => (string) ($job->work_type ?? 'no'),
            'employment_type' => (int) ($job->employment_type ?? 0),
            'department_name' => (string) ($category->name ?? 'General Role'),
            'category_name' => (string) ($category->name ?? 'General Role'),
            'state_version' => (int) ($job->state_version ?? $job->aggregate_version ?? 1),
            'recommendation_relation' => $recommendations,
        ];
    }
}
