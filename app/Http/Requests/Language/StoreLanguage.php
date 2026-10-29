<?php

namespace App\Http\Requests\Language;

use App\Http\Requests\CoreRequest;
use Illuminate\Validation\Rule;

class StoreLanguage extends CoreRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('language_code')) {
            $this->merge([
                'language_code' => strtolower(trim((string) $this->language_code)),
            ]);
        }

        if ($this->has('language_name')) {
            $this->merge([
                'language_name' => ucfirst(trim((string) $this->language_name)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('language_setting') ?? $this->route('id') ?? $this->input('id');

        return [
            'language_name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('language_settings', 'language_name')->ignore($id),
            ],
            'language_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z]{2,3}(-[a-zA-Z0-9]{2,4})?$/',
                Rule::unique('language_settings', 'language_code')->ignore($id),
            ],
            'status' => [
                'required',
                Rule::in(['enabled', 'disabled']),
            ],
        ];
    }

    public function messages()
    {
        return [
            'language_name.required' => __('app.name').' '.__('errors.fieldRequired'),
            'language_code.required' => __('app.code').' '.__('errors.fieldRequired'),
            'language_name.unique' => __('app.name').' '.__('errors.alreadyTaken'),
            'language_code.unique' => __('app.code').' '.__('errors.alreadyTaken'),
            'language_code.regex' => 'The language code format is invalid. Please use a valid ISO-639 code (e.g. "en", "es", "zh-cn").',
        ];
    }
}
