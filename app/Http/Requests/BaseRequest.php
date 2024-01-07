<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;



/**
 * Super clase para el request 
 */
class BaseRequest extends FormRequest
{

    use ValidatesRequests;

    /**
     * Autorizar todos los request
     */
    public function authorize()
    {
        return true;
    }




    /**
     * Devuelve el primer error HttpResponseException encontrado.
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
    
        $response = response()->json([
            'message' => $firstError,
        ], 422);
    
        throw new HttpResponseException($response);
    }
}