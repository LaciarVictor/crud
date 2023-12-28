<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * Esta clase es solo para validar un token
 */
class TokenVerificationController extends Controller
{
    
    /**
     * Verificar el token
     *
     * @param Request $request
     * @return void
     */
    public function verify(Request $request)
    {

        //capturar el token de la request
        $token = $request->bearerToken();
        $user = Auth::guard('sanctum')->user();

        // Si hay un token valido, y asignado a un usuario...
        if ($token && $user) {   

            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }
}
