<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BenchRmNoteTemplateController
 *
 * Manages global Relationship Manager note templates (company_id = NULL)
 * from the Super Admin panel for Bench Candidate / Talent profiles.
 * Completely isolated from recruiter_note_master and company-level customizations.
 */
class BenchRmNoteTemplateController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Relationship Manager Note Templates';
    }

    public function index()
    {
        $this->templates = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->orderBy('id', 'desc')
            ->get();

        return view('super-admin.rm-note.index', $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::table('bench_rm_note_master')->insert([
            'company_id' => null,
            'title'      => $request->title,
            'content'    => $request->content,
            'status'     => $request->has('status') ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Reply::success('Relationship Manager template added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->update([
                'title'      => $request->title,
                'content'    => $request->content,
                'status'     => $request->has('status') ? 1 : 0,
                'updated_at' => now(),
            ]);

        return Reply::success('Relationship Manager template updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->delete();

        return Reply::success('Relationship Manager template deleted successfully.');
    }

    public function getData($id)
    {
        $template = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (!$template) {
            return Reply::error('Template not found.');
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $template]);
    }
}
