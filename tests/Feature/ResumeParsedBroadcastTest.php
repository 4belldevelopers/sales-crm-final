<?php

namespace Tests\Feature;

use App\Events\ResumeParsed;
use App\Models\ResumeUpload;
use App\Services\ResumeCandidateSync;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Phase 8 — ResumeParsed broadcast contract.
 *
 * Properties under test:
 *   1. The event implements ShouldBroadcast.
 *   2. It targets `App.Models.Company.{cid}` private channel.
 *   3. Its `broadcastAs()` returns `resume.parsed` (the leading
 *      `.` in Echo listeners matches this prefix).
 *   4. The payload shape is minimal: NO fingerprint blob, NO
 *      database-only metadata leaked. Only `fillable` columns
 *      exhibit in the JS — the projection is keyed off
 *      {@see ResumeCandidateSync::COLUMN_MAP} so a column cannot
 *      appear in the broadcast unless the server wired it.
 *   5. Tenant identity is correct: channel = `App.Models.Company.{cid}`
 *      where cid = $row->company_id, not a foreign tenant.
 *
 * These tests do NOT bind an authenticated user; the channel
 * `Broadcast::channel('App.Models.Company.{companyId}', ...)` itself
 * is validated separately via direct invocation in
 * ResumeChannelAuthTest (smoke — we don't drive a websocket
 * fixture because no live socket is reachable in phpunit).
 */
class ResumeParsedBroadcastTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('cache.default', 'array');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('r2');
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        \Illuminate\Support\Facades\Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->unsignedInteger('user_id')->nullable();
            $t->unsignedInteger('candidate_id')->nullable();
            $t->string('resource_type', 32)->default('job_application');
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
            $t->string('status', 32)->default('Created');
            $t->string('parser_vendor', 64)->nullable();
            $t->string('parser_status', 32)->nullable();
            $t->unsignedTinyInteger('parse_attempt_count')->default(0);
            $t->text('parsed_data_json')->nullable();
            $t->timestamp('upload_started_at')->nullable();
            $t->timestamp('upload_completed_at')->nullable();
            $t->timestamp('parse_started_at')->nullable();
            $t->timestamp('parse_completed_at')->nullable();
            $t->timestamp('linked_at')->nullable();
            $t->unsignedTinyInteger('cleanup_attempt_count')->default(0);
            $t->string('cleanup_reason', 64)->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
            $t->unique('uuid');
        });
    }

    private function seedParsedRow(int $companyId, int $candidateId, array $parsed): ResumeUpload
    {
        $uuid = '11111111-1111-1111-1111-' . random_int(100000000, 999999999);
        $path = "Companies/test_{$companyId}/Resumes/2026/07/JERO_{$uuid}.pdf";
        $url = 'https://file.jero.works/' . $path;
        return ResumeUpload::create([
            'uuid' => $uuid,
            'company_id' => $companyId,
            'candidate_id' => $candidateId,
            'resource_type' => ResumeUpload::RESOURCE_TYPE_JOB_APPLICATION,
            'original_filename' => 'cv.pdf',
            'stored_filename' => basename($path),
            'r2_path' => $path,
            'r2_url' => $url,
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'status' => ResumeUpload::STATUS_PARSED,
            'parser_vendor' => 'phase2_stub',
            'parsed_data_json' => $parsed,
            'parse_completed_at' => Carbon::now(),
        ]);
    }

    public function test_resume_parsed_event_implements_should_broadcast(): void
    {
        $row = $this->seedParsedRow(7, 99, ['fingerprint' => 'abc123']);
        $event = new ResumeParsed($row, succeeded: true);
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    public function test_broadcast_targets_company_scoped_private_channel(): void
    {
        $row = $this->seedParsedRow(42, 99, []);
        $event = new ResumeParsed($row, succeeded: true);
        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $first = $channels[0];
        $this->assertInstanceOf(PrivateChannel::class, $first);
        // Laravel prepends 'private-' internally; Echo's client
        // strips the prefix before the subscription channel name.
        // The test asserts the server-side representation so a
        // future framework swap cannot silently change it.
        $this->assertSame('private-App.Models.Company.42', $first->name);
    }

    public function test_broadcast_as_returns_resume_parsed_event_name(): void
    {
        $row = $this->seedParsedRow(7, 99, []);
        $event = new ResumeParsed($row, succeeded: true);
        $this->assertSame('resume.parsed', $event->broadcastAs());
    }

    public function test_payload_minimal_includes_only_fillable_columns_not_fingerprint(): void
    {
        $parsed = [
            'fingerprint' => 'abc123',
            'full_name' => 'Smita Sharma',
            'email' => 'smita@example.com',
            'phone' => '+91 98765 43210',
            'current_company' => 'Popular Digital AI',
            'current_designation' => 'Senior Engineer',
            'careersummary' => 'Built the resume subsystem.',
            'detected_size_bytes' => 999999,
            'detected_mime' => 'application/pdf',
            'parsed_at' => '2026-07-24T00:00:00+00:00',
        ];
        $row = $this->seedParsedRow(7, 99, $parsed);
        $payload = (new ResumeParsed($row, succeeded: true))->broadcastWith();

        $this->assertSame($row->uuid, $payload['session_uuid']);
        $this->assertSame(7, $payload['company_id']);
        $this->assertSame(99, $payload['candidate_id']);
        $this->assertSame('Parsed', $payload['status']);

        // Fillable columns project — non-fillable columns NEVER see
        // the wire. Fingerprint and detected_* are part of the
        // parsed_data_json — they must NOT be in the broadcast.
        $this->assertArrayHasKey('fillable', $payload);
        $fillable = $payload['fillable'];
        $expected = ['full_name', 'email', 'phone', 'current_company', 'current_designation', 'careersummary'];
        // sort() in PHP returns true/false and mutates by reference.
        // Capture the sorted arrays first, then compare.
        $expectedSorted = $expected;
        sort($expectedSorted);
        $actualSorted = array_keys($fillable);
        sort($actualSorted);
        $this->assertSame($expectedSorted, $actualSorted);
        foreach ($expected as $col) {
            $this->assertSame($parsed[$col], $fillable[$col]);
        }
        $this->assertArrayNotHasKey('fingerprint', $payload);
        $this->assertArrayNotHasKey('detected_size_bytes', $payload);
        $this->assertArrayNotHasKey('detected_mime', $payload);
        // parsed_at IS a top-level field (server-emitted timestamp
        // for logging / UI); it is NOT inside `fillable`, just
        // confirmed by the assertArrayNotHasKey call here against
        // a shape that does NOT contain a top-level mistake.
        $this->assertArrayHasKey('parsed_at', $payload);
        $this->assertNotEmpty($payload['parsed_at']);
    }

    public function test_status_distinguishes_success_and_failure(): void
    {
        $row = $this->seedParsedRow(7, 99, []);
        $success = (new ResumeParsed($row, succeeded: true))->broadcastWith();
        $failure = (new ResumeParsed($row, succeeded: false))->broadcastWith();

        $this->assertSame('Parsed', $success['status']);
        $this->assertSame('ParseFailed', $failure['status']);
        // Both share the same channel + broadcastAs — subscribers
        // listen once and gate on the `status` field.
    }

    public function test_fillable_projection_keys_match_candidate_sync_column_map(): void
    {
        // The two projections MUST agree on what columns are
        // auto-fillable. If a column appears in one but not the
        // other, broadcasting leaks data the server won't write,
        // OR the server writes a column the JS doesn't know about.
        $parsed = [];
        foreach (ResumeCandidateSync::COLUMN_MAP as $key => $col) {
            $parsed[$key] = 'parsed-value-' . $key;
        }
        $row = $this->seedParsedRow(7, 99, $parsed);
        $payload = (new ResumeParsed($row, succeeded: true))->broadcastWith();

        $projectionKeys = array_keys($payload['fillable']);
        $expectedKeys = array_keys(ResumeCandidateSync::COLUMN_MAP);
        // sort() returns bool + mutates by reference. Compare copies.
        $expectedSorted = $expectedKeys;
        $actualSorted = $projectionKeys;
        sort($expectedSorted);
        sort($actualSorted);
        $this->assertSame(
            $expectedSorted,
            $actualSorted,
            'fillable projection must mirror COLUMN_MAP keys'
        );
    }
}
