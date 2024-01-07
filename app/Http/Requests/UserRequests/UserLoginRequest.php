<?php

namespace App\Http\Requests\UserRequests;

use App\Http\Requests\BaseRequest;



class UserLoginRequest extends BaseRequest
{



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       

        return [
            'user_name' => ['required', 'string', 'max:100'], 
            'password' => ['required', 'min:6']
        ];
    }
}