<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use DateTime;

use Exception;

/**
 * Gestiona las autenticaciones de usuario
 */
class AuthService
{


    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only(['user_name', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Usuario o contraseña incorrectos.'], 401);
            }

            $user = Auth::user();

            //Se asume que el usuario puede estar logueandose porque su token ha expirado.
            //Ver Comando tokens:clean.
            $this->revokeExpiredTokens($user);


            $accessToken = $this->createUserAccessToken($user);

            return response()->json(['message' => 'Inicio de sesión exitoso.', 'user' => $this->setJSONResponse($user), 'token' => $accessToken], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => "Error loguando al usuario.", 'error' => $ex->getMessage()], 500);
        }
    }




    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            $personalAccessToken = PersonalAccessToken::findToken($token);

            if ($personalAccessToken) {
                $user = $personalAccessToken->tokenable;
                $user->tokens()->delete();

                return response()->json(['message' => 'Cierre de sesión exitoso.']);
            } else {
                return response()->json(['message' => 'La solicitud debe contener un token de acceso activo.'], 404);
            }

            return response()->json(['message' => 'Error en el cierre de sesión.'], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => "Error desconocido en el cierre de sesión.", 'error' => $ex->getMessage()], 500);
        }
    }




    public function createUserAccessToken(User $user): string
    {
        try {
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
        } catch (Exception $ex) {
            return response()->json(['message' => "Error generando el token.", 'error' => $ex->getMessage()], 500);
        }
    }




    public function updateTokenExpiration($token)
    {
        try {
            $token->forceFill([
                'expires_at' => Carbon::now()->addMinutes(30)
            ])->save();
        } catch (Exception $ex) {
            return response()->json(['message' => "Error actualizando expiración del token.", 'error' => $ex->getMessage()], 500);
        }
    }




    public function updateUserTokenExpiration(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $token = $user->currentAccessToken();
                $this->updateTokenExpiration($token);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => "Error actualizando expiración del token en el usuario.", 'error' => $ex->getMessage()], 500);
        }
    }

    private function revokeExpiredTokens($user)
    {
        // Obtener todos los tokens del usuario
        $tokens = $user->tokens;

        // Filtrar y eliminar los tokens expirados
        $tokens->each(function ($token) {
            if ($token->expires_at < now()) {
                $token->delete();
            }
        });
    }



    public function setJSONResponse($userData): array
    {
        $user = $userData;

        $role_id = $user->roles->first()->id;

        $role_name = Role::where('id', $role_id)->first()->name;


        return [
            'id' => $user->id,
            'user_name' => $user->user_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_code' => $user->phone_code,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'created_at' => $this->formattedDate($user->created_at),
            'updated_at' => $this->formattedDate($user->updated_at),
            'role' => $role_name
        ];
    }

    function formattedDate($timestamp): string
    {
        // Crear un objeto DateTime a partir del timestamp
        $date = new DateTime($timestamp);

        // Reformatear la fecha al formato dd:mm:yyyy
        return $date->format('d/m/Y');
    }
}
