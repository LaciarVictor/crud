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
        $userId = $this->route('user'); // Obtén el ID del usuario que se está actualizando

        return [
            'userName' => ['required', 'string', 'max:100', Rule::unique('users', 'name')->ignore($userId)],
            'firstName' => ['nullable', 'string', 'max:100'],
            'lastName' => ['nullable', 'string', 'max:100'],
            'phoneCode' => ['nullable', 'string', 'max:100'],
            'phoneNumber' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'string', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['nullable', Rule::exists('roles', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'userName.unique' => __('El usuario ya existe.'),
            'email.unique' => __('El email ya existe.'),
            'role.exists' => __('El rol no existe.'),
        ];
    }
}