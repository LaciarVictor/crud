<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use Illuminate\Support\Carbon;

class AuthMiddleware
{
    
    protected $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }
/*Refreshea la fecha de caducidad del token con cada petición a la ruta 
que este middleware proteje.
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