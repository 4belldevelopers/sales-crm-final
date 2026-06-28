<?php
/*** ########################################################
Controller    	: 	AdminQustionController
Purmose			: 
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021	
########################################################***/ 

namespace App\Http\Controllers\ProductCompany;

use App\Helper\Reply;
use App\Http\Requests\Question\StoreRequest;
use App\Http\Requests\Question\UpdateRequest;
use App\Models\Question;
use Yajra\DataTables\Facades\DataTables;

class ProductCompanyQuestionController extends ProductCompanyBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'menu.question';
        $this->pageIcon = 'icon-grid';
    }

    
    public function index()
    	{
        abort_if(! $this->user->cans('view_question'), 403);

        return view('product_company.question.index', $this->data);
    }
    
    public function create()
    	{
        abort_if(! $this->user->cans('add_question'), 403);

        return view('product_company.question.create', $this->data);
    }
   
    public function store(StoreRequest $request)
    	{
        abort_if(! $this->user->cans('add_question'), 403);
        $question = new Question();
        $question->question = $request->question;
        $question->required = $request->required;
        $question->save();

        return Reply::redirect(route('product_company.questions.index'), __('menu.question').' '.__('messages.createdSuccessfully'));
    }
   
    public function show($id)
   		{
        //
    }
   
    public function edit($id)
    	{
        abort_if(! $this->user->cans('edit_question'), 403);

        $this->question = Question::find($id);
        return view('product_company.question.edit', $this->data);
    }
    
    public function update(UpdateRequest $request, $id)
    	{
        abort_if(! $this->user->cans('edit_question'), 403);
        $question = Question::find($id);
        $question->question = $request->question;
        $question->required = $request->required;
        $question->save();

        return Reply::redirect(route('product_company.questions.index'), __('menu.question').' '.__('messages.updatedSuccessfully'));
    }
   
    public function destroy($id)
    	{
        abort_if(! $this->user->cans('delete_question'), 403);

        Question::destroy($id);
        return Reply::success(__('messages.questionDeleted'));
    }

    public function data() 
		{
        abort_if(! $this->user->cans('view_question'), 403);

        $questions = Question::all();

        return DataTables::of($questions)
            ->addColumn('action', function ($row) {
                $action = '';

                if( $this->user->cans('edit_question')){
                    $action.= '<a href="' . route('product_company.questions.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="tooltip" data-original-title="'.__('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }

                if( $this->user->cans('delete_question')){
                    $action.= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="'.__('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('required', function ($row) {
                return ucfirst($row->required);
            })
            ->editColumn('requ', function ($row) {
                return ucfirst($row->question);
            })
            ->addIndexColumn()
            ->make(true);
    }

}
