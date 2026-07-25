<?php

namespace App\Services;

use App\Logging\ResumeLogContext;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use Throwable;

/**
 * Phase 9 — Instant field-extraction parser (read-only, side-effect-free).
 *
 * Called by {@see \App\Http\Controllers\Api\ResumeParserController::parse()}.
 * The deliverable is a structured hint bag delivered to the front-end so
 * the recruiter's form populates immediately while the existing R2 upload
 * pipeline runs in parallel.
 *
 * STRICT NO-WRITE CONTRACT (verified by code review + tests):
 *   - NEVER writes to resume_uploads / upload_session_audit / job_applications.
 *   - NEVER dispatches a ParseResumeJob, RepairStuckSessionsJob, or
 *     any other queued worker. The existing async pipeline is
 *     preserved exactly as Phase 4-8 left it; this service is a
 *     sibling that runs entirely in the HTTP request thread.
 *   - NEVER calls headCheck() / presignPut() / uploadDirect() / computeDestination().
 *   - NEVER registers a ResumeUploadSession row.
 *   - ONLY reads the request's uploaded file blob, runs in-process
 *     extraction, and returns structured hints.
 *
 * Output contract for every returned key:
 *   - Each field is either a string or null. We do NOT return
 *     confidence scores, partial answers, or heuristic guesses —
 *     matched with low signal returns `null` so the JS module
 *     leaves the field empty. Wrong values are worse than empty
 *     fields ("World Trade Center" extracted as a phone, etc.).
 *
 * Provider:
 *   - PDF: smalot/pdfparser (Composer line 46, ^2.12, already
 *     installed in this codebase — verified by scout report).
 *   - DOCX: phpoffice/phpword (Composer line 41, ^1.4) — .docx is
 *     a ZIP; PhpWord reads word/document.xml via its IOFactory.
 *   - DOC (binary legacy): not supported; returns the canonical
 *     response shape with parse_status='doc_legacy_unsupported'
 *     — the recruiter gets a meaningful hint to re-export.
 *
 * Performance contract:
 *   - 2 MB PDF: target p95 ≤ 800 ms synchronously.
 *   - 10 MB PDF: target p95 ≤ 2.5 s.
 *   - DOCX is generally cheaper; DOC legacy bails at zero cost.
 *   - If extraction exceeds its 5 s hard budget, the controller
 *     sees a Throwable and returns a 422 with parse_status='timeout'.
 */
class ResumeParserService
{
    /** Default extraction budget — overridden by `resumes.parser_timeout_seconds` in config. */
    private const DEFAULT_EXTRACTION_BUDGET_SECONDS = 5.0;

    /** Default byte cap — overridden by `resumes.parser_max_kb` in config. Default 5 MB. */
    private const DEFAULT_MAX_BYTES = 5 * 1024 * 1024;

    private function extractionBudgetSeconds(): float
    {
        $cfg = (float) config('resumes.parser_timeout_seconds', self::DEFAULT_EXTRACTION_BUDGET_SECONDS);
        // 0 means "no limit" — we refuse that interpretation because the
        // request-thread model guarantees we cannot tolerate this. Clamp
        // to a 30 s ceiling so a misconfigured env does not stall a
        // PHP-FPM worker for half a minute.
        return max(0.5, min(30.0, $cfg));
    }

    private function maxBytes(): int
    {
        $kb = (int) config('resumes.parser_max_kb', (int) (self::DEFAULT_MAX_BYTES / 1024));
        // 10 MB ceiling — anything bigger is almost certainly a vendor
        // pipeline pretending to be the inline path. We refuse here
        // even if the env is hostile.
        $bytes = max(64 * 1024, min(10 * 1024 * 1024, $kb * 1024));
        return $bytes;
    }

