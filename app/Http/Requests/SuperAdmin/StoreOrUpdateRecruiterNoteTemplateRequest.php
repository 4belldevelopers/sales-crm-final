<?php

namespace App\Http\Requests\SuperAdmin;

use App\Http\Requests\CoreRequest;

/**
 * StoreOrUpdateRecruiterNoteTemplateRequest
 *
 * Intent: Strictly validates and sanitizes creation and modification of global recruiter note templates
 * used across the ATS (recruiter_note_master where company_id = NULL).
 * Tenancy: Global Super Admin scope.
 */
class StoreOrUpdateRecruiterNoteTemplateRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (int) auth()->user()->is_superadmin === 1;
    }

    /**
     * Prepare inputs for validation (trimming and sanitizing).
     */
    protected function prepareForValidation(): void
    {
        $title = $this->input('title');
        if (is_string($title)) {
            $this->merge([
                'title' => trim($title),
            ]);
        }

        $content = $this->input('content');
        if (is_string($content)) {
            $this->merge([
                'content' => trim($content),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $stripped = trim(strip_tags((string) $value));
                    $clean = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $clean = preg_replace('/\s+/', '', $clean);
                    if ($clean === '' && ! preg_match('/<img|<table|<hr|<iframe/i', (string) $value)) {
                        $fail('The template content cannot be empty.');
                    }
                },
            ],
            'status' => 'nullable',
        ];
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Template Title',
            'content' => 'Template Content',
            'status' => 'Active Status',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a descriptive template title.',
            'title.max' => 'The template title must not exceed 255 characters.',
            'content.required' => 'The template content is required.',
        ];
    }

    /**
     * Returns sanitized data ready for database persistence.
     *
     * @return array{title: string, content: string, status: int}
     */
    public function sanitizedPayload(): array
    {
        $statusInput = $this->input('status');
        $status = ($statusInput === '1' || $statusInput === 1 || $statusInput === true || $statusInput === 'on') ? 1 : 0;

        return [
            'title' => (string) $this->input('title'),
            'content' => (string) $this->input('content'),
            'status' => $status,
        ];
    }
}
