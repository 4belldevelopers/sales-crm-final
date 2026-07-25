<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Logging\ResumeLogContext;
use App\Services\ResumeMetrics;
use App\Services\ResumeParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Throwable;

/**
 * Phase 9 — Lightweight parser endpoint.
 *
 * Accepts a multipart `file` upload from a logged-in recruiter's
 * browser, runs in-process extraction via {@see ResumeParserService},
 * and returns a structured JSON hint bag for the JS module to
 * populate empty fields. NO database writes, NO audit rows, NO
 * session creation, NO ParseResumeJob dispatch. This is a parallel
 * sibling to the existing presign-R2-PUT finalization pipeline —
 * recruiters experience instant field fill while the upload
 * completes in the background.
 *
 * Auth model: `auth` middleware only — broader than the existing
 * `service_company` group. We deliberately do NOT require the
 * service_company trait because the parser does not need
 * package-style persona checks; the recruiter's session cookie
 * is sufficient identity, and the response is the user's own
 * bytes so cross-tenant data exposure is impossible.
 *
 * Route layout (Phase 9): `routes/web.php` mounts `auth`-protected
 * POST /api/resume-parser/parse. We are NOT in the service route
 * group because that group requires `EnsureOneDeviceLogin`
 * middleware which would force single-device recruiter workflows;
 * parser must work from any device that has a valid session.
 */
class ResumeParserController extends Controller
{
    public function __construct(
        private readonly ResumeParserService $parser,
        private readonly ResumeMetrics $metrics,
    ) {}

    public function parse(Request $request): JsonResponse
    {
        $startedAt = microtime(true);
        $user = Auth::user();
        $companyId = (int) ($user?->company_id ?? 0);
        if ($companyId === 0) {
            return $this->fail('No company context', 403);
        }

        // Per-user rate limit — Redis INCR on `resumes:rl:parser:u:{id}`
        // with a 60-second TTL. Default 10 req/min; 0 disables. When
        // Redis is unreachable we fall through silently so a cache
        // outage cannot lock recruiters out of parsing.
        if (! $this->allowByRateLimit((int) $user->id)) {
            $retryAfter = (int) config('resumes.parser_rate_limit_window_seconds', 60);
            ResumeLogContext::warning('parser.rate_limited', [
                'company_id' => $companyId,
                'user_id' => (int) $user->id,
                'retry_after_seconds' => $retryAfter,
            ]);
            return $this->json([
                'parse_status' => 'rate_limited',
                'message' => 'Too many parse requests. Please wait a moment and try again.',
                'duration_ms' => 0,
                'size_bytes' => 0,
                'original_filename' => '',
            ], 429)->header('Retry-After', (string) $retryAfter);
        }

        $maxKb = (int) config('resumes.parser_max_kb', 5120);
        $request->validate([
            'file' => "required|file|mimes:pdf,doc,docx|max:{$maxKb}", // 5 MB default; tuned via env.
        ], [
            'file.mimes' => 'Only PDF, DOC and DOCX files are accepted.',
            'file.max' => "Resume must be {$maxKb} KB or smaller.",
        ]);

        try {
            $this->metrics->increment('parse_started_total', ['kind' => 'inline']);
        } catch (Throwable $e) {
            // Metrics must never 500 a parser response.
            ResumeLogContext::warning('metrics.write_failure', [
                'metric' => 'parse_started_total', 'exception' => $e->getMessage(),
            ]);
        }

        ResumeLogContext::event('api.resume_parser.parse.requested', [
            'company_id' => $companyId,
            'request_id' => $request->header('X-Request-Id'),
        ]);

        $file = $request->file('file');
        $rawFilename = (string) $file->getClientOriginalName();
        $originalFilename = ResumeParserService::sanitizeFilename($rawFilename);

        try {
            $response = $this->parser->parse($file, $originalFilename);
            $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
        } catch (Throwable $e) {
            // Defensive — the service returns the canonical response
            // shape even on failure via its own try/catch wrapper.
            // We end up here only if the wrapper itself explodes.
            $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
            ResumeLogContext::warning('parse.cancelled', [
                'company_id' => $companyId,
                'request_id' => $request->header('X-Request-Id'),
                'size_bytes' => (int) $file->getSize(),
                'original_filename' => $originalFilename,
                'parse_duration_ms' => $durationMs,
                'exception' => $e->getMessage(),
            ]);
            try {
                $this->metrics->recordParserOutcome('fail');
            } catch (Throwable $ignored) {}
            return $this->json([
                'parse_status' => 'failed',
                'duration_ms' => $durationMs,
                'size_bytes' => (int) $file->getSize(),
                'original_filename' => $originalFilename,
                'message' => 'Unable to parse resume.',
            ], 422);
        }

        // Successful path — even if parse_status !== 'ok' (e.g. unsupported
        // extension), we still return 200 with status=failed so the JS:
        //   - shows "Unable to parse resume. You can continue manually."
        //   - does NOT block submit (per the spec's error behaviour).
        try {
            $outcome = ($response['parse_status'] ?? '') === 'ok' ? 'success' : 'fail';
            $this->metrics->recordParserOutcome($outcome);
        } catch (Throwable $ignored) {
            // ditto
        }

        return $this->json($response, 200);
    }

    /**
     * Phase 9 follow-up — Redis-backed per-user rate limit.
     *
     * Returns true when the request is permitted. We deliberately
     * FAIL OPEN (return true) when Redis is unreachable so a cache
     * outage cannot lock recruiters out: the parser resource cap is
     * the secondary brake, and the recruiter UX priority beats the
     * rate-limit failure mode.
     *
     * The counter resets every minute so a recruiter who hit the cap
     * recovers automatically after the window expires. Configurable
     * via `resumes.parser_rate_limit_per_min` (default 10; 0
     * disables entirely).
     */
    private function allowByRateLimit(int $userId): bool
    {
        $perMin = (int) config('resumes.parser_rate_limit_per_min', 10);
        if ($perMin <= 0) {
            return true;                                       // disabled
        }
        $window = (int) config('resumes.parser_rate_limit_window_seconds', 60);
        $key = 'resumes:rl:parser:u:' . $userId;
        try {
            $hits = (int) Redis::incrby($key, 1);
            if ($hits === 1) {
                Redis::expire($key, $window);
            }
            return $hits <= $perMin;
        } catch (Throwable $e) {
            // Fail open — Redis outage must not block recruiters.
            ResumeLogContext::warning('parser.rate_limit_unavailable', [
                'user_id' => $userId,
                'exception' => $e->getMessage(),
            ]);
            return true;
        }
    }

    private function fail(string $message, int $status): JsonResponse
    {
        return $this->json([
            'parse_status' => $status === 403 ? 'forbidden' : 'failed',
            'message' => $message,
            'duration_ms' => 0,
            'size_bytes' => 0,
            'original_filename' => '',
        ], $status);
    }

    private function json(array $payload, int $status): JsonResponse
    {
        return response()->json($payload, $status);
    }
}
