<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class AuthMiddleware
{
    
    protected $authService;

    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
    
        if (Auth::guard('sanctum')->check()) {
            $this->authService->updateUserTokenExpiration($request);
        }
    
        return $response;
    }
}