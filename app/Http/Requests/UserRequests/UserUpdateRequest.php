<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class UserUpdateRequest extends BaseRequest
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
            'user_name' => ['required', 'string', 'max:100', Rule::unique('users', 'user_name')->ignore($userId)],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_code' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'string', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['nullable', Rule::exists('roles', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.unique' => __('El usuario ya existe.'),
            'email.unique' => __('El email ya existe.'),
            'role.exists' => __('El rol no existe.'),
        ];
    }
}