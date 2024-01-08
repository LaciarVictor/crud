<?php

namespace App\Http\Controllers;


use App\Http\Requests\UserRequests\UserLoginRequest;
use App\Services\User\AuthService;
use Illuminate\Http\JsonResponse;
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
    public function login(UserLoginRequest $request): JsonResponse
    {
            return $this->authService->login($request);
    }



    /**
     * Trata de desloguear al usuario
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): JsonResponse
    {

            return $this->authService->logout($request);

    }


}
