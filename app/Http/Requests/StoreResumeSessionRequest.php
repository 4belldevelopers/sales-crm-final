<?php

namespace App\Http\Requests;

// Intent: Validates the body of POST /resume/v2/sessions (presign request).
// Idempotency-Key header is validated separately in the controller because
// FormRequest only inspects the request body.

class StoreResumeSessionRequest extends CoreRequest
{
    public function authorize(): bool
    {
        return true; // route is already behind `auth` middleware
    }

    protected function prepareForValidation(): void
    {
        $filename = (string) $this->input('original_filename', '');
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $canonicalMimeMap = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (isset($canonicalMimeMap[$ext])) {
            $this->merge([
                'mime_type' => $canonicalMimeMap[$ext],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'original_filename' => ['required', 'string', 'max:255'],
            'file_size_bytes'   => ['required', 'integer', 'min:1', 'max:' . config('resumes.validation.max_file_size', 6_291_456)],
            'mime_type'         => ['required', 'string', 'in:' . implode(',', config('resumes.validation.allowed_mime_types', [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]))],
            'sha256_hex'        => ['nullable', 'string', 'size:64', 'regex:/^[a-f0-9]+$/i'],
        ];
    }

    public function messages(): array
    {
        return [
            'file_size_bytes.max' => 'File must be 6 MB or smaller.',
            'mime_type.in'        => 'Only PDF, DOC, and DOCX files are allowed.',
            'sha256_hex.size'     => 'sha256_hex must be exactly 64 hex characters.',
            'sha256_hex.regex'    => 'sha256_hex must be a valid hex string.',
        ];
    }
}
