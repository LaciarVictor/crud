<?php

namespace App\Http\Controllers;

use App\Services\User\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\User\UserService;


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

            $authService = new AuthService();
            $userService= new UserService($user, $authService);
            $response = $userService->setJSONResponse($user);
            return response()->json(['status' => true, 'user' => $response],200);
        }

        return response()->json(['status' => false],401);
    }
}
