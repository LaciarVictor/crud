<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    use ValidatesRequests;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('users', 'name')->ignore($userId)],
            'email' => ['required', 'email', 'string', 'max:100', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', Rule::exists('roles', 'id'),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The user already exists.'),
            'email.unique' => __('The email already exists.'),
            'role.exists' => __('The role does not exists.')
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
    
        $response = response()->json([
            'message' => $firstError,
        ], 422);
    
        throw new HttpResponseException($response);
    }
}
