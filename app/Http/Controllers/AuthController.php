<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserRequests\UserLoginRequest;
//use App\Http\Requests\UserRequests\UserRegisterRequest;
//use App\Http\Requests\UserRequests\UserStoreRequest;
//use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use Illuminate\Http\Request;

//use App\Models\User;
//use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{


    protected $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }




    /**
     * Verifica las credenciales del usuario, crea el token y envía el JSON.
     */
    public function login(UserLoginRequest $request)
    {
        try {

            return $this->authService->login($request);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error en el proceso de autenticación.', 'error' => $e->getMessage()], 500);
        }
    }



    /**
     *  Verifica las credenciales del usuario, borra el token y envía el JSON.
     */
    public function logout(Request $request)
    {
        try 
        {
            return $this->authService->logout($request);

        } 
    catch (\Throwable $e) {
        return response()->json(['message' => 'Error en el proceso de cierre de sesión.', 'error' => $e->getMessage()], 500);
    }
    }


}
