<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * V2 Add Team Step 4 — hiring_manager() N+1 fix
 *
 * The N+1 pattern queried distinct designations then fired one DB query
 * per designation inside a foreach loop, building $contPersonArr — a
 * structure the Blade view never actually renders.  The fix removes the
 * loop entirely and keeps only the single flat $this->assigned query.
 *
 * These structural tests verify the source stays in the fixed state so
 * a future edit cannot silently re-introduce the N+1 pattern.
 */
class HiringManagerBatchQueryTest extends TestCase
{
    private string $controllerFile;
    private string $source;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllerFile = base_path(
            'app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php'
        );
        $this->source = file_get_contents($this->controllerFile);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // A — N+1 loop is gone
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function n1_designation_query_is_removed(): void
    {
        // The first step of the old N+1: SELECT DISTINCT contact_person_designation
        // from a groupBy().  It must no longer exist inside hiring_manager().
        $hiringManagerBody = $this->extractHiringManagerBody();

        $this->assertStringNotContainsString(
            "groupBy('client_contact_info.contact_person_designation')",
            $hiringManagerBody,
            'The per-designation groupBy() query that seeds the N+1 loop must be removed from hiring_manager()'
        );
    }

    /** @test */
    public function n1_per_designation_foreach_is_removed(): void
    {
        $hiringManagerBody = $this->extractHiringManagerBody();

        // The inner N+1 loop matched on designation:
        $this->assertStringNotContainsString(
            "contact_person_designation', \$cval->contact_person_designation",
            $hiringManagerBody,
            'Per-designation where() inside a foreach must not exist in hiring_manager()'
        );
    }

    /** @test */
    public function cont_person_arr_assignment_is_removed(): void
    {
        $hiringManagerBody = $this->extractHiringManagerBody();

        // $this->contPersonArr = $contPersonArr was set but never used in the view.
        $this->assertStringNotContainsString(
            '$this->contPersonArr',
            $hiringManagerBody,
            '$this->contPersonArr was never rendered by hiring_manager.blade.php and must be removed'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // B — Single flat query is present and correct
    // ─────────────────────────────────────────────────────────────────────────

    /** @test */
    public function single_assigned_query_exists(): void
    {
        $hiringManagerBody = $this->extractHiringManagerBody();

        $this->assertStringContainsString(
            "\$this->assigned = DB::table('client_contact_info')",
            $hiringManagerBody,
            '$this->assigned flat query must remain in hiring_manager()'
        );
    }

    /** @test */
    public function assigned_query_fetches_all_required_columns(): void
    {
        $hiringManagerBody = $this->extractHiringManagerBody();

        foreach (['contact_person_name', 'contact_person_email', 'contact_person_number', 'calling_code', 'contact_person_designation'] as $col) {
            $this->assertStringContainsString(
                $col,
                $hiringManagerBody,
                "Column '{$col}' must be selected in the single assigned query"
            );
        }
    }

    /** @test */
    public function assigned_query_returns_array_not_collection(): void
    {
        $hiringManagerBody = $this->extractHiringManagerBody();

        // The view's line 599 has: is_array($assigned) ? $assigned : $assigned->all()
        // The ->toArray() call ensures is_array() is true, bypassing the ->all() branch.
        $this->assertStringContainsString(
            '->get()->toArray()',
            $hiringManagerBody,
            '$this->assigned must call ->get()->toArray() so the view receives a plain PHP array'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Extract just the body of hiring_manager() from the source file.
     * We slice from the method signature to the next public function,
     * avoiding false positives from other methods in the ~5 MB controller.
     */
    private function extractHiringManagerBody(): string
    {
        $start = strpos($this->source, 'public function hiring_manager($id)');
        $this->assertNotFalse($start, 'hiring_manager() method must exist in controller');

        // Find the next public function after hiring_manager
        $nextMethod = strpos($this->source, 'public function hiring_contact_person', $start);
        $this->assertNotFalse($nextMethod, 'hiring_contact_person() method must follow hiring_manager()');

        return substr($this->source, $start, $nextMethod - $start);
    }
}
