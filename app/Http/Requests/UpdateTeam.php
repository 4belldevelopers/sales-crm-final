<?php

namespace App\Http\Requests;

/**
 * Form Request: Update Team Member Profile
 *
 * Intent: Validates incoming request parameters when updating an existing team member profile.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class UpdateTeam extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'mobile' => 'nullable|string|max:30',
            'designation' => 'nullable|string|max:100',
            'country' => 'nullable',
            'state' => 'nullable',
            'city' => 'nullable',
            'logo' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Full name is required',
            'logo.max' => 'Image file size must not exceed 2MB',
        ];
    }
}
