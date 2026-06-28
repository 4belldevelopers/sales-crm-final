<?php

/*** ########################################################
Controller    	: 	ApplicationNoteController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/

namespace App\Http\Controllers\ProductCompany;

use App\Http\Requests\ApplicantNote\StoreNote;
use App\Models\ApplicantNote;
use App\Helper\Reply;

class ApplicantNoteController extends ProductCompanyBaseController
{
    public function store(StoreNote $request)
    	{
			$note 						= new ApplicantNote();
			$note->note_text 			= $request->note;
			$note->user_id 				= $this->user->id;
			$note->job_application_id 	= $request->id;
			$note->save();

        $this->notes = ApplicantNote::where('job_application_id', $request->id)
						->orderBy('id', 'desc')
						->get();
						
        $view = view('product_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function update(StoreNote $request, $id)
    	{
        $note = ApplicantNote::find($id);
        $note->note_text = $request->note;
        $note->user_id = $this->user->id;
        $note->save();

        $this->notes = ApplicantNote::where('job_application_id', $note->job_application_id)->orderBy('id', 'desc')->get();
        $view = view('product_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.noteAddSuccess'), ['view' => $view]);
    }

    public function destroy($id)
    	{
        $note = ApplicantNote::find($id);
        ApplicantNote::destroy($id);

        $this->notes = ApplicantNote::where('job_application_id', $note->job_application_id)->orderBy('id', 'desc')->get();
        $view = view('product_company.applicant_notes.show', $this->data)->render();
        return Reply::successWithData(__('messages.recordDeleted'), ['view' => $view]);
    }

}
