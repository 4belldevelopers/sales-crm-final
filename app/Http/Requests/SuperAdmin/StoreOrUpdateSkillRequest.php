<?php

namespace App\Http\Requests\SuperAdmin;

use App\Http\Requests\CoreRequest;

/**
 * StoreOrUpdateSkillRequest — Input Validation & Sanitization for Super Admin Master Skills.
 *
 * Intent: Strictly validates and sanitizes skill creation and updates for the master Elasticsearch
 * index ELASTIC_SKILLS. Handles Tagify JSON structures, comma-separated lists, lowercase normalization,
 * character whitelist enforcement, and deduplication.
 * Tenancy: Global Super Admin scope.
 */
class StoreOrUpdateSkillRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (int) auth()->user()->is_superadmin === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tokenId' => 'required|string',
            'skill_name' => 'required|string|max:5000',
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
            'tokenId' => 'Document Identifier',
            'skill_name' => 'Skill Name',
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
            'tokenId.required' => 'The skill identifier is missing.',
            'skill_name.required' => 'Please provide at least one skill name.',
            'skill_name.max' => 'The skill input payload is too large.',
        ];
    }

    /**
     * Parses, sanitizes, normalizes, and deduplicates submitted skill names.
     *
     * @return array<string>
     */
    public function getSanitizedSkills(): array
    {
        $raw = $this->input('skill_name', '');
        $skills = [];

        // Check if raw input is Tagify JSON
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                if (is_array($item) && isset($item['value'])) {
                    $skills[] = (string) $item['value'];
                } elseif (is_string($item)) {
                    $skills[] = $item;
                }
            }
        } else {
            // Fallback for comma-separated or plain text
            $skills = explode(',', (string) $raw);
        }

        $cleaned = [];
        foreach ($skills as $skill) {
            // Remove disallowed characters: allow letters, numbers, spaces, underscore, +, #, /, ., -
            $filtered = preg_replace('/[^a-zA-Z0-9 _+#\/.-]/', '', (string) $skill);
            $normalized = strtolower(trim($filtered));

            if ($normalized !== '' && strlen($normalized) <= 100) {
                $cleaned[] = $normalized;
            }
        }

        // Deduplicate and cap at 50 per operation
        return array_slice(array_values(array_unique($cleaned)), 0, 50);
    }
}
