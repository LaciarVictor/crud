<?php

namespace App\Http\Requests\UserRequests;
use Illuminate\Validation\Rule;



class UserUpdateRequest extends UserBaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'string', 'max:100'],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', Rule::exists('roles', 'id'),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'role.exists' => __('El rol no existe.')
        ];
    }
}