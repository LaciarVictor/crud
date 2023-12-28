<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserRequests\UserLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;


/**
 * Clase encargada de controlar la autenticación de usuarios
 */
class AuthController extends Controller
{


    protected $authService;

    /**
     * El constructor recibe un servicio que se encarga de gestionar autorizaciones
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }




    /**
    * Trata de loguear un usuario
    *
    * @param UserLoginRequest $request
    * @return void
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
     * Trata de desloguear al usuario
     *
     * @param Request $request
     * @return void
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
