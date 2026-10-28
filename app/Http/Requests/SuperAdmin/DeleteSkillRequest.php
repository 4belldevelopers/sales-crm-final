<?php

namespace App\Http\Requests\SuperAdmin;

use App\Http\Requests\CoreRequest;

/**
 * DeleteSkillRequest — Input Validation for Single or Bulk Master Skill Deletion.
 *
 * Intent: Strictly validates single document ID or batch document IDs for deletion
 * from the master Elasticsearch index ELASTIC_SKILLS.
 * Tenancy: Global Super Admin scope.
 */
class DeleteSkillRequest extends CoreRequest
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
            'id' => 'required_without:ids|nullable|string',
            'ids' => 'required_without:id|nullable|array',
            'ids.*' => 'string',
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
            'id.required_without' => 'Please provide a skill ID to delete.',
            'ids.required_without' => 'Please select at least one skill to delete.',
            'ids.array' => 'The selected skill IDs must be an array.',
        ];
    }

    /**
     * Returns an array of clean document IDs to be deleted.
     *
     * @return array<string>
     */
    public function getTargetDocIds(): array
    {
        $ids = [];

        if ($this->has('ids') && is_array($this->input('ids'))) {
            foreach ($this->input('ids') as $docId) {
                $docId = trim((string) $docId);
                if ($docId !== '') {
                    $ids[] = $docId;
                }
            }
        } elseif ($this->filled('id')) {
            $ids[] = trim((string) $this->input('id'));
        }

        return array_values(array_unique($ids));
    }
}