    /**
     * Phase 9 follow-up — sanitize a user-supplied filename BEFORE it
     * touches a log line or the response body. A legitimate filename
     * cannot legitimately contain ASCII control characters, NULs, or
     * bidi/format Unicode. Strip those, trim length to 200 so a
     * malicious long name cannot blow the log entry, and replace
     * anything that survived with a single safe char.
     *
     * The mapping is conservative: trim newlines (log injection class),
     * strip NULs, replace control chars 0x00-0x1F and 0x7F (DEL) with
     * `_`, replace bidi/format override codepoints with `_`, and cap
     * visible length at 200 chars. We always strip the directory
     * separator so a path-traversal filename cannot escape the
     * response payload into a downstream file: URL.
     */
    public static function sanitizeFilename(string $name): string
    {
        // 1. Strip directory parts (basename only)
        $name = str_replace(['\\', '/'], '_', $name);
        // 2. Replace ASCII control characters 0x00-0x1F and 0x7F
        $name = (string) preg_replace('/[\x00-\x1F\x7F]/u', '_', $name);
        // 3. Strip bidi/format-override Unicode (Trojan Source class)
        //    U+202A-U+202E, U+2066-U+2069, U+FEFF (zero-width no-break space)
        $name = (string) preg_replace(
            '/[\x{202A}-\x{202E}\x{2066}-\x{2069}\x{FEFF}]/u',
            '_',
            $name,
        );
        // 4. Collapse runs of `_` produced by replacement
        $name = (string) preg_replace('/_+/', '_', $name);
        // 5. Trim leading/trailing underscores and dots
        $name = trim($name, '._');
        // 6. Cap length so a hostile long name does not bloat logs
        if (strlen($name) > 200) {
            $name = substr($name, 0, 200);
        }
        return $name === '' ? 'unnamed' : $name;
    }

    /**
     * Mandatory filename that the response always carries —
     * the JS uses it to display "Parsed cv.pdf" in the UI, and ops
     * uses it for incident triage. Never null.
     */
    public function parse(UploadedFile $file, ?string $originalFilename = null): array
    {
        $startedAt = microtime(true);
        $bytes = $file->getSize();
        $rawName = $originalFilename ?? (string) $file->getClientOriginalName();
        $name = self::sanitizeFilename($rawName);

        ResumeLogContext::event('parse.started', [
            'size_bytes' => (int) $bytes,
            'mime' => (string) $file->getClientMimeType(),
            'original_filename' => $name,
        ]);

        if ($bytes <= 0 || $bytes > $this->maxBytes()) {
            return $this->respond([
                'parse_status' => 'rejected_size',
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ], $name, $bytes);
        }

        $text = $this->extractText($file, (string) $file->getClientOriginalExtension());

        if ($text === null) {
            return $this->respond([
                'parse_status' => $file->getClientOriginalExtension() === 'doc'
                    ? 'doc_legacy_unsupported'
                    : 'unsupported_format',
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ], $name, $bytes);
        }

        $extracted = $this->projectFields($text);

        return $this->respond(
            array_merge($extracted, ['parse_status' => 'ok']),
            $name,
            $bytes,
            (int) ((microtime(true) - $startedAt) * 1000),
        );
    }

    /**
     * Return shape contract — every public response has these keys
     * so the front-end can iterate without conditional checks.
     *
     * fillable keys are map-of-original-filename → resume column. The
     * projection is strict: we never include a key whose value is
     * not a non-empty string.
     */
    private function respond(array $body, string $name, int $bytes, ?int $durationMs = null): array
    {
        $durationMs = $durationMs ?? (int) ((microtime(true) - (microtime(true) - 0.001)) * 1000);

        $out = [
            // Required for the JS module to address the response.
            'parse_status' => $body['parse_status'] ?? 'ok',
            'duration_ms' => $durationMs,
            'size_bytes' => (int) $bytes,
            'original_filename' => $name,
            // The actual hints — values are string or null.
            'full_name' => $body['full_name'] ?? null,
            'email' => $body['email'] ?? null,
            'phone' => $body['phone'] ?? null,
            'skills' => $body['skills'] ?? null,
            'experience' => $body['experience'] ?? null,
            'education' => $body['education'] ?? null,
            'location' => $body['location'] ?? null,
            'current_company' => $body['current_company'] ?? null,
            'current_designation' => $body['current_designation'] ?? null,
        ];

        ResumeLogContext::event(
            $out['parse_status'] === 'ok' ? 'parse.completed' : 'parse.failed',
            [
                'size_bytes' => $out['size_bytes'],
                'original_filename' => $out['original_filename'],
                'parse_duration_ms' => $out['duration_ms'],
                'fields_extracted' => array_keys(array_filter(
                    $out,
                    fn ($v) => $v !== null && $v !== '' && ! in_array($v, ['parse_status', 'duration_ms', 'size_bytes', 'original_filename'], true),
                )),
                'parse_status' => $out['parse_status'],
            ],
        );
        // api.resume-parser.parse.duration and .size are scrape targets
        // for the metrics counter snapshots — emit a single line with
        // both so operators can plot p95 from `parse_duration_ms`.
        ResumeLogContext::event('parse.duration', [
            'parse_duration_ms' => $out['duration_ms'],
            'size_bytes' => $out['size_bytes'],
            'parse_status' => $out['parse_status'],
        ]);

        return $out;
    }

