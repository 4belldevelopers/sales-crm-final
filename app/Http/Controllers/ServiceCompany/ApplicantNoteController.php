<?php

/*** ########################################################
Controller    	: 	ApplicationNoteController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################** */

namespace App\Http\Controllers\ServiceCompany;

use App\Http\Requests\ApplicantNote\StoreNote;
use App\Models\ApplicantNote;
use App\Services\ElasticService;
use Carbon\Carbon;
use App\Helper\GeneralMethod;
use App\Helper\Reply;

class ApplicantNoteController extends ServiceCompanyBaseController
{

    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        
        $this->elasticService = $elasticService;
        
        
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $applicationId = $request->input('application_id') ?: $request->input('id');
        $companyId = auth()->user()->company_id;

        // Intent: Verify application belongs to user's company or authorized thirdparty company
        $app = \DB::table('job_applications')
            ->where('id', $applicationId)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                  ->orWhere('thirdparty_company_id', $companyId);
            })->first();

        if (! $app && ! auth()->user()->hasRole('admin')) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized notes access.']);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $notesQuery = ApplicantNote::with('user')
            ->where('job_application_id', $applicationId)
            ->orderBy('id', 'desc');

        $notesPaginator = $notesQuery->paginate($perPage, ['*'], 'page', $page);
        $this->notes = $notesPaginator->items();

        $view = view('service_company.applicant_notes.show', $this->data)->render();

        return Reply::dataOnly([
            'status' => 'success',
            'view' => $view,
            'current_page' => $notesPaginator->currentPage(),
            'last_page' => $notesPaginator->lastPage(),
            'total' => $notesPaginator->total(),
        ]);
    }

    public function store(StoreNote $request)
    {
        $wiseId 	= $request->wiseId;
      
        $note 						= new ApplicantNote();
        $note->note_text 			= $request->note;
        $note->user_id 				= auth()->user()->id;
        $note->job_application_id 	= $request->id;
        $note->save();

        try {
            $this->elasticService->getClient()->update([
                'index' => ELASTIC_APPLICANT_NOTES,
                'id'    => $note->id,
                'retry_on_conflict' => 5,
                'body'  => [
                    'doc' => [ 
                        'id' =>  $note->id,
                        'note_text' => $note->note_text,
                        'name' => auth()->user()->name,
                        'posted_by_image' => auth()->user()->image,
                        'user_id' => auth()->user()->id ?? null,
                        'add_job_wise_condidates_id' =>  $wiseId ?? null,
                        'job_application_id' => $request->id ?? null,
                        'created_at' => now()->toIso8601String(),
                        'updated_at' => now()->toIso8601String()
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Elasticsearch note store exception', ['error' => $e->getMessage()]);
        }

        $this->notes = ApplicantNote::with('user')->where('job_application_id', $request->id)->orderBy('id', 'desc')->take(15)->get();

        $companyId = auth()->user()->company_id ?? 0;
        $this->bustCandidateDrawerCache($request->id, $companyId);

        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function update(StoreNote $request, $id)
    {
        // Intent: Security fix — remove withoutGlobalScopes() and enforce tenant ownership validation (FINDING-SEC-01 - P0).
        $companyId = auth()->user()->company_id;
        $note = ApplicantNote::where('id', $id)->first();

        if (! $note) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Note not found or unauthorized.']);
        }

        $appCompany = \DB::table('job_applications')->where('id', $note->job_application_id)->value('company_id');
        if ($note->user_id !== auth()->id() && $appCompany !== $companyId && ! auth()->user()->hasRole('admin')) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized note update.']);
        }

        $note->note_text = $request->note;
        $note->save();

        $this->notes = ApplicantNote::with('user')->where('job_application_id', $note->job_application_id)->orderBy('id', 'desc')->take(15)->get();

        $this->bustCandidateDrawerCache($note->job_application_id, $companyId);

        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function destroy($id)
    {
        // Intent: Security fix — remove withoutGlobalScopes() and enforce tenant ownership validation (FINDING-SEC-01 - P0).
        $companyId = auth()->user()->company_id;
        $note = ApplicantNote::where('id', $id)->first();

        if (! $note) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Note not found or unauthorized.']);
        }

        $appCompany = \DB::table('job_applications')->where('id', $note->job_application_id)->value('company_id');
        if ($note->user_id !== auth()->id() && $appCompany !== $companyId && ! auth()->user()->hasRole('admin')) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Unauthorized note deletion.']);
        }

        ApplicantNote::destroy($id);

        try {
            $this->elasticService->getClient()->deleteByQuery([
                'index' => ELASTIC_APPLICANT_NOTES,
                'conflicts' => 'proceed',
                'body'  => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['id' => (int) $id]]
                            ],
                        ],
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Elasticsearch note delete exception', ['error' => $e->getMessage()]);
        }

        if ($note) {
            $this->notes = ApplicantNote::with('user')->where('job_application_id', $note->job_application_id)->orderBy('id', 'desc')->take(15)->get();
        } else {
            $this->notes = collect();
        }

        $this->bustCandidateDrawerCache($note->job_application_id, $companyId);

        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.recordDeleted'), ['view' => $view]);
    }

    /**
     * Atomically busts all tenant candidate drawer HTML caches across tracking pages
     * to ensure updated recruiter comments appear instantly without stale data.
     */
    private function bustCandidateDrawerCache($applicationId, $companyId)
    {
        cache()->forget('tenant:'.$companyId.':candidate_details:'.$applicationId);
        $drawerViewVersion = @filemtime(resource_path('views/service_company/job-applications/showdetail.blade.php')) ?: 0;
        $slugs = ['', 'cvtracker', 'apptracker', 'interview-schedule'];
        foreach ($slugs as $slug) {
            cache()->forget('tenant:'.$companyId.':candidate_details:'.$applicationId.':'.md5($slug).':view:'.$drawerViewVersion);
        }
    }


}
