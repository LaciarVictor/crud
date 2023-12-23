<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    
    public function login(UserLoginRequest $request): JsonResponse
{
    // Validar las credenciales del usuario y generar el token de acceso
    $credentials = $request->only(['name', 'password']);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Usuario o contraseña incorrectos.'], 401);
    }

    // Obtener el usuario autenticado
    $user = Auth::user();

    // Verificar si el usuario ya tiene un token de acceso activo
    if ($user->tokens->count() > 0) {
        return response()->json(['message' => 'El usuario ya tiene un token.'], 403);
    }

    // Crear un nuevo token de acceso para el usuario
    $accessToken = $this->createUserAccessToken($user);

    // Devolver el token de acceso
    return response()->json(['token' => $accessToken]);
}




    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Usuario deslogueado.']);

    }




    public function createUserAccessToken($user): string
    {
        // Devuelve el token existente si ya tiene uno
        $existingToken = $user->currentAccessToken();

        if ($existingToken !== null) {
            return $existingToken->plainTextToken;
        }

        // Generar un nuevo token de acceso
        $token = $user->createToken('token')->plainTextToken;

        // Agregar una expiración de 30 minutos al token
        $this->updateTokenExpiration($token);

        return $token;
    }




    public function updateTokenExpiration($token): void
    {
        if ($token instanceof PersonalAccessToken && !$token->expired()) {
            $newExpiration = now()->addMinutes(30); // Actualizar duración del token
            $token->forceFill([
                'expires_at' => $newExpiration,
            ])->save();
        }
    }




    public function updateUserTokenExpiration(Request $request): void
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $token = $user->currentAccessToken();

            if ($token instanceof PersonalAccessToken && !$token->expired()) {
                $this->updateTokenExpiration($token);
            }
        }
    }
}