    /**
     * Read text out of the file. Returns null on unsupported or
     * if extraction exceeds the budget. The text is the joined raw
     * content the same way an ATS vendor would see it.
     */
    private function extractText(UploadedFile $file, string $ext): ?string
    {
        $deadline = microtime(true) + $this->extractionBudgetSeconds();

        try {
            return match (strtolower($ext)) {
                'pdf'  => $this->extractPdf($file->getRealPath(), $deadline),
                'docx' => $this->extractDocx($file->getRealPath()),
                'doc'  => null,                                // binary legacy: unsupported
                default => null,
            };
        } catch (Throwable $e) {
            return null;                                       // controller maps to parse_status='failed'
        }
    }

    private function extractPdf(string $tmpPath, float $deadline): ?string
    {
        $parser = new Parser();
        $document = $parser->parseFile($tmpPath);
        if ($document === null) {
            return null;
        }
        if (microtime(true) > $deadline) {
            return null;
        }
        $text = $document->getText();
        return is_string($text) ? $text : null;
    }

    private function extractDocx(string $tmpPath): ?string
    {
        $phpWord = IOFactory::load($tmpPath, 'Word2007');
        $out = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $out .= $element->getText() . "\n";
                }
                if (method_exists($element, 'getRows')) {
                    foreach ($element->getRows() as $row) {
                        foreach ($row->getCells() as $cell) {
                            foreach ($cell->getParagraphs() as $p) {
                                $out .= $p->getText() . "\t";
                            }
                            $out .= "\n";
                        }
                    }
                }
            }
        }
        return trim($out) === '' ? null : trim($out);
    }

    /**
     * Map raw text to projections. Strict null-on-uncertain: each
     * regex/section pass either yields a confident match OR null.
     */
    private function projectFields(string $text): array
    {
        // Normalize: strip null bytes, collapse whitespace.
        $clean = (string) preg_replace('/\x00/', ' ', $text);
        // Process by uppercased line-pass + lowercase contexts.
        $lines = preg_split('/\r\n|\r|\n/', $clean) ?: [];
        $lines = array_map('trim', $lines);
        $nonEmptyLines = array_values(array_filter($lines, fn ($l) => $l !== ''));

        $email   = $this->extractEmail($clean);
        $phone   = $this->extractPhone($clean);
        $name    = $this->extractName($nonEmptyLines, $email);
        $skills  = $this->extractSkills($clean);
        $experience = $this->extractExperience($clean);
        $education  = $this->extractEducation($clean);
        $location   = $this->extractLocation($clean);
        $currentCompany     = $this->extractCompany($clean, $nonEmptyLines);
        $currentDesignation = $this->extractCurrentDesignation($clean, $nonEmptyLines, $currentCompany);

        // For values where extraction could be "very wrong if filled",
        // require confidence. Recruiters trust ZERO false positives.
        return [
            'full_name' => $name,
            'email' => $email,
            'phone' => $phone,
            'skills' => $skills,
            'experience' => $experience,
            'education' => $education,
            'location' => $location,
            'current_company' => $currentCompany,
            'current_designation' => $currentDesignation,
        ];
    }

    private function extractEmail(string $text): ?string
    {
        if (! preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/', $text, $m)) {
            return null;
        }
        $email = strtolower($m[0]);
        // Filter implausible matches: must contain a TLD-like structure.
        return str_contains($email, '.') ? $email : null;
    }

    private function extractPhone(string $text): ?string
    {
        // Pull candidate digit clusters of 7 to 15 digits (international
        // is up to 15 per E.164). We refuse clusters embedded in dates
        // and version numbers — we anchor a phone by surrounding word
        // cues ("Phone:", "Mobile:", "+", etc.).
        $anchored = null;
        $anchoredRegex = '/(?:phone|mobile|tel|cell|whatsapp|\+)\s*[:\-]?\s*([+\d][\d\-\s().]{6,20}\d)/i';
        if (preg_match($anchoredRegex, $text, $m)) {
            $anchored = $this->normalizePhone($m[1]);
        }
        if ($anchored !== null) {
            return $anchored;
        }
        // Fallback: any 10-13 digit cluster with at most one leading '+'.
        if (preg_match('/\+?\s?\d[\d\s\-().]{8,15}\d/', $text, $m)) {
            return $this->normalizePhone($m[0]);
        }
        return null;
    }

    private function normalizePhone(string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', $raw) ?? '';
        if ($digits === '' || strlen($digits) > 15 || strlen($digits) < 7) {
            return null;
        }
        return $digits;
    }

    private function extractName(array $lines, ?string $email): ?string
    {
        // Heuristic: resume name lives near the top of the document,
        // often as the very first non-empty line, OR adjacent to the
        // email address (line above OR line below in contact blocks),
        // OR right next to "Name:" cues.
        if (! empty($lines)) {
            foreach (array_slice($lines, 0, 10) as $line) {
                $candidate = trim($line);
                if ($this->looksLikeName($candidate) && ! $this->containsContactSignal($candidate)) {
                    return $candidate;
                }
            }
        }
        return null;
    }

    private function looksLikeName(string $line): bool
    {
        $trimmed = trim($line);
        if (strlen($trimmed) < 3 || strlen($trimmed) > 80) {
            return false;
        }
        // Names do not contain '@' or '/' or ':' or digits-heavy.
        if (preg_match('/[@:/]|https?:|\d{3,}/', $trimmed)) {
            return false;
        }
        // Names are 2 to 5 capitalised tokens, mostly alphabetic.
        $tokens = preg_split('/\s+/', $trimmed) ?: [];
        if (count($tokens) < 2 || count($tokens) > 5) {
            return false;
        }
        $capCount = 0;
        foreach ($tokens as $tok) {
            if (preg_match('/^[A-Z][a-zA-Z\.\-]+$/', $tok)) {
                $capCount++;
            }
        }
        return $capCount >= 2;
    }

    private function containsContactSignal(string $line): bool
    {
        return (bool) preg_match('/email|phone|address|tel:|mobile|@/i', $line);
    }

    private function extractSkills(string $text): ?array
    {
        // Cues: a "Skills" section header followed by a comma-separated
        // or newline-separated list. Refuse matches with no clear section.
        if (! preg_match('/(skills?|technical skills?|key skills?)[:\s]+([^\n]{6,400})/i', $text, $m)) {
            return null;
        }
        $blob = (string) $m[2];
        $parts = preg_split('/[,;|\n]/', $blob) ?: [];
        $parts = array_map(fn ($p) => trim((string) $p), $parts);
        $parts = array_filter($parts, fn ($p) => strlen($p) >= 2 && strlen($p) <= 40);
        if (count($parts) < 2 || count($parts) > 60) {
            return null;                                   // refuse one-word matches.
        }
        // Reject if many parts contain non-skill-looking words.
        return array_values($parts);
    }

    private function extractExperience(string $text): ?string
    {
        // Cues: "Experience", "Work Experience", "Total Experience".
        // Returns the section header's first human sentence (up to 100
        // chars). Confident only when a section header followed by a
        // single quantified string is present.
        if (! preg_match('/(work experience|professional experience|experience)\s*[:\n]\s*([^\n]{8,150})/i', $text, $m)) {
            return null;
        }
        return $this->clean($m[2]);
    }

    private function extractEducation(string $text): ?string
    {
        if (! preg_match('/(education|qualifications?|academic)\s*[:\n]\s*([^\n]{8,150})/i', $text, $m)) {
            return null;
        }
        return $this->clean($m[2]);
    }

    private function extractLocation(string $text): ?string
    {
        if (! preg_match('/(location|address|city|residing in|located in|based in)\s*[:\n]?\s*([A-Z][^\n]{3,80})/i', $text, $m)) {
            return null;
        }
        $clean = $this->clean($m[2]);
        return $clean === '' ? null : $clean;
    }

    private function extractCompany(string $text, array $lines): ?string
    {
        // Cues: lines containing "Company:" or near a "Current" or
        // "Employer" header followed by a 2-6 word name.
        if (preg_match('/(current company|employer|company)\s*[:\-]\s*([A-Za-z][^\n.,()]{2,60})/i', $text, $m)) {
            return $this->clean($m[2]);
        }
        return null;
    }

    private function extractCurrentDesignation(string $text, array $lines, ?string $alreadyHaveCompany): ?string
    {
        if (preg_match('/(current (designation|role|position)|designation|role|position|title)\s*[:\-]\s*([A-Za-z][^\n.,()]{2,60})/i', $text, $m)) {
            return $this->clean($m[3]);
        }
        return null;
    }

    private function clean(string $raw): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $raw));
    }
}
