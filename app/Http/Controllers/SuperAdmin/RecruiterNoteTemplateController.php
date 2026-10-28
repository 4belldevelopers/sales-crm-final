<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\StoreOrUpdateRecruiterNoteTemplateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * RecruiterNoteTemplateController
 *
 * Intent: Manages global recruiter note templates (company_id = NULL) from the Super Admin panel.
 * These standardized templates are consumed across the ATS by recruiters and hiring managers
 * during candidate application screening, interview notes, and relationship evaluations.
 *
 * Tenancy: Global Super Admin scope strictly filtered by whereNull('company_id').
 * Database Truth: recruiter_note_master (database/schema/mysql-schema.sql:3864).
 */
class RecruiterNoteTemplateController extends SuperAdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Recruiter Note Templates';
    }

    /**
     * Display the recruiter note templates listing page.
     *
     * Computes live KPI metrics (Total, Active, Inactive) and passes the formatted
     * collection to the Modern Card + Sub-Card Enterprise View.
     */
    public function index()
    {
        $templates = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->orderBy('id', 'desc')
            ->get();

        // Calculate KPI metrics
        $this->totalCount = $templates->count();
        $this->activeCount = $templates->where('status', 1)->count();
        $this->inactiveCount = $templates->where('status', 0)->count();

        // Enrich templates with precomputed telemetry for zero-latency client rendering
        $this->templates = $templates->map(function ($tpl) {
            $rawContent = (string) $tpl->content;
            $cleanText = trim(strip_tags($rawContent));
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $tpl->word_count = $cleanText !== '' ? str_word_count($cleanText) : 0;
            $tpl->char_count = mb_strlen($cleanText);
            $tpl->preview_text = Str::limit($cleanText, 180);
            $tpl->created_formatted = $tpl->created_at ? Carbon::parse($tpl->created_at)->format('d M Y') : '—';
            $tpl->updated_human = $tpl->updated_at ? Carbon::parse($tpl->updated_at)->diffForHumans() : ($tpl->created_at ? Carbon::parse($tpl->created_at)->diffForHumans() : 'Just now');

            return $tpl;
        });

        return view('super-admin.recruiter-note.index', $this->data);
    }

    /**
     * Store a newly created global recruiter note template.
     */
    public function store(StoreOrUpdateRecruiterNoteTemplateRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $payload = $request->sanitizedPayload();

                $insertedId = DB::table('recruiter_note_master')->insertGetId([
                    'company_id' => null,
                    'title' => $payload['title'],
                    'content' => $payload['content'],
                    'status' => $payload['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $template = DB::table('recruiter_note_master')
                    ->where('id', $insertedId)
                    ->whereNull('company_id')
                    ->first();

                return Reply::successWithData('Recruiter note template added successfully.', [
                    'template' => $template,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate store failure: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return Reply::error('Failed to create recruiter note template. '.$e->getMessage());
        }
    }

    /**
     * Update the specified global recruiter note template.
     */
    public function update(StoreOrUpdateRecruiterNoteTemplateRequest $request, $id)
    {
        try {
            $template = DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->first();

            if (! $template) {
                return Reply::error('Template not found or has already been removed.');
            }

            return DB::transaction(function () use ($request, $id) {
                $payload = $request->sanitizedPayload();

                DB::table('recruiter_note_master')
                    ->whereNull('company_id')
                    ->where('id', $id)
                    ->update([
                        'title' => $payload['title'],
                        'content' => $payload['content'],
                        'status' => $payload['status'],
                        'updated_at' => now(),
                    ]);

                $updated = DB::table('recruiter_note_master')
                    ->where('id', $id)
                    ->whereNull('company_id')
                    ->first();

                return Reply::successWithData('Recruiter note template updated successfully.', [
                    'template' => $updated,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate update failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to update recruiter note template. '.$e->getMessage());
        }
    }

    /**
     * Remove the specified global recruiter note template.
     */
    public function destroy($id)
    {
        try {
            $template = DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->first();

            if (! $template) {
                return Reply::error('Template not found or already deleted.');
            }

            DB::transaction(function () use ($id) {
                DB::table('recruiter_note_master')
                    ->whereNull('company_id')
                    ->where('id', $id)
                    ->delete();
            });

            return Reply::success('Template deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate destroy failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to delete recruiter note template.');
        }
    }

    /**
     * Fetch raw template data for modal edit or inspection.
     */
    public function getData($id)
    {
        $template = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where('id', $id)
            ->first();

        if (! $template) {
            return Reply::error('Template not found.');
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $template]);
    }

    /**
     * Instant 1-click AJAX status toggle (Active <=> Inactive).
     * Eliminates modal overhead for quick operational adjustments.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $template = DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->first();

            if (! $template) {
                return Reply::error('Template not found.');
            }

            $newStatus = (int) $template->status === 1 ? 0 : 1;

            DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            $message = $newStatus === 1
                ? 'Template activated successfully.'
                : 'Template deactivated successfully.';

            return Reply::successWithData($message, [
                'id' => (int) $id,
                'template_status' => $newStatus,
                'status_label' => $newStatus === 1 ? 'Active' : 'Inactive',
            ]);
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate toggleStatus failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to change template status.');
        }
    }

    /**
     * Instant 1-click duplicate: Clones an existing template as an inactive draft copy.
     */
    public function duplicate(Request $request, $id)
    {
        try {
            $template = DB::table('recruiter_note_master')
                ->whereNull('company_id')
                ->where('id', $id)
                ->first();

            if (! $template) {
                return Reply::error('Source template not found.');
            }

            $duplicateTitle = Str::limit(trim($template->title).' (Copy)', 250, '');

            $newId = DB::table('recruiter_note_master')->insertGetId([
                'company_id' => null,
                'title' => $duplicateTitle,
                'content' => $template->content,
                'status' => 0, // Cloned templates default to Inactive draft
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cloned = DB::table('recruiter_note_master')
                ->where('id', $newId)
                ->whereNull('company_id')
                ->first();

            return Reply::successWithData('Template duplicated successfully as draft.', [
                'template' => $cloned,
            ]);
        } catch (\Throwable $e) {
            Log::error('SuperAdmin RecruiterNoteTemplate duplicate failure: '.$e->getMessage(), [
                'id' => $id,
                'exception' => $e,
            ]);

            return Reply::error('Failed to duplicate template.');
        }
    }
}
