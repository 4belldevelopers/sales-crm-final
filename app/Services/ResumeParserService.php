<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Sends extracted resume text to OpenAI and returns structured candidate data.
 * Falls back to regex-based extraction if OpenAI is unavailable.
 */
class ResumeParserService
{
    private string $apiKey;
    private string $model = 'gpt-4o-mini'; // cost-efficient, fast

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', env('OPENAI_API_KEY', ''));
    }

    /**
     * Parse resume text into structured fields.
     *
     * @param  string $text  Raw text extracted from the resume file
     * @return array         Structured candidate data
     */
    public function parse(string $text): array
    {
        if (empty(trim($text))) {
            return $this->emptyResult('No text could be extracted from the resume.');
        }

        // Truncate to ~12 000 chars to stay within token limits
        $truncated = mb_substr($text, 0, 12000);

        if (!empty($this->apiKey)) {
            try {
                return $this->parseWithOpenAI($truncated);
            } catch (\Throwable $e) {
                Log::warning('ResumeParserService: OpenAI failed, falling back to regex', ['error' => $e->getMessage()]);
            }
        }

        return $this->parseWithRegex($text);
    }

    private function parseWithOpenAI(string $text): array
    {
        $prompt = <<<PROMPT
        Extract the following fields from this resume text. Return ONLY valid JSON, no markdown fences.

        Fields to extract:
        - full_name
        - email
        - phone
        - skills (array of skill strings)
        - total_experience_years (number, 0 if unknown)
        - current_company
        - current_designation
        - notice_period (e.g. "30 days", "Immediate", unknown)
        - expected_salary
        - location (city, state, country)
        - education (array of {degree, institution, year})
        - languages (array of language strings)
        - certifications (array of certification strings)
        - career_summary (short paragraph)

        Resume text:
        {$text}
        PROMPT;

        $payload = json_encode([
            'model'       => $this->model,
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a precise resume parser. Return only valid JSON.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'temperature' => 0,
            'max_tokens'  => 1500,
        ]);

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $this->apiKey, 'Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 60,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("OpenAI returned HTTP {$httpCode}");
        }

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '';
        $parsed  = json_decode($content, true);

        if (!is_array($parsed)) {
            throw new \RuntimeException('OpenAI returned non-JSON content');
        }

        return array_merge($this->emptyResult(), $parsed, ['source' => 'openai']);
    }

    /**
     * Lightweight regex fallback — mirrors the patterns in HomeController::resumExtract().
     */
    private function parseWithRegex(string $text): array
    {
        $email = '';
        if (preg_match('/\b[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Z|a-z]{2,}\b/', $text, $m)) {
            $email = $m[0];
        }

        $phone = '';
        $phonePatterns = ['/\b\d{10}\b/', '/\b\d{3}[-.\s]\d{3}[-.\s]\d{4}\b/', '/\(\d{3}\)\s?\d{3}[-.\s]\d{4}/'];
        foreach ($phonePatterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $phone = $m[0];
                break;
            }
        }

        return array_merge($this->emptyResult(), [
            'email'  => $email,
            'phone'  => $phone,
            'source' => 'regex',
        ]);
    }

    private function emptyResult(string $error = ''): array
    {
        return [
            'full_name'               => '',
            'email'                   => '',
            'phone'                   => '',
            'skills'                  => [],
            'total_experience_years'  => 0,
            'current_company'         => '',
            'current_designation'     => '',
            'notice_period'           => '',
            'expected_salary'         => '',
            'location'                => '',
            'education'               => [],
            'languages'               => [],
            'certifications'          => [],
            'career_summary'          => '',
            'source'                  => 'none',
            'parse_error'             => $error,
        ];
    }
}
