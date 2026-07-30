<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Manages global recruiter note templates (company_id = NULL) from the super admin panel.
class RecruiterNoteTemplateController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Recruiter Note Templates';
    }

    public function index()
    {
        $this->templates = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->orderBy('id', 'desc')
            ->get();

        return view('super-admin.recruiter-note.index', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::table('recruiter_note_master')->insert([
            'company_id' => null,
            'title'      => $request->title,
            'content'    => $request->content,
            'status'     => $request->has('status') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Reply::success('Template added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->update([
                'title'      => $request->title,
                'content'    => $request->content,
                'status'     => $request->has('status') ? 1 : 0,
                'updated_at' => now(),
            ]);

        return Reply::success('Template updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->delete();

        return Reply::success('Template deleted successfully.');
    }

    public function getData($id)
    {
        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (!$template) {
            return Reply::error('Template not found.');
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $template]);
    }
}
