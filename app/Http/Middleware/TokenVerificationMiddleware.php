<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenVerificationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->check()) {
            return response()->json(['status' => 'success', 'message' => 'Token válido'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Problema con el token'], 401);
    }
}