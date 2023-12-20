<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;




class UserBaseRequest extends FormRequest
{

    use ValidatesRequests;

    public function authorize()
    {
        return true;
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