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

    public function store(StoreNote $request)
    {

      
        
            $wiseId 	= $request->wiseId;
          
			$note 						= new ApplicantNote();
			$note->note_text 			= $request->note;
			$note->user_id 				=auth()->user()->id;
			$note->job_application_id 	= $request->id;
			$note->save();
 
            $response = $this->elasticService->getClient()->update([
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


            $this->job_additional_detail = "";
			
            // $this->notes = ApplicantNote::where('job_application_id',  $request->id)->get();
            $this->notes = ApplicantNote::with('user')->where('job_application_id', $request->id)->get();

           
            $cachekeys = [
                'candidateDetails_version_user_'.auth()->user()->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }
		     
						
        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function update(StoreNote $request, $id)
    {
        $note = ApplicantNote::find($id);
        $note->note_text = $request->note;
        $note->user_id = auth()->user()->id;
        $note->save();

        // $this->notes = ApplicantNote::where('job_application_id', $note->job_application_id)->get();
        $this->notes = ApplicantNote::with('user')->where('job_application_id', $note->job_application_id)->get();
        
        $cachekeys = [
            'candidateDetails_version_user_'.auth()->user()->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function destroy($id)
    	{
        $note = ApplicantNote::find($id);
        ApplicantNote::destroy($id);

        $this->elasticService->getClient()->deleteByQuery([
				'index' => ELASTIC_APPLICANT_NOTES,
                'conflicts' => 'proceed',   // ⭐ THIS LINE FIXES YOUR ERROR
				'body'  => [
					'query' => [
						'bool' => [
							'must' => [
								['term' => ['id' => $id]]
							],
						],
					],
				],
			]);

        if($note){
        //  $this->notes = ApplicantNote::where('job_application_id', $note->job_application_id)->get();
        $this->notes = ApplicantNote::with('user')->where('job_application_id', $note->job_application_id)->get();
        }else{
            $this->notes = collect();
        }

         $cachekeys = [
            'candidateDetails_version_user_'.auth()->user()->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $view = view('service_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.recordDeleted'), ['view' => $view]);
        
    }


}
