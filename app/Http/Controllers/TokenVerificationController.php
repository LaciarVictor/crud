<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class TokenVerificationController extends Controller
{
    public function verify(Request $request)
    {

        //captura el token de la request
        $token = $request->bearerToken();
        // $token = $request->header('Authorization');

        // Si hay un token, comprobar de que el token es válido.
        if ($token) {
            $user = Auth::guard('sanctum')->user();
            // Si el token es válido enviar true.
            if ($user) {
                return response()->json(['status' => true]);
            }
        }

        return response()->json(['status' => false]);
    }
}
