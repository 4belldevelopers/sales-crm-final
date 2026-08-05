<?php

/**
 * Deferred email notification after a candidate is submitted via store().
 *
 * Accepts lightweight identifiers; runs all JOIN queries asynchronously so the
 * HTTP submit thread only pays ~40 ms for primary ES indexing and redirects.
 * Dispatched AFTER DB::commit() so all joined rows are guaranteed visible.
 */

namespace App\Jobs;

use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueueCandidateNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;
    public int $timeout = 60;

    public function __construct(
        private readonly int    $addJobWiseCandidateId,
        private readonly int    $jobId,
        private readonly int    $companyId,
        private readonly string $submittedByEmail,
    ) {}

    public function handle(): void
    {
        try {
            $checkMailPermision = GeneralMethod::mailPermission(1);
            if (!$checkMailPermision) {
                return;
            }

            // Rebuild email context from the committed rows (mirrors store() lines 2162-2268).
            $wise_c = DB::table('job_applications')
                ->select(
                    'master_status.name',
                    'master_status.css_style',
                    'add_job_wise_condidates.child_status_id as childStatus',
                    'add_job_wise_condidates.master_status_id',
                    'jobs.client_id',
                    'jobs.title',
                    'jobs.id as job_id',
                    'users.name as posted_by',
                    'users.image as posted_by_image',
                    'companies.company_name',
                    'add_job_wise_condidates.created_at',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'recommendation_relation.to_company_id'
                )
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', 'job_applications.id')
                ->leftJoin('users', 'users.id', 'job_applications.submitted_by')
                ->leftJoin('companies', 'companies.id', 'users.company_id')
                ->join('master_status', 'master_status.id', 'add_job_wise_condidates.master_status_id')
                ->join('jobs', 'jobs.id', 'add_job_wise_condidates.job_id')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->where('add_job_wise_condidates.id', '=', $this->addJobWiseCandidateId)
                ->where('add_job_wise_condidates.job_id', '=', $this->jobId)
                ->first();

            if (!$wise_c) {
                Log::warning('QueueCandidateNotificationJob: add_job_wise_condidates row not found', [
                    'add_job_wise_candidate_id' => $this->addJobWiseCandidateId,
                    'job_id'                    => $this->jobId,
                ]);
                return;
            }

            // Fetch the linked job_application for candidate detail fields.
            $jobApplication = DB::table('job_applications')
                ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
                ->where('add_job_wise_condidates.id', $this->addJobWiseCandidateId)
                ->select('job_applications.*')
                ->first();

            if (!$jobApplication) {
                return;
            }

            $name = '--------';
            if (@$wise_c->master_status_id == 8) {
                $name = '';
            }
            if (!empty(@$wise_c->childStatus) && @$wise_c->childStatus > 0) {
                $childStatus = DB::table('candidate_status')->where('id', $wise_c->childStatus)->first();
                if (!empty($childStatus)) {
                    $name = @$childStatus->name;
                }
            }

            $np = $jobApplication->np_id;
            if (empty($np)) {
                $np = 'N/A';
            }
            if ($jobApplication->serving_notice_period_mode == 0) {
                $np = $jobApplication->np_id . ' Days';
                if ($jobApplication->np_id == 1) {
                    $np = $jobApplication->np_id . ' Day';
                }
            } elseif ($jobApplication->serving_notice_period_mode == 1) {
                $np = $jobApplication->np_id;
                if ($np != 'Immediate') {
                    $np = 'SNP LWD ' . date('d-M-Y', strtotime($jobApplication->np_id));
                }
            }

            $strAdded = DB::table('clients')
                ->select('companies.company_name', 'clients.name', 'clients.self_company')
                ->join('companies', 'companies.id', '=', 'clients.company_id')
                ->where('clients.id', @$wise_c->client_id)
                ->first();

            $contact_person_name = '';
            if ($strAdded && $strAdded->self_company == 1) {
                $contact_person_name = $strAdded->company_name;
            } elseif ($strAdded) {
                $contact_person_name = $strAdded->name;
            }

            $company_or_client = GeneralMethod::companyDetailsByClient($this->jobId);

            $data = [
                'VendorTeam'             => @$wise_c->company_name,
                'ReqID'                  => 'R' . @$wise_c->job_id,
                'Reqname'                => @$wise_c->title,
                'CompanyNameClientname'  => $company_or_client,
                'CandidateStatus'        => $jobApplication->duplicate,
                'CurrentStage'           => @$wise_c->name,
                'SubStage'               => $name,
                'CandidateName'          => $jobApplication->full_name,
                'Experience'             => $jobApplication->total_exp,
                'Position'               => $jobApplication->current_designation,
                'NoticePeriod'           => $np,
                'RefferedBy'             => @$wise_c->posted_by,
                'SubmittedOn'            => date('d-M-Y H:i:s', strtotime(@$wise_c->created_at)),
            ];

            $subject = "New Candidate Added | $wise_c->title | $contact_person_name | R-$wise_c->job_id";

            $basecompany  = GeneralMethod::baseCompany();
            $senderName   = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress  = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments  = [];

            $mailTypeSubject = GeneralMethod::mailTypeSubject(1, 1);
            $notificationemail = GeneralMethod::notification($this->companyId, 'Recuitment');
            $defailsEmail = DB::table('companies')->where('id', $this->companyId)->value('company_email');
            $to_Mail_Id   = $notificationemail->email ?? $defailsEmail ?? '';

            // Remove the submitter's own email from the notification list.
            $notification = DB::table('clients')
                ->where('id', '=', @$wise_c->client_id)
                ->where('self_company', '=', 1)
                ->pluck('email')
                ->toArray();
            $index = array_search($this->submittedByEmail, $notification);
            if ($index !== false) {
                unset($notification[$index]);
                $notification = array_values($notification);
            }
            $cc = [];

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.candidate.add-candidate',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $this->companyId
            )->onConnection('emails');

        } catch (\Throwable $e) {
            Log::error('QueueCandidateNotificationJob failed', [
                'add_job_wise_candidate_id' => $this->addJobWiseCandidateId,
                'job_id'                    => $this->jobId,
                'error'                     => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
