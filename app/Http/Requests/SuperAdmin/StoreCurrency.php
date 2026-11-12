<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrency extends FormRequest
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
        $currencyParam = $this->route('currency_setting') ?? $this->route('id');
        $currencyId = is_object($currencyParam) ? $currencyParam->id : $currencyParam;

        return [
            'currency_name' => 'required|string|max:191',
            'currency_symbol' => 'required|string|max:20',
            'currency_code' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
                'unique:currencies,currency_code,'.($currencyId ?? 'NULL').',id',
            ],
            'inr_based_rates' => 'required|numeric|min:0.000001',
            'usd_based_rates' => 'required|numeric|min:0.000001',
            'idempotency_key' => 'nullable|string|max:64',
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
            'currency_name.required' => 'Currency name is required.',
            'currency_symbol.required' => 'Currency symbol is required.',
            'currency_code.required' => 'Currency ISO code is required.',
            'currency_code.size' => 'Currency code must be exactly 3 characters (e.g. USD, EUR, INR).',
            'currency_code.regex' => 'Currency code must contain only 3 alphabetic characters.',
            'currency_code.unique' => 'This currency code already exists in the system.',
            'inr_based_rates.required' => 'INR based exchange rate is required.',
            'inr_based_rates.numeric' => 'INR based exchange rate must be a valid number.',
            'usd_based_rates.required' => 'USD based exchange rate is required.',
            'usd_based_rates.numeric' => 'USD based exchange rate must be a valid number.',
        ];
    }
}
