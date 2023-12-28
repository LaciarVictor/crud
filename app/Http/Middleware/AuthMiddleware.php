<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;


/**
 * Esta clase agrega tiempo a la expiración del token
 */
class AuthMiddleware
{

    protected $authService;

    /**
     * El constructor utiliza el sevicio authservice para gestionar un token.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }

    /**
     * Actualiza el tiempo de expiración del token.
     * Debe utilizarse junto a auth:sanctum para verificar la existencia del token.
     *
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        $this->authService->updateUserTokenExpiration($request);

        return $next($request);


        // $user = $request->user();

        // if ($user) {
        //     $token = $user->currentAccessToken();
        //     $this->authService->updateTokenExpiration($token);
        // }

        // return $next($request);


    }
}
