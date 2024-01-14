<?php

namespace App\Http\Requests\CompanyRequests;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class CompanyUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:100', Rule::unique('companies', 'name')],
            'legal_name' => ['required', 'string', 'max:100', Rule::unique('companies', 'legal_name')],
            'tax_id' => ['required', 'string', 'max:100', Rule::unique('companies', 'tax_id')],
            'street' => ['required', 'string', 'max:100'],
            'street_no' => ['required', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:100', Rule::unique('companies', 'phone_number')],
            'email' => ['required', 'string', 'max:100', Rule::unique('companies', 'email')],
            'industry' => ['required', 'string', 'max:100'],
            'parent_company_id' => ['nullable', 'integer', Rule::exists('companies', 'id')],
            'registration_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.unique' => __('La empresa ya existe.'),
            'legal_name.unique' => __('El nombre de fantasía ya existe.'),
            'tax_id.unique' => __('El cuit ya existe.'),
            'phone_number.unique' => __('El número de teléfono ya existe.'),
            'email.unique' => __('El email ya existe.'),
        ];
    }
}