<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Validation\Rule;



class UserLoginRequest extends UserBaseRequest
{



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       

        return [
            'userName' => ['required', 'string', 'max:100'], 
            'password' => ['required', 'min:6']
        ];
    }
}