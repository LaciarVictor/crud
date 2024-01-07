<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Gestiona las autenticaciones de usuario
 */
class AuthService
{


    public function login(UserLoginRequest $request): JsonResponse
    {
        // Validar las credenciales del usuario y generar el token de acceso
        $credentials = $request->only(['user_name', 'password']);
    
        //$coincide = Hash::check('123456', '$2y$12$jTxBUW.uhIVuOS/ff8oMjOpFOOGkvxc4sTZeUm/suZKDtjJ/1.oOK') ? true : false;



        
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Usuario o contraseña incorrectos.'], 401);
        }
    
        // Obtener el usuario autenticado
        $user = Auth::user();
        $tokens = $user->tokens;
    
        // Verificar si el usuario tiene tokens
        if ($tokens->isNotEmpty()) {
            // Verificar si al menos uno de los tokens es válido
            if ($tokens->every(function ($token) {
                return !$token->expired();
            })) {
                return response()->json(['message' => 'El usuario ya tiene una sesión activa.'], 403);
            }
    
            // Eliminar todos los tokens expirados
            $tokens->filter(function ($token) {
                return $token->expired();
            })->each(function ($token) {
                $token->delete();
            });
        }
    
        // Crear un nuevo token de acceso para el usuario
        $accessToken = $this->createUserAccessToken($user);
    
        // Devolver el token de acceso
        return response()->json(['token' => $accessToken]);
    }
    



    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        $personalAccessToken = PersonalAccessToken::findToken($token);

        if ($personalAccessToken) {
            $user = $personalAccessToken->tokenable;
            $user->tokens()->delete();

            return response()->json(['message' => 'Cierre de sesión exitoso.']);
        }
        else
        {
            return response()->json(['message' => 'La solicitud debe contener un token de acceso activo, por favor verifique'], 404);
        }

        return response()->json(['message' => 'Error en el cierre de sesión.'], 500);
    }




    public function createUserAccessToken(User $user): string
    {
        // Devuelve el token existente si ya tiene uno
        $existingToken = $user->currentAccessToken();

        if ($existingToken !== null) {
            return $existingToken->plainTextToken;
        }

        // Generar un nuevo token de acceso
        $token = $user->createToken('token');

        // Agregar una expiración de 30 minutos al token
        $this->updateTokenExpiration($token->accessToken);

        return $token->plainTextToken;
    }




    public function updateTokenExpiration($token): void
    {
        $token->forceFill([
            'expires_at' => Carbon::now()->addMinutes(30)
        ])->save();
    }




    public function updateUserTokenExpiration(Request $request): void
    {
        $user = $request->user();

        if ($user) {
            $token = $user->currentAccessToken();
            $this->updateTokenExpiration($token);
        }
    }
}
