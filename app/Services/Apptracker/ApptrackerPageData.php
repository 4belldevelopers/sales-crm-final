<?php

namespace App\Services\Apptracker;

use App\Helper\GeneralMethod;
use App\Models\Job;
use App\Services\ReferenceDataCache;
use Illuminate\Support\Collection;

/**
 * ApptrackerPageData — Phase F of the Step-16 Apptracker controller slice.
 *
 * Typed value object / assembler for Apptracker page bootstrap data (~600 LoC
 * to migrate from ServiceCompanyAppTrackerController::index() and related
 * page-load methods).
 *
 * Bundles all the data that every Apptracker page load needs:
 *   - Job details
 *   - Board columns (ApplicationStatus via ReferenceDataCache)
 *   - Feedback statuses
 *   - Ticket statuses
 *   - Qualification names
 *   - Counter totals (custome_count)
 *
 * Migration status: SKELETON
 *
 * @property-read Job        $job
 * @property-read Collection $boardColumns
 * @property-read array      $feedbackStatus
 * @property-read Collection $ticketStatuses
 * @property-read Collection $qualificationNames
 */
class ApptrackerPageData
{
    public readonly Job $job;
    public readonly Collection $boardColumns;
    public readonly array $feedbackStatus;
    public readonly Collection $ticketStatuses;
    public readonly Collection $qualificationNames;

    private function __construct(Job $job)
    {
        $this->job                = $job;
        $this->boardColumns       = ReferenceDataCache::applicationStatuses();
        $this->feedbackStatus     = ReferenceDataCache::feedbackStatus();
        $this->ticketStatuses     = ReferenceDataCache::ticketStatuses();
        $this->qualificationNames = ReferenceDataCache::qualificationNames();
    }

    /**
     * Build the page data object for a given job.
     *
     * @param  int  $jobId
     * @return static
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function forJob(int $jobId): static
    {
        $job = Job::withoutGlobalScopes()->findOrFail($jobId);
        return new static($job);
    }

    /**
     * Convert to array for compact() / view()->with() calls.
     */
    public function toArray(): array
    {
        return [
            'job'                => $this->job,
            'boardColumns'       => $this->boardColumns,
            'feedback_status'    => $this->feedbackStatus,
            'ticket_status'      => $this->ticketStatuses,
            'qualificationNames' => $this->qualificationNames,
        ];
    }
}
