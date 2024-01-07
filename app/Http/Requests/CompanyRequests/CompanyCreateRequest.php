<?php

namespace App\Http\Requests\CompanyRequests;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class CompanyCreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('companies', 'name')],
            'cuit' => ['required', 'bigint', 'max:100', Rule::unique('companies', 'cuit')],

        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('La empresa ya existe.'),
            'cuit.unique' => __('El cuit ya existe.'),

        ];
    }
}